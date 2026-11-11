<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - {{ $appName ?? config('app.name') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ app(\App\Services\SettingsService::class)->getFaviconUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            color: #0f172a;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }

        .gradient-overlay {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.92) 0%, rgba(124, 58, 237, 0.88) 100%);
        }

        /* ── Input Styling ── */
        .auth-field-input {
            width: 100%;
            height: 44px;
            padding: 10px 14px 10px 42px;
            border-radius: 10px;
            border: 1.5px solid #cbd5e1;
            font-size: 14px;
            color: #0f172a;
            background: #ffffff;
            transition: all 0.15s ease;
            outline: none;
            font-family: inherit;
        }

        .auth-field-input:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.14);
        }

        .auth-field-input.has-error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .auth-field-input.has-error:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14);
        }

        /* ── Primary Button ── */
        .btn-gradient-primary {
            width: 100%;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff !important;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-gradient-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.45);
            transform: translateY(-1px);
        }

        .btn-gradient-primary:active {
            transform: translateY(0);
        }

        .btn-gradient-primary:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none;
        }

        /* ── SVGs explicit sizing ── */
        svg.icon-fixed {
            flex-shrink: 0;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen w-full flex flex-col lg:flex-row overflow-x-hidden">

    <!-- Left Side - Video & Brand (matching Login layout) -->
    <div class="hidden lg:flex w-1/2 relative overflow-hidden flex-col justify-between p-12 text-white min-h-screen">
        <video autoplay muted loop playsinline class="absolute top-0 left-0 w-full h-full object-cover z-0">
            <source src="https://cdn.coverr.co/videos/coverr-woman-getting-hair-styled-at-salon-5647/1080p.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <div class="absolute top-0 left-0 w-full h-full gradient-overlay z-1"></div>

        <div class="relative z-10">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-white/80 hover:text-white transition-colors mb-8 text-sm font-medium">
                <svg class="icon-fixed" style="width: 18px; height: 18px;" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Home
            </a>

            <h1 class="text-5xl font-bold mb-4 leading-tight">
                Reset your <br> password
            </h1>
            <p class="text-base text-purple-100 max-w-md leading-relaxed">
                Securely recover access to your account in just a few simple steps.
            </p>
        </div>

        <div class="relative z-10 space-y-4 my-8">
            <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm p-3.5 rounded-xl border border-white/10">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-white flex-shrink-0">
                    <svg class="icon-fixed" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm">Protected Link</h3>
                    <p class="text-purple-100 text-xs">One-time reset token valid for 60 minutes</p>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm p-3.5 rounded-xl border border-white/10">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-white flex-shrink-0">
                    <svg class="icon-fixed" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm">Strict Privacy</h3>
                    <p class="text-purple-100 text-xs">Credentials are encrypted & never shared</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-xs text-purple-200/60">
            &copy; {{ date('Y') }} {{ $appName ?? config('app.name') }}. All rights reserved.
        </div>
    </div>

    <!-- Right Side - Form Container -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-10 lg:p-12 bg-white min-h-screen">
        <div class="w-full max-w-sm">

            <!-- Mobile Top Bar with Back Link -->
            <div class="lg:hidden flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-purple-600 transition-colors">
                    <svg class="icon-fixed" style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Sign In
                </a>
                <span class="text-xs font-bold text-slate-400">{{ $appName ?? config('app.name') }}</span>
            </div>

            @if (session('status'))
                {{-- ═══════════════════════════════════════════════
                     SUCCESS STATE: Clean Unified Confirmation Card
                     ═══════════════════════════════════════════════ --}}
                <div class="text-center">
                    <!-- Soft Green Badge -->
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #ecfdf5; border: 1.5px solid #a7f3d0; margin: 0 auto 18px; display: flex; align-items: center; justify-content: center; color: #059669;">
                        <svg class="icon-fixed" style="width: 30px; height: 30px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Check your email</h2>
                    <p class="text-sm text-slate-600 leading-relaxed mb-6">
                        If an account exists with that address, we've sent instructions to reset your password. Please check your inbox and spam folder.
                    </p>

                    <!-- Security Notice Strip -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; text-align: left;">
                        <svg class="icon-fixed" style="width: 18px; height: 18px; color: #6366f1;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.4;">
                            For your security, the reset link will expire in <strong style="color: #1e293b;">60 minutes</strong>.
                        </p>
                    </div>

                    <!-- Return to Sign In Button -->
                    <a href="{{ route('login') }}" class="btn-gradient-primary">
                        Return to Sign In
                    </a>

                    <!-- Try Again Subtext Link -->
                    <div style="margin-top: 20px; font-size: 13px; color: #64748b;">
                        Didn't receive the email?
                        <a href="{{ route('password.request') }}" style="color: #6366f1; font-weight: 600; text-decoration: none; margin-left: 4px; display: inline-flex; align-items: center; gap: 4px;">
                            <svg class="icon-fixed" style="width: 13px; height: 13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Try again
                        </a>
                    </div>
                </div>

            @else
                {{-- ═══════════════════════════════════════════════
                     FORM STATE: Clean, Polished Input
                     ═══════════════════════════════════════════════ --}}
                <div class="text-center lg:text-left mb-6">
                    <div style="width: 48px; height: 48px; border-radius: 14px; background: #f5f3ff; border: 1.5px solid #ede9fe; display: inline-flex; align-items: center; justify-content: center; color: #7c3aed; margin-bottom: 14px;">
                        <svg class="icon-fixed" style="width: 22px; height: 22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900">Forgot your password?</h2>
                    <p class="mt-1 text-slate-500 text-sm leading-relaxed">
                        No worries — enter your email address and we'll send you instructions to reset it.
                    </p>
                </div>

                @if ($errors->any())
                    <div style="background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 6px; padding: 12px; margin-bottom: 18px;">
                        <div style="display: flex; align-items: flex-start; gap: 8px;">
                            <svg class="icon-fixed" style="width: 16px; height: 16px; color: #ef4444; margin-top: 2px;" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <ul style="margin: 0; padding: 0; list-style: none; font-size: 12px; color: #b91c1c;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" id="forgotForm" autocomplete="off">
                    @csrf

                    <div style="margin-bottom: 20px;">
                        <label for="email" style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                            Email Address <span style="color: #ef4444;">*</span>
                        </label>
                        <div style="position: relative;">
                            <div style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                                <svg class="icon-fixed" style="width: 18px; height: 18px;" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required autofocus
                                class="auth-field-input {{ $errors->has('email') ? 'has-error' : '' }}"
                                placeholder="you@example.com" value="{{ old('email') }}">
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" class="btn-gradient-primary">
                        <svg class="icon-fixed" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Send Reset Link
                    </button>
                </form>

                <div style="text-align: center; margin-top: 24px;">
                    <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #64748b; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#7c3aed'" onmouseout="this.style.color='#64748b'">
                        <svg class="icon-fixed" style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Sign In
                    </a>
                </div>
            @endif

        </div>
    </div>

    <script>
        document.getElementById('forgotForm')?.addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<svg class="icon-fixed" style="width: 16px; height: 16px; animation: spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg> Sending reset link...`;
            }
        });
    </script>
    <style>
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</body>
</html>
