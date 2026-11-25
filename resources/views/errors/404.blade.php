<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $settingsService = app(\App\Services\SettingsService::class);
        $appName = $settingsService->get('app_name', config('app.name', 'SalonPro'));
        $supportEmail = $settingsService->get('contact_email') 
                        ?: $settingsService->get('business_email') 
                        ?: config('mail.from.address', 'support@fsqatar.com');
        $supportPhone = $settingsService->get('contact_phone') 
                        ?: $settingsService->get('business_phone', '+974 66266581');
        $workingHours = $settingsService->get('contact_working_hours', 'Mon – Sun: 8:00 AM – 9:00 PM');
        $logoUrl = $settingsService->getLogoUrl();
        $faviconUrl = $settingsService->getFaviconUrl();
        $cleanPhone = preg_replace('/[^\d+]/', '', $supportPhone);
    @endphp
    <title>404 - Page Not Found | {{ $appName }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --bg-page: #f8fafc;
            --text-title: #0f172a;
            --text-body: #475569;
            --text-muted: #64748b;
            --gold-accent: #c5a880;
            --gold-dark: #a27b46;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-page);
            color: var(--text-title);
        }

        /* Clean Enterprise Full Page Stage (Zero Scroll) */
        .page-stage {
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem 2.5rem 1.25rem;
            background-color: #ffffff;
            overflow: hidden;
        }

        /* Top Header */
        .stage-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            flex-shrink: 0;
        }

        .stage-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-title);
        }

        .stage-logo {
            max-height: 36px;
            width: auto;
            object-fit: contain;
        }

        .stage-brand-fallback {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #0f172a;
            color: var(--gold-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .stage-brand-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-title);
        }

        .stage-badge {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--gold-dark);
            background: #fdf8f0;
            border: 1px solid #f0e2cd;
            padding: 0.35rem 0.85rem;
            border-radius: 6px;
        }

        /* Center Content Area */
        .stage-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            max-width: 680px;
            margin: 0 auto;
            padding: 1rem 0;
        }

        .icon-circle {
            width: 72px;
            height: 72px;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: var(--gold-dark);
            font-size: 1.85rem;
        }

        .code-title {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(4.5rem, 9vw, 6.5rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -2px;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .heading-title {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(1.4rem, 2.2vw, 1.85rem);
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .desc-text {
            color: var(--text-muted);
            font-size: 0.98rem;
            line-height: 1.6;
            margin-bottom: 1.75rem;
        }

        /* Buttons */
        .actions-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .btn-brand-primary {
            background: #0f172a;
            color: #ffffff !important;
            font-weight: 600;
            padding: 0.75rem 1.85rem;
            border-radius: 8px;
            border: 1px solid #0f172a;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }

        .btn-brand-primary:hover {
            background: #1e293b;
            border-color: #1e293b;
            color: #ffffff !important;
            transform: translateY(-1px);
        }

        .btn-brand-secondary {
            background: #ffffff;
            color: var(--text-title) !important;
            border: 1px solid var(--border-color);
            font-weight: 600;
            padding: 0.75rem 1.65rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }

        .btn-brand-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a !important;
            transform: translateY(-1px);
        }

        /* Nav Links */
        .quick-destinations {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            flex-wrap: wrap;
        }

        .quick-chip {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            color: var(--text-body);
            padding: 0.35rem 0.9rem;
            border-radius: 6px;
            font-size: 0.82rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .quick-chip:hover {
            background: #ffffff;
            border-color: var(--gold-accent);
            color: var(--gold-dark);
            transform: translateY(-1px);
        }

        .quick-chip i {
            color: var(--gold-dark);
            font-size: 0.75rem;
        }

        /* Bottom Support Dock */
        .stage-footer {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            flex-shrink: 0;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .footer-icon {
            width: 34px;
            height: 34px;
            border-radius: 6px;
            background: #f1f5f9;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .footer-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-title);
        }

        .footer-hours {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .footer-contacts {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .contact-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            color: var(--text-title);
            padding: 0.4rem 0.95rem;
            border-radius: 6px;
            font-size: 0.82rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .contact-pill:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .contact-pill i {
            color: var(--gold-dark);
        }

        @media (max-width: 768px) {
            html, body {
                height: auto;
                overflow-y: auto;
            }
            .page-stage {
                height: auto;
                min-height: 100vh;
                padding: 1.25rem 1rem;
                overflow-y: auto;
            }
            .stage-content {
                padding: 2.5rem 0;
            }
            .stage-footer {
                flex-direction: column;
                align-items: flex-start;
            }
            .footer-contacts {
                width: 100%;
            }
            .contact-pill {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <div class="page-stage">
        
        <!-- Header -->
        <header class="stage-header">
            <a href="{{ route('home') }}" class="stage-brand">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $appName }}" class="stage-logo">
                @else
                    <div class="stage-brand-fallback">
                        <i class="fas fa-scissors"></i>
                    </div>
                @endif
                <span class="stage-brand-title">{{ $appName }}</span>
            </a>

            <div class="stage-badge">
                <i class="fas fa-shield-alt me-1"></i>
                <span>Status 404</span>
            </div>
        </header>

        <!-- Main Content (Pure Open Canvas, Clean Enterprise Styling) -->
        <main class="stage-content">
            
            <div class="icon-circle">
                <i class="fas fa-compass"></i>
            </div>

            <div class="code-title">404</div>

            <h1 class="heading-title">Page Not Found</h1>
            <p class="desc-text">
                The page you requested could not be located. It may have been moved, renamed, or is currently unavailable.
            </p>

            <!-- Role-Aware Primary Action Routing -->
            @php
                $dashboardUrl = route('home.dashboard');
                $dashboardLabel = 'Return to Dashboard';
                $salonSlug = null;
                
                if (auth()->check()) {
                    $user = auth()->user();
                    $salonSlug = optional($user->salon)->slug ?? request()->segment(1);

                    if ($user->hasRole('super_admin')) {
                        $dashboardUrl = route('admin.super.dashboard');
                        $dashboardLabel = 'Return to Super Admin';
                    } elseif ($user->hasRole('customer')) {
                        $dashboardUrl = $salonSlug ? route('customer.dashboard', ['salon_slug' => $salonSlug]) : route('home.dashboard');
                        $dashboardLabel = 'Return to Client Portal';
                    } elseif ($user->hasRole('employee')) {
                        $dashboardUrl = $salonSlug ? route('employee.dashboard', ['salon_slug' => $salonSlug]) : route('home.dashboard');
                        $dashboardLabel = 'Return to Staff Workspace';
                    } elseif ($salonSlug) {
                        $dashboardUrl = route('dashboard', ['salon_slug' => $salonSlug]);
                        $dashboardLabel = 'Return to Salon Dashboard';
                    }
                } else {
                    $dashboardUrl = route('home');
                    $dashboardLabel = 'Return to Homepage';
                }
            @endphp

            <div class="actions-bar">
                <a href="{{ $dashboardUrl }}" class="btn-brand-primary">
                    <i class="fas fa-arrow-left"></i>
                    <span>{{ $dashboardLabel }}</span>
                </a>

                <button onclick="window.history.back()" class="btn-brand-secondary">
                    <span>Go Back</span>
                </button>
            </div>

            <!-- Fast Navigation Links -->
            <div class="quick-destinations">
                @if(auth()->check())
                    <a href="{{ $dashboardUrl }}" class="quick-chip">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    @if($salonSlug && auth()->user()->hasRole('customer'))
                        <a href="{{ route('customer.appointments.create', ['salon_slug' => $salonSlug]) }}" class="quick-chip">
                            <i class="fas fa-calendar-plus"></i> Book Appointment
                        </a>
                        <a href="{{ route('customer.appointments.index', ['salon_slug' => $salonSlug]) }}" class="quick-chip">
                            <i class="fas fa-clock-rotate-left"></i> My Visits
                        </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="quick-chip">
                        <i class="fas fa-user-gear"></i> Account Profile
                    </a>
                @else
                    <a href="{{ route('home') }}" class="quick-chip">
                        <i class="fas fa-globe"></i> Home
                    </a>
                    <a href="{{ route('login') }}" class="quick-chip">
                        <i class="fas fa-right-to-bracket"></i> Sign In
                    </a>
                    <a href="{{ route('register') }}" class="quick-chip">
                        <i class="fas fa-user-plus"></i> Create Account
                    </a>
                @endif
            </div>

        </main>

        <!-- Clean Bottom Support Bar -->
        <footer class="stage-footer">
            <div class="footer-left">
                <div class="footer-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <div class="footer-title">Need help? Contact platform support</div>
                    <div class="footer-hours">{{ $workingHours }}</div>
                </div>
            </div>

            <div class="footer-contacts">
                @if($supportEmail)
                    <a href="mailto:{{ $supportEmail }}" class="contact-pill" title="Send email to platform support">
                        <i class="fas fa-envelope"></i>
                        <span>{{ $supportEmail }}</span>
                    </a>
                @endif

                @if($supportPhone)
                    <a href="tel:{{ $cleanPhone }}" class="contact-pill" title="Call platform helpline">
                        <i class="fas fa-phone"></i>
                        <span>{{ $supportPhone }}</span>
                    </a>
                @endif
            </div>
        </footer>

    </div>

</body>
</html>
