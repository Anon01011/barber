<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class DatabaseFailoverService
{
    // Cache keys
    const KEY_FAILOVER_ACTIVE  = 'db_failover_active';
    const KEY_FAILOVER_SINCE   = 'db_failover_since';
    const KEY_PRIMARY_STATUS   = 'db_primary_status';
    const KEY_REPLICA_STATUS   = 'db_replica_status';
    const KEY_REPLICA_SYNCED   = 'replica_last_synced';
    const KEY_REPLICA_ROWS     = 'replica_last_row_count';
    const KEY_HEALTH_COOLDOWN  = 'db_health_check_cooldown';
    const KEY_FAILOVER_COUNT   = 'db_failover_count_today';

    const REPLICA_CONN         = 'mysql_replica';
    const PRIMARY_CONN         = 'mysql';

    /**
     * Check if primary DB is alive (with 3-second timeout).
     */
    public function isPrimaryHealthy(): bool
    {
        try {
            DB::connection(self::PRIMARY_CONN)->statement('SELECT 1');
            return true;
        } catch (\Exception $e) {
            Log::critical('[FailoverService] Primary DB UNREACHABLE: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if replica DB is alive.
     */
    public function isReplicaHealthy(): bool
    {
        try {
            // Check if replica connection is configured
            $replicaDb = config('database.connections.' . self::REPLICA_CONN . '.database');
            if (!$replicaDb) {
                return false;
            }
            DB::connection(self::REPLICA_CONN)->statement('SELECT 1');
            return true;
        } catch (\Exception $e) {
            Log::warning('[FailoverService] Replica DB UNREACHABLE: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Is failover currently active?
     */
    public function isFailoverActive(): bool
    {
        return (bool) Cache::get(self::KEY_FAILOVER_ACTIVE, false);
    }

    /**
     * Get current active connection name.
     */
    public function getActiveConnection(): string
    {
        return $this->isFailoverActive() ? self::REPLICA_CONN : self::PRIMARY_CONN;
    }

    /**
     * Full status report for health dashboard.
     */
    public function getStatus(): array
    {
        $isFailoverActive = $this->isFailoverActive();
        $primaryStatus    = Cache::get(self::KEY_PRIMARY_STATUS, 'unknown');
        $replicaStatus    = Cache::get(self::KEY_REPLICA_STATUS, 'unknown');
        $replicaSynced    = Cache::get(self::KEY_REPLICA_SYNCED);
        $failoverSince    = Cache::get(self::KEY_FAILOVER_SINCE);
        $failoverCount    = Cache::get(self::KEY_FAILOVER_COUNT, 0);

        // Compute replica lag (time since last sync)
        $lagMinutes = null;
        if ($replicaSynced) {
            try {
                $lagMinutes = (int) now()->diffInMinutes(\Carbon\Carbon::parse($replicaSynced));
            } catch (\Exception $e) {
                $lagMinutes = null;
            }
        }

        return [
            'failover_active'    => $isFailoverActive,
            'active_connection'  => $this->getActiveConnection(),
            'primary_status'     => $primaryStatus,
            'replica_status'     => $replicaStatus,
            'replica_synced_at'  => $replicaSynced,
            'replica_lag_minutes'=> $lagMinutes,
            'failover_since'     => $failoverSince,
            'failover_count_today' => $failoverCount,
            'primary_host'       => config('database.connections.' . self::PRIMARY_CONN . '.host', '127.0.0.1'),
            'replica_host'       => config('database.connections.' . self::REPLICA_CONN . '.host', 'N/A'),
            'replica_configured' => !empty(config('database.connections.' . self::REPLICA_CONN . '.database')),
            'rpo_hours'          => 1,
            'rto_minutes'        => 5,
        ];
    }

    /**
     * Run health probe and update cache stamps.
     * Returns true if state changed (failover activated or deactivated).
     */
    public function runHealthCheck(): array
    {
        $primaryOk  = $this->isPrimaryHealthy();
        $replicaOk  = $this->isReplicaHealthy();
        $wasActive  = $this->isFailoverActive();
        $stateChanged = false;
        $action = 'no_change';

        // Persist status for dashboard
        Cache::put(self::KEY_PRIMARY_STATUS, $primaryOk ? 'healthy' : 'down', 300);
        Cache::put(self::KEY_REPLICA_STATUS, $replicaOk ? 'healthy' : 'down', 300);

        if (!$primaryOk && $replicaOk && !$wasActive) {
            // Primary down, replica available → ACTIVATE FAILOVER
            $this->activateFailover();
            $stateChanged = true;
            $action = 'failover_activated';
        } elseif ($primaryOk && $wasActive) {
            // Primary recovered → DEACTIVATE FAILOVER
            $this->deactivateFailover();
            $stateChanged = true;
            $action = 'primary_restored';
        }

        // Stamp last health check time for scheduler health display
        Cache::put('last_run:db-health-check', now()->toIso8601String(), 7200);

        return [
            'primary_ok'   => $primaryOk,
            'replica_ok'   => $replicaOk,
            'failover_active' => $this->isFailoverActive(),
            'state_changed'=> $stateChanged,
            'action'       => $action,
        ];
    }

    /**
     * Activate failover: switch app to replica, alert super admins.
     */
    public function activateFailover(): void
    {
        Cache::forever(self::KEY_FAILOVER_ACTIVE, true);
        Cache::forever(self::KEY_FAILOVER_SINCE, now()->toIso8601String());
        Cache::increment(self::KEY_FAILOVER_COUNT);

        Log::critical('[FailoverService] ⚠️ DATABASE FAILOVER ACTIVATED — switched to replica', [
            'primary_host' => config('database.connections.' . self::PRIMARY_CONN . '.host'),
            'replica_host' => config('database.connections.' . self::REPLICA_CONN . '.host'),
            'at'           => now()->toIso8601String(),
        ]);

        $this->notifySuperAdmins(
            '🔴 CRITICAL: Database Failover Activated',
            'The primary database went offline. The system automatically switched to the replica database. ' .
            'Primary: ' . config('database.connections.' . self::PRIMARY_CONN . '.host') . ' | ' .
            'Replica: ' . config('database.connections.' . self::REPLICA_CONN . '.host') . ' | ' .
            'Time: ' . now()->format('Y-m-d H:i:s T')
        );
    }

    /**
     * Deactivate failover: switch back to primary, notify admins.
     */
    public function deactivateFailover(): void
    {
        Cache::forget(self::KEY_FAILOVER_ACTIVE);
        Cache::forget(self::KEY_FAILOVER_SINCE);

        Log::info('[FailoverService] ✅ DATABASE FAILOVER DEACTIVATED — primary DB restored', [
            'at' => now()->toIso8601String(),
        ]);

        $this->notifySuperAdmins(
            '✅ Primary DB Restored',
            'The primary database is back online. Traffic has been automatically switched back from replica to primary. Time: ' . now()->format('Y-m-d H:i:s T')
        );
    }

    /**
     * Force manual activation of failover (for maintenance).
     */
    public function forceFailover(): void
    {
        Cache::forever(self::KEY_FAILOVER_ACTIVE, true);
        Cache::forever(self::KEY_FAILOVER_SINCE, now()->toIso8601String());

        Log::warning('[FailoverService] ⚙️ MANUAL FAILOVER ACTIVATED by super admin', [
            'at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Force manual restore to primary (for maintenance end).
     */
    public function forcePrimary(): void
    {
        Cache::forget(self::KEY_FAILOVER_ACTIVE);
        Cache::forget(self::KEY_FAILOVER_SINCE);

        Log::info('[FailoverService] ⚙️ MANUAL RESTORE TO PRIMARY by super admin', [
            'at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Sync replica DB from the latest SQL snapshot file.
     * This restores the snapshot into the mysql_replica connection.
     */
    public function syncReplicaFromSnapshot(): array
    {
        $snapshotPath = storage_path('app/backups/database/replica_latest.sql');

        if (!file_exists($snapshotPath)) {
            throw new \RuntimeException('Replica snapshot not found: ' . $snapshotPath);
        }

        $fileSizeMb = round(filesize($snapshotPath) / 1024 / 1024, 2);
        Log::info('[FailoverService] Starting replica sync from snapshot', [
            'file'    => $snapshotPath,
            'size_mb' => $fileSizeMb,
        ]);

        $replicaDb = config('database.connections.' . self::REPLICA_CONN . '.database');
        if (!$replicaDb) {
            throw new \RuntimeException('mysql_replica connection not configured in database.php.');
        }

        // Ensure replica DB exists
        try {
            DB::connection(self::REPLICA_CONN)->statement("CREATE DATABASE IF NOT EXISTS `{$replicaDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (\Exception $e) {
            // May already exist — ignore
        }

        // Execute the snapshot SQL in chunks
        $sql = file_get_contents($snapshotPath);
        $statements = array_filter(array_map('trim', explode(";\n", $sql)));
        $executed = 0;
        $errors = 0;

        DB::connection(self::REPLICA_CONN)->statement('SET FOREIGN_KEY_CHECKS=0');
        DB::connection(self::REPLICA_CONN)->statement('SET SQL_MODE=""');

        foreach ($statements as $stmt) {
            if (empty($stmt) || str_starts_with($stmt, '--')) continue;
            try {
                DB::connection(self::REPLICA_CONN)->unprepared($stmt . ';');
                $executed++;
            } catch (\Exception $e) {
                $errors++;
                Log::warning('[FailoverService] Replica sync stmt error: ' . $e->getMessage());
            }
        }

        DB::connection(self::REPLICA_CONN)->statement('SET FOREIGN_KEY_CHECKS=1');

        // Stamp sync time
        Cache::put(self::KEY_REPLICA_SYNCED, now()->toIso8601String(), 86400 * 7);
        Cache::put('last_run:db-sync-replica', now()->toIso8601String(), 7200);

        Log::info('[FailoverService] ✅ Replica sync complete', [
            'statements_executed' => $executed,
            'errors'             => $errors,
            'size_mb'            => $fileSizeMb,
        ]);

        return [
            'success'    => $errors === 0,
            'executed'   => $executed,
            'errors'     => $errors,
            'size_mb'    => $fileSizeMb,
            'synced_at'  => now()->toIso8601String(),
        ];
    }

    /**
     * Notify all super admins via database notification + email.
     */
    private function notifySuperAdmins(string $title, string $message): void
    {
        try {
            $superAdmins = User::role('super_admin')->get();
            if ($superAdmins->isEmpty()) return;

            foreach ($superAdmins as $admin) {
                $admin->notify(new \App\Notifications\SystemAlertNotification($title, $message, 'danger'));
            }
        } catch (\Exception $e) {
            Log::error('[FailoverService] Failed to notify super admins: ' . $e->getMessage());
        }
    }
}
