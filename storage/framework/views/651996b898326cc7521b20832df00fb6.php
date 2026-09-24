<?php if(auth()->check() && auth()->user()->hasRole(['salon_admin', 'super_admin'])): ?>
    <?php
        $salon = auth()->user()->salon;
        $currentBranch = app()->has('current_branch') ? app('current_branch') : null;
        $branches = $salon ? $salon->branches()->active()->get() : collect();
    ?>

    <?php if($branches->count() > 1): ?>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="branchSwitcher" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-building me-1"></i>
                <?php echo e($currentBranch ? $currentBranch->name : 'Select Branch'); ?>

            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="branchSwitcher">
                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <form action="<?php echo e(route('admin.branches.switch')); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="branch_id" value="<?php echo e($branch->id); ?>">
                            <button type="submit" class="dropdown-item <?php echo e($currentBranch && $currentBranch->id === $branch->id ? 'active' : ''); ?>">
                                <i class="fas fa-check me-2 <?php echo e($currentBranch && $currentBranch->id === $branch->id ? '' : 'invisible'); ?>"></i>
                                <?php echo e($branch->name); ?>

                            </button>
                        </form>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\components\branch-switcher.blade.php ENDPATH**/ ?>