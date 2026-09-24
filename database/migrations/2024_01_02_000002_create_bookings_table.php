<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('salon_id')->nullable()->constrained('salons')->onDelete('cascade');
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('customer_id')->constrained()->onDelete('cascade');
                $table->foreignId('service_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->foreignId('rejected_by_staff_id')->nullable()->constrained('users')->onDelete('set null');
                $table->dateTime('start_time');
                $table->dateTime('end_time');
                $table->decimal('amount', 10, 2)->default(0.00);
                $table->decimal('tip_amount', 10, 2)->default(0.00);
                $table->enum('payment_method', ['cash', 'card', 'upi', 'bank_transfer'])->nullable();
                $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
                $table->string('staff_assignment_status')->default('pending');
                $table->timestamp('staff_assigned_at')->nullable();
                $table->unsignedTinyInteger('rating')->nullable();
                $table->boolean('is_rated')->default(false);
                $table->string('rating_token', 64)->nullable()->unique();
                $table->timestamp('rating_token_expires_at')->nullable();
                $table->timestamps();

                // Add indexes for better performance
                $table->index(['staff_id', 'start_time', 'end_time']);
                $table->index('status');
                $table->index('branch_id');
                $table->index('salon_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};