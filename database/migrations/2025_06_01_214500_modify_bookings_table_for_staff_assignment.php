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
        Schema::table('bookings', function (Blueprint $table) {
            // Make staff_id nullable if it exists and is not already nullable
            if (Schema::hasColumn('bookings', 'staff_id')) {
                // Note: change() requires doctrine/dbal package, skip if not available
                try {
                    $table->unsignedBigInteger('staff_id')->nullable()->change();
                } catch (\Exception $e) {
                    // Skip if change() fails
                }
            }
            
            // Add staff assignment status if it doesn't exist
            if (!Schema::hasColumn('bookings', 'staff_assignment_status')) {
                $table->string('staff_assignment_status')->default('pending')->after('status');
            }
            
            // Add staff assignment timestamp if it doesn't exist
            if (!Schema::hasColumn('bookings', 'staff_assigned_at')) {
                $table->timestamp('staff_assigned_at')->nullable()->after('staff_assignment_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Remove staff assignment fields
            $table->dropColumn(['staff_assignment_status', 'staff_assigned_at']);
            
            // Make staff_id required again
            $table->unsignedBigInteger('staff_id')->nullable(false)->change();
        });
    }
};
