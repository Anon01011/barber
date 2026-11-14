<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Upgrade the user_device_pins index on (user_id, salon_id) to a unique constraint.
     *
     * Rationale: each user should have exactly one PIN record per salon context
     * (NULL salon_id for super-admins). The unique constraint enforces this at the
     * database level and pairs with the updateOrCreate() call in TrustedDeviceService.
     *
     * NULL handling: MySQL treats each NULL as distinct for unique indexes, so two rows
     * with the same user_id and NULL salon_id would NOT violate the constraint on MySQL < 8.
     * We handle this application-side by using updateOrCreate with user_id + salon_id.
     *
     * Before adding the unique index, we remove any duplicate rows (keeping the most recent).
     */
    public function up(): void
    {
        // 1. Remove duplicates — keep the row with the highest id per (user_id, salon_id) pair.
        //    This is safe because updateOrCreate already aims to maintain one row, but older
        //    data may have duplicates from before this migration.
        $duplicates = DB::table('user_device_pins as a')
            ->join('user_device_pins as b', function ($join) {
                $join->on('a.user_id', '=', 'b.user_id')
                    ->where(function ($q) {
                        // Handle both NULL and non-NULL salon_id
                        $q->whereColumn('a.salon_id', 'b.salon_id')
                            ->orWhere(function ($q2) {
                                $q2->whereNull('a.salon_id')->whereNull('b.salon_id');
                            });
                    })
                    ->where('a.id', '<', 'b.id');
            })
            ->pluck('a.id');

        if ($duplicates->isNotEmpty()) {
            DB::table('user_device_pins')->whereIn('id', $duplicates)->delete();
        }

        // 2. Drop old plain index (try both naming conventions) and add unique constraint.
        Schema::table('user_device_pins', function (Blueprint $table) {
            // Laravel generates index names based on table + columns. Try both conventions.
            try {
                $table->dropIndex(['user_id', 'salon_id']);
            } catch (\Exception $e) {
                // Index may already be dropped or named differently — continue safely
                \Illuminate\Support\Facades\Log::info('dropIndex user_device_pins skipped: ' . $e->getMessage());
            }

            // Add unique constraint — enforces one PIN per user per salon context
            $table->unique(['user_id', 'salon_id'], 'user_device_pins_user_salon_unique');
        });
    }

    public function down(): void
    {
        Schema::table('user_device_pins', function (Blueprint $table) {
            $table->dropUnique('user_device_pins_user_salon_unique');
            $table->index(['user_id', 'salon_id']);
        });
    }
};
