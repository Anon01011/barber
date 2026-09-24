<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Branch;
use Illuminate\Support\Facades\Mail;
use App\Mail\StaffWelcomeMail;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    /**
     * Display a listing of the staff.
     */
    public function index(Request $request)
    {
        $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
        $query = User::role('employee');

        if ($salon) {
            $query->where('salon_id', $salon->id);
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $staff = $query->orderBy('name')->paginate(10)->withQueryString();
        $branches = Branch::where('salon_id', $salon->id)->get();

        // Fetch roles that are either global or belong to this salon, excluding super_admin
        $roles = Role::where(function ($q) use ($salon) {
            $q->where('salon_id', $salon->id)
                ->orWhereNull('salon_id');
        })->where('name', '!=', 'super_admin')->get();

        $services = \App\Models\Service::where('salon_id', $salon->id)->get();

        return view('staff.index', compact('staff', 'branches', 'roles', 'services'));
    }

    /**
     * Display schedules for all staff members.
     */
    public function schedules(Request $request)
    {
        $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
        $staff = User::role('employee')
            ->where('salon_id', $salon->id)
            ->orderBy('name')
            ->get();

        // Determine date range based on view type
        $viewType = $request->get('view_type', 'week');
        $period = [];

        if ($viewType === 'day') {
            $date = \Carbon\Carbon::parse($request->get('date', now()));
            $period[] = $date;
        } elseif ($viewType === 'month') {
            $month = $request->get('month', now()->format('Y-m'));
            $start = \Carbon\Carbon::parse($month)->startOfMonth();
            $end = \Carbon\Carbon::parse($month)->endOfMonth();

            $curr = $start->copy();
            while ($curr->lte($end)) {
                $period[] = $curr->copy();
                $curr->addDay();
            }
        } elseif ($viewType === 'custom') {
            $start = \Carbon\Carbon::parse($request->get('start_date', now()));
            $end = \Carbon\Carbon::parse($request->get('end_date', now()->addDays(6)));

            // Limit to reasonable max (e.g. 31 days) to prevent performance issues
            if ($start->diffInDays($end) > 31) {
                $end = $start->copy()->addDays(31);
            }

            $curr = $start->copy();
            while ($curr->lte($end)) {
                $period[] = $curr->copy();
                $curr->addDay();
            }
        } else {
            // Week view (default)
            // Use 'date' if provided (e.g. to jump to a specific week), otherwise current week
            $refDate = $request->has('date') ? \Carbon\Carbon::parse($request->date) : now();
            $start = $refDate->startOfWeek(\Carbon\Carbon::SUNDAY);

            for ($i = 0; $i < 7; $i++) {
                $period[] = $start->copy()->addDays($i);
            }
        }

        // Load recurring weekly schedules for all staff
        $weeklySchedules = [];
        foreach ($staff as $member) {
            $weeklySchedules[$member->id] = \App\Models\StaffSchedule::where('staff_id', $member->id)
                ->get()
                ->keyBy('day_of_week');
        }

        // Load daily schedule overrides
        $startDate = $period[0]->copy()->startOfDay();
        $endDate = end($period)->copy()->endOfDay();

        $dailySchedules = \App\Models\StaffDailySchedule::where('salon_id', $salon->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('staff_id');

        // Build date-specific schedules based on day of week AND daily overrides
        $schedules = [];
        foreach ($staff as $member) {
            $schedules[$member->id] = [];
            foreach ($period as $date) {
                $dateKey = $date->format('Y-m-d');
                $dayOfWeek = $date->dayOfWeek;

                // Check for daily override first
                $memberDailySchedules = $dailySchedules[$member->id] ?? collect();
                $override = $memberDailySchedules->first(function ($item) use ($date) {
                    return $item->date->isSameDay($date);
                });

                if ($override) {
                    $schedules[$member->id][$dateKey] = $override;
                } else {
                    // Fallback to weekly template
                    $schedules[$member->id][$dateKey] = $weeklySchedules[$member->id][$dayOfWeek] ?? null;
                }
            }
        }

        // Load absences for the period
        $startDate = $period[0]->copy()->startOfDay();
        $endDate = end($period)->copy()->endOfDay();

        $absences = \App\Models\StaffAbsence::where('salon_id', $salon->id)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_at', [$startDate, $endDate])
                    ->orWhereBetween('end_at', [$startDate, $endDate])
                    ->orWhere(function ($sub) use ($startDate, $endDate) {
                        $sub->where('start_at', '<', $startDate)
                            ->where('end_at', '>', $endDate);
                    });
            })
            ->get()
            ->groupBy('staff_id');

        return view('staff.schedules', compact('staff', 'schedules', 'period', 'viewType', 'absences'));
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create()
    {
        return view('staff.create');
    }

    /**
     * Store a newly created staff member in storage.
     */
    public function store(Request $request)
    {
        $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }

        if (!$salon->canAddStaff()) {
            \Log::warning('Staff limit reached', [
                'salon_id' => $salon->id,
                'limit' => $salon->getStaffLimit(),
                'current' => $salon->users()->role('employee')->count()
            ]);
            return back()->with('error', 'You have reached the maximum number of staff members allowed for your plan.');
        }

        try {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
                'address' => ['nullable', 'string', 'max:500'],
                'role' => [
                    'required',
                    'string',
                    'not_in:super_admin',
                    Rule::exists('roles', 'name')->where(function ($query) use ($salon) {
                        $query->where('salon_id', $salon->id)
                            ->orWhereNull('salon_id');
                    })
                ],
                'status' => ['required', 'in:active,inactive'],
                'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('salon_id', $salon->id)],
                'commission_profile_id' => ['nullable', Rule::exists('commission_profiles', 'id')->where('salon_id', $salon->id)],
                'schedule' => ['nullable', 'array'],
                'schedule.*.day_of_week' => ['required_with:schedule', 'integer', 'between:0,6'],
                'schedule.*.is_working' => ['nullable', 'boolean'],
                'schedule.*.start_time' => ['nullable', 'date_format:H:i'],
                'schedule.*.end_time' => ['nullable', 'date_format:H:i'],
                'allow_login' => ['nullable', 'boolean'],
                'services' => ['nullable', 'array'],
                'services.*' => ['exists:services,id'],
            ];

            if ($request->boolean('allow_login')) {
                $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users'];
                $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
            } else {
                $rules['email'] = ['nullable', 'string', 'email', 'max:255', 'unique:users'];
                // No password validation if login is not allowed
            }

            $validated = $request->validate($rules);

            DB::beginTransaction();

            $email = $request->boolean('allow_login') ? $request->email : 'staff_' . uniqid() . '@' . $salon->slug . '.local';
            $password = $request->boolean('allow_login') ? Hash::make($request->password) : Hash::make(\Illuminate\Support\Str::random(16));

            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => $password,
                'phone' => $request->phone,
                'address' => $request->address,
                'salon_id' => $salon->id,
                'status' => $request->status,
                'branch_id' => $request->branch_id,
                'commission_profile_id' => $request->commission_profile_id,
            ]);

            if ($request->has('services')) {
                $user->services()->sync($request->services);
            }

            // Assign the selected role
            $user->assignRole($request->role);

            // ALWAYS assign 'employee' role as well to ensure they appear in the staff list
            if ($request->role !== 'employee') {
                $user->assignRole('employee');
            }

            // Save working schedule if provided
            if ($request->has('schedule') && is_array($request->schedule)) {
                $settings = app(\App\Services\SettingsService::class);
                $defaultStartTime = $settings->get('working_hours_start', '09:00', $salon->id);
                $defaultEndTime = $settings->get('working_hours_end', '17:00', $salon->id);

                foreach ($request->schedule as $day) {
                    if (isset($day['is_working']) && $day['is_working']) {
                        \App\Models\StaffSchedule::create([
                            'salon_id' => $salon->id,
                            'staff_id' => $user->id,
                            'day_of_week' => $day['day_of_week'],
                            'is_working' => true,
                            'start_time' => $day['start_time'] ?? $defaultStartTime,
                            'end_time' => $day['end_time'] ?? $defaultEndTime,
                        ]);
                    }
                }
            }

            // Send Welcome Email
            try {
                Mail::to($user->email)->send(new StaffWelcomeMail($user, $request->password));
                Log::info('Staff welcome email sent', ['user_id' => $user->id]);
            } catch (\Exception $e) {
                Log::error('Failed to send staff welcome email', ['error' => $e->getMessage()]);
            }

            DB::commit();

            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff member created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Staff creation validation failed', [
                'errors' => $e->errors(),
                'data' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating staff member', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Failed to create staff member: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified staff member.
     */
    public function show(User $staff)
    {
        return view('staff.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified staff member.
     */
    public function edit(User $staff)
    {
        // Return JSON for modal if AJAX request
        if (request()->ajax()) {
            // Load roles relationship to avoid N+1 query
            $staff->load('roles', 'services');

            return response()->json([
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => str_contains($staff->email, '.local') ? '' : $staff->email,
                'phone' => $staff->phone,
                'address' => $staff->address,
                'status' => $staff->status,
                'role' => $staff->roles->first()?->name ?? '',
                'branch_id' => $staff->branch_id,
                'commission_profile_id' => $staff->commission_profile_id,
                'allow_login' => !str_contains($staff->email, '.local'),
                'services' => $staff->services->pluck('id'),
            ]);
        }
        return view('staff.edit', compact('staff'));
    }

    /**
     * Update the specified staff member in storage.
     */
    public function update(Request $request, User $staff)
    {
        $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }

        try {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone,' . $staff->id],
                'address' => ['nullable', 'string', 'max:500'],
                'status' => ['required', 'in:active,inactive'],
                'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('salon_id', $salon->id)],
                'commission_profile_id' => ['nullable', Rule::exists('commission_profiles', 'id')->where('salon_id', $salon->id)],
                'services' => ['nullable', 'array'],
                'services.*' => ['exists:services,id'],
                'role' => [
                    'required',
                    'string',
                    'not_in:super_admin',
                    Rule::exists('roles', 'name')->where(function ($query) use ($salon) {
                        $query->where('salon_id', $salon->id)
                            ->orWhereNull('salon_id');
                    })
                ],
            ];

            if ($request->boolean('allow_login')) {
                $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $staff->id];
                if ($request->filled('password')) {
                    $rules['password'] = ['confirmed', Rules\Password::defaults()];
                }
            } else {
                $rules['email'] = ['nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $staff->id];
            }

            $request->validate($rules);

            DB::beginTransaction();

            $updateData = [
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => $request->status,
                'branch_id' => $request->branch_id,
                'commission_profile_id' => $request->commission_profile_id,
            ];

            if ($request->boolean('allow_login')) {
                $updateData['email'] = $request->email;
                if ($request->filled('password')) {
                    $updateData['password'] = Hash::make($request->password);
                }
            } else {
                // If login is disabled, ensure email is a dummy one if it wasn't already
                if (!str_contains($staff->email, '.local')) {
                    $updateData['email'] = 'staff_' . uniqid() . '@' . $salon->slug . '.local';
                }
            }

            $staff->update($updateData);

            if ($request->has('services')) {
                $staff->services()->sync($request->services);
            }

            // Sync roles - always include 'employee' to ensure visibility
            $staff->syncRoles([$request->role, 'employee']);

            DB::commit();

            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff member updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Staff update validation failed', [
                'staff_id' => $staff->id,
                'errors' => $e->errors(),
                'data' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating staff member', [
                'staff_id' => $staff->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Failed to update staff member: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified staff member from storage.
     */
    public function destroy(User $staff)
    {
        try {
            // Check if staff has bookings (optional but good practice)
            // if ($staff->bookings()->exists()) { ... }

            $staff->delete();

            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff member deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting staff member', [
                'staff_id' => $staff->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to delete staff member.');
        }
    }
}
