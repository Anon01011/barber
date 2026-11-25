<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use PDO;

class DatabaseReplicaService
{
    protected string $replicaDir;
    protected int $maxReplicas = 3;
    protected string $manifestFile;

    public function __construct()
    {
        $this->replicaDir = storage_path('app/db-replicas');
        $this->manifestFile = $this->replicaDir . '/manifest.json';

        if (!File::exists($this->replicaDir)) {
            File::makeDirectory($this->replicaDir, 0755, true);
        }
    }

    /**
     * Create a new database snapshot and rotate replicas to always maintain exactly 3.
     */
    public function createReplica(): array
    {
        try {
            $pdo = DB::connection()->getPdo();
            $tables = $this->getBaseTables($pdo);

            $tempFile = $this->replicaDir . '/temp_replica_' . time() . '.sql';
            $handle = fopen($tempFile, 'w');

            if (!$handle) {
                throw new \RuntimeException("Unable to create temporary snapshot file.");
            }

            // Header directives for transaction safety and speed
            fwrite($handle, "-- Barber / Salon CMS Database Replica Snapshot\n");
            fwrite($handle, "-- Generated At: " . now()->toIso8601String() . "\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
            fwrite($handle, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n");
            fwrite($handle, "SET AUTOCOMMIT=0;\n");
            fwrite($handle, "START TRANSACTION;\n\n");

            $totalRows = 0;

            foreach ($tables as $table) {
                // Export CREATE TABLE
                $stmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                if ($row && isset($row[1])) {
                    fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                    fwrite($handle, $row[1] . ";\n\n");
                }

                // Export table data in chunks
                $countStmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
                $rowCount = (int) $countStmt->fetchColumn();
                $totalRows += $rowCount;

                if ($rowCount > 0) {
                    $chunkSize = 100;
                    $offset = 0;

                    while ($offset < $rowCount) {
                        $dataStmt = $pdo->query("SELECT * FROM `{$table}` LIMIT {$chunkSize} OFFSET {$offset}");
                        $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

                        if (!empty($rows)) {
                            $columns = array_map(fn($c) => "`{$c}`", array_keys($rows[0]));
                            $colStr = implode(', ', $columns);

                            fwrite($handle, "INSERT INTO `{$table}` ({$colStr}) VALUES\n");

                            $valueRows = [];
                            foreach ($rows as $dataRow) {
                                $escapedValues = array_map(function ($val) use ($pdo) {
                                    if ($val === null) {
                                        return 'NULL';
                                    }
                                    return $pdo->quote($val);
                                }, array_values($dataRow));

                                $valueRows[] = "(" . implode(', ', $escapedValues) . ")";
                            }

                            fwrite($handle, implode(",\n", $valueRows) . ";\n\n");
                        }

                        $offset += $chunkSize;
                    }
                }
            }

            // Commit and re-enable constraints
            fwrite($handle, "COMMIT;\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);

            // Rotate existing 3 replicas:
            // replica_2 -> replica_3
            // replica_1 -> replica_2
            // temp -> replica_1
            $replica1 = $this->replicaDir . '/replica_1.sql';
            $replica2 = $this->replicaDir . '/replica_2.sql';
            $replica3 = $this->replicaDir . '/replica_3.sql';

            if (File::exists($replica2)) {
                File::copy($replica2, $replica3);
            }
            if (File::exists($replica1)) {
                File::copy($replica1, $replica2);
            }
            File::move($tempFile, $replica1);

            $manifest = $this->updateManifest();

            Log::info("Database replica successfully created and rotated. Total tables: " . count($tables) . ", Total rows: " . $totalRows);

            return [
                'success' => true,
                'message' => 'Replica created and rotated successfully.',
                'tables_count' => count($tables),
                'total_rows' => $totalRows,
                'manifest' => $manifest,
            ];
        } catch (\Throwable $e) {
            Log::error("Failed to create database replica: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Restore database from one of the 3 replicas (slot 1, 2, or 3).
     */
    public function restoreReplica(int $slot = 1): array
    {
        if ($slot < 1 || $slot > $this->maxReplicas) {
            return ['success' => false, 'error' => "Invalid replica slot: {$slot}. Must be 1, 2, or 3."];
        }

        $replicaFile = $this->replicaDir . "/replica_{$slot}.sql";

        if (!File::exists($replicaFile)) {
            return ['success' => false, 'error' => "Replica slot {$slot} does not exist at {$replicaFile}."];
        }

        try {
            $pdo = DB::connection()->getPdo();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
            $pdo->exec("SET AUTOCOMMIT=0;");
            $pdo->beginTransaction();

            $sql = File::get($replicaFile);
            $statements = array_filter(array_map('trim', explode(";\n", $sql)));

            foreach ($statements as $stmt) {
                if (!empty($stmt) && !str_starts_with($stmt, '--')) {
                    $pdo->exec($stmt);
                }
            }

            $pdo->commit();
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");

            // Flush caches after restore
            try {
                \Illuminate\Support\Facades\Cache::flush();
                \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');
                \Illuminate\Support\Facades\Artisan::call('view:clear');
            } catch (\Throwable $ignored) {}

            Log::warning("Database was restored from replica slot {$slot}.");

            return [
                'success' => true,
                'message' => "Database restored successfully from replica slot {$slot}.",
                'restored_file' => basename($replicaFile),
            ];
        } catch (\Throwable $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }

            Log::error("Failed to restore database from replica {$slot}: " . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify database health and check if failover is required.
     */
    public function checkHealth(): array
    {
        $status = [
            'connected' => false,
            'tables_present' => false,
            'core_tables' => [],
            'healthy' => false,
            'recommendation' => 'healthy',
        ];

        try {
            DB::connection()->getPdo();
            $status['connected'] = true;

            $coreTables = ['users', 'salons', 'roles', 'bookings', 'services', 'settings'];
            $existing = [];

            foreach ($coreTables as $t) {
                $exists = DB::getSchemaBuilder()->hasTable($t);
                $existing[$t] = $exists;
            }

            $status['core_tables'] = $existing;
            $status['tables_present'] = !in_array(false, $existing, true);
            $status['healthy'] = $status['connected'] && $status['tables_present'];

            if (!$status['healthy']) {
                $status['recommendation'] = 'failover_required';
            }

            return $status;
        } catch (\Throwable $e) {
            $status['error'] = $e->getMessage();
            $status['recommendation'] = 'failover_required';
            return $status;
        }
    }

    /**
     * Auto-recover if database crash or corrupted state is detected.
     */
    public function autoRecoverIfCrashed(): array
    {
        $health = $this->checkHealth();

        if ($health['healthy']) {
            return ['status' => 'healthy', 'action_taken' => 'none'];
        }

        Log::critical("Database failure detected! Initiating automatic replica failover...");

        // Try slot 1 (latest)
        $result = $this->restoreReplica(1);
        if ($result['success']) {
            return ['status' => 'recovered', 'slot' => 1, 'action_taken' => 'restored_slot_1'];
        }

        // Fallback to slot 2
        $result2 = $this->restoreReplica(2);
        if ($result2['success']) {
            return ['status' => 'recovered', 'slot' => 2, 'action_taken' => 'restored_slot_2'];
        }

        // Fallback to slot 3
        $result3 = $this->restoreReplica(3);
        if ($result3['success']) {
            return ['status' => 'recovered', 'slot' => 3, 'action_taken' => 'restored_slot_3'];
        }

        return ['status' => 'failed', 'error' => 'All 3 replica recovery attempts failed.'];
    }

    /**
     * Get list and status of all current 3 replicas.
     */
    public function listReplicas(): array
    {
        $manifest = $this->getManifest();
        $replicas = [];

        for ($i = 1; $i <= $this->maxReplicas; $i++) {
            $filePath = $this->replicaDir . "/replica_{$i}.sql";
            $exists = File::exists($filePath);

            $replicas[$i] = [
                'slot' => $i,
                'name' => "replica_{$i}.sql",
                'exists' => $exists,
                'size_formatted' => $exists ? $this->formatBytes(File::size($filePath)) : 'N/A',
                'size_bytes' => $exists ? File::size($filePath) : 0,
                'updated_at' => $exists ? date('Y-m-d H:i:s', File::lastModified($filePath)) : 'N/A',
                'sha256' => $exists ? hash_file('sha256', $filePath) : null,
                'metadata' => $manifest[$i] ?? null,
            ];
        }

        return $replicas;
    }

    /**
     * Update manifest with current replica metadata.
     */
    protected function updateManifest(): array
    {
        $manifest = [];

        for ($i = 1; $i <= $this->maxReplicas; $i++) {
            $path = $this->replicaDir . "/replica_{$i}.sql";
            if (File::exists($path)) {
                $manifest[$i] = [
                    'slot' => $i,
                    'file' => "replica_{$i}.sql",
                    'size_bytes' => File::size($path),
                    'updated_at' => date('c', File::lastModified($path)),
                    'sha256' => hash_file('sha256', $path),
                ];
            }
        }

        File::put($this->manifestFile, json_encode($manifest, JSON_PRETTY_PRINT));
        return $manifest;
    }

    /**
     * Get current manifest.
     */
    protected function getManifest(): array
    {
        if (File::exists($this->manifestFile)) {
            return json_decode(File::get($this->manifestFile), true) ?? [];
        }
        return [];
    }

    /**
     * Get base table names from PDO.
     */
    protected function getBaseTables(PDO $pdo): array
    {
        $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        $tables = [];
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        return $tables;
    }

    /**
     * Helper to format bytes.
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
