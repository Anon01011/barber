<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\TrustedDevice;
use App\Models\UserDevicePin;
use App\Services\TrustedDeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceManagementController extends Controller
{
    protected TrustedDeviceService $deviceService;

    public function __construct(TrustedDeviceService $deviceService)
    {
        $this->middleware('auth');
        $this->deviceService = $deviceService;
    }

    /**
     * List all trusted devices and PIN status for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentDevice = $this->deviceService->getTrustedDeviceFromRequest($request);
        $isCurrentDeviceTrusted = $currentDevice && $currentDevice->user_id === $user->id;

        $devices = TrustedDevice::where('user_id', $user->id)
            ->with('salon')
            ->orderBy('last_used_at', 'desc')
            ->get()
            ->map(function ($device) use ($currentDevice, $isCurrentDeviceTrusted) {
                return [
                    'id' => $device->id,
                    'device_name' => $device->device_name,
                    'browser' => $device->browser ?? 'Unknown',
                    'operating_system' => $device->operating_system ?? 'Unknown',
                    'salon_name' => $device->salon?->name ?? 'All Salons / Admin',
                    'first_registered' => $device->first_used_at?->format('M d, Y h:i A') ?? $device->created_at->format('M d, Y h:i A'),
                    'last_active' => $device->last_used_at?->diffForHumans() ?? 'Never',
                    'last_ip' => $device->last_ip ? (substr($device->last_ip, 0, 7) . '***') : null,
                    'status' => $device->status,
                    'is_current' => $isCurrentDeviceTrusted && $currentDevice->id === $device->id,
                    'is_trusted' => $device->isTrusted(),
                    'is_revoked' => $device->isRevoked(),
                    'is_expired' => $device->isExpired(),
                ];
            });

        $pinRecord = $user->devicePin;
        $pinStatus = [
            'has_pin' => $pinRecord !== null,
            'is_enabled' => $pinRecord ? (bool) $pinRecord->pin_enabled : false,
            'failed_attempts' => $pinRecord?->failed_attempts ?? 0,
            'is_locked' => $pinRecord ? $pinRecord->isLocked() : false,
            'last_verified' => $pinRecord?->last_verified_at?->diffForHumans() ?? 'Never',
        ];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'devices' => $devices,
                'pin' => $pinStatus,
                'is_current_device_trusted' => $isCurrentDeviceTrusted,
            ]);
        }

        return view('admin.settings.partials.trusted-devices', compact('devices', 'pinStatus', 'currentDevice'));
    }

    /**
     * Rename a trusted device.
     */
    public function rename(Request $request, $id)
    {
        $validated = $request->validate([
            'device_name' => ['required', 'string', 'min:2', 'max:150'],
        ]);

        $device = TrustedDevice::where('user_id', Auth::id())->findOrFail($id);

        try {
            $this->deviceService->renameDevice($device, Auth::user(), $validated['device_name']);

            return response()->json([
                'success' => true,
                'message' => 'Device renamed successfully.',
                'device_name' => $device->device_name,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Device rename error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to rename device. Please try again.'], 422);
        }
    }

    /**
     * Revoke a single device.
     */
    public function revoke(Request $request, $id)
    {
        $device = TrustedDevice::where('user_id', Auth::id())->findOrFail($id);

        try {
            $currentDevice = $this->deviceService->getTrustedDeviceFromRequest($request);
            $isCurrent = $currentDevice && $currentDevice->id === $device->id;

            $this->deviceService->revokeDevice($device, Auth::user(), 'User revoked device via settings');

            $response = response()->json([
                'success' => true,
                'message' => 'Device has been revoked. PIN login on that device is now disabled.',
                'is_current' => $isCurrent,
            ]);

            // If the user revoked the current browser's device, clear cookie
            if ($isCurrent) {
                $response->withCookie($this->deviceService->createForgetCookie());
            }

            return $response;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Device revoke error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to revoke device. Please try again.'], 422);
        }
    }

    /**
     * Trust the current browser/device (sets trusted device cookie and DB record).
     */
    public function trustCurrent(Request $request)
    {
        $user = Auth::user();
        if (!$this->deviceService->isUserEligible($user)) {
            return response()->json(['success' => false, 'message' => 'Your account is not eligible for trusted device PIN login.'], 403);
        }

        $existingDevice = $this->deviceService->getTrustedDeviceFromRequest($request);
        if ($existingDevice && $existingDevice->user_id === $user->id) {
            return response()->json(['success' => true, 'message' => 'This device is already trusted for your account.']);
        }

        try {
            $registration = $this->deviceService->registerDevice($user, $request, null, null);

            return response()->json([
                'success' => true,
                'message' => 'This device is now trusted! You can use your PIN on next login.',
            ])->withCookie($registration['cookie']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Trust current device error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to register this device as trusted. Please try again.'], 422);
        }
    }

    /**
     * Revoke all other trusted devices except current.
     */
    public function revokeAll(Request $request)
    {
        try {
            $currentDevice = $this->deviceService->getTrustedDeviceFromRequest($request);
            $count = $this->deviceService->revokeAllOtherDevices(Auth::user(), $currentDevice);

            return response()->json([
                'success' => true,
                'message' => "Successfully revoked {$count} other device(s).",
                'revoked_count' => $count,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Revoke all devices error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to revoke other devices. Please try again.'], 422);
        }
    }

    /**
     * Set up PIN (create first time from Settings).
     * Also registers this device as trusted (sets cookie) if not already trusted.
     */
    public function setupPin(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'pin' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
            'pin_confirmation' => ['required', 'same:pin'],
        ], [
            'pin.required' => 'Please enter a 6-digit PIN.',
            'pin.size' => 'The PIN must be exactly 6 digits.',
            'pin.regex' => 'The PIN must contain digits only.',
            'pin_confirmation.same' => 'The confirmation PIN does not match.',
        ]);

        $user = Auth::user();
        $result = $this->deviceService->changePin($user, $validated['pin'], $validated['current_password']);

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        // Also register this device as trusted so the cookie is set for next login
        $response = response()->json(['success' => true, 'message' => 'PIN has been created and enabled successfully.', 'device_registered' => false]);
        $existingDevice = $this->deviceService->getTrustedDeviceFromRequest($request);
        $isCurrentUsersDevice = $existingDevice && $existingDevice->user_id === $user->id;

        if (!$isCurrentUsersDevice && $this->deviceService->isUserEligible($user)) {
            try {
                $registration = $this->deviceService->registerDevice($user, $request, null, null);
                $response = response()->json(['success' => true, 'message' => 'PIN has been created and enabled successfully. This device is now trusted.', 'device_registered' => true])
                    ->withCookie($registration['cookie']);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Auto-register on setupPin failed: ' . $e->getMessage());
                // PIN was saved successfully; device trust failed — accurate partial-success response.
                // User can trust this browser via Settings > "Trust This Browser" button.
                $response = response()->json([
                    'success' => true,
                    'message' => 'PIN has been created successfully. To enable PIN login on this browser, use the "Trust This Browser" button in Settings.',
                    'device_registered' => false,
                ]);
            }
        }

        return $response;
    }

    /**
     * Change PIN from Settings.
     * Also registers this device as trusted (sets cookie) if not already trusted.
     */
    public function changePin(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'pin' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
            'pin_confirmation' => ['required', 'same:pin'],
        ], [
            'pin.required' => 'Please enter a 6-digit PIN.',
            'pin.size' => 'The PIN must be exactly 6 digits.',
            'pin.regex' => 'The PIN must contain digits only.',
            'pin_confirmation.same' => 'The confirmation PIN does not match.',
        ]);

        $user = Auth::user();
        $result = $this->deviceService->changePin($user, $validated['pin'], $validated['current_password']);

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        // Also register this device as trusted (sets cookie) if not already trusted
        $response = response()->json(['success' => true, 'message' => 'PIN changed successfully.', 'device_registered' => false]);
        $existingDevice = $this->deviceService->getTrustedDeviceFromRequest($request);
        $isCurrentUsersDevice = $existingDevice && $existingDevice->user_id === $user->id;

        if (!$isCurrentUsersDevice && $this->deviceService->isUserEligible($user)) {
            try {
                $registration = $this->deviceService->registerDevice($user, $request, null, null);
                $response = response()->json(['success' => true, 'message' => 'PIN changed successfully. This device is now trusted.', 'device_registered' => true])
                    ->withCookie($registration['cookie']);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Auto-register on changePin failed: ' . $e->getMessage());
                // PIN changed successfully; device trust failed — accurate partial-success response.
                // User can trust this browser via Settings > "Trust This Browser" button.
                $response = response()->json([
                    'success' => true,
                    'message' => 'PIN changed successfully. To enable PIN login on this browser, use the "Trust This Browser" button in Settings.',
                    'device_registered' => false,
                ]);
            }
        }

        return $response;
    }

    /**
     * Disable PIN login.
     */
    public function disablePin(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        $result = $this->deviceService->disablePin(Auth::user(), $validated['current_password']);

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        return response()->json(['success' => true, 'message' => 'PIN login disabled successfully.']);
    }

    /**
     * Enable PIN login.
     */
    public function enablePin(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        $result = $this->deviceService->enablePin(Auth::user(), $validated['current_password']);

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        return response()->json(['success' => true, 'message' => 'PIN login enabled successfully.']);
    }
}

