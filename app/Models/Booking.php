<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Traits\BelongsToSalon;
use App\Traits\BelongsToBranch;

class Booking extends Model
{
    use HasFactory;
    use BelongsToSalon, BelongsToBranch;

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($booking) {
            if ($booking->isDirty('status') && in_array($booking->status, [self::STATUS_COMPLETED, self::STATUS_STAFF_COMPLETED])) {
                $salonTimezone = salon_timezone();
                if ($booking->start_time->isFuture() && !$booking->start_time->copy()->setTimezone($salonTimezone)->isToday()) {
                    throw new \Exception('Bookings scheduled for the future cannot be completed. Only today\'s bookings can be completed today.');
                }
            }
        });
    }

    protected $fillable = [
        'customer_id',
        'branch_id',
        'service_id',
        'package_id',
        'package_service_status',
        'staff_id',
        'start_time',
        'end_time',
        'amount',
        'tip_amount',
        'payment_method',
        'payment_status',
        'paid_at',
        'notes',
        'status',
        'cancellation_reason',
        'reminder_sent',
        'rating',
        'feedback',
        'staff_assignment_status',
        'staff_assigned_at',
        'rejected_by_staff_id',
        'is_rated',
        'rating_token',
        'rating_token_expires_at',
        'salon_id',
        'created_by',
        'source',
        'booking_group_id',
        'selected_services'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'amount' => 'decimal:2',
        'tip_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'reminder_sent' => 'boolean',
        'rating' => 'integer',
        'staff_assigned_at' => 'datetime',
        'rating_token_expires_at' => 'datetime',
        'package_service_status' => 'integer',
        'selected_services' => 'array'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRM = 'confirm';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_STAFF_COMPLETED = 'staff_completed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';
    const STATUS_ARRIVED = 'arrived';
    const STATUS_STARTED = 'started';

    // Payment status constants
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_PARTIAL = 'partial';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

    // Payment method constants
    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_CARD = 'card';
    const PAYMENT_METHOD_UPI = 'upi';
    const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';
    const PAYMENT_METHOD_ONLINE = 'online';
    const PAYMENT_METHOD_MIXED = 'mixed';
    const PAYMENT_METHOD_OTHER = 'other';
    const PAYMENT_METHOD_PACKAGE = 'package';

    // Package service status constants
    const PACKAGE_SERVICE_USE_NOW = 0;      // Use immediately, create booking and consume balance
    const PACKAGE_SERVICE_SAVE_FUTURE = 1;  // Save for future, add to balance only
    const PACKAGE_SERVICE_NEW_PURCHASE = 2; // New purchase, create booking, do not consume existing balance

    const STAFF_ASSIGNMENT_PENDING = 'pending';
    const STAFF_ASSIGNMENT_ASSIGNED = 'assigned';
    const STAFF_ASSIGNMENT_REJECTED = 'rejected';

    // Validation rules
    public static $rules = [
        'customer_id' => 'required|exists:customers,id',
        'service_id' => 'required|exists:services,id',
        'staff_id' => 'nullable|exists:users,id',
        'start_time' => 'required|date|after:now',
        'notes' => 'nullable|string|max:500',
        'status' => 'required|in:pending,confirmed,staff_completed,completed,cancelled,no_show,arrived,started',
        'cancellation_reason' => 'nullable|required_if:status,cancelled|string|max:255',
        'rating' => 'nullable|integer|between:1,5',
        'feedback' => 'nullable|string|max:1000'
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all ratings for the booking.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'booking_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('start_time', '<', now());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_time', Carbon::today());
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', [self::STATUS_COMPLETED, self::STATUS_STAFF_COMPLETED]);
    }

    public function scopeStaffCompleted($query)
    {
        return $query->where('status', self::STATUS_STAFF_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeNoShow($query)
    {
        return $query->where('status', self::STATUS_NO_SHOW);
    }

    public function scopePendingAssignment($query)
    {
        return $query->where('staff_assignment_status', self::STAFF_ASSIGNMENT_PENDING);
    }

    public function scopeAssigned($query)
    {
        return $query->where('staff_assignment_status', self::STAFF_ASSIGNMENT_ASSIGNED);
    }

    // Accessors & Mutators
    public function getDurationAttribute()
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(salon_timezone());
    }

    public function getIsUpcomingAttribute()
    {
        return $this->start_time->isFuture();
    }

    public function getIsPastAttribute()
    {
        return $this->start_time->isPast();
    }

    public function getIsTodayAttribute()
    {
        return $this->start_time->isToday();
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_STAFF_COMPLETED => 'primary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_NO_SHOW => 'dark',
            self::STATUS_ARRIVED => 'warning',
            self::STATUS_STARTED => 'primary',
            default => 'secondary'
        };
    }

    // Business Logic Methods
    public function canBeCancelled()
    {
        return $this->status !== self::STATUS_COMPLETED
            && $this->status !== self::STATUS_CANCELLED
            && $this->start_time->isFuture();
    }

    public function canBeRescheduled()
    {
        return $this->status !== self::STATUS_COMPLETED
            && $this->status !== self::STATUS_CANCELLED
            && $this->start_time->isFuture();
    }

    public function cancel($reason = null)
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception('This booking cannot be cancelled.');
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancellation_reason' => $reason
        ]);

        Log::info('Booking cancelled', [
            'booking_id' => $this->id,
            'reason' => $reason
        ]);

        return true;
    }

    public function confirm()
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \Exception('Only pending bookings can be confirmed.');
        }

        $this->update(['status' => self::STATUS_CONFIRMED]);

        Log::info('Booking confirmed', [
            'booking_id' => $this->id
        ]);

        return true;
    }

    public function complete()
    {
        if (!in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_STAFF_COMPLETED])) {
            throw new \Exception('Only confirmed or staff completed bookings can be completed.');
        }

        // Check if booking is in the future
        $salonTimezone = salon_timezone();
        if ($this->start_time->isFuture() && !$this->start_time->copy()->setTimezone($salonTimezone)->isToday()) {
            throw new \Exception('Bookings scheduled for the future cannot be completed. Only today\'s bookings can be completed today.');
        }

        $this->update(['status' => self::STATUS_COMPLETED]);

        Log::info('Booking completed', [
            'booking_id' => $this->id
        ]);

        return true;
    }

    public function staffComplete()
    {
        if ($this->status !== self::STATUS_CONFIRMED) {
            throw new \Exception('Only confirmed bookings can be marked as staff completed.');
        }

        // Check if booking is in the future
        $salonTimezone = salon_timezone();
        if ($this->start_time->isFuture() && !$this->start_time->copy()->setTimezone($salonTimezone)->isToday()) {
            throw new \Exception('Bookings scheduled for the future cannot be completed. Only today\'s bookings can be completed today.');
        }

        $this->update(['status' => self::STATUS_STAFF_COMPLETED]);

        Log::info('Booking marked as staff completed', [
            'booking_id' => $this->id
        ]);

        return true;
    }

    public function markAsNoShow()
    {
        if ($this->status !== self::STATUS_CONFIRMED) {
            throw new \Exception('Only confirmed bookings can be marked as no-show.');
        }

        $this->update(['status' => self::STATUS_NO_SHOW]);

        Log::info('Booking marked as no-show', [
            'booking_id' => $this->id
        ]);

        return true;
    }

    /**
     * Check for scheduling conflicts in both bookings and appointments tables.
     * 
     * @param int $staffId
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param int|null $excludeBookingId
     * @return bool
     */
    public static function hasConflict($staffId, $startTime, $endTime, $excludeBookingId = null)
    {
        // Check bookings table - ACROSS ALL BRANCHES
        $bookingConflict = self::withoutGlobalScope('branch')
            ->where('staff_id', $staffId)
            ->where('status', '!=', self::STATUS_CANCELLED)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeBookingId) {
            $bookingConflict->where('id', '!=', $excludeBookingId);
        }

        if ($bookingConflict->exists()) {
            return true;
        }

        // Check appointments table - ACROSS ALL BRANCHES
        // Note: Using fully qualified class name to avoid circular dependency issues if imported
        $appointmentConflict = \App\Models\Appointment::withoutGlobalScope('branch')
            ->where('staff_id', $staffId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();

        return $appointmentConflict;
    }

    public function reschedule($newStartTime)
    {
        if (!$this->canBeRescheduled()) {
            throw new \Exception('This booking cannot be rescheduled.');
        }

        $newStartTime = Carbon::parse($newStartTime);
        $duration = $this->duration;
        $newEndTime = $newStartTime->copy()->addMinutes($duration);

        // Check for conflicts using the new cross-table method
        if (self::hasConflict($this->staff_id, $newStartTime, $newEndTime, $this->id)) {
            throw new \Exception('This time slot is already booked.');
        }

        $this->update([
            'start_time' => $newStartTime,
            'end_time' => $newEndTime
        ]);

        Log::info('Booking rescheduled', [
            'booking_id' => $this->id,
            'new_start_time' => $newStartTime
        ]);

        return true;
    }

    public function addRating($rating, $feedback = null)
    {
        if ($this->status !== self::STATUS_COMPLETED) {
            throw new \Exception('Only completed bookings can be rated.');
        }

        if ($rating < 1 || $rating > 5) {
            throw new \Exception('Rating must be between 1 and 5.');
        }

        $this->update([
            'rating' => $rating,
            'feedback' => $feedback
        ]);

        Log::info('Rating added to booking', [
            'booking_id' => $this->id,
            'rating' => $rating
        ]);

        return true;
    }

    public function sendReminder()
    {
        if ($this->reminder_sent) {
            return false;
        }

        if ($this->customer && $this->customer->user) {
            $this->customer->user->notify(new \App\Notifications\BookingReminder($this));
        } elseif ($this->customer && $this->customer->email) {
            // If customer has no user account but has email, we could send via Notification facade
            // But for now, let's assume user account is required for notifications
            \Illuminate\Support\Facades\Notification::route('mail', $this->customer->email)
                ->notify(new \App\Notifications\BookingReminder($this));
        }

        $this->update(['reminder_sent' => true]);

        Log::info('Reminder sent for booking', [
            'booking_id' => $this->id
        ]);

        return true;
    }

    public function assignStaff($staffId)
    {
        $this->update([
            'staff_id' => $staffId,
            'staff_assignment_status' => self::STAFF_ASSIGNMENT_ASSIGNED,
            'staff_assigned_at' => now(),
            'status' => self::STATUS_CONFIRMED
        ]);
    }

    /**
     * Check if booking is paid
     */
    public function isPaid(): bool
    {
        if ($this->payment_status === self::PAYMENT_STATUS_PAID) {
            return true;
        }

        // If it's a package booking and it's completed, it's effectively paid
        // We also consider it paid if it has a package_id, as it's covered by a pre-paid package
        if ($this->package_id) {
            return true;
        }

        return false;
    }

    /**
     * Mark booking as paid
     */
    public function markAsPaid(string $paymentMethod, ?float $tipAmount = null): bool
    {
        return $this->update([
            'payment_status' => self::PAYMENT_STATUS_PAID,
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
            'tip_amount' => $tipAmount ?? $this->tip_amount
        ]);
    }

    /**
     * Complete booking with payment
     */
    public function completeWithPayment(string $paymentMethod, ?float $tipAmount = null, bool $incrementTotalSpent = true): bool
    {
        // Check if booking is in the future
        $salonTimezone = salon_timezone();
        if ($this->start_time->isFuture() && !$this->start_time->copy()->setTimezone($salonTimezone)->isToday()) {
            throw new \Exception('Bookings scheduled for the future cannot be completed. Only today\'s bookings can be completed today.');
        }

        if (!in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_STAFF_COMPLETED])) {
            throw new \Exception('Only confirmed or staff completed bookings can be completed.');
        }

        $updated = $this->update([
            'status' => self::STATUS_COMPLETED,
            'payment_status' => self::PAYMENT_STATUS_PAID,
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
            'tip_amount' => $tipAmount ?? $this->tip_amount
        ]);

        if ($updated) {
            // Generate rating token
            $this->generateRatingToken();

            // Send completion email with rating link
            if ($this->customer && $this->customer->email) {
                \App\Jobs\SendBookingCompletionEmail::dispatch($this);
            }

            // Send SMS notification if enabled
            if ($this->customer && $this->customer->phone && $this->customer->send_transactional_sms) {
                // TODO: Implement SMS notification
            }

            // Update customer total spent and last purchase date
            if ($incrementTotalSpent && $this->customer) {
                // If it's a package booking, the amount is already paid via the package purchase
                // so we don't increment total_spent again to avoid double counting.
                if (!$this->package_id) {
                    $amountToIncrement = ($this->amount ?? 0) + ($tipAmount ?? $this->tip_amount ?? 0);
                    $this->customer->increment('total_spent', $amountToIncrement);
                } else if ($tipAmount > 0) {
                    // Still increment for the tip if provided
                    $this->customer->increment('total_spent', $tipAmount);
                }
                $this->customer->update(['last_visit_at' => now()]);
            }
        }

        return $updated;
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            self::PAYMENT_METHOD_CASH => 'Cash',
            self::PAYMENT_METHOD_CARD => 'Card',
            self::PAYMENT_METHOD_UPI => 'UPI',
            self::PAYMENT_METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::PAYMENT_METHOD_ONLINE => 'Online',
            self::PAYMENT_METHOD_MIXED => 'Mixed / Split',
            self::PAYMENT_METHOD_OTHER => 'Other',
            self::PAYMENT_METHOD_PACKAGE => 'Package',
            default => ucfirst($this->payment_method ?? 'N/A')
        };
    }

    public function rejectStaffAssignment()
    {
        $this->update([
            'staff_assignment_status' => self::STAFF_ASSIGNMENT_REJECTED,
            'staff_id' => null,
            'staff_assigned_at' => null
        ]);
    }

    /**
     * Generate a unique rating token for this booking
     */
    public function generateRatingToken(): string
    {
        $token = bin2hex(random_bytes(32));

        $this->update([
            'rating_token' => $token,
            'rating_token_expires_at' => now()->addDays(30)
        ]);

        return $token;
    }

    /**
     * Get the URL for rating this booking
     */
    public function getRatingUrl(): string
    {
        if (!$this->rating_token) {
            $this->generateRatingToken();
        }

        return route('ratings.guest.form', ['token' => $this->rating_token]);
    }

    /**
     * Check if the rating token is still valid
     */
    public function isRatingTokenValid(): bool
    {
        if (!$this->rating_token || !$this->rating_token_expires_at) {
            return false;
        }

        return $this->rating_token_expires_at->isFuture();
    }

    /**
     * Get the POS sale item associated with this booking
     */
    public function posSaleItem()
    {
        return $this->hasOne(PosSaleItem::class, 'booking_id');
    }
}