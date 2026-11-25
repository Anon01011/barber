<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Forgot Password – {{ $appName ?? config('app.name', 'SalonPro') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ app(\App\Services\SettingsService::class)->getFaviconUrl() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Compiled Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 bg-slate-50 text-slate-800">

    <div class="w-full max-w-md mx-auto py-6">

        <!-- Top Logo / App Name -->
        <div class="text-center mb-6">
            @if(!empty($appLogo))
                <a href="{{ url('/') }}" class="inline-block">
                    <img src="{{ $appLogo }}" alt="{{ $appName ?? config('app.name') }}" class="h-9 mx-auto object-contain" onerror="this.style.display='none'; document.getElementById('brandTextFallback').style.display='block';">
                </a>
                <span id="brandTextFallback" style="display: none;" class="text-xl font-bold text-obsidian tracking-tight">{{ $appName ?? config('app.name') }}</span>
            @else
                <a href="{{ url('/') }}" class="text-xl font-bold text-obsidian tracking-tight hover:text-gold transition-colors">
                    {{ $appName ?? config('app.name') }}
                </a>
            @endif
        </div>

        <!-- Main Card -->
        <div class="auth-card">

            @if (session('status'))
                <!-- Success State -->
                <div class="text-center py-2">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center mx-auto mb-3.5 text-lg">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>

                    <h2 class="text-xl font-bold text-obsidian tracking-tight mb-1">Check Your Inbox</h2>
                    <p class="text-xs text-slate-500 leading-relaxed mb-5">
                        {{ session('status') }}
                    </p>

                    <a href="{{ route('login') }}"
                       class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-obsidian border border-obsidian hover:bg-gold hover:border-gold hover:text-obsidian active:scale-[0.99] transition-all duration-200">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>Return to Sign In</span>
                    </a>
                </div>
            @else
                <!-- Form Header -->
                <div class="text-center mb-6">
                    <div class="w-11 h-11 rounded-xl bg-gold/15 text-gold-dark border border-gold/30 flex items-center justify-center mx-auto mb-3 text-base">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <h2 class="text-xl font-bold text-obsidian tracking-tight mb-1">Reset Password</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Enter your registered email address and we'll send you a secure password reset link.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-5 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-800 flex items-start gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                        <ul class="space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" id="forgotForm" autocomplete="off" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                                <i class="fa-regular fa-envelope text-sm"></i>
                            </span>
                            <input id="email" name="email" type="email" autocomplete="email" required autofocus
                                class="auth-input-field w-full pl-10 pr-4 py-2.5 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none transition-all"
                                placeholder="name@example.com" value="{{ old('email') }}">
                        </div>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-obsidian border border-obsidian hover:bg-gold hover:border-gold hover:text-obsidian active:scale-[0.99] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gold/40 focus:ring-offset-2 mt-2">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Send Reset Link</span>
                    </button>
                </form>

                <div class="text-center mt-6 pt-5 border-t border-slate-100">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-gold transition-colors">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        <span>Back to Sign In</span>
                    </a>
                </div>
            @endif

        </div>

        <!-- Footer Note -->
        <div class="text-center mt-5 text-xs text-slate-400">
            &copy; {{ date('Y') }} {{ $appName ?? config('app.name') }}. All rights reserved.
        </div>

    </div>

    <script>
        document.getElementById('forgotForm')?.addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> <span>Sending reset link...</span>`;
            }
        });
    </script>
</body>
</html>
