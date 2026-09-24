<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Salon;
use App\Services\SalonRoleSeederService;

class FixDemoSalonPermissionsSeeder extends Seeder
{
    public function run()
    {
        $salon = Salon::where('slug', 'demo-salon')->first();

        if ($salon) {
            $this->command->info("Syncing roles for salon: " . $salon->name . " (ID: " . $salon->id . ")");
            SalonRoleSeederService::seedDefaultRoles($salon->id);
            $this->command->info("Roles synced successfully.");
        } else {
            $this->command->error("Demo salon not found.");
        }
    }
}
