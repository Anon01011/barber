<?php

namespace App\Http\Controllers\SaaS;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SaasPayment;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewSalonRegistrationMail;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $plans = Plan::active()->ordered()->get();
        $settings = app(\App\Services\SettingsService::class);
        $appName = $settings->get('app_name', config('app.name'));

        return view('saas.register', compact('plans', 'settings', 'appName'));
    }

    public function register(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('=== REGISTRATION STARTED ===', [
            'request_data' => $request->except(['password', 'password_confirmation'])
        ]);
        // NOTE: Do NOT log full request data with passwords (OWASP A09 / VAPT Finding)

        // Normalize timezone aliases
        if ($request->timezone === 'Asia/Calcutta') {
            $request->merge(['timezone' => 'Asia/Kolkata']);
        }

        if ($request->filled('phone')) {
            $phoneVal = $request->input('phone');
            $request->merge([
                'phone' => function_exists('normalize_phone') ? \normalize_phone($phoneVal) : preg_replace('/[^\d+]/', '', $phoneVal),
            ]);
        }

        $request->validate([
            'salon_name' => 'required|string|max:255',
            'salon_slug' => 'required|string|max:255|unique:salons,slug',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'different:email',
                \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers()
            ],
            'business_type' => 'required|string|in:salon,barber,both',
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'nullable|string|in:online,manual',
            'timezone' => 'required|timezone',
            'currency' => 'required|string|max:10',
        ], [
            'password.different' => 'Your password cannot be the same as your email address for security reasons.',
        ]);

        // Check if plan is trial to enforce payment method requirement
        $plan = Plan::find($request->plan_id);
        
        // Validate plan compatibility with chosen business type
        if ($plan && $plan->business_type !== 'both' && $plan->business_type !== $request->business_type) {
            return back()->with('error', 'The selected plan is not compatible with your selected business type. Please choose a plan that matches your business type.')->withInput();
        }

        $isTrial = $plan && $plan->trial_days > 0;

        if (!$isTrial) {
            // For paid plans, payment method is required
            $request->validate([
                'payment_method' => 'required|string|in:online,manual',
            ]);

            // Validate that the selected payment method is enabled
            $settingsService = app(\App\Services\SettingsService::class);
            $paymentMethod = $request->payment_method;

            // Check if at least one payment method is available
            $onlineEnabled = $settingsService->get('enable_online_payment', true);
            $manualEnabled = $settingsService->get('enable_manual_payment', true);

            if (!$onlineEnabled && !$manualEnabled) {
                return back()->with('error', 'No payment methods are currently available. Please contact support.')->withInput();
            }

            if ($paymentMethod === 'online' && !$onlineEnabled) {
                return back()->with('error', 'Online payment is currently disabled. Please select another payment method.')->withInput();
            }

            if ($paymentMethod === 'manual' && !$manualEnabled) {
                return back()->with('error', 'Manual payment is currently disabled. Please select another payment method.')->withInput();
            }
        }

        \Illuminate\Support\Facades\Log::info('Validation passed');

        try {
            DB::beginTransaction();
            \Illuminate\Support\Facades\Log::info('Transaction started');

            // 1. Create Salon
            \Illuminate\Support\Facades\Log::info('Creating salon...', [
                'name' => $request->salon_name,
                'slug' => Str::slug($request->salon_slug)
            ]);

            // Determine payment method and trial status
            $isTrial = $plan->trial_days > 0;
            $paymentMethod = $isTrial ? null : $request->payment_method;
            $isManualPayment = $paymentMethod === 'manual';
            $trialDays = $plan->trial_days ?? 0;

            // Salon is only active if it's a trial.
            // Online payments require payment first. Manual payments require admin approval.
            $salonIsActive = ($isTrial && $trialDays > 0);

            \Illuminate\Support\Facades\Log::info('Creating Salon with localization:', [
                'timezone' => $request->timezone,
                'currency' => $request->currency,
            ]);

            $salon = Salon::create([
                'name' => $request->salon_name,
                'business_type' => $request->business_type,
                'slug' => Str::slug($request->salon_slug),
                'email' => $request->email,
                'phone' => $request->phone ?? '',
                'address' => $request->address ?? '', // Default empty address
                'timezone' => $request->timezone,
                'currency' => $request->currency,
                'is_active' => $salonIsActive,
                'trial_ends_at' => $trialDays > 0 ? now()->addDays($trialDays) : null,
            ]);

            \Illuminate\Support\Facades\Log::info('Salon object after creation:', $salon->toArray());

            // Sync localization settings to the settings table
            $settingsService = app(\App\Services\SettingsService::class);
            $settingsService->set('timezone', $request->timezone, $salon->id);
            $settingsService->set('currency_code', $request->currency, $salon->id);

            // Map currency code to symbol (basic mapping for now)
            $currencySymbols = [
                'USD' => '$',
                'EUR' => '€',
                'GBP' => '£',
                'INR' => '₹',
                'AED' => 'د.إ',
                'QAR' => 'QR',
                'SAR' => '﷼',
                'CAD' => '$',
                'AUD' => '$',
                'SGD' => '$',
                'JPY' => '¥',
                'HKD' => '$',
                'CHF' => 'CHF',
                'PKR' => '₨',
                'BDT' => '৳',
                'LKR' => 'Rs',
                'ZAR' => 'R'
            ];
            $currencySymbol = $currencySymbols[$request->currency] ?? '$';
            $settingsService->set('currency_symbol', $currencySymbol, $salon->id);

            \Illuminate\Support\Facades\Log::info('Salon created and settings synced', [
                'salon_id' => $salon->id,
                'settings' => \App\Models\Setting::where('salon_id', $salon->id)->get()->toArray()
            ]);

            // 2. Create Owner User
            \Illuminate\Support\Facades\Log::info('Creating user...', [
                'name' => $request->name,
                'email' => $request->email,
                'salon_id' => $salon->id
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone ?? '',
                'password' => Hash::make($request->password),
                'salon_id' => $salon->id,
                'status' => 'active',
            ]);

            \Illuminate\Support\Facades\Log::info('User created', ['user_id' => $user->id]);

            // Link owner to salon first so activeSubscription can resolve correctly later
            $salon->update(['owner_id' => $user->id]);

            \Illuminate\Support\Facades\Log::info('Owner linked to salon.');

            // 3. Create Default Branch (Only if plan supports it)
            $hasBranchFeature = $plan->hasFeature('Multi-Branch Support') ||
                $plan->hasFeature('multi_branch') ||
                $plan->hasFeature('branches');

            \Illuminate\Support\Facades\Log::info('Branch feature check', [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'has_branch_feature' => $hasBranchFeature,
                'plan_features' => $plan->features,
                'max_branches' => $plan->max_branches
            ]);

            if ($hasBranchFeature) {
                \Illuminate\Support\Facades\Log::info('Creating default branch (Plan supports multi-branch)...');
                \App\Models\Branch::create([
                    'salon_id' => $salon->id,
                    'name' => 'Main Branch',
                    'address' => $request->address ?? '',
                    'phone' => $request->phone ?? '',
                    'email' => $request->email,
                    'is_active' => true,
                ]);
                \Illuminate\Support\Facades\Log::info('Default branch created');
            } else {
                \Illuminate\Support\Facades\Log::info('Skipping default branch creation (Plan does not support multi-branch)');
            }

            // 4. Create Subscription
            \Illuminate\Support\Facades\Log::info('Creating subscription...', [
                'plan_id' => $plan->id,
                'salon_id' => $salon->id,
                'payment_method' => $paymentMethod,
                'is_trial' => $isTrial
            ]);

            // If trial, status is active. Online and Manual are pending until paid/approved.
            $subscriptionStatus = ($isTrial && $trialDays > 0) ? 'active' : 'pending';

            // Calculate subscription end date
            // For trial plans: ends when trial ends. Full duration added only after payment.
            // For paid plans: ends now (inactive) until payment is confirmed.
            if ($isTrial && $trialDays > 0) {
                $endsAt = now()->addDays($trialDays);
                $trialEndsAt = now()->addDays($trialDays);
            } else {
                $endsAt = now(); // Will be updated upon payment/approval
                $trialEndsAt = null;
            }

            $subscription = Subscription::create([
                'salon_id' => $salon->id,
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => $endsAt,
                'trial_ends_at' => $trialEndsAt,
                'status' => $subscriptionStatus,
                'payment_method' => $paymentMethod,
            ]);

            // Update salon with subscription details
            $salon->update([
                'subscription_id' => $subscription->id,
                'subscription_status' => $subscriptionStatus,
            ]);

            \Illuminate\Support\Facades\Log::info('Subscription created', ['status' => $subscriptionStatus]);

            // ─── Seed default roles & permissions AFTER subscription is committed ────
            // This ensures plan->hasFeature() works and all 100+ permissions are synced.
            \Illuminate\Support\Facades\Log::info('Seeding default roles for salon...');
            $salon->refresh(); // reload with subscription_id
            \App\Services\SalonRoleSeederService::seedDefaultRoles($salon->id, $plan);
            \Illuminate\Support\Facades\Log::info('Default roles seeded');

            // Assign salon-specific salon_admin role to the owner
            \Illuminate\Support\Facades\Log::info('Assigning salon_admin role...');
            $salonAdminRole = \App\Models\Role::withoutGlobalScopes()
                ->where('name', 'salon_admin')
                ->where('salon_id', $salon->id)
                ->first();

            if ($salonAdminRole) {
                $user->assignRole($salonAdminRole);
                \Illuminate\Support\Facades\Log::info('Salon Admin role assigned to user ' . $user->id);
            } else {
                throw new \Exception('Failed to find salon_admin role for salon ' . $salon->id);
            }
            // ─────────────────────────────────────────────────────────────────────────

            // Create pending payment record for manual payments
            if ($isManualPayment) {
                $settingsService = app(\App\Services\SettingsService::class);
                SaasPayment::create([
                    'salon_id' => $salon->id,
                    'subscription_id' => $subscription->id,
                    'amount' => $plan->price,
                    'currency' => $request->currency ?? $settingsService->get('currency', 'USD'),
                    'payment_method' => 'manual',
                    'transaction_id' => 'MANUAL-' . strtoupper(Str::random(10)),
                    'status' => 'pending',
                    'metadata' => ['registration' => true],
                ]);
                \Illuminate\Support\Facades\Log::info('Pending payment record created for manual registration');

                // Notify Admin
                try {
                    $adminEmail = $settingsService->get('admin_email', config('mail.from.address'));
                    Mail::to($adminEmail)->send(new NewSalonRegistrationMail($salon));
                    \Illuminate\Support\Facades\Log::info('Admin notification sent for new registration');
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send admin notification: ' . $e->getMessage());
                }
            }

            // Final persistence check for localization
            $salon->timezone = $request->timezone;
            $salon->currency = $request->currency;
            $salon->save();

            DB::commit();
            \Illuminate\Support\Facades\Log::info('Transaction committed');

            // Login the user with "remember" set to true for persistence
            \Illuminate\Support\Facades\Log::info('Logging in user...');
            \Illuminate\Support\Facades\Auth::login($user, true);
            \Illuminate\Support\Facades\Log::info('User logged in', ['user_id' => $user->id, 'session_id' => session()->getId()]);

            // Save session explicitly to ensure persistence across redirect
            $request->session()->save();

            // Send Welcome Email (Non-blocking)
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\SubscriptionWelcome($salon, $plan));
                \Illuminate\Support\Facades\Log::info('Welcome email sent');
            } catch (\Exception $e) {
                // Log error but DO NOT fail the request
                \Illuminate\Support\Facades\Log::error('Failed to send welcome email: ' . $e->getMessage());
            }

            // Redirect based on payment method
            if ($isTrial) {
                $redirectUrl = route('dashboard', ['salon_slug' => $salon->slug]);
                \Illuminate\Support\Facades\Log::info('Redirecting to dashboard', ['url' => $redirectUrl]);

                return redirect()->route('dashboard', ['salon_slug' => $salon->slug])
                    ->with('success', 'Registration successful! Your trial period has started.');
            } elseif ($isManualPayment) {
                return redirect()->route('saas.pending-approval')
                    ->with('success', 'Registration successful! Your account is pending approval.');
            } else {
                // For online payment, redirect to payment gateway
                return redirect()->route('saas.payment', ['salon_id' => $salon->id]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Salon Registration Failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['password', 'password_confirmation'])
            ]);
            return back()->with('error', 'Registration failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show pending approval page for salons awaiting manual payment approval
     */
    public function pendingApproval()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $salon = $user->salon;

        if (!$salon) {
            return redirect()->route('login')->with('error', 'No salon found for your account.');
        }

        // Get the subscription
        $subscription = $salon->subscriptions()->latest()->first();

        // If subscription is already active, redirect to dashboard
        if ($subscription && $subscription->status === 'active' && $salon->is_active) {
            return redirect()->route('dashboard', ['salon_slug' => $salon->slug])
                ->with('success', 'Your salon has been approved! Welcome aboard.');
        }

        // If subscription is rejected or expired, show appropriate message
        if ($subscription && in_array($subscription->status, ['cancelled', 'expired'])) {
            return view('saas.pending-approval', [
                'salon' => $salon,
                'subscription' => $subscription,
                'status' => 'rejected'
            ]);
        }

        // Show pending approval page
        return view('saas.pending-approval', [
            'salon' => $salon,
            'subscription' => $subscription,
            'status' => 'pending'
        ]);
    }
}
