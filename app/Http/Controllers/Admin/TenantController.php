<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TenantController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super_admin');
    }

    /**
     * Display a listing of all salons (tenants)
     */
    public function index()
    {
        $salons = Salon::with(['owner', 'subscription.plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_salons' => Salon::count(),
            'active_salons' => Salon::active()->count(),
            'trial_salons' => Salon::onTrial()->count(),
            'expired_salons' => Salon::expired()->count(),
            'total_revenue' => Subscription::where('status', 'active')->sum('amount'),
        ];

        return view('admin.tenants.index', compact('salons', 'stats'));
    }

    /**
     * Show the form for creating a new salon
     */
    public function create()
    {
        $plans = Plan::active()->ordered()->get();
        return view('admin.tenants.create', compact('plans'));
    }

    /**
     * Store a newly created salon
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'salon_name' => 'required|string|max:255',
            'salon_slug' => 'required|string|max:255|unique:salons,slug',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_password' => 'required|string|min:8',
            'plan_id' => 'required|exists:plans,id',
            'trial_days' => 'nullable|integer|min:0|max:365',
            'salon_address' => 'nullable|string|max:255',
            'salon_phone' => 'nullable|string|max:20',
            'salon_email' => 'nullable|email|max:255',
            'salon_website' => 'nullable|url|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            // Create salon owner user
            $owner = User::create([
                'name' => $validated['owner_name'],
                'email' => $validated['owner_email'],
                'password' => Hash::make($validated['owner_password']),
                'email_verified_at' => now(),
            ]);

            $owner->assignRole('salon_admin');

            // Create salon
            $salon = Salon::create([
                'name' => $validated['salon_name'],
                'slug' => $validated['salon_slug'],
                'address' => $validated['salon_address'] ?? null,
                'phone' => $validated['salon_phone'] ?? null,
                'email' => $validated['salon_email'] ?? null,
                'website' => $validated['salon_website'] ?? null,
                'owner_id' => $owner->id,
                'is_active' => true,
                'trial_ends_at' => isset($validated['trial_days']) && $validated['trial_days'] > 0
                    ? now()->addDays($validated['trial_days'])
                    : null,
            ]);

            // Assign salon to owner
            $owner->update(['salon_id' => $salon->id]);

            // Create subscription if plan selected
            if ($validated['plan_id']) {
                $plan = Plan::find($validated['plan_id']);

                Subscription::create([
                    'salon_id' => $salon->id,
                    'plan_id' => $plan->id,
                    'starts_at' => now(),
                    'ends_at' => now()->addDays($plan->duration_in_days),
                    'status' => 'active',
                    'amount' => $plan->price,
                ]);
            }
        });

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Salon created successfully.');
    }

    /**
     * Display the specified salon
     */
    public function show(Salon $salon)
    {
        $salon->load([
            'owner',
            'subscription.plan',
            'subscriptions.plan',
            'users',
            'branches',
            'customers' => function ($query) {
                $query->latest()->take(10);
            },
            'bookings' => function ($query) {
                $query->latest()->take(10);
            }
        ]);

        $stats = [
            'total_users' => $salon->users()->count(),
            'total_customers' => $salon->customers()->count(),
            'total_bookings' => $salon->bookings()->count(),
            'total_revenue' => $salon->getTotalRevenue(),
            'monthly_revenue' => $salon->getMonthlyRevenue(),
            'active_customers' => $salon->getActiveCustomerCount(),
        ];

        return view('admin.tenants.show', compact('salon', 'stats'));
    }

    /**
     * Show the form for editing the specified salon
     */
    public function edit(Salon $salon)
    {
        $plans = Plan::active()->ordered()->get();
        return view('admin.tenants.edit', compact('salon', 'plans'));
    }

    /**
     * Update the specified salon
     */
    public function update(Request $request, Salon $salon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:salons,slug,' . $salon->id,
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'trial_ends_at' => 'nullable|date',
        ]);

        $salon->update($validated);

        return redirect()->route('admin.tenants.show', $salon)
            ->with('success', 'Salon updated successfully.');
    }

    /**
     * Suspend the specified salon
     */
    public function suspend(Salon $salon)
    {
        $salon->update(['is_active' => false]);

        return redirect()->back()
            ->with('success', 'Salon suspended successfully.');
    }

    /**
     * Activate the specified salon
     */
    public function activate(Salon $salon)
    {
        $salon->update(['is_active' => true]);

        return redirect()->back()
            ->with('success', 'Salon activated successfully.');
    }

    /**
     * Delete the specified salon
     */
    public function destroy(Salon $salon)
    {
        // Soft delete or cascade delete based on requirements
        $salon->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Salon deleted successfully.');
    }

    /**
     * Impersonate salon owner
     */
    public function impersonate(Salon $salon)
    {
        if (!$salon->owner) {
            return redirect()->back()->with('error', 'Salon has no owner.');
        }

        // Store current admin user
        session(['impersonate' => auth()->id()]);

        // Login as salon owner
        auth()->login($salon->owner);

        return redirect('/dashboard')->with('info', 'You are now impersonating ' . $salon->owner->name);
    }

    /**
     * Stop impersonation
     */
    public function stopImpersonation()
    {
        $adminId = session('impersonate');

        if ($adminId) {
            $admin = User::find($adminId);
            if ($admin) {
                auth()->login($admin);
                session()->forget('impersonate');
                return redirect('/admin/tenants')->with('success', 'Returned to admin account.');
            }
        }

        return redirect('/dashboard');
    }
}
