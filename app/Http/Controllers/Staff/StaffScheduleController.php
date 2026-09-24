<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffSchedule;
use App\Models\StaffAbsence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffScheduleController extends Controller
{
    /**
     * Get schedule for a specific staff member.
     */
    public function getSchedule(Request $request, User $staff)
    {
        $schedule = StaffSchedule::where('staff_id', $staff->id)
            ->orderBy('day_of_week')
            ->get();

        // If no schedule exists, return default empty structure
        if ($schedule->isEmpty()) {
            $settings = app(\App\Services\SettingsService::class);
            $salonId = auth()->user()->salon_id;

            $startTime = $settings->get('working_hours_start', '09:00', $salonId);
            $endTime = $settings->get('working_hours_end', '17:00', $salonId);

            $defaultSchedule = [];
            for ($i = 0; $i < 7; $i++) {
                $defaultSchedule[] = [
                    'day_of_week' => $i,
                    'is_working' => false,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ];
            }
            return response()->json($defaultSchedule);
        }

        return response()->json($schedule);
    }

    /**
     * Update schedule for a staff member.
     */
    public function updateSchedule(Request $request, User $staff)
    {
        $validated = $request->validate([
            'schedule' => 'required|array|min:7|max:7',
            'schedule.*.day_of_week' => 'required|integer|between:0,6',
            'schedule.*.is_working' => 'required|boolean',
            'schedule.*.start_time' => 'nullable|date_format:H:i',
            'schedule.*.end_time' => 'nullable|date_format:H:i',
            'schedule.*.allows_overtime' => 'nullable|boolean',
            'schedule.*.overtime_start' => 'nullable|date_format:H:i',
            'schedule.*.overtime_end' => 'nullable|date_format:H:i',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['schedule'] as $day) {
                StaffSchedule::withoutGlobalScope('branch')->updateOrCreate(
                    [
                        'salon_id' => auth()->user()->salon_id,
                        'staff_id' => $staff->id,
                        'day_of_week' => $day['day_of_week'],
                    ],
                    [
                        'is_working' => $day['is_working'],
                        'start_time' => $day['is_working'] ? ($day['start_time'] ?? null) : null,
                        'end_time' => $day['is_working'] ? ($day['end_time'] ?? null) : null,
                        'allows_overtime' => $day['allows_overtime'] ?? false,
                        'overtime_start' => ($day['allows_overtime'] ?? false) ? ($day['overtime_start'] ?? null) : null,
                        'overtime_end' => ($day['allows_overtime'] ?? false) ? ($day['overtime_end'] ?? null) : null,
                        'branch_id' => app()->has('current_branch') ? app('current_branch')->id : null,
                    ]
                );
            }

            DB::commit();
            return response()->json(['message' => 'Schedule updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Staff schedule update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'staff_id' => $staff->id,
                'user_id' => auth()->id(),
                'salon_id' => auth()->user()->salon_id ?? 'NULL'
            ]);
            return response()->json(['message' => 'Failed to update schedule. Please try again.'], 500);
        }
    }

    /**
     * Update schedule for a specific date.
     */
    public function updateDateSchedule(Request $request, User $staff)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'is_working' => 'required|boolean',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'allows_overtime' => 'nullable|boolean',
            'overtime_start' => 'nullable|date_format:H:i',
            'overtime_end' => 'nullable|date_format:H:i',
        ]);

        try {
            \App\Models\StaffDailySchedule::updateOrCreate(
                [
                    'salon_id' => auth()->user()->salon_id,
                    'staff_id' => $staff->id,
                    'date' => $validated['date'],
                ],
                [
                    'is_working' => $validated['is_working'],
                    'start_time' => $validated['is_working'] ? ($validated['start_time'] ?? null) : null,
                    'end_time' => $validated['is_working'] ? ($validated['end_time'] ?? null) : null,
                    'allows_overtime' => $validated['allows_overtime'] ?? false,
                    'overtime_start' => ($validated['allows_overtime'] ?? false) ? ($validated['overtime_start'] ?? null) : null,
                    'overtime_end' => ($validated['allows_overtime'] ?? false) ? ($validated['overtime_end'] ?? null) : null,
                ]
            );

            return response()->json(['message' => 'Schedule for ' . $validated['date'] . ' updated successfully']);

        } catch (\Exception $e) {
            \Log::error('Staff daily schedule update failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to update schedule. Please try again.'], 500);
        }
    }

    /**
     * Update schedule for a specific week (as daily overrides).
     */
    public function updateWeekSchedule(Request $request, User $staff)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'schedule' => 'required|array|min:7|max:7',
            'schedule.*.day_of_week' => 'required|integer|between:0,6',
            'schedule.*.is_working' => 'required|boolean',
            'schedule.*.start_time' => 'nullable|date_format:H:i',
            'schedule.*.end_time' => 'nullable|date_format:H:i',
            'schedule.*.allows_overtime' => 'nullable|boolean',
            'schedule.*.overtime_start' => 'nullable|date_format:H:i',
            'schedule.*.overtime_end' => 'nullable|date_format:H:i',
        ]);

        try {
            DB::beginTransaction();

            $startDate = \Carbon\Carbon::parse($validated['start_date']);

            foreach ($validated['schedule'] as $day) {
                // Calculate the specific date for this day of the week
                // Assuming the schedule array is ordered 0 (Sun) to 6 (Sat)
                // and start_date is the Sunday of that week.
                // If start_date is not Sunday, we might need to adjust, but let's assume the frontend passes the correct start of week.

                $targetDate = $startDate->copy()->addDays($day['day_of_week']);

                \App\Models\StaffDailySchedule::updateOrCreate(
                    [
                        'salon_id' => auth()->user()->salon_id,
                        'staff_id' => $staff->id,
                        'date' => $targetDate->format('Y-m-d'),
                    ],
                    [
                        'is_working' => $day['is_working'],
                        'start_time' => $day['is_working'] ? ($day['start_time'] ?? null) : null,
                        'end_time' => $day['is_working'] ? ($day['end_time'] ?? null) : null,
                        'allows_overtime' => $day['allows_overtime'] ?? false,
                        'overtime_start' => ($day['allows_overtime'] ?? false) ? ($day['overtime_start'] ?? null) : null,
                        'overtime_end' => ($day['allows_overtime'] ?? false) ? ($day['overtime_end'] ?? null) : null,
                    ]
                );
            }

            DB::commit();
            return response()->json(['message' => 'Schedule for the week of ' . $validated['start_date'] . ' updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Staff week schedule update failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to update week schedule. Please try again.'], 500);
        }
    }

    /**
     * Get absences for a staff member.
     */
    public function getAbsences(Request $request, User $staff)
    {
        $absences = StaffAbsence::where('staff_id', $staff->id)
            ->where('end_at', '>=', now()->subMonths(1)) // Get recent and future absences
            ->orderBy('start_at')
            ->get();

        return response()->json($absences);
    }

    /**
     * Add an absence.
     */
    public function storeAbsence(Request $request, User $staff)
    {
        $validated = $request->validate([
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'reason' => 'nullable|string|max:255',
        ]);

        $absence = StaffAbsence::create([
            'salon_id' => auth()->user()->salon_id,
            'staff_id' => $staff->id,
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'reason' => $validated['reason'],
        ]);

        return response()->json(['message' => 'Absence added successfully', 'absence' => $absence]);
    }

    /**
     * Delete an absence.
     */
    public function destroyAbsence(User $staff, StaffAbsence $absence)
    {
        $absence->delete();
        return response()->json(['message' => 'Absence removed successfully']);
    }
}
