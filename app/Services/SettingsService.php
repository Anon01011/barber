<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected $cachePrefix = 'settings_';
    protected $cacheTime = 3600; // 1 hour

    protected $defaults = [
        // Theme & Branding
        'theme_color' => 'blue',
        'logo' => 'default-logo.png',

        // Business Details
        'business_name' => 'My Business',
        'business_email' => 'business@example.com',
        'business_phone' => '',
        'business_address' => '',

        // Social Media
        'facebook_url' => '',
        'instagram_url' => '',
        'twitter_url' => '',
        'linkedin_url' => '',

        // Appointment & Booking Settings
        'appointment_buffer_time' => 15,
        'allow_overlapping_bookings' => false,
        'require_deposit' => false,
        'deposit_amount' => 0,
        'deposit_type' => 'fixed',
        'cancellation_policy' => '',
        'cancellation_hours' => 24,
        'no_show_fee' => 0,
        'auto_confirm_bookings' => false,
        'send_booking_confirmation' => true,
        'send_reminder_sms' => false,
        'reminder_hours_before' => 24,

        // Guest Booking
        'guest_booking_enabled' => true,
        'default_guest_status' => 'pending',
        'working_hours_start' => '08:00',
        'working_hours_end' => '20:00',
        'advance_booking_days' => 30,
        'slot_duration' => 30,

        // Notification Preferences
        'notify_new_booking' => true,
        'notify_cancellation' => true,
        'notify_low_stock' => true,
        'notify_new_customer' => false,
        'notification_email' => '',
        'notification_channels' => ['email'],
        'daily_summary_enabled' => false,
        'daily_summary_time' => '18:00',

        // Payment & Tax Settings
        'tax_enabled' => false,
        'tax_enabled_pos' => false,
        'tax_enabled_services' => false,
        'tax_rate' => 0,
        'tax_name' => 'Tax',
        'tax_number' => '',
        'currency_code' => 'USD',
        'currency_symbol' => '$',
        'payment_methods' => ['cash', 'card'],
        'accept_cash' => true,
        'accept_card' => true,
        'accept_online' => false,
        'stripe_enabled' => false,
        'paypal_enabled' => false,
        'tip_enabled' => true,
        'default_tip_percentage' => 15,

        // Email & SMS Templates
        'email_signature' => '',
        'booking_confirmation_template' => 'Dear {customer_name}, your appointment is confirmed for {date} at {time}.',
        'booking_reminder_template' => 'Reminder: You have an appointment tomorrow at {time}.',
        'cancellation_template' => 'Your appointment on {date} at {time} has been cancelled.',
        'welcome_email_template' => 'Welcome to {business_name}! We\'re excited to serve you.',
        'sms_provider' => '',
        'sms_from_number' => '',

        // Security & Privacy
        'require_customer_verification' => false,
        'gdpr_enabled' => false,
        'data_retention_days' => 365,
        'allow_customer_data_export' => true,
        'allow_customer_data_deletion' => true,
        'session_timeout_minutes' => 120,
        'require_2fa' => false,

        // Localization & Regional
        'timezone' => 'UTC',
        'date_format' => 'Y-m-d',
        'time_format' => '24h',
        'language' => 'en',
        'week_start_day' => 'monday',

        // Advanced Features
        'loyalty_program_enabled' => false,
        'points_per_dollar' => 1,
        'referral_program_enabled' => false,
        'referral_discount' => 0,
        'birthday_discount_enabled' => false,
        'birthday_discount_percentage' => 10,
        'maintenance_mode' => false,
        'maintenance_message' => 'We are currently under maintenance. Please check back soon.',

        // Email Automation Defaults
        'email_subscription_expired_enabled' => true,
        'email_subscription_expiring_soon_enabled' => true,
        'email_subscription_expiring_soon_days_1' => 7,
        'email_subscription_expiring_soon_days_2' => 3,

        // Website & CMS Defaults (Luxury Salon & Modern Barbershop)
        'hero_badge' => '✨ Precision Grooming & Luxury Salon Care',
        'hero_title' => 'Masterful Precision & Modern Luxury for Hair, Beard & Style',
        'hero_subtitle' => 'Experience seamless online bookings, VIP master barbering, artisan hair color, and bespoke treatments in one premier grooming destination.',
        'hero_primary_btn_text' => 'Book Appointment',
        'hero_primary_btn_url' => '/register-salon',
        'hero_secondary_btn_text' => 'Explore Services',
        'hero_secondary_btn_url' => '#services-menu',
        'hero_image' => '',
        'hero_stat_1_val' => '15k+',
        'hero_stat_1_lbl' => 'Satisfied Clients',
        'hero_stat_2_val' => '4.9 ★',
        'hero_stat_2_lbl' => 'Average Rating',
        'hero_stat_3_val' => '100%',
        'hero_stat_3_lbl' => 'Certified Hygiene',

        'services_title' => 'Signature Grooming & Styling Menu',
        'services_subtitle' => 'Indulge in precision haircutting, classic straight razor shaves, bespoke color formulations, and restorative therapies.',
        'barber_heading' => "The Gentlemen's Barber Lounge",
        'barber_description' => 'Classic hot-towel straight razor shaves, skin fades, precision beard sculpting, and revitalizing scalp rituals.',
        'salon_heading' => 'The Haute Hair & Beauty Salon',
        'salon_description' => 'Bespoke hair design, dimensional balayage, keratin smoothing, and restorative botanical hair therapies.',

        'why_title' => 'The Standard of Excellence',
        'why_subtitle' => 'Every ritual is crafted with obsessive attention to detail, uncompromising hygiene, and VIP hospitality.',

        'cta_banner_title' => 'Ready for Your Signature Style?',
        'cta_banner_subtitle' => 'Reserve your appointment with our master barbers and visionary stylists today.',
        'cta_banner_btn_text' => 'Book Your Appointment',
        'cta_banner_btn_url' => '/register-salon',

        'contact_phone' => '+974 66266581',
        'contact_email' => 'info@fsqatar.com',
        'contact_address' => 'Salwa Road, Doha, State of Qatar',
        'contact_working_hours' => 'Mon – Sun: 8:00 AM – 9:00 PM',
        'contact_map_iframe' => '',
        'social_facebook' => 'https://facebook.com',
        'social_instagram' => 'https://instagram.com',
        'social_twitter' => 'https://twitter.com',
        'social_linkedin' => 'https://linkedin.com',
        'social_youtube' => '',

        'about_hero_title' => 'Where Timeless Craftsmanship Meets Modern Luxury',
        'about_hero_subtitle' => 'Dedicated to elevating the grooming and styling ritual for gentlemen and ladies who demand distinction, artistry, and VIP hospitality.',
        'about_story_title' => 'Our Heritage & Philosophy',
        'about_story_p1' => 'Born out of a genuine passion for grooming and artistic flair, our sanctuary bridges classic barbering traditions with cutting-edge salon innovations. From handcrafted hot-towel straight razor shaves to bespoke color formulation and restorative hair treatments, every touchpoint is designed to renew and inspire confidence.',
        'about_story_p2' => 'We believe true grooming is an art form. Our masters and stylists undergo continuous training in modern techniques, hygiene standards, and client satisfaction to ensure that whether you are in for a quick beard trim or a total transformation, you leave looking and feeling your absolute best.',
        'about_mission' => 'To deliver unmatched styling and grooming experiences through master craftsmanship, premium products, and frictionless technology, empowering every client to look confident and feel renewed.',
        'about_vision' => 'To stand as the gold standard in premier barber and salon care—a community-centric haven where heritage craftsmanship meets contemporary elegance.',
        'about_image' => '',

        'terms_content' => '',
        'privacy_content' => '',
    ];

    private $dbErrorOccurred = false;

    public function get($key, $default = null, $salonId = null, $branchId = null)
    {
        // 0. Short-circuit if we already know the DB is having issues
        if ($this->dbErrorOccurred) {
            return $default ?? ($this->defaults[$key] ?? '');
        }

        // Auto-detect salon_id if not provided
        if ($salonId === null && auth()->check() && auth()->user()->salon_id) {
            $salonId = auth()->user()->salon_id;
        }

        // Cache key includes salon and branch
        $cacheKey = $this->cachePrefix . $key .
            ($salonId ? '_s' . $salonId : '') .
            ($branchId ? '_b' . $branchId : '');

        try {
            $value = Cache::remember($cacheKey, $this->cacheTime, function () use ($key, $default, $salonId, $branchId) {
                // 1. Check Branch Specific Setting (if branch provided)
                if ($branchId) {
                    $setting = Setting::where('key', $key)
                        ->where('branch_id', $branchId)
                        ->when($salonId, function ($q) use ($salonId) {
                            return $q->where('salon_id', $salonId);
                        })
                        ->first();

                    if ($setting)
                        return $setting->value;
                }

                // 2. Check Salon Global Setting (if salon provided)
                if ($salonId) {
                    $setting = Setting::where('key', $key)
                        ->where('salon_id', $salonId)
                        ->whereNull('branch_id')
                        ->first();

                    if ($setting)
                        return $setting->value;
                }

                // 3. Check System Global Setting (fallback)
                $setting = Setting::where('key', $key)
                    ->whereNull('salon_id')
                    ->whereNull('branch_id')
                    ->first();

                $value = $setting ? $setting->value : ($default ?? $this->defaults[$key] ?? null);

                // Dynamic Default: Use actual salon name for business_name if not explicitly set
                if ($key === 'business_name' && ($value === 'My Business' || $value === null) && $salonId) {
                    $salon = \App\Models\Salon::find($salonId);
                    if ($salon) {
                        return $salon->name;
                    }
                }

                return $value;
            });

            // Filter out string "null" values and convert to empty strings for HTML5 validation
            if ($value === 'null' || $value === null) {
                $value = '';
            } elseif (is_bool($value) || is_int($value) || is_float($value)) {
                // Return scalar types as-is — do NOT run trim() on non-strings (PHP 8 TypeError)
                return $value;
            } elseif (is_string($value)) {
                // Remove surrounding quotes if present
                $value = trim($value, '"\'');
            }

            return $value;

        } catch (\Exception $e) {
            // Log the error but don't crash or hang
            $this->dbErrorOccurred = true;
            \Log::error("SettingsService DB Error (Metadata Lock?): " . $e->getMessage());
            return $default ?? ($this->defaults[$key] ?? '');
        }
    }

    public function set($key, $value, $salonId = null, $branchId = null)
    {
        // When storing a global setting (no salonId), bypass the BelongsToSalon
        // creating hook that would auto-assign the current_salon context.
        if ($salonId === null) {
            $setting = Setting::withoutGlobalScopes()->updateOrCreate(
                [
                    'key'       => $key,
                    'salon_id'  => null,
                    'branch_id' => $branchId,
                ],
                ['value' => $value]
            );
            // Force salon_id to null in case the creating hook already fired on a new record
            if ($setting->wasRecentlyCreated && $setting->salon_id !== null) {
                $setting->salon_id = null;
                $setting->saveQuietly();
            }
        } else {
            $setting = Setting::updateOrCreate(
                [
                    'key'       => $key,
                    'salon_id'  => $salonId,
                    'branch_id' => $branchId,
                ],
                ['value' => $value]
            );
        }

        \Log::info("SettingsService: Set $key", [
            'value'    => $value,
            'salon_id' => $salonId,
            'db_value' => $setting->value
        ]);

        $this->clearCache($key, $salonId, $branchId);
        return $setting;
    }

    public function getAll($salonId = null, $branchId = null)
    {
        // Auto-detect salon_id if not provided
        if ($salonId === null && auth()->check() && auth()->user()->salon_id) {
            $salonId = auth()->user()->salon_id;
        }

        $cacheKey = $this->cachePrefix . 'all' .
            ($salonId ? '_s' . $salonId : '') .
            ($branchId ? '_b' . $branchId : '');

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($salonId, $branchId) {
            // Start with defaults
            $settings = $this->defaults;

            // Dynamic Default: Use actual salon name for business_name
            if ($salonId) {
                $salon = \App\Models\Salon::find($salonId);
                if ($salon) {
                    $settings['business_name'] = $salon->name;
                }
            }

            // 1. Get System Global Settings (no salon/branch)
            $globalSettings = Setting::whereNull('salon_id')
                ->whereNull('branch_id')
                ->pluck('value', 'key')
                ->toArray();
            $settings = array_merge($settings, $globalSettings);

            // 2. Get Salon Global Settings (if salon provided)
            if ($salonId) {
                $salonSettings = Setting::where('salon_id', $salonId)
                    ->whereNull('branch_id')
                    ->pluck('value', 'key')
                    ->toArray();
                $settings = array_merge($settings, $salonSettings);
            }

            // 3. Get Branch Specific Settings (if branch provided)
            if ($branchId) {
                $branchSettings = Setting::where('branch_id', $branchId)
                    ->when($salonId, function ($q) use ($salonId) {
                        return $q->where('salon_id', $salonId);
                    })
                    ->pluck('value', 'key')
                    ->toArray();
                $settings = array_merge($settings, $branchSettings);
            }

            // Filter out string "null" values and convert to empty strings for HTML5 validation
            // Also strip any surrounding quotes that may have been incorrectly stored
            foreach ($settings as $key => $value) {
                if ($value === 'null' || $value === null) {
                    $settings[$key] = '';
                } elseif (is_bool($value) || is_int($value) || is_float($value)) {
                    // Keep scalar types as-is — trim() on non-string is TypeError in PHP 8
                    // $settings[$key] stays unchanged
                } elseif (is_string($value)) {
                    // Remove surrounding quotes if present
                    $settings[$key] = trim($value, '"\'');
                }
            }

            return $settings;
        });
    }

    public function clearCache($key = null, $salonId = null, $branchId = null)
    {
        if ($key) {
            Cache::forget($this->cachePrefix . $key .
                ($salonId ? '_s' . $salonId : '') .
                ($branchId ? '_b' . $branchId : ''));
        }
        Cache::forget($this->cachePrefix . 'all' .
            ($salonId ? '_s' . $salonId : '') .
            ($branchId ? '_b' . $branchId : ''));
    }

    public function getThemeColor()
    {
        $defaultColor = 'pink';
        if (auth()->check() && auth()->user()->salon && auth()->user()->salon->business_type === 'barber') {
            $defaultColor = 'barber';
        }
        return $this->get('theme_color', $defaultColor);
    }

    public function getBusinessName()
    {
        return $this->get('business_name', config('app.name'));
    }

    public function getBusinessEmail()
    {
        return $this->get('business_email', config('mail.from.address'));
    }

    public function getBusinessPhone()
    {
        return $this->get('business_phone', '');
    }

    public function getBusinessAddress()
    {
        return $this->get('business_address', '');
    }

    public function getSocialLinks()
    {
        return [
            'facebook' => $this->get('facebook_url'),
            'instagram' => $this->get('instagram_url'),
            'twitter' => $this->get('twitter_url'),
            'linkedin' => $this->get('linkedin_url'),
        ];
    }

    public static function featureEnabled(string $feature): bool
    {
        $instance = app(SettingsService::class);
        $features = $instance->get('features', []);
        return isset($features[$feature]) ? (bool) $features[$feature] : false;
    }

    public function getFaviconUrl()
    {
        $favicon = $this->get('app_favicon');
        if ($favicon && \Illuminate\Support\Facades\Storage::disk('public')->exists($favicon)) {
            $cacheBuster = \Illuminate\Support\Facades\Storage::disk('public')->lastModified($favicon);
            return '/storage/' . ltrim($favicon, '/') . '?v=' . $cacheBuster;
        }

        $cacheBuster = file_exists(public_path('favicons/favicon.png'))
            ? filemtime(public_path('favicons/favicon.png'))
            : time();

        return '/favicons/favicon.png?v=' . $cacheBuster;
    }

    public function getLogoUrl($salonId = null)
    {
        // 1. Try to get salon-specific logo first
        $logo = $this->get('logo', null, $salonId);

        // 2. If not found, try to get global app logo
        if (!$logo) {
            $logo = $this->get('app_logo');
        }

        // 3. Return URL or default with robust root-relative fallback
        if ($logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($logo)) {
            $cacheBuster = \Illuminate\Support\Facades\Storage::disk('public')->lastModified($logo);
            return '/storage/' . ltrim($logo, '/') . '?v=' . $cacheBuster;
        }

        // Robust fallback to default logo in public directory
        $cacheBuster = file_exists(public_path('logos/fst-logo.png'))
            ? filemtime(public_path('logos/fst-logo.png'))
            : time();

        return '/logos/fst-logo.png?v=' . $cacheBuster;
    }

    public function getHeroImageUrl()
    {
        $image = $this->get('hero_image');
        if ($image && \Illuminate\Support\Facades\Storage::disk('public')->exists($image)) {
            $cacheBuster = \Illuminate\Support\Facades\Storage::disk('public')->lastModified($image);
            return '/storage/' . ltrim($image, '/') . '?v=' . $cacheBuster;
        }
        // Premium default hero image for luxury salon and barber
        return 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80';
    }

    public function getAboutImageUrl()
    {
        $image = $this->get('about_image');
        if ($image && \Illuminate\Support\Facades\Storage::disk('public')->exists($image)) {
            $cacheBuster = \Illuminate\Support\Facades\Storage::disk('public')->lastModified($image);
            return '/storage/' . ltrim($image, '/') . '?v=' . $cacheBuster;
        }
        // Master barber craftsmanship & styling salon image
        return 'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80';
    }
}