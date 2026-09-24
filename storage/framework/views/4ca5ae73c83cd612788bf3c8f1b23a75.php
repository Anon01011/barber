<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
            <div>
                <h2 class="h3 fw-bold text-gray-800 mb-1"><?php echo e($branch->name); ?></h2>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <span class="text-muted small">
                        <i class="fas fa-map-marker-alt me-1"></i> <?php echo e($branch->address); ?>

                    </span>
                    <?php if($branch->is_active): ?>
                        <span class="badge bg-success-subtle text-white border border-success-subtle rounded-pill">Active</span>
                    <?php else: ?>
                        <span
                            class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="<?php echo e(route('admin.branches.index')); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('salon.manage_branches')): ?>
                    <a href="<?php echo e(route('admin.branches.edit', $branch)); ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i> Edit Branch
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Details -->
            <div class="col-lg-8">
                <!-- Contact Info Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h5 class="mb-0 text-primary fw-bold">Contact Information</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-4 py-3">
                                <div class="row">
                                    <div class="col-sm-4 text-muted fw-bold">Email Address</div>
                                    <div class="col-sm-8">
                                        <i class="fas fa-envelope me-2 text-secondary"></i>
                                        <a href="mailto:<?php echo e($branch->email); ?>"
                                            class="text-decoration-none"><?php echo e($branch->email); ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item px-4 py-3">
                                <div class="row">
                                    <div class="col-sm-4 text-muted fw-bold">Phone Number</div>
                                    <div class="col-sm-8">
                                        <i class="fas fa-phone me-2 text-secondary"></i>
                                        <a href="tel:<?php echo e($branch->phone); ?>"
                                            class="text-decoration-none"><?php echo e($branch->phone); ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item px-4 py-3">
                                <div class="row">
                                    <div class="col-sm-4 text-muted fw-bold">Address</div>
                                    <div class="col-sm-8 d-flex align-items-start">
                                        <i class="fas fa-map-marker-alt me-2 text-secondary mt-1"></i>
                                        <span><?php echo e($branch->address); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Staff List -->
                <div class="card shadow-sm border-0">
                    <div
                        class="card-header bg-light border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary fw-bold">Assigned Staff</h5>
                        <span class="badge bg-primary rounded-pill"><?php echo e($branch->users->count()); ?> Members</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php $__empty_1 = true; $__currentLoopData = $branch->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <li class="list-group-item px-4 py-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm rounded-circle me-3">
                                                <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($user->name)); ?>&background=random"
                                                    alt="<?php echo e($user->name); ?>" class="rounded-circle" width="40" height="40">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-primary fw-bold"><?php echo e($user->name); ?></h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i> <?php echo e($user->email); ?>

                                                </small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge bg-success-subtle text-white border border-success-subtle rounded-pill mb-1">
                                                <?php echo e($user->roles->first()->name ?? 'No Role'); ?>

                                            </span>
                                            <div class="small text-muted">Joined <?php echo e($user->created_at->format('M Y')); ?></div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <li class="list-group-item px-4 py-5 text-center text-muted">
                                    <i class="fas fa-users fa-3x mb-3 text-secondary opacity-50"></i>
                                    <p class="mb-0">No staff members assigned to this branch yet.</p>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Column: Stats & Quick Actions -->
            <div class="col-lg-4">
                <!-- Quick Stats -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h5 class="mb-0 text-primary fw-bold">Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center p-3 bg-primary-subtle rounded mb-3">
                            <div class="bg-primary text-white rounded p-3 me-3">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold">Total Staff</small>
                                <h4 class="mb-0 fw-bold text-dark"><?php echo e($branch->users->count()); ?></h4>
                            </div>
                        </div>

                        <div class="d-flex align-items-center p-3 bg-success-subtle rounded">
                            <div class="bg-success text-white rounded p-3 me-3">
                                <i class="fas fa-calendar-check fa-lg"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold">Today's Appts</small>
                                <h4 class="mb-0 fw-bold text-dark">
                                    <?php echo e($branch->appointments()->whereDate('start_time', today())->count()); ?>

                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h5 class="mb-0 text-primary fw-bold">Actions</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('admin.branches.switch')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="branch_id" value="<?php echo e($branch->id); ?>">
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-exchange-alt me-2"></i> Switch to this Branch
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views/admin/branches/show.blade.php ENDPATH**/ ?>