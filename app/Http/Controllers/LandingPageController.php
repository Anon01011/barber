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
        $appName = $this->settingsService->get('app_name', config('app.name', 'FST Salon & Barber'));
        $contactEmail = $this->settingsService->get('contact_email', 'info@fsqatar.com');

        return [
            'appName' => $appName,
            'appLogo' => $this->settingsService->getLogoUrl(),
            'appFavicon' => $this->settingsService->getFaviconUrl(),

            // Hero Section
            'heroBadge' => $this->settingsService->get('hero_badge', '✨ The All-In-One Cloud Platform for Salons & Barbershops'),
            'heroTitle' => $this->settingsService->get('hero_title', 'Supercharge Your Salon & Barbershop Operations & Growth'),
            'heroSubtitle' => $this->settingsService->get('hero_subtitle', 'Effortless 24/7 online appointments, omnichannel cloud POS, automated client retention, staff commissions, and multi-branch control built for modern beauty & grooming leaders.'),
            'heroPrimaryBtnText' => $this->settingsService->get('hero_primary_btn_text', 'Start 14-Day Free Trial'),
            'heroPrimaryBtnUrl' => $this->settingsService->get('hero_primary_btn_url', route('saas.register')),
            'heroSecondaryBtnText' => $this->settingsService->get('hero_secondary_btn_text', 'View Pricing Plans'),
            'heroSecondaryBtnUrl' => $this->settingsService->get('hero_secondary_btn_url', '#plans-section'),
            'heroImage' => $this->settingsService->getHeroImageUrl(),
            'heroStat1Val' => $this->settingsService->get('hero_stat_1_val', '15k+'),
            'heroStat1Lbl' => $this->settingsService->get('hero_stat_1_lbl', 'Active Salons & Barbers'),
            'heroStat2Val' => $this->settingsService->get('hero_stat_2_val', '4.9 ★'),
            'heroStat2Lbl' => $this->settingsService->get('hero_stat_2_lbl', 'Software Satisfaction'),
            'heroStat3Val' => $this->settingsService->get('hero_stat_3_val', '99.9%'),
            'heroStat3Lbl' => $this->settingsService->get('hero_stat_3_lbl', 'Cloud Uptime SLA'),
            'heroStat4Val' => $this->settingsService->get('hero_stat_4_val', '100%'),
            'heroStat4Lbl' => $this->settingsService->get('hero_stat_4_lbl', 'PCI-DSS Compliant'),

            // Services & Experiences Dynamic Titles
            'servicesTitle' => $this->settingsService->get('services_title', 'Signature Platform Capabilities'),
            'servicesSubtitle' => $this->settingsService->get('services_subtitle', 'Everything needed to run, scale, and automate modern barbershops and high-end beauty salons.'),
            'barberHeading' => $this->settingsService->get('barber_heading', "The Barbershop Edition"),
            'barberDesc' => $this->settingsService->get('barber_description', 'Fast chair rotations, walk-in lounge monitors, instant tap POS, and consumable blade tracking.'),
            'salonHeading' => $this->settingsService->get('salon_heading', 'The Luxury Salon Edition'),
            'salonDesc' => $this->settingsService->get('salon_description', 'Multi-service appointment chaining, client color formula history, assistant routing, and VIP packages.'),

            // Why Choose Us / Excellence
            'whyTitle' => $this->settingsService->get('why_title', 'Engineered for Salon Success'),
            'whySubtitle' => $this->settingsService->get('why_subtitle', 'Purpose-built workflows tailored to the unique rhythms of fast-paced barbershop rotations and high-touch luxury salon rituals.'),

            // Call to Action Banner
            'ctaBannerTag' => $this->settingsService->get('cta_banner_tag', 'Transform Your Business'),
            'ctaBannerTitle' => $this->settingsService->get('cta_banner_title', 'Ready to Modernize Your Salon Operations?'),
            'ctaBannerSubtitle' => $this->settingsService->get('cta_banner_subtitle', 'Join over 15,000 salon and barbershop owners who trust us with their bookings, payments, and client relationships.'),
            'ctaBannerBtnText' => $this->settingsService->get('cta_banner_btn_text', 'Register Salon Now'),
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
            'aboutHeroTitle' => $this->settingsService->get('about_hero_title', 'Powering Modern Salons & Barbershops Globally'),
            'aboutHeroSubtitle' => $this->settingsService->get('about_hero_subtitle', 'We empower beauty entrepreneurs, barbers, and multi-branch salon brands with intelligent technology, frictionless bookings, and automated growth.'),
            'aboutStoryTitle' => $this->settingsService->get('about_story_title', 'Our Mission & Heritage'),
            'aboutStoryP1' => $this->settingsService->get('about_story_p1', 'Born out of a genuine passion for grooming artistry and operational excellence, our platform bridges traditional craftsmanship with cutting-edge cloud intelligence. From single-chair independent barbers to luxury multi-location hair spas, we provide the digital foundation to grow sustainably.'),
            'aboutStoryP2' => $this->settingsService->get('about_story_p2', 'We believe technology should simplify your day, not complicate it. Our mission is to eliminate phone tag, automate scheduling, optimize inventory, and track team commissions effortlessly so you can focus on delivering unforgettable client transformations.'),
            'aboutMission' => $this->settingsService->get('about_mission', 'To empower beauty and grooming professionals worldwide with intuitive, enterprise-grade software that accelerates growth, elevates client loyalty, and maximizes profitability.'),
            'aboutVision' => $this->settingsService->get('about_vision', 'To be the world’s most trusted salon management ecosystem, defining the standard for salon operations, guest experiences, and business automation.'),
            'aboutImage' => $this->settingsService->getAboutImageUrl(),

            // Legal & Policies
            'termsPageTitle' => $this->settingsService->get('terms_page_title', 'Terms & Conditions'),
            'termsPageSubtitle' => $this->settingsService->get('terms_page_subtitle', 'SaaS Platform Service Agreement, Master Subscription Terms, and Salon Partner Policies.'),
            'termsLastUpdated' => $this->settingsService->get('terms_last_updated', 'November 2026'),
            'termsContent' => $this->settingsService->get('terms_content', ''),
            'termsFormatted' => self::formatLegalContent($this->settingsService->get('terms_content', '')),
            'termsSections' => $this->getTermsSections($appName, $this->settingsService->get('contact_email', 'info@fsqatar.com')),

            'privacyPageTitle' => $this->settingsService->get('privacy_page_title', 'Privacy Policy'),
            'privacyPageSubtitle' => $this->settingsService->get('privacy_page_subtitle', 'Your privacy and salon client data security are our highest priority. Learn how our cloud platform protects your data.'),
            'privacyLastUpdated' => $this->settingsService->get('privacy_last_updated', 'November 2026'),
            'privacyContent' => $this->settingsService->get('privacy_content', ''),
            'privacyFormatted' => self::formatLegalContent($this->settingsService->get('privacy_content', '')),
            'privacySections' => $this->getPrivacySections($appName, $this->settingsService->get('contact_email', 'info@fsqatar.com')),

            // Dynamic Reviews & Testimonials Section
            'reviewsTitle' => $this->settingsService->get('reviews_title', 'Trusted by 15,000+ Salons & Barbers Worldwide'),
            'reviewsSubtitle' => $this->settingsService->get('reviews_subtitle', 'Hear how leading barbershops and luxury hair studios streamlined appointments, eliminated no-shows, and scaled revenue.'),
            'reviewsItems' => $this->getReviewsItems(),

            // Dynamic FAQ Section
            'faqTitle' => $this->settingsService->get('faq_title', 'Frequently Asked Questions'),
            'faqSubtitle' => $this->settingsService->get('faq_subtitle', 'Everything you need to know about our salon platform, 14-day trial, POS hardware, and data migration.'),
            'faqItems' => $this->getFaqItems(),
        ];
    }

    /**
     * Retrieve and decode dynamic FAQ items safely
     */
    protected function getFaqItems(): array
    {
        $raw = $this->settingsService->get('faq_items', '');
        if (is_array($raw)) {
            return $raw;
        }
        if (is_string($raw) && !empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }

        return [
            [
                'question' => 'How does the 14-day free trial work?',
                'answer' => 'You get complete, unrestricted access to all salon management features for 14 days without entering credit card details. You can set up your services, add staff members, test online appointments, and start selling via POS immediately.'
            ],
            [
                'question' => 'Can I use this software for both a barbershop and a beauty salon?',
                'answer' => 'Yes! The platform is purpose-built with dual operational modes. Barbershops enjoy rapid walk-in rotations, chair dispatching, and 5-second POS checkouts, while hair and beauty salons benefit from multi-service appointment chaining, client color formulation cards, and VIP beauty packages.'
            ],
            [
                'question' => 'Can we migrate our existing customer data and appointment history?',
                'answer' => 'Absolutely. You can import your client contacts, inventory items, and price catalogs via Excel or CSV with one click. Our onboarding team is also on standby 24/7 to assist with automated data migration from legacy platforms.'
            ],
            [
                'question' => 'Does the platform support multi-branch salon chains?',
                'answer' => 'Yes. Our centralized HQ architecture allows multi-location brands to manage staff schedules, monitor consolidated cross-branch revenues, transfer inventory between branches, and share VIP customer profiles seamlessly.'
            ],
            [
                'question' => 'What hardware do we need for the POS register?',
                'answer' => 'Our cloud POS works smoothly on any device with a modern browser—including iPads, Android tablets, touchscreen POS terminals, Windows PCs, and Macs. It integrates with standard thermal receipt printers, barcode scanners, and wireless card terminals.'
            ],
            [
                'question' => 'Can I cancel or change my plan anytime?',
                'answer' => 'Yes, there are no long-term lock-in contracts. You can upgrade, downgrade, or cancel your subscription anytime directly from your Super Admin / SaaS billing portal.'
            ]
        ];
    }

    /**
     * Retrieve and decode dynamic Terms & Conditions sections safely
     */
    public function getTermsSections(string $appName = '', string $contactEmail = ''): array
    {
        $appName = $appName ?: $this->settingsService->get('app_name', config('app.name', 'FST Salon & Barber'));
        $contactEmail = $contactEmail ?: $this->settingsService->get('contact_email', 'info@fsqatar.com');

        $sections = [];
        $raw = $this->settingsService->get('terms_sections', '');
        if (is_array($raw)) {
            $sections = $raw;
        } elseif (is_string($raw) && !empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && !empty($decoded)) {
                $sections = $decoded;
            }
        }

        if (empty($sections)) {
            $legacy = $this->settingsService->get('terms_content', '');
            if (!empty($legacy)) {
                return [
                    [
                        'title' => 'Terms & Conditions',
                        'content' => $legacy,
                    ]
                ];
            }

            $sections = [
                [
                    'title' => 'Acceptance & Master Subscription Agreement',
                    'content' => "This Master Subscription Agreement (\"Agreement\") is entered into by and between **{app_name}** (\"Platform\", \"we\", \"us\", or \"our\") and the salon, barbershop, beauty spa, or enterprise organization (\"Subscriber\", \"Salon Partner\", or \"You\") subscribing to or utilizing our software-as-a-service application.\n\nBy registering an account, beginning a 14-day free trial, or accessing our cloud booking and POS platform, you acknowledge that you have read, understood, and agree to be legally bound by this Agreement."
                ],
                [
                    'title' => 'Salon Partner Accounts & Multi-Branch Access',
                    'content' => "As a Salon Partner, you are granted a non-exclusive, worldwide, revocable license to access and use {app_name} in accordance with your chosen subscription tier. You are solely responsible for:\n\n- Maintaining the confidentiality of your administrative credentials and employee login tokens.\n- Assigning appropriate role-based permissions (Super Admin, Salon Owner, Stylist, Front-Desk Cashier) to authorized team members.\n- Ensuring that staff PINs and trusted device authentications are promptly revoked when an employee departs."
                ],
                [
                    'title' => 'Online Booking Engine & Tenant Isolation',
                    'content' => "{app_name} operates on an enterprise multi-tenant cloud architecture. Each Salon Partner maintains a strictly isolated tenant database partition containing its own client profiles, appointments, services, color formulations, and inventory records.\n\n> **Individual Salon Booking Disclaimers:** While our platform provides the online reservation infrastructure, each individual Salon Partner establishes its own internal service policies, appointment cancellation rules, deposit requirements, and hygiene standards for its guest clients. {app_name} is not liable for disputes arising between individual salons and their end customers."
                ],
                [
                    'title' => 'Subscription Plans, Billing, Invoicing & Free Trial',
                    'content' => "**14-Day Free Trial:** Eligible new Salon Partners may access the full platform without upfront billing for fourteen (14) calendar days. No automated charges will occur until you confirm a paid subscription tier.\n\n**Billing Cycle & Renewals:** Subscriptions are billed in advance on a recurring monthly or annual basis. Invoices and VAT tax receipts are generated automatically inside your SaaS Admin portal. You may upgrade, downgrade, or cancel your subscription plan at any time through your dashboard settings."
                ],
                [
                    'title' => 'Salon Business & Customer Data Ownership',
                    'content' => "**You Own Your Data:** You retain complete and exclusive ownership of all customer contacts, booking histories, sales reports, stylist commission logs, and chemical notes entered into your account. {app_name} will never sell, lease, or monetize your salon records to any third party.\n\n**Data Portability:** You may export your full client database, inventory catalogs, and financial transaction records to standard CSV or Excel spreadsheets at any time directly from your administrative panel."
                ],
                [
                    'title' => 'Service Level Agreement (SLA) & 99.9% Cloud Availability',
                    'content' => "We engineer {app_name} for mission-critical reliability with a **99.9% uptime service level commitment**. Our cloud infrastructure features redundant database clustering, automated encrypted daily backups, and real-time failover routing.\n\nScheduled system updates and maintenance windows will be communicated via your dashboard with at least 48 hours advance notice and performed during off-peak business hours."
                ],
                [
                    'title' => 'Acceptable Use, Security & Account Termination',
                    'content' => "You agree not to reverse-engineer, decompile, or launch automated scraping against {app_name}. Both parties reserve the right to terminate this Agreement at any time. Upon account termination, your data remains accessible for export for thirty (30) days, after which it will be permanently purged in accordance with our strict data retention protocol."
                ],
            ];
        }

        foreach ($sections as &$sec) {
            $sec['content'] = str_replace(
                ['{app_name}', '{contact_email}'],
                [$appName, $contactEmail],
                $sec['content'] ?? ''
            );
        }

        return $sections;
    }

    /**
     * Retrieve and decode dynamic Privacy Policy sections safely
     */
    public function getPrivacySections(string $appName = '', string $contactEmail = ''): array
    {
        $appName = $appName ?: $this->settingsService->get('app_name', config('app.name', 'FST Salon & Barber'));
        $contactEmail = $contactEmail ?: $this->settingsService->get('contact_email', 'info@fsqatar.com');
        $contactUrl = route('contact');

        $sections = [];
        $raw = $this->settingsService->get('privacy_sections', '');
        if (is_array($raw)) {
            $sections = $raw;
        } elseif (is_string($raw) && !empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && !empty($decoded)) {
                $sections = $decoded;
            }
        }

        if (empty($sections)) {
            $legacy = $this->settingsService->get('privacy_content', '');
            if (!empty($legacy)) {
                return [
                    [
                        'title' => 'Privacy Policy',
                        'content' => $legacy,
                    ]
                ];
            }

            $sections = [
                [
                    'title' => 'Platform Roles: Data Controller vs. Data Processor',
                    'content' => "At **{app_name}**, we value the trust placed in us by salon owners, barbers, and their guest clientele. Under global data protection frameworks (including GDPR, CCPA, and regional digital privacy statutes):\n\n- **When We Act as Data Controller:** We are the Controller for account credentials, subscription billing records, and system telemetry collected from our direct Salon Partners and website visitors.\n- **When We Act as Data Processor:** For client appointments, customer contact cards, service histories, and treatment formulations entered into a salon's private database, the individual Salon Partner is the Data Controller, and {app_name} acts strictly as the Data Processor processing data on their instructions."
                ],
                [
                    'title' => 'Information We Collect',
                    'content' => "To provide an efficient and high-performance salon operating system, we process the following categories of information:\n\n- **Account & Salon Profile Data:** Salon name, business address, contact telephone, email address, owner tax identification, and staff member roster profiles.\n- **Transactional POS & Appointment Records:** Client booking times, services rendered, stylist commissions, payment timestamps, and inventory movements.\n- **Technical & Session Telemetry:** IP addresses, browser user-agent, operating system version, and trusted device tokens used for POS security verification."
                ],
                [
                    'title' => 'Cloud Infrastructure & 256-Bit Bank Grade Encryption',
                    'content' => "Security is embedded into every architectural layer of {app_name}:\n\n- **Encryption in Transit:** All web traffic and API calls are enforced over modern TLS 1.3 with 256-bit encryption.\n- **Encryption at Rest:** Database clusters and automated daily backups are encrypted using AES-256 standards.\n- **Strict Tenant Isolation:** Every salon operates within a sandboxed tenant boundary, preventing unauthorized cross-salon data exposure."
                ],
                [
                    'title' => 'Anti-Monetization Commitment: We Never Sell Your Data',
                    'content' => "We hold a firm stance on data integrity: **we will never sell, rent, license, or monetize your salon's client lists, booking records, or revenue data to third-party data brokers or advertisers**. Your customer relationships remain 100% proprietary to your business."
                ],
                [
                    'title' => 'Integrated Payment Processing & PCI-DSS Compliance',
                    'content' => "Credit card transactions, online reservation deposits, and subscription renewals are handled exclusively through certified PCI-DSS Level 1 payment service providers (e.g. Stripe, PayPal, or authorized bank gateways).\n\nFull primary credit card numbers (PAN) and CVV security codes are never stored on our application servers. Payment tokens and masked card representations are utilized for recurring SaaS billing and POS receipts."
                ],
                [
                    'title' => 'GDPR & CCPA Rights: Access, Correction & Data Export',
                    'content' => "You retain full autonomy over your personal and salon data:\n\n- **Right to Export:** Download all client lists, booking logs, and invoices in standard CSV/Excel format directly from your panel.\n- **Right to Rectification:** Update or correct salon profile details, stylist credentials, and branch settings at any time.\n- **Right to Erasure (\"Right to Be Forgotten\"):** Upon formal cancellation or account closure request, your data will be permanently wiped from our live database clusters within thirty (30) days."
                ],
                [
                    'title' => 'Contacting the SaaS Security & Data Privacy Officer',
                    'content' => "For any questions, data processing addendum (DPA) requests, or privacy concerns, please contact our dedicated compliance desk through our [Support & Contact Desk]({contact_url}) or email directly at **{contact_email}**."
                ],
            ];
        }

        foreach ($sections as &$sec) {
            $sec['content'] = str_replace(
                ['{app_name}', '{contact_url}', '{contact_email}'],
                [$appName, $contactUrl, $contactEmail],
                $sec['content'] ?? ''
            );
        }

        return $sections;
    }

    /**
     * Retrieve and decode dynamic Reviews / Testimonials safely
     */
    public function getReviewsItems(): array
    {
        $raw = $this->settingsService->get('reviews_items', '');
        if (is_array($raw)) {
            return $raw;
        }
        if (is_string($raw) && !empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }

        return [
            [
                'name' => 'David Kingston',
                'role' => 'Owner, Kingston Barbershop Lounge',
                'rating' => 5,
                'text' => 'Switching to this platform cut our no-shows from 20% down to under 2%. The automated SMS reminders and quick POS checkout saved my reception team 15 hours every week.',
                'avatar' => 'DK',
            ],
            [
                'name' => 'Elena Moretti',
                'role' => 'Founder, Haute Beauty & Hair Spa',
                'rating' => 5,
                'text' => 'The color formula notes and staff commission calculations are extraordinary. Our stylists love seeing their daily tips and commissions in real time on their own phones.',
                'avatar' => 'EM',
            ],
            [
                'name' => 'Rashid Al-Hassan',
                'role' => 'Managing Partner, The Gentlemen Club Chain',
                'rating' => 5,
                'text' => 'We expanded from 2 branches to 5 branches within 18 months because this software made inventory transfers and centralized reporting completely effortless.',
                'avatar' => 'RH',
            ],
        ];
    }

    /**
     * Safely format legal policy content supporting headings, bold text, links, and bullet lists.
     * Prevents XSS by escaping user input before formatting markup.
     */
    public static function formatLegalContent(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        $blocks = preg_split('/\r\n\r\n|\n\n/', trim($content));
        $output = [];
        $sectionIndex = 1;

        foreach ($blocks as $block) {
            $block = trim($block);
            if ($block === '') continue;

            // Check if block is a heading (## Heading)
            if (preg_match('/^##\s+(.+)$/m', $block, $m)) {
                $title = e(trim($m[1]));
                $output[] = '<div class="pub-legal-section" id="section-' . $sectionIndex . '">';
                $output[] = '  <h2 class="pub-legal-section-title"><span class="pub-legal-num">' . $sectionIndex . '</span>' . $title . '</h2>';
                $output[] = '</div>';
                $sectionIndex++;
                continue;
            }

            // Check if block is a sub-heading (### Subheading)
            if (preg_match('/^###\s+(.+)$/m', $block, $m)) {
                $title = e(trim($m[1]));
                $output[] = '<h3 class="pub-legal-subheading" style="font-size: 1.15rem; font-weight: 700; color: #0b0f17; margin: 1.5rem 0 0.5rem;">' . $title . '</h3>';
                continue;
            }

            // Check if block is a callout blockquote (> text)
            if (preg_match('/^>\s+(.+)$/s', $block, $m)) {
                $text = self::formatInline(e(trim($m[1])));
                $output[] = '<blockquote class="pub-legal-callout" style="border-left: 3px solid #c5a880; background: #fbf7f2; padding: 1rem 1.25rem; margin: 1.25rem 0; border-radius: 0 0.5rem 0.5rem 0; color: #78350f; font-size: 0.95rem;">' . $text . '</blockquote>';
                continue;
            }

            // Check if block is a bullet list (- item)
            if (preg_match('/^-\s+/m', $block)) {
                $lines = preg_split('/\r\n|\n/', $block);
                $listItems = [];
                foreach ($lines as $line) {
                    if (preg_match('/^-\s+(.+)$/', trim($line), $itemMatch)) {
                        $listItems[] = '<li style="margin-bottom: 0.5rem;">' . self::formatInline(e(trim($itemMatch[1]))) . '</li>';
                    }
                }
                if (!empty($listItems)) {
                    $output[] = '<ul class="pub-legal-list" style="padding-left: 1.5rem; margin: 1rem 0 1.5rem; color: #334155; line-height: 1.75;">' . implode('', $listItems) . '</ul>';
                    continue;
                }
            }

            // Normal paragraph
            $formatted = self::formatInline(e($block));
            $formatted = nl2br($formatted);
            $output[] = '<p class="pub-legal-text" style="color: #334155; line-height: 1.85; font-size: 1rem; margin-bottom: 1.25rem;">' . $formatted . '</p>';
        }

        return implode("\n", $output);
    }

    /**
     * Parse inline markdown tokens: **bold** and [label](url)
     */
    private static function formatInline(string $text): string
    {
        // Bold: **text**
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);

        // Links: [text](https://...)
        $text = preg_replace_callback('/\[(.*?)\]\((https?:\/\/[^\s\)]+)\)/', function ($matches) {
            $label = $matches[1];
            $url = filter_var($matches[2], FILTER_SANITIZE_URL);
            return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" style="color: #926b38; text-decoration: underline; font-weight: 600;">' . $label . '</a>';
        }, $text);

        return $text;
    }
}
