<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 fw-bold text-gray-900">Edit Plan: <?php echo e($plan->name); ?></h1>
                <p class="text-muted mb-0">Update subscription plan details and limits</p>
            </div>
            <a href="<?php echo e(route('admin.plans.index')); ?>" class="btn btn-light border">
                <i class="fas fa-arrow-left me-2"></i>Back to Plans
            </a>
        </div>

        <form method="POST" action="<?php echo e(route('admin.plans.update', $plan->id)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="row g-4">
                <!-- Main Form -->
                <div class="col-lg-8">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-3" id="planTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic"
                                type="button" role="tab">
                                <i class="fas fa-info-circle me-2"></i>Basic Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="features-tab" data-bs-toggle="tab" data-bs-target="#features"
                                type="button" role="tab">
                                <i class="fas fa-check-double me-2"></i>Features
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="limits-tab" data-bs-toggle="tab" data-bs-target="#limits"
                                type="button" role="tab">
                                <i class="fas fa-sliders-h me-2"></i>Limits
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="planTabsContent">
                        <!-- Basic Info Tab -->
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Plan Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control form-control-lg <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                id="name" name="name" value="<?php echo e(old('name', $plan->name)); ?>"
                                                placeholder="e.g., Premium Plan" required>
                                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Slug <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control form-control-lg <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                id="slug" name="slug" value="<?php echo e(old('slug', $plan->slug)); ?>"
                                                placeholder="premium-plan" required>
                                            <small class="text-muted">Auto-generated from name</small>
                                            <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Business Type <span
                                                    class="text-danger">*</span></label>
                                            <select name="business_type" class="form-select form-select-lg <?php $__errorArgs = ['business_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                                <option value="both" <?php echo e(old('business_type', $plan->business_type) == 'both' ? 'selected' : ''); ?>>Both (Salon & Barber)</option>
                                                <option value="salon" <?php echo e(old('business_type', $plan->business_type) == 'salon' ? 'selected' : ''); ?>>Salon Only</option>
                                                <option value="barber" <?php echo e(old('business_type', $plan->business_type) == 'barber' ? 'selected' : ''); ?>>Barber Only</option>
                                            </select>
                                            <?php $__errorArgs = ['business_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Price (<?php echo e(system_currency_symbol()); ?>)
                                                <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg">
                                                <span
                                                    class="input-group-text bg-light fw-bold"><?php echo e(system_currency_symbol()); ?></span>
                                                <input type="number" step="0.01"
                                                    class="form-control <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="price"
                                                    name="price" value="<?php echo e(old('price', $plan->price)); ?>" required>
                                            </div>
                                            <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Duration (Days) <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-calendar"></i></span>
                                                <input type="number"
                                                    class="form-control <?php $__errorArgs = ['duration_in_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="duration_in_days" name="duration_in_days"
                                                    value="<?php echo e(old('duration_in_days', $plan->duration_in_days)); ?>"
                                                    placeholder="30" required>
                                            </div>
                                            <?php $__errorArgs = ['duration_in_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Trial Duration (Days)</label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text bg-light"><i class="fas fa-clock"></i></span>
                                                <input type="number"
                                                    class="form-control <?php $__errorArgs = ['trial_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="trial_days" name="trial_days"
                                                    value="<?php echo e(old('trial_days', $plan->trial_days)); ?>" placeholder="14">
                                            </div>
                                            <small class="text-muted">Days allowed for free trial (Default: 14)</small>
                                            <?php $__errorArgs = ['trial_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="3"
                                                placeholder="Brief description of this plan"><?php echo e(old('description', $plan->description)); ?></textarea>
                                        </div>

                                        <div class="col-12">
                                            <div class="d-flex gap-4">
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input" id="is_active"
                                                        name="is_active" <?php echo e(old('is_active', $plan->is_active) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label fw-semibold" for="is_active">
                                                        <i class="fas fa-check-circle text-white me-2"></i>Active
                                                    </label>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input" id="is_popular"
                                                        name="is_popular" <?php echo e(old('is_popular', $plan->is_popular) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label fw-semibold" for="is_popular">
                                                        <i class="fas fa-star text-warning me-2"></i>Mark as Popular
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Features Tab -->
                        <div class="tab-pane fade" id="features" role="tabpanel">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="position-relative">
                                        <div class="input-group input-group-lg mb-3">
                                            <span class="input-group-text bg-light"><i class="fas fa-plus"></i></span>
                                            <input type="text" class="form-control feature-input" id="featureInput"
                                                placeholder="Start typing to see suggestions..." autocomplete="off">
                                            <button type="button" class="btn btn-primary" onclick="addFeature()">
                                                <i class="fas fa-plus me-2"></i>Add Feature
                                            </button>
                                        </div>
                                        <!-- Quick Add Feature Pills -->
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-2"><i class="fas fa-magic me-1 text-warning"></i>Quick Add Popular Features:</small>
                                            <div class="d-flex flex-wrap gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold" onclick="quickAddFeature('AI Insights & Automation')">
                                                    <i class="fas fa-brain text-warning me-1"></i>+ AI Insights & Automation
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="quickAddFeature('Booking System')">+ Booking System</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="quickAddFeature('POS System')">+ POS System</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="quickAddFeature('Inventory Management')">+ Inventory Management</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="quickAddFeature('Staff Management')">+ Staff Management</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="quickAddFeature('Customer Management')">+ Customer Management</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="quickAddFeature('Analytics & Reports')">+ Analytics & Reports</button>
                                            </div>
                                        </div>

                                        <!-- Suggestions Dropdown -->
                                        <div id="suggestions" class="list-group position-absolute w-100"
                                            style="z-index: 1000; display: none; max-height: 300px; overflow-y: auto;">
                                        </div>
                                    </div>
                                    <input type="hidden" name="features" id="features-json">
                                    <div id="features-list" class="d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Limits Tab -->
                        <div class="tab-pane fade" id="limits" role="tabpanel">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="alert alert-info border-0 mb-4">
                                        <i class="fas fa-info-circle me-2"></i>Leave fields empty for unlimited access
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-3" id="container_max_users">
                                            <label class="form-label fw-semibold small text-uppercase">
                                                <i class="fas fa-users text-primary me-2"></i>Max Staff
                                            </label>
                                            <input type="number" class="form-control" id="max_users" name="max_users"
                                                value="<?php echo e(old('max_users', $plan->max_users > 0 ? $plan->max_users : '')); ?>"
                                                placeholder="∞">
                                        </div>

                                        <div class="col-md-3" id="container_max_branches">
                                            <label class="form-label fw-semibold small text-uppercase">
                                                <i class="fas fa-store text-info me-2"></i>Max Branches
                                            </label>
                                            <input type="number" class="form-control" id="max_branches" name="max_branches"
                                                value="<?php echo e(old('max_branches', $plan->max_branches > 0 ? $plan->max_branches : '')); ?>"
                                                placeholder="∞">
                                        </div>

                                        <div class="col-md-3" id="container_max_customers">
                                            <label class="form-label fw-semibold small text-uppercase">
                                                <i class="fas fa-user-friends text-secondary me-2"></i>Max Customers
                                            </label>
                                            <input type="number" class="form-control" id="max_customers"
                                                name="limits[max_customers]"
                                                value="<?php echo e(old('limits.max_customers', isset($plan->limits['max_customers']) && $plan->limits['max_customers'] > 0 ? $plan->limits['max_customers'] : '')); ?>"
                                                placeholder="∞">
                                        </div>

                                        <div class="col-md-3" id="container_max_services">
                                            <label class="form-label fw-semibold small text-uppercase">
                                                <i class="fas fa-cut text-white me-2"></i>Max Services
                                            </label>
                                            <input type="number" class="form-control" id="max_services"
                                                name="limits[max_services]"
                                                value="<?php echo e(old('limits.max_services', isset($plan->limits['max_services']) && $plan->limits['max_services'] > 0 ? $plan->limits['max_services'] : '')); ?>"
                                                placeholder="∞">
                                        </div>

                                        <div class="col-md-3" id="container_max_products">
                                            <label class="form-label fw-semibold small text-uppercase">
                                                <i class="fas fa-box text-warning me-2"></i>Max Products
                                            </label>
                                            <input type="number" class="form-control" id="max_products"
                                                name="limits[max_products]"
                                                value="<?php echo e(old('limits.max_products', isset($plan->limits['max_products']) && $plan->limits['max_products'] > 0 ? $plan->limits['max_products'] : '')); ?>"
                                                placeholder="∞">
                                        </div>

                                        <div class="col-md-3" id="container_max_packages">
                                            <label class="form-label fw-semibold small text-uppercase">
                                                <i class="fas fa-gift text-purple me-2"></i>Max Packages
                                            </label>
                                            <input type="number" class="form-control" id="max_packages"
                                                name="limits[max_packages]"
                                                value="<?php echo e(old('limits.max_packages', isset($plan->limits['max_packages']) && $plan->limits['max_packages'] > 0 ? $plan->limits['max_packages'] : '')); ?>"
                                                placeholder="∞">
                                        </div>

                                        <div class="col-md-3" id="container_max_memberships">
                                            <label class="form-label fw-semibold small text-uppercase">
                                                <i class="fas fa-id-card text-teal me-2"></i>Max Memberships
                                            </label>
                                            <input type="number" class="form-control" id="max_memberships"
                                                name="limits[max_memberships]"
                                                value="<?php echo e(old('limits.max_memberships', isset($plan->limits['max_memberships']) && $plan->limits['max_memberships'] > 0 ? $plan->limits['max_memberships'] : '')); ?>"
                                                placeholder="∞">
                                        </div>

                                        <div class="col-md-3" id="container_max_bookings_per_month">
                                            <label class="form-label fw-semibold small text-uppercase">
                                                <i class="fas fa-calendar-check text-danger me-2"></i>Bookings/Month
                                            </label>
                                            <input type="number" class="form-control" id="max_bookings_per_month"
                                                name="limits[max_bookings_per_month]"
                                                value="<?php echo e(old('limits.max_bookings_per_month', isset($plan->limits['max_bookings_per_month']) && $plan->limits['max_bookings_per_month'] > 0 ? $plan->limits['max_bookings_per_month'] : '')); ?>"
                                                placeholder="∞">
                                        </div>

                                        <div class="col-md-3" id="container_max_guest_bookings_per_month">
                                            <label class="form-label fw-semibold small text-uppercase">
                                                <i class="fas fa-user-clock text-muted me-2"></i>Guest Bookings/Month
                                            </label>
                                            <input type="number" class="form-control" id="max_guest_bookings_per_month"
                                                name="limits[max_guest_bookings_per_month]"
                                                value="<?php echo e(old('limits.max_guest_bookings_per_month', isset($plan->limits['max_guest_bookings_per_month']) && $plan->limits['max_guest_bookings_per_month'] > 0 ? $plan->limits['max_guest_bookings_per_month'] : '')); ?>"
                                                placeholder="∞">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- End tab-content -->

                    <!-- Action Buttons -->
                    <div class="mt-4 d-flex gap-3">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-save me-2"></i>Update Plan
                        </button>
                        <a href="<?php echo e(route('admin.plans.index')); ?>" class="btn btn-light border btn-lg px-5">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div> <!-- End col-lg-8 -->

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-lightbulb text-warning me-2"></i>Quick Add Features
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Click to quickly add common features:</p>
                            <div class="d-flex flex-wrap gap-2">
                                <?php
                                    $allFeatures = [
                                        ['name' => 'POS System', 'module' => 'pos', 'icon' => 'fa-cash-register'],
                                        ['name' => 'Inventory Management', 'module' => 'inventory', 'icon' => 'fa-boxes'],
                                        ['name' => 'Staff Management', 'module' => 'staff', 'icon' => 'fa-users-cog'],
                                        ['name' => 'Customer Management', 'module' => 'customers', 'icon' => 'fa-user-friends'],
                                        ['name' => 'Booking System', 'module' => 'appointments', 'icon' => 'fa-calendar-alt'],
                                        ['name' => 'Service Management', 'module' => 'services', 'icon' => 'fa-cut'],
                                        ['name' => 'Analytics & Reports', 'module' => 'reports', 'icon' => 'fa-chart-line'],
                                        ['name' => 'Multi-Branch Support', 'module' => 'branches', 'icon' => 'fa-code-branch'],
                                        ['name' => 'Packages', 'module' => 'packages', 'icon' => 'fa-gift'],
                                        ['name' => 'Memberships', 'module' => 'memberships', 'icon' => 'fa-id-card'],
                                        ['name' => 'Commission Management', 'module' => 'commissions', 'icon' => 'fa-percent'],
                                        ['name' => 'Role Management', 'module' => 'roles', 'icon' => 'fa-user-shield'],
                                        ['name' => 'SMS Notifications', 'module' => 'sms', 'icon' => 'fa-sms'],
                                        ['name' => 'WhatsApp Notifications', 'module' => 'whatsapp', 'icon' => 'fa-whatsapp'],
                                        ['name' => 'Email Notifications', 'module' => 'marketing', 'icon' => 'fa-envelope'],
                                        ['name' => 'AI Insights & Automation', 'module' => 'ai', 'icon' => 'fa-brain'],
                                    ];
                                ?>

                                <?php $__currentLoopData = $allFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!$feature['module'] || empty($enabledModules) || !empty($enabledModules[$feature['module']])): ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill"
                                            onclick="quickAddFeature('<?php echo e($feature['name']); ?>')">
                                            <i class="fas <?php echo e($feature['icon']); ?> me-1"></i><?php echo e($feature['name']); ?>

                                        </button>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm bg-light mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-info-circle text-info me-2"></i>Plan Info
                            </h6>
                            <p class="mb-2"><strong>Active Subscriptions:</strong> <?php echo e($plan->subscriptions()->count()); ?></p>
                            <p class="mb-2"><strong>Created:</strong> <?php echo e($plan->created_at->format('M d, Y')); ?></p>
                            <?php if($plan->subscriptions()->count() > 0): ?>
                                <div class="alert alert-warning small mb-0 mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>This plan has active subscriptions. Changes
                                    will affect existing subscribers.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // Load existing features from plan
            let features = [];
            try {
                const planFeatures = <?php echo json_encode($plan->features ?? [], 15, 512) ?>;


                if (typeof planFeatures === 'string') {
                    // If it's a JSON string, parse it
                    features = JSON.parse(planFeatures);
                } else if (Array.isArray(planFeatures)) {
                    // If it's already an array, use it
                    features = planFeatures;
                } else if (planFeatures && typeof planFeatures === 'object') {
                    // If it's an object, convert values to array
                    features = Object.values(planFeatures);
                }

                // Ensure it's an array
                if (!Array.isArray(features)) {
                    features = [];
                }


            } catch (e) {

                features = [];
            }

            // Common features list for autocomplete
            // We start with all possible features, then filter based on enabled modules
            const allFeatures = [
                { name: 'POS System', module: 'pos' },
                { name: 'Inventory Management', module: 'inventory' },
                { name: 'Staff Management', module: 'staff' },
                { name: 'Customer Management', module: 'customers' },
                { name: 'Booking System', module: 'appointments' },
                { name: 'Service Management', module: 'services' },
                { name: 'Analytics & Reports', module: 'reports' },
                { name: 'Multi-Branch Support', module: 'branches' },
                { name: 'Packages', module: 'packages' },
                { name: 'Memberships', module: 'memberships' },
                { name: 'Commission Management', module: 'commissions' },
                { name: 'Role Management', module: 'roles' },
                { name: 'Email Notifications', module: 'marketing' },
                { name: 'SMS Notifications', module: 'sms' },
                { name: 'WhatsApp Notifications', module: 'whatsapp' },
                { name: 'AI Insights & Automation', module: 'ai' }
            ];

            // Get enabled modules from backend
            const enabledModules = <?php echo json_encode($enabledModules ?? [], 15, 512) ?>;

            // Filter features
            const commonFeatures = allFeatures.filter(feature => {
                // If feature has no specific module, it's always available
                if (!feature.module) return true;

                // If enabledModules is empty (array), it might mean all are enabled or none.
                // But usually SettingsService returns empty array if nothing set.
                // If it's an object/assoc array, we check keys.
                if (Array.isArray(enabledModules) && enabledModules.length === 0) {
                    // If completely empty, assume all enabled (default behavior)
                    return true;
                }

                return enabledModules[feature.module] === true || enabledModules[feature.module] === '1' || enabledModules[feature.module] === 1;
            }).map(f => f.name);

            // Feature to Limit mapping
            const featureLimits = {
                'Staff Management': ['max_users'],
                'Multi-Branch Support': ['max_branches'],
                'Customer Management': ['max_customers'],
                'Service Management': ['max_services'],
                'Inventory Management': ['max_products'],
                'Packages': ['max_packages'],
                'Memberships': ['max_memberships'],
                'Booking System': ['max_bookings_per_month', 'max_guest_bookings_per_month']
            };

            // Autocomplete functionality
            const featureInput = document.getElementById('featureInput');
            const suggestionsDiv = document.getElementById('suggestions');

            featureInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();

                if (query.length === 0) {
                    suggestionsDiv.style.display = 'none';
                    return;
                }

                const matches = commonFeatures.filter(feature =>
                    feature.toLowerCase().includes(query) && !features.includes(feature)
                );

                if (matches.length === 0) {
                    suggestionsDiv.style.display = 'none';
                    return;
                }

                suggestionsDiv.innerHTML = matches.map(feature => `
                                                                                                                                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center" onclick="selectSuggestion('${feature}')">
                                                                                                                                            <i class="fas fa-plus-circle text-primary me-3"></i>
                                                                                                                                            <span>${feature}</span>
                                                                                                                                        </button>
                                                                                                                                    `).join('');

                suggestionsDiv.style.display = 'block';
            });

            document.addEventListener('click', function (e) {
                if (!featureInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                    suggestionsDiv.style.display = 'none';
                }
            });

            function selectSuggestion(feature) {
                featureInput.value = feature;
                suggestionsDiv.style.display = 'none';
                addFeature();
            }

            function addFeature() {
                const input = document.querySelector('.feature-input');
                const feature = input.value.trim();

                if (feature && !features.includes(feature)) {
                    features.push(feature);
                    updateFeaturesList();
                    input.value = '';
                    suggestionsDiv.style.display = 'none';
                }
            }

            function quickAddFeature(feature) {
                if (!features.includes(feature)) {
                    features.push(feature);
                    updateFeaturesList();
                }
            }

            function removeFeature(index) {
                features.splice(index, 1);
                updateFeaturesList();
            }

            function toggleLimitFields() {
                // 1. Identify which limit fields should be enabled based on current features
                const enabledLimits = new Set();
                features.forEach(feature => {
                    if (featureLimits[feature]) {
                        featureLimits[feature].forEach(id => enabledLimits.add(id));
                    }
                });

                // 2. Iterate all possible limit fields and toggle them
                Object.values(featureLimits).flat().forEach(id => {
                    const container = document.getElementById('container_' + id);
                    const input = document.getElementById(id);

                    if (enabledLimits.has(id)) {
                        // Should be visible
                        if (container) container.style.display = 'block';
                        // IMPORTANT: Do NOT clear value here. Keep existing value (from DB or user input)
                    } else {
                        // Should be hidden
                        if (container) container.style.display = 'none';
                        // Clear value when generated hidden to prevent submission of ghost data
                        if (input) input.value = '';
                    }
                });
            }

            function updateFeaturesList() {
                const container = document.getElementById('features-list');

                if (features.length === 0) {
                    container.innerHTML = '<p class="text-muted small mb-0"><i class="fas fa-info-circle me-2"></i>No features added yet</p>';
                } else {
                    container.innerHTML = features.map((feature, index) => `
                                                                                <span class="badge bg-primary rounded-pill px-3 py-2 fs-6 d-inline-flex align-items-center mb-2 me-2">
                                                                                    <i class="fas fa-check me-2"></i>${feature}
                                                                                    <button type="button" class="btn btn-link text-white p-0 ms-2" onclick="removeFeature(${index})" title="Remove" style="text-decoration: none;">
                                                                                        <i class="fas fa-times-circle"></i>
                                                                                    </button>
                                                                                </span>
                                                                            `).join('');
                }

                document.getElementById('features-json').value = JSON.stringify(features);
                toggleLimitFields();
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelector('.feature-input').addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addFeature();
                    }
                });
                updateFeaturesList();
            });
        </script>

        <style>
            .text-purple {
                color: #6f42c1;
            }

            .text-teal {
                color: #20c997;
            }

            .nav-tabs .nav-link {
                border: none;
                color: #6c757d;
                font-weight: 500;
                padding: 0.75rem 1.5rem;
                border-bottom: 3px solid transparent;
            }

            .nav-tabs .nav-link:hover {
                border-bottom-color: #dee2e6;
                color: #495057;
            }

            .nav-tabs .nav-link.active {
                color: #4e73df;
                border-bottom-color: #4e73df;
                background: none;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #4e73df;
                box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
            }

            .form-check-input:checked {
                background-color: #4e73df;
                border-color: #4e73df;
            }

            .btn-outline-primary {
                border-width: 1.5px;
            }

            .btn-outline-primary:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .card {
                border-radius: 0.5rem;
            }

            .gap-2 {
                gap: 0.5rem !important;
            }

            .gap-3 {
                gap: 1rem !important;
            }

            .gap-4 {
                gap: 1.5rem !important;
            }

            #suggestions {
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                border-radius: 0.5rem;
                margin-top: -0.75rem;
            }

            #suggestions .list-group-item {
                border: none;
                border-bottom: 1px solid #e3e6f0;
                padding: 0.75rem 1rem;
                cursor: pointer;
                transition: all 0.2s;
            }

            #suggestions .list-group-item:first-child {
                border-top-left-radius: 0.5rem;
                border-top-right-radius: 0.5rem;
            }

            #suggestions .list-group-item:last-child {
                border-bottom-left-radius: 0.5rem;
                border-bottom-right-radius: 0.5rem;
                border-bottom: none;
            }

            #suggestions .list-group-item:hover {
                background-color: #f8f9fc;
                transform: translateX(5px);
            }
        </style>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\super-admin\plans\edit.blade.php ENDPATH**/ ?>