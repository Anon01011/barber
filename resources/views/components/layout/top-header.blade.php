@props(['salonSlug' => null, 'portalContext' => null])
@php
    $headerUser = auth()->user();
    if (!$portalContext) {
        if (request()->routeIs('customer.*')) {
            $portalContext = 'customer';
        } elseif (request()->routeIs('employee.*')) {
            $portalContext = 'employee';
        } elseif ($headerUser && $headerUser->hasAnyRole(['super_admin', 'salon_admin', 'manager'])) {
            $portalContext = 'admin';
        } elseif ($headerUser && $headerUser->hasRole('customer') && !$headerUser->hasRole('employee')) {
            $portalContext = 'customer';
        } elseif ($headerUser && $headerUser->hasRole('customer') && $headerUser->customer()->exists() && !$headerUser->employee()->exists()) {
            $portalContext = 'customer';
        } elseif ($headerUser && $headerUser->hasRole('employee')) {
            $portalContext = 'employee';
        } elseif ($headerUser && $headerUser->hasRole('customer')) {
            $portalContext = 'customer';
        } else {
            $portalContext = 'admin';
        }
    }
    if (!$salonSlug) {
        $salonSlug = request()->route('salon_slug') 
            ?? optional(auth()->user()?->salon)->slug 
            ?? (auth()->user()?->salon_id ? optional(\App\Models\Salon::find(auth()->user()->salon_id))->slug : null)
            ?? (app()->bound('current_salon') ? optional(app('current_salon'))->slug : null)
            ?? session('current_salon_slug');
    }
