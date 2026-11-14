<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\Traits\BelongsToSalon;

class UserDevicePin extends Model
{
    use HasFactory, BelongsToSalon;

    /**
     * Allow null salon_id for platform/super-admin users.
     * Prevents BelongsToSalon from throwing context errors.
     */
    public $allowNullSalon = true;

    protected $table = 'user_device_pins';

    protected $fillable = [
        'user_id',
        'salon_id',
        'trusted_device_id',
        'pin_hash',
        'failed_attempts',
        'locked_until',
        'pin_enabled',
        'last_verified_at',
    ];

    protected $hidden = [
        'pin_hash',
    ];

    protected $casts = [
        'failed_attempts' => 'integer',
        'locked_until' => 'datetime',
        'pin_enabled' => 'boolean',
        'last_verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }

    public function trustedDevice()
    {
        return $this->belongsTo(TrustedDevice::class, 'trusted_device_id');
    }

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    public function lockoutSecondsRemaining(): int
    {
        if (!$this->isLocked()) {
            return 0;
        }

        return max(0, (int) now()->diffInSeconds($this->locked_until, false));
    }

    public function verifyPin(string $pin): bool
    {
        if ($this->isLocked()) {
            return false;
        }

        if (Hash::check($pin, $this->pin_hash)) {
            $this->resetFailedAttempts();
            $this->update(['last_verified_at' => now()]);
            return true;
        }

        $this->incrementFailedAttempts();
        return false;
    }

    public function incrementFailedAttempts(): void
    {
        $attempts = $this->failed_attempts + 1;
        $lockedUntil = null;

        // Progressive lockout policy
        if ($attempts >= 5) {
            $lockMinutes = 15;
            if ($attempts >= 10) {
                $lockMinutes = 60;
            } elseif ($attempts >= 7) {
                $lockMinutes = 30;
            }
            $lockedUntil = now()->addMinutes($lockMinutes);
        }

        $this->update([
            'failed_attempts' => $attempts,
            'locked_until' => $lockedUntil,
        ]);
    }

    public function resetFailedAttempts(): void
    {
        $this->update([
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);
    }
}

