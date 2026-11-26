<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>Enable Quick PIN Login – <?php echo e($appName ?? config('app.name', 'SalonPro')); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(app(\App\Services\SettingsService::class)->getFaviconUrl()); ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Compiled Assets via Vite -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js']); ?>

    <style>
        .req-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            transition: all 0.15s ease;
        }
        .req-chip.active {
            background: #f0fdf4;
            color: #15803d;
            border-color: #bbf7d0;
        }
    </style>
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

        <!-- Main Card -->
        <div class="auth-card">

            <!-- Header Icon & Title -->
            <div class="text-center mb-5">
                <div class="w-12 h-12 rounded-xl bg-gold/15 text-gold-dark border border-gold/30 flex items-center justify-center mx-auto mb-3 text-lg">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                </div>
                <h2 class="text-xl font-bold text-obsidian tracking-tight mb-1">Enable 6-Digit PIN</h2>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Set up a fast 6-digit PIN on this device for one-tap sign-ins at the front desk or styling station.
                </p>
            </div>

            <!-- Device Trust Badge -->
            <div class="mb-5 p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 text-slate-700">
                    <i class="fa-solid fa-laptop text-slate-400"></i>
                    <span>Device: <strong><?php echo e($deviceName ?? $deviceInfo['default_name'] ?? 'This Device'); ?></strong></span>
                </div>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <i class="fa-solid fa-check text-[9px]"></i>
                    Verified
                </span>
            </div>

            <?php if($errors->any()): ?>
                <div class="mb-5 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-800 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                    <ul class="space-y-0.5">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('pin.setup.complete')); ?>" autocomplete="off" class="space-y-4">
                <?php echo csrf_field(); ?>

                <!-- PIN Input -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="pin" class="block text-xs font-semibold text-slate-600">Choose 6-Digit PIN</label>
                        <span id="pinLengthBadge" class="text-[11px] font-semibold text-slate-400">0/6 digits</span>
                    </div>
                    <div class="relative">
                        <input id="pin" name="pin" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autofocus
                            placeholder="••••••"
                            class="auth-input-field w-full py-2.5 px-4 text-center text-lg font-bold tracking-widest text-slate-900 bg-white border border-slate-200 rounded-xl placeholder-slate-300 focus:outline-none transition-all">
                        <button type="button" onclick="togglePinVisibility('pin', this)"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                            aria-label="Toggle PIN visibility">
                            <i class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>

                    <!-- Security Chips -->
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <span id="chipLength" class="req-chip">6 digits</span>
                        <span id="chipSeq" class="req-chip">No simple sequence</span>
                        <span id="chipRepeat" class="req-chip">No repeating numbers</span>
                    </div>
                </div>

                <!-- Confirm PIN -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="pin_confirmation" class="block text-xs font-semibold text-slate-600">Confirm 6-Digit PIN</label>
                        <span id="matchStatusText" class="text-[11px] font-semibold"></span>
                    </div>
                    <div class="relative">
                        <input id="pin_confirmation" name="pin_confirmation" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="6" required
                            placeholder="••••••"
                            class="auth-input-field w-full py-2.5 px-4 text-center text-lg font-bold tracking-widest text-slate-900 bg-white border border-slate-200 rounded-xl placeholder-slate-300 focus:outline-none transition-all">
                        <button type="button" onclick="togglePinVisibility('pin_confirmation', this)"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                            aria-label="Toggle PIN visibility">
                            <i class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-obsidian border border-obsidian hover:bg-gold hover:border-gold hover:text-obsidian active:scale-[0.99] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gold/40 focus:ring-offset-2 mt-2">
                    <i class="fa-solid fa-shield-halved text-xs"></i>
                    <span>Enable PIN &amp; Trust Device</span>
                </button>
            </form>

            <!-- Subtle Skip Action -->
            <div class="mt-5 text-center">
                <form method="POST" action="<?php echo e(route('pin.setup.skip')); ?>" class="inline-block">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-xs font-medium text-slate-500 hover:text-slate-800 transition-colors inline-flex items-center gap-1">
                        <span>Skip for now</span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                </form>
            </div>

        </div>

        <!-- Privacy Note -->
        <div class="text-center mt-5 text-xs text-slate-400">
            Encrypted with Argon2id &amp; multi-tenant isolation.
        </div>

    </div>

    <script>
        function togglePinVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash text-sm text-gold-dark';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye text-sm';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const pinInput = document.getElementById('pin');
            const confirmInput = document.getElementById('pin_confirmation');
            const pinLengthBadge = document.getElementById('pinLengthBadge');
            const matchStatusText = document.getElementById('matchStatusText');
            const chipLength = document.getElementById('chipLength');
            const chipSeq = document.getElementById('chipSeq');
            const chipRepeat = document.getElementById('chipRepeat');

            [pinInput, confirmInput].forEach(inp => {
                inp.addEventListener('input', function () {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
                    validateLive();
                });
            });

            function validateLive() {
                const pin = pinInput.value;
                const confirm = confirmInput.value;

                pinLengthBadge.textContent = `${pin.length}/6 digits`;

                const has6 = pin.length === 6;
                chipLength.className = has6 ? 'req-chip active' : 'req-chip';

                const seq = ['012345','123456','234567','345678','456789','543210','654321','765432','876543','987654'];
                const isSeq = seq.includes(pin);
                chipSeq.className = (pin.length > 2 && !isSeq) ? 'req-chip active' : 'req-chip';

                const isRepeat = /^([0-9])\1{5}$/.test(pin);
                chipRepeat.className = (pin.length > 2 && !isRepeat) ? 'req-chip active' : 'req-chip';

                if (confirm.length > 0) {
                    if (pin === confirm && has6) {
                        matchStatusText.textContent = '✓ PINs Match';
                        matchStatusText.className = 'text-[11px] font-semibold text-emerald-600';
                    } else if (confirm.length === 6 && pin !== confirm) {
                        matchStatusText.textContent = '✗ PINs do not match';
                        matchStatusText.className = 'text-[11px] font-semibold text-rose-600';
                    } else {
                        matchStatusText.textContent = '';
                    }
                } else {
                    matchStatusText.textContent = '';
                }
            }

            setTimeout(() => pinInput.focus(), 150);
        });
    </script>

</body>
</html>
<?php /**PATH C:\Ketan\projects\barber\resources\views/auth/pin-setup-prompt.blade.php ENDPATH**/ ?>