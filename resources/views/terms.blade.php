<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms & Conditions - {{ $cms['appName'] ?? $appName }}</title>
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
                <span class="pub-section-tag">Client Agreements</span>
                <h1 class="pub-inner-hero-title">Terms &amp; Conditions</h1>
                <p class="pub-inner-hero-desc">
                    Please review the policies governing appointments, etiquette, and digital bookings at {{ $cms['appName'] ?? $appName }}.
                </p>
                <p style="font-size: 0.8125rem; color: #94a3b8; margin-top: 1rem; font-weight: 600;">Last Updated: {{ date('F Y') }}</p>
            </div>
        </div>
    </section>

    <!-- ==================== DOCUMENT BODY ==================== -->
    <section style="padding: 4rem 0 6rem; background: #f8fafc;">
        <div class="pub-container" style="max-width: 54rem;">
            <div class="pub-legal-card">
                @if(!empty($cms['termsContent']))
                    <div style="color: #334155; line-height: 1.85; font-size: 1rem;">
                        {!! nl2br(e($cms['termsContent'])) !!}
                    </div>
                @else
                    <!-- Default Comprehensive Policy -->
                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">1</span>
                            Acceptance of Terms
                        </h2>
                        <p class="pub-legal-text">
                            By accessing our online booking portal, scheduling an appointment, or receiving services at <strong>{{ $cms['appName'] ?? $appName }}</strong>, you agree to comply with and be bound by these Terms and Conditions. If you do not agree to these terms, please refrain from booking or utilizing our services.
                        </p>
                    </div>

                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">2</span>
                            Appointments &amp; Punctuality
                        </h2>
                        <p class="pub-legal-text">
                            We value each client's dedicated time with their artisan. Please arrive 10 minutes prior to your scheduled reservation to complete your initial consultation. If you arrive more than 15 minutes past your appointment time, we will make every effort to accommodate you; however, your appointment may need to be abbreviated or rescheduled to honor subsequent bookings.
                        </p>
                    </div>

                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">3</span>
                            Cancellation &amp; Rescheduling Policy
                        </h2>
                        <p class="pub-legal-text">
                            We request a minimum of 24 hours advance notice for any appointment cancellations or rescheduling. Cancellations made with less than 24 hours notice, or failure to show for an appointment, may incur a cancellation fee or require upfront deposit upon future bookings.
                        </p>
                    </div>

                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">4</span>
                            Hygiene, Health &amp; Safety Standards
                        </h2>
                        <p class="pub-legal-text">
                            Our studio operates under strict medical-grade hygiene protocols. All scissors, clippers, and grooming implements undergo hospital-grade sterilization between clients. Straight razors utilize brand-new, single-use surgical steel blades disposed of immediately in sharps containers. Clients experiencing contagious skin or scalp conditions, open lesions, or fever are kindly asked to reschedule once fully recovered.
                        </p>
                    </div>

                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">5</span>
                            Pricing &amp; Payment
                        </h2>
                        <p class="pub-legal-text">
                            All published service prices are subject to change without prior notice. Extra length, high-density hair, or complex corrective color work may incur supplemental charges, which will be discussed transparently during your pre-service consultation. Payment is due in full upon completion of services. We accept major credit/debit cards, contactless mobile payments, and cash.
                        </p>
                    </div>

                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">6</span>
                            Satisfaction Guarantee &amp; Adjustments
                        </h2>
                        <p class="pub-legal-text">
                            We take immense pride in our craft. If you feel your haircut, color formulation, or beard trim requires fine adjustment, please notify our reception within 5 days of your visit. We will gladly schedule a complimentary adjustment with your original artisan to ensure your complete satisfaction.
                        </p>
                    </div>

                    <div class="pub-legal-section">
                        <h2 class="pub-legal-section-title">
                            <span class="pub-legal-num">7</span>
                            Personal Belongings &amp; Etiquette
                        </h2>
                        <p class="pub-legal-text">
                            {{ $cms['appName'] ?? $appName }} is not responsible for loss of or damage to personal items brought into the studio. We ask all guests to maintain a respectful, relaxing lounge atmosphere for all patrons by keeping phone conversations discreet.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @include('partials.saas-footer')

</body>

</html>
