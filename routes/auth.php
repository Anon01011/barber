<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('pin-login', [\App\Http\Controllers\Auth\PinLoginController::class, 'showPinLogin'])
        ->name('pin.login');
    Route::post('pin-login', [\App\Http\Controllers\Auth\PinLoginController::class, 'pinLogin'])
        ->middleware('throttle:15,1')
        ->name('pin.login.submit');
    Route::get('pin-login/switch', [\App\Http\Controllers\Auth\PinLoginController::class, 'switchAccount'])
        ->name('pin.switch');

    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:10,1');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:10,1');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // First-time PIN setup prompts
    Route::get('pin/setup-prompt', [\App\Http\Controllers\Auth\PinLoginController::class, 'showFirstTimePinPrompt'])
        ->name('pin.setup.prompt');
    Route::post('pin/setup-prompt', [\App\Http\Controllers\Auth\PinLoginController::class, 'completeFirstTimePinSetup'])
        ->name('pin.setup.complete');
    Route::post('pin/setup-skip', [\App\Http\Controllers\Auth\PinLoginController::class, 'skipFirstTimePinSetup'])
        ->name('pin.setup.skip');

    // Security & Trusted Devices management
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('devices', [\App\Http\Controllers\Security\DeviceManagementController::class, 'index'])
            ->name('devices.index');
        Route::post('devices/{id}/rename', [\App\Http\Controllers\Security\DeviceManagementController::class, 'rename'])
            ->name('devices.rename');
        Route::delete('devices/{id}', [\App\Http\Controllers\Security\DeviceManagementController::class, 'revoke'])
            ->name('devices.revoke');
        Route::post('devices/revoke-all', [\App\Http\Controllers\Security\DeviceManagementController::class, 'revokeAll'])
            ->name('devices.revoke-all');
        Route::post('devices/trust-current', [\App\Http\Controllers\Security\DeviceManagementController::class, 'trustCurrent'])
            ->name('devices.trust-current');

        Route::post('pin/setup', [\App\Http\Controllers\Security\DeviceManagementController::class, 'setupPin'])
            ->name('pin.setup');
        Route::post('pin/change', [\App\Http\Controllers\Security\DeviceManagementController::class, 'changePin'])
            ->name('pin.change');
        Route::post('pin/disable', [\App\Http\Controllers\Security\DeviceManagementController::class, 'disablePin'])
            ->name('pin.disable');
        Route::post('pin/enable', [\App\Http\Controllers\Security\DeviceManagementController::class, 'enablePin'])
            ->name('pin.enable');
    });
});
