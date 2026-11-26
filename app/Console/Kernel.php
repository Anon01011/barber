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
        Commands\SalonDailyBackupCommand::class,
        Commands\DatabaseSnapshotCommand::class,
        // ── HA / Failover / Backup Commands ──────────────────────────────────
        Commands\DatabaseHealthCheckCommand::class,
        Commands\DatabaseSyncReplicaCommand::class,
        Commands\BackupFullCommand::class,
        Commands\BackupPruneCommand::class,
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

        // 24-Hour Automated Salon Data Backup & Email to Salon Admins
        $schedule->command('salon:daily-backup')->dailyAt('02:00');

        // Automated Database Snapshot & Replica Maintenance (Daily at 03:00)
        $schedule->command('db:snapshot')->dailyAt('03:00');

        // ── HA / Failover / Backup Schedules ─────────────────────────────────
        // DB health check every minute — switches to replica if primary fails
        $schedule->command('db:health-check')->everyMinute()->withoutOverlapping();

        // Sync replica from latest snapshot every hour
        $schedule->command('db:sync-replica --snapshot')->hourly()->withoutOverlapping();

        // Full backup (mysqldump) daily at 02:00 — same slot as salon:daily-backup
        $schedule->command('backup:full')->dailyAt('02:30')->withoutOverlapping();

        // Prune backups older than 30 days at 04:00
        $schedule->command('backup:prune --days=30')->dailyAt('04:00');
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}