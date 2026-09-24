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
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'payment_method')) {
                $table->enum('payment_method', ['cash', 'card', 'upi', 'bank_transfer'])
                    ->nullable()
                    ->after('end_time');
            }
            
            if (!Schema::hasColumn('appointments', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'refunded'])
                    ->default('pending')
                    ->after('payment_method');
            }
            
            if (!Schema::hasColumn('appointments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_status');
            }
            
            if (!Schema::hasColumn('appointments', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable()->after('end_time');
            }
            
            if (!Schema::hasColumn('appointments', 'tip_amount')) {
                $table->decimal('tip_amount', 10, 2)->default(0)->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'paid_at', 'amount', 'tip_amount']);
        });
    }
};
