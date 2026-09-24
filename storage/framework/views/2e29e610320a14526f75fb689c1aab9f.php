<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-5">
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">Subscription Plans</h1>
                <p class="text-muted mb-0">Manage billing tiers and feature access for salons.</p>
            </div>
            <a href="<?php echo e(route('admin.plans.create')); ?>" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                <i class="fas fa-plus"></i>
                <span class="fw-medium">Create New Plan</span>
            </a>
        </div>

        <?php if($plans->count() == 0): ?>
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-clipboard-list fa-3x text-gray-300"></i>
                </div>
                <h5 class="text-gray-600">No subscription plans found</h5>
                <p class="text-muted">Create a plan to start offering subscriptions to salons.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden hover-lift transition-all">
                            <?php if($plan->is_popular): ?>
                                <div class="position-absolute top-0 start-50 translate-middle-x bg-warning text-dark px-3 py-1 rounded-bottom-3 shadow-sm" 
                                     style="z-index: 2; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">
                                    <i class="fas fa-star me-1"></i> MOST POPULAR
                                </div>
                                <div class="position-absolute top-0 start-0 w-100 h-1 bg-warning" style="height: 4px;"></div>
                            <?php else: ?>
                                <div class="position-absolute top-0 start-0 w-100 h-1 bg-primary" style="height: 4px;"></div>
                            <?php endif; ?>

                            <div class="card-body p-4 d-flex flex-column">
                                <!-- Status Badge -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge <?php echo e($plan->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'); ?> rounded-pill px-3">
                                        <?php echo e($plan->is_active ? 'Active' : 'Inactive'); ?>

                                    </span>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                            <li>
                                                <a class="dropdown-item py-2" href="<?php echo e(route('admin.plans.edit', $plan->id)); ?>">
                                                    <i class="fas fa-edit me-2 text-primary w-20"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="<?php echo e(route('admin.plans.duplicate', $plan->id)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="dropdown-item py-2">
                                                        <i class="fas fa-copy me-2 text-info w-20"></i> Duplicate
                                                    </button>
                                                </form>
                                            </li>
                                            <?php if($plan->subscriptions_count == 0): ?>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="<?php echo e(route('admin.plans.destroy', $plan->id)); ?>" method="POST" onsubmit="return confirm('Delete this plan?');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                                            <i class="fas fa-trash-alt me-2 w-20"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Title & Price -->
                                <div class="text-center mb-4">
                                    <h4 class="fw-bold text-dark mb-2"><?php echo e($plan->name); ?></h4>
                                    <div class="d-flex align-items-center justify-content-center text-primary">
                                        <span class="h3 mb-0 me-1 fw-bold"><?php echo e(system_currency_symbol()); ?></span>
                                        <span class="display-5 fw-bold"><?php echo e(number_format($plan->price, 0)); ?></span>
                                        <span class="text-muted ms-1 small">/ <?php echo e($plan->duration_in_days); ?>d</span>
                                    </div>
                                    <?php if($plan->description): ?>
                                        <p class="text-muted small mt-2 mb-0"><?php echo e(Str::limit($plan->description, 60)); ?></p>
                                    <?php endif; ?>
                                </div>

                                <hr class="opacity-10 my-0 mb-4">

                                <!-- Features -->
                                <div class="mb-4 flex-grow-1">
                                    <h6 class="text-uppercase text-muted fw-bold small mb-3">Key Features</h6>
                                    <ul class="list-unstyled mb-0">
                                        <?php
                                            $features = is_string($plan->features) ? (json_decode($plan->features, true) ?? []) : ($plan->features ?? []);
                                            $shownFeatures = array_slice($features, 0, 5);
                                        ?>
                                        <?php $__currentLoopData = $shownFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="d-flex align-items-start mb-2 small text-dark fs-6">
                                                <i class="fas fa-check text-success mt-1 me-2"></i>
                                                <span><?php echo e($feature); ?></span>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(count($features) > 5): ?>
                                            <li class="d-flex align-items-center small text-muted fst-italic ps-4">
                                                + <?php echo e(count($features) - 5); ?> more features
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>

                                <!-- Limits Grid -->
                                <div class="bg-light rounded-3 p-3 mb-4">
                                    <h6 class="text-uppercase text-muted fw-bold small mb-3">Resource Limits</h6>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-white p-1 rounded border text-primary"><i class="fas fa-users fa-sm"></i></div>
                                                <div class="lh-1">
                                                    <div class="small fw-bold"><?php echo e($plan->max_users === null ? '∞' : $plan->max_users); ?></div>
                                                    <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Staff</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-white p-1 rounded border text-info"><i class="fas fa-store fa-sm"></i></div>
                                                <div class="lh-1">
                                                    <div class="small fw-bold"><?php echo e($plan->max_branches === null ? '∞' : $plan->max_branches); ?></div>
                                                    <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Branches</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-white p-1 rounded border text-danger"><i class="fas fa-calendar-check fa-sm"></i></div>
                                                <div class="lh-1">
                                                    <div class="small fw-bold"><?php echo e(!isset($plan->limits['max_bookings_per_month']) || $plan->limits['max_bookings_per_month'] === -1 ? '∞' : $plan->limits['max_bookings_per_month']); ?></div>
                                                    <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Bookings</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-white p-1 rounded border text-secondary"><i class="fas fa-user-friends fa-sm"></i></div>
                                                <div class="lh-1">
                                                    <div class="small fw-bold"><?php echo e(!isset($plan->limits['max_customers']) || $plan->limits['max_customers'] === -1 ? '∞' : $plan->limits['max_customers']); ?></div>
                                                    <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Customers</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <a href="<?php echo e(route('admin.plans.edit', $plan->id)); ?>" class="btn btn-outline-primary fw-medium">
                                        Manage Plan
                                    </a>
                                </div>
                                
                                <div class="mt-3 text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-store me-1"></i> <?php echo e($plan->subscriptions_count); ?> Active Subscriptions
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php $__env->startPush('styles'); ?>
        <style>
            .hover-lift {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .hover-lift:hover {
                transform: translateY(-5px);
                box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
            }
            .w-20 {
                width: 20px;
                text-align: center;
            }
        </style>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\super-admin\plans\index.blade.php ENDPATH**/ ?>