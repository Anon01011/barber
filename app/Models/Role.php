<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use App\Traits\BelongsToSalon;

/**
 * Role Model
 * 
 * Extends Spatie's Role model with multi-tenancy support.
 * Roles can be either global (salon_id = null) or salon-specific.
 * 
 * Multi-tenancy: Uses BelongsToSalon trait for automatic salon scoping.
 * The scopeSalon() method is kept for backward compatibility and special cases
 * where you need to query both global and salon-specific roles.
 */
class Role extends SpatieRole
{
    use BelongsToSalon;

    /**
     * Allow roles to exist without a salon context (global roles).
     */
    public $allowNullSalon = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'status',
        'is_protected',
        'level',
        'salon_id',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => 'boolean',
        'is_protected' => 'boolean',
        'level' => 'integer'
    ];

    protected $appends = ['users_count', 'permissions_count'];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($role) {
            if ($role->is_protected) {
                throw new \Exception('Cannot delete protected role');
            }
        });
    }

    /**
     * Scope a query to only include roles for a specific salon or global roles.
     * 
     * Note: This scope is kept for backward compatibility and special cases.
     * The BelongsToSalon trait provides automatic salon scoping via SalonScope.
     * Use this method when you need to explicitly query both global and salon-specific roles.
     */
    public function scopeSalon($query, $salonId)
    {
        return $query->where(function ($q) use ($salonId) {
            $q->where('salon_id', $salonId)
                ->orWhereNull('salon_id');
        });
    }

    /**
     * Get the users that belong to this role.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles', 'role_id', 'model_id');
    }

    /**
     * Get the permissions that belong to this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id')
            ->withTimestamps();
    }

    /**
     * Get the count of users with this role.
     */
    public function getUsersCountAttribute(): int
    {
        // If users_count was loaded via withCount, return it
        if (array_key_exists('users_count', $this->attributes)) {
            return $this->attributes['users_count'];
        }

        // Otherwise calculate it, respecting salon scope
        $query = $this->users();

        if (auth()->check() && !auth()->user()->isSuperAdmin()) {
            $query->where('users.salon_id', auth()->user()->salon_id);
        }

        return $query->count();
    }

    /**
     * Get the count of permissions for this role.
     */
    public function getPermissionsCountAttribute(): int
    {
        return $this->permissions()->count();
    }

    /**
     * Check if the role has the given permission.
     */
    public function hasPermission($permission): bool
    {
        if (is_string($permission)) {
            $permission = Permission::findByName($permission);
        }

        return $this->permissions->contains('id', $permission->id);
    }

    /**
     * Scope a query to only include active roles.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include inactive roles.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    /**
     * Scope a query to only include roles with a level less than or equal to the given level.
     */
    public function scopeLevel($query, int $level)
    {
        return $query->where('level', '<=', $level);
    }

    /**
     * Get all permissions grouped by their group name.
     */
    public static function getGroupedPermissions(): Collection
    {
        return Permission::all()->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'other';
        });
    }

    /**
     * Check if the role is higher in hierarchy than another role.
     */
    public function isHigherThan(Role $role): bool
    {
        return $this->level > $role->level;
    }

    /**
     * Check if the role is lower in hierarchy than another role.
     */
    public function isLowerThan(Role $role): bool
    {
        return $this->level < $role->level;
    }

    /**
     * Check if the role is at the same level as another role.
     */
    public function isSameLevel(Role $role): bool
    {
        return $this->level === $role->level;
    }

    /**
     * Check if the role is active.
     */
    public function isActive(): bool
    {
        return $this->status;
    }

    /**
     * Check if the role is the super admin role.
     */
    public function isSuperAdmin(): bool
    {
        return $this->name === 'super_admin';
    }

    /**
     * Activate the role.
     */
    public function activate(): bool
    {
        if ($this->isSuperAdmin()) {
            return false;
        }

        return $this->update(['status' => true]);
    }

    /**
     * Deactivate the role.
     */
    public function deactivate(): bool
    {
        if ($this->isSuperAdmin()) {
            return false;
        }

        return $this->update(['status' => false]);
    }

    /**
     * Toggle the role status.
     */
    public function toggleStatus(): bool
    {
        if ($this->isSuperAdmin()) {
            return false;
        }

        return $this->update(['status' => !$this->status]);
    }
}

