<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SuperAdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class SalonManagementController extends Controller
{
    /**
     * Display a listing of salons
     */
    public function index(Request $request)
    {
        $query = Salon::with(['owner', 'activeSubscription.plan']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('license_key', 'like', "%{$search}%")
                    ->orWhereHas('owner', function ($qOwner) use ($search) {
                        $qOwner->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'pending') {
                $query->whereHas('activeSubscription', function ($q) {
                    $q->where('status', 'pending');
                });
            }
        }

        $salons = $query->latest()->paginate(15);

        return view('super-admin.salons.index', compact('salons'));
    }

    /**
     * Helper to resolve salon by slug, ID, or model instance
     */
    protected function resolveSalon($salon): Salon
    {
        if ($salon instanceof Salon) {
            return $salon;
        }

        if (is_numeric($salon)) {
            return Salon::where('id', $salon)->first() 
                ?? Salon::where('slug', $salon)->firstOrFail();
        }

        return Salon::where('slug', $salon)->firstOrFail();
    }

    /**
     * Approve a pending salon
     */
    public function approve($salon)
    {
        $salon = $this->resolveSalon($salon);

        try {
            \DB::beginTransaction();

            // Activate salon if inactive
            if (!$salon->is_active) {
                $salon->update(['is_active' => true]);
            }

            // Activate subscription
            $subscription = $salon->activeSubscription;
            if ($subscription && $subscription->status === 'pending') {
                $subscription->update(['status' => 'active']);
            }

            \DB::commit();

            // Log action
            SuperAdminAuditLog::logAction(
                'salon.approve',
                $salon->id,
                Salon::class,
                $salon->id,
                ['salon_name' => $salon->name]
            );

            return back()->with('success', 'Salon approved successfully');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Failed to approve salon: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new salon
     */
    public function create()
    {
        $plans = \App\Models\Plan::where('is_active', true)->get();
        return view('super-admin.salons.create', compact('plans'));
    }

    /**
     * Store a newly created salon
     */
    public function store(Request $request)
    {
        $request->merge([
            'phone' => normalize_phone($request->input('phone')),
            'owner_phone' => normalize_phone($request->input('owner_phone')),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_type' => 'required|string|in:salon,barber,both',
            'slug' => 'required|string|max:255|unique:salons',
            'email' => 'required|email|max:255|unique:salons',
            'phone' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('salons', 'phone'),
            ],
            'address' => 'nullable|string',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255|unique:users,email',
            'owner_phone' => 'nullable|string|max:50',
            'owner_password' => ['required', 'confirmed', Password::defaults()],
            'plan_id' => 'nullable|exists:plans,id',
            'trial_days' => 'nullable|integer|min:0',
            'timezone' => 'required|string|max:255',
            'currency' => 'required|string|max:10',
        ]);

        try {
            \DB::beginTransaction();

            // Create salon
            $salon = Salon::create([
                'name' => $validated['name'],
                'business_type' => $validated['business_type'],
                'slug' => $validated['slug'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'timezone' => $validated['timezone'],
                'currency' => $validated['currency'],
                'is_active' => true,
            ]);

            // Create owner user
            $owner = User::create([
                'name' => $validated['owner_name'],
                'email' => $validated['owner_email'],
                'phone' => $validated['owner_phone'] ?? null,
                'password' => \Hash::make($validated['owner_password']),
                'salon_id' => $salon->id,
            ]);

            // Retrieve plan if selected
            $plan = !empty($validated['plan_id']) ? \App\Models\Plan::find($validated['plan_id']) : null;

            // Seed default roles for this salon (Pass plan to ensure correct permissions)
            \App\Services\SalonRoleSeederService::seedDefaultRoles($salon->id, $plan);

            // Assign salon-specific salon_admin role
            $salonAdminRole = \App\Models\Role::where('name', 'salon_admin')
                ->where('salon_id', $salon->id)
                ->first();

            if ($salonAdminRole) {
                $owner->assignRole($salonAdminRole);
            } else {
                $owner->assignRole('salon_admin');
            }

            // Update salon owner_id
            $salon->update(['owner_id' => $owner->id]);

            // Create default branch (Only if plan supports it)
            if ($plan && $plan->hasFeature('Multi-Branch Support')) {
                \App\Models\Branch::create([
                    'salon_id' => $salon->id,
                    'name' => $salon->name . ' Main Branch',
                    'address' => $validated['address'] ?? '',
                    'phone' => $validated['phone'] ?? '',
                    'email' => $validated['email'],
                    'is_active' => true,
                ]);
            }

            // Create subscription if plan selected
            if ($plan) {
                $trialDays = $validated['trial_days'] ?? 0;

                \App\Models\Subscription::create([
                    'salon_id' => $salon->id,
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'trial_ends_at' => $trialDays > 0 ? now()->addDays($trialDays) : null,
                    'starts_at' => now(),
                    'ends_at' => now()->addDays($plan->duration_in_days),
                ]);
            }

            \DB::commit();

            // Log super admin action
            SuperAdminAuditLog::logAction(
                'salon.create',
                $salon->id,
                Salon::class,
                $salon->id,
                ['salon_name' => $salon->name, 'owner_email' => $validated['owner_email']]
            );

            return redirect()->route('admin.salons.show', $salon)
                ->with('success', 'Salon created successfully');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Salon creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create salon: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified salon
     */
    public function show($salon)
    {
        $salon = $this->resolveSalon($salon);
        $salon->load([
            'owner',
            'subscriptions.plan',
            'users' => fn($q) => $q->withoutGlobalScopes(),
            'branches' => fn($q) => $q->withoutGlobalScopes(),
            'activeSubscription.plan'
        ]);

        // 1. Calculate Monthly Revenue (Bookings + POS)
        $monthlyBookingRevenue = $salon->bookings()
            ->withoutGlobalScopes()
            ->whereIn('status', ['completed', 'staff_completed'])
            ->whereMonth('start_time', now()->month)
            ->whereYear('start_time', now()->year)
            ->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('pos_sale_items')
                    ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                    ->where('pos_sales.status', '!=', 'voided');
            })
            ->sum('amount');

        // POS Sales
        $monthlyPosRevenue = \App\Models\PosSale::where('salon_id', $salon->id)
            ->where('status', '!=', 'voided')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum(\DB::raw('total - tip'));

        $monthlyRevenue = $monthlyBookingRevenue + $monthlyPosRevenue;

        if ($monthlyRevenue <= 0) {
            $monthlyRevenue = $salon->bookings()
                ->withoutGlobalScopes()
                ->select('bookings.amount', 'services.price')
                ->leftJoin('services', 'bookings.service_id', '=', 'services.id')
                ->where('bookings.salon_id', $salon->id)
                ->where(function ($q) {
                    $q->where('bookings.payment_status', 'paid')
                        ->orWhere('bookings.amount', '>', 0);
                })
                ->whereNotIn('bookings.status', ['cancelled', 'no_show'])
                ->whereMonth('bookings.created_at', now()->month)
                ->whereYear('bookings.created_at', now()->year)
                ->sum(\DB::raw('CASE WHEN bookings.amount > 0 THEN bookings.amount ELSE COALESCE(services.price, 0) END'));
        }

        // 2. Calculate Total Revenue (Bookings + POS)
        $totalBookingRevenue = $salon->bookings()
            ->withoutGlobalScopes()
            ->whereIn('status', ['completed', 'staff_completed'])
            ->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('pos_sale_items')
                    ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                    ->where('pos_sales.status', '!=', 'voided');
            })
            ->sum('amount');

        $totalPosRevenue = \App\Models\PosSale::where('salon_id', $salon->id)
            ->where('status', '!=', 'voided')
            ->sum(\DB::raw('total - tip'));

        $totalRevenue = $totalBookingRevenue + $totalPosRevenue;

        if ($totalRevenue <= 0) {
            $totalRevenue = $salon->bookings()
                ->withoutGlobalScopes()
                ->select('bookings.amount', 'services.price')
                ->leftJoin('services', 'bookings.service_id', '=', 'services.id')
                ->where('bookings.salon_id', $salon->id)
                ->where(function ($q) {
                    $q->where('bookings.payment_status', 'paid')
                        ->orWhere('bookings.amount', '>', 0);
                })
                ->whereNotIn('bookings.status', ['cancelled', 'no_show'])
                ->sum(\DB::raw('CASE WHEN bookings.amount > 0 THEN bookings.amount ELSE COALESCE(services.price, 0) END'));
        }

        $stats = [
            'total_users' => $salon->users()->withoutGlobalScopes()->count(),
            'total_branches' => $salon->branches()->withoutGlobalScopes()->count(),
            'total_bookings' => $salon->bookings()->withoutGlobalScopes()->count(),
            'total_revenue' => $totalRevenue,
            'total_customers' => $salon->customers()->withoutGlobalScopes()->count(),
            'total_services' => $salon->services()->withoutGlobalScopes()->count(),
            'total_packages' => $salon->packages()->withoutGlobalScopes()->count(),
            'total_memberships' => $salon->memberships()->withoutGlobalScopes()->count(),
            'package_limit' => $salon->getPackageLimit(),
            'membership_limit' => $salon->getMembershipLimit(),
            'monthly_bookings' => $salon->bookings()->withoutGlobalScopes()->whereMonth('start_time', now()->month)->whereYear('start_time', now()->year)->count(),
            'monthly_revenue' => $monthlyRevenue,
        ];

        // Get recent payments
        $payments = $salon->saasPayments()
            ->latest()
            ->take(10)
            ->get();

        // Get branches with some stats
        $branches = $salon->branches()->withoutGlobalScopes()->withCount(['users', 'bookings'])->get();

        return view('super-admin.salons.show', compact('salon', 'stats', 'payments', 'branches'));
    }

    /**
     * Show the form for editing the specified salon
     */
    public function edit($salon)
    {
        $salon = $this->resolveSalon($salon);
        $salon->load(['owner', 'activeSubscription']);
        $plans = \App\Models\Plan::where('is_active', true)->get();
        return view('super-admin.salons.edit', compact('salon', 'plans'));
    }

    /**
     * Update the specified salon
     */
    public function update(Request $request, $salon)
    {
        $salon = $this->resolveSalon($salon);

        $request->merge([
            'phone' => normalize_phone($request->input('phone')),
            'owner_phone' => normalize_phone($request->input('owner_phone')),
            'website' => safe_http_url($request->input('website')),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'business_type' => 'required|string|in:salon,barber,both',
            'email' => 'required|email|max:255|unique:salons,email,' . $salon->id,
            'phone' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('salons', 'phone')->ignore($salon->id),
            ],
            'website' => ['nullable', 'url', 'max:255', 'regex:/^https?:\/\//i'],
            'address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'plan_id' => 'nullable|exists:plans,id',
            'trial_ends_at' => 'nullable|date',
            'mail_driver' => 'nullable|string|max:50',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|max:20',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255|unique:users,email,' . ($salon->owner_id ?? 0),
            'owner_phone' => 'nullable|string|max:50',
            'timezone' => 'required|string|max:255',
            'currency' => 'required|string|max:10',
        ]);

        try {
            \DB::beginTransaction();

            $oldData = $salon->only(['name', 'email', 'is_active', 'phone', 'website', 'address', 'trial_ends_at']);
            $newPlanId = $request->plan_id;
            $currentSubscription = $salon->activeSubscription;
            $data = [
                'name' => $request->name,
                'business_type' => $request->business_type,
                'email' => $request->email,
                'is_active' => $request->has('is_active'),
                'phone' => $request->phone,
                'website' => $request->website,
                'address' => $request->address,
                'trial_ends_at' => $request->trial_ends_at,
                'mail_driver' => $request->mail_driver,
                'mail_host' => $request->mail_host,
                'mail_port' => $request->mail_port,
                'mail_username' => $request->mail_username,
                'mail_encryption' => $request->mail_encryption,
                'mail_from_address' => $request->mail_from_address,
                'mail_from_name' => $request->mail_from_name,
                'timezone' => $request->timezone,
                'currency' => $request->currency,
            ];

            // Handle Logo Upload
            if ($request->hasFile('logo')) {
                if ($salon->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($salon->logo)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($salon->logo);
                }
                $data['logo'] = $request->file('logo')->store('salons/logos', 'public');
            }

            // Handle Mail Password
            if ($request->filled('mail_password')) {
                $data['mail_password'] = $request->mail_password;
            }

            $salon->update($data);

            // Update owner details
            if ($salon->owner) {
                $salon->owner->update([
                    'name' => $request->owner_name,
                    'email' => $request->owner_email,
                    'phone' => $request->owner_phone,
                ]);
            }

            // Handle plan change
            if ($newPlanId && (!$currentSubscription || $currentSubscription->plan_id != $newPlanId)) {
                $newPlan = \App\Models\Plan::find($newPlanId);

                if ($currentSubscription) {
                    $newSubscription = $currentSubscription->upgradeTo($newPlan);
                } else {
                    $newSubscription = \App\Models\Subscription::create([
                        'salon_id' => $salon->id,
                        'plan_id' => $newPlan->id,
                        'status' => 'active',
                        'starts_at' => now(),
                        'ends_at' => now()->addDays($newPlan->duration_in_days),
                    ]);
                }

                \App\Services\SalonRoleSeederService::seedDefaultRoles($salon->id, $newPlan);

                $salon->update([
                    'subscription_status' => 'active',
                    'subscription_id' => $newSubscription->id,
                    'is_active' => true
                ]);

                $salon->refresh();
            }

            \DB::commit();

            // Log super admin action
            SuperAdminAuditLog::logAction(
                'salon.update',
                $salon->id,
                Salon::class,
                $salon->id,
                ['old' => $oldData, 'new' => $salon->getChanges()]
            );

            return redirect()->route('admin.salons.show', $salon)
                ->with('success', 'Salon updated successfully');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Salon update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update salon: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified salon
     */
    public function destroy($salon)
    {
        $salon = $this->resolveSalon($salon);

        // Soft delete or deactivate
        $salon->update(['is_active' => false]);

        // Log super admin action
        SuperAdminAuditLog::logAction(
            'salon.deactivate',
            $salon->id,
            Salon::class,
            $salon->id,
            ['salon_name' => $salon->name]
        );

        return redirect()->route('admin.salons.index')
            ->with('success', 'Salon deactivated successfully');
    }

    /**
     * Impersonate salon owner
     */
    public function impersonate($salon)
    {
        $salon = $this->resolveSalon($salon);
        $salon->load('owner');

        if (!$salon->owner) {
            return back()->with('error', 'This salon does not have an owner assigned.');
        }

        // Store current admin ID in session to allow returning
        session(['impersonator_id' => auth()->id()]);
        session(['impersonator_name' => auth()->user()->name]);

        // Login as salon owner and regenerate session to prevent session fixation
        \Illuminate\Support\Facades\Auth::login($salon->owner);
        session()->regenerate();

        return redirect()->route('home.dashboard')
            ->with('success', 'You are now logged in as ' . e($salon->owner->name) . ' (Owner of ' . e($salon->name) . ')');
    }
}
