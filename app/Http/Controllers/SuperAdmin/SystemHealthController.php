<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SystemHealthService;
use App\Services\DatabaseBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SystemHealthController extends Controller
{
    protected SystemHealthService $healthService;
    protected DatabaseBackupService $backupService;

    public function __construct(SystemHealthService $healthService, DatabaseBackupService $backupService)
    {
        $this->healthService = $healthService;
        $this->backupService = $backupService;
    }

    /**
     * Dedicated System Health, Server & Database Inspection Page.
     */
    public function index()
    {
        $metrics = $this->healthService->getMetrics();
        $tables = $this->healthService->getTableBreakdown(25);
        $backups = $this->backupService->listBackups();

        return view('super-admin.system-health.index', compact('metrics', 'tables', 'backups'));
    }

    /**
     * Return JSON metrics for real-time polling.
     */
    public function liveMetrics()
    {
        return response()->json([
            'success' => true,
            'metrics' => $this->healthService->getMetrics(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Trigger immediate database snapshot/backup.
     */
    public function createBackup(Request $request)
    {
        try {
            $isReplicaOnly = $request->boolean('replica_only', false);
            $result = $this->backupService->createBackup($isReplicaOnly);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Database snapshot and replica updated successfully.',
                    'backup' => $result
                ]);
            }

            return redirect()->back()->with('success', "Database snapshot generated: {$result['filename']} ({$result['size_formatted']})");
        } catch (\Exception $e) {
            Log::error('SuperAdmin createBackup Error: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Failed to generate backup: ' . $e->getMessage());
        }
    }

    /**
     * Securely download a database backup.
     */
    public function downloadBackup(string $filename)
    {
        $path = $this->backupService->getBackupPath($filename);

        if (!$path) {
            abort(404, 'Backup file not found or invalid.');
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Delete an existing backup file.
     */
    public function deleteBackup(string $filename)
    {
        $deleted = $this->backupService->deleteBackup($filename);

        if ($deleted) {
            return redirect()->back()->with('success', 'Backup file deleted successfully.');
        }

        return redirect()->back()->with('error', 'Cannot delete active replica or file does not exist.');
    }
}
