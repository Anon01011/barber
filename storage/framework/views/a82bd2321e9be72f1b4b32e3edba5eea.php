<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold text-dark mb-1">
                <i class="fas fa-star text-warning me-2"></i>Customer Feedback &amp; Reviews
            </h1>
            <p class="text-muted small mb-0">Monitor service quality, barber ratings, and client satisfaction across your salon.</p>
        </div>
    </div>

    <!-- Summary KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center py-4">
                    <span class="text-muted small fw-bold text-uppercase mb-1">Average Salon Rating</span>
                    <div class="display-4 fw-bold text-dark mb-1"><?php echo e(number_format($averageRating, 1)); ?></div>
                    <div class="text-warning mb-2">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo e($i <= round($averageRating) ? '' : 'text-muted opacity-25'); ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="text-muted small">Based on <?php echo e(number_format($totalReviews)); ?> verified reviews</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">Rating Distribution</h6>
                    <?php $__currentLoopData = $distribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $star => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex align-items-center mb-2">
                            <span class="small fw-semibold text-dark me-2" style="width: 45px;"><?php echo e($star); ?> <i class="fas fa-star text-warning"></i></span>
                            <div class="progress flex-grow-1" style="height: 10px;">
                                <div class="progress-bar <?php echo e($star >= 4 ? 'bg-success' : ($star == 3 ? 'bg-warning' : 'bg-danger')); ?>"
                                     role="progressbar" style="width: <?php echo e($data['percent']); ?>%"></div>
                            </div>
                            <span class="small text-muted ms-3 text-end" style="width: 70px;"><?php echo e($data['count']); ?> (<?php echo e($data['percent']); ?>%)</span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-muted">Filter by Specialist</label>
                    <select name="staff_id" class="form-select form-select-sm">
                        <option value="">All Staff Members</option>
                        <?php $__currentLoopData = $staffList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($staff->id); ?>" <?php echo e(request('staff_id') == $staff->id ? 'selected' : ''); ?>>
                                <?php echo e($staff->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-muted">Filter by Star Rating</label>
                    <select name="rating" class="form-select form-select-sm">
                        <option value="">All Ratings</option>
                        <?php for($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo e($i); ?>" <?php echo e(request('rating') == $i ? 'selected' : ''); ?>>
                                <?php echo e($i); ?> Star<?php echo e($i > 1 ? 's' : ''); ?>

                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                    <a href="<?php echo e(route('admin.feedback.index', ['salon_slug' => request()->route('salon_slug')])); ?>" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Specialist</th>
                            <th>Rating</th>
                            <th>Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="small text-muted"><?php echo e($review->updated_at?->format('M d, Y')); ?></td>
                                <td class="fw-semibold text-dark"><?php echo e($review->customer->name ?? 'Guest'); ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo e($review->service->name ?? 'Service'); ?></span></td>
                                <td class="small"><?php echo e($review->staff->name ?? 'Unassigned'); ?></td>
                                <td>
                                    <span class="badge <?php echo e($review->rating >= 4 ? 'bg-success' : ($review->rating == 3 ? 'bg-warning text-dark' : 'bg-danger')); ?>">
                                        <?php echo e($review->rating); ?> <i class="fas fa-star ms-1"></i>
                                    </span>
                                </td>
                                <td class="small text-muted" style="max-width: 380px;">
                                    <?php echo e($review->feedback ?: 'No written review provided.'); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-comment-slash fa-2x mb-2 d-block opacity-50"></i>
                                    No customer reviews match the selected filters yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($reviews->hasPages()): ?>
            <div class="card-footer bg-white border-0 py-3">
                <?php echo e($reviews->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ketan\projects\barber\resources\views/admin/feedback/index.blade.php ENDPATH**/ ?>