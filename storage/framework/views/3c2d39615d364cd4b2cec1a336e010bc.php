<?php $__env->startPush('styles'); ?>
    <style>
        /* Modern Dashboard Styles */
        .dashboard-container {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        /* Modern Card Styles */
        .card-modern {
            border-radius: 16px;
            border: none;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .card-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Gradient Cards */
        .gradient-card-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .gradient-card-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .gradient-card-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .gradient-card-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        /* Stats Cards */
        .stat-card {
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        /* Modern Tabs */
        .nav-tabs-modern {
            border-bottom: 2px solid #e5e7eb;
            background: white;
            border-radius: 12px 12px 0 0;
            padding: 0.5rem 1rem 0;
        }

        .nav-tabs-modern .nav-link {
            border: none;
            color: #6b7280;
            font-weight: 600;
            padding: 1rem 1.5rem;
            position: relative;
            transition: all 0.3s ease;
            border-radius: 8px 8px 0 0;
        }

        .nav-tabs-modern .nav-link:hover {
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.05);
        }

        .nav-tabs-modern .nav-link.active {
            color: #4f46e5;
            background: transparent;
        }

        .nav-tabs-modern .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 3px 3px 0 0;
        }

        /* Widget Headers */
        .widget-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.25rem 1.5rem;
            border-radius: 12px 12px 0 0;
            font-weight: 600;
        }

        .widget-header-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .widget-header-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .widget-header-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .widget-header-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        /* List Items */
        .list-item-modern {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            background: #f9fafb;
            border-left: 3px solid transparent;
        }

        .list-item-modern:hover {
            background: #f3f4f6;
            border-left-color: #4f46e5;
            transform: translateX(4px);
        }

        /* Badges Modern */
        .badge-modern {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        /* Progress Bars */
        .progress-modern {
            height: 8px;
            border-radius: 10px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .progress-modern .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px;
            padding: 1.5rem;
        }

        /* Spacing */
        .section-gap {
            margin-bottom: 2rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out;
        }

        .x-small {
            font-size: 0.7rem !important;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3.5rem 2rem;
            text-align: center;
        }

        .empty-state i {
            font-size: 3.5rem;
            margin-bottom: 1.25rem;
            color: #cbd5e1;
            opacity: 0.8;
            display: block;
        }

        .empty-state p {
            color: #64748b;
            font-size: 1.05rem;
            font-weight: 500;
            margin: 0;
        }

        /* Multi-Staff Avatars */
        .staff-avatars {
            display: flex;
            align-items: center;
        }

        .staff-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
            border: 2px solid white;
            margin-left: -8px;
            position: relative;
            transition: all 0.2s ease;
        }

        .staff-avatar:first-child {
            margin-left: 0;
        }

        .staff-avatar:hover {
            transform: translateY(-2px) scale(1.1);
            z-index: 100 !important;
        }

        .staff-avatar-more {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
            border: 2px solid white;
            margin-left: -8px;
        }

        /* Tooltip Styling */
        .tooltip-inner {
            max-width: 300px;
            text-align: left;
            padding: 0.75rem;
        }

        .tooltip-service-item {
            padding: 0.25rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .tooltip-service-item:last-child {
            border-bottom: none;
        }

        .group-booking-badge {
            cursor: help;
        }

        /* Modern Filter Bar Styles */
        .filter-wrapper {
            position: sticky;
            top: 1rem;
            z-index: 1000;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .filter-glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 100px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
            padding: 0.6rem 1.2rem;
        }

        .period-pill-group {
            display: flex;
            gap: 0.5rem;
            background: rgba(0, 0, 0, 0.04);
            padding: 0.3rem;
            border-radius: 50px;
        }

        .period-pill {
            border: none;
            background: transparent;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }

        .period-pill:hover:not(.active) {
            background: rgba(0, 0, 0, 0.05);
            color: #1e293b;
        }

        .period-pill.active {
            background: #fff;
            color: #4f46e5;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
        }

        .date-range-display {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.5rem 1rem;
            background: rgba(79, 70, 229, 0.06);
            border-radius: 50px;
            color: #4f46e5;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid rgba(79, 70, 229, 0.1);
        }

        .custom-range-integrated {
            overflow: hidden;
            max-width: 0;
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
        }

        .custom-range-integrated.show {
            max-width: 600px;
            opacity: 1;
            margin-left: 1rem;
            padding-left: 1rem;
            border-left: 2px solid rgba(79, 70, 229, 0.15);
        }

        .integrated-input-wrapper {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            padding: 2px 12px;
            transition: all 0.2s ease;
        }

        .integrated-input-wrapper:focus-within {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .integrated-input {
            border: none;
            background: transparent;
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
            width: 120px;
            padding: 4px;
            outline: none !important;
        }

        .btn-apply-integrated {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            transition: all 0.3s ease;
            margin-left: 0.5rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .btn-apply-integrated:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(79, 70, 229, 0.3);
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        }

        .btn-refresh {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #4f46e5;
            color: white;
            border: none;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .btn-refresh:hover {
            transform: rotate(180deg);
            background: #4338ca;
        }

        .modern-input {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .modern-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        @media (max-width: 991px) {
            .filter-glass-card {
                border-radius: 20px;
                padding: 1rem;
            }

            .period-pill-group {
                flex-wrap: wrap;
                background: transparent;
                padding: 0;
            }

            .period-pill {
                background: rgba(0, 0, 0, 0.04);
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <!-- Modern Date Range Filter -->
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('system.view_dashboard')): ?>
            <div class="filter-wrapper animate-fade-in">
                <div class="filter-glass-card">
                    <form action="<?php echo e(url()->current()); ?>" method="GET" id="dateFilterForm"
                        class="d-flex flex-wrap align-items-center justify-content-between g-3">

                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="period-pill-group shadow-inner">
                                <button type="button" class="period-pill <?php echo e($dateRange == 'today' ? 'active' : ''); ?>"
                                    onclick="setDateRange('today')">Today</button>
                                <button type="button" class="period-pill <?php echo e($dateRange == 'yesterday' ? 'active' : ''); ?>"
                                    onclick="setDateRange('yesterday')">Yesterday</button>
                                <button type="button" class="period-pill <?php echo e($dateRange == 'last_7_days' ? 'active' : ''); ?>"
                                    onclick="setDateRange('last_7_days')">7 Days</button>
                                <button type="button" class="period-pill <?php echo e($dateRange == 'this_month' ? 'active' : ''); ?>"
                                    onclick="setDateRange('this_month')">Month</button>
                                <button type="button" class="period-pill <?php echo e($dateRange == 'custom' ? 'active' : ''); ?>"
                                    onclick="toggleCustomRange()">
                                    <i class="fas fa-sliders-h me-1"></i> Custom
                                </button>
                                <!-- Integrated Custom Range -->
                                <div id="custom-range-integrated"
                                    class="custom-range-integrated <?php echo e($dateRange == 'custom' ? 'show' : ''); ?>">
                                    <div class="integrated-input-wrapper">
                                        <i class="far fa-calendar-alt text-muted me-2 small"></i>
                                        <input type="date" name="start_date" id="start_date" class="integrated-input"
                                            value="<?php echo e($startDate->format('Y-m-d')); ?>">
                                    </div>
                                    <span class="text-muted small mx-2">to</span>
                                    <div class="integrated-input-wrapper">
                                        <i class="far fa-calendar-alt text-muted me-2 small"></i>
                                        <input type="date" name="end_date" id="end_date" class="integrated-input"
                                            value="<?php echo e($endDate->format('Y-m-d')); ?>">
                                    </div>
                                    <button type="submit" class="btn-apply-integrated">
                                        Apply Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="date_range" id="date_range_input" value="<?php echo e($dateRange); ?>">

                        <?php if($startDate && $endDate && $dateRange != 'custom'): ?>
                            <div class="date-range-display shadow-sm">
                                <i class="fas fa-calendar-day"></i>
                                <span><?php echo e($startDate->format('M d')); ?> — <?php echo e($endDate->format('M d, Y')); ?></span>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        <!-- Welcome Card -->
        <div class="row section-gap animate-fade-in">
            <div class="col-12">
                <div class="welcome-card card-modern">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(auth()->user()->name)); ?>&background=random"
                                alt="<?php echo e(auth()->user()->name); ?>" class="rounded-circle border border-3 border-white shadow"
                                style="width: 64px; height: 64px;">
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h4 class="mb-2 fw-bold">Welcome back, <?php echo e(auth()->user()->name); ?>! 👋</h4>
                            <p class="mb-0 opacity-90">
                                <i class="fas fa-user-tag me-2"></i>
                                <?php echo e(ucfirst(auth()->user()->roles->first()->name ?? 'No Role')); ?>

                                <?php if(auth()->user()->last_login_at): ?>
                                    <span class="ms-3">
                                        <i class="fas fa-clock me-2"></i>
                                        Last Login: <?php echo e(auth()->user()->last_login_at->diffForHumans()); ?>

                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('salon.view')): ?>
            <?php if(isset($salon_quick_stats)): ?>
                <div class="row section-gap animate-fade-in">
                    <div class="col-xl col-md-6 mb-3">
                        <div class="stat-card gradient-card-primary card-modern p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-uppercase x-small fw-semibold opacity-90 mb-1">Active Staff</div>
                                    <div class="h5 mb-0 fw-bold"><?php echo e($salon_quick_stats['active_staff'] ?? 0); ?></div>
                                </div>
                                <div class="stat-icon" style="width: 40px; height: 40px; font-size: 18px;">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl col-md-6 mb-3">
                        <div class="stat-card gradient-card-success card-modern p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-uppercase x-small fw-semibold opacity-90 mb-1">
                                        Appointments
                                    </div>
                                    <div class="h5 mb-0 fw-bold"><?php echo e($salon_quick_stats['today_appointments'] ?? 0); ?></div>
                                </div>
                                <div class="stat-icon" style="width: 40px; height: 40px; font-size: 18px;">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl col-md-6 mb-3">
                        <div class="stat-card gradient-card-warning card-modern p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-uppercase x-small fw-semibold opacity-90 mb-1">Pending</div>
                                    <div class="h5 mb-0 fw-bold"><?php echo e($salon_quick_stats['pending_appointments'] ?? 0); ?></div>
                                </div>
                                <div class="stat-icon" style="width: 40px; height: 40px; font-size: 18px;">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl col-md-6 mb-3">
                        <div class="stat-card gradient-card-info card-modern p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-uppercase x-small fw-semibold opacity-90 mb-1">Completed</div>
                                    <div class="h5 mb-0 fw-bold"><?php echo e($salon_quick_stats['completed_today'] ?? 0); ?></div>
                                </div>
                                <div class="stat-icon" style="width: 40px; height: 40px; font-size: 18px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Today's Revenue & Low Stock Alerts -->
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('system.view_dashboard')): ?>
            <?php if(isset($todays_sales) || isset($low_stock_products)): ?>
                <div class="row section-gap">
                    <!-- Today's Revenue Widget -->
                    <?php if(isset($todays_sales) && (auth()->user()->can('reports.sales') || auth()->user()->can('bookings.view_stats'))): ?>
                        <div class="col-xl mb-4" style="min-width: 250px;">
                            <div class="card-modern h-100">
                                <div class="widget-header widget-header-success py-2 px-3">
                                    <i class="fas fa-dollar-sign me-2 small"></i><span
                                        class="small fw-bold"><?php echo e($dateRange == 'today' ? "Today's" : "Period"); ?> Revenue</span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="text-center">
                                        <div class="h2 mb-2 fw-bold text-success">
                                            <?php echo e(currency_symbol()); ?><?php echo e(number_format($todays_sales['today_revenue'], 2)); ?>

                                        </div>
                                        <div class="mb-2">
                                            <?php if($todays_sales['revenue_growth'] > 0): ?>
                                                <span class="badge badge-modern bg-success x-small py-1 px-2">
                                                    <i class="fas fa-arrow-up me-1"></i> <?php echo e($todays_sales['revenue_growth']); ?>%
                                                </span>
                                            <?php elseif($todays_sales['revenue_growth'] < 0): ?>
                                                <span class="badge badge-modern bg-danger x-small py-1 px-2">
                                                    <i class="fas fa-arrow-down me-1"></i> <?php echo e(abs($todays_sales['revenue_growth'])); ?>%
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-modern bg-secondary x-small py-1 px-2">No Change</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="x-small text-muted">
                                            vs Prev: <?php echo e(currency_symbol()); ?><?php echo e(number_format($todays_sales['yesterday_revenue'], 2)); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Today's Refunds Widget (NEW) -->
                    <?php if(isset($todays_sales) && (auth()->user()->can('reports.sales') || auth()->user()->can('bookings.view_stats'))): ?>
                        <div class="col-xl mb-4" style="min-width: 250px;">
                            <div class="card-modern h-100">
                                <div class="widget-header widget-header-danger py-2 px-3">
                                    <i class="fas fa-undo me-2 small"></i><span
                                        class="small fw-bold"><?php echo e($dateRange == 'today' ? "Today's" : "Period"); ?> Refunds</span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="text-center">
                                        <div class="h2 mb-2 fw-bold text-danger">
                                            <?php echo e(currency_symbol()); ?><?php echo e(number_format($todays_sales['today_refunds'] ?? 0, 2)); ?>

                                        </div>
                                        <div class="mb-2">
                                            <span class="badge badge-modern bg-secondary x-small py-1 px-2">
                                                Count: <?php echo e($todays_sales['today_refunds_count'] ?? 0); ?>

                                            </span>
                                            <?php if(($todays_sales['today_refunds_fees'] ?? 0) > 0): ?>
                                                <span class="badge badge-modern bg-info x-small py-1 px-2 ms-1">
                                                    Fees: <?php echo e(currency_symbol()); ?><?php echo e(number_format($todays_sales['today_refunds_fees'], 2)); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="x-small text-muted">
                                            Net Outflow:
                                            <?php echo e(currency_symbol()); ?><?php echo e(number_format($todays_sales['today_net_refunds'] ?? 0, 2)); ?>

                                        </div>
                                        <div class="x-small text-muted mt-1">
                                            vs Prev:
                                            <?php echo e(currency_symbol()); ?><?php echo e(number_format($todays_sales['yesterday_refunds'] ?? 0, 2)); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Payment Methods Widget -->
                    <div class="col-lg-6 mb-4">
                        <div class="card-modern h-100 payment-methods-widget">
                            <div class="widget-header widget-header-info d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-credit-card me-2"></i>Payment Methods
                                </div>
                                <?php if(isset($payment_methods) && count($payment_methods) > 0): ?>
                                    <span class="badge bg-white text-info fw-bold">
                                        Total: <?php echo e(currency_symbol()); ?><?php echo e(number_format($total_bill_value ?? 0, 2)); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-4">
                                <?php if(isset($payment_methods) && count($payment_methods) > 0): ?>
                                    <div class="row g-4">
                                        <!-- Chart Section -->
                                        <div class="col-md-6 d-flex align-items-center justify-content-center">
                                            <div class="payment-chart-container"
                                                style="position: relative; height: 280px; width: 280px;">
                                                <canvas id="paymentMethodsChart"></canvas>
                                            </div>
                                        </div>

                                        <!-- Payment Method List -->
                                        <div class="col-md-6">
                                            <div class="payment-methods-list h-100 d-flex flex-column justify-content-center">
                                                <?php $__currentLoopData = $payment_methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $percent = $total_bill_value > 0 ? ($method['value'] / $total_bill_value) * 100 : 0;
                                                        $colors = [
                                                            'Cash' => ['text' => 'primary', 'bg' => 'primary', 'icon' => 'fa-money-bill-wave'],
                                                            'Card' => ['text' => 'success', 'bg' => 'success', 'icon' => 'fa-credit-card'],
                                                            'Online' => ['text' => 'info', 'bg' => 'info', 'icon' => 'fa-globe'],
                                                            'Other' => ['text' => 'warning', 'bg' => 'warning', 'icon' => 'fa-wallet'],
                                                            'Unpaid' => ['text' => 'danger', 'bg' => 'danger', 'icon' => 'fa-exclamation-circle']
                                                        ];
                                                        $colorData = $colors[$method['name']] ?? ['text' => 'secondary', 'bg' => 'secondary', 'icon' => 'fa-coins'];
                                                    ?>
                                                    <div class="payment-method-item mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="payment-icon-wrapper bg-<?php echo e($colorData['bg']); ?> bg-opacity-10 text-<?php echo e($colorData['text']); ?> me-3">
                                                                <i class="fas <?php echo e($colorData['icon']); ?>"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span class="fw-bold text-dark"><?php echo e($method['name']); ?></span>
                                                                    <div class="text-end">
                                                                        <span
                                                                            class="fw-bold text-dark"><?php echo e(currency_symbol()); ?><?php echo e(number_format($method['value'], 2)); ?></span>
                                                                        <small
                                                                            class="text-muted ms-1">(<?php echo e(number_format($percent, 1)); ?>%)</small>
                                                                    </div>
                                                                </div>
                                                                <div class="progress"
                                                                    style="height: 6px; border-radius: 10px; background-color: rgba(0,0,0,0.05);">
                                                                    <div class="progress-bar bg-<?php echo e($colorData['text']); ?>" role="progressbar"
                                                                        style="width: <?php echo e($percent); ?>%; border-radius: 10px;"
                                                                        aria-valuenow="<?php echo e($percent); ?>" aria-valuemin="0"
                                                                        aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-credit-card text-muted"></i>
                                        <p class="mb-0">No payment data available</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <style>
                        /* Revised Payment Methods Styles */
                        .payment-methods-widget {
                            overflow: hidden;
                        }

                        .payment-chart-container {
                            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.08));
                        }

                        .payment-method-item {
                            padding: 0.5rem 0.75rem;
                            border-radius: 12px;
                            transition: background-color 0.2s ease;
                        }

                        .payment-method-item:hover {
                            background-color: #f8f9fa;
                        }

                        .payment-icon-wrapper {
                            width: 48px;
                            height: 48px;
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 1.25rem;
                            transition: transform 0.2s ease;
                        }

                        .payment-method-item:hover .payment-icon-wrapper {
                            transform: scale(1.1);
                        }

                        @media (max-width: 768px) {
                            .payment-chart-container {
                                height: 220px !important;
                                width: 220px !important;
                            }
                        }
                    </style>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Analytics Tabs -->
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('system.view_dashboard')): ?>
            <?php if(isset($bill_count) || isset($total_bill_value) || isset($average_bill_value)): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-0 pt-3 px-3 pb-0">
                                <?php
                                    // Check if modules are enabled AND user has permission to view them
                                    $showSales = \App\Helpers\ModuleHelper::reportsEnabled() && (auth()->user()->can('reports.sales') || auth()->user()->can('pos.view_reports'));
                                    $showStaff = \App\Helpers\ModuleHelper::staffEnabled() && (auth()->user()->can('reports.staff') || auth()->user()->can('staff.view_performance'));
                                    $showCustomer = \App\Helpers\ModuleHelper::customersEnabled() && (auth()->user()->can('reports.customers') || auth()->user()->can('customers.view'));

                                    $activeTab = '';
                                    if ($showSales) {
                                        $activeTab = 'sales';
                                    } elseif ($showStaff) {
                                        $activeTab = 'staff';
                                    } elseif ($showCustomer) {
                                        $activeTab = 'customer';
                                    }
                                ?>
                                <ul class="nav nav-tabs-modern" id="analyticsTabs" role="tablist">
                                    <?php if($showSales): ?>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link <?php echo e($activeTab == 'sales' ? 'active' : ''); ?>" id="sales-tab"
                                                data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">
                                                <i class="fas fa-chart-line me-2"></i>Sales Insight
                                            </button>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($showStaff): ?>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link <?php echo e($activeTab == 'staff' ? 'active' : ''); ?>" id="staff-tab"
                                                data-bs-toggle="tab" data-bs-target="#staff" type="button" role="tab">
                                                <i class="fas fa-users me-2"></i>Staff Insight
                                            </button>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($showCustomer): ?>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link <?php echo e($activeTab == 'customer' ? 'active' : ''); ?>" id="customer-tab"
                                                data-bs-toggle="tab" data-bs-target="#customer" type="button" role="tab">
                                                <i class="fas fa-user-friends me-2"></i>Customer Insight
                                            </button>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="analyticsTabsContent">

                                    <!-- Sales Insight Tab -->
                                    <?php if($showSales): ?>
                                        <div class="tab-pane fade <?php echo e($activeTab == 'sales' ? 'show active' : ''); ?>" id="sales"
                                            role="tabpanel">
                                            <div class="d-flex justify-content-end mb-3">
                                                <a href="<?php echo e(route('admin.reports.sales', ['salon_slug' => $salon_slug ?? (request()->route('salon_slug') ?? ($current_salon->slug ?? ''))])); ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt me-1"></i> Full Report
                                                </a>
                                            </div>

                                            <!-- Today's Sales Comparison Widget -->
                                            <?php if(isset($todays_sales)): ?>
                                                <div class="card-modern mb-4">
                                                    <div class="widget-header">
                                                        <i
                                                            class="fas fa-calendar-day me-2"></i><?php echo e($dateRange == 'today' ? "Today's" : "Period"); ?>

                                                        Sales Performance
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <div class="row g-4">
                                                            <div class="col-md-4">
                                                                <div class="text-center p-4 rounded"
                                                                    style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);">
                                                                    <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                        <?php echo e($dateRange == 'today' ? "Today's" : "Selected"); ?>

                                                                        Revenue
                                                                    </div>
                                                                    <div class="h3 mb-2 fw-bold text-primary">
                                                                        <?php echo e(currency_symbol()); ?><?php echo e(number_format($todays_sales['today_revenue'], 2)); ?>

                                                                    </div>
                                                                    <div class="small">
                                                                        <?php if($todays_sales['revenue_growth'] > 0): ?>
                                                                            <span class="badge badge-modern bg-success">
                                                                                <i class="fas fa-arrow-up me-1"></i>
                                                                                <?php echo e($todays_sales['revenue_growth']); ?>%
                                                                            </span>
                                                                        <?php elseif($todays_sales['revenue_growth'] < 0): ?>
                                                                            <span class="badge badge-modern bg-danger">
                                                                                <i class="fas fa-arrow-down me-1"></i>
                                                                                <?php echo e(abs($todays_sales['revenue_growth'])); ?>%
                                                                            </span>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-modern bg-secondary">No Change</span>
                                                                        <?php endif; ?>
                                                                        <span class="text-muted ms-1">vs
                                                                            <?php echo e($dateRange == 'today' ? 'Yesterday' : 'Prev. Period'); ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="text-center p-4 rounded"
                                                                    style="background: linear-gradient(135deg, #11998e15 0%, #38ef7d15 100%);">
                                                                    <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                        Transactions</div>
                                                                    <div class="h3 mb-2 fw-bold text-success">
                                                                        <?php echo e($todays_sales['today_count']); ?>

                                                                    </div>
                                                                    <div class="small">
                                                                        <?php if($todays_sales['count_growth'] > 0): ?>
                                                                            <span class="badge badge-modern bg-success">
                                                                                <i class="fas fa-arrow-up me-1"></i>
                                                                                <?php echo e($todays_sales['count_growth']); ?>%
                                                                            </span>
                                                                        <?php elseif($todays_sales['count_growth'] < 0): ?>
                                                                            <span class="badge badge-modern bg-danger">
                                                                                <i class="fas fa-arrow-down me-1"></i>
                                                                                <?php echo e(abs($todays_sales['count_growth'])); ?>%
                                                                            </span>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-modern bg-secondary">No Change</span>
                                                                        <?php endif; ?>
                                                                        <span class="text-muted ms-1">vs
                                                                            <?php echo e($dateRange == 'today' ? 'Yesterday' : 'Prev. Period'); ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="text-center p-4 rounded"
                                                                    style="background: linear-gradient(135deg, #4facfe15 0%, #00f2fe15 100%);">
                                                                    <div class="text-muted small mb-2 text-uppercase fw-semibold">Peak Hour
                                                                    </div>
                                                                    <div class="h3 mb-2 fw-bold text-info">
                                                                        <?php echo e($todays_sales['peak_hour']); ?>

                                                                    </div>
                                                                    <div class="small text-muted">Most sales activity</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Sales Summary Cards -->
                                            <!-- Sales Summary Cards -->
                                            <div class="row g-4 mb-4">
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-primary mb-1">Bill
                                                                        Count</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark"><?php echo e($bill_count ?? 0); ?></div>
                                                                </div>
                                                                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                                                    <i class="fas fa-receipt fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-success mb-1">Net
                                                                        Revenue</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark">
                                                                        <?php echo e(currency_symbol()); ?><?php echo e(number_format($total_bill_value ?? 0, 2)); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-success bg-opacity-10 text-success">
                                                                    <i class="fas fa-dollar-sign fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-info mb-1">Avg. Bill
                                                                        Value</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark">
                                                                        <?php echo e(currency_symbol()); ?><?php echo e(number_format($average_bill_value ?? 0, 2)); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-info bg-opacity-10 text-info">
                                                                    <i class="fas fa-chart-bar fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-warning mb-1">Unpaid
                                                                        Value</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark">
                                                                        <?php echo e(currency_symbol()); ?><?php echo e(number_format($unpaid_value ?? 0, 2)); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                                                    <i class="fas fa-exclamation-circle fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if(($refund_gross ?? 0) > 0): ?>
                                                <div class="row g-4 mb-4">
                                                    <div class="col-xl-4 col-md-6">
                                                        <div class="card-modern h-100" style="border-left: 4px solid #dc3545;">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <div class="text-uppercase small fw-bold text-danger mb-1">Gross
                                                                            Refunded</div>
                                                                        <div class="h4 mb-0 fw-bold text-dark">
                                                                            <?php echo e(currency_symbol()); ?><?php echo e(number_format($refund_gross ?? 0, 2)); ?>

                                                                        </div>
                                                                    </div>
                                                                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                                                                        <i class="fas fa-undo-alt fa-lg"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-6">
                                                        <div class="card-modern h-100" style="border-left: 4px solid #17a2b8;">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <div class="text-uppercase small fw-bold text-info mb-1">Refund Fees
                                                                        </div>
                                                                        <div class="h4 mb-0 fw-bold text-dark">
                                                                            <?php echo e(currency_symbol()); ?><?php echo e(number_format($refund_fees ?? 0, 2)); ?>

                                                                        </div>
                                                                    </div>
                                                                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                                                                        <i class="fas fa-percentage fa-lg"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-6">
                                                        <div class="card-modern h-100" style="border-left: 4px solid #6f42c1;">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <div class="text-uppercase small fw-bold text-purple mb-1"
                                                                            style="color: #6f42c1;">Net Refunded Outflow</div>
                                                                        <div class="h4 mb-0 fw-bold text-dark">
                                                                            <?php echo e(currency_symbol()); ?><?php echo e(number_format($refund_net ?? 0, 2)); ?>

                                                                        </div>
                                                                    </div>
                                                                    <div class="stat-icon bg-purple bg-opacity-10 text-purple"
                                                                        style="color: #6f42c1; background-color: rgba(111, 66, 193, 0.1);">
                                                                        <i class="fas fa-file-invoice-dollar fa-lg"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>


                                            <!-- Revenue Trend Chart -->
                                            <div class="mb-4">
                                                <div class="card-modern">
                                                    <div class="widget-header widget-header-primary">
                                                        <i class="fas fa-chart-line me-2"></i>Revenue Trend (Last 6 Months)
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <div class="chart-area" style="height: 350px;">
                                                            <canvas id="revenueTrendChart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Charts Row -->
                                            <div class="row g-4 mb-4">
                                                <!-- Payment Methods Chart (Moved to Top) -->


                                                <!-- Top Services Chart -->
                                                <div class="col-lg-12">
                                                    <div class="card-modern h-100">
                                                        <div class="widget-header widget-header-success">
                                                            <i class="fas fa-star me-2"></i>Top 10 Services by Revenue
                                                        </div>
                                                        <div class="card-body p-4">
                                                            <?php if(isset($top_services) && count($top_services) > 0): ?>
                                                                <div class="chart-bar" style="height: 300px;">
                                                                    <canvas id="topServicesChart"></canvas>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="empty-state">
                                                                    <i class="fas fa-cut text-muted"></i>
                                                                    <p class="mb-0">No service data available</p>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Recent Bookings Table (All Bookings with Assign functionality) -->
                                            <div class="card-modern mb-4">
                                                <div class="widget-header widget-header-primary">
                                                    <i class="fas fa-calendar-check me-2"></i>Recent Bookings
                                                </div>
                                                <div class="card-body p-0">
                                                    <?php if(isset($recent_bookings_all) && count($recent_bookings_all) > 0): ?>
                                                        <div class="table-responsive">
                                                            <table class="table table-hover align-middle mb-0">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Date
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Service
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Customer
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Staff</th>
                                                                        <th class="text-end py-3 text-uppercase small fw-bold text-muted">
                                                                            Amount</th>
                                                                        <th
                                                                            class="text-center py-3 text-uppercase small fw-bold text-muted">
                                                                            Status</th>
                                                                        <th
                                                                            class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">
                                                                            Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $__currentLoopData = $recent_bookings_all; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <tr>
                                                                            <td class="ps-4">
                                                                                <div class="fw-bold text-dark">
                                                                                    <?php echo e(format_date($booking->start_time, 'M d')); ?>

                                                                                </div>
                                                                                <div class="small text-muted">
                                                                                    <?php echo e(format_time($booking->start_time)); ?>

                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <?php if($booking->group_count > 1): ?>
                                                                                    <?php
                                                                                        $tooltipHtml = "<div class='text-start'>";
                                                                                        foreach ($booking->groupBookings as $gb) {
                                                                                            $tooltipHtml .= "<div class='tooltip-service-item'>";
                                                                                            $tooltipHtml .= "<strong>" . ($gb->service->name ?? 'Unknown') . "</strong><br>";
                                                                                            $tooltipHtml .= "<small>Staff: " . ($gb->staff->name ?? 'Unassigned') . "</small>";
                                                                                            $tooltipHtml .= "</div>";
                                                                                        }
                                                                                        $tooltipHtml .= "</div>";
                                                                                    ?>
                                                                                    <span
                                                                                        class="badge bg-info bg-opacity-10 text-info me-2 group-booking-badge"
                                                                                        style="font-size: 0.75em;" data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top" data-bs-html="true"
                                                                                        title="<?php echo e($tooltipHtml); ?>">
                                                                                        Group
                                                                                    </span>
                                                                                    <span class="fw-semibold text-dark">Group Booking
                                                                                        (<?php echo e($booking->group_count); ?> services)</span>
                                                                                <?php else: ?>
                                                                                    <span
                                                                                        class="fw-semibold text-dark"><?php echo e($booking->service->name ?? 'Unknown'); ?></span>
                                                                                    <?php if($booking->package): ?>
                                                                                        <div class="mt-1">
                                                                                            <span
                                                                                                class="badge bg-purple-subtle text-purple-emphasis rounded-pill px-2 py-1"
                                                                                                style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.7em;">
                                                                                                <i
                                                                                                    class="fas fa-box me-1"></i><?php echo e($booking->package->name); ?>

                                                                                            </span>
                                                                                        </div>
                                                                                    <?php endif; ?>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td>
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-2"
                                                                                        style="width: 32px; height: 32px;">
                                                                                        <?php echo e(substr($booking->customer->name ?? 'U', 0, 1)); ?>

                                                                                    </div>
                                                                                    <div>
                                                                                        <div class="fw-semibold text-dark">
                                                                                            <?php echo e($booking->customer->name ?? 'Unknown'); ?>

                                                                                        </div>
                                                                                        <?php if($booking->customer && $booking->customer->is_guest): ?>
                                                                                            <span class="badge bg-secondary"
                                                                                                style="font-size: 0.6em;">Guest</span>
                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <?php if($booking->group_count > 1): ?>
                                                                                    <?php
                                                                                        $uniqueStaff = $booking->groupBookings->pluck('staff')->filter()->unique('id');
                                                                                    ?>
                                                                                    <?php if($uniqueStaff->count() > 0): ?>
                                                                                        <div class="staff-avatars">
                                                                                            <?php $__currentLoopData = $uniqueStaff->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                <div class="staff-avatar"
                                                                                                    style="z-index: <?php echo e(10 - $index); ?>;"
                                                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                                                    title="<?php echo e($staff->name); ?>">
                                                                                                    <?php echo e(substr($staff->name, 0, 1)); ?>

                                                                                                </div>
                                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                            <?php if($uniqueStaff->count() > 3): ?>
                                                                                                <div class="staff-avatar-more" data-bs-toggle="tooltip"
                                                                                                    data-bs-placement="top"
                                                                                                    title="<?php echo e($uniqueStaff->skip(3)->pluck('name')->implode(', ')); ?>">
                                                                                                    +<?php echo e($uniqueStaff->count() - 3); ?>

                                                                                                </div>
                                                                                            <?php endif; ?>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <span class="badge bg-light text-muted border">Unassigned</span>
                                                                                    <?php endif; ?>
                                                                                <?php else: ?>
                                                                                    <?php if($booking->staff): ?>
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center me-2"
                                                                                                style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                                                                <?php echo e(substr($booking->staff->name, 0, 1)); ?>

                                                                                            </div>
                                                                                            <span class="small"><?php echo e($booking->staff->name); ?></span>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <span class="badge bg-light text-muted border">Unassigned</span>
                                                                                    <?php endif; ?>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td class="text-end fw-bold text-dark">
                                                                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($booking->group_total, 2)); ?>

                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span
                                                                                    class="badge badge-modern bg-<?php echo e($booking->status === 'completed' ? 'success' : ($booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'info' : 'danger'))); ?>">
                                                                                    <?php echo e(ucfirst($booking->status)); ?>

                                                                                </span>
                                                                            </td>
                                                                            <td class="text-end pe-4">
                                                                                <?php if(is_null($booking->staff_id) && $booking->staff_assignment_status === 'pending'): ?>
                                                                                    <button
                                                                                        class="btn btn-sm btn-outline-primary assign-btn rounded-pill px-3"
                                                                                        data-id="<?php echo e($booking->id); ?>"
                                                                                        data-date="<?php echo e($booking->start_time->format('Y-m-d')); ?>">
                                                                                        Assign
                                                                                    </button>
                                                                                <?php else: ?>
                                                                                    <button
                                                                                        class="btn btn-sm btn-light text-muted view-booking rounded-circle"
                                                                                        data-booking-id="<?php echo e($booking->id); ?>" data-bs-toggle="modal"
                                                                                        data-bs-target="#bookingDetailsModal" title="View Details">
                                                                                        <i class="fas fa-eye"></i>
                                                                                    </button>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="p-3">
                                                            <?php echo e($recent_bookings_all->links()); ?>

                                                        </div>
                                                    <?php else: ?>
                                                        <div class="empty-state">
                                                            <i class="fas fa-calendar-check text-muted"></i>
                                                            <p class="mb-0">No recent bookings found.</p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Recent Sales Table -->
                                            <div class="card-modern">
                                                <div class="widget-header widget-header-primary">
                                                    <i class="fas fa-history me-2"></i>Recent Sales
                                                </div>
                                                <div class="card-body p-0">
                                                    <?php if(isset($recent_bookings) && count($recent_bookings) > 0): ?>
                                                        <div class="table-responsive">
                                                            <table class="table table-hover align-middle mb-0">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Date
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Type /
                                                                            Details</th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Customer
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Staff</th>
                                                                        <th class="text-end py-3 text-uppercase small fw-bold text-muted">
                                                                            Amount</th>
                                                                        <th
                                                                            class="text-center py-3 text-uppercase small fw-bold text-muted">
                                                                            Status</th>
                                                                        <th
                                                                            class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">
                                                                            Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $__currentLoopData = $recent_bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <tr>
                                                                            <td class="ps-4">
                                                                                <div class="fw-bold text-dark">
                                                                                    <?php echo e(format_date(\Carbon\Carbon::parse($sale->date), 'M d')); ?>

                                                                                </div>
                                                                                <div class="small text-muted">
                                                                                    <?php echo e(format_time(\Carbon\Carbon::parse($sale->date))); ?>

                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <?php if($sale->type === 'booking'): ?>
                                                                                    <span class="badge bg-primary bg-opacity-10 text-primary me-2"
                                                                                        style="font-size: 0.75em;">Service</span>
                                                                                    <?php if($sale->model && $sale->model->group_count > 1): ?>
                                                                                        <span class="fw-semibold text-dark">Group Booking
                                                                                            (<?php echo e($sale->model->group_count); ?> services)</span>
                                                                                    <?php else: ?>
                                                                                        <span
                                                                                            class="fw-semibold text-dark"><?php echo e($sale->model->service->name ?? 'Unknown'); ?></span>
                                                                                        <?php if($sale->model->package): ?>
                                                                                            <div class="mt-1">
                                                                                                <span
                                                                                                    class="badge bg-purple-subtle text-purple-emphasis rounded-pill px-2 py-1"
                                                                                                    style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.65em;">
                                                                                                    <i
                                                                                                        class="fas fa-box me-1"></i><?php echo e($sale->model->package->name); ?>

                                                                                                </span>
                                                                                            </div>
                                                                                        <?php endif; ?>
                                                                                    <?php endif; ?>
                                                                                <?php else: ?>
                                                                                    <span class="badge bg-warning bg-opacity-10 text-warning me-2"
                                                                                        style="font-size: 0.75em;">POS</span>
                                                                                    <span class="fw-semibold text-dark">POS Sale
                                                                                        #<?php echo e($sale->model->invoice_number); ?></span>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td>
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-2"
                                                                                        style="width: 32px; height: 32px;">
                                                                                        <?php echo e(substr($sale->model->customer->name ?? 'U', 0, 1)); ?>

                                                                                    </div>
                                                                                    <div>
                                                                                        <div class="fw-semibold text-dark">
                                                                                            <?php echo e($sale->model->customer->name ?? 'Unknown'); ?>

                                                                                        </div>
                                                                                        <?php if($sale->model->customer && $sale->model->customer->is_guest): ?>
                                                                                            <span class="badge bg-secondary"
                                                                                                style="font-size: 0.6em;">Guest</span>
                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <?php if($sale->type === 'booking'): ?>
                                                                                    <?php if($sale->model->staff): ?>
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center me-2"
                                                                                                style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                                                                <?php echo e(substr($sale->model->staff->name, 0, 1)); ?>

                                                                                            </div>
                                                                                            <span class="small"><?php echo e($sale->model->staff->name); ?></span>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <span class="badge bg-light text-muted border">Unassigned</span>
                                                                                    <?php endif; ?>
                                                                                <?php else: ?>
                                                                                    <?php if($sale->model->employee): ?>
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center me-2"
                                                                                                style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                                                                <?php echo e(substr($sale->model->employee->name, 0, 1)); ?>

                                                                                            </div>
                                                                                            <span
                                                                                                class="small"><?php echo e($sale->model->employee->name); ?></span>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <span class="text-muted">Unknown</span>
                                                                                    <?php endif; ?>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td class="text-end fw-bold text-dark">
                                                                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($sale->amount, 2)); ?>

                                                                            </td>
                                                                            <td class="text-center">
                                                                                <?php
                                                                                    $badgeClass = 'success';
                                                                                    $statusLabel = 'Paid';
                                                                                    $displayAmount = $sale->amount;

                                                                                    if ($sale->type === 'booking') {
                                                                                        $statusLabel = ucfirst($sale->status);
                                                                                        if ($sale->payment_status === 'refunded' || $sale->status === 'cancelled') {
                                                                                            $badgeClass = 'secondary';
                                                                                            $statusLabel = 'Refunded';
                                                                                        } elseif ($sale->payment_status === 'partial' || $sale->status === 'partial') {
                                                                                            $badgeClass = 'warning';
                                                                                            $statusLabel = 'Partial';
                                                                                        } elseif ($sale->payment_status === 'unpaid') {
                                                                                            $badgeClass = 'danger';
                                                                                            $statusLabel = 'Unpaid';
                                                                                        }
                                                                                    } else {
                                                                                        $statusLabel = ucfirst($sale->payment_status);
                                                                                        if ($sale->status === 'refunded') {
                                                                                            $badgeClass = 'secondary';
                                                                                            $statusLabel = 'Refunded';
                                                                                        } elseif ($sale->status === 'partially_refunded' || $sale->payment_status === 'partial') {
                                                                                            $badgeClass = 'warning';
                                                                                            $statusLabel = 'Partial';
                                                                                        } elseif ($sale->payment_status === 'unpaid') {
                                                                                            $badgeClass = 'danger';
                                                                                        }
                                                                                    }
                                                                                 ?>
                                                                                <span class="badge badge-modern bg-<?php echo e($badgeClass); ?>">
                                                                                    <?php echo e($statusLabel); ?>

                                                                                </span>
                                                                            </td>
                                                                            <td class="text-end pe-4">
                                                                                <?php if($sale->type === 'booking'): ?>
                                                                                    <button
                                                                                        class="btn btn-sm btn-light text-muted view-booking rounded-circle"
                                                                                        data-booking-id="<?php echo e($sale->id); ?>" data-bs-toggle="modal"
                                                                                        data-bs-target="#bookingDetailsModal" title="View Details">
                                                                                        <i class="fas fa-eye"></i>
                                                                                    </button>
                                                                                <?php else: ?>
                                                                                    <a href="<?php echo e(route('admin.pos.sales.show', $sale->id)); ?>"
                                                                                        class="btn btn-sm btn-light text-muted rounded-circle"
                                                                                        title="View Details">
                                                                                        <i class="fas fa-eye"></i>
                                                                                    </a>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="p-3">
                                                            <?php echo e($recent_bookings->links()); ?>

                                                        </div>
                                                    <?php else: ?>
                                                        <div class="empty-state">
                                                            <i class="fas fa-history text-muted"></i>
                                                            <p class="mb-0">No recent sales found.</p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Upcoming Appointments Widget -->
                                    <?php if($showSales): ?>
                                        <div class="mb-4">
                                            <div class="card-modern">
                                                <div class="widget-header widget-header-primary">
                                                    <i class="fas fa-calendar-alt me-2"></i>Upcoming Appointments
                                                </div>
                                                <div class="card-body p-0">
                                                    <?php
                                                        $upcomingAppointments = \App\Models\Booking::where('salon_id', auth()->user()->salon_id)
                                                            ->where('start_time', '>', now())
                                                            ->whereIn('status', ['pending', 'confirmed'])
                                                            ->select('bookings.*')
                                                            ->selectRaw('COALESCE((SELECT COUNT(*) FROM bookings b2 WHERE b2.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b2.salon_id = bookings.salon_id), 1) as group_count')
                                                            ->selectRaw('COALESCE((SELECT SUM(amount) FROM bookings b3 WHERE b3.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b3.salon_id = bookings.salon_id), amount) as group_total')
                                                            ->joinSub(
                                                                DB::table('bookings')
                                                                    ->select(DB::raw('MIN(id) as min_id'))
                                                                    ->where('salon_id', auth()->user()->salon_id)
                                                                    ->groupBy(DB::raw('COALESCE(booking_group_id, CAST(id AS CHAR))')),
                                                                'grouped',
                                                                'bookings.id',
                                                                '=',
                                                                'grouped.min_id'
                                                            )
                                                            ->with(['customer', 'service', 'staff', 'package'])
                                                            ->orderBy('start_time', 'asc')
                                                            ->take(10)
                                                            ->get()
                                                            ->map(function ($appointment) {
                                                                // Load all bookings in the group for tooltip
                                                                if ($appointment->booking_group_id) {
                                                                    $appointment->groupBookings = \App\Models\Booking::where('salon_id', auth()->user()->salon_id)
                                                                        ->where('booking_group_id', $appointment->booking_group_id)
                                                                        ->with(['service', 'staff', 'package'])
                                                                        ->get();
                                                                } else {
                                                                    $appointment->groupBookings = collect([$appointment]);
                                                                }
                                                                return $appointment;
                                                            });
                                                    ?>

                                                    <?php if($upcomingAppointments->count() > 0): ?>
                                                        <div class="table-responsive">
                                                            <table class="table table-hover align-middle mb-0">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Date &
                                                                            Time</th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Customer
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Service
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Staff</th>
                                                                        <th
                                                                            class="text-center py-3 text-uppercase small fw-bold text-muted">
                                                                            Status</th>
                                                                        <th
                                                                            class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">
                                                                            Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $__currentLoopData = $upcomingAppointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <tr>
                                                                            <td class="ps-4">
                                                                                <div class="fw-bold text-dark">
                                                                                    <?php echo e(format_date($appointment->start_time, 'M d, Y')); ?>

                                                                                </div>
                                                                                <div class="small text-muted">
                                                                                    <?php echo e(format_time($appointment->start_time)); ?>

                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-2"
                                                                                        style="width: 32px; height: 32px;">
                                                                                        <?php echo e(substr($appointment->customer->name ?? 'U', 0, 1)); ?>

                                                                                    </div>
                                                                                    <div>
                                                                                        <div class="fw-semibold text-dark">
                                                                                            <?php echo e($appointment->customer->name ?? 'Unknown'); ?>

                                                                                        </div>
                                                                                        <?php if($appointment->customer && $appointment->customer->is_guest): ?>
                                                                                            <span class="badge bg-secondary"
                                                                                                style="font-size: 0.6em;">Guest</span>
                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <?php if($appointment->group_count > 1): ?>
                                                                                    <?php
                                                                                        $tooltipHtml = "<div class='text-start'>";
                                                                                        foreach ($appointment->groupBookings as $gb) {
                                                                                            $tooltipHtml .= "<div class='tooltip-service-item'>";
                                                                                            $tooltipHtml .= "<strong>" . ($gb->service->name ?? 'Unknown') . "</strong><br>";
                                                                                            $tooltipHtml .= "<small>Staff: " . ($gb->staff->name ?? 'Unassigned') . "</small>";
                                                                                            $tooltipHtml .= "</div>";
                                                                                        }
                                                                                        $tooltipHtml .= "</div>";
                                                                                    ?>
                                                                                    <span
                                                                                        class="badge bg-info bg-opacity-10 text-info me-2 group-booking-badge"
                                                                                        style="font-size: 0.75em;" data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top" data-bs-html="true"
                                                                                        title="<?php echo e($tooltipHtml); ?>">
                                                                                        Group
                                                                                    </span>
                                                                                    <span class="fw-semibold text-dark">Group Booking
                                                                                        (<?php echo e($appointment->group_count); ?> services)</span>
                                                                                <?php else: ?>
                                                                                    <span
                                                                                        class="fw-semibold text-dark"><?php echo e($appointment->service->name ?? 'Unknown'); ?></span>
                                                                                    <?php if($appointment->package): ?>
                                                                                        <div class="mt-1">
                                                                                            <span
                                                                                                class="badge bg-purple-subtle text-purple-emphasis rounded-pill px-2 py-1"
                                                                                                style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.7em;">
                                                                                                <i
                                                                                                    class="fas fa-box me-1"></i><?php echo e($appointment->package->name); ?>

                                                                                            </span>
                                                                                        </div>
                                                                                    <?php endif; ?>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td>
                                                                                <?php if($appointment->group_count > 1): ?>
                                                                                    <?php
                                                                                        $uniqueStaff = $appointment->groupBookings->pluck('staff')->filter()->unique('id');
                                                                                    ?>
                                                                                    <?php if($uniqueStaff->count() > 0): ?>
                                                                                        <div class="staff-avatars">
                                                                                            <?php $__currentLoopData = $uniqueStaff->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                <div class="staff-avatar"
                                                                                                    style="z-index: <?php echo e(10 - $index); ?>;"
                                                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                                                    title="<?php echo e($staff->name); ?>">
                                                                                                    <?php echo e(substr($staff->name, 0, 1)); ?>

                                                                                                </div>
                                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                            <?php if($uniqueStaff->count() > 3): ?>
                                                                                                <div class="staff-avatar-more" data-bs-toggle="tooltip"
                                                                                                    data-bs-placement="top"
                                                                                                    title="<?php echo e($uniqueStaff->skip(3)->pluck('name')->implode(', ')); ?>">
                                                                                                    +<?php echo e($uniqueStaff->count() - 3); ?>

                                                                                                </div>
                                                                                            <?php endif; ?>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <span class="badge bg-light text-muted border">Unassigned</span>
                                                                                    <?php endif; ?>
                                                                                <?php else: ?>
                                                                                    <?php if($appointment->staff): ?>
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center me-2"
                                                                                                style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                                                                <?php echo e(substr($appointment->staff->name, 0, 1)); ?>

                                                                                            </div>
                                                                                            <span class="small"><?php echo e($appointment->staff->name); ?></span>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <span class="badge bg-light text-muted border">Unassigned</span>
                                                                                    <?php endif; ?>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span
                                                                                    class="badge badge-modern bg-<?php echo e($appointment->status === 'confirmed' ? 'success' : 'warning'); ?>">
                                                                                    <?php echo e(ucfirst($appointment->status)); ?>

                                                                                </span>
                                                                            </td>
                                                                            <td class="text-end pe-4">
                                                                                <button
                                                                                    class="btn btn-sm btn-light text-muted view-booking rounded-circle"
                                                                                    data-booking-id="<?php echo e($appointment->id); ?>"
                                                                                    data-bs-toggle="modal" data-bs-target="#bookingDetailsModal"
                                                                                    title="View Details">
                                                                                    <i class="fas fa-eye"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="empty-state">
                                                            <i class="fas fa-calendar-check text-muted"></i>
                                                            <p class="mb-0">No upcoming appointments</p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- No-Show Alerts Widget -->
                                    <?php if($showSales && isset($todays_no_shows)): ?>
                                        <div class="mb-4">
                                            <div class="card-modern">
                                                <div class="widget-header widget-header-danger">
                                                    <i class="fas fa-user-times me-2"></i>No-Shows
                                                    (<?php echo e($dateRange == 'today' ? 'Today' : 'Period'); ?>)
                                                </div>
                                                <div class="card-body p-4">
                                                    <?php if($todays_no_shows->count() > 0): ?>
                                                        <div class="space-y-2">
                                                            <?php $__currentLoopData = $todays_no_shows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $noShow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <div class="list-item-modern">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div>
                                                                            <div class="fw-bold text-dark">
                                                                                <?php echo e($noShow->customer->name ?? 'Unknown'); ?>

                                                                            </div>
                                                                            <div class="small text-muted">
                                                                                <i
                                                                                    class="fas fa-cut me-1"></i><?php echo e($noShow->service->name ?? 'N/A'); ?>

                                                                                <span class="mx-2">•</span>
                                                                                <i
                                                                                    class="fas fa-clock me-1"></i><?php echo e(format_time($noShow->start_time)); ?>

                                                                            </div>
                                                                        </div>
                                                                        <span class="badge badge-modern bg-danger">No Show</span>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="empty-state">
                                                            <i class="fas fa-check-circle text-success"></i>
                                                            <p class="mb-0">No no-shows <?php echo e($dateRange == 'today' ? 'today' : 'in period'); ?>!
                                                            </p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Outstanding Payments Widget -->
                                    <?php if($showSales && isset($outstanding_payments)): ?>
                                        <div class="mb-4">
                                            <div class="card-modern">
                                                <div
                                                    class="widget-header widget-header-warning d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-credit-card me-2"></i>Outstanding Payments
                                                    </div>
                                                    <span class="badge bg-white text-warning fw-bold">
                                                        Total:
                                                        <?php echo e(currency_symbol()); ?><?php echo e(number_format($outstanding_payments['total'], 2)); ?>

                                                    </span>
                                                </div>
                                                <div class="card-body p-4">
                                                    <?php if($outstanding_payments['items']->count() > 0): ?>
                                                        <div class="space-y-2">
                                                            <?php $__currentLoopData = $outstanding_payments['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <div class="list-item-modern">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div>
                                                                            <div class="fw-bold text-dark">
                                                                                <?php if(isset($payment->group_count) && $payment->group_count > 1): ?>
                                                                                    <span class="badge bg-info bg-opacity-10 text-info me-2"
                                                                                        style="font-size: 0.75em;">Group</span>
                                                                                    <?php echo e($payment->customer_name); ?> (Group Booking)
                                                                                <?php else: ?>
                                                                                    <?php echo e($payment->customer_name); ?>

                                                                                <?php endif; ?>
                                                                            </div>
                                                                            <div class="small text-muted mt-1">
                                                                                <span
                                                                                    class="badge badge-modern bg-<?php echo e($payment->type === 'booking' ? 'primary' : 'info'); ?> me-1">
                                                                                    <?php echo e(ucfirst($payment->type)); ?>

                                                                                </span>
                                                                                <?php if(isset($payment->status)): ?>
                                                                                    <span
                                                                                        class="badge badge-modern bg-<?php echo e($payment->status === 'Unpaid' ? 'danger' : 'warning'); ?> me-1">
                                                                                        <?php echo e($payment->status); ?>

                                                                                    </span>
                                                                                <?php endif; ?>
                                                                                <i
                                                                                    class="fas fa-calendar-alt me-1"></i><?php echo e(format_date($payment->date, 'M d, Y')); ?>

                                                                            </div>
                                                                        </div>
                                                                        <div class="text-end">
                                                                            <div class="fw-bold text-danger">
                                                                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($payment->amount, 2)); ?>

                                                                            </div>
                                                                            <div class="small mt-1">
                                                                                <span
                                                                                    class="badge badge-modern bg-<?php echo e($payment->days_overdue > 7 ? 'danger' : ($payment->days_overdue > 3 ? 'warning' : 'secondary')); ?>">
                                                                                    <?php echo e($payment->days_overdue); ?>

                                                                                    <?php echo e($payment->days_overdue == 1 ? 'day' : 'days'); ?> overdue
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="empty-state">
                                                            <i class="fas fa-check-circle text-success"></i>
                                                            <p class="mb-0">All payments are up to date!</p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Staff Insight Tab -->
                                    <?php if($showStaff): ?>
                                        <div class="tab-pane fade <?php echo e($activeTab == 'staff' ? 'show active' : ''); ?>" id="staff"
                                            role="tabpanel">
                                            <div class="d-flex justify-content-end mb-3">
                                                <a href="<?php echo e(route('admin.reports.staff')); ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt me-1"></i> Full Report
                                                </a>
                                            </div>

                                            <!-- Staff Commissions Widget -->
                                            <?php if(isset($staff_commissions)): ?>
                                                <div class="card-modern mb-4">
                                                    <div class="widget-header widget-header-success">
                                                        <i class="fas fa-dollar-sign me-2"></i>Staff Commissions & Tips
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <!-- Tabs for Today/Week/Month -->
                                                        <ul class="nav nav-pills mb-4" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link active" data-bs-toggle="pill"
                                                                    data-bs-target="#commissions-today"
                                                                    type="button"><?php echo e($dateRange == 'today' ? 'Today' : 'Period'); ?></button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" data-bs-toggle="pill"
                                                                    data-bs-target="#commissions-week" type="button">This Week</button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" data-bs-toggle="pill"
                                                                    data-bs-target="#commissions-month" type="button">This Month</button>
                                                            </li>
                                                        </ul>

                                                        <div class="tab-content">
                                                            <!-- Today -->
                                                            <div class="tab-pane fade show active" id="commissions-today">
                                                                <div class="row g-4">
                                                                    <div class="col-md-6">
                                                                        <div class="text-center p-4 rounded"
                                                                            style="background: linear-gradient(135deg, #11998e15 0%, #38ef7d15 100%);">
                                                                            <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                                Commissions</div>
                                                                            <div class="h3 mb-0 fw-bold text-success">
                                                                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($staff_commissions['today'], 2)); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="text-center p-4 rounded"
                                                                            style="background: linear-gradient(135deg, #4facfe15 0%, #00f2fe15 100%);">
                                                                            <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                                Tips</div>
                                                                            <div class="h3 mb-0 fw-bold text-info">
                                                                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($staff_commissions['today_tips'], 2)); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Week -->
                                                            <div class="tab-pane fade" id="commissions-week">
                                                                <div class="row g-4">
                                                                    <div class="col-md-6">
                                                                        <div class="text-center p-4 rounded"
                                                                            style="background: linear-gradient(135deg, #11998e15 0%, #38ef7d15 100%);">
                                                                            <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                                Commissions</div>
                                                                            <div class="h3 mb-0 fw-bold text-success">
                                                                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($staff_commissions['week'], 2)); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="text-center p-4 rounded"
                                                                            style="background: linear-gradient(135deg, #4facfe15 0%, #00f2fe15 100%);">
                                                                            <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                                Tips</div>
                                                                            <div class="h3 mb-0 fw-bold text-info">
                                                                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($staff_commissions['week_tips'], 2)); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Month -->
                                                            <div class="tab-pane fade" id="commissions-month">
                                                                <div class="row g-4 mb-4">
                                                                    <div class="col-md-6">
                                                                        <div class="text-center p-4 rounded"
                                                                            style="background: linear-gradient(135deg, #11998e15 0%, #38ef7d15 100%);">
                                                                            <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                                Commissions</div>
                                                                            <div class="h3 mb-0 fw-bold text-success">
                                                                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($staff_commissions['month'], 2)); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="text-center p-4 rounded"
                                                                            style="background: linear-gradient(135deg, #4facfe15 0%, #00f2fe15 100%);">
                                                                            <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                                Tips</div>
                                                                            <div class="h3 mb-0 fw-bold text-info">
                                                                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($staff_commissions['month_tips'], 2)); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Top Earners -->
                                                                <?php if(count($staff_commissions['top_earners']) > 0): ?>
                                                                    <div class="mt-4">
                                                                        <h6 class="text-muted mb-3 text-uppercase fw-semibold small">Top Earners
                                                                            This Month</h6>
                                                                        <div class="space-y-2">
                                                                            <?php $__currentLoopData = $staff_commissions['top_earners']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $earner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <div class="list-item-modern">
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                        <span
                                                                                            class="fw-semibold text-dark"><?php echo e($earner['name']); ?></span>
                                                                                        <span
                                                                                            class="badge badge-modern bg-success"><?php echo e(currency_symbol()); ?><?php echo e(number_format($earner['commission'], 2)); ?></span>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="row g-3">
                                                <!-- Staff Performance Chart -->
                                                <div class="col-lg-6">
                                                    <div class="card-modern h-100">
                                                        <div class="widget-header widget-header-primary">
                                                            <i class="fas fa-chart-bar me-2"></i>Staff Revenue Performance
                                                        </div>
                                                        <div class="card-body p-4">
                                                            <?php if(isset($staff_performance) && count($staff_performance) > 0): ?>
                                                                <div class="chart-bar" style="height: 300px;">
                                                                    <canvas id="staffPerformanceChart"></canvas>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="empty-state">
                                                                    <i class="fas fa-chart-bar text-muted"></i>
                                                                    <p class="mb-0">No staff performance data available</p>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Staff Schedule Table -->
                                                <div class="col-lg-6">
                                                    <div class="card-modern h-100">
                                                        <div class="widget-header widget-header-primary">
                                                            <i class="fas fa-calendar-day me-2"></i>Today's Schedule
                                                        </div>
                                                        <div class="card-body p-0">
                                                            <?php if(isset($staff_schedule) && count($staff_schedule) > 0): ?>
                                                                <div class="table-responsive">
                                                                    <table class="table table-hover align-middle mb-0">
                                                                        <thead class="bg-light">
                                                                            <tr>
                                                                                <th
                                                                                    class="ps-4 py-3 text-uppercase small fw-bold text-muted">
                                                                                    Staff</th>
                                                                                <th
                                                                                    class="text-center py-3 text-uppercase small fw-bold text-muted">
                                                                                    Appts</th>
                                                                                <th class="py-3 text-uppercase small fw-bold text-muted">
                                                                                    Next Slot</th>
                                                                                <th
                                                                                    class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">
                                                                                    Status</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php $__currentLoopData = $staff_schedule; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <tr>
                                                                                    <td class="ps-4">
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3"
                                                                                                style="width: 36px; height: 36px;">
                                                                                                <?php echo e(substr($schedule->staff->name, 0, 1)); ?>

                                                                                            </div>
                                                                                            <span
                                                                                                class="fw-semibold text-dark"><?php echo e($schedule->staff->name); ?></span>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <span
                                                                                            class="badge bg-light text-dark border"><?php echo e($schedule->appointments_count); ?></span>
                                                                                    </td>
                                                                                    <td class="text-muted small"><?php echo e($schedule->next_available); ?>

                                                                                    </td>
                                                                                    <td class="text-end pe-4">
                                                                                        <span
                                                                                            class="badge badge-modern bg-<?php echo e($schedule->status === 'available' ? 'success' : 'warning'); ?>">
                                                                                            <?php echo e(ucfirst($schedule->status)); ?>

                                                                                        </span>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="empty-state">
                                                                    <i class="fas fa-calendar-times text-muted"></i>
                                                                    <p class="mb-0">No schedule data available.</p>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Customer Insight Tab -->
                                    <?php if($showCustomer): ?>
                                        <div class="tab-pane fade <?php echo e($activeTab == 'customer' ? 'show active' : ''); ?>" id="customer"
                                            role="tabpanel">
                                            <div class="d-flex justify-content-end mb-3">
                                                <a href="<?php echo e(route('admin.reports.customers')); ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt me-1"></i> Full Report
                                                </a>
                                            </div>

                                            <!-- Customer Feedback Widget -->
                                            <?php if(isset($customer_feedback)): ?>
                                                <div class="card-modern mb-4">
                                                    <div class="widget-header widget-header-warning">
                                                        <i class="fas fa-star me-2"></i>Customer Feedback & Ratings
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <div class="row g-4 mb-4">
                                                            <div class="col-md-6">
                                                                <div class="text-center p-4 rounded"
                                                                    style="background: linear-gradient(135deg, #f093fb15 0%, #f5576c15 100%);">
                                                                    <div class="text-muted small mb-2 text-uppercase fw-semibold">Average
                                                                        Rating</div>
                                                                    <div class="h2 mb-2 fw-bold text-warning">
                                                                        <?php echo e($customer_feedback['average_rating']); ?>

                                                                        <i class="fas fa-star"></i>
                                                                    </div>
                                                                    <div class="small text-muted">Based on
                                                                        <?php echo e($customer_feedback['total_reviews']); ?> reviews
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="text-center p-4 rounded"
                                                                    style="background: linear-gradient(135deg, #4facfe15 0%, #00f2fe15 100%);">
                                                                    <div class="text-muted small mb-2 text-uppercase fw-semibold">Total
                                                                        Reviews</div>
                                                                    <div class="h2 mb-2 fw-bold text-info">
                                                                        <?php echo e($customer_feedback['total_reviews']); ?>

                                                                    </div>
                                                                    <div class="small text-muted">All time</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php if(count($customer_feedback['recent_reviews']) > 0): ?>
                                                            <div class="mt-4">
                                                                <h6 class="text-muted mb-3 text-uppercase fw-semibold small">Recent Reviews</h6>
                                                                <div class="space-y-2">
                                                                    <?php $__currentLoopData = $customer_feedback['recent_reviews']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <div class="list-item-modern">
                                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                                <div class="fw-semibold text-dark">
                                                                                    <?php echo e($review->customer->name ?? 'Anonymous'); ?>

                                                                                </div>
                                                                                <div>
                                                                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                                                                        <i class="fas fa-star <?php echo e($i <= $review->rating ? 'text-warning' : 'text-muted'); ?>"
                                                                                            style="font-size: 0.8rem;"></i>
                                                                                    <?php endfor; ?>
                                                                                </div>
                                                                            </div>
                                                                            <?php if(isset($review->comment)): ?>
                                                                                <p class="mb-2 small text-muted"><?php echo e(Str::limit($review->comment, 100)); ?>

                                                                                </p>
                                                                            <?php endif; ?>
                                                                            <small
                                                                                class="text-muted d-block text-end"><?php echo e($review->created_at->diffForHumans()); ?></small>
                                                                        </div>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </div>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="empty-state">
                                                                <i class="fas fa-comments text-muted"></i>
                                                                <p class="mb-0">No reviews yet</p>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Birthday Reminders Widget -->
                                            <?php if(isset($upcoming_birthdays) && $upcoming_birthdays->count() > 0): ?>
                                                <div class="card-modern mb-4">
                                                    <div class="widget-header widget-header-info">
                                                        <i class="fas fa-birthday-cake me-2"></i>Upcoming Birthdays (Next 7 Days)
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <div class="space-y-2">
                                                            <?php $__currentLoopData = $upcoming_birthdays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <div class="list-item-modern d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <div class="fw-semibold text-dark"><?php echo e($customer->name); ?></div>
                                                                        <small class="text-muted">
                                                                            <i
                                                                                class="fas fa-phone me-1"></i><?php echo e($customer->phone ?? 'No phone'); ?>

                                                                        </small>
                                                                    </div>
                                                                    <div class="text-end">
                                                                        <div class="fw-bold text-info">
                                                                            <?php echo e($customer->birthday->format('M d')); ?>

                                                                        </div>
                                                                        <small class="text-muted">
                                                                            <?php if($customer->days_until == 0): ?>
                                                                                <span class="badge badge-modern bg-success">Today!</span>
                                                                            <?php elseif($customer->days_until == 1): ?>
                                                                                <span class="badge badge-modern bg-warning">Tomorrow</span>
                                                                            <?php else: ?>
                                                                                <?php echo e($customer->days_until); ?> days
                                                                            <?php endif; ?>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="row g-4 mb-4">
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-primary mb-1">Total
                                                                        Customers</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark"><?php echo e($total_customers ?? 0); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                                                    <i class="fas fa-users fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-success mb-1">Active
                                                                        Customers</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark"><?php echo e($active_customers ?? 0); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-success bg-opacity-10 text-success">
                                                                    <i class="fas fa-user-check fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-info mb-1">New This
                                                                        Month</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark">
                                                                        <?php echo e($new_customers_month ?? 0); ?>

                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-info bg-opacity-10 text-info">
                                                                    <i class="fas fa-user-plus fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-warning mb-1">
                                                                        Returning Rate</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark">
                                                                        <?php echo e(number_format($returning_rate ?? 0, 1)); ?>%
                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                                                    <i class="fas fa-sync-alt fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Top Customers Table -->
                                            <div class="card-modern">
                                                <div class="widget-header widget-header-primary">
                                                    <i class="fas fa-crown me-2"></i>Top Customers
                                                </div>
                                                <div class="card-body p-0">
                                                    <?php if(isset($top_customers) && count($top_customers) > 0): ?>
                                                        <div class="table-responsive">
                                                            <table class="table table-hover align-middle mb-0">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">
                                                                            Customer</th>
                                                                        <th
                                                                            class="text-center py-3 text-uppercase small fw-bold text-muted">
                                                                            Visits</th>
                                                                        <th class="text-end py-3 text-uppercase small fw-bold text-muted">
                                                                            Total Spent</th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Last Visit
                                                                        </th>
                                                                        <th
                                                                            class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">
                                                                            Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $__currentLoopData = $top_customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <tr>
                                                                            <td class="ps-4">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3"
                                                                                        style="width: 36px; height: 36px;">
                                                                                        <?php echo e(substr($customer->name, 0, 1)); ?>

                                                                                    </div>
                                                                                    <span
                                                                                        class="fw-semibold text-dark"><?php echo e($customer->name); ?></span>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span
                                                                                    class="badge bg-light text-dark border"><?php echo e($customer->visits_count); ?></span>
                                                                            </td>
                                                                            <td class="text-end fw-bold text-dark">
                                                                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($customer->total_spent, 2)); ?>

                                                                            </td>
                                                                            <td class="text-muted small"><?php echo e($customer->last_visit); ?></td>
                                                                            <td class="text-end pe-4">
                                                                                <span
                                                                                    class="badge badge-modern bg-<?php echo e($customer->status === 'active' ? 'success' : 'warning'); ?>">
                                                                                    <?php echo e(ucfirst($customer->status)); ?>

                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="empty-state">
                                                            <i class="fas fa-users-slash text-muted"></i>
                                                            <p class="mb-0">No top customers data available.</p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Customer Dashboard -->
        <?php if (\Illuminate\Support\Facades\Blade::check('role', 'customer')): ?>
        <div class="row g-3 mb-4">
            <!-- Stats Cards -->
            <div class="col-xl-4 col-md-6">
                <div class="stat-card gradient-card-1">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Upcoming Appointments</div>
                                <div class="stat-value"><?php echo e($upcoming_appointments_count ?? 0); ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="stat-card gradient-card-2">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Completed Visits</div>
                                <div class="stat-value"><?php echo e($completed_appointments ?? 0); ?></div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="stat-card gradient-card-3">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Total Spent</div>
                                <div class="stat-value">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($total_spent ?? 0, 2)); ?>

                                </div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Upcoming Appointments -->
            <div class="col-lg-8">
                <div class="card-modern h-100">
                    <div class="widget-header widget-header-primary d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-calendar-day me-2"></i>Upcoming Appointments
                        </div>
                        <a href="<?php echo e(route('customer.appointments.create')); ?>"
                            class="btn btn-sm btn-light text-primary fw-bold rounded-pill px-3">
                            <i class="fas fa-plus me-1"></i> Book New
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <?php if(isset($upcoming_appointments) && $upcoming_appointments->count() > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Date & Time</th>
                                            <th class="py-3 text-uppercase small fw-bold text-muted">Service</th>
                                            <th class="py-3 text-uppercase small fw-bold text-muted">Staff</th>
                                            <th class="text-center py-3 text-uppercase small fw-bold text-muted">Status</th>
                                            <th class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $upcoming_appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark"><?php echo e(format_date($appointment->start_time)); ?></div>
                                                    <div class="small text-muted"><?php echo e(format_time($appointment->start_time)); ?></div>
                                                </td>
                                                <td><span
                                                        class="fw-semibold text-dark"><?php echo e($appointment->service->name ?? 'Unknown'); ?></span>
                                                </td>
                                                <td>
                                                    <?php if($appointment->staff): ?>
                                                        <div class="d-flex align-items-center">
                                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2"
                                                                style="width: 28px; height: 28px; font-size: 0.7rem;">
                                                                <?php echo e(substr($appointment->staff->name, 0, 1)); ?>

                                                            </div>
                                                            <span class="small"><?php echo e($appointment->staff->name); ?></span>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-muted border">Any Staff</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge badge-modern bg-<?php echo e($appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'pending' ? 'warning' : 'danger')); ?>">
                                                        <?php echo e(ucfirst($appointment->status)); ?>

                                                    </span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="<?php echo e(route('customer.appointments.show', $appointment->id)); ?>"
                                                            class="btn btn-light text-primary rounded-circle me-1"
                                                            title="View Details"
                                                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if(!in_array($appointment->status, ['completed', 'cancelled'])): ?>
                                                            <button type="button"
                                                                class="btn btn-light text-info reschedule-btn rounded-circle"
                                                                data-appointment-id="<?php echo e($appointment->id); ?>"
                                                                data-service-id="<?php echo e($appointment->service_id); ?>"
                                                                data-current-date="<?php echo e($appointment->start_time->format('Y-m-d')); ?>"
                                                                data-current-time="<?php echo e($appointment->start_time->format('H:i')); ?>"
                                                                title="Reschedule"
                                                                style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-calendar-alt"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if($upcoming_appointments->hasPages()): ?>
                                <div class="card-footer bg-white border-top p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-muted small">
                                            Showing <?php echo e($upcoming_appointments->firstItem()); ?> to
                                            <?php echo e($upcoming_appointments->lastItem()); ?> of <?php echo e($upcoming_appointments->total()); ?>

                                            appointments
                                        </div>
                                        <div>
                                            <?php echo e($upcoming_appointments->links('pagination::bootstrap-5')); ?>

                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-check text-muted"></i>
                                <p class="mb-3">No upcoming appointments</p>
                                <a href="<?php echo e(route('customer.appointments.create')); ?>" class="btn btn-primary rounded-pill px-4">
                                    Book Your First Appointment
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-lg-4">
                <div class="card-modern h-100">
                    <div class="widget-header widget-header-info">
                        <i class="fas fa-history me-2"></i>Recent Activity
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($recent_activities) && count($recent_activities) > 0): ?>
                            <div class="space-y-2">
                                <?php $__currentLoopData = $recent_activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="list-item-modern">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <small class="text-muted">
                                                <i class="far fa-clock me-1"></i>
                                                <?php echo e($activity->created_at->diffForHumans()); ?>

                                            </small>
                                            <span class="badge badge-modern bg-light text-dark border">
                                                <?php echo e(ucfirst($activity->type)); ?>

                                            </span>
                                        </div>
                                        <p class="mb-0 text-dark small"><?php echo e($activity->description); ?></p>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-history text-muted"></i>
                                <p class="mb-0">No recent activity found</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="card-modern">
                    <div class="widget-header widget-header-primary d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-calendar-alt me-2"></i>Appointment Calendar
                        </div>
                        <span class="badge bg-white text-primary">
                            <i class="fas fa-info-circle me-1"></i> Click a date to book
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div id="calendar" class="p-4"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>


        <?php $__env->startPush('styles'); ?>
            <style>
                .fc-event {
                    cursor: pointer;
                }

                .fc-daygrid-day {
                    cursor: pointer;
                    transition: background-color 0.2s;
                }

                .fc-daygrid-day:hover {
                    background-color: rgba(0, 0, 0, 0.02);
                }

                .fc-toolbar-title {
                    font-size: 1.2rem !important;
                }

                .fc-button {
                    font-size: 0.8rem !important;
                }
            </style>
        <?php $__env->stopPush(); ?>

        <!-- Include Bill Activity Modal -->
        <?php echo $__env->make('admin.customers.partials.bill_activity_modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Booking Modal -->
        <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white border-0">
                        <h5 class="modal-title fw-bold" id="bookingModalLabel">
                            <i class="fas fa-calendar-plus me-2"></i>Book Your Appointment
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="modalBookingForm">
                            <?php echo csrf_field(); ?>

                            <!-- Service Selection Dropdown -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="fas fa-concierge-bell me-2 text-primary"></i>Select Service
                                </label>
                                <select class="form-select form-select-lg" name="service_id" id="modal_service_select"
                                    required>
                                    <option value="">Choose a service...</option>
                                    <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($service->id); ?>" data-duration="<?php echo e($service->duration); ?>"
                                            data-price="<?php echo e($service->price); ?>">
                                            <?php echo e($service->name); ?> -
                                            <?php echo e(currency_symbol()); ?><?php echo e(number_format($service->price, 2)); ?>

                                            (<?php echo e($service->duration); ?> min)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <!-- Service Summary Card -->
                            <div class="alert alert-light border-start border-primary border-4 d-none mb-4"
                                id="modalServiceSummary">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle fa-2x text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 fw-bold text-dark">Service Details</h6>
                                        <div class="row g-2 small">
                                            <div class="col-6">
                                                <i class="fas fa-clock text-muted me-1"></i>
                                                <strong>Duration:</strong> <span id="modalServiceDuration">0</span> minutes
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-dollar-sign text-muted me-1"></i>
                                                <strong>Price:</strong> $<span id="modalServicePrice">0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Date and Time Selection -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-calendar me-2 text-primary"></i>Appointment Date
                                    </label>
                                    <input type="date" name="date" id="modal_date" class="form-control form-control-lg"
                                        required min="<?php echo e(date('Y-m-d')); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-clock me-2 text-primary"></i>Appointment Time
                                    </label>
                                    <select name="time" id="modal_time" class="form-select form-select-lg" required>
                                        <option value="">Choose a time slot...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Notes Section -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="fas fa-sticky-note me-2 text-primary"></i>Additional Notes <span
                                        class="text-muted small">(Optional)</span>
                                </label>
                                <textarea name="notes" id="modal_notes" class="form-control" rows="3"
                                    placeholder="Any special requests or requirements..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-primary px-4" onclick="submitModalBooking()">
                            <i class="fas fa-calendar-check me-1"></i>Book Appointment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment Details Modal -->
        <div class="modal fade" id="appointmentDetailsModal" tabindex="-1" aria-labelledby="appointmentDetailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="appointmentDetailsModalLabel">
                            <i class="fas fa-calendar-alt me-2"></i>Appointment Details
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="appointment-details">
                            <!-- Service Info -->
                            <div class="detail-item mb-3">
                                <div class="detail-label">
                                    <i class="fas fa-cut text-primary me-2"></i>Service
                                </div>
                                <div class="detail-value fw-bold" id="detail_service">-</div>
                            </div>

                            <!-- Staff Info -->
                            <div class="detail-item mb-3">
                                <div class="detail-label">
                                    <i class="fas fa-user text-primary me-2"></i>Staff Member
                                </div>
                                <div class="detail-value" id="detail_staff">-</div>
                            </div>

                            <!-- Date & Time -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-calendar text-primary me-2"></i>Date
                                        </div>
                                        <div class="detail-value" id="detail_date">-</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-clock text-primary me-2"></i>Time
                                        </div>
                                        <div class="detail-value" id="detail_time">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="detail-item mb-3">
                                <div class="detail-label">
                                    <i class="fas fa-info-circle text-primary me-2"></i>Status
                                </div>
                                <div class="detail-value">
                                    <span class="badge" id="detail_status">-</span>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="detail-item mb-3">
                                <div class="detail-label">
                                    <i class="fas fa-dollar-sign text-primary me-2"></i>Amount
                                </div>
                                <div class="detail-value fw-bold text-white" id="detail_amount">-</div>
                            </div>

                            <!-- Notes (if any) -->
                            <div class="detail-item" id="detail_notes_container" style="display: none;">
                                <div class="detail-label">
                                    <i class="fas fa-sticky-note text-primary me-2"></i>Notes
                                </div>
                                <div class="detail-value text-muted" id="detail_notes">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Close
                        </button>
                        <a href="#" id="view_appointment_btn" class="btn btn-primary">
                            <i class="fas fa-eye me-1"></i>View Full Details
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reschedule Appointment Modal -->
        <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="rescheduleModalLabel">
                            <i class="fas fa-calendar-alt me-2"></i>Reschedule Appointment
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="rescheduleForm">
                        <div class="modal-body">
                            <input type="hidden" id="reschedule_appointment_id">
                            <input type="hidden" id="reschedule_service_id">

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Select a new date and time for your appointment</small>
                            </div>

                            <!-- Date & Time Selection -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-calendar text-info me-2"></i>New Date
                                    </label>
                                    <input type="date" id="reschedule_date" class="form-control form-control-lg" required
                                        min="<?php echo e(date('Y-m-d')); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-clock text-info me-2"></i>New Time
                                    </label>
                                    <select id="reschedule_time" class="form-select form-select-lg" required>
                                        <option value="">Choose time slot...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-check me-1"></i>Confirm Reschedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php $__env->startPush('styles'); ?>
            <style>
                /* Appointment Details Modal Styles */
                .appointment-details {
                    padding: 0.5rem;
                }

                .detail-item {
                    padding: 0.75rem;
                    background: #f8f9fa;
                    border-radius: 0.5rem;
                }

                .detail-label {
                    font-size: 0.875rem;
                    color: #6c757d;
                    margin-bottom: 0.25rem;
                }

                .detail-value {
                    font-size: 1rem;
                    color: #212529;
                }

                /* Modal Enhancements */
                .modal-content {
                    border-radius: 1rem;
                    overflow: hidden;
                }

                .modal-header {
                    padding: 1.5rem;
                }

                .modal-body {
                    background-color: #f8f9fa;
                }

                .form-select-lg,
                .form-control-lg {
                    border-radius: 0.5rem;
                    border: 2px solid #e9ecef;
                    transition: all 0.3s ease;
                }

                .form-select-lg:focus,
                .form-control-lg:focus {
                    border-color: #0d6efd;
                    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
                }

                .form-control:focus,
                .form-select:focus {
                    border-color: #0d6efd;
                    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
                }

                .alert-light {
                    background-color: #fff;
                }
            </style>
        <?php $__env->stopPush(); ?>

        <?php $__env->startPush('scripts'); ?>
            <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
            <script>         // Helper function to get browser's timezone         function getBrowserTimezone() {             try {                 return Intl.DateTimeFormat().resolvedOptions().timeZone;             } catch (e) {                 return 'local';             }         }
                                                 // Helper function to get timezone with salon preference and browser fallback         function getCalendarTimezone(salonTimezone) {             if (salonTimezone && salonTimezone.trim() !== '' && salonTimezone !== 'UTC') {                 try {                     Intl.DateTimeFormat(undefined, { timeZone: salonTimezone });                     return salonTimezone;                 } catch (e) {
                                                         }             }             return getBrowserTimezone();         }         // Helper functions to format date and time in specific timezone         function formatDateInTimezone(date, timezone) {             return new Intl.DateTimeFormat('en-CA', {                 timeZone: timezone,                 year: 'numeric',                 month: '2-digit',                 day: '2-digit'             }).format(date);         }         function formatTimeInTimezone(date, timezone) { return new Intl.DateTimeFormat('en-GB', { timeZone: timezone, hour: '2-digit', minute: '2-digit', hour12: false }).format(date); }         document.addEventListener('DOMContentLoaded', function () {             // Only initialize if calendar element exists             var calendarEl = document.getElementById('calendar');             if (calendarEl) {                 // Get timezone: use salon timezone with fallback to browser timezone                 const salonTimezone = '<?php echo e($settings->get("timezone", "UTC")); ?>';                 const calendarTimezone = getCalendarTimezone(salonTimezone);             var calendar = new FullCalendar.Calendar(calendarEl, {                 initialView: 'dayGridMonth', timeZone: calendarTimezone, headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' }, events: '<?php echo e(route("customer.api.appointments.index")); ?>', dateClick: function (info) {                         // Open modal with selected date                         document.getElementById('modal_date').value = info.dateStr;                         var bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));                         bookingModal.show();                     },                     eventClick: function (info) {                         // Fetch and show appointment details in modal                         info.jsEvent.preventDefault();                     if (info.event.id) {                             // Get salon slug from current URL                             const pathParts = window.location.pathname.split('/');                             const salonSlug = getSalonSlug(); // Using global helper                         fetch(`/${salonSlug}/customer/api/appointments/${info.event.id}`).then(response => response.json()).then(data => {                                     // Populate modal with appointment data                                     document.getElementById('detail_service').textContent = data.service?.name || '-';                                     document.getElementById('detail_staff').textContent = data.staff?.name || 'Any Staff';                             // Format date and time using timezone                                     const startTime = new Date(data.start_time);                                     document.getElementById('detail_date').textContent = formatDateInTimezone(startTime, calendarTimezone);                                     document.getElementById('detail_time').textContent = formatTimeInTimezone(startTime, calendarTimezone);                             // Status badge                                     const statusBadge = document.getElementById('detail_status');                                     statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);                                     statusBadge.className = 'badge';                                     if (data.status === 'confirmed') {                                         statusBadge.classList.add('bg-success');                                     } else if (data.status === 'pending') {                                         statusBadge.classList.add('bg-warning');                                     } else if (data.status === 'completed') {                                         statusBadge.classList.add('bg-info');                                     } else {                                         statusBadge.classList.add('bg-danger');                                     }                             // Amount                                     document.getElementById('detail_amount').textContent = '$' + parseFloat(data.amount || 0).toFixed(2);                             // Notes                                     if (data.notes) {                                         document.getElementById('detail_notes').textContent = data.notes;                                         document.getElementById('detail_notes_container').style.display = 'block';                                     } else {                                         document.getElementById('detail_notes_container').style.display = 'none';                                     }                             // Update view details button link                                     document.getElementById('view_appointment_btn').href = '/' + salonSlug + '/customer/appointments/' + data.id;                             // Show modal                                     var detailsModal = new bootstrap.Modal(document.getElementById('appointmentDetailsModal'));                                     detailsModal.show();                                 })                                 .catch(error => {                                     alert('Unable to load appointment details. Please try again.');                                 });                         }                     },                     eventDidMount: function (info) {                         // Add tooltip                         if (info.event.extendedProps.status) {                             info.el.title = info.event.title + ' (' + info.event.extendedProps.status + ')';                         }                     }                 });                 calendar.render();             }                             // Modal service selection handler (dropdown)             const modalServiceSelect = document.getElementById('modal_service_select');             const modalDateInput = document.getElementById('modal_date');             const modalTimeSelect = document.getElementById('modal_time');                             modalServiceSelect.addEventListener('change', function () {                                 const selectedOption = this.options[this.selectedIndex]; if (selectedOption.value) {                                     const duration = selectedOption.dataset.duration; const price = selectedOption.dataset.price;                                     document.getElementById('modalServiceDuration').textContent = duration; document.getElementById('modalServicePrice').textContent = parseFloat(price).toFixed(2); document.getElementById('modalServiceSummary').classList.remove('d-none');                                     // Fetch available slots if date is selected                     if (modalDateInput.value) {                         fetchModalAvailableSlots(modalDateInput.value, selectedOption.value);                     }                 } else {                     document.getElementById('modalServiceSummary').classList.add('d-none');                 }             });                                     // Modal date change handler             modalDateInput.addEventListener('change', function () {                 const selectedService = modalServiceSelect.value;                 if (selectedService) {                     fetchModalAvailableSlots(this.value, selectedService);                 }             });                                     // Fetch available time slots for modal             function fetchModalAvailableSlots(date, serviceId) {                 fetch(`<?php echo e(route('customer.api.appointments.available-slots')); ?>?date=${date}&service_id=${serviceId}`)                     .then(response => response.json())                     .then(data => {                         modalTimeSelect.innerHTML = '<option value="">Choose a time slot</option>';                         data.forEach(slot => {                             if (slot.available) {                                 const option = document.createElement('option');                                 option.value = slot.time;                                 option.textContent = slot.time;                                 modalTimeSelect.appendChild(option);                             }                         });                     })                     .catch(error => {                         Swal.fire({                             icon: 'error',                             title: 'Oops...',                             text: 'Failed to fetch available time slots. Please try again.'                         });                     });             }         });                                     // Submit modal booking         function submitModalBooking() {             const form = document.getElementById('modalBookingForm');             const formData = new FormData(form);                                     fetch('<?php echo e(route("customer.appointments.store")); ?>', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }).then(response => response.json()).then(data => {                                         if (data.message) { Swal.fire({ icon: 'success', title: 'Success!', text: data.message, showConfirmButton: false, timer: 1500 }).then(() => { location.reload(); }); } else { throw new Error('Failed to book appointment'); }                                     }
                // Reschedule Functionality         document.addEventListener('DOMContentLoaded', function () {             const rescheduleModal = new bootstrap.Modal(document.getElementById('rescheduleModal'));             const rescheduleForm = document.getElementById('rescheduleForm');             const rescheduleDateInput = document.getElementById('reschedule_date');             const rescheduleTimeSelect = document.getElementById('reschedule_time');
                // Handle Reschedule Button Click             document.querySelectorAll('.reschedule-btn').forEach(button => {                 button.addEventListener('click', function () {                     const appointmentId = this.dataset.appointmentId;                     const serviceId = this.dataset.serviceId;                     const currentDate = this.dataset.currentDate;
                document.getElementById('reschedule_appointment_id').value = appointmentId; document.getElementById('reschedule_service_id').value = serviceId; rescheduleDateIn                                                             put.value = currentDate;
                // Clear time select                     rescheduleTimeSelect.innerHTML = '<option value="">Choose time slot...</option>';
                // Show modal                     rescheduleModal.show();
                // Trigger slot fetch for current date                     fetchRescheduleSlots(currentDate, serviceId);                 });             });
                // Handle Date Change             rescheduleDateInput.addEventListener('change', function () {                 const serviceId = document.getElementById('reschedule_service_id').value;                 if (this.value && serviceId) {                     fetchRescheduleSlots(this.value, serviceId);                 }             });
                // Fetch Slots Function             function fetchRescheduleSlots(date, serviceId) {                 // Get salon slug from URL                 const salonSlug = "<?php echo e(app()->bound('current_salon') ? app('current_salon')->slug : ''); ?>";
                rescheduleTimeSelect.innerHTML = '<option value="">Loading...</option>'; rescheduleTimeSelect.disabled = true;
                fetch(`/${salonSlug}/customer/api/available-slots?date=${date}&service_id=${serviceId}`).then(response => response.json()).then(data => {
                    rescheduleTimeSelect.innerHTML = '<option value="">Choose time slot...</option>'; rescheduleTimeSelect.disabled = false;
                    if (data.length === 0) { rescheduleTimeSelect.innerHTML = '<option value="">No slots available</option>'; return; }
                    data.forEach(slot => { if (slot.available) { const option = document.createElement('option'); option.value = slot.time; option.textContent = slot.time; rescheduleTimeSelect.appendChild(option); } });
                }).catch(error => { rescheduleTimeSelect.innerHTML = '<option value="">Error loading slots</option>'; rescheduleTimeSelect.disabled = false; });
                                                                                }
                // Handle Form Submission             reschedule        Form.addEventListener('submit', function (e) {                 e.preventDefault();
                const appointmentId = document.getElementById('r        eschedule_appointment_id').value; const date = rescheduleDateInput.value; const time = rescheduleTimeSelect.value;
                if (!date || !time) { Swal.fire('Error', 'Please select both date and time', 'error'); return; }
                // Get salon slug from URL                 const salonSlug = "<?php echo e(app()->bound('current_salon') ? app('current_salon')->slug : ''); ?>";
                const submitBtn = this.querySelector('button[type="submit"]'); const originalText = submitBtn.innerHTML; submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
                fetch(`/${salonSlug}/customer/appointments/${appointmentId}/reschedule`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ date: date, time: time }) }).then(response => response.json()).then(data => { if (data.message) { rescheduleModal.hide(); Swal.fire({ icon: 'success', title: 'Rescheduled!', text: data.message, showConfirmButton: false, timer: 1500 }).then(() => { location.reload(); }); } else { throw new Error(data.message || 'Failed to reschedule'); } }).catch(error => { Swal.fire('Error', error.message || 'Failed to reschedule appointment', 'error'); }).finally(() => { submitBtn.disabled = false; submitBtn.innerHTML = originalText; });
                                                                            });
                                                                        });
            </script>
        <?php $__env->stopPush(); ?>

        <!-- Role-specific Content (Super Admin, Salon Admin, Manager) -->
        <!-- ... (Keeping existing role-specific content blocks but wrapping them in cleaner containers if needed, 
                                                                                                                                                                                                                                                     for now assuming the abo    ve analytics block replaces the main dashboard view for these roles as per original code structure) ... -->

        <!-- NOTE: The original code had role-specific blocks below the analytics tabs. 
                                                                                                                                                                                                                                                     I will preserve them but     ensure they are styled consistently. -->

        <?php if (\Illuminate\Support\Facades\Blade::check('role', 'super_admin')): ?>
        <div class="row g-3 mb-4">
            <!-- System Overview -->
            <div class="col-xl-8">
                <div class="card-modern h-100">
                    <div class="widget-header widget-header-primary">
                        <i class="fas fa-server me-2"></i>System Overview
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center h-100">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Total Users</div>
                                    <div class="h3 mb-0 fw-bold text-gray-800"><?php echo e($total_users); ?></div>
                                </div>
                            </div>
                            <?php if(\App\Helpers\ModuleHelper::staffEnabled()): ?>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-light text-center h-100">
                                        <div class="text-uppercase small fw-bold text-success mb-2">Active Employees</div>
                                        <div class="h3 mb-0 fw-bold text-gray-800"><?php echo e($active_employees); ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center h-100">
                                    <div class="text-uppercase small fw-bold text-info mb-2">Total Roles</div>
                                    <div class="h3 mb-0 fw-bold text-gray-800"><?php echo e($total_roles); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-xl-4">
                <div class="card-modern h-100">
                    <div class="widget-header widget-header-primary">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="<?php echo e(route('admin.salon-settings.index', ['salon_slug' => $salon->slug])); ?>"
                                class="list-group-item list-group-item-action px-4 py-3 border-0">
                                <i class="fas fa-cog me-3 text-muted"></i>System Settings
                            </a>
                            <a href="<?php echo e(route('admin.roles.index')); ?>"
                                class="list-group-item list-group-item-action px-4 py-3 border-0 bg-light">
                                <i class="fas fa-user-tag me-3 text-muted"></i>Manage Roles
                            </a>
                            <?php if(\App\Helpers\ModuleHelper::staffEnabled()): ?>
                                <a href="<?php echo e(route('admin.employees.index')); ?>"
                                    class="list-group-item list-group-item-action px-4 py-3 border-0">
                                    <i class="fas fa-users me-3 text-muted"></i>Manage Employees
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('salon.view')): ?>
                <div class="row g-3 mb-4">
                    <!-- Salon Overview -->
                    <div class="col-xl-8">
                        <div class="card-modern h-100">
                            <div class="widget-header widget-header-info">
                                <i class="fas fa-store me-2"></i>Salon Overview
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <?php if(\App\Helpers\ModuleHelper::appointmentsEnabled()): ?>
                                        <div class="col-md-4">
                                            <div class="p-3 border rounded bg-light text-center h-100">
                                                <div class="text-uppercase small fw-bold text-info mb-2">Today's Appts</div>
                                                <div class="h3 mb-0 fw-bold text-gray-800"><?php echo e($today_appointments); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(\App\Helpers\ModuleHelper::servicesEnabled()): ?>
                                        <div class="col-md-4">
                                            <div class="p-3 border rounded bg-light text-center h-100">
                                                <div class="text-uppercase small fw-bold text-primary mb-2">Total Services</div>
                                                <div class="h3 mb-0 fw-bold text-gray-800"><?php echo e($total_services); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(\App\Helpers\ModuleHelper::customersEnabled()): ?>
                                        <div class="col-md-4">
                                            <div class="p-3 border rounded bg-light text-center h-100">
                                                <div class="text-uppercase small fw-bold text-warning mb-2">Total Customers</div>
                                                <div class="h3 mb-0 fw-bold text-gray-800"><?php echo e($total_customers); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-xl-4">
                        <div class="card-modern h-100">
                            <div class="widget-header widget-header-info">
                                <i class="fas fa-bolt me-2"></i>Quick Actions
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('salon.manage_settings')): ?>
                                        <a href="<?php echo e(route('admin.salon-settings.index')); ?>"
                                            class="list-group-item list-group-item-action px-4 py-3 border-0">
                                            <i class="fas fa-cog me-3 text-muted"></i>Salon Settings
                                        </a>
                                    <?php endif; ?>
                                    <?php if(\App\Helpers\ModuleHelper::staffEnabled()): ?>
                                        <a href="<?php echo e(route('admin.staff.index')); ?>"
                                            class="list-group-item list-group-item-action px-4 py-3 border-0">
                                            <i class="fas fa-user-tie me-3 text-muted"></i>Add Staff
                                        </a>
                                    <?php endif; ?>
                                    <?php if(\App\Helpers\ModuleHelper::customersEnabled()): ?>
                                        <a href="<?php echo e(route('admin.customers.create')); ?>"
                                            class="list-group-item list-group-item-action px-4 py-3 border-0">
                                            <i class="fas fa-user-plus me-3 text-muted"></i>Add Customer
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Usage -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="card-modern">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('salon.manage_settings')): ?>
                                <div class="widget-header widget-header-primary">
                                    <i class="fas fa-crown me-2"></i>Subscription Usage (<?php echo e($subscription['name'] ?? 'N/A'); ?>)
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <?php if(isset($subscription['limits'])): ?>
                                            <?php
                                                $featureMapping = [
                                                    'staff' => 'Staff Management',
                                                    'branches' => 'Multi-Branch Support',
                                                    'customers' => 'Customer Management',
                                                    'services' => 'Service Management',
                                                    'products' => 'Inventory Management',
                                                    'bookings' => 'Booking System',
                                                    'guest_bookings' => 'Booking System',
                                                    'memberships' => 'Memberships',
                                                    'packages' => 'Packages',
                                                ];
                                                $planFeatures = $subscription['features'] ?? [];
                                                // Ensure features is always an array (defensive check)
                                                if (!is_array($planFeatures)) {
                                                    $planFeatures = json_decode($planFeatures, true) ?? [];
                                                }
                                                $hasAllFeatures = in_array('All Features', $planFeatures) || in_array('All Premium Features', $planFeatures);
                                            ?>
                                            <?php $__currentLoopData = $subscription['limits']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $limit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $shouldShow = true;

                                                    // 1. Check Global Module Settings
                                                    if ($key == 'staff' && !\App\Helpers\ModuleHelper::staffEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'branches' && !\App\Helpers\ModuleHelper::branchesEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'customers' && !\App\Helpers\ModuleHelper::customersEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'services' && !\App\Helpers\ModuleHelper::servicesEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'products' && !\App\Helpers\ModuleHelper::inventoryEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'bookings' && !\App\Helpers\ModuleHelper::appointmentsEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'guest_bookings' && !\App\Helpers\ModuleHelper::appointmentsEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'memberships' && !\App\Helpers\ModuleHelper::membershipsEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'packages' && !\App\Helpers\ModuleHelper::packagesEnabled())
                                                        $shouldShow = false;

                                                    // 2. Check Plan Features
                                                    if ($shouldShow && !$hasAllFeatures && isset($featureMapping[$key])) {
                                                        if (!in_array($featureMapping[$key], $planFeatures)) {
                                                            $shouldShow = false;
                                                        }
                                                    }
                                                ?>

                                                <?php if($shouldShow): ?>
                                                    <div class="col-md-3 col-sm-6">
                                                        <div class="border rounded p-3 h-100 bg-light">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <div class="text-xs font-weight-bold text-uppercase text-muted">
                                                                    <?php echo e($key == 'guest_bookings' ? 'Guest Bookings' : ucfirst($key)); ?>

                                                                </div>
                                                                <?php
                                                                    $icon = match ($key) {
                                                                        'staff' => 'users',
                                                                        'branches' => 'building',
                                                                        'customers' => 'user-friends',
                                                                        'services' => 'cut',
                                                                        'products' => 'box',
                                                                        'bookings' => 'calendar-alt',
                                                                        'guest_bookings' => 'user-clock',
                                                                        'memberships' => 'id-card',
                                                                        'packages' => 'cubes',
                                                                        default => 'check-circle'
                                                                    };
                                                                ?>
                                                                <i class="fas fa-<?php echo e($icon); ?> text-gray-300"></i>
                                                            </div>
                                                            <div class="h5 mb-2 fw-bold text-gray-800">
                                                                <?php echo e($limit['current']); ?> <span class="text-muted small fw-normal">/
                                                                    <?php echo e($limit['is_unlimited'] ? 'Unlimited' : $limit['max']); ?></span>
                                                            </div>
                                                            <?php if(!$limit['is_unlimited']): ?>
                                                                <?php
                                                                    $percentage = $limit['max'] > 0 ? ($limit['current'] / $limit['max']) * 100 : 0;
                                                                    $color = $percentage >= 90 ? 'danger' : ($percentage >= 75 ? 'warning' : 'success');
                                                                ?>
                                                                <div class="progress progress-modern">
                                                                    <div class="progress-bar bg-<?php echo e($color); ?>" role="progressbar"
                                                                        style="width: <?php echo e($percentage); ?>%" aria-valuenow="<?php echo e($limit['current']); ?>"
                                                                        aria-valuemin="0" aria-valuemax="<?php echo e($limit['max']); ?>"></div>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="progress progress-modern">
                                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%"
                                                                        aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bookings.view')): ?>
        <div class="row g-3 mb-4">
            <!-- Daily Overview -->
            <div class="col-xl-8">
                <div class="card-modern h-100">
                    <div class="widget-header widget-header-info">
                        <i class="fas fa-calendar-day me-2"></i>Daily Overview
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center h-100">
                                    <div class="text-uppercase small fw-bold text-info mb-2">
                                        <?php echo e($dateRange == 'today' ? "Today's" : "Selected Period"); ?> Tasks
                                    </div>
                                    <div class="h3 mb-0 fw-bold text-gray-800"><?php echo e($today_tasks); ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center h-100">
                                    <div class="text-uppercase small fw-bold text-success mb-2">Completed</div>
                                    <div class="h3 mb-0 fw-bold text-gray-800"><?php echo e($completed_tasks); ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center h-100">
                                    <div class="text-uppercase small fw-bold text-warning mb-2">Pending</div>
                                    <div class="h3 mb-0 fw-bold text-gray-800"><?php echo e($pending_tasks); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-xl-4">
                <div class="card-modern h-100">
                    <div class="widget-header widget-header-info">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="<?php echo e(route('admin.bookings.index')); ?>"
                                class="list-group-item list-group-item-action px-4 py-3 border-0">
                                <i class="fas fa-tasks me-3 text-muted"></i>Manage Tasks
                            </a>
                            <?php if(\App\Helpers\ModuleHelper::staffEnabled()): ?>
                                <a href="<?php echo e(route('admin.staff.index')); ?>"
                                    class="list-group-item list-group-item-action px-4 py-3 border-0">
                                    <i class="fas fa-user-tie me-3 text-muted"></i>Add Staff
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
    <?php endif; ?>

    </div>

    <?php echo $__env->make('admin.bookings.partials.booking_details_modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Assign Staff Modal -->
    <div class="modal fade" id="assignStaffModal" tabindex="-1" aria-labelledby="assignStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignStaffModalLabel">Assign Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignStaffForm">
                        <input type="hidden" id="assignBookingId" name="booking_id">

                        <div class="mb-3">
                            <label for="assignStaffId" class="form-label">Select Staff</label>
                            <select class="form-select" id="assignStaffId" name="staff_id" required>
                                <option value="">Choose staff member...</option>
                                <?php $__currentLoopData = \App\Models\User::role('employee')->where('salon_id', auth()->user()->salon_id)->where('status', 'active')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($staff->id); ?>"><?php echo e($staff->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="assignDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="assignDate" name="date" required
                                min="<?php echo e(date('Y-m-d')); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="assignTime" class="form-label">Time</label>
                            <select class="form-select" id="assignTime" name="time" required disabled>
                                <option value="">Select date and staff first...</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmAssignBtn">Confirm Assignment</button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Currency settings
            const currencySymbol = <?php echo json_encode(currency_symbol(), 15, 512) ?>;

            window.setDateRange = function (range) {
                const input = document.getElementById('date_range_input');
                input.value = range;
                if (range !== 'custom') {
                    document.getElementById('dateFilterForm').submit();
                } else {
                    toggleCustomRange();
                }
            }

            window.toggleCustomRange = function () {
                const integrated = document.getElementById('custom-range-integrated');
                integrated.classList.toggle('show');
                document.getElementById('date_range_input').value = 'custom';
            }
            $(document).ready(function () {
                // --- Chart Data Preparation ---

                // Revenue Trend Data
                <?php if(isset($monthly_revenue) && count($monthly_revenue) > 0): ?>
                    const monthlyRevenue = <?php echo json_encode($monthly_revenue, 15, 512) ?>;
                    const revenueLabels = monthlyRevenue.map(m => m.month);
                    const revenueData = monthlyRevenue.map(m => m.revenue);

                    const ctxRevenue = document.getElementById('revenueTrendChart').getContext('2d');
                    new Chart(ctxRevenue, {
                        type: 'line',
                        data: {
                            labels: revenueLabels,
                            datasets: [{
                                label: 'Total Revenue',
                                data: revenueData,
                                fill: true,
                                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                                borderColor: 'rgba(78, 115, 223, 1)',
                                pointRadius: 3,
                                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                                pointBorderColor: 'rgba(78, 115, 223, 1)',
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                                pointHitRadius: 10,
                                pointBorderWidth: 2,
                                tension: 0.3
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            return 'Revenue: ' + currencySymbol + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value) { return currencySymbol + value; }
                                    }
                                }
                            }
                        }
                    });
                <?php endif; ?>

                    // Payment Methods Data
                    <?php if(isset($payment_methods) && count($payment_methods) > 0): ?>
                        const paymentMethods = <?php echo json_encode($payment_methods, 15, 512) ?>;
                        const paymentLabels = paymentMethods.map(p => p.name);
                        const paymentValues = paymentMethods.map(p => p.value);

                        const ctxPayment = document.getElementById('paymentMethodsChart').getContext('2d');
                        new Chart(ctxPayment, {
                            type: 'doughnut',
                            data: {
                                labels: paymentLabels,
                                datasets: [{
                                    data: paymentValues,
                                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'bottom' },
                                    tooltip: {
                                        callbacks: {
                                            label: function (context) {
                                                let label = context.label || '';
                                                if (label) { label += ': '; }
                                                label += currencySymbol + context.parsed.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                                return label;
                                            }
                                        }
                                    }
                                },
                                cutout: '70%',
                            }
                        });
                    <?php endif; ?>

                    // Top Services Data
                    <?php if(isset($top_services) && count($top_services) > 0): ?>
                        const topServices = <?php echo json_encode($top_services, 15, 512) ?>;
                        const serviceLabels = topServices.map(s => s.name);
                        const serviceRevenue = topServices.map(s => s.value);

                        const ctxServices = document.getElementById('topServicesChart').getContext('2d');
                        new Chart(ctxServices, {
                            type: 'bar',
                            data: {
                                labels: serviceLabels,
                                datasets: [{
                                    label: 'Revenue',
                                    data: serviceRevenue,
                                    backgroundColor: '#36b9cc',
                                    borderRadius: 5
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: function (context) {
                                                return 'Revenue: ' + currencySymbol + context.parsed.x.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function (value) { return currencySymbol + value; }
                                        }
                                    }
                                }
                            }
                        });
                    <?php endif; ?>

                    // Staff Performance Data
                    <?php if(isset($staff_performance) && count($staff_performance) > 0): ?>
                        const staffStats = <?php echo json_encode($staff_performance, 15, 512) ?>;
                        const staffLabels = staffStats.map(s => s.name);
                        const staffRevenueData = staffStats.map(s => s.revenue);

                        const ctxStaff = document.getElementById('staffPerformanceChart').getContext('2d');
                        new Chart(ctxStaff, {
                            type: 'bar',
                            data: {
                                labels: staffLabels,
                                datasets: [{
                                    label: 'Revenue Generated',
                                    data: staffRevenueData,
                                    backgroundColor: '#4e73df',
                                    borderRadius: 5,
                                    barPercentage: 0.5
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: function (context) {
                                                return 'Revenue: ' + currencySymbol + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function (value) { return currencySymbol + value; }
                                        }
                                    }
                                }
                            }
                        });
                    <?php endif; ?>
                // --- Assign Staff Functionality ---
                let assignModal = new bootstrap.Modal(document.getElementById('assignStaffModal'));

                // Open Modal
                $(document).on('click', '.assign-btn', function () {
                    const bookingId = $(this).data('id');
                    const bookingDate = $(this).data('date');

                    $('#assignBookingId').val(bookingId);
                    $('#assignStaffId').val('');
                    $('#assignDate').val(bookingDate); // Pre-fill date
                    $('#assignTime').html('<option value="">Select staff first...</option>').prop('disabled', true);
                    assignModal.show();
                });

                // Fetch Available Slots
                function fetchSlots() {
                    const staffId = $('#assignStaffId').val();
                    const date = $('#assignDate').val();

                    if (staffId && date) {
                        $('#assignTime').html('<option value="">Loading...</option>').prop('disabled', true);

                        $.ajax({
                            url: "<?php echo e(route('admin.bookings.available-slots')); ?>",
                            method: 'GET',
                            data: { staff_id: staffId, date: date },
                            success: function (response) {
                                let options = '<option value="">Select time...</option>';
                                if (response.slots && response.slots.length > 0) {
                                    response.slots.forEach(function (slot) {
                                        // Format time for display (e.g., 2023-10-27 14:30:00 -> 14:30)
                                        const time = slot.split(' ')[1].substring(0, 5);
                                        options += `<option value="${slot}">${time}</option>`;
                                    });
                                    $('#assignTime').html(options).prop('disabled', false);
                                } else {
                                    $('#assignTime').html('<option value="">No slots available</option>');
                                }
                            },
                            error: function () {
                                $('#assignTime').html('<option value="">Error loading slots</option>');
                            }
                        });
                    }
                }

                $('#assignStaffId, #assignDate').on('change', fetchSlots);

                // Confirm Assignment
                $('#confirmAssignBtn').click(function () {
                    const bookingId = $('#assignBookingId').val();
                    const staffId = $('#assignStaffId').val();
                    const assignedTime = $('#assignTime').val(); // This is the full datetime string from the slot

                    if (!staffId || !assignedTime) {
                        alert('Please select staff and time.');
                        return;
                    }

                    const btn = $(this);
                    btn.prop('disabled', true).text('Assigning...');

                    $.ajax({
                        url: "<?php echo e(route('admin.bookings.assign', ['booking' => 0])); ?>".replace('/0/assign', '/' + bookingId + '/assign'),
                        method: 'POST',
                        data: {
                            _token: "<?php echo e(csrf_token()); ?>",
                            staff_id: staffId,
                            assigned_time: assignedTime
                        },
                        success: function (response) {
                            if (response.success) {
                                assignModal.hide();
                                // Reload page to show updated status
                                window.location.reload();
                            } else {
                                alert(response.message || 'Failed to assign staff.');
                                btn.prop('disabled', false).text('Confirm Assignment');
                            }
                        },
                        error: function (xhr) {
                            alert(xhr.responseJSON?.message || 'An error occurred.');
                            btn.prop('disabled', false).text('Confirm Assignment');
                        }
                    });
                });

                // Initialize Bootstrap tooltips for group bookings and multi-staff icons
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl, {
                        html: true
                    });
                });

            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\dashboard.blade.php ENDPATH**/ ?>