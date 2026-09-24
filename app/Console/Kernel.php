<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\AssignSuperAdminPermissions::class,
        Commands\SendBookingReminders::class,
        Commands\SendDailySummary::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Send booking reminders
        $schedule->command('bookings:send-reminders')->dailyAt('09:00');

        // Check for expired subscriptions and send expiry notifications
        $schedule->command('subscriptions:check-expired')->dailyAt('01:00');

        // Send renewal reminders
        $schedule->command('subscriptions:send-renewal-reminders')->dailyAt('10:00');

        // Send daily summary emails
        // Runs every minute to check if any salon's daily_summary_time has arrived in their timezone
        $schedule->command('salon:send-daily-summary')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}