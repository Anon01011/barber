<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Skip if pos_sale_items table already exists (created by earlier migration)
        if (!Schema::hasTable('pos_sale_items')) {
            Schema::create('pos_sale_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sale_id')->constrained('pos_sales')->onDelete('cascade');
                $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
                $table->string('item_name');
                $table->text('description')->nullable();
                $table->integer('quantity');
                $table->decimal('unit_price', 10, 2);
                $table->decimal('discount', 10, 2)->default(0);
                $table->decimal('tax', 10, 2)->default(0);
                $table->decimal('subtotal', 10, 2);
                $table->decimal('total', 10, 2);
                $table->timestamps();

                // Add indexes for better performance
                $table->index('sale_id');
                $table->index('service_id');
                $table->index(['created_at', 'updated_at']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('pos_sale_items');
    }
}; 