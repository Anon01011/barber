<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToSalon;

class Customer extends Model
{
    use HasFactory, BelongsToSalon, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'customer_id',
        'email',
        'phone',
        'country_code',
        'secondary_number',
        'secondary_country_code',
        'gender',
        'dob',
        'anniversary',
        'address',
        'location',
        'source',
        'preferred_contact',
        'send_promotional_sms',
        'send_transactional_sms',
        'notes',
        'medical_notes',
        'custom_fields',
        'status',
        'last_visit_at',
        'user_id',
        'preferred_service',
        'is_guest',
        'salon_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_visit_at' => 'datetime',
        'dob' => 'date',
        'anniversary' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_guest' => 'boolean',
        'send_promotional_sms' => 'boolean',
        'send_transactional_sms' => 'boolean',
        'custom_fields' => 'array',
    ];

    protected $withCount = ['bookings'];
    protected $appends = ['formatted_last_visit', 'total_bookings_count', 'last_visit', 'preferred_service_name', 'display_phone', 'display_email', 'full_name'];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            // Auto-generate customer_id if not provided
            if (!$customer->customer_id) {
                $customer->customer_id = static::generateCustomerId($customer->salon_id);
            }

            // Auto-populate name from first_name and last_name if not provided
            if (!$customer->name && ($customer->first_name || $customer->last_name)) {
                $customer->name = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
            }
        });

        static::updating(function ($customer) {
            // Update name if first_name or last_name changes
            if ($customer->isDirty(['first_name', 'last_name'])) {
                $customer->name = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
            }
        });
    }

    /**
     * Generate unique customer ID
     */
    protected static function generateCustomerId($salonId): string
    {
        $prefix = 'CUST-' . str_pad($salonId, 4, '0', STR_PAD_LEFT) . '-';
        $lastCustomer = static::withTrashed()
            ->where('salon_id', $salonId)
            ->where('customer_id', 'like', $prefix . '%')
            ->orderBy('customer_id', 'desc')
            ->first();

        if ($lastCustomer && preg_match('/-([\d]+)$/', $lastCustomer->customer_id, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get full name accessor
     */
    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        }
        if ($this->name && $this->name !== 'Unknown') {
            return $this->name;
        }
        return $this->user->name ?? $this->name ?? 'N/A';
    }

    /**
     * Get the bookings for the customer.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class)->latest();
    }

    /**
     * Get the POS sales for the customer.
     */
    public function posSales()
    {
        return $this->hasMany(PosSale::class);
    }

    /**
     * Get the customer's package balances (remaining services).
     */
    public function packageBalances()
    {
        return $this->hasMany(CustomerPackageBalance::class);
    }

    /**
     * Get the customer's active package balances (with remaining quantity > 0).
     */
    public function activePackageBalances()
    {
        return $this->hasMany(CustomerPackageBalance::class)->where('quantity_remaining', '>', 0);
    }

    /**
     * Get package purchases through POS sales.
     */
    public function packagePurchases()
    {
        return $this->hasManyThrough(
            PosSaleItem::class,
            PosSale::class,
            'customer_id', // Foreign key on pos_sales table
            'sale_id',    // Foreign key on pos_sale_items table
            'id',         // Local key on customers table
            'id'          // Local key on pos_sales table
        )->whereNotNull('package_id');
    }

    /**
     * Get bookings that used packages.
     */
    public function packageBookings()
    {
        return $this->hasMany(Booking::class)->whereNotNull('package_id');
    }

    /**
     * Get the user associated with the customer.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer's favorite services.
     */
    public function favoriteServices()
    {
        return $this->belongsToMany(Service::class, 'customer_favorite_services')
            ->withTimestamps()
            ->where('status', 'active');
    }

    /**
     * Get the appointments for the customer.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the customer's last visit in a human-readable format.
     */
    public function getFormattedLastVisitAttribute()
    {
        return $this->last_visit_at ? $this->last_visit_at->diffForHumans() : 'Never';
    }

    /**
     * Get the total number of bookings for the customer.
     */
    public function getTotalBookingsCountAttribute()
    {
        return $this->bookings_count;
    }

    /**
     * Get the customer's last visit date.
     */
    public function getLastVisitAttribute()
    {
        return $this->last_visit_at ? $this->last_visit_at->format('M d, Y') : 'Never';
    }

    /**
     * Get the customer's preferred service name.
     */
    public function getPreferredServiceNameAttribute()
    {
        if ($this->preferred_service) {
            return $this->preferred_service;
        }

        // Use a raw query to avoid the ONLY_FULL_GROUP_BY issue
        $mostBookedServiceId = \DB::table('bookings')
            ->select('service_id', \DB::raw('COUNT(*) as booking_count'))
            ->where('customer_id', $this->id)
            ->groupBy('service_id')
            ->orderBy('booking_count', 'desc')
            ->value('service_id');

        if (!$mostBookedServiceId) {
            return 'None';
        }

        // Get the service name
        $service = \App\Models\Service::find($mostBookedServiceId);

        return $service ? $service->name : 'None';
    }

    /**
     * Check if a service is favorited by the customer.
     */
    public function hasFavoriteService($serviceId)
    {
        return $this->favoriteServices()->where('service_id', $serviceId)->exists();
    }

    /**
     * Toggle favorite status for a service.
     */
    public function toggleFavoriteService($serviceId)
    {
        if ($this->hasFavoriteService($serviceId)) {
            $this->favoriteServices()->detach($serviceId);
            return false;
        } else {
            $this->favoriteServices()->attach($serviceId);
            return true;
        }
    }

    /**
     * Get the customer's preferred contact method.
     */
    public function getPreferredContactMethodAttribute()
    {
        return ucfirst($this->preferred_contact);
    }

    /**
     * Get the customer's status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'active' => 'bg-success-subtle text-white',
            'inactive' => 'bg-warning-subtle text-warning',
            default => 'bg-secondary-subtle text-secondary',
        };
    }

    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive customers.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to search for customers by name, email, or phone.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('customer_id', 'like', "%{$search}%");
        });
    }

    /**
     * Get the customer's total bookings count.
     */
    public function getTotalBookingsAttribute()
    {
        return $this->bookings()->count();
    }

    /**
     * Get the customer's last booking.
     */
    public function getLastBookingAttribute()
    {
        return $this->bookings()->latest()->first();
    }

    /**
     * Get the customer's memberships.
     */
    public function memberships()
    {
        return $this->hasMany(CustomerMembership::class);
    }

    /**
     * Get the customer's active memberships.
     */
    public function activeMemberships()
    {
        return $this->memberships()->active()->with('membership');
    }

    /**
     * Check if customer has an active membership.
     */
    public function hasActiveMembership()
    {
        return $this->activeMemberships()->exists();
    }

    /**
     * Get the customer's active membership (first one if multiple).
     */
    public function getActiveMembership()
    {
        return $this->activeMemberships()->first();
    }

    /**
     * Get the customer's phone number (masked or unmasked based on settings).
     */
    public function getDisplayPhoneAttribute(): string
    {
        $phone = \App\Helpers\CustomerDataHelper::getMaskedPhone($this);

        // Add country code if available
        if ($this->country_code && $phone !== 'N/A') {
            return $this->country_code . ' ' . $phone;
        }

        return $phone;
    }

    /**
     * Get the customer's phone number with country code (unmasked).
     */
    public function getFormattedPhoneWithCodeAttribute(): string
    {
        if (!$this->phone) {
            return 'N/A';
        }

        $countryCode = $this->country_code ?? '+91';
        return $countryCode . ' ' . $this->phone;
    }

    /**
     * Get the customer's secondary phone number with country code.
     */
    public function getFormattedSecondaryPhoneAttribute(): ?string
    {
        if (!$this->secondary_number) {
            return null;
        }

        $countryCode = $this->secondary_country_code ?? $this->country_code ?? '+91';
        return $countryCode . ' ' . $this->secondary_number;
    }

    /**
     * Get the customer's email (masked or unmasked based on settings).
     */
    public function getDisplayEmailAttribute(): string
    {
        return \App\Helpers\CustomerDataHelper::getMaskedEmail($this);
    }

    /**
     * Get the ratings for the customer.
     */
    public function ratings()
    {
        return $this->hasManyThrough(Rating::class, Booking::class);
    }
}
