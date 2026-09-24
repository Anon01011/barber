<?php $__env->startSection('title', 'Packages'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Packages</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Packages</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">


                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Package List</h4>
                            <a href="<?php echo e(route('admin.packages.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add New Package
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th>Tax Rate</th>
                                        <th>Validity</th>
                                        <th>Services</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($loop->iteration); ?></td>
                                            <td><?php echo e($package->name); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo e($package->type == 'fixed' ? 'primary' : 'info'); ?>">
                                                    <?php echo e(ucfirst($package->type)); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e(format_currency($package->price)); ?></td>
                                            <td><?php echo e($package->tax_rate); ?>%</td>
                                            <td><?php echo e($package->validity_value); ?> <?php echo e(ucfirst($package->validity_unit)); ?></td>
                                            <td><?php echo e($package->services_count); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo e($package->is_active ? 'success' : 'danger'); ?>">
                                                    <?php echo e($package->is_active ? 'Active' : 'Inactive'); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('admin.packages.show', $package)); ?>"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.packages.edit', $package)); ?>"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="<?php echo e(route('admin.packages.destroy', $package)); ?>" method="POST"
                                                    class="d-inline">
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
                                            <td colspan="9" class="text-center">No packages found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php echo e($packages->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\packages\index.blade.php ENDPATH**/ ?>