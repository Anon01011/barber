<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['salonSlug' => null, 'portalContext' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['salonSlug' => null, 'portalContext' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<!-- Mobile Bottom Navigation Bar (Visible on mobile/tablet screens) -->
    <?php if(auth()->guard()->check()): ?>
        <?php
            $navUser = auth()->user();
            if (!isset($portalContext)) {
                if (request()->routeIs('customer.*')) {
                    $portalContext = 'customer';
                } elseif (request()->routeIs('employee.*')) {
                    $portalContext = 'employee';
                } elseif ($navUser && $navUser->hasAnyRole(['super_admin', 'salon_admin', 'manager'])) {
                    $portalContext = 'admin';
                } elseif ($navUser && $navUser->hasRole('customer') && !$navUser->hasRole('employee')) {
                    $portalContext = 'customer';
                } elseif ($navUser && $navUser->hasRole('customer') && $navUser->customer()->exists() && !$navUser->employee()->exists()) {
                    $portalContext = 'customer';
                } elseif ($navUser && ($navUser->hasRole('employee') || $navUser->hasRole('staff'))) {
                    $portalContext = 'employee';
                } elseif ($navUser && $navUser->hasRole('customer')) {
                    $portalContext = 'customer';
                } else {
                    $portalContext = 'admin';
                }
            }
            if (!isset($salonSlug)) {
                $salonSlug = request()->route('salon_slug') 
                    ?? optional(auth()->user()->salon)->slug 
                    ?? (auth()->user()->salon_id ? optional(\App\Models\Salon::find(auth()->user()->salon_id))->slug : null)
                    ?? (app()->bound('current_salon') ? optional(app('current_salon'))->slug : null)
                    ?? session('current_salon_slug');
            }
        ?>
        <nav class="mobile-bottom-nav d-lg-none" id="mobileBottomNav">
            <?php if($portalContext === 'customer'): ?>
                <?php
                    $custMobSalonSlug = $salonSlug ?? optional(auth()->user()->salon)->slug ?? request()->current_salon->slug ?? 'demo-salon';
                ?>
                <a href="<?php echo e(route('customer.dashboard', ['salon_slug' => $custMobSalonSlug])); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('customer.dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo e(route('customer.appointments.create', ['salon_slug' => $custMobSalonSlug])); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('customer.appointments.create') ? 'active' : ''); ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span>Book</span>
                </a>
                <a href="<?php echo e(route('customer.appointments.index', ['salon_slug' => $custMobSalonSlug])); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('customer.appointments.index') || request()->routeIs('customer.appointments.show') ? 'active' : ''); ?>">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Appts</span>
                </a>
                <?php if(\App\Helpers\ModuleHelper::loyaltyEnabled()): ?>
                    <a href="<?php echo e(route('customer.points.index', ['salon_slug' => $custMobSalonSlug])); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('customer.points.*') ? 'active' : ''); ?>">
                        <i class="fas fa-coins text-warning"></i>
                        <span>Points</span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('profile.edit')); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('profile.edit') ? 'active' : ''); ?>">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                <?php endif; ?>
                <a href="javascript:void(0)" class="mobile-bottom-nav-item" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                    <span>Menu</span>
                </a>

            <?php elseif($portalContext === 'employee'): ?>
                <?php
                    $empMobSalonSlug = $salonSlug ?? optional(auth()->user()->salon)->slug ?? 'demo-salon';
                ?>
                <a href="<?php echo e(route('employee.dashboard', ['salon_slug' => $empMobSalonSlug])); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('employee.dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pos.view')): ?>
                    <?php if(optional(auth()->user()->salon)->canUseFeature('POS System') && \App\Helpers\ModuleHelper::posEnabled()): ?>
                        <a href="<?php echo e(route('admin.pos.index', ['salon_slug' => $empMobSalonSlug, 'view' => 'barber'])); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('admin.pos.*') ? 'active' : ''); ?>">
                            <i class="fas fa-cash-register"></i>
                            <span>POS</span>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <a href="<?php echo e(route('employee.appointments.index', ['salon_slug' => $empMobSalonSlug])); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('employee.appointments.*') ? 'active' : ''); ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>Appts</span>
                </a>
                <a href="<?php echo e(route('profile.edit')); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('profile.edit') || request()->routeIs('employee.profile.*') ? 'active' : ''); ?>">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
                <a href="javascript:void(0)" class="mobile-bottom-nav-item" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                    <span>Menu</span>
                </a>

            <?php else: ?>
                
                <?php if(auth()->user()->hasRole('super_admin')): ?>
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
                <?php else: ?>
                    <?php
                        $mobileDashRoute = $salonSlug ? route('dashboard', ['salon_slug' => $salonSlug]) : route('home.dashboard');
                    ?>
                    <a href="<?php echo e($mobileDashRoute); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('dashboard') || request()->routeIs('home.dashboard') ? 'active' : ''); ?>">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pos.view')): ?>
                        <a href="<?php echo e($salonSlug ? route('admin.pos.index', ['salon_slug' => $salonSlug]) : '#'); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('admin.pos.*') ? 'active' : ''); ?>">
                            <i class="fas fa-cash-register"></i>
                            <span>Terminal</span>
                        </a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bookings.view')): ?>
                        <a href="<?php echo e($salonSlug ? route('admin.bookings.index', ['salon_slug' => $salonSlug]) : '#'); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('admin.bookings.*') ? 'active' : ''); ?>">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Bookings</span>
                        </a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customers.view')): ?>
                        <a href="<?php echo e($salonSlug ? route('admin.customers.index', ['salon_slug' => $salonSlug]) : '#'); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('admin.customers.*') ? 'active' : ''); ?>">
                            <i class="fas fa-users"></i>
                            <span>Clients</span>
                        </a>
                    <?php endif; ?>
                    <a href="javascript:void(0)" class="mobile-bottom-nav-item" id="mobileMenuBtn">
                        <i class="fas fa-bars"></i>
                        <span>Menu</span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
    <?php endif; ?>

<?php /**PATH C:\Ketan\projects\barber\resources\views/components/layout/mobile-bottom-nav.blade.php ENDPATH**/ ?>