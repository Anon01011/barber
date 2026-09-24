<?php

namespace App\Listeners;

use App\Events\BookingStatusUpdated;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendBookingStatusSmsNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(\App\Services\NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(BookingStatusUpdated $event)
    {
        try {
            $this->notificationService->sendBookingStatusUpdate($event->booking, $event->oldStatus);
        } catch (\Exception $e) {
            Log::error("BookingStatusUpdated Listener Error: " . $e->getMessage());
        }
    }
}
