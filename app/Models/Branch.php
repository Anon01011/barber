<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToSalon;

class Branch extends Model
{
    use SoftDeletes, BelongsToSalon;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'email',
        'is_active',
        'salon_id'
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
        'is_active' => 'boolean',
    ];

    // Relationships
    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the inventory items assigned to this branch (many-to-many).
     */
    public function inventoryItems()
    {
        return $this->belongsToMany(InventoryItem::class, 'branch_inventory_item')
                    ->withTimestamps();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function posSales(): HasMany
    {
        return $this->hasMany(PosSale::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(StaffCommission::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
