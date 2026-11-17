<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($salon->name ?? config('app.name', 'Salon')); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon"
        href="<?php echo e(app(\App\Services\SettingsService::class)->getFaviconUrl($salon->id ?? null)); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/css/welcome.css']); ?>

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Header Button Styles -->
    <style>
        html, body {
            overflow-x: hidden !important;
            max-width: 100vw;
            width: 100%;
        }

        .nav-link,
        .btn-primary,
        .btn-secondary {
            padding: 0.4rem 0.85rem;
            font-size: 0.8125rem;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, #d63384, #e91e63);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #c02569, #d63384);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(214, 51, 132, 0.2);
        }

        .btn-secondary {
            background: transparent;
            color: #d63384;
            border: 1.5px solid #d63384;
        }

        .btn-secondary:hover {
            background: #d63384;
            color: white;
            transform: translateY(-1px);
        }

        .nav-link {
            color: #333;
            padding: 0.5rem 0.75rem;
        }

        .nav-link:hover {
            color: #d63384;
            background: rgba(214, 51, 132, 0.05);
        }

        /* Card Styles for Dashboard */
        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: box-shadow 0.2s ease;
        }

        .dashboard-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-confirmed {
            background-color: #10b981;
            color: white;
        }

        .status-pending {
            background-color: #f59e0b;
            color: white;
        }

        /* Image Slider Styles */
        .image-slider {
            position: relative;
            width: 200px;
            height: 60px;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .slider-container {
            display: flex;
            width: 100%;
            max-width: 600px;
            animation: slide 9s infinite;
        }

        .slider-container img {
            width: 200px;
            height: 60px;
            object-fit: cover;
            flex-shrink: 0;
        }

        @keyframes slide {
            0% {
                transform: translateX(0);
            }

            33.33% {
                transform: translateX(-200px);
            }

            66.66% {
                transform: translateX(-400px);
            }

            100% {
                transform: translateX(0);
            }
        }
    </style>

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>

    <!-- Additional Styles -->
    <?php echo $__env->yieldPushContent('styles'); ?>

    <!-- Integrations -->
    <?php
        $settingsService = app(\App\Services\SettingsService::class);
        $googleAnalyticsId = $settingsService->get('google_analytics_id');
        $facebookPixelId = $settingsService->get('facebook_pixel_id');
    ?>

    <?php if($googleAnalyticsId): ?>
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e($googleAnalyticsId); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '<?php echo e($googleAnalyticsId); ?>');
        </script>
    <?php endif; ?>

    <?php if($facebookPixelId): ?>
        <!-- Facebook Pixel -->
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq) return; n = f.fbq = function () {
                    n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
                n.queue = []; t = b.createElement(e); t.async = !0;
                t.src = v; s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?php echo e($facebookPixelId); ?>');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id=<?php echo e($facebookPixelId); ?>&ev=PageView&noscript=1" /></noscript>
    <?php endif; ?>
</head>

<body class="antialiased overflow-x-hidden">
    <!-- Header -->
    <header class="bg-white shadow-sm fixed w-full z-50 top-0">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">
                <!-- Left: Logo -->
                <div class="flex items-center min-w-0 pr-2">
                    <?php
                        $logoUrl = app(\App\Services\SettingsService::class)->getLogoUrl($salon->id ?? null);
                        $salonName = \Illuminate\Support\Str::title($salon->name ?? config('app.name', 'Salon Name'));
                    ?>
                    <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2 min-w-0">
                        <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($salonName); ?>" class="h-8 sm:h-10 w-auto object-contain max-w-[130px] sm:max-w-none flex-shrink-0"
                            onerror="this.style.display='none'; document.getElementById('header-salon-name').style.display='block';">
                        <span id="header-salon-name" class="text-base sm:text-2xl font-bold font-playfair text-pink-600 truncate"
                            style="display: none;"><?php echo e($salonName); ?></span>
                    </a>
                </div>

                <!-- Right: Profile / Actions -->
                <div class="flex items-center space-x-2 sm:space-x-3 flex-shrink-0">
                    <?php if(Route::has('login')): ?>
                        <?php if(auth()->guard()->check()): ?>
                            <div class="relative">
                                <button id="profile-toggle"
                                    class="flex items-center space-x-1 sm:space-x-2 hover:bg-gray-100 px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-lg transition-colors">
                                    <i class="fas fa-user-circle text-xl sm:text-2xl text-gray-600"></i>
                                    <span class="text-xs sm:text-sm font-medium max-w-[90px] truncate sm:max-w-none"><?php echo e(Auth::user()->name ?? 'User'); ?></span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                                </button>
                                <!-- Dropdown -->
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden border border-gray-100"
                                    id="profile-dropdown">
                                    <a href="<?php echo e(url('/dashboard')); ?>"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if(!request()->routeIs('booking.guest') && !empty($salon?->slug)): ?>
                                <a href="<?php echo e(route('booking.guest', ['salon_slug' => $salon->slug])); ?>" class="btn-primary text-xs sm:text-sm px-2.5 py-1.5 sm:px-4 sm:py-2 rounded-md">
                                    <i class="fas fa-calendar-check mr-1"></i><span>Book Now</span>
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('login')); ?>" class="btn-secondary text-xs sm:text-sm px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-md">
                                <i class="fas fa-sign-in-alt sm:mr-1"></i><span class="hidden sm:inline">Sign In</span><span class="sm:hidden">Login</span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-14 sm:pt-16 min-h-screen bg-gray-50 overflow-x-hidden">
        <?php echo $__env->yieldContent('content'); ?>
        <?php echo e($slot ?? ''); ?>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="mb-4">
                        <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($salonName); ?>" class="h-12 w-auto object-contain"
                            onerror="this.style.display='none'; document.getElementById('footer-salon-name').style.display='block';">
                        <h3 id="footer-salon-name" class="text-xl font-bold" style="display: none;"><?php echo e($salonName); ?>

                        </h3>
                    </div>
                    <p class="text-gray-400">Your beauty journey starts here. Experience luxury and excellence in every
                        service.</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="<?php echo e(route('home')); ?>" class="text-gray-400 hover:text-white transition">Home</a>
                        </li>
                        <li><a href="#services" class="text-gray-400 hover:text-white transition">Services</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition">About Us</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-gray-400">
                        <?php if(isset($salon->address) && $salon->address): ?>
                            <li><i class="fas fa-map-marker-alt mr-2"></i><?php echo e($salon->address); ?></li>
                        <?php endif; ?>
                        <?php if(isset($salon->phone) && $salon->phone): ?>
                            <li><i class="fas fa-phone mr-2"></i><?php echo e($salon->phone); ?></li>
                        <?php endif; ?>
                        <?php if(isset($salon->email) && $salon->email): ?>
                            <li><i class="fas fa-envelope mr-2"></i><?php echo e($salon->email); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo e(date('Y')); ?> <?php echo e($salonName); ?>. All rights
                    reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <?php echo $__env->yieldPushContent('scripts'); ?>

    <script>
        // Profile Dropdown Toggle
        document.addEventListener('DOMContentLoaded', function () {
            const profileToggle = document.getElementById('profile-toggle');
            if (profileToggle) {
                profileToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const dropdown = document.getElementById('profile-dropdown');
                    dropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', function () {
                    document.getElementById('profile-dropdown')?.classList.add('hidden');
                });
            }
        });
    </script>

</html><?php /**PATH C:\Ketan\projects\barber\resources\views/layouts/guest.blade.php ENDPATH**/ ?>