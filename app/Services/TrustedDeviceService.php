<?php

namespace App\Services;

use App\Models\TrustedDevice;
use App\Models\UserDevicePin;
use App\Models\DevicePinAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;

class TrustedDeviceService
{
    public const COOKIE_NAME = 'salon_trusted_device';
    public const TRUST_DAYS = 90;
    public const MAX_ATTEMPTS = 5;

    /**
     * Determine if a user is eligible for Trusted Device & PIN Login.
     * Customers are strictly excluded.
     */
    public function isUserEligible(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->status !== 'active') {
            return false;
        }

        // Strictly exclude purely customer accounts
        if ($user->hasRole('customer') && !$user->hasAnyRole(['super_admin', 'salon_admin', 'admin', 'manager', 'employee', 'staff', 'barber', 'stylist'])) {
            return false;
        }

        // Must possess an authorized salon or platform role
        return $user->hasAnyRole(['super_admin', 'salon_admin', 'admin', 'manager', 'employee', 'staff', 'barber', 'stylist']);
    }

    /**
     * Alias for isUserEligible
     */
    public function isEligibleUser(?User $user): bool
    {
        return $this->isUserEligible($user);
    }

    /**
     * Validate 6-digit numeric PIN strength.
     * Rejects common, sequential, and repeated patterns.
     */
    public function validatePinStrength(string $pin): array
    {
        // 1. Must be exact 6 numeric digits
        if (!preg_match('/^[0-9]{6}$/', $pin)) {
            return [
                'valid' => false,
                'message' => 'PIN must be exactly 6 numeric digits.',
            ];
        }

        // 2. Reject repeated digits (000000, 111111, 222222, etc.)
        if (preg_match('/^([0-9])\1{5}$/', $pin)) {
            return [
                'valid' => false,
                'message' => 'PIN is too simple. Repeated digits (e.g. 000000, 111111) are not allowed.',
            ];
        }

        // 3. Reject sequential ascending or descending
        $sequential = [
            '012345', '123456', '234567', '345678', '456789',
            '543210', '654321', '765432', '876543', '987654',
        ];
        if (in_array($pin, $sequential, true)) {
            return [
                'valid' => false,
                'message' => 'PIN is too predictable. Sequential numbers (e.g. 123456, 654321) are not allowed.',
            ];
        }

        // 4. Reject repeated 2-digit and 3-digit patterns (121212, 123123, 112233)
        $patternWeak = [
            '121212', '212121', '123123', '321321', '112233', '332211',
            '696969', '101010', '010101', '123321',
        ];
        if (in_array($pin, $patternWeak, true)) {
            return [
                'valid' => false,
                'message' => 'PIN is too common. Please choose a more secure combination.',
            ];
        }

        return [
            'valid' => true,
            'message' => 'PIN is valid and secure.',
        ];
    }

    /**
     * Parse browser and operating system details from Request User-Agent.
     */
    public function parseDeviceDetails(Request $request): array
    {
        $userAgent = $request->userAgent() ?? '';

        // Browser Detection
        $browser = 'Unknown Browser';
        if (preg_match('/Edg\/([0-9\.]+)/i', $userAgent, $m)) {
            $browser = 'Microsoft Edge ' . explode('.', $m[1])[0];
        } elseif (preg_match('/Chrome\/([0-9\.]+)/i', $userAgent, $m)) {
            $browser = 'Google Chrome ' . explode('.', $m[1])[0];
        } elseif (preg_match('/Firefox\/([0-9\.]+)/i', $userAgent, $m)) {
            $browser = 'Mozilla Firefox ' . explode('.', $m[1])[0];
        } elseif (preg_match('/Safari\/([0-9\.]+)/i', $userAgent, $m) && !preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Apple Safari ' . explode('.', $m[1])[0];
        } elseif (preg_match('/Opera|OPR\/([0-9\.]+)/i', $userAgent, $m)) {
            $browser = 'Opera';
        }

        // Operating System Detection
        $os = 'Unknown OS';
        if (preg_match('/Windows NT 10.0/i', $userAgent)) {
            $os = 'Windows 10/11';
        } elseif (preg_match('/Windows NT 6.3/i', $userAgent)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/Windows NT 6.1/i', $userAgent)) {
            $os = 'Windows 7';
        } elseif (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            $os = 'iOS Device';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = 'Android Device';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        }

        $defaultName = $browser . ' on ' . $os;

        return [
            'browser' => $browser,
            'operating_system' => $os,
            'default_name' => $defaultName,
        ];
    }

    /**
     * Register a new trusted device for an authenticated user.
     * Generates a 64-char random token, saves SHA-256 hash in DB, and returns an encrypted cookie.
     */
    public function registerDevice(User $user, Request $request, ?string $pin = null, ?string $customDeviceName = null): array
    {
        if (!$this->isUserEligible($user)) {
            throw new \InvalidArgumentException('Customer accounts are not eligible for trusted device registration.');
        }

        $deviceInfo = $this->parseDeviceDetails($request);
        $deviceUuid = Str::uuid()->toString();
        $rawToken = Str::random(64);
        $tokenHash = hash('sha256', $rawToken);

        $deviceName = trim($customDeviceName ?: $deviceInfo['default_name']);

        $device = TrustedDevice::create([
            'user_id' => $user->id,
            'salon_id' => $user->salon_id,
            'device_uuid' => $deviceUuid,
            'device_name' => $deviceName,
            'device_token_hash' => $tokenHash,
            'browser' => $deviceInfo['browser'],
            'operating_system' => $deviceInfo['operating_system'],
            'last_ip' => $request->ip(),
            'first_used_at' => now(),
            'last_used_at' => now(),
            'expires_at' => now()->addDays(self::TRUST_DAYS),
            'status' => 'trusted',
        ]);

        DevicePinAuditLog::log(
            'device_registered',
            $user->id,
            $user->salon_id,
            $device->id,
            ['device_name' => $deviceName, 'browser' => $deviceInfo['browser'], 'os' => $deviceInfo['operating_system']]
        );

        // If a PIN is supplied during registration, setup or update user PIN
        if ($pin !== null) {
            $pinStrength = $this->validatePinStrength($pin);
            if (!$pinStrength['valid']) {
                throw new \InvalidArgumentException($pinStrength['message']);
            }

            $pinRecord = UserDevicePin::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'salon_id' => $user->salon_id,
                ],
                [
                    'trusted_device_id' => $device->id,
                    'pin_hash' => Hash::make($pin),
                    'failed_attempts' => 0,
                    'locked_until' => null,
                    'pin_enabled' => true,
                    'last_verified_at' => now(),
                ]
            );

            DevicePinAuditLog::log(
                'pin_created',
                $user->id,
                $user->salon_id,
                $device->id,
                ['action' => 'setup_on_device_registration']
            );
        }

        // Build the Secure HttpOnly cookie: device_uuid|raw_token
        $cookieValue = $deviceUuid . '|' . $rawToken;
        $cookieMinutes = self::TRUST_DAYS * 24 * 60; // 90 days in minutes
        $secure = $request->isSecure() || app()->environment('production');

        $cookie = cookie(
            self::COOKIE_NAME,
            $cookieValue,
            $cookieMinutes,
            '/',
            null,
            $secure,
            true, // HttpOnly
            false,
            'Lax' // SameSite
        );

        return [
            'device' => $device,
            'cookie' => $cookie,
        ];
    }

    /**
     * Retrieve the recognized active TrustedDevice from the incoming request cookie.
     */
    public function getTrustedDeviceFromRequest(Request $request): ?TrustedDevice
    {
        $cookieValue = $request->cookie(self::COOKIE_NAME);
        if (!$cookieValue || !is_string($cookieValue)) {
            return null;
        }

        $parts = explode('|', $cookieValue, 2);
        if (count($parts) !== 2) {
            return null;
        }

        [$deviceUuid, $rawToken] = $parts;
        if (empty($deviceUuid) || empty($rawToken)) {
            return null;
        }

        $device = TrustedDevice::where('device_uuid', $deviceUuid)
            ->where('status', 'trusted')
            ->whereNull('revoked_at')
            ->with(['user.salon', 'user.roles'])
            ->first();

        if (!$device) {
            return null;
        }

        // Verify cryptographic token hash using constant-time comparison
        $calculatedHash = hash('sha256', $rawToken);
        if (!hash_equals($device->device_token_hash, $calculatedHash)) {
            return null;
        }

        // Check expiration
        if ($device->isExpired()) {
            return null;
        }

        // Check if user is eligible and active
        if (!$device->user || !$this->isUserEligible($device->user)) {
            return null;
        }

        // If user belongs to a salon, verify that salon is active
        if ($device->user->salon && !$device->user->salon->is_active) {
            return null;
        }

        return $device;
    }

    /**
     * Verify PIN against the recognized trusted device and authenticate user.
     * Uses a DB transaction with lockForUpdate to prevent race conditions on
     * concurrent failed attempts (each failure increments atomically in SQL).
     */
    public function verifyPin(TrustedDevice $device, string $pin, Request $request): array
    {
        $user = $device->user;
        if (!$user || !$this->isUserEligible($user)) {
            return [
                'success' => false,
                'error' => 'User account is not eligible for PIN authentication.',
                'locked' => false,
            ];
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($device, $pin, $request, $user) {
            $pinRecord = UserDevicePin::where('user_id', $user->id)
                ->where('pin_enabled', true)
                ->lockForUpdate()
                ->first();

            if (!$pinRecord) {
                return [
                    'success' => false,
                    'error' => 'PIN login is not configured or enabled for this account.',
                    'locked' => false,
                ];
            }

            // Re-check lock inside transaction with fresh data
            if ($pinRecord->isLocked()) {
                $minutes = max(1, (int) ceil($pinRecord->lockoutSecondsRemaining() / 60));
                return [
                    'success' => false,
                    'error' => "This device is temporarily locked due to consecutive failed attempts. Please try again in {$minutes} minute(s) or use your password.",
                    'locked' => true,
                    'seconds_remaining' => $pinRecord->lockoutSecondsRemaining(),
                ];
            }

            // Validate PIN hash
            if (Hash::check($pin, $pinRecord->pin_hash)) {
                // Success: reset failed attempts
                $pinRecord->resetFailedAttempts();
                $pinRecord->update(['last_verified_at' => now()]);
                $device->markAsUsed($request->ip());

                DevicePinAuditLog::log(
                    'pin_login_success',
                    $user->id,
                    $user->salon_id,
                    $device->id,
                    ['ip' => $request->ip(), 'device_name' => $device->device_name]
                );

                return [
                    'success' => true,
                    'user' => $user,
                    'device' => $device,
                ];
            }

            // Failed attempt: increment atomically in SQL to handle concurrency
            \Illuminate\Support\Facades\DB::table('user_device_pins')
                ->where('id', $pinRecord->id)
                ->update([
                    'failed_attempts' => \Illuminate\Support\Facades\DB::raw('failed_attempts + 1'),
                    'updated_at' => now(),
                ]);

            // Re-load fresh record to get updated count and check lockout
            $pinRecord->refresh();
            $freshAttempts = $pinRecord->failed_attempts;

            // Apply progressive lockout thresholds
            $lockedUntil = null;
            if ($freshAttempts >= 5) {
                $lockMinutes = 15;
                if ($freshAttempts >= 10) {
                    $lockMinutes = 60;
                } elseif ($freshAttempts >= 7) {
                    $lockMinutes = 30;
                }
                $lockedUntil = now()->addMinutes($lockMinutes);
                \Illuminate\Support\Facades\DB::table('user_device_pins')
                    ->where('id', $pinRecord->id)
                    ->update(['locked_until' => $lockedUntil, 'updated_at' => now()]);
                $pinRecord->refresh();
            }

            DevicePinAuditLog::log(
                'pin_login_failed',
                $user->id,
                $user->salon_id,
                $device->id,
                ['ip' => $request->ip(), 'failed_attempts' => $freshAttempts]
            );

            if ($pinRecord->isLocked()) {
                DevicePinAuditLog::log(
                    'pin_locked',
                    $user->id,
                    $user->salon_id,
                    $device->id,
                    ['locked_until' => $pinRecord->locked_until?->toIso8601String()]
                );

                $minutes = max(1, (int) ceil($pinRecord->lockoutSecondsRemaining() / 60));
                return [
                    'success' => false,
                    'error' => "Too many incorrect PIN attempts. This device has been locked for {$minutes} minutes for your security.",
                    'locked' => true,
                    'seconds_remaining' => $pinRecord->lockoutSecondsRemaining(),
                ];
            }

            $remaining = max(1, self::MAX_ATTEMPTS - $freshAttempts);

            return [
                'success' => false,
                'error' => "Incorrect PIN. You have {$remaining} attempt(s) remaining before a temporary lockout.",
                'locked' => false,
                'remaining_attempts' => $remaining,
            ];
        });
    }

    /**
     * Revoke a single trusted device.
     */
    public function revokeDevice(TrustedDevice $device, User $actingUser, ?string $reason = null): void
    {
        // Enforce authorization
        if ($device->user_id !== $actingUser->id && !$actingUser->isSuperAdmin() && !$actingUser->hasRole('salon_admin')) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized to revoke this device.');
        }

        $device->revoke($reason);

        DevicePinAuditLog::log(
            'device_revoked',
            $device->user_id,
            $device->salon_id,
            $device->id,
            ['revoked_by' => $actingUser->id, 'reason' => $reason]
        );
    }

    /**
     * Revoke all other devices for a user.
     */
    public function revokeAllOtherDevices(User $user, ?TrustedDevice $currentDevice = null): int
    {
        $query = TrustedDevice::where('user_id', $user->id)
            ->where('status', 'trusted')
            ->whereNull('revoked_at');

        if ($currentDevice) {
            $query->where('id', '!=', $currentDevice->id);
        }

        $devicesToRevoke = $query->get();
        $count = $devicesToRevoke->count();

        foreach ($devicesToRevoke as $d) {
            $d->revoke('revoked_all_other_devices');
        }

        DevicePinAuditLog::log(
            'all_devices_revoked',
            $user->id,
            $user->salon_id,
            $currentDevice?->id,
            ['revoked_count' => $count]
        );

        return $count;
    }

    /**
     * Rename a trusted device.
     */
    public function renameDevice(TrustedDevice $device, User $actingUser, string $newName): TrustedDevice
    {
        if ($device->user_id !== $actingUser->id && !$actingUser->isSuperAdmin() && !$actingUser->hasRole('salon_admin')) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized to rename this device.');
        }

        $oldName = $device->device_name;
        $cleanName = trim(strip_tags($newName));
        if (empty($cleanName)) {
            throw new \InvalidArgumentException('Device name cannot be empty.');
        }

        $device->update(['device_name' => substr($cleanName, 0, 150)]);

        DevicePinAuditLog::log(
            'device_renamed',
            $device->user_id,
            $device->salon_id,
            $device->id,
            ['old_name' => $oldName, 'new_name' => $cleanName]
        );

        return $device;
    }

    /**
     * Change PIN with password confirmation.
     */
    public function changePin(User $user, string $newPin, ?string $currentPassword = null): array
    {
        if ($currentPassword !== null && !Hash::check($currentPassword, $user->password)) {
            return [
                'success' => false,
                'message' => 'The current password provided is incorrect.',
            ];
        }

        $strength = $this->validatePinStrength($newPin);
        if (!$strength['valid']) {
            return [
                'success' => false,
                'message' => $strength['message'],
            ];
        }

        $pinRecord = UserDevicePin::updateOrCreate(
            [
                'user_id' => $user->id,
                'salon_id' => $user->salon_id,
            ],
            [
                'pin_hash' => Hash::make($newPin),
                'failed_attempts' => 0,
                'locked_until' => null,
                'pin_enabled' => true,
                'last_verified_at' => now(),
            ]
        );

        DevicePinAuditLog::log(
            'pin_changed',
            $user->id,
            $user->salon_id,
            null,
            ['action' => 'user_pin_update']
        );

        return [
            'success' => true,
            'message' => 'PIN has been updated successfully.',
        ];
    }

    /**
     * Disable PIN login.
     */
    public function disablePin(User $user, ?string $currentPassword = null): array
    {
        if ($currentPassword !== null && !Hash::check($currentPassword, $user->password)) {
            return [
                'success' => false,
                'message' => 'The current password provided is incorrect.',
            ];
        }

        UserDevicePin::where('user_id', $user->id)->update(['pin_enabled' => false]);

        DevicePinAuditLog::log(
            'pin_disabled',
            $user->id,
            $user->salon_id,
            null,
            ['action' => 'user_pin_disabled']
        );

        return [
            'success' => true,
            'message' => 'PIN login has been disabled.',
        ];
    }

    /**
     * Enable PIN login.
     */
    public function enablePin(User $user, ?string $currentPassword = null): array
    {
        if ($currentPassword !== null && !Hash::check($currentPassword, $user->password)) {
            return [
                'success' => false,
                'message' => 'The current password provided is incorrect.',
            ];
        }

        $pinRecord = UserDevicePin::where('user_id', $user->id)->first();
        if (!$pinRecord) {
            return [
                'success' => false,
                'message' => 'Please create a PIN before enabling PIN login.',
            ];
        }

        $pinRecord->update(['pin_enabled' => true]);

        DevicePinAuditLog::log(
            'pin_enabled',
            $user->id,
            $user->salon_id,
            null,
            ['action' => 'user_pin_enabled']
        );

        return [
            'success' => true,
            'message' => 'PIN login has been enabled.',
        ];
    }

    /**
     * Generate cookie to clear the trusted device from client browser.
     */
    public function createForgetCookie(): Cookie
    {
        return cookie()->forget(self::COOKIE_NAME);
    }
}

