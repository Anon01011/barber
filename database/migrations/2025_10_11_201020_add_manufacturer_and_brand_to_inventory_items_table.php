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
        Schema::table('inventory_items', function (Blueprint $table) {
            // Check if columns exist before adding
            if (!Schema::hasColumn('inventory_items', 'manufacturer')) {
                $table->string('manufacturer')->nullable()->after('expiry_date');
            }
            if (!Schema::hasColumn('inventory_items', 'brand')) {
                $table->string('brand')->nullable()->after('manufacturer');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn(['manufacturer', 'brand']);
        });
    }
};
