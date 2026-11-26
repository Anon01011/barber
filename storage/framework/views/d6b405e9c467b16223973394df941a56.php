
<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
    <div class="card-body p-3">
        
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom">
            <div class="nav nav-pills gap-1" role="tablist">
                <?php
                    $currentType = request('type');
                    $currentStatus = request('status');
                    $baseQuery = request()->except('page');
                ?>
                <a href="<?php echo e(route('admin.inventory.index', array_merge($baseQuery, ['type' => null, 'status' => null]))); ?>" 
                    class="nav-link rounded-pill py-1 px-3 small fw-semibold <?php echo e(empty($currentType) && empty($currentStatus) ? 'active bg-dark text-white' : 'text-muted bg-light'); ?>">
                    <i class="fas fa-boxes me-1"></i> All Products
                </a>
                <a href="<?php echo e(route('admin.inventory.index', array_merge($baseQuery, ['type' => 'retail', 'status' => null]))); ?>" 
                    class="nav-link rounded-pill py-1 px-3 small fw-semibold <?php echo e($currentType === 'retail' ? 'active bg-dark text-white' : 'text-muted bg-light'); ?>">
                    <i class="fas fa-shopping-bag me-1"></i> Retail Shelves
                </a>
                <a href="<?php echo e(route('admin.inventory.index', array_merge($baseQuery, ['type' => 'consumable', 'status' => null]))); ?>" 
                    class="nav-link rounded-pill py-1 px-3 small fw-semibold <?php echo e($currentType === 'consumable' ? 'active bg-dark text-white' : 'text-muted bg-light'); ?>">
                    <i class="fas fa-flask me-1"></i> Backbar / In-Service
                </a>
                <a href="<?php echo e(route('admin.inventory.index', array_merge($baseQuery, ['status' => 'low', 'type' => null]))); ?>" 
                    class="nav-link rounded-pill py-1 px-3 small fw-semibold <?php echo e($currentStatus === 'low' ? 'active bg-danger text-white' : 'text-danger bg-danger-subtle'); ?>">
                    <i class="fas fa-exclamation-circle me-1"></i> Low Stock (<?php echo e($kpiStats['low_stock_items'] ?? 0); ?>)
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="<?php echo e(route('admin.inventory.reports.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-chart-pie me-1"></i> Reports
                </a>
                <a href="<?php echo e(route('admin.inventory.categories.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-tags me-1"></i> Categories
                </a>
            </div>
        </div>

        
        <form action="<?php echo e(route('admin.inventory.index')); ?>" method="GET" class="row g-2 align-items-center">
            <?php if(request('type')): ?>
                <input type="hidden" name="type" value="<?php echo e(request('type')); ?>">
            <?php endif; ?>
            <?php if(request('status')): ?>
                <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
            <?php endif; ?>

            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 ps-3">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" 
                        placeholder="Search by product name, SKU, or barcode..." value="<?php echo e(request('search')); ?>">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                            <?php echo e($category->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <?php if($branches->count() > 1): ?>
                <div class="col-6 col-md-2">
                    <select name="branch" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Branches</option>
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch') == $branch->id ? 'selected' : ''); ?>>
                                <?php echo e($branch->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="col-12 col-md-auto ms-auto d-flex gap-2">
                <button type="submit" class="btn btn-dark btn-sm rounded-3 px-3">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <?php if(request()->has('search') || request()->has('category') || request()->has('status') || request()->has('branch') || request()->has('type')): ?>
                    <a href="<?php echo e(route('admin.inventory.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-3 px-2" title="Reset Filters">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\Ketan\projects\barber\resources\views/inventory/partials/filter-bar.blade.php ENDPATH**/ ?>