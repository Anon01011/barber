<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixRolesTimestampsFinal extends Migration
{
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('roles', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        // Update any existing records to have timestamps
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
        // No need to drop the columns in the down method
    }
}
