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
        // Add unique constraint on customer_id per salon
        // This prevents race condition in customer ID generation
        Schema::table('customers', function (Blueprint $table) {
            if (\DB::getDriverName() === 'mysql') {
                \DB::statement('
                    UPDATE customers c1
                    LEFT JOIN (
                        SELECT salon_id, customer_id, MIN(id) as min_id
                        FROM customers
                        WHERE customer_id IS NOT NULL
                        GROUP BY salon_id, customer_id
                        HAVING COUNT(*) > 1
                    ) c2 ON c1.salon_id = c2.salon_id AND c1.customer_id = c2.customer_id AND c1.id != c2.min_id
                    SET c1.customer_id = CONCAT(c1.customer_id, "-DUP-", c1.id)
                    WHERE c2.min_id IS NOT NULL
                ');
            }

            // Add unique constraint
            $table->unique(['salon_id', 'customer_id'], 'unique_salon_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('unique_salon_customer_id');
        });
    }
};
