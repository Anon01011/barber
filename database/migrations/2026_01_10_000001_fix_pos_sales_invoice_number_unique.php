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
        Schema::table('pos_sales', function (Blueprint $table) {
            // Drop the existing unique index on invoice_number
            // Note: The index name is usually table_column_unique
            $table->dropUnique('pos_sales_invoice_number_unique');

            // Add a composite unique index on salon_id and invoice_number
            $table->unique(['salon_id', 'invoice_number'], 'pos_sales_salon_invoice_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_sales', function (Blueprint $table) {
            $table->dropUnique('pos_sales_salon_invoice_unique');
            $table->unique('invoice_number', 'pos_sales_invoice_number_unique');
        });
    }
};
