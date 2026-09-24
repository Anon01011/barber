<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';
    protected $description = 'Send booking reminders to customers';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        // Find bookings for tomorrow that haven't had a reminder sent
        // We look for bookings starting between now and 24 hours from now, 
        // effectively catching upcoming appointments for the next day.
        // Adjust logic as per specific requirement (e.g. exactly 24h before vs "the day before")
        
        // Let's go with "Tomorrow's bookings" logic for a daily cron job
        $start = Carbon::tomorrow()->startOfDay();
        $end = Carbon::tomorrow()->endOfDay();

        $bookings = Booking::whereBetween('start_time', [$start, $end])
            ->where('status', Booking::STATUS_CONFIRMED)
            ->where('reminder_sent', false)
            ->with(['customer', 'salon', 'service', 'staff']) // Eager load relationships
            ->get();

        $this->info("Found {$bookings->count()} bookings for reminders.");

        $count = 0;
        foreach ($bookings as $booking) {
            try {
                $this->notificationService->sendBookingReminder($booking);
                $booking->update(['reminder_sent' => true]);
                $count++;
                $this->info("Sent reminder for booking #{$booking->id}");
            } catch (\Exception $e) {
                Log::error("Failed to send reminder for booking {$booking->id}: " . $e->getMessage());
                $this->error("Failed to send reminder for booking #{$booking->id}");
            }
        }

        $this->info("Sent {$count} booking reminders.");
    }
}
