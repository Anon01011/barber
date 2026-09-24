<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddRejectedByStaffIdToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only add rejected_by_staff_id if it doesn't exist
        if (!Schema::hasColumn('bookings', 'rejected_by_staff_id')) {
            // First add the column at the end
            Schema::table('bookings', function (Blueprint $table) {
                $table->unsignedBigInteger('rejected_by_staff_id')->nullable();
            });

            // Then add the foreign key
            Schema::table('bookings', function (Blueprint $table) {
                $table->foreign('rejected_by_staff_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null');
            });

            // Position the column after staff_assigned_at if it exists, otherwise after status
            if (DB::getDriverName() !== 'sqlite') {
                if (Schema::hasColumn('bookings', 'staff_assigned_at')) {
                    DB::statement('ALTER TABLE `bookings` MODIFY COLUMN `rejected_by_staff_id` BIGINT UNSIGNED NULL AFTER `staff_assigned_at`');
                } else {
                    DB::statement('ALTER TABLE `bookings` MODIFY COLUMN `rejected_by_staff_id` BIGINT UNSIGNED NULL AFTER `status`');
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('bookings', 'rejected_by_staff_id')) {
                $table->dropForeign(['rejected_by_staff_id']);
                $table->dropColumn('rejected_by_staff_id');
            }
        });
    }
}
