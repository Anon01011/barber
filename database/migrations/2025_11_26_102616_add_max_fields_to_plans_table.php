<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'max_users')) {
                $table->integer('max_users')->nullable()->after('features');
            }
            if (!Schema::hasColumn('plans', 'max_branches')) {
                $table->integer('max_branches')->nullable()->after('max_users');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['max_users', 'max_branches']);
        });
    }
};
