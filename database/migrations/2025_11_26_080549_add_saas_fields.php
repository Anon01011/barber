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
        // Settings table updates
        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('salon_id')->nullable()->after('key')->constrained()->cascadeOnDelete();
            
            // Drop previous unique index
            $table->dropUnique(['key', 'branch_id']);
            
            // Add new composite unique index
            $table->unique(['key', 'salon_id', 'branch_id']);
        });

        // Users table updates
        if (!Schema::hasColumn('users', 'branch_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('salon_id')->constrained()->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropForeign(['salon_id']);
            $table->dropUnique(['key', 'salon_id', 'branch_id']);
            $table->dropColumn('salon_id');
            
            // Restore previous unique index
            $table->unique(['key', 'branch_id']);
        });
    }
};
