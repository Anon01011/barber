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
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('validity_value')->default(30)->after('tax_rate');
            $table->enum('validity_unit', ['days', 'weeks', 'months', 'years'])->default('days')->after('validity_value');
            $table->text('validity_description')->nullable()->after('validity_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['validity_value', 'validity_unit', 'validity_description']);
        });
    }
};
