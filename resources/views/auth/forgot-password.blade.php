<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - {{ $appName }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ app(\App\Services\SettingsService::class)->getFaviconUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }

        .gradient-overlay {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.9) 0%, rgba(124, 58, 237, 0.8) 100%);
        }
    </style>
</head>

<body class="bg-slate-50 h-screen w-full overflow-hidden flex">

    <!-- Left Side - Video & Brand (matching Login layout) -->
    <div class="hidden lg:flex w-1/2 relative overflow-hidden flex-col justify-between p-12 text-white h-full">
        <video autoplay muted loop playsinline class="absolute top-0 left-0 w-full h-full object-cover z-0">
            <source src="https://cdn.coverr.co/videos/coverr-woman-getting-hair-styled-at-salon-5647/1080p.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <div class="absolute top-0 left-0 w-full h-full gradient-overlay z-1"></div>

        <div class="relative z-10">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-white/80 hover:text-white transition-colors mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Home
            </a>

            <h1 class="text-5xl font-bold mb-4 leading-tight">
                Reset your <br> password
            </h1>
            <p class="text-lg text-purple-100 max-w-md leading-relaxed">
                Securely recover access to your account in just a few simple steps.
            </p>
        </div>

        <div class="relative z-10 space-y-4">
            <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm p-3.5 rounded-xl border border-white/10">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-white flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm">Strict Privacy</h3>
                    <p class="text-purple-100 text-xs">Credentials are encrypted & never shared</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-xs text-purple-200/60 mt-4">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </div>
    </div>

    <!-- Right Side - Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-10 bg-white overflow-y-auto">
        <div class="w-full max-w-sm space-y-6">

            <!-- Mobile Top Bar with Back Link -->
            <div class="lg:hidden flex items-center justify-between pb-2 border-b border-slate-100">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-purple-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Sign In
                </a>
                <span class="text-xs font-semibold text-slate-400">{{ $appName }}</span>
            </div>

            <!-- Header -->
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-purple-50 text-purple-600 mb-3 border border-purple-100 shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900">Forgot your password?</h2>
                <p class="mt-1 text-slate-500 text-sm">No worries — enter your email address and we'll send you instructions to reset it.</p>
            </div>

            {{-- Success State --}}
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/70 p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 text-emerald-600 mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-semibold text-emerald-900">Check your inbox</h3>
                            <p class="text-xs text-emerald-700 leading-relaxed">
                                If an account exists for that email, a password reset link has been sent. Please check your inbox and spam folder.
                            </p>
                            <p class="text-[11px] text-emerald-600/90 pt-1">
                                For your security, this reset link expires in <strong>60 minutes</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-2 space-y-3">
                    <a href="{{ route('password.request') }}"
                        class="w-full flex items-center justify-center gap-2 py-2 px-4 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Try a different email address
                    </a>

                    <a href="{{ route('login') }}"
                        class="w-full flex items-center justify-center py-2.5 px-4 rounded-lg text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-md transition-all">
                        Return to Sign In
                    </a>
                </div>
            @else
                {{-- Form Validation Errors --}}
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-r-md">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-4 w-4 text-red-400 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4" id="forgotForm" autocomplete="off">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-medium text-slate-700 mb-1.5">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required autofocus
                                class="pl-9 block w-full rounded-lg border-slate-200 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5 transition-colors {{ $errors->has('email') ? 'border-red-400 focus:border-red-500 focus:ring-red-500 bg-red-50/30' : '' }}"
                                placeholder="you@example.com" value="{{ old('email') }}">
                        </div>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 hover:shadow-purple-500/30 hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Send Reset Link
                    </button>
                </form>

                <div class="pt-2 text-center">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-purple-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg> Sending reset link...`;
                btn.classList.add('opacity-80', 'cursor-not-allowed');
            }
        });
    </script>
</body>
</html>
