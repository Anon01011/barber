<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Salon;
use App\Models\Booking;
use App\Models\Customer;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendDailySummary extends Command
{
    protected $signature = 'salon:send-daily-summary';
    protected $description = 'Send daily summary emails to salons that have it enabled';

    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        parent::__construct();
        $this->settingsService = $settingsService;
    }

    public function handle()
    {
        $this->info('Starting comprehensive daily summary process...');

        $salons = Salon::where('status', 'active')->get();
        $sentCount = 0;
        $skippedCount = 0;

        foreach ($salons as $salon) {
            try {
                // 1. Check if daily summary is enabled
                $enabled = $this->settingsService->get('daily_summary_enabled', false, $salon->id);
                if (!$enabled) {
                    $skippedCount++;
                    continue;
                }

                // 2. Timezone-aware timing check
                $salonTimezone = $this->settingsService->get('timezone', 'UTC', $salon->id);
                $nowInSalon = now($salonTimezone);
                $preferredTime = $this->settingsService->get('daily_summary_time', '09:00', $salon->id);

                // Run only if current time in salon matches preferred time (H:i)
                if ($nowInSalon->format('H:i') !== $preferredTime) {
                    // Optimization: If NOT running from scheduler, allow it for debug/manual runs
                    if (app()->runningInConsole() && !$this->option('force')) {
                        // Skip silently if scheduled time hasn't arrived
                        continue;
                    }
                }

                // 3. Get Recipient Email
                $notificationEmail = $this->settingsService->get('notification_email', null, $salon->id);
                if (!$notificationEmail) {
                    $notificationEmail = $this->settingsService->get('business_email', $salon->email, $salon->id);
                }

                if (!$notificationEmail) {
                    $this->warn("Salon {$salon->name} (ID: {$salon->id}) has no email configured. Skipping.");
                    $skippedCount++;
                    continue;
                }

                // 4. Gather metrics for "Yesterday" in Salon Timezone
                $yesterday = $nowInSalon->copy()->subDay();
                $startOfDay = $yesterday->copy()->startOfDay()->setTimezone('UTC');
                $endOfDay = $yesterday->copy()->endOfDay()->setTimezone('UTC');

                $stats = $this->gatherStats($salon->id, $startOfDay, $endOfDay, $nowInSalon);

                // 5. Dispatch Premium Email
                Mail::to($notificationEmail)->send(new \App\Mail\DailySummaryMail($salon, $stats, $yesterday));

                $sentCount++;
                $this->info("✓ Sent premium daily summary to {$salon->name} ({$notificationEmail})");

            } catch (\Exception $e) {
                $this->error("Failed to send summary for salon {$salon->name}: " . $e->getMessage());
                \Log::error("Daily summary error for salon {$salon->id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            }
        }

        $this->info("\nDaily summary process complete!");
        $this->info("Sent: {$sentCount} | Skipped: {$skippedCount}");

        return 0;
    }

    protected function gatherStats($salonId, $startOfDay, $endOfDay, $nowInSalon)
    {
        // 1. Booking Metrics (Yesterday)
        $bookings = Booking::where('salon_id', $salonId)
            ->whereBetween('start_time', [$startOfDay, $endOfDay])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending
            ')
            ->first();

        // 2. Revenue Metrics (Yesterday)
        // Combine POS and Bookings correctly (avoiding double counting by using POS status)
        $posRevenue = \App\Models\PosSale::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', '!=', 'voided')
            ->sum(DB::raw('total - tip'));

        $bookingRevenueDirect = Booking::where('salon_id', $salonId)
            ->whereBetween('start_time', [$startOfDay, $endOfDay])
            ->where('status', 'completed')
            ->whereDoesntHave('posSaleItem') // Prevent double counting with POS
            ->sum('amount');

        // 3. New Customers (Yesterday)
        $newCustomers = Customer::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->count();

        // 4. Staff Performance (Yesterday)
        $topStaff = DB::table('pos_sales')
            ->join('users', 'pos_sales.employee_id', '=', 'users.id')
            ->where('pos_sales.salon_id', $salonId)
            ->whereBetween('pos_sales.created_at', [$startOfDay, $endOfDay])
            ->where('pos_sales.status', '!=', 'voided')
            ->select('users.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(pos_sales.total - pos_sales.tip) as revenue'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        // 5. Today's Snapshot (Salon Timezone)
        $todayStart = $nowInSalon->copy()->startOfDay()->setTimezone('UTC');
        $todayEnd = $nowInSalon->copy()->endOfDay()->setTimezone('UTC');

        $upcomingToday = Booking::where('salon_id', $salonId)
            ->whereBetween('start_time', [$todayStart, $todayEnd])
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        return [
            'total_bookings' => $bookings->total ?? 0,
            'completed_bookings' => $bookings->completed ?? 0,
            'confirmed_bookings' => $bookings->confirmed ?? 0,
            'cancelled_bookings' => $bookings->cancelled ?? 0,
            'pending_bookings' => $bookings->pending ?? 0,
            'total_revenue' => (float) ($posRevenue + $bookingRevenueDirect),
            'new_customers' => $newCustomers,
            'staff_performance' => $topStaff,
            'upcoming_today' => $upcomingToday,
            'unpaid_amount' => (float) (
                Booking::where('salon_id', $salonId)
                    ->whereBetween('start_time', [$startOfDay, $endOfDay])
                    ->where('status', 'completed')
                    ->whereIn('payment_status', ['unpaid', 'pending'])
                    ->sum('amount')
                + \App\Models\PosSale::where('salon_id', $salonId)
                    ->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->where('status', '!=', 'voided')
                    ->sum('outstanding_amount')
            ),
            'partial_paid_amount' => (float) (\App\Models\PosSale::where('salon_id', $salonId)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->where('payment_status', 'partial')
                ->where('status', '!=', 'voided')
                ->sum(DB::raw('total - outstanding_amount - tip')))
        ];
    }
}
