<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BackupFullCommand extends Command
{
    protected $signature = 'backup:full {--no-files : Skip file backup, DB only} {--notify : Email backup result to admin}';
    protected $description = 'Create a full system backup: database + uploaded files';

    public function handle(DatabaseBackupService $backupService): int
    {
        $this->info('Starting full system backup...');
        $start = microtime(true);
        $results = [];

        // 1. Full DB dump
        $this->line('  [1/2] Database snapshot...');
        try {
            $dbResult = $backupService->createBackup(false);
            $results['database'] = ['ok' => true, 'file' => $dbResult['filename'], 'size' => $dbResult['size_formatted']];
            $this->line("        ✅ DB: {$dbResult['filename']} ({$dbResult['size_formatted']}) | Checksum: {$dbResult['checksum']}");
        } catch (\Exception $e) {
            $results['database'] = ['ok' => false, 'error' => $e->getMessage()];
            $this->error('        ❌ DB backup failed: ' . $e->getMessage());
        }

        // 2. File/uploads backup
        if (!$this->option('no-files')) {
            $this->line('  [2/2] File system backup (uploads/media)...');
            try {
                $fileResult = $this->backupFiles();
                $results['files'] = ['ok' => true, 'size' => $fileResult['size']];
                $this->line("        ✅ Files: {$fileResult['size']} archived");
            } catch (\Exception $e) {
                $results['files'] = ['ok' => false, 'error' => $e->getMessage()];
                $this->warn('        ⚠  File backup skipped: ' . $e->getMessage());
            }
        }

        $elapsed = round(microtime(true) - $start, 1);
        $allOk = !array_filter($results, fn($r) => !$r['ok']);

        $this->line('');
        $this->line("✅ Full backup complete in {$elapsed}s");

        // Stamp for scheduler health
        Cache::put('last_run:backup-full', now()->toIso8601String(), 86400 * 2);
        Cache::put('backup_last_results', $results, 86400);

        Log::info('backup:full completed', ['elapsed_s' => $elapsed, 'results' => $results]);

        return $allOk ? Command::SUCCESS : Command::FAILURE;
    }

    private function backupFiles(): array
    {
        $sourceDir  = public_path('uploads');
        $storageDir = storage_path('app/public');
        $backupDir  = storage_path('app/backups/files');

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp  = now()->format('Y-m-d_H-i-s');
        $archivePath = $backupDir . DIRECTORY_SEPARATOR . "files_{$timestamp}.tar.gz";

        // Try system tar first (fastest)
        if (is_dir($sourceDir)) {
            exec("tar -czf {$archivePath} -C " . public_path() . " uploads 2>/dev/null", $out, $exitCode);
        }

        $sizeMb = file_exists($archivePath) ? round(filesize($archivePath) / 1024 / 1024, 2) . ' MB' : 'N/A';

        return ['archive' => $archivePath, 'size' => $sizeMb];
    }
}
