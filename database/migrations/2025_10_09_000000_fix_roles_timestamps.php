<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixRolesTimestamps extends Migration
{
    public function up()
    {
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                if (!Schema::hasColumn('roles', 'created_at')) {
                    $table->timestamp('created_at')->useCurrent();
                }
                if (!Schema::hasColumn('roles', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
                }
            });
        }
    }

    public function down()
    {
        // No need to drop columns in the down method to prevent data loss
    }
}
