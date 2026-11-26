<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SystemHealthService;
use App\Services\DatabaseBackupService;
use App\Services\DatabaseFailoverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SystemHealthController extends Controller
{
    protected SystemHealthService $healthService;
    protected DatabaseBackupService $backupService;
    protected DatabaseFailoverService $failoverService;

    public function __construct(
        SystemHealthService $healthService,
        DatabaseBackupService $backupService,
        DatabaseFailoverService $failoverService
    ) {
        $this->healthService   = $healthService;
        $this->backupService   = $backupService;
        $this->failoverService = $failoverService;
    }

    /**
     * Dedicated System Health, Server & Database Inspection Page.
     */
    public function index()
    {
        $metrics        = $this->healthService->getMetrics();
        $tables         = $this->healthService->getTableBreakdown(25);
        $backups        = $this->backupService->listBackups();
        $failoverStatus = $this->failoverService->getStatus();

        return view('super-admin.system-health.index', compact('metrics', 'tables', 'backups', 'failoverStatus'));
    }

    /**
     * Return JSON metrics for real-time polling.
     */
    public function liveMetrics()
    {
        return response()->json([
            'success'         => true,
            'metrics'         => $this->healthService->getMetrics(),
            'failover_status' => $this->failoverService->getStatus(),
            'timestamp'       => now()->toIso8601String(),
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
                    'backup'  => $result
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

    // ── HA Failover Control ───────────────────────────────────────────────────

    /**
     * Force activate failover to replica DB.
     */
    public function activateFailover(Request $request)
    {
        try {
            $result = $this->failoverService->forceFailover();
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Failover activated — application is now using the replica database.', 'result' => $result]);
            }
            return redirect()->back()->with('success', 'Failover activated. Now using replica database.');
        } catch (\Exception $e) {
            Log::error('SystemHealth activateFailover: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failover activation failed: ' . $e->getMessage());
        }
    }

    /**
     * Force deactivate failover — switch back to primary DB.
     */
    public function deactivateFailover(Request $request)
    {
        try {
            $result = $this->failoverService->forcePrimary();
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Primary database restored.', 'result' => $result]);
            }
            return redirect()->back()->with('success', 'Primary database connection restored.');
        } catch (\Exception $e) {
            Log::error('SystemHealth deactivateFailover: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to restore primary: ' . $e->getMessage());
        }
    }

    /**
     * Trigger a manual replica sync from latest snapshot.
     */
    public function syncReplica(Request $request)
    {
        try {
            $result = $this->failoverService->syncReplicaFromSnapshot();
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Replica sync complete.', 'result' => $result]);
            }
            return redirect()->back()->with('success', 'Replica synced from latest snapshot successfully.');
        } catch (\Exception $e) {
            Log::error('SystemHealth syncReplica: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Replica sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Return current failover status as JSON.
     */
    public function failoverStatus()
    {
        return response()->json($this->failoverService->getStatus());
    }
}
