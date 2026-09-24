<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">System Broadcasts</h1>
                <p class="text-muted mb-0">Manage global announcements and targeted notifications.</p>
            </div>
            <a href="<?php echo e(route('admin.notifications.create')); ?>" class="btn btn-primary shadow-sm fw-medium">
                <i class="fas fa-bullhorn me-2"></i> Create Broadcast
            </a>
        </div>

        <!-- Notifications List -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-muted small text-uppercase fw-bold">Title</th>
                                <th class="py-3 text-muted small text-uppercase fw-bold">Type</th>
                                <th class="py-3 text-muted small text-uppercase fw-bold">Target Audience</th>
                                <th class="py-3 text-muted small text-uppercase fw-bold">Status</th>
                                <th class="py-3 text-muted small text-uppercase fw-bold">Created</th>
                                <th class="pe-4 py-3 text-end text-muted small text-uppercase fw-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark"><?php echo e($notification->title); ?></div>
                                        <div class="small text-muted text-truncate" style="max-width: 300px;">
                                            <?php echo e(Str::limit($notification->message, 50)); ?>

                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $badgeClass = match ($notification->type) {
                                                'info' => 'bg-info-subtle text-info',
                                                'success' => 'bg-success-subtle text-success',
                                                'warning' => 'bg-warning-subtle text-warning',
                                                'danger' => 'bg-danger-subtle text-danger',
                                                default => 'bg-secondary-subtle text-secondary'
                                            };
                                            $icon = match ($notification->type) {
                                                'info' => 'fas fa-info-circle',
                                                'success' => 'fas fa-check-circle',
                                                'warning' => 'fas fa-exclamation-triangle',
                                                'danger' => 'fas fa-exclamation-circle',
                                                default => 'fas fa-bell'
                                            };
                                        ?>
                                        <span class="badge <?php echo e($badgeClass); ?> border px-3 py-2 rounded-pill">
                                            <i class="<?php echo e($icon); ?> me-1"></i> <?php echo e(ucfirst($notification->type)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php if($notification->target_salon_id): ?>
                                            <div class="d-flex align-items-center text-dark">
                                                <div class="bg-light rounded-circle p-2 me-2 text-center"
                                                    style="width: 32px; height: 32px;">
                                                    <i class="fas fa-store text-muted small"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium small"><?php echo e($notification->salon->name); ?></div>
                                                    <div class="text-xs text-muted">Specific Salon</div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="d-flex align-items-center text-dark">
                                                <div class="bg-primary-subtle rounded-circle p-2 me-2 text-center"
                                                    style="width: 32px; height: 32px;">
                                                    <i class="fas fa-globe text-primary small"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium small">All Workspaces</div>
                                                    <div class="text-xs text-muted">Global Broadcast</div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="<?php echo e(route('admin.notifications.toggle', $notification)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <button type="submit"
                                                class="btn btn-sm <?php echo e($notification->is_active ? 'btn-outline-success' : 'btn-outline-secondary'); ?> rounded-pill px-3"
                                                title="Toggle Status">
                                                <i class="fas fa-power-off me-1"></i>
                                                <?php echo e($notification->is_active ? 'Active' : 'Draft'); ?>

                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-muted small">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        <?php echo e($notification->created_at->format('M d, Y')); ?>

                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal"
                                                data-bs-target="#viewModal<?php echo e($notification->id); ?>" title="View Details">
                                                <i class="fas fa-eye text-primary"></i>
                                            </button>
                                            <form action="<?php echo e(route('admin.notifications.destroy', $notification)); ?>"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this notification?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-light border text-danger"
                                                    title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- View Modal -->
                                <div class="modal fade" id="viewModal<?php echo e($notification->id); ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold text-gray-800"><?php echo e($notification->title); ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body pt-3">
                                                <span class="badge <?php echo e($badgeClass); ?> mb-3">
                                                    <?php echo e(ucfirst($notification->type)); ?>

                                                </span>
                                                <div class="p-3 bg-light rounded-3 mb-3 text-dark">
                                                    <?php echo e($notification->message); ?>

                                                </div>
                                                <div class="d-flex justify-content-between align-items-center text-muted small">
                                                    <div>
                                                        <i class="fas fa-bullseye me-1"></i>
                                                        Target:
                                                        <strong><?php echo e($notification->target_salon_id ? $notification->salon->name : 'All Salons'); ?></strong>
                                                    </div>
                                                    <div>
                                                        <i class="far fa-clock me-1"></i>
                                                        <?php echo e($notification->created_at->format('M d, Y h:i A')); ?>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0 pt-0">
                                                <button type="button" class="btn btn-light"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="mb-3">
                                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                                                style="width: 64px; height: 64px;">
                                                <i class="far fa-bell-slash fa-2x text-muted opacity-50"></i>
                                            </div>
                                        </div>
                                        <h6 class="text-muted fw-bold">No notifications found</h6>
                                        <p class="text-muted small mb-0">Create a new broadcast to alert your users.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($notifications->hasPages()): ?>
                    <div class="card-footer bg-white border-top py-3">
                        <?php echo e($notifications->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\super-admin\notifications\index.blade.php ENDPATH**/ ?>