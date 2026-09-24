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
        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('key')->constrained()->nullOnDelete();
            
            // Drop the existing unique index on 'key'
            $table->dropUnique(['key']);
            
            // Add a new composite unique index
            $table->unique(['key', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropUnique(['key', 'branch_id']);
            $table->dropColumn('branch_id');
            
            // Restore the original unique index (this might fail if there are duplicates now, but it's standard rollback)
            $table->unique('key');
        });
    }
};
