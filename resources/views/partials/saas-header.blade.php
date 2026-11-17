<!-- Header Navigation -->
<header id="site-header">
    <div class="pub-container">
        <div class="pub-nav-inner">
            <!-- Brand Logo -->
            @php
                $headerLogo = !empty($appLogo) ? $appLogo : app(\App\Services\SettingsService::class)->getLogoUrl();
            @endphp
            <a href="{{ route('home') }}" class="pub-brand">
                @if(!empty($headerLogo))
                    <img src="{{ $headerLogo }}" alt="{{ $appName }}" class="pub-brand-logo">
                @else
                    <div class="pub-brand-badge">
                        {{ substr($appName ?? 'FS', 0, 1) }}
                    </div>
                    <div class="pub-brand-text">
                        <span class="pub-brand-name">{{ $appName }}</span>
                        <span class="pub-brand-tag">Salon & Barber Cloud</span>
                    </div>
                @endif
            </a>

            <!-- Desktop Nav Links -->
            <nav class="pub-desktop-nav">
                <a href="{{ route('home') }}" class="pub-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    Home
                </a>
                <a href="{{ route('home') }}#features" class="pub-nav-link">
                    Features
                </a>
                <a href="{{ route('home') }}#salon-barber" class="pub-nav-link">
                    For Barbers &amp; Salons
                </a>
                <a href="{{ route('home') }}#plans-section" class="pub-nav-link">
                    Pricing Plans
                </a>
                <a href="{{ route('about') }}" class="pub-nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                    About Us
                </a>
                <a href="{{ route('contact') }}" class="pub-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                    Contact
                </a>
            </nav>

            <!-- Right Action CTAs -->
            <div class="pub-header-actions">
                <a href="{{ route('login') }}" class="pub-btn-outline" style="padding: 0.55rem 1.15rem; font-size: 0.8125rem;">
                    <i class="fas fa-user-circle" style="font-size: 0.85rem; color: #64748b;"></i>
                    <span>Sign In</span>
                </a>
                <a href="{{ route('saas.register') }}" class="pub-btn-primary" style="padding: 0.6rem 1.35rem; font-size: 0.8125rem;">
                    <span>Register Salon</span>
                    <i class="fas fa-arrow-right" style="font-size: 0.75rem; color: #c5a880;"></i>
                </a>

                <!-- Mobile Hamburger Button -->
                <button type="button" id="mobile-menu-btn" class="pub-mobile-toggle" aria-label="Toggle Navigation">
                    <svg style="width: 1.35rem; height: 1.35rem; display: block;" id="menu-icon-bars" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg style="width: 1.35rem; height: 1.35rem; display: none;" id="menu-icon-close" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div id="mobile-menu">
        <div class="pub-mobile-drawer-body">
            <div class="pub-container">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <a href="{{ route('home') }}" style="padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; background: {{ request()->routeIs('home') ? '#f1f5f9' : '#ffffff' }}; border: 1px solid #e2e8f0; color: #0f172a;">
                        <i class="fas fa-home" style="color: #c5a880;"></i> Home
                    </a>
                    <a href="{{ route('home') }}#features" style="padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; background: #ffffff; border: 1px solid #e2e8f0; color: #0f172a;">
                        <i class="fas fa-bolt" style="color: #c5a880;"></i> Features
                    </a>
                    <a href="{{ route('home') }}#salon-barber" style="padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; background: #ffffff; border: 1px solid #e2e8f0; color: #0f172a;">
                        <i class="fas fa-cut" style="color: #c5a880;"></i> Barber &amp; Salon
                    </a>
                    <a href="{{ route('home') }}#plans-section" style="padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; background: #ffffff; border: 1px solid #e2e8f0; color: #0f172a;">
                        <i class="fas fa-tags" style="color: #c5a880;"></i> Pricing Plans
                    </a>
                    <a href="{{ route('about') }}" style="padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; background: {{ request()->routeIs('about') ? '#f1f5f9' : '#ffffff' }}; border: 1px solid #e2e8f0; color: #0f172a;">
                        <i class="fas fa-info-circle" style="color: #c5a880;"></i> About Us
                    </a>
                    <a href="{{ route('contact') }}" style="padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; background: {{ request()->routeIs('contact') ? '#f1f5f9' : '#ffffff' }}; border: 1px solid #e2e8f0; color: #0f172a;">
                        <i class="fas fa-envelope" style="color: #c5a880;"></i> Contact Us
                    </a>
                </div>

                @if(!empty($cms['contactPhone']))
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $cms['contactPhone']) }}"
                        style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1.25rem; border-radius: 0.5rem; background: #fdfbf7; border: 1px solid #e8dccb; color: #926b38; font-size: 0.8125rem; font-weight: 700; margin-bottom: 1rem; text-decoration: none;">
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-phone-alt"></i>
                            <span>Sales &amp; Support: {{ $cms['contactPhone'] }}</span>
                        </span>
                        <span style="text-decoration: underline;">Call Now</span>
                    </a>
                @endif

                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('login') }}" class="pub-btn-outline" style="flex: 1; justify-content: center;">
                        <i class="fas fa-user-circle"></i>
                        <span>Sign In</span>
                    </a>
                    <a href="{{ route('saas.register') }}" class="pub-btn-primary" style="flex: 1; justify-content: center;">
                        <span>Register Salon</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
