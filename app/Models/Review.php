<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToSalon;

/**
 * Review Model
 * 
 * Represents customer reviews for services.
 * Multi-tenancy: Scoped to salon via BelongsToSalon trait
 */
class Review extends Model
{
    use BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'user_id',
        'service_id',
        'booking_id',
        'rating',
        'comment',
        'status'
    ];

    protected $casts = [
        'rating' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that made the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service that was reviewed.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the booking associated with the review.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
