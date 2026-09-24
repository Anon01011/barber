<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\BelongsToSalon;

class Service extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'duration',
        'price',
        'status',
        'category_id',
        'salon_id',
        'available_for_online_booking'
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
        'duration' => 'integer',
        'price' => 'decimal:2',
        'available_for_online_booking' => 'boolean'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_service');
    }

    /**
     * The staff (users) that provide this service.
     */
    public function staff()
    {
        return $this->belongsToMany(User::class, 'service_user');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /**
     * Get all reviews for the service.
     */
    /**
     * Get all approved reviews for the service.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get all ratings for the service.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get the average rating of the service.
     */
    public function averageRating()
    {
        return $this->reviews()
            ->where('status', 'approved')
            ->select(DB::raw('AVG(rating) as average_rating'))
            ->value('average_rating') ?: 0;
    }

    /**
     * Get the average rating attribute (for API/JSON responses).
     */
    public function getAverageRatingAttribute()
    {
        return $this->averageRating();
    }

    /**
     * Scope a query to only include active services.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include services available for online booking.
     */
    public function scopeAvailableForOnlineBooking($query)
    {
        return $query->where('available_for_online_booking', true);
    }
}