<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SuperAdminSettingsController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->middleware(['auth', 'verified', 'role:super_admin']);
        $this->settingsService = $settingsService;
    }

    public function index()
    {
        $settings = $this->settingsService->getAll();
        return view('super-admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // General Settings
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|image|max:2048',
            'app_favicon' => 'nullable|image|mimes:png,ico,x-icon,gif,jpeg,jpg|max:512',
            'app_timezone' => 'required|string',
            'date_format' => 'required|string',
            'time_format' => 'required|string',

            // Payment Settings
            'currency' => 'required|string|max:3',
            'currency_symbol' => 'nullable|string|max:5',
            'currency_code' => 'nullable|string|max:3',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'stripe_publishable_key' => 'nullable|string',
            'stripe_secret_key' => 'nullable|string',
            'paypal_client_id' => 'nullable|string',
            'paypal_secret' => 'nullable|string',
            'enable_manual_payment' => 'nullable|boolean',
            'enable_online_payment' => 'nullable|boolean',

            // Email Settings
            'mail_driver' => 'required|in:smtp,sendmail,mailgun,ses,postmark,resend,log,array',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',

            // Driver Specific Settings
            'mailgun_domain' => 'nullable|string',
            'mailgun_secret' => 'nullable|string',
            'mailgun_endpoint' => 'nullable|string',
            'ses_key' => 'nullable|string',
            'ses_secret' => 'nullable|string',
            'ses_region' => 'nullable|string',
            'postmark_token' => 'nullable|string',
            'resend_key' => 'nullable|string',

            // Integration Settings
            'google_analytics_id' => 'nullable|string',
            'facebook_pixel_id' => 'nullable|string',
            'enable_sms' => 'nullable|boolean',
            'sms_provider' => 'nullable|in:twilio',
            'twilio_sid' => 'nullable|string',
            'twilio_token' => 'nullable|string',
            'twilio_from' => 'nullable|string',
            // Email Automation Settings
            'email_subscription_expired_enabled' => 'nullable|boolean',
            'email_subscription_expiring_soon_enabled' => 'nullable|boolean',
            'email_subscription_expiring_soon_days_1' => 'nullable|integer|min:1|max:30',
            'email_subscription_expiring_soon_days_2' => 'nullable|integer|min:1|max:30',
        ]);

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            $oldLogo = $this->settingsService->get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $validated['app_logo'] = $request->file('app_logo')->store('logos', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('app_favicon')) {
            $oldFavicon = $this->settingsService->get('app_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            $validated['app_favicon'] = $request->file('app_favicon')->store('favicons', 'public');
        }

        // Save all settings
        foreach ($validated as $key => $value) {
            $this->settingsService->set($key, $value);
        }

        // Handle boolean checkboxes
        $this->settingsService->set('enable_sms', $request->boolean('enable_sms'));
        $this->settingsService->set('enable_manual_payment', $request->boolean('enable_manual_payment'));
        $this->settingsService->set('enable_online_payment', $request->boolean('enable_online_payment'));

        $this->settingsService->set('email_subscription_expired_enabled', $request->boolean('email_subscription_expired_enabled'));
        $this->settingsService->set('email_subscription_expiring_soon_enabled', $request->boolean('email_subscription_expiring_soon_enabled'));

        // Clear only global settings cache (not salon-specific caches)
        foreach (array_keys($validated) as $key) {
            $this->settingsService->clearCache($key);
        }
        $this->settingsService->clearCache('enable_sms');
        $this->settingsService->clearCache('enable_manual_payment');
        $this->settingsService->clearCache('enable_online_payment');
        $this->settingsService->clearCache(); // Clear the 'all' cache for global settings

        return redirect()->route('admin.system-settings.index')
            ->with('success', 'System settings updated successfully.');
    }

    public function testEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            // Temporarily override config for this request
            $settings = $this->settingsService->getAll();

            $mailConfig = [
                'mail.default' => $settings['mail_driver'] ?? config('mail.default'),
                'mail.from.address' => $settings['mail_from_address'] ?? config('mail.from.address'),
                'mail.from.name' => $settings['mail_from_name'] ?? config('mail.from.name'),
            ];

            // Add driver specific configs
            if (($settings['mail_driver'] ?? '') === 'smtp') {
                $mailConfig['mail.mailers.smtp.host'] = $settings['mail_host'] ?? config('mail.mailers.smtp.host');
                $mailConfig['mail.mailers.smtp.port'] = isset($settings['mail_port']) ? (int) $settings['mail_port'] : config('mail.mailers.smtp.port');
                $mailConfig['mail.mailers.smtp.encryption'] = $settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption');
                $mailConfig['mail.mailers.smtp.username'] = $settings['mail_username'] ?? config('mail.mailers.smtp.username');
                $mailConfig['mail.mailers.smtp.password'] = $settings['mail_password'] ?? config('mail.mailers.smtp.password');
            } elseif (($settings['mail_driver'] ?? '') === 'mailgun') {
                $mailConfig['services.mailgun.domain'] = $settings['mailgun_domain'] ?? config('services.mailgun.domain');
                $mailConfig['services.mailgun.secret'] = $settings['mailgun_secret'] ?? config('services.mailgun.secret');
                $mailConfig['services.mailgun.endpoint'] = $settings['mailgun_endpoint'] ?? config('services.mailgun.endpoint');
            } elseif (($settings['mail_driver'] ?? '') === 'ses') {
                $mailConfig['services.ses.key'] = $settings['ses_key'] ?? config('services.ses.key');
                $mailConfig['services.ses.secret'] = $settings['ses_secret'] ?? config('services.ses.secret');
                $mailConfig['services.ses.region'] = $settings['ses_region'] ?? config('services.ses.region');
            } elseif (($settings['mail_driver'] ?? '') === 'postmark') {
                $mailConfig['services.postmark.token'] = $settings['postmark_token'] ?? config('services.postmark.token');
            } elseif (($settings['mail_driver'] ?? '') === 'resend') {
                $mailConfig['services.resend.key'] = $settings['resend_key'] ?? config('services.resend.key');
            }

            config($mailConfig);

            \Mail::raw('This is a test email from your Salon CMS System Settings.', function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Test Email - Salon CMS');
            });

            return back()->with('success', 'Test email sent successfully to ' . $request->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    public function clearCache()
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');

            // Also clear settings cache
            $this->settingsService->clearCache();

            return back()->with('success', 'System cache cleared successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
}
