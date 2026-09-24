<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-percent text-primary me-2"></i>Commission Profiles
            </h1>
            <a href="<?php echo e(route('admin.commissions.create')); ?>" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add Commission Profile
            </a>
        </div>



        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Commission Profiles</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Profile Name</th>
                                <th>Type</th>
                                <th>Rules</th>
                                <th>Assigned Staff</th>
                                <th>Include Tax</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="font-weight-bold"><?php echo e($profile->name); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($profile->type === 'by_item' ? 'primary' : 'info'); ?>">
                                            <?php echo e($profile->type === 'by_item' ? 'By Item' : 'By Target'); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($profile->rules_count); ?> rule(s)</td>
                                    <td><?php echo e($profile->staff_count); ?> staff</td>
                                    <td>
                                        <?php if($profile->include_tax): ?>
                                            <span class="badge bg-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($profile->is_active): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e(format_date($profile->created_at)); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('admin.commissions.edit', $profile)); ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.commissions.destroy', $profile)); ?>" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this commission profile?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>


                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            $(document).ready(function () {
                $('#dataTable').DataTable({
                    "order": [[6, "desc"]]
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\commissions\index.blade.php ENDPATH**/ ?>