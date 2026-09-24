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
            $table->string('payment_status')->default('paid')->after('quantity_remaining');
        });

        // Update existing records to 'paid' status (assuming they were created when packages were paid)
        DB::table('customer_package_balances')->update(['payment_status' => 'paid']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_package_balances', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};
