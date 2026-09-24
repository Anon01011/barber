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
        // 1. Drop Foreign Key if exists
        $fkExists = false;
        if (DB::getDriverName() === 'mysql') {
            $fkExists = DB::table('information_schema.TABLE_CONSTRAINTS')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'customer_package_balances')
                ->where('CONSTRAINT_NAME', 'customer_package_balances_customer_id_foreign')
                ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
                ->exists();
        }

        if ($fkExists) {
            Schema::table('customer_package_balances', function (Blueprint $table) {
                $table->dropForeign('customer_package_balances_customer_id_foreign');
            });
        }

        // 2 & 3. Drop Indices if exist
        $indicesToCheck = ['cust_pkg_srv_unique', 'unique_customer_package_service'];
        foreach ($indicesToCheck as $indexName) {
            $indexExists = false;
            if (DB::getDriverName() === 'mysql') {
                $indexExists = DB::table('information_schema.STATISTICS')
                    ->where('TABLE_SCHEMA', DB::getDatabaseName())
                    ->where('TABLE_NAME', 'customer_package_balances')
                    ->where('INDEX_NAME', $indexName)
                    ->exists();
            }

            if ($indexExists) {
                try {
                    Schema::table('customer_package_balances', function (Blueprint $table) use ($indexName) {
                        $table->dropUnique($indexName);
                    });
                } catch (\Exception $e) {}
            }
        }

        // 4 & 5. Add new constraints
        $newIndexExists = false;
        if (DB::getDriverName() === 'mysql') {
            $newIndexExists = DB::table('information_schema.STATISTICS')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'customer_package_balances')
                ->where('INDEX_NAME', 'cust_pkg_sale_srv_unique')
                ->exists();
        }

        if (!$newIndexExists) {
            try {
                Schema::table('customer_package_balances', function (Blueprint $table) {
                    $table->unique(['customer_id', 'package_id', 'pos_sale_id', 'service_id'], 'cust_pkg_sale_srv_unique');
                });
            } catch (\Exception $e) {}
        }

        Schema::table('customer_package_balances', function (Blueprint $table) {
            // Re-add Foreign Key (check if missing first? usually drop logic above ensures it's gone)
            // We can safely add it back since we checked/dropped it above. 
            // BUT if we are in a state where FK was dropped but we crash later, we need to ensure we don't double add if it somehow stayed?
            // Actually, Laravel's schema builder might error if it exists. 
            // Let's rely on the fact we dropped it if it existed.

            // Wait, if we dropped it, we should add it.
            // But if the script failed halfway before, FK might be gone, so we dropped nothing, now we add it. Correct.
            // If the script ran fully, FK is there. We drop, then add. Correct.

            // Verify if FK exists again just to be ultra safe against partial commits?
            // No, standard flow is fine.
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_package_balances', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropUnique('cust_pkg_sale_srv_unique');

            // Re-add the previous constraint (without pos_sale_id)
            $table->unique(['customer_id', 'package_id', 'service_id'], 'cust_pkg_srv_unique');

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
};
