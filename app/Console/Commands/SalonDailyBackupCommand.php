<?php

namespace App\Console\Commands;

use App\Models\Salon;
use App\Services\SalonBackupService;
use Illuminate\Console\Command;

class SalonDailyBackupCommand extends Command
{
    protected $signature = 'salon:daily-backup {salon_id? : Specific salon ID to backup}';
    protected $description = 'Generate daily salon backups and email them to salon admins';

    public function handle(SalonBackupService $service): int
    {
        $specificId = $this->argument('salon_id');

        if ($specificId) {
            $salons = Salon::where('id', $specificId)->get();
        } else {
            // Backup all active salons
            $salons = Salon::where('is_active', true)->get();
        }

        $this->info("Starting automated backup for {$salons->count()} salon(s)...");

        $success = 0;
        $failed = 0;

        foreach ($salons as $salon) {
            // Check plan entitlement for automated backup
            if (!$salon->canUseFeature('data_backup') && !$salon->canUseFeature('Data Backup & Safety') && !$salon->canUseFeature('Data Backup')) {
                $this->line("Skipped salon #{$salon->id}: {$salon->name} (data_backup module not in active plan)");
                continue;
            }

            $this->line("Processing salon #{$salon->id}: {$salon->name}...");
            $sent = $service->sendDailyBackupEmail($salon->id);
            if ($sent) {
                $this->info("✓ Backup emailed for {$salon->name}");
                $success++;
            } else {
                $this->warn("✗ Failed or skipped for {$salon->name}");
                $failed++;
            }
        }

        $this->info("Completed: {$success} sent, {$failed} skipped/failed.");
        return Command::SUCCESS;
    }
}
