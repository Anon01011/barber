<?php $__env->startSection('title', 'Memberships'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Memberships</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Memberships</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">


                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Membership List</h4>
                            <a href="<?php echo e(route('admin.memberships.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add New Membership
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Discount Value</th>
                                        <th>Taxable</th>
                                        <th>Validity (Days)</th>
                                        <th>Services</th>
                                        <th>Products</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $memberships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membership): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($loop->iteration); ?></td>
                                            <td><?php echo e($membership->name); ?></td>
                                            <td><?php echo e(format_currency($membership->discount_value)); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo e($membership->is_taxable ? 'success' : 'secondary'); ?>">
                                                    <?php echo e($membership->is_taxable ? 'Yes' : 'No'); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e($membership->validity_days); ?></td>
                                            <td><?php echo e($membership->services_count); ?></td>
                                            <td><?php echo e($membership->inventory_items_count); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo e($membership->is_active ? 'success' : 'danger'); ?>">
                                                    <?php echo e($membership->is_active ? 'Active' : 'Inactive'); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('admin.memberships.show', $membership)); ?>"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.memberships.edit', $membership)); ?>"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="<?php echo e(route('admin.memberships.destroy', $membership)); ?>"
                                                    method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="9" class="text-center">No memberships found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php echo e($memberships->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\memberships\index.blade.php ENDPATH**/ ?>