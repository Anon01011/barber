<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendBookingSmsNotification implements ShouldQueue
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
    public function handle(BookingCreated $event)
    {
        try {
            $this->notificationService->sendBookingConfirmation($event->booking);
        } catch (\Exception $e) {
            Log::error("BookingCreated Listener Error: " . $e->getMessage());
        }
    }
}
