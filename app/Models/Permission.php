<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'group',
        'is_protected',
        'is_visible'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_protected' => 'boolean',
        'is_visible' => 'boolean'
    ];

    protected $appends = ['display_name', 'roles_count'];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($permission) {
            if ($permission->is_protected) {
                throw new \Exception('Cannot delete protected permission');
            }
        });
    }

    /**
     * Get the roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id', 'role_id')
            ->withTimestamps();
    }

    /**
     * Get the count of roles with this permission.
     */
    public function getRolesCountAttribute(): int
    {
        return $this->roles()->count();
    }

    /**
     * Get the display name of the permission.
     */
    public function getDisplayNameAttribute(): string
    {
        $parts = explode('.', $this->name);
        $action = end($parts);
        
        return ucwords(str_replace('_', ' ', $action));
    }

    /**
     * Get the group name of the permission.
     */
    public function getGroupNameAttribute(): string
    {
        $parts = explode('.', $this->name);
        return ucfirst($parts[0] ?? 'other');
    }

    /**
     * Scope a query to only include visible permissions.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope a query to only include permissions from a specific group.
     */
    public function scopeInGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Get all permissions grouped by their group name.
     */
    public static function getGrouped(): Collection
    {
        return self::visible()
            ->orderBy('name')
            ->get()
            ->groupBy('group_name');
    }

    /**
     * Check if the permission is assigned to any role.
     */
    public function isAssigned(): bool
    {
        return $this->roles_count > 0;
    }

    /**
     * Get the permission's parent group.
     */
    public function getParentGroup(): string
    {
        $parts = explode('.', $this->name);
        array_pop($parts);
        return implode('.', $parts);
    }

    /**
     * Check if this is a parent permission (has children).
     */
    public function isParent(): bool
    {
        return self::where('name', 'like', $this->name . '.%')->exists();
    }

    /**
     * Get all child permissions of this permission.
     */
    public function getChildren(): Collection
    {
        return self::where('name', 'like', $this->name . '.%')->get();
    }
}
