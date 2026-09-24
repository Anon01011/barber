<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showAdminLoginForm()
    {
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();
            $user->load('salon'); // Eager load salon relationship

            // Check if user has any admin role
            if ($user->hasAnyRole(['super_admin', 'salon_admin', 'admin', 'manager', 'employee'])) {
                // Refresh permissions
                $user->forgetCachedPermissions();
                $user->load('roles.permissions');

                // Single Salon Mode Handling
                if (env('SINGLE_SALON_MODE', false)) {
                    $slug = env('SINGLE_SALON_SLUG', 'intranet');
                    if ($user->hasRole('employee')) {
                        return redirect()->route('employee.dashboard', ['salon_slug' => $slug]);
                    }
                    return redirect()->route('dashboard', ['salon_slug' => $slug]);
                }

                // Route Super Admin to Super Admin dashboard
                if ($user->hasRole('super_admin')) {
                    return redirect()->intended('/admin/dashboard');
                }

                // SaaS Check for salon users
                if ($user->salon) {
                    if (!$user->salon->is_active) {
                        // Check if there's a pending subscription (e.g. manual payment waiting for approval)
                        $pendingSubscription = $user->salon->subscriptions()
                            ->where('status', 'pending')
                            ->latest()
                            ->first();

                        if (!$pendingSubscription) {
                            Auth::logout();
                            return back()->withErrors(['email' => 'Your salon subscription has expired or is inactive.']);
                        }
                        // If pending subscription exists, allow login so they can see the pending approval page
                    }

                    if (!$user->salon->activeSubscription) {
                        // Allow login if pending subscription exists
                        $pendingSubscription = $user->salon->subscriptions()
                            ->where('status', 'pending')
                            ->latest()
                            ->first();

                        if (!$pendingSubscription) {
                            Auth::logout();
                            return back()->withErrors(['email' => 'Your salon does not have an active subscription.']);
                        }
                    }

                    // Set salon context in session
                    session(['salon_id' => $user->salon_id]);
                } else {
                    Auth::logout();
                    return back()->withErrors(['email' => 'Your account is not properly configured.']);
                }

                // Redirect employees to employee dashboard
                if ($user->hasRole('employee')) {
                    return redirect()->route('employee.dashboard', ['salon_slug' => $user->salon->slug]);
                }

                return redirect()->route('dashboard', ['salon_slug' => $user->salon->slug]);
            }

            Auth::logout();
            return back()->withErrors(['email' => 'You do not have admin access.']);
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();
            $user->load('salon'); // Eager load salon relationship

            // Refresh permissions
            $user->forgetCachedPermissions();
            $user->load('roles.permissions');

            // Single Salon Mode Handling
            if (env('SINGLE_SALON_MODE', false)) {
                $slug = env('SINGLE_SALON_SLUG', 'intranet');
                if ($user->hasRole('employee')) {
                    return redirect()->route('employee.dashboard', ['salon_slug' => $slug]);
                }
                return redirect()->route('dashboard', ['salon_slug' => $slug]);
            }

            // Route based on role
            if ($user->hasRole('super_admin')) {
                // Super Admin - no salon context needed
                return redirect()->intended('/admin/dashboard');
            }

            // SaaS Check for salon users
            if ($user->salon) {
                if (!$user->salon->is_active) {
                    // Check if there's a pending subscription (e.g. manual payment waiting for approval)
                    $pendingSubscription = $user->salon->subscriptions()
                        ->where('status', 'pending')
                        ->latest()
                        ->first();

                    if (!$pendingSubscription) {
                        Auth::logout();
                        return back()->withErrors(['email' => 'Your salon subscription has expired or is inactive.']);
                    }
                    // If pending subscription exists, allow login so they can see the pending approval page
                }

                // Check if salon has active subscription
                if (!$user->salon->activeSubscription) {
                    // Allow login if pending subscription exists
                    $pendingSubscription = $user->salon->subscriptions()
                        ->where('status', 'pending')
                        ->latest()
                        ->first();

                    if (!$pendingSubscription) {
                        Auth::logout();
                        return back()->withErrors(['email' => 'Your salon does not have an active subscription.']);
                    }
                }

                // Set salon context in session
                session(['salon_id' => $user->salon_id]);
            } else {
                // User has no salon and is not super admin
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is not properly configured.']);
            }

            // Redirect employees to employee dashboard
            if ($user->hasRole('employee')) {
                return redirect()->route('employee.dashboard', ['salon_slug' => $user->salon->slug]);
            }

            return redirect()->route('dashboard', ['salon_slug' => $user->salon->slug]);
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    protected function authenticated(Request $request, $user)
    {
        // Redirect to dashboard
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    protected function redirectTo()
    {
        return RouteServiceProvider::HOME;
    }
}
