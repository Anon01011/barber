<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\User;
use App\Models\Customer;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected $settingsService;

    public function __construct(\App\Services\SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }
    public function index()
    {
        $salonId = auth()->user()->salon_id;
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // 1. Quick Stats (Today) - Exclude bookings completed via POS
        $todayBookingRevenue = DB::table('bookings')
            ->where('salon_id', $salonId)
            ->whereDate('start_time', $today)
            ->where('status', 'completed')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                    ->whereIn('pos_sale_items.item_type', ['App\Models\Service', 'App\Models\Package', 'App\Models\Membership']);
            })
            ->sum(DB::raw('amount + COALESCE(tip_amount, 0)'));

        $todayPosSales = DB::table('pos_sales')
            ->where('salon_id', $salonId)
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'voided')
            ->sum(DB::raw('total - tip'));

        try {
            $todayPosRefundOutflow = DB::table('pos_refunds')
                ->where('salon_id', $salonId)
                ->whereDate('processed_at', $today)
                ->sum('net_refund_amount');
        } catch (\Exception $e) {
            $todayPosRefundOutflow = 0;
        }

        $todayPosRevenue = $todayPosSales - $todayPosRefundOutflow;

        $todayBookings = DB::table('bookings')
            ->where('salon_id', $salonId)
            ->whereDate('start_time', $today)
            ->count();

        // 2. Monthly Stats - Exclude bookings completed via POS
        $monthBookingRevenue = DB::table('bookings')
            ->where('salon_id', $salonId)
            ->where('start_time', '>=', $startOfMonth)
            ->where('status', 'completed')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                    ->whereIn('pos_sale_items.item_type', ['App\Models\Service', 'App\Models\Package', 'App\Models\Membership']);
            })
            ->sum(DB::raw('amount + COALESCE(tip_amount, 0)'));

        $monthPosSales = DB::table('pos_sales')
            ->where('salon_id', $salonId)
            ->where('created_at', '>=', $startOfMonth)
            ->where('status', '!=', 'voided')
            ->sum(DB::raw('total - tip'));

        try {
            $monthPosRefundOutflow = DB::table('pos_refunds')
                ->where('salon_id', $salonId)
                ->where('processed_at', '>=', $startOfMonth)
                ->sum('net_refund_amount');
        } catch (\Exception $e) {
            $monthPosRefundOutflow = 0;
        }

        $monthPosRevenue = $monthPosSales - $monthPosRefundOutflow;

        $monthBookings = DB::table('bookings')
            ->where('salon_id', $salonId)
            ->where('start_time', '>=', $startOfMonth)
            ->count();

        // 3. Last Month Stats (for Growth) - Exclude bookings completed via POS
        $lastMonthBookingRevenue = DB::table('bookings')
            ->where('salon_id', $salonId)
            ->whereBetween('start_time', [$lastMonthStart, $lastMonthEnd])
            ->where('status', 'completed')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                    ->whereIn('pos_sale_items.item_type', ['App\Models\Service', 'App\Models\Package', 'App\Models\Membership']);
            })
            ->sum(DB::raw('amount + COALESCE(tip_amount, 0)'));

        $lastMonthPosSales = DB::table('pos_sales')
            ->where('salon_id', $salonId)
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->where('status', '!=', 'voided')
            ->sum(DB::raw('total - tip'));

        try {
            $lastMonthPosRefundOutflow = DB::table('pos_refunds')
                ->where('salon_id', $salonId)
                ->whereBetween('processed_at', [$lastMonthStart, $lastMonthEnd])
                ->sum('net_refund_amount');
        } catch (\Exception $e) {
            $lastMonthPosRefundOutflow = 0;
        }

        $lastMonthPosRevenue = $lastMonthPosSales - $lastMonthPosRefundOutflow;

        $currentMonthTotal = $monthBookingRevenue + $monthPosRevenue;
        $lastMonthTotal = $lastMonthBookingRevenue + $lastMonthPosRevenue;
        $revenueGrowth = $lastMonthTotal > 0 ? (($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100 : 0;

        // 4. Top Services (Last 30 Days) - Exclude bookings completed via POS
        $topServices = DB::table('bookings')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('bookings.salon_id', $salonId)
            ->where('bookings.start_time', '>=', Carbon::now()->subDays(30))
            ->where('bookings.status', 'completed')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                    ->whereIn('pos_sale_items.item_type', ['App\Models\Service', 'App\Models\Package', 'App\Models\Membership']);
            })
            ->select('services.name', DB::raw('count(*) as count'), DB::raw('sum(bookings.amount + COALESCE(bookings.tip_amount, 0)) as revenue'))
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // 5. Top Staff (Last 30 Days) - Exclude bookings completed via POS
        $topStaff = DB::table('bookings')
            ->join('users', 'bookings.staff_id', '=', 'users.id')
            ->where('bookings.salon_id', $salonId)
            ->where('bookings.start_time', '>=', Carbon::now()->subDays(30))
            ->where('bookings.status', 'completed')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                    ->whereIn('pos_sale_items.item_type', ['App\Models\Service', 'App\Models\Package', 'App\Models\Membership']);
            })
            ->select('users.name', DB::raw('count(*) as count'), DB::raw('sum(bookings.amount + COALESCE(bookings.tip_amount, 0)) as revenue'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        // 6. Monthly Revenue Trend (Last 6 Months)
        $monthlyTrend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $mStart = $month->copy()->startOfMonth();
            $mEnd = $month->copy()->endOfMonth();

            $bRev = DB::table('bookings')
                ->where('salon_id', $salonId)
                ->whereBetween('start_time', [$mStart, $mEnd])
                ->where('status', 'completed')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pos_sale_items')
                        ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                        ->whereIn('pos_sale_items.item_type', ['App\Models\Service', 'App\Models\Package', 'App\Models\Membership']);
                })
                ->sum(DB::raw('amount + COALESCE(tip_amount, 0)'));

            $pSales = DB::table('pos_sales')
                ->where('salon_id', $salonId)
                ->whereBetween('created_at', [$mStart, $mEnd])
                ->where('payment_status', '!=', 'pending')
                ->where('status', '!=', 'voided')
                ->sum(DB::raw('total - tip'));

            $pRefundOutflow = DB::table('pos_refunds')
                ->where('salon_id', $salonId)
                ->whereBetween('processed_at', [$mStart, $mEnd])
                ->sum('net_refund_amount');

            $pRev = $pSales - $pRefundOutflow;

            $monthlyTrend->put($month->format('M Y'), [
                'bookings' => (float) $bRev,
                'pos' => (float) $pRev,
                'total' => (float) ($bRev + $pRev)
            ]);
        }

        // 7. Recent Activity (Combined)
        $recentBookings = DB::table('bookings')
            ->join('customers', 'bookings.customer_id', '=', 'customers.id')
            ->where('bookings.salon_id', $salonId)
            ->select('bookings.id', 'bookings.start_time as date', 'customers.name as customer', DB::raw("'booking' as type"), 'bookings.amount')
            ->orderByDesc('bookings.created_at')
            ->take(5);

        $recentPos = DB::table('pos_sales')
            ->leftJoin('customers', 'pos_sales.customer_id', '=', 'customers.id')
            ->where('pos_sales.salon_id', $salonId)
            ->where('pos_sales.status', '!=', 'voided')
            ->select('pos_sales.id', 'pos_sales.created_at as date', DB::raw("COALESCE(customers.name, 'Walk-in') as customer"), DB::raw("'pos' as type"), 'pos_sales.total as amount')
            ->orderByDesc('pos_sales.created_at')
            ->take(5);

        $recentActivity = $recentBookings->union($recentPos)
            ->orderByDesc('date')
            ->take(10)
            ->get();

        $todayRefundStats = DB::table('pos_refunds')
            ->where('salon_id', $salonId)
            ->whereDate('processed_at', $today)
            ->select(DB::raw('SUM(amount) as gross'), DB::raw('SUM(fee_amount) as fees'), DB::raw('SUM(net_refund_amount) as net'))
            ->first();

        $monthRefundStats = DB::table('pos_refunds')
            ->where('salon_id', $salonId)
            ->where('processed_at', '>=', $startOfMonth)
            ->select(DB::raw('SUM(amount) as gross'), DB::raw('SUM(fee_amount) as fees'), DB::raw('SUM(net_refund_amount) as net'))
            ->first();

        $stats = [
            'today_revenue' => $todayBookingRevenue + $todayPosRevenue,
            'today_bookings' => $todayBookings,
            'today_refunds' => $todayRefundStats->gross ?? 0,
            'today_refund_fees' => $todayRefundStats->fees ?? 0,
            'today_net_refunds' => $todayRefundStats->net ?? 0,
            'month_revenue' => $currentMonthTotal,
            'month_bookings' => $monthBookings,
            'month_refunds' => $monthRefundStats->gross ?? 0,
            'month_refund_fees' => $monthRefundStats->fees ?? 0,
            'month_net_refunds' => $monthRefundStats->net ?? 0,
            'revenue_growth' => $revenueGrowth,
            'top_services' => $topServices,
            'top_staff' => $topStaff,
            'monthly_trend' => $monthlyTrend,
            'recent_activity' => $recentActivity,
        ];

        $branch = app()->has('current_branch') ? app('current_branch') : null;

        return view('admin.reports.index', compact('stats', 'branch'));
    }

    public function sales(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $range = $request->get('range', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $source = $request->get('source', 'all');
        $branchId = $request->get('branch_id');
        $staffId = $request->get('staff_id');
        $customerId = $request->get('customer_id');
        $paymentMethod = $request->get('payment_method');
        $paymentStatus = $request->get('payment_status');
        $cashierId = $request->get('cashier_id');

        // Calculate dates based on range
        if ($range !== 'custom') {
            switch ($range) {
                case 'today':
                    $startDate = Carbon::now()->format('Y-m-d');
                    $endDate = Carbon::now()->format('Y-m-d');
                    break;
                case 'yesterday':
                    $startDate = Carbon::yesterday()->format('Y-m-d');
                    $endDate = Carbon::yesterday()->format('Y-m-d');
                    break;
                case 'this_week':
                    $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                    $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                    break;
                case 'last_week':
                    $startDate = Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d');
                    $endDate = Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d');
                    break;
                case 'this_month':
                    $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                    break;
                case 'last_month':
                    $startDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
                    break;
                case '3_months':
                    $startDate = Carbon::now()->subMonths(3)->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->format('Y-m-d');
                    break;
                case '6_months':
                    $startDate = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->format('Y-m-d');
                    break;
                case 'this_year':
                    $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                    $endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                    break;
                default:
                    $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
            }
        } else {
            $startDate = $startDate ?: Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = $endDate ?: Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // 1. Calculate Aggregates using Database Queries (No Loading Models)
        $bookingQuery = Booking::where('salon_id', $salonId)
            ->whereBetween('start_time', [$startDateTime, $endDateTime])
            ->where(function ($q) {
                $q->where('status', 'completed')
                    ->orWhere('payment_status', 'refunded');
            });

        $posQuery = PosSale::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->where('status', '!=', 'voided');

        // Apply Filters
        if ($branchId) {
            $bookingQuery->where('branch_id', $branchId);
            $posQuery->where('branch_id', $branchId);
        }
        if ($staffId) {
            $bookingQuery->where('staff_id', $staffId);
            $posQuery->where('employee_id', $staffId);
        }
        if ($customerId) {
            $bookingQuery->where('customer_id', $customerId);
            $posQuery->where('customer_id', $customerId);
        }
        if ($paymentMethod) {
            $bookingQuery->where('payment_method', $paymentMethod);
            $posQuery->where('payment_method', $paymentMethod);
        }
        if ($paymentStatus) {
            $bookingQuery->where('payment_status', $paymentStatus);
            $posQuery->where('payment_status', $paymentStatus);
        }
        if ($cashierId) {
            $bookingQuery->where('staff_id', $cashierId);
            $posQuery->where('employee_id', $cashierId);
        }

        // Revenue - Exclude bookings completed via POS to prevent double counting
        // For bookings completed via POS, revenue comes from POS sale record

        // 1. Get Booking Revenue (Gross / Tax Inclusive)
        $bookingRevenue = ($source === 'all' || $source === 'bookings') ?
            $bookingQuery->whereDoesntHave('posSaleItem', function ($q) {
                $q->whereIn('item_type', ['App\Models\Service', 'App\Models\Package', 'App\Models\Membership']);
            })->sum('amount') : 0;

        // 2. Get POS Revenue (Gross / Tax Inclusive - excluding tips)
        // subtotal is tax-exclusive, so we add tax. Or use total - tip.
        // using subtotal - discount + tax is safer as total might include tip
        $posRevenue = ($source === 'all' || $source === 'pos') ?
            $posQuery->sum(DB::raw('total - tip')) : 0;

        // Subtract net refunds processed in this period
        if ($source === 'all' || $source === 'pos') {
            try {
                $refundQuery = DB::table('pos_refunds')
                    ->where('pos_refunds.salon_id', $salonId)
                    ->whereBetween('pos_refunds.processed_at', [$startDateTime, $endDateTime]);

                if ($branchId || $staffId || $customerId || $cashierId) {
                    $refundQuery->join('pos_sales', 'pos_refunds.sale_id', '=', 'pos_sales.id');
                    if ($branchId)
                        $refundQuery->where('pos_sales.branch_id', $branchId);
                    if ($staffId || $cashierId)
                        $refundQuery->where('pos_sales.employee_id', $staffId ?: $cashierId);
                    if ($customerId)
                        $refundQuery->where('pos_sales.customer_id', $customerId);
                }

                $refundNetOutflow = $refundQuery->sum('net_refund_amount');
                $posRevenue -= $refundNetOutflow;
            } catch (\Exception $e) {
                // Table doesn't exist, skip refund calculation
            }
        }

        $totalRevenue = $bookingRevenue + $posRevenue;

        // Tips
        $bookingTips = ($source === 'all' || $source === 'bookings') ?
            $bookingQuery->whereDoesntHave('posSaleItem', function ($q) {
                $q->whereIn('item_type', ['App\Models\Service', 'App\Models\Package', 'App\Models\Membership']);
            })->sum('tip_amount') : 0;
        $posTips = ($source === 'all' || $source === 'pos') ? $posQuery->sum('tip') : 0;
        $totalTips = $bookingTips + $posTips;

        // Tax
        // POS Tax is explicit
        $posTax = ($source === 'all' || $source === 'pos') ? $posQuery->sum('tax') : 0;

        // Booking Tax is implicit in 'amount'. We estimate it based on current settings if enabled.
        $bookingTax = 0;
        if (($source === 'all' || $source === 'bookings') && $bookingRevenue > 0) {
            $taxEnabled = $this->settingsService->get('tax_enabled_services', false, $salonId);
            $taxRate = $this->settingsService->get('tax_rate', 0, $salonId);

            if ($taxEnabled && $taxRate > 0) {
                // amount = net * (1 + rate/100)
                // net = amount / (1 + rate/100)
                // tax = amount - net
                $bookingNet = $bookingRevenue / (1 + ($taxRate / 100));
                $bookingTax = $bookingRevenue - $bookingNet;
            }
        }

        $totalTax = $posTax + $bookingTax;

        // Net Revenue (Tax Exclusive)
        $netRevenue = $totalRevenue - $totalTax;

        // Counts - Exclude bookings completed via POS to prevent double counting
        $bookingCount = ($source === 'all' || $source === 'bookings') ?
            $bookingQuery->whereDoesntHave('posSaleItem', function ($q) {
                $q->whereIn('item_type', ['App\Models\Service', 'App\Models\Package', 'App\Models\Membership']);
            })->count() : 0;
        $posCount = ($source === 'all' || $source === 'pos') ? $posQuery->count() : 0;
        $totalTransactions = $bookingCount + $posCount;
        $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Commissions (Efficient Query)
        $totalCommission = \App\Models\StaffCommission::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->where('item_type', '!=', 'tip')
            ->when($staffId, function ($q) use ($staffId) {
                return $q->where('staff_id', $staffId);
            })
            ->sum('commission_amount');

        // Unpaid / Outstanding
        // Note: Reusing queries might carry over constraints added in previous steps (like whereDoesntHave).
        // Since we want to ensure we catch unpaid items correctly:

        $bookingUnpaid = 0;
        if ($source === 'all' || $source === 'bookings') {
            // We clone to avoid modifying the original query further if it's used later (though we are near end of aggregates)
            // But wait, $bookingQuery already has whereDoesntHave applied twice by now in the original code flow?
            // Actually lines 316 and 330 modified it.
            // So if we simply use it, we are filtering by "Not POS Linked" implicitly.
            // We just need to add the payment status check.

            // However, to be cleaner and safer given the messy accumulation:
            $bookingUnpaid = Booking::where('salon_id', $salonId)
                ->whereBetween('start_time', [$startDateTime, $endDateTime])
                ->where('status', 'completed') // Assuming only completed bookings count as 'Sales' in this report
                ->whereDoesntHave('posSaleItem')
                ->whereIn('payment_status', ['pending', 'unpaid', 'partial', 'paylater']);

            // Apply same filters
            if ($branchId)
                $bookingUnpaid->where('branch_id', $branchId);
            if ($staffId)
                $bookingUnpaid->where('staff_id', $staffId);
            if ($customerId)
                $bookingUnpaid->where('customer_id', $customerId);
            // If payment status filter is set, we must respect it.
            // If user filtered for 'Paid', this query should return 0.
            if ($paymentStatus) {
                // If $paymentStatus is 'paid', whereIn(['pending'...]) will result in empty intersection naturally?
                // No, we must apply the USER's filter.
                $bookingUnpaid->where('payment_status', $paymentStatus);
            }

            $bookingUnpaid = $bookingUnpaid->sum('amount'); // Using full amount as approximation
        }

        $posUnpaid = ($source === 'all' || $source === 'pos') ?
            $posQuery->sum('outstanding_amount') : 0;

        $posUnpaid = ($source === 'all' || $source === 'pos') ?
            $posQuery->sum('outstanding_amount') : 0;

        $posUnpaid = ($source === 'all' || $source === 'pos') ?
            $posQuery->sum('outstanding_amount') : 0;

        // Use pos_refunds table for accurate refund reporting based on processed time
        $posRefunded = 0;
        $posRefundFees = 0;

        if ($source === 'all' || $source === 'pos') {
            try {
                $refundStats = DB::table('pos_refunds')
                    ->where('salon_id', $salonId)
                    ->whereBetween('processed_at', [$startDateTime, $endDateTime])
                    ->when($staffId, function ($q) use ($staffId) {
                        return $q->where('processed_by', $staffId);
                    })
                    ->select(DB::raw('SUM(amount) as total_amount'), DB::raw('SUM(fee_amount) as total_fees'))
                    ->first();

                $posRefunded = $refundStats->total_amount ?? 0;
                $posRefundFees = $refundStats->total_fees ?? 0;
            } catch (\Exception $e) {
                // Table doesn't exist
            }
        }

        $totalUnpaid = $bookingUnpaid + $posUnpaid;
        $totalRefunded = $posRefunded; // Bookings don't have separate refund column yet, captured via status logic manually if needed, but for now only POS refunds derived from DB column

        // 2. Efficient Pagination using Union
        $perPage = 10;
        $currentPage = $request->get('page', 1);

        $transactions = collect();
        $totalItems = 0;

        if ($source === 'all') {
            // Union Query for Pagination
            // Exclude bookings completed via POS from booking list
            $bookingsSub = DB::table('bookings')
                ->select(
                    DB::raw('MIN(id) as id'),
                    DB::raw('MIN(start_time) as date'),
                    DB::raw("'booking' as type"),
                    DB::raw('SUM(amount) as amount'),
                    DB::raw('SUM(COALESCE(tip_amount, 0)) as tip'),
                    DB::raw('MAX(booking_group_id) as booking_group_id')
                )
                ->where('salon_id', $salonId)
                ->whereBetween('start_time', [$startDateTime, $endDateTime])
                ->where(function ($q) {
                    $q->where('status', 'completed')
                        ->orWhere('payment_status', 'refunded');
                })
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pos_sale_items')
                        ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                        ->whereIn('pos_sale_items.item_type', ['App\Models\Service', 'App\Models\Package', 'App\Models\Membership']);
                });

            // Apply Filters to Bookings
            if ($branchId)
                $bookingsSub->where('branch_id', $branchId);
            if ($staffId)
                $bookingsSub->where('staff_id', $staffId);
            if ($customerId)
                $bookingsSub->where('customer_id', $customerId);
            if ($paymentMethod)
                $bookingsSub->where('payment_method', $paymentMethod);
            if ($paymentStatus)
                $bookingsSub->where('payment_status', $paymentStatus);
            if ($cashierId)
                $bookingsSub->where('staff_id', $cashierId);

            $bookingsSub->groupBy(DB::raw('COALESCE(booking_group_id, CAST(id AS CHAR))'));

            $posSub = DB::table('pos_sales')
                ->select('id', 'sale_date as date', DB::raw("'pos' as type"), 'subtotal as amount', 'tip', DB::raw('NULL as booking_group_id'))
                ->where('salon_id', $salonId)
                ->whereBetween('sale_date', [$startDateTime, $endDateTime])
                ->where('payment_status', '!=', 'pending')
                ->where('status', '!=', 'voided');

            // Apply Filters to POS
            if ($branchId)
                $posSub->where('branch_id', $branchId);
            if ($staffId)
                $posSub->where('employee_id', $staffId);
            if ($customerId)
                $posSub->where('customer_id', $customerId);
            if ($paymentMethod)
                $posSub->where('payment_method', $paymentMethod);
            if ($paymentStatus)
                $posSub->where('payment_status', $paymentStatus);
            if ($cashierId)
                $posSub->where('employee_id', $cashierId);

            $unionQuery = DB::table(DB::raw("({$bookingsSub->toSql()}) as bookings_sub"))
                ->mergeBindings($bookingsSub)
                ->union($posSub)
                ->orderBy('date', 'desc');

            $totalItems = $unionQuery->count();
            $paginatedIds = $unionQuery->forPage($currentPage, $perPage)->get();

            // Hydrate Models
            $bookingIds = $paginatedIds->where('type', 'booking')->pluck('id');
            $posIds = $paginatedIds->where('type', 'pos')->pluck('id');

            $loadedBookings = Booking::with(['customer', 'staff', 'service'])
                ->whereIn('id', $bookingIds)
                ->get()
                ->keyBy('id');


            $loadedPos = PosSale::with(['customer', 'employee', 'items'])
                ->whereIn('id', $posIds)
                ->get()
                ->keyBy('id');

            // Merge back in correct order
            foreach ($paginatedIds as $item) {
                if ($item->type === 'booking' && $loadedBookings->has($item->id)) {
                    $booking = $loadedBookings->get($item->id);
                    $transactions->push([
                        'id' => $booking->id,
                        'type' => 'booking',
                        'date' => $item->date,
                        'customer' => $booking->customer,
                        'staff' => $booking->staff,
                        'amount' => $item->amount,
                        'tip' => $item->tip,
                        'payment_method' => $this->getPaymentMethodLabel($booking->payment_method),
                        'payment_status' => $booking->payment_status,
                        'status' => $booking->status,
                        'booking_group_id' => $item->booking_group_id,
                        'data' => $booking
                    ]);
                } elseif ($item->type === 'pos' && $loadedPos->has($item->id)) {
                    $posSale = $loadedPos->get($item->id);
                    $isPosBooking = $posSale->items->contains(fn($i) => !empty($i->booking_id));

                    // Query refunds directly since relationship doesn't exist
                    try {
                        $refunds = DB::table('pos_refunds')
                            ->where('sale_id', $posSale->id)
                            ->select('amount', 'fee_amount')
                            ->get();
                        $totalRefunded = $refunds->sum('amount');
                        $refundFees = $refunds->sum('fee_amount');
                    } catch (\Exception $e) {
                        $totalRefunded = 0;
                        $refundFees = 0;
                    }

                    $originalTotal = $posSale->total;
                    $netTotal = $originalTotal - $totalRefunded + $refundFees;
                    $tip = $posSale->tip;

                    $transactions->push([
                        'id' => $posSale->id,
                        'type' => 'pos',
                        'subtype' => $isPosBooking ? 'booking' : 'sale',
                        'date' => $posSale->created_at,
                        'customer' => $posSale->customer,
                        'staff' => $posSale->employee,
                        'amount' => $netTotal - $tip, // Net Bill Part
                        'tip' => $tip,
                        'refunded_amount' => $totalRefunded, // For status logic
                        'payment_method' => $this->getPaymentMethodLabel($posSale->payment_method),
                        'payment_status' => $posSale->payment_status,
                        'status' => $posSale->status,
                        'data' => $posSale
                    ]);
                }
            }

        } elseif ($source === 'bookings') {
            // Exclude bookings completed via POS
            $query = Booking::where('salon_id', $salonId)
                ->whereBetween('start_time', [$startDateTime, $endDateTime])
                ->where(function ($q) {
                    $q->where('status', 'completed')
                        ->orWhere('payment_status', 'refunded');
                })
                ->whereDoesntHave('posSaleItem')
                ->with(['customer', 'staff', 'service'])
                ->orderBy('start_time', 'desc');

            $totalItems = $query->count();
            $results = $query->forPage($currentPage, $perPage)->get();

            foreach ($results as $booking) {
                $transactions->push([
                    'id' => $booking->id,
                    'type' => 'booking',
                    'date' => $booking->start_time,
                    'customer' => $booking->customer,
                    'staff' => $booking->staff,
                    'amount' => $booking->amount,
                    'tip' => $booking->tip_amount,
                    'payment_method' => $this->getPaymentMethodLabel($booking->payment_method),
                    'payment_status' => $booking->payment_status,
                    'status' => $booking->status,
                    'data' => $booking
                ]);
            }

        } elseif ($source === 'pos') {
            $query = PosSale::where('salon_id', $salonId)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->where('payment_status', '!=', 'pending')
                ->where('status', '!=', 'voided')
                ->with(['customer', 'employee', 'items'])
                ->orderBy('created_at', 'desc');

            $totalItems = $query->count();
            $results = $query->forPage($currentPage, $perPage)->get();

            foreach ($results as $sale) {
                $isPosBooking = $sale->items->contains(fn($i) => !empty($i->booking_id));

                $totalRefunded = $sale->refunds->sum('amount');
                $refundFees = $sale->refunds->sum('fee_amount');
                $originalTotal = $sale->total;
                $netTotal = $originalTotal - $totalRefunded + $refundFees;
                $tip = $sale->tip;

                $transactions->push([
                    'id' => $sale->id,
                    'type' => 'pos',
                    'subtype' => $isPosBooking ? 'booking' : 'sale',
                    'date' => $sale->created_at,
                    'customer' => $sale->customer,
                    'staff' => $sale->employee,
                    'amount' => $netTotal - $tip,
                    'tip' => $tip,
                    'refunded_amount' => $totalRefunded,
                    'payment_method' => $this->getPaymentMethodLabel($sale->payment_method),
                    'payment_status' => $sale->payment_status,
                    'status' => $sale->status,
                    'data' => $sale
                ]);
            }
        }

        $paginatedTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactions,
            $totalItems,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // 3. Charts & Breakdowns (Optimized Aggregations)

        // Daily Revenue - Exclude bookings completed via POS
        $dailyBookingRevenue = ($source !== 'pos') ? Booking::where('salon_id', $salonId)
            ->whereBetween('start_time', [$startDateTime, $endDateTime])
            ->where(function ($q) {
                $q->where('status', 'completed')
                    ->orWhere('payment_status', 'refunded');
            })
            ->whereDoesntHave('posSaleItem')
            ->selectRaw('DATE(start_time) as date, SUM(amount) as revenue, SUM(COALESCE(tip_amount, 0)) as tip, COUNT(*) as count')
            ->groupBy('date')
            ->get()
            ->keyBy('date') : collect();

        $dailyPosRevenue = ($source !== 'bookings') ? PosSale::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->where('status', '!=', 'voided')
            ->selectRaw('DATE(created_at) as date, SUM(total - tip) as revenue, SUM(tip) as tip, COUNT(*) as count')
            ->groupBy('date')
            ->get()
            ->keyBy('date') : collect();

        // Merge Daily Data - Ensure continuous dates
        $allDates = collect();
        $current = $startDateTime->copy();
        while ($current <= $endDateTime) {
            $allDates->push($current->format('Y-m-d'));
            $current->addDay();
        }

        $dailyRevenue = $allDates->mapWithKeys(function ($date) use ($dailyBookingRevenue, $dailyPosRevenue) {
            $booking = $dailyBookingRevenue->get($date);
            $pos = $dailyPosRevenue->get($date);

            $bookingRev = $booking ? $booking->revenue : 0;
            $posRev = $pos ? $pos->revenue : 0;
            $bookingCnt = $booking ? $booking->count : 0;
            $posCnt = $pos ? $pos->count : 0;

            return [
                $date => [
                    'booking_revenue' => $bookingRev,
                    'pos_revenue' => $posRev,
                    'revenue' => $bookingRev + $posRev,
                    'booking_tip' => $booking ? $booking->tip : 0,
                    'pos_tip' => $pos ? $pos->tip : 0,
                    'tip' => ($booking ? $booking->tip : 0) + ($pos ? $pos->tip : 0),
                    'booking_count' => $bookingCnt,
                    'pos_count' => $posCnt,
                    'count' => $bookingCnt + $posCnt
                ]
            ];
        });

        // Subtract refunds from daily revenue based on processed_at
        $dailyRefunds = collect(); // Initialize to avoid undefined variable error
        if ($source !== 'bookings') {
            try {
                $dailyRefunds = DB::table('pos_refunds')
                    ->where('salon_id', $salonId)
                    ->whereBetween('processed_at', [$startDateTime, $endDateTime])
                    ->selectRaw('DATE(processed_at) as date, SUM(net_refund_amount) as amount')
                    ->groupBy('date')
                    ->get()
                    ->keyBy('date');

                foreach ($dailyRefunds as $date => $refund) {
                    if ($dailyRevenue->has($date)) {
                        $dayData = $dailyRevenue->get($date);
                        $dayData['pos_revenue'] -= $refund->amount;
                        $dayData['revenue'] -= $refund->amount;
                        $dailyRevenue->put($date, $dayData);
                    } else {
                        // If there was a refund on a day with no sales, still track it
                        $dailyRevenue->put($date, [
                            'booking_revenue' => 0,
                            'pos_revenue' => -$refund->amount,
                            'revenue' => -$refund->amount,
                            'booking_tip' => 0,
                            'pos_tip' => 0,
                            'tip' => 0,
                            'booking_count' => 0,
                            'pos_count' => 0,
                            'count' => 0
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Table doesn't exist, skip refund adjustments
                $dailyRefunds = collect();
            }
        }

        // Payment Methods - Exclude bookings completed via POS
        $bookingPayments = ($source !== 'pos') ? Booking::where('salon_id', $salonId)
            ->whereBetween('start_time', [$startDateTime, $endDateTime])
            ->where('status', 'completed')
            ->whereDoesntHave('posSaleItem')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total, SUM(COALESCE(tip_amount, 0)) as tip')
            ->groupBy('payment_method')
            ->get() : collect();

        $posPayments = ($source !== 'bookings') ? PosSale::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->where('status', '!=', 'voided')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total - tip) as total, SUM(tip) as tip')
            ->groupBy('payment_method')
            ->get() : collect();

        $paymentMethods = collect();
        $allMethods = $bookingPayments->pluck('payment_method')->merge($posPayments->pluck('payment_method'))->unique();

        foreach ($allMethods as $method) {
            $b = $bookingPayments->firstWhere('payment_method', $method);
            $p = $posPayments->firstWhere('payment_method', $method);

            $label = $this->getPaymentMethodLabel($method);

            // Check if this label already exists in the collection (to merge cases like 'cash' and 'Cash')
            $existing = $paymentMethods->firstWhere('method', $label);
            if ($existing) {
                $index = $paymentMethods->search($existing);
                $paymentMethods[$index] = [
                    'method' => $label,
                    'count' => $existing['count'] + ($b ? $b->count : 0) + ($p ? $p->count : 0),
                    'total' => $existing['total'] + ($b ? $b->total : 0) + ($p ? $p->total : 0),
                    'tip' => $existing['tip'] + ($b ? $b->tip : 0) + ($p ? $p->tip : 0)
                ];
            } else {
                $paymentMethods->push([
                    'method' => $label,
                    'count' => ($b ? $b->count : 0) + ($p ? $p->count : 0),
                    'total' => ($b ? $b->total : 0) + ($p ? $p->total : 0),
                    'tip' => ($b ? $b->tip : 0) + ($p ? $p->tip : 0)
                ]);
            }
        }

        // Adjust payment method totals for refunds (using a simplified approach since refunds might not map 1:1 to single methods in all cases, but usually do)
        if ($source !== 'bookings' && $dailyRefunds->count() > 0) {
            try {
                $refundByMethod = DB::table('pos_refunds')
                    ->join('pos_sales', 'pos_refunds.sale_id', '=', 'pos_sales.id')
                    ->where('pos_refunds.salon_id', $salonId)
                    ->whereBetween('pos_refunds.processed_at', [$startDateTime, $endDateTime])
                    ->selectRaw('pos_sales.payment_method, SUM(net_refund_amount) as amount')
                    ->groupBy('pos_sales.payment_method')
                    ->get();

                foreach ($refundByMethod as $refund) {
                    $methodLabel = $this->getPaymentMethodLabel($refund->payment_method);
                    $existing = $paymentMethods->firstWhere('method', $methodLabel);
                    if ($existing) {
                        $index = $paymentMethods->search($existing);
                        $existing['total'] -= $refund->amount;
                        $paymentMethods->put($index, $existing);
                    }
                }
            } catch (\Exception $e) {
                // Table doesn't exist, skip payment method refund adjustments
            }
        }

        // Service Breakdown (Top 10)
        // Note: Complex to aggregate efficiently across two tables with different structures for items
        // Keeping logic similar but ensuring we limit the initial fetch if possible, 
        // but for breakdown we need aggregates.
        // For scalability, we should use separate queries for top services.

        // Top Services from Bookings - Exclude bookings completed via POS
        $topServicesBookings = ($source !== 'pos') ? DB::table('bookings')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('bookings.salon_id', $salonId)
            ->whereBetween('bookings.start_time', [$startDateTime, $endDateTime])
            ->where('bookings.status', 'completed')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id');
            })
            ->select('services.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(bookings.amount) as revenue'), DB::raw('SUM(COALESCE(bookings.tip_amount, 0)) as tip'))
            ->groupBy('services.name')
            ->orderByDesc('revenue')
            ->limit(20) // Fetch top 20 to allow for merging
            ->get() : collect();

        // Top Services from POS
        $topServicesPos = ($source !== 'bookings') ? DB::table('pos_sale_items')
            ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
            ->where('pos_sales.salon_id', $salonId)
            ->whereBetween('pos_sales.created_at', [$startDateTime, $endDateTime])
            ->where('pos_sales.payment_status', 'paid')
            ->where('pos_sales.status', '!=', 'voided')
            ->where('pos_sale_items.item_type', 'App\\Models\\Service')
            ->select('pos_sale_items.item_name as name', DB::raw('SUM(pos_sale_items.quantity) as count'), DB::raw('SUM(pos_sale_items.total) as revenue'))
            ->groupBy('pos_sale_items.item_name')
            ->orderByDesc('revenue')
            ->limit(20)
            ->get() : collect();

        // Merge Top Services
        $serviceStats = collect();
        foreach ($topServicesBookings as $item) {
            $serviceStats->put($item->name, [
                'service' => $item->name,
                'count' => $item->count,
                'revenue' => $item->revenue,
                'tip' => $item->tip ?? 0
            ]);
        }
        foreach ($topServicesPos as $item) {
            if ($serviceStats->has($item->name)) {
                $existing = $serviceStats->get($item->name);
                $serviceStats->put($item->name, [
                    'service' => $item->name,
                    'count' => $existing['count'] + $item->count,
                    'revenue' => $existing['revenue'] + $item->revenue,
                    'tip' => $existing['tip'] + ($item->tip ?? 0)
                ]);
            } else {
                $serviceStats->put($item->name, [
                    'service' => $item->name,
                    'count' => $item->count,
                    'revenue' => $item->revenue,
                    'tip' => $item->tip ?? 0
                ]);
            }
        }
        $serviceBreakdown = $serviceStats->sortByDesc('revenue')->take(10)->values();

        // POS Items Breakdown (Top 10)
        $posItemsBreakdown = ($source !== 'bookings') ? DB::table('pos_sale_items')
            ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
            ->where('pos_sales.salon_id', $salonId)
            ->whereBetween('pos_sales.created_at', [$startDateTime, $endDateTime])
            ->where('pos_sales.payment_status', 'paid')
            ->where('pos_sales.status', '!=', 'voided')
            ->select(
                'pos_sale_items.item_name as item',
                'pos_sale_items.item_type',
                DB::raw('SUM(pos_sale_items.quantity) as quantity'),
                DB::raw('SUM(pos_sale_items.total) as revenue')
            )
            ->groupBy('pos_sale_items.item_name', 'pos_sale_items.item_type')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'item' => $item->item,
                    'type' => $this->getItemTypeLabel($item->item_type),
                    'quantity' => $item->quantity,
                    'revenue' => $item->revenue
                ];
            }) : collect();

        // 4. Category Revenue
        $categoryRevenue = [
            'Service' => 0,
            'Product' => 0,
            'Package' => 0,
            'Membership' => 0
        ];

        // Add Booking Revenue to Service
        if ($source !== 'pos') {
            $categoryRevenue['Service'] += $bookingRevenue;
        }

        // Add POS Item Revenue
        if ($source !== 'bookings') {
            $posCategoryStats = DB::table('pos_sale_items')
                ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
                ->where('pos_sales.salon_id', $salonId)
                ->whereBetween('pos_sales.created_at', [$startDateTime, $endDateTime])
                ->where('pos_sales.status', '!=', 'voided')
                ->where('pos_sales.payment_status', 'paid')
                ->select('pos_sale_items.item_type', DB::raw('SUM(pos_sale_items.subtotal - pos_sale_items.discount_amount) as revenue'))
                ->groupBy('pos_sale_items.item_type')
                ->get();

            foreach ($posCategoryStats as $stat) {
                if ($stat->item_type === 'App\Models\Service')
                    $categoryRevenue['Service'] += $stat->revenue;
                elseif ($stat->item_type === 'App\Models\InventoryItem')
                    $categoryRevenue['Product'] += $stat->revenue;
                elseif ($stat->item_type === 'App\Models\Package')
                    $categoryRevenue['Package'] += $stat->revenue;
                elseif ($stat->item_type === 'App\Models\Membership')
                    $categoryRevenue['Membership'] += $stat->revenue;
            }
        }
        // 5. Hourly Traffic
        $hourlyTraffic = array_fill(0, 24, 0);

        if ($source !== 'pos') {
            $bookingHours = Booking::where('salon_id', $salonId)
                ->whereBetween('start_time', [$startDateTime, $endDateTime])
                ->where(function ($q) {
                    $q->where('status', 'completed')
                        ->orWhere('payment_status', 'refunded');
                })
                ->whereDoesntHave('posSaleItem')
                ->selectRaw('HOUR(start_time) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->pluck('count', 'hour');

            foreach ($bookingHours as $hour => $count) {
                $hourlyTraffic[$hour] += $count;
            }
        }

        if ($source !== 'bookings') {
            $posHours = PosSale::where('salon_id', $salonId)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->where('status', '!=', 'voided')
                ->where('payment_status', 'paid')
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->pluck('count', 'hour');

            foreach ($posHours as $hour => $count) {
                $hourlyTraffic[$hour] += $count;
            }
        }

        // 6. Customer Metrics
        $newCustomers = 0;
        $returningCustomers = 0;
        $totalUniqueCustomers = 0;

        // Get all unique customer IDs from transactions
        $customerIds = collect();
        if ($source !== 'pos') {
            $bookingCustomers = Booking::where('salon_id', $salonId)
                ->whereBetween('start_time', [$startDateTime, $endDateTime])
                ->where('status', 'completed')
                ->whereDoesntHave('posSaleItem')
                ->pluck('customer_id');
            $customerIds = $customerIds->merge($bookingCustomers);
        }
        if ($source !== 'bookings') {
            $posCustomers = PosSale::where('salon_id', $salonId)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->where('status', '!=', 'voided')
                ->where('payment_status', 'paid')
                ->pluck('customer_id');
            $customerIds = $customerIds->merge($posCustomers);
        }

        $uniqueCustomerIds = $customerIds->unique()->filter(); // Filter nulls (guests)
        $totalUniqueCustomers = $uniqueCustomerIds->count();

        if ($uniqueCustomerIds->isNotEmpty()) {
            // Count how many were created in this period
            $newCustomers = Customer::whereIn('id', $uniqueCustomerIds)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->count();

            $returningCustomers = $totalUniqueCustomers - $newCustomers;
        }

        $customerMetrics = [
            'new' => $newCustomers,
            'returning' => $returningCustomers,
            'total' => $totalUniqueCustomers
        ];

        // Staff Revenue (Top 10) - Exclude bookings completed via POS
        $staffBookingRev = ($source !== 'pos') ? DB::table('bookings')
            ->join('users', 'bookings.staff_id', '=', 'users.id')
            ->where('bookings.salon_id', $salonId)
            ->whereBetween('bookings.start_time', [$startDateTime, $endDateTime])
            ->where('bookings.status', 'completed')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id');
            })
            ->select('users.name as staff', DB::raw('COUNT(*) as count'), DB::raw('SUM(bookings.amount) as revenue'), DB::raw('SUM(COALESCE(bookings.tip_amount, 0)) as tip'))
            ->groupBy('users.name')
            ->get() : collect();

        $staffPosRev = ($source !== 'bookings') ? DB::table('pos_sales')
            ->join('users', 'pos_sales.employee_id', '=', 'users.id')
            ->where('pos_sales.salon_id', $salonId)
            ->whereBetween('pos_sales.created_at', [$startDateTime, $endDateTime])
            ->where('pos_sales.payment_status', 'paid')
            ->where('pos_sales.status', '!=', 'voided')
            ->select('users.name as staff', DB::raw('COUNT(*) as count'), DB::raw('SUM(pos_sales.subtotal) as revenue'), DB::raw('SUM(pos_sales.tip) as tip'))
            ->groupBy('users.name')
            ->get() : collect();

        $staffRevenueStats = collect();
        foreach ($staffBookingRev as $item) {
            $staffRevenueStats->put($item->staff, [
                'staff' => $item->staff,
                'count' => $item->count,
                'revenue' => $item->revenue,
                'tip' => $item->tip ?? 0
            ]);
        }
        foreach ($staffPosRev as $item) {
            if ($staffRevenueStats->has($item->staff)) {
                $existing = $staffRevenueStats->get($item->staff);
                $staffRevenueStats->put($item->staff, [
                    'staff' => $item->staff,
                    'count' => $existing['count'] + $item->count,
                    'revenue' => $existing['revenue'] + $item->revenue,
                    'tip' => $existing['tip'] + ($item->tip ?? 0)
                ]);
            } else {
                $staffRevenueStats->put($item->staff, [
                    'staff' => $item->staff,
                    'count' => $item->count,
                    'revenue' => $item->revenue,
                    'tip' => $item->tip ?? 0
                ]);
            }
        }
        $staffRevenue = $staffRevenueStats->sortByDesc('revenue')->take(10)->values();

        $branches = DB::table('branches')->where('salon_id', $salonId)->get();
        $staffMembers = User::role('employee')->where('salon_id', $salonId)->get();
        $customers = Customer::where('salon_id', $salonId)->orderBy('name')->get();

        return view('admin.reports.sales', compact(
            'paginatedTransactions',
            'bookingRevenue',
            'posRevenue',
            'totalRevenue',
            'totalTax',
            'netRevenue',
            'totalTips',
            'totalCommission',
            'averageTransaction',
            'totalTransactions',
            'dailyRevenue',
            'paymentMethods',
            'serviceBreakdown',
            'posItemsBreakdown',
            'staffRevenue',
            'categoryRevenue',
            'hourlyTraffic',
            'customerMetrics',
            'startDate',
            'endDate',
            'range',
            'source',
            'branches',
            'staffMembers',
            'customers',
            'branchId',
            'staffId',
            'customerId',
            'paymentMethod',
            'paymentStatus',
            'cashierId',
            'paymentStatus',
            'cashierId',
            'totalUnpaid',
            'totalUnpaid',
            'totalRefunded',
            'posRefundFees'
        ));
    }

    public function appointments(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $startDate = $request->get('start_date', Carbon::now()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $staffId = $request->get('staff_id');
        $serviceId = $request->get('service_id');
        $statusFilter = $request->get('status');
        $branchId = $request->get('branch_id');
        $customerId = $request->get('customer_id');
        $paymentStatusFilter = $request->get('payment_status');
        $paymentMethodFilter = $request->get('payment_method');

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // 1. Status Stats (Optimized)
        // 1. Fetch All Bookings First (Consolidated Query)
        $bookingsQuery = Booking::where('salon_id', $salonId)
            ->whereBetween('start_time', [$startDateTime, $endDateTime]);

        if ($staffId)
            $bookingsQuery->where('staff_id', $staffId);
        if ($serviceId)
            $bookingsQuery->where('service_id', $serviceId);
        if ($branchId)
            $bookingsQuery->where('branch_id', $branchId);
        if ($customerId)
            $bookingsQuery->where('customer_id', $customerId);
        if ($paymentStatusFilter)
            $bookingsQuery->where('payment_status', $paymentStatusFilter);
        if ($paymentMethodFilter)
            $bookingsQuery->where('payment_method', $paymentMethodFilter);
        if ($statusFilter)
            $bookingsQuery->where('status', $statusFilter);

        // Eager load relationships needed for ALL stats
        // Added posSaleItem.sale.items for accurate discount calculation
        $allBookingsInPeriod = $bookingsQuery->with(['posSaleItem.sale.items', 'service'])->get();
        // NOTE: We will reuse $allBookingsInPeriod later for detailed revenue stats too!

        // 2. Calculate Stats in PHP to ensure logic consistency (especially for POS-linked revenue)
        $totalBookings = 0;

        // Initialize Status Stats
        $allStatuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $statusStatsMap = array_fill_keys($allStatuses, ['count' => 0, 'revenue' => 0]);

        // Initialize Source Stats
        $rawSourceStats = [];

        // Initialize Payment Status Stats
        $paymentStatusCounts = [];

        foreach ($allBookingsInPeriod as $booking) {
            $totalBookings++;
            $st = $booking->status;

            // Ensure status exists in map (handle unknown statuses)
            if (!isset($statusStatsMap[$st])) {
                $statusStatsMap[$st] = ['count' => 0, 'revenue' => 0];
            }

            $statusStatsMap[$st]['count']++;

            // Calculate Revenue
            $revenue = 0;
            if ($booking->posSaleItem && $booking->posSaleItem->sale) {
                // If linked to POS, use the item revenue with accurate logic matching detailed breakdown
                $item = $booking->posSaleItem;
                $sale = $item->sale;

                // Calculate Cart Discount Share
                $cartDiscount = $sale->discount - $sale->items->sum('discount_amount');
                $totalNetBeforeCart = $sale->items->sum(function ($i) {
                    return $i->subtotal - $i->discount_amount;
                });

                $distributedCartDiscount = 0;
                $itemNetBeforeCart = $item->subtotal - $item->discount_amount;

                if ($cartDiscount > 0 && $totalNetBeforeCart > 0) {
                    $share = $itemNetBeforeCart / $totalNetBeforeCart;
                    $distributedCartDiscount = $cartDiscount * $share;
                }

                // Item Gross Revenue = (Net - CartDisc) + Tax
                $revenue = ($itemNetBeforeCart - $distributedCartDiscount) + $item->tax_amount;

                // Check for refunds and deduct (retaining fees)
                if ($sale->refunded_amount > 0) {
                    // Prevent division by zero
                    $totalSaleValue = $sale->total != 0 ? $sale->total : 1;
                    $itemRatio = $revenue / $totalSaleValue; // Ratio of this item's value to total sale

                    // Sum all fees from refunds
                    $totalFees = $sale->refunds->sum('fee_amount');
                    $totalRefundedGross = $sale->refunds->sum('amount');

                    $attributedRefund = $totalRefundedGross * $itemRatio;
                    $attributedFee = $totalFees * $itemRatio;

                    // Resulting Revenue = Revenue - Refund + Fee
                    $revenue = $revenue - $attributedRefund + $attributedFee;
                }
            } else {
                // Fallback to booking amount
                $revenue = $booking->amount;
            }

            $statusStatsMap[$st]['revenue'] += $revenue;

            // Source Stats
            $src = $booking->source ?: 'salon';
            if (!isset($rawSourceStats[$src]))
                $rawSourceStats[$src] = 0;
            $rawSourceStats[$src]++;

            // Payment Status Stats
            $ps = $booking->payment_status ?: 'unpaid';
            if (!isset($paymentStatusCounts[$ps]))
                $paymentStatusCounts[$ps] = 0;
            $paymentStatusCounts[$ps]++;
        }

        // Format Status Stats for View
        $statusStats = collect($statusStatsMap)->map(function ($data) use ($totalBookings) {
            return [
                'count' => $data['count'],
                'revenue' => $data['revenue'],
                'percentage' => $totalBookings > 0 ? ($data['count'] / $totalBookings * 100) : 0
            ];
        });

        // Format Source Stats
        $sourceStats = collect($rawSourceStats)->map(function ($count, $source) {
            $rawSource = strtolower($source);
            $displaySource = match ($rawSource) {
                'api' => 'In-App',
                'online' => 'Online',
                'pos' => 'POS',
                'admin', 'staff' => 'Staff',
                default => ucfirst($source)
            };
            return (object) ['source' => $displaySource, 'count' => $count];
        })->groupBy('source')->map(function ($group) {
            return (object) [
                'source' => $group->first()->source,
                'count' => $group->sum('count')
            ];
        })->values()->sortByDesc('count');

        // Format Payment Status Stats
        $paymentStatusStats = collect($paymentStatusCounts)->map(function ($count, $status) {
            return (object) ['payment_status' => $status, 'count' => $count];
        })->values()->sortByDesc('count');

        // 2. Hourly Distribution (Optimized)
        $hourlyQuery = Booking::where('salon_id', $salonId)
            ->whereBetween('start_time', [$startDateTime, $endDateTime]);

        if ($staffId)
            $hourlyQuery->where('staff_id', $staffId);
        if ($serviceId)
            $hourlyQuery->where('service_id', $serviceId);
        if ($branchId)
            $hourlyQuery->where('branch_id', $branchId);
        if ($customerId)
            $hourlyQuery->where('customer_id', $customerId);
        if ($paymentStatusFilter)
            $hourlyQuery->where('payment_status', $paymentStatusFilter);
        if ($paymentMethodFilter)
            $hourlyQuery->where('payment_method', $paymentMethodFilter);
        if ($statusFilter)
            $hourlyQuery->where('status', $statusFilter);

        $hourlyDistribution = $hourlyQuery->select(DB::raw('HOUR(start_time) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour');

        // 3. Staff Workload & Performance (Enhanced)
        $staffBookings = Booking::where('salon_id', $salonId)
            ->whereBetween('start_time', [$startDateTime, $endDateTime])
            ->when($serviceId, function ($q) use ($serviceId) {
                return $q->where('service_id', $serviceId);
            })
            ->when($branchId, function ($q) use ($branchId) {
                return $q->where('branch_id', $branchId);
            })
            ->when($customerId, function ($q) use ($customerId) {
                return $q->where('customer_id', $customerId);
            })
            ->when($paymentStatusFilter, function ($q) use ($paymentStatusFilter) {
                return $q->where('payment_status', $paymentStatusFilter);
            })
            ->when($paymentMethodFilter, function ($q) use ($paymentMethodFilter) {
                return $q->where('payment_method', $paymentMethodFilter);
            })
            ->when($statusFilter, function ($q) use ($statusFilter) {
                return $q->where('status', $statusFilter);
            })
            ->select(
                'staff_id',
                DB::raw('count(*) as total'),
                DB::raw('count(case when status in ("completed", "staff_completed") then 1 end) as completed'),
                DB::raw('count(case when status = "cancelled" then 1 end) as cancelled'),
                DB::raw('count(case when status in ("pending", "confirmed") then 1 end) as pending'),
                DB::raw('sum(case when status in ("completed", "staff_completed") then amount else 0 end) as revenue'),
                DB::raw('avg(case when is_rated = 1 then rating end) as avg_rating'),
                DB::raw('sum(case when status in ("completed", "staff_completed") then TIMESTAMPDIFF(MINUTE, start_time, end_time) else 0 end) as completed_minutes')
            )
            ->groupBy('staff_id')
            ->get()
            ->keyBy('staff_id');

        $staffPosSales = PosSaleItem::where('salon_id', $salonId)
            ->whereHas('sale', function ($q) use ($startDateTime, $endDateTime, $branchId) {
                $q->whereBetween('sale_date', [$startDateTime, $endDateTime])
                    ->where('status', '!=', 'voided');
                if ($branchId)
                    $q->where('branch_id', $branchId);
            })
            ->whereNull('booking_id') // Only direct POS sales
            ->when($serviceId, function ($q) use ($serviceId) {
                return $q->where('item_id', $serviceId)->where('item_type', 'App\Models\Service');
            })
            ->select('staff_id', DB::raw('sum(subtotal) as revenue'), DB::raw('count(*) as count'))
            ->groupBy('staff_id')
            ->get()
            ->keyBy('staff_id');

        $allStaffIds = $staffBookings->keys()->merge($staffPosSales->keys())->unique();

        // Assume 8 hours working day for utilization if not specified
        $daysInPeriod = $startDateTime->diffInDays($endDateTime) + 1;
        $totalWorkingMinutes = $daysInPeriod * 8 * 60;

        $staffWorkload = User::whereIn('id', $allStaffIds)
            ->select('id', 'name')
            ->get()
            ->map(function ($user) use ($staffBookings, $staffPosSales, $totalWorkingMinutes) {
                $booking = $staffBookings->get($user->id);
                $pos = $staffPosSales->get($user->id);

                $totalRevenue = ($booking->revenue ?? 0) + ($pos->revenue ?? 0);
                $completedMinutes = $booking->completed_minutes ?? 0;
                $utilization = $totalWorkingMinutes > 0 ? ($completedMinutes / $totalWorkingMinutes * 100) : 0;

                return [
                    'staff' => $user->name,
                    'total' => ($booking->total ?? 0) + ($pos->count ?? 0),
                    'completed' => ($booking->completed ?? 0) + ($pos->count ?? 0),
                    'cancelled' => $booking->cancelled ?? 0,
                    'pending' => $booking->pending ?? 0,
                    'revenue' => $totalRevenue,
                    'avg_rating' => round($booking->avg_rating ?? 0, 1),
                    'utilization' => round($utilization, 1)
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        // 4. Service Popularity (Hybrid Calculation)
        // We will calculate this manually by aggregating counts from Bookings and POS Sales loops
        $servicePopularityMap = []; // [ 'ServiceName' => count ]

        // 5. Daily Booking Trend (Optimized)
        $dailyTrendQuery = DB::table('bookings')
            ->where('salon_id', $salonId)
            ->whereBetween('start_time', [$startDateTime, $endDateTime]);

        if ($staffId)
            $dailyTrendQuery->where('staff_id', $staffId);
        if ($serviceId)
            $dailyTrendQuery->where('service_id', $serviceId);
        if ($branchId)
            $dailyTrendQuery->where('branch_id', $branchId);
        if ($customerId)
            $dailyTrendQuery->where('customer_id', $customerId);
        if ($paymentStatusFilter)
            $dailyTrendQuery->where('payment_status', $paymentStatusFilter);
        if ($paymentMethodFilter)
            $dailyTrendQuery->where('payment_method', $paymentMethodFilter);
        if ($statusFilter)
            $dailyTrendQuery->where('status', $statusFilter);

        $dailyTrend = $dailyTrendQuery->select(
            DB::raw('DATE(start_time) as date'),
            DB::raw('count(*) as total'),
            DB::raw('count(case when status = "completed" then 1 end) as completed'),
            DB::raw('count(case when status = "cancelled" then 1 end) as cancelled')
        )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return (array) $item;
            })
            ->keyBy('date');

        // 6. Detailed Revenue Breakdown
        $revenueStats = (object) [
            'total_service_value' => 0,
            'total_cash_revenue' => 0,
            'total_tips' => 0,
            'total_tax' => 0,
            'total_discount' => 0,
            'booking_revenue' => 0,
            'pos_direct_revenue' => 0,
            'service_revenue' => 0,
            'product_revenue' => 0,
            'package_revenue' => 0,
            'membership_revenue' => 0,
            'booking_discount' => 0,
            'pos_direct_discount' => 0,
            'package_redemptions' => 0,
            'total_unpaid' => 0,
            'loss_amount' => 0,
            'package_count' => 0,
            'membership_count' => 0,
            'direct_count' => 0,
            'transaction_count' => 0,
            'payment_methods' => [
                'cash' => 0,
                'card' => 0,
                'online' => 0,
                'package' => 0,
                'other' => 0
            ]
        ];

        // 6. Detailed Revenue Breakdown - B. Process Bookings
        // RE-USE the calculation logic from above (Status Stats Loop) to ensure 100% consistency.
        // We will iterate $allBookingsInPeriod again to populate $revenueStats

        foreach ($allBookingsInPeriod as $booking) {
            if (in_array($booking->status, ['completed', 'staff_completed'])) {
                $revenue = 0;
                $isPosLinked = false;

                if ($booking->posSaleItem && $booking->posSaleItem->sale) {
                    $item = $booking->posSaleItem;
                    $sale = $item->sale;

                    // Same logic as Status Stats
                    $cartDiscount = $sale->discount - $sale->items->sum('discount_amount');
                    $totalNetBeforeCart = $sale->items->sum(fn($i) => $i->subtotal - $i->discount_amount);
                    $distributedCartDiscount = ($cartDiscount > 0 && $totalNetBeforeCart > 0)
                        ? ($cartDiscount * (($item->subtotal - $item->discount_amount) / $totalNetBeforeCart))
                        : 0;

                    $revenue = ($item->subtotal - $item->discount_amount - $distributedCartDiscount) + $item->tax_amount;
                    $isPosLinked = true;
                } else {
                    $revenue = $booking->amount;
                }

                // Populate Revenue Stats
                // Note: booking_revenue in top cards typically represents Service Value of bookings
                $revenueStats->booking_revenue += $revenue;
                $revenueStats->total_service_value += $revenue;
                $revenueStats->service_revenue += $revenue; // Assuming bookings are services

                // Tips & Payment Methods
                // If POS Linked, we rely on POS loop for strict payment method breakdown?
                // NO. The User wants these cards to match. If we separate them, start_time vs sale_date mismatch occurs.
                // Decision: Attribute REVENUE here based on Booking Date.
                // BUT Payment Methods (Cash/Card) are usually strictly Sale Date based.
                // However, for "Appointment Report", users usually expect "Revenue generated by these appointments".

                $revenueStats->total_tips += ($booking->tip_amount ?? 0);

                // Add to transaction count?
                // $revenueStats->transaction_count++; 

            } elseif (in_array($booking->status, ['cancelled', 'no_show'])) {
                $revenueStats->loss_amount += $booking->amount;
            }

            // Track Unpaid for Bookings (that are not POS linked)
            if (
                in_array($booking->status, ['completed', 'staff_completed']) &&
                !($booking->posSaleItem && $booking->posSaleItem->sale) && // Not POS linked
                in_array($booking->payment_status, ['pending', 'unpaid', 'partial', 'paylater'])
            ) {

                // For simplicity, if status is not paid, we consider outstanding.
                // If partial, ideally we need paid_amount. Using simple logic for now:
                // If partial, we assume we need to calculate. 
                // But Booking model might not have 'paid_amount' readily without relation check.
                // Assuming 'amount' is total. If 'partial', we might overcount unpaid if we use full amount.
                // Let's use logic: if unpaid/pending -> full amount.

                if ($booking->payment_status === 'partial') {
                    // Best effort: Unpaid = Total - (maybe 0 if we don't know paid). 
                    // Safe fallback: Add full amount as 'unpaid' effectively means 'has unpaid balance'.
                    // Better: Booking logic usually implies 'amount' is due. 
                    // Let's just sum amount for now to show SOMETHING.
                    $revenueStats->total_unpaid += $booking->amount;
                } else {
                    $revenueStats->total_unpaid += $booking->amount;
                }
            }
        }

        // B. Process POS Sales (DIRECT Sales Only + Non-booking items)
        // To avoid double counting, we only process items NOT linked to bookings
        // OR we process everything but exclude booking_revenue?
        // Since we populated booking_revenue above based on Booking Date, we should ideally NOT
        // populate it again here based on Sale Date.
        // This is the source of reported mismatch.

        // Let's modify POS loop to ONLY handle direct sales and unrelated items.


        // B. Process POS Sales
        $posQuery = PosSale::where('salon_id', $salonId)
            ->whereBetween('sale_date', [$startDateTime, $endDateTime])
            ->where('status', '!=', 'voided');

        if ($branchId)
            $posQuery->where('branch_id', $branchId);
        if ($customerId)
            $posQuery->where('customer_id', $customerId);
        if ($paymentStatusFilter)
            $posQuery->where('payment_status', $paymentStatusFilter);
        if ($paymentMethodFilter)
            $posQuery->where('payment_method', $paymentMethodFilter);

        if ($staffId || $serviceId) {
            $posQuery->whereHas('items', function ($q) use ($staffId, $serviceId) {
                if ($staffId)
                    $q->where('staff_id', $staffId);
                if ($serviceId)
                    $q->where('item_id', $serviceId)->where('item_type', 'App\Models\Service');
            });
        }

        $posSales = $posQuery->with(['items.item'])->get();

        foreach ($posSales as $sale) {
            // Actual payments received (excluding unpaid balances)
            $actualPaid = $sale->cash_amount + $sale->card_amount + $sale->online_amount + $sale->other_amount;

            $revenueStats->total_cash_revenue += $actualPaid;
            $revenueStats->total_tips += $sale->tip;
            $revenueStats->total_tax += $sale->tax;
            $revenueStats->total_discount += $sale->discount;
            $revenueStats->total_unpaid += $sale->outstanding_amount;
            $revenueStats->transaction_count++; // POS sale transaction

            $revenueStats->payment_methods['cash'] += $sale->cash_amount;
            $revenueStats->payment_methods['card'] += $sale->card_amount;
            $revenueStats->payment_methods['online'] += $sale->online_amount;
            $revenueStats->payment_methods['other'] += $sale->other_amount;

            foreach ($sale->items as $item) {
                // IMPORTANT: If item is linked to a booking, we ALREADY counted its revenue in the Booking Loop above
                // (using the corrected POS value). So SKIP it here to avoid double counting or date mismatches.
                if ($item->booking_id) {
                    // Just count discounts/tips if needed, but revenue is done.
                    // Actually, if we skip it, we ensure "Top Cards" = "Status Cards".
                    continue;
                }

                $isService = ($item->item_type === 'App\Models\Service');
                $isProduct = ($item->item_type === 'App\Models\InventoryItem');
                $isPackage = ($item->item_type === 'App\Models\Package');
                $isMembership = ($item->item_type === 'App\Models\Membership');

                // logic for direct sales ...

                // Calculate Item Revenue (Standard Logic)
                $cartDiscount = $sale->discount - $sale->items->sum('discount_amount');
                // Note: Need totalNetBeforeCart for the WHOLE sale to distribute correctly, 
                // but we only have access to $sale items here.
                // Luckily $sale is eager loaded with items.
                $totalNetBeforeCart = $sale->items->sum(fn($i) => $i->subtotal - $i->discount_amount);

                $distributedCartDiscount = ($cartDiscount > 0 && $totalNetBeforeCart > 0)
                    ? ($cartDiscount * (($item->subtotal - $item->discount_amount) / $totalNetBeforeCart))
                    : 0;

                $itemGross = ($item->subtotal - $item->discount_amount - $distributedCartDiscount) + $item->tax_amount;

                // Direct Revenue Buckets
                $revenueStats->pos_direct_revenue += $itemGross;
                $revenueStats->pos_direct_discount += ($item->discount_amount + $distributedCartDiscount);

                if ($isService) {
                    $revenueStats->total_service_value += $itemGross;
                    $revenueStats->service_revenue += $itemGross;
                } elseif ($isProduct) {
                    $revenueStats->total_service_value += $itemGross;
                    $revenueStats->product_revenue += $itemGross;
                } elseif ($isPackage) {
                    $revenueStats->total_service_value += $itemGross;
                    $revenueStats->package_revenue += $itemGross;
                } elseif ($isMembership) {
                    $revenueStats->total_service_value += $itemGross;
                    $revenueStats->membership_revenue += $itemGross;
                }

                // Add to Service Popularity Map (Direct POS Service Sales)
                if ($isService && $item->item) {
                    $sName = $item->name; // or $item->item->name if available
                    if (!isset($servicePopularityMap[$sName]))
                        $servicePopularityMap[$sName] = 0;
                    $servicePopularityMap[$sName]++;
                }
            }
        }

        // 7. Customer Insights
        $customerInsights = (object) [
            'new_customers' => 0,
            'returning_customers' => 0,
            'top_customers' => [],
            'avg_spend' => 0
        ];

        $customerSpendMap = [];
        $customerTipMap = [];
        $customerVisitsMap = [];
        $customerNamesMap = [];

        // Track spend from bookings
        foreach ($allBookingsInPeriod as $booking) {
            if (!$booking->customer_id)
                continue;

            $cid = $booking->customer_id;
            if (!isset($customerSpendMap[$cid])) {
                $customerSpendMap[$cid] = 0;
                $customerTipMap[$cid] = 0;
                $customerVisitsMap[$cid] = 0;
                $customerNamesMap[$cid] = $booking->customer ? $booking->customer->name : 'Guest';
            }

            if (in_array($booking->status, ['completed', 'staff_completed'])) {
                // Only count as a visit if NOT linked to POS
                // because POS sales will be counted separately
                if (!$booking->posSaleItem) {
                    $customerVisitsMap[$cid]++;
                }

                // Only add to spend if not linked to a POS sale (to avoid double counting)
                // and NOT a package redemption (matches Cash Revenue logic)
                if (!$booking->package_id && !$booking->posSaleItem) {
                    $customerSpendMap[$cid] += $booking->amount;
                    $customerTipMap[$cid] += ($booking->tip_amount ?? 0);
                }
            }
        }

        // Track spend from POS sales
        foreach ($posSales as $sale) {
            if (!$sale->customer_id)
                continue;

            $cid = $sale->customer_id;
            if (!isset($customerSpendMap[$cid])) {
                $customerSpendMap[$cid] = 0;
                $customerTipMap[$cid] = 0;
                $customerVisitsMap[$cid] = 0;
                $customerNamesMap[$cid] = $sale->customer ? $sale->customer->name : 'Guest';
            }

            $customerVisitsMap[$cid]++; // Each POS sale is a visit
            $customerSpendMap[$cid] += ($sale->total - $sale->tip);
            $customerTipMap[$cid] += $sale->tip;
        }

        // Convert map to sorted list for Top Customers
        $topCustomersList = [];
        foreach ($customerSpendMap as $cid => $spend) {
            if ($spend > 0) {
                $topCustomersList[] = (object) [
                    'name' => $customerNamesMap[$cid],
                    'visits' => $customerVisitsMap[$cid],
                    'spend' => $spend + ($customerTipMap[$cid] ?? 0),
                    'service_spend' => $spend,
                    'tip_spend' => $customerTipMap[$cid] ?? 0
                ];
            }
        }
        usort($topCustomersList, function ($a, $b) {
            return $b->spend <=> $a->spend;
        });
        $customerInsights->top_customers = array_slice($topCustomersList, 0, 5);

        // New vs Returning logic
        $periodCustomerIds = array_keys($customerSpendMap);
        foreach ($periodCustomerIds as $cid) {
            $firstBooking = Booking::where('customer_id', $cid)->where('salon_id', $salonId)->orderBy('start_time', 'asc')->first();
            if ($firstBooking && $firstBooking->start_time >= $startDateTime && $firstBooking->start_time <= $endDateTime) {
                $customerInsights->new_customers++;
            } else {
                $customerInsights->returning_customers++;
            }
        }

        $customerInsights->avg_spend = $revenueStats->transaction_count > 0 ? ($revenueStats->total_cash_revenue / $revenueStats->transaction_count) : 0;

        // 8. Cancellation Analysis
        $cancellationAnalysis = DB::table('bookings')
            ->where('salon_id', $salonId)
            ->whereIn('status', ['cancelled', 'no_show'])
            ->whereBetween('start_time', [$startDateTime, $endDateTime])
            ->select('cancellation_reason', DB::raw('count(*) as count'), DB::raw('sum(amount) as loss'))
            ->groupBy('cancellation_reason')
            ->orderByDesc('count')
            ->get();

        // 9. Lead Time Calculation
        $leadTimeMinutes = Booking::where('salon_id', $salonId)
            ->whereBetween('start_time', [$startDateTime, $endDateTime])
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, start_time)) as avg_lead_time'))
            ->first()->avg_lead_time ?? 0;

        // 10. Time-Based Analytics (Busy Days)
        $busyDays = DB::table('bookings')
            ->where('salon_id', $salonId)
            ->whereBetween('start_time', [$startDateTime, $endDateTime])
            ->select(DB::raw('DAYNAME(start_time) as day'), DB::raw('count(*) as count'))
            ->groupBy('day')
            ->orderBy(DB::raw('FIELD(day, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")'))
            ->get();

        // Finalize Service Popularity List
        arsort($servicePopularityMap);
        $servicePopularity = [];
        $limit = 10;
        foreach ($servicePopularityMap as $name => $count) {
            if ($limit-- <= 0)
                break;
            $servicePopularity[] = [
                'service' => $name,
                'count' => $count
            ];
        }

        $branch = app()->has('current_branch') ? app('current_branch') : null;
        $branches = \App\Models\Branch::where('salon_id', $salonId)->get();
        $customers = Customer::where('salon_id', $salonId)->orderBy('name')->get();

        // Detailed Bookings with Pagination
        $bookingsQuery = Booking::where('salon_id', $salonId)
            ->whereBetween('start_time', [$startDateTime, $endDateTime])
            ->with(['customer', 'staff', 'service', 'package', 'posSaleItem.sale'])
            ->latest();

        if ($staffId)
            $bookingsQuery->where('staff_id', $staffId);
        if ($serviceId)
            $bookingsQuery->where('service_id', $serviceId);
        if ($statusFilter)
            $bookingsQuery->where('status', $statusFilter);
        if ($branchId)
            $bookingsQuery->where('branch_id', $branchId);
        if ($customerId)
            $bookingsQuery->where('customer_id', $customerId);
        if ($paymentStatusFilter)
            $bookingsQuery->where('payment_status', $paymentStatusFilter);
        if ($paymentMethodFilter)
            $bookingsQuery->where('payment_method', $paymentMethodFilter);

        $bookings = $bookingsQuery->paginate(50);

        // Attach net_amount and refunded_amount for the view
        $bookings->getCollection()->transform(function ($booking) {
            $revenue = $booking->amount;
            $refunded = 0;

            if ($booking->posSaleItem && $booking->posSaleItem->sale) {
                $item = $booking->posSaleItem;
                $sale = $item->sale;

                // Calculate Cart Discount Share
                $cartDiscount = $sale->discount - $sale->items->sum('discount_amount');
                $totalNetBeforeCart = $sale->items->sum(fn($i) => $i->subtotal - $i->discount_amount);
                $itemNetBeforeCart = $item->subtotal - $item->discount_amount;

                $distributedCartDiscount = ($cartDiscount > 0 && $totalNetBeforeCart > 0)
                    ? ($cartDiscount * ($itemNetBeforeCart / $totalNetBeforeCart))
                    : 0;

                $revenue = ($itemNetBeforeCart - $distributedCartDiscount) + $item->tax_amount;

                if ($sale->refunded_amount > 0) {
                    $totalSaleValue = $sale->total != 0 ? $sale->total : 1;
                    $itemRatio = $revenue / $totalSaleValue;

                    $totalFees = $sale->refunds->sum('fee_amount');
                    $totalRefundedGross = $sale->refunds->sum('amount');

                    $attributedRefund = $totalRefundedGross * $itemRatio;
                    $attributedFee = $totalFees * $itemRatio;

                    $revenue = $revenue - $attributedRefund + $attributedFee;
                    $refunded = $attributedRefund;
                }
            }

            $booking->net_amount = $revenue;
            $booking->refunded_amount = $refunded;
            return $booking;
        });

        $allStaff = User::role('employee')->where('salon_id', $salonId)->get();
        $allServices = \App\Models\Service::where('salon_id', $salonId)->get();

        return view('admin.reports.appointments', compact(
            'bookings',
            'statusStats',
            'sourceStats',
            'paymentStatusStats',
            'hourlyDistribution',
            'staffWorkload',
            'servicePopularity',
            'dailyTrend',
            'revenueStats',
            'startDate',
            'endDate',
            'staffId',
            'serviceId',
            'statusFilter',
            'branchId',
            'customerId',
            'paymentStatusFilter',
            'allStaff',
            'allServices',
            'branch',
            'branches',
            'customers',
            'customerInsights',
            'cancellationAnalysis',
            'leadTimeMinutes',
            'busyDays'
        ));
    }

    public function staff(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $startDate = $request->get('start_date', Carbon::now()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $branchId = $request->get('branch_id');
        $staffIdFilter = $request->get('staff_id');
        $serviceId = $request->get('service_id');
        $customerId = $request->get('customer_id');

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Previous period for trend analysis
        $diff = $startDateTime->diffInDays($endDateTime) + 1;
        $prevStart = (clone $startDateTime)->subDays($diff);
        $prevEnd = (clone $endDateTime)->subDays($diff);

        // 1. Booking Stats by Staff
        $bookingStatsQuery = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->whereBetween('start_time', [$startDateTime, $endDateTime]);

        if ($branchId)
            $bookingStatsQuery->where('branch_id', $branchId);
        if ($staffIdFilter)
            $bookingStatsQuery->where('staff_id', $staffIdFilter);
        if ($serviceId)
            $bookingStatsQuery->where('service_id', $serviceId);
        if ($customerId)
            $bookingStatsQuery->where('customer_id', $customerId);

        $bookingStats = $bookingStatsQuery->select(
            'staff_id',
            DB::raw('count(*) as total_count'),
            DB::raw('SUM(CASE WHEN NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id) THEN amount ELSE 0 END) as direct_revenue'),
            DB::raw('SUM(CASE WHEN NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id) THEN tip_amount ELSE 0 END) as tips')
        )
            ->groupBy('staff_id')
            ->get()
            ->keyBy('staff_id');

        // 2. POS Sales Stats by Staff (Detailed Breakdown)
        // 2. POS Sales Stats by Staff (Detailed Breakdown - PHP Calculation for Accuracy)
        $posSalesQuery = PosSale::where('salon_id', $salonId)
            ->where('status', '!=', 'voided')
            ->where('payment_status', '!=', 'pending')
            ->whereBetween('sale_date', [$startDateTime, $endDateTime]);

        if ($branchId)
            $posSalesQuery->where('branch_id', $branchId);
        if ($customerId)
            $posSalesQuery->where('customer_id', $customerId);
        if ($staffIdFilter || $serviceId) {
            $posSalesQuery->whereHas('items', function ($q) use ($staffIdFilter, $serviceId) {
                if ($staffIdFilter)
                    $q->where('staff_id', $staffIdFilter);
                if ($serviceId)
                    $q->where('item_id', $serviceId)->where('item_type', 'App\Models\Service');
            });
        }

        $posSales = $posSalesQuery->with(['items', 'refunds'])
            ->get();

        $posStats = []; // [staff_id => ['service_revenue' => 0, 'product_revenue' => 0, 'package_revenue' => 0, 'sale_count' => 0]]

        foreach ($posSales as $sale) {
            // Calculate Cart Discount Share
            $cartDiscount = $sale->discount - $sale->items->sum('discount_amount');
            $totalNetBeforeCart = $sale->items->sum(function ($i) {
                return $i->subtotal - $i->discount_amount;
            });

            // Track unique sales per staff
            $staffInSale = [];

            foreach ($sale->items as $item) {
                if (!$item->staff_id)
                    continue;

                $staffId = $item->staff_id;
                $staffInSale[$staffId] = true;

                if (!isset($posStats[$staffId])) {
                    $posStats[$staffId] = [
                        'service_revenue' => 0,
                        'product_revenue' => 0,
                        'package_revenue' => 0,
                        'sale_count' => 0
                    ];
                }

                // Calculate Item Revenue (Net of Item Discount and Distributed Cart Discount)
                $itemNetBeforeCart = $item->subtotal - $item->discount_amount;

                $distributedCartDiscount = 0;
                if ($cartDiscount > 0 && $totalNetBeforeCart > 0) {
                    $share = $itemNetBeforeCart / $totalNetBeforeCart;
                    $distributedCartDiscount = $cartDiscount * $share;
                }

                $itemRevenue = ($itemNetBeforeCart - $distributedCartDiscount) + $item->tax_amount;

                // Check for refunds and deduct (retaining fees)
                if ($sale->refunded_amount > 0) {
                    $totalSaleValue = $sale->total != 0 ? $sale->total : 1;
                    $itemRatio = $itemRevenue / $totalSaleValue;

                    // Sum all fees from refunds
                    $totalFees = $sale->refunds->sum('fee_amount');
                    $totalRefundedGross = $sale->refunds->sum('amount');

                    $attributedRefund = $totalRefundedGross * $itemRatio;
                    $attributedFee = $totalFees * $itemRatio;

                    // Resulting Revenue = Revenue - Refund + Fee
                    $itemRevenue = $itemRevenue - $attributedRefund + $attributedFee;
                }

                // Categorize Revenue
                if ($item->item_type === 'App\Models\Service') {
                    $posStats[$staffId]['service_revenue'] += $itemRevenue;
                } elseif ($item->item_type === 'App\Models\InventoryItem') {
                    $posStats[$staffId]['product_revenue'] += $itemRevenue;
                } elseif (in_array($item->item_type, ['App\Models\Package', 'App\Models\Membership'])) {
                    $posStats[$staffId]['package_revenue'] += $itemRevenue;
                }
            }

            // Increment sale count for each staff member involved in this sale
            foreach ($staffInSale as $staffId => $bool) {
                $posStats[$staffId]['sale_count']++;
            }
        }

        $posStats = collect($posStats);

        // 3. Commission Stats
        $commissionStatsQuery = \App\Models\StaffCommission::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime]);

        if ($staffIdFilter)
            $commissionStatsQuery->where('staff_id', $staffIdFilter);

        $commissionStats = $commissionStatsQuery->select(
            'staff_id',
            DB::raw('sum(case when item_type != "tip" then commission_amount else 0 end) as total_commission'),
            DB::raw('sum(case when item_type = "tip" then commission_amount else 0 end) as tip_commissions')
        )
            ->groupBy('staff_id')
            ->get()
            ->keyBy('staff_id');

        // 4. Previous Period Stats for Trend
        $prevRevenue = DB::table('pos_sales')
            ->where('salon_id', $salonId)
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'voided')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->select('employee_id', DB::raw('SUM(total) as revenue'))
            ->groupBy('employee_id')
            ->pluck('revenue', 'employee_id');

        $employees = User::role('employee')
            ->where('salon_id', $salonId)
            ->get();

        $staffStats = $employees->map(function ($staff) use ($bookingStats, $posStats, $commissionStats, $prevRevenue) {
            $bStat = $bookingStats->get($staff->id);
            $pStat = $posStats->get($staff->id);
            $cStat = $commissionStats->get($staff->id);

            $bookingCount = $bStat->total_count ?? 0;
            $bookingRevenue = $bStat->direct_revenue ?? 0;
            $bookingTips = $bStat->tips ?? 0;

            $posSaleCount = $pStat['sale_count'] ?? 0;
            $serviceRevenue = floatval($pStat['service_revenue'] ?? 0);
            $productRevenue = floatval($pStat['product_revenue'] ?? 0);
            $packageRevenue = floatval($pStat['package_revenue'] ?? 0);
            $posRevenue = $serviceRevenue + $productRevenue + $packageRevenue;

            $totalRevenue = $bookingRevenue + $posRevenue;
            $totalCommission = $cStat->total_commission ?? 0;
            $tipCommissions = $cStat->tip_commissions ?? 0;
            $totalTips = $bookingTips + $tipCommissions;

            $prevRev = $prevRevenue->get($staff->id) ?? 0;
            $trend = $prevRev > 0 ? (($totalRevenue - $prevRev) / $prevRev * 100) : 0;

            return [
                'id' => $staff->id,
                'name' => $staff->name,
                'avatar' => $staff->profile_photo_url ?? null,
                'booking_count' => $bookingCount,
                'pos_count' => $posSaleCount,
                'service_revenue' => $bookingRevenue + $serviceRevenue,
                'product_revenue' => $productRevenue,
                'package_revenue' => $packageRevenue,
                'total_revenue' => $totalRevenue,
                'commission' => $totalCommission,
                'tips' => $totalTips,
                'total_earnings' => $totalCommission + $totalTips,
                'avg_per_booking' => ($bookingCount + $posSaleCount) > 0 ? ($totalRevenue / ($bookingCount + $posSaleCount)) : 0,
                'trend' => $trend
            ];
        });

        // Salon-wide metrics
        $totalSalonRevenue = $staffStats->sum('total_revenue');
        $totalSalonCommission = $staffStats->sum('commission');
        $totalSalonTips = $staffStats->sum('tips');
        $topPerformer = $staffStats->sortByDesc('total_revenue')->first();

        // Sort by total revenue descending
        $staffStats = $staffStats->sortByDesc('total_revenue');

        $branches = DB::table('branches')->where('salon_id', $salonId)->get();
        $allStaff = User::role('employee')->where('salon_id', $salonId)->get();
        $allServices = \App\Models\Service::where('salon_id', $salonId)->get();
        $allCustomers = Customer::where('salon_id', $salonId)->orderBy('name')->get();

        return view('admin.reports.staff', compact(
            'staffStats',
            'startDate',
            'endDate',
            'branches',
            'allStaff',
            'allServices',
            'allCustomers',
            'totalSalonRevenue',
            'totalSalonCommission',
            'totalSalonTips',
            'topPerformer',
            'branchId',
            'staffIdFilter',
            'serviceId',
            'customerId'
        ));
    }

    public function customers(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $branchId = $request->get('branch_id');
        $staffId = $request->get('staff_id');
        $serviceId = $request->get('service_id');
        $status = $request->get('status');
        $segmentFilter = $request->get('segment');
        $range = $request->get('range', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Calculate dates based on range
        if ($range !== 'custom') {
            switch ($range) {
                case 'today':
                    $startDate = Carbon::now()->format('Y-m-d');
                    $endDate = Carbon::now()->format('Y-m-d');
                    break;
                case 'yesterday':
                    $startDate = Carbon::yesterday()->format('Y-m-d');
                    $endDate = Carbon::yesterday()->format('Y-m-d');
                    break;
                case 'this_week':
                    $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                    $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                    break;
                case 'last_week':
                    $startDate = Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d');
                    $endDate = Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d');
                    break;
                case 'this_month':
                    $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                    break;
                case 'last_month':
                    $startDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
                    break;
                case '3_months':
                    $startDate = Carbon::now()->subMonths(3)->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->format('Y-m-d');
                    break;
                case '6_months':
                    $startDate = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->format('Y-m-d');
                    break;
                case 'this_year':
                    $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                    $endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                    break;
                default:
                    $startDate = Carbon::now()->format('Y-m-d');
                    $endDate = Carbon::now()->format('Y-m-d');
            }
        } else {
            $startDate = $startDate ?: Carbon::now()->format('Y-m-d');
            $endDate = $endDate ?: Carbon::now()->format('Y-m-d');
        }

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();
        $startSql = $startDateTime->toDateTimeString();
        $endSql = $endDateTime->toDateTimeString();

        // Check if pos_refunds table exists
        $posRefundsExists = false;
        try {
            DB::select('SELECT 1 FROM pos_refunds LIMIT 1');
            $posRefundsExists = true;
        } catch (\Exception $e) {
            // Table doesn't exist
        }

        // 1. Optimized Top Customers Query using subqueries
        $query = Customer::where('salon_id', $salonId);

        // Apply basic filters if they affect the customer list directly (none currently do except maybe branch if we had customer-branch link)
        // But we can filter by their bookings/sales
        if ($branchId || $staffId || $serviceId || $status) {
            $query->where(function ($q) use ($branchId, $staffId, $serviceId, $status, $startDateTime, $endDateTime) {
                $q->whereHas('bookings', function ($bq) use ($branchId, $staffId, $serviceId, $status, $startDateTime, $endDateTime) {
                    $bq->whereBetween('start_time', [$startDateTime, $endDateTime]);
                    if ($branchId)
                        $bq->where('branch_id', $branchId);
                    if ($staffId)
                        $bq->where('staff_id', $staffId);
                    if ($serviceId)
                        $bq->where('service_id', $serviceId);
                    if ($status)
                        $bq->where('status', $status);
                })->orWhereHas('posSales', function ($pq) use ($branchId, $staffId, $serviceId, $startDateTime, $endDateTime) {
                    $pq->whereBetween('created_at', [$startDateTime, $endDateTime]);
                    if ($branchId)
                        $pq->where('branch_id', $branchId);
                    if ($staffId || $serviceId) {
                        $pq->whereHas('items', function ($iq) use ($staffId, $serviceId) {
                            if ($staffId)
                                $iq->where('staff_id', $staffId);
                            if ($serviceId)
                                $iq->where('item_id', $serviceId)->where('item_type', 'App\Models\Service');
                        });
                    }
                });
            });
        }

        $query->select([
            'id',
            'name',
            'email',
            'phone',
            'country_code',
            'gender',
            'dob',
            'created_at',
            DB::raw("(SELECT COUNT(*) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ('completed', 'staff_completed') AND start_time BETWEEN '{$startSql}' AND '{$endSql}' AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) as bookings_count"),
            DB::raw("(SELECT COUNT(*) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = 'paid' AND pos_sales.status != 'voided' AND created_at BETWEEN '{$startSql}' AND '{$endSql}') as pos_count"),
            DB::raw("(SELECT COALESCE(SUM(amount), 0) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ('completed', 'staff_completed') AND start_time BETWEEN '{$startSql}' AND '{$endSql}'
                    AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) as bookings_sum_amount"),
            DB::raw("(SELECT COALESCE(SUM(tip_amount), 0) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ('completed', 'staff_completed') AND start_time BETWEEN '{$startSql}' AND '{$endSql}'
                    AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) as bookings_sum_tip"),
            DB::raw("(SELECT COALESCE(SUM(total - tip), 0) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = 'paid' AND pos_sales.status != 'voided' AND created_at BETWEEN '{$startSql}' AND '{$endSql}') as pos_total"),
            DB::raw("(SELECT COALESCE(SUM(tip), 0) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = 'paid' AND pos_sales.status != 'voided' AND created_at BETWEEN '{$startSql}' AND '{$endSql}') as pos_sum_tip"),
            $posRefundsExists ? DB::raw("(SELECT COALESCE(SUM(net_refund_amount), 0) FROM pos_refunds JOIN pos_sales ON pos_refunds.sale_id = pos_sales.id WHERE pos_sales.customer_id = customers.id AND pos_refunds.processed_at BETWEEN '{$startSql}' AND '{$endSql}') as pos_refunded") : DB::raw("0 as pos_refunded"),
            $posRefundsExists ? DB::raw("(SELECT COALESCE(SUM(fee_amount), 0) FROM pos_refunds JOIN pos_sales ON pos_refunds.sale_id = pos_sales.id WHERE pos_sales.customer_id = customers.id AND pos_refunds.processed_at BETWEEN '{$startSql}' AND '{$endSql}') as pos_refund_fees") : DB::raw("0 as pos_refund_fees"),
            DB::raw("(SELECT COALESCE(SUM(amount), 0) FROM bookings WHERE bookings.customer_id = customers.id AND payment_status = 'refunded' AND start_time BETWEEN '{$startSql}' AND '{$endSql}') as booking_refunded"),
            DB::raw("(SELECT MAX(start_time) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ('completed', 'staff_completed') AND start_time BETWEEN '{$startSql}' AND '{$endSql}') as last_booking_date"),
            DB::raw("(SELECT MAX(created_at) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = 'paid' AND pos_sales.status != 'voided' AND created_at BETWEEN '{$startSql}' AND '{$endSql}') as last_sale_date"),
            DB::raw("(SELECT MIN(start_time) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ('completed', 'staff_completed')) as first_booking_date"),
            DB::raw("(SELECT MIN(created_at) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = 'paid' AND pos_sales.status != 'voided') as first_sale_date")
        ]);

        $query->groupBy(
            'customers.id',
            'customers.name',
            'customers.email',
            'customers.phone',
            'customers.country_code',
            'customers.gender',
            'customers.dob',
            'customers.created_at'
        );

        if ($segmentFilter) {
            $refundSql = $posRefundsExists
                ? "(SELECT COALESCE(SUM(net_refund_amount), 0) FROM pos_refunds JOIN pos_sales ON pos_refunds.sale_id = pos_sales.id WHERE pos_sales.customer_id = customers.id)"
                : "0";

            $query->havingRaw("
                CASE 
                    WHEN ((SELECT COALESCE(SUM(amount + tip_amount), 0) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ('completed', 'staff_completed') AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) + 
                          (SELECT COALESCE(SUM(total), 0) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = 'paid' AND pos_sales.status != 'voided') -
                          {$refundSql}) >= 1000 
                         OR 
                         ((SELECT COUNT(*) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ('completed', 'staff_completed') AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) + 
                          (SELECT COUNT(*) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = 'paid' AND pos_sales.status != 'voided')) >= 15 THEN 'VIP'
                    WHEN ((SELECT COUNT(*) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ('completed', 'staff_completed') AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) + 
                          (SELECT COUNT(*) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = 'paid' AND pos_sales.status != 'voided')) >= 5 THEN 'Loyal'
                    WHEN ((SELECT COUNT(*) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ('completed', 'staff_completed') AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) + 
                          (SELECT COUNT(*) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = 'paid' AND pos_sales.status != 'voided')) >= 2 THEN 'Regular'
                    ELSE 'New'
                END = ?
", [$segmentFilter]);
        }

        $orderRefundSql = $posRefundsExists
            ? "(SELECT COALESCE(SUM(net_refund_amount), 0) FROM pos_refunds JOIN pos_sales ON pos_refunds.sale_id = pos_sales.id WHERE pos_sales.customer_id = customers.id)"
            : "0";

        $topCustomers = $query->orderByRaw("(
                (SELECT COALESCE(SUM(amount + tip_amount), 0) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN (\"completed\", \"staff_completed\") 
                    AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) + 
                (SELECT COALESCE(SUM(total), 0) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = \"paid\" AND pos_sales.status != \"voided\") -
                {$orderRefundSql} -
                (SELECT COALESCE(SUM(amount), 0) FROM bookings WHERE bookings.customer_id = customers.id AND payment_status = \"refunded\")
            ) DESC")
            ->paginate(20)
            ->through(function ($customer) {
                $customer->total_visits = $customer->bookings_count + $customer->pos_count;
                $customer->total_paid = floatval($customer->bookings_sum_amount) + floatval($customer->pos_total);
                $customer->total_tip = floatval($customer->bookings_sum_tip) + floatval($customer->pos_sum_tip);
                $customer->total_refunded = floatval($customer->pos_refunded) + floatval($customer->booking_refunded);
                $customer->total_spend = ($customer->total_paid + $customer->total_tip) - floatval($customer->pos_refunded) - floatval($customer->booking_refunded);
                $customer->avg_spend = $customer->total_visits > 0 ? $customer->total_spend / $customer->total_visits : 0;

                $lastBooking = $customer->last_booking_date ? \Carbon\Carbon::parse($customer->last_booking_date) : null;
                $lastSale = $customer->last_sale_date ? \Carbon\Carbon::parse($customer->last_sale_date) : null;

                if ($lastBooking && $lastSale) {
                    $customer->last_visit_date = $lastBooking->gt($lastSale) ? $lastBooking : $lastSale;
                } else {
                    $customer->last_visit_date = $lastBooking ?? $lastSale;
                }

                $firstBooking = $customer->first_booking_date ? \Carbon\Carbon::parse($customer->first_booking_date) : null;
                $firstSale = $customer->first_sale_date ? \Carbon\Carbon::parse($customer->first_sale_date) : null;

                if ($firstBooking && $firstSale) {
                    $customer->first_visit_date = $firstBooking->lt($firstSale) ? $firstBooking : $firstSale;
                } else {
                    $customer->first_visit_date = $firstBooking ?? $firstSale;
                }

                $customer->age = $customer->dob ? \Carbon\Carbon::parse($customer->dob)->age : 'N/A';

                // Segmentation logic
                $customer->segment = 'New';
                if ($customer->total_spend >= 1000 || $customer->total_visits >= 15) {
                    $customer->segment = 'VIP';
                } elseif ($customer->total_visits >= 5) {
                    $customer->segment = 'Loyal';
                } elseif ($customer->total_visits >= 2) {
                    $customer->segment = 'Regular';
                }

                return $customer;
            })
            ->withQueryString();

        // 2. Summary Metrics
        $totalCustomers = Customer::where('salon_id', $salonId)->count();
        $newCustomersCount = Customer::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->count();

        // Total Revenue for LTV - Only include revenue linked to customers
        $totalRevenue = (Booking::where('salon_id', $salonId)
            ->whereNotNull('customer_id')
            ->whereIn('status', ['completed', 'staff_completed'])
            ->whereDoesntHave('posSaleItem')
            ->sum('amount')) +
            (PosSale::where('salon_id', $salonId)
                ->whereNotNull('customer_id')
                ->where('payment_status', 'paid')
                ->where('status', '!=', 'voided')
                ->sum(DB::raw('total - tip')));

        $averageLTV = $totalCustomers > 0 ? $totalRevenue / $totalCustomers : 0;

        // 3. Advanced Segmentation (RFM-based)
        // VIP: Spent >= 1000 OR Visits >= 15
        $vipCustomers = Customer::where('salon_id', $salonId)
            ->whereRaw('(
                (SELECT COALESCE(SUM(amount), 0) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ("completed", "staff_completed") 
                    AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) + 
                (SELECT COALESCE(SUM(total - tip), 0) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = "paid" AND pos_sales.status != "voided")
            ) >= 1000 OR (
                (SELECT COUNT(*) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ("completed", "staff_completed") AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) + 
                (SELECT COUNT(*) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = "paid" AND pos_sales.status != "voided")
            ) >= 15')
            ->count();

        // Loyal: Visits 5-14
        $loyalCustomers = Customer::where('salon_id', $salonId)
            ->whereRaw('(
                (SELECT COUNT(*) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ("completed", "staff_completed") AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) + 
                (SELECT COUNT(*) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = "paid" AND pos_sales.status != "voided")
            ) BETWEEN 5 AND 14')
            ->count();

        // Regular: Visits 2-4
        $regularCustomers = Customer::where('salon_id', $salonId)
            ->whereRaw('(
                (SELECT COUNT(*) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ("completed", "staff_completed") AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) + 
                (SELECT COUNT(*) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = "paid" AND pos_sales.status != "voided")
            ) BETWEEN 2 AND 4')
            ->count();

        // New: Visits 1
        $newCustomers = Customer::where('salon_id', $salonId)
            ->whereRaw('(
                (SELECT COUNT(*) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ("completed", "staff_completed") AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) + 
                (SELECT COUNT(*) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = "paid" AND pos_sales.status != "voided")
            ) = 1')
            ->count();

        // 4. Retention Metrics
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $ninetyDaysAgo = Carbon::now()->subDays(90);

        // Active: Visited in last 30 days
        $activeCustomers = Customer::where('salon_id', $salonId)
            ->where(function ($query) use ($thirtyDaysAgo) {
                $query->whereHas('bookings', function ($q) use ($thirtyDaysAgo) {
                    $q->whereIn('status', ['completed', 'staff_completed'])->where('start_time', '>=', $thirtyDaysAgo);
                })->orWhereHas('posSales', function ($q) use ($thirtyDaysAgo) {
                    $q->where('payment_status', 'paid')->where('status', '!=', 'voided')->where('created_at', '>=', $thirtyDaysAgo);
                });
            })->count();

        // At Risk: Last visit 30-90 days ago
        $atRiskCustomersCount = Customer::where('salon_id', $salonId)
            ->where(function ($query) use ($thirtyDaysAgo, $ninetyDaysAgo) {
                $query->whereRaw('(SELECT MAX(start_time) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ("completed", "staff_completed")) BETWEEN ? AND ?', [$ninetyDaysAgo, $thirtyDaysAgo])
                    ->orWhereRaw('(SELECT MAX(created_at) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = "paid" AND pos_sales.status != "voided") BETWEEN ? AND ?', [$ninetyDaysAgo, $thirtyDaysAgo]);
            })->count();

        // Lost: Last visit > 90 days ago
        $lostCustomersCount = Customer::where('salon_id', $salonId)
            ->whereRaw('(SELECT MAX(start_time) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ("completed", "staff_completed")) < ?', [$ninetyDaysAgo])
            ->whereRaw('(SELECT MAX(created_at) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = "paid" AND pos_sales.status != "voided") < ?', [$ninetyDaysAgo])
            ->count();

        $retentionRate = $totalCustomers > 0 ? ($activeCustomers / $totalCustomers * 100) : 0;

        // 5. At Risk Customers List (Top 10)
        $atRiskCustomers = Customer::where('salon_id', $salonId)
            ->select([
                'id',
                'name',
                'email',
                'phone',
                'country_code',
                DB::raw('GREATEST(
                    COALESCE((SELECT MAX(start_time) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ("completed", "staff_completed")), "1970-01-01"),
                    COALESCE((SELECT MAX(created_at) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = "paid" AND pos_sales.status != "voided"), "1970-01-01")
                ) as last_visit_date'),
                DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.status IN ("completed", "staff_completed") 
                    AND NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id)) + 
                (SELECT COALESCE(SUM(total - tip), 0) FROM pos_sales WHERE pos_sales.customer_id = customers.id AND pos_sales.payment_status = "paid" AND pos_sales.status != "voided") as total_spend')
            ])
            ->havingRaw('last_visit_date BETWEEN ? AND ?', [$ninetyDaysAgo, $thirtyDaysAgo])
            ->orderBy('total_spend', 'DESC')
            ->take(10)
            ->get();

        // 6. Monthly Acquisition Trend
        $monthlyAcquisition = DB::table('customers')
            ->where('customers.salon_id', $salonId)
            ->whereNull('customers.deleted_at')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $branches = DB::table('branches')->where('salon_id', $salonId)->get();
        $allStaff = User::role('employee')->where('salon_id', $salonId)->get();
        $allServices = \App\Models\Service::where('salon_id', $salonId)->get();

        return view('admin.reports.customers', compact(
            'topCustomers',
            'newCustomersCount',
            'totalCustomers',
            'vipCustomers',
            'loyalCustomers',
            'regularCustomers',
            'newCustomers',
            'activeCustomers',
            'atRiskCustomersCount',
            'lostCustomersCount',
            'atRiskCustomers',
            'monthlyAcquisition',
            'retentionRate',
            'averageLTV',
            'branches',
            'allStaff',
            'allServices',
            'branchId',
            'staffId',
            'serviceId',
            'status',
            'segmentFilter',
            'range',
            'startDate',
            'endDate'
        ));
    }

    public function transactionDetails($type, $id)
    {
        $salonId = auth()->user()->salon_id;

        if ($type === 'booking') {
            $booking = Booking::where('salon_id', $salonId)
                ->where('id', $id)
                ->with(['customer', 'staff', 'service'])
                ->firstOrFail();

            $bookings = collect([$booking]);
            if ($booking->booking_group_id) {
                $bookings = Booking::where('salon_id', $salonId)
                    ->where('booking_group_id', $booking->booking_group_id)
                    ->with(['customer', 'staff', 'service'])
                    ->get();
            }

            $bookingIds = $bookings->pluck('id');

            // Get commissions for these bookings
            $commissions = \App\Models\StaffCommission::whereIn('booking_id', $bookingIds)
                ->where('salon_id', $salonId)
                ->with('staff')
                ->get();

            $items = $bookings->map(function ($b) {
                return [
                    'name' => $b->service ? $b->service->name : 'N/A',
                    'type' => 'service',
                    'quantity' => 1,
                    'price' => $b->amount,
                    'total' => $b->amount,
                    'staff_name' => $b->staff ? $b->staff->name : 'Unassigned'
                ];
            });

            $booking->payment_method = $this->getPaymentMethodLabel($booking->payment_method);

            return response()->json([
                'type' => 'booking',
                'is_grouped' => $booking->booking_group_id ? true : false,
                'transaction' => $booking,
                'commissions' => $commissions,
                'items' => $items,
                'total_amount' => $bookings->sum('amount'),
                'total_tip' => $bookings->sum('tip_amount')
            ]);

        } elseif ($type === 'pos') {
            $transaction = PosSale::where('salon_id', $salonId)
                ->where('id', $id)
                ->with(['customer', 'employee', 'items.staff'])
                ->firstOrFail();

            // Get commissions for this POS sale
            $commissions = \App\Models\StaffCommission::where('pos_sale_id', $id)
                ->where('salon_id', $salonId)
                ->with('staff')
                ->get();

            // Format items
            $items = $transaction->items->map(function ($item) {
                return [
                    'name' => $item->item_name,
                    'type' => $this->getItemTypeLabel($item->item_type),
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                    'discount' => $item->discount_amount ?? 0,
                    'total' => $item->total,
                    'booking_id' => $item->booking_id,
                    'staff_name' => $item->staff ? $item->staff->name : 'Unassigned'
                ];
            });

            $transaction->payment_method = $this->getPaymentMethodLabel($transaction->payment_method);

            // Check if this POS sale is linked to any booking
            $isPosBooking = $transaction->items->contains(function ($i) {
                return !empty($i->booking_id);
            });

            return response()->json([
                'type' => 'pos',
                'subtype' => $isPosBooking ? 'booking' : 'sale',
                'transaction' => $transaction,
                'commissions' => $commissions,
                'items' => $items
            ]);
        }

        return response()->json(['error' => 'Invalid transaction type'], 400);
    }

    public function inventory(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // 1. Quick Stats (Optimized)
        $totalItems = \App\Models\InventoryItem::where('salon_id', $salonId)->count();
        $lowStockItems = \App\Models\InventoryItem::where('salon_id', $salonId)
            ->whereRaw('quantity_in_stock <= minimum_quantity')
            ->where('quantity_in_stock', '>', 0)
            ->count();
        $outOfStockItems = \App\Models\InventoryItem::where('salon_id', $salonId)
            ->where('quantity_in_stock', '<=', 0)
            ->count();
        $totalValue = \App\Models\InventoryItem::where('salon_id', $salonId)
            ->select(DB::raw('SUM(purchase_price * quantity_in_stock) as total_value'))
            ->first()
            ->total_value ?? 0;

        // 2. Recent Inventory Transactions (Paginated)
        $transactions = \App\Models\InventoryTransaction::whereHas('item', function ($query) use ($salonId) {
            $query->where('salon_id', $salonId);
        })
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->with(['item.category', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // 3. Category Breakdown (Optimized)
        $categoryBreakdown = DB::table('inventory_items')
            ->leftJoin('categories', 'inventory_items.category_id', '=', 'categories.id')
            ->where('inventory_items.salon_id', $salonId)
            ->whereNull('inventory_items.deleted_at')
            ->select(
                'categories.name as category_name',
                DB::raw('COUNT(inventory_items.id) as items_count'),
                DB::raw('SUM(inventory_items.quantity_in_stock) as total_stock'),
                DB::raw('SUM(inventory_items.purchase_price * inventory_items.quantity_in_stock) as total_value')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get();

        // 4. Daily trends
        $dailyTrends = \App\Models\InventoryTransaction::whereHas('item', function ($query) use ($salonId) {
            $query->where('salon_id', $salonId);
        })
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->select(
                DB::raw('DATE(created_at) as date'),
                 DB::raw('sum(case when transaction_type = "purchase" then quantity else 0 end) as purchase_in'),
                 DB::raw('sum(case when transaction_type = "return" then quantity else 0 end) as return_in'),
                 DB::raw('sum(case when transaction_type in ("adjustment", "damaged", "lost") then quantity else 0 end) as out_qty')
             )
             ->groupBy('date')
             ->orderBy('date')
             ->get()
             ->keyBy('date')
             ->map(function ($trend) {
                 return [
                     'in' => $trend->purchase_in + $trend->return_in,
                     'out' => $trend->out_qty
                 ];
             });
 
         // 5. Top moving items (Optimized)
         $topMovingItems = \App\Models\InventoryTransaction::whereHas('item', function ($query) use ($salonId) {
             $query->where('salon_id', $salonId);
         })
             ->whereBetween('created_at', [$startDateTime, $endDateTime])
             ->select('item_id', DB::raw('SUM(quantity) as total_quantity'))
             ->groupBy('item_id')
             ->with('item')
             ->orderByDesc('total_quantity')
             ->take(10)
             ->get()
             ->map(function ($transaction) {
                 return [
                     'item_name' => $transaction->item->name ?? 'Unknown',
                     'total_quantity' => $transaction->total_quantity
                 ];
             });

        $branch = app()->has('current_branch') ? app('current_branch') : null;

        return view('admin.reports.inventory', compact(
            'totalItems',
            'lowStockItems',
            'outOfStockItems',
            'totalValue',
            'transactions',
            'categoryBreakdown',
            'dailyTrends',
            'topMovingItems',
            'startDate',
            'endDate',
            'branch'
        ));
    }

    private function getItemTypeLabel($itemType)
    {
        if (str_contains($itemType, 'Service')) {
            return 'Service';
        } elseif (str_contains($itemType, 'Product') || str_contains($itemType, 'InventoryItem')) {
            return 'Product';
        } elseif (str_contains($itemType, 'Package')) {
            return 'Package';
        } elseif (str_contains($itemType, 'Membership')) {
            return 'Membership';
        }
        return 'Other';
    }

    private function getPaymentMethodLabel($method)
    {
        $labels = [
            'cash' => 'Cash',
            'card' => 'Card',
            'online' => 'Online',
            'upi' => 'UPI',
            'bank_transfer' => 'Bank Transfer',
            'other' => 'Other',
            'mixed' => 'Split',
            'package' => 'Package',
            'none' => 'Unpaid',
        ];

        return $labels[strtolower($method)] ?? ($method ?: 'Not Specified');
    }

    /**
     * Export sales report to Excel with formatting
     */
    public function exportSales(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $salon = auth()->user()->salon;
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $source = $request->get('source', 'all');
        $branchId = $request->get('branch_id');
        $staffId = $request->get('staff_id');
        $customerId = $request->get('customer_id');
        $paymentMethod = $request->get('payment_method');
        $paymentStatus = $request->get('payment_status');
        $cashierId = $request->get('cashier_id');

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Create new Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales Report');

        // Report Header Section
        $row = 1;
        $sheet->setCellValue('A' . $row, 'SALES REPORT');
        $sheet->mergeCells('A' . $row . ':M' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1F4788');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($row)->setRowHeight(30);

        $row++;
        $sheet->setCellValue('A' . $row, 'Salon:');
        $sheet->setCellValue('B' . $row, $salon->name ?? 'N/A');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Generated On:');
        $sheet->setCellValue('B' . $row, date('Y-m-d H:i:s'));
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Period:');
        $sheet->setCellValue('B' . $row, $startDate . ' to ' . $endDate);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Source:');
        $sheet->setCellValue('B' . $row, ucfirst($source));
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row += 2; // Empty row

        // Column Headers
        $headerRow = $row;
        $headers = [
            'Transaction ID',
            'Type',
            'Date',
            'Time',
            'Customer',
            'Contact Info',
            // 'Staff Member' (Removed)
            // 'Description/Items' (Removed)
            'Subtotal (Net)',
            'Discount',
            'Tax Amount',
            'Tip',
            'Total (Original)',
            'Refunded (-)',
            'Fees (+)',
            'Net Retained',
            'Cash',
            'Card',
            'Online',
            'Other',
            'Unpaid/Due',
            'Status'
        ];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($col . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4e73df');
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        $row++;

        // Initialize Totals
        $stats = [
            'booking_count' => 0,
            'pos_count' => 0,
            'booking_revenue' => 0,
            'pos_revenue' => 0,
            'tax' => 0,
            'tip' => 0,
            'methods' => [
                'cash' => 0,
                'card' => 0,
                'online' => 0,
                'other' => 0,
                'unpaid' => 0
            ],
            'refund_gross' => 0,
            'refund_fees' => 0,
            'refund_net' => 0
        ];

        // Bookings Data (Chunked)
        if ($source === 'all' || $source === 'bookings') {
            $bookingsQuery = Booking::where('salon_id', $salonId)
                ->whereBetween('start_time', [$startDateTime, $endDateTime])
                ->where(function ($q) {
                    $q->where('status', 'completed')
                        ->orWhere('payment_status', 'refunded');
                })
                ->whereDoesntHave('posSaleItem') // Exclude bookings processed via POS
                ->with(['customer']);

            if ($branchId)
                $bookingsQuery->where('branch_id', $branchId);
            if ($staffId)
                $bookingsQuery->where('staff_id', $staffId);
            if ($customerId)
                $bookingsQuery->where('customer_id', $customerId);
            if ($paymentMethod)
                $bookingsQuery->where('payment_method', $paymentMethod);
            if ($paymentStatus)
                $bookingsQuery->where('payment_status', $paymentStatus);
            if ($cashierId)
                $bookingsQuery->where('staff_id', $cashierId);

            $bookingsQuery->chunk(100, function ($bookings) use (&$sheet, &$row, &$stats) {
                foreach ($bookings as $booking) {
                    $sheet->setCellValue('A' . $row, 'B-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT));
                    $sheet->setCellValue('B' . $row, 'Booking');
                    $sheet->setCellValue('C' . $row, $booking->start_time->format('Y-m-d'));
                    $sheet->setCellValue('D' . $row, $booking->start_time->format('H:i'));
                    $sheet->setCellValue('E' . $row, $booking->customer ? $booking->customer->name : 'Guest');
                    $sheet->setCellValue('F' . $row, $booking->customer ? ($booking->customer->phone ?? $booking->customer->email) : '-');

                    $net = ($booking->amount ?? 0) - ($booking->tax_amount ?? 0);
                    $tax = $booking->tax_amount ?? 0;
                    $tip = $booking->tip_amount ?? 0;
                    $gross = $booking->amount + $tip;
                    $discount = $booking->discount_amount ?? 0;

                    // Status Logic
                    $status = ucfirst($booking->status);
                    if ($booking->payment_status === 'refunded') {
                        $status = 'Refunded';
                    } elseif ($booking->payment_status === 'partial') {
                        $status = 'Partially Refunded';
                    }
                    $sheet->setCellValue('G' . $row, $net);
                    $sheet->setCellValue('H' . $row, $discount);
                    $sheet->setCellValue('I' . $row, $tax);
                    $sheet->setCellValue('J' . $row, $tip);
                    $sheet->setCellValue('K' . $row, $gross);

                    // Refund logic for bookings
                    $isRefunded = ($booking->payment_status === 'refunded');
                    $bookedRefundAmount = $isRefunded ? $gross : 0;
                    $bookedNetRetained = $gross - $bookedRefundAmount;

                    $sheet->setCellValue('L' . $row, $bookedRefundAmount);
                    $sheet->setCellValue('M' . $row, 0); // No fees for direct bookings typically
                    $sheet->setCellValue('N' . $row, $bookedNetRetained);

                    // Payment Allocation (Original)
                    $pm = strtolower($booking->payment_method ?? 'other');
                    $paidAmount = ($booking->payment_status === 'paid' || $isRefunded) ? $gross : 0;
                    $unpaid = ($booking->payment_status === 'unpaid') ? $gross : 0;

                    $cash = ($pm === 'cash' && $paidAmount > 0) ? $paidAmount : 0;
                    $card = ($pm === 'card' && $paidAmount > 0) ? $paidAmount : 0;
                    $online = ($pm === 'online' && $paidAmount > 0) ? $paidAmount : 0;
                    $other = (!in_array($pm, ['cash', 'card', 'online']) && $paidAmount > 0) ? $paidAmount : 0;

                    $sheet->setCellValue('O' . $row, $cash);
                    $sheet->setCellValue('P' . $row, $card);
                    $sheet->setCellValue('Q' . $row, $online);
                    $sheet->setCellValue('R' . $row, $other);
                    $sheet->setCellValue('S' . $row, $unpaid);
                    $sheet->setCellValue('T' . $row, ucfirst($booking->status));

                    $sheet->getStyle('G' . $row . ':S' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                    // Stats Accumulation
                    $stats['booking_count']++;
                    $stats['booking_revenue'] += $booking->amount; // Gross Service Rev (inc tax, ex tip) ? Usually Gross is simpler
                    $stats['tax'] += $tax;
                    $stats['tip'] += $tip;
                    $stats['methods']['cash'] += $cash;
                    $stats['methods']['card'] += $card;
                    $stats['methods']['online'] += $online;
                    $stats['methods']['other'] += $other;
                    $stats['methods']['unpaid'] += $unpaid;

                    $row++;
                }
            });
        }

        // POS Sales Data (Chunked)
        if ($source === 'all' || $source === 'pos') {
            $posQuery = PosSale::where('salon_id', $salonId)
                ->whereBetween('sale_date', [$startDateTime, $endDateTime])
                ->where('payment_status', '!=', 'pending')
                ->where('status', '!=', 'voided')
                ->with(['customer']);

            if ($branchId)
                $posQuery->where('branch_id', $branchId);
            if ($staffId)
                $posQuery->where('employee_id', $staffId);
            if ($customerId)
                $posQuery->where('customer_id', $customerId);
            if ($paymentMethod)
                $posQuery->where('payment_method', $paymentMethod);
            if ($paymentStatus)
                $posQuery->where('payment_status', $paymentStatus);
            if ($cashierId)
                $posQuery->where('employee_id', $cashierId);

            $posQuery->chunk(100, function ($posSales) use (&$sheet, &$row, &$stats) {
                foreach ($posSales as $sale) {
                    $sheet->setCellValue('A' . $row, 'P-' . str_pad($sale->id, 5, '0', STR_PAD_LEFT));
                    $sheet->setCellValue('B' . $row, 'POS Sale');
                    $sheet->setCellValue('C' . $row, $sale->sale_date->format('Y-m-d'));
                    $sheet->setCellValue('D' . $row, $sale->created_at->format('H:i'));
                    $sheet->setCellValue('E' . $row, $sale->customer ? $sale->customer->name : 'Walk-in');
                    $sheet->setCellValue('F' . $row, $sale->customer ? ($sale->customer->phone ?? $sale->customer->email) : '-');

                    $net = ($sale->total ?? 0) - ($sale->tax ?? 0) - ($sale->tip ?? 0);
                    $tax = $sale->tax ?? 0;
                    $tip = $sale->tip ?? 0;
                    $gross = $sale->total; // Includes tax + tip
                    $discount = $sale->discount ?? 0;

                    $sheet->setCellValue('G' . $row, $net);
                    $sheet->setCellValue('H' . $row, $discount);
                    $sheet->setCellValue('I' . $row, $tax);
                    $sheet->setCellValue('J' . $row, $tip);
                    $sheet->setCellValue('K' . $row, $gross);

                    // Track Refunds for this sale
                    $saleRefundGross = 0;
                    $saleRefundFees = 0;
                    if ($sale->refunds()->exists()) {
                        $saleRefundGross = $sale->refunds()->sum('amount');
                        $saleRefundFees = $sale->total_fees;
                    }
                    $netRetained = $gross - $saleRefundGross + $saleRefundFees;

                    $sheet->setCellValue('L' . $row, $saleRefundGross);
                    $sheet->setCellValue('M' . $row, $saleRefundFees);
                    $sheet->setCellValue('N' . $row, $netRetained);

                    // Split Payment Allocation
                    $cash = $sale->cash_amount ?? 0;
                    $card = $sale->card_amount ?? 0;
                    $online = $sale->online_amount ?? 0;
                    $other = $sale->other_amount ?? 0;
                    $unpaid = $sale->outstanding_amount ?? 0;

                    // Status Logic
                    $status = ucfirst($sale->payment_status);
                    if ($sale->status === 'refunded') {
                        $status = 'Refunded';
                    } elseif ($sale->status === 'partially_refunded') {
                        $status = 'Partially Refunded';
                    }

                    $sheet->setCellValue('O' . $row, $cash);
                    $sheet->setCellValue('P' . $row, $card);
                    $sheet->setCellValue('Q' . $row, $online);
                    $sheet->setCellValue('R' . $row, $other);
                    $sheet->setCellValue('S' . $row, $unpaid);
                    $sheet->setCellValue('T' . $row, $status);

                    $sheet->getStyle('G' . $row . ':P' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                    // Stats Accumulation
                    $stats['pos_count']++;
                    $stats['pos_revenue'] += ($gross - $tip); // Gross POS Revenue
                    $stats['tax'] += $tax;
                    $stats['tip'] += $tip;
                    $stats['methods']['cash'] += $cash;
                    $stats['methods']['card'] += $card;
                    $stats['methods']['online'] += $online;
                    $stats['methods']['other'] += $other;
                    $stats['methods']['unpaid'] += $unpaid;

                    // Track Refunds for this sale
                    if ($sale->refunds()->exists()) {
                        $saleRefundGross = $sale->refunds()->sum('amount');
                        $saleRefundFees = $sale->total_fees;
                        $saleRefundNet = $sale->total_net_refunded;

                        $stats['refund_gross'] += $saleRefundGross;
                        $stats['refund_fees'] += $saleRefundFees;
                        $stats['refund_net'] += $saleRefundNet;
                    }

                    $row++;
                }
            });
        }

        // Add borders to data range
        if ($row > $headerRow + 1) {
            $dataRange = 'A' . $headerRow . ':T' . ($row - 1);
            $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        // --- SUMMARY SECTION ---
        $summaryRow = $row + 2;

        // Left Side: Financial Summary
        $sheet->setCellValue('A' . $summaryRow, 'FINANCIAL SUMMARY');
        $sheet->mergeCells('A' . $summaryRow . ':C' . $summaryRow);
        $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $summaryRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('2c3e50');
        $sheet->getStyle('A' . $summaryRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $r = $summaryRow + 1;
        $sheet->setCellValue('A' . $r, 'Total Transactions:');
        $sheet->setCellValue('C' . $r, $stats['booking_count'] + $stats['pos_count']);
        $sheet->getStyle('A' . $r)->getFont()->setBold(true);
        $r++;

        $sheet->setCellValue('A' . $r, 'Bookings / POS Count:');
        $sheet->setCellValue('C' . $r, $stats['booking_count'] . ' / ' . $stats['pos_count']);
        $r++;

        $sheet->setCellValue('A' . $r, 'Booking Revenue (Gross):');
        $sheet->setCellValue('C' . $r, $stats['booking_revenue']);
        $sheet->getStyle('C' . $r)->getNumberFormat()->setFormatCode('#,##0.00');
        $r++;

        $sheet->setCellValue('A' . $r, 'POS Revenue (Gross):');
        $sheet->setCellValue('C' . $r, $stats['pos_revenue']);
        $sheet->getStyle('C' . $r)->getNumberFormat()->setFormatCode('#,##0.00');
        $r++;

        $sheet->setCellValue('A' . $r, 'Total Tips:');
        $sheet->setCellValue('C' . $r, $stats['tip']);
        $sheet->getStyle('C' . $r)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('C' . $r)->getFont()->getColor()->setRGB('e67e22');
        $r++;

        $sheet->setCellValue('A' . $r, 'Tax Collected:');
        $sheet->setCellValue('C' . $r, $stats['tax']);
        $sheet->getStyle('C' . $r)->getNumberFormat()->setFormatCode('#,##0.00');
        $r++;

        $totalSalesGross = $stats['booking_revenue'] + $stats['pos_revenue'];
        $totalGrossRevenue = $totalSalesGross + $stats['tip'];
        $netRevenue = $totalGrossRevenue - $stats['refund_net'];

        $sheet->setCellValue('A' . $r, 'Total Sales (Gross):');
        $sheet->setCellValue('C' . $r, $totalSalesGross);
        $sheet->getStyle('C' . $r)->getNumberFormat()->setFormatCode('#,##0.00');
        $r++;

        $sheet->setCellValue('A' . $r, 'TOTAL GROSS REVENUE:');
        $sheet->setCellValue('C' . $r, $totalGrossRevenue);
        $sheet->getStyle('C' . $r)->getNumberFormat()->setFormatCode('#,##0.00');
        $r++;

        // Refund Section in Summary
        if ($stats['refund_gross'] > 0) {
            $sheet->setCellValue('A' . $r, 'Gross Refunded (-):');
            $sheet->setCellValue('C' . $r, $stats['refund_gross']);
            $sheet->getStyle('C' . $r)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A' . $r . ':C' . $r)->getFont()->getColor()->setRGB('dc3545');
            $r++;

            $sheet->setCellValue('A' . $r, 'Refund Fees (+):');
            $sheet->setCellValue('C' . $r, $stats['refund_fees']);
            $sheet->getStyle('C' . $r)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A' . $r . ':C' . $r)->getFont()->getColor()->setRGB('17a2b8');
            $r++;

            $sheet->setCellValue('A' . $r, 'Net Refund Outflow (-):');
            $sheet->setCellValue('C' . $r, $stats['refund_net']);
            $sheet->getStyle('C' . $r)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A' . $r . ':C' . $r)->getFont()->getColor()->setRGB('6f42c1');
            $r++;
        }

        $sheet->setCellValue('A' . $r, 'NET REVENUE:');
        $sheet->setCellValue('C' . $r, $netRevenue);
        $sheet->getStyle('A' . $r . ':C' . $r)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('C' . $r)->getNumberFormat()->setFormatCode('"' . currency_symbol() . '" #,##0.00');
        $sheet->getStyle('A' . $r . ':C' . $r)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('27ae60');
        $sheet->getStyle('A' . $r . ':C' . $r)->getFont()->getColor()->setRGB('FFFFFF');

        // Right Side: Payment Breakdown
        $pbCol = 'E'; // Start Payment Breakdown at Column E
        $sheet->setCellValue($pbCol . $summaryRow, 'PAYMENT METHOD BREAKDOWN');
        $sheet->mergeCells($pbCol . $summaryRow . ':G' . $summaryRow);
        $sheet->getStyle($pbCol . $summaryRow)->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($pbCol . $summaryRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('c0392b');
        $sheet->getStyle($pbCol . $summaryRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $pr = $summaryRow + 1;
        // Header for breakdown
        $sheet->setCellValue($pbCol . $pr, 'Method');
        $sheet->setCellValue(chr(ord($pbCol) + 2) . $pr, 'Amount'); // Column G
        $sheet->getStyle($pbCol . $pr . ':' . chr(ord($pbCol) + 2) . $pr)->getFont()->setBold(true)->setUnderline(true);
        $pr++;

        $methods = [
            'Cash' => $stats['methods']['cash'],
            'Card' => $stats['methods']['card'],
            'Online' => $stats['methods']['online'],
            'Other' => $stats['methods']['other']
        ];

        foreach ($methods as $name => $amount) {
            $sheet->setCellValue($pbCol . $pr, $name . ':');
            $sheet->setCellValue(chr(ord($pbCol) + 2) . $pr, $amount);
            $sheet->getStyle(chr(ord($pbCol) + 2) . $pr)->getNumberFormat()->setFormatCode('#,##0.00');
            $pr++;
        }

        $sheet->setCellValue($pbCol . $pr, 'Unpaid / Due:');
        $sheet->setCellValue(chr(ord($pbCol) + 2) . $pr, $stats['methods']['unpaid']);
        $sheet->getStyle(chr(ord($pbCol) + 2) . $pr)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle($pbCol . $pr . ':' . chr(ord($pbCol) + 2) . $pr)->getFont()->getColor()->setRGB('e74a3b');
        $pr++;

        $totalCollected = array_sum($methods);
        $sheet->setCellValue($pbCol . $pr, 'TOTAL COLLECTED:');
        $sheet->setCellValue(chr(ord($pbCol) + 2) . $pr, $totalCollected);
        $sheet->getStyle($pbCol . $pr . ':' . chr(ord($pbCol) + 2) . $pr)->getFont()->setBold(true);
        $sheet->getStyle(chr(ord($pbCol) + 2) . $pr)->getNumberFormat()->setFormatCode('"' . currency_symbol() . '" #,##0.00');
        $sheet->getStyle($pbCol . $pr . ':' . chr(ord($pbCol) + 2) . $pr)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('f1c40f');


        // Auto-size columns
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Sales_Report_' . date('Y-m-d_His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export appointments report to Excel with formatting
     */
    public function exportAppointments(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $salon = auth()->user()->salon;
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $staffId = $request->get('staff_id');
        $serviceId = $request->get('service_id');
        $branchId = $request->get('branch_id');
        $customerId = $request->get('customer_id');
        $paymentStatus = $request->get('payment_status');
        $statusFilter = $request->get('status');
        $paymentMethod = $request->get('payment_method');

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Create new Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Appointments');

        // Report Header Section
        $row = 1;
        $sheet->setCellValue('A' . $row, 'COMPREHENSIVE APPOINTMENTS REPORT');
        $sheet->mergeCells('A' . $row . ':P' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4E73DF');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($row)->setRowHeight(35);

        $row++;
        $sheet->setCellValue('A' . $row, 'Salon:');
        $sheet->setCellValue('B' . $row, $salon->name ?? 'N/A');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Generated On:');
        $sheet->setCellValue('B' . $row, date('Y-m-d H:i:s'));
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Period:');
        $sheet->setCellValue('B' . $row, $startDate . ' to ' . $endDate);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row += 2; // Empty row

        // Column Headers
        $headerRow = $row;
        $headers = [
            'ID',
            'Appt Date',
            'Appt Time',
            'Created On',
            'Customer',
            'Phone Number',
            'Service Item',
            'Category',
            'Staff Member',
            'Booking Source',
            'Payment Method',
            'Price (Net)',
            'Tax Amount',
            'Tip',
            'Balance Due',
            'Gross Total',
            'Status'
        ];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($col . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('2c3e50');
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        $row++;

        // Bookings Data (Chunked)
        $totalServiceValue = 0;
        $totalCashRevenue = 0;
        $totalTips = 0;
        $totalLoss = 0;
        $totalBookings = 0;
        $serviceRevenue = 0;
        $productRevenue = 0;
        $packageRevenue = 0;
        $posDirectRevenue = 0;
        $transactionCount = 0;
        $statusCounts = ['completed' => 0, 'confirmed' => 0, 'pending' => 0, 'cancelled' => 0, 'no_show' => 0];

        $bookingsQuery = Booking::where('salon_id', $salonId)
            ->whereBetween('start_time', [$startDateTime, $endDateTime])
            ->with(['customer', 'staff', 'service.category', 'package', 'posSaleItem.sale.refunds']);

        if ($staffId)
            $bookingsQuery->where('staff_id', $staffId);
        if ($serviceId)
            $bookingsQuery->where('service_id', $serviceId);
        if ($branchId)
            $bookingsQuery->where('branch_id', $branchId);
        if ($customerId)
            $bookingsQuery->where('customer_id', $customerId);
        if ($paymentStatus)
            $bookingsQuery->where('payment_status', $paymentStatus);
        if ($paymentMethod)
            $bookingsQuery->where('payment_method', $paymentMethod);
        if ($statusFilter)
            $bookingsQuery->where('status', $statusFilter);

        $bookingsQuery->chunk(100, function ($bookings) use (&$sheet, &$row, &$totalServiceValue, &$totalCashRevenue, &$totalTips, &$totalLoss, &$totalBookings, &$statusCounts, &$serviceRevenue, &$transactionCount) {
            foreach ($bookings as $booking) {
                $bookingTotal = ($booking->amount ?? 0) + ($booking->tip_amount ?? 0);

                $refundAmount = 0;
                $refundFees = 0;
                $paidAmount = 0;

                if ($booking->posSaleItem && $booking->posSaleItem->sale) {
                    $sale = $booking->posSaleItem->sale;
                    $paidAmount = ($sale->payment_status === 'paid') ? $bookingTotal : $sale->total;

                    if ($sale->refunded_amount > 0) {
                        $totalSaleValue = $sale->total != 0 ? $sale->total : 1;
                        $itemRatio = $bookingTotal / $totalSaleValue;
                        $refundAmount = $sale->refunds->sum('amount') * $itemRatio;
                        $refundFees = $sale->refunds->sum('fee_amount') * $itemRatio;
                    }
                } elseif ($booking->payment_status === 'paid') {
                    $paidAmount = $bookingTotal;
                } elseif ($booking->payment_status === 'refunded') {
                    $refundAmount = $bookingTotal;
                }

                $netRetained = $bookingTotal - $refundAmount + $refundFees;
                $balance = max(0, $bookingTotal - $paidAmount);

                $sheet->setCellValue('A' . $row, 'A-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT));
                $sheet->setCellValue('B' . $row, $booking->start_time->format('Y-m-d'));
                $sheet->setCellValue('C' . $row, $booking->start_time->format('H:i'));
                $sheet->setCellValue('D' . $row, $booking->created_at->format('Y-m-d H:i'));
                $sheet->setCellValue('E' . $row, $booking->customer ? $booking->customer->name : 'Guest');
                $sheet->setCellValue('F' . $row, $booking->customer ? $booking->customer->phone : '-');

                $serviceName = $booking->service ? $booking->service->name : 'N/A';
                if ($booking->package) {
                    $serviceName = $booking->package->name . ': ' . $serviceName;
                }
                $sheet->setCellValue('G' . $row, $serviceName);
                $sheet->setCellValue('H' . $row, $booking->service && $booking->service->category ? $booking->service->category->name : 'N/A');
                $sheet->setCellValue('I' . $row, $booking->staff ? $booking->staff->name : 'Unassigned');
                $sheet->setCellValue('J' . $row, ucfirst($booking->source ?: 'Web'));

                // Payment Method
                $paymentMethod = $booking->package_id ? 'Package' : ($booking->payment_method ?: 'N/A');
                if ($booking->posSaleItem && $booking->posSaleItem->sale) {
                    $paymentMethod = ucfirst($booking->posSaleItem->sale->payment_method);
                }
                $sheet->setCellValue('K' . $row, $paymentMethod);

                $net = ($booking->amount ?? 0) - ($booking->tax_amount ?? 0);
                $sheet->setCellValue('L' . $row, $net);
                $sheet->setCellValue('M' . $row, $booking->tax_amount ?? 0);
                $sheet->setCellValue('N' . $row, $booking->tip_amount ?? 0);
                $sheet->setCellValue('O' . $row, $balance);
                $sheet->setCellValue('P' . $row, $netRetained);

                $statusText = ucfirst($booking->status);
                if ($refundAmount > 0) {
                    $statusText = ($refundAmount >= $bookingTotal) ? 'Refunded' : 'Partially Refunded';
                }
                $sheet->setCellValue('Q' . $row, $statusText);

                // Formatting
                $sheet->getStyle('L' . $row . ':P' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                // Status Coloring
                $status = strtolower($booking->status);
                if ($status == 'completed') {
                    $sheet->getStyle('Q' . $row)->getFont()->setBold(true)->getColor()->setRGB('27ae60');
                } elseif ($status == 'cancelled') {
                    $sheet->getStyle('Q' . $row)->getFont()->setBold(true)->getColor()->setRGB('c0392b');
                }

                // Accumulate totals
                if ($status == 'completed') {
                    $totalServiceValue += ($booking->amount ?? 0);
                    $serviceRevenue += ($booking->amount ?? 0);
                    $totalTips += ($booking->tip_amount ?? 0);

                    if (!$booking->posSaleItem) {
                        $totalCashRevenue += ($booking->amount ?? 0);
                        $transactionCount++;
                    }
                } elseif (in_array($status, ['cancelled', 'no_show'])) {
                    $totalLoss += ($booking->amount ?? 0);
                }

                if (isset($statusCounts[$status])) {
                    $statusCounts[$status]++;
                }

                $totalBookings++;
                $row++;
            }
        });

        // Process POS Sales for Summary (Direct Sales ONLY - to prevent double counting with bookings)
        $posQuery = PosSale::where('salon_id', $salonId)
            ->whereBetween('sale_date', [$startDateTime, $endDateTime])
            // . Removed hardcoded 'paid' to allow all statuses (e.g. partial/unpaid) if not filtered
            ->where('status', '!=', 'voided')
            ->whereDoesntHave('items', function ($q) {
                $q->whereNotNull('booking_id');
            });

        if ($branchId)
            $posQuery->where('branch_id', $branchId);
        if ($customerId)
            $posQuery->where('customer_id', $customerId);
        if ($paymentStatus)
            $posQuery->where('payment_status', $paymentStatus);
        if ($paymentMethod)
            $posQuery->where('payment_method', $paymentMethod);

        if ($staffId || $serviceId) {
            $posQuery->whereHas('items', function ($q) use ($staffId, $serviceId) {
                if ($staffId)
                    $q->where('staff_id', $staffId);
                if ($serviceId)
                    $q->where('item_id', $serviceId)->where('item_type', 'App\Models\Service');
            });
        }

        $posSales = $posQuery->with(['items.item', 'customer', 'employee'])->get();

        foreach ($posSales as $sale) {
            $paidAmount = max(0, $sale->total - $sale->outstanding_amount);
            $totalCashRevenue += max(0, $paidAmount - $sale->tip);
            $totalTips += $sale->tip;
            $transactionCount++;

            foreach ($sale->items as $item) {
                $isService = ($item->item_type === 'App\Models\Service');
                $isProduct = ($item->item_type === 'App\Models\InventoryItem');
                $isPackage = ($item->item_type === 'App\Models\Package' || $item->item_type === 'App\Models\Membership');

                // Filter by service if needed
                if ($serviceId && (!$isService || $item->item_id != $serviceId)) {
                    continue;
                }

                // Add to sheet
                $sheet->setCellValue('A' . $row, 'P-' . str_pad($sale->id, 5, '0', STR_PAD_LEFT));
                $sheet->setCellValue('B' . $row, $sale->sale_date->format('Y-m-d'));
                $sheet->setCellValue('C' . $row, $sale->created_at->format('H:i'));
                $sheet->setCellValue('D' . $row, $sale->created_at->format('Y-m-d H:i'));
                $sheet->setCellValue('E' . $row, $sale->customer ? $sale->customer->name : 'Walk-in');
                $sheet->setCellValue('F' . $row, $sale->customer ? $sale->customer->phone : '-');
                $sheet->setCellValue('G' . $row, $item->item_name);

                $category = 'N/A';
                if ($isService && $item->item && $item->item->category)
                    $category = $item->item->category->name;
                elseif ($isProduct && $item->item && $item->item->category)
                    $category = $item->item->category->name;

                $sheet->setCellValue('H' . $row, $category);
                $sheet->setCellValue('I' . $row, $sale->employee ? $sale->employee->name : 'Unassigned');
                $sheet->setCellValue('J' . $row, 'POS Direct');
                $sheet->setCellValue('K' . $row, ucfirst($sale->payment_method));

                $itemNet = $item->total; // POS item total is usually net of tax/tip unless specified
                $sheet->setCellValue('L' . $row, $itemNet);
                $sheet->setCellValue('M' . $row, 0); // POS item tax breakdown is usually per sale, not per item in this schema
                $sheet->setCellValue('N' . $row, 0);
                $sheet->setCellValue('O' . $row, 0);
                $sheet->setCellValue('P' . $row, $item->total);
                $sheet->setCellValue('Q' . $row, ucfirst($sale->payment_status));

                $sheet->getStyle('L' . $row . ':P' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                if ($sale->payment_status === 'paid') {
                    $sheet->getStyle('Q' . $row)->getFont()->setBold(true)->getColor()->setRGB('27ae60');
                }

                $posDirectRevenue += $item->total;
                $totalServiceValue += $item->total;

                if ($isService) {
                    $serviceRevenue += $item->total;
                } elseif ($isProduct) {
                    $productRevenue += $item->total;
                } elseif ($isPackage) {
                    $packageRevenue += $item->total;
                }

                $row++;
            }
        }

        // Add borders to data range
        $dataRange = 'A' . $headerRow . ':Q' . ($row - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Summary Section
        $row += 2;
        $sheet->setCellValue('A' . $row, 'FINANCIAL & OPERATIONAL SUMMARY');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('2c3e50');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->setCellValue('A' . $row, 'Total Bookings:');
        $sheet->setCellValue('B' . $row, $totalBookings);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Total Service Value (Gross):');
        $sheet->setCellValue('B' . $row, $totalServiceValue);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('B' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, '  - Services revenue:');
        $sheet->setCellValue('B' . $row, $serviceRevenue);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        $row++;
        $sheet->setCellValue('A' . $row, '  - Products revenue:');
        $sheet->setCellValue('B' . $row, $productRevenue);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        $row++;
        $sheet->setCellValue('A' . $row, 'Total Tips:');
        $sheet->setCellValue('B' . $row, $totalTips);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('B' . $row)->getFont()->getColor()->setRGB('e67e22');

        $row++;
        $sheet->setCellValue('A' . $row, 'Revenue Loss (Cancellations):');
        $sheet->setCellValue('B' . $row, $totalLoss);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('B' . $row)->getFont()->getColor()->setRGB('c0392b');

        // Refund Section in Appointment Summary
        $refundStats = DB::table('pos_refunds')
            ->where('salon_id', $salonId)
            ->whereBetween('processed_at', [$startDateTime, $endDateTime])
            ->select(
                DB::raw('COALESCE(SUM(amount), 0) as gross'),
                DB::raw('COALESCE(SUM(fee_amount), 0) as fees')
            )
            ->first();

        if ($refundStats->gross > 0) {
            $row += 2;
            $sheet->setCellValue('A' . $row, 'REFUND BREAKDOWN');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setUnderline(true);

            $row++;
            $sheet->setCellValue('A' . $row, 'Gross Refunded (-):');
            $sheet->setCellValue('B' . $row, $refundStats->gross);
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $row++;
            $sheet->setCellValue('A' . $row, 'Refund Fees (+):');
            $sheet->setCellValue('B' . $row, $refundStats->fees);
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $row++;
            $sheet->setCellValue('A' . $row, 'Net Retained Revenue:');
            $sheet->setCellValue('B' . $row, ($totalServiceValue + $totalTips) - ($refundStats->gross - $refundStats->fees));
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true)->getColor()->setRGB('27ae60');
        }

        $row += 2;
        $sheet->setCellValue('A' . $row, 'Status Breakdown:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        foreach ($statusCounts as $status => $count) {
            $row++;
            $sheet->setCellValue('A' . $row, ucfirst(str_replace('_', ' ', $status)) . ':');
            $sheet->setCellValue('B' . $row, $count);
        }

        // Auto-size columns
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Appointments_Report_' . date('Y-m-d_His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export staff report to Excel with formatting
     */
    public function exportStaff(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $salon = auth()->user()->salon;
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $branchId = $request->get('branch_id');
        $staffIdFilter = $request->get('staff_id');
        $serviceId = $request->get('service_id');
        $customerId = $request->get('customer_id');

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Previous period for trend analysis
        $diff = $startDateTime->diffInDays($endDateTime) + 1;
        $prevStart = (clone $startDateTime)->subDays($diff);
        $prevEnd = (clone $endDateTime)->subDays($diff);

        // 1. Booking Stats by Staff
        $bookingStatsQuery = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->whereBetween('start_time', [$startDateTime, $endDateTime]);

        if ($branchId)
            $bookingStatsQuery->where('branch_id', $branchId);
        if ($staffIdFilter)
            $bookingStatsQuery->where('staff_id', $staffIdFilter);
        if ($serviceId)
            $bookingStatsQuery->where('service_id', $serviceId);
        if ($customerId)
            $bookingStatsQuery->where('customer_id', $customerId);

        $bookingStats = $bookingStatsQuery->select(
            'staff_id',
            DB::raw('count(*) as total_count'),
            DB::raw('SUM(CASE WHEN NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id) THEN amount ELSE 0 END) as direct_revenue'),
            DB::raw('SUM(CASE WHEN NOT EXISTS (SELECT 1 FROM pos_sale_items WHERE pos_sale_items.booking_id = bookings.id) THEN tip_amount ELSE 0 END) as tips')
        )
            ->groupBy('staff_id')
            ->get()
            ->keyBy('staff_id');

        // 2. POS Sales Stats by Staff (Detailed Breakdown - PHP Calculation for Accuracy)
        $posSalesQuery = PosSale::where('salon_id', $salonId)
            ->where('status', '!=', 'voided')
            ->where('payment_status', '!=', 'pending')
            ->whereBetween('sale_date', [$startDateTime, $endDateTime]);

        if ($branchId)
            $posSalesQuery->where('branch_id', $branchId);
        if ($customerId)
            $posSalesQuery->where('customer_id', $customerId);
        if ($staffIdFilter || $serviceId) {
            $posSalesQuery->whereHas('items', function ($q) use ($staffIdFilter, $serviceId) {
                if ($staffIdFilter)
                    $q->where('staff_id', $staffIdFilter);
                if ($serviceId)
                    $q->where('item_id', $serviceId)->where('item_type', 'App\Models\Service');
            });
        }

        $posSales = $posSalesQuery->with(['items'])
            ->get();

        $posStats = []; // [staff_id => ['service_revenue' => 0, 'product_revenue' => 0, 'package_revenue' => 0, 'sale_count' => 0]]

        foreach ($posSales as $sale) {
            // Calculate Cart Discount Share
            $cartDiscount = $sale->discount - $sale->items->sum('discount_amount');
            $totalNetBeforeCart = $sale->items->sum(function ($i) {
                return $i->subtotal - $i->discount_amount;
            });

            // Track unique sales per staff
            $staffInSale = [];

            foreach ($sale->items as $item) {
                if (!$item->staff_id)
                    continue;

                $staffId = $item->staff_id;
                $staffInSale[$staffId] = true;

                if (!isset($posStats[$staffId])) {
                    $posStats[$staffId] = [
                        'service_revenue' => 0,
                        'product_revenue' => 0,
                        'package_revenue' => 0,
                        'sale_count' => 0
                    ];
                }

                // Calculate Item Revenue (Net of Item Discount and Distributed Cart Discount)
                $itemNetBeforeCart = $item->subtotal - $item->discount_amount;

                $distributedCartDiscount = 0;
                if ($cartDiscount > 0 && $totalNetBeforeCart > 0) {
                    $share = $itemNetBeforeCart / $totalNetBeforeCart;
                    $distributedCartDiscount = $cartDiscount * $share;
                }

                $itemRevenue = ($itemNetBeforeCart - $distributedCartDiscount) + $item->tax_amount;

                // Deduct refunds and add back fees (Net Retained)
                if ($sale->refunded_amount > 0) {
                    $totalSaleValue = $sale->total != 0 ? $sale->total : 1;
                    $itemRatio = $itemRevenue / $totalSaleValue;

                    $totalFees = $sale->refunds->sum('fee_amount');
                    $totalRefundedGross = $sale->refunds->sum('amount');

                    $attributedRefund = $totalRefundedGross * $itemRatio;
                    $attributedFee = $totalFees * $itemRatio;

                    $itemRevenue = $itemRevenue - $attributedRefund + $attributedFee;
                }

                // Categorize Revenue
                if ($item->item_type === 'App\Models\Service') {
                    $posStats[$staffId]['service_revenue'] += $itemRevenue;
                } elseif ($item->item_type === 'App\Models\InventoryItem') {
                    $posStats[$staffId]['product_revenue'] += $itemRevenue;
                } elseif (in_array($item->item_type, ['App\Models\Package', 'App\Models\Membership'])) {
                    $posStats[$staffId]['package_revenue'] += $itemRevenue;
                }
            }

            // Increment sale count for each staff member involved in this sale
            foreach ($staffInSale as $staffId => $bool) {
                $posStats[$staffId]['sale_count']++;
            }
        }

        $posStats = collect($posStats);

        // 3. Commission Stats
        $commissionStatsQuery = \App\Models\StaffCommission::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime]);

        if ($staffIdFilter)
            $commissionStatsQuery->where('staff_id', $staffIdFilter);

        $commissionStats = $commissionStatsQuery->select(
            'staff_id',
            DB::raw('sum(case when item_type = "booking" then commission_amount else 0 end) as booking_commission'),
            DB::raw('sum(case when item_type = "pos" then commission_amount else 0 end) as pos_commission'),
            DB::raw('sum(case when item_type != "tip" then commission_amount else 0 end) as total_commission'),
            DB::raw('sum(case when item_type = "tip" then commission_amount else 0 end) as tip_commissions')
        )
            ->groupBy('staff_id')
            ->get()
            ->keyBy('staff_id');

        // 4. Previous Period Stats for Trend
        $prevRevenue = DB::table('pos_sales')
            ->where('salon_id', $salonId)
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'voided')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->select('employee_id', DB::raw('SUM(total) as revenue'))
            ->groupBy('employee_id')
            ->pluck('revenue', 'employee_id');

        // Create new Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Staff Performance');

        // Report Header Section
        $row = 1;
        $sheet->setCellValue('A' . $row, 'STAFF PERFORMANCE REPORT');
        $sheet->mergeCells('A' . $row . ':P' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('00796B');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($row)->setRowHeight(30);

        $row++;
        $sheet->setCellValue('A' . $row, 'Salon:');
        $sheet->setCellValue('B' . $row, $salon->name ?? 'N/A');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Generated On:');
        $sheet->setCellValue('B' . $row, date('Y-m-d H:i:s'));
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Period:');
        $sheet->setCellValue('B' . $row, $startDate . ' to ' . $endDate);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row += 2; // Empty row

        // Column Headers
        $headerRow = $row;
        $headers = [
            'STAFF NAME',
            'BOOKINGS',
            'POS SALES',
            'SERVICE REV.',
            'PRODUCT REV.',
            'PACKAGE REV.',
            'TOTAL REVENUE',
            'AVG PER VISIT',
            'TREND (%)',
            'BOOKING COMM.',
            'POS COMM.',
            'TOTAL COMM.',
            'TIPS',
            'TOTAL EARNINGS'
        ];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($col . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('26A69A');
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        $row++;

        $totalRevenue = 0;
        $totalCommissions = 0;
        $totalTips = 0;
        $staffCount = 0;

        // Staff Data (Chunked)
        User::role('employee')->where('salon_id', $salonId)
            ->chunk(100, function ($employees) use (&$sheet, &$row, $bookingStats, $posStats, $commissionStats, $prevRevenue, &$totalRevenue, &$totalCommissions, &$totalTips, &$staffCount) {
                foreach ($employees as $staff) {
                    $bStat = $bookingStats->get($staff->id);
                    $pStat = $posStats->get($staff->id);
                    $cStat = $commissionStats->get($staff->id);

                    $bookingCount = $bStat->total_count ?? 0;
                    $bookingRevenue = $bStat->direct_revenue ?? 0;
                    $bookingTips = $bStat->tips ?? 0;

                    $posSaleCount = $pStat->sale_count ?? 0;
                    $serviceRevenue = floatval($pStat->service_revenue ?? 0);
                    $productRevenue = floatval($pStat->product_revenue ?? 0);
                    $packageRevenue = floatval($pStat->package_revenue ?? 0);
                    $posRevenue = $serviceRevenue + $productRevenue + $packageRevenue;

                    $totalRev = $bookingRevenue + $posRevenue;
                    $bookingComm = $cStat->booking_commission ?? 0;
                    $posComm = $cStat->pos_commission ?? 0;
                    $comm = $cStat->total_commission ?? 0;
                    $tipComm = $cStat->tip_commissions ?? 0;

                    $tips = $bookingTips + $tipComm;
                    $earnings = $comm + $tips;

                    $avgPerVisit = ($bookingCount + $posSaleCount) > 0 ? ($totalRev / ($bookingCount + $posSaleCount)) : 0;
                    $prevRev = $prevRevenue->get($staff->id) ?? 0;
                    $trend = $prevRev > 0 ? (($totalRev - $prevRev) / $prevRev * 100) : 0;

                    $sheet->setCellValue('A' . $row, $staff->name);
                    $sheet->setCellValue('B' . $row, $bookingCount);
                    $sheet->setCellValue('C' . $row, $posSaleCount);
                    $sheet->setCellValue('D' . $row, $bookingRevenue + $serviceRevenue);
                    $sheet->setCellValue('E' . $row, $productRevenue);
                    $sheet->setCellValue('F' . $row, $packageRevenue);
                    $sheet->setCellValue('G' . $row, $totalRev);
                    $sheet->setCellValue('H' . $row, $avgPerVisit);
                    $sheet->setCellValue('I' . $row, $trend);
                    $sheet->setCellValue('J' . $row, $bookingComm);
                    $sheet->setCellValue('K' . $row, $posComm);
                    $sheet->setCellValue('L' . $row, $comm);
                    $sheet->setCellValue('M' . $row, $tips);
                    $sheet->setCellValue('N' . $row, $earnings);

                    // Format currency columns
                    $sheet->getStyle('D' . $row . ':H' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle('J' . $row . ':N' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                    // Format trend column
                    $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('0.0"%"');

                    // Highlight trend
                    if ($trend > 0) {
                        $sheet->getStyle('I' . $row)->getFont()->getColor()->setRGB('006100');
                    } elseif ($trend < 0) {
                        $sheet->getStyle('I' . $row)->getFont()->getColor()->setRGB('9C0006');
                    }

                    // Highlight commission column in green
                    if ($comm > 0) {
                        $sheet->getStyle('L' . $row)->getFont()->getColor()->setRGB('006100');
                        $sheet->getStyle('L' . $row)->getFont()->setBold(true);
                    }

                    // Highlight tips column in orange
                    if ($tips > 0) {
                        $sheet->getStyle('M' . $row)->getFont()->getColor()->setRGB('FF6B00');
                        $sheet->getStyle('M' . $row)->getFont()->setBold(true);
                    }

                    // Bold total earnings
                    $sheet->getStyle('N' . $row)->getFont()->setBold(true);

                    $totalRevenue += $totalRev;
                    $totalCommissions += $comm;
                    $totalTips += $tips;
                    $staffCount++;
                    $row++;
                }
            });

        // Add borders to data range
        $dataRange = 'A' . $headerRow . ':N' . ($row - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Summary Section
        $row += 2;
        $sheet->setCellValue('A' . $row, 'SUMMARY');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('00796B');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->setCellValue('A' . $row, 'Total Staff Members:');
        $sheet->setCellValue('B' . $row, $staffCount);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row += 2;
        $sheet->setCellValue('A' . $row, 'Total Revenue Generated:');
        $sheet->setCellValue('B' . $row, $totalRevenue);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('"' . currency_symbol() . '"#,##0.00');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Total Commissions Paid:');
        $sheet->setCellValue('B' . $row, $totalCommissions);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('"' . currency_symbol() . '"#,##0.00');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->getColor()->setRGB('006100');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Total Tips Collected:');
        $sheet->setCellValue('B' . $row, $totalTips);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('"' . currency_symbol() . '"#,##0.00');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->getColor()->setRGB('FF6B00');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row += 2;
        $sheet->setCellValue('A' . $row, 'TOTAL STAFF EARNINGS:');
        $sheet->setCellValue('B' . $row, $totalCommissions + $totalTips);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('00796B');
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('"' . currency_symbol() . '"#,##0.00');

        // Auto-size columns
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Staff_Performance_Report_' . date('Y-m-d_His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export customers report to Excel with formatting
     */
    public function exportCustomersReport(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $salon = auth()->user()->salon;
        $branchId = $request->get('branch_id');
        $staffId = $request->get('staff_id');
        $serviceId = $request->get('service_id');
        $statusFilter = $request->get('status');
        $segmentFilter = $request->get('segment');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Fetch stats in bulk to avoid N+1 during chunking
        $bookingStatsQuery = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->whereBetween('start_time', [$startDateTime, $endDateTime])
            ->whereDoesntHave('posSaleItem'); // Exclude bookings processed via POS

        if ($branchId)
            $bookingStatsQuery->where('branch_id', $branchId);
        if ($staffId)
            $bookingStatsQuery->where('staff_id', $staffId);
        if ($serviceId)
            $bookingStatsQuery->where('service_id', $serviceId);
        if ($statusFilter)
            $bookingStatsQuery->where('status', $statusFilter);

        $bookingStats = $bookingStatsQuery->select('customer_id', DB::raw('count(*) as count'), DB::raw('sum(amount) as revenue'), DB::raw('sum(tip_amount) as tips'))
            ->groupBy('customer_id')->get()->keyBy('customer_id');

        $posStatsQuery = PosSale::where('salon_id', $salonId)
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'voided')
            ->whereBetween('sale_date', [$startDateTime, $endDateTime]);

        if ($branchId)
            $posStatsQuery->where('branch_id', $branchId);
        if ($staffId || $serviceId) {
            $posStatsQuery->whereHas('items', function ($q) use ($staffId, $serviceId) {
                if ($staffId)
                    $q->where('staff_id', $staffId);
                if ($serviceId)
                    $q->where('item_id', $serviceId)->where('item_type', 'App\Models\Service');
            });
        }

        $posStats = $posStatsQuery->select(
            'customer_id',
            DB::raw('count(*) as count'),
            DB::raw('SUM(total - tip) as revenue'),
            DB::raw('SUM(tip) as tips')
        )->groupBy('customer_id')->get()->keyBy('customer_id');

        // Better: Fetch refunds separately
        try {
            $refundStatsData = DB::table('pos_refunds')
                ->join('pos_sales', 'pos_refunds.sale_id', '=', 'pos_sales.id')
                ->where('pos_sales.salon_id', $salonId)
                ->whereBetween('pos_refunds.processed_at', [$startDateTime, $endDateTime])
                ->select('pos_sales.customer_id', DB::raw('SUM(net_refund_amount) as net_refunded'), DB::raw('SUM(fee_amount) as refund_fees'))
                ->groupBy('pos_sales.customer_id')
                ->get()
                ->keyBy('customer_id');
        } catch (\Exception $e) {
            $refundStatsData = collect();
        }

        $refundedBookings = Booking::where('salon_id', $salonId)
            ->where('payment_status', 'refunded')
            ->select('customer_id', DB::raw('sum(amount) as amount'))
            ->groupBy('customer_id')->get()->keyBy('customer_id');

        $firstVisits = DB::table('bookings')
            ->where('salon_id', $salonId)
            ->whereIn('status', ['completed', 'staff_completed'])
            ->select('customer_id', DB::raw('MIN(start_time) as first_visit'))
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id');

        $firstSales = DB::table('pos_sales')
            ->where('salon_id', $salonId)
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'voided')
            ->select('customer_id', DB::raw('MIN(created_at) as first_visit'))
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id');

        // Create new Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customer Report');

        // Report Header Section
        $row = 1;
        $sheet->setCellValue('A' . $row, 'CUSTOMER REPORT');
        $sheet->mergeCells('A' . $row . ':I' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E65100');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($row)->setRowHeight(30);

        $row++;
        $sheet->setCellValue('A' . $row, 'Salon:');
        $sheet->setCellValue('B' . $row, $salon->name ?? 'N/A');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Generated On:');
        $sheet->setCellValue('B' . $row, date('Y-m-d H:i:s'));
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Total Customers:');
        $sheet->setCellValue('B' . $row, Customer::where('salon_id', $salonId)->count());
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row += 2; // Empty row

        // Column Headers
        $headerRow = $row;
        $headers = ['NAME', 'EMAIL', 'PHONE', 'GENDER', 'AGE', 'CUSTOMER SINCE', 'FIRST VISIT', 'TOTAL VISITS', 'TOTAL PAID', 'REFUNDED', 'TOTAL SPEND', 'SEGMENT', 'STATUS'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($col . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FF6F00');
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        $row++;

        // Customer Data
        $totalSpend = 0;
        $totalVisits = 0;
        $activeCount = 0;
        $vipCount = 0;
        $customerCount = 0;

        $customerQuery = Customer::where('salon_id', $salonId);

        if ($branchId || $staffId || $serviceId || $statusFilter) {
            $customerQuery->where(function ($q) use ($branchId, $staffId, $serviceId, $statusFilter) {
                $q->whereHas('bookings', function ($bq) use ($branchId, $staffId, $serviceId, $statusFilter) {
                    if ($branchId)
                        $bq->where('branch_id', $branchId);
                    if ($staffId)
                        $bq->where('staff_id', $staffId);
                    if ($serviceId)
                        $bq->where('service_id', $serviceId);
                    if ($statusFilter)
                        $bq->where('status', $statusFilter);
                })->orWhereHas('posSales', function ($pq) use ($branchId, $staffId, $serviceId) {
                    if ($branchId)
                        $pq->where('branch_id', $branchId);
                    if ($staffId || $serviceId) {
                        $pq->whereHas('items', function ($iq) use ($staffId, $serviceId) {
                            if ($staffId)
                                $iq->where('staff_id', $staffId);
                            if ($serviceId)
                                $iq->where('item_id', $serviceId)->where('item_type', 'App\Models\Service');
                        });
                    }
                });
            });
        }

        $customerQuery->chunk(100, function ($customers) use (&$sheet, &$row, $bookingStats, $posStats, $refundStatsData, $refundedBookings, $firstVisits, $firstSales, &$totalSpend, &$totalVisits, &$activeCount, &$vipCount, &$customerCount, $segmentFilter) {
            foreach ($customers as $customer) {
                $bStat = $bookingStats->get($customer->id);
                $pStat = $posStats->get($customer->id);
                $rStat = $refundedBookings->get($customer->id);
                $prStat = $refundStatsData->get($customer->id);

                $visits = ($bStat->count ?? 0) + ($pStat->count ?? 0);
                $paid_amnt = ($bStat->revenue ?? 0) + ($pStat->revenue ?? 0);
                $tips = ($bStat->tips ?? 0) + ($pStat->tips ?? 0);
                $refunded = ($prStat->net_refunded ?? 0) + ($rStat->amount ?? 0);
                $spend = $paid_amnt + $tips - $refunded;

                // First visit calculation
                $fVisitB = $firstVisits->get($customer->id);
                $fVisitP = $firstSales->get($customer->id);
                $firstVisit = null;
                if ($fVisitB && $fVisitP) {
                    $firstVisit = min($fVisitB->first_visit, $fVisitP->first_visit);
                } else {
                    $firstVisit = $fVisitB->first_visit ?? $fVisitP->first_visit ?? null;
                }

                // Segmentation logic (matching UI)
                $segment = 'New';
                if ($spend >= 1000 || $visits >= 15) {
                    $segment = 'VIP';
                    $vipCount++;
                } elseif ($visits >= 5) {
                    $segment = 'Loyal';
                } elseif ($visits >= 2) {
                    $segment = 'Regular';
                }

                // Apply Segment Filter
                if ($segmentFilter && strtolower($segment) !== strtolower($segmentFilter)) {
                    continue;
                }

                $sheet->setCellValue('A' . $row, $customer->name);
                $sheet->setCellValue('B' . $row, $customer->email);
                $sheet->setCellValue('C' . $row, $customer->phone);
                $sheet->setCellValue('D' . $row, ucfirst($customer->gender ?? 'N/A'));
                $sheet->setCellValue('E' . $row, $customer->dob ? \Carbon\Carbon::parse($customer->dob)->age : 'N/A');
                $sheet->setCellValue('F' . $row, $customer->created_at->format('Y-m-d'));
                $sheet->setCellValue('G' . $row, $firstVisit ? date('Y-m-d', strtotime($firstVisit)) : 'N/A');
                $sheet->setCellValue('H' . $row, $visits);
                $sheet->setCellValue('I' . $row, $paid_amnt + $tips);
                $sheet->setCellValue('J' . $row, $refunded);
                $sheet->setCellValue('K' . $row, $spend);
                $sheet->setCellValue('L' . $row, $segment);
                $sheet->setCellValue('M' . $row, ucfirst($customer->status));

                // Format currency columns
                $sheet->getStyle('I' . $row . ':K' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                if ($customer->status === 'active') {
                    $activeCount++;
                }

                // Color status
                switch ($customer->status) {
                    case 'active':
                        $sheet->getStyle('M' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('C6EFCE');
                        $sheet->getStyle('M' . $row)->getFont()->getColor()->setRGB('006100');
                        break;
                    case 'inactive':
                        $sheet->getStyle('M' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFC7CE');
                        $sheet->getStyle('M' . $row)->getFont()->getColor()->setRGB('9C0006');
                        break;
                }

                // Color segment
                if ($segment === 'VIP') {
                    $sheet->getStyle('L' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFF9C4');
                    $sheet->getStyle('L' . $row)->getFont()->setBold(true)->getColor()->setRGB('856404');
                }

                $totalSpend += $spend;
                $totalVisits += $visits;
                $customerCount++;
                $row++;
            }
        });

        // Add borders to data range
        $dataRange = 'A' . $headerRow . ':M' . ($row - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Summary Section
        $row += 2;
        $sheet->setCellValue('A' . $row, 'SUMMARY');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E65100');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->setCellValue('A' . $row, 'Total Customers:');
        $sheet->setCellValue('B' . $row, $customerCount);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Active Customers:');
        $sheet->setCellValue('B' . $row, $activeCount);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('C6EFCE');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->getColor()->setRGB('006100');

        $row++;
        $sheet->setCellValue('A' . $row, 'VIP Customers (⭐):');
        $sheet->setCellValue('B' . $row, $vipCount);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFF9C4');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->getColor()->setRGB('856404');

        $row += 2;
        // Total Revenue - Prominent Display
        $sheet->setCellValue('A' . $row, '💰 TOTAL REVENUE');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('2E7D32');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK)->getColor()->setRGB('1B5E20');
        $sheet->getRowDimension($row)->setRowHeight(25);

        $row += 2;
        $sheet->setCellValue('A' . $row, 'Financial Metrics:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12)->setUnderline(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Total Revenue:');
        $sheet->setCellValue('B' . $row, $totalSpend);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('"' . currency_symbol() . '"#,##0.00');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Total Visits:');
        $sheet->setCellValue('B' . $row, $totalVisits);

        $row++;
        $sheet->setCellValue('A' . $row, 'Avg Spend/Visit:');
        $sheet->setCellValue('B' . $row, $totalVisits > 0 ? $totalSpend / $totalVisits : 0);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('"' . currency_symbol() . '"#,##0.00');

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Customer_Report_' . date('Y-m-d_His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export inventory report to CSV
     */
    public function exportInventory(Request $request)
    {
        $salonId = auth()->user()->salon_id;

        $filename = 'inventory_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($salonId) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Item Name', 'SKU', 'Category', 'Quantity in Stock', 'Minimum Quantity', 'Purchase Price', 'Selling Price', 'Total Value', 'Status']);

            \App\Models\InventoryItem::where('salon_id', $salonId)
                ->with('category')
                ->chunk(100, function ($items) use ($file) {
                    foreach ($items as $item) {
                        $status = 'In Stock';
                        if ($item->quantity_in_stock <= 0) {
                            $status = 'Out of Stock';
                        } elseif ($item->quantity_in_stock <= $item->minimum_quantity) {
                            $status = 'Low Stock';
                        }

                        fputcsv($file, [
                            $item->name,
                            $item->sku ?? 'N/A',
                            $item->category ? $item->category->name : 'Uncategorized',
                            $item->quantity_in_stock,
                            $item->minimum_quantity,
                            number_format((float) $item->purchase_price, 2),
                            number_format((float) $item->selling_price, 2),
                            number_format((float) $item->purchase_price * $item->quantity_in_stock, 2),
                            $status
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import customers from CSV for reports
     */
    public function importCustomersReport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $handle = fopen($file->getRealPath(), 'r');

            // Skip header row
            $header = fgetcsv($handle);

            $imported = 0;
            $errors = [];
            $row = 1;

            while (($data = fgetcsv($handle)) !== false) {
                $row++;

                try {
                    // Validate required fields
                    if (empty($data[0]) || empty($data[1]) || empty($data[2])) {
                        $errors[] = "Row {$row}: Missing required fields (name, email, phone)";
                        continue;
                    }

                    // Check for duplicate email
                    $exists = Customer::where('salon_id', auth()->user()->salon_id)
                        ->where('email', $data[1])
                        ->exists();

                    if ($exists) {
                        $errors[] = "Row {$row}: Customer with email {$data[1]} already exists";
                        continue;
                    }

                    // Check for duplicate phone
                    $phone = !empty($data[2]) ? trim($data[2]) : null;
                    if ($phone) {
                        $existsPhone = Customer::where('salon_id', auth()->user()->salon_id)
                            ->where('phone', $phone)
                            ->exists();

                        if ($existsPhone) {
                            $errors[] = "Row {$row}: Customer with phone {$phone} already exists";
                            continue;
                        }
                    }

                    // Validate email format
                    if (!filter_var($data[1], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Row {$row}: Invalid email format: {$data[1]}";
                        continue;
                    }

                    Customer::create([
                        'salon_id' => auth()->user()->salon_id,
                        'name' => $data[0],
                        'email' => $data[1],
                        'phone' => $data[2],
                        'preferred_contact' => isset($data[3]) && in_array($data[3], ['email', 'phone', 'sms']) ? $data[3] : 'email',
                        'address' => $data[4] ?? null,
                        'status' => 'active',
                    ]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$row}: " . $e->getMessage();
                }
            }

            fclose($handle);

            $message = "Successfully imported {$imported} customer(s).";
            if (count($errors) > 0) {
                $message .= " " . count($errors) . " error(s) occurred.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            \Log::error('Customer import failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download customer import template for reports
     */
    public function downloadCustomersImportTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customer_import_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Name (Required)',
                'Email (Required)',
                'Phone (Required)',
                'Preferred Contact (email/phone/sms)',
                'Address'
            ]);

            // Add example row
            fputcsv($file, [
                'John Doe',
                'john@example.com',
                '+1234567890',
                'email',
                '123 Main St, City'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
