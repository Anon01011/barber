<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commission_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('commission_profiles', 'calculation_interval')) {
                $table->string('calculation_interval')->nullable()->after('type'); // daily, weekly, monthly
            }
            if (!Schema::hasColumn('commission_profiles', 'qualifying_item')) {
                $table->string('qualifying_item')->nullable()->after('calculation_interval'); // all, service, product, membership, package
            }
            if (!Schema::hasColumn('commission_profiles', 'is_cascade')) {
                $table->boolean('is_cascade')->default(false)->after('qualifying_item');
            }
        });

        Schema::table('commission_rules', function (Blueprint $table) {
            if (!Schema::hasColumn('commission_rules', 'target_from')) {
                $table->decimal('target_from', 10, 2)->default(0.00)->after('target_amount');
            }
            if (!Schema::hasColumn('commission_rules', 'target_to')) {
                $table->decimal('target_to', 10, 2)->nullable()->after('target_from');
            }
        });

        Schema::table('staff_commissions', function (Blueprint $table) {
            if (!Schema::hasColumn('staff_commissions', 'period_start')) {
                $table->date('period_start')->nullable()->after('status');
            }
            if (!Schema::hasColumn('staff_commissions', 'period_end')) {
                $table->date('period_end')->nullable()->after('period_start');
            }
        });

        // Modify enum column on staff_commissions to include 'target'
        try {
            DB::statement("ALTER TABLE staff_commissions MODIFY COLUMN item_type ENUM('service', 'product', 'membership', 'package', 'tip', 'target')");
        } catch (\Exception $e) {
            // Fallback / log if not using MySQL or alter fails
            \Log::warning("Failed to alter staff_commissions enum: " . $e->getMessage());
        }
    }

    public function down(): void
    {
        Schema::table('commission_profiles', function (Blueprint $table) {
            $table->dropColumn(['calculation_interval', 'qualifying_item', 'is_cascade']);
        });

        Schema::table('commission_rules', function (Blueprint $table) {
            $table->dropColumn(['target_from', 'target_to']);
        });

        Schema::table('staff_commissions', function (Blueprint $table) {
            $table->dropColumn(['period_start', 'period_end']);
        });

        try {
            DB::statement("ALTER TABLE staff_commissions MODIFY COLUMN item_type ENUM('service', 'product', 'membership', 'package', 'tip')");
        } catch (\Exception $e) {
            \Log::warning("Failed to revert staff_commissions enum: " . $e->getMessage());
        }
    }
};
