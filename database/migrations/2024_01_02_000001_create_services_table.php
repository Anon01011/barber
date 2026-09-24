<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('salon_id')->nullable()->constrained('salons')->onDelete('cascade');
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('duration')->comment('Duration in minutes');
                $table->decimal('price', 10, 2);
                $table->foreignId('category_id')->nullable()->constrained('service_categories')->nullOnDelete();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                // Add indexes
                $table->index('status');
                $table->index('category_id');
                $table->index('salon_id');
                $table->index('branch_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
};