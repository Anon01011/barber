<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TrustedDevice;
use App\Models\User;
use App\Services\TrustedDeviceService;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class PinLoginController extends Controller
{
    protected TrustedDeviceService $deviceService;

    public function __construct(TrustedDeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    /**
     * Display the PIN Login screen if the device is recognized.
     */
    public function showPinLogin(Request $request)
    {
        // If user is already authenticated, redirect to appropriate dashboard
        if (Auth::check()) {
            return redirect()->route('home.dashboard');
        }

        // If explicitly requested password mode, redirect to standard login form
        if ($request->query('mode') === 'password') {
            return redirect()->route('login');
        }

        $device = $this->deviceService->getTrustedDeviceFromRequest($request);

        // If no trusted device recognized, redirect to normal login
        if (!$device) {
            return redirect()->route('login');
        }

        $user = $device->user;
        $salon = $user->salon;

        // Fetch user's PIN status
        $pinRecord = $user->devicePin;
        if (!$pinRecord || !$pinRecord->pin_enabled) {
            return redirect()->route('login');
        }

        // Mask email for display (e.g. j***n@example.com)
        $email = $user->email;
        $emailParts = explode('@', $email);
        $namePart = $emailParts[0];
        $domainPart = $emailParts[1] ?? '';
        $maskedEmail = strlen($namePart) > 2
            ? substr($namePart, 0, 1) . str_repeat('*', max(3, strlen($namePart) - 2)) . substr($namePart, -1) . '@' . $domainPart
            : substr($namePart, 0, 1) . '***@' . $domainPart;

        // Branding
        $settingsService = app(SettingsService::class);
        $appName = $salon?->name ?? $settingsService->get('app_name', config('app.name'));
        $appLogo = $salon?->logo ? asset('storage/' . $salon->logo) : $settingsService->getLogoUrl();

        $isLocked = $pinRecord->isLocked();
        $lockoutSeconds = $pinRecord->lockoutSecondsRemaining();

        return view('auth.pin-login', compact(
            'device',
            'user',
            'salon',
            'maskedEmail',
            'appName',
            'appLogo',
            'isLocked',
            'lockoutSeconds'
        ));
    }

    /**
     * Authenticate via 6-digit PIN.
     */
    public function pinLogin(Request $request)
    {
        $throttleKey = 'pin-login|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $message = "Too many attempts. Please try again in {$seconds} seconds.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $message], 429);
            }
            return back()->withErrors(['pin' => $message]);
        }

        $validated = $request->validate([
            'pin' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'pin.required' => 'Please enter your 6-digit PIN.',
            'pin.size' => 'The PIN must be exactly 6 digits.',
            'pin.regex' => 'The PIN must contain numbers only.',
        ]);

        $device = $this->deviceService->getTrustedDeviceFromRequest($request);

        if (!$device) {
            RateLimiter::hit($throttleKey, 60);
            $message = 'This browser is not recognized as a trusted device. Please sign in with your email and password.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $message, 'redirect' => route('login')], 403);
            }
            return redirect()->route('login')->withErrors(['email' => $message]);
        }

        $verification = $this->deviceService->verifyPin($device, $validated['pin'], $request);

        if (!$verification['success']) {
            RateLimiter::hit($throttleKey, 60);
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $verification['error'],
                    'locked' => $verification['locked'] ?? false,
                    'seconds_remaining' => $verification['seconds_remaining'] ?? 0,
                ], 422);
            }
            return back()->withErrors(['pin' => $verification['error']]);
        }

        // Login verified user — do NOT set remember-me; PIN auth uses a session-based login only.
        $user = $verification['user'];

        // Subscription check: mirror the password-login rule (active OR pending subscription required)
        if ($user->salon_id && $user->salon) {
            $salon = $user->salon;
            if (!$salon->is_active) {
                $pendingSubscription = $salon->subscriptions()
                    ->where('status', 'pending')
                    ->latest()
                    ->first();
                if (!$pendingSubscription) {
                    Auth::logout();
                    $message = 'Your salon subscription has expired or is inactive.';
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'error' => $message, 'redirect' => route('login')], 403);
                    }
                    return redirect()->route('login')->withErrors(['email' => $message]);
                }
            }
            if (!$salon->activeSubscription) {
                $pendingSubscription = $salon->subscriptions()
                    ->where('status', 'pending')
                    ->latest()
                    ->first();
                if (!$pendingSubscription) {
                    Auth::logout();
                    $message = 'Your salon does not have an active subscription.';
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'error' => $message, 'redirect' => route('login')], 403);
                    }
                    return redirect()->route('login')->withErrors(['email' => $message]);
                }
            }
        }

        Auth::login($user, false);
        $request->session()->regenerate();
        RateLimiter::clear($throttleKey);

        // Set multi-tenant salon session
        if ($user->salon_id) {
            session(['salon_id' => $user->salon_id]);
        }

        // Refresh permissions
        $user->forgetCachedPermissions();
        $user->loadMissing(['roles.permissions', 'salon']);

        // Determine destination redirect
        $redirectUrl = $this->determineRedirectUrl($user);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect()->intended($redirectUrl);
    }

    /**
     * Switch account / Clear device cookie and go to normal login.
     */
    public function switchAccount(Request $request)
    {
        $forgetCookie = $this->deviceService->createForgetCookie();

        if ($request->filled('forget_device')) {
            $device = $this->deviceService->getTrustedDeviceFromRequest($request);
            if ($device) {
                // Use the device's own user as acting user (this is a guest route — Auth::user() is null).
                // revokeDevice accepts the owner as acting user, which satisfies the ownership check.
                $actingUser = $device->user ?? ($device->user_id ? \App\Models\User::find($device->user_id) : null);
                if ($actingUser) {
                    try {
                        $this->deviceService->revokeDevice($device, $actingUser, 'User clicked forget device on switch');
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning('switchAccount revokeDevice failed: ' . $e->getMessage());
                    }
                }
            }
        }

        return redirect()->route('login', ['mode' => 'password'])->withCookie($forgetCookie);
    }

    /**
     * Show First-Time PIN Setup Prompt after normal password login.
     */
    public function showFirstTimePinPrompt(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$this->deviceService->isUserEligible($user)) {
            return redirect()->route('home.dashboard');
        }

        // Check if device is already trusted
        $existingDevice = $this->deviceService->getTrustedDeviceFromRequest($request);
        if ($existingDevice && $existingDevice->user_id === $user->id) {
            return redirect()->route('home.dashboard');
        }

        $deviceInfo = $this->deviceService->parseDeviceDetails($request);
        $settingsService = app(SettingsService::class);
        $appName = $user->salon?->name ?? $settingsService->get('app_name', config('app.name'));
        $appLogo = $user->salon?->logo ? asset('storage/' . $user->salon->logo) : $settingsService->getLogoUrl();

        return view('auth.pin-setup-prompt', compact('user', 'deviceInfo', 'appName', 'appLogo'));
    }

    /**
     * Complete First-Time PIN Setup: save PIN, register trusted device, and set cookie.
     */
    public function completeFirstTimePinSetup(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$this->deviceService->isUserEligible($user)) {
            return redirect()->route('home.dashboard');
        }

        $validated = $request->validate([
            'pin' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
            'pin_confirmation' => ['required', 'same:pin'],
            'device_name' => ['nullable', 'string', 'max:150'],
        ], [
            'pin.required' => 'Please enter a 6-digit PIN.',
            'pin.size' => 'The PIN must be exactly 6 numeric digits.',
            'pin.regex' => 'The PIN must contain digits only.',
            'pin_confirmation.same' => 'The confirmation PIN does not match.',
        ]);

        $strength = $this->deviceService->validatePinStrength($validated['pin']);
        if (!$strength['valid']) {
            return back()->withErrors(['pin' => $strength['message']])->withInput();
        }

        try {
            // If the user already has a PIN, do NOT overwrite it via device registration.
            // This path is first-time setup; re-using the existing PIN preserves security
            // and prevents device trust from silently changing the account-wide PIN.
            $existingPinRecord = $user->devicePin;
            $pinToRegister = ($existingPinRecord && $existingPinRecord->pin_hash)
                ? null           // Device registration only — no PIN overwrite
                : $validated['pin']; // Genuine first-time PIN creation

            $registration = $this->deviceService->registerDevice(
                $user,
                $request,
                $pinToRegister,
                $validated['device_name'] ?? null
            );

            // If this was first-time PIN setup (no existing PIN), the PIN was saved during registerDevice.
            // If user already had a PIN, we still need to ensure it is enabled.
            if ($existingPinRecord && !$existingPinRecord->pin_enabled) {
                $existingPinRecord->update(['pin_enabled' => true]);
            }

            session()->forget('pin_setup_pending');

            $redirectUrl = $this->determineRedirectUrl($user);

            return redirect($redirectUrl)
                ->withCookie($registration['cookie'])
                ->with('success', 'Trusted device registered! You can now log in quickly using your PIN.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Trusted device registration failed: ' . $e->getMessage(), ['user_id' => $user->id]);
            return back()->withErrors(['pin' => 'Unable to register this device right now. Please verify your PIN and try again.'])->withInput();
        }
    }

    /**
     * Skip PIN Setup and proceed to dashboard.
     */
    public function skipFirstTimePinSetup(Request $request)
    {
        session()->forget('pin_setup_pending');
        session(['pin_setup_skipped' => true]);

        $user = Auth::user();
        $redirectUrl = $user ? $this->determineRedirectUrl($user) : route('home.dashboard');

        return redirect($redirectUrl);
    }

    /**
     * Determine role-based destination redirect.
     */
            protected function determineRedirectUrl(User $user): string
    {
        try {
            if ($user->hasRole('super_admin')) {
                return route('admin.super.dashboard');
            }

            if ($user->salon_id && $user->salon) {
                $slug = trim($user->salon->slug);

                if ($user->hasRole('employee') && !$user->hasAnyRole(['salon_admin', 'manager', 'receptionist'])) {
                    $isBarberSalon = method_exists($user->salon, 'isBarber') && $user->salon->isBarber();
                    if ($isBarberSalon) {
                        $canUsePos = $user->salon && $user->salon->canUseFeature('POS System') && \App\Helpers\ModuleHelper::posEnabled() && $user->can('pos.view');
                        if ($canUsePos) {
                            return route('admin.pos.index', ['salon_slug' => $slug, 'view' => 'barber']);
                        }
                    }
                    return route('employee.dashboard', ['salon_slug' => $slug]);
                }

                if (method_exists($user->salon, 'isBarber') && $user->salon->isBarber()) {
                    return route('admin.pos.index', ['salon_slug' => $slug]);
                }

                return route('dashboard', ['salon_slug' => $slug]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('determineRedirectUrl failed, falling back to home: ' . $e->getMessage());
        }

        return route('home.dashboard');
    }
}
