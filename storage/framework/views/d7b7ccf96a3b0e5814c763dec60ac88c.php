<?php $__env->startSection('title', 'Staff Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4 py-4">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-staff">Staff Management</h2>
                <p class="text-muted mb-0">Manage your salon staff members and their roles</p>
            </div>
            <div class="d-flex gap-2 mt-3 mt-md-0">
                <a href="<?php echo e(route('admin.staff.schedules')); ?>" class="btn btn-staff-outline"
                    style="background-color: #ec4899; color: #fff;">
                    <i class="fas fa-calendar-week me-2"></i>View Schedules
                </a>
                <button type="button" class="btn btn-staff-outline" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                    <i class="fas fa-plus me-2"></i>Add New Staff
                </button>
            </div>
        </div>



        <!-- Content Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="row g-3 align-items-center justify-content-between">
                    <div class="col-12 col-md-auto">
                        <h5 class="card-title mb-0 text-staff">Staff List</h5>
                    </div>
                    <div class="col-12 col-md-auto">
                        <form action="<?php echo e(route('admin.staff.index')); ?>" method="GET" class="d-flex gap-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0"
                                    placeholder="Search staff..." value="<?php echo e(request('search')); ?>">
                            </div>
                            <select name="status" class="form-select form-select-sm" style="width: auto;"
                                onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                                <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inactive
                                </option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-muted small fw-bold text-uppercase">Staff Member</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">Contact</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">Role</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">Status</th>
                                <th class="px-4 py-3 text-end text-muted small fw-bold text-uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <div class="avatar-title rounded-circle bg-primary text-white shadow-sm">
                                                    <?php echo e(collect(explode(' ', $member->name))->map(fn($s) => strtoupper(substr($s, 0, 1)))->take(2)->join('')); ?>

                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold text-dark"><?php echo e($member->name); ?></h6>
                                                <small class="text-muted" style="font-size: 0.75rem;">ID:
                                                    #<?php echo e($member->staff_id ?? $member->id); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex flex-column">
                                            <span class="text-dark small"><i
                                                    class="fas fa-envelope me-2 text-muted"></i><?php echo e($member->email ? \App\Helpers\CustomerDataHelper::getMaskedStaffEmail($member) : 'No Email'); ?></span>
                                            <?php if($member->phone): ?>
                                                <span class="text-muted small mt-1"><i
                                                        class="fas fa-phone me-2 text-muted"></i><?php echo e(\App\Helpers\CustomerDataHelper::getMaskedStaffPhone($member)); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <?php $__currentLoopData = $member->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span
                                                class="badge bg-light text-dark border fw-normal"><?php echo e(ucfirst(str_replace('_', ' ', $role->name))); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td class="py-3">
                                        <?php if($member->status == 'active'): ?>
                                            <span
                                                class="badge bg-success-subtle text-white border border-success-subtle rounded-pill px-3">Active</span>
                                        <?php else: ?>
                                            <span
                                                class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-light text-muted"
                                                onclick="editStaff(<?php echo e($member->id); ?>)" data-bs-toggle="tooltip"
                                                title="Edit Details">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light text-muted"
                                                onclick="manageSchedule(<?php echo e($member->id); ?>, '<?php echo e($member->name); ?>')"
                                                data-bs-toggle="tooltip" title="Manage Schedule">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                            <form action="<?php echo e(route('admin.staff.destroy', $member->id)); ?>" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-light text-danger"
                                                    data-bs-toggle="tooltip" title="Delete Staff">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                                            <p class="mb-0">No staff members found.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($staff->hasPages()): ?>
                <div class="card-footer bg-white border-top-0 py-3">
                    <?php echo e($staff->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Staff Modal -->
    <?php $__env->startComponent('components.modal', ['id' => 'addStaffModal', 'title' => 'Add New Staff', 'width' => '600px']); ?>
    <form action="<?php echo e(route('admin.staff.store')); ?>" method="POST" id="addStaffForm" class="p-2">
        <?php echo csrf_field(); ?>
        <div class="row g-3">
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="create_allow_login" name="allow_login" value="1"
                        onchange="toggleCreateLoginFields()">
                    <label class="form-check-label small fw-bold text-muted" for="create_allow_login">Allow Login</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold text-muted mb-1">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
            </div>
            <div class="col-12 login-field" style="display: none;">
                <label class="form-label small fw-bold text-muted mb-1">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" id="create_email" class="form-control" placeholder="Enter email address">
            </div>
            <label class="form-label small fw-bold text-muted mb-1">Phone <span class="text-danger">*</span></label>
            <div class="input-group">

                <input type="text" name="phone" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    placeholder="Mobile number" required value="<?php echo e(old('phone')); ?>">
                <input type="hidden" name="country_code" value="+974">
            </div>
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
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Role <span class="text-danger">*</span></label>
                <select name="role" id="create_role" class="form-select" required>
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($role->name); ?>" <?php echo e($role->name == 'employee' ? 'selected' : ''); ?>>
                            <?php echo e(ucfirst(str_replace('_', ' ', $role->name))); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Commission Profile</label>
                <select name="commission_profile_id" class="form-select">
                    <option value="">No Commission Profile</option>
                    <?php $__currentLoopData = \App\Models\CommissionProfile::where('salon_id', auth()->user()->salon_id)->where('is_active', true)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($profile->id); ?>"><?php echo e($profile->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold text-muted mb-1">Address</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Enter full address"></textarea>
            </div>
            <div class="col-md-6 login-field" style="display: none;">
                <label class="form-label small fw-bold text-muted mb-1">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" id="create_password" class="form-control">
            </div>
            <div class="col-md-6 login-field" style="display: none;">
                <label class="form-label small fw-bold text-muted mb-1">Confirm Password <span
                        class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" id="create_password_confirmation" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Branch</label>
                <select name="branch_id" class="form-select">
                    <option value="">Select Branch (Optional)</option>
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold text-muted mb-1">Assign Services</label>
                <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                    <div class="d-flex justify-content-between mb-2">
                        <input type="text" class="form-control form-control-sm w-50" placeholder="Search services..."
                            onkeyup="filterServices(this, 'create_services_container')">
                        <div>
                            <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none"
                                onclick="toggleAllServices('create_services_container', true)">Select All</button>
                            <span class="text-muted mx-1">|</span>
                            <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none"
                                onclick="toggleAllServices('create_services_container', false)">Deselect All</button>
                        </div>
                    </div>
                    <div id="create_services_container">
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-check service-item">
                                <input class="form-check-input" type="checkbox" name="services[]" value="<?php echo e($service->id); ?>"
                                    id="create_service_<?php echo e($service->id); ?>">
                                <label class="form-check-label small" for="create_service_<?php echo e($service->id); ?>">
                                    <?php echo e($service->name); ?>

                                </label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mt-4 pt-3 border-top">
            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-staff px-4">Create Staff Member</button>
        </div>
    </form>
    <?php echo $__env->renderComponent(); ?>

    <!-- Edit Staff Modal -->
    <?php $__env->startComponent('components.modal', ['id' => 'editStaffModal', 'title' => 'Edit Staff Details', 'width' => '600px']); ?>
    <form id="editStaffForm" method="POST" class="p-2">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="row g-3">
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="edit_allow_login" name="allow_login" value="1"
                        onchange="toggleEditLoginFields()">
                    <label class="form-check-label small fw-bold text-muted" for="edit_allow_login">Allow Login</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold text-muted mb-1">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="col-12 edit-login-field" style="display: none;">
                <label class="form-label small fw-bold text-muted mb-1">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" id="edit_email" class="form-control">
            </div>
            <label class="form-label small fw-bold text-muted mb-1">Phone <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="text" name="phone" id="edit_phone" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    placeholder="Mobile number" required>
                <input type="hidden" name="country_code" value="+974">
            </div>
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
            <div class="col-md-6 edit-login-field" style="display: none;">
                <label class="form-label small fw-bold text-muted mb-1">Role <span class="text-danger">*</span></label>
                <select name="role" id="edit_role" class="form-select">
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($role->name); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $role->name))); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Commission Profile</label>
                <select name="commission_profile_id" id="edit_commission_profile_id" class="form-select">
                    <option value="">No Commission Profile</option>
                    <?php $__currentLoopData = \App\Models\CommissionProfile::where('salon_id', auth()->user()->salon_id)->where('is_active', true)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($profile->id); ?>"><?php echo e($profile->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold text-muted mb-1">Address</label>
                <textarea name="address" id="edit_address" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6 edit-login-field" style="display: none;">
                <label class="form-label small fw-bold text-muted mb-1">Password (Optional)</label>
                <input type="password" name="password" id="edit_password" class="form-control"
                    placeholder="Leave blank to keep">
            </div>
            <div class="col-md-6 edit-login-field" style="display: none;">
                <label class="form-label small fw-bold text-muted mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" id="edit_password_confirmation" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Branch</label>
                <select name="branch_id" id="edit_branch" class="form-select">
                    <option value="">Select Branch (Optional)</option>
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Status <span class="text-danger">*</span></label>
                <select name="status" id="edit_status" class="form-select" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold text-muted mb-1">Assign Services</label>
                <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                    <div class="d-flex justify-content-between mb-2">
                        <input type="text" class="form-control form-control-sm w-50" placeholder="Search services..."
                            onkeyup="filterServices(this, 'edit_services_container')">
                        <div>
                            <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none"
                                onclick="toggleAllServices('edit_services_container', true)">Select All</button>
                            <span class="text-muted mx-1">|</span>
                            <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none"
                                onclick="toggleAllServices('edit_services_container', false)">Deselect All</button>
                        </div>
                    </div>
                    <div id="edit_services_container">
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-check service-item">
                                <input class="form-check-input" type="checkbox" name="services[]" value="<?php echo e($service->id); ?>"
                                    id="edit_service_<?php echo e($service->id); ?>">
                                <label class="form-check-label small" for="edit_service_<?php echo e($service->id); ?>">
                                    <?php echo e($service->name); ?>

                                </label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end mt-4 pt-3 border-top">
            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-staff px-4">Update Staff</button>
        </div>
    </form>
    <?php echo $__env->renderComponent(); ?>

    <!-- Schedule Modal -->
    <?php $__env->startComponent('components.modal', ['id' => 'scheduleModal', 'title' => 'Manage Schedule', 'width' => '800px']); ?>
    <div class="modal-body p-0">
        <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
            <h6 id="scheduleStaffName" class="mb-0 fw-bold text-staff"></h6>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="toggleAllDays" onchange="toggleAllDays(this)">
                <label class="form-check-label small fw-bold text-muted mb-0 ms-1" for="toggleAllDays">Toggle All
                    Days</label>
            </div>
        </div>

        <form id="scheduleForm" class="p-3">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="table-responsive border rounded mb-3">
                <table class="table table-sm table-borderless mb-0 small align-middle">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="py-2 ps-3">Day</th>
                            <th class="py-2 text-center">Status</th>
                            <th class="py-2">Regular Hours</th>
                            <th class="py-2 text-center">Overtime</th>
                            <th class="py-2">OT Hours</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleTableBody">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-sm btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-staff px-4">Save Schedule</button>
            </div>
        </form>

        <div class="bg-light p-3 border-top">
            <h6 class="mb-3 small fw-bold text-uppercase text-muted">Absences / Time Off</h6>
            <form id="absenceForm" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="datetime-local" name="start_at" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <input type="datetime-local" name="end_at" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <input type="text" name="reason" class="form-control" placeholder="Reason">
                            <button class="btn btn-outline-primary" type="submit">Add</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="table-responsive bg-white border rounded" style="max-height: 150px; overflow-y: auto;">
                <table class="table table-sm table-hover mb-0 small">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th class="py-1 ps-3">Start</th>
                            <th class="py-1">End</th>
                            <th class="py-1">Reason</th>
                            <th class="py-1 text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody id="absencesTableBody">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php echo $__env->renderComponent(); ?>

    <?php $__env->startPush('styles'); ?>
        <style>
            /* Staff Theme Colors */
            .text-staff {
                color: #ec4899 !important;
            }

            .btn-staff {
                background-color: #ec4899;
                border-color: #ec4899;
                color: white;
            }

            .btn-staff:hover {
                background-color: #db2777;
                border-color: #db2777;
                color: white;
            }

            .btn-staff-outline {
                border-color: #ec4899;
                color: #ec4899;
                background-color: transparent;
            }

            .btn-staff-outline:hover {
                background-color: #ec4899;
                border-color: #ec4899;
                color: white;
            }

            /* Avatar Styles */
            .avatar {
                width: 35px;
                height: 35px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                vertical-align: middle;
            }

            .avatar-title {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
                font-weight: 600;
                padding: 2px;
                text-align: center;
                line-height: 1;
            }

            /* Background Utilities */
            .bg-primary-subtle {
                background-color: rgba(13, 110, 253, 0.1);
            }

            .bg-success-subtle {
                background-color: rgba(25, 135, 84, 0.1);
            }

            .bg-secondary-subtle {
                background-color: rgba(108, 117, 125, 0.1);
            }

            /* Z-Index Fixes */
            .modal {
                z-index: 1060 !important;
            }

            .modal-backdrop {
                z-index: 1055 !important;
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            let currentStaffId = null;
            const salonDefaultStart = "<?php echo e(app(\App\Services\SettingsService::class)->get('working_hours_start', '09:00')); ?>";
            const salonDefaultEnd = "<?php echo e(app(\App\Services\SettingsService::class)->get('working_hours_end', '17:00')); ?>";

            // Initialize Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            function editStaff(id) {
                fetch(`<?php echo e(url('')); ?>/<?php echo e(request()->current_salon->slug ?? ''); ?>/admin/staff/${id}/edit`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('edit_name').value = data.name;
                        document.getElementById('edit_email').value = data.email;

                        const phoneInput = document.getElementById('edit_phone');
                        phoneInput.value = data.phone || '';

                        // Let intlTelInput update the flag if it is initialized
                        if (window.customerPhoneHandler) {
                            const handlerData = window.customerPhoneHandler.phoneInputs.find(p => p.input === phoneInput);
                            if (handlerData && handlerData.iti && data.phone) {
                                handlerData.iti.setNumber(data.phone);
                            }
                        }

                        // Trigger input event to auto-detect country for legacy numbers
                        phoneInput.dispatchEvent(new Event('input', { bubbles: true }));

                        document.getElementById('edit_address').value = data.address || '';
                        document.getElementById('edit_status').value = data.status;
                        document.getElementById('edit_role').value = data.role;
                        document.getElementById('edit_branch').value = data.branch_id || '';
                        document.getElementById('edit_commission_profile_id').value = data.commission_profile_id || '';

                        // Handle login fields
                        document.getElementById('edit_allow_login').checked = data.allow_login;
                        toggleEditLoginFields();

                        // Handle services
                        const serviceIds = data.services || [];
                        document.querySelectorAll('#edit_services_container input[type="checkbox"]').forEach(cb => {
                            cb.checked = serviceIds.includes(parseInt(cb.value));
                        });

                        document.getElementById('editStaffForm').action = `<?php echo e(url('')); ?>/<?php echo e(request()->current_salon->slug ?? ''); ?>/admin/staff/${id}`;

                        var myModal = new bootstrap.Modal(document.getElementById('editStaffModal'));
                        myModal.show();
                    })

            }

            function toggleCreateLoginFields() {
                const allowLogin = document.getElementById('create_allow_login').checked;
                const fields = document.querySelectorAll('.login-field');
                fields.forEach(field => field.style.display = allowLogin ? 'block' : 'none');

                document.getElementById('create_email').required = allowLogin;
                document.getElementById('create_password').required = allowLogin;
                document.getElementById('create_password_confirmation').required = allowLogin;
                // Role is always required
                document.getElementById('create_role').required = true;
            }

            function toggleEditLoginFields() {
                const allowLogin = document.getElementById('edit_allow_login').checked;
                const fields = document.querySelectorAll('.edit-login-field');
                fields.forEach(field => field.style.display = allowLogin ? 'block' : 'none');

                document.getElementById('edit_email').required = allowLogin;
                // Role is always required
                document.getElementById('edit_role').required = true;
            }

            function filterServices(input, containerId) {
                const filter = input.value.toLowerCase();
                const container = document.getElementById(containerId);
                const items = container.getElementsByClassName('service-item');

                for (let i = 0; i < items.length; i++) {
                    const label = items[i].getElementsByTagName('label')[0];
                    if (label.innerText.toLowerCase().indexOf(filter) > -1) {
                        items[i].style.display = "";
                    } else {
                        items[i].style.display = "none";
                    }
                }
            }

            function toggleAllServices(containerId, checked) {
                const container = document.getElementById(containerId);
                const checkboxes = container.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(cb => {
                    if (cb.closest('.service-item').style.display !== 'none') {
                        cb.checked = checked;
                    }
                });
            }

            function manageSchedule(staffId, staffName) {
                currentStaffId = staffId;
                document.getElementById('scheduleStaffName').textContent = `Schedule for ${staffName}`;

                fetch(`<?php echo e(url('')); ?>/<?php echo e(request()->current_salon->slug ?? ''); ?>/admin/staff/${staffId}/schedule`)
                    .then(res => res.json())
                    .then(data => {
                        const tbody = document.getElementById('scheduleTableBody');
                        tbody.innerHTML = '';

                        const scheduleMap = {};
                        data.forEach(item => scheduleMap[item.day_of_week] = item);

                        for (let i = 0; i < 7; i++) {
                            const item = scheduleMap[i] || {
                                day_of_week: i,
                                is_working: false,
                                start_time: salonDefaultStart,
                                end_time: salonDefaultEnd,
                                allows_overtime: false,
                                overtime_start: '18:00',
                                overtime_end: '20:00'
                            };
                            const isWorking = item.is_working ? 'checked' : '';
                            const allowsOT = item.allows_overtime ? 'checked' : '';
                            const startTime = item.start_time ? item.start_time.substring(0, 5) : salonDefaultStart.substring(0, 5);
                            const endTime = item.end_time ? item.end_time.substring(0, 5) : salonDefaultEnd.substring(0, 5);
                            const otStart = item.overtime_start ? item.overtime_start.substring(0, 5) : '18:00';
                            const otEnd = item.overtime_end ? item.overtime_end.substring(0, 5) : '20:00';

                            const row = `
                                                                                                                                                                        <tr>
                                                                                                                                                                            <td class="ps-3 fw-medium text-muted">${daysOfWeek[i]}</td>
                                                                                                                                                                            <td class="text-center">
                                                                                                                                                                                <div class="form-check form-switch d-inline-block mb-0">
                                                                                                                                                                                    <input class="form-check-input" type="checkbox" name="schedule[${i}][is_working]" value="1" ${isWorking} onchange="toggleTimeInputs(this)">
                                                                                                                                                                                    <input type="hidden" name="schedule[${i}][day_of_week]" value="${i}">
                                                                                                                                                                                    <input type="hidden" name="schedule[${i}][is_working]" value="0" disabled> 
                                                                                                                                                                                </div>
                                                                                                                                                                            </td>
                                                                                                                                                                            <td>
                                                                                                                                                                                <div class="d-flex align-items-center gap-1">
                                                                                                                                                                                    <input type="time" class="form-control form-control-sm px-1 text-center" style="height: 28px;" name="schedule[${i}][start_time]" value="${startTime}" ${!item.is_working ? 'disabled' : ''}>
                                                                                                                                                                                    <span class="text-muted small">-</span>
                                                                                                                                                                                    <input type="time" class="form-control form-control-sm px-1 text-center" style="height: 28px;" name="schedule[${i}][end_time]" value="${endTime}" ${!item.is_working ? 'disabled' : ''}>
                                                                                                                                                                                </div>
                                                                                                                                                                            </td>
                                                                                                                                                                            <td class="text-center">
                                                                                                                                                                                <div class="form-check form-switch d-inline-block mb-0">
                                                                                                                                                                                    <input class="form-check-input ot-checkbox" type="checkbox" name="schedule[${i}][allows_overtime]" value="1" ${allowsOT} onchange="toggleOTInputs(this)" ${!item.is_working ? 'disabled' : ''}>
                                                                                                                                                                                </div>
                                                                                                                                                                            </td>
                                                                                                                                                                            <td>
                                                                                                                                                                                <div class="d-flex align-items-center gap-1">
                                                                                                                                                                                    <input type="time" class="form-control form-control-sm px-1 text-center ot-input" style="height: 28px;" name="schedule[${i}][overtime_start]" value="${otStart}" ${!item.allows_overtime || !item.is_working ? 'disabled' : ''}>
                                                                                                                                                                                    <span class="text-muted small">-</span>
                                                                                                                                                                                    <input type="time" class="form-control form-control-sm px-1 text-center ot-input" style="height: 28px;" name="schedule[${i}][overtime_end]" value="${otEnd}" ${!item.allows_overtime || !item.is_working ? 'disabled' : ''}>
                                                                                                                                                                                </div>
                                                                                                                                                                            </td>
                                                                                                                                                                        </tr>
                                                                                                                                                                    `;
                            tbody.insertAdjacentHTML('beforeend', row);
                        }

                        // Fix for checkbox value submission
                        document.querySelectorAll('#scheduleTableBody input[type="checkbox"][name$="[is_working]"]').forEach(cb => {
                            cb.addEventListener('change', function () {
                                const container = this.closest('.form-check');
                                const hidden = container.querySelector('input[type="hidden"][name$="[is_working]"]');
                                if (hidden) hidden.disabled = this.checked;
                            });
                            // Initial state
                            const container = cb.closest('.form-check');
                            const hidden = container.querySelector('input[type="hidden"][name$="[is_working]"]');
                            if (hidden) hidden.disabled = cb.checked;
                        });
                    });

                loadAbsences(staffId);
                // Reset toggle all checkbox
                document.getElementById('toggleAllDays').checked = false;
                new bootstrap.Modal(document.getElementById('scheduleModal')).show();
            }

            function toggleAllDays(masterCheckbox) {
                const checkboxes = document.querySelectorAll('#scheduleTableBody input[type="checkbox"][name$="[is_working]"]');
                checkboxes.forEach(cb => {
                    cb.checked = masterCheckbox.checked;
                    toggleTimeInputs(cb);

                    // Trigger the hidden input fix - find the hidden input with the same name in the same container
                    const container = cb.closest('.form-check');
                    const hidden = container.querySelector('input[type="hidden"][name$="[is_working]"]');
                    if (hidden) hidden.disabled = cb.checked;
                });
            }

            function toggleTimeInputs(checkbox) {
                const row = checkbox.closest('tr');
                const regularInputs = row.querySelectorAll('input[type="time"]:not(.ot-input)');
                const otCheckbox = row.querySelector('.ot-checkbox');

                regularInputs.forEach(input => input.disabled = !checkbox.checked);

                if (otCheckbox) {
                    otCheckbox.disabled = !checkbox.checked;
                    if (!checkbox.checked) {
                        otCheckbox.checked = false;
                        toggleOTInputs(otCheckbox);
                    } else {
                        toggleOTInputs(otCheckbox);
                    }
                }
            }

            function toggleOTInputs(checkbox) {
                const row = checkbox.closest('tr');
                const otInputs = row.querySelectorAll('.ot-input');
                otInputs.forEach(input => input.disabled = !checkbox.checked);
            }

            function loadAbsences(staffId) {
                fetch(`<?php echo e(url('')); ?>/<?php echo e(request()->current_salon->slug ?? ''); ?>/admin/staff/${staffId}/schedule/absences`)
                    .then(res => res.json())
                    .then(data => {
                        const tbody = document.getElementById('absencesTableBody');
                        tbody.innerHTML = '';
                        if (data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No upcoming absences</td></tr>';
                            return;
                        }
                        data.forEach(absence => {
                            const row = `
                                                                                                                                                                        <tr>
                                                                                                                                                                            <td>${new Date(absence.start_at).toLocaleString()}</td>
                                                                                                                                                                            <td>${new Date(absence.end_at).toLocaleString()}</td>
                                                                                                                                                                            <td>${absence.reason || '-'}</td>
                                                                                                                                                                            <td>
                                                                                                                                                                                <button type="button" class="btn btn-xs btn-danger" onclick="deleteAbsence(${absence.id})">&times;</button>
                                                                                                                                                                            </td>
                                                                                                                                                                        </tr>
                                                                                                                                                                    `;
                            tbody.insertAdjacentHTML('beforeend', row);
                        });
                    });
            }

            document.getElementById('scheduleForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = { schedule: [] };

                const rawData = {};
                for (let [key, value] of formData.entries()) {
                    const match = key.match(/schedule\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const index = match[1];
                        const field = match[2];
                        if (!rawData[index]) rawData[index] = {};
                        rawData[index][field] = value;
                    }
                }

                data.schedule = Object.values(rawData).map(item => ({
                    day_of_week: parseInt(item.day_of_week),
                    is_working: item.is_working == '1',
                    start_time: item.start_time,
                    end_time: item.end_time,
                    allows_overtime: item.allows_overtime == '1',
                    overtime_start: item.overtime_start,
                    overtime_end: item.overtime_end
                }));

                fetch(`<?php echo e(url('')); ?>/<?php echo e(request()->current_salon->slug ?? ''); ?>/admin/staff/${currentStaffId}/schedule`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                    })
                    .catch(err => alert('Error saving schedule'));
            });

            document.getElementById('absenceForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch(`<?php echo e(url('')); ?>/<?php echo e(request()->current_salon->slug ?? ''); ?>/admin/staff/${currentStaffId}/schedule/absences`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.message) {
                            this.reset();
                            loadAbsences(currentStaffId);
                        }
                    })
                    .catch(err => alert('Error adding absence'));
            });

            // Form validation for Add Staff
            const addStaffForm = document.getElementById('addStaffForm');
            if (addStaffForm) {
                addStaffForm.addEventListener('submit', function (e) {
                    if (window.customerPhoneHandler && !window.customerPhoneHandler.validateForm(this)) {
                        e.preventDefault();
                        return false;
                    }
                });
            }

            // Form validation for Edit Staff
            const editStaffForm = document.getElementById('editStaffForm');
            if (editStaffForm) {
                editStaffForm.addEventListener('submit', function (e) {
                    if (window.customerPhoneHandler && !window.customerPhoneHandler.validateForm(this)) {
                        e.preventDefault();
                        return false;
                    }
                });
            }

            function deleteAbsence(id) {
                if (!confirm('Remove this absence?')) return;
                fetch(`<?php echo e(url('')); ?>/<?php echo e(request()->current_salon->slug ?? ''); ?>/admin/staff/${currentStaffId}/schedule/absences/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(res => res.json())
                    .then(() => loadAbsences(currentStaffId));
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\staff\index.blade.php ENDPATH**/ ?>