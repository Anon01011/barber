<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - {{ $appName ?? config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ app(\App\Services\SettingsService::class)->getFaviconUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1,h2,h3,h4,h5,h6 { font-family: 'Outfit', sans-serif; }
        .gradient-overlay {
            background: linear-gradient(135deg, rgba(79,70,229,0.92) 0%, rgba(124,58,237,0.88) 100%);
        }
        .fp-input {
            display: block; width: 100%;
            padding: 0.65rem 1rem 0.65rem 2.75rem;
            border: 1.5px solid #e2e8f0; border-radius: 0.5rem;
            font-size: 0.875rem; background: #fff; color: #1e293b;
            transition: border-color .15s, box-shadow .15s; outline: none;
        }
        .fp-input:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.12); }
        .fp-input.is-error { border-color: #ef4444; background: #fef2f2; }
        .fp-input.is-error:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.12); }
    </style>
</head>

<body class="bg-slate-50 h-screen w-full overflow-hidden flex">

    {{-- ===== Left Brand Panel ===== --}}
    <div class="hidden lg:flex w-5/12 relative overflow-hidden flex-col justify-between p-12 text-white h-full">
        <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover z-0">
            <source src="https://cdn.coverr.co/videos/coverr-woman-getting-hair-styled-at-salon-5647/1080p.mp4" type="video/mp4">
        </video>
        <div class="absolute inset-0 gradient-overlay z-1"></div>

        <div class="relative z-10">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-white/80 hover:text-white transition-colors mb-10">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Back to Home
            </a>
            <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center mb-6 border border-white/20">
                <i class="fa-solid fa-lock-open text-xl"></i>
            </div>
            <h1 class="text-4xl font-bold mb-4 leading-tight">Reset your<br>password</h1>
            <p class="text-purple-100 text-base leading-relaxed max-w-sm">
                Enter your email and we'll send a secure reset link to get you back in.
            </p>
        </div>

        <div class="relative z-10 space-y-3">
            <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm p-3 rounded-xl border border-white/10">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-shield-halved text-sm"></i>
                </div>
                <div>
                    <p class="font-semibold text-sm">Secure link</p>
                    <p class="text-purple-100 text-xs">Valid for 60 minutes only</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm p-3 rounded-xl border border-white/10">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-envelope-circle-check text-sm"></i>
                </div>
                <div>
                    <p class="font-semibold text-sm">Check your inbox</p>
                    <p class="text-purple-100 text-xs">And spam folder if needed</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-xs text-purple-200/60">
            &copy; {{ date('Y') }} {{ $appName ?? config('app.name') }}. All rights reserved.
        </div>
    </div>

    {{-- ===== Right Form Panel ===== --}}
    <div class="w-full lg:w-7/12 flex items-center justify-center p-6 lg:p-12 overflow-y-auto">
        <div class="w-full max-w-md">

            {{-- Mobile back link --}}
            <a href="{{ url('/') }}" class="lg:hidden flex items-center gap-2 text-slate-500 hover:text-purple-600 text-sm mb-6 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i> Back to Home
            </a>

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900">Forgot your password?</h2>
                <p class="mt-1 text-slate-500 text-sm">No worries — we'll send you reset instructions.</p>
            </div>

            @php
                $dbDriver      = DB::table('settings')->where('key', 'mail_driver')->value('value');
                $isDevMailOnly = !$dbDriver || in_array($dbDriver, ['log', 'array', 'null', '']);
                $envDriver     = config('mail.default', 'log');
                $isEnvDev      = in_array($envDriver, ['log', 'array', 'null']);
                $mailNotReady  = $isDevMailOnly && $isEnvDev;
            @endphp

            {{-- ✅ Success --}}
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-emerald-800 text-sm mb-1">Reset link processed!</h3>
                            @if($mailNotReady)
                                <p class="text-emerald-700 text-xs leading-relaxed">
                                    <strong>⚠️ Dev mode:</strong> No SMTP is configured — the reset link was written to
                                    <code class="bg-emerald-100 px-1 py-0.5 rounded font-mono">storage/logs/laravel.log</code>
                                    instead of being emailed.<br>
                                    Configure SMTP in the
                                    <a href="{{ route('admin.system-settings.index') }}" class="font-semibold underline">Super Admin → Settings → Email</a>
                                    to send real emails.
                                </p>
                            @else
                                <p class="text-emerald-700 text-xs leading-relaxed">
                                    {{ session('status') }}<br>
                                    Please check your inbox (and spam folder). The link expires in <strong>60 minutes</strong>.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- ❌ Validation Errors --}}
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5 flex-shrink-0"></i>
                    <ul class="text-xs text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ⚠️ SMTP not configured warning (before submission) --}}
            @if (!session('status') && $mailNotReady)
                <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 text-sm flex-shrink-0"></i>
                    <div class="text-xs text-amber-800 leading-relaxed">
                        <strong>SMTP not configured.</strong>
                        Password reset emails won't be sent until you configure a mail driver in
                        <a href="{{ route('admin.system-settings.index') }}" class="font-semibold underline hover:text-amber-900">
                            Super Admin → Settings → Email
                        </a>.
                    </div>
                </div>
            @endif

            {{-- Form --}}
            @if (!session('status'))
            <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </span>
                            <input
                                id="email" type="email" name="email"
                                value="{{ old('email') }}"
                                required autofocus autocomplete="email"
                                placeholder="you@example.com"
                                class="fp-input {{ $errors->has('email') ? 'is-error' : '' }}"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg shadow-md text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                        Send Password Reset Link
                    </button>
                </div>
            </form>
            @else
            {{-- After success: show try-again option --}}
            <div class="text-center">
                <a href="{{ route('password.request') }}"
                    class="inline-flex items-center gap-2 text-sm font-medium text-purple-600 hover:text-purple-700 transition-colors">
                    <i class="fa-solid fa-rotate-right text-xs"></i>
                    Try a different email
                </a>
            </div>
            @endif

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-purple-600 transition-colors font-medium">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Back to Sign In
                </a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('forgotForm')?.addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-sm"></i> Sending...';
                btn.classList.add('opacity-75', 'cursor-not-allowed');
            }
        });
    </script>
</body>
</html>
