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
            if (!Schema::hasColumn('inventory_items', 'product_type')) {
                $table->string('product_type', 30)->default('retail')->after('category_id')->comment('retail, consumable, dual');
            }
            if (!Schema::hasColumn('inventory_items', 'pack_capacity')) {
                $table->decimal('pack_capacity', 10, 2)->default(1.00)->after('product_type')->comment('Total units or ml/g inside one package');
            }
            if (!Schema::hasColumn('inventory_items', 'base_unit')) {
                $table->string('base_unit', 20)->default('pcs')->after('pack_capacity')->comment('Base dispensing unit: ml, g, pcs, pump, drops');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('inventory_items', 'product_type')) {
                $columnsToDrop[] = 'product_type';
            }
            if (Schema::hasColumn('inventory_items', 'pack_capacity')) {
                $columnsToDrop[] = 'pack_capacity';
            }
            if (Schema::hasColumn('inventory_items', 'base_unit')) {
                $columnsToDrop[] = 'base_unit';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
