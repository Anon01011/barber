<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['notifications']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['notifications']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="nav-item dropdown" x-data="{ open: false }" @click.away="open = false">
    <button class="nav-link position-relative" @click="open = !open">
        <i class="fas fa-bell"></i>
        <?php if($notifications->count() > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo e($notifications->count()); ?>

            </span>
        <?php endif; ?>
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0" x-show="open"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
        style="width: 320px; max-height: 400px; overflow-y: auto;">

        <div class="p-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Notifications</h6>
                <?php if($notifications->count() > 0): ?>
                    <form action="<?php echo e(route('notifications.markAllAsRead')); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0">
                            Mark all as read
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="list-group list-group-flush">
            <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="list-group-item <?php echo e($notification->read_at ? '' : 'bg-light'); ?>">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <?php if($notification->data['type'] === 'appointment_created'): ?>
                                <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                            <?php elseif($notification->data['type'] === 'appointment_accepted'): ?>
                                <div class="avatar avatar-sm bg-success-subtle text-white rounded-circle">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            <?php elseif($notification->data['type'] === 'appointment_rejected'): ?>
                                <div class="avatar avatar-sm bg-danger-subtle text-danger rounded-circle">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            <?php elseif($notification->data['type'] === 'appointment_status_changed'): ?>
                                <div class="avatar avatar-sm bg-info-subtle text-info rounded-circle">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                            <?php else: ?>
                                <div class="avatar avatar-sm bg-secondary-subtle text-secondary rounded-circle">
                                    <i class="fas fa-bell"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-1"><?php echo e($notification->data['message']); ?></p>
                            <small class="text-muted"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                        </div>
                        <?php if(!$notification->read_at): ?>
                            <div class="flex-shrink-0 ms-2">
                                <form action="<?php echo e(route('notifications.markAsRead', $notification->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0">
                                        <i class="fas fa-check text-white"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-4">
                    <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">No notifications</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if($notifications->count() > 0): ?>
            <div class="p-3 border-top text-center">
                <a href="<?php echo e(route('notifications.index')); ?>" class="text-decoration-none">
                    View all notifications
                </a>
            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\components\header-notifications.blade.php ENDPATH**/ ?>