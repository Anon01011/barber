<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="salon-slug" content="<?php echo e(request()->current_salon->slug ?? request()->segment(1)); ?>">

    <title><?php echo e(app(\App\Services\SettingsService::class)->get('app_name', config('app.name', 'Laravel'))); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(app(\App\Services\SettingsService::class)->getFaviconUrl()); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <?php
        $isRtl = false;
        if (isset($_COOKIE['googtrans'])) {
            if (strpos($_COOKIE['googtrans'], '/ar') !== false) {
                $isRtl = true;
            }
        }
    ?>

    <!-- Bootstrap CSS -->
    <?php if($isRtl): ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css"
            integrity="sha384-nU14brUcp6StFntEOOEBvcJm4huWjB0OcIeQ3fltAfSmuZFrkAif0T+UtNGlKKQv" crossorigin="anonymous">
    <?php else: ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- ApexCharts CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.css">

    <!-- Custom CSS -->
    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/layout.css',
        'resources/js/app.js',
        'resources/css/roles.css',
        'resources/css/compact-forms.css'
    ]); ?>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- International Tel Input CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">

    <!-- Impersonation Banner CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('css/impersonation.css')); ?>">

    <style>
        /* Force hide sidebar text and chevrons when collapsed */
        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .chevron {
            display: none !important;
        }

        /* Center icons in collapsed state */
        .sidebar.collapsed .sidebar-item,
        .sidebar.collapsed .sub-item {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .sidebar.collapsed .sidebar-icon {
            margin-right: 0 !important;
        }

        /* Adjust sub-menu container */
        .sidebar.collapsed .collapse>div {
            padding: 0 !important;
            background: rgba(0, 0, 0, 0.1);
        }

        /* Ensure sub-items are visible but text is hidden */
        .sidebar.collapsed .sub-item {
            display: flex !important;
        }

        /* intl-tel-input custom styles */
        .iti {
            width: 100%;
        }

        .iti__flag-container {
            z-index: 2;
        }

        .iti__selected-flag {
            padding: 0 8px 0 12px;
        }

        .iti--separate-dial-code .iti__selected-dial-code {
            margin-left: 6px;
            color: #495057;
        }

        /* FullCalendar Grid Styling */
        .fc-timegrid-slot {
            height: 12px !important;
            /* Compact height for 5-minute slots (3 slots = 36px per 15 mins) */
            border-bottom: 1px solid #f8f9fa !important;
            /* Very light border for major slots */
        }

        .fc-timegrid-slot-minor {
            border-bottom-style: dotted !important;
            border-bottom-color: #e9ecef !important;
            /* Slightly visible for 5-min slots */
        }

        .fc-timegrid-axis-cushion,
        .fc-timegrid-slot-label-cushion {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
            text-transform: lowercase;
            /* Ensure am/pm is lowercase */
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #f0f0f0 !important;
            /* Overall lighter grid */
        }

        /* RTL Specific Overrides */
        <?php if($isRtl): ?>
            .sidebar {
                left: auto;
                right: 0;
            }

            .header {
                left: 0;
                right: var(--sidebar-width);
            }

            .header.collapsed {
                left: 0;
                right: var(--sidebar-collapsed-width);
            }

            .main-content {
                margin-left: 0;
                margin-right: var(--sidebar-width);
            }

            .main-content.expanded {
                margin-left: 0;
                margin-right: var(--sidebar-collapsed-width);
            }

            .sidebar-icon {
                margin-right: 0;
                margin-left: 1rem;
            }

            .ms-auto {
                margin-right: auto !important;
                margin-left: 0 !important;
            }

            .me-2 {
                margin-left: 0.5rem !important;
                margin-right: 0 !important;
            }

            .ms-3 {
                margin-right: 1rem !important;
                margin-left: 0 !important;
            }

            /* DataTables RTL */
            div.dataTables_wrapper div.dataTables_filter {
                text-align: left;
            }

            .page-item.first .page-link,
            .page-item.last .page-link,
            .page-item.next .page-link,
            .page-item.previous .page-link {
                transform: rotate(180deg);
            }

        <?php endif; ?>

        /* Hide Google Translate Top Bar - Aggressive */
        .goog-te-banner-frame.skiptranslate,
        .goog-te-banner-frame,
        .goog-te-balloon-frame,
        #goog-gt-tt,
        .VIpgJd-ZVi9od-ORHb-OEVmcd,
        iframe[name="goog-te-banner-frame"],
        iframe[class*="goog-te-banner-frame"] {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            width: 0 !important;
            opacity: 0 !important;
            pointer-events: none !important;
            z-index: -1000 !important;
        }

        /* Prevent Body Push Down */
        body {
            top: 0px !important;
            position: static !important;
            margin-top: 0px !important;
        }

        /* Hide the google translate element itself */
        #google_translate_element {
            display: none !important;
        }

        /* Beautiful Language Toggle */
        .lang-toggle-pretty {
            position: relative;
            display: inline-flex;
            width: 90px;
            height: 34px;
            background: #f1f5f9;
            border-radius: 50px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            cursor: pointer;
            align-items: center;
            user-select: none;
            direction: ltr !important;
            /* Force LTR to keep slider mechanics consistent */
        }

        .lang-toggle-pretty input {
            display: none;
        }

        .lang-toggle-pretty .bg-slider {
            position: absolute;
            top: 2px;
            left: 2px;
            width: calc(50% - 2px);
            height: calc(100% - 4px);
            background: #ffffff;
            border-radius: 50px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s cubic-bezier(0.4, 0.0, 0.2, 1), background 0.3s;
            z-index: 1;
        }

        .lang-toggle-pretty .labels {
            position: relative;
            z-index: 2;
            display: flex;
            width: 100%;
            justify-content: space-between;
        }

        .lang-toggle-pretty span {
            width: 50%;
            text-align: center;
            font-size: 11px;
            font-weight: 800;
            color: #94a3b8;
            transition: color 0.3s;
            line-height: 32px;
        }

        /* Active Text Colors */
        .lang-toggle-pretty input:not(:checked)~.labels .label-en {
            color: #0d6efd;
        }

        .lang-toggle-pretty input:checked~.labels .label-ar {
            color: #ffffff;
        }

        /* Slider Movement */
        .lang-toggle-pretty input:checked~.bg-slider {
            transform: translateX(100%);
            background: #198754;
            /* Green for AR */
        }
    </style>
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
    <!-- Anti-flicker Theme Script & Dynamic Theme Variables -->
    <?php
        $activePalette = app(\App\Services\SettingsService::class)->getThemePalette();
    ?>
    <style id="theme-dynamic-root">
        :root {
            --theme-primary: <?php echo e($activePalette['primary']); ?>;
            --theme-accent: <?php echo e($activePalette['accent']); ?>;
            --theme-accent-hover: <?php echo e($activePalette['accent_hover']); ?>;
            --theme-accent-light: <?php echo e($activePalette['accent_light']); ?>;
            --theme-sidebar: <?php echo e($activePalette['sidebar']); ?>;
            --theme-surface: <?php echo e($activePalette['surface']); ?>;
            --theme-border: <?php echo e($activePalette['border']); ?>;
        }

        /* Dynamic Luxury Button Engine */
        .btn-primary,
        .btn-luxury {
            background: linear-gradient(135deg, var(--theme-accent, #c5a880) 0%, var(--theme-accent-hover, #b38e5d) 100%) !important;
            border: 1px solid var(--theme-accent, #c5a880) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(197, 168, 128, 0.2);
            font-weight: 500;
        }
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-luxury:hover,
        .btn-luxury:focus {
            background: linear-gradient(135deg, var(--theme-accent-hover, #b38e5d) 0%, #9e7a4b 100%) !important;
            border-color: var(--theme-accent-hover, #b38e5d) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(197, 168, 128, 0.35);
            transform: translateY(-1px);
        }
        .btn-outline-primary,
        .btn-luxury-outline {
            background: transparent !important;
            border: 1.5px solid var(--theme-accent, #c5a880) !important;
            color: var(--theme-accent, #c5a880) !important;
            font-weight: 500;
        }
        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-luxury-outline:hover,
        .btn-luxury-outline:focus {
            background: linear-gradient(135deg, var(--theme-accent, #c5a880) 0%, var(--theme-accent-hover, #b38e5d) 100%) !important;
            border-color: var(--theme-accent, #c5a880) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(197, 168, 128, 0.25);
            transform: translateY(-1px);
        }
        .btn-luxury-dark {
            background: linear-gradient(135deg, #0b0f17 0%, #1a2232 100%) !important;
            border: 1px solid rgba(197, 168, 128, 0.35) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
            font-weight: 500;
        }
        .btn-luxury-dark:hover,
        .btn-luxury-dark:focus {
            background: linear-gradient(135deg, #141b27 0%, #243045 100%) !important;
            border-color: var(--theme-accent, #c5a880) !important;
            color: var(--theme-accent, #c5a880) !important;
            transform: translateY(-1px);
        }
        .btn-luxury-ghost {
            background: rgba(197, 168, 128, 0.12) !important;
            border: 1px solid transparent !important;
            color: var(--theme-accent, #c5a880) !important;
            font-weight: 500;
        }
        .btn-luxury-ghost:hover,
        .btn-luxury-ghost:focus {
            background: rgba(197, 168, 128, 0.22) !important;
            color: var(--theme-accent-hover, #b38e5d) !important;
            border-color: rgba(197, 168, 128, 0.3) !important;
        }
        .btn-outline-secondary,
        .btn-secondary,
        .btn-luxury-secondary {
            background: #ffffff !important;
            border: 1.5px solid #d1d5db !important;
            color: #374151 !important;
            font-weight: 500 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .btn-outline-secondary:hover,
        .btn-outline-secondary:focus,
        .btn-secondary:hover,
        .btn-secondary:focus,
        .btn-luxury-secondary:hover,
        .btn-luxury-secondary:focus {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
        }
        .form-check-input:checked {
            background-color: var(--theme-accent, #c5a880) !important;
            border-color: var(--theme-accent, #c5a880) !important;
        }
        .form-check-input:focus {
            border-color: var(--theme-accent, #c5a880) !important;
            box-shadow: 0 0 0 0.2rem rgba(197, 168, 128, 0.25) !important;
        }
        .form-control:focus,
        .form-select:focus {
            border-color: var(--theme-accent, #c5a880) !important;
            box-shadow: 0 0 0 0.2rem rgba(197, 168, 128, 0.2) !important;
        }
    </style>
    <script>
        (function () {
            try {
                const colorMap = {
                    'luxury': '#0b0f17',
                    'barber': '#B45309',
                    'pink': '#e83e8c',
                    'blue': '#0d6efd',
                    'purple': '#6f42c1',
                    'green': '#198754'
                };

                // Server-side default
                const serverColor = "<?php echo e(app(\App\Services\SettingsService::class)->getThemeColor()); ?>";

                // Client-side override
                const savedColor = localStorage.getItem('salon_theme_color') || serverColor;

                const colorHex = colorMap[savedColor] || colorMap['luxury'];

                // Inject style immediately to prevent FOUC
                const style = document.createElement('style');
                style.id = 'theme-css-override';
                style.innerHTML = `
                    #sidebar { background: ${colorHex} !important; background-color: ${colorHex} !important; transition: none !important; }
                    .sidebar { background: ${colorHex} !important; background-color: ${colorHex} !important; }
                `;
                document.head.appendChild(style);
            } catch (e) {

            }
        })();
    </script>
</head>
<body class="<?php echo e(session()->has('impersonator_id') ? 'has-impersonation-banner' : ''); ?>" <?php echo e($isRtl ? 'dir=rtl' : ''); ?>>
    <?php
        $authUser = auth()->user();
        if (request()->routeIs('customer.*')) {
            $portalContext = 'customer';
        } elseif (request()->routeIs('employee.*')) {
            $portalContext = 'employee';
        } elseif ($authUser && $authUser->hasAnyRole(['super_admin', 'salon_admin', 'manager'])) {
            $portalContext = 'admin';
        } elseif ($authUser && $authUser->hasRole('customer') && !$authUser->hasRole('employee')) {
            $portalContext = 'customer';
        } elseif ($authUser && $authUser->hasRole('customer') && $authUser->customer()->exists() && !$authUser->employee()->exists()) {
            $portalContext = 'customer';
        } elseif ($authUser && $authUser->hasRole('employee')) {
            $portalContext = 'employee';
        } elseif ($authUser && $authUser->hasRole('customer')) {
            $portalContext = 'customer';
        } else {
            $portalContext = 'admin';
        }

        $salonSlug = request()->route('salon_slug') 
            ?? optional(auth()->user()?->salon)->slug 
            ?? (auth()->user()?->salon_id ? optional(\App\Models\Salon::find(auth()->user()->salon_id))->slug : null)
            ?? (app()->bound('current_salon') ? optional(app('current_salon'))->slug : null)
            ?? session('current_salon_slug');
    ?>

    <?php if(auth()->guard()->check()): ?>
        <?php if (isset($component)) { $__componentOriginal77570bf4ef14a4776127fe50074f966f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal77570bf4ef14a4776127fe50074f966f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout.desktop-sidebar','data' => ['salonSlug' => $salonSlug,'portalContext' => $portalContext]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout.desktop-sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['salon-slug' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($salonSlug),'portal-context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($portalContext)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal77570bf4ef14a4776127fe50074f966f)): ?>
<?php $attributes = $__attributesOriginal77570bf4ef14a4776127fe50074f966f; ?>
<?php unset($__attributesOriginal77570bf4ef14a4776127fe50074f966f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal77570bf4ef14a4776127fe50074f966f)): ?>
<?php $component = $__componentOriginal77570bf4ef14a4776127fe50074f966f; ?>
<?php unset($__componentOriginal77570bf4ef14a4776127fe50074f966f); ?>
<?php endif; ?>
    <?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal71dc6f001ab17d4e62c88d252666a9f3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal71dc6f001ab17d4e62c88d252666a9f3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout.top-header','data' => ['salonSlug' => $salonSlug,'portalContext' => $portalContext]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout.top-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['salon-slug' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($salonSlug),'portal-context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($portalContext)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal71dc6f001ab17d4e62c88d252666a9f3)): ?>
<?php $attributes = $__attributesOriginal71dc6f001ab17d4e62c88d252666a9f3; ?>
<?php unset($__attributesOriginal71dc6f001ab17d4e62c88d252666a9f3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal71dc6f001ab17d4e62c88d252666a9f3)): ?>
<?php $component = $__componentOriginal71dc6f001ab17d4e62c88d252666a9f3; ?>
<?php unset($__componentOriginal71dc6f001ab17d4e62c88d252666a9f3); ?>
<?php endif; ?>

    <!-- Global Notification Container -->
    <div id="notificationContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 2000">
        <div class="d-flex flex-column align-items-end" id="notificationList">
            <!-- Dynamic notifications will appear here -->
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="container-fluid px-4">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Please check the form for errors:</strong>
                    <ul class="mb-0 mt-2">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>

        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <!-- Mobile Bottom Navigation Bar -->
    <?php if (isset($component)) { $__componentOriginal97ecd86fe497f6b831c058f04b860675 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal97ecd86fe497f6b831c058f04b860675 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout.mobile-bottom-nav','data' => ['salonSlug' => $salonSlug,'portalContext' => $portalContext]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout.mobile-bottom-nav'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['salon-slug' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($salonSlug),'portal-context' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($portalContext)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal97ecd86fe497f6b831c058f04b860675)): ?>
<?php $attributes = $__attributesOriginal97ecd86fe497f6b831c058f04b860675; ?>
<?php unset($__attributesOriginal97ecd86fe497f6b831c058f04b860675); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal97ecd86fe497f6b831c058f04b860675)): ?>
<?php $component = $__componentOriginal97ecd86fe497f6b831c058f04b860675; ?>
<?php unset($__componentOriginal97ecd86fe497f6b831c058f04b860675); ?>
<?php endif; ?>

    <!-- ApexCharts JS -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.js"></script>

    <!-- Scripts -->
    <?php if (isset($component)) { $__componentOriginal266551cab190c05ebeb364e779ae2b90 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal266551cab190c05ebeb364e779ae2b90 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout.app-scripts','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout.app-scripts'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal266551cab190c05ebeb364e779ae2b90)): ?>
<?php $attributes = $__attributesOriginal266551cab190c05ebeb364e779ae2b90; ?>
<?php unset($__attributesOriginal266551cab190c05ebeb364e779ae2b90); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal266551cab190c05ebeb364e779ae2b90)): ?>
<?php $component = $__componentOriginal266551cab190c05ebeb364e779ae2b90; ?>
<?php unset($__componentOriginal266551cab190c05ebeb364e779ae2b90); ?>
<?php endif; ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Ketan\projects\barber\resources\views/layouts/app.blade.php ENDPATH**/ ?>