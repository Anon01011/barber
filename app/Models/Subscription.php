<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'salon_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'status',
        'payment_method',
        'payment_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'cancelled_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(SaasPayment::class)->latestOfMany();
    }


    /**
     * Check if subscription is currently active
     * 
     * A subscription is considered active if:
     * 1. Status is 'active' AND (ends_at is null OR ends_at is in the future)
     * 2. Status is 'cancelled' AND ends_at is in the future (grace period)
     * 
     * Grace Period Explanation:
     * When a subscription is cancelled, it remains active until the paid period ends.
     * This allows salons to continue using features they've already paid for.
     * Once ends_at passes, the subscription is truly expired.
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        // Active status OR cancelled but still within the paid period
        return ($this->status === 'active' || $this->status === 'cancelled')
            && ($this->ends_at === null || $this->ends_at->isFuture());
    }


    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        if (!$this->ends_at) {
            return false; // No end date means never expires
        }

        return $this->ends_at->isPast() && $this->status !== 'active';
    }

    /**
     * Check if subscription is expiring soon (within 7 days)
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->ends_at) {
            return false; // No end date means unlimited
        }

        return $this->ends_at->isFuture() && $this->ends_at->diffInDays(now()) <= 7;
    }

    /**
     * Get days until subscription expiration
     */
    public function daysUntilExpiration(): ?int
    {
        if (!$this->ends_at) {
            return null; // No end date
        }

        if ($this->ends_at->isPast()) {
            return 0; // Already expired
        }

        return (int) now()->diffInDays($this->ends_at, false);
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'active')
                ->orWhere(function ($subQ) {
                    $subQ->where('status', 'cancelled')
                        ->where('ends_at', '>', now());
                });
        })->where(function ($q) {
            $q->whereNull('ends_at')
                ->orWhere('ends_at', '>', now());
        });
    }

    /**
     * Renew the current subscription
     */
    public function renew(): bool
    {
        if (!$this->plan) {
            return false;
        }

        // Calculate new end date from current end date or now
        $startDate = $this->ends_at && $this->ends_at->isFuture()
            ? $this->ends_at
            : now();

        $this->update([
            'ends_at' => $startDate->addDays($this->plan->duration_in_days),
            'status' => 'active'
        ]);

        return true;
    }

    /**
     * Calculate prorated amount for plan change
     * 
     * @param Plan $newPlan
     * @return array ['credit' => float, 'charge' => float, 'days_remaining' => int]
     */
    public function calculateProration(Plan $newPlan): array
    {
        $daysRemaining = $this->daysUntilExpiration();

        if (!$this->plan || $daysRemaining <= 0) {
            return [
                'credit' => 0,
                'charge' => $newPlan->price,
                'days_remaining' => max(0, $daysRemaining),
                'prorated_charge' => $newPlan->price,
                'is_downgrade' => false,
                'old_daily_rate' => 0,
                'new_daily_rate' => round($newPlan->price / max(1, $newPlan->duration_in_days), 2),
            ];
        }

        $totalDays = max(1, $this->plan->duration_in_days);
        $dailyRateOld = $this->plan->price / $totalDays;
        $dailyRateNew = $newPlan->price / max(1, $newPlan->duration_in_days);

        // Credit for unused days on current plan
        $credit = $daysRemaining * $dailyRateOld;

        // Charge for remaining days on new plan
        $charge = $daysRemaining * $dailyRateNew;

        // Net amount to charge (can be negative for downgrades = credit)
        $proratedCharge = $charge - $credit;

        return [
            'credit' => round($credit, 2),
            'charge' => round($charge, 2),
            'days_remaining' => $daysRemaining,
            'prorated_charge' => round($proratedCharge, 2), // Can be negative
            'is_downgrade' => $proratedCharge < 0,
            'old_daily_rate' => round($dailyRateOld, 2),
            'new_daily_rate' => round($dailyRateNew, 2),
        ];
    }

    /**
     * Upgrade to a new plan with proration
     */
    public function upgradeTo(Plan $newPlan, array $proration = null): ?Subscription
    {
        // Calculate proration if not provided
        if ($proration === null) {
            $proration = $this->calculateProration($newPlan);
        }

        // Wrap in transaction to prevent orphaned subscriptions
        return \DB::transaction(function () use ($newPlan) {
            // Mark current subscription as cancelled
            $this->update(['status' => 'cancelled']);

            // Create new subscription
            $newSubscription = static::create([
                'salon_id' => $this->salon_id,
                'plan_id' => $newPlan->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($newPlan->duration_in_days),
                'status' => 'active',
            ]);

            return $newSubscription;
        });
    }

    /**
     * Check if can upgrade to a specific plan
     */
    public function canUpgradeTo(Plan $newPlan): bool
    {
        // Can't upgrade to the same plan
        if ($this->plan_id === $newPlan->id) {
            return false;
        }

        // Can upgrade if new plan price is higher or has more features
        return !$this->plan || $newPlan->price >= $this->plan->price;
    }

    /**
     * Check if feature limit is exceeded
     */
    public function isLimitExceeded(string $feature): bool
    {
        if (!$this->plan) {
            return true;
        }

        $limit = $this->plan->getLimit($feature);

        // Use standardized unlimited check (null only)
        if ($limit === null) {
            return false; // Unlimited
        }

        // Get current usage based on feature
        $currentUsage = $this->getCurrentUsage($feature);

        return $currentUsage >= $limit;
    }

    /**
     * Get current usage for a specific feature
     */
    protected function getCurrentUsage(string $feature): int
    {
        $salon = $this->salon;

        switch ($feature) {
            case 'max_users':
                // Count all users who are NOT customers (employees, managers, etc.)
                return $salon->users()->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'customer');
                })->count();

            case 'max_staff':
                return $salon->users()->role('employee')->count();

            case 'max_branches':
                return $salon->branches()->count();

            case 'max_services':
                return $salon->services()->count();

            case 'max_products':
                return $salon->products()->whereNotNull('salon_id')->count();

            case 'max_customers':
                return $salon->customers()->count();

            case 'max_memberships':
                return $salon->memberships()->count();

            case 'max_packages':
                return $salon->packages()->count();

            case 'max_bookings_per_month':
                return $salon->bookings()
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();

            case 'max_guest_bookings_per_month':
                return $salon->bookings()
                    ->whereHas('customer', function ($query) {
                        $query->where('is_guest', true);
                    })
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();

            default:
                return 0;
        }
    }
}
