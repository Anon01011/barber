<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BelongsToSalon;

/**
 * CustomerMembership Model
 * 
 * Represents a customer's active or past membership subscription.
 * This is the pivot/junction model that connects customers to memberships
 * with additional attributes like start_date, end_date, and is_active.
 * 
 * Multi-tenancy: Scoped to salon via BelongsToSalon trait
 */
class CustomerMembership extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'customer_id',
        'membership_id',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the customer that owns the membership.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the membership.
     */
    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    /**
     * Scope a query to only include active memberships.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    /**
     * Check if the membership is currently active.
     */
    public function isActive()
    {
        return $this->is_active &&
               $this->start_date <= now() &&
               $this->end_date >= now();
    }
}
