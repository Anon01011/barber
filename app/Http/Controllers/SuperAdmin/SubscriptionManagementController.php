<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Salon;
use App\Models\Plan;
use App\Models\SaasPayment;
use Illuminate\Http\Request;

class SubscriptionManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['salon', 'plan']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->latest()->paginate(20);

        return view('super-admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new subscription
     */
    public function create(Request $request)
    {
        $salons = Salon::where('is_active', true)->get();
        $plans = Plan::where('is_active', true)->get();

        // If salon_id is provided in query string, pre-select it
        $selectedSalonId = $request->query('salon_id');

        return view('super-admin.subscriptions.create', compact('salons', 'plans', 'selectedSalonId'));
    }

    /**
     * Store a newly created subscription
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'salon_id' => 'required|exists:salons,id',
            'plan_id' => 'required|exists:plans,id',
            'status' => 'required|in:active,cancelled,expired',
            'trial_days' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
        ]);

        $plan = Plan::find($validated['plan_id']);
        $trialDays = $validated['trial_days'] ?? $plan->trial_days ?? 0;
        $startsAt = $validated['starts_at'] ?? now();

        $subscription = Subscription::create([
            'salon_id' => $validated['salon_id'],
            'plan_id' => $validated['plan_id'],
            'status' => $validated['status'],
            'trial_ends_at' => $trialDays > 0 ? now()->addDays($trialDays) : null,
            'starts_at' => $startsAt,
            'ends_at' => \Carbon\Carbon::parse($startsAt)->addDays($plan->duration_in_days),
        ]);

        // Create payment record if subscription is active
        if ($validated['status'] === 'active') {
            SaasPayment::create([
                'salon_id' => $validated['salon_id'],
                'subscription_id' => $subscription->id,
                'amount' => $plan->price,
                'currency' => system_currency(),
                'payment_method' => 'manual',
                'transaction_id' => 'ADMIN-SUB-' . uniqid(),
                'status' => 'completed',
                'paid_at' => now(),
            ]);
        }

        return redirect()->route('admin.subscriptions.show', $subscription->id)
            ->with('success', 'Subscription created successfully');
    }

    /**
     * Extend subscription
     */
    public function extend(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        $validated = $request->validate([
            'days' => 'required|integer|min:1',
        ]);

        $subscription->update([
            'ends_at' => $subscription->ends_at->addDays($validated['days']),
        ]);

        return back()->with('success', "Subscription extended by {$validated['days']} days");
    }

    public function show($id)
    {
        $subscription = Subscription::with(['salon', 'plan'])->findOrFail($id);
        return view('super-admin.subscriptions.show', compact('subscription'));
    }

    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        $request->validate([
            'status' => 'required|in:active,cancelled,expired,paused',
            'ends_at' => 'nullable|date',
        ]);

        $subscription->update($request->only(['status', 'ends_at']));

        // Clear permission cache if status changed
        if ($request->has('status')) {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }

        return back()->with('success', 'Subscription updated successfully');
    }

    /**
     * Cancel a subscription
     */
    public function cancel(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        if ($subscription->status === 'cancelled') {
            return back()->with('error', 'Subscription is already cancelled.');
        }

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        try {
            \DB::beginTransaction();

            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Log cancellation reason if provided
            if ($request->cancellation_reason) {
                \App\Models\SuperAdminAuditLog::logAction(
                    'subscription.cancel',
                    auth()->id(),
                    Subscription::class,
                    $subscription->id,
                    [
                        'reason' => $request->cancellation_reason,
                        'salon_id' => $subscription->salon_id
                    ]
                );
            }

            \DB::commit();

            return back()->with('success', 'Subscription cancelled successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Subscription cancellation failed', [
                'subscription_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    /**
     * Pause a subscription
     */
    public function pause($id)
    {
        $subscription = Subscription::findOrFail($id);

        if ($subscription->status !== 'active') {
            return back()->with('error', 'Only active subscriptions can be paused.');
        }

        try {
            \DB::beginTransaction();

            $subscription->update([
                'status' => 'paused',
                'paused_at' => now(),
            ]);

            // Update salon status
            $subscription->salon->update([
                'subscription_status' => 'paused'
            ]);

            \App\Models\SuperAdminAuditLog::logAction(
                'subscription.pause',
                auth()->id(),
                Subscription::class,
                $subscription->id,
                ['salon_id' => $subscription->salon_id]
            );

            \DB::commit();

            return back()->with('success', 'Subscription paused successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Subscription pause failed', [
                'subscription_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to pause subscription: ' . $e->getMessage());
        }
    }

    /**
     * Resume a paused subscription
     */
    public function resume($id)
    {
        $subscription = Subscription::findOrFail($id);

        if ($subscription->status !== 'paused') {
            return back()->with('error', 'Only paused subscriptions can be resumed.');
        }

        try {
            \DB::beginTransaction();

            // Calculate paused duration
            $pausedAt = $subscription->paused_at ?? $subscription->updated_at;
            $pausedDuration = now()->diffInDays($pausedAt);

            // Extend ends_at by the paused duration
            $newEndsAt = $subscription->ends_at->addDays($pausedDuration);

            $subscription->update([
                'status' => 'active',
                'paused_at' => null,
                'ends_at' => $newEndsAt,
            ]);

            // Update salon status
            $subscription->salon->update([
                'subscription_status' => 'active',
                'is_active' => true
            ]);

            \App\Models\SuperAdminAuditLog::logAction(
                'subscription.resume',
                auth()->id(),
                Subscription::class,
                $subscription->id,
                [
                    'salon_id' => $subscription->salon_id,
                    'paused_duration_days' => $pausedDuration
                ]
            );

            \DB::commit();

            return back()->with('success', "Subscription resumed successfully. Extended by {$pausedDuration} days.");

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Subscription resume failed', [
                'subscription_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to resume subscription: ' . $e->getMessage());
        }
    }

    /**
     * Approve a pending subscription (manual payment)
     */
    public function approve($id)
    {
        $subscription = Subscription::with(['salon', 'plan'])->findOrFail($id);

        if ($subscription->status !== 'pending') {
            return back()->with('error', 'Only pending subscriptions can be approved.');
        }

        try {
            \DB::beginTransaction();

            // Update subscription status
            $subscription->update(['status' => 'active']);

            // Activate the salon
            $subscription->salon->update([
                'is_active' => true,
                'subscription_status' => 'active',
                'subscription_id' => $subscription->id
            ]);

            // Sync permissions for the new subscription
            \App\Services\SalonRoleSeederService::seedDefaultRoles($subscription->salon_id, $subscription->plan);

            // Clear cache to ensure new permissions are effective immediately
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            // Create payment record for manual payment
            // Check for existing pending payment
            $pendingPayment = SaasPayment::where('subscription_id', $subscription->id)
                ->where('status', 'pending')
                ->first();

            if ($pendingPayment) {
                $pendingPayment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                    'notes' => 'Manually approved by admin'
                ]);
            } else {
                // Create payment record for manual payment
                SaasPayment::create([
                    'salon_id' => $subscription->salon_id,
                    'subscription_id' => $subscription->id,
                    'amount' => $subscription->plan->price,
                    'currency' => system_currency(),
                    'payment_method' => 'manual',
                    'transaction_id' => 'MANUAL-APPROVED-' . uniqid(),
                    'status' => 'completed',
                    'paid_at' => now(),
                    'notes' => 'Manually approved by admin'
                ]);
            }

            \DB::commit();

            // Send approval email
            try {
                $owner = $subscription->salon->owner;
                if ($owner && $owner->email) {
                    \Mail::to($owner->email)->send(new \App\Mail\SubscriptionApproved($subscription->salon, $subscription->plan));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send approval email: ' . $e->getMessage());
            }

            return back()->with('success', 'Subscription approved successfully. The salon is now active.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Failed to approve subscription: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve subscription: ' . $e->getMessage());
        }
    }


}
