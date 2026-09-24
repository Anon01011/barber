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
        Schema::create('membership_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained('memberships')->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->enum('discount_type', ['amount', 'percent'])->default('amount');
            $table->decimal('discount_value', 8, 2)->default(0);
            $table->decimal('membership_price', 8, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->unique(['membership_id', 'inventory_item_id'], 'membership_inventory_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_inventory_items');
    }
};
