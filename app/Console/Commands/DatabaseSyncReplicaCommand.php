<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use App\Services\DatabaseFailoverService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DatabaseSyncReplicaCommand extends Command
{
    protected $signature = 'db:sync-replica {--snapshot : Also create a fresh full snapshot before syncing}';
    protected $description = 'Sync the replica DB from the latest backup snapshot (hourly)';

    public function handle(DatabaseBackupService $backupService, DatabaseFailoverService $failoverService): int
    {
        $this->info('Starting replica sync...');

        try {
            // Optionally create a fresh snapshot first
            if ($this->option('snapshot')) {
                $this->line('  → Generating fresh snapshot before sync...');
                $backup = $backupService->createBackup(true); // replica_only = true (updates replica_latest.sql)
                $this->line("  → Snapshot: {$backup['filename']} ({$backup['size_formatted']})");
            }

            // Sync from snapshot into mysql_replica DB
            $result = $failoverService->syncReplicaFromSnapshot();

            if ($result['success']) {
                $this->info("✅  Replica synced successfully.");
                $this->line("    Statements: {$result['executed']} | Errors: {$result['errors']} | Size: {$result['size_mb']} MB");
            } else {
                $this->warn("⚠  Replica synced with {$result['errors']} error(s). Check laravel.log.");
            }

            // Stamp for scheduler health dashboard
            Cache::put('last_run:db-sync-replica', now()->toIso8601String(), 7200);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to sync replica: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('db:sync-replica failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
