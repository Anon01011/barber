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
        // First, update any existing NULL values to the default
        \DB::statement("UPDATE inventory_items SET reorder_level = 10 WHERE reorder_level IS NULL");
        
        // Then modify the column to have a default value
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->integer('reorder_level')->default(10)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->integer('reorder_level')->default(null)->nullable()->change();
        });
    }
};
