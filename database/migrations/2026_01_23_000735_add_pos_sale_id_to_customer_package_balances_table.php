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
        Schema::table('customer_package_balances', function (Blueprint $table) {
            $table->unsignedBigInteger('pos_sale_id')->nullable()->after('customer_id');
            // We can add a foreign key constraint if pos_sales table exists and we want strict integrity
            // But usually safer to check if table exists first or just index it
            if (Schema::hasTable('pos_sales')) {
                $table->foreign('pos_sale_id')->references('id')->on('pos_sales')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_package_balances', function (Blueprint $table) {
            if (Schema::hasColumn('customer_package_balances', 'pos_sale_id')) {
                // Drop foreign key first if it exists
                $conn = Schema::getConnection();
                $dbSchemaManager = $conn->getDoctrineSchemaManager();
                $tableDetails = $dbSchemaManager->listTableDetails('customer_package_balances');

                if ($tableDetails->hasForeignKey('customer_package_balances_pos_sale_id_foreign')) {
                    $table->dropForeign(['pos_sale_id']);
                }

                $table->dropColumn('pos_sale_id');
            }
        });
    }
};
