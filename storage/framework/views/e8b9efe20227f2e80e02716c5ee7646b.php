<?php $__env->startSection('title', $customer->name . ' - Customer Details'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        /* Remove hover animations as requested */
        .table tbody tr:hover {
            background-color: transparent !important;
        }

        .view-booking-btn:hover {
            transform: none !important;
        }

        .badge {
            font-weight: 500;
        }
    </style>
    <div class="container-fluid px-4 py-3">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"
                                class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.customers.index')); ?>"
                                class="text-decoration-none">Customers</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo e($customer->name); ?></li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0 fw-bold">Customer Details</h1>
            </div>
        </div>

        <!-- Customer Profile Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm customer-profile-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="me-4">
                                <div class="avatar avatar-xxl">
                                    <div class="avatar-title rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center shadow-sm"
                                        style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="fas fa-user fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h2 class="mb-2 fw-bold text-dark"><?php echo e($customer->name); ?></h2>
                                        <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                                            <span
                                                class="badge <?php echo e(($customer->status ?? 'active') === 'active' ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis'); ?> px-3 py-2 rounded-pill">
                                                <i class="fas fa-circle-dot me-1" style="font-size: 0.5rem;"></i>
                                                <?php echo e(ucfirst($customer->status ?? 'active')); ?>

                                            </span>
                                            <span class="text-muted d-flex align-items-center">
                                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                                Member since <?php echo e(format_date($customer->created_at)); ?>

                                            </span>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li><a class="dropdown-item" href="#"><i
                                                        class="fas fa-envelope me-2 text-primary"></i>Send
                                                    Email</a></li>
                                            <?php
                                                $salon = $customer->salon;
                                                $smsEnabled = $salon && $salon->canUseFeature('SMS Notifications') && app(\App\Services\SettingsService::class)->get('enable_sms', false, $salon->id);
                                            ?>
                                            <?php if($smsEnabled): ?>
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="fas fa-sms me-2 text-info"></i>Send
                                                        SMS</a></li>
                                            <?php endif; ?>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    onclick="toggleCustomerStatus(<?php echo e($customer->id); ?>)">
                                                    <i
                                                        class="fas <?php echo e(($customer->status ?? 'active') === 'active' ? 'fa-ban text-warning' : 'fa-check-circle text-success'); ?> me-2"></i>
                                                    <?php echo e(($customer->status ?? 'active') === 'active' ? 'Mark as Inactive' : 'Mark as Active'); ?>

                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <?php if($stats['total_bookings'] == 0): ?>
                                                <li><a class="dropdown-item text-danger" href="#"
                                                        onclick="deleteCustomer(<?php echo e($customer->id); ?>)">
                                                        <i class="fas fa-trash-alt me-2"></i>Delete Customer
                                                    </a></li>
                                            <?php else: ?>
                                                <li><span class="dropdown-item text-muted" data-bs-toggle="tooltip"
                                                        title="Cannot delete customer with existing bookings">
                                                        <i class="fas fa-trash-alt me-2"></i>Delete Disabled
                                                    </span></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>

                                <div class="row g-4 mt-2">
                                    <div class="col-md-4 col-lg-3">
                                        <div class="info-item p-3 rounded-3 bg-light border-0 h-100">
                                            <div class="d-flex align-items-start">
                                                <div
                                                    class="icon-shape icon-sm bg-primary-subtle text-primary rounded-3 me-3 flex-shrink-0">
                                                    <i class="fas fa-envelope"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 small text-muted fw-medium text-uppercase"
                                                        style="font-size: 0.7rem; letter-spacing: 0.5px;">Email</p>
                                                    <p class="mb-0 fw-medium">
                                                        <?php if($customer->email): ?>
                                                            <?php if(\App\Helpers\CustomerDataHelper::shouldMaskData()): ?>
                                                                <?php echo e($customer->display_email); ?>

                                                            <?php else: ?>
                                                                <a href="mailto:<?php echo e($customer->email); ?>"
                                                                    class="text-decoration-none text-dark">
                                                                    <?php echo e($customer->display_email); ?>

                                                                </a>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">N/A</span>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-3">
                                        <div class="info-item p-3 rounded-3 bg-light border-0 h-100">
                                            <div class="d-flex align-items-start">
                                                <div
                                                    class="icon-shape icon-sm bg-success-subtle text-success rounded-3 me-3 flex-shrink-0">
                                                    <i class="fas fa-phone"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 small text-muted fw-medium text-uppercase"
                                                        style="font-size: 0.7rem; letter-spacing: 0.5px;">Phone</p>
                                                    <p class="mb-0 fw-medium">
                                                        <?php if($customer->phone): ?>
                                                            <?php if(\App\Helpers\CustomerDataHelper::shouldMaskData()): ?>
                                                                <?php echo e($customer->display_phone); ?>

                                                            <?php else: ?>
                                                                <a href="tel:<?php echo e(($customer->country_code ?? '+91') . $customer->phone); ?>"
                                                                    class="text-decoration-none text-dark">
                                                                    <?php echo e($customer->display_phone); ?>

                                                                </a>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">N/A</span>
                                                        <?php endif; ?>
                                                    </p>
                                                    <?php if($customer->secondary_number): ?>
                                                        <p class="mb-0 small mt-2 text-muted">
                                                            <span class="fw-medium">Alt:</span>
                                                            <?php if(\App\Helpers\CustomerDataHelper::shouldMaskData()): ?>
                                                                <?php echo e(($customer->secondary_country_code ?? '+91') . ' ' . \App\Helpers\CustomerDataHelper::maskPhone($customer->secondary_number)); ?>

                                                            <?php else: ?>
                                                                <a href="tel:<?php echo e(($customer->secondary_country_code ?? '+91') . $customer->secondary_number); ?>"
                                                                    class="text-decoration-none text-muted">
                                                                    <?php echo e(($customer->secondary_country_code ?? '+91') . ' ' . $customer->secondary_number); ?>

                                                                </a>
                                                            <?php endif; ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-3">
                                        <div class="info-item p-3 rounded-3 bg-light border-0 h-100">
                                            <div class="d-flex align-items-start">
                                                <div
                                                    class="icon-shape icon-sm bg-info-subtle text-info rounded-3 me-3 flex-shrink-0">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 small text-muted fw-medium text-uppercase"
                                                        style="font-size: 0.7rem; letter-spacing: 0.5px;">Address</p>
                                                    <p class="mb-0 fw-medium"><?php echo e($customer->address ?? 'N/A'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-3">
                                        <div class="info-item p-3 rounded-3 bg-light border-0 h-100">
                                            <div class="d-flex align-items-start">
                                                <div
                                                    class="icon-shape icon-sm bg-warning-subtle text-warning rounded-3 me-3 flex-shrink-0">
                                                    <i class="fas fa-birthday-cake"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 small text-muted fw-medium text-uppercase"
                                                        style="font-size: 0.7rem; letter-spacing: 0.5px;">Date of Birth</p>
                                                    <p class="mb-0 fw-medium">
                                                        <?php echo e($customer->dob ? $customer->dob->format('d M Y') : 'N/A'); ?>

                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-3">
                                        <div class="info-item p-3 rounded-3 bg-light border-0 h-100">
                                            <div class="d-flex align-items-start">
                                                <div
                                                    class="icon-shape icon-sm bg-danger-subtle text-danger rounded-3 me-3 flex-shrink-0">
                                                    <i class="fas fa-heart"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 small text-muted fw-medium text-uppercase"
                                                        style="font-size: 0.7rem; letter-spacing: 0.5px;">Anniversary</p>
                                                    <p class="mb-0 fw-medium">
                                                        <?php echo e($customer->anniversary ? $customer->anniversary->format('d M Y') : 'N/A'); ?>

                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Medical/Allergy Notes Warning -->
                                <div class="mt-4">
                                    <?php if($customer->medical_notes): ?>
                                        <div class="alert alert-warning border-warning border-2 rounded-3 shadow-sm mt-3 mb-0"
                                            role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-3 flex-shrink-0">
                                                    <div class="icon-shape bg-warning-subtle text-warning rounded-circle">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="alert-heading mb-2 fw-bold d-flex align-items-center">
                                                        <i class="fas fa-notes-medical me-2"></i>
                                                        Medical / Allergy Notes
                                                    </h6>
                                                    <p class="mb-0"><?php echo e($customer->medical_notes); ?></p>
                                                </div>
                                                <button class="btn btn-sm btn-warning rounded-pill flex-shrink-0"
                                                    data-bs-toggle="modal" data-bs-target="#editMedicalNotesModal">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </button>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-light border border-2 rounded-3 mt-3 mb-0" role="alert">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="text-muted d-flex align-items-center">
                                                    <i class="fas fa-notes-medical me-2 text-muted"></i>
                                                    No medical or allergy notes recorded
                                                </div>
                                                <button class="btn btn-sm btn-outline-primary rounded-pill"
                                                    data-bs-toggle="modal" data-bs-target="#editMedicalNotesModal">
                                                    <i class="fas fa-plus me-1"></i> Add Medical Notes
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm stat-card stat-card-primary">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-2 small fw-medium text-uppercase"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Bookings</p>
                                <h2 class="mb-0 fw-bold text-dark"><?php echo e($stats['total_bookings']); ?></h2>
                            </div>
                            <div class="icon-shape icon-lg bg-primary-subtle text-primary rounded-3 flex-shrink-0">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <?php if(isset($stats['bookings_change']) && $stats['bookings_change'] > 0): ?>
                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-2 py-1">
                                    <i class="fas fa-arrow-up me-1"></i> <?php echo e(number_format($stats['bookings_change'], 1)); ?>%
                                </span>
                            <?php elseif(isset($stats['bookings_change']) && $stats['bookings_change'] < 0): ?>
                                <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-2 py-1">
                                    <i class="fas fa-arrow-down me-1"></i>
                                    <?php echo e(number_format(abs($stats['bookings_change']), 1)); ?>%
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-1">
                                    <i class="fas fa-minus me-1"></i> 0%
                                </span>
                            <?php endif; ?>
                            <span class="text-muted small ms-2">vs last month</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm stat-card stat-card-success">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-2 small fw-medium text-uppercase"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Spent</p>
                                <h2 class="mb-0 fw-bold text-dark">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($stats['total_spent'], 2)); ?>

                                </h2>
                            </div>
                            <div class="icon-shape icon-lg bg-success-subtle text-success rounded-3 flex-shrink-0">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <?php if($stats['spent_change'] > 0): ?>
                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-2 py-1">
                                    <i class="fas fa-arrow-up me-1"></i> <?php echo e(number_format($stats['spent_change'], 1)); ?>%
                                </span>
                            <?php elseif($stats['spent_change'] < 0): ?>
                                <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-2 py-1">
                                    <i class="fas fa-arrow-down me-1"></i> <?php echo e(number_format(abs($stats['spent_change']), 1)); ?>%
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-1">
                                    <i class="fas fa-minus me-1"></i> 0%
                                </span>
                            <?php endif; ?>
                            <span class="text-muted small ms-2">vs last month</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm stat-card stat-card-danger">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-2 small fw-medium text-uppercase"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">Unpaid Balance</p>
                                <h2
                                    class="mb-0 fw-bold <?php echo e($stats['unpaid_balance'] > 0 ? 'text-danger' : 'text-success'); ?>">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($stats['unpaid_balance'], 2)); ?>

                                </h2>
                            </div>
                            <div class="icon-shape icon-lg bg-danger-subtle text-danger rounded-3 flex-shrink-0">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                        <div>
                            <span class="text-muted small">
                                <?php if($stats['unpaid_balance'] > 0): ?>
                                    <i class="fas fa-exclamation-circle me-1 text-danger"></i>Outstanding amount
                                <?php else: ?>
                                    <i class="fas fa-check-circle me-1 text-success"></i>All clear
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm stat-card stat-card-warning">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-2 small fw-medium text-uppercase"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">Favorite Service</p>
                                <h2 class="mb-0 fw-bold text-dark" style="font-size: 1.5rem;">
                                    <?php echo e($stats['favorite_service'] ?? 'N/A'); ?>

                                </h2>
                            </div>
                            <div class="icon-shape icon-lg bg-warning-subtle text-warning rounded-3 flex-shrink-0">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <div>
                            <span class="text-muted small">Most booked service</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm stat-card stat-card-info">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-2 small fw-medium text-uppercase"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">Last Visit</p>
                                <h2 class="mb-0 fw-bold text-dark" style="font-size: 1.25rem;">
                                    <?php if($stats['last_visit']): ?>
                                        <?php echo e(\Carbon\Carbon::parse($stats['last_visit'])->diffForHumans()); ?>

                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </h2>
                            </div>
                            <div class="icon-shape icon-lg bg-info-subtle text-info rounded-3 flex-shrink-0">
                                <i class="fas fa-history"></i>
                            </div>
                        </div>
                        <div>
                            <span class="text-muted small">
                                <?php if($stats['last_visit']): ?>
                                    <i
                                        class="fas fa-calendar me-1"></i><?php echo e(format_date(\Carbon\Carbon::parse($stats['last_visit']))); ?>

                                <?php else: ?>
                                    No visits yet
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Membership Card -->
        <?php if($customer->hasActiveMembership()): ?>
            <?php
                $activeMembership = $customer->getActiveMembership();
                $isExpiringSoon = $activeMembership->end_date < now()->addDays(30);
            ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card h-100 border-0 shadow-sm membership-card"
                        style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium text-uppercase"
                                        style="font-size: 0.75rem; letter-spacing: 0.5px;">Active Membership</p>
                                    <h3 class="mb-2 fw-bold text-dark"><?php echo e($activeMembership->membership->name); ?></h3>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <span class="badge bg-info-subtle text-info-emphasis px-3 py-2 rounded-pill">
                                            <i
                                                class="fas fa-tag me-1"></i><?php echo e(ucfirst($activeMembership->membership->membership_type)); ?>

                                        </span>
                                        <?php if($isExpiringSoon): ?>
                                            <span class="badge bg-warning text-white px-3 py-2 rounded-pill">
                                                <i class="fas fa-clock me-1"></i>Expiring Soon
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="icon-shape icon-lg bg-info-subtle text-info rounded-3 flex-shrink-0">
                                    <i class="fas fa-id-card"></i>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="p-3 bg-white rounded-3 border-0">
                                        <p class="mb-2 small text-muted fw-medium">Validity Period</p>
                                        <p class="mb-0 fw-medium">
                                            <i class="fas fa-calendar-check me-2 text-primary"></i>
                                            Active from <?php echo e(format_date($activeMembership->start_date)); ?>

                                        </p>
                                        <p class="mb-0 fw-medium mt-1">
                                            <i class="fas fa-calendar-times me-2 text-danger"></i>
                                            to <?php echo e(format_date($activeMembership->end_date)); ?>

                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-white rounded-3 border-0">
                                        <p class="mb-2 small text-muted fw-medium">Expiry Status</p>
                                        <p class="mb-0">
                                            <?php if($activeMembership->end_date->isPast()): ?>
                                                <span class="badge bg-danger-subtle text-danger-emphasis px-3 py-2 rounded-pill">
                                                    <i class="fas fa-exclamation-circle me-1"></i>Expired
                                                    <?php echo e($activeMembership->end_date->diffForHumans()); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success-emphasis px-3 py-2 rounded-pill">
                                                    <i
                                                        class="fas fa-check-circle me-1"></i><?php echo e($activeMembership->end_date->diffForHumans()); ?>

                                                    remaining
                                                </span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 p-3 bg-white rounded-3 border-0">
                                <p class="mb-1 small text-muted fw-medium">Membership Details</p>
                                <p class="mb-0 fw-medium">
                                    <strong><?php echo e($activeMembership->membership->name); ?></strong> -
                                    <?php echo e($activeMembership->membership->validity_value); ?>

                                    <?php echo e($activeMembership->membership->validity_unit); ?>

                                    validity period
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <div
                                class="icon-shape icon-lg bg-secondary-subtle text-secondary rounded-3 mx-auto mb-3 d-inline-block">
                                <i class="fas fa-id-card fa-2x"></i>
                            </div>
                            <h5 class="mb-2 fw-bold">No Active Membership</h5>
                            <p class="text-muted mb-4">This customer doesn't have an active membership.</p>
                            <a href="<?php echo e(route('admin.memberships.index')); ?>" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-plus me-1"></i> View Memberships
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Package Services Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold d-flex align-items-center">
                                <i class="fas fa-box text-primary me-2"></i>Package Services
                            </h5>
                            <div class="d-flex gap-2">
                                <?php if(isset($packageData['stats']) && count($packageData['stats']) > 0): ?>
                                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2">
                                        <?php echo e(count($packageData['stats'])); ?>

                                        <?php echo e(Str::plural('Active Package', count($packageData['stats']))); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Active Packages with Remaining Services -->
                        <?php if(isset($packageData['stats']) && is_array($packageData['stats']) && count($packageData['stats']) > 0): ?>
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2"></i>Active Packages
                                </h6>
                                <div class="row g-3">
                                    <?php $__currentLoopData = $packageData['stats']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $packageStat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $package = $packageStat['package'] ?? null;
                                            $balances = $packageStat['balances'] ?? collect();
                                            $totalRemaining = $packageStat['total_remaining'] ?? 0;

                                            if (!$package)
                                                continue;

                                            // Calculate total services in package for progress bar
                                            $totalServices = $balances->sum(function ($b) {
                                                return $b->quantity_remaining ?? 0;
                                            });
                                            $progressPercent = $totalServices > 0 ? min(100, ($totalRemaining / $totalServices) * 100) : 0;
                                        ?>
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="card border-0 shadow-sm h-100 package-card"
                                                style="background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div class="flex-grow-1">
                                                            <h6 class="fw-bold mb-1 text-dark"><?php echo e($package->name ?? 'N/A'); ?></h6>
                                                            <span
                                                                class="badge bg-info-subtle text-info-emphasis rounded-pill px-2 py-1 small">
                                                                <?php echo e(ucfirst($package->type ?? 'fixed')); ?>

                                                            </span>
                                                        </div>
                                                        <div
                                                            class="icon-shape icon-sm bg-primary-subtle text-primary rounded-3 flex-shrink-0">
                                                            <i class="fas fa-box"></i>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="text-muted small">Total Remaining</span>
                                                            <span class="fw-bold text-success"><?php echo e($totalRemaining); ?> Services</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px;">
                                                            <div class="progress-bar bg-success" role="progressbar"
                                                                style="width: <?php echo e($progressPercent); ?>%">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="border-top pt-3">
                                                        <p class="small text-muted mb-2 fw-semibold">Remaining Services:</p>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <?php $__empty_1 = true; $__currentLoopData = $balances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $balance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                                <?php if(($balance->quantity_remaining ?? 0) > 0): ?>
                                                                    <span
                                                                        class="badge bg-light text-dark border px-2 py-1 rounded-pill small">
                                                                        <?php echo e($balance->service->name ?? 'N/A'); ?>

                                                                        <span
                                                                            class="fw-bold text-primary">(<?php echo e($balance->quantity_remaining); ?>)</span>
                                                                    </span>
                                                                <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                <span class="text-muted small">No services remaining</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 mb-4">
                                <div class="text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-medium">No Active Packages</p>
                                    <p class="small text-muted mt-2">This customer doesn't have any active package services.</p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Package Purchase History -->
                        <?php if(isset($packageData['purchases']) && is_object($packageData['purchases']) && $packageData['purchases']->count() > 0): ?>
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-shopping-cart text-info me-2"></i>Purchase History
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="border-0 py-3 px-4 fw-semibold">Date</th>
                                                <th class="border-0 py-3 px-4 fw-semibold">Package</th>
                                                <th class="border-0 py-3 px-4 fw-semibold">Invoice</th>
                                                <th class="text-end border-0 py-3 px-4 fw-semibold">Amount</th>
                                                <th class="border-0 py-3 px-4 fw-semibold">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $packageData['purchases']->flatten()->sortByDesc('created_at')->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($purchase && $purchase->package): ?>
                                                    <tr class="border-bottom">
                                                        <td class="px-4 py-3">
                                                            <div class="fw-medium text-dark"><?php echo e(format_date($purchase->created_at)); ?>

                                                            </div>
                                                            <small class="text-muted"><?php echo e(format_time($purchase->created_at)); ?></small>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <span class="fw-medium"><?php echo e($purchase->package->name ?? 'N/A'); ?></span>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <span class="badge bg-light text-dark border">
                                                                <?php echo e($purchase->sale->invoice_number ?? 'N/A'); ?>

                                                            </span>
                                                        </td>
                                                        <td class="text-end px-4 py-3">
                                                            <span class="fw-bold text-dark">
                                                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($purchase->total ?? 0, 2)); ?>

                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <?php
                                                                $paymentStatus = $purchase->sale->payment_status ?? 'pending';
                                                                $statusClass = [
                                                                    'completed' => 'success',
                                                                    'paid' => 'success',
                                                                    'pending' => 'warning',
                                                                    'cancelled' => 'danger'
                                                                ][$paymentStatus] ?? 'secondary';
                                                            ?>
                                                            <span
                                                                class="badge bg-<?php echo e($statusClass); ?>-subtle text-<?php echo e($statusClass); ?>-emphasis rounded-pill px-3 py-1">
                                                                <?php echo e(ucfirst($paymentStatus)); ?>

                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Package Usage History (Bookings) -->
                        <?php if(isset($packageData['bookings']) && is_object($packageData['bookings']) && $packageData['bookings']->count() > 0): ?>
                            <div>
                                <h6 class="fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-history text-warning me-2"></i>Usage History
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="border-0 py-3 px-4 fw-semibold">Date</th>
                                                <th class="border-0 py-3 px-4 fw-semibold">Package</th>
                                                <th class="border-0 py-3 px-4 fw-semibold">Service Used</th>
                                                <th class="border-0 py-3 px-4 fw-semibold">Staff</th>
                                                <th class="border-0 py-3 px-4 fw-semibold">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $packageData['bookings']->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($booking && $booking->package): ?>
                                                    <tr class="border-bottom">
                                                        <td class="px-4 py-3">
                                                            <div class="fw-medium text-dark"><?php echo e(format_date($booking->start_time)); ?>

                                                            </div>
                                                            <small class="text-muted"><?php echo e(format_time($booking->start_time)); ?></small>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <span class="fw-medium"><?php echo e($booking->package->name ?? 'N/A'); ?></span>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <span
                                                                class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-2">
                                                                <?php echo e($booking->service->name ?? 'N/A'); ?>

                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <span class="fw-medium"><?php echo e($booking->staff->name ?? 'Unassigned'); ?></span>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <?php
                                                                $statusClass = [
                                                                    'completed' => 'success',
                                                                    'confirmed' => 'primary',
                                                                    'pending' => 'warning',
                                                                    'cancelled' => 'danger',
                                                                    'no_show' => 'dark'
                                                                ][$booking->status] ?? 'secondary';
                                                            ?>
                                                            <span
                                                                class="badge bg-<?php echo e($statusClass); ?>-subtle text-<?php echo e($statusClass); ?>-emphasis rounded-pill px-3 py-1">
                                                                <?php echo e(ucfirst(str_replace('_', ' ', $booking->status))); ?>

                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if(
                                (!isset($packageData['stats']) || count($packageData['stats']) == 0) &&
                                (!isset($packageData['purchases']) || $packageData['purchases']->count() == 0) &&
                                (!isset($packageData['bookings']) || $packageData['bookings']->count() == 0)
                            ): ?>
                            <div class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-medium">No Package Data Available</p>
                                    <p class="small text-muted mt-2">This customer hasn't purchased or used any packages yet.
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Reviews -->
        <?php if($customer->ratings->count() > 0): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold d-flex align-items-center">
                                    <i class="fas fa-star text-warning me-2"></i>Service Reviews
                                </h5>
                                <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2">
                                    <?php echo e($customer->ratings->count()); ?> <?php echo e(Str::plural('Review', $customer->ratings->count())); ?>

                                </span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0 py-3 px-4 fw-semibold">Date</th>
                                            <th class="border-0 py-3 px-4 fw-semibold">Service</th>
                                            <th class="border-0 py-3 px-4 fw-semibold">Staff</th>
                                            <th class="border-0 py-3 px-4 fw-semibold">Rating</th>
                                            <th class="border-0 py-3 px-4 fw-semibold">Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $customer->ratings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rating): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="border-bottom">
                                                <td class="px-4 py-3">
                                                    <div class="fw-medium text-dark"><?php echo e(format_date($rating->created_at)); ?></div>
                                                    <small class="text-muted"><?php echo e(format_time($rating->created_at)); ?></small>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="fw-medium"><?php echo e($rating->service->name ?? 'N/A'); ?></span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="fw-medium"><?php echo e($rating->staff->name ?? 'N/A'); ?></span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-warning me-2">
                                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                                <?php if($i <= $rating->rating): ?>
                                                                    <i class="fas fa-star"></i>
                                                                <?php else: ?>
                                                                    <i class="far fa-star"></i>
                                                                <?php endif; ?>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <span
                                                            class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2">
                                                            <?php echo e($rating->rating); ?>/5
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <?php if($rating->comment): ?>
                                                        <p class="mb-0 text-muted small fst-italic">"<?php echo e($rating->comment); ?>"</p>
                                                    <?php else: ?>
                                                        <span class="text-muted small">-</span>
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
            </div>
        <?php endif; ?>

        <!-- Bookings and Activity -->
        <div class="row g-4">
            <!-- All Bookings -->
            <div class="col-12 col-xl-8">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <h5 class="mb-0 fw-bold d-flex align-items-center">
                                <i class="fas fa-calendar-check text-primary me-2"></i>All Bookings
                            </h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fas fa-search text-muted"></i></span>
                                    <input type="text" id="bookingSearch" class="form-control border-start-0"
                                        placeholder="Search bookings...">
                                </div>
                                <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="no_show">No Show</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="sticky-top bg-light" style="top: 0; z-index: 1;">
                                    <tr>
                                        <th class="border-0 py-3 px-4 fw-semibold">Date & Time</th>
                                        <th class="border-0 py-3 px-4 fw-semibold">Service</th>
                                        <th class="border-0 py-3 px-4 fw-semibold">Staff</th>
                                        <th class="border-0 py-3 px-4 fw-semibold">Duration</th>
                                        <th class="text-end border-0 py-3 px-4 fw-semibold">Amount</th>
                                        <th class="border-0 py-3 px-4 fw-semibold">Status</th>
                                        <th class="text-end border-0 py-3 px-4 fw-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="bookingsTable">
                                    <?php $__empty_1 = true; $__currentLoopData = $customer->bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                                    <tr data-status="<?php echo e($booking->status); ?>"
                                                                        data-search="<?php echo e(strtolower($booking->service->name ?? '')); ?> <?php echo e(strtolower($booking->staff->name ?? '')); ?> <?php echo e(strtolower($booking->status)); ?>"
                                                                        class="border-bottom">
                                                                        <td class="px-4 py-3">
                                                                            <div class="fw-medium text-dark"><?php echo e(format_date($booking->start_time)); ?></div>
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-clock me-1"></i><?php echo e(format_time($booking->start_time)); ?>

                                                                                <?php if($booking->end_time): ?>
                                                                                    - <?php echo e(format_time($booking->end_time)); ?>

                                                                                <?php endif; ?>
                                                                            </small>
                                                                        </td>
                                                                        <td class="px-4 py-3">
                                                                            <div class="fw-medium text-dark"><?php echo e($booking->service->name ?? 'N/A'); ?></div>
                                                                            <?php if($booking->package): ?>
                                                                                <div class="mt-1">
                                                                                    <span
                                                                                        class="badge bg-purple-subtle text-purple-emphasis rounded-pill px-2 py-1"
                                                                                        style="background-color: #f3e8ff !important; color: #7c3aed !important;">
                                                                                        <i class="fas fa-box me-1"></i><?php echo e($booking->package->name); ?>

                                                                                    </span>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <?php if($booking->service?->category): ?>
                                                                                <small class="text-muted">
                                                                                    <i class="fas fa-tag me-1"></i><?php echo e($booking->service->category->name); ?>

                                                                                </small>
                                                                            <?php endif; ?>
                                                                        </td>
                                                                        <td class="px-4 py-3">
                                                                            <span class="fw-medium"><?php echo e($booking->staff->name ?? 'Unassigned'); ?></span>
                                                                        </td>
                                                                        <td class="px-4 py-3">
                                                                            <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2">
                                                                                <?php echo e($booking->service?->duration ? $booking->service->duration . ' mins' : '-'); ?>

                                                                            </span>
                                                                        </td>
                                                                        <td class="text-end px-4 py-3">
                                                                            <span class="fw-bold text-dark">
                                                                                <?php echo e(config('settings.currency_symbol', '$')); ?><?php echo e(number_format($booking->amount, 2)); ?>

                                                                            </span>
                                                                        </td>
                                                                        <td class="px-4 py-3">
                                                                            <?php
                                                                                $statusClass = [
                                                                                    'completed' => 'success',
                                                                                    'confirmed' => 'primary',
                                                                                    'pending' => 'warning',
                                                                                    'cancelled' => 'danger',
                                                                                    'no_show' => 'dark'
                                                                                ][$booking->status] ?? 'secondary';
                                                                            ?>
                                                                            <span
                                                                                class="badge bg-<?php echo e($statusClass); ?>-subtle text-<?php echo e($statusClass); ?>-emphasis rounded-pill px-3 py-1">
                                                                                <?php echo e(ucfirst(str_replace('_', ' ', $booking->status))); ?>

                                                                            </span>
                                                                        </td>
                                                                        <td class="text-end px-4 py-3">
                                                                            <div class="btn-group">
                                                                                <a href="#"
                                                                                    class="btn btn-sm btn-outline-primary view-booking-btn rounded-start-pill"
                                                                                    data-bs-toggle="tooltip" title="View Details" data-booking="<?php echo e(json_encode([
                                            'id' => $booking->id,
                                            'customer_id' => $booking->customer_id,
                                            'customer' => $customer->name,
                                            'service_id' => $booking->service_id,
                                            'service' => $booking->service->name ?? 'N/A',
                                            'staff_id' => $booking->staff_id,
                                            'staff' => $booking->staff->name ?? 'Not assigned',
                                            'start_time' => $booking->start_time,
                                            'end_time' => $booking->end_time,
                                            'duration' => $booking->duration ?? 0,
                                            'status' => $booking->status,
                                            'notes' => $booking->notes ?? '',
                                        ])); ?>">
                                                                                    <i class="fas fa-eye"></i>
                                                                                </a>
                                                                                <a href="<?php echo e(route('admin.bookings.edit', $booking)); ?>"
                                                                                    class="btn btn-sm btn-outline-secondary rounded-end-pill"
                                                                                    data-bs-toggle="tooltip" title="Edit">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="mb-3">
                                                    <i class="fas fa-calendar-xmark fa-3x text-muted opacity-50"></i>
                                                </div>
                                                <h5 class="fw-bold">No bookings found</h5>
                                                <p class="text-muted">This customer doesn't have any booking history yet.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-3">
                        <small class="text-muted d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            Showing <?php echo e($customer->bookings->count()); ?> of <?php echo e($stats['total_bookings']); ?>

                            <?php echo e(Str::plural('booking', $stats['total_bookings'])); ?>

                        </small>
                    </div>
                </div>
            </div>

            <!-- Customer Notes & Activity -->
            <div class="col-12 col-xl-4">
                <!-- Customer Notes -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold d-flex align-items-center">
                                <i class="fas fa-sticky-note text-primary me-2"></i>Customer Notes
                            </h5>
                            <button class="btn btn-sm btn-primary rounded-pill" data-bs-toggle="modal"
                                data-bs-target="#addNoteModal">
                                <i class="fas fa-plus me-1"></i> Add Note
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="customer-notes">
                            <?php if($customer->notes): ?>
                                <div class="p-3 bg-light rounded-3 border-0">
                                    <p class="mb-2 fw-medium"><?php echo e($customer->notes); ?></p>
                                    <div class="small text-muted d-flex align-items-center">
                                        <i class="fas fa-clock me-1"></i>
                                        Last updated <?php echo e($customer->updated_at->diffForHumans()); ?>

                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-sticky-note fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0 fw-medium">No notes available</p>
                                        <p class="small text-muted mt-2">Add a note to keep track of important information</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold d-flex align-items-center">
                            <i class="fas fa-history text-info me-2"></i>Recent Activity
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="timeline">
                            <?php
                                $activities = collect([]);

                                // Add bookings as activities
                                $activities = $activities->merge($customer->bookings->map(function ($booking) {
                                    return (object) [
                                        'type' => 'booking',
                                        'title' => 'New Booking',
                                        'description' => 'Booked ' . ($booking->service->name ?? 'service'),
                                        'date' => $booking->created_at,
                                        'icon' => 'calendar-check',
                                        'color' => 'primary'
                                    ];
                                }));

                                // Add other activities here (e.g., notes, updates, etc.)

                                // Sort activities by date
                                $activities = $activities->sortByDesc('date')->take(5);
                            ?>

                            <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="timeline-item">
                                    <div
                                        class="timeline-icon icon-item icon-item-sm bg-<?php echo e($activity->color); ?>-subtle text-<?php echo e($activity->color); ?>-emphasis shadow-sm">
                                        <i class="fas fa-<?php echo e($activity->icon); ?>"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1 fw-semibold"><?php echo e($activity->title); ?></h6>
                                        <p class="mb-1 small text-muted"><?php echo e($activity->description); ?></p>
                                        <div class="small text-muted d-flex align-items-center">
                                            <i class="fas fa-clock me-1"></i><?php echo e($activity->date->diffForHumans()); ?>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0 fw-medium">No recent activity</p>
                                        <p class="small text-muted mt-2">Activity will appear here</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="fas fa-sticky-note me-2"></i>Add Note
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="updateNotesForm" action="<?php echo e(route('admin.customers.update-notes', $customer)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="note" class="form-label fw-semibold">Note</label>
                            <textarea class="form-control rounded-3" id="note" name="notes" rows="4"
                                placeholder="Enter your note here..."><?php echo e($customer->notes ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-save me-1"></i>Save Note
                        </button>
                    </div>
                </form>

                <?php $__env->startPush('scripts'); ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const form = document.getElementById('updateNotesForm');

                            if (form) {
                                form.addEventListener('submit', async function (e) {
                                    e.preventDefault();

                                    const formData = new FormData(form);
                                    const submitButton = form.querySelector('button[type="submit"]');
                                    const originalButtonText = submitButton.innerHTML;

                                    try {
                                        submitButton.disabled = true;
                                        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

                                        const response = await fetch(form.action, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json',
                                                'X-HTTP-Method-Override': 'PUT'
                                            },
                                            body: formData
                                        });

                                        const data = await response.json();

                                        if (response.ok) {
                                            // Update the notes display in real-time
                                            const notesContainer = document.querySelector('.customer-notes');
                                            if (notesContainer) {
                                                notesContainer.innerHTML = formData.get('notes') ||
                                                    '<div class="text-muted">No notes available</div>';
                                            }

                                            // Show success message using SweetAlert since we removed the toast
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success',
                                                text: data.message || 'Notes updated successfully',
                                                timer: 2000,
                                                showConfirmButton: false
                                            });

                                            // Close the modal after a short delay
                                            setTimeout(() => {
                                                const modal = bootstrap.Modal.getInstance(document.getElementById('addNoteModal'));
                                                if (modal) modal.hide();
                                            }, 1000);
                                        } else {
                                            throw new Error(data.message || 'Failed to update notes');
                                        }
                                    } catch (error) {
                                        console.error('Error:', error);
                                        // Show error using SweetAlert
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: error.message || 'An error occurred while updating notes',
                                            confirmButtonText: 'OK'
                                        });
                                    } finally {
                                        submitButton.disabled = false;
                                        submitButton.innerHTML = originalButtonText;
                                    }
                                });
                            }
                        });
                    </script>
                <?php $__env->stopPush(); ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Medical Notes Modal -->
    <div class="modal fade" id="editMedicalNotesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="fas fa-notes-medical me-2"></i>
                        Medical / Allergy Notes
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateMedicalNotesForm" action="<?php echo e(route('admin.customers.update-notes', $customer)); ?>"
                    method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 rounded-3 mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Important:</strong> Record any allergies, medical conditions, or special precautions
                            staff should be aware of.
                        </div>
                        <div class="mb-3">
                            <label for="medical_notes" class="form-label fw-semibold">Medical / Allergy Notes</label>
                            <textarea class="form-control rounded-3" id="medical_notes" name="medical_notes" rows="5"
                                placeholder="e.g., Allergic to certain hair products, skin sensitivity, etc."><?php echo e($customer->medical_notes ?? ''); ?></textarea>
                            <small class="text-muted d-flex align-items-center mt-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                This will be displayed prominently to alert staff
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4">
                            <i class="fas fa-save me-1"></i> Save Medical Notes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="fas fa-calendar-check me-2"></i>Booking Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border-0 mb-3">
                                <h6 class="text-muted mb-2 small fw-semibold text-uppercase">Service</h6>
                                <p id="booking-service" class="mb-0 fw-medium"></p>
                            </div>
                            <div class="p-3 bg-light rounded-3 border-0 mb-3">
                                <h6 class="text-muted mb-2 small fw-semibold text-uppercase">Date & Time</h6>
                                <p id="booking-datetime" class="mb-0 fw-medium"></p>
                            </div>
                            <div class="p-3 bg-light rounded-3 border-0">
                                <h6 class="text-muted mb-2 small fw-semibold text-uppercase">Duration</h6>
                                <p id="booking-duration" class="mb-0 fw-medium"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border-0 mb-3">
                                <h6 class="text-muted mb-2 small fw-semibold text-uppercase">Customer</h6>
                                <p id="booking-customer" class="mb-0 fw-medium"></p>
                            </div>
                            <div class="p-3 bg-light rounded-3 border-0 mb-3">
                                <h6 class="text-muted mb-2 small fw-semibold text-uppercase">Assigned Staff</h6>
                                <p id="booking-staff" class="mb-0 fw-medium"></p>
                            </div>
                            <div class="p-3 bg-light rounded-3 border-0">
                                <h6 class="text-muted mb-2 small fw-semibold text-uppercase">Status</h6>
                                <p id="booking-status" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-light rounded-3 border-0">
                        <h6 class="text-muted mb-2 small fw-semibold text-uppercase">Notes</h6>
                        <p id="booking-notes" class="mb-0 fw-medium">No notes available</p>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="edit-booking-btn" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-edit me-1"></i> Edit Booking
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        }

        .icon-shape {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            transition: all 0.3s ease;
        }

        .icon-shape.icon-sm {
            width: 2.5rem;
            height: 2.5rem;
            font-size: 0.875rem;
        }

        .icon-shape.icon-lg {
            width: 3.5rem;
            height: 3.5rem;
            font-size: 1.25rem;
        }

        .stat-card {
            transition: all 0.3s ease;
            border-top: 3px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg) !important;
        }

        .stat-card-primary {
            border-top-color: #667eea;
        }

        .stat-card-success {
            border-top-color: #10b981;
        }

        .stat-card-danger {
            border-top-color: #ef4444;
        }

        .stat-card-warning {
            border-top-color: #f59e0b;
        }

        .stat-card-info {
            border-top-color: #3b82f6;
        }

        .customer-profile-card {
            background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);
        }

        .info-item {
            transition: all 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow) !important;
        }

        .membership-card {
            border: 1px solid rgba(102, 126, 234, 0.2);
        }

        .timeline {
            position: relative;
            padding-left: 2.5rem;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 2rem;
            padding-left: 2rem;
            border-left: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .timeline-item:hover {
            border-left-color: #667eea;
        }

        .timeline-item:last-child {
            border-left-color: transparent;
            padding-bottom: 0;
        }

        .timeline-icon {
            position: absolute;
            left: -1.25rem;
            top: 0;
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 3px solid #fff;
            transition: all 0.3s ease;
        }

        .timeline-item:hover .timeline-icon {
            transform: scale(1.1);
            box-shadow: var(--shadow);
        }

        .timeline-content {
            padding: 0.5rem 0 0.5rem 1rem;
        }

        .avatar {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-title {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: var(--primary-gradient);
            border-radius: 50%;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .avatar-title:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-lg);
        }

        .booking-row {
            transition: all 0.2s ease;
        }

        .booking-row:hover {
            background-color: #f8f9fa !important;
            transform: translateX(5px);
        }

        .package-card {
            transition: all 0.3s ease;
            border-left: 3px solid #667eea;
        }

        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg) !important;
            border-left-color: #764ba2;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow) !important;
        }

        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .badge {
            font-weight: 500;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        @media (max-width: 768px) {
            .stat-card {
                margin-bottom: 1rem;
            }

            .timeline {
                padding-left: 1.5rem;
            }
        }
    </style>

    <!-- Custom JS for the page -->
    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Handle medical notes form submission
                const medicalNotesForm = document.getElementById('updateMedicalNotesForm');
                if (medicalNotesForm) {
                    medicalNotesForm.addEventListener('submit', async function (e) {
                        e.preventDefault();

                        const formData = new FormData(medicalNotesForm);
                        const submitButton = medicalNotesForm.querySelector('button[type="submit"]');
                        const originalButtonText = submitButton.innerHTML;

                        try {
                            submitButton.disabled = true;
                            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

                            const response = await fetch(medicalNotesForm.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-HTTP-Method-Override': 'PUT'
                                },
                                body: formData
                            });

                            const data = await response.json();

                            if (response.ok) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Medical notes updated successfully',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Reload page to show updated medical notes
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                throw new Error(data.message || 'Failed to update medical notes');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'An error occurred while updating medical notes',
                                confirmButtonText: 'OK'
                            });
                        } finally {
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalButtonText;
                        }
                    });
                }

                // Handle view booking details
                document.addEventListener('click', function (e) {
                    const viewBtn = e.target.closest('.view-booking-btn');
                    if (viewBtn) {
                        e.preventDefault();
                        const bookingData = JSON.parse(viewBtn.getAttribute('data-booking'));

                        // Format date and time
                        const startTime = new Date(bookingData.start_time);
                        const endTime = new Date(bookingData.end_time);
                        const options = {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        };
                        const dateTimeStr = `${startTime.toLocaleDateString('en-US', options)} - ${endTime.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`;

                        // Update modal content
                        document.getElementById('booking-service').textContent = bookingData.service || 'N/A';
                        document.getElementById('booking-datetime').textContent = dateTimeStr;
                        document.getElementById('booking-duration').textContent = `${bookingData.duration || 0} minutes`;
                        document.getElementById('booking-customer').textContent = bookingData.customer || 'N/A';
                        document.getElementById('booking-staff').textContent = bookingData.staff || 'Not assigned';

                        // Format status with badge
                        const statusElement = document.getElementById('booking-status');
                        statusElement.className = 'badge ' + getStatusBadgeClass(bookingData.status);
                        statusElement.textContent = bookingData.status.charAt(0).toUpperCase() + bookingData.status.slice(1);

                        // Set notes or show default message
                        const notesElement = document.getElementById('booking-notes');
                        if (bookingData.notes && bookingData.notes.trim() !== '') {
                            notesElement.textContent = bookingData.notes;
                            notesElement.previousElementSibling.style.display = 'block';
                        } else {
                            notesElement.textContent = 'No notes available';
                            notesElement.previousElementSibling.style.display = 'none';
                        }

                        // Set edit link with the correct route format
                        const editUrl = '<?php echo e(url('/')); ?>/admin/bookings/' + bookingData.id + '/edit';
                        document.getElementById('edit-booking-btn').href = editUrl;

                        // Show the modal
                        const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
                        modal.show();
                    }
                });

                // Helper function to get badge class based on status
                function getStatusBadgeClass(status) {
                    switch (status) {
                        case 'confirmed':
                            return 'bg-success';
                        case 'pending':
                            return 'bg-warning text-dark';
                        case 'cancelled':
                            return 'bg-danger';
                        case 'completed':
                            return 'bg-info';
                        default:
                            return 'bg-secondary';
                    }
                }

                // Get filter elements
                const searchInput = document.getElementById('bookingSearch');
                const statusFilter = document.getElementById('statusFilter');
                const bookingsTable = document.getElementById('bookingsTable');
                const bookingCountElement = document.getElementById('bookingCount');

                // Get all booking rows
                const bookingRows = bookingsTable ? Array.from(bookingsTable.querySelectorAll('tr[data-status]')) : [];

                // Function to update the visible booking count
                function updateVisibleBookingCount() {
                    if (bookingCountElement) {
                        const visibleRows = bookingRows.filter(row =>
                            row.style.display !== 'none' && row.querySelector('td')
                        ).length;
                        bookingCountElement.textContent = visibleRows;
                    }
                }

                // Function to filter bookings
                function filterBookings() {
                    const searchTerm = searchInput.value.toLowerCase();
                    const statusValue = statusFilter.value.toLowerCase();
                    let visibleCount = 0;

                    bookingRows.forEach(row => {
                        if (row.querySelector('td')) { // Skip the empty state row
                            const status = row.getAttribute('data-status');
                            const searchText = row.getAttribute('data-search') || '';

                            const matchesSearch = searchText.includes(searchTerm);
                            const matchesStatus = !statusValue || status === statusValue;

                            if (matchesSearch && matchesStatus) {
                                row.style.display = '';
                                visibleCount++;
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    });

                    // Update the visible count
                    if (bookingCountElement) {
                        bookingCountElement.textContent = visibleCount;
                    }

                    // Show/hide empty state
                    const emptyState = document.getElementById('emptyBookingsState');
                    if (emptyState) {
                        if (visibleCount === 0) {
                            emptyState.style.display = '';
                        } else {
                            emptyState.style.display = 'none';
                        }
                    }
                }

                // Add event listeners
                if (searchInput) searchInput.addEventListener('input', filterBookings);
                if (statusFilter) statusFilter.addEventListener('change', filterBookings);

                // Initial filter to handle any pre-filled values
                filterBookings();
            });

            // Toggle Customer Status
            function toggleCustomerStatus(customerId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to change the status of this customer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // This handles the salon slug prefix automatically
                        const url = "<?php echo e(route('admin.customers.toggle-status', ':id')); ?>".replace(':id', customerId);

                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire(
                                        'Updated!',
                                        data.message,
                                        'success'
                                    ).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        data.message || 'Something went wrong.',
                                        'error'
                                    );
                                }
                            })
                            .catch(error => {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong.',
                                    'error'
                                );
                            });
                    }
                });
            }

            // Delete Customer
            function deleteCustomer(customerId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = "<?php echo e(route('admin.customers.destroy', ':id')); ?>".replace(':id', customerId);

                        fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire(
                                        'Deleted!',
                                        data.message,
                                        'success'
                                    ).then(() => {
                                        window.location.href = "<?php echo e(route('admin.customers.index')); ?>";
                                    });
                                } else {
                                    Swal.fire(
                                        'Cannot Delete',
                                        data.message || 'Something went wrong.',
                                        'error'
                                    );
                                }
                            })
                            .catch(error => {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong.',
                                    'error'
                                );
                            });
                    }
                });
            }

        </script>
    <?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\customers\details.blade.php ENDPATH**/ ?>