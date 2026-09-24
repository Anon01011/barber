<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Create Subscription</h2>
                    <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Subscriptions
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="<?php echo e(route('admin.subscriptions.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            <div class="mb-3">
                                <label for="salon_id" class="form-label">Salon *</label>
                                <select class="form-select <?php $__errorArgs = ['salon_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="salon_id"
                                    name="salon_id" required>
                                    <option value="">Select Salon</option>
                                    <?php $__currentLoopData = $salons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($salon->id); ?>" <?php echo e((old('salon_id', $selectedSalonId) == $salon->id) ? 'selected' : ''); ?>>
                                            <?php echo e($salon->name); ?> (<?php echo e($salon->slug); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['salon_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-3">
                                <label for="plan_id" class="form-label">Plan *</label>
                                <select class="form-select <?php $__errorArgs = ['plan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="plan_id"
                                    name="plan_id" required>
                                    <option value="">Select Plan</option>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($plan->id); ?>" <?php echo e(old('plan_id') == $plan->id ? 'selected' : ''); ?>

                                            data-price="<?php echo e($plan->price); ?>" data-duration="<?php echo e($plan->duration_in_days); ?>">
                                            <?php echo e($plan->name); ?> -
                                            <?php echo e(system_currency_symbol()); ?><?php echo e(number_format($plan->price, 2)); ?>/<?php echo e($plan->duration_in_days); ?>

                                            days
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['plan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="status"
                                        name="status" required>
                                        <option value="active" <?php echo e(old('status', 'active') == 'active' ? 'selected' : ''); ?>>
                                            Active</option>
                                        <option value="cancelled" <?php echo e(old('status') == 'cancelled' ? 'selected' : ''); ?>>
                                            Cancelled</option>
                                        <option value="expired" <?php echo e(old('status') == 'expired' ? 'selected' : ''); ?>>Expired
                                        </option>
                                    </select>
                                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="trial_days" class="form-label">Trial Days</label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['trial_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="trial_days" name="trial_days" value="<?php echo e(old('trial_days', 0)); ?>" min="0">
                                    <small class="text-muted">0 = No trial period</small>
                                    <?php $__errorArgs = ['trial_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="starts_at" class="form-label">Start Date</label>
                                <input type="date" class="form-control <?php $__errorArgs = ['starts_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="starts_at" name="starts_at" value="<?php echo e(old('starts_at', now()->format('Y-m-d'))); ?>">
                                <small class="text-muted">Leave blank for today</small>
                                <?php $__errorArgs = ['starts_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>End Date:</strong> Will be automatically calculated based on plan duration
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Subscription
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">Subscription Details</h6>
                    </div>
                    <div class="card-body">
                        <div id="subscription-preview">
                            <p class="text-muted">Select a plan to see details</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // Show plan details when selected
            document.getElementById('plan_id').addEventListener('change', function (e) {
                const option = e.target.options[e.target.selectedIndex];
                const price = option.dataset.price;
                const duration = option.dataset.duration;
                const trialDays = document.getElementById('trial_days').value || 0;
                const startsAt = document.getElementById('starts_at').value || new Date().toISOString().split('T')[0];

                if (price && duration) {
                    const startDate = new Date(startsAt);
                    const endDate = new Date(startDate);
                    endDate.setDate(endDate.getDate() + parseInt(duration));

                    const trialEndDate = new Date(startDate);
                    trialEndDate.setDate(trialEndDate.getDate() + parseInt(trialDays));

                    document.getElementById('subscription-preview').innerHTML = `
                            <dl class="row mb-0">
                                <dt class="col-sm-5">Plan:</dt>
                                <dd class="col-sm-7">${option.text.split(' - ')[0]}</dd>

                                <dt class="col-sm-5">Price:</dt>
                                <dd class="col-sm-7"><?php echo e(system_currency_symbol()); ?>${price}</dd>

                                <dt class="col-sm-5">Duration:</dt>
                                <dd class="col-sm-7">${duration} days</dd>

                                <dt class="col-sm-5">Starts:</dt>
                                <dd class="col-sm-7">${startDate.toLocaleDateString()}</dd>

                                ${trialDays > 0 ? `
                                <dt class="col-sm-5">Trial Ends:</dt>
                                <dd class="col-sm-7">${trialEndDate.toLocaleDateString()}</dd>
                                ` : ''}

                                <dt class="col-sm-5">Ends:</dt>
                                <dd class="col-sm-7">${endDate.toLocaleDateString()}</dd>
                            </dl>
                        `;
                }
            });

            // Update preview when trial days or start date changes
            document.getElementById('trial_days').addEventListener('input', function () {
                document.getElementById('plan_id').dispatchEvent(new Event('change'));
            });

            document.getElementById('starts_at').addEventListener('change', function () {
                document.getElementById('plan_id').dispatchEvent(new Event('change'));
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\super-admin\subscriptions\create.blade.php ENDPATH**/ ?>