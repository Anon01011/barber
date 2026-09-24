<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">Create New Salon</h1>
                <p class="text-muted mb-0">Set up a new salon workspace and assign an owner.</p>
            </div>
            <a href="<?php echo e(route('admin.salons.index')); ?>" class="btn btn-light border shadow-sm text-muted">
                <i class="fas fa-arrow-left me-2"></i> Back to Salons
            </a>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Main Form -->
            <div class="col-lg-8">
                <form action="<?php echo e(route('admin.salons.store')); ?>" method="POST" id="salonForm">
                    <?php echo csrf_field(); ?>
                    
                    <!-- 1. Salon Details Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-store me-2"></i> Salon Information
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-medium text-dark">Salon Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                        value="<?php echo e(old('name')); ?>" required placeholder="e.g. Luxe Beauty Lounge">
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label for="slug" class="form-label fw-medium text-dark">Workspace URL <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted">/</span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="slug" name="slug" 
                                            value="<?php echo e(old('slug')); ?>" required placeholder="my-salon">
                                    </div>
                                    <small class="text-muted">Unique identifier for the salon's system URL.</small>
                                    <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label for="business_type" class="form-label fw-medium text-dark">Business Type <span class="text-danger">*</span></label>
                                    <select name="business_type" id="business_type" class="form-select" required>
                                        <option value="salon" <?php echo e(old('business_type') == 'salon' ? 'selected' : ''); ?>>Salon / Spa</option>
                                        <option value="barber" <?php echo e(old('business_type') == 'barber' ? 'selected' : ''); ?>>Barber / Barbershop</option>
                                        <option value="both" <?php echo e(old('business_type') == 'both' ? 'selected' : ''); ?>>Both (Salon & Barber)</option>
                                    </select>
                                    <?php $__errorArgs = ['business_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-medium text-dark">Salon Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                        value="<?php echo e(old('email')); ?>" required placeholder="contact@example.com">
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-medium text-dark">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                        value="<?php echo e(old('phone')); ?>" placeholder="+1 234 567 8900">
                                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                
                                <div class="col-12">
                                    <label for="address" class="form-label fw-medium text-dark">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2" 
                                        placeholder="Full address of the salon..."><?php echo e(old('address')); ?></textarea>
                                    <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label for="timezone" class="form-label fw-medium text-dark">Timezone <span class="text-danger">*</span></label>
                                    <select class="form-select" id="timezone" name="timezone" required>
                                        <?php $__currentLoopData = timezone_identifiers_list(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timezone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($timezone); ?>" <?php echo e(old('timezone', 'UTC') == $timezone ? 'selected' : ''); ?>>
                                                <?php echo e($timezone); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="currency" class="form-label fw-medium text-dark">Currency <span class="text-danger">*</span></label>
                                    <select class="form-select" id="currency" name="currency" required>
                                        <option value="USD" <?php echo e(old('currency', 'USD') == 'USD' ? 'selected' : ''); ?>>USD ($)</option>
                                        <option value="EUR" <?php echo e(old('currency') == 'EUR' ? 'selected' : ''); ?>>EUR (€)</option>
                                        <option value="GBP" <?php echo e(old('currency') == 'GBP' ? 'selected' : ''); ?>>GBP (£)</option>
                                        <option value="INR" <?php echo e(old('currency') == 'INR' ? 'selected' : ''); ?>>INR (₹)</option>
                                        <option value="AED" <?php echo e(old('currency') == 'AED' ? 'selected' : ''); ?>>AED (د.إ)</option>
                                        <option value="SAR" <?php echo e(old('currency') == 'SAR' ? 'selected' : ''); ?>>SAR (﷼)</option>
                                        <option value="CAD" <?php echo e(old('currency') == 'CAD' ? 'selected' : ''); ?>>CAD ($)</option>
                                        <option value="AUD" <?php echo e(old('currency') == 'AUD' ? 'selected' : ''); ?>>AUD ($)</option>
                                    </select>
                                    <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Owner Details Card -->
                     <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-shield me-2"></i> Owner Account
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-info border-0 bg-info-subtle text-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                An admin user account will be created for the owner with these credentials.
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="owner_name" class="form-label fw-medium text-dark">Owner Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="owner_name" name="owner_name" 
                                        value="<?php echo e(old('owner_name')); ?>" required placeholder="Full Name">
                                    <?php $__errorArgs = ['owner_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label for="owner_email" class="form-label fw-medium text-dark">Owner Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="owner_email" name="owner_email" 
                                        value="<?php echo e(old('owner_email')); ?>" required placeholder="owner@email.com">
                                    <?php $__errorArgs = ['owner_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label for="owner_phone" class="form-label fw-medium text-dark">Mobile Number</label>
                                    <input type="text" class="form-control" id="owner_phone" name="owner_phone" 
                                        value="<?php echo e(old('owner_phone')); ?>" placeholder="+1 234 567 8900">
                                    <?php $__errorArgs = ['owner_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6"></div> <!-- Spacer -->

                                <div class="col-md-6">
                                    <label for="owner_password" class="form-label fw-medium text-dark">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="owner_password" name="owner_password" required>
                                    <small class="text-muted">Min. 8 characters</small>
                                    <?php $__errorArgs = ['owner_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="owner_password_confirmation" class="form-label fw-medium text-dark">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="owner_password_confirmation" name="owner_password_confirmation" required>
                                </div>
                            </div>
                        </div>
                     </div>

                     <!-- 3. Subscription Card -->
                     <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-credit-card me-2"></i> Subscription Plan
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label for="plan_id" class="form-label fw-medium text-dark">Select Plan</label>
                                    <select class="form-select" id="plan_id" name="plan_id">
                                        <option value="">No Plan (Manual Setup Later)</option>
                                        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($plan->id); ?>" <?php echo e(old('plan_id') == $plan->id ? 'selected' : ''); ?>>
                                                <?php echo e($plan->name); ?> - <?php echo e(system_currency_symbol()); ?><?php echo e(number_format($plan->price, 2)); ?> / <?php echo e($plan->duration_in_days); ?> days
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['plan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="trial_days" class="form-label fw-medium text-dark">Trial Period (Days)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="trial_days" name="trial_days" 
                                            value="<?php echo e(old('trial_days', 14)); ?>" min="0">
                                        <span class="input-group-text bg-light text-muted">Days</span>
                                    </div>
                                    <?php $__errorArgs = ['trial_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                     </div>

                     <div class="d-flex justify-content-end gap-3 mb-5">
                         <a href="<?php echo e(route('admin.salons.index')); ?>" class="btn btn-light border px-4">Cancel</a>
                         <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                             Create Salon
                         </button>
                     </div>
                </form>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 2rem;">
                    <div class="card border-0 shadow-sm bg-primary text-white mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3"><i class="fas fa-magic me-2"></i> Automated Setup</h6>
                            <p class="small opacity-75 mb-0">
                                The system will automatically configure the workspace, creating default branches, user roles, and initializing the subscription.
                            </p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="m-0 fw-bold text-dark">Checklist</h6>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-4 py-3 d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span class="small text-muted">Salon account creation</span>
                                </li>
                                <li class="list-group-item px-4 py-3 d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span class="small text-muted">Owner role assignment</span>
                                </li>
                                <li class="list-group-item px-4 py-3 d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span class="small text-muted">Default "Main Branch"</span>
                                </li>
                                <li class="list-group-item px-4 py-3 d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span class="small text-muted">Subscription activation</span>
                                </li>
                                <li class="list-group-item px-4 py-3 d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span class="small text-muted">Welcome email dispatch</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // Auto-generate slug from name
            document.getElementById('name').addEventListener('input', function (e) {
                const slug = e.target.value
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                document.getElementById('slug').value = slug;
            });

            // Detect and set local timezone
            document.addEventListener('DOMContentLoaded', function() {
                const timezoneSelect = document.getElementById('timezone');
                if (timezoneSelect && (!timezoneSelect.value || timezoneSelect.value === 'UTC')) {
                    try {
                        const localTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                        if (localTimezone) {
                            const optionExists = Array.from(timezoneSelect.options).some(option => option.value === localTimezone);
                            if (optionExists) {
                                timezoneSelect.value = localTimezone;
                            }
                        }
                    } catch(e) { }
                }
            });

            // Form validation
            document.getElementById('salonForm').addEventListener('submit', function (e) {
                if (window.customerPhoneHandler) {
                    if (!window.customerPhoneHandler.validateForm(this)) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Phone Number',
                            text: 'Please check the phone numbers and try again.'
                        });
                    }
                }
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\super-admin\salons\create.blade.php ENDPATH**/ ?>