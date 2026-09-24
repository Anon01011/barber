<?php

namespace App\Listeners;

use Illuminate\Auth\Events\RoleAssigned; // Assuming a RoleAssigned event exists or use appropriate event
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;

class SyncEmployeeRecord
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function handle($event)
    {
        $user = $event->user ?? null;
        $role = $event->role ?? null;

        if (!$user instanceof User || !$role) {
            return;
        }

        if ($role->name === 'employee') {
            // Check if employee record exists
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                // Create employee record from user data
                Employee::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => 'active',
                ]);
                Log::info("Employee record created for user_id {$user->id}");
            } else {
                // Update employee record if needed
                $employee->update([
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => 'active',
                ]);
                Log::info("Employee record updated for user_id {$user->id}");
            }
        }
    }
}
