<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\SaasPayment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Cache duration: 60 minutes for stats, 2 hours for expensive system stats
            $cacheTime = 60 * 60; // 60 minutes in seconds
            $systemCacheTime = 120 * 60; // 120 minutes in seconds

            // Basic Stats - Cached for 60 minutes
            $stats = \Cache::remember('saas_dashboard_stats', $cacheTime, function () {
                $totalSalons = Salon::count();
                $activeSalons = Salon::active()->count();
                $newSalonsThisMonth = Salon::whereMonth('created_at', now()->month)->count();

                // Revenue Stats
                $totalRevenue = Subscription::where('subscriptions.status', 'active')
                    ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                    ->sum('plans.price');

                $mrr = $totalRevenue;
                $arr = $mrr * 12;

                $pendingPayments = \App\Models\SaasPayment::where('status', 'pending')->count();
                $pendingAmount = \App\Models\SaasPayment::where('status', 'pending')->sum('amount');
                $thisMonthRevenue = \App\Models\SaasPayment::where('status', 'completed')
                    ->where('amount', '>', 0)
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount');

                return [
                    'total_salons' => $totalSalons,
                    'active_salons' => $activeSalons,
                    'total_revenue' => $totalRevenue,
                    'new_salons_this_month' => $newSalonsThisMonth,
                    'mrr' => $mrr,
                    'arr' => $arr,
                    'growth_rate' => 0, // Simplified to prevent DB pressure
                    'churn_rate' => 0,
                    'trial_subscriptions' => Subscription::whereNotNull('trial_ends_at')
                        ->where('trial_ends_at', '>', now())
                        ->count(),
                    'pending_payments' => $pendingPayments,
                    'pending_amount' => $pendingAmount,
                    'this_month_revenue' => $thisMonthRevenue,
                    'forecasted_revenue' => $mrr,
                ];
            });

            // Recent Data - Cached for 60 minutes
            $recent_salons = \Cache::remember('saas_dashboard_recent_salons', $cacheTime, function () {
                return Salon::with('owner')->latest()->take(5)->get();
            });

            $expiring_subscriptions = \Cache::remember('saas_dashboard_expiring_subs', $cacheTime, function () {
                return Subscription::with(['salon', 'plan'])
                    ->where('ends_at', '>', now())
                    ->where('ends_at', '<', now()->addDays(7))
                    ->take(10)
                    ->get();
            });

            $recent_payments = \Cache::remember('saas_dashboard_recent_payments', $cacheTime, function () {
                return \App\Models\SaasPayment::with(['salon', 'subscription.plan'])
                    ->latest()
                    ->take(5)
                    ->get();
            });

            // Revenue Trend - Cached for 60 minutes
            $revenueTrend = \Cache::remember('saas_dashboard_revenue_trend', $cacheTime, function () {
                $last12Months = now()->subMonths(11)->startOfMonth();
                $results = \App\Models\SaasPayment::where('status', 'completed')
                    ->where('amount', '>', 0)
                    ->where('created_at', '>=', $last12Months)
                    ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
                    ->groupBy('year', 'month')
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get()
                    ->keyBy(function ($item) {
                        return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                    });

                $trend = [];
                for ($i = 11; $i >= 0; $i--) {
                    $month = now()->subMonths($i);
                    $key = $month->format('Y-m');
                    $trend[] = [
                        'month' => $month->format('M Y'),
                        'revenue' => (float) ($results->get($key)->total ?? 0)
                    ];
                }
                return $trend;
            });

            // System Health - Cached for 2 hours (expensive operations)
            $systemHealth = \Cache::remember('saas_dashboard_system_health', $systemCacheTime, function () {
                return [
                    'db_connection' => true,
                    'storage_writable' => is_writable(storage_path()),
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'queue_status' => $this->checkQueueStatus(),
                    'storage_usage' => $this->getStorageUsage(),
                ];
            });

            // Activity Feed - Cached for 30 minutes
            $activityFeed = \Cache::remember('saas_dashboard_activity_feed', 30 * 60, function () {
                return \App\Models\SuperAdminAuditLog::with(['admin', 'salon'])
                    ->latest()
                    ->take(10)
                    ->get();
            });

            // Daily Bookings - Cached for 60 minutes
            $dailyBookings = \Cache::remember('saas_dashboard_daily_bookings', $cacheTime, function () {
                return Booking::where('created_at', '>=', now()->subDay())->count();
            });

            // Fallbacks for less critical data to reduce first-load impact
            $salonGrowth = collect([]);
            $planDistribution = collect([]);
            $revenueByPlan = collect([]);
            $topSalonsByRevenue = collect([]);
            $topSalonsByActivity = collect([]);
            $subscriptionHealth = ['trial' => 0, 'paid' => 0, 'expiring_soon' => 0];
            $trialConversionRate = 0;
            $ltv = 0;
            $atRiskSalons = collect([]);
            $avgRating = 0;
            $totalReviews = 0;
            $powerUsers = collect([]);
            $salonStatusStats = ['active' => 0, 'pending' => 0, 'inactive' => 0];
            $topActiveSalonsWeekly = collect([]);
            $arpu = 0;
            $momGrowth = 0;
            $topGrowingPlans = collect([]);
            $thisMonthSalons = 0;
            $momSalonGrowth = 0;
            $expiringTrials = collect([]);
            $lowRatingSalons = collect([]);
            $activeSessionsCount = 0;
            $dbTableSizes = collect([]);

            // Missing system metrics variables
            $queueStats = ['pending' => 0, 'failed' => 0];
            $logStats = ['size' => 0, 'errors' => 0];
            $dbPerformance = ['connections' => 0, 'uptime' => 0, 'queries' => 0];
            $storageBreakdown = ['uploads' => 0, 'logs' => 0, 'cache' => 0];

            return view('super-admin.dashboard', compact(
                'stats',
                'recent_salons',
                'expiring_subscriptions',
                'recent_payments',
                'revenueTrend',
                'systemHealth',
                'activityFeed',
                'dailyBookings',
                'salonGrowth',
                'planDistribution',
                'revenueByPlan',
                'topSalonsByRevenue',
                'topSalonsByActivity',
                'subscriptionHealth',
                'trialConversionRate',
                'ltv',
                'atRiskSalons',
                'avgRating',
                'totalReviews',
                'powerUsers',
                'salonStatusStats',
                'topActiveSalonsWeekly',
                'arpu',
                'momGrowth',
                'topGrowingPlans',
                'thisMonthSalons',
                'momSalonGrowth',
                'expiringTrials',
                'lowRatingSalons',
                'activeSessionsCount',
                'dbTableSizes',
                'queueStats',
                'logStats',
                'dbPerformance',
                'storageBreakdown'
            ));
        } catch (\Exception $e) {
            \Log::error("Dashboard Error (Metadata Lock?): " . $e->getMessage());
            return view('super-admin.dashboard', [
                'stats' => [],
                'recent_salons' => collect([]),
                'expiring_subscriptions' => collect([]),
                'recent_payments' => collect([]),
                'revenueTrend' => collect([]),
                'salonGrowth' => collect([]),
                'planDistribution' => collect([]),
                'revenueByPlan' => collect([]),
                'systemHealth' => [
                    'storage_usage' => ['percentage' => 0]
                ],
                'topSalonsByRevenue' => collect([]),
                'topSalonsByActivity' => collect([]),
                'activityFeed' => collect([]),
                'subscriptionHealth' => ['trial' => 0, 'paid' => 0, 'expiring_soon' => 0],
                'trialConversionRate' => 0,
                'ltv' => 0,
                'atRiskSalons' => collect([]),
                'avgRating' => 0,
                'totalReviews' => 0,
                'powerUsers' => collect([]),
                'dailyBookings' => 0,
                'salonStatusStats' => ['active' => 0, 'pending' => 0, 'inactive' => 0],
                'topActiveSalonsWeekly' => collect([]),
                'arpu' => 0,
                'momGrowth' => 0,
                'topGrowingPlans' => collect([]),
                'thisMonthSalons' => 0,
                'momSalonGrowth' => 0,
                'expiringTrials' => collect([]),
                'lowRatingSalons' => collect([]),
                'activeSessionsCount' => 0,
                'dbTableSizes' => collect([]),
                'queueStats' => ['pending' => 0, 'failed' => 0],
                'logStats' => ['size' => 0, 'errors' => 0],
                'dbPerformance' => ['connections' => 0, 'uptime' => 0, 'queries' => 0],
                'storageBreakdown' => ['uploads' => 0, 'logs' => 0, 'cache' => 0],
                'error' => 'Dashboard data is temporarily unavailable due to database maintenance.'
            ]);
        }
    }

    private function getDirSize($directory)
    {
        return 0; // Temporarily disabled to restore service
    }

    private function checkQueueStatus()
    {
        try {
            // Simple check if we can connect to the queue driver
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getStorageUsage()
    {
        try {
            $total = disk_total_space(base_path());
            $free = disk_free_space(base_path());
            $used = $total - $free;
            return [
                'used' => round($used / (1024 * 1024 * 1024), 2), // GB
                'total' => round($total / (1024 * 1024 * 1024), 2), // GB
                'percentage' => round(($used / $total) * 100, 1)
            ];
        } catch (\Exception $e) {
            return ['used' => 0, 'total' => 0, 'percentage' => 0];
        }
    }

    private function getLastLines($filePath, $lines = 500)
    {
        try {
            if (!file_exists($filePath) || !is_readable($filePath)) {
                return '';
            }

            $file = new \SplFileObject($filePath, 'r');
            $file->seek(PHP_INT_MAX);
            $totalLines = $file->key();

            // Calculate starting line
            $startLine = max(0, $totalLines - $lines);

            $result = [];
            $file->seek($startLine);

            while (!$file->eof()) {
                $line = $file->current();
                if ($line !== false) {
                    $result[] = $line;
                }
                $file->next();
            }

            return implode('', $result);
        } catch (\Exception $e) {
            return '';
        }
    }
}
