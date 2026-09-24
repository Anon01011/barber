<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     * This runs BEFORE the user is saved to the database.
     */
    public function creating(User $user): void
    {
        // Only generate staff_id for users who will be assigned the employee role
        // We check if this is a staff creation by looking at the request
        if ($this->shouldGenerateStaffId($user)) {
            try {
                $user->staff_id = $this->generateStaffId($user->salon_id, $user->branch_id);
            } catch (\Exception $e) {
                Log::error('Failed to generate staff_id', [
                    'salon_id' => $user->salon_id,
                    'branch_id' => $user->branch_id,
                    'error' => $e->getMessage()
                ]);
                // Don't block user creation if staff_id generation fails
                // It can be assigned manually later
            }
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }

    /**
     * Determine if we should generate a staff_id for this user
     */
    private function shouldGenerateStaffId(User $user): bool
    {
        // Check if the request has a 'role' parameter set to 'employee'
        // This is set when creating staff through StaffController
        $role = request()->input('role');
        
        // Also check if salon_id is set (staff must belong to a salon)
        return $role === 'employee' && !empty($user->salon_id);
    }

    /**
     * Generate the next staff_id for the given salon/branch combination
     * Uses database locking to prevent race conditions
     */
    private function generateStaffId(?int $salonId, ?int $branchId): int
    {
        return DB::transaction(function () use ($salonId, $branchId) {
            // Lock the table to prevent race conditions when multiple staff are created simultaneously
            // Get the maximum staff_id for this salon/branch combination
            $maxStaffId = User::where('salon_id', $salonId)
                ->where('branch_id', $branchId)
                ->lockForUpdate() // This prevents race conditions
                ->max('staff_id');
            
            // Return the next staff_id (starting from 1 if none exist)
            return ($maxStaffId ?? 0) + 1;
        });
    }
}
