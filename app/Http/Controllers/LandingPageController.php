<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SettingsService;
use App\Models\Plan;

class LandingPageController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Legacy landing index route
     */
    public function index()
    {
        return $this->welcome();
    }

    /**
     * Welcome / Home page
     */
    public function welcome()
    {
        if (env('SINGLE_SALON_MODE', false)) {
            return redirect()->route('login');
        }

        if (auth()->check()) {
            return redirect()->route('home.dashboard');
        }

        $cms = $this->getCmsData();
        $appName = $cms['appName'];
        $appLogo = $cms['appLogo'];
        $currencySymbol = $this->settingsService->get('currency_symbol', '$');

        // Fetch active plans for the pricing section
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('welcome', compact('cms', 'appName', 'appLogo', 'plans', 'currencySymbol'));
    }

    /**
     * About Us page
     */
    public function about()
    {
        $cms = $this->getCmsData();
        $appName = $cms['appName'];
        $appLogo = $cms['appLogo'];

        return view('about', compact('cms', 'appName', 'appLogo'));
    }

    /**
     * Contact Us page
     */
    public function contact()
    {
        $cms = $this->getCmsData();
        $appName = $cms['appName'];
        $appLogo = $cms['appLogo'];

        return view('contact', compact('cms', 'appName', 'appLogo'));
    }

    /**
     * Handle Contact Form submission
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:150',
            'service_interest' => 'nullable|string|max:100',
            'message' => 'required|string|max:3000',
        ]);

        // Attempt sending notification email if recipient configured, or gracefully log
        try {
            $notificationEmail = $this->settingsService->get('contact_email', $this->settingsService->get('business_email', config('mail.from.address')));
            if ($notificationEmail) {
                // Safe attempt to send notice
                \Mail::raw(
                    "New Inquiry Received from Website Contact Form:\n\n" .
                    "Name: " . $validated['name'] . "\n" .
                    "Email: " . $validated['email'] . "\n" .
                    "Phone: " . ($validated['phone'] ?? 'N/A') . "\n" .
                    "Subject: " . ($validated['subject'] ?? 'Website Inquiry') . "\n" .
                    "Service Interest: " . ($validated['service_interest'] ?? 'General') . "\n\n" .
                    "Message:\n" . $validated['message'],
                    function ($msg) use ($notificationEmail, $validated) {
                        $msg->to($notificationEmail)
                            ->subject("New Inquiry from " . $validated['name'] . " - " . ($validated['subject'] ?? 'Website Contact'));
                    }
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Contact form email failed to send: ' . $e->getMessage());
        }

        return back()->with('success', 'Thank you for reaching out! We have received your message and will get back to you shortly.');
    }

    /**
     * Terms and Conditions page
     */
    public function terms()
    {
        $cms = $this->getCmsData();
        $appName = $cms['appName'];
        $appLogo = $cms['appLogo'];

        return view('terms', compact('cms', 'appName', 'appLogo'));
    }

    /**
     * Privacy Policy page
     */
    public function privacy()
    {
        $cms = $this->getCmsData();
        $appName = $cms['appName'];
        $appLogo = $cms['appLogo'];

        return view('privacy', compact('cms', 'appName', 'appLogo'));
    }

    /**
     * Helper to retrieve all dynamic CMS settings with smart defaults
     */
    protected function getCmsData(): array
    {
        return [
            'appName' => $this->settingsService->get('app_name', config('app.name', 'FST Salon & Barber')),
            'appLogo' => $this->settingsService->getLogoUrl(),
            'appFavicon' => $this->settingsService->getFaviconUrl(),

            // Hero Section
            'heroBadge' => $this->settingsService->get('hero_badge', '✨ Precision Grooming & Luxury Salon Care'),
            'heroTitle' => $this->settingsService->get('hero_title', 'Masterful Precision & Modern Luxury for Hair, Beard & Style'),
            'heroSubtitle' => $this->settingsService->get('hero_subtitle', 'Experience seamless online bookings, VIP master barbering, artisan hair color, and bespoke treatments in one premier grooming destination.'),
            'heroPrimaryBtnText' => $this->settingsService->get('hero_primary_btn_text', 'Book Appointment'),
            'heroPrimaryBtnUrl' => $this->settingsService->get('hero_primary_btn_url', route('saas.register')),
            'heroSecondaryBtnText' => $this->settingsService->get('hero_secondary_btn_text', 'Explore Services'),
            'heroSecondaryBtnUrl' => $this->settingsService->get('hero_secondary_btn_url', '#services-menu'),
            'heroImage' => $this->settingsService->getHeroImageUrl(),
            'heroStat1Val' => $this->settingsService->get('hero_stat_1_val', '15k+'),
            'heroStat1Lbl' => $this->settingsService->get('hero_stat_1_lbl', 'Satisfied Clients'),
            'heroStat2Val' => $this->settingsService->get('hero_stat_2_val', '4.9 ★'),
            'heroStat2Lbl' => $this->settingsService->get('hero_stat_2_lbl', 'Average Rating'),
            'heroStat3Val' => $this->settingsService->get('hero_stat_3_val', '100%'),
            'heroStat3Lbl' => $this->settingsService->get('hero_stat_3_lbl', 'Certified Hygiene'),

            // Services & Experiences Dynamic Titles
            'servicesTitle' => $this->settingsService->get('services_title', 'Signature Grooming & Styling Menu'),
            'servicesSubtitle' => $this->settingsService->get('services_subtitle', 'Indulge in precision haircutting, classic straight razor shaves, bespoke color formulations, and restorative therapies.'),
            'barberHeading' => $this->settingsService->get('barber_heading', "The Gentlemen's Barber Lounge"),
            'barberDesc' => $this->settingsService->get('barber_description', 'Classic hot-towel straight razor shaves, skin fades, precision beard sculpting, and revitalizing scalp rituals.'),
            'salonHeading' => $this->settingsService->get('salon_heading', 'The Haute Hair & Beauty Salon'),
            'salonDesc' => $this->settingsService->get('salon_description', 'Bespoke hair design, dimensional balayage, keratin smoothing, and restorative botanical hair therapies.'),

            // Why Choose Us / Excellence
            'whyTitle' => $this->settingsService->get('why_title', 'The Standard of Excellence'),
            'whySubtitle' => $this->settingsService->get('why_subtitle', 'Every ritual is crafted with obsessive attention to detail, uncompromising hygiene, and VIP hospitality.'),

            // Call to Action Banner
            'ctaBannerTitle' => $this->settingsService->get('cta_banner_title', 'Ready for Your Signature Style?'),
            'ctaBannerSubtitle' => $this->settingsService->get('cta_banner_subtitle', 'Reserve your appointment with our master barbers and visionary stylists today.'),
            'ctaBannerBtnText' => $this->settingsService->get('cta_banner_btn_text', 'Book Your Appointment'),
            'ctaBannerBtnUrl' => $this->settingsService->get('cta_banner_btn_url', route('saas.register')),

            // Contact Information
            'contactPhone' => $this->settingsService->get('contact_phone', '+974 66266581'),
            'contactEmail' => $this->settingsService->get('contact_email', 'info@fsqatar.com'),
            'contactAddress' => $this->settingsService->get('contact_address', 'Salwa Road, Doha, State of Qatar'),
            'contactHours' => $this->settingsService->get('contact_working_hours', 'Mon – Sun: 8:00 AM – 9:00 PM'),
            'contactMap' => $this->settingsService->get('contact_map_iframe', ''),

            // Social Media Links
            'socialFacebook' => $this->settingsService->get('social_facebook', 'https://facebook.com'),
            'socialInstagram' => $this->settingsService->get('social_instagram', 'https://instagram.com'),
            'socialTwitter' => $this->settingsService->get('social_twitter', 'https://twitter.com'),
            'socialLinkedin' => $this->settingsService->get('social_linkedin', 'https://linkedin.com'),
            'socialYoutube' => $this->settingsService->get('social_youtube', ''),

            // About Us Content
            'aboutHeroTitle' => $this->settingsService->get('about_hero_title', 'Where Timeless Craftsmanship Meets Modern Luxury'),
            'aboutHeroSubtitle' => $this->settingsService->get('about_hero_subtitle', 'Dedicated to elevating the grooming and styling ritual for gentlemen and ladies who demand distinction, artistry, and VIP hospitality.'),
            'aboutStoryTitle' => $this->settingsService->get('about_story_title', 'Our Heritage & Philosophy'),
            'aboutStoryP1' => $this->settingsService->get('about_story_p1', 'Born out of a genuine passion for grooming and artistic flair, our sanctuary bridges classic barbering traditions with cutting-edge salon innovations. From handcrafted hot-towel straight razor shaves to bespoke color formulation and restorative hair treatments, every touchpoint is designed to renew and inspire confidence.'),
            'aboutStoryP2' => $this->settingsService->get('about_story_p2', 'We believe true grooming is an art form. Our masters and stylists undergo continuous training in modern techniques, hygiene standards, and client satisfaction to ensure that whether you are in for a quick beard trim or a total transformation, you leave looking and feeling your absolute best.'),
            'aboutMission' => $this->settingsService->get('about_mission', 'To deliver unmatched styling and grooming experiences through master craftsmanship, premium products, and frictionless technology, empowering every client to look confident and feel renewed.'),
            'aboutVision' => $this->settingsService->get('about_vision', 'To stand as the gold standard in premier barber and salon care—a community-centric haven where heritage craftsmanship meets contemporary elegance.'),
            'aboutImage' => $this->settingsService->getAboutImageUrl(),

            // Legal & Policies
            'termsContent' => $this->settingsService->get('terms_content', ''),
            'privacyContent' => $this->settingsService->get('privacy_content', ''),
        ];
    }
}
