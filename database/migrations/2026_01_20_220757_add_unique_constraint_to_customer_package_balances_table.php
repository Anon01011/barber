<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, remove any duplicate records (keep the one with highest quantity_remaining)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                DELETE t1 FROM customer_package_balances t1
                INNER JOIN customer_package_balances t2 
                WHERE 
                    t1.id < t2.id AND
                    t1.customer_id = t2.customer_id AND
                    t1.package_id = t2.package_id AND
                    t1.service_id = t2.service_id
            ");
        }

        // Add unique constraint
        Schema::table('customer_package_balances', function (Blueprint $table) {
            $table->unique(['customer_id', 'package_id', 'service_id'], 'unique_customer_package_service');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_package_balances', function (Blueprint $table) {
            $table->dropUnique('unique_customer_package_service');
        });
    }
};
