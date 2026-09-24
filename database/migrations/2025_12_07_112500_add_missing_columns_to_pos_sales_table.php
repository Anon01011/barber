<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pos_sales', function (Blueprint $table) {
            // Add missing columns
            if (!Schema::hasColumn('pos_sales', 'tip')) {
                $table->decimal('tip', 10, 2)->default(0)->after('discount');
            }
            if (!Schema::hasColumn('pos_sales', 'cash_amount')) {
                $table->decimal('cash_amount', 10, 2)->default(0)->after('tip');
            }
            if (!Schema::hasColumn('pos_sales', 'card_amount')) {
                $table->decimal('card_amount', 10, 2)->default(0)->after('cash_amount');
            }
            if (!Schema::hasColumn('pos_sales', 'outstanding_amount')) {
                $table->decimal('outstanding_amount', 10, 2)->default(0)->after('card_amount');
            }
            if (!Schema::hasColumn('pos_sales', 'sale_date')) {
                $table->timestamp('sale_date')->nullable()->after('notes');
            }
        });
    }

    public function down()
    {
        Schema::table('pos_sales', function (Blueprint $table) {
            $table->dropColumn(['tip', 'cash_amount', 'card_amount', 'outstanding_amount', 'sale_date']);
        });
    }
};