@endphp
    {{-- ═══════════════════════════════════════════════════════════════════
         SECURITY: Impersonation Banner
         Shown ABOVE the header when Super Admin is impersonating a salon owner.
         Provides mandatory "Return to Platform Admin" exit mechanism.
         ═══════════════════════════════════════════════════════════════════ --}}
    @if(session()->has('impersonator_id'))
        @php
            $impersonatedSalonName = session('impersonated_salon_name', 'a Salon');
            $impersonatorName      = session('impersonator_name', 'Super Admin');
        @endphp
        <div id="impersonationBanner" class="impersonation-banner-top">
            <div class="impersonation-banner-inner">
                <div class="impersonation-left">
                    <span class="impersonation-badge">
                        <i class="fas fa-shield-alt"></i> Impersonation Mode Active
                    </span>
                    <span class="impersonation-text">
                        Acting as owner of <strong>{{ e($impersonatedSalonName) }}</strong>
                        <span class="impersonation-audit-note">&bull; Logged as <em>{{ e($impersonatorName) }}</em></span>
                    </span>
                </div>
                <div class="impersonation-right">
                    <form method="POST" action="{{ route('admin.salons.stop-impersonate') }}" class="m-0 p-0">
                        @csrf
                        <button type="submit" id="stopImpersonateBtn" class="btn-return-admin">
                            <i class="fas fa-arrow-left"></i>
                            <span>Return to Platform Admin</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="header" id="header">
        <div class="header-content">
            <div class="d-flex align-items-center">
                <button type="button" class="toggle-sidebar" id="toggleSidebar" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                @auth
                    @if(optional(auth()->user()->salon)->hasBranchModule() && session('current_branch_name') && session('current_branch_name') !== 'All Branches')
                        <div class="ms-3">
                            <span
                                class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                {{ session('current_branch_name') }}
                            </span>
                        </div>
                    @endif
                @endauth
            </div>

            <div class="d-flex align-items-center">
                <!-- Language Switcher -->
                @php
                    $showLangToggle = true; // Default to showing for guests/super admins
                    if (auth()->check() && optional(auth()->user())->salon) {
                        $settingsService = app(\App\Services\SettingsService::class);
                        // Get setting with default 1 (true)
                        // Ensure we handle string "1"/"0" correctly just in case
                        $val = $settingsService->get('enable_language_toggle', 1);
                        $showLangToggle = filter_var($val, FILTER_VALIDATE_BOOLEAN) || $val === 1 || $val === '1';
                    }
                @endphp

                @if($showLangToggle)
                    <div class="me-3">
                        <label class="lang-toggle-pretty">
                            <input type="checkbox" id="languageToggle">
                            <span class="bg-slider"></span>
                            <div class="labels notranslate" translate="no">
                                <span class="label-en">EN</span>
                                <span class="label-ar">AR</span>
                            </div>
                        </label>
                    </div>
                @endif

                <div id="google_translate_element" style="display:none;"></div>

                <script type="text/javascript">
                    function googleTranslateElementInit() {
                        new google.translate.TranslateElement({
                            pageLanguage: 'en',
                            includedLanguages: 'en,ar',
                            layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                            autoDisplay: false
                        }, 'google_translate_element');
                    }
                </script>
                <script type="text/javascript"
                    src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

                <script>
                    var isToggleEnabled = @json($showLangToggle);

                    function changeLanguage(lang) {
                        // 1. Store preference in localStorage (Persistent)
                        localStorage.setItem('app_language', lang);

                        // 2. Cookie Management - Nuclear Option
                        var domain = window.location.hostname;
                        var parts = domain.split('.');

                        // Clear cookies for current domain, and all parent domains
                        // e.g. salon.fsterp.com, fsterp.com
                        var d = domain;
                        while (parts.length >= 2) {
                            var d = parts.join('.');
                            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + d;
                            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=." + d;
                            parts.shift();
                        }

                        // 3. Set new cookie
                        var cookieValue = '/en/' + lang;

                        // Set strictly on the current domain/host to ensure it takes precedence
                        document.cookie = "googtrans=" + cookieValue + "; path=/";
                        document.cookie = "googtrans=" + cookieValue + "; path=/; domain=" + domain;

                        // 4. Reload
                        location.reload();
                    }

                    document.addEventListener('DOMContentLoaded', function () {

                        // Safety: Check if we are in a reload loop
                        function isLooping() {
                            var lastFix = sessionStorage.getItem('lang_fix_timestamp');
                            if (lastFix) {
                                var diff = Date.now() - parseInt(lastFix);
                                // If we tried to fix less than 3 seconds ago, assume loop
                                if (diff < 3000) {

                                    return true;
                                }
                            }
                            return false;
                        }

                        function autoFixLanguage(targetLang) {
                            if (isLooping()) return;

                            sessionStorage.setItem('lang_fix_timestamp', Date.now());

                            changeLanguage(targetLang);
                        }

                        // -- FORCE ENGLISH IF TOGGLE DISABLED --
                        if (!isToggleEnabled) {
                            var currentStored = localStorage.getItem('app_language');
                            // If user is currently stuck in Arabic, force revert to English
                            if (currentStored === 'ar') {
                                autoFixLanguage('en');
                                return;
                            }
                        }

                        // -- PERSISTENCE CHECK --

                        // 1. Get stored preference
                        var storedLang = localStorage.getItem('app_language');

                        // 2. If we have a stored preference, enforce it
                        if (storedLang) {
                            var currentCookie = document.cookie.match('(^|;)\\s*googtrans\\s*=\\s*([^;]+)');
                            var cookieValue = currentCookie ? currentCookie.pop() : '';

                            // If cookie is missing or doesn't match our stored preference
                            var expectedCookieEnd = '/' + storedLang;

                            // Relaxed check: just ensure the cookie contains the target language
                            // This helps if Google modifies the cookie format slightly (e.g. /auto/ar)
                            var cookieLang = cookieValue ? cookieValue.split('/').filter(Boolean).pop() : '';
                            var matches = cookieLang === storedLang || (cookieValue && cookieValue.endsWith(expectedCookieEnd));

                            if (!matches) {
                                // Double check: if we want 'en' and cookie is empty/missing, that's usually fine implies default
                                // But if stored is 'ar' and cookie is missing, we must fix
                                if (storedLang === 'ar' || (storedLang === 'en' && cookieValue.includes('/ar'))) {
                                    autoFixLanguage(storedLang);
                                    return;
                                }
                            }
                        }


                        // -- UI UPDATES --

                        // Determine current effective language
                        var lang = storedLang || 'en';

                        // Update Toggle Switch State (Only if it exists)
                        var toggle = document.getElementById('languageToggle');
                        if (toggle) {
                            toggle.checked = (lang === 'ar');

                            // Add event listener for change
                            toggle.addEventListener('change', function () {
                                if (this.checked) {
                                    changeLanguage('ar');
                                } else {
                                    changeLanguage('en');
                                }
                            });
                        }

                        // Apply RTL if Arabic
                        if (lang === 'ar') {
                            document.body.classList.add('rtl');
                            document.documentElement.setAttribute('dir', 'rtl');
                            document.documentElement.setAttribute('lang', 'ar');
                        }

                        // Force hide banner (JS backup for CSS)
                        function hideBanner() {
                            var frame = document.querySelector('.goog-te-banner-frame');
                            if (frame) {
                                frame.style.setProperty('display', 'none', 'important');
                                frame.remove(); // Nuke it
                            }
                            document.body.style.setProperty('top', '0px', 'important');
                            document.body.style.setProperty('position', 'static', 'important');
                        }

                        // 1. Initial Hide
                        hideBanner();
                        setTimeout(hideBanner, 0);
                        setTimeout(hideBanner, 500);

                        // 2. MutationObserver (Nuclear Option)
                        var observer = new MutationObserver(function (mutations) {
                            hideBanner();
                        });

                        observer.observe(document.body, {
                            attributes: true,
                            childList: true,
                            subtree: false // We only care about direct children (like the iframe) or body attributes
                        });

                        // 3. Interval check just in case
                        setInterval(hideBanner, 1000);
                    });
                </script>
                @auth
                        <!-- Subscription Expiration Warning -->
                        @if(!env('SINGLE_SALON_MODE', false) && auth()->user()->hasRole('salon_admin') && auth()->user()->salon && $salonSlug)
                            @php
                                $subscription = auth()->user()->salon->subscription;
                                $daysRemaining = $subscription ? $subscription->daysUntilExpiration() : null;
                                $showWarning = $daysRemaining !== null && $daysRemaining > 0 && $daysRemaining <= 30;
                            @endphp

                            @if($showWarning)
                                <div class="me-2 me-md-3">
                                    <a href="{{ route('admin.saas.subscription.index', ['salon_slug' => $salonSlug]) }}"
                                        class="text-decoration-none" title="Plan expires in {{ $daysRemaining }} {{ Str::plural('day', $daysRemaining) }}">
                                        <div class="alert alert-warning mb-0 py-1 py-md-2 px-2 px-md-3 d-flex align-items-center text-nowrap"
                                            style="font-size: 0.82rem; border-radius: 8px; white-space: nowrap;">
                                            <i class="fas fa-exclamation-triangle me-1 me-md-2 text-warning-emphasis"></i>
                                            <span class="fw-semibold">
                                                <span class="d-none d-md-inline">Plan expires in </span>
                                                <span class="badge bg-warning text-dark">{{ $daysRemaining }}<span class="d-none d-md-inline"> {{ Str::plural('day', $daysRemaining) }}</span><span class="d-inline d-md-none">d</span></span>
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endif


                        <!-- Notification Bell -->
                        @if(isset($system_notifications))
                            <div class="notification-dropdown dropdown me-2 me-md-3">
                                <a href="#" class="text-dark position-relative" id="notificationDropdown" aria-label="Notifications" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fas fa-bell fa-lg"></i>
                                    @if(count($system_notifications) > 0)
                                        <span id="notificationBadge"
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ count($system_notifications) }}
                                            <span class="visually-hidden">unread messages</span>
                                        </span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in border-0 shadow-lg"
                                    aria-labelledby="notificationDropdown"
                                    style="width: 320px; max-height: 400px; overflow-y: auto;">
                                    <li class="dropdown-header bg-white py-3 px-3 border-bottom">
                                        <h6 class="m-0 font-weight-bold text-primary">Notifications</h6>
                                    </li>
                                    @if(count($system_notifications) > 0)
                                        @foreach($system_notifications as $notification)
                                            <li>
                                                <a class="dropdown-item d-flex align-items-start px-3 py-2 border-bottom" href="#"
                                                    onclick='event.preventDefault(); showNotificationDetail(@json($notification));'>
                                                    <div class="me-2 flex-shrink-0">
                                                        <div class="rounded-circle bg-{{ $notification->type }} bg-opacity-10 text-{{ $notification->type }} d-flex align-items-center justify-content-center"
                                                            style="width: 32px; height: 32px;">
                                                            <i
                                                                class="fas fa-{{ $notification->type == 'info' ? 'info' : ($notification->type == 'success' ? 'check' : ($notification->type == 'warning' ? 'exclamation' : 'exclamation-triangle')) }} fa-sm"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="font-weight-bold text-dark text-wrap small"
                                                                style="line-height: 1.2;">{{ $notification->title }}</span>
                                                            <small class="text-muted"
                                                                style="font-size: 0.7rem;">{{ $notification->created_at->diffForHumans(null, true, true) }}</small>
                                                        </div>
                                                        <div class="small text-secondary text-truncate"
                                                            style="max-width: 200px; font-size: 0.75rem;">
                                                            {{ Str::limit(strip_tags($notification->message), 50) }}
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    @else
                                        <li>
                                            <div class="dropdown-item text-center small text-muted py-4">No new notifications</div>
                                        </li>
                                    @endif
                                    @if(count($system_notifications) > 0)
                                        <li class="dropdown-divider"></li>
                                        <li>
                                            <button class="dropdown-item text-center small text-primary fw-bold py-2"
                                                id="markAllReadBtn">
                                                <i class="fas fa-check-double me-1"></i> Mark All as Read
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif

                        @if(($portalContext === 'customer' || auth()->user()->hasRole('customer')) && \App\Helpers\ModuleHelper::loyaltyEnabled() && (optional(auth()->user()->salon)->hasLoyaltyProgram() || (method_exists(optional(auth()->user()->salon), 'isOnTrial') && optional(auth()->user()->salon)->isOnTrial())))
                            @php
                                $custPoints = auth()->user()->customer?->loyalty_points ?? 0;
                            @endphp
                            <div class="me-2 me-md-3">
                                <span class="badge rounded-pill d-inline-flex align-items-center gap-1 px-2 px-md-3 py-2 shadow-sm"
                                    style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: #fcd34d; border: 1px solid rgba(251, 191, 36, 0.35); font-size: 0.78rem;"
                                    title="Your Loyalty Points">
                                    <i class="fas fa-coins text-warning"></i>
                                    <span class="fw-bold">{{ number_format($custPoints) }}</span>
                                    <span class="d-none d-sm-inline text-white-50 ms-1" style="font-size: 0.7rem;">pts</span>
                                </span>
                            </div>
                        @endif

                        <div class="user-dropdown">
                            <button type="button" class="user-dropdown-toggle p-1 p-md-2 btn btn-link text-decoration-none border-0 text-dark" id="userDropdownToggle" aria-haspopup="true" aria-expanded="false">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random"
                                    alt="{{ auth()->user()->name }}" class="rounded-circle me-1 me-md-2"
                                    style="width: 32px; height: 32px; min-width: 32px;">
                                <span class="me-2 d-none d-md-inline text-truncate" style="max-width: 140px;">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down" style="font-size: 0.75rem;"></i>
                            </button>
                            <div class="user-dropdown-menu" id="userDropdownMenu">
                                @if(auth()->user()->hasRole('customer') && auth()->user()->hasAnyRole(['employee', 'salon_admin', 'manager', 'super_admin']))
                                    @php
                                        $custSwitchSlug = optional(auth()->user()->salon)->slug ?? request()->current_salon->slug ?? request()->segment(1) ?? 'demo-salon';
                                    @endphp
                                    @if($portalContext === 'customer')
                                        @if(auth()->user()->hasRole('employee'))
                                            <a href="{{ route('employee.dashboard', ['salon_slug' => $custSwitchSlug]) }}" class="user-dropdown-item text-primary fw-semibold">
                                                <i class="fas fa-briefcase"></i>
                                                Switch to Staff Area
                                            </a>
                                        @else
                                            <a href="{{ route('dashboard', ['salon_slug' => $custSwitchSlug]) }}" class="user-dropdown-item text-primary fw-semibold">
                                                <i class="fas fa-store"></i>
                                                Switch to Admin Area
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('customer.dashboard', ['salon_slug' => $custSwitchSlug]) }}" class="user-dropdown-item text-primary fw-semibold">
                                            <i class="fas fa-user-circle"></i>
                                            Switch to Customer Area
                                        </a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                                    <i class="fas fa-user"></i>
                                    Profile
                                </a>
                                @if(!env('SINGLE_SALON_MODE', false) && auth()->user()->hasRole('salon_admin'))
                                    <a href="{{ route('admin.saas.subscription.index', ['salon_slug' => $salonSlug]) }}"
                                        class="user-dropdown-item">
                                        <i class="fas fa-crown"></i>
                                        My Plan
                                    </a>
                                    <a href="{{ route('admin.saas.subscription.history', ['salon_slug' => $salonSlug]) }}"
                                        class="user-dropdown-item">
                                        <i class="fas fa-history"></i>
                                        Subscription History
                                    </a>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                                    <i class="fas fa-cog"></i>
                                    Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="user-dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </a>
                                <a href="#"
                                    onclick="event.preventDefault(); if(confirm('Log out and remove this device from trusted devices? You will need your password to log in next time.')) { document.getElementById('forget-device-input').value = '1'; document.getElementById('logout-form').submit(); }"
                                    class="user-dropdown-item text-muted" style="font-size: 11.5px; opacity: 0.85;">
                                    <i class="fas fa-ban"></i>
                                    Logout & Forget Device
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                    <input type="hidden" name="forget_device" id="forget-device-input" value="0">
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                <div class="guest-nav">
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary me-2">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Register</a>
                </div>
            @endauth
        </div>
    </div>

