<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'pos_sales',
            'pos_sale_items',
            'customer_package_balances',
            'bookings',
            'inventory_items',
            'inventory_transactions',
            'staff_commissions'
        ];

        foreach ($tables as $table) {
            if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                try {
                    DB::statement("ALTER TABLE `{$table}` ENGINE = InnoDB");
                } catch (\Exception $e) {
                    \Log::error("Failed to convert table {$table} to InnoDB: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Usually no need to revert to MyISAM unless explicitly required, 
        // as InnoDB is generally better. 
    }
};
