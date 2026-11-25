<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToSalon;
use App\Traits\BelongsToBranch;

class PosSaleItemConsumable extends Model
{
    use HasFactory, BelongsToSalon, BelongsToBranch;

    protected $table = 'pos_sale_item_consumables';

    protected $fillable = [
        'salon_id',
        'branch_id',
        'pos_sale_id',
        'pos_sale_item_id',
        'inventory_item_id',
        'staff_id',
        'quantity_used',
        'unit',
        'unit_cost',
        'total_cost',
        'billed_amount',
        'notes',
    ];

    protected $casts = [
        'quantity_used' => 'decimal:3',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:2',
        'billed_amount' => 'decimal:2',
    ];

    /**
     * The parent POS sale.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class, 'pos_sale_id');
    }

    /**
     * The parent POS sale line item (the Service).
     */
    public function posSaleItem(): BelongsTo
    {
        return $this->belongsTo(PosSaleItem::class, 'pos_sale_item_id');
    }

    /**
     * Scope to exclude voided sales.
     */
    public function scopeActiveSales($query)
    {
        return $query->whereDoesntHave('sale', fn($q) => $q->where('status', 'voided'));
    }

    /**
     * Alias for posSaleItem.
     */
    public function saleItem(): BelongsTo
    {
        return $this->posSaleItem();
    }

    /**
     * The inventory item consumed.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    /**
     * The staff member who dispensed the product.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
