<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;

class SalonRoleSeederService
{
    /**
     * Seed default roles for a salon
     *
     * @param int $salonId
     * @param \App\Models\Plan|null $plan
     * @return array Created roles
     */
    public static function seedDefaultRoles(int $salonId, ?\App\Models\Plan $plan = null): array
    {
        $createdRoles = [];

        // Fetch salon to check plan
        $salon = \App\Models\Salon::find($salonId);

        // Bind current_salon to ensure TenantPermissionRegistrar uses the correct cache key
        app()->instance('current_salon', $salon);

        // Use provided plan or fetch from active subscription
        $plan = $plan ?? $salon?->activeSubscription?->plan;

        // Define role templates with their permissions
        $roleTemplates = self::getRoleTemplates($plan, $salonId);

        foreach ($roleTemplates as $roleName => $template) {
            // First, check if role already exists using raw DB query to bypass all scopes
            $existingRoleId = \Illuminate\Support\Facades\DB::table('roles')
                ->where('name', $roleName)
                ->where('salon_id', $salonId)
                ->where('guard_name', 'web')
                ->value('id');

            if ($existingRoleId) {
                // Role exists, just fetch it
                $role = Role::withoutGlobalScopes()->find($existingRoleId);
                \Illuminate\Support\Facades\Log::info("Role already exists: {$roleName} for salon {$salonId}");
            } else {
                // Create new role using DB::table to bypass global scopes completely
                try {
                    $roleId = \Illuminate\Support\Facades\DB::table('roles')->insertGetId([
                        'name' => $roleName,
                        'salon_id' => $salonId,
                        'guard_name' => 'web',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $role = Role::withoutGlobalScopes()->find($roleId);
                    \Illuminate\Support\Facades\Log::info("Created new role: {$roleName} for salon {$salonId}");
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->errorInfo[1] == 1062) { // Duplicate entry - race condition
                        \Illuminate\Support\Facades\Log::warning("Race condition detected for role: {$roleName} for salon {$salonId}. Fetching existing.");
                        $existingRoleId = \Illuminate\Support\Facades\DB::table('roles')
                            ->where('name', $roleName)
                            ->where('salon_id', $salonId)
                            ->where('guard_name', 'web')
                            ->value('id');

                        if ($existingRoleId) {
                            $role = Role::withoutGlobalScopes()->find($existingRoleId);
                        } else {
                            throw new \Exception("Failed to create or find role {$roleName} for salon {$salonId}");
                        }
                    } else {
                        throw $e;
                    }
                }
            }

            // Sync permissions
            $role->syncPermissions($template['permissions']);

            $createdRoles[] = $role;
        }

        // CRITICAL: Clear Spatie's permission cache after syncing
        // Without this, users from other salons may see stale cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return $createdRoles;
    }

    /**
     * Get available permissions based on plan features and enabled modules
     *
     * @param \App\Models\Plan|null $plan
     * @param int|null $salonId
     * @return array Array of permission names
     */
    public static function getAvailablePermissions($plan = null, $salonId = null): array
    {
        return PermissionService::getAvailablePermissions($plan, $salonId);
    }

    /**
     * Get role templates with permissions
     *
     * @param \App\Models\Plan|null $plan
     * @param int|null $salonId
     * @return array
     */
    public static function getRoleTemplates($plan = null, $salonId = null): array
    {
        return PermissionService::getRoleTemplates($plan, $salonId);
    }
}
