<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Log;
use App\Traits\BelongsToSalon;

/**
 * User Model
 * 
 * Represents authenticated users in the system with roles and permissions.
 * 
 * DISTINCTION FROM EMPLOYEE MODEL:
 * - User: Handles authentication, authorization, roles, permissions, and access control
 * - Employee: Handles staff profile data (avatar, position, scheduling, customer-facing info)
 * 
 * DUAL ROLE CAPABILITY:
 * Users can be both customers and staff:
 * - As Customer: Has a customer() relationship, makes bookings via customerBookings()
 * - As Staff: Has an employee() relationship, receives booking assignments via staffBookings()
 * 
 * Multi-tenancy: Scoped to salon via BelongsToSalon trait (except super_admin)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, BelongsToSalon;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'status',
        'branch_id',
        'salon_id',
        'staff_id',
        'commission_profile_id',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the customer record associated with the user.
     * 
     * A user can have a customer profile if they book services.
     */
    public function customer()
    {
        return $this->hasOne(Customer::class, 'user_id');
    }

    /**
     * Get the employee record associated with the user.
     * 
     * A user can have an employee profile if they are staff.
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }

    /**
     * Get the commission profile assigned to this staff member.
     */
    public function commissionProfile()
    {
        return $this->belongsTo(CommissionProfile::class);
    }

    /**
     * Get all commissions earned by this staff member.
     */
    public function staffCommissions()
    {
        return $this->hasMany(StaffCommission::class, 'staff_id');
    }

    /**
     * Check if user is a super admin
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }


    /**
     * Assign multiple permissions to the user
     *
     * @param array|string $permissions
     * @return $this
     */
    public function assignPermissions($permissions)
    {
        return $this->syncPermissions($permissions);
    }

    /**
     * Get all roles for the user
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllRoles()
    {
        return $this->getRoleNames();
    }

    /**
     * Get bookings where this user is the customer.
     * 
     * Note: Renamed from bookings() to customerBookings() for clarity.
     * Use staffBookings() for bookings assigned to this user as staff.
     */
    public function customerBookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    /**
     * Get bookings assigned to this user as staff.
     * 
     * This relationship represents bookings where the user is the service provider.
     */
    public function staffBookings()
    {
        return $this->hasMany(Booking::class, 'staff_id');
    }

    /**
     * Get all bookings related to this user (both as customer and staff).
     * 
     * This is a helper method that combines customerBookings and staffBookings.
     * Returns a collection, not a query builder.
     */
    public function allBookings()
    {
        return $this->customerBookings->merge($this->staffBookings);
    }

    /**
     * Legacy method for backward compatibility.
     * 
     * @deprecated Use customerBookings() instead
     */
    public function bookings()
    {
        return $this->customerBookings();
    }

    /**
     * Get ratings given by this user as a customer.
     */
    /**
     * Get ratings given by this user as a customer.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'customer_id');
    }

    /**
     * Get ratings received by this user as a staff member.
     */
    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'employee_id');
    }


    /**
     * The services that belong to the user (staff).
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_user');
    }

    /**
     * Get the schedules for the staff member.
     */
    public function staffSchedules()
    {
        return $this->hasMany(StaffSchedule::class, 'staff_id');
    }

    /**
     * Get the absences for the staff member.
     */
    public function absences()
    {
        return $this->hasMany(StaffAbsence::class, 'staff_id');
    }
}
