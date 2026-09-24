<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - SalonPro</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(app(\App\Services\SettingsService::class)->getFaviconUrl()); ?>">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/auth.css']); ?>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- International Tel Input CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
    <style>
        .iti {
            width: 100%;
            margin-bottom: 0.75rem;
        }

        .iti__flag-container {
            z-index: 2;
        }

        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #334155;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <!-- Left Side -->
        <div class="auth-left">
            <a href="<?php echo e(url('/')); ?>" class="back-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Back to Home
            </a>
            <div class="auth-left-content">
                <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; line-height: 1.2;">Join SalonPro
                    Today</h1>
                <p style="font-size: 1rem; opacity: 0.9; margin-bottom: 2rem; line-height: 1.5;">Create your account and
                    start managing your salon experience.</p>

                <div style="margin-bottom: 2rem;">
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <span>Easy Appointment Booking</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <span>Personalized Service History</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <span>Exclusive Member Benefits</span>
                    </div>
                </div>

                <div style="display: flex; flex-wrap: wrap; gap: 0.375rem;">
                    <span class="role-badge">Customer</span>
                    <span class="role-badge">Member</span>
                    <span class="role-badge">VIP</span>
                </div>
            </div>
        </div>

        <!-- Right Side -->
        <div class="auth-right">
            <div class="auth-card">
                <h2 class="auth-header">Create Account</h2>
                <p style="text-align: center; color: #64748b; margin-bottom: 1.25rem; font-size: 0.875rem;">Join our
                    beauty community</p>

                <?php if($errors->any()): ?>
                    <div
                        style="background: #fef2f2; color: #b91c1c; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem; font-size: 0.75rem;">
                        <ul style="margin: 0; padding-left: 1rem;">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('register')); ?>" autocomplete="off" id="registrationForm">
                    <?php echo csrf_field(); ?>
                    <div style="margin-bottom: 0.75rem;">
                        <input type="text" name="name" class="auth-input" placeholder="Full Name"
                            value="<?php echo e(old('name')); ?>" required autofocus>
                    </div>
                    <div style="margin-bottom: 0.75rem;">
                        <input type="email" name="email" class="auth-input" placeholder="Email Address"
                            value="<?php echo e(old('email')); ?>" required>
                    </div>
                    <div style="margin-bottom: 0.75rem;">
                        <input type="tel" name="phone" class="auth-input" placeholder="Phone Number"
                            value="<?php echo e(old('phone')); ?>" required>
                    </div>
                    <div style="margin-bottom: 0.75rem;">
                        <div class="password-wrapper">
                            <input id="password" type="password" name="password" class="auth-input" placeholder="Password" required style="padding-right: 2.5rem;">
                            <button type="button" onclick="togglePasswordVisibility('password', this)" class="password-toggle">
                                <svg class="h-4 w-4" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <div class="password-wrapper">
                            <input id="password_confirmation" type="password" name="password_confirmation" class="auth-input" placeholder="Confirm Password" required style="padding-right: 2.5rem;">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="password-toggle">
                                <svg class="h-4 w-4" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; margin-bottom: 1rem; font-size: 0.75rem;">
                        <label style="display: flex; align-items: center; gap: 0.375rem; color: #64748b;">
                            <input type="checkbox" name="terms" required style="accent-color: var(--primary);">
                            I agree to the <a href="#" class="auth-link">Terms of Service</a> and <a href="#"
                                class="auth-link">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit" class="auth-button">Create Account</button>
                </form>

                <div style="text-align: center; margin-top: 1rem; font-size: 0.75rem; color: #64748b;">
                    Already have an account? <a href="<?php echo e(route('login')); ?>" class="auth-link">Sign In</a>
                </div>

                <div class="test-credentials">
                    <h3>Test Account</h3>
                    <div class="test-credential-item" onclick="fillCredentials('customer@salonpro.com', 'customer123')">
                        <span class="test-credential-role">Customer</span>
                        <span style="color: #64748b;">customer@salonpro.com / customer123</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- International Tel Input JS -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
    <script src="<?php echo e(asset('js/customer-phone-handler.js')); ?>?v=<?php echo e(time()); ?>"></script>
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

        function fillCredentials(email, password) {
            document.querySelector('input[name="email"]').value = email;
            document.querySelector('input[name="password"]').value = password;
            document.querySelector('input[name="password_confirmation"]').value = password;
        }

        // Form validation
        document.getElementById('registrationForm').addEventListener('submit', function (e) {
            if (window.customerPhoneHandler) {
                if (!window.customerPhoneHandler.validateForm(this)) {
                    e.preventDefault();
                    // Since this page doesn't have SweetAlert by default, we'll use a simple alert or just let the handler show the error
                }
            }
        });
    </script>
</body>

</html><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views/auth/register.blade.php ENDPATH**/ ?>