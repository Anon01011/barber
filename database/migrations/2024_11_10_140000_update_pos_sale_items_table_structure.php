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
        Schema::table('pos_sale_items', function (Blueprint $table) {
            // Add polymorphic fields if missing
            if (!Schema::hasColumn('pos_sale_items', 'item_id')) {
                $table->unsignedBigInteger('item_id')->nullable()->after('service_id');
            }
            if (!Schema::hasColumn('pos_sale_items', 'item_type')) {
                $table->string('item_type')->nullable()->after('item_id');
            }
            // Add tax_rate and tax_amount (tax is already there, but add rate)
            if (!Schema::hasColumn('pos_sale_items', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('pos_sale_items', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
            }
            // Add discount_amount (discount is already there)
            if (!Schema::hasColumn('pos_sale_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('discount');
            }
            // Add notes
            if (!Schema::hasColumn('pos_sale_items', 'notes')) {
                $table->text('notes')->nullable()->after('total');
            }
            // Add package_id after item_id
            if (!Schema::hasColumn('pos_sale_items', 'package_id')) {
                $table->unsignedBigInteger('package_id')->nullable()->after('item_id');
            }
            // Indexes - check if they exist before adding
            $existingIndexes = [];
            try {
                if (DB::getDriverName() === 'mysql') {
                    $indexes = DB::select("SHOW INDEXES FROM pos_sale_items WHERE Key_name != 'PRIMARY'");
                    $existingIndexes = array_column($indexes, 'Key_name');
                } else if (DB::getDriverName() === 'sqlite') {
                    $indexes = DB::select("PRAGMA index_list('pos_sale_items')");
                    $existingIndexes = array_column($indexes, 'name');
                }
            } catch (\Exception $e) {}

            if (!in_array('pos_sale_items_item_id_index', $existingIndexes) && Schema::hasColumn('pos_sale_items', 'item_id')) {
                try { $table->index('item_id'); } catch (\Exception $e) {}
            }
            if (!in_array('pos_sale_items_item_type_index', $existingIndexes) && Schema::hasColumn('pos_sale_items', 'item_type')) {
                try { $table->index('item_type'); } catch (\Exception $e) {}
            }
            if (!in_array('pos_sale_items_package_id_index', $existingIndexes) && Schema::hasColumn('pos_sale_items', 'package_id')) {
                try { $table->index('package_id'); } catch (\Exception $e) {}
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_sale_items', function (Blueprint $table) {
            $table->dropColumn(['item_id', 'item_type', 'tax_rate', 'tax_amount', 'discount_amount', 'notes', 'package_id']);
        });
    }
};
