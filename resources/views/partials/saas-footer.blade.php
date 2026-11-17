<!-- Footer Section -->
<footer class="pub-footer">
    <div class="pub-container">
        <div class="pub-footer-grid">
            <!-- Brand & Philosophy Column -->
            @php
                $footerLogo = !empty($appLogo) ? $appLogo : app(\App\Services\SettingsService::class)->getLogoUrl();
            @endphp
            <div>
                <a href="{{ route('home') }}" style="display: inline-flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; text-decoration: none;">
                    @if(!empty($footerLogo))
                        <div style="background: rgba(255, 255, 255, 0.08); padding: 0.35rem 0.75rem; border-radius: 0.5rem; display: inline-flex; align-items: center; border: 1px solid rgba(255, 255, 255, 0.1);">
                            <img src="{{ $footerLogo }}" alt="{{ $appName }}" style="height: 2.25rem; width: auto; object-fit: contain;">
                        </div>
                    @else
                        <div class="pub-brand-badge">
                            {{ substr($appName ?? 'FS', 0, 1) }}
                        </div>
                        <span class="pub-footer-brand-title" style="margin-bottom: 0;">{{ $appName }}</span>
                    @endif
                </a>
                <p class="pub-footer-brand-desc">
                    The premier destination bridging traditional master barbering with contemporary luxury salon care. Precision fades, bespoke color, restorative treatments, and seamless digital booking.
                </p>

                <!-- Working Hours Badge -->
                @if(!empty($cms['contactHours']))
                    <div style="display: inline-flex; align-items: center; gap: 0.65rem; padding: 0.45rem 1rem; border-radius: 9999px; background: #131926; border: 1px solid #1f293d; font-size: 0.75rem; color: #cbd5e1; margin-bottom: 1.25rem;">
                        <span style="width: 0.5rem; height: 0.5rem; border-radius: 50%; background: #10b981;"></span>
                        <span style="font-weight: 700; color: #94a3b8;">Hours:</span>
                        <span style="color: #ffffff; font-weight: 600;">{{ $cms['contactHours'] }}</span>
                    </div>
                @endif

                <!-- Social Links -->
                <div class="pub-social-row">
                    @if(!empty($cms['socialFacebook']))
                        <a href="{{ $cms['socialFacebook'] }}" target="_blank" rel="noopener noreferrer" class="pub-social-btn" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif
                    @if(!empty($cms['socialInstagram']))
                        <a href="{{ $cms['socialInstagram'] }}" target="_blank" rel="noopener noreferrer" class="pub-social-btn" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    @endif
                    @if(!empty($cms['socialTwitter']))
                        <a href="{{ $cms['socialTwitter'] }}" target="_blank" rel="noopener noreferrer" class="pub-social-btn" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    @endif
                    @if(!empty($cms['socialLinkedin']))
                        <a href="{{ $cms['socialLinkedin'] }}" target="_blank" rel="noopener noreferrer" class="pub-social-btn" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    @endif
                    @if(!empty($cms['socialYoutube']))
                        <a href="{{ $cms['socialYoutube'] }}" target="_blank" rel="noopener noreferrer" class="pub-social-btn" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Platform Links -->
            <div>
                <h4 class="pub-footer-col-title">Platform</h4>
                <ul class="pub-footer-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('home') }}#features">Platform Capabilities</a></li>
                    <li><a href="{{ route('home') }}#salon-barber">Barbershop &amp; Salon Editions</a></li>
                    <li><a href="{{ route('home') }}#plans-section">SaaS Pricing Plans</a></li>
                    <li><a href="{{ route('home') }}#faq-section">Frequently Asked Questions</a></li>
                    <li><a href="{{ route('saas.register') }}">Start 14-Day Free Trial</a></li>
                </ul>
            </div>

            <!-- Company & Legal Links -->
            <div>
                <h4 class="pub-footer-col-title">Company &amp; Legal</h4>
                <ul class="pub-footer-links">
                    <li><a href="{{ route('about') }}">About Our Platform</a></li>
                    <li><a href="{{ route('contact') }}">Contact Sales &amp; Support</a></li>
                    <li><a href="{{ route('terms') }}">SaaS Terms &amp; Conditions</a></li>
                    <li><a href="{{ route('privacy') }}">Privacy &amp; Data Security</a></li>
                    <li><a href="{{ route('login') }}">Salon &amp; Staff Login</a></li>
                </ul>
            </div>

            <!-- Direct Concierge / Reach Us -->
            <div>
                <h4 class="pub-footer-col-title">Concierge</h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @if(!empty($cms['contactPhone']))
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $cms['contactPhone']) }}" style="display: flex; align-items: flex-start; gap: 0.75rem; color: #cbd5e1; text-decoration: none;">
                            <i class="fas fa-phone-alt" style="color: #c5a880; margin-top: 0.25rem;"></i>
                            <div>
                                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; font-weight: 700;">Direct Line</div>
                                <div style="font-weight: 700; color: #ffffff; font-size: 0.95rem;">{{ $cms['contactPhone'] }}</div>
                            </div>
                        </a>
                    @endif

                    @if(!empty($cms['contactEmail']))
                        <a href="mailto:{{ $cms['contactEmail'] }}" style="display: flex; align-items: flex-start; gap: 0.75rem; color: #cbd5e1; text-decoration: none;">
                            <i class="fas fa-envelope" style="color: #c5a880; margin-top: 0.25rem;"></i>
                            <div>
                                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; font-weight: 700;">Reservations</div>
                                <div style="font-weight: 600; color: #ffffff; font-size: 0.9rem;">{{ $cms['contactEmail'] }}</div>
                            </div>
                        </a>
                    @endif

                    @if(!empty($cms['contactAddress']))
                        <div style="display: flex; align-items: flex-start; gap: 0.75rem; color: #cbd5e1;">
                            <i class="fas fa-map-marker-alt" style="color: #c5a880; margin-top: 0.25rem;"></i>
                            <div>
                                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; font-weight: 700;">Location</div>
                                <div style="font-size: 0.85rem; color: #94a3b8; line-height: 1.5;">{{ $cms['contactAddress'] }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="pub-footer-bottom">
            <div>
                &copy; {{ date('Y') }} {{ $cms['appName'] ?? $appName }}. All rights reserved.
            </div>
            <div class="pub-legal-links">
                <a href="{{ route('terms') }}">Terms</a>
                <span>&bull;</span>
                <a href="{{ route('privacy') }}">Privacy</a>
                <span>&bull;</span>
                <a href="{{ route('contact') }}">Support</a>
            </div>
        </div>
    </div>
</footer>
