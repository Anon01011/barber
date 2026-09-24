<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('phone')) {
            $phoneVal = $request->input('phone');
            $request->merge([
                'phone' => function_exists('normalize_phone') ? \normalize_phone($phoneVal) : preg_replace('/[^\d+]/', '', $phoneVal),
            ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string', 'max:20', 'unique:' . User::class, 'phone'],
            'password' => [
                'required',
                'confirmed',
                'different:email',
                Rules\Password::min(8)->letters()->numbers()
            ],
        ], [
            'password.different' => 'Your password cannot be the same as your email address for security reasons.',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // Create customer record if we have a salon context
            // In a multi-tenant setup, customers must belong to a salon.
            // If registering globally, we skip customer creation until they interact with a salon.
            $salonId = app()->bound('current_salon') ? app('current_salon')->id : (auth()->check() ? auth()->user()->salon_id : null);
            if ($salonId) {
                try {
                    $existingCustomer = Customer::where('salon_id', $salonId)
                        ->where(function ($q) use ($request) {
                            $q->where('email', $request->email)
                              ->orWhere('phone', $request->phone);
                        })
                        ->first();

                    if ($existingCustomer) {
                        $existingCustomer->update([
                            'user_id' => $user->id,
                            'name' => $request->name,
                            'email' => $request->email,
                            'phone' => $request->phone,
                        ]);
                    } else {
                        Customer::create([
                            'salon_id' => $salonId,
                            'name' => $request->name,
                            'email' => $request->email,
                            'phone' => $request->phone,
                            'preferred_contact' => 'email',
                            'status' => 'active',
                            'user_id' => $user->id
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log error but allow user creation to proceed
                    \Log::warning('Failed to create or link customer record during registration: ' . $e->getMessage());
                }
            }

            // Ensure customer role exists
            $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

            // Add default permissions for customer role if it's newly created
            if ($customerRole->wasRecentlyCreated) {
                $permissions = [
                    'appointments.view',
                    'appointments.create',
                    'appointments.cancel',
                    'services.view'
                ];

                foreach ($permissions as $permission) {
                    Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
                }

                $customerRole->syncPermissions($permissions);
            }

            // Assign customer role
            $user->assignRole($customerRole);

            DB::commit();

            event(new Registered($user));

            Auth::login($user);

            // Redirect based on role
            if ($user->hasRole('customer')) {
                // Only redirect to appointments if we are in a salon context
                if (app()->bound('current_salon')) {
                    return redirect()->route('customer.appointments.index');
                }
                // Otherwise go to dashboard/home
                return redirect(route('dashboard', absolute: false));
            }
            return redirect(route('dashboard', absolute: false));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
