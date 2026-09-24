<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\CommissionProfile;
use App\Models\CommissionRule;
use App\Models\StaffCommission;
use App\Models\User;
use App\Models\PosSaleItem;

class CommissionService
{
    /**
     * Calculate and create commission for a booking.
     */
    public function calculateBookingCommission(Booking $booking): ?StaffCommission
    {
        // Validate booking status - only calculate for completed bookings
        if ($booking->status !== 'completed') {
            \Log::info("Commission calculation skipped: Booking not completed", [
                'booking_id' => $booking->id,
                'status' => $booking->status
            ]);
            return null;
        }

        if (!$booking->staff_id || !$booking->service_id) {
            return null;
        }

        // Prevent duplicate commissions
        $existing = StaffCommission::where('booking_id', $booking->id)->first();
        if ($existing) {
            \Log::warning("Commission already exists for booking", [
                'booking_id' => $booking->id,
                'commission_id' => $existing->id
            ]);
            return $existing;
        }

        // Validate amount
        if ($booking->amount <= 0) {
            \Log::warning("Invalid booking amount for commission calculation", [
                'booking_id' => $booking->id,
                'amount' => $booking->amount
            ]);
            return null;
        }

        $staff = User::find($booking->staff_id);
        if (!$staff || !$staff->commission_profile_id) {
            return null;
        }

        // Validate salon scope
        if ($staff->salon_id !== $booking->salon_id) {
            throw new \App\Exceptions\CommissionCalculationException(
                "Commission calculation failed: Staff salon mismatch. Booking ID: {$booking->id}, Staff ID: {$staff->id}"
            );
        }

        $profile = CommissionProfile::with('rules')->find($staff->commission_profile_id);
        if (!$profile || !$profile->is_active) {
            return null;
        }

        // Validate profile salon scope
        if ($profile->salon_id !== $booking->salon_id) {
            throw new \App\Exceptions\CommissionCalculationException(
                "Commission calculation failed: Profile salon mismatch. Booking ID: {$booking->id}, Profile ID: {$profile->id}"
            );
        }

        if ($profile->type === 'by_target') {
            $this->updateTargetCommissionsForStaff($booking->staff_id, $booking->updated_at);
            return null;
        }

        // Find applicable rule for this service
        $rule = $this->findApplicableRule($profile, 'service', $booking->service_id);
        if (!$rule) {
            return null;
        }

        $saleAmount = $booking->amount;
        $commissionAmount = $rule->calculateCommission($saleAmount, $profile->include_tax);

        return StaffCommission::create([
            'salon_id' => $booking->salon_id,
            'branch_id' => $booking->branch_id,
            'staff_id' => $booking->staff_id,
            'booking_id' => $booking->id,
            'item_type' => 'service',
            'item_id' => $booking->service_id,
            'sale_amount' => $saleAmount,
            'commission_amount' => $commissionAmount,
            'commission_profile_id' => $profile->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Calculate commission for a POS sale item.
     */
    public function calculatePosSaleCommission($posSale, $itemType, $itemId, $staffId, $amount): ?StaffCommission
    {
        $staff = User::find($staffId);
        if (!$staff || !$staff->commission_profile_id) {
            return null;
        }

        // Validate salon scope
        if ($staff->salon_id !== $posSale->salon_id) {
            \Log::warning("Commission calculation skipped: Staff salon mismatch (POS)", [
                'pos_sale_id' => $posSale->id,
                'staff_id' => $staff->id,
                'sale_salon' => $posSale->salon_id,
                'staff_salon' => $staff->salon_id
            ]);
            return null;
        }

        $profile = CommissionProfile::with('rules')->find($staff->commission_profile_id);
        if (!$profile || !$profile->is_active) {
            return null;
        }

        // Validate profile salon scope
        if ($profile->salon_id !== $posSale->salon_id) {
            \Log::warning("Commission calculation skipped: Profile salon mismatch (POS)", [
                'pos_sale_id' => $posSale->id,
                'profile_id' => $profile->id,
                'sale_salon' => $posSale->salon_id,
                'profile_salon' => $profile->salon_id
            ]);
            return null;
        }

        if ($profile->type === 'by_target') {
            $this->updateTargetCommissionsForStaff($staffId, $posSale->updated_at);
            return null;
        }


        $rule = $this->findApplicableRule($profile, $itemType, $itemId);
        if (!$rule) {
            return null;
        }

        $commissionAmount = $rule->calculateCommission($amount, $profile->include_tax);

        return StaffCommission::create([
            'salon_id' => $posSale->salon_id,
            'branch_id' => $posSale->branch_id,
            'staff_id' => $staffId,
            'pos_sale_id' => $posSale->id,
            'item_type' => $itemType,
            'item_id' => $itemId,
            'sale_amount' => $amount,
            'commission_amount' => $commissionAmount,
            'commission_profile_id' => $profile->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Find the applicable commission rule for an item.
     */
    protected function findApplicableRule(CommissionProfile $profile, string $itemType, ?int $itemId): ?CommissionRule
    {
        // First, try to find a specific rule for this item
        $specificRule = $profile->rules()
            ->where('item_type', $itemType)
            ->where('item_id', $itemId)
            ->first();

        if ($specificRule) {
            return $specificRule;
        }

        // If no specific rule, look for a general rule for this item type
        $generalRule = $profile->rules()
            ->where('item_type', $itemType)
            ->whereNull('item_id')
            ->first();

        return $generalRule;
    }

    /**
     * Calculate total commissions for a staff member in a date range.
     */
    public function getStaffCommissionSummary(int $staffId, $startDate = null, $endDate = null): array
    {
        $query = StaffCommission::forStaff($staffId);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $commissions = $query->get();

        return [
            'total_pending' => $commissions->where('status', 'pending')->sum('commission_amount'),
            'total_approved' => $commissions->where('status', 'approved')->sum('commission_amount'),
            'total_paid' => $commissions->where('status', 'paid')->sum('commission_amount'),
            'total_all' => $commissions->sum('commission_amount'),
            'count_pending' => $commissions->where('status', 'pending')->count(),
            'count_approved' => $commissions->where('status', 'approved')->count(),
            'count_paid' => $commissions->where('status', 'paid')->count(),
            'count_all' => $commissions->count(),
        ];
    }

    /**
     * Approve multiple commissions.
     */
    public function approveCommissions(array $commissionIds): int
    {
        return StaffCommission::whereIn('id', $commissionIds)
            ->where('status', 'pending')
            ->update(['status' => 'approved']);
    }

    /**
     * Mark multiple commissions as paid.
     */
    public function markCommissionsAsPaid(array $commissionIds): int
    {
        return StaffCommission::whereIn('id', $commissionIds)
            ->where('status', 'approved')
            ->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
    }

    private function mapModelToItemType(string $modelClass): ?string
    {
        if ($modelClass === 'App\Models\Service') return 'service';
        if ($modelClass === 'App\Models\InventoryItem') return 'product';
        if ($modelClass === 'App\Models\Membership') return 'membership';
        if ($modelClass === 'App\Models\Package') return 'package';
        return null;
    }

    public function getCommissionableAmount(float $saleAmount, string $itemType, bool $includeTax, int $salonId): float
    {
        $amount = $saleAmount;
        if (!$includeTax) {
            try {
                $settingsService = app(\App\Services\SettingsService::class);
                $taxEnabled = false;
                if ($itemType === 'service') {
                    $taxEnabled = $settingsService->get('tax_enabled_services', false, $salonId);
                } else {
                    $taxEnabled = $settingsService->get('tax_enabled_pos', false, $salonId);
                }

                if ($taxEnabled) {
                    $taxRate = $settingsService->get('tax_rate', 0, $salonId);
                    if ($taxRate > 0) {
                        $amount = $amount / (1 + ($taxRate / 100));
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to get tax rate for commissionable amount calculation', [
                    'error' => $e->getMessage()
                ]);
            }
        }
        return $amount;
    }

    public function calculateTargetCommissionAmount(float $totalSales, $profile): float
    {
        $rules = $profile->rules()->orderBy('target_from', 'asc')->get();
        if ($rules->isEmpty()) {
            return 0.00;
        }

        $highestSlab = null;
        foreach ($rules as $rule) {
            if ($totalSales >= $rule->target_from) {
                $highestSlab = $rule;
            }
        }

        if (!$highestSlab) {
            return 0.00;
        }

        if ($profile->is_cascade) {
            if ($highestSlab->commission_type === 'percentage') {
                return ($totalSales * $highestSlab->commission_value) / 100;
            } else {
                return $highestSlab->commission_value;
            }
        } else {
            $commission = 0.00;
            foreach ($rules as $rule) {
                if ($totalSales <= $rule->target_from) {
                    continue;
                }

                $from = $rule->target_from;
                $to = $rule->target_to ?? INF;

                $slabSales = min($totalSales, $to) - $from;
                if ($slabSales <= 0) {
                    continue;
                }

                if ($rule->commission_type === 'percentage') {
                    $commission += ($slabSales * $rule->commission_value) / 100;
                } else {
                    $commission += $rule->commission_value;
                }
            }
            return $commission;
        }
    }

    public function updateTargetCommissionsForStaff(int $staffId, $date = null): void
    {
        $date = $date ? \Carbon\Carbon::parse($date) : now();
        $staff = User::find($staffId);
        if (!$staff || !$staff->commission_profile_id) {
            return;
        }

        $profile = CommissionProfile::with('rules')->find($staff->commission_profile_id);
        if (!$profile || !$profile->is_active || $profile->type !== 'by_target') {
            return;
        }

        $interval = $profile->calculation_interval ?? 'monthly';
        $periodStart = null;
        $periodEnd = null;

        if ($interval === 'daily') {
            $periodStart = $date->copy()->startOfDay();
            $periodEnd = $date->copy()->endOfDay();
        } elseif ($interval === 'weekly') {
            $periodStart = $date->copy()->startOfWeek();
            $periodEnd = $date->copy()->endOfWeek();
        } else { // monthly
            $periodStart = $date->copy()->startOfMonth();
            $periodEnd = $date->copy()->endOfMonth();
        }

        $salonId = $staff->salon_id;
        $qualifyingItem = $profile->qualifying_item ?? 'all';
        $includeTax = $profile->include_tax;

        // Sum booking sales
        $bookingSalesQuery = Booking::where('staff_id', $staffId)
            ->where('salon_id', $salonId)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$periodStart, $periodEnd]);

        $bookingSales = $bookingSalesQuery->get();
        $totalSales = 0.00;

        foreach ($bookingSales as $booking) {
            if ($qualifyingItem === 'all' || $qualifyingItem === 'service') {
                $totalSales += $this->getCommissionableAmount((float)$booking->amount, 'service', $includeTax, $salonId);
            }
        }

        // Sum POS sales
        $posSaleItemsQuery = PosSaleItem::where('staff_id', $staffId)
            ->where('salon_id', $salonId)
            ->whereHas('sale', function ($query) use ($periodStart, $periodEnd) {
                $query->where('payment_status', 'paid')
                      ->whereBetween('updated_at', [$periodStart, $periodEnd]);
            });

        $posSaleItems = $posSaleItemsQuery->get();

        foreach ($posSaleItems as $item) {
            $itemType = $this->mapModelToItemType($item->item_type);
            if ($itemType && ($qualifyingItem === 'all' || $qualifyingItem === $itemType)) {
                $totalSales += $this->getCommissionableAmount((float)$item->subtotal, $itemType, $includeTax, $salonId);
            }
        }

        // Calculate commission amount
        $commissionAmount = $this->calculateTargetCommissionAmount($totalSales, $profile);

        // Save target commission record
        $existing = StaffCommission::where('staff_id', $staffId)
            ->where('salon_id', $salonId)
            ->where('item_type', 'target')
            ->where('period_start', $periodStart->toDateString())
            ->where('period_end', $periodEnd->toDateString())
            ->first();

        if ($existing) {
            if ($existing->status === 'pending') {
                if ($commissionAmount > 0) {
                    $existing->update([
                        'sale_amount' => $totalSales,
                        'commission_amount' => $commissionAmount,
                    ]);
                } else {
                    $existing->delete();
                }
            } else {
                \Log::warning("Target commission already approved/paid for period", [
                    'staff_id' => $staffId,
                    'period' => $periodStart->toDateString() . ' to ' . $periodEnd->toDateString(),
                    'status' => $existing->status
                ]);
            }
        } else {
            if ($commissionAmount > 0) {
                StaffCommission::create([
                    'salon_id' => $salonId,
                    'branch_id' => $staff->branch_id,
                    'staff_id' => $staffId,
                    'item_type' => 'target',
                    'item_id' => 0,
                    'sale_amount' => $totalSales,
                    'commission_amount' => $commissionAmount,
                    'commission_profile_id' => $profile->id,
                    'status' => 'pending',
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                ]);
            }
        }
    }
}
