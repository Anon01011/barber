<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Salon;
use App\Models\Role;
use App\Models\User;
use App\Services\SalonRoleSeederService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all existing salons
        $salons = Salon::all();

        foreach ($salons as $salon) {
            \Log::info("Processing salon: {$salon->name} (ID: {$salon->id})");

            // Seed default roles for this salon
            SalonRoleSeederService::seedDefaultRoles($salon->id);

            // Find users belonging to this salon
            $salonUsers = User::where('salon_id', $salon->id)->get();

            foreach ($salonUsers as $user) {
                // Get user's current roles (global ones)
                $currentRoles = $user->roles()->whereNull('salon_id')->pluck('name')->toArray();

                \Log::info("User {$user->email} has global roles: " . implode(', ', $currentRoles));

                // Remove global roles (except super_admin)
                foreach ($currentRoles as $roleName) {
                    if ($roleName !== 'super_admin') {
                        $globalRole = Role::where('name', $roleName)->whereNull('salon_id')->first();
                        if ($globalRole) {
                            $user->removeRole($globalRole);
                        }
                    }
                }

                // Assign salon-specific versions of the roles
                foreach ($currentRoles as $roleName) {
                    if ($roleName !== 'super_admin') {
                        $salonRole = Role::where('name', $roleName)
                            ->where('salon_id', $salon->id)
                            ->first();

                        if ($salonRole) {
                            $user->assignRole($salonRole);
                            \Log::info("Assigned salon-specific {$roleName} to {$user->email}");
                        }
                    }
                }
            }
        }

        // Delete old global roles (except super_admin)
        $globalRolesToDelete = ['salon_admin', 'manager', 'employee', 'customer'];
        foreach ($globalRolesToDelete as $roleName) {
            $role = Role::where('name', $roleName)->whereNull('salon_id')->first();
            if ($role) {
                \Log::info("Deleting global role: {$roleName}");
                $role->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not easily reversible
        // You would need to recreate global roles and reassign users
        \Log::warning('Reverting assign_existing_roles_to_salons migration is not supported');
    }
};
