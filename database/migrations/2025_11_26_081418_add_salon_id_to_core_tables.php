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
        $tables = [
            'bookings',
            'services',
            'customers',
            'employees',
            'inventory_items',
            'pos_sales',
            'service_categories',
            'inventory_categories',
            'packages',
            'memberships'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'salon_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('salon_id')->nullable()->constrained('salons')->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_tables', function (Blueprint $table) {
            //
        });
    }
};
