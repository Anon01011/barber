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
            // Drop global unique constraints
            $table->dropUnique(['sku']);
            $table->dropUnique(['barcode']);
            
            // Add scoped unique constraints
            $table->unique(['salon_id', 'sku']);
            $table->unique(['salon_id', 'barcode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            // Drop scoped unique constraints
            $table->dropUnique(['salon_id', 'sku']);
            $table->dropUnique(['salon_id', 'barcode']);
            
            // Add back global unique constraints
            $table->unique('sku');
            $table->unique('barcode');
        });
    }
};
