<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(__('PIN Login')); ?> – <?php echo e($appName); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e($faviconUrl ?? app(\App\Services\SettingsService::class)->getFaviconUrl()); ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Compiled Assets via Vite -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js']); ?>
</head>

<body class="min-h-screen w-full flex items-center justify-center p-4 bg-slate-50 text-slate-800">

    <div class="w-full max-w-md mx-auto py-6">

        <!-- Top Salon Logo -->
        <div class="text-center mb-6">
            <?php if(!empty($appLogo)): ?>
                <a href="<?php echo e(url('/')); ?>" class="inline-block">
                    <img src="<?php echo e($appLogo); ?>" alt="<?php echo e($appName); ?>" class="h-9 mx-auto object-contain" onerror="this.style.display='none'; document.getElementById('brandTextFallback').style.display='block';">
                </a>
                <span id="brandTextFallback" style="display: none;" class="text-xl font-bold text-obsidian tracking-tight"><?php echo e($appName); ?></span>
            <?php else: ?>
                <a href="<?php echo e(url('/')); ?>" class="text-xl font-bold text-obsidian tracking-tight hover:text-gold transition-colors">
                    <?php echo e($appName); ?>

                </a>
            <?php endif; ?>
        </div>

        <!-- Main Card with clean modern shadow -->
        <div class="auth-card">

            <!-- User Recognition Profile Header -->
            <div class="text-center mb-5">
                <div class="user-avatar-badge">
                    <?php if($user->avatar): ?>
                        <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="<?php echo e($user->name); ?>" class="w-full h-full object-cover rounded-full">
                    <?php else: ?>
                        <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                    <?php endif; ?>
                    <div class="trusted-pill" title="Verified Trusted Device">
                        <i class="fas fa-check text-[9px]"></i>
                    </div>
                </div>

                <h2 class="text-xl font-bold text-obsidian tracking-tight mb-0.5"><?php echo e($user->name); ?></h2>
                <div class="text-xs text-slate-500 mb-2"><?php echo e($maskedEmail); ?></div>

                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                    <i class="fas fa-laptop text-slate-400 text-xs"></i>
                    <span><?php echo e($device->device_name); ?></span>
                </div>

                <div class="mt-2 text-xs text-slate-500">
                    Not you? <a href="<?php echo e(route('pin.switch')); ?>" class="text-gold-dark hover:text-gold font-semibold underline ml-1">Sign into another account</a>
                </div>
            </div>

            <!-- Lockout Alert Banner -->
            <div id="lockoutBanner" style="display: <?php echo e($isLocked ? 'block' : 'none'); ?>;" class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-800">
                <div class="flex items-start gap-2">
                    <i class="fas fa-lock text-red-500 mt-0.5"></i>
                    <div>
                        <div class="font-bold text-red-900">Device Temporarily Locked</div>
                        <div id="lockoutMessage" class="text-red-700 mt-0.5">
                            Too many incorrect PIN attempts. Try again in <strong id="lockoutTimer">--:--</strong> or sign in with your password.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generic Inline Error Message -->
            <div id="pinErrorAlert" style="display: <?php echo e((!empty($errors) && $errors->has('pin')) ? 'block' : 'none'); ?>;" class="mb-4 p-2.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 text-center font-medium">
                <?php echo e((!empty($errors) && $errors->has('pin')) ? $errors->first('pin') : ''); ?>

            </div>

            <!-- PIN Form -->
            <form id="pinLoginForm" method="POST" action="<?php echo e(route('pin.login.submit')); ?>" autocomplete="off">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="pin" id="fullPinInput" value="">

                <label id="pinGroupLabel" for="pinDigit0" class="block text-center text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2">
                    Enter 6-Digit PIN
                </label>

                <!-- 6 Separate Input Boxes -->
                <div class="pin-input-group" id="pinInputGroup" role="group" aria-labelledby="pinGroupLabel">
                    <?php for($i = 0; $i < 6; $i++): ?>
                        <input type="password"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               maxlength="1"
                               class="pin-digit"
                               id="pinDigit<?php echo e($i); ?>"
                               aria-label="PIN digit <?php echo e($i + 1); ?> of 6"
                               data-index="<?php echo e($i); ?>"
                               autocomplete="off"
                               <?php echo e($isLocked ? 'disabled' : ''); ?>>
                    <?php endfor; ?>
                </div>

                <!-- Submit Button -->
                <div class="mt-5">
                    <button type="submit" id="btnUnlock"
                        class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-obsidian border border-obsidian hover:bg-gold hover:border-gold hover:text-obsidian active:scale-[0.99] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gold/40 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        <?php echo e($isLocked ? 'disabled' : ''); ?>>
                        <i class="fas fa-unlock-alt text-xs"></i>
                        <span id="btnText">Unlock &amp; Sign In</span>
                        <div id="btnSpinner" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin ml-2 hidden"></div>
                    </button>
                </div>
            </form>

            <!-- Action Links -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <div class="flex flex-col gap-2.5">
                    <a href="<?php echo e(route('login', ['mode' => 'password'])); ?>"
                       class="text-xs font-semibold text-slate-700 hover:text-gold transition-colors inline-flex items-center justify-center gap-1.5">
                        <i class="fas fa-key text-[11px] text-gold"></i>
                        Use Password Instead
                    </a>

                    <div class="flex items-center justify-center gap-3 text-xs text-slate-500 pt-1">
                        <a href="<?php echo e(route('password.request')); ?>" class="hover:text-slate-800 transition-colors">
                            Forgot PIN?
                        </a>
                        <span>&bull;</span>
                        <a href="<?php echo e(route('pin.switch')); ?>" class="hover:text-slate-800 transition-colors">
                            Switch Account
                        </a>
                        <span>&bull;</span>
                        <a href="<?php echo e(route('pin.switch', ['forget_device' => 1])); ?>"
                           class="text-rose-500 hover:text-rose-700 transition-colors"
                           onclick="return confirm('Forget this browser as a trusted device? You will need your password to log in again.');">
                            Forget Device
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer Help -->
        <div class="text-center mt-5 text-xs text-slate-400">
            Protected by multi-tenant device recognition &amp; Argon2id encryption.
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

            let isLocked = <?php echo json_encode($isLocked, 15, 512) ?>;
            let lockoutSeconds = <?php echo json_encode($lockoutSeconds, 15, 512) ?>;

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
                btnSpinner.classList.remove('hidden');
                pinErrorAlert.style.display = 'none';

                fetch(pinForm.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ pin: pin })
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (response.ok && data.success) {
                        btnText.textContent = 'Unlocked!';
                        window.location.href = data.redirect || '<?php echo e(route("home.dashboard")); ?>';
                    } else {
                        isSubmitting = false;
                        btnUnlock.disabled = false;
                        btnText.textContent = 'Unlock & Sign In';
                        btnSpinner.classList.add('hidden');

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
                    btnSpinner.classList.add('hidden');
                    pinErrorAlert.textContent = 'An unexpected network error occurred.';
                    pinErrorAlert.style.display = 'block';
                });
            }
        });
    </script>
</body>

</html>
<?php /**PATH C:\Ketan\projects\barber\resources\views/auth/pin-login.blade.php ENDPATH**/ ?>