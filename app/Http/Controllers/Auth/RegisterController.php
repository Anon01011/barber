<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        if (env('SINGLE_SALON_MODE', false)) {
            return redirect()->route('login');
        }
        return view('auth.register');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
            ]);

            // Create customer record if we have a salon context
            $salonId = app()->bound('current_salon') ? app('current_salon')->id : (auth()->check() ? auth()->user()->salon_id : null);
            if ($salonId) {
                $existingCustomer = Customer::where('salon_id', $salonId)
                    ->where(function ($q) use ($data) {
                        $q->where('email', $data['email'])
                          ->orWhere('phone', $data['phone']);
                    })
                    ->first();

                if ($existingCustomer) {
                    $existingCustomer->update([
                        'user_id' => $user->id,
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'phone' => $data['phone'],
                    ]);
                } else {
                    Customer::create([
                        'salon_id' => $salonId,
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'phone' => $data['phone'],
                        'preferred_contact' => 'email',
                        'status' => 'active',
                        'user_id' => $user->id
                    ]);
                }
            }

            // Assign customer role
            $customerRole = Role::firstOrCreate(['name' => 'customer']);
            $user->assignRole($customerRole);

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
