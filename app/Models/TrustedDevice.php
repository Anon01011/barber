<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToSalon;

class TrustedDevice extends Model
{
    use HasFactory, BelongsToSalon;

    /**
     * Allow null salon_id for platform/super-admin users.
     * Prevents BelongsToSalon from throwing context errors.
     */
    public $allowNullSalon = true;

    protected $table = 'trusted_devices';

    protected $fillable = [
        'user_id',
        'salon_id',
        'device_uuid',
        'device_name',
        'device_token_hash',
        'browser',
        'operating_system',
        'last_ip',
        'first_used_at',
        'last_used_at',
        'expires_at',
        'revoked_at',
        'status',
    ];

    protected $casts = [
        'first_used_at' => 'datetime',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }

    public function pins()
    {
        return $this->hasMany(UserDevicePin::class, 'trusted_device_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(DevicePinAuditLog::class, 'trusted_device_id');
    }

    public function isTrusted(): bool
    {
        if ($this->status !== 'trusted') {
            return false;
        }

        if ($this->revoked_at !== null) {
            return false;
        }

        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function isRevoked(): bool
    {
        return $this->status === 'revoked' || $this->revoked_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function markAsUsed(?string $ip = null): void
    {
        $this->update([
            'last_used_at' => now(),
            'last_ip' => $ip ?? $this->last_ip,
            'expires_at' => now()->addDays(90), // Refresh 90 days of trust on usage
        ]);
    }

    public function revoke(?string $reason = null): void
    {
        $this->update([
            'status' => 'revoked',
            'revoked_at' => now(),
        ]);
    }
}

