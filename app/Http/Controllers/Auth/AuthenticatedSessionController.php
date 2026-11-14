<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        // If user is already authenticated, redirect to appropriate dashboard
        if (Auth::check()) {
            \Illuminate\Support\Facades\Log::info('AuthenticatedSessionController: User already logged in, redirecting to home.dashboard');
            return redirect()->route('home.dashboard');
        }

        // Recognize trusted device: redirect to PIN login if recognized and not in password fallback mode
        if ($request->query('mode') !== 'password') {
            $deviceService = app(\App\Services\TrustedDeviceService::class);
            $device = $deviceService->getTrustedDeviceFromRequest($request);
            if ($device && $device->user && $device->user->devicePin && $device->user->devicePin->pin_enabled) {
                return redirect()->route('pin.login');
            }
        }

        \Illuminate\Support\Facades\Log::info('AuthenticatedSessionController: Showing login view');
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user && $user->salon_id) {
            session(['salon_id' => $user->salon_id]);
        }

        // Check if eligible for Trusted Device registration
        $deviceService = app(\App\Services\TrustedDeviceService::class);
        $forgetOtherUserCookie = null;
        if ($deviceService->isUserEligible($user)) {
            $currentDevice = $deviceService->getTrustedDeviceFromRequest($request);
            // Multi-user device check: if this browser was previously trusted by another user,
            // disconnect the previous user's association to prevent unauthorized cross-account PIN access.
            if ($currentDevice && $currentDevice->user_id !== $user->id) {
                $forgetOtherUserCookie = $deviceService->createForgetCookie();
                $currentDevice = null;
            }

            if (!$currentDevice && !session('pin_setup_skipped')) {
                session(['pin_setup_pending' => true]);
                $response = redirect()->route('pin.setup.prompt');
                if ($forgetOtherUserCookie) {
                    $response->withCookie($forgetOtherUserCookie);
                }
                return $response;
            }
        }

        // Redirect to global dashboard handler
        $response = redirect()->intended(route('home.dashboard'));
        if ($forgetOtherUserCookie) {
            $response->withCookie($forgetOtherUserCookie);
        }
        return $response;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $deviceService = app(\App\Services\TrustedDeviceService::class);
        $forgetCookie = null;

        // If user explicitly chose "Log out and remove this device from trusted devices"
        if ($request->input('forget_device') === '1') {
            $device = $deviceService->getTrustedDeviceFromRequest($request);
            if ($device) {
                // Only revoke if the device belongs to the currently authenticated user.
                // If it belongs to a different account (shared browser), skip revocation but
                // still clear the cookie so this browser is no longer recognised.
                if (Auth::check() && $device->user_id === Auth::id()) {
                    try {
                        $deviceService->revokeDevice($device, Auth::user(), 'User chose forget device on logout');
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning('Logout revokeDevice failed: ' . $e->getMessage());
                    }
                }
                // Always issue the forget cookie regardless of ownership
            }
            $forgetCookie = $deviceService->createForgetCookie();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $response = redirect('/');
        if ($forgetCookie) {
            $response->withCookie($forgetCookie);
        }

        return $response;
    }
}
