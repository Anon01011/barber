<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Feature Not Available</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            background: linear-gradient(135deg, #fff5f7 0%, #ffe5ec 50%, #ffd6e0 100%);
            min-height: 100vh;
            position: relative;
        }

        /* Animated background blobs */
        .blob-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
            animation: blob 20s infinite ease-in-out;
        }

        .blob-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #ec4899, #f43f5e);
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .blob-2 {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, #f472b6, #fb7185);
            top: -50px;
            right: -100px;
            animation-delay: 4s;
        }

        .blob-3 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #fda4af, #fbbf24);
            bottom: -100px;
            left: 20%;
            animation-delay: 8s;
        }

        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        /* Main container */
        .error-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* Glass card effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            box-shadow: 0 20px 60px rgba(236, 72, 153, 0.2);
            max-width: 650px;
            width: 100%;
            animation: slideUp 0.6s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Lock icon */
        .icon-circle {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: pulse 2s infinite;
            box-shadow: 0 10px 40px rgba(236, 72, 153, 0.4);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 10px 40px rgba(236, 72, 153, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 15px 50px rgba(236, 72, 153, 0.5); }
        }

        .icon-circle i {
            font-size: 2.5rem;
            color: white;
        }

        /* Error code */
        .error-code {
            font-size: 5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.75rem;
        }

        /* Title and text */
        .error-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }

        .error-message {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        /* Plan badge */
        .plan-badge {
            background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);
            color: white;
            padding: 0.625rem 1.5rem;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 8px 25px rgba(236, 72, 153, 0.3);
        }

        /* Buttons */
        .btn-custom {
            padding: 0.75rem 1.75rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.9rem;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(236, 72, 153, 0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(236, 72, 153, 0.4);
            color: white;
        }

        .btn-secondary-custom {
            background: white;
            color: #ec4899;
            border: 2px solid #ec4899;
        }

        .btn-secondary-custom:hover {
            background: #ec4899;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(236, 72, 153, 0.3);
        }

        /* Features section */
        .features-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f3f4f6;
        }

        .features-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .feature-badge {
            background: linear-gradient(135deg, #fce7f3 0%, #fecdd3 100%);
            color: #be123c;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin: 0.25rem;
            border: 1px solid rgba(236, 72, 153, 0.2);
        }

        /* Help text */
        .help-text {
            margin-top: 1.5rem;
            color: #9ca3af;
            font-size: 0.8rem;
        }

        .help-text a {
            color: #ec4899;
            text-decoration: none;
            font-weight: 600;
        }

        .help-text a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .glass-card {
                padding: 2rem 1.5rem;
            }

            .error-code {
                font-size: 4rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .icon-circle {
                width: 80px;
                height: 80px;
            }

            .icon-circle i {
                font-size: 2rem;
            }

            .btn-custom {
                padding: 0.625rem 1.25rem;
                font-size: 0.85rem;
            }
        }

        @media (max-height: 700px) {
            .glass-card {
                padding: 1.5rem;
            }
            
            .error-code {
                font-size: 3.5rem;
                margin-bottom: 0.5rem;
            }
            
            .icon-circle {
                width: 70px;
                height: 70px;
                margin-bottom: 1rem;
            }
            
            .icon-circle i {
                font-size: 1.75rem;
            }
            
            .features-section {
                margin-top: 1rem;
                padding-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated background blobs -->
    <div class="blob-container">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <!-- Main content -->
    <div class="error-container">
        <div class="glass-card text-center">
            <!-- Lock icon -->
            <div class="icon-circle">
                <i class="fas fa-lock"></i>
            </div>

            <!-- Error code -->
            <div class="error-code">403</div>

            <!-- Title -->
            <h1 class="error-title">Feature Not Available</h1>

            <!-- Message -->
            <p class="error-message">
                {{ $exception->getMessage() ?: 'This feature is not available in your current plan. Please upgrade to access this feature.' }}
            </p>

            <!-- Current Plan Info -->
            @if(auth()->check() && auth()->user()->salon && auth()->user()->salon->activeSubscription)
                @php
                    $subscription = auth()->user()->salon->activeSubscription;
                    $plan = $subscription->plan;
                @endphp
                <div class="plan-badge">
                    <i class="fas fa-crown"></i>
                    <span>Current Plan: {{ $plan->name }}</span>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-flex gap-2 justify-content-center flex-wrap mb-3">
                @if(auth()->check() && auth()->user()->hasRole('salon_admin'))
                    @php
                        $salonSlug = auth()->user()->salon ? auth()->user()->salon->slug : null;
                    @endphp
                    @if($salonSlug)
                        <a href="{{ route('admin.saas.subscription.index', ['salon_slug' => $salonSlug]) }}" class="btn-custom btn-primary-custom">
                            <i class="fas fa-rocket"></i>
                            Upgrade Plan
                        </a>
                    @endif
                @endif
                
                @if(auth()->check())
                    @php
                        // Determine the correct dashboard route based on user role and salon
                        $dashboardUrl = null;
                        $user = auth()->user();
                        
                        if ($user->hasRole('super_admin')) {
                            // Super admin goes to super admin dashboard
                            $dashboardUrl = route('admin.super.dashboard');
                        } elseif ($user->salon && $user->salon->slug) {
                            // Regular users with salon go to salon dashboard
                            $dashboardUrl = route('dashboard', ['salon_slug' => $user->salon->slug]);
                        } else {
                            // Fallback to home dashboard redirect
                            $dashboardUrl = route('home.dashboard');
                        }
                    @endphp
                    
                    <a href="{{ $dashboardUrl }}" class="btn-custom btn-secondary-custom">
                        <i class="fas fa-home"></i>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('home') }}" class="btn-custom btn-secondary-custom">
                        <i class="fas fa-home"></i>
                        Home
                    </a>
                @endif
                
                <button onclick="window.history.back()" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i>
                    Go Back
                </button>
            </div>

            <!-- Features Info -->
            @if(auth()->check() && auth()->user()->salon && auth()->user()->salon->activeSubscription)
                <div class="features-section">
                    <div class="features-title">
                        <i class="fas fa-star" style="color: #f59e0b;"></i>
                        Your Available Features
                    </div>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        @php
                            $features = $plan->features ?? [];
                            if (is_string($features)) {
                                $features = json_decode($features, true) ?? [];
                            }
                        @endphp
                        @if(count($features) > 0)
                            @foreach($features as $feature)
                                <span class="feature-badge">
                                    <i class="fas fa-check-circle"></i>
                                    {{ $feature }}
                                </span>
                            @endforeach
                        @else
                            <p class="text-muted mb-0 small">No features available in your current plan.</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Help Text -->
            <div class="help-text">
                <i class="fas fa-question-circle"></i>
                Need help? Contact <a href="mailto:support@salon.com">support</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
