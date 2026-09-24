<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddMissingTimestampsToRoles extends Migration
{
    public function up()
    {
        // Add created_at if it doesn't exist
        if (!Schema::hasColumn('roles', 'created_at')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // Add updated_at if it doesn't exist
        if (!Schema::hasColumn('roles', 'updated_at')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            });
        }

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
