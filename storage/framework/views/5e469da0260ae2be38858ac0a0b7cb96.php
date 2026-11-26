<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>Sign In – <?php echo e($appName ?? config('app.name', 'SalonPro')); ?></title>

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

    <style>
        :root {
            --obsidian: #0b0f17;
            --gold: #c5a880;
            --gold-dark: #916f3d;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 0;
        }

        h1, h2, h3 { font-family: 'Outfit', sans-serif; }

        .auth-hero-bg {
            background: radial-gradient(circle at 20% 30%, rgba(197,168,128,0.15) 0%, transparent 50%),
                        linear-gradient(160deg, #07090f 0%, #0b0f17 60%, #111724 100%);
        }

        .auth-input-field:focus {
            border-color: #c5a880 !important;
            box-shadow: 0 0 0 3px rgba(197,168,128,0.15) !important;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col lg:flex-row">

    
    <div class="hidden lg:flex lg:w-5/12 xl:w-[42%] auth-hero-bg flex-col justify-between p-12 xl:p-16 select-none">

        
        <div>
            <a href="<?php echo e(url('/')); ?>" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-gold transition-colors mb-12">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Back to home</span>
            </a>

            <div class="flex items-center gap-3 mb-10">
                <?php if(!empty($appLogo)): ?>
                    <img src="<?php echo e($appLogo); ?>" alt="<?php echo e($appName ?? 'SalonPro'); ?>" class="h-9 w-auto max-w-[180px] object-contain">
                <?php else: ?>
                    <div class="w-9 h-9 rounded-xl bg-gold/20 border border-gold/30 text-gold flex items-center justify-center">
                        <i class="fa-solid fa-scissors text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight"><?php echo e($appName ?? 'SalonPro'); ?></span>
                <?php endif; ?>
            </div>

            <h1 class="text-3xl xl:text-4xl font-bold text-white leading-snug mb-4">
                Manage your salon<br>
                <span class="text-gold">beautifully.</span>
            </h1>
            <p class="text-slate-400 text-sm leading-relaxed max-w-xs">
                Appointments, POS, staff scheduling and client loyalty — all in one place.
            </p>
        </div>

        
        <div class="space-y-5">
            <?php $__currentLoopData = [
                ['fa-calendar-check', 'Smart Scheduling', 'Real-time bookings with zero conflicts.'],
                ['fa-cash-register', 'Integrated POS', 'Fast checkout, tips & commission tracking.'],
                ['fa-fingerprint', 'PIN Staff Access', 'Tablet-ready 6-digit login for front desk.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon, $title, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 text-gold flex items-center justify-center flex-shrink-0 text-xs mt-0.5">
                    <i class="fa-solid <?php echo e($icon); ?>"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-white"><?php echo e($title); ?></p>
                    <p class="text-xs text-slate-400 mt-0.5"><?php echo e($desc); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <p class="text-xs text-slate-500 pt-4 border-t border-white/[0.07]">
                &copy; <?php echo e(date('Y')); ?> <?php echo e($appName ?? 'SalonPro'); ?>. All rights reserved.
            </p>
        </div>
    </div>

    
    <div class="flex-1 min-h-screen flex items-center justify-center p-6 sm:p-10 bg-white">
        <div class="w-full max-w-sm">

            
            <div class="lg:hidden mb-8 text-center">
                <?php if(!empty($appLogo)): ?>
                    <img src="<?php echo e($appLogo); ?>" alt="<?php echo e($appName ?? 'SalonPro'); ?>" class="h-8 w-auto mx-auto">
                <?php else: ?>
                    <span class="text-xl font-bold text-obsidian"><?php echo e($appName ?? 'SalonPro'); ?></span>
                <?php endif; ?>
            </div>

            
            <div class="mb-7">
                <h2 class="text-2xl font-bold text-obsidian tracking-tight">Welcome back</h2>
                <p class="text-sm text-slate-500 mt-1">Sign in to your workspace</p>
            </div>

            
            <?php
                $recognizedDevice = app(\App\Services\TrustedDeviceService::class)->getTrustedDeviceFromRequest(request());
            ?>
            <?php if($recognizedDevice && $recognizedDevice->user && $recognizedDevice->user->devicePin?->pin_enabled): ?>
                <div class="mb-6 flex items-center justify-between p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs">
                    <div class="flex items-center gap-2 text-slate-700 font-medium">
                        <i class="fa-solid fa-mobile-screen-button text-gold"></i>
                        <span>Trusted device — <strong><?php echo e($recognizedDevice->user->name); ?></strong></span>
                    </div>
                    <a href="<?php echo e(route('pin.login')); ?>" class="px-2.5 py-1 bg-obsidian text-gold rounded-lg font-bold text-[11px] hover:bg-gold hover:text-obsidian transition-all">
                        Use PIN
                    </a>
                </div>
            <?php endif; ?>

            
            <?php if(session('status')): ?>
                <div class="mb-5 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span><?php echo e(session('status')); ?></span>
                </div>
            <?php endif; ?>

            <?php if(isset($errors) && $errors->any()): ?>
                <div class="mb-5 p-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-xs flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                    <ul class="space-y-0.5">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            
            <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-4" id="loginForm">
                <?php echo csrf_field(); ?>

                
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-600 mb-1.5">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </span>
                        <input id="email" name="email" type="email" autocomplete="email" required autofocus
                            value="<?php echo e(old('email')); ?>"
                            placeholder="name@business.com"
                            class="auth-input-field w-full pl-10 pr-4 py-2.5 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none transition-all">
                    </div>
                </div>

                
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="text-xs font-semibold text-slate-600">Password</label>
                        <?php if(Route::has('password.request')): ?>
                            <a href="<?php echo e(route('password.request')); ?>" class="text-xs text-gold-dark hover:text-gold transition-colors font-medium">
                                Forgot password?
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            placeholder="Enter your password"
                            class="auth-input-field w-full pl-10 pr-11 py-2.5 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none transition-all">
                        <button type="button" onclick="togglePasswordVisibility('password', this)"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                            aria-label="Toggle password visibility">
                            <i class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-obsidian focus:ring-gold/30 transition">
                    <label for="remember_me" class="ml-2 text-xs text-slate-600 select-none cursor-pointer">Keep me signed in</label>
                </div>

                
                <button type="submit" id="btn-submit"
                    class="auth-btn-primary w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold active:scale-[0.99] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gold/40 focus:ring-offset-2 mt-2">
                    <span>Sign In</span>
                    <i class="fa-solid fa-arrow-right text-xs opacity-80"></i>
                </button>
            </form>

            
            <div class="mt-8 pt-6 border-t border-slate-100 space-y-2 text-center">
                <p class="text-xs text-slate-500">
                    New salon?
                    <a href="<?php echo e(route('saas.register')); ?>" class="font-semibold text-gold-dark hover:text-gold transition-colors ml-1">Start free trial</a>
                </p>
                <p class="text-xs text-slate-500">
                    <a href="<?php echo e(route('pin.login')); ?>" class="text-slate-400 hover:text-slate-600 transition-colors inline-flex items-center gap-1">
                        <i class="fa-solid fa-key text-[10px] text-gold"></i>
                        Switch to PIN sign-in
                    </a>
                </p>
            </div>

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
    </script>

</body>
</html>
<?php /**PATH C:\Ketan\projects\barber\resources\views/auth/login.blade.php ENDPATH**/ ?>