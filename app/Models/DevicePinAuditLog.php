<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevicePinAuditLog extends Model
{
    use HasFactory;

    protected $table = 'device_pin_audit_logs';

    protected $fillable = [
        'user_id',
        'salon_id',
        'trusted_device_id',
        'event',
        'ip_address',
        'user_agent',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
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

    public static function log(string $event, ?int $userId = null, ?int $salonId = null, ?int $deviceId = null, array $details = []): self
    {
        $request = request();
        
        return self::create([
            'user_id' => $userId,
            'salon_id' => $salonId,
            'trusted_device_id' => $deviceId,
            'event' => $event,
            'ip_address' => $request?->ip(),
            'user_agent' => substr((string) ($request?->userAgent() ?? ''), 0, 500),
            'details' => $details,
        ]);
    }
}

