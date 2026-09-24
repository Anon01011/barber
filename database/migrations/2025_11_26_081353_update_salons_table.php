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
        Schema::table('salons', function (Blueprint $table) {
            if (!Schema::hasColumn('salons', 'slug')) {
                $table->string('slug')->unique()->after('name');
            }
            if (!Schema::hasColumn('salons', 'owner_id')) {
                $table->foreignId('owner_id')->nullable()->after('slug')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('salons', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('website');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            //
        });
    }
};
