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
        // Drop the existing global unique constraint on SKU
        try {
            DB::statement('ALTER TABLE products DROP INDEX products_sku_unique');
        } catch (\Exception $e) {
            // Index might not exist, that's okay
        }
        
        Schema::table('products', function (Blueprint $table) {
            // Create composite unique index: same SKU can exist in different branches
            // but must be unique within a salon-branch combination
            $table->unique(['salon_id', 'branch_id', 'sku'], 'products_salon_branch_sku_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the composite unique index
            $table->dropUnique('products_salon_branch_sku_unique');
            
            // Restore the global SKU unique constraint
            $table->unique('sku', 'products_sku_unique');
        });
    }
};
