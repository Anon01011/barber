<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salon_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('pos_sale_id')->nullable()->constrained('pos_sales')->onDelete('set null');
            $table->enum('item_type', ['service', 'product', 'membership', 'package']);
            $table->unsignedBigInteger('item_id');
            $table->decimal('sale_amount', 10, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->foreignId('commission_profile_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['salon_id', 'staff_id', 'status']);
            $table->index(['booking_id']);
            $table->index(['pos_sale_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_commissions');
    }
};
