<?php

namespace App\Services;

use App\Models\Salon;
use App\Models\Booking;
use App\Models\PosSale;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AiAnalyticsService
{
    /**
     * Get complete real database AI & ML insights report for a salon.
     */
    public function getAiInsights(Salon $salon): array
    {
        $cacheKey = "ai_insights_salon_{$salon->id}_v4";
        
        return Cache::remember($cacheKey, 300, function () use ($salon) {
            $forecast = $this->generateDemandForecast($salon);
            $churnRisk = $this->calculateCustomerChurnRisk($salon);
            $staffYield = $this->analyzeStaffUtilization($salon);
            $peakHours = $this->analyzePeakHours($salon);
            $inventoryForecast = $this->predictInventoryDepletion($salon);
            $serviceMatrix = $this->analyzeServiceMatrix($salon);
            $customerSegmentation = $this->analyzeCustomerSegmentation($salon);
            $executiveSummary = $this->generateExecutiveSummary($salon, $forecast, $churnRisk, $staffYield, $inventoryForecast, $serviceMatrix);

            // Instant instantiation of new predictive engines for comprehensive hub insights
            $predictiveEngine = new \App\Services\AI\AiPredictiveAnalyticsEngine();
            $marketingEngine = new \App\Services\AI\AiMarketingGeneratorEngine();

            $noShowRisk = $predictiveEngine->predictNoShowRisk($salon);
            $demandSurges = $predictiveEngine->predictDemandSurges($salon);
            $upsellRules = $predictiveEngine->getUpsellRecommendations($salon);
            $milestones = $marketingEngine->getUpcomingMilestoneOffers($salon);
            $reviewAnalysis = $marketingEngine->analyzeReviews($salon);

            return [
                'executive_summary' => $executiveSummary,
                'forecast' => $forecast,
                'churn_risk' => $churnRisk,
                'staff_yield' => $staffYield,
                'peak_hours' => $peakHours,
                'inventory_forecast' => $inventoryForecast,
                'service_matrix' => $serviceMatrix,
                'customer_segmentation' => $customerSegmentation,
                'no_show_risk' => $noShowRisk,
                'demand_surges' => $demandSurges,
                'upsell_rules' => $upsellRules,
                'milestones' => $milestones,
                'review_analysis' => $reviewAnalysis,
                'generated_at' => now()->format('Y-m-d H:i:s'),
            ];
        });
    }

    /**
     * Time-Series Demand Forecasting (100% Real DB Data)
     */
    public function generateDemandForecast(Salon $salon): array
    {
        // 60 days historical data
        $startDate = now()->subDays(60)->startOfDay();
        $endDate = now()->endOfDay();

        $historicalSales = DB::table('pos_sales')
            ->where('salon_id', $salon->id)
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'voided')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as daily_total'), DB::raw('COUNT(id) as sale_count'))
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $historicalBookings = DB::table('bookings')
            ->where('salon_id', $salon->id)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(id) as booking_count'))
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Calculate 30-day baseline average without dummy fallbacks
        $last30DaysTotal = 0;
        $last30DaysBookings = 0;

        for ($i = 0; $i < 30; $i++) {
            $dateKey = now()->subDays($i)->toDateString();
            $last30DaysTotal += (float) ($historicalSales[$dateKey]->daily_total ?? 0);
            $last30DaysBookings += (int) ($historicalBookings[$dateKey]->booking_count ?? 0);
        }

        $avgDailyRevenue = $last30DaysTotal > 0 ? ($last30DaysTotal / 30) : 0;
        $avgDailyBookings = $last30DaysBookings > 0 ? ($last30DaysBookings / 30) : 0;

        // Compare last 30 vs previous 30 to determine real velocity
        $prev30DaysTotal = 0;
        for ($i = 30; $i < 60; $i++) {
            $dateKey = now()->subDays($i)->toDateString();
            $prev30DaysTotal += (float) ($historicalSales[$dateKey]->daily_total ?? 0);
        }

        $growthMultiplier = 1.0;
        if ($prev30DaysTotal > 0 && $last30DaysTotal > 0) {
            $growthMultiplier = max(0.7, min(1.4, $last30DaysTotal / $prev30DaysTotal));
        }

        // Generate 30-day future predictions with day-of-week seasonality
        $forecast = [];
        $totalPredictedRevenue = 0;
        $totalPredictedBookings = 0;

        for ($d = 1; $d <= 30; $d++) {
            $futureDate = now()->addDays($d);
            $dayOfWeek = $futureDate->dayOfWeek; // 0 = Sun, 6 = Sat

            // Seasonality factor (Weekends +20%, Mondays -10%)
            $seasonalityFactor = match ($dayOfWeek) {
                5, 6 => 1.20,
                0 => 1.10,
                1 => 0.90,
                default => 1.0,
            };

            $predictedRev = round($avgDailyRevenue * $growthMultiplier * $seasonalityFactor, 2);
            $predictedBookings = (int) round($avgDailyBookings * $growthMultiplier * $seasonalityFactor);

            $forecast[] = [
                'date' => $futureDate->format('Y-m-d'),
                'day_name' => $futureDate->format('D'),
                'predicted_revenue' => $predictedRev,
                'predicted_bookings' => $predictedBookings,
            ];

            $totalPredictedRevenue += $predictedRev;
            $totalPredictedBookings += $predictedBookings;
        }

        $growthTrendPercentage = 0;
        if ($prev30DaysTotal > 0) {
            $growthTrendPercentage = round((($last30DaysTotal - $prev30DaysTotal) / $prev30DaysTotal) * 100, 1);
        }

        return [
            'daily_forecast' => $forecast,
            'projected_30day_revenue' => round($totalPredictedRevenue, 2),
            'projected_30day_bookings' => $totalPredictedBookings,
            'growth_rate_trend' => $growthTrendPercentage,
            'avg_daily_revenue' => round($avgDailyRevenue, 2),
            'last_30_revenue' => round($last30DaysTotal, 2),
            'prev_30_revenue' => round($prev30DaysTotal, 2),
        ];
    }

    /**
     * Customer Churn Risk Classification (Real CRM Heuristics)
     */
    public function calculateCustomerChurnRisk(Salon $salon): array
    {
        $customers = Customer::where('salon_id', $salon->id)
            ->where('status', 'active')
            ->withCount(['bookings' => function ($q) {
                $q->where('status', '!=', 'cancelled');
            }])
            ->get();

        $highRisk = [];
        $mediumRisk = [];
        $lowRisk = [];

        foreach ($customers as $customer) {
            $lastVisit = $customer->last_visit_at ?? $customer->created_at;
            $daysSinceLastVisit = Carbon::parse($lastVisit)->diffInDays(now());
            $visitCount = $customer->bookings_count ?? 0;
            $totalSpent = (float) ($customer->total_spent ?? 0);

            // Risk Scoring Logic
            $riskScore = 0;
            if ($daysSinceLastVisit > 90) {
                $riskScore += 60;
            } elseif ($daysSinceLastVisit > 45) {
                $riskScore += 35;
            } elseif ($daysSinceLastVisit > 30) {
                $riskScore += 15;
            }

            if ($visitCount >= 2 && $daysSinceLastVisit > 45) {
                $riskScore += 25; // Repeat customer slipping away
            }

            $customerData = [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone ?? 'On file',
                'email' => $customer->email ?? 'N/A',
                'days_since_last_visit' => $daysSinceLastVisit,
                'total_visits' => $visitCount,
                'total_spent' => $totalSpent,
                'risk_score' => min(100, $riskScore),
                'recommended_action' => $daysSinceLastVisit > 60 
                    ? 'Dispatch 20% Retention Voucher' 
                    : 'Dispatch Reminder SMS',
            ];

            if ($riskScore >= 50) {
                $highRisk[] = $customerData;
            } elseif ($riskScore >= 25) {
                $mediumRisk[] = $customerData;
            } else {
                $lowRisk[] = $customerData;
            }
        }

        // Sort high risk by highest lifetime spend
        usort($highRisk, fn($a, $b) => $b['total_spent'] <=> $a['total_spent']);

        return [
            'total_customers_evaluated' => count($customers),
            'high_risk_count' => count($highRisk),
            'medium_risk_count' => count($mediumRisk),
            'low_risk_count' => count($lowRisk),
            'high_risk_customers' => array_slice($highRisk, 0, 10),
            'churn_rate_percentage' => count($customers) > 0 ? round((count($highRisk) / count($customers)) * 100, 1) : 0,
        ];
    }

    /**
     * Staff Performance & Occupancy Analysis
     */
    public function analyzeStaffUtilization(Salon $salon): array
    {
        $staffMembers = User::where('salon_id', $salon->id)
            ->role('employee')
            ->where('status', 'active')
            ->get();

        $startDate = now()->subDays(30)->startOfDay();
        $endDate = now()->endOfDay();

        $staffAnalysis = [];

        foreach ($staffMembers as $staff) {
            $completedBookings = Booking::where('salon_id', $salon->id)
                ->where('staff_id', $staff->id)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $totalBookedMinutes = 0;
            $totalRevenue = 0;

            foreach ($completedBookings as $b) {
                $duration = Carbon::parse($b->start_time)->diffInMinutes(Carbon::parse($b->end_time));
                $totalBookedMinutes += max(15, $duration);
                $totalRevenue += (float) $b->amount;
            }

            // Assume standard 160 working hours / month = 9,600 minutes
            $availableMinutes = 9600;
            $occupancyRate = round(($totalBookedMinutes / $availableMinutes) * 100, 1);

            $staffAnalysis[] = [
                'staff_id' => $staff->id,
                'staff_name' => $staff->name,
                'completed_bookings' => count($completedBookings),
                'total_revenue' => round($totalRevenue, 2),
                'occupancy_rate' => min(100, $occupancyRate),
                'status_label' => $occupancyRate > 70 ? 'High Yield' : ($occupancyRate < 25 ? 'Available Capacity' : 'Optimal'),
            ];
        }

        usort($staffAnalysis, fn($a, $b) => $b['total_revenue'] <=> $a['total_revenue']);

        return [
            'staff_metrics' => $staffAnalysis,
            'total_staff' => count($staffMembers),
            'avg_salon_occupancy' => count($staffAnalysis) > 0 ? round(array_sum(array_column($staffAnalysis, 'occupancy_rate')) / count($staffAnalysis), 1) : 0,
        ];
    }

    /**
     * Peak Hours & Booking Distribution Matrix
     */
    public function analyzePeakHours(Salon $salon): array
    {
        $hourlyDistribution = DB::table('bookings')
            ->where('salon_id', $salon->id)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [now()->subDays(60), now()])
            ->select(DB::raw('HOUR(start_time) as hour'), DB::raw('COUNT(id) as booking_count'))
            ->groupBy('hour')
            ->pluck('booking_count', 'hour')
            ->toArray();

        $formattedHours = [];
        $peakHour = null;
        $maxCount = 0;

        for ($h = 8; $h <= 20; $h++) {
            $count = (int) ($hourlyDistribution[$h] ?? 0);
            $formattedHours[] = [
                'hour_label' => date('g A', strtotime("{$h}:00")),
                'count' => $count,
                'is_peak' => false,
            ];

            if ($count > $maxCount) {
                $maxCount = $count;
                $peakHour = date('g A', strtotime("{$h}:00"));
            }
        }

        foreach ($formattedHours as &$item) {
            if ($maxCount > 0 && $item['count'] >= ($maxCount * 0.75)) {
                $item['is_peak'] = true;
            }
        }

        return [
            'hourly_data' => $formattedHours,
            'busiest_hour' => $peakHour ?? 'Afternoon Peak',
            'yield_recommendation' => 'Publish 15% off morning slot vouchers (9 AM - 11 AM) to rebalance customer bookings from peak hours.',
        ];
    }

    /**
     * Predict Inventory Stock Depletion & Reorder Urgency
     */
    public function predictInventoryDepletion(Salon $salon): array
    {
        $items = InventoryItem::where('salon_id', $salon->id)
            ->where('is_active', true)
            ->get();

        $depletionList = [];

        foreach ($items as $item) {
            $unitsSold = DB::table('pos_sale_items')
                ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
                ->where('pos_sales.salon_id', $salon->id)
                ->where('pos_sale_items.item_id', $item->id)
                ->whereIn('pos_sale_items.item_type', ['product', \App\Models\InventoryItem::class])
                ->where('pos_sales.created_at', '>=', now()->subDays(30))
                ->sum('pos_sale_items.quantity');

            // Daily consumption based strictly on real POS items sold
            $dailyConsumption = $unitsSold > 0 ? ($unitsSold / 30) : 0;
            $daysLeft = $dailyConsumption > 0 ? (int) floor($item->quantity_in_stock / $dailyConsumption) : 999;

            if ($item->quantity_in_stock <= $item->reorder_level || $daysLeft <= 14) {
                $depletionList[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'current_stock' => $item->quantity_in_stock,
                    'reorder_level' => $item->reorder_level,
                    'estimated_days_left' => $daysLeft > 365 ? '30+' : (string) $daysLeft,
                    'urgency' => ($item->quantity_in_stock <= ($item->reorder_level / 2) || $daysLeft <= 3) ? 'CRITICAL' : 'WARNING',
                ];
            }
        }

        return [
            'at_risk_items_count' => count($depletionList),
            'items' => array_slice($depletionList, 0, 8),
        ];
    }

    /**
     * Top Service Category & Revenue Performance Matrix
     */
    public function analyzeServiceMatrix(Salon $salon): array
    {
        $services = Service::where('salon_id', $salon->id)
            ->active()
            ->withCount(['bookings' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->get();

        $matrix = [];
        foreach ($services as $service) {
            $revenue = (float) DB::table('bookings')
                ->where('salon_id', $salon->id)
                ->where('service_id', $service->id)
                ->where('status', 'completed')
                ->sum('amount');

            $matrix[] = [
                'id' => $service->id,
                'name' => $service->name,
                'price' => (float) $service->price,
                'completed_bookings' => $service->bookings_count ?? 0,
                'total_revenue' => $revenue,
            ];
        }

        usort($matrix, fn($a, $b) => $b['total_revenue'] <=> $a['total_revenue']);

        return array_slice($matrix, 0, 5);
    }

    /**
     * Customer CRM Lifetime Value & Segmentation
     */
    public function analyzeCustomerSegmentation(Salon $salon): array
    {
        $totalCustomers = Customer::where('salon_id', $salon->id)->count();
        $vipCustomers = Customer::where('salon_id', $salon->id)->where('total_spent', '>=', 500)->count();
        $regularCustomers = Customer::where('salon_id', $salon->id)->whereBetween('total_spent', [100, 499.99])->count();
        $newCustomers = Customer::where('salon_id', $salon->id)->where('created_at', '>=', now()->subDays(30))->count();

        return [
            'total_customers' => $totalCustomers,
            'vip_count' => $vipCustomers,
            'regular_count' => $regularCustomers,
            'new_30day_count' => $newCustomers,
        ];
    }

    /**
     * Executive AI Natural Language Summary
     */
    private function generateExecutiveSummary(Salon $salon, array $forecast, array $churnRisk, array $staffYield, array $inventoryForecast, array $serviceMatrix): array
    {
        $bullets = [];

        // 1. Revenue Velocity Bullet
        $trend = $forecast['growth_rate_trend'];
        if ($forecast['last_30_revenue'] > 0) {
            $bullets[] = "30-Day Revenue performance totals **" . format_currency($forecast['last_30_revenue']) . "**. Forecasted 30-day future demand project totals **" . format_currency($forecast['projected_30day_revenue']) . "** (" . ($trend >= 0 ? "+{$trend}% growth velocity" : "{$trend}% velocity") . ").";
        } else {
            $bullets[] = "No completed POS transactions recorded in the baseline period. Target afternoon walk-ins and dynamic slot promotions to initiate sales momentum.";
        }

        // 2. Customer Retention Bullet
        $highRisk = $churnRisk['high_risk_count'];
        if ($highRisk > 0) {
            $bullets[] = "**{$highRisk} customer(s)** identified with churn risk (>45 days since last visit). Executing a automated retention campaign is recommended.";
        } else {
            $bullets[] = "Customer retention is operating within healthy parameters across registered CRM clients.";
        }

        // 3. Staff & Capacity Bullet
        $avgOccupancy = $staffYield['avg_salon_occupancy'];
        $totalStaff = $staffYield['total_staff'];
        $bullets[] = "Staff team includes **{$totalStaff} active employee(s)** with an average occupancy yield of **{$avgOccupancy}%**.";

        // 4. Inventory Stockouts Bullet
        $stockRisk = $inventoryForecast['at_risk_items_count'];
        if ($stockRisk > 0) {
            $bullets[] = "**{$stockRisk} inventory product(s)** require reordering. Generate a draft purchase order to maintain inventory stock levels.";
        } else {
            $bullets[] = "All inventory stock items are above reorder thresholds.";
        }

        return $bullets;
    }
}
