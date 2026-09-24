<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('commission_rules', function (Blueprint $table) {
            $table->foreignId('salon_id')->after('id')->constrained()->onDelete('cascade');
            $table->index('salon_id');
        });

        // Populate salon_id from commission_profiles
        if (DB::getDriverName() === 'mysql') {
            DB::statement('
                UPDATE commission_rules cr
                INNER JOIN commission_profiles cp ON cr.commission_profile_id = cp.id
                SET cr.salon_id = cp.salon_id
            ');
        } else if (DB::getDriverName() === 'sqlite') {
            DB::statement('
                UPDATE commission_rules 
                SET salon_id = (SELECT salon_id FROM commission_profiles WHERE commission_profiles.id = commission_rules.commission_profile_id)
                WHERE EXISTS (SELECT 1 FROM commission_profiles WHERE commission_profiles.id = commission_rules.commission_profile_id)
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commission_rules', function (Blueprint $table) {
            $table->dropForeign(['salon_id']);
            $table->dropIndex(['salon_id']);
            $table->dropColumn('salon_id');
        });
    }
};
