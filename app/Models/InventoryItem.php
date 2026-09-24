<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\InventoryCategory;
use App\Models\Supplier;
use App\Models\InventoryTransaction;
use App\Traits\BelongsToSalon;
use App\Traits\BelongsToBranch;

class InventoryItem extends Model
{
    use SoftDeletes, BelongsToSalon, BelongsToBranch;

    protected $fillable = [
        'salon_id',
        'branch_id',
        'category_id',
        'supplier_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'description',
        'purchase_price',
        'selling_price',
        'quantity_in_stock',
        'minimum_quantity',
        'reorder_level',
        'unit_type',
        'location',
        'image_path',
        'notes',
        'tax_rate',
        'is_taxable',
        'weight',
        'dimensions',
        'expiry_date',
        'manufacturer',
        'brand',
        'is_active'
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'quantity_in_stock' => 'integer',
        'minimum_quantity' => 'integer',
        'reorder_level' => 'integer',
        'tax_rate' => 'decimal:2',
        'is_taxable' => 'boolean',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'expiry_date' => 'date',
        'category_id' => 'integer',
        'supplier_id' => 'integer',
        'dimensions' => 'array',
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

    protected $dates = [
        'expiry_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $appends = [
        'image_url',
        'stock_status',
        'profit_margin',
        'profit_per_item'
    ];

    // Stock status constants
    const STATUS_IN_STOCK = 'in_stock';
    const STATUS_LOW_STOCK = 'low_stock';
    const STATUS_OUT_OF_STOCK = 'out_of_stock';

    // Unit types
    public static $unitTypes = [
        'pcs' => 'Pieces',
        'kg' => 'Kilograms',
        'g' => 'Grams',
        'l' => 'Liters',
        'ml' => 'Milliliters',
        'm' => 'Meters',
        'cm' => 'Centimeters',
        'box' => 'Box',
        'pack' => 'Pack',
        'set' => 'Set',
        'pair' => 'Pair'
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate a unique SKU if not provided
            if (empty($model->sku)) {
                $model->sku = static::generateUniqueSku($model->name);
            }

            // Generate slug if not provided
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name) . '-' . Str::random(6);
            }

            // Ensure SKU is uppercase and valid format
            if (!empty($model->sku)) {
                $model->sku = strtoupper($model->sku);
                // Validate SKU format (uppercase letters, numbers, hyphens only)
                if (!preg_match('/^[A-Z0-9\-]+$/', $model->sku)) {
                    throw new \InvalidArgumentException('SKU must contain only uppercase letters, numbers, and hyphens.');
                }
            }

