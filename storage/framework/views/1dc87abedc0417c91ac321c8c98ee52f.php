<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Profile Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow border-0 overflow-hidden">
                    <div class="card-header bg-primary position-relative"
                        style="height: 150px; background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);">
                        <!-- Optional: Add a cover image here if available -->
                    </div>
                    <div class="card-body position-relative pt-0 pb-4">
                        <div class="d-sm-flex align-items-end">
                            <div class="profile-avatar-container position-relative mb-3 mb-sm-0 me-4"
                                style="margin-top: -75px;">
                                <?php if($user->avatar): ?>
                                    <img src="<?php echo e(Storage::url($user->avatar)); ?>" alt="<?php echo e($user->name); ?>"
                                        class="img-thumbnail rounded-circle shadow"
                                        style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($user->name)); ?>&background=fff&color=4e73df&size=150&font-size=0.5"
                                        alt="<?php echo e($user->name); ?>" class="img-thumbnail rounded-circle shadow"
                                        style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;">
                                <?php endif; ?>
                            </div>
                            <div class="mb-3 mb-sm-0 flex-grow-1 pt-3">
                                <h1 class="h3 text-gray-900 font-weight-bold mb-1"><?php echo e($user->name); ?></h1>
                                <p class="text-muted mb-2"><i class="fas fa-envelope me-2"></i><?php echo e($user->email); ?></p>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span
                                            class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $role->name))); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge bg-light text-dark border rounded-pill px-3">
                                        <i class="fas fa-calendar-alt me-1"></i> Joined
                                        <?php echo e($user->created_at->format('M Y')); ?>

                                    </span>
                                    <?php if($user->status === 'active'): ?>
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
                                            <i class="fas fa-check-circle me-1"></i> Active
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3">
                                            <i class="fas fa-ban me-1"></i> <?php echo e(ucfirst($user->status)); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="d-none d-md-block pt-3">
                                <!-- Action buttons could go here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0 px-3">
                        <ul class="nav nav-tabs nav-tabs-custom card-header-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active py-3 px-4 fw-bold" id="overview-tab" data-bs-toggle="tab"
                                    data-bs-target="#overview" type="button" role="tab" aria-controls="overview"
                                    aria-selected="true">
                                    <i class="fas fa-user me-2"></i>Overview
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-3 px-4 fw-bold" id="edit-tab" data-bs-toggle="tab"
                                    data-bs-target="#edit" type="button" role="tab" aria-controls="edit"
                                    aria-selected="false">
                                    <i class="fas fa-edit me-2"></i>Edit Profile
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-3 px-4 fw-bold" id="security-tab" data-bs-toggle="tab"
                                    data-bs-target="#security" type="button" role="tab" aria-controls="security"
                                    aria-selected="false">
                                    <i class="fas fa-shield-alt me-2"></i>Security
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content" id="profileTabsContent">

                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active" id="overview" role="tabpanel"
                                aria-labelledby="overview-tab">
                                <h5 class="text-primary font-weight-bold mb-4">Account Details</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-primary">
                                                    <i class="fas fa-id-card fa-lg"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block text-uppercase fw-bold"
                                                        style="font-size: 0.7rem;">Full Name</small>
                                                    <span class="fw-bold text-dark"><?php echo e($user->name); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-primary">
                                                    <i class="fas fa-envelope fa-lg"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block text-uppercase fw-bold"
                                                        style="font-size: 0.7rem;">Email Address</small>
                                                    <span class="fw-bold text-dark"><?php echo e($user->email); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-primary">
                                                    <i class="fas fa-phone fa-lg"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block text-uppercase fw-bold"
                                                        style="font-size: 0.7rem;">Phone Number</small>
                                                    <span
                                                        class="fw-bold text-dark"><?php echo e($user->phone ?? 'Not Provided'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-primary">
                                                    <i class="fas fa-map-marker-alt fa-lg"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block text-uppercase fw-bold"
                                                        style="font-size: 0.7rem;">Address</small>
                                                    <span
                                                        class="fw-bold text-dark"><?php echo e($user->address ?? 'Not Provided'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-primary">
                                                    <i class="fas fa-user-tag fa-lg"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block text-uppercase fw-bold"
                                                        style="font-size: 0.7rem;">Role</small>
                                                    <span
                                                        class="fw-bold text-dark"><?php echo e($user->roles->pluck('name')->map(fn($n) => ucfirst(str_replace('_', ' ', $n)))->join(', ')); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-primary">
                                                    <i class="fas fa-clock fa-lg"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block text-uppercase fw-bold"
                                                        style="font-size: 0.7rem;">Last Login</small>
                                                    <span
                                                        class="fw-bold text-dark"><?php echo e($user->last_login_at ? $user->last_login_at->format('F d, Y h:i A') : 'Never'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Profile Tab -->
                            <div class="tab-pane fade" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                                <div class="row">
                                    <div class="col-12 col-lg-10">
                                        <h5 class="text-primary font-weight-bold mb-4">Update Profile Information</h5>
                                        <form method="post" action="<?php echo e(route('profile.update')); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('patch'); ?>

                                            <div class="mb-4">
                                                <label for="name" class="form-label fw-bold text-gray-700">Display
                                                    Name</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-end-0"><i
                                                            class="fas fa-user text-muted"></i></span>
                                                    <input type="text"
                                                        class="form-control border-start-0 ps-0 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                        id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>"
                                                        required autofocus autocomplete="name">
                                                </div>
                                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>

                                            <div class="mb-4">
                                                <label for="email" class="form-label fw-bold text-gray-700">Email
                                                    Address</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-end-0"><i
                                                            class="fas fa-envelope text-muted"></i></span>
                                                    <input type="email"
                                                        class="form-control border-start-0 ps-0 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                        id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>"
                                                        required autocomplete="username">
                                                </div>
                                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                                <?php if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail()): ?>
                                                    <div
                                                        class="mt-3 p-3 bg-warning-subtle rounded border border-warning-subtle">
                                                        <p class="text-sm text-gray-800 mb-2">
                                                            <i class="fas fa-exclamation-circle me-1"></i> Your email address is
                                                            unverified.
                                                        </p>
                                                        <button form="send-verification" class="btn btn-sm btn-warning">Re-send
                                                            Verification Email</button>
                                                    </div>
                                                    <?php if(session('status') === 'verification-link-sent'): ?>
                                                        <div class="alert alert-success mt-2 mb-0" role="alert">
                                                            A new verification link has been sent to your email address.
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <label for="phone" class="form-label fw-bold text-gray-700">Phone
                                                        Number</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0"><i
                                                                class="fas fa-phone text-muted"></i></span>
                                                        <input type="text"
                                                            class="form-control border-start-0 ps-0 <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                            id="phone" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>"
                                                            autocomplete="tel">
                                                    </div>
                                                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <label for="address"
                                                        class="form-label fw-bold text-gray-700">Address</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0"><i
                                                                class="fas fa-map-marker-alt text-muted"></i></span>
                                                        <input type="text"
                                                            class="form-control border-start-0 ps-0 <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                            id="address" name="address"
                                                            value="<?php echo e(old('address', $user->address)); ?>"
                                                            autocomplete="street-address">
                                                    </div>
                                                    <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-end gap-3 mt-5">
                                                <button type="submit"
                                                    class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                                                    <i class="fas fa-save me-2"></i>Save Changes
                                                </button>
                                                <?php if(session('status') === 'profile-updated'): ?>
                                                    <span class="text-success small fade-in fw-bold"><i
                                                            class="fas fa-check-circle me-1"></i> Saved Successfully</span>
                                                <?php endif; ?>
                                            </div>
                                        </form>
                                        <form id="send-verification" method="post"
                                            action="<?php echo e(route('verification.send')); ?>">
                                            <?php echo csrf_field(); ?>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Tab -->
                            <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                                <div class="row">
                                    <div class="col-12 col-lg-10">
                                        <!-- Password Section -->
                                        <div class="mb-5">
                                            <h5 class="text-primary font-weight-bold mb-4">Change Password</h5>
                                            <form method="post" action="<?php echo e(route('password.update')); ?>">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('put'); ?>

                                                <div class="mb-3">
                                                    <label for="current_password"
                                                        class="form-label fw-bold text-gray-700">Current Password</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0"><i
                                                                class="fas fa-lock text-muted"></i></span>
                                                        <input type="password"
                                                            class="form-control border-start-0 ps-0 <?php if($errors->updatePassword->has('current_password')): ?> is-invalid <?php endif; ?>"
                                                            id="current_password" name="current_password"
                                                            autocomplete="current-password">
                                                    </div>
                                                    <?php if($errors->updatePassword->has('current_password')): ?>
                                                        <div class="text-danger small mt-1">
                                                            <?php echo e($errors->updatePassword->first('current_password')); ?></div>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="password" class="form-label fw-bold text-gray-700">New
                                                            Password</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light border-end-0"><i
                                                                    class="fas fa-key text-muted"></i></span>
                                                            <input type="password"
                                                                class="form-control border-start-0 ps-0 <?php if($errors->updatePassword->has('password')): ?> is-invalid <?php endif; ?>"
                                                                id="password" name="password" autocomplete="new-password">
                                                        </div>
                                                        <?php if($errors->updatePassword->has('password')): ?>
                                                            <div class="text-danger small mt-1">
                                                                <?php echo e($errors->updatePassword->first('password')); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="password_confirmation"
                                                            class="form-label fw-bold text-gray-700">Confirm New
                                                            Password</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light border-end-0"><i
                                                                    class="fas fa-check-circle text-muted"></i></span>
                                                            <input type="password"
                                                                class="form-control border-start-0 ps-0 <?php if($errors->updatePassword->has('password_confirmation')): ?> is-invalid <?php endif; ?>"
                                                                id="password_confirmation" name="password_confirmation"
                                                                autocomplete="new-password">
                                                        </div>
                                                        <?php if($errors->updatePassword->has('password_confirmation')): ?>
                                                            <div class="text-danger small mt-1">
                                                                <?php echo e($errors->updatePassword->first('password_confirmation')); ?>

                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center justify-content-end gap-3 mt-3">
                                                    <button type="submit"
                                                        class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                                                        <i class="fas fa-key me-2"></i>Update Password
                                                    </button>
                                                    <?php if(session('status') === 'password-updated'): ?>
                                                        <span class="text-success small fade-in fw-bold"><i
                                                                class="fas fa-check-circle me-1"></i> Password Updated</span>
                                                    <?php endif; ?>
                                                </div>
                                            </form>
                                        </div>

                                        <hr class="my-5">

                                        <!-- Delete Account Section -->
                                        <div class="bg-danger-subtle rounded-3 p-4 border border-danger-subtle">
                                            <div class="d-flex align-items-start">
                                                <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-danger">
                                                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                                                </div>
                                                <div>
                                                    <h5 class="text-danger font-weight-bold">Delete Account</h5>
                                                    <p class="text-muted small mb-3">
                                                        Once your account is deleted, all of its resources and data will be
                                                        permanently deleted. Before deleting your account, please download
                                                        any data or information that you wish to retain.
                                                    </p>
                                                    <button type="button" class="btn btn-danger btn-sm rounded-pill px-3"
                                                        data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
                                                        <i class="fas fa-trash-alt me-1"></i> Delete Account
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form method="post" action="<?php echo e(route('profile.destroy')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('delete'); ?>
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel"><i
                                class="fas fa-exclamation-triangle me-2"></i>Delete Account</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-gray-700 mb-4">
                            Are you sure you want to delete your account? Once your account is deleted, all of its resources
                            and data will be permanently deleted. Please enter your password to confirm you would like to
                            permanently delete your account.
                        </p>
                        <div class="mb-3">
                            <label for="password_deletion" class="form-label fw-bold">Password</label>
                            <input type="password"
                                class="form-control <?php if($errors->userDeletion->has('password')): ?> is-invalid <?php endif; ?>"
                                id="password_deletion" name="password" placeholder="Enter your password to confirm">
                            <?php if($errors->userDeletion->has('password')): ?>
                                <div class="invalid-feedback"><?php echo e($errors->userDeletion->first('password')); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
        <style>
            .nav-tabs-custom .nav-link {
                color: #6c757d;
                border: none;
                border-bottom: 3px solid transparent;
                transition: all 0.3s ease;
            }

            .nav-tabs-custom .nav-link:hover {
                color: #4e73df;
                border-color: transparent;
            }

            .nav-tabs-custom .nav-link.active {
                color: #4e73df;
                background-color: transparent;
                border-color: #4e73df;
            }

            .fade-in {
                animation: fadeIn 0.5s;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .form-control:focus {
                border-color: #bac8f3;
                box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
            }

            .input-group-text {
                background-color: #f8f9fc;
                border-color: #d1d3e2;
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php if($errors->userDeletion->any()): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var myModal = new bootstrap.Modal(document.getElementById('confirmUserDeletionModal'));
                myModal.show();
            });
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views/profile/edit.blade.php ENDPATH**/ ?>