<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SuperAdminAuditLog Model
 * 
 * Tracks all actions performed by super admins, especially cross-tenant operations.
 * This provides accountability and audit trail for system administrators.
 * 
 * Note: This model does NOT use BelongsToSalon trait as it tracks cross-salon actions.
 */
class SuperAdminAuditLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'salon_id',
        'target_type',
        'target_id',
        'data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the admin user who performed the action
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the salon that was affected (if applicable)
     */
    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }

    /**
     * Get the target model (polymorphic)
     */
    public function target()
    {
        if ($this->target_type && $this->target_id) {
            return $this->target_type::find($this->target_id);
        }
        return null;
    }

    /**
     * Scope to filter by admin
     */
    public function scopeByAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    /**
     * Scope to filter by salon
     */
    public function scopeBySalon($query, $salonId)
    {
        return $query->where('salon_id', $salonId);
    }

    /**
     * Scope to filter by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Static helper to log an action
     */
    public static function logAction(
        string $action,
        ?int $salonId = null,
        ?string $targetType = null,
        ?int $targetId = null,
        array $data = []
    ): self {
        return self::create([
            'admin_id' => auth()->id(),
            'action' => $action,
            'salon_id' => $salonId,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
