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
        Schema::table('memberships', function (Blueprint $table) {
            $table->enum('membership_type', ['all', 'services', 'products'])->default('all')->after('description');
            $table->integer('validity_value')->default(30)->after('is_taxable');
            $table->enum('validity_unit', ['days', 'weeks', 'months', 'years'])->default('days')->after('validity_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn(['membership_type', 'validity_value', 'validity_unit']);
        });
    }
};
