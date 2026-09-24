<?php $__env->startSection('title', 'Add Employee'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Add New Employee</h3>
        </div>

        <div class="p-6">
            <form action="<?php echo e(route('admin.employees.store')); ?>" method="POST" class="space-y-6">
                <?php echo csrf_field(); ?>

                    <!-- Allow Login -->
                    <div class="sm:col-span-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="allow_login" name="allow_login" type="checkbox" value="1" <?php echo e(old('allow_login') ? 'checked' : ''); ?>

                                       class="focus:ring-pink-500 h-4 w-4 text-pink-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="allow_login" class="font-medium text-gray-700">Allow Login</label>
                                <p class="text-gray-500">If checked, this employee will be able to login to the system.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email <span id="email_required" class="text-red-500 hidden">*</span></label>
                        <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Login Fields Container -->
                    <div id="login_fields" class="sm:col-span-2 grid grid-cols-1 gap-6 sm:grid-cols-2 <?php echo e(old('allow_login') ? '' : 'hidden'); ?>">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" id="password"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700">Role <span class="text-red-500">*</span></label>
                            <select name="role_id" id="role_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                                <option value="">Select a role</option>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->id); ?>" <?php echo e(old('role_id') == $role->id ? 'selected' : ''); ?>>
                                        <?php echo e($role->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="is_active" id="is_active" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                            <option value="1" <?php echo e(old('is_active') == '1' ? 'selected' : ''); ?>>Active</option>
                            <option value="0" <?php echo e(old('is_active') == '0' ? 'selected' : ''); ?>>Inactive</option>
                        </select>
                        <?php $__errorArgs = ['is_active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Services Assignment -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Services</label>
                        <div class="border border-gray-300 rounded-md p-4">
                            <div class="flex justify-between items-center mb-4">
                                <input type="text" id="service_search" placeholder="Search services..." 
                                       class="rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm w-1/3">
                                <div class="space-x-2">
                                    <button type="button" id="select_all_services" class="text-sm text-pink-600 hover:text-pink-500">Select All</button>
                                    <span class="text-gray-300">|</span>
                                    <button type="button" id="deselect_all_services" class="text-sm text-gray-600 hover:text-gray-500">Deselect All</button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 max-h-60 overflow-y-auto" id="services_container">
                                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-start service-item">
                                        <div class="flex items-center h-5">
                                            <input id="service_<?php echo e($service->id); ?>" name="services[]" type="checkbox" value="<?php echo e($service->id); ?>"
                                                   <?php echo e((is_array(old('services')) && in_array($service->id, old('services'))) ? 'checked' : ''); ?>

                                                   class="focus:ring-pink-500 h-4 w-4 text-pink-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="service_<?php echo e($service->id); ?>" class="font-medium text-gray-700 service-name"><?php echo e($service->name); ?></label>
                                            <p class="text-gray-500 text-xs"><?php echo e($service->duration); ?> min - $ <?php echo e(number_format($service->price, 2)); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>

                <div class="flex justify-end space-x-3">
                    <a href="<?php echo e(route('admin.employees.index')); ?>" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                        <i class="fas fa-save mr-2"></i>
                        Save Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const allowLoginCheckbox = document.getElementById('allow_login');
        const loginFields = document.getElementById('login_fields');
        const emailRequired = document.getElementById('email_required');
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        const roleSelect = document.getElementById('role_id');

        function toggleLoginFields() {
            if (allowLoginCheckbox.checked) {
                loginFields.classList.remove('hidden');
                emailRequired.classList.remove('hidden');
                passwordInput.required = true;
                passwordConfirmInput.required = true;
                roleSelect.required = true;
            } else {
                loginFields.classList.add('hidden');
                emailRequired.classList.add('hidden');
                passwordInput.required = false;
                passwordConfirmInput.required = false;
                roleSelect.required = false;
            }
        }

        if (allowLoginCheckbox) {
            allowLoginCheckbox.addEventListener('change', toggleLoginFields);
            // Initial check
            toggleLoginFields();
        }

        // Service Search
        const serviceSearch = document.getElementById('service_search');
        const serviceItems = document.querySelectorAll('.service-item');

        if (serviceSearch) {
            serviceSearch.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                serviceItems.forEach(item => {
                    const name = item.querySelector('.service-name').textContent.toLowerCase();
                    if (name.includes(searchTerm)) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            });
        }

        // Select/Deselect All
        const selectAllBtn = document.getElementById('select_all_services');
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                document.querySelectorAll('input[name="services[]"]').forEach(checkbox => {
                    if (!checkbox.closest('.service-item').classList.contains('hidden')) {
                        checkbox.checked = true;
                    }
                });
            });
        }

        const deselectAllBtn = document.getElementById('deselect_all_services');
        if (deselectAllBtn) {
            deselectAllBtn.addEventListener('click', function() {
                document.querySelectorAll('input[name="services[]"]').forEach(checkbox => {
                    if (!checkbox.closest('.service-item').classList.contains('hidden')) {
                        checkbox.checked = false;
                    }
                });
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\employees\create.blade.php ENDPATH**/ ?>