<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;
use App\Models\SaasPayment;

class Salon extends Model
{
    protected $fillable = [
        'name',
        'business_type',
        'slug',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'status',
        'is_active',
        'license_key',
        'subscription_id',
        'subscription_status',
        'trial_ends_at',
        'owner_id',
        'setup_completed_at',
        'last_login_at',
        'database_name', // For future multi-database support
        'mail_driver',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'timezone',
        'currency',
        'refund_fee',
        'refund_fee_type',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'refund_fee' => 'decimal:2',
        'setup_completed_at' => 'datetime',
        'last_login_at' => 'datetime',
        'mail_port' => 'integer',
        'mail_password' => 'encrypted'
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Retrieve the model for a bound value with slug support and ID fallback.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if (is_numeric($value)) {
            return static::where('id', $value)->first() 
                ?? static::where('slug', $value)->firstOrFail();
        }

        return static::where('slug', $value)->firstOrFail();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function subscription(): HasOne
    {
        // Return the latest subscription, but prioritize 'active' status over others
        return $this->hasOne(Subscription::class)
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->latest('id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        // Get the latest ACTIVE subscription (not expired, not cancelled beyond grace period)
        return $this->hasOne(Subscription::class)
            ->where(function ($query) {
                $query->where('status', 'active')
                    ->orWhere(function ($subQuery) {
                        // Include cancelled subscriptions still within paid period
                        $subQuery->where('status', 'cancelled')
                            ->where('ends_at', '>', now());
                    });
            })
            ->where(function ($query) {
                // Ensure subscription hasn't expired
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->latest('id');
    }

    public function getPlanAttribute()
    {
        return $this->activeSubscription ? $this->activeSubscription->plan : null;
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function saasPayments(): HasMany
    {
        return $this->hasMany(SaasPayment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function emailTemplates(): HasMany
    {
        return $this->hasMany(EmailTemplate::class);
    }

    /**
     * Check if salon is active (has active subscription and is not suspended)
     */
    public function isActive(): bool
    {
        $subscription = $this->activeSubscription;
        return $this->is_active && ($subscription ? $subscription->isActive() : false);
    }

    public function currentSubscription()
    {
        return $this->activeSubscription;
    }

    /**
     * Get active subscription as a property accessor
     * This allows $salon->activeSubscription to work in views
     */
    public function getActiveSubscriptionAttribute()
    {
        return $this->activeSubscription()->first();
    }

    /**
     * Check if salon can use a specific feature
     * During active trial, all features are accessible.
     */
    public function canUseFeature(string $feature): bool
    {
        // During active trial, grant access to all features
        if ($this->isOnTrial()) {
            return true;
        }

        $subscription = $this->currentSubscription();

        if (!$subscription || !$subscription->plan) {
            return false;
        }

        return $subscription->plan->hasFeature($feature);
    }

    /**
     * Get feature limit for salon
     */
    public function getFeatureLimit(string $feature): ?int
    {
        $subscription = $this->currentSubscription();

        if (!$subscription || !$subscription->plan) {
            return 0;
        }

        return $subscription->plan->getLimit($feature);
    }

    /**
     * Check if feature limit is exceeded
     */
    public function isFeatureLimitExceeded(string $feature): bool
    {
        $subscription = $this->currentSubscription();

        if (!$subscription) {
            return true;
        }

        return $subscription->isLimitExceeded($feature);
    }

    /**
     * Check if salon is on trial
     */
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture() && !$this->currentSubscription();
    }

    /**
     * Get days left in trial
     */
    public function trialDaysLeft(): ?int
    {
        if (!$this->isOnTrial()) {
            return null;
        }

        return (int) Carbon::now()->diffInDays($this->trial_ends_at, false);
    }

    /**
     * Check if salon setup is completed
     */
    public function isSetupCompleted(): bool
    {
        return !is_null($this->setup_completed_at);
    }

    /**
     * Mark setup as completed
     */
    public function completeSetup(): void
    {
        $this->update(['setup_completed_at' => now()]);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get total revenue for salon
     */
    public function getTotalRevenue(): float
    {
        return $this->payments()->where('type', 'income')->sum('amount');
    }

    /**
     * Get monthly revenue
     */
    public function getMonthlyRevenue(): float
    {
        return $this->payments()
            ->where('type', 'income')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
    }

    /**
     * Get active customer count
     */
    public function getActiveCustomerCount(): int
    {
        return $this->customers()
            ->where('last_visit_at', '>=', now()->subMonths(3))
            ->count();
    }

    /**
     * Scope for active salons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for salons with expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->whereDoesntHave('subscription', function ($q) {
            $q->active();
        });
    }

    /**
     * Scope for salons on trial
     */
    public function scopeOnTrial($query)
    {
        return $query->where('trial_ends_at', '>', now())
            ->whereDoesntHave('subscription');
    }

    /**
     * Get staff limit from active plan
     */
    public function getStaffLimit(): ?int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription || !$subscription->plan) {
            \Log::info('getStaffLimit: No subscription or plan', ['salon_id' => $this->id]);
            return null; // Unlimited if no subscription
        }

        $limit = $subscription->plan->max_users;

        \Log::info('getStaffLimit debug', [
            'salon_id' => $this->id,
            'plan_name' => $subscription->plan->name,
            'limit' => $limit,
            'type' => gettype($limit),
            'is_unlimited' => ($limit === null || $limit === -1)
        ]);

        // Return null for unlimited, otherwise return the actual limit
        if ($limit === null) {
            return null;
        }

        return $limit;
    }

    /**
     * Get branch limit from active plan
     */
    public function getBranchLimit(): ?int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription || !$subscription->plan) {
            return null; // Unlimited if no subscription
        }
        return $subscription->plan->max_branches;
    }

    /**
     * Get customer limit from active plan
     */
    public function getCustomerLimit(): ?int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription || !$subscription->plan) {
            return null; // Unlimited if no subscription
        }
        return $subscription->plan->getLimit('max_customers');
    }

    /**
     * Check if salon can add more staff
     * Counts all non-customer users (employees, managers, admins, etc.)
     */
    public function canAddStaff(): bool
    {
        $limit = $this->getStaffLimit();
        if ($limit === null || $limit === -1) {
            return true; // Unlimited
        }

        // Count all users who are NOT customers (matches Subscription model logic)
        $currentCount = $this->users()->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'customer');
        })->count();

        return $currentCount < $limit;
    }

    /**
     * Check if salon can add more branches
     */
    public function canAddBranch(): bool
    {
        $limit = $this->getBranchLimit();
        if ($limit === null) {
            return true; // Unlimited
        }
        $currentCount = $this->branches()->count();
        return $currentCount < $limit;
    }

    /**
     * Check if salon's plan includes branch module
     */
    public function hasBranchModule(): bool
    {
        $subscription = $this->activeSubscription;
        if (!$subscription || !$subscription->plan) {
            return false; // No branch module without subscription
        }

        // Check multiple possible feature names for compatibility
        return $subscription->plan->hasFeature('Multi-Branch Support') ||
            $subscription->plan->hasFeature('multi_branch') ||
            $subscription->plan->hasFeature('branches');
    }

    /**
     * Check if salon can add more customers
     */
    public function canAddCustomer(): bool
    {
        $limit = $this->getCustomerLimit();
        if ($limit === null) {
            return true; // Unlimited
        }
        $currentCount = $this->customers()->count();
        return $currentCount < $limit;
    }

    /**
     * Get booking limit per month from active plan
     */
    public function getBookingLimit(): ?int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription || !$subscription->plan) {
            return null; // Unlimited if no subscription
        }
        return $subscription->plan->getLimit('max_bookings_per_month');
    }

    /**
     * Get service limit from active plan
     */
    public function getServiceLimit(): ?int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription || !$subscription->plan) {
            return null; // Unlimited if no subscription
        }
        return $subscription->plan->getLimit('max_services');
    }

    /**
     * Get service category limit from active plan
     */
    public function getServiceCategoryLimit(): ?int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription || !$subscription->plan) {
            return null; // Unlimited if no subscription
        }
        return $subscription->plan->getLimit('max_service_categories');
    }

    /**
     * Get product limit from active plan
     */
    public function getProductLimit(): ?int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription || !$subscription->plan) {
            return null; // Unlimited if no subscription
        }
        return $subscription->plan->getLimit('max_products');
    }

    /**
     * Get membership limit from active plan
     */
    public function getMembershipLimit(): ?int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription || !$subscription->plan) {
            return null; // Unlimited if no subscription
        }
        return $subscription->plan->getLimit('max_memberships');
    }

    /**
     * Get package limit from active plan
     */
    public function getPackageLimit(): ?int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription || !$subscription->plan) {
            return null; // Unlimited if no subscription
        }
        return $subscription->plan->getLimit('max_packages');
    }

    /**
     * Get guest booking limit per month from active plan
     */
    public function getGuestBookingLimit(): ?int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription || !$subscription->plan) {
            return null; // Unlimited if no subscription
        }
        return $subscription->plan->getLimit('max_guest_bookings_per_month');
    }

    /**
     * Check if salon can add more bookings this month
     */
    public function canAddBooking(): bool
    {
        $limit = $this->getBookingLimit();
        if ($limit === null) {
            return true; // Unlimited
        }
        $currentCount = $this->bookings()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        return $currentCount < $limit;
    }

    /**
     * Check if salon can add more services
     */
    public function canAddService(): bool
    {
        $limit = $this->getServiceLimit();
        if ($limit === null) {
            return true; // Unlimited
        }
        // Only count salon-specific services, not global ones (salon_id = null)
        $currentCount = $this->services()->whereNotNull('salon_id')->count();

        \Log::info('canAddService check', [
            'salon_id' => $this->id,
            'limit' => $limit,
            'current_count' => $currentCount,
            'total_services' => $this->services()->count(),
            'can_add' => $currentCount < $limit
        ]);

        return $currentCount < $limit;
    }

    /**
     * Check if salon can add more service categories
     */
    public function canAddServiceCategory(): bool
    {
        $limit = $this->getServiceCategoryLimit();
        if ($limit === null) {
            return true; // Unlimited
        }
        // Only count salon-specific categories
        $currentCount = ServiceCategory::where('salon_id', $this->id)->count();

        return $currentCount < $limit;
    }

    /**
     * Check if salon can add more products
     */
    public function canAddProduct(): bool
    {
        $limit = $this->getProductLimit();
        if ($limit === null) {
            return true; // Unlimited
        }
        // Only count salon-specific products, not global ones (salon_id = null)
        $currentCount = $this->products()->whereNotNull('salon_id')->count();
        return $currentCount < $limit;
    }

    /**
     * Check if salon can add more memberships
     */
    public function canAddMembership(): bool
    {
        $limit = $this->getMembershipLimit();
        if ($limit === null) {
            return true; // Unlimited
        }
        $currentCount = $this->memberships()->count();
        return $currentCount < $limit;
    }

    /**
     * Check if salon can add more packages
     */
    public function canAddPackage(): bool
    {
        $limit = $this->getPackageLimit();
        if ($limit === null) {
            return true; // Unlimited
        }
        // Only count salon-specific packages, not global ones (salon_id = null)
        $currentCount = $this->packages()->whereNotNull('salon_id')->count();
        return $currentCount < $limit;
    }

    /**
     * Check if salon can add more guest bookings this month
     */
    public function canAddGuestBooking(): bool
    {
        $limit = $this->getGuestBookingLimit();
        if ($limit === null) {
            return true; // Unlimited
        }
        $currentCount = $this->bookings()
            ->whereHas('customer', function ($query) {
                $query->where('is_guest', true);
            })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        return $currentCount < $limit;
    }
    /**
     * Get the salon's logo URL, with a default fallback.
     */
    public function getLogoUrlAttribute(): string
    {
        return app(\App\Services\SettingsService::class)->getLogoUrl($this->id);
    }

    /**
     * Get the salon's favicon URL, with a default fallback.
     */
    public function getFaviconUrlAttribute(): string
    {
        return app(\App\Services\SettingsService::class)->getFaviconUrl();
    }
}
