<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->decimal('refund_fee', 8, 2)->default(0)->after('status');
            $table->enum('refund_fee_type', ['fixed', 'percentage'])->default('fixed')->after('refund_fee');
        });

        Schema::table('pos_refunds', function (Blueprint $table) {
            $table->decimal('fee_amount', 10, 2)->default(0)->after('amount');
            $table->decimal('net_refund_amount', 10, 2)->default(0)->after('fee_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->dropColumn(['refund_fee', 'refund_fee_type']);
        });

        Schema::table('pos_refunds', function (Blueprint $table) {
            $table->dropColumn(['fee_amount', 'net_refund_amount']);
        });
    }
};
