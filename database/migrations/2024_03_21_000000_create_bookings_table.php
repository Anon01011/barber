<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Skip if bookings table already exists (created by earlier migration)
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained()->onDelete('cascade');
                $table->foreignId('service_id')->constrained()->onDelete('cascade');
                $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
                $table->dateTime('start_time');
                $table->dateTime('end_time');
                $table->decimal('amount', 10, 2)->default(0.00);
                $table->decimal('tip_amount', 10, 2)->default(0.00);
                $table->text('notes')->nullable();
                $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
                $table->timestamps();

                // Add indexes for better performance
                $table->index(['staff_id', 'start_time', 'end_time']);
                $table->index('status');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}; 