<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - {{ $appName ?? config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ app(\App\Services\SettingsService::class)->getFaviconUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1,h2,h3,h4,h5,h6 { font-family: 'Outfit', sans-serif; }
        .gradient-overlay {
            background: linear-gradient(135deg, rgba(79,70,229,0.92) 0%, rgba(124,58,237,0.88) 100%);
        }
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

    <!-- Left Side - Video & Brand -->
    <div class="hidden lg:flex w-1/2 relative overflow-hidden flex-col justify-between p-12 text-white h-full">
        <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover z-0">
            <source src="https://cdn.coverr.co/videos/coverr-woman-getting-hair-styled-at-salon-5647/1080p.mp4" type="video/mp4">
        </video>
        <div class="absolute inset-0 gradient-overlay z-1"></div>

        <div class="relative z-10">
            <a href="{{ route('login') }}" class="flex items-center gap-2 text-white/80 hover:text-white transition-colors mb-8">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Back to Sign In
            </a>
            <h1 class="text-5xl font-bold mb-4 leading-tight">Create new <br> password</h1>
            <p class="text-lg text-purple-100 max-w-md leading-relaxed">
                Choose a strong password to protect your account. Use at least 8 characters with letters and numbers.
            </p>
        </div>

        <div class="relative z-10 space-y-4">
            <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm p-3.5 rounded-xl border border-white/10">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0 text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm">Strong Security</h3>
                    <p class="text-purple-100 text-xs">Always choose unique, non-dictionary passwords</p>
                </div>
            </div>
            <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm p-3.5 rounded-xl border border-white/10">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0 text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm">Instant Sync</h3>
                    <p class="text-purple-100 text-xs">Takes effect immediately across all sessions</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-xs text-purple-200/60 mt-4">
            &copy; {{ date('Y') }} {{ $appName ?? config('app.name') }}. All rights reserved.
        </div>
    </div>

    <!-- Right Side - Reset Form -->
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
                <span class="text-xs font-semibold text-slate-400">{{ $appName ?? config('app.name') }}</span>
            </div>

            <!-- Header -->
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-purple-50 text-purple-600 mb-3 border border-purple-100 shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900">Set new password</h2>
                <p class="mt-1 text-slate-500 text-sm">Please choose a secure password for your account.</p>
            </div>

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

            <form method="POST" action="{{ route('password.store') }}" class="space-y-4" id="resetForm" autocomplete="off">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-700 mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="username" required
                            value="{{ old('email', $request->email) }}"
                            class="pl-9 block w-full rounded-lg border-slate-200 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5 transition-colors"
                            placeholder="you@example.com">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-medium text-slate-700 mb-1.5">New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            class="pl-9 pr-10 block w-full rounded-lg border-slate-200 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5 transition-colors"
                            placeholder="••••••••">
                        <button type="button" id="togglePwd" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600" title="Toggle password visibility">
                            <svg id="eyePwd" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Live Password Criteria Checklist -->
                    <div id="pwdCriteria" class="hidden mt-2 p-2.5 rounded-lg border border-slate-100 bg-slate-50">
                        <div class="flex flex-wrap gap-2.5 text-xs">
                            <span id="cLen" class="pwd-rule"><span class="badge">✕</span>At least 8 chars</span>
                            <span id="cChars" class="pwd-rule"><span class="badge">✕</span>Letters & numbers</span>
                            <span id="cMatch" class="pwd-rule"><span class="badge">✕</span>Passwords match</span>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                            class="pl-9 pr-10 block w-full rounded-lg border-slate-200 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5 transition-colors"
                            placeholder="••••••••">
                        <button type="button" id="togglePwdConf" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600" title="Toggle password visibility">
                            <svg id="eyeConf" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <div id="confMsg" class="hidden mt-1.5 text-xs flex items-center gap-1"></div>
                </div>

                <button type="submit" id="submitBtn"
                    class="w-full flex items-center justify-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 hover:shadow-purple-500/30 hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Reset Password
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

            if (confInput && conf.length > 0) {
                if (match) {
                    confInput.classList.remove('border-red-400', 'focus:border-red-500', 'focus:ring-red-500');
                    confInput.classList.add('border-emerald-500', 'focus:border-emerald-500', 'focus:ring-emerald-500');
                    if (confMsg) {
                        confMsg.classList.remove('hidden');
                        confMsg.className = 'mt-1.5 text-xs flex items-center gap-1 text-emerald-600';
                        confMsg.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Passwords match';
                    }
                } else {
                    confInput.classList.remove('border-emerald-500', 'focus:border-emerald-500', 'focus:ring-emerald-500');
                    confInput.classList.add('border-red-400', 'focus:border-red-500', 'focus:ring-red-500');
                    if (confMsg) {
                        confMsg.classList.remove('hidden');
                        confMsg.className = 'mt-1.5 text-xs flex items-center gap-1 text-red-600';
                        confMsg.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Passwords do not match';
                    }
                }
            } else if (confMsg) {
                confMsg.classList.add('hidden');
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
                btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg> Resetting password...`;
                btn.classList.add('opacity-80', 'cursor-not-allowed');
            }
        });
    })();
    </script>
</body>
</html>
