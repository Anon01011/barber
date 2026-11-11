<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\GlobalMailConfigService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    protected GlobalMailConfigService $mailConfig;

    public function __construct(GlobalMailConfigService $mailConfig)
    {
        $this->mailConfig = $mailConfig;
    }

    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        $appName = app(\App\Services\SettingsService::class)->get('app_name', config('app.name'));
        return view('auth.forgot-password', compact('appName'));
    }

    /**
     * Handle an incoming password reset link request.
     *
     * Apply the super-admin configured SMTP settings from the database before
     * sending the reset link so the email goes through the configured mail provider
     * rather than falling back to the .env log driver.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Apply global SMTP config from super-admin DB settings.
        $this->mailConfig->apply();

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
