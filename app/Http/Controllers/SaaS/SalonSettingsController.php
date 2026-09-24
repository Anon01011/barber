<?php

namespace App\Http\Controllers\SaaS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Traits\HandlesSalonSettings;

class SalonSettingsController extends Controller
{
    use HandlesSalonSettings;
    protected $settingsService;
    protected $mailService;

    public function __construct(\App\Services\SettingsService $settingsService, \App\Services\MailService $mailService)
    {
        $this->settingsService = $settingsService;
        $this->mailService = $mailService;
    }

    public function edit()
    {
        $salon = auth()->user()->salon;
        $settings = $this->settingsService->getAll($salon->id);
        return view('saas.settings.edit', compact('salon', 'settings'));
    }

    public function update(Request $request)
    {
        $salon = auth()->user()->salon;

        try {
            // Check if mail settings are present in the request
            if ($request->has('mail_driver')) {
                $validatedMail = $request->validate([
                    'mail_driver' => 'required|in:smtp,sendmail,log',
                    'mail_host' => 'required_if:mail_driver,smtp|nullable|string',
                    'mail_port' => 'required_if:mail_driver,smtp|nullable|integer',
                    'mail_username' => 'nullable|string',
                    'mail_password' => 'nullable|string',
                    'mail_encryption' => 'nullable|in:tls,ssl',
                    'mail_from_address' => 'required|email',
                    'mail_from_name' => 'required|string',
                ]);

                // Don't update password if it's empty
                if (empty($validatedMail['mail_password'])) {
                    unset($validatedMail['mail_password']);
                }

                $salon->update($validatedMail);
            }

            $this->performSalonUpdate($request, $salon);

            // Automatically clear cache after saving settings
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');

            return redirect()->route('admin.salon-settings.index')->with('success', 'Salon settings updated and cache cleared successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update salon settings: ' . $e->getMessage());
        }
    }


    public function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');

        return back()->with('success', 'System cache cleared successfully.');
    }

    // Mail Settings Methods
    public function editMail()
    {
        $salon = auth()->user()->salon;
        $settings = $this->settingsService->getAll($salon->id);

        // Use the unified settings index with the mail tab active
        request()->merge(['tab' => 'mail']);
        return view('admin.settings.index', compact('salon', 'settings'));
    }

    public function updateMail(Request $request)
    {
        $salon = auth()->user()->salon;

        $validated = $request->validate([
            'mail_driver' => 'required|in:smtp,sendmail,log',
            'mail_host' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_port' => 'required_if:mail_driver,smtp|nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        // Don't update password if it's empty (user doesn't want to change it)
        if (empty($validated['mail_password'])) {
            unset($validated['mail_password']);
        }

        $salon->update($validated);

        return redirect()->route('admin.saas.settings.mail')->with('success', 'Mail settings updated successfully.');
    }


    public function testMail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            $salon = auth()->user()->salon;
            $this->mailService->sendRaw($salon, $request->test_email, 'Test Email - Salon CMS', 'This is a test email from your salon mail configuration.');

            return response()->json(['success' => true, 'message' => 'Test email sent successfully to ' . $request->test_email]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send test email: ' . $e->getMessage()]);
        }
    }

    private function getAllowedTemplateTypes()
    {
        return [
            'booking_confirmation',
            'booking_reminder',
            'booking_cancelled',
            'booking_rescheduled',
            'welcome',
            'sale_receipt',
            'booking_status_updated'
        ];
    }

    // Email Template Methods
    public function emailTemplates()
    {
        $salon = auth()->user()->salon;
        $allowedTypes = $this->getAllowedTemplateTypes();

        $templates = \App\Models\EmailTemplate::where(function ($query) use ($salon) {
            $query->where('salon_id', $salon->id)
                ->orWhereNull('salon_id');
        })
            ->whereIn('type', $allowedTypes)
            ->get()
            ->groupBy('type');

        $templateTypes = collect($allowedTypes);

        return view('saas.settings.templates.index', compact('templates', 'templateTypes', 'salon'));
    }

    public function editEmailTemplate($type)
    {
        if (!in_array($type, $this->getAllowedTemplateTypes())) {
            abort(404, 'Invalid template type.');
        }

        $salon = auth()->user()->salon;

        // Get salon-specific template or system default
        $template = \App\Models\EmailTemplate::forSalon($salon->id, $type);

        // If no template exists, create a blank one for editing
        if (!$template) {
            // Try to get system default to clone
            $systemDefault = \App\Models\EmailTemplate::whereNull('salon_id')
                ->where('type', $type)
                ->first();

            if ($systemDefault) {
                // Clone system default as starting point
                $template = new \App\Models\EmailTemplate([
                    'type' => $type,
                    'salon_id' => $salon->id,
                    'subject' => $systemDefault->subject,
                    'content' => $systemDefault->content,
                    'variables' => $systemDefault->variables,
                    'is_active' => true
                ]);
            } else {
                // No system default, create blank
                $template = new \App\Models\EmailTemplate([
                    'type' => $type,
                    'salon_id' => $salon->id,
                    'subject' => '',
                    'content' => '',
                    'variables' => []
                ]);
            }
        }

        return view('saas.settings.templates.edit', compact('template', 'salon'));
    }

    public function updateEmailTemplate(Request $request, $type)
    {
        if (!in_array($type, $this->getAllowedTemplateTypes())) {
            abort(404, 'Invalid template type.');
        }

        $salon = auth()->user()->salon;

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Update or create salon-specific template
        \App\Models\EmailTemplate::updateOrCreate(
            [
                'salon_id' => $salon->id,
                'type' => $type
            ],
            $validated
        );

        return redirect()->route('admin.saas.settings.email-templates')->with('success', 'Email template updated successfully.');
    }

    public function deleteEmailTemplate($type)
    {
        $salon = auth()->user()->salon;

        \App\Models\EmailTemplate::where('salon_id', $salon->id)
            ->where('type', $type)
            ->delete();

        return redirect()->route('admin.saas.settings.email-templates')->with('success', 'Custom template deleted. System default will be used.');
    }
}
