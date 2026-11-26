<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemHealthService
{
    /**
     * Get comprehensive system metrics.
     */
    public function getMetrics(): array
    {
        return [
            'database' => $this->getDatabaseMetrics(),
            'storage' => $this->getStorageMetrics(),
            'server' => $this->getServerMetrics(),
            'logs' => $this->getLogMetrics(),
            'queue' => $this->getQueueMetrics(),
            'activity_trend' => $this->getActivityTrend(),
        ];
    }

    /**
     * Real MySQL Database Health and Size Metrics.
     */
    public function getDatabaseMetrics(): array
    {
        try {
            $dbName = config('database.connections.mysql.database');

            // Total Database Size in MB
            $sizeRow = DB::selectOne("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS total_mb,
                    ROUND(SUM(data_length) / 1024 / 1024, 2) AS data_mb,
                    ROUND(SUM(index_length) / 1024 / 1024, 2) AS index_mb,
                    COUNT(table_name) AS table_count
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$dbName]);

            // Database Server Status
            $connections = 0;
            $uptimeSeconds = 0;
            $queries = 0;

            $statusVars = DB::select("SHOW STATUS WHERE Variable_name IN ('Threads_connected', 'Uptime', 'Questions', 'Slow_queries')");
            foreach ($statusVars as $var) {
                if ($var->Variable_name === 'Threads_connected') $connections = (int)$var->Value;
                if ($var->Variable_name === 'Uptime') $uptimeSeconds = (int)$var->Value;
                if ($var->Variable_name === 'Questions') $queries = (int)$var->Value;
            }

            // MySQL Version
            $versionRow = DB::selectOne("SELECT VERSION() AS ver");
            $mysqlVersion = $versionRow ? $versionRow->ver : 'MySQL';

            return [
                'status' => 'healthy',
                'database_name' => $dbName,
                'version' => $mysqlVersion,
                'total_size_mb' => (float)($sizeRow->total_mb ?? 0),
                'data_size_mb' => (float)($sizeRow->data_mb ?? 0),
                'index_size_mb' => (float)($sizeRow->index_mb ?? 0),
                'table_count' => (int)($sizeRow->table_count ?? 0),
                'connections' => $connections,
                'uptime_hours' => round($uptimeSeconds / 3600, 1),
                'uptime_days' => round($uptimeSeconds / 86400, 1),
                'queries_count' => $queries,
            ];
        } catch (\Exception $e) {
            Log::error('SystemHealthService getDatabaseMetrics Error: ' . $e->getMessage());
            return [
                'status' => 'degraded',
                'database_name' => config('database.connections.mysql.database'),
                'version' => 'Unknown',
                'total_size_mb' => 0,
                'data_size_mb' => 0,
                'index_size_mb' => 0,
                'table_count' => 0,
                'connections' => 0,
                'uptime_hours' => 0,
                'uptime_days' => 0,
                'queries_count' => 0,
            ];
        }
    }

    /**
     * Get top database tables breakdown.
     */
    public function getTableBreakdown(int $limit = 15): array
    {
        try {
            $dbName = config('database.connections.mysql.database');
            $rows = DB::select("
                SELECT 
                    table_name AS name,
                    table_rows AS `rows`,
                    ROUND((data_length + index_length) / 1024 / 1024, 2) AS size_mb,
                    ROUND((data_length + index_length) / 1024 / 1024, 2) AS size,
                    ROUND(data_length / 1024 / 1024, 2) AS data_mb,
                    ROUND(index_length / 1024 / 1024, 2) AS index_mb,
                    engine AS `engine`,
                    table_collation AS collation
                FROM information_schema.tables 
                WHERE table_schema = ?
                ORDER BY (data_length + index_length) DESC
                LIMIT ?
            ", [$dbName, $limit]);

            foreach ($rows as $r) {
                $r->engine = $r->engine ?? $r->ENGINE ?? 'InnoDB';
            }

            return $rows;
        } catch (\Exception $e) {
            Log::error('SystemHealthService getTableBreakdown Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Storage Usage Breakdown.
     */
    public function getStorageMetrics(): array
    {
        $storagePath = storage_path();
        $totalDisk = @disk_total_space($storagePath) ?: 1;
        $freeDisk = @disk_free_space($storagePath) ?: 0;
        $usedDisk = $totalDisk - $freeDisk;

        $uploadsSize = $this->calculateDirectorySize(public_path('uploads'));
        $publicStorageSize = $this->calculateDirectorySize(storage_path('app/public'));
        $logsSize = $this->calculateDirectorySize(storage_path('logs'));
        $cacheSize = $this->calculateDirectorySize(storage_path('framework/cache'));
        $backupSize = $this->calculateDirectorySize(storage_path('app/backups'));

        return [
            'disk_total_gb' => round($totalDisk / 1024 / 1024 / 1024, 2),
            'disk_free_gb' => round($freeDisk / 1024 / 1024 / 1024, 2),
            'disk_used_gb' => round($usedDisk / 1024 / 1024 / 1024, 2),
            'disk_used_percentage' => round(($usedDisk / $totalDisk) * 100, 1),
            'uploads_mb' => round(($uploadsSize + $publicStorageSize) / 1024 / 1024, 2),
            'logs_mb' => round($logsSize / 1024 / 1024, 2),
            'cache_mb' => round($cacheSize / 1024 / 1024, 2),
            'backups_mb' => round($backupSize / 1024 / 1024, 2),
        ];
    }

    /**
     * Log File Health & Statistics.
     */
    public function getLogMetrics(): array
    {
        $logPath = storage_path('logs/laravel.log');
        $sizeBytes = File::exists($logPath) ? File::size($logPath) : 0;
        $sizeMb = round($sizeBytes / 1024 / 1024, 2);

        $errorCount = 0;
        $recentLines = [];

        if (File::exists($logPath) && $sizeBytes > 0) {
            // Read last 250KB safely to avoid memory exhaustion
            $readBytes = min(250 * 1024, $sizeBytes);
            $fp = fopen($logPath, 'r');
            if ($fp) {
                fseek($fp, -$readBytes, SEEK_END);
                $buffer = fread($fp, $readBytes);
                fclose($fp);

                $lines = explode("\n", $buffer);
                $recentLines = array_slice(array_reverse(array_filter(array_map('trim', $lines))), 0, 30);

                foreach ($lines as $line) {
                    if (stripos($line, '.ERROR:') !== false || stripos($line, '.CRITICAL:') !== false || stripos($line, '.EMERGENCY:') !== false) {
                        $errorCount++;
                    }
                }
            }
        }

        return [
            'size_mb' => $sizeMb,
            'size_formatted' => $sizeMb >= 1024 ? round($sizeMb / 1024, 2) . ' GB' : $sizeMb . ' MB',
            'error_count' => $errorCount,
            'recent_entries' => $recentLines,
            'exists' => File::exists($logPath),
        ];
    }

    /**
     * Server, Runtime & PHP Details.
     */
    public function getServerMetrics(): array
    {
        $memUsage = memory_get_usage(true);
        $memPeak = memory_get_peak_usage(true);

        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'os' => PHP_OS . ' (' . php_uname('m') . ')',
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'PHP CLI / Built-in',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'opcache_enabled' => function_exists('opcache_get_status') && !empty(opcache_get_status(false)['opcache_enabled']),
            'memory_used_mb' => round($memUsage / 1024 / 1024, 2),
            'memory_peak_mb' => round($memPeak / 1024 / 1024, 2),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
        ];
    }

    /**
     * Background Queue Health.
     */
    public function getQueueMetrics(): array
    {
        try {
            $failed = DB::table('failed_jobs')->count();
            $pending = DB::table('jobs')->count();
            return [
                'pending' => $pending,
                'failed' => $failed,
                'status' => $failed > 0 ? 'attention' : 'optimal',
            ];
        } catch (\Exception $e) {
            return [
                'pending' => 0,
                'failed' => 0,
                'status' => 'optimal',
            ];
        }
    }

    /**
     * Activity trend for graph (last 7 days appointment and registration load).
     */
    public function getActivityTrend(): array
    {
        return Cache::remember('saas_health_activity_trend', 300, function () {
            $days = collect();
            $bookingsData = collect();
            $salonsData = collect();

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                $days->push($date->format('M d'));

                try {
                    $bookingsCount = DB::table('bookings')
                        ->whereDate('created_at', $dateStr)
                        ->count();
                    $bookingsData->push($bookingsCount);

                    $salonsCount = DB::table('salons')
                        ->whereDate('created_at', $dateStr)
                        ->count();
                    $salonsData->push($salonsCount);
                } catch (\Exception $e) {
                    $bookingsData->push(0);
                    $salonsData->push(0);
                }
            }

            return [
                'labels' => $days->toArray(),
                'bookings' => $bookingsData->toArray(),
                'salons' => $salonsData->toArray(),
            ];
        });
    }

    /**
     * Safely calculate directory size in bytes.
     */
    private function calculateDirectorySize(string $dir): int
    {
        if (!File::isDirectory($dir)) {
            return 0;
        }

        $size = 0;
        try {
            foreach (File::allFiles($dir) as $file) {
                $size += $file->getSize();
            }
        } catch (\Exception $e) {
            // Permission or locking fallback
        }
        return $size;
    }
}
