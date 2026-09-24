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
        if (!Schema::hasColumn('services', 'is_active')) {
            Schema::table('services', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('price');
            });
        }

        if (!Schema::hasColumn('memberships', 'is_recurring')) {
            Schema::table('memberships', function (Blueprint $table) {
                $table->boolean('is_recurring')->default(false)->after('validity_unit');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn('is_recurring');
        });
    }
};
