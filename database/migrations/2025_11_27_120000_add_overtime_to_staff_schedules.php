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
        Schema::table('staff_schedules', function (Blueprint $table) {
            $table->time('overtime_start')->nullable()->after('end_time');
            $table->time('overtime_end')->nullable()->after('overtime_start');
            $table->boolean('allows_overtime')->default(false)->after('is_working');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_schedules', function (Blueprint $table) {
            $table->dropColumn(['overtime_start', 'overtime_end', 'allows_overtime']);
        });
    }
};
