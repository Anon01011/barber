<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EnsureTimestampsInRoles extends Migration
{
    public function up()
    {
        // Drop existing timestamp columns if they exist with incorrect types
        if (Schema::hasColumn('roles', 'created_at')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('created_at');
            });
        }
        
        if (Schema::hasColumn('roles', 'updated_at')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('updated_at');
            });
        }

        // Add new timestamp columns with correct types
        Schema::table('roles', function (Blueprint $table) {
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });

        // For existing records, set timestamps to current time
        DB::table('roles')
            ->whereNull('created_at')
            ->orWhereNull('updated_at')
            ->update([
                'created_at' => now(),
                'updated_at' => now()
            ]);
    }

    public function down()
    {
        // We won't drop the columns in the down method to prevent data loss
    }
}
