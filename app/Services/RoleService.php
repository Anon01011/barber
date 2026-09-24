<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoleService
{
    /**
     * Get all roles with their permissions.
     */
    public function getAllRoles(): Collection
    {
        return Role::with('permissions')
            ->orderBy('level', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new role with the given data.
     */
    public function createRole(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'] ?? 'web',
                'description' => $data['description'] ?? null,
                'is_protected' => $data['is_protected'] ?? false,
                'level' => $data['level'] ?? 0,
                'status' => $data['status'] ?? true,
            ]);

            if (isset($data['permissions'])) {
                $this->syncPermissions($role, $data['permissions']);
            }

            return $role->load('permissions');
        });
    }

    /**
     * Update the given role with the provided data.
     */
    public function updateRole(Role $role, array $data): Role
    {
        if ($role->is_protected) {
            throw ValidationException::withMessages([
                'role' => 'Cannot update a protected role.'
            ]);
        }

        return DB::transaction(function () use ($role, $data) {
            $role->update([
                'name' => $data['name'] ?? $role->name,
                'description' => $data['description'] ?? $role->description,
                'level' => $data['level'] ?? $role->level,
                'status' => $data['status'] ?? $role->status,
            ]);

            if (isset($data['permissions'])) {
                $this->syncPermissions($role, $data['permissions']);
            }

            return $role->load('permissions');
        });
    }

    /**
     * Delete the given role.
     */
    public function deleteRole(Role $role): bool
    {
        if ($role->is_protected) {
            throw ValidationException::withMessages([
                'role' => 'Cannot delete a protected role.'
            ]);
        }

        if ($role->users_count > 0) {
            throw ValidationException::withMessages([
                'role' => 'Cannot delete a role that is assigned to users.'
            ]);
        }

        return $role->delete();
    }

    /**
     * Sync permissions for the given role.
     */
    public function syncPermissions(Role $role, array $permissionIds): void
    {
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);
    }

    /**
     * Get all permissions grouped by their group name.
     */
    public function getGroupedPermissions(): Collection
    {
        return Permission::visible()
            ->orderBy('name')
            ->get()
            ->groupBy('group_name');
    }

    /**
     * Assign a role to a user with an optional expiration date.
     */
    public function assignRoleToUser($user, $role, $expiresAt = null): void
    {
        if ($expiresAt) {
            $user->roles()->syncWithoutDetaching([
                $role->id => ['expires_at' => $expiresAt]
            ]);
        } else {
            $user->assignRole($role);
        }
    }

    /**
     * Revoke a role from a user.
     */
    public function revokeRoleFromUser($user, $role): void
    {
        $user->removeRole($role);
    }

    /**
     * Get all roles that the current user can manage.
     */
    public function getAssignableRoles($user): Collection
    {
        $userRole = $user->roles()->orderBy('level', 'desc')->first();
        
        if (!$userRole) {
            return collect();
        }

        return Role::where('level', '<=', $userRole->level)
            ->where('id', '!=', $user->id) // Prevent self-assignment
            ->orderBy('level', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Check if a user has a specific permission.
     */
    public function userHasPermission($user, string $permission): bool
    {
        return $user->hasPermissionTo($permission);
    }

    /**
     * Get all permissions for a specific guard.
     */
    public function getPermissionsByGuard(string $guardName): Collection
    {
        return Permission::where('guard_name', $guardName)
            ->orderBy('name')
            ->get();
    }
}
