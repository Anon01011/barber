<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, drop the old unique index if it exists
        $indexExists = false;
        $newIndexExists = false;

        if (DB::getDriverName() === 'mysql') {
            $indexExists = collect(DB::select("SHOW INDEX FROM roles WHERE Key_name = 'roles_name_guard_name_unique'"))->count() > 0;
            if ($indexExists) {
                DB::statement('ALTER TABLE roles DROP INDEX roles_name_guard_name_unique');
            }
            $newIndexExists = collect(DB::select("SHOW INDEX FROM roles WHERE Key_name = 'roles_name_guard_name_salon_id_unique'"))->count() > 0;
        }

        // Shorten the columns to fit in the index
        Schema::table('roles', function (Blueprint $table) {
            $table->string('name', 100)->change();
            $table->string('guard_name', 100)->change();
        });

        if (!$newIndexExists) {
            try {
                Schema::table('roles', function (Blueprint $table) {
                    $table->unique(['name', 'guard_name', 'salon_id'], 'roles_name_guard_name_salon_id_unique');
                });
            } catch (\Exception $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new unique index
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_name_guard_name_salon_id_unique');
        });
        
        // Restore column lengths
        Schema::table('roles', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('guard_name', 255)->change();
        });
        
        // Restore the old unique index
        Schema::table('roles', function (Blueprint $table) {
            $table->unique(['name', 'guard_name']);
        });
    }
};
