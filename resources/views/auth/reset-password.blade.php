<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - {{ $appName ?? config('app.name') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ app(\App\Services\SettingsService::class)->getFaviconUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            padding: 10px 42px 10px 42px;
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

        .auth-field-input.has-success {
            border-color: #10b981;
            background-color: #f0fdf4;
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

        .btn-gradient-primary:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none;
        }

        /* ── Password criteria badges ── */
        .pwd-rule {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 500;
            color: #94a3b8;
            transition: color 0.15s;
        }

        .pwd-rule.valid { color: #059669; }
        .pwd-rule.invalid { color: #ef4444; }

        .pwd-rule .badge {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            font-weight: 700;
            background: #e2e8f0;
            color: #64748b;
            flex-shrink: 0;
        }

        .pwd-rule.valid .badge { background: #d1fae5; color: #059669; }
        .pwd-rule.invalid .badge { background: #fee2e2; color: #ef4444; }

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

            <!-- Header -->
            <div style="text-align: center; margin-bottom: 26px;">
                <div style="width: 52px; height: 52px; border-radius: 16px; background: #f5f3ff; border: 1.5px solid #ede9fe; display: inline-flex; align-items: center; justify-content: center; color: #7c3aed; margin-bottom: 16px; box-shadow: 0 4px 12px -2px rgba(124, 58, 237, 0.12);">
                    <svg class="icon-fixed" style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0 0 8px;">Create new password</h2>
                <p style="font-size: 14px; color: #64748b; line-height: 1.5; margin: 0;">
                    Please choose a strong, secure password for your account.
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

            <form method="POST" action="{{ route('password.store') }}" id="resetForm" autocomplete="off">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div style="margin-bottom: 16px;">
                    <label for="email" style="display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 7px;">
                        Email Address
                    </label>
                    <div style="position: relative;">
                        <div style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                            <svg class="icon-fixed" style="width: 18px; height: 18px;" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="username" required
                            value="{{ old('email', $request->email) }}"
                            class="auth-field-input"
                            placeholder="you@example.com">
                    </div>
                </div>

                <!-- Password -->
                <div style="margin-bottom: 16px;">
                    <label for="password" style="display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 7px;">
                        New Password <span style="color: #ef4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <div style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                            <svg class="icon-fixed" style="width: 18px; height: 18px;" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            class="auth-field-input"
                            placeholder="••••••••">
                        <button type="button" id="togglePwd" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 4px; cursor: pointer; color: #94a3b8;" title="Toggle password visibility">
                            <svg id="eyePwd" class="icon-fixed" style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Live Password Criteria Checklist -->
                    <div id="pwdCriteria" style="display: none; margin-top: 8px; padding: 10px 12px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <span id="cLen" class="pwd-rule"><span class="badge">✕</span>At least 8 chars</span>
                            <span id="cChars" class="pwd-rule"><span class="badge">✕</span>Letters & numbers</span>
                            <span id="cMatch" class="pwd-rule"><span class="badge">✕</span>Passwords match</span>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div style="margin-bottom: 22px;">
                    <label for="password_confirmation" style="display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 7px;">
                        Confirm New Password <span style="color: #ef4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <div style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                            <svg class="icon-fixed" style="width: 18px; height: 18px;" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                            class="auth-field-input"
                            placeholder="••••••••">
                        <button type="button" id="togglePwdConf" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 4px; cursor: pointer; color: #94a3b8;" title="Toggle password visibility">
                            <svg id="eyeConf" class="icon-fixed" style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <div id="confMsg" style="display: none; margin-top: 6px; font-size: 12px; align-items: center; gap: 4px;"></div>
                </div>

                <button type="submit" id="submitBtn" class="btn-gradient-primary">
                    <svg class="icon-fixed" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Reset Password
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

        </div>

        <!-- Footer Note -->
        <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #94a3b8;">
            &copy; {{ date('Y') }} {{ $appName ?? config('app.name') }}. All rights reserved.
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
                criteria.style.display = 'block';
            }

            updateCriterion(cLen,   len);
            updateCriterion(cChars, chars);
            updateCriterion(cMatch, match);

            if (confInput && conf.length > 0) {
                confMsg.style.display = 'flex';
                if (match) {
                    confInput.classList.remove('has-error');
                    confInput.classList.add('has-success');
                    confMsg.style.color = '#059669';
                    confMsg.innerHTML = '<svg class="icon-fixed" style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Passwords match';
                } else {
                    confInput.classList.remove('has-success');
                    confInput.classList.add('has-error');
                    confMsg.style.color = '#ef4444';
                    confMsg.innerHTML = '<svg class="icon-fixed" style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Passwords do not match';
                }
            } else if (confMsg) {
                confMsg.style.display = 'none';
            }
        }

        pwdInput?.addEventListener('input',  validatePwd);
        pwdInput?.addEventListener('focus',  () => criteria.style.display = 'block');
        confInput?.addEventListener('input', validatePwd);

        // Submit spinner
        document.getElementById('resetForm')?.addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<svg class="icon-fixed" style="width: 16px; height: 16px; animation: spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg> Resetting password...`;
            }
        });
    })();
    </script>
</body>
</html>
