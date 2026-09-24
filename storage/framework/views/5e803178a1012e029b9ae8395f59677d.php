<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <!-- Anti-flicker Theme Script -->
    <script>
        (function () {
            try {
                const colorMap = {
                    'pink': '#e83e8c',
                    'blue': '#0d6efd',
                    'purple': '#6f42c1',
                    'green': '#198754',
                    'barber': '#B45309'
                };

                // Server-side default
                const serverColor = "<?php echo e(app(\App\Services\SettingsService::class)->getThemeColor()); ?>";

                // Client-side override
                const savedColor = localStorage.getItem('salon_theme_color') || serverColor;

                const colorHex = colorMap[savedColor] || colorMap['blue'];

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

<body<?php echo e($isRtl ? 'dir=rtl' : ''); ?>>
    <?php if(auth()->guard()->check()): ?>
        <?php
            $salonSlug = request()->route('salon_slug') ?? optional(auth()->user()->salon)->slug;
        ?>
        <?php $settings = app('App\Services\SettingsService'); ?>
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <img src="<?php echo e($settings->getLogoUrl()); ?>" alt="Logo" class="sidebar-brand-icon"
                        style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%;">
                    <span class="sidebar-brand-text">
                        <?php echo e($settings->get('business_name') === 'My Business' ? (optional(auth()->user()->salon)->name ?? $settings->get('business_name')) : ($settings->get('business_name') ?? optional(auth()->user()->salon)->name ?? 'Salon CMS')); ?>

                    </span>
                </div>
            </div>
            <div class="sidebar-menu">
                <!-- Common Menu Items -->
                <?php if (\Illuminate\Support\Facades\Blade::check('role', 'employee')): ?>
                <a href="<?php echo e(route('employee.dashboard', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                    class="sidebar-item <?php echo e(request()->routeIs('employee.dashboard') ? 'active' : ''); ?>"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                    <i class="fas fa-home sidebar-icon"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
    <?php else: ?>
                        <a href="<?php echo e(route('home.dashboard')); ?>"
                            class="sidebar-item <?php echo e(request()->routeIs('dashboard') || request()->routeIs('admin.super.dashboard') || request()->routeIs('home.dashboard') ? 'active' : ''); ?>"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                            <i class="fas fa-home sidebar-icon"></i>
                            <span class="sidebar-text">Dashboard</span>
                        </a>
                        <?php endif; ?>

                        

                        <!-- Customer Menu Items -->
                        <?php if (\Illuminate\Support\Facades\Blade::check('role', 'customer')): ?>
                        <a href="<?php echo e(route('customer.appointments.create', ['salon_slug' => request()->current_salon->slug ?? 'demo-salon'])); ?>"
                            class="sidebar-item <?php echo e(request()->routeIs('customer.appointments.create') ? 'active' : ''); ?>"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="Book Appointment">
                            <i class="fas fa-plus-circle sidebar-icon"></i>
                            <span class="sidebar-text">Book Appointment</span>
                        </a>
                        <a href="<?php echo e(route('customer.appointments.index', ['salon_slug' => request()->current_salon->slug ?? 'demo-salon'])); ?>"
                            class="sidebar-item <?php echo e(request()->routeIs('customer.appointments.index') || request()->routeIs('customer.appointments.show') ? 'active' : ''); ?>"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="My Appointments">
                            <i class="fas fa-calendar-alt sidebar-icon"></i>
                            <span class="sidebar-text">My Appointments</span>
                        </a>
                        <a href="<?php echo e(route('profile.edit')); ?>"
                            class="sidebar-item <?php echo e(request()->routeIs('profile.edit') ? 'active' : ''); ?>" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Profile">
                            <i class="fas fa-user sidebar-icon"></i>
                            <span class="sidebar-text">Profile</span>
                        </a>
                        <?php endif; ?>

                        <!-- Admin/Manager Menu Items -->
                        

                        <!-- Bookings Section -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bookings.view')): ?>
                            <?php if(optional(auth()->user()->salon)->canUseFeature('Booking System') && \App\Helpers\ModuleHelper::appointmentsEnabled()): ?>
                                <div class="sidebar-section">
                                    <a href="#" class="sidebar-item <?php echo e(request()->routeIs('admin.bookings.*') ? 'active' : ''); ?>"
                                        data-bs-toggle="collapse" data-bs-target="#bookingsSubmenu"
                                        aria-expanded="<?php echo e(request()->routeIs('admin.bookings.*') ? 'true' : 'false'); ?>"
                                        data-bs-placement="right" title="Bookings">
                                        <i class="fas fa-calendar-alt sidebar-icon"></i>
                                        <span class="sidebar-text">Bookings</span>
                                        <i
                                            class="fas fa-chevron-<?php echo e(request()->routeIs('admin.bookings.*') ? 'up' : 'down'); ?> ms-auto chevron"></i>
                                    </a>
                                    <div class="collapse <?php echo e(request()->routeIs('admin.bookings.*') ? 'show' : ''); ?>" id="bookingsSubmenu">
                                        <div class="px-4 py-1">
                                            <a href="<?php echo e(route('admin.bookings.index', ['salon_slug' => $salonSlug])); ?>"
                                                class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.bookings.index') ? 'active' : ''); ?>"
                                                data-bs-toggle="tooltip" data-bs-placement="right" title="All Bookings">
                                                <i class="fas fa-list-ul sidebar-icon"></i>
                                                <span class="sidebar-text">All Bookings</span>
                                            </a>

                                        </div>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bookings.view_stats')): ?>
                                            <div class="px-4 py-1">
                                                <a href="<?php echo e(route('admin.bookings.stats', ['salon_slug' => $salonSlug])); ?>"
                                                    class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.bookings.stats') ? 'active' : ''); ?>"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Booking Stats">
                                                    <i class="fas fa-chart-bar sidebar-icon"></i>
                                                    <span class="sidebar-text">Booking Stats</span>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <a href="<?php echo e(route('admin.guest-booking.index', ['salon_slug' => $salonSlug])); ?>"
                                    class="sidebar-item <?php echo e(request()->routeIs('admin.guest-booking.*') ? 'active' : ''); ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Guest Booking">
                                    <i class="fas fa-link sidebar-icon"></i>
                                    <span class="sidebar-text">Guest Booking</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- POS Section -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pos.view')): ?>
                            <?php if(optional(auth()->user()->salon)->canUseFeature('POS System') && \App\Helpers\ModuleHelper::posEnabled()): ?>
                                <?php $posBusinessType = optional(auth()->user()->salon)->business_type; ?>
                                
                                <a href="<?php echo e(route('admin.bookings.index', ['salon_slug' => $salonSlug])); ?>"
                                    class="sidebar-item <?php echo e(request()->routeIs('admin.bookings.index') ? 'active' : ''); ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Quick Sale">
                                    <i class="fas fa-bolt sidebar-icon text-warning"></i>
                                    <span class="sidebar-text">Quick Sale</span>
                                </a>

                                <div class="sidebar-section">
                                    <a href="#" class="sidebar-item <?php echo e(request()->routeIs('admin.pos.*') ? 'active' : ''); ?>"
                                        data-bs-toggle="collapse" data-bs-target="#posSubmenu"
                                        aria-expanded="<?php echo e(request()->routeIs('admin.pos.*') ? 'true' : 'false'); ?>" data-bs-placement="right"
                                        title="<?php echo e($posBusinessType === 'barber' ? 'Barber POS' : ($posBusinessType === 'both' ? 'Salon & Barber POS' : 'POS Management')); ?>">
                                        <i class="fas <?php echo e($posBusinessType === 'barber' ? 'fa-scissors text-warning' : ($posBusinessType === 'both' ? 'fa-store text-warning' : 'fa-cash-register')); ?> sidebar-icon"></i>
                                        <span class="sidebar-text"><?php echo e($posBusinessType === 'barber' ? 'Barber POS' : ($posBusinessType === 'both' ? 'Salon & Barber POS' : 'POS Management')); ?></span>
                                        <i
                                            class="fas fa-chevron-<?php echo e(request()->routeIs('admin.pos.*') ? 'up' : 'down'); ?> ms-auto chevron"></i>
                                    </a>
                                    <div class="collapse <?php echo e(request()->routeIs('admin.pos.*') ? 'show' : ''); ?>" id="posSubmenu">
                                        <div class="px-4 py-1">
                                            <?php if($posBusinessType === 'both'): ?>
                                                
                                                <a href="<?php echo e(route('admin.pos.index', ['salon_slug' => $salonSlug])); ?>"
                                                    class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.pos.index') && !request()->query('view') ? 'active' : ''); ?>"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Salon Terminal">
                                                    <i class="fas fa-shopping-cart sidebar-icon"></i>
                                                    <span class="sidebar-text">Salon Terminal</span>
                                                </a>
                                                <a href="<?php echo e(route('admin.pos.index', ['salon_slug' => $salonSlug])); ?>?view=barber"
                                                    class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.pos.index') && request()->query('view') === 'barber' ? 'active' : ''); ?>"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Barber Terminal">
                                                    <i class="fas fa-scissors sidebar-icon text-warning"></i>
                                                    <span class="sidebar-text">Barber Terminal</span>
                                                </a>
                                            <?php else: ?>
                                                
                                                <a href="<?php echo e(route('admin.pos.index', ['salon_slug' => $salonSlug])); ?>"
                                                    class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.pos.index') ? 'active' : ''); ?>"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo e($posBusinessType === 'barber' ? 'Barber Terminal' : 'POS Terminal'); ?>">
                                                    <i class="fas <?php echo e($posBusinessType === 'barber' ? 'fa-scissors' : 'fa-shopping-cart'); ?> sidebar-icon"></i>
                                                    <span class="sidebar-text"><?php echo e($posBusinessType === 'barber' ? 'Barber Terminal' : 'POS Terminal'); ?></span>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('admin.pos.sales.index', ['salon_slug' => $salonSlug])); ?>"
                                                class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.pos.sales.*') ? 'active' : ''); ?>"
                                                data-bs-toggle="tooltip" data-bs-placement="right" title="Sales History">
                                                <i class="fas fa-receipt sidebar-icon"></i>
                                                <span class="sidebar-text">Sales History</span>
                                            </a>
                                            <a href="<?php echo e(route('admin.pos.analytics.index', ['salon_slug' => $salonSlug])); ?>"
                                                class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.pos.analytics.*') ? 'active' : ''); ?>"
                                                data-bs-toggle="tooltip" data-bs-placement="right" title="Analytics">
                                                <i class="fas fa-chart-line sidebar-icon"></i>
                                                <span class="sidebar-text">Analytics</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php endif; ?>

                        <!-- Customers -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customers.view')): ?>
                            <?php if(optional(auth()->user()->salon)->canUseFeature('Customer Management') && \App\Helpers\ModuleHelper::customersEnabled()): ?>
                                <a href="<?php echo e(route('admin.customers.index', ['salon_slug' => $salonSlug])); ?>"
                                    class="sidebar-item <?php echo e(request()->routeIs('admin.customers.*') ? 'active' : ''); ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Customers">
                                    <i class="fas fa-users sidebar-icon"></i>
                                    <span class="sidebar-text">Customers</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Staff & Commissions -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('staff.view')): ?>
                            <?php if(optional(auth()->user()->salon)->canUseFeature('Staff Management') && \App\Helpers\ModuleHelper::staffEnabled()): ?>
                                <a href="<?php echo e(route('admin.staff.index', ['salon_slug' => $salonSlug])); ?>"
                                    class="sidebar-item <?php echo e(request()->routeIs('admin.staff.*') ? 'active' : ''); ?>" data-bs-toggle="tooltip"
                                    data-bs-placement="right" title="Staff">
                                    <i class="fas fa-users sidebar-icon"></i>
                                    <span class="sidebar-text">Staff</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('commissions.view')): ?>
                            <?php if(optional(auth()->user()->salon)->canUseFeature('Commission Management') && \App\Helpers\ModuleHelper::isEnabled('commissions')): ?>
                                <a href="<?php echo e(route('admin.commissions.index', ['salon_slug' => $salonSlug])); ?>"
                                    class="sidebar-item <?php echo e(request()->routeIs('admin.commissions.*') ? 'active' : ''); ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Commissions">
                                    <i class="fas fa-percent sidebar-icon"></i>
                                    <span class="sidebar-text">Commissions</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Catalog (Services, Packages, Memberships, Inventory) -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('services.view')): ?>
                            <?php if(optional(auth()->user()->salon)->canUseFeature('Booking System') && \App\Helpers\ModuleHelper::servicesEnabled()): ?>
                                <a href="<?php echo e(route('admin.services.categories.index', ['salon_slug' => $salonSlug])); ?>"
                                    class="sidebar-item <?php echo e(request()->routeIs('admin.services.categories.*') ? 'active' : ''); ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Categories">
                                    <i class="fas fa-tags sidebar-icon"></i>
                                    <span class="sidebar-text">Categories</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('packages.view')): ?>
                            <?php if(optional(auth()->user()->salon)->canUseFeature('Packages') && \App\Helpers\ModuleHelper::packagesEnabled()): ?>
                                <a href="<?php echo e(route('admin.packages.index', ['salon_slug' => $salonSlug])); ?>"
                                    class="sidebar-item <?php echo e(request()->routeIs('admin.packages.*') ? 'active' : ''); ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Packages">
                                    <i class="fas fa-gift sidebar-icon"></i>
                                    <span class="sidebar-text">Packages</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('memberships.view')): ?>
                            <?php if(optional(auth()->user()->salon)->canUseFeature('Memberships') && \App\Helpers\ModuleHelper::membershipsEnabled()): ?>
                                <a href="<?php echo e(route('admin.memberships.index', ['salon_slug' => $salonSlug])); ?>"
                                    class="sidebar-item <?php echo e(request()->routeIs('admin.memberships.*') ? 'active' : ''); ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Memberships">
                                    <i class="fas fa-id-card sidebar-icon"></i>
                                    <span class="sidebar-text">Memberships</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('inventory.view')): ?>
                            <?php if(optional(auth()->user()->salon)->canUseFeature('Inventory Management') && \App\Helpers\ModuleHelper::inventoryEnabled()): ?>
                                <a href="<?php echo e(route('admin.inventory.index', ['salon_slug' => $salonSlug])); ?>"
                                    class="sidebar-item <?php echo e(request()->routeIs('admin.inventory.*') ? 'active' : ''); ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Inventory">
                                    <i class="fas fa-boxes sidebar-icon"></i>
                                    <span class="sidebar-text">Inventory</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- AI Copilot & Automations -->
                        <?php if(optional(auth()->user()->salon)->canUseFeature('AI Insights & Automation') && \App\Helpers\ModuleHelper::aiEnabled()): ?>
                            <a href="<?php echo e(route('admin.ai.hub', ['salon_slug' => $salonSlug])); ?>"
                                class="sidebar-item <?php echo e(request()->routeIs('admin.ai.*') ? 'active' : ''); ?>"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="AI Copilot & Automations">
                                <i class="fas fa-robot sidebar-icon text-warning"></i>
                                <span class="sidebar-text fw-bold">AI Copilot</span>
                                <span class="badge bg-purple text-white ms-auto" style="font-size: 0.65rem;">PRO AI</span>
                            </a>
                        <?php endif; ?>

                        <!-- Reports -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.view')): ?>
                            <?php if(optional(auth()->user()->salon)->canUseFeature('Analytics & Reports') && \App\Helpers\ModuleHelper::reportsEnabled()): ?>
                                <div class="sidebar-section">
                                    <a href="#" class="sidebar-item <?php echo e(request()->routeIs('admin.reports.*') ? 'active' : ''); ?>"
                                        data-bs-toggle="collapse" data-bs-target="#reportsSubmenu"
                                        aria-expanded="<?php echo e(request()->routeIs('admin.reports.*') ? 'true' : 'false'); ?>"
                                        data-bs-placement="right" title="Reports">
                                        <i class="fas fa-chart-pie sidebar-icon"></i>
                                        <span class="sidebar-text">Reports</span>
                                        <i
                                            class="fas fa-chevron-<?php echo e(request()->routeIs('admin.reports.*') ? 'up' : 'down'); ?> ms-auto chevron"></i>
                                    </a>
                                    <div class="collapse <?php echo e(request()->routeIs('admin.reports.*') ? 'show' : ''); ?>" id="reportsSubmenu">
                                        <div class="px-4 py-1">
                                            <?php if(optional(auth()->user()->salon)->canUseFeature('AI Insights & Automation') && \App\Helpers\ModuleHelper::aiEnabled()): ?>
                                                <a href="<?php echo e(route('admin.reports.ai-insights', ['salon_slug' => $salonSlug])); ?>"
                                                    class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.reports.ai-insights') ? 'active' : ''); ?>"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Smart AI Insights">
                                                    <i class="fas fa-brain sidebar-icon text-warning"></i>
                                                    <span class="sidebar-text fw-bold">Smart AI Insights</span>
                                                    <span class="badge bg-purple text-white ms-auto" style="font-size: 0.65rem;">AI</span>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('admin.reports.sales', ['salon_slug' => $salonSlug])); ?>"
                                                class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.reports.sales') ? 'active' : ''); ?>"
                                                data-bs-toggle="tooltip" data-bs-placement="right" title="Sales Reports">
                                                <i class="fas fa-chart-line sidebar-icon"></i>
                                                <span class="sidebar-text">Sales Reports</span>
                                            </a>
                                            <a href="<?php echo e(route('admin.reports.appointments', ['salon_slug' => $salonSlug])); ?>"
                                                class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.reports.appointments') ? 'active' : ''); ?>"
                                                data-bs-toggle="tooltip" data-bs-placement="right" title="Appointment Reports">
                                                <i class="fas fa-calendar-alt sidebar-icon"></i>
                                                <span class="sidebar-text">Appointment Reports</span>
                                            </a>
                                            <a href="<?php echo e(route('admin.reports.staff', ['salon_slug' => $salonSlug])); ?>"
                                                class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.reports.staff') ? 'active' : ''); ?>"
                                                data-bs-toggle="tooltip" data-bs-placement="right" title="Staff Performance">
                                                <i class="fas fa-users sidebar-icon"></i>
                                                <span class="sidebar-text">Staff Performance</span>
                                            </a>
                                            <a href="<?php echo e(route('admin.reports.customers', ['salon_slug' => $salonSlug])); ?>"
                                                class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.reports.customers') ? 'active' : ''); ?>"
                                                data-bs-toggle="tooltip" data-bs-placement="right" title="Customer Insights">
                                                <i class="fas fa-user-friends sidebar-icon"></i>
                                                <span class="sidebar-text">Customer Insights</span>
                                            </a>
                                            <?php if(optional(auth()->user()->salon)->canUseFeature('Commission Management')): ?>
                                                <a href="<?php echo e(route('admin.commissions.reports', ['salon_slug' => $salonSlug])); ?>"
                                                    class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.commissions.reports') ? 'active' : ''); ?>"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Commissions">
                                                    <i class="fas fa-hand-holding-usd sidebar-icon"></i>
                                                    <span class="sidebar-text">Commissions</span>
                                                </a>
                                                <a href="<?php echo e(route('admin.reports.commissions.payouts', ['salon_slug' => $salonSlug])); ?>"
                                                    class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.reports.commissions.payouts') ? 'active' : ''); ?>"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Payouts">
                                                    <i class="fas fa-wallet sidebar-icon"></i>
                                                    <span class="sidebar-text">Payouts</span>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Salon Management -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['salon.manage_settings', 'salon.manage_branches'])): ?>
                            <div class="sidebar-section">
                                <div class="sidebar-section-header">
                                    <i class="fas fa-store sidebar-icon"></i>
                                    <span class="sidebar-text">Salon Management</span>
                                </div>

                                

                                
                                <?php if(auth()->user()->hasAnyRole(['salon_admin', 'manager'])): ?>
                                    <a href="<?php echo e(route('admin.saas.settings.mail', ['salon_slug' => $salonSlug])); ?>"
                                        class="sidebar-item <?php echo e(request()->routeIs('admin.saas.settings.*') ? 'active' : ''); ?>"
                                        data-bs-toggle="tooltip" data-bs-placement="right" title="Salon Settings">
                                        <i class="fas fa-cogs sidebar-icon"></i>
                                        <span class="sidebar-text">Salon Settings</span>
                                    </a>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('salon.manage_branches')): ?>
                                    <?php if(optional(auth()->user()->salon)->canUseFeature('Multi-Branch Support') && \App\Helpers\ModuleHelper::branchesEnabled()): ?>
                                        <a href="<?php echo e(route('admin.branches.index', ['salon_slug' => $salonSlug])); ?>"
                                            class="sidebar-item <?php echo e(request()->routeIs('admin.branches.*') ? 'active' : ''); ?>"
                                            data-bs-toggle="tooltip" data-bs-placement="right" title="Branches">
                                            <i class="fas fa-building sidebar-icon"></i>
                                            <span class="sidebar-text">Branches</span>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>

                                
                                <?php if(auth()->user()->hasRole('salon_admin') && optional(auth()->user()->salon)->canUseFeature('Role Management') && \App\Helpers\ModuleHelper::isEnabled('roles')): ?>
                                    <a href="<?php echo e(route('admin.roles.index', ['salon_slug' => $salonSlug])); ?>"
                                        class="sidebar-item <?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>"
                                        data-bs-toggle="tooltip" data-bs-placement="right" title="Roles & Permissions">
                                        <i class="fas fa-user-shield sidebar-icon"></i>
                                        <span class="sidebar-text">Roles & Permissions</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        

                        <!-- Employee Menu Items -->
                        <?php if (\Illuminate\Support\Facades\Blade::check('role', 'employee')): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bookings.view')): ?>
                            <a href="<?php echo e(route('employee.appointments.index', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                                class="sidebar-item <?php echo e(request()->routeIs('employee.appointments.*') ? 'active' : ''); ?>"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="My Appointments">
                                <i class="fas fa-calendar-check sidebar-icon"></i>
                                <span class="sidebar-text">My Appointments</span>
                            </a>
                            <a href="<?php echo e(route('employee.appointments.today', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                                class="sidebar-item <?php echo e(request()->routeIs('employee.appointments.today') ? 'active' : ''); ?>"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Today's Appointments">
                                <i class="fas fa-calendar-day sidebar-icon"></i>
                                <span class="sidebar-text">Today's Appointments</span>
                            </a>
                            <a href="<?php echo e(route('employee.appointments.upcoming', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                                class="sidebar-item <?php echo e(request()->routeIs('employee.appointments.upcoming') ? 'active' : ''); ?>"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Upcoming">
                                <i class="fas fa-calendar-week sidebar-icon"></i>
                                <span class="sidebar-text">Upcoming</span>
                            </a>
                            <a href="<?php echo e(route('employee.appointments.completed', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                                class="sidebar-item <?php echo e(request()->routeIs('employee.appointments.completed') ? 'active' : ''); ?>"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Completed">
                                <i class="fas fa-check-double sidebar-icon"></i>
                                <span class="sidebar-text">Completed</span>
                            </a>
                            <a href="<?php echo e(route('employee.earnings', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                                class="sidebar-item <?php echo e(request()->routeIs('employee.earnings') ? 'active' : ''); ?>"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="My Earnings">
                                <i class="fas fa-dollar-sign sidebar-icon"></i>
                                <span class="sidebar-text">My Earnings</span>
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('profile.edit')); ?>"
                            class="sidebar-item <?php echo e(request()->routeIs('profile.edit') ? 'active' : ''); ?>" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Profile">
                            <i class="fas fa-user sidebar-icon"></i>
                            <span class="sidebar-text">Profile</span>
                        </a>
                        <?php endif; ?>

                        <!-- System Management Section - Only for Super Admin -->
                        <?php if (\Illuminate\Support\Facades\Blade::check('role', 'super_admin')): ?>
                        <div class="sidebar-section">
                            <div class="sidebar-section-header">
                                <i class="fas fa-cogs sidebar-icon"></i>
                                <span class="sidebar-text">System Management</span>
                            </div>

                            <!-- Salons Management -->
                            <a href="<?php echo e(route('admin.salons.index')); ?>"
                                class="sidebar-item <?php echo e(request()->routeIs('admin.salons.*') ? 'active' : ''); ?>"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Salons">
                                <i class="fas fa-store sidebar-icon"></i>
                                <span class="sidebar-text">Salons</span>
                            </a>

                            <!-- Plans Management -->
                            <a href="<?php echo e(route('admin.plans.index')); ?>"
                                class="sidebar-item <?php echo e(request()->routeIs('admin.plans.*') ? 'active' : ''); ?>"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Plans">
                                <i class="fas fa-tags sidebar-icon"></i>
                                <span class="sidebar-text">Plans</span>
                            </a>

                            <!-- Subscriptions Management -->
                            <a href="<?php echo e(route('admin.subscriptions.index')); ?>"
                                class="sidebar-item <?php echo e(request()->routeIs('admin.subscriptions.*') ? 'active' : ''); ?>"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Subscriptions">
                                <i class="fas fa-crown sidebar-icon"></i>
                                <span class="sidebar-text">Subscriptions</span>
                            </a>

                            <!-- Payments Management -->
                            <a href="<?php echo e(route('admin.payments.index')); ?>"
                                class="sidebar-item <?php echo e(request()->routeIs('admin.payments.*') ? 'active' : ''); ?>"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Payments">
                                <i class="fas fa-credit-card sidebar-icon"></i>
                                <span class="sidebar-text">Payments</span>
                            </a>

                            <!-- Reports -->
                            <a href="<?php echo e(route('admin.saas.reports.index')); ?>"
                                class="sidebar-item <?php echo e(request()->routeIs('admin.saas.reports.*') ? 'active' : ''); ?>"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Reports">
                                <i class="fas fa-chart-bar sidebar-icon"></i>
                                <span class="sidebar-text">Reports</span>
                            </a>

                            <!-- Modules Management -->
                            <a href="<?php echo e(route('admin.modules.index')); ?>"
                                class="sidebar-item <?php echo e(request()->routeIs('admin.modules.*') ? 'active' : ''); ?>"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Modules">
                                <i class="fas fa-puzzle-piece sidebar-icon"></i>
                                <span class="sidebar-text">Modules</span>
                            </a>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('system.manage_roles')): ?>
                                <a href="<?php echo e(route('admin.super.roles.index')); ?>"
                                    class="sidebar-item <?php echo e(request()->routeIs('admin.super.roles.*') ? 'active' : ''); ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Roles & Permissions">
                                    <i class="fas fa-user-shield sidebar-icon"></i>
                                    <span class="sidebar-text">Roles & Permissions</span>
                                </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('system.manage_settings')): ?>
                                <a href="<?php echo e(route('admin.system-settings.index')); ?>"
                                    class="sidebar-item <?php echo e(request()->routeIs('admin.system-settings.*') ? 'active' : ''); ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="System Settings">
                                    <i class="fas fa-cog sidebar-icon"></i>
                                    <span class="sidebar-text">System Settings</span>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

    <!-- Header -->
    <div class="header" id="header">
        <div class="header-content">
            <div class="d-flex align-items-center">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <?php if(auth()->guard()->check()): ?>
                    <?php if(\App\Helpers\ModuleHelper::branchesEnabled() && session('current_branch_name') && session('current_branch_name') !== 'All Branches'): ?>
                        <div class="ms-3">
                            <span
                                class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <?php echo e(session('current_branch_name')); ?>

                            </span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="d-flex align-items-center">
                <!-- Language Switcher -->
                <?php
                    $showLangToggle = true; // Default to showing for guests/super admins
                    if (auth()->check() && optional(auth()->user())->salon) {
                        $settingsService = app(\App\Services\SettingsService::class);
                        // Get setting with default 1 (true)
                        // Ensure we handle string "1"/"0" correctly just in case
                        $val = $settingsService->get('enable_language_toggle', 1);
                        $showLangToggle = filter_var($val, FILTER_VALIDATE_BOOLEAN) || $val === 1 || $val === '1';
                    }
                ?>

                <?php if($showLangToggle): ?>
                    <div class="me-3">
                        <label class="lang-toggle-pretty">
                            <input type="checkbox" id="languageToggle">
                            <span class="bg-slider"></span>
                            <div class="labels notranslate" translate="no">
                                <span class="label-en">EN</span>
                                <span class="label-ar">AR</span>
                            </div>
                        </label>
                    </div>
                <?php endif; ?>

                <div id="google_translate_element" style="display:none;"></div>

                <script type="text/javascript">
                    function googleTranslateElementInit() {
                        new google.translate.TranslateElement({
                            pageLanguage: 'en',
                            includedLanguages: 'en,ar',
                            layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                            autoDisplay: false
                        }, 'google_translate_element');
                    }
                </script>
                <script type="text/javascript"
                    src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

                <script>
                    var isToggleEnabled = <?php echo json_encode($showLangToggle, 15, 512) ?>;

                    function changeLanguage(lang) {
                        // 1. Store preference in localStorage (Persistent)
                        localStorage.setItem('app_language', lang);

                        // 2. Cookie Management - Nuclear Option
                        var domain = window.location.hostname;
                        var parts = domain.split('.');

                        // Clear cookies for current domain, and all parent domains
                        // e.g. salon.fsterp.com, fsterp.com
                        var d = domain;
                        while (parts.length >= 2) {
                            var d = parts.join('.');
                            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + d;
                            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=." + d;
                            parts.shift();
                        }

                        // 3. Set new cookie
                        var cookieValue = '/en/' + lang;

                        // Set strictly on the current domain/host to ensure it takes precedence
                        document.cookie = "googtrans=" + cookieValue + "; path=/";
                        document.cookie = "googtrans=" + cookieValue + "; path=/; domain=" + domain;

                        // 4. Reload
                        location.reload();
                    }

                    document.addEventListener('DOMContentLoaded', function () {

                        // Safety: Check if we are in a reload loop
                        function isLooping() {
                            var lastFix = sessionStorage.getItem('lang_fix_timestamp');
                            if (lastFix) {
                                var diff = Date.now() - parseInt(lastFix);
                                // If we tried to fix less than 3 seconds ago, assume loop
                                if (diff < 3000) {

                                    return true;
                                }
                            }
                            return false;
                        }

                        function autoFixLanguage(targetLang) {
                            if (isLooping()) return;

                            sessionStorage.setItem('lang_fix_timestamp', Date.now());

                            changeLanguage(targetLang);
                        }

                        // -- FORCE ENGLISH IF TOGGLE DISABLED --
                        if (!isToggleEnabled) {
                            var currentStored = localStorage.getItem('app_language');
                            // If user is currently stuck in Arabic, force revert to English
                            if (currentStored === 'ar') {
                                autoFixLanguage('en');
                                return;
                            }
                        }

                        // -- PERSISTENCE CHECK --

                        // 1. Get stored preference
                        var storedLang = localStorage.getItem('app_language');

                        // 2. If we have a stored preference, enforce it
                        if (storedLang) {
                            var currentCookie = document.cookie.match('(^|;)\\s*googtrans\\s*=\\s*([^;]+)');
                            var cookieValue = currentCookie ? currentCookie.pop() : '';

                            // If cookie is missing or doesn't match our stored preference
                            var expectedCookieEnd = '/' + storedLang;

                            // Relaxed check: just ensure the cookie contains the target language
                            // This helps if Google modifies the cookie format slightly (e.g. /auto/ar)
                            var matches = cookieValue && (cookieValue.endsWith(expectedCookieEnd) || cookieValue.includes('/' + storedLang));

                            if (!matches) {
                                // Double check: if we want 'en' and cookie is empty/missing, that's usually fine implies default
                                // But if stored is 'ar' and cookie is missing, we must fix
                                if (storedLang === 'ar' || (storedLang === 'en' && cookieValue.includes('/ar'))) {
                                    autoFixLanguage(storedLang);
                                    return;
                                }
                            }
                        }


                        // -- UI UPDATES --

                        // Determine current effective language
                        var lang = storedLang || 'en';

                        // Update Toggle Switch State (Only if it exists)
                        var toggle = document.getElementById('languageToggle');
                        if (toggle) {
                            toggle.checked = (lang === 'ar');

                            // Add event listener for change
                            toggle.addEventListener('change', function () {
                                if (this.checked) {
                                    changeLanguage('ar');
                                } else {
                                    changeLanguage('en');
                                }
                            });
                        }

                        // Apply RTL if Arabic
                        if (lang === 'ar') {
                            document.body.classList.add('rtl');
                            document.documentElement.setAttribute('dir', 'rtl');
                            document.documentElement.setAttribute('lang', 'ar');
                        }

                        // Force hide banner (JS backup for CSS)
                        function hideBanner() {
                            var frame = document.querySelector('.goog-te-banner-frame');
                            if (frame) {
                                frame.style.setProperty('display', 'none', 'important');
                                frame.remove(); // Nuke it
                            }
                            document.body.style.setProperty('top', '0px', 'important');
                            document.body.style.setProperty('position', 'static', 'important');
                        }

                        // 1. Initial Hide
                        hideBanner();
                        setTimeout(hideBanner, 0);
                        setTimeout(hideBanner, 500);

                        // 2. MutationObserver (Nuclear Option)
                        var observer = new MutationObserver(function (mutations) {
                            hideBanner();
                        });

                        observer.observe(document.body, {
                            attributes: true,
                            childList: true,
                            subtree: false // We only care about direct children (like the iframe) or body attributes
                        });

                        // 3. Interval check just in case
                        setInterval(hideBanner, 1000);
                    });
                </script>
                <?php if(auth()->guard()->check()): ?>
                        <!-- Subscription Expiration Warning -->
                        <?php if(!env('SINGLE_SALON_MODE', false) && auth()->user()->hasRole('salon_admin') && auth()->user()->salon): ?>
                            <?php
                                $subscription = auth()->user()->salon->subscription;
                                $daysRemaining = $subscription ? $subscription->daysUntilExpiration() : null;
                                $showWarning = $daysRemaining !== null && $daysRemaining > 0 && $daysRemaining <= 30;
                            ?>

                            <?php if($showWarning): ?>
                                <div class="me-3">
                                    <a href="<?php echo e(route('admin.saas.subscription.index', ['salon_slug' => $salonSlug])); ?>"
                                        class="text-decoration-none">
                                        <div class="alert alert-warning mb-0 py-2 px-3 d-flex align-items-center"
                                            style="font-size: 0.85rem; border-radius: 8px;">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <span class="fw-semibold">
                                                Plan expires in
                                                <span class="badge bg-warning text-dark"><?php echo e($daysRemaining); ?>

                                                    <?php echo e(Str::plural('day', $daysRemaining)); ?></span>
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>


                        <!-- Notification Bell -->
                        <?php if(isset($system_notifications)): ?>
                            <div class="notification-dropdown dropdown me-3">
                                <a href="#" class="text-dark position-relative" id="notificationDropdown" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fas fa-bell fa-lg"></i>
                                    <?php if(count($system_notifications) > 0): ?>
                                        <span id="notificationBadge"
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            <?php echo e(count($system_notifications)); ?>

                                            <span class="visually-hidden">unread messages</span>
                                        </span>
                                    <?php endif; ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in border-0 shadow-lg"
                                    aria-labelledby="notificationDropdown"
                                    style="width: 320px; max-height: 400px; overflow-y: auto;">
                                    <li class="dropdown-header bg-white py-3 px-3 border-bottom">
                                        <h6 class="m-0 font-weight-bold text-primary">Notifications</h6>
                                    </li>
                                    <?php if(count($system_notifications) > 0): ?>
                                        <?php $__currentLoopData = $system_notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-start px-3 py-2 border-bottom" href="#"
                                                    onclick='event.preventDefault(); showNotificationDetail(<?php echo json_encode($notification, 15, 512) ?>);'>
                                                    <div class="me-2 flex-shrink-0">
                                                        <div class="rounded-circle bg-<?php echo e($notification->type); ?> bg-opacity-10 text-<?php echo e($notification->type); ?> d-flex align-items-center justify-content-center"
                                                            style="width: 32px; height: 32px;">
                                                            <i
                                                                class="fas fa-<?php echo e($notification->type == 'info' ? 'info' : ($notification->type == 'success' ? 'check' : ($notification->type == 'warning' ? 'exclamation' : 'exclamation-triangle'))); ?> fa-sm"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="font-weight-bold text-dark text-wrap small"
                                                                style="line-height: 1.2;"><?php echo e($notification->title); ?></span>
                                                            <small class="text-muted"
                                                                style="font-size: 0.7rem;"><?php echo e($notification->created_at->diffForHumans(null, true, true)); ?></small>
                                                        </div>
                                                        <div class="small text-secondary text-truncate"
                                                            style="max-width: 200px; font-size: 0.75rem;">
                                                            <?php echo e(Str::limit(strip_tags($notification->message), 50)); ?>

                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <li>
                                            <div class="dropdown-item text-center small text-muted py-4">No new notifications</div>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(count($system_notifications) > 0): ?>
                                        <li class="dropdown-divider"></li>
                                        <li>
                                            <button class="dropdown-item text-center small text-primary fw-bold py-2"
                                                id="markAllReadBtn">
                                                <i class="fas fa-check-double me-1"></i> Mark All as Read
                                            </button>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="user-dropdown">
                            <div class="user-dropdown-toggle" id="userDropdownToggle">
                                <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(auth()->user()->name)); ?>&background=random"
                                    alt="<?php echo e(auth()->user()->name); ?>" class="rounded-circle me-2"
                                    style="width: 32px; height: 32px;">
                                <span class="me-2"><?php echo e(auth()->user()->name); ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="user-dropdown-menu" id="userDropdownMenu">
                                <a href="<?php echo e(route('profile.edit')); ?>" class="user-dropdown-item">
                                    <i class="fas fa-user"></i>
                                    Profile
                                </a>
                                <?php if(!env('SINGLE_SALON_MODE', false) && auth()->user()->hasRole('salon_admin')): ?>
                                    <a href="<?php echo e(route('admin.saas.subscription.index', ['salon_slug' => $salonSlug])); ?>"
                                        class="user-dropdown-item">
                                        <i class="fas fa-crown"></i>
                                        My Plan
                                    </a>
                                    <a href="<?php echo e(route('admin.saas.subscription.history', ['salon_slug' => $salonSlug])); ?>"
                                        class="user-dropdown-item">
                                        <i class="fas fa-history"></i>
                                        Subscription History
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('profile.edit')); ?>" class="user-dropdown-item">
                                    <i class="fas fa-cog"></i>
                                    Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="<?php echo e(route('logout')); ?>"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="user-dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </a>
                                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                                    <?php echo csrf_field(); ?>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                <div class="guest-nav">
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-sm btn-outline-primary me-2">Login</a>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-sm btn-primary">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

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

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>window.jQuery || document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"><\/script>')</script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Toggle Sidebar
        const sidebar = document.getElementById('sidebar');
        const header = document.getElementById('header');
        const mainContent = document.getElementById('mainContent');
        const toggleSidebar = document.getElementById('toggleSidebar');

        // Check if sidebar was previously collapsed
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
            header.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }

        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            header.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');

            // Save state to localStorage
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));

            // Toggle tooltips based on sidebar state
            toggleTooltips();
        });

        // Initialize Bootstrap tooltips
        function initTooltips() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            return tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Toggle tooltips based on sidebar state
        function toggleTooltips() {
            const tooltipTriggerList = document.querySelectorAll('.sidebar-item[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(function (element) {
                const tooltip = bootstrap.Tooltip.getInstance(element);
                if (sidebar.classList.contains('collapsed')) {
                    // Enable tooltips when collapsed
                    if (!tooltip) {
                        new bootstrap.Tooltip(element);
                    } else {
                        tooltip.enable();
                    }
                } else {
                    // Disable tooltips when expanded
                    if (tooltip) {
                        tooltip.disable();
                    }
                }
            });
        }

        // Initialize tooltips on page load
        initTooltips();

        // Set initial tooltip state based on sidebar
        toggleTooltips();

        // User Dropdown
        const userDropdownToggle = document.getElementById('userDropdownToggle');
        const userDropdownMenu = document.getElementById('userDropdownMenu');

        userDropdownToggle.addEventListener('click', () => {
            userDropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userDropdownToggle.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                userDropdownMenu.classList.remove('show');
            }
        });

        // Set role-specific background as default
        <?php if(auth()->guard()->check()): ?>
                                                                                                                                                                        const userRole = '<?php echo e(auth()->user()->roles->first()->name ?? ""); ?>';
            if (userRole) {
                sidebar.classList.add(`bg-${userRole.replace('_', '-')}`);
            }
        <?php endif; ?>

        // Apply saved theme color from localStorage (overrides role-based color)
        // Theme color has priority because it's user-selected
        const savedThemeColor = localStorage.getItem('salon_theme_color');
        if (savedThemeColor) {
            const themeColorMap = {
                'pink': '#e83e8c',
                'blue': '#0d6efd',
                'purple': '#6f42c1',
                'green': '#198754',
                'barber': '#B45309'
            };

            if (sidebar && themeColorMap[savedThemeColor]) {
                // Use inline style with !important to override CSS class and gradients
                sidebar.style.setProperty('background', themeColorMap[savedThemeColor], 'important');
                sidebar.style.setProperty('background-color', themeColorMap[savedThemeColor], 'important');
            }
        }

        // Notification Detail Modal Logic
        function showNotificationDetail(notification) {
            // Populate modal content
            const headerEl = document.getElementById('detailModalHeader');
            const closeBtnEl = document.getElementById('detailModalCloseBtn');
            const titleEl = document.getElementById('detailModalHeading');
            const timeEl = document.getElementById('detailModalTime');
            const messageEl = document.getElementById('detailModalMessage');
            const iconEl = document.getElementById('detailModalIcon');
            const actionBtnEl = document.getElementById('detailModalActionBtn');

            if (titleEl) titleEl.innerText = notification.title;

            // Format date
            if (timeEl) {
                const date = new Date(notification.created_at);
                timeEl.innerText = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
            }

            // Use innerHTML for rich text content
            if (messageEl) messageEl.innerHTML = notification.message;

            // Color Map
            const colorMap = {
                'info': { bg: 'bg-info', text: 'text-white', icon: 'info-circle' },
                'success': { bg: 'bg-success', text: 'text-white', icon: 'check-circle' },
                'warning': { bg: 'bg-warning', text: 'text-dark', icon: 'exclamation-triangle' },
                'danger': { bg: 'bg-danger', text: 'text-white', icon: 'exclamation-circle' }
            };
            const style = colorMap[notification.type] || colorMap['info'];

            // Apply Header Styles
            if (headerEl) {
                headerEl.className = `modal-header border-bottom-0 py-3 ${style.bg} ${style.text}`;
            }

            // Apply Text Styles
            if (titleEl) titleEl.className = `modal-title fw-bold mb-0 ${style.text}`;
            if (timeEl) timeEl.className = `small ${style.text === 'text-white' ? 'text-white-50' : 'text-muted'}`;

            // Apply Close Button Style
            if (closeBtnEl) {
                if (style.text === 'text-white') {
                    closeBtnEl.classList.add('btn-close-white');
                } else {
                    closeBtnEl.classList.remove('btn-close-white');
                }
            }

            // Set icon
            if (iconEl) {
                const iconHtml = `<div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25 ${style.text}" style="width: 40px; height: 40px;">
                        <i class="fas fa-${style.icon} fa-lg"></i>
                    </div>`;
                iconEl.innerHTML = iconHtml;
            }

            // Handle Action Button
            if (actionBtnEl) {
                if (notification.link) {
                    actionBtnEl.href = notification.link;
                    actionBtnEl.style.display = 'inline-block';
                    actionBtnEl.className = `btn btn-${notification.type === 'warning' ? 'warning' : 'primary'}`;
                } else {
                    actionBtnEl.style.display = 'none';
                }
            }

            // Show modal
            const modalElement = document.getElementById('notificationDetailModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }

            // Mark as read in localStorage (client-side only for now)
            let readNotifications = JSON.parse(localStorage.getItem('read_notifications') || '[]');
            if (!readNotifications.includes(notification.id)) {
                readNotifications.push(notification.id);
                localStorage.setItem('read_notifications', JSON.stringify(readNotifications));

                // Update badge
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    const currentCount = parseInt(badge.innerText.trim());
                    if (!isNaN(currentCount) && currentCount > 1) {
                        badge.innerText = currentCount - 1;
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }

            // Update last seen for auto-popup logic
            localStorage.setItem('last_seen_notification_id', notification.id);
        }

        // Mark All as Read functionality
        const markAllReadBtn = document.getElementById('markAllReadBtn');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                fetch('<?php echo e(route("notifications.mark-all-read")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear all notifications from localStorage
                            localStorage.setItem('read_notifications', JSON.stringify([]));

                            // Hide badge
                            const badge = document.getElementById('notificationBadge');
                            if (badge) {
                                badge.style.display = 'none';
                            }

                            // Reload page to refresh notification list
                            location.reload();
                        }
                    })
                    .catch(error => {

                    });
            });
        }
    </script>

    <!-- Notification Detail Modal -->
    <div class="modal fade" id="notificationDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden">
                <div id="detailModalHeader" class="modal-header border-bottom-0 py-3">
                    <h5 class="modal-title fw-bold mb-0" id="detailModalHeading">Notification</h5>
                    <button type="button" class="btn-close" id="detailModalCloseBtn" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">

                    <div id="detailModalMessage" class="mb-4"></div>
                    <a href="#" id="detailModalActionBtn" class="btn btn-primary px-4" style="display: none;">View
                        Details</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Components -->
    <?php echo $__env->make('components.notifications', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('components'); ?>

    <!-- International Tel Input JS -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>

    <!-- Customer Phone Handler -->
    <script src="<?php echo e(asset('js/customer-phone-handler.js')); ?>?v=<?php echo e(time()); ?>"></script>

    <!-- Global Helpers -->
    <script>
        function getSalonSlug() {
            // 1. Try meta tag (most reliable)
            const meta = document.querySelector('meta[name="salon-slug"]');
            if (meta && meta.getAttribute('content')) return meta.getAttribute('content');

            // 2. Try PHP variable injected by controller/middleware
            // Note: Use optional to avoid errors if object is missing
            const phpSlug = "<?php echo e(app()->bound('current_salon') ? app('current_salon')->slug : ''); ?>";
            if (phpSlug) return phpSlug;

            // 3. Fallback: Parse from URL
            const segments = window.location.pathname.split('/').filter(s => s);
            if (segments.length > 0) {
                // If the first segment is an admin/customer/staff prefix, it's not the slug
                const commonPrefixes = ['admin', 'customer', 'staff', 'employee', 'pos'];
                if (commonPrefixes.includes(segments[0])) return '';
                return segments[0];
            }
            return '';
        }
    </script>

    <!-- Scripts -->
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <!-- Floating AI Copilot Assistant Widget -->
    <?php if(auth()->check() && auth()->user()->salon && optional(auth()->user()->salon)->canUseFeature('AI Insights & Automation') && \App\Helpers\ModuleHelper::aiEnabled() && app(\App\Services\SettingsService::class)->get('enable_ai_floating_widget', true, auth()->user()->salon_id)): ?>
        <style>
            #floatingAiInput::placeholder {
                color: #94a3b8 !important;
                opacity: 1 !important;
            }
            .floating-action-pill {
                background: rgba(99, 102, 241, 0.18);
                color: #c7d2fe;
                border: 1px solid rgba(99, 102, 241, 0.35);
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.76rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .floating-action-pill:hover {
                background: #4f46e5;
                color: #ffffff;
                border-color: #4f46e5;
            }
        </style>
        <button id="floatingAiBtn" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="position: fixed; bottom: 24px; right: 24px; width: 56px; height: 56px; z-index: 9999; border: 2px solid #818cf8; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); transition: transform 0.2s ease;" onclick="toggleFloatingAiModal()">
            <i class="fas fa-brain fa-lg text-white"></i>
        </button>

        <div id="floatingAiModal" class="card shadow-lg border-0 rounded-4 overflow-hidden" style="position: fixed; bottom: 90px; right: 24px; width: 420px; max-width: 92vw; height: 540px; z-index: 9999; display: none; background: #0f172a; color: #ffffff; border: 1px solid #334155; box-shadow: 0 20px 40px rgba(0,0,0,0.5)!important;">
            <div class="card-header bg-transparent border-bottom border-slate-700 py-3 px-4 d-flex justify-content-between align-items-center" style="background: #1e293b!important;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-brain text-indigo-400 me-2 fa-lg" style="color: #818cf8;"></i>
                    <h6 class="fw-bold mb-0 text-white" style="font-size: 0.95rem;">Salon AI Copilot</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" onclick="toggleFloatingAiModal()"></button>
            </div>
            <div class="card-body p-3 d-flex flex-column" style="height: calc(100% - 60px);">
                <div id="floatingChatBox" class="flex-grow-1 mb-3 p-3 rounded-3" style="background: rgba(15, 23, 42, 0.95); border: 1px solid #1e293b; overflow-y: auto; max-height: 400px;">
                    <div class="mb-2 p-3 rounded-3" style="background: #1e293b; border: 1px solid #334155; font-size: 0.88rem; color: #f8fafc;">
                        <div class="fw-bold text-white mb-1" style="color: #818cf8!important;"><i class="fas fa-microchip me-1"></i> Copilot Assistant</div>
                        <div>Ask anything about revenue, top staff, stock warnings, or customer retention:</div>
                    </div>
                </div>
                <form id="floatingAiForm" onsubmit="return handleFloatingAiSubmit(event)">
                    <div class="input-group">
                        <input type="text" id="floatingAiInput" class="form-control text-white" placeholder="Ask AI Copilot a question..." autocomplete="off" onkeydown="if(event.key==='Enter'||event.keyCode===13){handleFloatingAiSubmit(event);}" style="background-color: #1e293b !important; color: #ffffff !important; border: 1px solid #334155 !important; font-size: 0.9rem;">
                        <button type="submit" class="btn px-3" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; font-weight: 600;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            if (typeof escapeHtml !== 'function') {
                function escapeHtml(text) {
                    return text ? String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
                }
            }

            if (typeof escapeJsString !== 'function') {
                function escapeJsString(text) {
                    return text ? String(text).replace(/\\/g, '\\\\').replace(/'/g, "\\'") : '';
                }
            }

            if (typeof formatResponseText !== 'function') {
                function formatResponseText(text) {
                    if (!text) return '';
                    return text
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>')
                        .replace(/\n/g, '<br>');
                }
            }

            function toggleFloatingAiModal() {
                const modal = document.getElementById('floatingAiModal');
                if (modal.style.display === 'none' || !modal.style.display) {
                    modal.style.display = 'block';
                    setTimeout(() => {
                        const input = document.getElementById('floatingAiInput');
                        if (input) input.focus();
                    }, 100);
                } else {
                    modal.style.display = 'none';
                }
            }

            function handleFloatingAiSubmit(e) {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                const input = document.getElementById('floatingAiInput');
                if (!input) return false;

                const msg = input.value.trim();
                if (!msg) return false;

                const chatBox = document.getElementById('floatingChatBox');
                if (!chatBox) return false;

                chatBox.innerHTML += `<div class="mb-2 p-2 px-3 rounded-3 text-end" style="background: linear-gradient(135deg, #4f46e5, #6366f1); color:#fff; font-size:0.85rem; margin-left:auto; max-width:85%; box-shadow: 0 4px 10px rgba(79,70,229,0.3);"><strong>You:</strong> ${escapeHtml(msg)}</div>`;
                
                const loaderId = 'loader_' + Date.now();
                chatBox.innerHTML += `<div id="${loaderId}" class="mb-2 p-2 px-3 rounded-3 text-slate-400 small" style="background:#1e293b; border:1px solid #334155;"><i class="fas fa-spinner fa-spin me-2 text-indigo-400"></i>AI Copilot is thinking...</div>`;
                chatBox.scrollTop = chatBox.scrollHeight;
                
                input.value = '';

                const salonSlug = "<?php echo e(optional(auth()->user()->salon)->slug); ?>";
                const routeUrl = salonSlug ? `/${salonSlug}/admin/ai/copilot` : "<?php echo e(route('admin.ai.copilot', ['salon_slug' => optional(auth()->user()->salon)->slug ?? 'default'])); ?>";

                fetch(routeUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                    },
                    body: JSON.stringify({ message: msg })
                })
                .then(res => res.json())
                .then(data => {
                    const loader = document.getElementById(loaderId);
                    if (loader) loader.remove();

                    const responseText = data.response || "No response received.";
                    let respHtml = `<div class="mb-2 p-3 rounded-3" style="background:#1e293b; border:1px solid #334155; font-size:0.88rem; color:#f8fafc;"><div class="fw-bold mb-1" style="color:#818cf8!important;"><i class="fas fa-brain me-1"></i> Copilot Intelligence</div><div>${formatResponseText(responseText)}</div>`;
                    
                    if (data.suggested_actions && data.suggested_actions.length > 0) {
                        respHtml += `<div class="mt-2 d-flex flex-wrap gap-1">` + data.suggested_actions.map(act => 
                            `<button type="button" class="floating-action-pill" onclick="handleSuggestedAction('${act.action}', '${escapeJsString(act.label)}')"><i class="fas fa-bolt me-1"></i>${escapeHtml(act.label)}</button>`
                        ).join('') + `</div>`;
                    }

                    respHtml += `</div>`;
                    chatBox.innerHTML += respHtml;
                    chatBox.scrollTop = chatBox.scrollHeight;
                })
                .catch(err => {
                    const loader = document.getElementById(loaderId);
                    if (loader) loader.remove();

                    chatBox.innerHTML += `<div class="mb-2 p-2 rounded-3 text-danger small" style="background:#1e293b; border:1px solid #ef4444;"><i class="fas fa-exclamation-circle me-1"></i> Error querying AI Copilot. Please try again.</div>`;
                    chatBox.scrollTop = chatBox.scrollHeight;
                });

                return false;
            }

            if (typeof handleSuggestedAction !== 'function') {
                function handleSuggestedAction(actionName, labelText) {
                    const salonSlug = "<?php echo e(optional(auth()->user()->salon)->slug); ?>";
                    const navRoutes = {
                        'view_customers': `/${salonSlug}/admin/customers`,
                        'manage_services': `/${salonSlug}/admin/services/categories`,
                        'view_staff': `/${salonSlug}/admin/bookings`,
                        'view_staff_schedule': `/${salonSlug}/admin/bookings`,
                        'open_pos': `/${salonSlug}/admin/pos`,
                        'view_appointments': `/${salonSlug}/admin/bookings`
                    };

                    if (navRoutes[actionName]) {
                        window.location.href = navRoutes[actionName];
                        return;
                    }

                    if (actionName === 'top_staff' || actionName === 'revenue') {
                        const input = document.getElementById('floatingAiInput');
                        if (input) {
                            input.value = labelText || actionName;
                            handleFloatingAiSubmit();
                        }
                        return;
                    }

                    triggerAutomation(actionName);
                }
            }

            if (typeof triggerAutomation !== 'function') {
                function triggerAutomation(actionName) {
                    const salonSlug = "<?php echo e(optional(auth()->user()->salon)->slug); ?>";
                    const routeUrl = salonSlug ? `/${salonSlug}/admin/ai/automation` : "<?php echo e(route('admin.ai.automation', ['salon_slug' => optional(auth()->user()->salon)->slug ?? 'default'])); ?>";

                    const chatBox = document.getElementById('floatingChatBox');
                    if (chatBox) {
                        chatBox.innerHTML += `<div class="mb-2 p-2 rounded-3 text-indigo-300 small" style="background:#1e293b; border:1px solid #4f46e5;"><i class="fas fa-cog fa-spin me-1"></i> Executing AI Automation: ${escapeHtml(actionName)}...</div>`;
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }

                    fetch(routeUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                        },
                        body: JSON.stringify({ action: actionName })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (chatBox) {
                            const msg = (data.result && data.result.message) ? data.result.message : 'Workflow executed successfully.';
                            chatBox.innerHTML += `<div class="mb-2 p-2 px-3 rounded-3 text-success small" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3);"><i class="fas fa-check-circle me-1"></i> AI Automation Completed: ${escapeHtml(msg)}</div>`;
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    })
                    .catch(err => {
                        if (chatBox) {
                            chatBox.innerHTML += `<div class="mb-2 p-2 px-3 rounded-3 text-info small" style="background:#1e293b; border:1px solid #334155;"><i class="fas fa-info-circle me-1"></i> Automation request completed.</div>`;
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    });
                }
            }
        </script>
    <?php endif; ?>
    </body>

</html><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views/layouts/app.blade.php ENDPATH**/ ?>