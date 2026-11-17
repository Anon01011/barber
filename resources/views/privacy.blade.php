<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Policy - {{ $cms['appName'] ?? $appName }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ app(\App\Services\SettingsService::class)->getFaviconUrl() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Dedicated Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/public-pages.css') }}">

    <!-- Dedicated Scripts -->
    <script src="{{ asset('js/public-pages.js') }}" defer></script>
</head>

<body>

    @include('partials.saas-header')

    <!-- ==================== HEADER BANNER ==================== -->
    <section class="pub-inner-hero">
        <div class="pub-container">
            <div class="pub-inner-hero-content">
                <span class="pub-section-tag">Data Protection</span>
                <h1 class="pub-inner-hero-title">Privacy Policy</h1>
                <p class="pub-inner-hero-desc">
                    Your trust is our paramount commitment. Learn how {{ $cms['appName'] ?? $appName }} safeguards your personal information and appointment records.
                </p>
                <p style="font-size: 0.8125rem; color: #94a3b8; margin-top: 1rem; font-weight: 600;">Last Updated: {{ date('F Y') }}</p>
            </div>
        </div>
    </section>

    <!-- ==================== DOCUMENT BODY ==================== -->
    <section style="padding: 4rem 0 6rem; background: #f8fafc;">
        <div class="pub-container" style="max-width: 54rem;">
            <div class="pub-legal-card">
                @if(!empty($cms['privacyContent']))
                    <div style="color: #334155; line-height: 1.85; font-size: 1rem;">
                        {!! nl2br(e($cms['privacyContent'])) !!}
                    </div>
                @else
                    <!-- Default Comprehensive Policy -->
                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">1</span>
                            Information We Collect
                        </h2>
                        <p class="pub-legal-text">
                            When you schedule an appointment or register with <strong>{{ $cms['appName'] ?? $appName }}</strong>, we collect personal details necessary to deliver our services, including: your name, email address, mobile telephone number, booking history, preferred stylist/barber, and any relevant service preferences or chemical allergies you voluntarily disclose.
                        </p>
                    </div>

                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">2</span>
                            How We Use Your Information
                        </h2>
                        <p class="pub-legal-text">
                            We utilize your data strictly to manage appointment bookings, send automated confirmations and reminder alerts via SMS or email, process billing, address customer inquiries, and maintain your salon treatment formulation history for optimal ongoing results.
                        </p>
                    </div>

                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">3</span>
                            Payment Information Security
                        </h2>
                        <p class="pub-legal-text">
                            All electronic transactions are processed through encrypted, PCI-DSS compliant payment gateways. We do not store full credit card numbers, CVV codes, or sensitive banking credentials on our local servers.
                        </p>
                    </div>

                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">4</span>
                            SMS &amp; Email Communications
                        </h2>
                        <p class="pub-legal-text">
                            By booking an appointment, you consent to receive transactional notifications regarding your visit (such as booking confirmations, 24-hour reminders, or reschedule alerts). You may opt out of non-essential promotional communications at any time by contacting our concierge desk or clicking the unsubscribe link.
                        </p>
                    </div>

                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">5</span>
                            Data Sharing &amp; Third-Party Services
                        </h2>
                        <p class="pub-legal-text">
                            We never sell, rent, or trade your personal data to external advertisers. Data is shared solely with trusted technological infrastructure partners (such as cloud hosting providers, transactional SMS gateways, and secure payment processors) who are contractually bound to confidentiality and rigorous data protection standards.
                        </p>
                    </div>

                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">6</span>
                            Data Retention &amp; Security Controls
                        </h2>
                        <p class="pub-legal-text">
                            We implement industry-standard encryption protocols (TLS/SSL) and restrictive administrative access controls to prevent unauthorized data loss, alteration, or exposure. Your records are retained only as long as necessary to provide services and comply with applicable tax and legal obligations.
                        </p>
                    </div>

                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">7</span>
                            Your Privacy Rights &amp; Contact
                        </h2>
                        <p class="pub-legal-text">
                            You have the right to request access to the personal data we hold about you, request corrections to inaccuracies, or ask for the deletion of your account history where permitted by law. For any inquiries concerning this policy, please reach out to our privacy officer via our <a href="{{ route('contact') }}" style="color: #926b38; text-decoration: underline; font-weight: 700;">Contact Concierge</a>.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @include('partials.saas-footer')

</body>

</html>
