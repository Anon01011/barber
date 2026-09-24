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
        // Create inventory_categories table
        if (!Schema::hasTable('inventory_categories')) {
            Schema::create('inventory_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('salon_id')->nullable()->constrained('salons')->onDelete('cascade');
                $table->string('name');
                $table->string('slug')->nullable()->unique();
                $table->text('description')->nullable();
                $table->string('icon')->default('box');
                $table->string('color')->default('#6c757d');
                $table->boolean('is_active')->default(true);
                $table->foreignId('parent_id')->nullable()->constrained('inventory_categories')->nullOnDelete();
                $table->integer('order')->default(0);
                $table->string('meta_title')->nullable();
                $table->string('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Add indexes
                $table->index('name');
                $table->index('is_active');
            });
        }

        // Create suppliers table
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('salon_id')->nullable()->constrained('salons')->onDelete('cascade');
                $table->string('name');
                $table->string('contact_person')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('tax_number')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Add indexes
                $table->index('name');
                $table->index('email');
                $table->index('is_active');
            });
        }

        // Create inventory_items table
        if (!Schema::hasTable('inventory_items')) {
            Schema::create('inventory_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('salon_id')->nullable()->constrained('salons')->onDelete('cascade');
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('category_id')
                    ->nullable()
                    ->constrained('inventory_categories')
                    ->nullOnDelete();
                $table->foreignId('supplier_id')
                    ->nullable()
                    ->constrained('suppliers')
                    ->nullOnDelete();
                $table->string('name');
                $table->string('slug')->nullable()->unique();
                $table->string('sku')->unique();
                $table->string('barcode')->nullable()->unique();
                $table->text('description')->nullable();
                $table->text('notes')->nullable();
                $table->decimal('purchase_price', 10, 2)->default(0);
                $table->decimal('selling_price', 10, 2)->default(0);
                $table->decimal('quantity_in_stock', 12, 4)->default(0);
                $table->decimal('minimum_quantity', 12, 4)->default(0);
                $table->integer('reorder_level')->default(5);
                $table->string('unit_type')->default('pcs');
                $table->string('location')->nullable();
                $table->enum('stock_status', ['in_stock', 'low_stock', 'out_of_stock'])->default('in_stock');
                $table->date('expiry_date')->nullable();
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->string('dimensions')->nullable();
                $table->string('manufacturer')->nullable();
                $table->string('brand')->nullable();
                $table->boolean('is_taxable')->default(true);
                $table->decimal('weight', 8, 2)->nullable();
                $table->string('image_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                // Add indexes
                $table->index('name');
                $table->index('sku');
                $table->index('barcode');
                $table->index('stock_status');
                $table->index('is_active');
                $table->index('salon_id');
                $table->index('branch_id');
            });
        }

        // Create inventory_transactions table
        if (!Schema::hasTable('inventory_transactions')) {
            Schema::create('inventory_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('item_id')->constrained('inventory_items');
                $table->enum('transaction_type', ['purchase', 'sale', 'adjustment', 'return']);
                $table->decimal('quantity', 12, 4);
                $table->decimal('unit_cost', 10, 2)->default(0);
                $table->decimal('total_cost', 12, 2)->default(0);
                $table->foreignId('reference_id')->nullable()->comment('Reference to purchase order, sale, etc.');
                $table->string('reference_type')->nullable()->comment('Model class name for reference');
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();

                // Add indexes
                $table->index('transaction_type');
                $table->index(['reference_id', 'reference_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order to avoid foreign key constraint errors
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('inventory_categories');
    }
};
