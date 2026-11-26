<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/saas-dashboard.css')); ?>?v=<?php echo e(file_exists(public_path('css/saas-dashboard.css')) ? filemtime(public_path('css/saas-dashboard.css')) : '1.0'); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-3">
        <!-- Command Center Header -->
        <div class="row align-items-center mb-3 g-3">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-primary text-white rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h1 class="h4 mb-0 text-gray-800 font-weight-bold">Command Center</h1>
                        <p class="text-muted x-small mb-0">Platform oversight, analytics &amp; multi-tenant administration.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <form action="<?php echo e(route('admin.salons.index')); ?>" method="GET" class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden border">
                    <span class="input-group-text bg-white border-0 ps-3"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 ps-1" placeholder="Search salons, owners, license keys, or emails..." value="<?php echo e(request('search')); ?>">
                    <button class="btn btn-primary px-3 rounded-0 fw-semibold" type="submit">Search</button>
                </form>
            </div>
            <div class="col-md-3 text-end">
                <div class="d-flex gap-2 justify-content-end">
                    <button onclick="window.location.reload()" class="btn btn-white btn-xs shadow-sm border px-3 rounded-pill fw-semibold">
                        <i class="fas fa-sync-alt fa-sm text-primary me-1"></i> Refresh
                    </button>
                    <a href="<?php echo e(route('admin.saas.reports.index')); ?>" class="btn btn-primary btn-xs shadow-sm border-0 px-3 rounded-pill fw-semibold">
                        <i class="fas fa-chart-pie fa-sm me-1"></i> Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Navigation Toolbar -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 py-2 px-3 bg-white rounded-3 shadow-sm border">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="text-muted text-uppercase fw-bold x-small me-1"><i class="fas fa-compass me-1 text-primary"></i> Quick Access:</span>
                <a href="<?php echo e(route('admin.plans.index')); ?>" class="btn btn-sm btn-light border py-1 px-3 rounded-pill quick-action-pill text-dark text-decoration-none">
                    <i class="fas fa-tags text-primary me-1"></i> Subscription Plans
                </a>
                <a href="<?php echo e(route('admin.salons.index')); ?>" class="btn btn-sm btn-light border py-1 px-3 rounded-pill quick-action-pill text-dark text-decoration-none">
                    <i class="fas fa-store text-success me-1"></i> Salon Tenants
                </a>
                <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="btn btn-sm btn-light border py-1 px-3 rounded-pill quick-action-pill text-dark text-decoration-none">
                    <i class="fas fa-id-card text-info me-1"></i> Active Subscriptions
                </a>
                <a href="<?php echo e(route('admin.payments.index')); ?>" class="btn btn-sm btn-light border py-1 px-3 rounded-pill quick-action-pill text-dark text-decoration-none">
                    <i class="fas fa-receipt text-amber-accessible me-1"></i> Payment Approvals
                    <?php if(!empty($stats['pending_payments']) && $stats['pending_payments'] > 0): ?>
                        <span class="badge badge-warning-accessible ms-1 rounded-pill" style="font-size: 0.65rem;"><?php echo e($stats['pending_payments']); ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo e(route('admin.system-settings.index')); ?>" class="btn btn-sm btn-light border py-1 px-3 rounded-pill quick-action-pill text-dark text-decoration-none">
                    <i class="fas fa-sliders-h text-secondary me-1"></i> System Settings
                </a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?php echo e(route('admin.salons.create')); ?>" class="btn btn-sm btn-outline-success py-1 px-3 rounded-pill fw-semibold">
                    <i class="fas fa-plus me-1"></i> Add Salon
                </a>
                <a href="<?php echo e(route('admin.plans.create')); ?>" class="btn btn-sm btn-primary py-1 px-3 rounded-pill fw-semibold shadow-sm">
                    <i class="fas fa-plus me-1"></i> New Plan
                </a>
                <span class="badge bg-success-light text-success fw-bold px-2 py-1 rounded-pill x-small">
                    <i class="fas fa-circle fa-xs me-1 text-success"></i> SaaS Live
                </span>
            </div>
        </div>

        <!-- SECTION 1: PRIORITY STRATEGIC METRICS (Always Visible) -->
        <div class="row g-2 g-md-3 mb-3">
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-body p-2 p-md-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase">Monthly Rev</div>
                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Total revenue collected from all subscriptions in the current calendar month."></i>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($stats['total_revenue'], 2)); ?></div>
                        <div class="mt-1 x-small text-success font-weight-bold"><i class="fas fa-arrow-up me-1"></i> 3.5%</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-body p-2 p-md-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="text-xs font-weight-bold text-success text-uppercase">MRR</div>
                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Monthly Recurring Revenue - The predictable total revenue generated by all active subscriptions."></i>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($stats['mrr'] ?? 0, 2)); ?></div>
                        <div class="mt-1 x-small text-muted">Recurring</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-body p-2 p-md-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="text-xs font-weight-bold text-info text-uppercase">ARR</div>
                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Annualized Run Rate - A projection of your annual revenue based on the current MRR (MRR x 12)."></i>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($stats['arr'] ?? 0, 2)); ?></div>
                        <div class="mt-1 x-small text-muted">Annualized</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-body p-2 p-md-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="text-xs font-weight-bold text-amber-accessible text-uppercase">Trial Conv.</div>
                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Percentage of trial users who upgraded to a paid plan within the last 90 days."></i>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($trialConversionRate, 1)); ?>%</div>
                        <div class="mt-1 x-small text-muted">Last 90 Days</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-body p-2 p-md-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="text-xs font-weight-bold text-purple text-uppercase">LTV (Avg)</div>
                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Average Lifetime Value - The total revenue expected from a single salon over its entire relationship with the platform."></i>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($ltv, 2)); ?></div>
                        <div class="mt-1 x-small text-muted">Lifetime Value</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 12px; border-left: 3px solid #4e73df !important;">
                    <div class="card-body p-2 p-md-3 position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase">Forecast</div>
                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Projected total revenue for the current month based on historical trends and current growth."></i>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($stats['forecasted_revenue'] ?? 0, 2)); ?></div>
                        <div class="mt-1 x-small text-primary font-weight-bold"><i class="fas fa-magic me-1"></i> Projected MTD</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: CRITICAL HEALTH & ACTIONS (Always Visible) -->
        <div class="row g-3 mb-3">
            <!-- Pending Approvals -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #d97706 !important;">
                    <div class="card-header bg-transparent border-0 py-2">
                        <h6 class="m-0 font-weight-bold text-amber-accessible small d-flex justify-content-between align-items-center">
                            Pending Approvals
                            <i class="fas fa-info-circle text-muted opacity-50" data-bs-toggle="tooltip" title="Payments waiting for manual verification and approval by a super-admin."></i>
                        </h6>
                    </div>
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['pending_payments']); ?></div>
                            <div class="x-small text-muted"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($stats['pending_amount'], 2)); ?> Pending</div>
                        </div>
                        <a href="<?php echo e(route('admin.payments.index')); ?>?status=pending" class="btn btn-xs btn-warning-accessible">Review</a>
                    </div>
                </div>
            </div>

            <!-- Subscription Health -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 py-2">
                        <h6 class="m-0 font-weight-bold text-gray-800 small d-flex justify-content-between align-items-center">
                            Subscription Health
                            <i class="fas fa-info-circle text-muted opacity-50" data-bs-toggle="tooltip" title="Real-time breakdown of your customer base by subscription status."></i>
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row text-center g-0">
                            <div class="col-4 border-end"><div class="small font-weight-bold text-gray-800"><?php echo e($subscriptionHealth['paid']); ?></div><div class="x-small text-muted">Paid</div></div>
                            <div class="col-4 border-end"><div class="small font-weight-bold text-gray-800"><?php echo e($subscriptionHealth['trial']); ?></div><div class="x-small text-muted">Trial</div></div>
                            <div class="col-4"><div class="small font-weight-bold text-amber-accessible"><?php echo e($subscriptionHealth['expiring_soon']); ?></div><div class="x-small text-muted">Expiring</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Platform Sentiment -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 py-2">
                        <h6 class="m-0 font-weight-bold text-gray-800 small d-flex justify-content-between align-items-center">
                            Platform Sentiment
                            <i class="fas fa-info-circle text-muted opacity-50" data-bs-toggle="tooltip" title="Overall platform satisfaction based on average salon ratings and total reviews."></i>
                        </h6>
                    </div>
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($avgRating, 1)); ?> <i class="fas fa-star text-warning fa-xs"></i></div>
                            <div class="x-small text-muted"><?php echo e($totalReviews); ?> Reviews</div>
                        </div>
                        <i class="fas fa-smile fa-2x text-success opacity-25"></i>
                    </div>
                </div>
            </div>

            <!-- Infrastructure Status -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 py-2">
                        <h6 class="m-0 font-weight-bold text-gray-800 small d-flex justify-content-between align-items-center">
                            Infrastructure
                            <i class="fas fa-info-circle text-muted opacity-50" data-bs-toggle="tooltip" title="Real-time status of the database and system disk usage."></i>
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="x-small text-muted">DB</span>
                            <span class="x-small font-weight-bold text-success">Operational</span>
                        </div>
                        <div class="progress mb-0" style="height: 4px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo e($systemHealth['storage_usage']['percentage']); ?>%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span class="x-small text-muted">Disk Usage</span>
                            <span class="x-small font-weight-bold"><?php echo e($systemHealth['storage_usage']['percentage']); ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: MAIN VISUAL ANALYTICS (Always Visible) -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 py-3 d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-gray-800">
                            Revenue Performance Trend
                            <i class="fas fa-info-circle text-muted ms-2 x-small" data-bs-toggle="tooltip" title="12-month visual trend of your total revenue collection."></i>
                        </h6>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary-light text-primary px-3 py-2">Last 12 Months</span>
                            <a href="<?php echo e(route('admin.saas.reports.revenue')); ?>" class="btn btn-xs btn-light border"><i class="fas fa-expand-alt"></i></a>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div style="height: 250px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 4: THE DATA HUB (Tabbed Detailed Data) -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-header bg-white border-0 p-0">
                        <ul class="nav nav-tabs nav-justified custom-data-tabs" id="dataHubTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active py-3" data-bs-toggle="tab" data-bs-target="#hub-ops">
                                    <i class="fas fa-tasks me-2"></i> Operations
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link py-3" data-bs-toggle="tab" data-bs-target="#hub-market">
                                    <i class="fas fa-chart-line me-2"></i> Market & Performance
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link py-3" data-bs-toggle="tab" data-bs-target="#hub-insights">
                                    <i class="fas fa-lightbulb me-2"></i> Growth & Risk
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link py-3" data-bs-toggle="tab" data-bs-target="#hub-system">
                                    <i class="fas fa-cogs me-2"></i> System & Audit
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0">
                        <div class="tab-content p-4">
                            <!-- TAB 1: OPERATIONS (Registrations, Payments, Expiring) -->
                            <div class="tab-pane fade show active" id="hub-ops">
                                <!-- Operations Summary Row -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <div class="p-3 rounded bg-light border-start border-primary">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase">Daily Booking Volume</div>
                                                <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Total number of appointments booked across all salons in the last 24 hours."></i>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($dailyBookings)); ?></div>
                                            <div class="x-small text-muted">Last 24 hours</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 rounded bg-light border-start border-success">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="text-xs font-weight-bold text-success text-uppercase">Active Salons</div>
                                                <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Number of salons that are currently active and operational on the platform."></i>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e($salonStatusStats['active']); ?></div>
                                            <div class="x-small text-muted">Currently operational</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 rounded bg-light border-start" style="border-left: 4px solid #d97706 !important;">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="text-xs font-weight-bold text-amber-accessible text-uppercase">Pending Approvals</div>
                                                <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Payments waiting for manual verification and approval."></i>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['pending_payments']); ?></div>
                                            <div class="x-small text-muted">Waiting for review</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 rounded bg-white border shadow-sm d-flex align-items-center justify-content-between">
                                            <div class="text-xs font-weight-bold text-gray-800 text-uppercase">Quick Actions</div>
                                            <div class="d-flex gap-1">
                                                <a href="<?php echo e(route('admin.salons.create')); ?>" class="btn btn-xs btn-primary" title="Add Salon"><i class="fas fa-plus"></i></a>
                                                <a href="<?php echo e(route('admin.payments.index')); ?>?status=pending" class="btn btn-xs btn-warning-accessible" title="Approve"><i class="fas fa-check"></i></a>
                                                <a href="<?php echo e(route('admin.system-settings.index')); ?>" class="btn btn-xs btn-light border" title="Settings"><i class="fas fa-cog"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-lg-3">
                                        <h6 class="font-weight-bold text-gray-800 mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-user-plus text-primary me-2"></i> Recent Registrations</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="The latest salons to join the platform."></i>
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle x-small">
                                                <thead class="bg-light"><tr><th>Salon</th><th>Date</th><th class="text-end">Status</th></tr></thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $recent_salons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="font-weight-bold"><?php echo e($salon->name); ?></td>
                                                            <td class="text-muted"><?php echo e($salon->created_at->format('M d')); ?></td>
                                                            <td class="text-end"><span class="badge bg-<?php echo e($salon->is_active ? 'success' : 'danger'); ?>-light text-<?php echo e($salon->is_active ? 'success' : 'danger'); ?> px-2"><?php echo e($salon->is_active ? 'Active' : 'Pending'); ?></span></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <h6 class="font-weight-bold text-gray-800 mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-credit-card text-success me-2"></i> Recent Payments</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="The most recent subscription payments received."></i>
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle x-small">
                                                <thead class="bg-light"><tr><th>Salon</th><th>Amount</th><th class="text-end">Status</th></tr></thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $recent_payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="font-weight-bold"><?php echo e($payment->salon->name); ?></td>
                                                            <td class="font-weight-bold"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($payment->amount, 2)); ?></td>
                                                            <td class="text-end"><span class="badge bg-<?php echo e($payment->status == 'completed' ? 'success' : 'warning'); ?>-light text-<?php echo e($payment->status == 'completed' ? 'success' : 'warning'); ?> px-2"><?php echo e(ucfirst($payment->status)); ?></span></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <h6 class="font-weight-bold text-gray-800 mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-hourglass-half text-amber-accessible me-2"></i> Expiring Soon</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Subscriptions ending within the next 30 days that require renewal attention."></i>
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle x-small">
                                                <thead class="bg-light"><tr><th>Salon</th><th>Plan</th><th class="text-end">Expires</th></tr></thead>
                                                <tbody>
                                                    <?php $__empty_1 = true; $__currentLoopData = $expiring_subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <tr>
                                                            <td class="font-weight-bold"><?php echo e($sub->salon->name); ?></td>
                                                            <td><span class="badge bg-info-light text-info"><?php echo e($sub->plan->name); ?></span></td>
                                                            <td class="text-end text-amber-accessible font-weight-bold"><?php echo e($sub->ends_at->diffForHumans()); ?></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <tr><td colspan="3" class="text-center py-4 text-muted">No subscriptions expiring soon.</td></tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <h6 class="font-weight-bold text-gray-800 mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-star text-info me-2"></i> Top Active Salons (7d)</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Salons with the highest booking activity over the last 7 days."></i>
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle x-small">
                                                <thead class="bg-light"><tr><th>Salon</th><th class="text-end">Bookings</th></tr></thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $topActiveSalonsWeekly; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="font-weight-bold"><?php echo e($salon->name); ?></td>
                                                            <td class="text-end font-weight-bold text-info"><?php echo e(number_format($salon->booking_count)); ?></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: MARKET & PERFORMANCE (Plan Distribution, Revenue by Plan, Leaders) -->
                            <div class="tab-pane fade" id="hub-market">
                                <!-- Market Summary Row -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <div class="p-3 rounded bg-light border-start border-primary">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase">ARPU (Avg Revenue Per User)</div>
                                                <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Average Revenue Per User - The average monthly revenue generated by each active salon."></i>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($arpu, 2)); ?></div>
                                            <div class="x-small text-muted">Based on active salons</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded bg-light border-start border-success">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="text-xs font-weight-bold text-success text-uppercase">MoM Revenue Growth</div>
                                                <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Percentage change in total revenue compared to the previous month."></i>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($momGrowth, 1)); ?>%</div>
                                            <div class="x-small text-<?php echo e($momGrowth >= 0 ? 'success' : 'danger'); ?> font-weight-bold">
                                                <i class="fas fa-arrow-<?php echo e($momGrowth >= 0 ? 'up' : 'down'); ?> me-1"></i> 
                                                <?php echo e($momGrowth >= 0 ? 'Increase' : 'Decrease'); ?> vs last month
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded bg-light border-start" style="border-left: 4px solid #d97706 !important;">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="text-xs font-weight-bold text-amber-accessible text-uppercase">Trending Plan</div>
                                                <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="The subscription plan with the highest number of new sign-ups in the last 30 days."></i>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e($topGrowingPlans->first()->name ?? 'N/A'); ?></div>
                                            <div class="x-small text-muted">Most new subs (30d)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-lg-4">
                                        <h6 class="font-weight-bold text-gray-800 mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-chart-pie text-info me-2"></i> Plan Distribution</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Visual breakdown of salons across different subscription tiers."></i>
                                        </h6>
                                        <div style="height: 200px;"><canvas id="planChart"></canvas></div>
                                        <div class="mt-3 text-center x-small" id="planLegend"></div>
                                    </div>
                                    <div class="col-lg-4">
                                        <h6 class="font-weight-bold text-gray-800 mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-money-bill-wave text-success me-2"></i> Revenue by Plan</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Total revenue generated by each specific subscription plan."></i>
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle x-small">
                                                <thead class="bg-light"><tr><th>Plan</th><th class="text-end">Total Revenue</th></tr></thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $revenueByPlan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="font-weight-bold"><?php echo e($plan->name); ?></td>
                                                            <td class="text-end font-weight-bold text-primary"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($plan->total_revenue, 2)); ?></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <h6 class="font-weight-bold text-gray-800 mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-chart-line text-purple me-2"></i> Top Growing Plans (30d)</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Plans showing the most significant growth in new subscribers."></i>
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle x-small">
                                                <thead class="bg-light"><tr><th>Plan</th><th class="text-end">New Subs</th></tr></thead>
                                                <tbody>
                                                    <?php $__empty_1 = true; $__currentLoopData = $topGrowingPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <tr>
                                                            <td class="font-weight-bold"><?php echo e($plan->name); ?></td>
                                                            <td class="text-end font-weight-bold text-purple"><?php echo e($plan->new_subs); ?></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <tr><td colspan="2" class="text-center py-3 text-muted">No new subscriptions.</td></tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: INSIGHTS (Growth & Risk) -->
                            <div class="tab-pane fade" id="hub-insights">
                                <!-- Growth Summary Row -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <div class="p-3 rounded bg-light border-start border-success">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="text-xs font-weight-bold text-success text-uppercase">MoM Salon Growth</div>
                                                <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Percentage increase in new salon registrations compared to last month."></i>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($momSalonGrowth, 1)); ?>%</div>
                                            <div class="x-small text-muted"><?php echo e($thisMonthSalons); ?> New salons this month</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded bg-light border-start" style="border-left: 4px solid #d97706 !important;">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="text-xs font-weight-bold text-amber-accessible text-uppercase">Expiring Trials (7d)</div>
                                                <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Trial subscriptions ending in the next 7 days - high priority for conversion."></i>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e($expiringTrials->count()); ?></div>
                                            <div class="x-small text-muted">Conversion opportunities</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded bg-light border-start border-danger">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="text-xs font-weight-bold text-danger text-uppercase">Low Rating Salons</div>
                                                <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Salons with an average rating below 3.0 that may need support or intervention."></i>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e($lowRatingSalons->count()); ?></div>
                                            <div class="x-small text-muted">Requires attention</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-lg-4">
                                        <h6 class="font-weight-bold text-success mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-bolt me-2"></i> Power Users (Top Growth)</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Salons showing the highest growth in booking volume month-over-month."></i>
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle x-small">
                                                <thead class="bg-light"><tr><th>Salon</th><th>Bookings (30d)</th><th class="text-end">Action</th></tr></thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $powerUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="font-weight-bold text-success"><?php echo e($salon->name); ?></td>
                                                            <td><?php echo e(number_format($salon->booking_count)); ?></td>
                                                            <td class="text-end"><a href="<?php echo e(route('admin.salons.show', $salon)); ?>" class="btn btn-xs btn-light rounded-circle"><i class="fas fa-chevron-right"></i></a></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <h6 class="font-weight-bold text-amber-accessible mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-hourglass-half me-2"></i> Expiring Trials</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Detailed list of salons whose trials are ending soon."></i>
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle x-small">
                                                <thead class="bg-light"><tr><th>Salon</th><th>Ends In</th><th class="text-end">Action</th></tr></thead>
                                                <tbody>
                                                    <?php $__empty_1 = true; $__currentLoopData = $expiringTrials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <tr>
                                                            <td class="font-weight-bold"><?php echo e($trial->salon->name); ?></td>
                                                            <td class="text-amber-accessible font-weight-bold"><?php echo e($trial->trial_ends_at->diffForHumans()); ?></td>
                                                            <td class="text-end"><a href="<?php echo e(route('admin.salons.show', $trial->salon ?? $trial->salon_id)); ?>" class="btn btn-xs btn-warning-accessible">Convert</a></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <tr><td colspan="3" class="text-center py-3 text-muted">No trials expiring soon.</td></tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <h6 class="font-weight-bold text-danger mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-exclamation-triangle me-2"></i> At-Risk & Low Rating</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Salons flagged for inactivity (7 days) or poor customer feedback."></i>
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle x-small">
                                                <thead class="bg-light"><tr><th>Salon</th><th>Issue</th><th class="text-end">Action</th></tr></thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $atRiskSalons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="font-weight-bold text-danger"><?php echo e($salon->name); ?></td>
                                                            <td><span class="badge bg-danger-light text-danger">Inactive 7d</span></td>
                                                            <td class="text-end"><a href="<?php echo e(route('admin.salons.show', $salon)); ?>" class="btn btn-xs btn-light rounded-circle"><i class="fas fa-chevron-right"></i></a></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $__currentLoopData = $lowRatingSalons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="font-weight-bold text-danger"><?php echo e($salon->name); ?></td>
                                                            <td><span class="badge badge-warning-accessible">Low Rating</span></td>
                                                            <td class="text-end"><a href="<?php echo e(route('admin.salons.show', $salon)); ?>" class="btn btn-xs btn-light rounded-circle"><i class="fas fa-chevron-right"></i></a></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: SYSTEM & AUDIT (Deep Tech Infrastructure) -->
                            <div class="tab-pane fade" id="hub-system">
                                <!-- System Health & Database Safety Banner -->
                                <div class="p-3 mb-4 rounded-3 border bg-white shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="saas-avatar saas-avatar-green" style="width: 44px; height: 44px;">
                                            <i class="fas fa-heartbeat fa-lg"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="mb-0 fw-bold text-dark">System Health &amp; Runtime Database Safety</h6>
                                                <span class="chip-pill chip-pill-success"><i class="fas fa-check-circle"></i> Standby Replica Active</span>
                                            </div>
                                            <p class="text-muted x-small mb-0">Total DB Size: <strong><?php echo e(number_format($dbPerformance['size_mb'] ?? 0, 2)); ?> MB</strong> &bull; Log File: <strong><?php echo e($logStats['size'] ?? 0); ?> MB</strong> &bull; Total Tables: <strong><?php echo e($systemHealth['storage_usage']['tables'] ?? 68); ?></strong></p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <form action="<?php echo e(route('admin.system-health.backup.create')); ?>" method="POST" class="d-inline m-0">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                                <i class="fas fa-database me-1"></i> Create DB Snapshot Now
                                            </button>
                                        </form>
                                        <a href="<?php echo e(route('admin.system-health.index')); ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm">
                                            <i class="fas fa-chart-line me-1"></i> Dedicated System Health Page &rarr;
                                        </a>
                                    </div>
                                </div>

                                <!-- Infrastructure Health Row -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <div class="p-3 rounded bg-light border-start border-primary">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase">Queue Health</div>
                                                <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Status of background tasks. High 'Failed' counts may indicate system issues."></i>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e($queueStats['pending']); ?> Pending / <?php echo e($queueStats['failed']); ?> Failed</div>
                                            <div class="x-small text-<?php echo e($queueStats['failed'] > 0 ? 'danger' : 'success'); ?> font-weight-bold">
                                                <i class="fas fa-<?php echo e($queueStats['failed'] > 0 ? 'exclamation-circle' : 'check-circle'); ?> me-1"></i>
                                                <?php echo e($queueStats['failed'] > 0 ? 'Attention Required' : 'System Healthy'); ?>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 rounded bg-light border-start border-info">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="text-xs font-weight-bold text-info text-uppercase">Log Monitoring</div>
                                                <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Current size of system logs and count of errors/exceptions in the log file."></i>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e($logStats['size']); ?> MB</div>
                                            <div class="x-small text-<?php echo e($logStats['errors'] > 0 ? 'warning' : 'muted'); ?> font-weight-bold">
                                                <i class="fas fa-bug me-1"></i> <?php echo e($logStats['errors']); ?> Errors detected in latest log
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 rounded bg-light border-start border-success">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="text-xs font-weight-bold text-success text-uppercase">DB Performance</div>
                                                <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Real-time database connection count and total system uptime."></i>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e($dbPerformance['connections'] ?? 0); ?> Connections (<?php echo e(number_format($dbPerformance['size_mb'] ?? 0, 2)); ?> MB)</div>
                                            <div class="x-small text-muted">Uptime: <?php echo e(round(($dbPerformance['uptime'] ?? 0) / 3600, 1)); ?> Hours</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 rounded bg-light border-start" style="border-left: 4px solid #d97706 !important;">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="text-xs font-weight-bold text-amber-accessible text-uppercase">Active Sessions</div>
                                                <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Number of users (admins, staff, customers) active on the platform in the last 2 hours."></i>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($activeSessionsCount ?? 0)); ?></div>
                                            <div class="x-small text-muted">Last 2 hours</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-lg-4">
                                        <h6 class="font-weight-bold text-gray-800 mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-hdd text-info me-2"></i> Storage Breakdown (MB)</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Detailed breakdown of disk usage by uploads, logs, and system cache."></i>
                                        </h6>
                                        <div class="table-responsive mb-4">
                                            <table class="table table-hover align-middle x-small">
                                                <thead class="bg-light"><tr><th>Directory</th><th class="text-end">Size</th></tr></thead>
                                                <tbody>
                                                    <tr><td><i class="fas fa-upload text-muted me-2"></i> Public Uploads</td><td class="text-end font-weight-bold"><?php echo e($storageBreakdown['uploads'] ?? 0); ?> MB</td></tr>
                                                    <tr><td><i class="fas fa-file-alt text-muted me-2"></i> System Logs</td><td class="text-end font-weight-bold"><?php echo e($storageBreakdown['logs'] ?? 0); ?> MB</td></tr>
                                                    <tr><td><i class="fas fa-bolt text-muted me-2"></i> Framework Cache</td><td class="text-end font-weight-bold"><?php echo e($storageBreakdown['cache'] ?? 0); ?> MB</td></tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <h6 class="font-weight-bold text-gray-800 mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-database text-info me-2"></i> Largest Tables</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Top 5 database tables by size - useful for identifying data growth patterns."></i>
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle x-small">
                                                <thead class="bg-light"><tr><th>Table Name</th><th class="text-end">Size (MB)</th></tr></thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $dbTableSizes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="font-weight-bold"><?php echo e($table->name ?? 'N/A'); ?></td>
                                                            <td class="text-end font-weight-bold text-info"><?php echo e(number_format($table->size_mb ?? $table->size ?? 0, 2)); ?></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <h6 class="font-weight-bold text-gray-800 mt-4 mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-server me-2"></i> Environment Details</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Core system versions and total database queries executed."></i>
                                        </h6>
                                        <div class="list-group list-group-flush x-small">
                                            <div class="list-group-item d-flex justify-content-between px-0"><span>PHP Version</span><span class="font-weight-bold"><?php echo e($systemHealth['php_version'] ?? PHP_VERSION); ?></span></div>
                                            <div class="list-group-item d-flex justify-content-between px-0"><span>Laravel Version</span><span class="font-weight-bold"><?php echo e($systemHealth['laravel_version'] ?? app()->version()); ?></span></div>
                                            <div class="list-group-item d-flex justify-content-between px-0"><span>Total Queries</span><span class="font-weight-bold"><?php echo e(number_format($dbPerformance['queries'] ?? 0)); ?></span></div>
                                        </div>
                                        <form action="<?php echo e(route('admin.system-settings.clear-cache')); ?>" method="POST" class="mt-4">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-danger btn-sm w-100 shadow-sm"><i class="fas fa-broom me-2"></i> Flush System Cache</button>
                                        </form>
                                    </div>
                                    <div class="col-lg-8">
                                        <h6 class="font-weight-bold text-gray-800 mb-3 d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-history me-2"></i> System Audit Trail</span>
                                            <i class="fas fa-info-circle text-muted x-small" data-bs-toggle="tooltip" title="Chronological log of all administrative actions performed on the platform."></i>
                                        </h6>
                                        <div class="timeline p-2">
                                            <?php $__empty_1 = true; $__currentLoopData = $activityFeed ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <div class="timeline-item pb-3 position-relative">
                                                    <div class="timeline-marker bg-primary"></div>
                                                    <div class="timeline-content ps-4">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0 x-small font-weight-bold text-gray-800"><?php echo e($log->action); ?></h6>
                                                            <span class="text-muted x-small"><?php echo e($log->created_at->diffForHumans()); ?></span>
                                                        </div>
                                                        <div class="x-small text-muted">By <?php echo e($log->admin->name ?? 'System'); ?> • <?php echo e($log->salon->name ?? 'Global'); ?></div>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <div class="text-center py-4 text-muted x-small">No recent audit logs found.</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            window.saasDashboardConfig = {
                revenueTrend: <?php echo json_encode($revenueTrend ?? ['labels' => [], 'data' => []], 512) ?>,
                planDistribution: <?php echo json_encode($planDistribution ?? ['labels' => [], 'data' => [], 'colors' => []]) ?>
            };
        </script>
        <script src="<?php echo e(asset('js/saas-dashboard.js')); ?>"></script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ketan\projects\barber\resources\views/super-admin/dashboard.blade.php ENDPATH**/ ?>