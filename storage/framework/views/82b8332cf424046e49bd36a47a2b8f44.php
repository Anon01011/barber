<?php $__env->startSection('title', 'Service Categories'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4 py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0">Service Categories</h2>
            <div>
                <a href="<?php echo e(route('services.index')); ?>" class="btn btn-outline-primary me-2">
                    <i class="fas fa-list me-1"></i>Manage Services
                </a>
                <a href="<?php echo e(route('admin.categories.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Category
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($category->name); ?></td>
                                    <td><?php echo e($category->description); ?></td>
                                    <td>
                                        <span
                                            class="badge <?php echo e($category->status === 'active' ? 'bg-success' : 'bg-secondary'); ?>">
                                            <?php echo e(ucfirst($category->status)); ?>

                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?php echo e(route('admin.categories.edit', $category)); ?>"
                                            class="btn btn-sm btn-light me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.categories.destroy', $category)); ?>" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this category?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-sm btn-light text-danger"><i
                                                    class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No categories found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\categories\index.blade.php ENDPATH**/ ?>