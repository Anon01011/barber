<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Security: Revoke all other trusted devices on password change
        $deviceService = app(\App\Services\TrustedDeviceService::class);
        $currentDevice = $deviceService->getTrustedDeviceFromRequest($request);
        $deviceService->revokeAllOtherDevices($request->user(), $currentDevice);

        return back()->with('status', 'password-updated');
    }
}