            // Set default values
            $model->is_active = $model->is_active ?? true;
            $model->is_taxable = $model->is_taxable ?? true;
            $model->tax_rate = $model->tax_rate ?? 0;
        });

        static::updating(function ($model) {
            // Ensure SKU is uppercase and valid format if provided
            if (!empty($model->sku)) {
                $model->sku = strtoupper($model->sku);
                // Validate SKU format (uppercase letters, numbers, hyphens only)
                if (!preg_match('/^[A-Z0-9\-]+$/', $model->sku)) {
                    throw new \InvalidArgumentException('SKU must contain only uppercase letters, numbers, and hyphens.');
                }
            }

            // No need to update stock status here as it's handled by the accessor
        });

        static::deleting(function ($model) {
            // Delete associated image when product is deleted
            if ($model->image_path) {
                Storage::disk('public')->delete($model->image_path);
            }
        });
    }

    /**
     * Generate a unique SKU based on product name
     */
    public static function generateUniqueSku($name, $suffix = '')
    {
        // Clean and prepare the name
        $cleanName = trim($name);
        if (empty($cleanName)) {
            // Fallback to random SKU if no name provided
            return 'ITM-' . strtoupper(Str::random(8));
        }

        // Create base SKU from name - take first 3 words or first 15 characters
        $words = explode(' ', $cleanName);
        $baseWords = array_slice($words, 0, 3);
        $base = implode('', array_map(function ($word) {
            return strtoupper(substr($word, 0, 3));
        }, $baseWords));

        // Ensure minimum length of 3 characters
        if (strlen($base) < 3) {
            $base = strtoupper(substr($cleanName, 0, 3));
        }

        // Add suffix if provided
        $sku = $base . $suffix;
        $count = 1;

        // Ensure uniqueness
        while (static::withTrashed()->where('sku', $sku)->exists()) {
            $sku = $base . '-' . $count++;
        }

        return $sku;
    }

    /**
     * Update the stock status based on current quantity
     * This method is now a wrapper for getStockStatusAttribute() for backward compatibility
     */
    /**
     * Calculate the stock status based on current quantity.
     * 
     * @return string
     */
    public function calculateStockStatus()
    {
        return $this->getStockStatusAttribute();
    }

    /**
     * Get the category that owns the inventory item.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    /**
     * Get the supplier that provides the inventory item.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Get the URL to the product image with a placeholder fallback.
     * 
     * @param string|null $size Optional size parameter (not used, kept for backward compatibility)
     * @return string
     */
    public function getImageUrlAttribute($size = null)
    {
        // Return the actual image if it exists
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            return asset('storage/' . $this->image_path);
        }

        // Return a base64 encoded SVG placeholder if no image is set
        return 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
            <rect width="200" height="200" fill="#f3f4f6"/>
            <text x="50%" y="50%" font-family="Arial" font-size="14" text-anchor="middle" alignment-baseline="middle" fill="#9ca3af">
                No Image
            </text>
        </svg>');
    }

    /**
     * Handle image upload for the inventory item.
     *
     * @param \Illuminate\Http\UploadedFile|null $image
     * @return string|null
     * @throws \Exception
     */
    public function uploadImage($image)
    {
        if (!$image) {
            return null;
        }

        try {
            // Delete old image if exists
            $this->deleteImage();

            // Generate a unique filename
            $filename = 'item_' . $this->id . '_' . time() . '.' . $image->getClientOriginalExtension();

            // Store the new image
            $path = $image->storeAs('inventory', $filename, 'public');

            return $path;
        } catch (\Exception $e) {
            \Log::error('Error uploading image: ' . $e->getMessage());
            throw new \Exception('Failed to upload image: ' . $e->getMessage());
        }
    }

    /**
     * Delete the associated image file.
     *
     * @return bool
     */
    public function deleteImage()
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            try {
                Storage::disk('public')->delete($this->image_path);
                return true;
            } catch (\Exception $e) {
                \Log::error('Error deleting image: ' . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    /**
     * Get the profit margin percentage.
     */
    public function getProfitMarginAttribute()
    {
        if ($this->purchase_price <= 0) {
            return 0;
        }

        $profit = $this->selling_price - $this->purchase_price;
        return round(($profit / $this->purchase_price) * 100, 2);
    }

    /**
     * Get the profit per item.
     */
    public function getProfitPerItemAttribute()
    {
        return $this->selling_price - $this->purchase_price;
    }

    /**
     * Check if the item is low in stock.
     */
    public function isLowStock()
    {
        return $this->reorder_level > 0 && $this->quantity_in_stock > 0 &&
            $this->quantity_in_stock <= $this->reorder_level;
    }

    /**
     * Check if the item is out of stock.
     */
    public function isOutOfStock()
    {
        return $this->quantity_in_stock <= 0;
    }

    /**
     * Get all transactions for the inventory item.
     */
    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'item_id');
    }

    /**
     * Get all variants for the inventory item.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'inventory_item_id');
    }

    /**
     * Get the branches that this inventory item is assigned to.
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_inventory_item')
            ->withPivot('quantity', 'minimum_quantity', 'is_active')
            ->withTimestamps();
    }

    /**
     * Update the stock level for this item.
     *
     * @param int $quantity
     * @param string $type 'in' or 'out'
     * @return void
     */
    public function updateStock(int $quantity, string $type): void
    {
        $current = (float) $this->quantity_in_stock;
        $newStock = $type === 'in' ? $current + $quantity : max(0.0, $current - $quantity);
        $this->setAttribute('quantity_in_stock', $newStock);
        $this->save();
        // No need to call updateStockStatus() as it's handled by the accessor
    }

    /**
     * Get the stock status of the item.
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->quantity_in_stock <= 0) {
            return self::STATUS_OUT_OF_STOCK;
        }

        // Check if the quantity is at or below the reorder level
        if ($this->reorder_level > 0 && $this->quantity_in_stock <= $this->reorder_level) {
            return self::STATUS_LOW_STOCK;
        }

        // Check if the quantity is at or below the minimum quantity
        if ($this->minimum_quantity > 0 && $this->quantity_in_stock <= $this->minimum_quantity) {
            return self::STATUS_LOW_STOCK;
        }

        return self::STATUS_IN_STOCK;
    }

    /**
     * Scope a query to only include active items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include low stock items.
     */
    public function scopeLowStock($query)
    {
        return $query->where('quantity_in_stock', '>', 0)
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('minimum_quantity', '>', 0)
                        ->whereColumn('quantity_in_stock', '<=', 'minimum_quantity');
                })
                ->orWhere(function ($sub) {
                    $sub->where('reorder_level', '>', 0)
                        ->whereColumn('quantity_in_stock', '<=', 'reorder_level');
                });
            });
    }

    /**
     * Scope a query to only include out of stock items.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity_in_stock', '<=', 0);
    }
}
