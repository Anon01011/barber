<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\InventoryItem;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get current salon ID from authenticated user
        $salonId = auth()->user()->salon_id;

        // Get date range from request or default to last 30 days
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfDay();

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->subDays(30)->startOfDay();

        // Ensure end date is not before start date
        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy()->addDay();
        }

        // Get all analytics data
        $salesData = $this->getSalesData($startDate, $endDate, $salonId);
        $paymentAnalytics = $this->getPaymentAnalytics($startDate, $endDate, $salonId);
        $voidAnalytics = $this->getVoidAnalytics($startDate, $endDate, $salonId);
        $salesTrends = $this->getRevenueTrends($startDate, $endDate, $salonId);
        $topProducts = $this->getTopProducts($startDate, $endDate, $salonId);
        $salesByCategory = $this->getSalesByCategory($startDate, $endDate, $salonId);
        $recentTransactions = $this->getRecentTransactions($salonId);

        // Get additional data for charts
        $weeklySales = $this->getWeeklySalesData($startDate, $endDate, $salonId);
        $monthlySales = $this->getMonthlySalesData($startDate, $endDate, $salonId);
        $hourlySales = $this->getHourlySalesData($startDate, $endDate, $salonId);

        // Define colors
        $categoryColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#fd7e14'];

        return view('pos.analytics.index', [
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'salesData' => $salesData,
            'paymentAnalytics' => $paymentAnalytics,
            'voidAnalytics' => $voidAnalytics,
            'revenueTrends' => $salesTrends, // Renamed to match view
            'weeklySales' => $weeklySales,
            'monthlySales' => $monthlySales,
            'hourlySales' => $hourlySales,
            'topProducts' => $topProducts,
            'salesByCategory' => $salesByCategory,
            'recentTransactions' => $recentTransactions,
            'categoryColors' => $categoryColors,
        ]);
    }

    /**
     * Get sales data for the given date range
     */
    private function getSalesData($startDate, $endDate, $salonId)
    {
        // Base query for VALID sales (excluding voided)
        $baseQuery = PosSale::where('salon_id', $salonId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', '!=', 'voided');

        // Calculate Revenue (Total - Tip)
        $totalRevenue = (clone $baseQuery)->sum(DB::raw('total - tip'));

        // Calculate Total Tips
        $totalTips = (clone $baseQuery)->sum('tip');

        // Calculate Total Discounts
        $totalDiscounts = (clone $baseQuery)->sum('discount');

        // Calculate Paid Amount (Revenue portion only)
        // Paid = Total - Outstanding. Revenue portion = Paid - Tip.
        $paidRevenue = (clone $baseQuery)->sum(DB::raw('total - outstanding_amount - tip'));

        // Calculate Unpaid Amount (Outstanding)
        $unpaidAmount = (clone $baseQuery)->sum('outstanding_amount');

        // Calculate Voided Amount - Separate query for voided sales
        $voidedQuery = PosSale::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'voided');

        $voidedAmount = $voidedQuery->sum('total');
        $voidedCount = $voidedQuery->count();

        // POS Sales (No Booking ID) - Valid sales only, excluding tips
        $posSalesTotal = (clone $baseQuery)
            ->whereDoesntHave('items', function ($q) {
                $q->whereNotNull('booking_id');
            })
            ->sum(DB::raw('total - tip'));

        // Booking Sales (Has Booking ID) - Valid sales only, excluding tips
        $bookingSalesTotal = (clone $baseQuery)
            ->whereHas('items', function ($q) {
                $q->whereNotNull('booking_id');
            })
            ->sum(DB::raw('total - tip'));

        // Get counts by status
        $totalTransactions = $baseQuery->count();

        // Paid count: fully paid
        $paidTransactions = (clone $baseQuery)->where('payment_status', 'paid')->count();

        // Unpaid count: pending or partial
        $unpaidTransactions = (clone $baseQuery)->whereIn('payment_status', ['unpaid', 'pending', 'partial'])->count();

        // Calculate Refund Stats from pos_refunds table
        try {
            $refundStats = DB::table('pos_refunds')
                ->where('salon_id', $salonId)
                ->whereBetween('processed_at', [$startDate, $endDate])
                ->select(
                    DB::raw('COALESCE(SUM(amount), 0) as gross'),
                    DB::raw('COALESCE(SUM(fee_amount), 0) as fees'),
                    DB::raw('COALESCE(SUM(net_refund_amount), 0) as net')
                )
                ->first();
        } catch (\Exception $e) {
            $refundStats = (object) ['gross' => 0, 'fees' => 0, 'net' => 0];
        }

        // Calculate Net Revenue
        $netRevenue = $paidRevenue - $refundStats->net;

        return [
            'totalSales' => $totalRevenue, // Total Invoiced Revenue (Excluding Tips)
            'totalTips' => $totalTips,
            'totalDiscounts' => $totalDiscounts,
            'totalPaid' => $paidRevenue, // Total Paid Revenue (Cumulative Inflow)
            'totalRefunded' => $refundStats->gross,
            'refundFees' => $refundStats->fees,
            'netRefunded' => $refundStats->net,
            'netRevenue' => $netRevenue, // Actual Revenue remaining (Paid - Net Refunded)
            'totalUnpaid' => $unpaidAmount, // Total Outstanding
            'totalVoided' => $voidedAmount, // Total Voided Amount
            'posSalesTotal' => $posSalesTotal,
            'bookingSalesTotal' => $bookingSalesTotal,
            'totalTransactions' => $totalTransactions,
            'paidTransactions' => $paidTransactions,
            'unpaidTransactions' => $unpaidTransactions,
            'voidedTransactions' => $voidedCount,
            'averageOrderValue' => $baseQuery->avg(DB::raw('total - tip')) ?? 0,
            'totalProductsSold' => PosSaleItem::whereHas('sale', function ($query) use ($startDate, $endDate, $salonId) {
                $query->where('salon_id', $salonId)
                    ->where('status', '!=', 'voided')
                    ->whereBetween('sale_date', [$startDate, $endDate]);
            })->where('item_type', 'like', '%InventoryItem%')->sum('quantity'),
        ];
    }

    /**
     * Get top selling products
     */
    private function getTopProducts($startDate, $endDate, $salonId, $limit = 5)
    {
        return PosSaleItem::select(
            'item_name as name',
            DB::raw('COALESCE(SUM(quantity), 0) as total_quantity'),
            DB::raw('COALESCE(SUM(total), 0) as total_revenue')
        )
            ->whereHas('sale', function ($query) use ($salonId) {
                $query->where('salon_id', $salonId);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('item_name')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get sales by category (Service vs Product)
     */
    private function getSalesByCategory($startDate, $endDate, $salonId)
    {
        // Group by item_type (App\Models\Service vs App\Models\InventoryItem)
        return PosSaleItem::select(
            'item_type',
            DB::raw('COALESCE(SUM(quantity), 0) as total_quantity'),
            DB::raw('COALESCE(SUM(total), 0) as total_revenue')
        )
            ->whereHas('sale', function ($query) use ($salonId) {
                $query->where('salon_id', $salonId);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('item_type')
            ->orderBy('total_revenue', 'desc')
            ->get()
            ->map(function ($item) {
                $name = 'Other';
                if (str_contains($item->item_type, 'Service')) {
                    $name = 'Services';
                } elseif (str_contains($item->item_type, 'InventoryItem')) {
                    $name = 'Products';
                } elseif (str_contains($item->item_type, 'Package')) {
                    $name = 'Packages';
                }

                return [
                    'name' => $name,
                    'total_quantity' => (int) $item->total_quantity,
                    'total_revenue' => (float) $item->total_revenue
                ];
            });
    }

    /**
     * Get validated timezone offset formatted as +/-HH:MM
     */
    private function getTimezoneOffset(): string
    {
        $offset = now(salon_timezone())->format('P');
        return preg_match('/^[+-]\d{2}:\d{2}$/', $offset) ? $offset : '+00:00';
    }

    /**
     * Get revenue trends over time (Daily)
     */
    private function getRevenueTrends($startDate, $endDate, $salonId)
    {
        $offset = $this->getTimezoneOffset();

        $sales = PosSale::select(
            DB::raw("DATE_FORMAT(CONVERT_TZ(sale_date, '+00:00', '$offset'), '%Y-%m-%d') as date"),
            DB::raw('SUM(total - tip) as total')
        )
            ->where('salon_id', $salonId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Fill in missing dates
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $trends = [];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $trends[$dateString] = $sales[$dateString] ?? 0;
        }

        return $trends;
    }

    /**
     * Get hourly sales data (Working Graph)
     */
    private function getHourlySalesData($startDate, $endDate, $salonId)
    {
        $offset = $this->getTimezoneOffset();

        $sales = PosSale::select(
            DB::raw("HOUR(CONVERT_TZ(sale_date, '+00:00', '$offset')) as hour"),
            DB::raw('SUM(total - tip) as total')
        )
            ->where('salon_id', $salonId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('total', 'hour')
            ->toArray();

        // Fill in all 24 hours
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyData[$i] = $sales[$i] ?? 0;
        }

        return $hourlyData;
    }

    /**
     * Get weekly sales data
     */
    private function getWeeklySalesData($startDate, $endDate, $salonId)
    {
        $offset = $this->getTimezoneOffset();
        // Note: YEARWEEK uses system variable 'default_week_format'. 
        // For consistent results, we should adjust date first.

        $sales = PosSale::select(
            DB::raw("YEARWEEK(CONVERT_TZ(sale_date, '+00:00', '$offset'), 1) as yearweek"),
            DB::raw("MIN(DATE(CONVERT_TZ(sale_date, '+00:00', '$offset'))) as start_date"),
            DB::raw('SUM(total - tip) as total')
        )
            ->where('salon_id', $salonId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy('yearweek')
            ->orderBy('yearweek')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->yearweek => $item->total];
            })
            ->toArray();

        // Fill in missing weeks
        $weeklyData = [];
        $currentDate = $startDate->copy()->startOfWeek();
        $endDateFinal = $endDate->copy()->endOfWeek();

        while ($currentDate->lte($endDateFinal)) {
            $key = $currentDate->format('oW');
            $label = 'Week of ' . $currentDate->format('M d');

            $weeklyData[] = [
                'week' => $label,
                'total' => $sales[$key] ?? 0
            ];

            $currentDate->addWeek();
        }

        return collect($weeklyData);
    }

    /**
     * Get monthly sales data
     */
    private function getMonthlySalesData($startDate, $endDate, $salonId)
    {
        $offset = $this->getTimezoneOffset();

        $sales = PosSale::select(
            DB::raw("DATE_FORMAT(CONVERT_TZ(sale_date, '+00:00', '$offset'), '%Y-%m') as month"),
            DB::raw('SUM(total - tip) as total')
        )
            ->where('salon_id', $salonId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Fill in missing months
        $monthlyData = [];
        $currentDate = $startDate->copy()->startOfMonth();
        $endDateFinal = $endDate->copy()->endOfMonth();

        while ($currentDate->lte($endDateFinal)) {
            $key = $currentDate->format('Y-m');
            $label = $currentDate->format('M Y');

            $monthlyData[] = [
                'month' => $label,
                'total' => $sales[$key] ?? 0
            ];

            $currentDate->addMonth();
        }

        return collect($monthlyData);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PosSale $posSale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PosSale $posSale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosSale $posSale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PosSale $posSale)
    {
        //
    }

    /**
     * Get payment analytics (paid, unpaid, voided amounts)
     */
    private function getPaymentAnalytics($startDate, $endDate, $salonId)
    {
        $baseQuery = PosSale::where('salon_id', $salonId)
            ->whereBetween('sale_date', [$startDate, $endDate]);

        // Get payment summary
        // Paid Amount = Actual money received (cash + card + online + other) for non-voided transactions
        // Unpaid Amount = Outstanding amount for non-voided transactions
        // Voided Amount = Total amount of voided transactions

        $paymentSummary = (clone $baseQuery)
            ->select(
                DB::raw('COALESCE(SUM(total), 0) as total_amount'),
                DB::raw('COALESCE(SUM(CASE WHEN status != "voided" THEN (total - tip - outstanding_amount) ELSE 0 END), 0) as paid_revenue'),
                DB::raw('COALESCE(SUM(CASE WHEN status != "voided" THEN tip ELSE 0 END), 0) as tip_amount'),
                DB::raw('COALESCE(SUM(CASE WHEN status != "voided" THEN outstanding_amount ELSE 0 END), 0) as unpaid_amount'),
                DB::raw('COALESCE(SUM(CASE WHEN status = "voided" THEN total ELSE 0 END), 0) as voided_amount'),
                DB::raw('COALESCE(SUM(CASE WHEN status != "voided" THEN discount ELSE 0 END), 0) as discount_amount'),
                DB::raw('COUNT(DISTINCT CASE WHEN payment_status = "paid" AND status != "voided" THEN id END) as paid_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN (payment_status = "unpaid" OR payment_status = "pending" OR payment_status = "partial") AND status != "voided" THEN id END) as unpaid_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN status = "voided" THEN id END) as voided_count')
            )
            ->first();

        // Fetch Refund Stats for Distribution
        try {
            $refundStats = DB::table('pos_refunds')
                ->where('salon_id', $salonId)
                ->whereBetween('processed_at', [$startDate, $endDate])
                ->select(
                    DB::raw('COALESCE(SUM(amount), 0) as gross'),
                    DB::raw('COALESCE(SUM(fee_amount), 0) as fees'),
                    DB::raw('COALESCE(SUM(net_refund_amount), 0) as net')
                )
                ->first();
        } catch (\Exception $e) {
            $refundStats = (object) ['gross' => 0, 'fees' => 0, 'net' => 0];
        }

        // Calculate percentages based on total potential value (paid + unpaid + voided + discount)
        // Actually, let's use the sum of these components as the denominator for the chart
        $chartTotal = $paymentSummary->paid_revenue + $paymentSummary->tip_amount + $paymentSummary->unpaid_amount + $paymentSummary->voided_amount + $paymentSummary->discount_amount;

        $paidPercentage = $chartTotal > 0 ? ($paymentSummary->paid_revenue / $chartTotal) * 100 : 0;
        $tipPercentage = $chartTotal > 0 ? ($paymentSummary->tip_amount / $chartTotal) * 100 : 0;
        $unpaidPercentage = $chartTotal > 0 ? ($paymentSummary->unpaid_amount / $chartTotal) * 100 : 0;
        $voidedPercentage = $chartTotal > 0 ? ($paymentSummary->voided_amount / $chartTotal) * 100 : 0;
        $discountPercentage = $chartTotal > 0 ? ($paymentSummary->discount_amount / $chartTotal) * 100 : 0;

        $refundNetPercentage = $chartTotal > 0 ? ($refundStats->net / $chartTotal) * 100 : 0;

        return [
            'total_amount' => $paymentSummary->total_amount,
            'paid_amount' => $paymentSummary->paid_revenue,
            'tip_amount' => $paymentSummary->tip_amount,
            'unpaid_amount' => $paymentSummary->unpaid_amount,
            'voided_amount' => $paymentSummary->voided_amount,
            'discount_amount' => $paymentSummary->discount_amount,
            'net_refunded' => $refundStats->net,
            'refund_fees' => $refundStats->fees,
            'paid_count' => $paymentSummary->paid_count,
            'unpaid_count' => $paymentSummary->unpaid_count,
            'voided_count' => $paymentSummary->voided_count,
            'paid_percentage' => $paidPercentage,
            'tip_percentage' => $tipPercentage,
            'unpaid_percentage' => $unpaidPercentage,
            'voided_percentage' => $voidedPercentage,
            'discount_percentage' => $discountPercentage,
            'refund_net_percentage' => $refundNetPercentage,
        ];
    }

    /**
     * Get void analytics
     */
    private function getVoidAnalytics($startDate, $endDate, $salonId)
    {
        // Check if the void_reason column exists
        $hasVoidReasonColumn = \Schema::hasColumn('pos_sales', 'void_reason');

        $voidReasons = PosSale::where('salon_id', $salonId)
            ->where('status', 'voided')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->when($hasVoidReasonColumn, function ($query) {
                return $query->select(
                    'void_reason',
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COALESCE(SUM(total), 0) as total_amount')
                )
                    ->groupBy('void_reason')
                    ->orderBy('count', 'desc');
            }, function ($query) {
                // If void_reason column doesn't exist, just get the count and total
                return $query->select(
                    DB::raw('"No reason specified" as void_reason'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COALESCE(SUM(total), 0) as total_amount')
                );
            })
            ->get();

        $totalVoided = PosSale::where('salon_id', $salonId)
            ->where('status', 'voided')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->count();

        $totalVoidedAmount = PosSale::where('salon_id', $salonId)
            ->where('status', 'voided')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->sum('total');

        return [
            'total_voided' => $totalVoided,
            'total_voided_amount' => $totalVoidedAmount,
            'average_voided_amount' => $totalVoided > 0 ? $totalVoidedAmount / $totalVoided : 0,
            'void_reasons' => $voidReasons,
        ];
    }

    /**
     * Get recent transactions for the analytics dashboard
     */
    private function getRecentTransactions($salonId, $limit = 5)
    {
        return PosSale::with(['customer', 'employee', 'items'])
            ->where('salon_id', $salonId)
            ->select('id', 'invoice_number', 'total', 'payment_status', 'status', 'created_at', 'customer_id', 'employee_id')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($sale) {
                // Query refunds directly since relationship doesn't exist
                try {
                    $refunds = DB::table('pos_refunds')
                        ->where('sale_id', $sale->id)
                        ->select('amount', 'fee_amount')
                        ->get();
                    $totalRefunded = $refunds->sum('amount');
                    $refundFees = $refunds->sum('fee_amount');
                } catch (\Exception $e) {
                    $totalRefunded = 0;
                    $refundFees = 0;
                }

                $originalTotal = $sale->total;
                $netTotal = $originalTotal - $totalRefunded + $refundFees;

                return [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'total' => $netTotal,
                    'refunded_amount' => $totalRefunded,
                    'payment_status' => $sale->payment_status,
                    'status' => $sale->status,
                    'created_at' => $sale->created_at,
                    'customer_name' => $sale->customer ? $sale->customer->name : 'Walk-in Customer',
                    'employee_name' => $sale->employee ? $sale->employee->name : 'Unknown',
                    'time_ago' => $sale->created_at->diffForHumans(),
                ];
            });
    }
}
