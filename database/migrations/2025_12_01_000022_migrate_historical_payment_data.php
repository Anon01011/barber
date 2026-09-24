<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all completed bookings to have paid status with cash as default
        DB::table('bookings')
            ->where('status', 'completed')
            ->whereNull('payment_status')
            ->update([
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'paid_at' => DB::raw('updated_at')
            ]);
            
        // Update all completed appointments to have paid status with cash as default
        if (Schema::hasTable('appointments')) {
            DB::table('appointments')
                ->where('status', 'completed')
                ->whereNull('payment_status')
                ->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'cash',
                    'paid_at' => DB::raw('updated_at')
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset payment fields for historical data
        DB::table('bookings')
            ->where('payment_method', 'cash')
            ->update([
                'payment_status' => 'pending',
                'payment_method' => null,
                'paid_at' => null
            ]);
            
        if (Schema::hasTable('appointments')) {
            DB::table('appointments')
                ->where('payment_method', 'cash')
                ->update([
                    'payment_status' => 'pending',
                    'payment_method' => null,
                    'paid_at' => null
                ]);
        }
    }
};
