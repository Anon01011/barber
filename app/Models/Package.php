<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\BelongsToSalon;

class Package extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'name',
        'slug',
        'description',
        'type',
        'price',
        'special_price',
        'tax_rate',
        'validity_value',
        'validity_unit',
        'validity_description',
        'is_active',
        'service_limit',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug) && !empty($model->name)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if (empty($model->slug) && !empty($model->name)) {
                $model->slug = Str::slug($model->name);
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
        'price' => 'decimal:2',
        'special_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'validity_value' => 'integer',
        'is_active' => 'boolean',
        'service_limit' => 'integer',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'package_services')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getValidityPeriodAttribute()
    {
        return $this->validity_value . ' ' . Str::plural($this->validity_unit, $this->validity_value);
    }

    public function getFormattedValidityAttribute()
    {
        return $this->validity_value . ' ' . ucfirst(Str::plural($this->validity_unit, $this->validity_value));
    }

    public function calculateTotalFromServices()
    {
        return $this->services()->withPivot('quantity')->get()->sum(function ($service) {
            return $service->price * $service->pivot->quantity;
        });
    }

    public function getEffectivePriceAttribute()
    {
        if ($this->special_price !== null) {
            return $this->special_price;
        }

        $calculated = $this->calculateTotalFromServices();
        return $calculated > 0 ? $calculated : ($this->price ?? 0);
    }
}
