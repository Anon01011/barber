<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>No Active Plan - {{ config('app.name') }}</title>
    @vite(['resources/css/auth.css'])
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="auth-container">
        <!-- Left Side -->
        <div class="auth-left">
            <a href="{{ url('/') }}" class="back-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Back to Home
            </a>
            <div class="auth-left-content">
                <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; line-height: 1.2;">Welcome to
                    {{ config('app.name') }}</h1>
                <p style="font-size: 1rem; opacity: 0.9; margin-bottom: 2rem; line-height: 1.5;">Your all-in-one
                    solution for modern salon management.</p>

                <div style="margin-bottom: 2rem;">
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <span>Unlimited Appointments</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <span>Advanced Reporting</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <span>Customer Management</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <span>Staff Scheduling</span>
                    </div>
                </div>

                <div style="display: flex; flex-wrap: wrap; gap: 0.375rem;">
                    <span class="role-badge">Basic Plan</span>
                    <span class="role-badge">Pro Plan</span>
                    <span class="role-badge">Enterprise</span>
                </div>
            </div>
        </div>

        <!-- Right Side -->
        <div class="auth-right">
            <div class="auth-card">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div
                        style="width: 80px; height: 80px; background-color: #e0e7ff; color: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto; font-size: 2rem;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h2 class="auth-header">No Active Plan</h2>
                    <p style="text-align: center; color: #64748b; margin-bottom: 1.25rem; font-size: 0.875rem;">You need
                        an active subscription to access the dashboard.</p>
                </div>

                <div
                    style="background: #fef3c7; color: #92400e; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem; text-align: center;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') ?? 'Your subscription has expired or has not been activated yet.' }}
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @if(auth()->check() && auth()->user()->salon)
                        <a href="{{ route('admin.saas.subscription.index', ['salon_slug' => auth()->user()->salon->slug]) }}"
                            class="auth-button" style="text-decoration: none; text-align: center;">
                            <i class="fas fa-credit-card me-2"></i>View Subscription Plans
                        </a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="auth-button"
                            style="background: transparent; border: 1px solid #e2e8f0; color: #64748b;">
                            <i class="fas fa-sign-out-alt me-2"></i>Sign Out
                        </button>
                    </form>
                </div>

                <div style="text-align: center; margin-top: 2rem; font-size: 0.75rem; color: #64748b;">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>

</html>