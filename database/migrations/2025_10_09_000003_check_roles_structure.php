<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckRolesStructure extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('roles', 'created_at')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        // No need to drop anything
    }
}
