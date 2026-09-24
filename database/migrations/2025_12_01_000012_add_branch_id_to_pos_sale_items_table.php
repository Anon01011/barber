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
        if (!Schema::hasColumn('pos_sale_items', 'branch_id')) {
            Schema::table('pos_sale_items', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('sale_id')->constrained('branches')->onDelete('cascade');
            });

            // Add index safely
            $indexExists = false;
            if (DB::getDriverName() === 'mysql') {
                $indexes = DB::select("SHOW INDEXES FROM pos_sale_items WHERE Key_name = 'pos_sale_items_branch_id_index'");
                $indexExists = count($indexes) > 0;
            }

            if (!$indexExists) {
                // Schema::table('pos_sale_items', function (Blueprint $table) {
                //     $table->index('branch_id');
                // });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pos_sale_items', 'branch_id')) {
            Schema::table('pos_sale_items', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropIndex(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
