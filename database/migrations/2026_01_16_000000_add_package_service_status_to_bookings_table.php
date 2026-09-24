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
        if (Schema::hasColumn('bookings', 'package_service_status')) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table) {
            // Add package_service_status column
            // 0 = use now (immediate booking, consume balance)
            // 1 = save for future (add to balance only, no booking yet)
            $table->tinyInteger('package_service_status')
                ->default(0)
                ->after('package_id')
                ->comment('0=use now, 1=save for future');

            // Add index for better query performance on package-related queries
            $table->index(['package_id', 'package_service_status'], 'idx_package_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_package_status');
            $table->dropColumn('package_service_status');
        });
    }
};
