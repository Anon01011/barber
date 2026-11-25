<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $cms['privacyPageTitle'] ?? 'Privacy Policy' }} - {{ $cms['appName'] ?? $appName }}</title>
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
        <div class="pub-container pub-legal-container">
            <div class="pub-inner-hero-content">
                <span class="pub-section-tag">Data Protection &amp; Security</span>
                <h1 class="pub-inner-hero-title">{{ $cms['privacyPageTitle'] ?? 'Privacy Policy' }}</h1>
                <p class="pub-inner-hero-desc">
                    {{ $cms['privacyPageSubtitle'] ?? 'Your privacy and salon client data security are our highest priority. Learn how our cloud platform protects your data.' }}
                </p>
                <p style="font-size: 0.8125rem; color: #94a3b8; margin-top: 1rem; font-weight: 600;">
                    <i class="fas fa-calendar-alt me-1"></i> Last Updated: {{ $cms['privacyLastUpdated'] ?? date('F Y') }}
                </p>
            </div>
        </div>
    </section>

    <!-- ==================== DOCUMENT BODY ==================== -->
    <section style="padding: 4rem 0 6rem; background: #f8fafc;">
        <div class="pub-container pub-legal-container">
            <div class="pub-legal-grid">

                <!-- Left Sidebar: Sticky Table of Contents & Compliance Summary -->
                <aside class="pub-legal-sidebar">
                    <div class="pub-toc-box">
                        <div class="pub-toc-title">
                            <i class="fas fa-shield-alt" style="color: #c5a880;"></i>
                            <span>Privacy Sections</span>
                        </div>
                        <nav>
                            <ul class="pub-toc-list">
                                @forelse($cms['privacySections'] ?? [] as $index => $section)
                                    <li>
                                        <a href="#section-{{ $index + 1 }}" class="pub-toc-link">
                                            {{ $index + 1 }}. {{ $section['title'] ?? ('Section ' . ($index + 1)) }}
                                        </a>
                                    </li>
                                @empty
                                    <li><a href="#section-1" class="pub-toc-link">Privacy Policy</a></li>
                                @endforelse
                            </ul>
                        </nav>
                    </div>

                    <div class="pub-legal-info-card">
                        <div class="pub-legal-info-tag">Privacy Officer</div>
                        <h4 class="pub-legal-info-title">Data Protection &amp; DPO</h4>
                        <p class="pub-legal-info-desc">
                            Request full data exports, submit GDPR deletion requests, or inspect compliance audits anytime.
                        </p>
                        <a href="{{ route('contact') }}" class="pub-btn-gold" style="width: 100%; text-align: center; justify-content: center; padding: 0.75rem 1rem; font-size: 0.8125rem;">
                            <span>Contact Privacy Officer</span>
                            <i class="fas fa-user-shield" style="font-size: 0.75rem;"></i>
                        </a>
                        <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.8rem;">
                            <a href="{{ route('terms') }}" style="color: #c5a880; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                                <span>Read Platform Terms</span>
                                <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i>
                            </a>
                        </div>
                    </div>
                </aside>

                <!-- Right Reading Pane: Formatted Document -->
                <main class="pub-legal-reading-pane">
                    @forelse($cms['privacySections'] ?? [] as $index => $section)
                        <div class="pub-legal-section" id="section-{{ $index + 1 }}">
                            <h2 class="pub-legal-section-title">
                                <span class="pub-legal-num">{{ $index + 1 }}</span>
                                {{ $section['title'] ?? ('Section ' . ($index + 1)) }}
                            </h2>
                            <div class="pub-legal-body">
                                {!! \App\Http\Controllers\LandingPageController::formatLegalContent($section['content'] ?? '') !!}
                            </div>
                        </div>
                    @empty
                        @if(!empty($cms['privacyFormatted']))
                            @if(!str_contains($cms['privacyFormatted'], 'id="section-1"'))
                                <div id="section-1">
                                    {!! $cms['privacyFormatted'] !!}
                                </div>
                            @else
                                {!! $cms['privacyFormatted'] !!}
                            @endif
                        @elseif(!empty($cms['privacyContent']))
                            <div id="section-1" style="color: #334155; line-height: 1.85; font-size: 1rem;">
                                {!! nl2br(e($cms['privacyContent'])) !!}
                            </div>
                        @else
                            <div class="alert alert-info" id="section-1">
                                Privacy Policy is currently being updated.
                            </div>
                        @endif
                    @endforelse
                </main>

            </div>
        </div>
    </section>

    @include('partials.saas-footer')

</body>

</html>