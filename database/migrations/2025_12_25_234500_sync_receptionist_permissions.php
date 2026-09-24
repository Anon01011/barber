<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Salon;
use App\Services\SalonRoleSeederService;
use Illuminate\Support\Facades\Log;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Log::info('Starting to sync receptionist permissions for all salons...');

        $salons = Salon::all();

        foreach ($salons as $salon) {
            try {
                Log::info("Syncing roles for salon ID: {$salon->id} ({$salon->name})");
                SalonRoleSeederService::seedDefaultRoles($salon->id);
            } catch (\Exception $e) {
                Log::error("Failed to sync roles for salon ID: {$salon->id}. Error: " . $e->getMessage());
            }
        }

        Log::info('Finished syncing receptionist permissions.');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to reverse this without knowing previous state
    }
};
