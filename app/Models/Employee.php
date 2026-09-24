<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToSalon;

/**
 * Employee Model
 * 
 * Represents the staff/employee profile data for salon staff members.
 * 
 * DISTINCTION FROM USER MODEL:
 * - User: Handles authentication, authorization, roles, and permissions
 * - Employee: Handles staff profile data (avatar, position, bookings, ratings)
 * 
 * RELATIONSHIP:
 * - One User can have one Employee record (hasOne relationship)
 * - Employee belongs to a User (belongsTo relationship)
 * 
 * USAGE:
 * - Use User model for login, permissions, and access control
 * - Use Employee model for staff scheduling, bookings, and customer-facing data
 * 
 * Multi-tenancy: Scoped to salon via BelongsToSalon trait
 */
class Employee extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role_id',
        'salon_id',
        'position',
        'status',
        'avatar',
        'user_id'
    ];

    protected $appends = ['avatar_url'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'staff_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'employee_service');
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
    }

    public function getRatingAttribute()
    {
        return $this->bookings()->avg('rating') ?: 0;
    }
}