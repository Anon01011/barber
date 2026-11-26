<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(app(\App\Services\SettingsService::class)->get('app_name', config('app.name', 'Laravel'))); ?> - Super Admin
    </title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(app(\App\Services\SettingsService::class)->getFaviconUrl()); ?>">

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- International Tel Input CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
    <style>
        .iti {
            width: 100%;
        }

        .iti__flag-container {
            z-index: 2;
        }
    </style>

    <!-- Custom CSS -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/layout.css', 'resources/js/app.js']); ?>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <?php if(auth()->guard()->check()): ?>
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <?php
                        $logoUrl = app(\App\Services\SettingsService::class)->getLogoUrl();
                    ?>
                    <img src="<?php echo e($logoUrl); ?>" alt="Logo" class="sidebar-brand-logo me-2" style="height: 28px; width: 28px;">
                    <span
                        class="sidebar-brand-text"><?php echo e(app(\App\Services\SettingsService::class)->get('app_name', config('app.name', 'SaaS Admin'))); ?></span>
                </div>
                <div class="sidebar-divider"></div>
            </div>
            <div class="sidebar-menu">
                <!-- Dashboard -->
                <a href="<?php echo e(route('admin.super.dashboard')); ?>"
                    class="sidebar-item <?php echo e(request()->routeIs('admin.super.dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-home sidebar-icon"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>

                <!-- Salon Management -->
                <a href="<?php echo e(route('admin.salons.index')); ?>"
                    class="sidebar-item <?php echo e(request()->routeIs('admin.salons.*') ? 'active' : ''); ?>">
                    <i class="fas fa-store sidebar-icon"></i>
                    <span class="sidebar-text">Salons</span>
                </a>

                <!-- Plan Management -->
                <a href="<?php echo e(route('admin.plans.index')); ?>"
                    class="sidebar-item <?php echo e(request()->routeIs('admin.plans.*') ? 'active' : ''); ?>">
                    <i class="fas fa-tags sidebar-icon"></i>
                    <span class="sidebar-text">Plans</span>
                </a>

                <!-- Subscription Management -->
                <a href="<?php echo e(route('admin.subscriptions.index')); ?>"
                    class="sidebar-item <?php echo e(request()->routeIs('admin.subscriptions.*') ? 'active' : ''); ?>">
                    <i class="fas fa-file-contract sidebar-icon"></i>
                    <span class="sidebar-text">Subscriptions</span>
                </a>

                <!-- Payment Management -->
                <a href="<?php echo e(route('admin.payments.index')); ?>"
                    class="sidebar-item <?php echo e(request()->routeIs('admin.payments.*') ? 'active' : ''); ?>">
                    <i class="fas fa-dollar-sign sidebar-icon"></i>
                    <span class="sidebar-text">Payments</span>
                </a>

                <!-- Reports -->
                <div class="sidebar-section">
                    <a href="#" class="sidebar-item <?php echo e(request()->routeIs('admin.saas.reports.*') ? 'active' : ''); ?>"
                        data-bs-toggle="collapse" data-bs-target="#reportsSubmenu"
                        aria-expanded="<?php echo e(request()->routeIs('admin.saas.reports.*') ? 'true' : 'false'); ?>">
                        <i class="fas fa-chart-bar sidebar-icon"></i>
                        <span class="sidebar-text">Reports</span>
                        <i
                            class="fas fa-chevron-<?php echo e(request()->routeIs('admin.saas.reports.*') ? 'up' : 'down'); ?> ms-auto chevron"></i>
                    </a>
                    <div class="collapse <?php echo e(request()->routeIs('admin.saas.reports.*') ? 'show' : ''); ?>"
                        id="reportsSubmenu">
                        <div class="px-4 py-1">
                            <a href="<?php echo e(route('admin.saas.reports.index')); ?>"
                                class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.saas.reports.index') ? 'active' : ''); ?>">
                                <i class="fas fa-tachometer-alt sidebar-icon"></i>
                                <span class="sidebar-text">Overview</span>
                            </a>
                            <a href="<?php echo e(route('admin.saas.reports.revenue')); ?>"
                                class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.saas.reports.revenue') ? 'active' : ''); ?>">
                                <i class="fas fa-dollar-sign sidebar-icon"></i>
                                <span class="sidebar-text">Revenue</span>
                            </a>
                            <a href="<?php echo e(route('admin.saas.reports.subscriptions')); ?>"
                                class="sidebar-item sub-item <?php echo e(request()->routeIs('admin.saas.reports.subscriptions') ? 'active' : ''); ?>">
                                <i class="fas fa-file-contract sidebar-icon"></i>
                                <span class="sidebar-text">Subscriptions</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- System Health & DB Safety -->
                <a href="<?php echo e(route('admin.system-health.index')); ?>"
                    class="sidebar-item <?php echo e(request()->routeIs('admin.system-health.*') ? 'active' : ''); ?>">
                    <i class="fas fa-heartbeat sidebar-icon"></i>
                    <span class="sidebar-text">System Health</span>
                </a>

                <!-- System Notifications -->
                <a href="<?php echo e(route('admin.notifications.index')); ?>"
                    class="sidebar-item <?php echo e(request()->routeIs('admin.notifications.*') ? 'active' : ''); ?>">
                    <i class="fas fa-bell sidebar-icon"></i>
                    <span class="sidebar-text">Notifications</span>
                </a>

                <!-- Settings -->
                <a href="<?php echo e(route('admin.system-settings.index')); ?>"
                    class="sidebar-item <?php echo e(request()->routeIs('admin.system-settings.*') ? 'active' : ''); ?>">
                    <i class="fas fa-cog sidebar-icon"></i>
                    <span class="sidebar-text">Settings</span>
                </a>

                <!-- Email Templates -->
                <a href="<?php echo e(route('admin.email-templates.index')); ?>"
                    class="sidebar-item <?php echo e(request()->routeIs('admin.email-templates.*') ? 'active' : ''); ?>">
                    <i class="fas fa-envelope sidebar-icon"></i>
                    <span class="sidebar-text">Email Templates</span>
                </a>

                <!-- Module Management -->
                <a href="<?php echo e(route('admin.modules.index')); ?>"
                    class="sidebar-item <?php echo e(request()->routeIs('admin.modules.*') ? 'active' : ''); ?>">
                    <i class="fas fa-th-large sidebar-icon"></i>
                    <span class="sidebar-text">Modules</span>
                </a>
            </div>
        </div>
        <!-- Mobile Sidebar Backdrop -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- Header -->
        <div class="header" id="header">
            <div class="header-content">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <?php if(auth()->guard()->check()): ?>
                    <div class="d-flex align-items-center gap-3">
                        <?php
                            $saUnreadCount = auth()->user()->unreadNotifications()->count();
                            $saUnreadNotifications = auth()->user()->unreadNotifications()->latest()->take(10)->get();
                        ?>
                        <div class="dropdown">
                            <a href="#" class="position-relative d-inline-flex align-items-center justify-content-center shadow-sm"
                               id="saNotificationDropdown" aria-label="Notifications" data-bs-toggle="dropdown" aria-expanded="false"
                               style="width: 36px; height: 36px; border-radius: 50%; background: #ffffff; border: 1px solid #e2e8f0; text-decoration: none;">
                                <i class="fas fa-bell" style="font-size: 0.95rem; color: #475569;"></i>
                                <span id="notif-unread-badge"
                                    data-unread-count="<?php echo e($saUnreadCount); ?>"
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="<?php echo e($saUnreadCount === 0 ? 'display:none;' : ''); ?> font-size: 0.65rem; padding: 0.25em 0.5em;">
                                    <?php echo e($saUnreadCount > 99 ? '99+' : $saUnreadCount); ?>

                                    <span class="visually-hidden">unread notifications</span>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 shadow-lg p-0"
                                aria-labelledby="saNotificationDropdown"
                                style="width: 340px; max-height: 420px; overflow-y: auto; border-radius: 12px;">
                                <li class="dropdown-header bg-white py-3 px-3 border-bottom d-flex align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-dark" style="font-size: 0.88rem;">
                                        <i class="fas fa-bell text-primary me-1"></i> Notifications
                                    </h6>
                                    <?php if($saUnreadCount > 0): ?>
                                        <form action="<?php echo e(route('notifications.mark-all-read')); ?>" method="POST" class="d-inline m-0 p-0">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-link btn-xs text-primary text-decoration-none p-0 fw-semibold" style="font-size: 0.72rem;">
                                                Mark all as read
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </li>
                                <div id="saNotificationList">
                                    <?php $__empty_1 = true; $__currentLoopData = $saUnreadNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                            $snData = $notification->data ?? [];
                                            $snType = $snData['type'] ?? 'info';
                                            $snTitle = $snData['title'] ?? 'Notification';
                                            $snMsg = $snData['message'] ?? '';
                                        ?>
                                        <li class="border-bottom">
                                            <form action="<?php echo e(route('notifications.mark-read', $notification->id)); ?>" method="POST" class="m-0 p-0">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="dropdown-item d-flex align-items-start px-3 py-2 text-wrap w-100 text-start bg-transparent border-0" style="white-space: normal;">
                                                    <div class="me-2 flex-shrink-0 mt-1">
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                            style="width: 28px; height: 28px; background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                                                            <i class="fas fa-<?php echo e($snType === 'danger' || $snType === 'error' ? 'exclamation-circle text-danger' : ($snType === 'success' ? 'check-circle text-success' : ($snType === 'warning' ? 'exclamation-triangle text-warning' : 'info-circle text-primary'))); ?> fa-xs"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1" style="min-width: 0;">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="font-weight-bold text-dark small text-truncate" style="max-width: 170px;"><?php echo e($snTitle); ?></span>
                                                            <small class="text-muted" style="font-size: 0.68rem;"><?php echo e($notification->created_at->diffForHumans(null, true, true)); ?></small>
                                                        </div>
                                                        <div class="small text-muted text-truncate" style="max-width: 220px; font-size: 0.73rem;">
                                                            <?php echo e(Str::limit(strip_tags($snMsg), 55)); ?>

                                                        </div>
                                                    </div>
                                                </button>
                                            </form>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <li>
                                            <div class="dropdown-item text-center small text-muted py-4">
                                                <i class="far fa-bell-slash fa-2x d-block mb-2 text-muted opacity-50"></i>
                                                No unread notifications
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                </div>
                            </ul>
                        </div>

                        <div class="user-dropdown">
                            <div class="user-dropdown-toggle" id="userDropdownToggle">
                                <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(auth()->user()->name)); ?>&background=random"
                                    alt="<?php echo e(auth()->user()->name); ?>" class="rounded-circle me-2" style="width: 32px; height: 32px;">
                                <span class="me-2"><?php echo e(auth()->user()->name); ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="user-dropdown-menu" id="userDropdownMenu">
                                <a href="#" class="user-dropdown-item">
                                    <i class="fas fa-user"></i>
                                    Profile
                                </a>
                                <a href="#" class="user-dropdown-item">
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
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Page Content -->
            <div class="container-fluid">


                <?php if($errors->any()): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
        <!-- Mobile Bottom Navigation Bar (Super Admin) -->
        <nav class="mobile-bottom-nav d-lg-none" id="mobileBottomNav">
            <a href="<?php echo e(route('admin.super.dashboard')); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('admin.super.dashboard') ? 'active' : ''); ?>">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo e(route('admin.salons.index')); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('admin.salons.*') ? 'active' : ''); ?>">
                <i class="fas fa-store"></i>
                <span>Salons</span>
            </a>
            <a href="<?php echo e(route('admin.plans.index')); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('admin.plans.*') ? 'active' : ''); ?>">
                <i class="fas fa-tags"></i>
                <span>Plans</span>
            </a>
            <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('admin.subscriptions.*') ? 'active' : ''); ?>">
                <i class="fas fa-crown"></i>
                <span>Subs</span>
            </a>
            <a href="javascript:void(0)" class="mobile-bottom-nav-item" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
                <span>Menu</span>
            </a>
        </nav>
    <?php endif; ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Responsive Sidebar & Drawer Logic
        const sidebar = document.getElementById('sidebar');
        const header = document.getElementById('header');
        const mainContent = document.getElementById('mainContent');
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');

        function isMobile() {
            return window.innerWidth < 992;
        }

        function openMobileSidebar() {
            if (sidebar) sidebar.classList.add('mobile-show');
            if (sidebarBackdrop) sidebarBackdrop.classList.add('show');
            document.body.classList.add('mobile-drawer-open');
        }

        function closeMobileSidebar() {
            if (sidebar) sidebar.classList.remove('mobile-show');
            if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
            document.body.classList.remove('mobile-drawer-open');
        }

        // Check if sidebar was previously collapsed
        if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar?.classList.add('collapsed');
            header?.classList.add('collapsed');
            mainContent?.classList.add('expanded');
        }

        toggleSidebar?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (isMobile()) {
                if (sidebar?.classList.contains('mobile-show')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            } else {
                sidebar?.classList.toggle('collapsed');
                header?.classList.toggle('collapsed');
                mainContent?.classList.toggle('expanded');
                localStorage.setItem('sidebarCollapsed', sidebar?.classList.contains('collapsed') ? 'true' : 'false');
            }
        });

        mobileMenuBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            openMobileSidebar();
        });

        sidebarBackdrop?.addEventListener('click', closeMobileSidebar);

        if (sidebar) {
            sidebar.querySelectorAll('.sidebar-item:not([data-bs-toggle="collapse"])').forEach(item => {
                item.addEventListener('click', () => {
                    if (isMobile()) closeMobileSidebar();
                });
            });
        }

        window.addEventListener('resize', () => {
            if (!isMobile() && sidebar?.classList.contains('mobile-show')) {
                closeMobileSidebar();
            }
        });

        // User Dropdown
        const userDropdownToggle = document.getElementById('userDropdownToggle');
        const userDropdownMenu = document.getElementById('userDropdownMenu');

        userDropdownToggle?.addEventListener('click', () => {
            userDropdownMenu?.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (userDropdownToggle && userDropdownMenu && !userDropdownToggle.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                userDropdownMenu.classList.remove('show');
            }
        });
    </script>

    <!-- International Tel Input JS -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
    <script src="<?php echo e(asset('js/customer-phone-handler.js')); ?>?v=<?php echo e(time()); ?>"></script>

    <?php echo $__env->make('components.notifications', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH C:\Ketan\projects\barber\resources\views/layouts/super-admin.blade.php ENDPATH**/ ?>