@props(['salonSlug' => null, 'portalContext' => null])
<!-- Mobile Bottom Navigation Bar (Visible on mobile/tablet screens) -->
    @auth
        @php
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
                } elseif ($navUser && $navUser->hasRole('employee')) {
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
        @endphp
        <nav class="mobile-bottom-nav d-lg-none" id="mobileBottomNav">
            @if($portalContext === 'customer')
                @php
                    $custMobSalonSlug = $salonSlug ?? optional(auth()->user()->salon)->slug ?? request()->current_salon->slug ?? 'demo-salon';
                @endphp
                <a href="{{ route('customer.dashboard', ['salon_slug' => $custMobSalonSlug]) }}" class="mobile-bottom-nav-item {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('customer.appointments.create', ['salon_slug' => $custMobSalonSlug]) }}" class="mobile-bottom-nav-item {{ request()->routeIs('customer.appointments.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i>
                    <span>Book</span>
                </a>
                <a href="{{ route('customer.appointments.index', ['salon_slug' => $custMobSalonSlug]) }}" class="mobile-bottom-nav-item {{ request()->routeIs('customer.appointments.index') || request()->routeIs('customer.appointments.show') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Appts</span>
                </a>
                @if(\App\Helpers\ModuleHelper::loyaltyEnabled())
                    <a href="{{ route('customer.points.index', ['salon_slug' => $custMobSalonSlug]) }}" class="mobile-bottom-nav-item {{ request()->routeIs('customer.points.*') ? 'active' : '' }}">
                        <i class="fas fa-coins text-warning"></i>
                        <span>Points</span>
                    </a>
                @else
                    <a href="{{ route('profile.edit') }}" class="mobile-bottom-nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                @endif
                <a href="javascript:void(0)" class="mobile-bottom-nav-item" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                    <span>Menu</span>
                </a>

            @elseif($portalContext === 'employee')
                @php
                    $empMobSalonSlug = $salonSlug ?? optional(auth()->user()->salon)->slug ?? 'demo-salon';
                @endphp
                <a href="{{ route('employee.dashboard', ['salon_slug' => $empMobSalonSlug]) }}" class="mobile-bottom-nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                @can('pos.view')
                    @if(optional(auth()->user()->salon)->canUseFeature('POS System') && \App\Helpers\ModuleHelper::posEnabled())
                        <a href="{{ route('admin.pos.index', ['salon_slug' => $empMobSalonSlug, 'view' => 'barber']) }}" class="mobile-bottom-nav-item {{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">
                            <i class="fas fa-cash-register"></i>
                            <span>POS</span>
                        </a>
                    @endif
                @endcan
                <a href="{{ route('employee.appointments.index', ['salon_slug' => $empMobSalonSlug]) }}" class="mobile-bottom-nav-item {{ request()->routeIs('employee.appointments.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Appts</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="mobile-bottom-nav-item {{ request()->routeIs('profile.edit') || request()->routeIs('employee.profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
                <a href="javascript:void(0)" class="mobile-bottom-nav-item" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                    <span>Menu</span>
                </a>

            @else
                {{-- Admin / Super Admin Portal --}}
                @if(auth()->user()->hasRole('super_admin'))
                    <a href="{{ route('admin.super.dashboard') }}" class="mobile-bottom-nav-item {{ request()->routeIs('admin.super.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.salons.index') }}" class="mobile-bottom-nav-item {{ request()->routeIs('admin.salons.*') ? 'active' : '' }}">
                        <i class="fas fa-store"></i>
                        <span>Salons</span>
                    </a>
                    <a href="{{ route('admin.plans.index') }}" class="mobile-bottom-nav-item {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i>
                        <span>Plans</span>
                    </a>
                    <a href="{{ route('admin.subscriptions.index') }}" class="mobile-bottom-nav-item {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                        <i class="fas fa-crown"></i>
                        <span>Subs</span>
                    </a>
                    <a href="javascript:void(0)" class="mobile-bottom-nav-item" id="mobileMenuBtn">
                        <i class="fas fa-bars"></i>
                        <span>Menu</span>
                    </a>
                @else
                    @php
                        $mobileDashRoute = $salonSlug ? route('dashboard', ['salon_slug' => $salonSlug]) : route('home.dashboard');
                    @endphp
                    <a href="{{ $mobileDashRoute }}" class="mobile-bottom-nav-item {{ request()->routeIs('dashboard') || request()->routeIs('home.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    @can('pos.view')
                        <a href="{{ $salonSlug ? route('admin.pos.index', ['salon_slug' => $salonSlug]) : '#' }}" class="mobile-bottom-nav-item {{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">
                            <i class="fas fa-cash-register"></i>
                            <span>Terminal</span>
                        </a>
                    @endcan
                    @can('bookings.view')
                        <a href="{{ $salonSlug ? route('admin.bookings.index', ['salon_slug' => $salonSlug]) : '#' }}" class="mobile-bottom-nav-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Bookings</span>
                        </a>
                    @endcan
                    @can('customers.view')
                        <a href="{{ $salonSlug ? route('admin.customers.index', ['salon_slug' => $salonSlug]) : '#' }}" class="mobile-bottom-nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Clients</span>
                        </a>
                    @endcan
                    <a href="javascript:void(0)" class="mobile-bottom-nav-item" id="mobileMenuBtn">
                        <i class="fas fa-bars"></i>
                        <span>Menu</span>
                    </a>
                @endif
            @endif
        </nav>
    @endauth

