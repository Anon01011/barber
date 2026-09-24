<?php

namespace App\Services\AI;

use App\Models\Salon;
use App\Models\Booking;
use App\Models\PosSale;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use App\Models\InventoryItem;
use App\Models\StaffAbsence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AiMemoryService
{
    /**
     * Store recent AI interaction in salon memory cache.
     */
    public function rememberInteraction(Salon $salon, string $query, string $response): void
    {
        $cacheKey = "ai_memory_history_{$salon->id}";
        $history = Cache::get($cacheKey, []);

        // Keep last 10 interactions in memory
        $history[] = [
            'query' => $query,
            'response' => strip_tags($response),
            'timestamp' => now()->toDateTimeString(),
        ];

        if (count($history) > 10) {
            array_shift($history);
        }

        Cache::put($cacheKey, $history, 86400); // 24 hours memory retention
    }

    /**
     * Get recent conversation memory for salon.
     */
    public function getMemoryHistory(Salon $salon): array
    {
        return Cache::get("ai_memory_history_{$salon->id}", []);
    }

    /**
     * Build dynamic real-time database snapshot for salon reasoning.
     */
    public function getSalonDatabaseSnapshot(Salon $salon): array
    {
        $salonId = $salon->id;
        $today = Carbon::today();
        $thisMonthStart = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // 1. Dynamic Real Revenue
        $todayRevenue = (float) PosSale::where('salon_id', $salonId)
            ->where('payment_status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('total');

        $thisMonthRevenue = (float) PosSale::where('salon_id', $salonId)
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $thisMonthStart)
            ->sum('total');

        $lastMonthRevenue = (float) PosSale::where('salon_id', $salonId)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('total');

        // 2. Dynamic Real Customer Stats
        $totalCustomers = Customer::where('salon_id', $salonId)->count();
        $topCustomer = Customer::where('salon_id', $salonId)
            ->orderByDesc('total_spent')
            ->first();

        // 3. Dynamic Real Service Stats
        $topServiceRecord = DB::table('bookings')
            ->where('salon_id', $salonId)
            ->where('status', '!=', 'cancelled')
            ->select('service_id', DB::raw('COUNT(*) as total_bookings'))
            ->groupBy('service_id')
            ->orderByDesc('total_bookings')
            ->first();

        $topService = null;
        if ($topServiceRecord) {
            $topService = Service::where('salon_id', $salonId)->find($topServiceRecord->service_id);
        }

        // 4. Dynamic Real Staff & Absence Stats
        $totalStaff = User::where('salon_id', $salonId)->role('employee')->count();
        $absentTodayStaffCount = StaffAbsence::where('salon_id', $salonId)
            ->whereDate('start_at', '<=', $today)
            ->whereDate('end_at', '>=', $today)
            ->count();

        // 5. Dynamic Real Low Stock Items
        $lowStockItems = InventoryItem::where('salon_id', $salonId)
            ->where('is_active', true)
            ->whereColumn('quantity_in_stock', '<=', 'reorder_level')
            ->get();

        // 6. Dynamic Real Bookings & Cancellation/No-Show Health
        $todayBookingsCount = Booking::where('salon_id', $salonId)
            ->whereDate('start_time', $today)
            ->where('status', '!=', 'cancelled')
            ->count();

        $upcomingBookingsCount = Booking::where('salon_id', $salonId)
            ->where('start_time', '>', now())
            ->where('status', 'confirmed')
            ->count();

        $monthCancellations = Booking::where('salon_id', $salonId)
            ->where('status', 'cancelled')
            ->where('created_at', '>=', $thisMonthStart)
            ->count();

        $monthNoShows = Booking::where('salon_id', $salonId)
            ->where('status', 'no_show')
            ->where('created_at', '>=', $thisMonthStart)
            ->count();

        // 7. Dynamic POS Register & Tips
        $todaySalesCount = PosSale::where('salon_id', $salonId)
            ->where('status', '!=', 'voided')
            ->whereDate('created_at', $today)
            ->count();

        $todayTips = (float) PosSale::where('salon_id', $salonId)
            ->whereDate('created_at', $today)
            ->sum('tip');

        $avgBasketSize = $todaySalesCount > 0 ? round($todayRevenue / $todaySalesCount, 2) : 0;

        // 8. Dynamic Staff Commissions
        $monthCommissions = (float) DB::table('staff_commissions')
            ->where('salon_id', $salonId)
            ->where('created_at', '>=', $thisMonthStart)
            ->sum('commission_amount');

        // 9. Inventory Asset Valuation
        $totalStockValuation = (float) InventoryItem::where('salon_id', $salonId)
            ->where('is_active', true)
            ->sum(DB::raw('quantity_in_stock * COALESCE(purchase_price, selling_price, 0)'));

        // 10. Memberships & Package Balances
        $activeMemberships = DB::table('customer_memberships')
            ->where('salon_id', $salonId)
            ->where('is_active', true)
            ->count();

        $activePackageBalances = DB::table('customer_package_balances')
            ->where('salon_id', $salonId)
            ->where('quantity_remaining', '>', 0)
            ->count();

        return [
            'today_revenue' => $todayRevenue,
            'this_month_revenue' => $thisMonthRevenue,
            'last_month_revenue' => $lastMonthRevenue,
            'total_customers' => $totalCustomers,
            'top_customer' => $topCustomer ? [
                'name' => $topCustomer->name,
                'total_spent' => (float) $topCustomer->total_spent,
                'phone' => $topCustomer->phone,
            ] : null,
            'top_service' => $topService ? [
                'name' => $topService->name,
                'bookings' => $topServiceRecord->total_bookings,
                'price' => (float) $topService->price,
            ] : null,
            'total_staff' => $totalStaff,
            'absent_staff_today' => $absentTodayStaffCount,
            'low_stock_count' => count($lowStockItems),
            'low_stock_items' => $lowStockItems->pluck('name')->toArray(),
            'today_bookings' => $todayBookingsCount,
            'upcoming_bookings' => $upcomingBookingsCount,
            'month_cancellations' => $monthCancellations,
            'month_no_shows' => $monthNoShows,
            'today_sales_count' => $todaySalesCount,
            'today_tips' => $todayTips,
            'avg_basket_size' => $avgBasketSize,
            'month_commissions' => $monthCommissions,
            'total_stock_valuation' => round($totalStockValuation, 2),
            'active_memberships' => $activeMemberships,
            'active_package_balances' => $activePackageBalances,
        ];
    }
}
