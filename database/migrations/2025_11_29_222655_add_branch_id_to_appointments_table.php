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
        if (!Schema::hasTable('appointments')) {
            Schema::create('appointments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('salon_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
                $table->foreignId('service_id')->constrained()->cascadeOnDelete();
                $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('start_time');
                $table->dateTime('end_time');
                $table->string('status')->default('pending');
                $table->text('notes')->nullable();
                $table->boolean('reminder_sent')->default(false);
                $table->string('cancellation_reason')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                $table->index('branch_id');
                $table->index('salon_id');
            });
        } else {
            Schema::table('appointments', function (Blueprint $table) {
                if (!Schema::hasColumn('appointments', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                    $table->index('branch_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('appointments')) {
            if (Schema::hasColumn('appointments', 'branch_id')) {
                 Schema::table('appointments', function (Blueprint $table) {
                    $table->dropForeign(['branch_id']);
                    $table->dropIndex(['branch_id']);
                    $table->dropColumn('branch_id');
                });
            }
        }
    }
};
