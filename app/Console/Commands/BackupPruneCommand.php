<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BackupPruneCommand extends Command
{
    protected $signature = 'backup:prune {--days=30 : Delete backups older than N days} {--dry-run : List files to delete without deleting}';
    protected $description = 'Delete old backup files beyond the retention period';

    public function handle(): int
    {
        $days      = (int) $this->option('days');
        $dryRun    = $this->option('dry-run');
        $backupDir = storage_path('app/backups/database');
        $filesDir  = storage_path('app/backups/files');
        $cutoff    = now()->subDays($days)->timestamp;

        $deleted   = 0;
        $freed     = 0;

        foreach ([$backupDir, $filesDir] as $dir) {
            if (!is_dir($dir)) continue;

            $files = glob($dir . DIRECTORY_SEPARATOR . '*');
            foreach ($files as $file) {
                // Never delete the latest replica file
                if (basename($file) === 'replica_latest.sql') continue;

                if (filemtime($file) < $cutoff) {
                    $size  = filesize($file);
                    $freed += $size;
                    $this->line(($dryRun ? '[DRY] ' : '') . "🗑  " . basename($file) . " (" . round($size / 1024, 1) . " KB)");

                    if (!$dryRun) {
                        unlink($file);
                        $deleted++;
                    }
                }
            }
        }

        $freedMb = round($freed / 1024 / 1024, 2);
        $verb    = $dryRun ? 'Would free' : 'Freed';
        $this->info("{$verb} {$freedMb} MB across {$deleted} backup files older than {$days} days.");

        Cache::put('last_run:backup-prune', now()->toIso8601String(), 86400 * 2);

        Log::info('backup:prune completed', ['deleted' => $deleted, 'freed_mb' => $freedMb, 'dry_run' => $dryRun]);

        return Command::SUCCESS;
    }
}
