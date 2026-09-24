<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardRedirectController extends Controller
{
    /**
     * Redirect the user to the appropriate dashboard.
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                \Illuminate\Support\Facades\Log::info('DashboardRedirect: No user found, redirecting to login');
                return redirect()->route('login');
            }

            // ... [rest of the logic] ...
            // (Note: I'm keeping the logic but wrapping it)

            // Allow super admin bypass early
            if ($user->hasRole('super_admin')) {
                return redirect()->route('admin.super.dashboard');
            }

            // Normal redirection logic...
            if ($user->salon_id) {
                $user->load('salon');
                if ($user->salon && !empty(trim($user->salon->slug))) {
                    $slug = trim($user->salon->slug);
                    if ($user->hasRole('employee') && !$user->hasAnyRole(['salon_admin', 'manager', 'receptionist'])) {
                        return redirect()->route('employee.dashboard', ['salon_slug' => $slug]);
                    }

                    // If user's salon operates as barber or has barber plan, route directly to terminal POS interface
                    if ($user->salon->isBarber()) {
                        return redirect()->route('admin.pos.index', ['salon_slug' => $slug]);
                    }

                    return redirect()->route('dashboard', ['salon_slug' => $slug]);
                }
            }

            // Fallback
            Auth::logout();
            return redirect()->route('home')->with('error', 'Temporary issue accessing dashboard. Please try again later.');

        } catch (\Exception $e) {
            \Log::error("DashboardRedirect Error: " . $e->getMessage());
            // Safe fallback if DB is locked
            if (Auth::check() && Auth::user()->hasRole('super_admin')) {
                return redirect()->route('admin.super.dashboard');
            }
            return redirect()->route('home')->with('error', 'Database is currently busy. Please refresh in a moment.');
        }
    }
}
