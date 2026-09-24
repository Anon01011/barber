<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Salon;
use App\Models\User;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::where('salon_id', auth()->user()->salon_id)->with('salon', 'user', 'services')->paginate(10);
        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'super_admin')->get();
        $services = \App\Models\Service::where('salon_id', auth()->user()->salon_id)->get();
        return view('admin.employees.index', compact('employees', 'roles', 'services'));
    }

    public function create()
    {
        $salons = Salon::all();
        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'super_admin')->get();
        $services = \App\Models\Service::where('salon_id', auth()->user()->salon_id)->get();
        return view('admin.employees.create', compact('salons', 'roles', 'services'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',

            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('employees', 'phone')->where(function ($query) {
                    return $query->where('salon_id', auth()->user()->salon_id);
                })
            ],
            'position' => 'nullable|string|max:255',
            'status' => 'required|string',
            'salon_id' => 'nullable|exists:salons,id',
            'avatar' => 'nullable|image|max:2048',
            'allow_login' => 'nullable|boolean',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ];

        if ($request->boolean('allow_login')) {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['role_id'] = 'required|exists:roles,id';
        } else {
            $rules['email'] = 'nullable|email';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Assign salon_id from auth user
        $validated['salon_id'] = auth()->user()->salon_id;

        // Enforce staff limit based on salon plan
        $salon = auth()->user()->salon;
        if ($salon && !$salon->canAddStaff()) {
            return redirect()->back()->withInput()->with('error', 'Staff limit reached for your current plan. Please upgrade to add more staff.');
        }

        $userId = null;
        if ($request->boolean('allow_login')) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                'salon_id' => $validated['salon_id'],
                'status' => $validated['status'] === 'active' ? 1 : 0,
            ]);
            $user->assignRole($request->role_id);
            $userId = $user->id;
        }

        $employeeData = collect($validated)->except(['password', 'password_confirmation', 'role_id', 'allow_login', 'services'])->toArray();
        $employeeData['user_id'] = $userId;

        $employee = Employee::create($employeeData);

        if (isset($validated['services'])) {
            $employee->services()->sync($validated['services']);
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        $salons = Salon::all();
        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'super_admin')->get();
        $services = \App\Models\Service::where('salon_id', auth()->user()->salon_id)->get();
        $employee->load('services', 'user');
        return view('admin.employees.edit', compact('employee', 'salons', 'roles', 'services'));
    }

    public function update(Request $request, Employee $employee)
    {
        $rules = [
            'name' => 'required|string|max:255',

            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('employees', 'phone')->ignore($employee->id)->where(function ($query) {
                    return $query->where('salon_id', auth()->user()->salon_id);
                })
            ],
            'position' => 'nullable|string|max:255',
            'status' => 'required|string',
            'salon_id' => 'nullable|exists:salons,id',
            'avatar' => 'nullable|image|max:2048',
            'allow_login' => 'nullable|boolean',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ];

        if ($request->boolean('allow_login')) {
            $rules['email'] = 'required|email|unique:users,email,' . ($employee->user_id ?? 'NULL');
            $rules['role_id'] = 'required|exists:roles,id';
            if ($request->filled('password')) {
                $rules['password'] = 'required|string|min:8|confirmed';
            }
        } else {
            $rules['email'] = 'nullable|email';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->boolean('allow_login')) {
            if ($employee->user_id) {
                $user = User::find($employee->user_id);
                $user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'status' => $validated['status'] === 'active' ? 1 : 0,
                ]);
                if ($request->filled('password')) {
                    $user->update(['password' => \Illuminate\Support\Facades\Hash::make($validated['password'])]);
                }
                $user->syncRoles($request->role_id);
            } else {
                // Create new user if not exists
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => \Illuminate\Support\Facades\Hash::make($validated['password'] ?? 'password'), // Default password if not provided
                    'salon_id' => auth()->user()->salon_id,
                    'status' => $validated['status'] === 'active' ? 1 : 0,
                ]);
                $user->assignRole($request->role_id);
                $employee->user_id = $user->id;
            }
        } else {
            // If login disabled, maybe delete user or deactivate?
            // For now, let's just keep it but maybe unlink? Or do nothing?
            // Requirement didn't specify, but let's assume we just update employee details.
        }

        $employeeData = collect($validated)->except(['password', 'password_confirmation', 'role_id', 'allow_login', 'services'])->toArray();
        $employee->update($employeeData);

        if (isset($validated['services'])) {
            $employee->services()->sync($validated['services']);
        } else {
            $employee->services()->detach();
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted successfully.');
    }

    /**
     * Display the specified employee's bookings.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function bookings($id)
    {
        $employee = Employee::findOrFail($id);

        $bookings = $employee->bookings()
            ->with(['customer', 'services', 'staff'])
            ->latest()
            ->paginate(10);

        return view('admin.employees.bookings', [
            'employee' => $employee,
            'bookings' => $bookings
        ]);
    }
}