<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToSalon;

class CommissionProfile extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'name',
        'type',
        'include_tax',
        'is_active',
        'calculation_interval',
        'qualifying_item',
        'is_cascade',
    ];

    protected $casts = [
        'include_tax' => 'boolean',
        'is_active' => 'boolean',
        'is_cascade' => 'boolean',
    ];

    /**
     * Get the salon that owns the commission profile.
     */
    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }

    /**
     * Get the commission rules for this profile.
     */
    public function rules(): HasMany
    {
        return $this->hasMany(CommissionRule::class);
    }

    /**
     * Get the staff members using this profile.
     */
    public function staff(): HasMany
    {
        return $this->hasMany(User::class, 'commission_profile_id');
    }

    /**
     * Get the commissions calculated using this profile.
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(StaffCommission::class);
    }

    /**
     * Scope to only active profiles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
