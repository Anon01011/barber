<?php

namespace App\Permissions;

use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use AllowDynamicProperties;

#[AllowDynamicProperties]
class TenantPermissionRegistrar extends PermissionRegistrar
{
    /** @var int|null */
    protected $lastSalonId = -1;

    /**
     * Get the cache key for the permissions.
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        $key = config('permission.cache.key');
        $salonId = $this->getSalonId();

        if ($salonId) {
            return $key . '.salon.' . $salonId;
        }

        return $key;
    }

    /**
     * Get the current salon ID from various sources.
     *
     * @return int|null
     */
    protected function getSalonId(): ?int
    {
        // 1. Check if bound in container (set by middleware or manually)
        if (app()->bound('current_salon')) {
            $salon = app()->bound('current_salon') ? app('current_salon') : null;
            return is_object($salon) ? $salon->id : $salon;
        }

        // 2. Check authenticated user
        if (auth()->check()) {
            $user = auth()->user();
            // Users with null salon_id (like super admins) use the global cache
            if (is_null($user->salon_id)) {
                return null;
            }
            return $user->salon_id;
        }

        // 3. Check session
        if (session()->has('salon_id')) {
            return session()->get('salon_id');
        }

        return null;
    }

    /**
     * Get the permissions from the cache.
     * 
     * Overridden to handle salon ID changes during the request.
     */
    public function getPermissions(array $params = [], bool $onlyOne = false): Collection
    {
        $currentSalonId = $this->getSalonId();

        if ($this->permissions === null || $this->lastSalonId !== $currentSalonId) {
            $this->lastSalonId = $currentSalonId;

            // Reset internal permissions to force reload from cache or DB
            $this->permissions = null;

            $permissionClass = $this->getPermissionClass();
            $this->permissions = $this->cache->remember($this->getCacheKey(), $this->cacheExpirationTime, function () use ($permissionClass) {
                // SEC-ROLE-01 FIX: load roles with their status so we can filter
                // inactive (disabled) roles out of permission resolution.
                return $permissionClass::with(['roles' => function ($q) {
                    // Load all roles but mark each with status so callers can filter
                    $q->select(['roles.id', 'roles.name', 'roles.guard_name', 'roles.status']);
                }])->get();
            });
        }

        $permissions = clone $this->permissions;

        // SEC-ROLE-01: Strip permissions whose ONLY granting roles are inactive.
        // A permission is still granted if at least one active role carries it.
        // Global/system roles (super_admin, salon_admin) always have status = true
        // so they are unaffected.
        $permissions = $permissions->filter(function ($permission) {
            $roles = $permission->roles ?? collect();
            if ($roles->isEmpty()) {
                // No roles: direct permission — keep
                return true;
            }
            // Keep permission if at least one role that carries it is active (status != false)
            return $roles->contains(function ($role) {
                return (bool) ($role->status ?? true);
            });
        })->values();

        foreach ($params as $key => $value) {
            $permissions = $permissions->where($key, $value);
        }

        if ($onlyOne) {
            $permissions = $permissions->take(1);
        }

        return $permissions;
    }

    /**
     * Clear only the internal (static) cache of permissions and roles.
     * This does NOT clear the external cache (Redis/File).
     */
    public function clearInternalCache()
    {
        $this->permissions = null;
        $this->roles = null;
        $this->lastSalonId = -1;
    }

    /**
     * Override forgetCachedPermissions to ensure we clear the correct cache key.
     */
    public function forgetCachedPermissions()
    {
        $this->clearInternalCache();

        return Cache::forget($this->getCacheKey());
    }
}
