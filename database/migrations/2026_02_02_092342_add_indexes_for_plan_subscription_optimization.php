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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Index for finding active subscriptions by salon
            $table->index(['salon_id', 'status', 'ends_at'], 'idx_salon_status_ends');

            // Index for subscription status queries
            $table->index(['status', 'ends_at'], 'idx_status_ends');

            // Index for plan lookups
            $table->index('plan_id', 'idx_plan_id');
        });

        Schema::table('plans', function (Blueprint $table) {
            // Index for active plans
            $table->index(['is_active', 'sort_order'], 'idx_active_sort');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index for salon user queries (for limit checking)
            $table->index('salon_id', 'idx_users_salon_id');
        });

        Schema::table('branches', function (Blueprint $table) {
            // Index for salon branch queries (for limit checking)
            $table->index('salon_id', 'idx_branches_salon_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('idx_salon_status_ends');
            $table->dropIndex('idx_status_ends');
            $table->dropIndex('idx_plan_id');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropIndex('idx_active_sort');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_salon_id');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropIndex('idx_branches_salon_id');
        });
    }
};
