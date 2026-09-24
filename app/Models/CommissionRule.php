<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToSalon;

class CommissionRule extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'commission_profile_id',
        'item_type',
        'item_id',
        'commission_type',
        'commission_value',
        'target_amount',
        'target_from',
        'target_to',
    ];

    protected $casts = [
        'commission_value' => 'decimal:2',
        'target_amount' => 'decimal:2',
        'target_from' => 'decimal:2',
        'target_to' => 'decimal:2',
    ];

    /**
     * Get the commission profile that owns the rule.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(CommissionProfile::class, 'commission_profile_id');
    }

    /**
     * Get the related item (polymorphic).
     */
    public function item()
    {
        if (!$this->item_type || !$this->item_id) {
            return null;
        }

        return match ($this->item_type) {
            'service' => Service::find($this->item_id),
            'product' => Product::find($this->item_id),
            'membership' => Membership::find($this->item_id),
            'package' => Package::find($this->item_id),
            default => null,
        };
    }

    /**
     * Calculate commission for a given sale amount.
     */
    public function calculateCommission(float $saleAmount, bool $includeTax = false): float
    {
        $amount = $saleAmount;

        // If profile doesn't include tax and we have tax, subtract it
        if (!$includeTax && $this->profile && !$this->profile->include_tax) {
            // Get salon tax rate from settings
            try {
                $salon = $this->profile->salon;
                if ($salon) {
                    $settingsService = app(\App\Services\SettingsService::class);
                    $taxEnabled = false;
                    if ($this->item_type === 'service') {
                        $taxEnabled = $settingsService->get('tax_enabled_services', false, $salon->id);
                    } else {
                        // For products, packages, memberships
                        $taxEnabled = $settingsService->get('tax_enabled_pos', false, $salon->id);
                    }

                    if ($taxEnabled) {
                        $taxRate = $settingsService->get('tax_rate', 0, $salon->id);

                        if ($taxRate > 0) {
                            // Remove tax from amount: amount_without_tax = amount_with_tax / (1 + tax_rate)
                            $amount = $amount / (1 + ($taxRate / 100));
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to get tax rate for commission calculation', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        if ($this->commission_type === 'percentage') {
            return ($amount * $this->commission_value) / 100;
        }

        return $this->commission_value;
    }

    /**
     * Check if this rule applies to all items of a type.
     */
    public function appliesToAll(): bool
    {
        return $this->item_id === null;
    }
}
