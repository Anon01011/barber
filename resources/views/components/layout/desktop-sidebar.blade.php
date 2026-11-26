
@props(['salonSlug' => null, 'portalContext' => null])
@php
    $salonSlug = $salonSlug 
        ?? request()->route('salon_slug') 
        ?? optional(auth()->user()?->salon)->slug 
        ?? (auth()->user()?->salon_id ? optional(\App\Models\Salon::find(auth()->user()->salon_id))->slug : null)
        ?? (app()->bound('current_salon') ? optional(app('current_salon'))->slug : null)
        ?? session('current_salon_slug');
@endphp
@inject('settings', 'App\Services\SettingsService')
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <img src="{{ $settings->getLogoUrl() }}" alt="Logo" class="sidebar-brand-icon"
                        style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%;">
                    <span class="sidebar-brand-text">
                        {{ $settings->get('business_name') === 'My Business' ? (optional(auth()->user()->salon)->name ?? $settings->get('business_name')) : ($settings->get('business_name') ?? optional(auth()->user()->salon)->name ?? 'Salon CMS') }}
                    </span>
                </div>
            </div>
            <div class="sidebar-menu">
                @php
                    $sidebarUser = auth()->user();
                    // Clean mutually exclusive portal context:
                    // 1. If user is currently on customer routes -> customer portal
                    // 2. If user is currently on employee routes -> employee portal
                    // 3. Otherwise by primary role: admin/manager -> admin, employee -> employee, customer -> customer
                    if (request()->routeIs('customer.*')) {
                        $portalContext = 'customer';
                    } elseif (request()->routeIs('employee.*')) {
                        $portalContext = 'employee';
                    } elseif ($sidebarUser->hasAnyRole(['super_admin', 'salon_admin', 'manager'])) {
                        $portalContext = 'admin';
                    } elseif ($sidebarUser->hasRole('customer') && !$sidebarUser->hasRole('employee')) {
                        $portalContext = 'customer';
                    } elseif ($sidebarUser->hasRole('customer') && $sidebarUser->customer()->exists() && !$sidebarUser->employee()->exists()) {
                        $portalContext = 'customer';
                    } elseif ($sidebarUser->hasRole('employee') || $sidebarUser->hasRole('staff')) {
                        $portalContext = 'employee';
                    } elseif ($sidebarUser->hasRole('customer')) {
                        $portalContext = 'customer';
                    } else {
                        $portalContext = 'admin';
                    }
                @endphp

                @if($portalContext === 'customer')
                    <!-- Customer Menu Items Only -->
                    @php
                        $custSalonSlug = optional($sidebarUser->salon)->slug ?? request()->current_salon->slug ?? 'demo-salon';
                    @endphp
                    <a href="{{ route('customer.dashboard', ['salon_slug' => $custSalonSlug]) }}"
                        class="sidebar-item {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                        <i class="fas fa-home sidebar-icon"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('customer.appointments.create', ['salon_slug' => $custSalonSlug]) }}"
                        class="sidebar-item {{ request()->routeIs('customer.appointments.create') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Book Appointment">
                        <i class="fas fa-plus-circle sidebar-icon"></i>
                        <span class="sidebar-text">Book Appointment</span>
                    </a>
                    <a href="{{ route('customer.appointments.index', ['salon_slug' => $custSalonSlug]) }}"
                        class="sidebar-item {{ request()->routeIs('customer.appointments.index') || request()->routeIs('customer.appointments.show') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="My Appointments">
                        <i class="fas fa-calendar-alt sidebar-icon"></i>
                        <span class="sidebar-text">My Appointments</span>
                    </a>
                    @if(\App\Helpers\ModuleHelper::loyaltyEnabled() && (optional($sidebarUser->salon)->hasLoyaltyProgram() || (method_exists(optional($sidebarUser->salon), 'isOnTrial') && optional($sidebarUser->salon)->isOnTrial())))
                        @php
                            $customerRecord = $sidebarUser->customer;
                            $points = $customerRecord ? ($customerRecord->loyalty_points ?? 0) : 0;
                        @endphp
                        <a href="{{ route('customer.points.index', ['salon_slug' => $custSalonSlug]) }}"
                            class="sidebar-item {{ request()->routeIs('customer.points.*') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="My Loyalty Points & Rewards">
                            <i class="fas fa-coins text-warning sidebar-icon"></i>
                            <span class="sidebar-text d-flex align-items-center justify-content-between flex-grow-1 me-2">
                                <span>Loyalty Points</span>
                                <span class="badge bg-warning text-dark fw-bold px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;">{{ number_format($points) }}</span>
                            </span>
                        </a>
                    @endif
                    <a href="{{ route('profile.edit') }}"
                        class="sidebar-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" data-bs-toggle="tooltip"
                        data-bs-placement="right" title="Profile">
                        <i class="fas fa-user sidebar-icon"></i>
                        <span class="sidebar-text">Profile</span>
                    </a>

                @elseif($portalContext === 'employee')
                    <!-- Employee Menu Items Only -->
                    <a href="{{ route('employee.dashboard', ['salon_slug' => optional($sidebarUser->salon)->slug]) }}"
                        class="sidebar-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                        <i class="fas fa-home sidebar-icon"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    @can('pos.view')
                        @if(optional($sidebarUser->salon)->canUseFeature('POS System') && \App\Helpers\ModuleHelper::posEnabled())
                        <a href="{{ route('admin.pos.index', ['salon_slug' => optional($sidebarUser->salon)->slug, 'view' => 'barber']) }}"
                            class="sidebar-item {{ request()->routeIs('admin.pos.index') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="POS">
                            <i class="fas fa-cash-register sidebar-icon"></i>
                            <span class="sidebar-text">POS</span>
                        </a>
                        <a href="{{ route('admin.pos.sales.index', ['salon_slug' => optional($sidebarUser->salon)->slug]) }}"
                            class="sidebar-item {{ request()->routeIs('admin.pos.sales.*') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="Sales History">
                            <i class="fas fa-receipt sidebar-icon"></i>
                            <span class="sidebar-text">Sales History</span>
                        </a>
                        @endif
                    @endcan
                    @can('bookings.view')
                        @if(optional($sidebarUser->salon)->canUseFeature('Booking System') && \App\Helpers\ModuleHelper::appointmentsEnabled())
                        <a href="{{ route('employee.appointments.index', ['salon_slug' => optional($sidebarUser->salon)->slug]) }}"
                            class="sidebar-item {{ request()->routeIs('employee.appointments.index') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="My Appointments">
                            <i class="fas fa-calendar-check sidebar-icon"></i>
                            <span class="sidebar-text">My Appointments</span>
                        </a>
                        <a href="{{ route('employee.appointments.today', ['salon_slug' => optional($sidebarUser->salon)->slug]) }}"
                            class="sidebar-item {{ request()->routeIs('employee.appointments.today') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="Today's Appointments">
                            <i class="fas fa-calendar-day sidebar-icon"></i>
                            <span class="sidebar-text">Today's Appointments</span>
                        </a>
                        <a href="{{ route('employee.appointments.upcoming', ['salon_slug' => optional($sidebarUser->salon)->slug]) }}"
                            class="sidebar-item {{ request()->routeIs('employee.appointments.upcoming') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="Upcoming">
                            <i class="fas fa-calendar-week sidebar-icon"></i>
                            <span class="sidebar-text">Upcoming</span>
                        </a>
                        <a href="{{ route('employee.appointments.completed', ['salon_slug' => optional($sidebarUser->salon)->slug]) }}"
                            class="sidebar-item {{ request()->routeIs('employee.appointments.completed') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="Completed">
                            <i class="fas fa-check-double sidebar-icon"></i>
                            <span class="sidebar-text">Completed</span>
                        </a>
                        <a href="{{ route('employee.earnings', ['salon_slug' => optional($sidebarUser->salon)->slug]) }}"
                            class="sidebar-item {{ request()->routeIs('employee.earnings') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="My Earnings">
                            <i class="fas fa-dollar-sign sidebar-icon"></i>
                            <span class="sidebar-text">My Earnings</span>
                        </a>
                        @endif
                    @endcan
                    <a href="{{ route('profile.edit') }}"
                        class="sidebar-item {{ request()->routeIs('profile.edit') || request()->routeIs('employee.profile.*') ? 'active' : '' }}" data-bs-toggle="tooltip"
                        data-bs-placement="right" title="Profile">
                        <i class="fas fa-user sidebar-icon"></i>
                        <span class="sidebar-text">Profile</span>
                    </a>

                @else
                    <!-- Admin / Manager Menu Items -->
                    @php
                        $dashRoute = $sidebarUser->hasRole('super_admin')
                            ? route('admin.super.dashboard')
                            : ($salonSlug ? route('dashboard', ['salon_slug' => $salonSlug]) : route('home.dashboard'));
                    @endphp
                    <a href="{{ $dashRoute }}"
                        class="sidebar-item {{ request()->routeIs('dashboard') || request()->routeIs('admin.super.dashboard') || request()->routeIs('home.dashboard') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                        <i class="fas fa-home sidebar-icon"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>

                        <!-- Bookings Section -->
                        @can('bookings.view')
                            @if(optional(auth()->user()->salon)->canUseFeature('Booking System') && \App\Helpers\ModuleHelper::appointmentsEnabled())
                                <div class="sidebar-section">
                                    <a href="#" class="sidebar-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}"
                                        data-bs-toggle="collapse" data-bs-target="#bookingsSubmenu"
                                        aria-expanded="{{ request()->routeIs('admin.bookings.*') ? 'true' : 'false' }}"
                                        data-bs-placement="right" title="Bookings">
                                        <i class="fas fa-calendar-alt sidebar-icon"></i>
                                        <span class="sidebar-text">Bookings</span>
                                        <i
                                            class="fas fa-chevron-{{ request()->routeIs('admin.bookings.*') ? 'up' : 'down' }} ms-auto chevron"></i>
                                    </a>
                                    <div class="collapse {{ request()->routeIs('admin.bookings.*') ? 'show' : '' }}" id="bookingsSubmenu">
                                        <div class="px-4 py-1">
                                            <a href="{{ route('admin.bookings.index', ['salon_slug' => $salonSlug]) }}"
                                                class="sidebar-item sub-item {{ request()->routeIs('admin.bookings.index') ? 'active' : '' }}"
                                                data-bs-toggle="tooltip" data-bs-placement="right" title="All Bookings">
                                                <i class="fas fa-list-ul sidebar-icon"></i>
                                                <span class="sidebar-text">All Bookings</span>
                                            </a>

                                        </div>
                                        @can('bookings.view_stats')
                                            <div class="px-4 py-1">
                                                <a href="{{ route('admin.bookings.stats', ['salon_slug' => $salonSlug]) }}"
                                                    class="sidebar-item sub-item {{ request()->routeIs('admin.bookings.stats') ? 'active' : '' }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Booking Stats">
                                                    <i class="fas fa-chart-bar sidebar-icon"></i>
                                                    <span class="sidebar-text">Booking Stats</span>
                                                </a>
                                            </div>
                                        @endcan
                                    </div>
                                </div>

                                <a href="{{ route('admin.guest-booking.index', ['salon_slug' => $salonSlug]) }}"
                                    class="sidebar-item {{ request()->routeIs('admin.guest-booking.*') ? 'active' : '' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Guest Booking">
                                    <i class="fas fa-link sidebar-icon"></i>
                                    <span class="sidebar-text">Guest Booking</span>
                                </a>
                            @endif
                        @endcan

                        <!-- POS Section -->
                        @can('pos.view')
                            @if(optional(auth()->user()->salon)->canUseFeature('POS System') && \App\Helpers\ModuleHelper::posEnabled())
                                @php $posBusinessType = optional(auth()->user()->salon)->business_type; @endphp
                                {{-- Standalone Quick Sale --}}
                                <a href="{{ route('admin.bookings.index', ['salon_slug' => $salonSlug]) }}"
                                    class="sidebar-item {{ request()->routeIs('admin.bookings.index') ? 'active' : '' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Quick Sale">
                                    <i class="fas fa-bolt sidebar-icon text-warning"></i>
                                    <span class="sidebar-text">Quick Sale</span>
                                </a>

                                <div class="sidebar-section">
                                    <a href="#" class="sidebar-item {{ request()->routeIs('admin.pos.*') ? 'active' : '' }}"
                                        data-bs-toggle="collapse" data-bs-target="#posSubmenu"
                                        aria-expanded="{{ request()->routeIs('admin.pos.*') ? 'true' : 'false' }}" data-bs-placement="right"
                                        title="{{ $posBusinessType === 'barber' ? 'Barber POS' : ($posBusinessType === 'both' ? 'Salon & Barber POS' : 'POS Management') }}">
                                        <i class="fas {{ $posBusinessType === 'barber' ? 'fa-scissors text-warning' : ($posBusinessType === 'both' ? 'fa-store text-warning' : 'fa-cash-register') }} sidebar-icon"></i>
                                        <span class="sidebar-text">{{ $posBusinessType === 'barber' ? 'Barber POS' : ($posBusinessType === 'both' ? 'Salon & Barber POS' : 'POS Management') }}</span>
                                        <i
                                            class="fas fa-chevron-{{ request()->routeIs('admin.pos.*') ? 'up' : 'down' }} ms-auto chevron"></i>
                                    </a>
                                    <div class="collapse {{ request()->routeIs('admin.pos.*') ? 'show' : '' }}" id="posSubmenu">
                                        <div class="px-4 py-1">
                                            @if($posBusinessType === 'both')
                                                {{-- Both: Show Salon Terminal and Barber Terminal separately --}}
                                                <a href="{{ route('admin.pos.index', ['salon_slug' => $salonSlug]) }}"
                                                    class="sidebar-item sub-item {{ request()->routeIs('admin.pos.index') && !request()->query('view') ? 'active' : '' }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Salon Terminal">
                                                    <i class="fas fa-shopping-cart sidebar-icon"></i>
                                                    <span class="sidebar-text">Salon Terminal</span>
                                                </a>
                                                <a href="{{ route('admin.pos.index', ['salon_slug' => $salonSlug]) }}?view=barber"
                                                    class="sidebar-item sub-item {{ request()->routeIs('admin.pos.index') && request()->query('view') === 'barber' ? 'active' : '' }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Barber Terminal">
                                                    <i class="fas fa-scissors sidebar-icon text-warning"></i>
                                                    <span class="sidebar-text">Barber Terminal</span>
                                                </a>
                                            @else
                                                {{-- Single terminal for barber or salon only --}}
                                                <a href="{{ route('admin.pos.index', ['salon_slug' => $salonSlug]) }}"
                                                    class="sidebar-item sub-item {{ request()->routeIs('admin.pos.index') ? 'active' : '' }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="{{ $posBusinessType === 'barber' ? 'Barber Terminal' : 'POS Terminal' }}">
                                                    <i class="fas {{ $posBusinessType === 'barber' ? 'fa-scissors' : 'fa-shopping-cart' }} sidebar-icon"></i>
                                                    <span class="sidebar-text">{{ $posBusinessType === 'barber' ? 'Barber Terminal' : 'POS Terminal' }}</span>
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.pos.sales.index', ['salon_slug' => $salonSlug]) }}"
                                                class="sidebar-item sub-item {{ request()->routeIs('admin.pos.sales.*') ? 'active' : '' }}"
                                                data-bs-toggle="tooltip" data-bs-placement="right" title="Sales History">
                                                <i class="fas fa-receipt sidebar-icon"></i>
                                                <span class="sidebar-text">Sales History</span>
                                            </a>
                                            <a href="{{ route('admin.pos.analytics.index', ['salon_slug' => $salonSlug]) }}"
                                                class="sidebar-item sub-item {{ request()->routeIs('admin.pos.analytics.*') ? 'active' : '' }}"
                                                data-bs-toggle="tooltip" data-bs-placement="right" title="Analytics">
                                                <i class="fas fa-chart-line sidebar-icon"></i>
                                                <span class="sidebar-text">Analytics</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @endcan

                        <!-- Customers -->
                        @can('customers.view')
                            @if(optional(auth()->user()->salon)->canUseFeature('Customer Management') && \App\Helpers\ModuleHelper::customersEnabled())
                                <a href="{{ route('admin.customers.index', ['salon_slug' => $salonSlug]) }}"
                                    class="sidebar-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Customers">
                                    <i class="fas fa-users sidebar-icon"></i>
                                    <span class="sidebar-text">Customers</span>
                                </a>
                            @endif
                        @endcan

                        <!-- Customer Feedback & Reviews -->
                        @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('salon_admin') || auth()->user()->can('marketing.manage_reviews'))
                            @if(\App\Helpers\ModuleHelper::feedbackEnabled() && (optional(auth()->user()->salon)->hasFeedbackModule() || (method_exists(optional(auth()->user()->salon), 'isOnTrial') && optional(auth()->user()->salon)->isOnTrial())))
                                <a href="{{ route('admin.feedback.index', ['salon_slug' => $salonSlug]) }}"
                                    class="sidebar-item {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Feedback & Reviews">
                                    <i class="fas fa-star sidebar-icon"></i>
                                    <span class="sidebar-text">Feedback &amp; Reviews</span>
                                </a>
                            @endif
                        @endif

                        <!-- Staff & Commissions -->
                        @can('staff.view')
                            @if(optional(auth()->user()->salon)->canUseFeature('Staff Management') && \App\Helpers\ModuleHelper::staffEnabled() && !auth()->user()->hasRole('employee'))
                                <a href="{{ route('admin.staff.index', ['salon_slug' => $salonSlug]) }}"
                                    class="sidebar-item {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}" data-bs-toggle="tooltip"
                                    data-bs-placement="right" title="Staff">
                                    <i class="fas fa-users sidebar-icon"></i>
                                    <span class="sidebar-text">Staff</span>
                                </a>
                            @endif
                        @endcan

                        @can('commissions.view')
                            @if(optional(auth()->user()->salon)->canUseFeature('Commission Management') && \App\Helpers\ModuleHelper::isEnabled('commissions'))
                                <a href="{{ route('admin.commissions.index', ['salon_slug' => $salonSlug]) }}"
                                    class="sidebar-item {{ request()->routeIs('admin.commissions.*') ? 'active' : '' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Commissions">
                                    <i class="fas fa-percent sidebar-icon"></i>
                                    <span class="sidebar-text">Commissions</span>
                                </a>
                            @endif
                        @endcan

                        <!-- Catalog (Services, Packages, Memberships, Inventory) -->
                        @can('services.view')
                            @if((optional(auth()->user()->salon)->canUseFeature('Service Management') || optional(auth()->user()->salon)->canUseFeature('Booking System')) && \App\Helpers\ModuleHelper::servicesEnabled())
                                <a href="{{ route('admin.services.categories.index', ['salon_slug' => $salonSlug]) }}"
                                    class="sidebar-item {{ request()->routeIs('admin.services.categories.*') ? 'active' : '' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Categories">
                                    <i class="fas fa-tags sidebar-icon"></i>
                                    <span class="sidebar-text">Categories</span>
                                </a>
                            @endif
                        @endcan

                        @can('packages.view')
                            @if(optional(auth()->user()->salon)->canUseFeature('Packages') && \App\Helpers\ModuleHelper::packagesEnabled())
                                <a href="{{ route('admin.packages.index', ['salon_slug' => $salonSlug]) }}"
                                    class="sidebar-item {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Packages">
                                    <i class="fas fa-gift sidebar-icon"></i>
                                    <span class="sidebar-text">Packages</span>
                                </a>
                            @endif
                        @endcan

                        @can('memberships.view')
                            @if(optional(auth()->user()->salon)->canUseFeature('Memberships') && \App\Helpers\ModuleHelper::membershipsEnabled())
                                <a href="{{ route('admin.memberships.index', ['salon_slug' => $salonSlug]) }}"
                                    class="sidebar-item {{ request()->routeIs('admin.memberships.*') ? 'active' : '' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Memberships">
                                    <i class="fas fa-id-card sidebar-icon"></i>
                                    <span class="sidebar-text">Memberships</span>
                                </a>
                            @endif
                        @endcan

                        @can('inventory.view')
                            @if(optional(auth()->user()->salon)->canUseFeature('Inventory Management') && \App\Helpers\ModuleHelper::inventoryEnabled())
                                <a href="{{ route('admin.inventory.index', ['salon_slug' => $salonSlug]) }}"
                                    class="sidebar-item {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Inventory">
                                    <i class="fas fa-boxes sidebar-icon"></i>
                                    <span class="sidebar-text">Inventory</span>
                                </a>
                            @endif
                        @endcan

                        <!-- AI Copilot & Automations -->
                        @if(optional(auth()->user()->salon)->canUseFeature('AI Insights & Automation') && \App\Helpers\ModuleHelper::aiEnabled())
                            <a href="{{ route('admin.ai.hub', ['salon_slug' => $salonSlug]) }}"
                                class="sidebar-item {{ request()->routeIs('admin.ai.*') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="AI Copilot & Automations">
                                <i class="fas fa-robot sidebar-icon text-warning"></i>
                                <span class="sidebar-text fw-bold">AI Copilot</span>
                                <span class="badge bg-purple text-white ms-auto" style="font-size: 0.65rem;">PRO AI</span>
                            </a>
                        @endif

                        <!-- Reports -->
                        @can('reports.view')
                            @if(optional(auth()->user()->salon)->canUseFeature('Analytics & Reports') && \App\Helpers\ModuleHelper::reportsEnabled())
                                <div class="sidebar-section">
                                    <a href="#" class="sidebar-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                                        data-bs-toggle="collapse" data-bs-target="#reportsSubmenu"
                                        aria-expanded="{{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }}"
                                        data-bs-placement="right" title="Reports">
                                        <i class="fas fa-chart-pie sidebar-icon"></i>
                                        <span class="sidebar-text">Reports</span>
                                        <i
                                            class="fas fa-chevron-{{ request()->routeIs('admin.reports.*') ? 'up' : 'down' }} ms-auto chevron"></i>
                                    </a>
                                    <div class="collapse {{ request()->routeIs('admin.reports.*') ? 'show' : '' }}" id="reportsSubmenu">
                                        <div class="px-4 py-1">
                                            @if(optional(auth()->user()->salon)->canUseFeature('AI Insights & Automation') && \App\Helpers\ModuleHelper::aiEnabled())
                                                <a href="{{ route('admin.reports.ai-insights', ['salon_slug' => $salonSlug]) }}"
                                                    class="sidebar-item sub-item {{ request()->routeIs('admin.reports.ai-insights') ? 'active' : '' }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Smart AI Insights">
                                                    <i class="fas fa-brain sidebar-icon text-warning"></i>
                                                    <span class="sidebar-text fw-bold">Smart AI Insights</span>
                                                    <span class="badge bg-purple text-white ms-auto" style="font-size: 0.65rem;">AI</span>
                                                </a>
                                            @endif
                                            @can('reports.sales')
                                                <a href="{{ route('admin.reports.sales', ['salon_slug' => $salonSlug]) }}"
                                                    class="sidebar-item sub-item {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Sales Reports">
                                                    <i class="fas fa-chart-line sidebar-icon"></i>
                                                    <span class="sidebar-text">Sales Reports</span>
                                                </a>
                                            @endcan
                                            @can('reports.appointments')
                                                <a href="{{ route('admin.reports.appointments', ['salon_slug' => $salonSlug]) }}"
                                                    class="sidebar-item sub-item {{ request()->routeIs('admin.reports.appointments') ? 'active' : '' }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Appointment Reports">
                                                    <i class="fas fa-calendar-alt sidebar-icon"></i>
                                                    <span class="sidebar-text">Appointment Reports</span>
                                                </a>
                                            @endcan
                                            @can('reports.staff')
                                                <a href="{{ route('admin.reports.staff', ['salon_slug' => $salonSlug]) }}"
                                                    class="sidebar-item sub-item {{ request()->routeIs('admin.reports.staff') ? 'active' : '' }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Staff Performance">
                                                    <i class="fas fa-users sidebar-icon"></i>
                                                    <span class="sidebar-text">Staff Performance</span>
                                                </a>
                                            @endcan
                                            @can('customers.view')
                                                <a href="{{ route('admin.reports.customers', ['salon_slug' => $salonSlug]) }}"
                                                    class="sidebar-item sub-item {{ request()->routeIs('admin.reports.customers') ? 'active' : '' }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Customer Insights">
                                                    <i class="fas fa-user-friends sidebar-icon"></i>
                                                    <span class="sidebar-text">Customer Insights</span>
                                                </a>
                                            @endcan
                                            @if(optional(auth()->user()->salon)->canUseFeature('Commission Management') && \App\Helpers\ModuleHelper::isEnabled('commissions'))
                                                @can('commissions.view_reports')
                                                    <a href="{{ route('admin.commissions.reports', ['salon_slug' => $salonSlug]) }}"
                                                        class="sidebar-item sub-item {{ request()->routeIs('admin.commissions.reports') ? 'active' : '' }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="right" title="Commissions">
                                                        <i class="fas fa-hand-holding-usd sidebar-icon"></i>
                                                        <span class="sidebar-text">Commissions</span>
                                                    </a>
                                                    <a href="{{ route('admin.reports.commissions.payouts', ['salon_slug' => $salonSlug]) }}"
                                                        class="sidebar-item sub-item {{ request()->routeIs('admin.reports.commissions.payouts') ? 'active' : '' }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="right" title="Payouts">
                                                        <i class="fas fa-wallet sidebar-icon"></i>
                                                        <span class="sidebar-text">Payouts</span>
                                                    </a>
                                                @endcan
                                            @endif

                                            @if(optional(auth()->user()->salon)->canUseFeature('Inventory Management') && \App\Helpers\ModuleHelper::inventoryEnabled())
                                                @can('reports.inventory')
                                                    <a href="{{ route('admin.inventory.reports.index', ['salon_slug' => $salonSlug]) }}"
                                                        class="sidebar-item sub-item {{ request()->routeIs('admin.inventory.reports*') ? 'active' : '' }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="right" title="Inventory & Consumables">
                                                        <i class="fas fa-boxes sidebar-icon"></i>
                                                        <span class="sidebar-text">Inventory Reports</span>
                                                    </a>
                                                @endcan
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endcan

                        <!-- Salon Management -->
                        @if(auth()->user()->hasAnyRole(['super_admin', 'salon_admin']) || auth()->user()->can('salon.manage_settings') || auth()->user()->can('salon.manage_branches'))
                            <div class="sidebar-section">
                                <div class="sidebar-section-header">
                                    <i class="fas fa-store sidebar-icon"></i>
                                    <span class="sidebar-text">Salon Management</span>
                                </div>

                                {{-- Salon Settings removed - using SaaS Settings instead to avoid duplication --}}

                                {{-- Salon Settings for Salon Owners --}}
                                @if(auth()->user()->hasAnyRole(['salon_admin', 'manager']))
                                    <a href="{{ route('admin.saas.settings.mail', ['salon_slug' => $salonSlug]) }}"
                                        class="sidebar-item {{ request()->routeIs('admin.saas.settings.*') ? 'active' : '' }}"
                                        data-bs-toggle="tooltip" data-bs-placement="right" title="Salon Settings">
                                        <i class="fas fa-cogs sidebar-icon"></i>
                                        <span class="sidebar-text">Salon Settings</span>
                                    </a>
                                @endif

                                @if(\App\Helpers\ModuleHelper::dataBackupEnabled() && (auth()->user()->hasRole('super_admin') || (auth()->user()->hasRole('salon_admin') && optional(auth()->user()->salon)->canUseFeature('data_backup'))))
                                    <a href="{{ route('admin.backup.index', ['salon_slug' => $salonSlug]) }}"
                                        class="sidebar-item {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}"
                                        data-bs-toggle="tooltip" data-bs-placement="right" title="Data Backup &amp; Restore">
                                        <i class="fas fa-database sidebar-icon"></i>
                                        <span class="sidebar-text">Data Backup</span>
                                    </a>
                                @endif

                                @can('salon.manage_branches')
                                    @if(optional(auth()->user()->salon)->canUseFeature('Multi-Branch Support') && \App\Helpers\ModuleHelper::branchesEnabled())
                                        <a href="{{ route('admin.branches.index', ['salon_slug' => $salonSlug]) }}"
                                            class="sidebar-item {{ request()->routeIs('admin.branches.*') ? 'active' : '' }}"
                                            data-bs-toggle="tooltip" data-bs-placement="right" title="Branches">
                                            <i class="fas fa-building sidebar-icon"></i>
                                            <span class="sidebar-text">Branches</span>
                                        </a>
                                    @endif
                                @endcan

                                {{-- Roles & Permissions for Salon Admin --}}
                                @if(auth()->user()->hasRole('salon_admin') && optional(auth()->user()->salon)->canUseFeature('Role Management') && \App\Helpers\ModuleHelper::isEnabled('roles'))
                                    <a href="{{ route('admin.roles.index', ['salon_slug' => $salonSlug]) }}"
                                        class="sidebar-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                                        data-bs-toggle="tooltip" data-bs-placement="right" title="Roles & Permissions">
                                        <i class="fas fa-user-shield sidebar-icon"></i>
                                        <span class="sidebar-text">Roles & Permissions</span>
                                    </a>
                                @endif
                            </div>
                        @endif

                        <!-- Employee Menu Items -->
                        @role('employee')
                        @if(!auth()->user()->hasAnyRole(['salon_admin', 'super_admin']))
                        @can('pos.view')
                            @if(optional(auth()->user()->salon)->canUseFeature('POS System') && \App\Helpers\ModuleHelper::posEnabled())
                            <a href="{{ route('admin.pos.index', ['salon_slug' => optional(auth()->user()->salon)->slug, 'view' => 'barber']) }}"
                                class="sidebar-item {{ request()->routeIs('admin.pos.index') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="POS">
                                <i class="fas fa-cash-register sidebar-icon"></i>
                                <span class="sidebar-text">POS</span>
                            </a>
                            <a href="{{ route('admin.pos.sales.index', ['salon_slug' => optional(auth()->user()->salon)->slug]) }}"
                                class="sidebar-item {{ request()->routeIs('admin.pos.sales.*') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Sales History">
                                <i class="fas fa-receipt sidebar-icon"></i>
                                <span class="sidebar-text">Sales History</span>
                            </a>
                            @endif
                        @endcan
                        @can('bookings.view')
                            @if(optional(auth()->user()->salon)->canUseFeature('Booking System') && \App\Helpers\ModuleHelper::appointmentsEnabled())
                            <a href="{{ route('employee.appointments.index', ['salon_slug' => optional(auth()->user()->salon)->slug]) }}"
                                class="sidebar-item {{ request()->routeIs('employee.appointments.index') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="My Appointments">
                                <i class="fas fa-calendar-check sidebar-icon"></i>
                                <span class="sidebar-text">My Appointments</span>
                            </a>
                            <a href="{{ route('employee.appointments.today', ['salon_slug' => optional(auth()->user()->salon)->slug]) }}"
                                class="sidebar-item {{ request()->routeIs('employee.appointments.today') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Today's Appointments">
                                <i class="fas fa-calendar-day sidebar-icon"></i>
                                <span class="sidebar-text">Today's Appointments</span>
                            </a>
                            <a href="{{ route('employee.appointments.upcoming', ['salon_slug' => optional(auth()->user()->salon)->slug]) }}"
                                class="sidebar-item {{ request()->routeIs('employee.appointments.upcoming') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Upcoming">
                                <i class="fas fa-calendar-week sidebar-icon"></i>
                                <span class="sidebar-text">Upcoming</span>
                            </a>
                            <a href="{{ route('employee.appointments.completed', ['salon_slug' => optional(auth()->user()->salon)->slug]) }}"
                                class="sidebar-item {{ request()->routeIs('employee.appointments.completed') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Completed">
                                <i class="fas fa-check-double sidebar-icon"></i>
                                <span class="sidebar-text">Completed</span>
                            </a>
                            <a href="{{ route('employee.earnings', ['salon_slug' => optional(auth()->user()->salon)->slug]) }}"
                                class="sidebar-item {{ request()->routeIs('employee.earnings') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="My Earnings">
                                <i class="fas fa-dollar-sign sidebar-icon"></i>
                                <span class="sidebar-text">My Earnings</span>
                            </a>
                            @endif
                        @endcan
                        <a href="{{ route('profile.edit') }}"
                            class="sidebar-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Profile">
                            <i class="fas fa-user sidebar-icon"></i>
                            <span class="sidebar-text">Profile</span>
                        </a>
                        @endif
                        @endrole

                        <!-- System Management Section - Only for Super Admin -->
                        @role('super_admin')
                        <div class="sidebar-section">
                            <div class="sidebar-section-header">
                                <i class="fas fa-cogs sidebar-icon"></i>
                                <span class="sidebar-text">System Management</span>
                            </div>

                            <!-- Salons Management -->
                            <a href="{{ route('admin.salons.index') }}"
                                class="sidebar-item {{ request()->routeIs('admin.salons.*') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Salons">
                                <i class="fas fa-store sidebar-icon"></i>
                                <span class="sidebar-text">Salons</span>
                            </a>

                            <!-- Plans Management -->
                            <a href="{{ route('admin.plans.index') }}"
                                class="sidebar-item {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Plans">
                                <i class="fas fa-tags sidebar-icon"></i>
                                <span class="sidebar-text">Plans</span>
                            </a>

                            <!-- Subscriptions Management -->
                            <a href="{{ route('admin.subscriptions.index') }}"
                                class="sidebar-item {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Subscriptions">
                                <i class="fas fa-crown sidebar-icon"></i>
                                <span class="sidebar-text">Subscriptions</span>
                            </a>

                            <!-- Payments Management -->
                            <a href="{{ route('admin.payments.index') }}"
                                class="sidebar-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Payments">
                                <i class="fas fa-credit-card sidebar-icon"></i>
                                <span class="sidebar-text">Payments</span>
                            </a>

                            <!-- Reports -->
                            <a href="{{ route('admin.saas.reports.index') }}"
                                class="sidebar-item {{ request()->routeIs('admin.saas.reports.*') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Reports">
                                <i class="fas fa-chart-bar sidebar-icon"></i>
                                <span class="sidebar-text">Reports</span>
                            </a>

                            <!-- Modules Management -->
                            <a href="{{ route('admin.modules.index') }}"
                                class="sidebar-item {{ request()->routeIs('admin.modules.*') ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Modules">
                                <i class="fas fa-puzzle-piece sidebar-icon"></i>
                                <span class="sidebar-text">Modules</span>
                            </a>

                            @can('system.manage_roles')
                                <a href="{{ route('admin.super.roles.index') }}"
                                    class="sidebar-item {{ request()->routeIs('admin.super.roles.*') ? 'active' : '' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Roles & Permissions">
                                    <i class="fas fa-user-shield sidebar-icon"></i>
                                    <span class="sidebar-text">Roles & Permissions</span>
                                </a>
                            @endcan
                            @can('system.manage_settings')
                                <a href="{{ route('admin.system-settings.index') }}"
                                    class="sidebar-item {{ request()->routeIs('admin.system-settings.*') ? 'active' : '' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="System Settings">
                                    <i class="fas fa-cog sidebar-icon"></i>
                                    <span class="sidebar-text">System Settings</span>
                                </a>
                            @endcan
                        </div>
                        @endrole
                @endif {{-- end $portalContext --}}
            </div> {{-- end .sidebar-menu --}}
        </div> {{-- end .sidebar --}}
    <!-- Mobile Sidebar Backdrop -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

