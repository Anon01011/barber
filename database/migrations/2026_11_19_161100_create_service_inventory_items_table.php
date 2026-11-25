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
        if (!Schema::hasTable('service_inventory_items')) {
            Schema::create('service_inventory_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('salon_id')->constrained('salons')->onDelete('cascade');
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
                $table->decimal('quantity', 10, 3)->comment('Standard recipe quantity (e.g. 45.000 for ml/g, 1.000 for pcs)');
                $table->string('unit', 20)->default('pcs')->comment('ml, g, pcs, pump, drops, foil');
                $table->boolean('is_optional')->default(false)->comment('If true, optional service add-on');
                $table->boolean('is_billable_extra')->default(false)->comment('If true, extra charge passed to client beyond base service fee');
                $table->decimal('extra_price_per_unit', 10, 2)->default(0.00)->comment('Customer surcharge per extra unit');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['salon_id', 'service_id']);
                $table->index(['service_id', 'inventory_item_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_inventory_items');
    }
};
