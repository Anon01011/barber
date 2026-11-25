<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToSalon;

class ServiceInventoryItem extends Model
{
    use HasFactory, BelongsToSalon;

    protected $table = 'service_inventory_items';

    protected $fillable = [
        'salon_id',
        'service_id',
        'inventory_item_id',
        'quantity',
        'unit',
        'is_optional',
        'is_billable_extra',
        'extra_price_per_unit',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'is_optional' => 'boolean',
        'is_billable_extra' => 'boolean',
        'extra_price_per_unit' => 'decimal:2',
    ];

    /**
     * The service that owns this recipe item.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * The inventory item consumed.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    /**
     * Calculate the estimated cost for this line item based on current purchase price.
     */
    public function getEstimatedCostAttribute(): float
    {
        if (!$this->inventoryItem) {
            return 0.0;
        }

        $costPerUnit = $this->inventoryItem->cost_per_unit;
        return round($costPerUnit * (float) $this->quantity, 4);
    }
}
