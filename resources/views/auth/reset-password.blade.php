<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - {{ app(\App\Services\SettingsService::class)->get('app_name', config('app.name')) }}</title>
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
        .rp-input {
            display: block; width: 100%;
            padding: 0.65rem 2.75rem 0.65rem 2.75rem;
            border: 1.5px solid #e2e8f0; border-radius: 0.5rem;
            font-size: 0.875rem; background: #fff; color: #1e293b;
            transition: border-color .15s, box-shadow .15s; outline: none;
        }
        .rp-input:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.12); }
        .rp-input.is-error { border-color: #ef4444; background: #fef2f2; }
        .rp-input.is-error:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.12); }
        .rp-input.is-success { border-color: #10b981; background: #f0fdf4; }
        .input-wrapper { position: relative; }
        .input-icon-left {
            position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%);
            color: #94a3b8; pointer-events: none; font-size: 0.875rem;
        }
        .pw-toggle {
            position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; padding: 4px; cursor: pointer;
            color: #94a3b8; border-radius: 4px; transition: color .15s;
        }
        .pw-toggle:hover { color: #475569; }
        .pwd-rule { display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.7rem; font-weight: 500; color: #94a3b8; }
        .pwd-rule.valid { color: #059669; }
        .pwd-rule.invalid { color: #ef4444; }
        .pwd-rule .badge {
            width: 14px; height: 14px; border-radius: 50%; display: inline-flex;
            align-items: center; justify-content: center; font-size: 8px; font-weight: 700;
            background: #e2e8f0; color: #64748b; flex-shrink: 0;
        }
        .pwd-rule.valid .badge { background: #d1fae5; color: #059669; }
        .pwd-rule.invalid .badge { background: #fee2e2; color: #ef4444; }
    </style>
</head>

<body class="bg-slate-50 h-screen w-full overflow-hidden flex">

    @php $appName = app(\App\Services\SettingsService::class)->get('app_name', config('app.name')); @endphp

    {{-- ===== Left Brand Panel ===== --}}
    <div class="hidden lg:flex w-5/12 relative overflow-hidden flex-col justify-between p-12 text-white h-full">
        <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover z-0">
            <source src="https://cdn.coverr.co/videos/coverr-woman-getting-hair-styled-at-salon-5647/1080p.mp4" type="video/mp4">
        </video>
        <div class="absolute inset-0 gradient-overlay z-1"></div>

        <div class="relative z-10">
            <a href="{{ route('login') }}" class="flex items-center gap-2 text-white/80 hover:text-white transition-colors mb-10">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Back to Login
            </a>
            <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center mb-6 border border-white/20">
                <i class="fa-solid fa-key text-xl"></i>
            </div>
            <h1 class="text-4xl font-bold mb-4 leading-tight">Set a new<br>password</h1>
            <p class="text-purple-100 text-base leading-relaxed max-w-sm">
                Choose a strong password to protect your account. Use a mix of letters and numbers.
            </p>
        </div>

        <div class="relative z-10 space-y-3">
            <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm p-3 rounded-xl border border-white/10">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-shield-halved text-sm"></i>
                </div>
                <div>
                    <p class="font-semibold text-sm">Password tips</p>
                    <p class="text-purple-100 text-xs">At least 8 chars with letters & numbers</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm p-3 rounded-xl border border-white/10">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-lock text-sm"></i>
                </div>
                <div>
                    <p class="font-semibold text-sm">Keep it private</p>
                    <p class="text-purple-100 text-xs">Never share your password with anyone</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-xs text-purple-200/60">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </div>
    </div>

    {{-- ===== Right Form Panel ===== --}}
    <div class="w-full lg:w-7/12 flex items-center justify-center p-6 lg:p-12 overflow-y-auto">
        <div class="w-full max-w-md">

            <a href="{{ route('login') }}" class="lg:hidden flex items-center gap-2 text-slate-500 hover:text-purple-600 text-sm mb-6 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i> Back to Login
            </a>

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900">Create new password</h2>
                <p class="mt-1 text-slate-500 text-sm">Must be at least 8 characters with letters and numbers.</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5 flex-shrink-0 text-sm"></i>
                    <ul class="text-xs text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" id="resetForm">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="space-y-5">
                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <div class="input-wrapper">
                            <span class="input-icon-left"><i class="fa-solid fa-envelope"></i></span>
                            <input id="email" type="email" name="email"
                                value="{{ old('email', $request->email) }}"
                                required autofocus autocomplete="username"
                                placeholder="you@example.com"
                                class="rp-input {{ $errors->has('email') ? 'is-error' : '' }}">
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            New Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="input-wrapper">
                            <span class="input-icon-left"><i class="fa-solid fa-lock"></i></span>
                            <input id="password" type="password" name="password"
                                required autocomplete="new-password"
                                placeholder="••••••••"
                                class="rp-input {{ $errors->has('password') ? 'is-error' : '' }}">
                            <button type="button" class="pw-toggle" id="togglePwd" title="Toggle password">
                                <svg id="eyePwd" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror

                        {{-- Password criteria strip --}}
                        <div id="pwdCriteria" class="hidden mt-2 p-2.5 rounded-lg border border-slate-200 bg-slate-50">
                            <div class="flex flex-wrap gap-3">
                                <span id="cLen" class="pwd-rule"><span class="badge">✕</span>8+ characters</span>
                                <span id="cChars" class="pwd-rule"><span class="badge">✕</span>Letters & numbers</span>
                                <span id="cMatch" class="pwd-rule"><span class="badge">✕</span>Passwords match</span>
                            </div>
                        </div>
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Confirm Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="input-wrapper">
                            <span class="input-icon-left"><i class="fa-solid fa-lock"></i></span>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                required autocomplete="new-password"
                                placeholder="••••••••"
                                class="rp-input {{ $errors->has('password_confirmation') ? 'is-error' : '' }}">
                            <button type="button" class="pw-toggle" id="togglePwdConf" title="Toggle confirm password">
                                <svg id="eyeConf" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <div id="confMsg" class="hidden mt-1.5 text-xs flex items-center gap-1"></div>
                        @error('password_confirmation')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg shadow-md text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <i class="fa-solid fa-check text-sm"></i>
                        Reset Password
                    </button>
                </div>
            </form>

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
    (function () {
        const eyeOpen  = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        const eyeSlash = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.253 0 2.426.235 3.521.657m-.943 2.29A3 3 0 1012 15h.01M3 3l18 18"/>`;

        function togglePw(inputId, svgId) {
            const input = document.getElementById(inputId);
            const svg   = document.getElementById(svgId);
            if (!input || !svg) return;
            const isPwd = input.type === 'password';
            input.type = isPwd ? 'text' : 'password';
            svg.innerHTML = isPwd ? eyeSlash : eyeOpen;
        }

        document.getElementById('togglePwd')?.addEventListener('click',     () => togglePw('password', 'eyePwd'));
        document.getElementById('togglePwdConf')?.addEventListener('click',  () => togglePw('password_confirmation', 'eyeConf'));

        // Live password criteria
        const pwdInput  = document.getElementById('password');
        const confInput = document.getElementById('password_confirmation');
        const criteria  = document.getElementById('pwdCriteria');
        const cLen   = document.getElementById('cLen');
        const cChars = document.getElementById('cChars');
        const cMatch = document.getElementById('cMatch');
        const confMsg = document.getElementById('confMsg');

        function updateCriterion(el, passed) {
            if (!el) return;
            const badge = el.querySelector('.badge');
            el.classList.toggle('valid',   passed);
            el.classList.toggle('invalid', !passed);
            if (badge) badge.textContent = passed ? '✓' : '✕';
        }

        function validatePwd() {
            if (!pwdInput) return;
            const val  = pwdInput.value;
            const conf = confInput?.value ?? '';
            const len   = val.length >= 8;
            const chars = /[a-zA-Z]/.test(val) && /[0-9]/.test(val);
            const match = val.length > 0 && val === conf;

            if (val.length > 0) {
                criteria?.classList.remove('hidden');
            }

            updateCriterion(cLen,   len);
            updateCriterion(cChars, chars);
            updateCriterion(cMatch, match);

            // Confirm input border
            if (confInput && conf.length > 0) {
                confInput.classList.toggle('is-success', match);
                confInput.classList.toggle('is-error',   !match);
                if (confMsg) {
                    confMsg.classList.remove('hidden');
                    if (match) {
                        confMsg.className = 'mt-1.5 text-xs flex items-center gap-1 text-emerald-600';
                        confMsg.innerHTML = '<i class="fa-solid fa-circle-check"></i> Passwords match';
                    } else {
                        confMsg.className = 'mt-1.5 text-xs flex items-center gap-1 text-red-600';
                        confMsg.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Passwords do not match';
                    }
                }
            }
        }

        pwdInput?.addEventListener('input',  validatePwd);
        pwdInput?.addEventListener('focus',  () => criteria?.classList.remove('hidden'));
        confInput?.addEventListener('input', validatePwd);

        // Submit spinner
        document.getElementById('resetForm')?.addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-sm"></i> Resetting...';
                btn.classList.add('opacity-75', 'cursor-not-allowed');
            }
        });
    })();
    </script>
</body>
</html>
