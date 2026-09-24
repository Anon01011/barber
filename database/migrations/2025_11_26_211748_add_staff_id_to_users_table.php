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
        Schema::table('users', function (Blueprint $table) {
            // Add staff_id column after id
            $table->unsignedInteger('staff_id')->nullable()->after('id');
            
            // Add composite unique index to ensure staff_id is unique per salon/branch
            // This allows staff_id to start from 1 in each branch/salon
            $table->unique(['salon_id', 'branch_id', 'staff_id'], 'unique_staff_per_branch_salon');
        });

        // Backfill staff_id for existing staff members with employee role
        $this->backfillStaffIds();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the unique constraint first
            $table->dropUnique('unique_staff_per_branch_salon');
            
            // Drop the staff_id column
            $table->dropColumn('staff_id');
        });
    }

    /**
     * Backfill staff_id for existing users with employee role
     */
    private function backfillStaffIds(): void
    {
        $users = \DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'employee')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->select('users.id', 'users.salon_id', 'users.branch_id')
            ->orderBy('users.salon_id')
            ->orderBy('users.branch_id')
            ->orderBy('users.id')
            ->get();

        $staffCounters = [];

        foreach ($users as $user) {
            // Create a unique key for each salon/branch combination
            $key = $user->salon_id . '_' . ($user->branch_id ?? 'null');
            
            // Initialize counter for this salon/branch if not exists
            if (!isset($staffCounters[$key])) {
                $staffCounters[$key] = 1;
            }
            
            // Update the user with the staff_id
            \DB::table('users')
                ->where('id', $user->id)
                ->update(['staff_id' => $staffCounters[$key]]);
            
            // Increment the counter
            $staffCounters[$key]++;
        }
    }
};
