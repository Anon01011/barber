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
        Schema::table('branch_inventory_item', function (Blueprint $table) {
            if (!Schema::hasColumn('branch_inventory_item', 'quantity')) {
                $table->decimal('quantity', 8, 4)->default(0);
            }
            if (!Schema::hasColumn('branch_inventory_item', 'minimum_quantity')) {
                $table->decimal('minimum_quantity', 8, 4)->default(0);
            }
            if (!Schema::hasColumn('branch_inventory_item', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_inventory_item', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'minimum_quantity', 'is_active']);
        });
    }
};
