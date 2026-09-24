<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\BelongsToSalon;
use App\Traits\BelongsToBranch;

/**
 * PosSaleItem Model
 * 
 * Represents individual items in a POS sale.
 * Multi-tenancy: Scoped to salon via BelongsToSalon trait
 */
class PosSaleItem extends Model
{
    use BelongsToSalon, BelongsToBranch;

    protected $fillable = [
        'salon_id',
        'branch_id',
        'sale_id',
        'item_id',
        'package_id',
        'item_name',
        'item_type',
        'quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'subtotal',
        'discount_amount',
        'total',
        'notes',
        'staff_id',
        'booking_id'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the sale that owns the item.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class, 'sale_id');
    }

    /**
     * Get the polymorphic item associated with the sale item.
     */
    public function item(): MorphTo
    {
        return $this->morphTo('item', 'item_type', 'item_id');
    }

    /**
     * Get the service associated with the sale item.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'item_id');
    }

    /**
     * Get the product associated with the sale item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    /**
     * Get the package this item belongs to, if any.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    /**
     * Get the booking associated with this sale item.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Get the staff assigned to this sale item.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Calculate the subtotal for the item.
     */
    public function calculateSubtotal(): float
    {
        return round($this->quantity * $this->unit_price, 2);
    }

    /**
     * Calculate the total for the item including tax and discount.
     */
    public function calculateTotal(): float
    {
        $subtotal = $this->calculateSubtotal();
        $total = $subtotal + $this->tax_amount - $this->discount_amount;
        return round($total, 2);
    }

    /**
     * Get the formatted unit price.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return format_currency((float) $this->unit_price);
    }

    /**
     * Get the formatted subtotal.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return format_currency((float) $this->subtotal);
    }

    /**
     * Get the formatted total.
     */
    public function getFormattedTotalAttribute(): string
    {
        return format_currency((float) $this->total);
    }
}
