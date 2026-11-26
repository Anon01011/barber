<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SalonBackupService;
use App\Models\Salon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SalonBackupController extends Controller
{
    protected SalonBackupService $backupService;

    public function __construct(SalonBackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Check if current salon/user can access the Data Backup & Safety feature.
     */
    protected function checkEntitlement(Salon $salon): bool
    {
        // First, check global module switch! If SaaS admin turned off data backup, it is off for everyone
        if (!\App\Helpers\ModuleHelper::dataBackupEnabled()) {
            return false;
        }

        $user = auth()->user();
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        return $salon->canUseFeature('data_backup') 
            || $salon->canUseFeature('Data Backup & Safety')
            || $salon->canUseFeature('Data Backup')
            || $salon->canUseFeature('Backup System');
    }

    /**
     * Show Salon Data Backup & Import Management Page.
     */
    public function index()
    {
        $salonId = auth()->user()->salon_id;
        $salon = Salon::findOrFail($salonId);
        $canUseBackup = $this->checkEntitlement($salon);
        $backups = $this->backupService->listBackups($salonId);

        // Record stats for this salon
        $stats = [
            'services' => \App\Models\Service::where('salon_id', $salonId)->count(),
            'categories' => \App\Models\ServiceCategory::where('salon_id', $salonId)->count(),
            'packages' => \App\Models\Package::where('salon_id', $salonId)->count(),
            'memberships' => \Illuminate\Support\Facades\Schema::hasTable('memberships') 
                ? \Illuminate\Support\Facades\DB::table('memberships')->where('salon_id', $salonId)->count() 
                : 0,
            'inventory' => \Illuminate\Support\Facades\Schema::hasTable('inventory_items')
                ? \Illuminate\Support\Facades\DB::table('inventory_items')->where('salon_id', $salonId)->count()
                : 0,
            'customers' => \App\Models\Customer::where('salon_id', $salonId)->count(),
            'bookings' => \App\Models\Booking::where('salon_id', $salonId)->count(),
            'pos_sales' => \Illuminate\Support\Facades\Schema::hasTable('pos_sales')
                ? \Illuminate\Support\Facades\DB::table('pos_sales')->where('salon_id', $salonId)->count()
                : 0,
        ];

        // Quota limits from active subscription plan
        $maxBackupSizeMb = $salon->getBackupSizeLimitMb();
        $maxSnapshots = $salon->getMaxBackupSnapshotsLimit();

        return view('admin.backup.index', compact('salon', 'canUseBackup', 'backups', 'stats', 'maxBackupSizeMb', 'maxSnapshots'));
    }

    /**
     * Export and directly download salon backup JSON.
     */
    public function export(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $salon = Salon::findOrFail($salonId);

        if (!$this->checkEntitlement($salon)) {
            return redirect()->route('admin.backup.index', ['salon_slug' => $salon->slug])
                ->with('error', 'Your current subscription plan does not include the Data Backup & Safety feature. Please upgrade your plan.');
        }

        try {
            $selectedModules = $request->input('modules', []);
            if (!is_array($selectedModules)) {
                $selectedModules = [];
            }

            $backup = $this->backupService->export($salonId, $selectedModules);

            return response()->download($backup['path'], $backup['filename'], [
                'Content-Type' => 'application/json',
            ]);
        } catch (\OverflowException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('SalonBackupController export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate backup: ' . $e->getMessage());
        }
    }

    /**
     * Send backup to Salon Admin email immediately.
     */
    public function sendToEmail(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $salon = Salon::findOrFail($salonId);

        if (!$this->checkEntitlement($salon)) {
            return redirect()->route('admin.backup.index', ['salon_slug' => $salon->slug])
                ->with('error', 'Your current subscription plan does not include the Data Backup & Safety feature. Please upgrade your plan.');
        }

        try {
            $selectedModules = $request->input('modules', []);
            if (!is_array($selectedModules)) {
                $selectedModules = [];
            }

            $sent = $this->backupService->sendDailyBackupEmail($salonId, $selectedModules);

            if ($sent) {
                return redirect()->back()->with('success', 'Salon backup generated and emailed successfully to your admin address.');
            }

            return redirect()->back()->with('error', 'Unable to email backup: no valid administrator email found.');
        } catch (\OverflowException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('SalonBackupController sendToEmail error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error sending backup email: ' . $e->getMessage());
        }
    }

    /**
     * Import and restore salon data from uploaded JSON backup file.
     */
    public function import(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $salon = Salon::findOrFail($salonId);

        if (!$this->checkEntitlement($salon)) {
            return redirect()->route('admin.backup.index', ['salon_slug' => $salon->slug])
                ->with('error', 'Your current subscription plan does not include the Data Backup & Safety feature. Please upgrade your plan.');
        }

        $request->validate([
            'backup_file' => 'required|file|max:51200', // Max 50MB uploaded file
        ]);

        $file = $request->file('backup_file');
        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Uploaded backup file is invalid.');
        }

        try {
            $jsonContent = file_get_contents($file->getRealPath());
            $result = $this->backupService->import($salonId, $jsonContent);

            $summaryParts = [];
            foreach ($result['restored'] as $key => $count) {
                $summaryParts[] = ucfirst(str_replace('_', ' ', $key)) . ": {$count}";
            }
            $summary = implode(', ', $summaryParts);

            return redirect()->back()->with('success', "Salon data restored successfully! Restored: {$summary}");
        } catch (\OverflowException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', 'Invalid backup format: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('SalonBackupController import error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to import backup: ' . $e->getMessage());
        }
    }
}
