<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DatabaseBackupService
{
    protected string $backupDir;
    protected string $replicaFile;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups/database');
        $this->replicaFile = $this->backupDir . DIRECTORY_SEPARATOR . 'replica_latest.sql';
        
        if (!File::isDirectory($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Create a full SQL backup and update runtime replica.
     *
     * @param bool $isReplicaOnly If true, only updates the standby replica file.
     * @return array Metadata about the created backup.
     */
    public function createBackup(bool $isReplicaOnly = false): array
    {
        $dbName = config('database.connections.mysql.database');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$dbName}_{$timestamp}.sql";
        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        // Open target file
        $targetPath = $isReplicaOnly ? $this->replicaFile : $filePath;
        $fp = fopen($targetPath, 'w');
        if (!$fp) {
            throw new \RuntimeException("Unable to open backup target file for writing: {$targetPath}");
        }

        try {
            // Write Header
            fwrite($fp, "-- ============================================================\n");
            fwrite($fp, "-- Full Database Backup / Runtime Replica Snapshot\n");
            fwrite($fp, "-- Database: {$dbName}\n");
            fwrite($fp, "-- Generated: " . now()->toIso8601String() . "\n");
            fwrite($fp, "-- Server: " . php_uname() . "\n");
            fwrite($fp, "-- ============================================================\n\n");
            fwrite($fp, "SET FOREIGN_KEY_CHECKS=0;\n");
            fwrite($fp, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n");
            fwrite($fp, "SET NAMES utf8mb4;\n\n");

            // Get all tables
            $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
            $tableKey = 'Tables_in_' . $dbName;

            foreach ($tables as $t) {
                $tableName = $t->$tableKey ?? reset($t);
                if (!$tableName) continue;

                // 1. Table structure
                fwrite($fp, "-- ------------------------------------------------------------\n");
                fwrite($fp, "-- Table structure for `{$tableName}`\n");
                fwrite($fp, "-- ------------------------------------------------------------\n");
                fwrite($fp, "DROP TABLE IF EXISTS `{$tableName}`;\n");

                $createRow = DB::selectOne("SHOW CREATE TABLE `{$tableName}`");
                if ($createRow && isset($createRow->{'Create Table'})) {
                    fwrite($fp, $createRow->{'Create Table'} . ";\n\n");
                }

                // 2. Table data in offset batches
                $totalRows = DB::table($tableName)->count();
                if ($totalRows > 0) {
                    fwrite($fp, "-- Dumping data for table `{$tableName}` ({$totalRows} rows)\n");
                    fwrite($fp, "LOCK TABLES `{$tableName}` WRITE;\n");

                    $offset = 0;
                    $batchSize = 250;
                    while ($offset < $totalRows) {
                        $rows = DB::table($tableName)->offset($offset)->limit($batchSize)->get();
                        if ($rows->isEmpty()) break;

                        $columns = array_keys((array)$rows->first());
                        $escapedColumns = array_map(fn($col) => "`{$col}`", $columns);
                        $colList = implode(', ', $escapedColumns);

                        $insertValues = [];
                        foreach ($rows as $row) {
                            $rowValues = [];
                            foreach ($columns as $col) {
                                $val = $row->$col;
                                if ($val === null) {
                                    $rowValues[] = 'NULL';
                                } elseif (is_numeric($val) && !is_string($val)) {
                                    $rowValues[] = $val;
                                } else {
                                    $rowValues[] = "'" . addslashes((string)$val) . "'";
                                }
                            }
                            $insertValues[] = '(' . implode(', ', $rowValues) . ')';
                        }

                        fwrite($fp, "INSERT INTO `{$tableName}` ({$colList}) VALUES\n" . implode(",\n", $insertValues) . ";\n");
                        $offset += $batchSize;
                    }

                    fwrite($fp, "UNLOCK TABLES;\n\n");
                }
            }

            fwrite($fp, "SET FOREIGN_KEY_CHECKS=1;\n");
            fwrite($fp, "-- End of backup file\n");
            fclose($fp);

            // If this was an archive backup, also update replica_latest.sql
            if (!$isReplicaOnly) {
                File::copy($filePath, $this->replicaFile);
            }

            $sizeBytes = File::size($targetPath);
            $sizeMb = round($sizeBytes / 1024 / 1024, 2);
            $checksum = hash_file('sha256', $targetPath);

            Log::info("Database backup created successfully: {$filename} ({$sizeMb} MB)");

            return [
                'filename' => basename($targetPath),
                'path' => $targetPath,
                'size_mb' => $sizeMb,
                'size_formatted' => $sizeMb >= 1024 ? round($sizeMb / 1024, 2) . ' GB' : $sizeMb . ' MB',
                'checksum' => $checksum,
                'created_at' => now()->format('Y-m-d H:i:s'),
                'replica_updated' => true,
            ];
        } catch (\Exception $e) {
            fclose($fp);
            Log::error("Failed to create database backup: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * List all database backups.
     */
    public function listBackups(): array
    {
        if (!File::isDirectory($this->backupDir)) {
            return [];
        }

        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            $name = $file->getFilename();
            $sizeBytes = $file->getSize();
            $sizeMb = round($sizeBytes / 1024 / 1024, 2);
            $modified = date('Y-m-d H:i:s', $file->getMTime());

            $isReplica = ($name === 'replica_latest.sql');

            $backups[] = [
                'filename' => $name,
                'size_mb' => $sizeMb,
                'size_formatted' => $sizeMb >= 1024 ? round($sizeMb / 1024, 2) . ' GB' : $sizeMb . ' MB',
                'created_at' => $modified,
                'is_replica' => $isReplica,
            ];
        }

        // Sort: replica first, then newest first
        usort($backups, function ($a, $b) {
            if ($a['is_replica'] && !$b['is_replica']) return -1;
            if (!$a['is_replica'] && $b['is_replica']) return 1;
            return strcmp($b['created_at'], $a['created_at']);
        });

        return $backups;
    }

    /**
     * Get path for safe download.
     */
    public function getBackupPath(string $filename): ?string
    {
        $safeName = basename($filename);
        $path = $this->backupDir . DIRECTORY_SEPARATOR . $safeName;

        if (File::exists($path) && str_ends_with($safeName, '.sql')) {
            return $path;
        }
        return null;
    }

    /**
     * Delete an existing backup file.
     */
    public function deleteBackup(string $filename): bool
    {
        $safeName = basename($filename);
        if ($safeName === 'replica_latest.sql') {
            return false; // Prevent deleting active replica snapshot
        }

        $path = $this->backupDir . DIRECTORY_SEPARATOR . $safeName;
        if (File::exists($path)) {
            return File::delete($path);
        }
        return false;
    }
}
