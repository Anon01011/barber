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
        Schema::table('pos_sales', function (Blueprint $table) {
            $table->decimal('tip', 8, 2)->default(0)->after('total');
            $table->decimal('cash_amount', 8, 2)->default(0)->after('tip');
            $table->decimal('card_amount', 8, 2)->default(0)->after('cash_amount');
            $table->decimal('outstanding_amount', 8, 2)->default(0)->after('card_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_sales', function (Blueprint $table) {
            $table->dropColumn(['tip', 'cash_amount', 'card_amount', 'outstanding_amount']);
        });
    }
};
