<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SaasPayment;
use App\Models\Subscription;
use App\Models\Salon;
use App\Models\Plan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show reports dashboard
     */
    public function index()
    {
        // Executive Summary Data
        $totalRevenue = SaasPayment::where('status', 'completed')->sum('amount');

        $thisMonthRevenue = SaasPayment::where('status', 'completed')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $lastMonthRevenue = SaasPayment::where('status', 'completed')
            ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('amount');

        // Calculate growth
        $revenueGrowth = 0;
        if ($lastMonthRevenue > 0) {
            $revenueGrowth = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        } elseif ($thisMonthRevenue > 0) {
            $revenueGrowth = 100;
        }

        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $totalSalons = Salon::count();

        return view('super-admin.reports.index', compact(
            'totalRevenue',
            'thisMonthRevenue',
            'revenueGrowth',
            'activeSubscriptions',
            'totalSalons'
        ));
    }

    /**
     * Revenue report
     */
    public function revenue(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'salon_id' => 'nullable|exists:salons,id',
        ]);

        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : now()->subMonths(6);
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : now();

        $query = SaasPayment::where('status', 'completed')
            ->where('amount', '>', 0)
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($request->salon_id) {
            $query->where('salon_id', $request->salon_id);
        }

        // Get payments
        $payments = $query->with(['salon', 'subscription.plan'])->get();

        // Calculate statistics
        $stats = [
            'total_revenue' => $payments->sum('amount'),
            'total_transactions' => $payments->count(),
            'average_transaction' => $payments->count() > 0 ? $payments->avg('amount') : 0,
            'payment_methods' => $payments->groupBy('payment_method')->map->count(),
        ];

        // Monthly breakdown
        $monthlyRevenue = $payments->groupBy(function ($payment) {
            return $payment->created_at->format('Y-m');
        })->map(function ($group) {
            return [
                'revenue' => $group->sum('amount'),
                'count' => $group->count(),
            ];
        })->sortKeys();

        // Plan breakdown
        $planRevenue = $payments->groupBy(function ($payment) {
            return $payment->subscription->plan->name ?? 'N/A';
        })->map(function ($group) {
            return [
                'revenue' => $group->sum('amount'),
                'count' => $group->count(),
            ];
        })->sortByDesc('revenue');

        $salons = Salon::select('id', 'name')->orderBy('name')->get();

        return view('super-admin.reports.revenue', compact(
            'stats',
            'monthlyRevenue',
            'planRevenue',
            'payments',
            'dateFrom',
            'dateTo',
            'salons'
        ));
    }

    /**
     * Subscription report
     */
    public function subscriptions(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|in:active,cancelled,expired,paused,pending',
        ]);

        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : now()->subMonths(6);
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : now();

        $query = Subscription::whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->with(['salon', 'plan'])->get();

        // Statistics
        $stats = [
            'total_subscriptions' => $subscriptions->count(),
            'active' => Subscription::where('status', 'active')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
            'paused' => Subscription::where('status', 'paused')->count(),
            'pending' => Subscription::where('status', 'pending')->count(),
        ];

        // Monthly new subscriptions
        $monthlySubscriptions = $subscriptions->groupBy(function ($sub) {
            return $sub->created_at->format('Y-m');
        })->map->count()->sortKeys();

        // Plan distribution
        $planDistribution = $subscriptions->groupBy(function ($sub) {
            return $sub->plan->name;
        })->map->count()->sortByDesc(function ($count) {
            return $count;
        });

        // Churn analysis (last 6 months)
        $churnData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');

            $activeStart = Subscription::where('status', 'active')
                ->where('created_at', '<', $month->startOfMonth())
                ->count();

            $cancelled = Subscription::where('status', 'cancelled')
                ->whereYear('updated_at', $month->year)
                ->whereMonth('updated_at', $month->month)
                ->count();

            $churnRate = $activeStart > 0 ? ($cancelled / $activeStart) * 100 : 0;

            $churnData[$monthKey] = round($churnRate, 2);
        }

        return view('super-admin.reports.subscriptions', compact(
            'stats',
            'monthlySubscriptions',
            'planDistribution',
            'churnData',
            'subscriptions',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Export revenue report to CSV
     */
    /**
     * Export revenue report to CSV
     */
    public function exportRevenue(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : now()->subMonths(6);
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : now();

        $filename = 'revenue_report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Payment ID',
                'Date',
                'Time',
                'Salon Name',
                'Salon Email',
                'Owner Name',
                'Plan Name',
                'Plan Price',
                'Plan Term (Days)',
                'Amount Paid',
                'Payment Method',
                'Transaction ID',
                'Status',
                'Notes'
            ]);

            // Use cursor for memory efficiency
            $payments = SaasPayment::where('status', 'completed')
                ->where('amount', '>', 0)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->with(['salon.owner', 'subscription.plan'])
                ->cursor();

            // Data rows
            foreach ($payments as $payment) {
                $salon = $payment->salon;
                $plan = $payment->subscription->plan ?? null;
                $owner = $salon ? $salon->owner : null;

                fputcsv($file, [
                    $payment->id,
                    $payment->created_at->format('Y-m-d'),
                    $payment->created_at->format('H:i:s'),
                    $salon->name ?? 'N/A',
                    $salon->email ?? 'N/A',
                    $owner->name ?? 'N/A',
                    $plan->name ?? 'N/A',
                    $plan ? number_format($plan->price, 2) : 'N/A',
                    $plan->duration_in_days ?? 'N/A',
                    number_format($payment->amount, 2),
                    $payment->payment_method ?? 'N/A',
                    $payment->transaction_id ?? 'N/A',
                    $payment->status,
                    $payment->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export subscription report to CSV
     */
    /**
     * Export subscription report to CSV
     */
    public function exportSubscriptions(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : now()->subMonths(6);
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : now();

        $filename = 'subscriptions_report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Subscription ID',
                'Salon Name',
                'Salon Email',
                'Owner Name',
                'Plan Name',
                'Plan Price',
                'Billing Cycle (Days)',
                'Status',
                'Start Date',
                'End Date',
                'Trial Ends At',
                'Cancelled At',
                'Created At'
            ]);

            // Use cursor for memory efficiency
            $subscriptions = Subscription::whereBetween('created_at', [$dateFrom, $dateTo])
                ->with(['salon.owner', 'plan'])
                ->cursor();

            // Data rows
            foreach ($subscriptions as $subscription) {
                $salon = $subscription->salon;
                $owner = $salon ? $salon->owner : null;
                $plan = $subscription->plan;

                fputcsv($file, [
                    $subscription->id,
                    $salon->name ?? 'N/A',
                    $salon->email ?? 'N/A',
                    $owner->name ?? 'N/A',
                    $plan->name ?? 'N/A',
                    $plan ? number_format($plan->price, 2) : 'N/A',
                    $plan->duration_in_days ?? 'N/A',
                    $subscription->status,
                    $subscription->starts_at ? $subscription->starts_at->format('Y-m-d') : 'N/A',
                    $subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : 'N/A',
                    $subscription->trial_ends_at ? $subscription->trial_ends_at->format('Y-m-d') : 'N/A',
                    $subscription->ends_at && $subscription->status == 'cancelled' ? $subscription->ends_at->format('Y-m-d') : '',
                    $subscription->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
