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
        // Check if products table exists, if not create it
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('salon_id')->constrained('salons')->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('sku')->nullable()->unique();
                $table->string('barcode')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->decimal('cost_price', 10, 2)->default(0);
                $table->foreignId('category_id')->nullable()->constrained('inventory_categories')->onDelete('set null');
                $table->string('brand')->nullable();
                $table->integer('stock_quantity')->default(0);
                $table->integer('low_stock_threshold')->default(10);
                $table->boolean('is_active')->default(true);
                $table->string('image_path')->nullable();
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->boolean('is_taxable')->default(true);
                $table->timestamps();
                $table->softDeletes();
                
                $table->index('salon_id');
                $table->index('category_id');
                $table->index('is_active');
            });
        } else {
            // If table exists, ensure it has salon_id
            if (!Schema::hasColumn('products', 'salon_id')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->foreignId('salon_id')->nullable()->after('id');
                    $table->index('salon_id');
                });
                
                // Set a default salon_id if there are existing records
                // You may need to adjust this based on your data
                DB::statement('UPDATE products SET salon_id = 1 WHERE salon_id IS NULL');
                
                Schema::table('products', function (Blueprint $table) {
                    $table->foreignId('salon_id')->nullable(false)->change();
                    $table->foreign('salon_id')->references('id')->on('salons')->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
