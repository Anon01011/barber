<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Access Restricted | {{ config('app.name', 'SalonPro') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --theme-primary: #0b0f17;
            --theme-surface: #111724;
            --theme-surface-card: rgba(17, 23, 36, 0.92);
            --theme-accent: #c5a880;
            --theme-accent-hover: #b38e5d;
            --theme-accent-light: rgba(197, 168, 128, 0.12);
            --theme-border: rgba(197, 168, 128, 0.22);
            --theme-text-muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: radial-gradient(circle at 50% 20%, #1a2232 0%, #0b0f17 75%, #05070a 100%);
            color: #f8fafc;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Luxury Glow Effects */
        .ambient-glow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: 1;
        }

        .glow-1 {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(197, 168, 128, 0.12) 0%, transparent 70%);
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            filter: blur(80px);
        }

        .glow-2 {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(179, 142, 93, 0.08) 0%, transparent 70%);
            bottom: -50px;
            right: 10%;
            filter: blur(90px);
        }

        /* Error Container */
        .error-wrapper {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
        }

        /* Luxury Glass Card */
        .luxury-card {
            background: var(--theme-surface-card);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--theme-border);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            max-width: 620px;
            width: 100%;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.65), 0 0 50px rgba(197, 168, 128, 0.08);
            animation: fadeInScale 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .luxury-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, var(--theme-accent) 50%, transparent 100%);
            opacity: 0.8;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Shield Icon */
        .icon-shield {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, rgba(197, 168, 128, 0.18) 0%, rgba(197, 168, 128, 0.05) 100%);
            border: 1px solid var(--theme-border);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3), inset 0 0 20px rgba(197, 168, 128, 0.15);
        }

        .icon-shield i {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #f5e6d3 0%, var(--theme-accent) 50%, var(--theme-accent-hover) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Error Code */
        .error-num {
            font-size: 4.5rem;
            font-weight: 800;
            letter-spacing: -2px;
            line-height: 1;
            margin-bottom: 0.75rem;
            background: linear-gradient(135deg, #ffffff 0%, var(--theme-accent) 60%, var(--theme-accent-hover) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .error-headline {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.75rem;
            letter-spacing: -0.3px;
        }

        .error-desc {
            color: var(--theme-text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Plan Badge */
        .plan-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--theme-accent-light);
            border: 1px solid var(--theme-border);
            color: var(--theme-accent);
            padding: 0.4rem 1.25rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        /* Luxury Buttons */
        .btn-luxury-gold {
            background: linear-gradient(135deg, var(--theme-accent) 0%, var(--theme-accent-hover) 100%);
            color: #0b0f17 !important;
            font-weight: 600;
            padding: 0.75rem 1.75rem;
            border-radius: 12px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.25s ease;
            box-shadow: 0 8px 24px rgba(197, 168, 128, 0.25);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-luxury-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(197, 168, 128, 0.35);
            filter: brightness(1.08);
            color: #05070a !important;
        }

        .btn-luxury-outline {
            background: rgba(255, 255, 255, 0.03);
            color: #f1f5f9 !important;
            border: 1px solid rgba(197, 168, 128, 0.35);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.25s ease;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-luxury-outline:hover {
            background: rgba(197, 168, 128, 0.1);
            border-color: var(--theme-accent);
            color: var(--theme-accent) !important;
            transform: translateY(-2px);
        }

        /* Features Section */
        .features-panel {
            margin-top: 2rem;
            padding-top: 1.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .features-heading {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.75px;
            color: var(--theme-accent);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .feature-chip {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .feature-chip i {
            color: #10b981;
            font-size: 0.75rem;
        }

        /* Help Info */
        .support-info {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #64748b;
        }

        .support-info a {
            color: var(--theme-accent);
            text-decoration: none;
            font-weight: 500;
        }

        .support-info a:hover {
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .luxury-card {
                padding: 2.25rem 1.5rem;
                border-radius: 18px;
            }

            .error-num {
                font-size: 3.5rem;
            }

            .error-headline {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <!-- Ambient Golden Glow -->
    <div class="ambient-glow">
        <div class="glow-1"></div>
        <div class="glow-2"></div>
    </div>

    <!-- Error Content -->
    <div class="error-wrapper">
        <div class="luxury-card text-center">
            <!-- Shield Lock Icon -->
            <div class="icon-shield">
                <i class="fas fa-shield-halved"></i>
            </div>

            <!-- Error Code -->
            <div class="error-num">403</div>

            <!-- Title -->
            <h1 class="error-headline">Access Restricted</h1>

            <!-- Message -->
            <p class="error-desc">
                {{ $exception->getMessage() ?: 'You do not have sufficient permissions to view this section, or this feature is not included in your active plan.' }}
            </p>

            <!-- Current Plan Badge (if available) -->
            @if(auth()->check() && optional(auth()->user()->salon)->activeSubscription)
                @php
                    $subscription = auth()->user()->salon->activeSubscription;
                    $plan = $subscription->plan;
                @endphp
                <div class="plan-pill">
                    <i class="fas fa-crown"></i>
                    <span>Current Tier: {{ $plan->name ?? 'Standard' }}</span>
                </div>
            @endif

            <!-- Action Buttons with Role-Aware Resolution -->
            <div class="d-flex gap-2 justify-content-center flex-wrap mb-2">
                @php
                    $dashboardUrl = route('home.dashboard');
                    $salonSlug = null;
                    
                    if (auth()->check()) {
                        $user = auth()->user();
                        $salonSlug = optional($user->salon)->slug ?? request()->segment(1);

                        if ($user->hasRole('super_admin')) {
                            $dashboardUrl = route('admin.super.dashboard');
                        } elseif ($user->hasRole('customer')) {
                            $dashboardUrl = $salonSlug ? route('customer.dashboard', ['salon_slug' => $salonSlug]) : route('home.dashboard');
                        } elseif ($user->hasRole('employee')) {
                            $dashboardUrl = $salonSlug ? route('employee.dashboard', ['salon_slug' => $salonSlug]) : route('home.dashboard');
                        } elseif ($salonSlug) {
                            $dashboardUrl = route('dashboard', ['salon_slug' => $salonSlug]);
                        }
                    }
                @endphp

                @if(auth()->check() && auth()->user()->hasRole('salon_admin') && $salonSlug)
                    <a href="{{ route('admin.saas.subscription.index', ['salon_slug' => $salonSlug]) }}" class="btn-luxury-gold">
                        <i class="fas fa-rocket"></i>
                        Upgrade Plan
                    </a>
                @endif

                <a href="{{ $dashboardUrl }}" class="btn-luxury-gold">
                    <i class="fas fa-home"></i>
                    Return to Dashboard
                </a>

                <button onclick="window.history.back()" class="btn-luxury-outline">
                    <i class="fas fa-arrow-left"></i>
                    Go Back
                </button>
            </div>

            <!-- Features Breakdown (if subscription exists) -->
            @if(auth()->check() && optional(auth()->user()->salon)->activeSubscription)
                @php
                    $planFeatures = $plan->features ?? [];
                    if (is_string($planFeatures)) {
                        $planFeatures = json_decode($planFeatures, true) ?? [];
                    }
                @endphp
                @if(!empty($planFeatures))
                    <div class="features-panel">
                        <div class="features-heading">
                            <i class="fas fa-gem"></i>
                            Features Included in Your Plan
                        </div>
                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            @foreach(array_slice($planFeatures, 0, 6) as $feat)
                                <span class="feature-chip">
                                    <i class="fas fa-check"></i>
                                    {{ $feat }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <!-- Support Footer with Dynamic SaaS Settings -->
            @php
                $settingsService = app(\App\Services\SettingsService::class);
                $supportEmail = $settingsService->get('contact_email') 
                                ?: $settingsService->get('business_email') 
                                ?: config('mail.from.address', 'support@fsqatar.com');
                $supportPhone = $settingsService->get('contact_phone') 
                                ?: $settingsService->get('business_phone', '+974 66266581');
                $cleanPhone = preg_replace('/[^\d+]/', '', $supportPhone);
            @endphp
            <div class="support-info d-flex align-items-center justify-content-center gap-3 flex-wrap">
                <span>
                    <i class="far fa-circle-question me-1"></i>
                    Need assistance? Contact our team:
                </span>
                @if($supportEmail)
                    <a href="mailto:{{ $supportEmail }}" class="text-decoration-none">
                        <i class="fas fa-envelope me-1" style="color: var(--theme-accent);"></i>{{ $supportEmail }}
                    </a>
                @endif
                @if($supportPhone)
                    <span class="text-white-50">|</span>
                    <a href="tel:{{ $cleanPhone }}" class="text-decoration-none">
                        <i class="fas fa-phone me-1" style="color: var(--theme-accent);"></i>{{ $supportPhone }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
