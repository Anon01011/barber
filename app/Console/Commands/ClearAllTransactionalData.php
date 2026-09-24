<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearAllTransactionalData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clear-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all transactional data (bookings, sales, customers, etc.) while keeping master data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('This will delete ALL transactional data including bookings, sales, and customers. Are you sure?')) {
            return;
        }

        $this->info('Starting to clear transactional data...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            $tables = [
                'bookings',
                'booking_rejections',
                'appointments',
                'pos_sales',
                'pos_sale_items',
                'inventory_transactions',
                'payments',
                'customers',
                'customer_memberships',
                'customer_favorite_services',
                'reviews',
                'ratings',
                'staff_commissions',
                'system_notifications',
                'super_admin_audit_logs',
            ];

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->info("Cleared table: {$table}");
                } else {
                    $this->warn("Table not found: {$table}");
                }
            }

            // Optional: Reset auto-increment? Truncate does this automatically.

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('All transactional data cleared successfully.');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->error('Failed to clear data: ' . $e->getMessage());
        }
    }
}
