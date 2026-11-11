<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Salon;
use App\Models\Role;
use App\Models\Permission;
use App\Services\SalonRoleSeederService;
use Illuminate\Support\Facades\Log;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Log::info('Starting to sync employee POS permissions for all salons...');

        $posPermissions = [
            'pos.view',
            'pos.create_sale',
            'pos.manage_payment',
            'customers.view',
            'customers.create',
        ];

        // Ensure permissions exist in database
        foreach ($posPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 1. Direct assignment to all employee roles across salons
        $employeeRoles = Role::withoutGlobalScopes()->where('name', 'employee')->get();
        foreach ($employeeRoles as $role) {
            try {
                $role->givePermissionTo($posPermissions);
            } catch (\Exception $e) {
                Log::warning("Could not direct assign to role ID {$role->id}: " . $e->getMessage());
            }
        }

        // 2. Also run seedDefaultRoles for each salon to align all templates
        $salons = Salon::all();
        foreach ($salons as $salon) {
            try {
                SalonRoleSeederService::seedDefaultRoles($salon->id);
            } catch (\Exception $e) {
                Log::error("Failed to sync roles for salon ID: {$salon->id}. Error: " . $e->getMessage());
            }
        }

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Log::info('Finished syncing employee POS permissions.');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
