<?php

namespace App\Console\Commands;

use App\Services\DatabaseFailoverService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DatabaseHealthCheckCommand extends Command
{
    protected $signature = 'db:health-check {--force-failover : Force switch to replica regardless of primary state} {--force-primary : Force switch back to primary}';
    protected $description = 'Check primary and replica DB health; auto-activate or deactivate failover';

    public function handle(DatabaseFailoverService $service): int
    {
        if ($this->option('force-failover')) {
            $service->forceFailover();
            $this->warn('⚠️  MANUAL FAILOVER: App switched to replica DB.');
            return Command::SUCCESS;
        }

        if ($this->option('force-primary')) {
            $service->forcePrimary();
            $this->info('✅  MANUAL RESTORE: App switched back to primary DB.');
            return Command::SUCCESS;
        }

        $result = $service->runHealthCheck();

        $primaryLabel = $result['primary_ok'] ? '<fg=green>HEALTHY</>' : '<fg=red>DOWN</>';
        $replicaLabel = $result['replica_ok'] ? '<fg=green>HEALTHY</>' : '<fg=yellow>UNAVAILABLE</>';

        $this->line('');
        $this->line('  Primary DB : ' . $primaryLabel);
        $this->line('  Replica DB : ' . $replicaLabel);
        $this->line('  Failover   : ' . ($result['failover_active'] ? '<fg=red>ACTIVE (using replica)</>' : '<fg=green>STANDBY</>'));
        $this->line('  Action     : ' . $result['action']);
        $this->line('');

        if ($result['action'] === 'failover_activated') {
            $this->error('‼  Primary DB is DOWN — failover to replica ACTIVATED. Super admins notified.');
            return Command::FAILURE;
        }

        if ($result['action'] === 'primary_restored') {
            $this->info('✅  Primary DB RESTORED — traffic switched back. Super admins notified.');
        }

        return Command::SUCCESS;
    }
}
