<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Set New Password – {{ $appName ?? config('app.name', 'SalonPro') }}</title>

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

            <!-- Form Header -->
            <div class="text-center mb-6">
                <div class="w-11 h-11 rounded-xl bg-gold/15 text-gold-dark border border-gold/30 flex items-center justify-center mx-auto mb-3 text-base">
                    <i class="fa-solid fa-lock-open"></i>
                </div>
                <h2 class="text-xl font-bold text-obsidian tracking-tight mb-1">Set New Password</h2>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Create a strong, secure password for your account.
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

            <form method="POST" action="{{ route('password.store') }}" id="resetForm" autocomplete="off" class="space-y-4">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </span>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="auth-input-field w-full pl-10 pr-4 py-2.5 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none transition-all"
                            value="{{ old('email', $request->email) }}" readonly>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        New Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input id="password" name="password" type="password" autocomplete="new-password" required autofocus
                            placeholder="Min. 8 characters"
                            class="auth-input-field w-full pl-10 pr-11 py-2.5 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none transition-all">
                        <button type="button" onclick="togglePasswordVisibility('password', this)"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                            aria-label="Toggle password visibility">
                            <i class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Confirm New Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                            <i class="fa-solid fa-lock-open text-sm"></i>
                        </span>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                            placeholder="Re-type your password"
                            class="auth-input-field w-full pl-10 pr-11 py-2.5 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none transition-all">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                            aria-label="Toggle password visibility">
                            <i class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="submitBtn"
                    class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-obsidian border border-obsidian hover:bg-gold hover:border-gold hover:text-obsidian active:scale-[0.99] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gold/40 focus:ring-offset-2 mt-2">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Reset Password</span>
                </button>
            </form>

            <div class="text-center mt-6 pt-5 border-t border-slate-100">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-gold transition-colors">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Back to Sign In</span>
                </a>
            </div>

        </div>

        <!-- Footer Note -->
        <div class="text-center mt-5 text-xs text-slate-400">
            &copy; {{ date('Y') }} {{ $appName ?? config('app.name') }}. All rights reserved.
        </div>

    </div>

    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isHidden);
            icon.classList.toggle('fa-eye-slash', isHidden);
        }

        document.getElementById('resetForm')?.addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> <span>Resetting password...</span>`;
            }
        });
    </script>
</body>
</html>
