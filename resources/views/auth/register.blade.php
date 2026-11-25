<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Create Account – {{ $appName ?? config('app.name', 'SalonPro') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl ?? app(\App\Services\SettingsService::class)->getFaviconUrl() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- International Tel Input CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">

    <!-- Compiled Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col lg:flex-row bg-slate-50">

    {{-- ── LEFT: Minimal Brand & Value Panel ────────────────────────────────── --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-[42%] auth-hero-bg flex-col justify-between p-12 xl:p-16 select-none text-white">

        {{-- Top Navigation & Brand --}}
        <div>
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-gold transition-colors mb-12">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Back to home</span>
            </a>

            <div class="flex items-center gap-3 mb-10">
                @if(!empty($appLogo))
                    <img src="{{ $appLogo }}" alt="{{ $appName ?? 'SalonPro' }}" class="h-9 w-auto max-w-[180px] object-contain">
                @else
                    <div class="w-9 h-9 rounded-xl bg-gold/20 border border-gold/30 text-gold flex items-center justify-center">
                        <i class="fa-solid fa-scissors text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight">{{ $appName ?? 'SalonPro' }}</span>
                @endif
            </div>

            <h1 class="text-3xl xl:text-4xl font-bold text-white leading-snug mb-4">
                Join our salon<br>
                <span class="text-gold">community.</span>
            </h1>
            <p class="text-slate-400 text-sm leading-relaxed max-w-xs">
                Book appointments, track service history, and enjoy exclusive member privileges.
            </p>
        </div>

        {{-- Features & Links --}}
        <div class="space-y-5">
            @foreach([
                ['fa-calendar-check', 'Instant Online Booking', 'Choose your stylist, select a slot, and get instant confirmation.'],
                ['fa-clock-rotate-left', 'Complete History', 'View past cuts, stylist notes, and favorite grooming products.'],
                ['fa-bell', 'Automated Reminders', 'Get timely SMS and email alerts with seamless rescheduling.'],
            ] as [$icon, $title, $desc])
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 text-gold flex items-center justify-center flex-shrink-0 text-xs mt-0.5">
                    <i class="fa-solid {{ $icon }}"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">{{ $title }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $desc }}</p>
                </div>
            </div>
            @endforeach

            {{-- Salon Owner Notice --}}
            <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/10 flex items-center justify-between gap-3 text-xs">
                <span class="text-slate-300">Salon or barbershop owner?</span>
                <a href="{{ route('saas.register') }}" class="font-bold text-gold hover:underline whitespace-nowrap">
                    Register Business &rarr;
                </a>
            </div>

            <p class="text-xs text-slate-500 pt-4 border-t border-white/[0.07]">
                &copy; {{ date('Y') }} {{ $appName ?? 'SalonPro' }}. All rights reserved.
            </p>
        </div>
    </div>

    {{-- ── RIGHT: Clean Form Panel ─────────────────────────────────────────── --}}
    <div class="flex-1 min-h-screen flex items-center justify-center p-6 sm:p-10 bg-white overflow-y-auto">
        <div class="w-full max-w-md my-auto py-8">

            {{-- Mobile brand --}}
            <div class="lg:hidden mb-8 text-center">
                @if(!empty($appLogo))
                    <img src="{{ $appLogo }}" alt="{{ $appName ?? 'SalonPro' }}" class="h-8 w-auto mx-auto">
                @else
                    <span class="text-xl font-bold text-obsidian">{{ $appName ?? 'SalonPro' }}</span>
                @endif
            </div>

            {{-- Heading --}}
            <div class="mb-7">
                <h2 class="text-2xl font-bold text-obsidian tracking-tight">Create your account</h2>
                <p class="text-sm text-slate-500 mt-1">Sign up for client membership and appointments</p>
            </div>

            {{-- Errors --}}
            @if(isset($errors) && $errors->any())
                <div class="mb-5 p-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-xs flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                    <ul class="space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('register') }}" autocomplete="off" id="registrationForm" class="space-y-4">
                @csrf

                {{-- Full Name --}}
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-600 mb-1.5">Full Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                            <i class="fa-regular fa-user text-sm"></i>
                        </span>
                        <input id="name" name="name" type="text" autocomplete="name" required autofocus
                            value="{{ old('name') }}"
                            placeholder="e.g. John Doe"
                            class="auth-input-field w-full pl-10 pr-4 py-2.5 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none transition-all">
                    </div>
                </div>

                {{-- Email Address --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-600 mb-1.5">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </span>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            value="{{ old('email') }}"
                            placeholder="name@example.com"
                            class="auth-input-field w-full pl-10 pr-4 py-2.5 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none transition-all">
                    </div>
                </div>

                {{-- Phone Number --}}
                <div>
                    <label for="phone" class="block text-xs font-semibold text-slate-600 mb-1.5">Phone Number</label>
                    <div class="relative">
                        <input id="phone" name="phone" type="tel" autocomplete="tel" required
                            value="{{ old('phone') }}"
                            placeholder="+1 (555) 000-0000"
                            class="auth-input-field w-full py-2.5 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none transition-all">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-600 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            oninput="evaluatePasswordStrength(this.value)"
                            placeholder="Min. 8 characters with letters & numbers"
                            class="auth-input-field w-full pl-10 pr-11 py-2.5 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none transition-all">
                        <button type="button" onclick="togglePasswordVisibility('password', this)"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                            aria-label="Toggle password visibility">
                            <i class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>

                    {{-- Strength Meter --}}
                    <div class="mt-2">
                        <div class="strength-bar-track" id="strength-bars">
                            <div class="strength-bar-segment" id="bar-1"></div>
                            <div class="strength-bar-segment" id="bar-2"></div>
                            <div class="strength-bar-segment" id="bar-3"></div>
                            <div class="strength-bar-segment" id="bar-4"></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1">
                            <span id="strength-label">Password strength</span>
                            <span>Min 8 characters</span>
                        </div>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-600 mb-1.5">Confirm Password</label>
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

                {{-- Terms Checkbox --}}
                <div class="pt-1">
                    <label class="flex items-start gap-2 cursor-pointer text-xs text-slate-600 select-none">
                        <input type="checkbox" name="terms" required
                            class="mt-0.5 h-4 w-4 rounded border-slate-300 text-obsidian focus:ring-gold/30 transition">
                        <span>
                            I agree to the
                            <a href="{{ route('terms') }}" target="_blank" class="font-semibold text-gold-dark hover:underline">Terms of Service</a>
                            and
                            <a href="{{ route('privacy') }}" target="_blank" class="font-semibold text-gold-dark hover:underline">Privacy Policy</a>.
                        </span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit" id="btn-submit"
                    class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-obsidian border border-obsidian hover:bg-gold hover:border-gold hover:text-obsidian active:scale-[0.99] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gold/40 focus:ring-offset-2 mt-2">
                    <span>Create Account</span>
                    <i class="fa-solid fa-arrow-right text-xs opacity-80"></i>
                </button>
            </form>

            {{-- Footer Links --}}
            <div class="mt-8 pt-6 border-t border-slate-100 space-y-2 text-center">
                <p class="text-xs text-slate-500">
                    Already have an account?
                    <a href="{{ route('login', ['role' => 'customer']) }}" class="font-semibold text-gold-dark hover:text-gold transition-colors ml-1">Sign In</a>
                </p>
                <p class="text-xs text-slate-500">
                    Own a salon business?
                    <a href="{{ route('saas.register') }}" class="font-semibold text-slate-700 hover:text-gold transition-colors ml-1">Register Business &rarr;</a>
                </p>
            </div>

        </div>
    </div>

    <!-- International Tel Input JS -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>

    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isHidden);
            icon.classList.toggle('fa-eye-slash', isHidden);
        }

        function evaluatePasswordStrength(password) {
            let score = 0;
            if (!password) {
                updateBars(0, 'Empty', '#e2e8f0');
                return;
            }

            if (password.length >= 8) score++;
            if (/[a-zA-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^a-zA-Z0-9]/.test(password) || password.length >= 12) score++;

            if (score === 1) updateBars(1, 'Weak', '#ef4444');
            else if (score === 2) updateBars(2, 'Fair', '#f59e0b');
            else if (score === 3) updateBars(3, 'Good', '#3b82f6');
            else if (score >= 4) updateBars(4, 'Strong', '#10b981');
        }

        function updateBars(activeCount, text, color) {
            for (let i = 1; i <= 4; i++) {
                const bar = document.getElementById('bar-' + i);
                if (bar) {
                    bar.style.backgroundColor = (i <= activeCount) ? color : '#e2e8f0';
                }
            }
            const label = document.getElementById('strength-label');
            if (label) {
                label.textContent = text;
                label.style.color = activeCount > 0 ? color : '#94a3b8';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const phoneInput = document.querySelector("#phone");
            if (phoneInput && window.intlTelInput) {
                const iti = window.intlTelInput(phoneInput, {
                    initialCountry: "auto",
                    geoIpLookup: function (callback) {
                        fetch("https://ipapi.co/json")
                            .then(res => res.json())
                            .then(data => callback(data.country_code))
                            .catch(() => callback("us"));
                    },
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js"
                });

                const form = document.querySelector("#registrationForm");
                if (form) {
                    form.addEventListener('submit', function () {
                        if (iti.isValidNumber()) {
                            phoneInput.value = iti.getNumber();
                        }
                    });
                }
            }
        });
    </script>

</body>
</html>
