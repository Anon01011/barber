<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToSalon;
use App\Traits\BelongsToBranch;

/**
 * Appointment Model
 * 
 * Represents scheduled appointments for salon services.
 * Multi-tenancy: Scoped to salon via BelongsToSalon trait
 */
class Appointment extends Model
{
    use SoftDeletes, BelongsToSalon, BelongsToBranch;

    protected $fillable = [
        'salon_id',
        'branch_id',
        'customer_id',
        'service_id',
        'staff_id',
        'start_time',
        'end_time',
        'amount',
        'tip_amount',
        'payment_method',
        'payment_status',
        'paid_at',
        'status',
        'notes',
        'reminder_sent',
        'cancellation_reason'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'amount' => 'decimal:2',
        'tip_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'reminder_sent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the customer who booked the appointment.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the service for the appointment.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the staff member assigned to the appointment.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Scope a query to only include upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())
                    ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope a query to only include past appointments.
     */
    public function scopePast($query)
    {
        return $query->where('end_time', '<', now());
    }

    /**
     * Scope a query to only include appointments for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('start_time', $date);
    }

    /**
     * Check if the appointment is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->start_time->isFuture() && $this->status !== 'cancelled';
    }

    /**
     * Check if the appointment is past.
     */
    public function isPast(): bool
    {
        return $this->end_time->isPast();
    }

    /**
     * Check if the appointment is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get the duration of the appointment in minutes.
     */
    public function getDurationAttribute(): int
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * Get the formatted start time.
     */
    public function getFormattedStartTimeAttribute(): string
    {
        return $this->start_time->format('M d, Y h:i A');
    }

    /**
     * Get the formatted end time.
     */
    public function getFormattedEndTimeAttribute(): string
    {
        return $this->end_time->format('M d, Y h:i A');
    }
    /**
     * Check for scheduling conflicts in both appointments and bookings tables.
     * 
     * @param int $staffId
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param int|null $excludeAppointmentId
     * @return bool
     */
    public static function hasConflict($staffId, $startTime, $endTime, $excludeAppointmentId = null)
    {
        // Check appointments table - ACROSS ALL BRANCHES
        $appointmentConflict = self::withoutGlobalScope('branch')
            ->where('staff_id', $staffId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeAppointmentId) {
            $appointmentConflict->where('id', '!=', $excludeAppointmentId);
        }

        if ($appointmentConflict->exists()) {
            return true;
        }

        // Check bookings table - ACROSS ALL BRANCHES
        // Note: Using fully qualified class name to avoid circular dependency issues if imported
        $bookingConflict = \App\Models\Booking::withoutGlobalScope('branch')
            ->where('staff_id', $staffId)
            ->where('status', '!=', \App\Models\Booking::STATUS_CANCELLED)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();

        return $bookingConflict;
    }
} 