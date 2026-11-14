<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('PIN Login') }} - {{ $appName }}</title>

    <!-- Tailwind & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
        }

        /* ── Clean Card with Reduced 12px Radius ── */
        .auth-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            padding: 32px 28px;
        }

        /* ── 6-Digit PIN Boxes with 8px Radius ── */
        .pin-input-group {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 14px 0 18px;
        }

        .pin-digit {
            width: 46px;
            height: 52px;
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            outline: none;
            transition: all 0.15s ease-in-out;
            caret-color: #4f46e5;
            font-family: inherit;
        }

        .pin-digit:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            background: #ffffff;
        }

        .pin-digit.filled {
            border-color: #6366f1;
            background: #f8fafc;
        }

        .pin-digit.is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
            animation: shake 0.35s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        /* ── Button with Reduced 8px Radius ── */
        .btn-gradient-primary {
            width: 100%;
            height: 44px;
            border-radius: 8px;
            background: #4f46e5;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: background 0.15s ease;
            font-family: inherit;
        }

        .btn-gradient-primary:hover {
            background: #4338ca;
        }

        .btn-gradient-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* ── Avatar Badge ── */
        .user-avatar-badge {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
            color: #4f46e5;
            background: #eef2ff;
            border: 2px solid #c7d2fe;
            position: relative;
        }

        .trusted-pill {
            position: absolute;
            bottom: -2px;
            right: -2px;
            background: #10b981;
            color: #ffffff;
            border: 2px solid #ffffff;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .visually-hidden {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
    </style>
</head>

<body class="min-h-screen w-full flex items-center justify-center p-4">

    <div class="w-full max-w-md mx-auto">

        <!-- Top Salon Logo -->
        <div class="text-center mb-5">
            @if(!empty($appLogo))
                <a href="{{ url('/') }}" class="inline-block">
                    <img src="{{ $appLogo }}" alt="{{ $appName }}" class="h-9 mx-auto object-contain" onerror="this.style.display='none'; document.getElementById('brandTextFallback').style.display='block';">
                </a>
                <span id="brandTextFallback" style="display: none;" class="text-lg font-bold text-slate-800 tracking-tight">{{ $appName }}</span>
            @else
                <a href="{{ url('/') }}" class="text-lg font-bold text-slate-800 tracking-tight hover:text-indigo-600 transition-colors">
                    {{ $appName }}
                </a>
            @endif
        </div>

        <!-- Main Card with 12px radius -->
        <div class="auth-card">

            <!-- User Recognition Profile Header -->
            <div class="text-center mb-4">
                <div class="user-avatar-badge">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                    <div class="trusted-pill" title="Verified Trusted Device">
                        <i class="fas fa-check" style="font-size: 8px;"></i>
                    </div>
                </div>

                <h2 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 2px;">{{ $user->name }}</h2>
                <div style="font-size: 12.5px; color: #64748b; margin-bottom: 8px;">{{ $maskedEmail }}</div>

                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 500; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;">
                    <i class="fas fa-laptop" style="font-size: 11px; color: #64748b;"></i>
                    <span>{{ $device->device_name }}</span>
                </div>

                <div style="margin-top: 8px; font-size: 11.5px; color: #64748b;">
                    Not you? <a href="{{ route('pin.switch') }}" style="color: #4f46e5; font-weight: 600; text-decoration: underline;">Sign into another account</a>
                </div>
            </div>

            <!-- Lockout Alert Banner with 8px radius -->
            <div id="lockoutBanner" style="display: {{ $isLocked ? 'block' : 'none' }}; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 12px; margin-bottom: 16px;">
                <div style="display: flex; align-items: flex-start; gap: 8px;">
                    <i class="fas fa-lock text-danger" style="margin-top: 2px;"></i>
                    <div>
                        <div style="font-weight: 600; color: #991b1b; font-size: 12.5px;">Device Temporarily Locked</div>
                        <div id="lockoutMessage" style="font-size: 11.5px; color: #b91c1c; margin-top: 1px;">
                            Too many incorrect PIN attempts. Try again in <strong id="lockoutTimer">--:--</strong> or sign in with your password.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generic Inline Error Message -->
            <div id="pinErrorAlert" style="display: {{ (!empty($errors) && $errors->has('pin')) ? 'block' : 'none' }}; background: #fef2f2; border: 1px solid #fee2e2; border-radius: 8px; padding: 8px 12px; margin-bottom: 14px; font-size: 12px; color: #b91c1c; text-align: center;">
                {{ (!empty($errors) && $errors->has('pin')) ? $errors->first('pin') : '' }}
            </div>

            <!-- PIN Form -->
            <form id="pinLoginForm" method="POST" action="{{ route('pin.login.submit') }}" autocomplete="off">
                @csrf
                <input type="hidden" name="pin" id="fullPinInput" value="">

                <label id="pinGroupLabel" for="pinDigit0" style="display: block; text-align: center; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                    Enter 6-Digit PIN
                </label>

                <!-- 6 Separate Input Boxes with 8px radius (Clean Centered Group) -->
                <div class="pin-input-group" id="pinInputGroup" role="group" aria-labelledby="pinGroupLabel">
                    @for($i = 0; $i < 6; $i++)
                        <input type="password"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               maxlength="1"
                               class="pin-digit"
                               id="pinDigit{{ $i }}"
                               aria-label="PIN digit {{ $i + 1 }} of 6"
                               data-index="{{ $i }}"
                               autocomplete="off"
                               {{ $isLocked ? 'disabled' : '' }}>
                    @endfor
                </div>

                <!-- Submit Button with 8px radius -->
                <div class="mt-4">
                    <button type="submit" id="btnUnlock" class="btn-gradient-primary" {{ $isLocked ? 'disabled' : '' }}>
                        <i class="fas fa-unlock-alt" style="font-size: 13px;"></i>
                        <span id="btnText">Unlock & Sign In</span>
                        <div id="btnSpinner" class="spinner" style="display: none;"></div>
                    </button>
                </div>
            </form>

            <!-- Action Links -->
            <div class="mt-5 pt-4" style="border-top: 1px solid #f1f5f9; text-align: center;">
                <div class="flex flex-col gap-2">
                    <a href="{{ route('login', ['mode' => 'password']) }}"
                       class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors inline-flex items-center justify-center gap-1.5">
                        <i class="fas fa-key" style="font-size: 11px;"></i>
                        Use Password Instead
                    </a>

                    <div class="flex items-center justify-center gap-3 text-xs text-slate-500 pt-1">
                        <a href="{{ route('password.request') }}" class="hover:text-slate-800 transition-colors">
                            Forgot PIN?
                        </a>
                        <span>•</span>
                        <a href="{{ route('pin.switch') }}" class="hover:text-slate-800 transition-colors">
                            Switch Account
                        </a>
                        <span>•</span>
                        <a href="{{ route('pin.switch', ['forget_device' => 1]) }}"
                           class="text-rose-500 hover:text-rose-700 transition-colors"
                           onclick="return confirm('Forget this browser as a trusted device? You will need your password to log in again.');">
                            Forget Device
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer Help -->
        <div class="text-center mt-5 text-xs text-slate-500">
            Protected by multi-tenant device recognition & Argon2id encryption.
        </div>

    </div>

    <!-- JavaScript for 6-Digit Auto-Advance, Paste, and AJAX/Lockout -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const digits = Array.from(document.querySelectorAll('.pin-digit'));
            const fullPinInput = document.getElementById('fullPinInput');
            const pinForm = document.getElementById('pinLoginForm');
            const btnUnlock = document.getElementById('btnUnlock');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            const pinErrorAlert = document.getElementById('pinErrorAlert');
            const lockoutBanner = document.getElementById('lockoutBanner');
            const lockoutTimer = document.getElementById('lockoutTimer');

            let isLocked = @json($isLocked);
            let lockoutSeconds = @json($lockoutSeconds);

            if (isLocked && lockoutSeconds > 0) {
                startLockoutCountdown(lockoutSeconds);
            }

            function startLockoutCountdown(seconds) {
                let remaining = seconds;
                updateTimerDisplay(remaining);

                const interval = setInterval(() => {
                    remaining--;
                    if (remaining <= 0) {
                        clearInterval(interval);
                        lockoutBanner.style.display = 'none';
                        digits.forEach(d => d.removeAttribute('disabled'));
                        btnUnlock.removeAttribute('disabled');
                        digits[0].focus();
                    } else {
                        updateTimerDisplay(remaining);
                    }
                }, 1000);
            }

            function updateTimerDisplay(totalSeconds) {
                const mins = Math.floor(totalSeconds / 60);
                const secs = totalSeconds % 60;
                lockoutTimer.textContent = `${mins}:${secs < 10 ? '0' : ''}${secs}`;
            }

            if (!isLocked && digits.length > 0) {
                setTimeout(() => digits[0].focus(), 150);
            }

            function updateFullPin() {
                const pin = digits.map(d => d.value).join('');
                fullPinInput.value = pin;
                if (pin.length === 6) {
                    btnUnlock.disabled = false;
                    submitPin();
                }
            }

            digits.forEach((digit, index) => {
                digit.addEventListener('input', function () {
                    const val = this.value.replace(/[^0-9]/g, '');
                    this.value = val ? val.slice(-1) : '';

                    if (this.value) {
                        this.classList.add('filled');
                        this.classList.remove('is-invalid');
                        if (index < 5) {
                            digits[index + 1].focus();
                        }
                    } else {
                        this.classList.remove('filled');
                    }
                    updateFullPin();
                });

                digit.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace') {
                        if (!this.value && index > 0) {
                            digits[index - 1].focus();
                            digits[index - 1].value = '';
                            digits[index - 1].classList.remove('filled');
                            e.preventDefault();
                        }
                    } else if (e.key === 'ArrowLeft' && index > 0) {
                        digits[index - 1].focus();
                        e.preventDefault();
                    } else if (e.key === 'ArrowRight' && index < 5) {
                        digits[index + 1].focus();
                        e.preventDefault();
                    }
                });

                digit.addEventListener('paste', function (e) {
                    e.preventDefault();
                    const pasteData = (e.clipboardData || window.clipboardData).getData('text');
                    const cleanDigits = pasteData.replace(/[^0-9]/g, '').slice(0, 6);

                    cleanDigits.split('').forEach((char, i) => {
                        if (digits[i]) {
                            digits[i].value = char;
                            digits[i].classList.add('filled');
                        }
                    });

                    if (cleanDigits.length < 6 && digits[cleanDigits.length]) {
                        digits[cleanDigits.length].focus();
                    } else if (digits[5]) {
                        digits[5].focus();
                    }

                    updateFullPin();
                });
            });

            pinForm.addEventListener('submit', function (e) {
                e.preventDefault();
                submitPin();
            });

            let isSubmitting = false;
            function submitPin() {
                const pin = digits.map(d => d.value).join('');
                if (pin.length !== 6 || isSubmitting) return;

                isSubmitting = true;
                btnUnlock.disabled = true;
                btnText.textContent = 'Verifying...';
                btnSpinner.style.display = 'inline-block';
                pinErrorAlert.style.display = 'none';

                fetch(pinForm.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ pin: pin })
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (response.ok && data.success) {
                        btnText.textContent = 'Unlocked!';
                        window.location.href = data.redirect || '{{ route("home.dashboard") }}';
                    } else {
                        isSubmitting = false;
                        btnUnlock.disabled = false;
                        btnText.textContent = 'Unlock & Sign In';
                        btnSpinner.style.display = 'none';

                        // Display accurate error from server (error, validation errors, message, or fallback)
                        const errorMsg = data.error
                            || (data.errors ? Object.values(data.errors).flat().join(' ') : null)
                            || data.message
                            || 'Authentication failed. Please check your PIN.';

                        pinErrorAlert.textContent = errorMsg;
                        pinErrorAlert.style.display = 'block';

                        digits.forEach(d => {
                            d.classList.add('is-invalid');
                            d.value = '';
                            d.classList.remove('filled');
                        });
                        fullPinInput.value = '';

                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1800);
                            return;
                        }

                        if (data.locked && data.seconds_remaining) {
                            lockoutBanner.style.display = 'block';
                            digits.forEach(d => d.setAttribute('disabled', 'disabled'));
                            btnUnlock.disabled = true;
                            startLockoutCountdown(data.seconds_remaining);
                        } else {
                            setTimeout(() => {
                                digits.forEach(d => d.classList.remove('is-invalid'));
                                digits[0].focus();
                            }, 500);
                        }
                    }
                })
                .catch(() => {
                    isSubmitting = false;
                    btnUnlock.disabled = false;
                    btnText.textContent = 'Unlock & Sign In';
                    btnSpinner.style.display = 'none';
                    pinErrorAlert.textContent = 'An unexpected network error occurred.';
                    pinErrorAlert.style.display = 'block';
                });
            }
        });
    </script>
</body>

</html>
