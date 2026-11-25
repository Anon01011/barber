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
        if (Schema::hasTable('customers') && !Schema::hasColumn('customers', 'loyalty_points')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->unsignedInteger('loyalty_points')->default(0)->after('total_spent');
            });
        }

        if (!Schema::hasTable('loyalty_transactions')) {
            Schema::create('loyalty_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('salon_id')->constrained('salons')->onDelete('cascade');
                $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
                $table->foreignId('pos_sale_id')->nullable()->constrained('pos_sales')->onDelete('set null');
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
                $table->integer('points'); // Positive for earned, negative for redeemed
                $table->string('type', 32); // earned, redeemed, refund_reversal, manual_adjustment
                $table->decimal('conversion_rate', 8, 4)->nullable(); // e.g. 0.05 currency units per point
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->string('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();

                $table->index(['salon_id', 'customer_id']);
                $table->index('pos_sale_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');

        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'loyalty_points')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('loyalty_points');
            });
        }
    }
};
