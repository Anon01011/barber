<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Salon;
use App\Models\User;
use Illuminate\Support\Benchmark;

class MeasurePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:measure-performance {--salon= : The ID of the salon to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Measure performance of critical application pages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Performance Measurement...');

        $salonId = $this->option('salon');
        $salon = $salonId ? Salon::find($salonId) : Salon::first();

        if (!$salon) {
            $this->error('No salon found to test.');
            return;
        }

        $this->info("Testing Salon: {$salon->name} (ID: {$salon->id})");

        // Login as admin
        $admin = User::where('salon_id', $salon->id)->role('salon_admin')->first();
        if (!$admin) {
            $this->error('No admin user found for this salon.');
            return;
        }
        $this->info("Using Admin User: {$admin->email}");

        // Define pages to test (Internal simulation via code execution, not HTTP for now to test DB/Logic)
        $scenarios = [
            'Dashboard Stats' => function () use ($salon) {
                return $salon->getMonthlyRevenue() + $salon->getActiveCustomerCount();
            },
            'Recent Bookings Query' => function () use ($salon) {
                return $salon->bookings()->with(['customer', 'service', 'staff'])->latest()->take(50)->get();
            },
            'Sales Report Query' => function () use ($salon) {
                return $salon->payments()
                    ->whereBetween('created_at', [now()->subMonth(), now()])
                    ->selectRaw('DATE(created_at) as date, sum(amount) as total')
                    ->groupBy('date')
                    ->get();
            },
            'Customer Search' => function () use ($salon) {
                return $salon->customers()
                    ->where('name', 'like', '%a%')
                    ->orWhere('phone', 'like', '%555%')
                    ->take(20)
                    ->get();
            }
        ];

        foreach ($scenarios as $name => $callback) {
            $this->info("\nTesting: $name");

            // Measure DB Queries
            DB::enableQueryLog();
            $start = microtime(true);
            $callback();
            $duration = (microtime(true) - $start) * 1000; // ms
            $queries = DB::getQueryLog();
            DB::disableQueryLog();
            DB::flushQueryLog();

            $queryCount = count($queries);
            $this->line("  Duration: " . number_format($duration, 2) . " ms");
            $this->line("  Queries:  " . $queryCount);

            if ($duration > 500) {
                $this->warn("  [SLOW] Duration exceeds 500ms");
            }
            if ($queryCount > 50) {
                $this->warn("  [HIGH QUERIES] Query count exceeds 50");
            }
        }

        $this->info("\nPerformance Test Completed.");
    }
}
