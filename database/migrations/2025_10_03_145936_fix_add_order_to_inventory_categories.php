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
        if (Schema::hasTable('inventory_categories') && !Schema::hasColumn('inventory_categories', 'order')) {
            Schema::table('inventory_categories', function (Blueprint $table) {
                $table->integer('order')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_categories', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_categories', 'order')) {
                $table->dropColumn('order');
            }
        });
    }
};
