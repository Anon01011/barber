<?php

namespace App\Console\Commands;

use App\Services\DatabaseReplicaService;
use Illuminate\Console\Command;

class DatabaseReplicaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:replica 
                            {--create : Create a new snapshot and rotate 3 rolling replicas}
                            {--list : Show current status of the 3 database replicas}
                            {--restore= : Restore database from replica slot (1, 2, or 3)}
                            {--verify : Check database connectivity and core table integrity}
                            {--auto-recover : Auto-recover if database corruption/crash is detected}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage rolling 3-replica database snapshots, runtime updates, health verification, and crash failover';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseReplicaService $replicaService): int
    {
        // 1. Restore action
        if ($slot = $this->option('restore')) {
            $slotNum = (int) $slot;
            $this->warn("Initiating database restore from Replica Slot {$slotNum}...");

            if (!$this->confirm("Are you sure you want to overwrite the current database with Replica Slot {$slotNum}?", true)) {
                $this->info("Restore cancelled by user.");
                return Command::SUCCESS;
            }

            $result = $replicaService->restoreReplica($slotNum);
            if ($result['success']) {
                $this->info("SUCCESS: " . $result['message']);
                return Command::SUCCESS;
            }

            $this->error("FAILED: " . ($result['error'] ?? 'Unknown error occurred.'));
            return Command::FAILURE;
        }

        // 2. Verify health action
        if ($this->option('verify')) {
            $this->info("Verifying database health and integrity...");
            $health = $replicaService->checkHealth();

            $this->table(['Check', 'Status'], [
                ['Connection', $health['connected'] ? 'CONNECTED' : 'FAILED'],
                ['Core Tables Present', $health['tables_present'] ? 'ALL PRESENT' : 'MISSING TABLES'],
                ['Overall Status', $health['healthy'] ? 'HEALTHY' : 'UNHEALTHY / CRASHED'],
                ['Recommendation', $health['recommendation']],
            ]);

            return $health['healthy'] ? Command::SUCCESS : Command::FAILURE;
        }

        // 3. Auto-recover action
        if ($this->option('auto-recover')) {
            $this->info("Running auto-recovery check...");
            $result = $replicaService->autoRecoverIfCrashed();

            $this->info("Status: " . $result['status']);
            $this->info("Action: " . ($result['action_taken'] ?? 'none'));

            return $result['status'] === 'failed' ? Command::FAILURE : Command::SUCCESS;
        }

        // 4. Default or explicit --create action
        if ($this->option('create') || !$this->option('list')) {
            $this->info("Creating new runtime database snapshot and rotating 3 replicas...");
            $result = $replicaService->createReplica();

            if ($result['success']) {
                $this->info("Snapshot created! Tables: {$result['tables_count']}, Total Rows: {$result['total_rows']}");
            } else {
                $this->error("Failed to create snapshot: " . ($result['error'] ?? 'Unknown error'));
                return Command::FAILURE;
            }
        }

        // 5. Always display the 3 replicas list
        $replicas = $replicaService->listReplicas();
        $rows = [];

        foreach ($replicas as $slot => $rep) {
            $rows[] = [
                'Slot ' . $slot,
                $rep['name'],
                $rep['exists'] ? 'EXISTS' : 'NOT FOUND',
                $rep['size_formatted'],
                $rep['updated_at'],
                $rep['sha256'] ? substr($rep['sha256'], 0, 16) . '...' : 'N/A',
            ];
        }

        $this->newLine();
        $this->info("Current 3 Database Replicas Status:");
        $this->table(['Slot', 'File', 'Status', 'Size', 'Updated At', 'Checksum (SHA256)'], $rows);

        return Command::SUCCESS;
    }
}
