<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\CommissionService;
use App\Events\BookingCreated;
use App\Events\BookingStatusUpdated;
use App\Events\BookingRescheduled;

class BookingObserver
{
    /**
     * Handle events after all transactions have committed.
     */
    public bool $afterCommit = true;

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

        // Check if new booking notification is enabled for this salon
        $notifyEnabled = app(\App\Services\SettingsService::class)->get('notify_new_booking', true, $booking->salon_id);
        if (!$notifyEnabled) {
            return;
        }

        // Automatically dispatch salon-scoped notifications (system & email)
        try {
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifySalonAdmins($booking, 'created');

            // If staff is already assigned on creation, also notify the assigned staff
            if ($booking->staff_id) {
                $notificationService->notifyStaffOfNewBooking($booking);
            }
        } catch (\Exception $e) {
            \Log::error('BookingObserver created notification error: ' . $e->getMessage(), [
                'booking_id' => $booking->id
            ]);
        }
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

                    // Send system & email notification to the assigned staff member
                    try {
                        $booking->loadMissing(['staff', 'service', 'customer']);
                        if ($booking->staff) {
                            \Illuminate\Support\Facades\Notification::send(
                                $booking->staff,
                                new \App\Notifications\BookingNotification($booking, 'completed')
                            );
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to notify staff of completed booking: ' . $e->getMessage());
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
