<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LoyaltyTransaction;
use App\Models\PosSale;
use App\Models\Booking;
use App\Models\Salon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoyaltyService
{
    protected SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Resolve the current salon context.
     */
    protected function resolveSalon(?Salon $salon = null): ?Salon
    {
        if ($salon) {
            return $salon;
        }

        if (app()->bound('current_salon') && app('current_salon')) {
            return app('current_salon');
        }

        $user = auth()->user();
        return $user?->salon ?? ($user?->salon_id ? Salon::find($user->salon_id) : null);
    }

    /**
     * Check if loyalty program is active for the salon (both plan feature and salon toggle enabled).
     */
    public function isEnabled(?Salon $salon = null): bool
    {
        $salon = $this->resolveSalon($salon);
        if (!$salon) {
            return false;
        }

        // Must be included in SaaS plan (or active trial)
        $isOnTrial = method_exists($salon, 'isOnTrial') && $salon->isOnTrial();
        if (!$salon->hasLoyaltyProgram() && !$isOnTrial) {
            return false;
        }

        // Must be enabled in salon settings
        $settings = $this->getSettings($salon);
        return !empty($settings['enabled']);
    }

    /**
     * Get loyalty settings for a salon.
     */
    public function getSettings(?Salon $salon = null): array
    {
        $salon = $this->resolveSalon($salon);
        $salonId = $salon?->id;

        $rawEnabled = $this->settingsService->get('loyalty_enabled', null, $salonId);
        $enabled = ($rawEnabled === null || $rawEnabled === '') ? true : filter_var($rawEnabled, FILTER_VALIDATE_BOOLEAN);
        $pointsPerCurrency = (int) $this->settingsService->get('loyalty_points_per_currency', 1, $salonId);
        $redemptionRate = (float) $this->settingsService->get('loyalty_point_redemption_rate', 0.05, $salonId);
        $minPoints = (int) $this->settingsService->get('loyalty_min_points_to_redeem', 50, $salonId);
        $maxDiscountPct = (int) $this->settingsService->get('loyalty_max_discount_percentage', 50, $salonId);
        $expiryMonths = (int) $this->settingsService->get('loyalty_points_expiry_months', 12, $salonId);

        return [
            'enabled' => $enabled,
            'points_per_currency' => max(1, $pointsPerCurrency),
            'redemption_rate' => max(0.0001, $redemptionRate),
            'min_points_to_redeem' => max(0, $minPoints),
            'max_discount_percentage' => min(100, max(1, $maxDiscountPct)),
            'points_expiry_months' => max(0, $expiryMonths),
        ];
    }

    /**
     * Calculate allowable redemption for a customer on a given subtotal.
     */
    public function calculateMaxRedeemable(Customer $customer, float $subtotal, ?Salon $salon = null): array
    {
        $salon = $this->resolveSalon($salon);
        $settings = $this->getSettings($salon);

        if (!$this->isEnabled($salon)) {
            return [
                'can_redeem' => false,
                'reason' => 'Loyalty program is not active.',
                'available_points' => (int) $customer->loyalty_points,
                'max_points' => 0,
                'max_discount' => 0.00,
            ];
        }

        $availablePoints = (int) ($customer->loyalty_points ?? 0);
        if ($availablePoints < $settings['min_points_to_redeem']) {
            return [
                'can_redeem' => false,
                'reason' => "Minimum {$settings['min_points_to_redeem']} points required to redeem.",
                'available_points' => $availablePoints,
                'max_points' => 0,
                'max_discount' => 0.00,
            ];
        }

        // Max discount allowed by bill percentage
        $maxDiscountByPct = round(($subtotal * $settings['max_discount_percentage']) / 100, 2);

        // Max discount customer's points can afford
        $maxDiscountByPoints = round($availablePoints * $settings['redemption_rate'], 2);

        // Effective max discount is the lower of the two
        $maxDiscount = min($subtotal, min($maxDiscountByPct, $maxDiscountByPoints));

        // Exact points needed for that discount (floor so discount never exceeds configured percentage limit)
        $maxPoints = (int) floor(($maxDiscount + 1e-9) / $settings['redemption_rate']);
        $maxPoints = min($availablePoints, max(0, $maxPoints));
        $maxDiscount = round($maxPoints * $settings['redemption_rate'], 2);

        return [
            'can_redeem' => $maxDiscount > 0 && $maxPoints > 0,
            'reason' => null,
            'available_points' => $availablePoints,
            'max_points' => $maxPoints,
            'max_discount' => $maxDiscount,
            'redemption_rate' => $settings['redemption_rate'],
        ];
    }

    /**
     * Redeem customer points and log the deduction ledger.
     */
    public function redeemPoints(
        Customer $customer,
        int $pointsToRedeem,
        float $discountAmount,
        ?PosSale $posSale = null,
        ?Booking $booking = null,
        ?User $creator = null,
        ?string $notes = null
    ): LoyaltyTransaction {
        if ($pointsToRedeem <= 0) {
            throw new \InvalidArgumentException('Points to redeem must be a positive integer.');
        }

        $salon = $this->resolveSalon($customer->salon);
        $settings = $this->getSettings($salon);

        return DB::transaction(function () use ($customer, $pointsToRedeem, $discountAmount, $settings, $posSale, $booking, $creator, $notes) {
            // Lock customer row to prevent concurrent double-spending
            $lockedCustomer = Customer::where('id', $customer->id)->lockForUpdate()->firstOrFail();

            if ((int) $lockedCustomer->loyalty_points < $pointsToRedeem) {
                throw new \Exception("Customer has insufficient points ({$lockedCustomer->loyalty_points}) to redeem {$pointsToRedeem} points.");
            }

            // Decrement points atomically
            $lockedCustomer->decrement('loyalty_points', $pointsToRedeem);
            $customer->loyalty_points = $lockedCustomer->fresh()->loyalty_points;

            // Log ledger
            return LoyaltyTransaction::create([
                'salon_id' => $lockedCustomer->salon_id,
                'customer_id' => $lockedCustomer->id,
                'pos_sale_id' => $posSale?->id,
                'booking_id' => $booking?->id,
                'points' => -$pointsToRedeem,
                'type' => 'redeemed',
                'conversion_rate' => $settings['redemption_rate'],
                'discount_amount' => $discountAmount,
                'notes' => $notes ?? ("Redeemed {$pointsToRedeem} points for " . currency_symbol() . number_format($discountAmount, 2) . " discount"),
                'created_by' => $creator?->id ?? auth()->id(),
            ]);
        });
    }

    /**
     * Award loyalty points to a customer based on net paid amount.
     */
    public function awardPoints(
        Customer $customer,
        float $netAmountPaid,
        ?PosSale $posSale = null,
        ?Booking $booking = null,
        ?User $creator = null,
        ?string $notes = null
    ): ?LoyaltyTransaction {
        $salon = $this->resolveSalon($customer->salon);
        if (!$this->isEnabled($salon) || $netAmountPaid <= 0) {
            return null;
        }

        $settings = $this->getSettings($salon);
        $targetTotalPoints = (int) floor($netAmountPaid * $settings['points_per_currency']);

        if ($targetTotalPoints <= 0) {
            return null;
        }

        return DB::transaction(function () use ($customer, $targetTotalPoints, $netAmountPaid, $settings, $posSale, $booking, $creator, $notes) {
            $lockedCustomer = Customer::where('id', $customer->id)->lockForUpdate()->firstOrFail();
            $pointsToAward = $targetTotalPoints;

            if ($posSale) {
                // Lock sale row so concurrent/replayed submissions reconcile cumulatively
                PosSale::where('id', $posSale->id)->lockForUpdate()->first();
                $alreadyEarned = (int) LoyaltyTransaction::where('pos_sale_id', $posSale->id)
                    ->where('type', 'earned')
                    ->sum('points');
                $pointsToAward = $targetTotalPoints - $alreadyEarned;

                if ($pointsToAward <= 0) {
                    return null;
                }
            }

            // Increment customer balance atomically
            $lockedCustomer->increment('loyalty_points', $pointsToAward);
            $customer->loyalty_points = $lockedCustomer->fresh()->loyalty_points;

            // Record transaction
            return LoyaltyTransaction::create([
                'salon_id' => $lockedCustomer->salon_id,
                'customer_id' => $lockedCustomer->id,
                'pos_sale_id' => $posSale?->id,
                'booking_id' => $booking?->id,
                'points' => $pointsToAward,
                'type' => 'earned',
                'conversion_rate' => $settings['redemption_rate'],
                'discount_amount' => 0.00,
                'notes' => $notes ?? ("Earned {$pointsToAward} points on purchase of " . currency_symbol() . number_format($netAmountPaid, 2)),
                'created_by' => $creator?->id ?? auth()->id(),
            ]);
        });
    }

    /**
     * Reverse earned and redeemed loyalty points for a voided or fully refunded POS sale.
     */
    public function reverseSaleLoyalty(PosSale $sale, string $reason = 'Voided/Refunded'): void
    {
        if (!$sale->customer_id) {
            return;
        }

        DB::transaction(function () use ($sale, $reason) {
            PosSale::where('id', $sale->id)->lockForUpdate()->first();
            $customer = Customer::where('id', $sale->customer_id)->lockForUpdate()->first();
            if (!$customer) {
                return;
            }

            $transactions = LoyaltyTransaction::where('pos_sale_id', $sale->id)->get();
            if ($transactions->isEmpty()) {
                return;
            }

            // Guard against duplicate reversals
            $alreadyReversed = $transactions->where('type', 'adjusted')->isNotEmpty();
            if ($alreadyReversed) {
                return;
            }

            $earnedTotal = (int) $transactions->where('type', 'earned')->sum('points');
            $redeemedTotal = (int) abs($transactions->where('type', 'redeemed')->sum('points'));

            // Reverse earned points (subtract up to current balance or full earned amount)
            if ($earnedTotal > 0) {
                $deduct = min($earnedTotal, max(0, (int) $customer->loyalty_points));
                if ($deduct > 0) {
                    $customer->decrement('loyalty_points', $deduct);
                }
                LoyaltyTransaction::create([
                    'salon_id' => $sale->salon_id,
                    'customer_id' => $customer->id,
                    'pos_sale_id' => $sale->id,
                    'points' => -$earnedTotal,
                    'type' => 'adjusted',
                    'conversion_rate' => $transactions->where('type', 'earned')->first()?->conversion_rate ?? 0.05,
                    'discount_amount' => 0.00,
                    'notes' => "{$reason} Sale #{$sale->invoice_number}: reversed {$earnedTotal} earned points",
                    'created_by' => auth()->id(),
                ]);
            }

            // Restore redeemed points
            if ($redeemedTotal > 0) {
                $customer->increment('loyalty_points', $redeemedTotal);
                LoyaltyTransaction::create([
                    'salon_id' => $sale->salon_id,
                    'customer_id' => $customer->id,
                    'pos_sale_id' => $sale->id,
                    'points' => $redeemedTotal,
                    'type' => 'adjusted',
                    'conversion_rate' => $transactions->where('type', 'redeemed')->first()?->conversion_rate ?? 0.05,
                    'discount_amount' => 0.00,
                    'notes' => "{$reason} Sale #{$sale->invoice_number}: restored {$redeemedTotal} redeemed points",
                    'created_by' => auth()->id(),
                ]);
            }
        });
    }
}
