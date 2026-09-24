<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\BelongsToSalon;

class ProductVariant extends Model
{
    use BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'inventory_item_id',
        'name',
        'value',
        'sku',
        'price_adjustment',
        'stock_quantity',
        'is_active'
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:2',
        'stock_quantity' => 'decimal:4',
        'is_active' => 'boolean',
        'inventory_item_id' => 'integer',
    ];

    /**
     * Get the inventory item that owns the variant.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
