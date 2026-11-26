<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class DatabaseSnapshotCommand extends Command
{
    protected $signature = 'db:snapshot {--replica-only : Only update the standby replica snapshot}';
    protected $description = 'Create a database backup snapshot and maintain standby replica';

    public function handle(DatabaseBackupService $service): int
    {
        $isReplicaOnly = $this->option('replica-only');
        $this->info('Generating database snapshot & replica...');

        try {
            $result = $service->createBackup($isReplicaOnly);
            $this->info("✓ Database backup completed: {$result['filename']} ({$result['size_formatted']})");
            $this->line("Checksum: {$result['checksum']}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to generate database snapshot: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
