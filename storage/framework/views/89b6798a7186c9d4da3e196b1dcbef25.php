<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - <?php echo e($appName); ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(app(\App\Services\SettingsService::class)->getFaviconUrl()); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Outfit', sans-serif;
        }

        .gradient-overlay {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.9) 0%, rgba(124, 58, 237, 0.8) 100%);
        }
    </style>
</head>

<body class="bg-slate-50 h-screen w-full overflow-hidden flex">

    <!-- Left Side - Video & Brand -->
    <div class="hidden lg:flex w-1/2 relative overflow-hidden flex-col justify-between p-12 text-white h-full">
        <!-- Video Background -->
        <video autoplay muted loop playsinline class="absolute top-0 left-0 w-full h-full object-cover z-0">
            <source src="https://cdn.coverr.co/videos/coverr-woman-getting-hair-styled-at-salon-5647/1080p.mp4"
                type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- Gradient Overlay -->
        <div class="absolute top-0 left-0 w-full h-full gradient-overlay z-1"></div>

        <!-- Content -->
        <div class="relative z-10">
            <a href="<?php echo e(url('/')); ?>"
                class="flex items-center gap-2 text-white/80 hover:text-white transition-colors mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Back to Home
            </a>

            <h1 class="text-5xl font-bold mb-4 leading-tight">
                Welcome to <br> <?php echo e($appName); ?>

            </h1>
            <p class="text-lg text-purple-100 max-w-md leading-relaxed">
                Your all-in-one solution for modern salon management.
            </p>
        </div>

        <div class="relative z-10 space-y-4">
            <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm p-3 rounded-xl border border-white/10">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-base">Smart Scheduling</h3>
                    <p class="text-purple-100 text-xs">Automated booking & reminders</p>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm p-3 rounded-xl border border-white/10">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-base">Staff Management</h3>
                    <p class="text-purple-100 text-xs">Track performance & commissions</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-xs text-purple-200/60 mt-4">
            © <?php echo e(date('Y')); ?> <?php echo e($appName); ?>. All rights reserved.
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 bg-white overflow-y-auto">
        <div class="w-full max-w-sm space-y-6">
            <div class="text-center lg:text-left">
                <h2 class="text-2xl font-bold text-slate-900">Welcome Back</h2>
                <p class="mt-1 text-slate-600 text-sm">Please sign in to your account</p>
            </div>

            <?php if($errors->any()): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-r-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-4 w-4 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <ul class="list-disc list-inside text-xs text-red-700">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-4" autocomplete="off">
                <?php echo csrf_field(); ?>

                <div>
                    <label for="email" class="block text-xs font-medium text-slate-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="pl-9 block w-full rounded-lg border-slate-200 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5 transition-colors"
                            placeholder="you@example.com" value="<?php echo e(old('email')); ?>">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="pl-9 pr-10 block w-full rounded-lg border-slate-200 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5 transition-colors"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePasswordVisibility('password', this)"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox"
                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-slate-300 rounded">
                        <label for="remember_me" class="ml-2 block text-xs text-slate-600">Remember me</label>
                    </div>

                    <?php if(Route::has('password.request')): ?>
                        <div class="text-xs">
                            <a href="<?php echo e(route('password.request')); ?>"
                                class="font-medium text-purple-600 hover:text-purple-500 transition-colors">
                                Forgot password?
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 hover:shadow-purple-500/30 hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Sign in
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-2 bg-white text-slate-500">New to <?php echo e($appName); ?>?</span>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="<?php echo e(route('saas.register')); ?>"
                        class="font-medium text-purple-600 hover:text-purple-500 transition-colors text-sm">
                        Register your salon
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const svg = button.querySelector('svg');
            if (input.type === 'password') {
                input.type = 'text';
                svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.253 0 2.426.235 3.521.657m-.943 2.29A3 3 0 1012 15h.01M3 3l18 18" />';
            } else {
                input.type = 'password';
                svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        }

        // Auto-fill for demo purposes if needed
        function fillCredentials(email, password) {
            document.querySelector('input[name="email"]').value = email;
            document.querySelector('input[name="password"]').value = password;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.querySelector('input[name="email"]');
            const passwordInput = document.querySelector('input[name="password"]');
            const rememberCheckbox = document.getElementById('remember_me');
            const loginForm = document.querySelector('form');

            // Load saved credentials
            if (localStorage.getItem('remember_me') === 'true') {
                if (emailInput) emailInput.value = localStorage.getItem('remember_email') || '';
                if (passwordInput) passwordInput.value = localStorage.getItem('remember_password') || '';
                if (rememberCheckbox) rememberCheckbox.checked = true;
            }

            // Save credentials on submit
            if (loginForm) {
                loginForm.addEventListener('submit', function() {
                    if (rememberCheckbox && rememberCheckbox.checked) {
                        localStorage.setItem('remember_me', 'true');
                        if (emailInput) localStorage.setItem('remember_email', emailInput.value);
                        if (passwordInput) localStorage.setItem('remember_password', passwordInput.value);
                    } else {
                        localStorage.removeItem('remember_me');
                        localStorage.removeItem('remember_email');
                        localStorage.removeItem('remember_password');
                    }
                });
            }
        });
    </script>
</body>

</html><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\auth\login.blade.php ENDPATH**/ ?>