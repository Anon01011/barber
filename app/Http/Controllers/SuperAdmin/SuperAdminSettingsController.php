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

            // Website & CMS Settings
            'hero_badge' => 'nullable|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_primary_btn_text' => 'nullable|string|max:100',
            'hero_primary_btn_url' => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/[^\s]+|\/[^\s]*|#[^\s]*)$/i'],
            'hero_secondary_btn_text' => 'nullable|string|max:100',
            'hero_secondary_btn_url' => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/[^\s]+|\/[^\s]*|#[^\s]*)$/i'],
            'hero_image' => 'nullable|image|max:4096',
            'hero_stat_1_val' => 'nullable|string|max:50',
            'hero_stat_1_lbl' => 'nullable|string|max:100',
            'hero_stat_2_val' => 'nullable|string|max:50',
            'hero_stat_2_lbl' => 'nullable|string|max:100',
            'hero_stat_3_val' => 'nullable|string|max:50',
            'hero_stat_3_lbl' => 'nullable|string|max:100',
            'hero_stat_4_val' => 'nullable|string|max:50',
            'hero_stat_4_lbl' => 'nullable|string|max:100',

            'services_title' => 'nullable|string|max:255',
            'services_subtitle' => 'nullable|string',
            'barber_heading' => 'nullable|string|max:255',
            'barber_description' => 'nullable|string',
            'salon_heading' => 'nullable|string|max:255',
            'salon_description' => 'nullable|string',

            'why_title' => 'nullable|string|max:255',
            'why_subtitle' => 'nullable|string',

            'cta_banner_tag' => 'nullable|string|max:100',
            'cta_banner_title' => 'nullable|string|max:255',
            'cta_banner_subtitle' => 'nullable|string',
            'cta_banner_btn_text' => 'nullable|string|max:100',
            'cta_banner_btn_url' => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/[^\s]+|\/[^\s]*|#[^\s]*)$/i'],

            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:150',
            'contact_address' => 'nullable|string|max:255',
            'contact_working_hours' => 'nullable|string|max:150',
            'contact_map_iframe' => 'nullable|string',
            'social_facebook' => 'nullable|string|max:255',
            'social_instagram' => 'nullable|string|max:255',
            'social_twitter' => 'nullable|string|max:255',
            'social_linkedin' => 'nullable|string|max:255',
            'social_youtube' => 'nullable|string|max:255',

            'about_hero_title' => 'nullable|string|max:255',
            'about_hero_subtitle' => 'nullable|string',
            'about_story_title' => 'nullable|string|max:255',
            'about_story_p1' => 'nullable|string',
            'about_story_p2' => 'nullable|string',
            'about_mission' => 'nullable|string',
            'about_vision' => 'nullable|string',
            'about_image' => 'nullable|image|max:4096',

            'terms_page_title' => 'nullable|string|max:255',
            'terms_page_subtitle' => 'nullable|string',
            'terms_last_updated' => 'nullable|string|max:100',
            'terms_content' => 'nullable|string',
            'terms_sections_present' => 'nullable',
            'terms_section_titles' => 'nullable|array',
            'terms_section_titles.*' => 'nullable|string|max:255',
            'terms_section_contents' => 'nullable|array',
            'terms_section_contents.*' => 'nullable|string|max:15000',

            'privacy_page_title' => 'nullable|string|max:255',
            'privacy_page_subtitle' => 'nullable|string',
            'privacy_last_updated' => 'nullable|string|max:100',
            'privacy_content' => 'nullable|string',
            'privacy_sections_present' => 'nullable',
            'privacy_section_titles' => 'nullable|array',
            'privacy_section_titles.*' => 'nullable|string|max:255',
            'privacy_section_contents' => 'nullable|array',
            'privacy_section_contents.*' => 'nullable|string|max:15000',

            // Dynamic Reviews & Testimonials
            'reviews_title' => 'nullable|string|max:255',
            'reviews_subtitle' => 'nullable|string',
            'reviews_present' => 'nullable',
            'review_names' => 'nullable|array',
            'review_names.*' => 'nullable|string|max:150',
            'review_roles' => 'nullable|array',
            'review_roles.*' => 'nullable|string|max:150',
            'review_ratings' => 'nullable|array',
            'review_ratings.*' => 'nullable|integer|min:1|max:5',
            'review_texts' => 'nullable|array',
            'review_texts.*' => 'nullable|string|max:3000',
            'review_avatars' => 'nullable|array',
            'review_avatars.*' => 'nullable|string|max:255',

            // Dynamic FAQ
            'faq_title' => 'nullable|string|max:255',
            'faq_subtitle' => 'nullable|string',
            'faq_present' => 'nullable',
            'faq_questions' => 'nullable|array',
            'faq_questions.*' => 'nullable|string|max:500',
            'faq_answers' => 'nullable|array',
            'faq_answers.*' => 'nullable|string|max:3000',
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

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            $oldHero = $this->settingsService->get('hero_image');
            if ($oldHero && Storage::disk('public')->exists($oldHero)) {
                Storage::disk('public')->delete($oldHero);
            }
            $validated['hero_image'] = $request->file('hero_image')->store('cms', 'public');
        }

        // Handle about image upload
        if ($request->hasFile('about_image')) {
            $oldAbout = $this->settingsService->get('about_image');
            if ($oldAbout && Storage::disk('public')->exists($oldAbout)) {
                Storage::disk('public')->delete($oldAbout);
            }
            $validated['about_image'] = $request->file('about_image')->store('cms', 'public');
        }

        // Handle dynamic Terms & Conditions sections
        if ($request->has('terms_sections_present')) {
            $termsList = [];
            if ($request->has('terms_section_titles') && is_array($request->terms_section_titles)) {
                foreach ($request->terms_section_titles as $idx => $title) {
                    $t = trim($title ?? '');
                    $c = trim($request->terms_section_contents[$idx] ?? '');
                    if ($t !== '' || $c !== '') {
                        $termsList[] = [
                            'title' => $t,
                            'content' => $c,
                        ];
                    }
                }
            }
            $this->settingsService->set('terms_sections', json_encode($termsList));
            $this->settingsService->clearCache('terms_sections');
        }

        // Handle dynamic Privacy Policy sections
        if ($request->has('privacy_sections_present')) {
            $privacyList = [];
            if ($request->has('privacy_section_titles') && is_array($request->privacy_section_titles)) {
                foreach ($request->privacy_section_titles as $idx => $title) {
                    $t = trim($title ?? '');
                    $c = trim($request->privacy_section_contents[$idx] ?? '');
                    if ($t !== '' || $c !== '') {
                        $privacyList[] = [
                            'title' => $t,
                            'content' => $c,
                        ];
                    }
                }
            }
            $this->settingsService->set('privacy_sections', json_encode($privacyList));
            $this->settingsService->clearCache('privacy_sections');
        }

        // Handle dynamic Reviews & Testimonials
        if ($request->has('reviews_present')) {
            $reviewList = [];
            if ($request->has('review_names') && is_array($request->review_names)) {
                foreach ($request->review_names as $idx => $name) {
                    $n = trim($name ?? '');
                    $txt = trim($request->review_texts[$idx] ?? '');
                    if ($n !== '' && $txt !== '') {
                        $reviewList[] = [
                            'name' => $n,
                            'role' => trim($request->review_roles[$idx] ?? 'Salon Owner'),
                            'rating' => max(1, min(5, (int) ($request->review_ratings[$idx] ?? 5))),
                            'text' => $txt,
                            'avatar' => trim($request->review_avatars[$idx] ?? ''),
                        ];
                    }
                }
            }
            $this->settingsService->set('reviews_items', json_encode($reviewList));
            $this->settingsService->clearCache('reviews_items');
        }

        // Handle dynamic FAQ questions and answers
        if ($request->has('faq_present')) {
            $faqList = [];
            if ($request->has('faq_questions') && is_array($request->faq_questions)) {
                foreach ($request->faq_questions as $idx => $q) {
                    $q = trim($q ?? '');
                    $a = trim($request->faq_answers[$idx] ?? '');
                    if ($q !== '' && $a !== '') {
                        $faqList[] = [
                            'question' => $q,
                            'answer' => $a,
                        ];
                    }
                }
            }
            $this->settingsService->set('faq_items', json_encode($faqList));
            $this->settingsService->clearCache('faq_items');
        }

        // Unset raw arrays and presence markers before saving scalar settings
        unset(
            $validated['terms_sections_present'],
            $validated['terms_section_titles'],
            $validated['terms_section_contents'],
            $validated['privacy_sections_present'],
            $validated['privacy_section_titles'],
            $validated['privacy_section_contents'],
            $validated['reviews_present'],
            $validated['review_names'],
            $validated['review_roles'],
            $validated['review_ratings'],
            $validated['review_texts'],
            $validated['review_avatars'],
            $validated['faq_present'],
            $validated['faq_questions'],
            $validated['faq_answers']
        );

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
