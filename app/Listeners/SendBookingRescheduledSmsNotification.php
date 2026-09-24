<?php

namespace App\Listeners;

use App\Events\BookingRescheduled;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendBookingRescheduledSmsNotification implements ShouldQueue
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
    public function handle(BookingRescheduled $event)
    {
        try {
            $this->notificationService->sendBookingRescheduled($event->booking);
        } catch (\Exception $e) {
            Log::error("BookingRescheduled Listener Error: " . $e->getMessage());
        }
    }
}
