<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Skip if services table already exists (created by earlier migration)
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('duration')->comment('Duration in minutes');
                $table->decimal('price', 10, 2);
                $table->foreignId('category_id')->nullable()->constrained('service_categories')->nullOnDelete();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();

                // Add indexes
                $table->index('status');
                $table->index('category_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
}; 