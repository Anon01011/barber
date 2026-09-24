<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">Edit Salon</h1>
                <p class="text-muted mb-0">Update details for <span class="fw-bold"><?php echo e($salon->name); ?></span></p>
            </div>
            <a href="<?php echo e(route('admin.salons.show', $salon)); ?>" class="btn btn-light border shadow-sm text-muted">
                <i class="fas fa-arrow-left me-2"></i> Back to Details
            </a>
        </div>

        <form method="POST" action="<?php echo e(route('admin.salons.update', $salon)); ?>" enctype="multipart/form-data"
            id="salonEditForm">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="row g-4 justify-content-center">
                <!-- Main Content -->
                <div class="col-lg-8">

                    <!-- 1. Basic Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-store me-2"></i> Salon Information
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <!-- Logo Upload -->
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-medium text-dark">Salon Logo</label>
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="position-relative">
                                            <img src="<?php echo e($salon->logo ? Storage::url($salon->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($salon->name) . '&background=random'); ?>"
                                                class="rounded-circle border shadow-sm"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                            <div class="form-text mt-1">Recommended: 512x512px. Max 2MB.</div>
                                        </div>
                                    </div>
                                    <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-medium text-dark">Salon Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="<?php echo e(old('name', $salon->name)); ?>" required>
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
                                    <label for="business_type" class="form-label fw-medium text-dark">Business Type</label>
                                    <select name="business_type" id="business_type" class="form-select" required>
                                        <option value="salon" <?php echo e(old('business_type', $salon->business_type) == 'salon' ? 'selected' : ''); ?>>Salon / Spa</option>
                                        <option value="barber" <?php echo e(old('business_type', $salon->business_type) == 'barber' ? 'selected' : ''); ?>>Barber / Barbershop</option>
                                        <option value="both" <?php echo e(old('business_type', $salon->business_type) == 'both' ? 'selected' : ''); ?>>Both (Salon & Barber)</option>
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
                                    <label for="email" class="form-label fw-medium text-dark">Business Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?php echo e(old('email', $salon->email)); ?>" required>
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
                                        value="<?php echo e(old('phone', $salon->phone)); ?>">
                                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label for="website" class="form-label fw-medium text-dark">Website</label>
                                    <input type="url" class="form-control" id="website" name="website"
                                        value="<?php echo e(old('website', $salon->website)); ?>" placeholder="https://">
                                    <?php $__errorArgs = ['website'];
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
                                    <textarea class="form-control" id="address" name="address"
                                        rows="2"><?php echo e(old('address', $salon->address)); ?></textarea>
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
                                    <label for="timezone" class="form-label fw-medium text-dark">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone" required>
                                        <?php $__currentLoopData = timezone_identifiers_list(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timezone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($timezone); ?>" <?php echo e(old('timezone', $salon->timezone) == $timezone ? 'selected' : ''); ?>>
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
                                    <label for="currency" class="form-label fw-medium text-dark">Currency</label>
                                    <select class="form-select" id="currency" name="currency" required>
                                        <option value="USD" <?php echo e(old('currency', $salon->currency) == 'USD' ? 'selected' : ''); ?>>USD ($)</option>
                                        <option value="EUR" <?php echo e(old('currency', $salon->currency) == 'EUR' ? 'selected' : ''); ?>>EUR (€)</option>
                                        <option value="GBP" <?php echo e(old('currency', $salon->currency) == 'GBP' ? 'selected' : ''); ?>>GBP (£)</option>
                                        <option value="INR" <?php echo e(old('currency', $salon->currency) == 'INR' ? 'selected' : ''); ?>>INR (₹)</option>
                                        <option value="AED" <?php echo e(old('currency', $salon->currency) == 'AED' ? 'selected' : ''); ?>>AED (د.إ)</option>
                                        <option value="SAR" <?php echo e(old('currency', $salon->currency) == 'SAR' ? 'selected' : ''); ?>>SAR (﷼)</option>
                                        <option value="CAD" <?php echo e(old('currency', $salon->currency) == 'CAD' ? 'selected' : ''); ?>>CAD ($)</option>
                                        <option value="AUD" <?php echo e(old('currency', $salon->currency) == 'AUD' ? 'selected' : ''); ?>>AUD ($)</option>
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

                    <!-- 2. Owner Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-tie me-2"></i> Owner Information
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-warning border-0 bg-warning-subtle text-warning small mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Warning:</strong> Changing the email address will update the owner's login
                                credentials.
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="owner_name" class="form-label fw-medium text-dark">Owner Name</label>
                                    <input type="text" class="form-control" id="owner_name" name="owner_name"
                                        value="<?php echo e(old('owner_name', $salon->owner->name ?? '')); ?>" required>
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
                                    <label for="owner_email" class="form-label fw-medium text-dark">Owner Email</label>
                                    <input type="email" class="form-control" id="owner_email" name="owner_email"
                                        value="<?php echo e(old('owner_email', $salon->owner->email ?? '')); ?>" required>
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
                                    <label for="owner_phone" class="form-label fw-medium text-dark">Owner Phone</label>
                                    <input type="text" class="form-control" id="owner_phone" name="owner_phone"
                                        value="<?php echo e(old('owner_phone', $salon->owner->phone ?? '')); ?>">
                                    <?php $__errorArgs = ['owner_phone'];
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

                    <!-- 3. Mail Settings (Collapsed by default? No, open is fine) -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div
                            class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-envelope me-2"></i> SMTP Configuration
                            </h6>
                            <span class="badge bg-light text-muted border">Advanced</span>
                        </div>
                        <div class="card-body p-4">
                            <p class="small text-muted mb-3">Configure custom email settings for this salon to send emails
                                from their own domain.</p>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="mail_driver" class="form-label fw-medium text-dark">Mail Driver</label>
                                    <select class="form-select" id="mail_driver" name="mail_driver">
                                        <option value="smtp" <?php echo e(old('mail_driver', $salon->mail_driver) == 'smtp' ? 'selected' : ''); ?>>SMTP</option>
                                        <option value="mailgun" <?php echo e(old('mail_driver', $salon->mail_driver) == 'mailgun' ? 'selected' : ''); ?>>Mailgun</option>
                                        <option value="ses" <?php echo e(old('mail_driver', $salon->mail_driver) == 'ses' ? 'selected' : ''); ?>>Amazon SES</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="mail_host" class="form-label fw-medium text-dark">Mail Host</label>
                                    <input type="text" class="form-control" id="mail_host" name="mail_host"
                                        value="<?php echo e(old('mail_host', $salon->mail_host)); ?>" placeholder="smtp.provider.com">
                                </div>
                                <div class="col-md-2">
                                    <label for="mail_port" class="form-label fw-medium text-dark">Port</label>
                                    <input type="number" class="form-control" id="mail_port" name="mail_port"
                                        value="<?php echo e(old('mail_port', $salon->mail_port)); ?>" placeholder="587">
                                </div>

                                <div class="col-md-6">
                                    <label for="mail_username" class="form-label fw-medium text-dark">Username</label>
                                    <input type="text" class="form-control" id="mail_username" name="mail_username"
                                        value="<?php echo e(old('mail_username', $salon->mail_username)); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="mail_password" class="form-label fw-medium text-dark">Password</label>
                                    <input type="password" class="form-control" id="mail_password" name="mail_password"
                                        placeholder="••••••••">
                                    <small class="text-muted">Leave blank to keep unchanged</small>
                                </div>

                                <div class="col-md-4">
                                    <label for="mail_encryption" class="form-label fw-medium text-dark">Encryption</label>
                                    <select class="form-select" id="mail_encryption" name="mail_encryption">
                                        <option value="" <?php echo e(old('mail_encryption', $salon->mail_encryption) == '' ? 'selected' : ''); ?>>None</option>
                                        <option value="tls" <?php echo e(old('mail_encryption', $salon->mail_encryption) == 'tls' ? 'selected' : ''); ?>>TLS</option>
                                        <option value="ssl" <?php echo e(old('mail_encryption', $salon->mail_encryption) == 'ssl' ? 'selected' : ''); ?>>SSL</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="mail_from_address" class="form-label fw-medium text-dark">From Email</label>
                                    <input type="email" class="form-control" id="mail_from_address" name="mail_from_address"
                                        value="<?php echo e(old('mail_from_address', $salon->mail_from_address)); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="mail_from_name" class="form-label fw-medium text-dark">From Name</label>
                                    <input type="text" class="form-control" id="mail_from_name" name="mail_from_name"
                                        value="<?php echo e(old('mail_from_name', $salon->mail_from_name)); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="col-lg-4">
                    <!-- Status & Plan -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-credit-card me-2"></i> Subscription
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label for="plan_id" class="form-label fw-medium text-dark">Current Plan</label>
                                <select class="form-select <?php $__errorArgs = ['plan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="plan_id"
                                    name="plan_id">
                                    <option value="">-- No Plan --</option>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($plan->id); ?>" <?php echo e(old('plan_id', $salon->activeSubscription->plan_id ?? '') == $plan->id ? 'selected' : ''); ?>>
                                            <?php echo e($plan->name); ?>

                                            (<?php echo e(system_currency_symbol()); ?><?php echo e(number_format($plan->price, 2)); ?>)
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

                            <div class="mb-4">
                                <label for="trial_ends_at" class="form-label fw-medium text-dark">Trial Ends On</label>
                                <input type="date" class="form-control" id="trial_ends_at" name="trial_ends_at"
                                    value="<?php echo e(old('trial_ends_at', $salon->trial_ends_at ? $salon->trial_ends_at->format('Y-m-d') : '')); ?>">
                            </div>

                            <hr class="my-4">

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo e(old('is_active', $salon->is_active) ? 'checked' : ''); ?>>
                                <label class="form-check-label fw-bold text-dark" for="is_active">Active Status</label>
                            </div>
                            <small class="text-muted d-block">Disable to suspend access for this salon immediately.</small>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary fw-bold shadow-sm">
                                    <i class="fas fa-save me-2"></i> Update Salon
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-danger mb-3">Danger Zone</h6>
                            <p class="small text-muted mb-3">Irreversible actions regarding this workspace.</p>
                            <button type="button" class="btn btn-outline-danger w-100" onclick="confirmDeactivate()">
                                <i class="fas fa-trash-alt me-2"></i> Delete Salon
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form id="deactivateForm" method="POST" action="<?php echo e(route('admin.salons.destroy', $salon)); ?>" class="d-none">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
        </form>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            function confirmDeactivate() {
                Swal.fire({
                    title: 'Delete Salon?',
                    text: "You are about to permanently delete <?php echo e($salon->name); ?>. This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Delete it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deactivateForm').submit();
                    }
                });
            }

            // Form validation
            document.getElementById('salonEditForm').addEventListener('submit', function (e) {
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
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\super-admin\salons\edit.blade.php ENDPATH**/ ?>