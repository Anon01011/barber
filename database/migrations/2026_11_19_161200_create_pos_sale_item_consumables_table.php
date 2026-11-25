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
        if (!Schema::hasTable('pos_sale_item_consumables')) {
            Schema::create('pos_sale_item_consumables', function (Blueprint $table) {
                $table->id();
                $table->foreignId('salon_id')->constrained('salons')->onDelete('cascade');
                $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
                $table->foreignId('pos_sale_id')->constrained('pos_sales')->onDelete('cascade');
                $table->foreignId('pos_sale_item_id')->constrained('pos_sale_items')->onDelete('cascade');
                $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('restrict');
                $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null')->comment('Staff who dispensed the product');
                $table->decimal('quantity_used', 10, 3)->comment('Actual quantity consumed in this service');
                $table->string('unit', 20)->default('pcs');
                $table->decimal('unit_cost', 10, 4)->comment('Material cost price per unit at moment of service');
                $table->decimal('total_cost', 10, 2)->comment('quantity_used * unit_cost');
                $table->decimal('billed_amount', 10, 2)->default(0.00)->comment('Any extra customer surcharge billed');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['salon_id', 'pos_sale_id']);
                $table->index(['pos_sale_item_id', 'inventory_item_id'], 'idx_sale_item_consumable');
                $table->index(['salon_id', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_sale_item_consumables');
    }
};
