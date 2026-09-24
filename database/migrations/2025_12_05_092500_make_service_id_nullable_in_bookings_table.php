<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'service_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                // Make service_id nullable to support package bookings
                $table->foreignId('service_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Revert service_id back to non-nullable
            $table->foreignId('service_id')->nullable(false)->change();
        });
    }
};
