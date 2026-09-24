<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('roles') && !Schema::hasColumn('roles', 'status')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->boolean('status')->default(true)->after('guard_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('roles', 'status')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
}
