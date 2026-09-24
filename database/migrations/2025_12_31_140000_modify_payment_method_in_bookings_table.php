<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Modify payment_method to include 'online'
            // We'll convert it to VARCHAR(50) to be more flexible and avoid future ENUM issues
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_method VARCHAR(50) NULL");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Revert to ENUM if needed, but keeping as VARCHAR is usually fine
            // If strictly needed:
            // DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_method ENUM('cash', 'card', 'upi', 'bank_transfer') NULL");
        });
    }
};
