<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToSalon;

class Membership extends Model
{
    use BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'name',
        'slug',
        'description',
        'discount_value',
        'is_taxable',
        'membership_type',
        'validity_value',
        'validity_unit',
        'is_active',
        'is_recurring',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug) && !empty($model->name)) {
                $model->slug = \Illuminate\Support\Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if (empty($model->slug) && !empty($model->name)) {
                $model->slug = \Illuminate\Support\Str::slug($model->name);
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

    protected $casts = [
        'discount_value' => 'decimal:2',
        'is_taxable' => 'boolean',
        'validity_value' => 'integer',
        'is_active' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'membership_services')
            ->withPivot('quantity', 'membership_price', 'discount_type', 'discount_value')
            ->withTimestamps();
    }

    public function inventoryItems()
    {
        return $this->belongsToMany(InventoryItem::class, 'membership_inventory_items')
            ->withPivot('quantity', 'membership_price', 'discount_type', 'discount_value')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFormattedValidityAttribute()
    {
        return $this->validity_value . ' ' . ucfirst($this->validity_unit);
    }

    public function calculateTotalMembershipPrice()
    {
        $servicesTotal = 0;
        foreach ($this->services as $service) {
            $servicesTotal += ($service->pivot->membership_price ?? 0) * ($service->pivot->quantity ?? 1);
        }

        $productsTotal = 0;
        foreach ($this->inventoryItems as $item) {
            $productsTotal += ($item->pivot->membership_price ?? 0) * ($item->pivot->quantity ?? 1);
        }

        return $servicesTotal + $productsTotal;
    }

    public function calculateTotalValue()
    {
        $servicesTotal = 0;
        foreach ($this->services as $service) {
            $servicesTotal += ($service->price ?? 0) * ($service->pivot->quantity ?? 1);
        }

        $productsTotal = 0;
        foreach ($this->inventoryItems as $item) {
            $productsTotal += ($item->selling_price ?? 0) * ($item->pivot->quantity ?? 1);
        }

        return $servicesTotal + $productsTotal;
    }
}
