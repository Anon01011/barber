<?php $__env->startSection('title', 'Stock Alerts'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">Stock Alerts</h1>
                    <button type="button" class="btn btn-primary" onclick="generateAlerts()">
                        <i class="fas fa-sync me-1"></i> Generate Alerts
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Total Alerts</p>
                                <h3 class="mb-0"><?php echo e($stats['total']); ?></h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-bell text-primary fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Unresolved</p>
                                <h3 class="mb-0 text-warning"><?php echo e($stats['unresolved']); ?></h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Critical</p>
                                <h3 class="mb-0 text-danger"><?php echo e($stats['critical']); ?></h3>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i class="fas fa-exclamation-circle text-danger fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Out of Stock</p>
                                <h3 class="mb-0 text-danger"><?php echo e($stats['out_of_stock']); ?></h3>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i class="fas fa-box-open text-danger fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="unresolved" <?php echo e(request('status') == 'unresolved' ? 'selected' : ''); ?>>Unresolved
                            </option>
                            <option value="resolved" <?php echo e(request('status') == 'resolved' ? 'selected' : ''); ?>>Resolved</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="low_stock" <?php echo e(request('type') == 'low_stock' ? 'selected' : ''); ?>>Low Stock
                            </option>
                            <option value="out_of_stock" <?php echo e(request('type') == 'out_of_stock' ? 'selected' : ''); ?>>Out of
                                Stock</option>
                            <option value="expiring_soon" <?php echo e(request('type') == 'expiring_soon' ? 'selected' : ''); ?>>Expiring
                                Soon</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="severity" class="form-select" onchange="this.form.submit()">
                            <option value="">All Severities</option>
                            <option value="info" <?php echo e(request('severity') == 'info' ? 'selected' : ''); ?>>Info</option>
                            <option value="warning" <?php echo e(request('severity') == 'warning' ? 'selected' : ''); ?>>Warning</option>
                            <option value="critical" <?php echo e(request('severity') == 'critical' ? 'selected' : ''); ?>>Critical
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="<?php echo e(route('admin.inventory.alerts.index')); ?>" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Alerts Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Message</th>
                                <th>Severity</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo e($alert->inventoryItem->image_url); ?>"
                                                alt="<?php echo e($alert->inventoryItem->name); ?>" class="rounded me-2"
                                                style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <div class="fw-medium"><?php echo e($alert->inventoryItem->name); ?></div>
                                                <small class="text-muted"><?php echo e($alert->inventoryItem->sku); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-<?php echo e($alert->type == 'out_of_stock' ? 'danger' : ($alert->type == 'low_stock' ? 'warning' : 'info')); ?>">
                                            <?php echo e(str_replace('_', ' ', ucfirst($alert->type))); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($alert->message); ?></td>
                                    <td>
                                        <span
                                            class="badge bg-<?php echo e($alert->severity == 'critical' ? 'danger' : ($alert->severity == 'warning' ? 'warning' : 'info')); ?>">
                                            <?php echo e(ucfirst($alert->severity)); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($alert->created_at->diffForHumans()); ?></td>
                                    <td>
                                        <?php if($alert->is_resolved): ?>
                                            <span class="badge bg-success">Resolved</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if(!$alert->is_resolved): ?>
                                            <form action="<?php echo e(route('admin.inventory.alerts.resolve', $alert)); ?>" method="POST"
                                                class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn btn-sm btn-success" title="Resolve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('admin.inventory.edit', $alert->inventoryItem)); ?>"
                                            class="btn btn-sm btn-primary" title="View Product">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        No alerts found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($alerts->hasPages()): ?>
                <div class="card-footer bg-white">
                    <?php echo e($alerts->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            function generateAlerts() {
                if (!confirm('Generate stock alerts for all items?')) return;

                const btn = event.target.closest('button');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generating...';

                fetch('<?php echo e(route("admin.inventory.alerts.generate")); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(err => {
                        alert('Failed to generate alerts');

                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-sync me-1"></i> Generate Alerts';
                    });
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\inventory\alerts\index.blade.php ENDPATH**/ ?>