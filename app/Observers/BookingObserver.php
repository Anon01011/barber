<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\CommissionService;
use App\Events\BookingCreated;
use App\Events\BookingStatusUpdated;
use App\Events\BookingRescheduled;

class BookingObserver
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        event(new BookingCreated($booking));
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // 1. Handle status change
        if ($booking->isDirty('status')) {
            $originalStatus = $booking->getOriginal('status');
            $newStatus = $booking->status;

            if ($originalStatus !== $newStatus) {
                // Dispatch event for listeners (Email, SMS, WhatsApp)
                event(new BookingStatusUpdated($booking, $originalStatus));

                // Specific logic for completed bookings
                if ($newStatus === 'completed') {
                    try {
                        $this->commissionService->calculateBookingCommission($booking);
                    } catch (\App\Exceptions\CommissionCalculationException $e) {
                        \Log::error($e->getMessage());
                    }
                }
            }
        }

        // 2. Handle reschedule (time change)
        if ($booking->isDirty('start_time') || $booking->isDirty('end_time')) {
            // Dispatch event for listeners (Email, SMS, WhatsApp)
            if ($booking->status !== 'cancelled') {
                event(new BookingRescheduled($booking));
            }
        }
    }
}
