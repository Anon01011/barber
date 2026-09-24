<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('bookings', 'payment_method')) {
                    $table->enum('payment_method', ['cash', 'card', 'upi', 'bank_transfer'])
                        ->nullable()
                        ->after('amount');
                }
                
                if (!Schema::hasColumn('bookings', 'payment_status')) {
                    $table->enum('payment_status', ['pending', 'paid', 'refunded'])
                        ->default('pending')
                        ->after('payment_method');
                }
                
                if (!Schema::hasColumn('bookings', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('payment_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'paid_at']);
        });
    }
};