<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToSalon;

/**
 * Rating Model
 * 
 * Represents customer ratings for bookings and services.
 * Multi-tenancy: Scoped to salon via BelongsToSalon trait
 */
class Rating extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'booking_id',
        'customer_id',
        'service_id',
        'employee_id',
        'rating',
        'comment'
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the rating.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service that was rated.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the staff member who provided the service.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the booking associated with the rating.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the customer who gave the rating.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}