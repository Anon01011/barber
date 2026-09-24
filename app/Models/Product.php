<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToSalon;
use App\Traits\BelongsToBranch;

/**
 * Product Model
 * 
 * Represents products/inventory items available for sale in the salon.
 * This is different from InventoryItem which is for internal inventory management.
 * Products are customer-facing items that can be sold through POS.
 * 
 * Multi-tenancy: Scoped to salon via BelongsToSalon trait
 */
class Product extends Model
{
    use HasFactory, SoftDeletes, BelongsToSalon, BelongsToBranch;

    protected $fillable = [
        'salon_id',
        'branch_id',
        'name',
        'description',
        'sku',
        'barcode',
        'price',
        'cost_price',
        'category_id',
        'brand',
        'stock_quantity',
        'low_stock_threshold',
        'is_active',
        'image_path',
        'tax_rate',
        'is_taxable',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_active' => 'boolean',
        'is_taxable' => 'boolean',
        'tax_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the salon that owns the product.
     * Provided by BelongsToSalon trait.
     */

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    /**
     * Get the variants for the product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
    }

    /**
     * Check if product is low on stock.
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    /**
     * Check if product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    /**
     * Get profit margin percentage.
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price <= 0) {
            return 0;
        }
        
        $profit = $this->price - $this->cost_price;
        return round(($profit / $this->cost_price) * 100, 2);
    }

    /**
     * Get profit per item.
     */
    public function getProfitPerItemAttribute(): float
    {
        return $this->price - $this->cost_price;
    }
}
