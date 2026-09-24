<?php

namespace App\Services;

use App\Models\Salon;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AnalyticsService
{
    /**
     * Get salon overview statistics
     */
    public function getSalonOverview(Salon $salon, $period = 'month')
    {
        $cacheKey = "analytics_overview_{$salon->id}_{$period}";
        $cacheTime = $period === 'day' ? 3600 : 7200; // 1 hour for daily, 2 hours for others

        return Cache::remember($cacheKey, $cacheTime, function () use ($salon, $period) {
            $dateRange = $this->getDateRange($period);

            return [
                'revenue' => $this->getRevenueStats($salon, $dateRange),
                'bookings' => $this->getBookingStats($salon, $dateRange),
                'customers' => $this->getCustomerStats($salon, $dateRange),
                'services' => $this->getServiceStats($salon, $dateRange),
                'performance' => $this->getPerformanceMetrics($salon, $dateRange),
            ];
        });
    }

    /**
     * Get system-wide analytics for super admin
     */
    public function getSystemOverview($period = 'month')
    {
        $cacheKey = "analytics_system_{$period}";
        $cacheTime = 3600; // 1 hour

        return Cache::remember($cacheKey, $cacheTime, function () use ($period) {
            $dateRange = $this->getDateRange($period);

            return [
                'total_salons' => Salon::count(),
                'active_salons' => Salon::active()->count(),
                'total_revenue' => Payment::whereBetween('created_at', $dateRange)->sum('amount'),
                'total_bookings' => Booking::whereBetween('created_at', $dateRange)->count(),
                'total_customers' => Customer::whereBetween('created_at', $dateRange)->count(),
                'subscription_stats' => $this->getSubscriptionStats($dateRange),
                'growth_metrics' => $this->getGrowthMetrics($period),
            ];
        });
    }

    /**
     * Get revenue statistics
     */
    private function getRevenueStats(Salon $salon, array $dateRange)
    {
        $payments = Payment::where('salon_id', $salon->id)
            ->whereBetween('created_at', $dateRange)
            ->selectRaw('
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expenses,
                COUNT(*) as transaction_count
            ')
            ->first();

        $previousPeriod = $this->getPreviousPeriodRange($dateRange);
        $previousRevenue = Payment::where('salon_id', $salon->id)
            ->whereBetween('created_at', $previousPeriod)
            ->where('type', 'income')
            ->sum('amount');

        $currentRevenue = $payments->total_income ?? 0;
        $growth = $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;

        return [
            'current' => $currentRevenue,
            'previous' => $previousRevenue,
            'growth_percentage' => round($growth, 2),
            'expenses' => $payments->total_expenses ?? 0,
            'profit' => $currentRevenue - ($payments->total_expenses ?? 0),
            'transaction_count' => $payments->transaction_count ?? 0,
        ];
    }

    /**
     * Get booking statistics
     */
    private function getBookingStats(Salon $salon, array $dateRange)
    {
        $bookings = Booking::where('salon_id', $salon->id)
            ->whereBetween('created_at', $dateRange)
            ->selectRaw('
                COUNT(*) as total_bookings,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_bookings,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_bookings,
                SUM(CASE WHEN status = "no_show" THEN 1 ELSE 0 END) as no_show_bookings,
                AVG(TIMESTAMPDIFF(MINUTE, created_at, start_time)) as avg_advance_booking_hours
            ')
            ->first();

        $previousPeriod = $this->getPreviousPeriodRange($dateRange);
        $previousBookings = Booking::where('salon_id', $salon->id)
            ->whereBetween('created_at', $previousPeriod)
            ->count();

        $currentBookings = $bookings->total_bookings ?? 0;
        $growth = $previousBookings > 0 ? (($currentBookings - $previousBookings) / $previousBookings) * 100 : 0;

        return [
            'total' => $currentBookings,
            'completed' => $bookings->completed_bookings ?? 0,
            'cancelled' => $bookings->cancelled_bookings ?? 0,
            'no_show' => $bookings->no_show_bookings ?? 0,
            'completion_rate' => $currentBookings > 0 ? round(($bookings->completed_bookings ?? 0) / $currentBookings * 100, 2) : 0,
            'growth_percentage' => round($growth, 2),
            'avg_advance_booking_hours' => round(($bookings->avg_advance_booking_hours ?? 0) / 60, 1), // Convert to hours
        ];
    }

    /**
     * Get customer statistics
     */
    private function getCustomerStats(Salon $salon, array $dateRange)
    {
        $customers = Customer::where('salon_id', $salon->id)
            ->selectRaw('
                COUNT(*) as total_customers,
                SUM(CASE WHEN is_guest = 0 THEN 1 ELSE 0 END) as registered_customers,
                SUM(CASE WHEN is_guest = 1 THEN 1 ELSE 0 END) as guest_customers,
                SUM(total_spent) as total_customer_value,
                AVG(total_spent) as avg_customer_value
            ')
            ->first();

        $newCustomers = Customer::where('salon_id', $salon->id)
            ->whereBetween('created_at', $dateRange)
            ->count();

        $activeCustomers = Customer::where('salon_id', $salon->id)
            ->where('last_visit_at', '>=', now()->subDays(30))
            ->count();

        return [
            'total' => $customers->total_customers ?? 0,
            'registered' => $customers->registered_customers ?? 0,
            'guest' => $customers->guest_customers ?? 0,
            'new' => $newCustomers,
            'active' => $activeCustomers,
            'total_value' => $customers->total_customer_value ?? 0,
            'avg_value' => $customers->avg_customer_value ?? 0,
            'retention_rate' => $this->calculateRetentionRate($salon, $dateRange),
        ];
    }

    /**
     * Get service performance statistics
     */
    private function getServiceStats(Salon $salon, array $dateRange)
    {
        $services = Service::where('salon_id', $salon->id)
            ->withCount(['bookings' => function ($query) use ($dateRange) {
                $query->whereBetween('created_at', $dateRange);
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(10)
            ->get();

        $totalBookings = $services->sum('bookings_count');

        return $services->map(function ($service) use ($totalBookings) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'bookings_count' => $service->bookings_count,
                'percentage' => $totalBookings > 0 ? round(($service->bookings_count / $totalBookings) * 100, 2) : 0,
                'revenue' => $service->price * $service->bookings_count,
            ];
        });
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics(Salon $salon, array $dateRange)
    {
        // Peak hours analysis
        $peakHours = Booking::where('salon_id', $salon->id)
            ->whereBetween('created_at', $dateRange)
            ->selectRaw('HOUR(start_time) as hour, COUNT(*) as bookings_count')
            ->groupBy('hour')
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

        // Staff performance
        $staffPerformance = DB::table('bookings')
            ->join('users', 'bookings.staff_id', '=', 'users.id')
            ->where('bookings.salon_id', $salon->id)
            ->whereBetween('bookings.created_at', $dateRange)
            ->selectRaw('
                users.name,
                COUNT(bookings.id) as total_bookings,
                SUM(CASE WHEN bookings.status = "completed" THEN 1 ELSE 0 END) as completed_bookings,
                AVG(bookings.rating) as avg_rating
            ')
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_bookings', 'desc')
            ->take(10)
            ->get();

        return [
            'peak_hours' => $peakHours,
            'staff_performance' => $staffPerformance,
            'utilization_rate' => $this->calculateUtilizationRate($salon, $dateRange),
            'customer_satisfaction' => $this->calculateCustomerSatisfaction($salon, $dateRange),
        ];
    }

    /**
     * Get subscription statistics for system overview
     */
    private function getSubscriptionStats(array $dateRange)
    {
        return [
            'active_subscriptions' => \App\Models\Subscription::where('status', 'active')->count(),
            'trial_subscriptions' => \App\Models\Subscription::where('status', 'trialing')->count(),
            'cancelled_subscriptions' => \App\Models\Subscription::where('status', 'cancelled')
                ->whereBetween('updated_at', $dateRange)->count(),
            'new_subscriptions' => \App\Models\Subscription::whereBetween('created_at', $dateRange)->count(),
        ];
    }

    /**
     * Get growth metrics
     */
    private function getGrowthMetrics($period)
    {
        $currentRange = $this->getDateRange($period);
        $previousRange = $this->getPreviousPeriodRange($currentRange);

        $currentRevenue = Payment::whereBetween('created_at', $currentRange)->sum('amount');
        $previousRevenue = Payment::whereBetween('created_at', $previousRange)->sum('amount');

        $currentBookings = Booking::whereBetween('created_at', $currentRange)->count();
        $previousBookings = Booking::whereBetween('created_at', $previousRange)->count();

        $currentSalons = Salon::whereBetween('created_at', $currentRange)->count();
        $previousSalons = Salon::whereBetween('created_at', $previousRange)->count();

        return [
            'revenue_growth' => $previousRevenue > 0 ? round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 2) : 0,
            'booking_growth' => $previousBookings > 0 ? round((($currentBookings - $previousBookings) / $previousBookings) * 100, 2) : 0,
            'salon_growth' => $previousSalons > 0 ? round((($currentSalons - $previousSalons) / $previousSalons) * 100, 2) : 0,
        ];
    }

    /**
     * Calculate customer retention rate
     */
    private function calculateRetentionRate(Salon $salon, array $dateRange)
    {
        $startDate = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        // Customers who visited in the period
        $visitingCustomers = Customer::where('salon_id', $salon->id)
            ->whereHas('bookings', function ($query) use ($dateRange) {
                $query->whereBetween('created_at', $dateRange);
            })
            ->count();

        // Customers who visited in the previous period
        $previousPeriod = $this->getPreviousPeriodRange($dateRange);
        $previousCustomers = Customer::where('salon_id', $salon->id)
            ->whereHas('bookings', function ($query) use ($previousPeriod) {
                $query->whereBetween('created_at', $previousPeriod);
            })
            ->count();

        // Customers who visited in both periods
        $retainedCustomers = Customer::where('salon_id', $salon->id)
            ->whereHas('bookings', function ($query) use ($dateRange) {
                $query->whereBetween('created_at', $dateRange);
            })
            ->whereHas('bookings', function ($query) use ($previousPeriod) {
                $query->whereBetween('created_at', $previousPeriod);
            })
            ->count();

        return $previousCustomers > 0 ? round(($retainedCustomers / $previousCustomers) * 100, 2) : 0;
    }

    /**
     * Calculate utilization rate
     */
    private function calculateUtilizationRate(Salon $salon, array $dateRange)
    {
        // This is a simplified calculation - in reality, you'd need working hours data
        $totalWorkingHours = 8 * count($dateRange); // Assuming 8 hours per day
        $bookedHours = Booking::where('salon_id', $salon->id)
            ->whereBetween('created_at', $dateRange)
            ->where('status', 'completed')
            ->sum(DB::raw('TIMESTAMPDIFF(HOUR, start_time, end_time)'));

        return $totalWorkingHours > 0 ? round(($bookedHours / $totalWorkingHours) * 100, 2) : 0;
    }

    /**
     * Calculate customer satisfaction
     */
    private function calculateCustomerSatisfaction(Salon $salon, array $dateRange)
    {
        $avgRating = Booking::where('salon_id', $salon->id)
            ->whereBetween('created_at', $dateRange)
            ->whereNotNull('rating')
            ->avg('rating');

        return round($avgRating ?? 0, 2);
    }

    /**
     * Get date range for analytics
     */
    private function getDateRange($period)
    {
        $now = now();

        switch ($period) {
            case 'day':
                return [$now->startOfDay(), $now->endOfDay()];
            case 'week':
                return [$now->startOfWeek(), $now->endOfWeek()];
            case 'month':
                return [$now->startOfMonth(), $now->endOfMonth()];
            case 'quarter':
                return [$now->startOfQuarter(), $now->endOfQuarter()];
            case 'year':
                return [$now->startOfYear(), $now->endOfYear()];
            default:
                return [$now->startOfMonth(), $now->endOfMonth()];
        }
    }

    /**
     * Get previous period range for comparison
     */
    private function getPreviousPeriodRange(array $currentRange)
    {
        $start = Carbon::parse($currentRange[0]);
        $end = Carbon::parse($currentRange[1]);
        $diff = $start->diffInDays($end);

        return [
            $start->copy()->subDays($diff + 1),
            $end->copy()->subDays($diff + 1)
        ];
    }
}
