<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Super Admin Sign In – {{ $appName ?? config('app.name', 'SalonPro') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl ?? app(\App\Services\SettingsService::class)->getFaviconUrl() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Compiled Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 lg:p-8 auth-hero-bg text-slate-100">

    <div class="w-full max-w-md mx-auto">
        <!-- Top Security Badge -->
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 mb-6 group">
                @if(!empty($appLogo))
                    <img src="{{ $appLogo }}" alt="{{ $appName ?? 'SalonPro' }}" class="h-10 w-auto max-w-[200px] object-contain">
                @else
                    <div class="w-10 h-10 rounded-xl bg-gold/20 border border-gold/30 text-gold flex items-center justify-center font-bold text-lg shadow-lg">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-white font-heading">
                        {{ $appName ?? 'SalonPro' }}
                    </span>
                @endif
            </a>

            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-gold/15 text-gold border border-gold/30 mb-3">
                <i class="fa-solid fa-lock text-[10px]"></i>
                <span>SaaS Super Admin Console</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight font-heading">
                Platform Administration
            </h1>
            <p class="text-xs text-slate-400 mt-1">
                Authorized super administrators only. All login sessions are monitored and logged.
            </p>
        </div>

        <!-- Admin Card -->
        <div class="bg-obsidian-800/90 border border-obsidian-border rounded-2xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">

            <!-- Session Status / Errors -->
            @if (session('status'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 text-xs">
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-red-400 text-sm mt-0.5"></i>
                        <div>
                            <span class="font-bold block mb-1">Authentication Failed:</span>
                            <ul class="list-disc list-inside space-y-0.5 text-red-300">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-4" autocomplete="off" id="adminLoginForm">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Admin Email <span class="text-red-400">*</span>
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-user-shield text-sm"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="username" required autofocus
                            class="auth-input-field block w-full pl-10 pr-4 py-2.5 text-sm text-white bg-obsidian-900/90 border border-slate-700/80 rounded-xl transition placeholder-slate-500 focus:outline-none"
                            placeholder="admin@platform.com" value="{{ old('email') }}">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                            Password <span class="text-red-400">*</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-xs font-semibold text-gold hover:text-gold-hover transition-colors">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-key text-sm"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="auth-input-field block w-full pl-10 pr-11 py-2.5 text-sm text-white bg-obsidian-900/90 border border-slate-700/80 rounded-xl transition placeholder-slate-500 focus:outline-none"
                            placeholder="••••••••••••">
                        <button type="button" onclick="togglePasswordVisibility('password', this)"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition-colors"
                            aria-label="Toggle password visibility">
                            <i class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Security Policy -->
                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                        <input id="remember_me" name="remember" type="checkbox"
                            class="h-4 w-4 rounded border-slate-700 bg-obsidian-900 text-gold focus:ring-gold/30 transition">
                        <span class="ml-2 text-xs text-slate-300 font-medium">Remember admin session</span>
                    </label>

                    <span class="text-[11px] text-slate-500">
                        TLS 1.3 / Strict
                    </span>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-obsidian bg-gold border border-gold hover:bg-gold-hover hover:border-gold-hover hover:text-white active:scale-[0.99] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gold/40 focus:ring-offset-2 focus:ring-offset-obsidian">
                        <span>Access Admin Dashboard</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-800 text-center">
                <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Switch to Standard Salon / Staff Login</span>
                </a>
            </div>
        </div>

        <!-- Footer Notice -->
        <div class="text-center text-xs text-slate-500 mt-6">
            &copy; {{ date('Y') }} {{ $appName ?? 'SalonPro' }}. Super Admin Security Boundary.
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>

</html>
