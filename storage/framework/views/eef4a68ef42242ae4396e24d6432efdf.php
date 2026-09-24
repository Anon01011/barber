<?php $__env->startSection('title', 'Edit Customer - ' . ($customer->full_name ?? $customer->name)); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Edit Customer</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.customers.index')); ?>">Customers</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo e($customer->full_name ?? $customer->name); ?>

                        </li>
                    </ol>
                </nav>
            </div>
            <a href="<?php echo e(route('admin.customers.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary"><i class="fas fa-user-edit me-2"></i>Edit Information</h5>
                        <span class="badge bg-<?php echo e($customer->status === 'active' ? 'success' : 'secondary'); ?>">
                            <?php echo e(ucfirst($customer->status)); ?>

                        </span>
                    </div>
                    <div class="card-body p-4">
                        <form action="<?php echo e(route('admin.customers.update', $customer)); ?>" method="POST" id="customerForm">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            <!-- Basic Information -->
                            <div class="row g-4">
                                <div class="col-12">
                                    <h6 class="text-uppercase text-muted small fw-bold mb-3">Basic Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="first_name" class="form-label">First Name <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-user text-muted"></i></span>
                                                <input type="text"
                                                    class="form-control <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="first_name" name="first_name"
                                                    value="<?php echo e(old('first_name', $customer->first_name)); ?>" required
                                                    placeholder="John">
                                                <?php $__errorArgs = ['first_name'];
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

                                        <div class="col-md-4">
                                            <label for="last_name" class="form-label">Last Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-user text-muted"></i></span>
                                                <input type="text"
                                                    class="form-control <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="last_name" name="last_name"
                                                    value="<?php echo e(old('last_name', $customer->last_name)); ?>" placeholder="Doe">
                                                <?php $__errorArgs = ['last_name'];
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

                                        <div class="col-md-4">
                                            <label for="customer_id" class="form-label">Customer ID</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-id-card text-muted"></i></span>
                                                <input type="text" class="form-control" id="customer_id"
                                                    value="<?php echo e($customer->customer_id); ?>" readonly disabled>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-envelope text-muted"></i></span>
                                                <input type="email"
                                                    class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email"
                                                    name="email"
                                                    value="<?php echo e(old('email', \App\Helpers\CustomerDataHelper::shouldMaskData() ? $customer->display_email : $customer->email)); ?>"
                                                    placeholder="john@example.com">
                                                <?php $__errorArgs = ['email'];
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

                                        <div class="col-md-6">
                                            <label for="gender" class="form-label">Gender</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-venus-mars text-muted"></i></span>
                                                <select class="form-select <?php $__errorArgs = ['gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="gender" name="gender">
                                                    <option value="">Select Gender</option>
                                                    <option value="male" <?php echo e(old('gender', $customer->gender) == 'male' ? 'selected' : ''); ?>>Male</option>
                                                    <option value="female" <?php echo e(old('gender', $customer->gender) == 'female' ? 'selected' : ''); ?>>Female</option>
                                                    <option value="other" <?php echo e(old('gender', $customer->gender) == 'other' ? 'selected' : ''); ?>>Other</option>
                                                </select>
                                                <?php $__errorArgs = ['gender'];
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
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="my-2">
                                </div>

                                <!-- Contact Information -->
                                <div class="col-12">
                                    <h6 class="text-uppercase text-muted small fw-bold mb-3">Contact Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">Mobile Number <span
                                                    class="text-danger">*</span></label>
                                            <input type="tel" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                id="phone" name="phone" value="<?php echo e(old('phone', $customer->phone)); ?>"
                                                required placeholder="Enter Mobile Number">
                                            <input type="hidden" name="country_code"
                                                value="<?php echo e(old('country_code', $customer->country_code ?? '+91')); ?>">
                                            <?php $__errorArgs = ['phone'];
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

                                        <div class="col-md-6">
                                            <label for="secondary_number" class="form-label">Secondary Number</label>
                                            <input type="tel"
                                                class="form-control <?php $__errorArgs = ['secondary_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                id="secondary_number" name="secondary_number"
                                                value="<?php echo e(old('secondary_number', $customer->secondary_number)); ?>"
                                                placeholder="Alternate Contact Number">
                                            <input type="hidden" name="secondary_country_code"
                                                value="<?php echo e(old('secondary_country_code', $customer->secondary_country_code ?? '+91')); ?>">
                                            <?php $__errorArgs = ['secondary_number'];
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

                                        <div class="col-md-6">
                                            <label for="preferred_contact" class="form-label">Preferred Contact Method <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-comment-dots text-muted"></i></span>
                                                <select class="form-select <?php $__errorArgs = ['preferred_contact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="preferred_contact" name="preferred_contact" required>
                                                    <option value="phone" <?php echo e(old('preferred_contact', $customer->preferred_contact) == 'phone' ? 'selected' : ''); ?>>Phone
                                                    </option>
                                                    <option value="email" <?php echo e(old('preferred_contact', $customer->preferred_contact) == 'email' ? 'selected' : ''); ?>>Email
                                                    </option>
                                                    <option value="sms" <?php echo e(old('preferred_contact', $customer->preferred_contact) == 'sms' ? 'selected' : ''); ?>>SMS
                                                    </option>
                                                </select>
                                                <?php $__errorArgs = ['preferred_contact'];
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

                                        <div class="col-md-6">
                                            <label for="location" class="form-label">Location</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-map-marker-alt text-muted"></i></span>
                                                <input type="text"
                                                    class="form-control <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="location" name="location"
                                                    value="<?php echo e(old('location', $customer->location)); ?>"
                                                    placeholder="City, State">
                                                <?php $__errorArgs = ['location'];
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
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="my-2">
                                </div>

                                <!-- Personal Details -->
                                <div class="col-12">
                                    <h6 class="text-uppercase text-muted small fw-bold mb-3">Personal Details</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="dob" class="form-label">Date of Birth</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-birthday-cake text-muted"></i></span>
                                                <input type="date" class="form-control <?php $__errorArgs = ['dob'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="dob" name="dob"
                                                    value="<?php echo e(old('dob', $customer->dob ? $customer->dob->format('Y-m-d') : '')); ?>">
                                                <?php $__errorArgs = ['dob'];
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

                                        <div class="col-md-4">
                                            <label for="anniversary" class="form-label">Anniversary</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-heart text-muted"></i></span>
                                                <input type="date"
                                                    class="form-control <?php $__errorArgs = ['anniversary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="anniversary" name="anniversary"
                                                    value="<?php echo e(old('anniversary', $customer->anniversary ? $customer->anniversary->format('Y-m-d') : '')); ?>">
                                                <?php $__errorArgs = ['anniversary'];
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

                                        <div class="col-md-4">
                                            <label for="source" class="form-label">Source</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-bullhorn text-muted"></i></span>
                                                <select class="form-select <?php $__errorArgs = ['source'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="source" name="source">
                                                    <option value="">Select Source</option>
                                                    <option value="walk-in" <?php echo e(old('source', $customer->source) == 'walk-in' ? 'selected' : ''); ?>>Walk-in</option>
                                                    <option value="referral" <?php echo e(old('source', $customer->source) == 'referral' ? 'selected' : ''); ?>>Referral</option>
                                                    <option value="online" <?php echo e(old('source', $customer->source) == 'online' ? 'selected' : ''); ?>>Online</option>
                                                    <option value="social-media" <?php echo e(old('source', $customer->source) == 'social-media' ? 'selected' : ''); ?>>Social Media
                                                    </option>
                                                    <option value="advertisement" <?php echo e(old('source', $customer->source) == 'advertisement' ? 'selected' : ''); ?>>
                                                        Advertisement</option>
                                                    <option value="other" <?php echo e(old('source', $customer->source) == 'other' ? 'selected' : ''); ?>>Other</option>
                                                </select>
                                                <?php $__errorArgs = ['source'];
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
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="my-2">
                                </div>

                                <!-- SMS/WhatsApp Preferences -->
                                <div class="col-12">
                                    <h6 class="text-uppercase text-muted small fw-bold mb-3">SMS/WhatsApp Preferences</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="send_promotional_sms"
                                                    name="send_promotional_sms" value="1" <?php echo e(old('send_promotional_sms', $customer->send_promotional_sms) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="send_promotional_sms">
                                                    <i class="fas fa-bullhorn me-1"></i> Send Promotional SMS/WhatsApp
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="send_transactional_sms"
                                                    name="send_transactional_sms" value="1" <?php echo e(old('send_transactional_sms', $customer->send_transactional_sms) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="send_transactional_sms">
                                                    <i class="fas fa-receipt me-1"></i> Send Transactional SMS/WhatsApp
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="my-2">
                                </div>

                                <!-- Additional Information -->
                                <div class="col-12">
                                    <h6 class="text-uppercase text-muted small fw-bold mb-3">Additional Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="status" class="form-label">Account Status <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-toggle-on text-muted"></i></span>
                                                <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="status" name="status" required>
                                                    <option value="active" <?php echo e(old('status', $customer->status) == 'active' ? 'selected' : ''); ?>>Active</option>
                                                    <option value="inactive" <?php echo e(old('status', $customer->status) == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
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
                                        </div>

                                        <div class="col-md-12">
                                            <label for="address" class="form-label">Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-map-marker-alt text-muted"></i></span>
                                                <textarea class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="address" name="address" rows="2"
                                                    placeholder="123 Main St, City, Country"><?php echo e(old('address', $customer->address)); ?></textarea>
                                                <?php $__errorArgs = ['address'];
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

                                        <div class="col-md-12">
                                            <label for="notes" class="form-label">Customer Note</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-sticky-note text-muted"></i></span>
                                                <textarea class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="notes" name="notes" rows="3"
                                                    placeholder="Any special instructions or notes..."><?php echo e(old('notes', $customer->notes)); ?></textarea>
                                                <?php $__errorArgs = ['notes'];
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

                                        <div class="col-md-12">
                                            <label for="medical_notes" class="form-label">Medical Notes</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-notes-medical text-muted"></i></span>
                                                <textarea class="form-control <?php $__errorArgs = ['medical_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="medical_notes" name="medical_notes" rows="2"
                                                    placeholder="Any allergies, medical conditions, or special requirements..."><?php echo e(old('medical_notes', $customer->medical_notes)); ?></textarea>
                                                <?php $__errorArgs = ['medical_notes'];
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
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="my-2">
                                </div>

                                <!-- Custom Fields -->
                                <div class="col-12">
                                    <h6 class="text-uppercase text-muted small fw-bold mb-3">Custom Fields</h6>
                                    <div id="custom-fields-container">
                                        <?php if($customer->custom_fields && is_array($customer->custom_fields)): ?>
                                            <?php $__currentLoopData = $customer->custom_fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="row g-2 mb-2 custom-field-row">
                                                    <div class="col-md-5">
                                                        <input type="text" class="form-control"
                                                            name="custom_fields[<?php echo e($index); ?>][label]"
                                                            value="<?php echo e($field['label'] ?? ''); ?>" placeholder="Field Label">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control"
                                                            name="custom_fields[<?php echo e($index); ?>][value]"
                                                            value="<?php echo e($field['value'] ?? ''); ?>" placeholder="Field Value">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger remove-custom-field w-100">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="add-custom-field">
                                        <i class="fas fa-plus me-1"></i> Add Custom Field
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                <a href="<?php echo e(route('admin.customers.index')); ?>" class="btn btn-light me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-1"></i> Update Customer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Custom fields functionality
                let customFieldIndex = <?php echo e($customer->custom_fields ? count($customer->custom_fields) : 0); ?>;

                document.getElementById('add-custom-field').addEventListener('click', function () {
                    const container = document.getElementById('custom-fields-container');

                    const fieldHtml = `
                                        <div class="row g-2 mb-2 custom-field-row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="custom_fields[${customFieldIndex}][label]" placeholder="Field Label">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="custom_fields[${customFieldIndex}][value]" placeholder="Field Value">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-sm btn-danger remove-custom-field w-100">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `;

                    container.insertAdjacentHTML('beforeend', fieldHtml);
                    customFieldIndex++;
                });

                // Remove custom field
                document.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-custom-field') || e.target.parentElement.classList.contains('remove-custom-field')) {
                        e.target.closest('.custom-field-row').remove();
                    }
                });

                // Email validation based on preferred contact
                const preferredContactSelect = document.getElementById('preferred_contact');
                const emailInput = document.getElementById('email');

                preferredContactSelect.addEventListener('change', function () {
                    if (this.value === 'email') {
                        emailInput.setAttribute('required', 'required');
                        emailInput.closest('.col-md-6').querySelector('label').innerHTML = 'Email Address <span class="text-danger">*</span>';
                    } else {
                        emailInput.removeAttribute('required');
                        emailInput.closest('.col-md-6').querySelector('label').innerHTML = 'Email Address';
                    }
                });

                // Trigger on page load
                preferredContactSelect.dispatchEvent(new Event('change'));
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\customers\edit.blade.php ENDPATH**/ ?>