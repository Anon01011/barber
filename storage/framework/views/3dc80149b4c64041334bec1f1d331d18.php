<?php $__env->startPush('styles'); ?>
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.3);
            --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --orange-gradient: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%);
            --blue-gradient: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
            --danger-gradient: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }

        .stat-card-premium {
            border: none;
            border-radius: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            color: white;
            position: relative;
            min-height: 140px;
        }

        .stat-card-premium:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .stat-card-premium .card-body {
            position: relative;
            z-index: 2;
            padding: 1.5rem;
        }

        .stat-card-premium .bg-icon {
            position: absolute;
            right: -15px;
            bottom: -15px;
            font-size: 5.5rem;
            opacity: 0.2;
            z-index: 1;
            transform: rotate(-15deg);
            color: white;
        }

        .bg-primary-grad {
            background: var(--primary-gradient);
        }

        .bg-success-grad {
            background: var(--success-gradient);
        }

        .bg-orange-grad {
            background: var(--orange-gradient);
        }

        .bg-blue-grad {
            background: var(--blue-gradient);
        }

        .bg-danger-grad {
            background: var(--danger-gradient);
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--premium-shadow);
            border-radius: 20px;
        }

        .filter-card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            padding: 0.6rem 1rem;
            background-color: #f8f9fa;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.1);
            border-color: #667eea;
            background-color: #fff;
        }

        .btn-premium {
            background: var(--primary-gradient);
            border: none;
            color: white;
            border-radius: 12px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-premium:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .table-premium thead th {
            background-color: #f8f9fc;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #4e73df;
            border: none;
            padding: 1rem;
        }

        .badge-premium {
            padding: 0.5em 1em;
            border-radius: 30px;
            font-weight: 600;
        }

        .avatar-xs {
            width: 32px;
            height: 32px;
            line-height: 32px;
            font-size: 0.8rem;
        }

        .quick-filter-btn {
            border-radius: 30px;
            padding: 0.4rem 1.2rem;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            border: 1px solid #e0e0e0;
            background: white;
            color: #666;
        }

        .quick-filter-btn:hover,
        .quick-filter-btn.active {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }

        .sticky-totals {
            position: sticky;
            bottom: 0;
            background: #f8f9fc;
            font-weight: bold;
            z-index: 10;
            border-top: 2px solid #e3e6f0;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-calendar-check text-primary me-2"></i>Appointments Report
                <?php if(isset($branch) && $branch): ?>
                    <span class="badge bg-primary-soft text-primary ms-2"
                        style="font-size: 0.4em; vertical-align: middle; border: 1px solid #4e73df;"><?php echo e($branch->name); ?></span>
                <?php endif; ?>
            </h1>
            <div class="d-flex gap-2">
                <button class="btn btn-success btn-sm shadow-sm" id="exportAppointmentsBtn">
                    <i class="fas fa-download me-2"></i>Export Report
                </button>
                <a href="<?php echo e(route('admin.reports.index')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Quick Filters -->
        <!-- <div class="d-flex flex-wrap gap-2 mb-4">
                    <?php
                        $today = date('Y-m-d');
                        $yesterday = date('Y-m-d', strtotime('-1 day'));
                        $thisMonthStart = date('Y-m-01');
                        $lastMonthStart = date('Y-m-01', strtotime('-1 month'));
                        $lastMonthEnd = date('Y-m-t', strtotime('-1 month'));
                    ?>
                    <button class="quick-filter-btn <?php echo e($startDate == $today && $endDate == $today ? 'active' : ''); ?>" 
                        onclick="setDates('<?php echo e($today); ?>', '<?php echo e($today); ?>')">Today</button>
                    <button class="quick-filter-btn <?php echo e($startDate == $yesterday && $endDate == $yesterday ? 'active' : ''); ?>" 
                        onclick="setDates('<?php echo e($yesterday); ?>', '<?php echo e($yesterday); ?>')">Yesterday</button>
                    <button class="quick-filter-btn <?php echo e($startDate == $thisMonthStart && $endDate == date('Y-m-t') ? 'active' : ''); ?>" 
                        onclick="setDates('<?php echo e($thisMonthStart); ?>', '<?php echo e(date('Y-m-t')); ?>')">This Month</button>
                    <button class="quick-filter-btn <?php echo e($startDate == $lastMonthStart && $endDate == $lastMonthEnd ? 'active' : ''); ?>" 
                        onclick="setDates('<?php echo e($lastMonthStart); ?>', '<?php echo e($lastMonthEnd); ?>')">Last Month</button>
                </div> -->

        <!-- Date & Advanced Filters -->
        <!-- Date & Advanced Filters -->
        <div class="card glass-card mb-4">
            <div class="card-body p-4">
                <form method="GET" action="<?php echo e(route('admin.reports.appointments')); ?>" id="filterForm" class="row g-3 align-items-end">
                    <!-- Date Period -->
                    <div class="col-xl-3 col-md-6">
                        <label class="form-label small fw-bold text-uppercase text-muted mb-1">Date Period</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted border-end-0"><i class="far fa-calendar-alt"></i></span>
                            <input type="date" class="form-control border-start-0 ps-0" id="start_date" name="start_date" value="<?php echo e($startDate); ?>" placeholder="From">
                            <span class="input-group-text bg-white text-muted border-0">to</span>
                            <input type="date" class="form-control border-start-0 ps-0" id="end_date" name="end_date" value="<?php echo e($endDate); ?>" placeholder="To">
                        </div>
                    </div>

                    <!-- Primary Filters -->
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <label for="branch_id" class="form-label small fw-bold text-uppercase text-muted mb-1">Branch</label>
                        <select class="form-select" id="branch_id" name="branch_id">
                            <option value="">All Branches</option>
                            <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($b->id); ?>" <?php echo e($branchId == $b->id ? 'selected' : ''); ?>><?php echo e($b->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <label for="staff_id" class="form-label small fw-bold text-uppercase text-muted mb-1">Staff</label>
                        <select class="form-select" id="staff_id" name="staff_id">
                            <option value="">All Staff</option>
                            <?php $__currentLoopData = $allStaff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($staff->id); ?>" <?php echo e($staffId == $staff->id ? 'selected' : ''); ?>><?php echo e($staff->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <label for="status" class="form-label small fw-bold text-uppercase text-muted mb-1">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="completed" <?php echo e($statusFilter == 'completed' ? 'selected' : ''); ?>>Completed</option>
                            <option value="confirmed" <?php echo e($statusFilter == 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                            <option value="pending" <?php echo e($statusFilter == 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="cancelled" <?php echo e($statusFilter == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                            <option value="no_show" <?php echo e($statusFilter == 'no_show' ? 'selected' : ''); ?>>No Show</option>
                        </select>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-premium flex-grow-1 shadow-sm">
                                <i class="fas fa-filter me-2"></i>Apply
                            </button>
                            <button type="button" class="btn btn-light border shadow-sm" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="false" title="Advanced Filters">
                                <i class="fas fa-sliders-h text-primary"></i>
                            </button>
                            <a href="<?php echo e(route('admin.reports.appointments')); ?>" class="btn btn-light border shadow-sm" title="Reset Filters">
                                <i class="fas fa-undo text-secondary"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Advanced Filters Collapsed -->
                     <div class="collapse w-100 mt-2" id="advancedFilters">
                        <div class="p-3 bg-light rounded-4 border mt-2">
                            <div class="row g-3">
                                 <div class="col-md-3">
                                    <label for="service_id" class="form-label small fw-bold text-uppercase text-muted mb-1">Service</label>
                                    <select class="form-select" id="service_id" name="service_id">
                                        <option value="">All Services</option>
                                        <?php $__currentLoopData = $allServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($service->id); ?>" <?php echo e($serviceId == $service->id ? 'selected' : ''); ?>><?php echo e($service->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                 <div class="col-md-3">
                                    <label for="customer_id" class="form-label small fw-bold text-uppercase text-muted mb-1">Customer</label>
                                    <select class="form-select" id="customer_id" name="customer_id">
                                        <option value="">All Customers</option>
                                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($c->id); ?>" <?php echo e($customerId == $c->id ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="payment_status" class="form-label small fw-bold text-uppercase text-muted mb-1">Payment Status</label>
                                    <select class="form-select" id="payment_status" name="payment_status">
                                        <option value="">All Payment Status</option>
                                        <option value="paid" <?php echo e($paymentStatusFilter == 'paid' ? 'selected' : ''); ?>>Paid</option>
                                        <option value="unpaid" <?php echo e($paymentStatusFilter == 'unpaid' ? 'selected' : ''); ?>>Unpaid</option>
                                        <option value="partial" <?php echo e($paymentStatusFilter == 'partial' ? 'selected' : ''); ?>>Partial</option>
                                        <option value="refunded" <?php echo e($paymentStatusFilter == 'refunded' ? 'selected' : ''); ?>>Refunded</option>
                                    </select>
                                </div>
                                 <div class="col-md-3">
                                    <label for="payment_method" class="form-label small fw-bold text-uppercase text-muted mb-1">Payment Method</label>
                                    <select class="form-select" id="payment_method" name="payment_method">
                                        <option value="">All Methods</option>
                                        <option value="cash" <?php echo e(request('payment_method') == 'cash' ? 'selected' : ''); ?>>Cash</option>
                                        <option value="card" <?php echo e(request('payment_method') == 'card' ? 'selected' : ''); ?>>Card</option>
                                        <option value="online" <?php echo e(request('payment_method') == 'online' ? 'selected' : ''); ?>>Online</option>
                                        <option value="package" <?php echo e(request('payment_method') == 'package' ? 'selected' : ''); ?>>Package</option>
                                        <option value="other" <?php echo e(request('payment_method') == 'other' ? 'selected' : ''); ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

            <!-- Revenue Summary Stats -->
            <div class="row mb-4">
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="card stat-card-premium bg-primary-grad shadow h-100">
                        <div class="card-body">
                            <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Gross Value</div>
                            <div class="h2 mb-0 font-weight-bold"><?php echo e(format_currency($revenueStats->total_service_value ?? 0)); ?>

                            </div>
                            <div class="mt-2 small opacity-75">Total value before discounts</div>
                            <i class="fas fa-concierge-bell bg-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="card stat-card-premium bg-success-grad shadow h-100">
                        <div class="card-body">
                            <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Total Revenue</div>
                            <div class="h2 mb-0 font-weight-bold">
                                <?php echo e(format_currency($revenueStats->total_cash_revenue ?? 0)); ?>

                                <?php if(($revenueStats->total_tips ?? 0) > 0): ?>
                                    <span style="font-size: 0.5em; vertical-align: middle;"></span>
                                <?php endif; ?>
                            </div>
                            <div class="mt-2 small fw-bold" style="opacity: 0.9; letter-spacing: 0.5px;">Actual payments
                                received</div>
                            <i class="fas fa-wallet bg-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="card stat-card-premium bg-orange-grad shadow h-100">
                        <div class="card-body">
                            <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Total Tips</div>
                            <div class="h2 mb-0 font-weight-bold"><?php echo e(format_currency($revenueStats->total_tips ?? 0)); ?></div>
                            <div class="mt-2 small opacity-75">Gratuity collected</div>
                            <i class="fas fa-coins bg-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="card stat-card-premium bg-danger-grad shadow h-100">
                        <div class="card-body">
                            <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Loss Amount</div>
                            <div class="h2 mb-0 font-weight-bold"><?php echo e(format_currency($revenueStats->loss_amount ?? 0)); ?></div>
                            <div class="mt-2 small opacity-75">Cancelled/No-show loss</div>
                            <i class="fas fa-exclamation-triangle bg-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="card stat-card-premium bg-blue-grad shadow h-100">
                        <div class="card-body">
                            <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Sales</div>
                            <div class="h2 mb-0 font-weight-bold"><?php echo e($revenueStats->transaction_count); ?></div>
                            <div class="mt-2 small opacity-75">Items: <?php echo e($revenueStats->package_count); ?> Pkg | <?php echo e($revenueStats->membership_count); ?> Mem | <?php echo e($revenueStats->direct_count); ?> Dir</div>
                            <i class="fas fa-receipt bg-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="card stat-card-premium bg-primary-grad shadow h-100" style="filter: hue-rotate(45deg);">
                        <div class="card-body">
                            <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Avg Ticket</div>
                            <div class="h2 mb-0 font-weight-bold"><?php echo e(format_currency($customerInsights->avg_spend)); ?></div>
                            <div class="mt-2 small opacity-75">Avg spend per booking</div>
                            <i class="fas fa-chart-line bg-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <!-- Revenue Source Breakdown -->
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card glass-card shadow-sm border-0 h-100">
                        <div class="card-header py-3 bg-white border-0 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Revenue & Payments</h6>
                            <i class="fas fa-file-invoice-dollar text-muted"></i>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small font-weight-bold">Services</span>
                                    <span class="small font-weight-bold"><?php echo e(format_currency($revenueStats->service_revenue)); ?></span>
                                </div>
                                <div class="progress progress-premium" style="height: 8px;">
                                    <?php
                                        $totalVal = $revenueStats->total_service_value ?: 1;
                                        $servicePercent = ($revenueStats->service_revenue / $totalVal) * 100;
                                    ?>
                                    <div class="progress-bar bg-primary" role="progressbar"
                                        style="width: <?php echo e($servicePercent); ?>%"></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small font-weight-bold">Products</span>
                                    <span class="small font-weight-bold"><?php echo e(format_currency($revenueStats->product_revenue)); ?></span>
                                </div>
                                <div class="progress progress-premium" style="height: 8px;">
                                    <?php $productPercent = ($revenueStats->product_revenue / $totalVal) * 100; ?>
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: <?php echo e($productPercent); ?>%"></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small font-weight-bold">Packages</span>
                                    <span class="small font-weight-bold"><?php echo e(format_currency($revenueStats->package_revenue)); ?></span>
                                </div>
                                <div class="progress progress-premium" style="height: 8px;">
                                    <?php $packagePercent = ($revenueStats->package_revenue / $totalVal) * 100; ?>
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo e($packagePercent); ?>%">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small font-weight-bold">Memberships</span>
                                    <span class="small font-weight-bold"><?php echo e(format_currency($revenueStats->membership_revenue)); ?></span>
                                </div>
                                <div class="progress progress-premium" style="height: 8px;">
                                    <?php $membershipPercent = ($revenueStats->membership_revenue / $totalVal) * 100; ?>
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: <?php echo e($membershipPercent); ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small font-weight-bold text-success">Tips</span>
                                    <span class="small font-weight-bold text-success"><?php echo e(format_currency($revenueStats->total_tips)); ?></span>
                                </div>
                                <div class="progress progress-premium" style="height: 8px;">
                                    <?php $tipPercent = ($revenueStats->total_tips / $totalVal) * 100; ?>
                                    <div class="progress-bar bg-orange-grad" role="progressbar"
                                        style="width: <?php echo e($tipPercent); ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small font-weight-bold text-danger">Discounts</span>
                                    <span class="small font-weight-bold text-danger">-<?php echo e(format_currency($revenueStats->total_discount)); ?></span>
                                </div>
                                <div class="progress progress-premium" style="height: 8px;">
                                    <?php $discountPercent = ($revenueStats->total_discount / $totalVal) * 100; ?>
                                    <div class="progress-bar bg-danger" role="progressbar"
                                        style="width: <?php echo e($discountPercent); ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small font-weight-bold text-info">Package Redemptions</span>
                                    <span class="small font-weight-bold text-info">-<?php echo e(format_currency($revenueStats->package_redemptions)); ?></span>
                                </div>
                                <div class="progress progress-premium" style="height: 8px;">
                                    <?php $redemptionPercent = ($revenueStats->package_redemptions / $totalVal) * 100; ?>
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?php echo e($redemptionPercent); ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small font-weight-bold text-success">Tax</span>
                                    <span class="small font-weight-bold text-success">+<?php echo e(format_currency($revenueStats->total_tax)); ?></span>
                                </div>
                                <div class="progress progress-premium" style="height: 8px;">
                                    <?php $taxPercent = ($revenueStats->total_tax / $totalVal) * 100; ?>
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($taxPercent); ?>%">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small font-weight-bold text-warning">Unpaid Balances</span>
                                    <span class="small font-weight-bold text-warning">-<?php echo e(format_currency($revenueStats->total_unpaid)); ?></span>
                                </div>
                                <div class="progress progress-premium" style="height: 8px;">
                                    <?php $unpaidPercent = ($revenueStats->total_unpaid / $totalVal) * 100; ?>
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: <?php echo e($unpaidPercent); ?>%"></div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small font-weight-bold">Bookings Revenue</span>
                                    <span class="small font-weight-bold">
                                        <?php echo e(format_currency($revenueStats->booking_revenue)); ?>

                                    </span>
                                </div>
                                <div class="progress progress-premium" style="height: 8px;">
                                    <?php
                                        $totalRev = ($revenueStats->booking_revenue + $revenueStats->pos_direct_revenue) ?: 1;
                                        $bookingPercent = ($revenueStats->booking_revenue / $totalRev) * 100;
                                    ?>
                                    <div class="progress-bar bg-primary" role="progressbar"
                                        style="width: <?php echo e($bookingPercent); ?>%"></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small font-weight-bold">Direct POS Sales</span>
                                    <span class="small font-weight-bold">
                                        <?php echo e(format_currency($revenueStats->pos_direct_revenue)); ?>

                                        <?php if($revenueStats->pos_direct_discount > 0): ?>
                                            <span class="text-danger small">(-<?php echo e(format_currency($revenueStats->pos_direct_discount)); ?>)</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="progress progress-premium" style="height: 8px;">
                                    <?php $posPercent = ($revenueStats->pos_direct_revenue / $totalRev) * 100; ?>
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo e($posPercent); ?>%">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h6 class="small font-weight-bold text-muted mb-3">Payment Methods</h6>
                            <div class="row g-3">
                                <?php $__currentLoopData = $revenueStats->payment_methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($amount > 0): ?>
                                                                            <div class="col-6">
                                                                                <div class="p-2 border rounded-3 bg-light">
                                                                                    <div class="text-uppercase text-muted" style="font-size: 0.65rem; font-weight: 800;"><?php echo e($method); ?></div>
                                                                                    <div class="fw-bold text-dark"><?php echo e(format_currency($amount)); ?></div>
                                                                                </div>
                                                                            </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Insights -->
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card glass-card shadow-sm border-0 h-100">
                        <div class="card-header py-3 bg-white border-0 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Customer Insights</h6>
                            <i class="fas fa-users text-muted"></i>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-4">
                                <div class="col-6 border-end">
                                    <div class="h4 mb-0 font-weight-bold text-primary"><?php echo e($customerInsights->new_customers); ?>

                                    </div>
                                    <div class="small text-muted text-uppercase font-weight-bold">New</div>
                                </div>
                                <div class="col-6">
                                    <div class="h4 mb-0 font-weight-bold text-info"><?php echo e($customerInsights->returning_customers); ?>

                                    </div>
                                    <div class="small text-muted text-uppercase font-weight-bold">Returning</div>
                                </div>
                            </div>

                            <h6 class="small font-weight-bold text-muted mb-3">Top Customers (by Spend)</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mb-0">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th>Name</th>
                                            <th class="text-center">Visits</th>
                                            <th class="text-end">Spend</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $customerInsights->top_customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                    <tr>
                                                                                                        <td class="small fw-bold"><?php echo e($customer->name); ?></td>
                                                                                                        <td class="small text-center"><span class="badge bg-light text-dark"><?php echo e($customer->visits); ?></span></td>
                                                                                                        <td class="small text-end">
                                                                                                            <div class="fw-bold text-primary"><?php echo e(format_currency($customer->spend)); ?></div>
                                                                                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                                                                                Svc: <?php echo e(format_currency($customer->service_spend)); ?> | Tip: <?php echo e(format_currency($customer->tip_spend)); ?>

                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cancellation & Advanced Analytics -->
                <div class="col-xl-4 col-lg-12 mb-4">
                    <div class="card glass-card shadow-sm border-0 h-100">
                        <div class="card-header py-3 bg-white border-0 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Efficiency & Loss</h6>
                            <i class="fas fa-bolt text-muted"></i>
                        </div>
                        <div class="card-body">
                            <div class="mb-4 p-3 bg-light rounded-3">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <div class="small text-muted text-uppercase mb-1">Avg Lead Time</div>
                                        <div class="h5 mb-0 font-weight-bold"><?php echo e(round($leadTimeMinutes / 60, 1)); ?> Hours</div>
                                    </div>
                                    <div class="col-6 border-start">
                                        <div class="small text-muted text-uppercase mb-1">Busy Day</div>
                                        <div class="h5 mb-0 font-weight-bold text-primary">
                                            <?php echo e($busyDays->sortByDesc('count')->first()->day ?? 'N/A'); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="small font-weight-bold text-muted mb-3">Cancellation Reasons</h6>
                            <?php $__empty_1 = true; $__currentLoopData = $cancellationAnalysis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $analysis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small"><?php echo e($analysis->cancellation_reason ?: 'No Reason'); ?></span>
                                        <span class="small text-danger fw-bold"><?php echo e(format_currency($analysis->loss)); ?></span>
                                    </div>
                                    <div class="progress progress-premium" style="height: 4px;">
                                        <?php $lossPercent = $revenueStats->loss_amount > 0 ? ($analysis->loss /
                                        $revenueStats->loss_amount * 100) : 0; ?>
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo e($lossPercent); ?>%">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="text-center py-4 text-muted small">No cancellations recorded</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <!-- Status Breakdown -->
                <div class="col-12">
                    <div class="card glass-card shadow-sm mb-4 border-0 h-100">
                        <div class="card-header py-3 bg-white border-0">
                            <h6 class="m-0 font-weight-bold text-primary">Appointment Performance</h6>
                        </div>
                        <div class="card-body">
                                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-4 mb-4">
                                    <?php $__currentLoopData = $statusStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $color = match($status) {
                                                'completed' => 'success',
                                                'confirmed' => 'info', 
                                                'pending' => 'warning',
                                                'cancelled' => 'danger',
                                                'no_show' => 'dark',
                                                default => 'secondary'
                                            };
                                            $icon = match($status) {
                                                'completed' => 'fa-check-circle',
                                                'confirmed' => 'fa-calendar-check',
                                                'pending' => 'fa-clock',
                                                'cancelled' => 'fa-times-circle',
                                                'no_show' => 'fa-user-slash',
                                                default => 'fa-circle'
                                            };
                                        ?>
                                        <div class="col">
                                            <div class="card h-100 border-0 shadow-sm hover-elevate transition-all" style="background: linear-gradient(to bottom right, #ffffff, #f8f9fa);">
                                                <div class="card-body text-center p-3 position-relative overflow-hidden">
                                                    <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background-color: var(--bs-<?php echo e($color); ?>);"></div>
                                                    
                                                    <div class="mb-3 mt-2">
                                                        <span class="badge bg-<?php echo e($color); ?> bg-opacity-10 text-<?php echo e($color); ?> p-2 rounded-circle mb-2">
                                                            <i class="fas <?php echo e($icon); ?> fa-lg"></i>
                                                        </span>
                                                        <div class="small fw-bold text-uppercase text-muted mt-1 tracking-wider">
                                                            <?php echo e(str_replace('_', ' ', $status)); ?>

                                                        </div>
                                                    </div>

                                                    <h2 class="display-6 fw-bold mb-1 text-dark"><?php echo e($data['count']); ?></h2>
                                                    <div class="text-xs text-muted mb-3 font-monospace"><?php echo e(number_format($data['percentage'], 1)); ?>%</div>
                                                    
                                                    <div class="pt-2 border-top border-light">
                                                        <div class="small text-muted mb-0">Revenue</div>
                                                        <div class="h5 mb-0 fw-bold text-<?php echo e($color); ?>">
                                                            <?php echo e(format_currency($data['revenue'] ?? 0)); ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="small font-weight-bold text-muted mb-3">Booking Source</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle">
                                            <tbody>
                                                <?php $__currentLoopData = $sourceStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td class="small fw-bold text-uppercase text-muted" style="width: 140px;">
                                                            <?php echo e(ucfirst($source->source ?: 'Unknown')); ?>

                                                        </td>
                                                        <td>
                                                            <div class="progress progress-premium" style="height: 6px;">
                                                                <?php
                                                                    $maxSource = $sourceStats->max('count') ?: 1;
                                                                    $sourcePercent = ($source->count / $maxSource) * 100;
                                                                    $t = $sourceStats->sum('count') ?: 1;
                                                                    $realPercent = ($source->count / $t) * 100;
                                                                ?>
                                                                <div class="progress-bar bg-gradient-premium-blue" role="progressbar"
                                                                    style="width: <?php echo e($sourcePercent); ?>%"></div>
                                                            </div>
                                                        </td>
                                                        <td class="text-end small fw-bold" style="width: 100px;">
                                                            <?php echo e($source->count); ?> <span class="text-muted fw-normal ms-1">(<?php echo e(round($realPercent)); ?>%)</span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="small font-weight-bold text-muted mb-3">Payment Status</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle">
                                            <tbody>
                                                <?php $__currentLoopData = $paymentStatusStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td class="small fw-bold text-uppercase text-muted" style="width: 140px;">
                                                            <?php echo e(ucfirst($stat->payment_status ?: 'Unpaid')); ?>

                                                        </td>
                                                        <td>
                                                            <div class="progress progress-premium" style="height: 6px;">
                                                                <?php
                                                                    $maxPayment = $paymentStatusStats->max('count') ?: 1;
                                                                    $payPercent = ($stat->count / $maxPayment) * 100;
                                                                    $totalPay = $paymentStatusStats->sum('count') ?: 1;
                                                                    $realPayPercent = ($stat->count / $totalPay) * 100;
                                                                    $color = match($stat->payment_status) {
                                                                        'paid' => 'bg-success',
                                                                        'unpaid' => 'bg-danger',
                                                                        'partial' => 'bg-warning',
                                                                        'refunded' => 'bg-secondary',
                                                                        default => 'bg-secondary'
                                                                    };
                                                                ?>
                                                                <div class="progress-bar <?php echo e($color); ?>" role="progressbar"
                                                                    style="width: <?php echo e($payPercent); ?>%"></div>
                                                            </div>
                                                        </td>
                                                        <td class="text-end small fw-bold" style="width: 100px;">
                                                            <?php echo e($stat->count); ?> <span class="text-muted fw-normal ms-1">(<?php echo e(round($realPayPercent)); ?>%)</span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Row -->
            <div class="row mb-4">
                <!-- Hourly Distribution -->
                <div class="col-lg-6 mb-4">
                    <div class="card glass-card border-0 h-100">
                        <div class="card-header py-3 bg-white border-0">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-clock me-2"></i>Hourly Distribution
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if($hourlyDistribution->count() > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-premium align-middle">
                                        <thead>
                                            <tr>
                                                <th>Hour</th>
                                                <th class="text-end">Bookings</th>
                                                <th style="width: 40%">Distribution</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $maxHourly = $hourlyDistribution->max() ?: 1; ?>
                                            <?php $__currentLoopData = $hourlyDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hour => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="fw-bold"><?php echo e(str_pad($hour, 2, '0', STR_PAD_LEFT)); ?>:00</td>
                                                    <td class="text-end fw-bold"><?php echo e($count); ?></td>
                                                    <td>
                                                        <div class="progress progress-premium">
                                                            <div class="progress-bar bg-info" role="progressbar"
                                                                style="width: <?php echo e(($count / $maxHourly * 100)); ?>%">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-clock fa-3x text-light mb-3"></i>
                                    <p class="text-muted mb-0">No hourly data available</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Daily Trend -->
                <div class="col-lg-6 mb-4">
                    <div class="card glass-card border-0 h-100">
                        <div class="card-header py-3 bg-white border-0">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-line me-2"></i>Daily Booking Trend
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if($dailyTrend->count() > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-premium align-middle">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th class="text-end">Bookings</th>
                                                <th style="width: 40%">Trend</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $maxDaily = collect($dailyTrend)->max('total') ?: 1; ?>
                                            <?php $__currentLoopData = $dailyTrend; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="fw-bold"><?php echo e(format_date(\Carbon\Carbon::parse($date))); ?></td>
                                                    <td class="text-end fw-bold"><?php echo e($data['total']); ?></td>
                                                    <td>
                                                        <div class="progress progress-premium">
                                                            <div class="progress-bar bg-primary" role="progressbar"
                                                                style="width: <?php echo e(($data['total'] / $maxDaily * 100)); ?>%">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-chart-line fa-3x text-light mb-3"></i>
                                    <p class="text-muted mb-0">No daily data available</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <!-- Staff Performance -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card glass-card border-0 h-100">
                        <div class="card-header py-3 bg-white border-0 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Staff Performance</h6>
                            <span class="badge bg-light text-primary border-0">Sorted by Revenue</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-premium align-middle">
                                    <thead>
                                        <tr>
                                            <th>Staff</th>
                                            <th class="text-center">Bookings</th>
                                            <th class="text-end">Revenue</th>
                                            <th class="text-center">Rating</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $staffWorkload; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div class="d-flex align-items-center">
                                                                                                                <div class="avatar-xs me-2 bg-gradient-premium-purple text-white rounded-circle d-flex align-items-center justify-content-center"
                                                                                                                    style="width: 32px; height: 32px;">
                                                                                                                    <?php echo e(substr($staff['staff'], 0, 1)); ?>

                                                                                                                </div>
                                                                                                                <div>
                                                                                                                    <div class="fw-bold text-dark"><?php echo e($staff['staff']); ?></div>
                                                                                                                    <div class="progress progress-premium mt-1"
                                                                                                                        style="height: 4px; width: 100px;">
                                                                                                                        <?php
                                                                                                                            $maxRevenue = collect($staffWorkload)->max('revenue') ?: 1;
                                                                                                                            $revPercent = ($staff['revenue'] / $maxRevenue) * 100;
                                                                                                                        ?>
                                                                                                                        <div class="progress-bar bg-primary"
                                                                                                                            style="width: <?php echo e($revPercent); ?>%"></div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td class="text-center">
                                                                                                            <span class="badge bg-success-soft text-success rounded-pill px-2"><?php echo e($staff['completed']); ?></span>
                                                                                                            <span class="text-muted small">/ <?php echo e($staff['total']); ?></span>
                                                                                                        </td>
                                                                                                        <td class="text-end fw-bold text-dark"><?php echo e(format_currency($staff['revenue'])); ?></td>
                                                                                                        <td class="text-center">
                                                                                                            <?php if($staff['avg_rating'] > 0): ?>
                                                                                                                <div class="text-warning small">
                                                                                                                    <?php for($i = 1; $i <= 5; $i++): ?> <i
                                                                                                                        class="fas fa-star <?php echo e($i <= round($staff['avg_rating']) ? '' : 'text-light'); ?>">
                                                                                                                        </i>
                                                                                                                    <?php endfor; ?>
                                                                                                                        <span class="ms-1 fw-bold text-dark"><?php echo e($staff['avg_rating']); ?></span>
                                                                                                                </div>
                                                                                                            <?php else: ?>
                                                                                                                <span class="text-muted small">No ratings</span>
                                                                                                            <?php endif; ?>
                                                                                                        </td>
                                                                                                    </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Popularity -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card glass-card border-0 h-100">
                        <div class="card-header py-3 bg-white border-0">
                            <h6 class="m-0 font-weight-bold text-primary">Top 10 Services</h6>
                        </div>
                        <div class="card-body">
                            <?php $__currentLoopData = $servicePopularity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small font-weight-bold text-dark"><?php echo e($service['service']); ?></span>
                                        <span class="badge bg-primary-soft text-primary rounded-pill"><?php echo e($service['count']); ?>

                                            bookings</span>
                                    </div>
                                    <div class="progress progress-premium">
                                        <?php
                                            $maxCount = collect($servicePopularity)->max('count') ?: 1;
                                            $percent = ($service['count'] / $maxCount) * 100;
                                        ?>
                                        <div class="progress-bar bg-gradient-premium-blue" role="progressbar"
                                            style="width: <?php echo e($percent); ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Detailed Appointments -->
            <div class="card glass-card border-0 mb-4">
                <div class="card-header py-4 bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Appointment Details
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium align-middle mb-0" id="appointmentsTable">
                            <thead>
                                <tr>
                                    <th class="ps-4">Date & Time</th>
                                    <th>Customer</th>
                                    <th>Service / Category</th>
                                    <th>Staff</th>
                                    <th>Source</th>
                                    <th>Payment</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Tip</th>
                                    <th class="text-end">Balance</th>
                                    <th class="text-end pe-4">Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $totalAmount = 0;
                                    $totalTip = 0;
                                    $totalBalance = 0;
                                    $totalFinal = 0;
                                ?>
                                <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <?php
                                                                                 $bookingTotal = ($booking->net_amount ?? $booking->amount ?? 0) + ($booking->tip_amount ?? 0);
                                                                                 $totalAmount += ($booking->net_amount ?? $booking->amount ?? 0);
                                                                                 $totalTip += ($booking->tip_amount ?? 0);
                                                                                 $totalFinal += $bookingTotal;

                                                                                $paidAmount = 0;
                                                                                if ($booking->posSaleItem && $booking->posSaleItem->sale) {
                                                                                    if ($booking->posSaleItem->sale->payment_status === 'paid') {
                                                                                        $paidAmount = $bookingTotal;
                                                                                    } else {
                                                                                        $paidAmount = $booking->posSaleItem->sale->total;
                                                                                    }
                                                                                } elseif ($booking->payment_status === 'paid') {
                                                                                    $paidAmount = $bookingTotal;
                                                                                }
                                                                                $balance = max(0, $bookingTotal - $paidAmount);
                                                                                $totalBalance += $balance;
                                                                            ?>
                                                                            <tr class="appointment-row" style="cursor: pointer;"
                                                                                onclick="viewBookingDetails(<?php echo e($booking->id); ?>)">
                                                                                <td class="ps-4">
                                                                                    <div class="fw-bold text-dark"><?php echo e(format_date($booking->start_time)); ?></div>
                                                                                    <small class="text-muted"><i class="far fa-clock me-1"></i><?php echo e($booking->start_time->format('H:i')); ?></small>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="fw-bold text-dark"><?php echo e($booking->customer ? $booking->customer->name :
                                    'Guest'); ?></div>
                                                                                    <small class="text-muted"><?php echo e($booking->customer ? $booking->customer->display_phone : '-'); ?></small>
                                                                                </td>
                                                                                <td>
                                                                                    <?php if($booking->package): ?>
                                                                                        <div class="text-primary fw-bold"><?php echo e($booking->package->name); ?></div>
                                                                                        <small class="text-muted"><?php echo e($booking->service ? $booking->service->name : 'N/A'); ?></small>
                                                                                    <?php else: ?>
                                                                                                                                                            <div class="text-dark fw-medium"><?php echo e($booking->service ? $booking->service->name : 'N/A'); ?></div>
                                                                                                                                                            <small class="text-muted text-uppercase" style="font-size: 0.65rem;"><?php echo e($booking->service && $booking->service->category ? $booking->service->category->name
                                                                                        : 'Uncategorized'); ?></small>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="small text-dark"><?php echo e($booking->staff ? $booking->staff->name : 'Unassigned'); ?></div>
                                                                                </td>
                                                                                <td>
                                                                                    <span class="badge bg-light text-dark border small"><?php echo e(ucfirst($booking->source ?:
                                    'POS')); ?></span>
                                                                                </td>
                                                                                <td>
                                                                                    <?php if($booking->package_id): ?>
                                                                                        <span class="badge badge-premium bg-info-soft text-info">Package</span>
                                                                                    <?php else: ?>
                                                                                        <span class="badge badge-premium bg-light text-dark">
                                                                                            <?php echo e(ucfirst($booking->payment_method ?: 'N/A')); ?>

                                                                                        </span>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                                 <td class="text-end fw-medium text-dark"><?php echo e(format_currency((float)($booking->net_amount ?? $booking->amount ?? 0))); ?></td>
                                                                                <td class="text-end text-success small">+<?php echo e(format_currency((float)($booking->tip_amount ?? 0))); ?>

                                                                                </td>
                                                                                <td class="text-end text-<?php echo e($balance > 0 ? 'danger' : 'muted'); ?> small"><?php echo e(format_currency((float)$balance)); ?></td>
                                                                                <td class="text-end fw-bold text-dark pe-4"><?php echo e(format_currency((float)$bookingTotal)); ?></td>
                                                                                <td>
                                                                                     <?php
                                                                                         $statusColor = match ($booking->status) {
                                                                                             'completed' => 'success',
                                                                                             'confirmed' => 'primary',
                                                                                             'cancelled' => 'danger',
                                                                                             'no_show' => 'warning',
                                                                                             default => 'secondary'
                                                                                         };
                                                                                         $statusLabel = ucfirst($booking->status);
                                                                                         if (($booking->refunded_amount ?? 0) > 0) {
                                                                                             $statusColor = 'info';
                                                                                             $statusLabel = 'Partial Refund';
                                                                                             if ($booking->payment_status === 'refunded' || $booking->status === 'cancelled') {
                                                                                                 $statusColor = 'secondary';
                                                                                                 $statusLabel = 'Refunded';
                                                                                             }
                                                                                         }
                                                                                     ?>
                                                                                     <span class="badge badge-premium bg-<?php echo e($statusColor); ?>-soft text-<?php echo e($statusColor); ?>">
                                                                                         <?php echo e($statusLabel); ?>

                                                                                     </span>
                                                                                </td>
                                                                            </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="sticky-totals">
                                <tr>
                                    <td colspan="6" class="ps-4">Page Totals</td>
                                    <td class="text-end"><?php echo e(format_currency($totalAmount)); ?></td>
                                    <td class="text-end"><?php echo e(format_currency($totalTip)); ?></td>
                                    <td class="text-end text-danger"><?php echo e(format_currency($totalBalance)); ?></td>
                                    <td class="text-end pe-4"><?php echo e(format_currency($totalFinal)); ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-0 py-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing <?php echo e($bookings->firstItem()); ?> to <?php echo e($bookings->lastItem()); ?> of <?php echo e($bookings->total()); ?>

                                appointments
                            </div>
                            <div>
                                <?php echo e($bookings->appends(request()->all())->links()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php $__env->startPush('scripts'); ?>
            <script>
                function setDates(start, end) {
                    document.getElementById('start_date').value = start;
                    document.getElementById('end_date').value = end;
                    document.getElementById('filterForm').submit();
                }

                function viewBookingDetails(id) {
                    window.location.href = `<?php echo e(route('admin.bookings.index')); ?>?booking_id=${id}`;
                }

                $(document).ready(function () {
                    // Initialize tooltips if any
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    });

                    // Export Appointments Report
                    document.getElementById('exportAppointmentsBtn').addEventListener('click', function () {
                        const startDate = document.getElementById('start_date').value;
                        const endDate = document.getElementById('end_date').value;
                        const branchId = document.getElementById('branch_id').value;
                        const staffId = document.getElementById('staff_id').value;
                        const serviceId = document.getElementById('service_id').value;
                        const customerId = document.getElementById('customer_id').value;
                        const status = document.getElementById('status').value;
                        const paymentStatus = document.getElementById('payment_status').value;
                        const paymentMethod = document.getElementById('payment_method').value;

                        const url = new URL('<?php echo e(route("admin.reports.appointments.export")); ?>', window.location.origin);
                        if (startDate) url.searchParams.append('start_date', startDate);
                        if (endDate) url.searchParams.append('end_date', endDate);
                        if (branchId) url.searchParams.append('branch_id', branchId);
                        if (staffId) url.searchParams.append('staff_id', staffId);
                        if (serviceId) url.searchParams.append('service_id', serviceId);
                        if (customerId) url.searchParams.append('customer_id', customerId);
                        if (status) url.searchParams.append('status', status);
                        if (paymentStatus) url.searchParams.append('payment_status', paymentStatus);
                        if (paymentMethod) url.searchParams.append('payment_method', paymentMethod);

                        window.location.href = url.toString();
                    });
                });
            </script>
        <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\reports\appointments.blade.php ENDPATH**/ ?>