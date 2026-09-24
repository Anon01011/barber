<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Services\SalonRoleSeederService;
use Illuminate\Support\Facades\DB;

class UpdateSalonRolePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:update-permissions {--salon_id= : Specific salon ID to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update salon roles with new granular permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $salonId = $this->option('salon_id');

        if ($salonId) {
            $this->info("Updating roles for salon ID: {$salonId}");
            $this->updateSalonRoles($salonId);
        } else {
            $this->info("Updating roles for all salons...");

            // Get all salon IDs
            $salonIds = DB::table('salons')->pluck('id');

            $bar = $this->output->createProgressBar(count($salonIds));
            $bar->start();

            foreach ($salonIds as $id) {
                $this->updateSalonRoles($id);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        }

        $this->info("✅ Role permissions updated successfully!");
    }

    /**
     * Update roles for a specific salon
     */
    private function updateSalonRoles($salonId)
    {
        try {
            // Re-seed the roles which will update permissions
            SalonRoleSeederService::seedDefaultRoles($salonId);

            $this->line("  Updated roles for salon {$salonId}");
        } catch (\Exception $e) {
            $this->error("  Failed to update salon {$salonId}: " . $e->getMessage());
        }
    }
}
