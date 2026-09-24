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
        if (!Schema::hasColumn('staff_daily_schedules', 'branch_id')) {
            Schema::table('staff_daily_schedules', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('salon_id')->constrained('branches')->onDelete('cascade');
                $table->index('branch_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('staff_daily_schedules', 'branch_id')) {
            Schema::table('staff_daily_schedules', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropIndex(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
