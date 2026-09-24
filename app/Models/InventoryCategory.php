<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Traits\BelongsToSalon;

class InventoryCategory extends Model
{
    use SoftDeletes, BelongsToSalon;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'salon_id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'parent_id',
        'order',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'color' => '#6c757d',
        'is_active' => true,
        'icon' => 'fas fa-box',
        'order' => 0,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'parent_id' => 'integer'
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

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

    /**
     * Get the validation rules for the model.
     *
     * @return array
     */
    public static function rules($id = null)
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('inventory_categories', 'name')->ignore($id)->where(function ($query) {
                    $salonId = auth()->user()->salon_id ?? (app()->bound('current_salon') ? app('current_salon')->id : null);
                    return $query->where('salon_id', $salonId);
                })
            ],
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'parent_id' => 'nullable|exists:inventory_categories,id',
            'order' => 'integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get all items for this category
     */
    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'category_id');
    }

    /**
     * Get the parent category
     */
    public function parent()
    {
        return $this->belongsTo(InventoryCategory::class, 'parent_id');
    }

    /**
     * Get the child categories
     */
    public function children()
    {
        return $this->hasMany(InventoryCategory::class, 'parent_id')->orderBy('order');
    }

    /**
     * Scope a query to only include root categories.
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order categories.
     */
    public function scopeOrdered($query, $direction = 'asc')
    {
        return $query->orderBy('order', $direction)
            ->orderBy('name', $direction);
    }

    /**
     * Get the full path of the category including all parents
     */
    public function getFullPathAttribute()
    {
        $category = $this;
        $path = [];

        while ($category) {
            array_unshift($path, $category->name);
            $category = $category->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Get the count of all items in this category and its children
     */
    public function getTotalItemsCountAttribute()
    {
        $count = $this->items()->count();

        foreach ($this->children as $child) {
            $count += $child->total_items_count;
        }

        return $count;
    }
}
