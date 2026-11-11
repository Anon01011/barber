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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 50%, #f5f3ff 100%);
            color: #0f172a;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }

        /* ── Input Styling ── */
        .auth-field-input {
            width: 100%;
            height: 46px;
            padding: 10px 14px 10px 42px;
            border-radius: 12px;
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
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.12);
        }

        .auth-field-input.has-error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .auth-field-input.has-error:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12);
        }

        /* ── Primary Gradient Button ── */
        .btn-gradient-primary {
            width: 100%;
            height: 46px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff !important;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.2px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(124, 58, 237, 0.3);
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-gradient-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.42);
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

        svg.icon-fixed {
            flex-shrink: 0;
            display: inline-block;
            vertical-align: middle;
        }

        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>

<body class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 lg:p-8">

    <div class="w-full max-w-md mx-auto">

        <!-- Top Logo / App Name -->
        <div class="text-center mb-6">
            @if(!empty($appLogo))
                <a href="{{ url('/') }}" class="inline-block">
                    <img src="{{ $appLogo }}" alt="{{ $appName ?? config('app.name') }}" class="h-10 mx-auto object-contain" onerror="this.style.display='none'; document.getElementById('brandTextFallback').style.display='block';">
                </a>
                <span id="brandTextFallback" style="display: none;" class="text-xl font-bold text-slate-800 tracking-tight">{{ $appName ?? config('app.name') }}</span>
            @else
                <a href="{{ url('/') }}" class="text-xl font-extrabold text-slate-800 tracking-tight hover:text-purple-600 transition-colors">
                    {{ $appName ?? config('app.name') }}
                </a>
            @endif
        </div>

        <!-- Main Card -->
        <div style="background: #ffffff; border-radius: 20px; border: 1px solid rgba(226, 232, 240, 0.8); box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 0 1px 1px rgba(15, 23, 42, 0.02); padding: 36px 32px;">

            @if (session('status'))
                {{-- ═══════════════════════════════════════════════
                     SUCCESS STATE: Confirmation View
                     ═══════════════════════════════════════════════ --}}
                <div style="text-align: center;">

                    <!-- Soft Emerald Badge -->
                    <div style="width: 68px; height: 68px; border-radius: 50%; background: #ecfdf5; border: 1.5px solid #a7f3d0; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; color: #059669; box-shadow: 0 10px 20px -8px rgba(16, 185, 129, 0.25);">
                        <svg class="icon-fixed" style="width: 32px; height: 32px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h2 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0 0 8px;">Check your email</h2>
                    <p style="font-size: 14px; color: #64748b; line-height: 1.6; margin: 0 0 24px;">
                        If an account exists with that address, we've sent password reset instructions. Please check your inbox and spam folder.
                    </p>

                    <!-- Notice Pill -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; text-align: left;">
                        <svg class="icon-fixed" style="width: 18px; height: 18px; color: #6366f1;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p style="margin: 0; font-size: 12.5px; color: #64748b; line-height: 1.4;">
                            For your security, this reset link expires in <strong style="color: #1e293b;">60 minutes</strong>.
                        </p>
                    </div>

                    <!-- Return to Sign In Button -->
                    <a href="{{ route('login') }}" class="btn-gradient-primary">
                        Return to Sign In
                    </a>

                    <!-- Try Again Link -->
                    <div style="margin-top: 22px; font-size: 13px; color: #64748b;">
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
                     FORM STATE: Clean Centered Card
                     ═══════════════════════════════════════════════ --}}
                <div style="text-align: center; margin-bottom: 26px;">
                    <!-- Key Icon Badge -->
                    <div style="width: 52px; height: 52px; border-radius: 16px; background: #f5f3ff; border: 1.5px solid #ede9fe; display: inline-flex; align-items: center; justify-content: center; color: #7c3aed; margin-bottom: 16px; box-shadow: 0 4px 12px -2px rgba(124, 58, 237, 0.12);">
                        <svg class="icon-fixed" style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h2 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0 0 8px;">Forgot your password?</h2>
                    <p style="font-size: 14px; color: #64748b; line-height: 1.5; margin: 0;">
                        No worries — enter your email address and we'll send you instructions to reset it.
                    </p>
                </div>

                @if ($errors->any())
                    <div style="background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 8px; padding: 12px 14px; margin-bottom: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 8px;">
                            <svg class="icon-fixed" style="width: 16px; height: 16px; color: #ef4444; margin-top: 2px;" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <ul style="margin: 0; padding: 0; list-style: none; font-size: 12.5px; color: #b91c1c;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" id="forgotForm" autocomplete="off">
                    @csrf

                    <div style="margin-bottom: 22px;">
                        <label for="email" style="display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 7px;">
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

                <div style="text-align: center; margin-top: 26px;">
                    <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #64748b; text-decoration: none; transition: color 0.15s;" onmouseover="this.style.color='#7c3aed'" onmouseout="this.style.color='#64748b'">
                        <svg class="icon-fixed" style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Sign In
                    </a>
                </div>
            @endif

        </div>

        <!-- Footer Note -->
        <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #94a3b8;">
            &copy; {{ date('Y') }} {{ $appName ?? config('app.name') }}. All rights reserved.
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
</body>
</html>
