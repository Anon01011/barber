<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Salon;
use App\Services\SalonRoleSeederService;
use Illuminate\Support\Facades\DB;

class SyncSalonPermissions extends Command
{
    protected $signature = 'salon:sync-permissions
                            {--salon-id= : Specific salon ID to sync (leave blank for all salons)}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Re-seed all salon roles with correct plan-based permissions. Fixes missing sidebar items after registration.';

    public function handle(): int
    {
        $salonId = $this->option('salon-id');

        $salons = $salonId
            ? Salon::where('id', $salonId)->get()
            : Salon::all();

        if ($salons->isEmpty()) {
            $this->error('No salons found' . ($salonId ? " with ID {$salonId}" : '') . '.');
            return self::FAILURE;
        }

        if (!$this->option('force') && !$this->confirm("Re-seed permissions for {$salons->count()} salon(s)?", true)) {
            $this->info('Cancelled.');
            return self::SUCCESS;
        }

        $this->info("Syncing permissions for {$salons->count()} salon(s)...");

        $errors = [];
        foreach ($salons as $salon) {
            try {
                app()->instance('current_salon', $salon);
                $subscription = $salon->activeSubscription;
                $plan = $subscription ? $subscription->plan : null;

                $roles = SalonRoleSeederService::seedDefaultRoles($salon->id, $plan);

                $this->line("<info>Salon [{$salon->id}] {$salon->name}</info> | Plan: " . ($plan?->name ?? 'none'));
                foreach ($roles as $role) {
                    $count = DB::table('role_has_permissions')->where('role_id', $role->id)->count();
                    $this->line("  <comment>{$role->name}</comment> -> {$count} permissions");
                }
            } catch (\Throwable $e) {
                $errors[] = "Salon [{$salon->id}]: " . $e->getMessage();
                $this->error("Salon [{$salon->id}] {$salon->name}: " . $e->getMessage());
            }
        }

        if (!empty($errors)) {
            $this->error('Completed with ' . count($errors) . ' error(s).');
            return self::FAILURE;
        }

        $this->info('All salon permissions synced successfully.');
        return self::SUCCESS;
    }
}
