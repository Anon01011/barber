<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SaasPayment;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\Salon;
use App\Models\SuperAdminAuditLog;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = SaasPayment::with(['salon', 'subscription.plan']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by salon
        if ($request->filled('salon_id')) {
            $query->where('salon_id', $request->salon_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by transaction ID
        if ($request->filled('search')) {
            $query->where('transaction_id', 'like', '%' . $request->search . '%');
        }

        $payments = $query->latest()->paginate(20);

        $stats = [
            'total_revenue' => SaasPayment::where('status', 'completed')->where('amount', '>', 0)->sum('amount'),
            'pending_payments' => SaasPayment::where('status', 'pending')->count(),
            'pending_amount' => SaasPayment::where('status', 'pending')->sum('amount'),
            'total_payments' => SaasPayment::count(),
        ];

        // Get salons for filter dropdown
        $salons = \App\Models\Salon::select('id', 'name')->orderBy('name')->get();

        return view('super-admin.payments.index', compact('payments', 'stats', 'salons'));
    }

    public function show($id)
    {
        $payment = SaasPayment::with(['salon', 'subscription'])->findOrFail($id);
        return view('super-admin.payments.show', compact('payment'));
    }

    /**
     * Approve a pending payment
     */
    public function approve($id)
    {
        $payment = SaasPayment::findOrFail($id);

        if ($payment->status !== 'pending') {
            return back()->with('error', 'Only pending payments can be approved.');
        }

        try {
            \DB::beginTransaction();

            $salon = $payment->salon;
            $metadata = $payment->metadata ?? [];
            $type = $metadata['type'] ?? 'unknown';

            if ($type === 'upgrade') {
                $newPlanId = $metadata['new_plan_id'] ?? null;
                $proration = $metadata['proration'] ?? null;

                if ($newPlanId) {
                    $newPlan = Plan::find($newPlanId);
                    if ($newPlan) {
                        $currentSub = $payment->subscription ?? $salon->subscription;

                        if ($currentSub) {
                            $newSubscription = $currentSub->upgradeTo($newPlan, $proration);
                        } else {
                            // Create new if none exists
                            $newSubscription = Subscription::create([
                                'salon_id' => $salon->id,
                                'plan_id' => $newPlan->id,
                                'starts_at' => now(),
                                'ends_at' => now()->addDays($newPlan->duration_in_days),
                                'status' => 'active',
                            ]);
                        }

                        // Sync permissions for the new plan
                        \App\Services\SalonRoleSeederService::seedDefaultRoles($salon->id, $newPlan);

                        $salon->update([
                            'subscription_status' => 'active',
                            'is_active' => true,
                            'subscription_id' => $newSubscription->id
                        ]);

                        $salon->refresh();
                    }
                }
            } elseif ($type === 'renewal') {
                $salon->subscription->renew();

                // Sync permissions just in case plan features changed
                \App\Services\SalonRoleSeederService::seedDefaultRoles($salon->id, $salon->subscription->plan);

                // Update salon status
                $salon->update([
                    'subscription_status' => 'active',
                    'is_active' => true
                ]);
            }

            // Mark payment as completed
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'notes' => 'Manually approved by admin'
            ]);

            \DB::commit();

            // Send approval email
            try {
                $owner = $salon->owner;
                if ($owner && $owner->email) {
                    // Use optional chaining or check if subscription exists before accessing plan
                    $plan = $salon->subscription ? $salon->subscription->plan : null;
                    if ($plan) {
                        \Mail::to($owner->email)->send(new \App\Mail\SubscriptionApproved($salon, $plan));
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send payment approval email: ' . $e->getMessage());
            }

            return back()->with('success', 'Payment approved and ' . $type . ' processed successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Failed to approve payment: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve payment: ' . $e->getMessage());
        }
    }

    /**
     * Reject a pending payment
     */
    public function reject(Request $request, $id)
    {
        $payment = SaasPayment::findOrFail($id);

        if ($payment->status !== 'pending') {
            return back()->with('error', 'Only pending payments can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            \DB::beginTransaction();

            // Update payment status
            $payment->update([
                'status' => 'failed',
                'notes' => 'Rejected: ' . $request->rejection_reason,
                'processed_at' => now(),
            ]);

            // If payment has a subscription, mark it as cancelled
            if ($payment->subscription_id) {
                $subscription = $payment->subscription;
                if ($subscription->status === 'pending') {
                    $subscription->update(['status' => 'cancelled']);
                }
            }

            \DB::commit();

            // Log action
            \App\Models\SuperAdminAuditLog::logAction(
                'payment.reject',
                auth()->id(),
                \App\Models\SaasPayment::class,
                $payment->id,
                ['reason' => $request->rejection_reason, 'salon_id' => $payment->salon_id]
            );

            return back()->with('success', 'Payment rejected successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Payment rejection failed', [
                'payment_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to reject payment: ' . $e->getMessage());
        }
    }

    /**
     * Process a refund
     */
    public function refund(Request $request, $id)
    {
        $payment = SaasPayment::findOrFail($id);

        if ($payment->status !== 'completed') {
            return back()->with('error', 'Only completed payments can be refunded.');
        }

        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'refund_reason' => 'required|string|max:500',
        ]);

        try {
            \DB::beginTransaction();

            // Create refund payment record
            $refund = SaasPayment::create([
                'salon_id' => $payment->salon_id,
                'subscription_id' => $payment->subscription_id,
                'amount' => -$request->refund_amount,
                'payment_method' => $payment->payment_method,
                'status' => 'completed',
                'type' => 'refund',
                'notes' => 'Refund for payment #' . $payment->id . ': ' . $request->refund_reason,
                'processed_at' => now(),
            ]);

            // Update original payment
            $payment->update([
                'notes' => ($payment->notes ?? '') . "\nRefunded: $" . $request->refund_amount . ' on ' . now()->format('Y-m-d'),
            ]);

            \DB::commit();

            // Log action
            \App\Models\SuperAdminAuditLog::logAction(
                'payment.refund',
                auth()->id(),
                \App\Models\SaasPayment::class,
                $payment->id,
                [
                    'refund_amount' => $request->refund_amount,
                    'reason' => $request->refund_reason,
                    'salon_id' => $payment->salon_id
                ]
            );

            return back()->with('success', 'Refund processed successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Refund processing failed', [
                'payment_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to process refund: ' . $e->getMessage());
        }
    }
}
