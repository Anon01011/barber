<?php $__env->startSection('title', 'Employees'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Employees</h3>
                    <button type="button" onclick="openCreateModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                        <i class="fas fa-plus mr-2"></i>
                        Add Employee
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="<?php echo e($employee->profile_photo_url); ?>"
                                                    alt="<?php echo e($employee->name); ?>">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo e($employee->name); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo e($employee->email); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">
                                            <?php echo e($employee->role ? $employee->role->name : 'N/A'); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($employee->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                            <?php echo e(ucfirst($employee->status)); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button type="button"
                                            onclick="openEditModal(<?php echo e(json_encode($employee)); ?>, <?php echo e(json_encode($employee->services->pluck('id'))); ?>)"
                                            class="text-pink-600 hover:text-pink-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?php echo e(route('admin.employees.destroy', $employee)); ?>" method="POST"
                                            class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure you want to delete this employee?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <?php echo e($employees->links()); ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div id="employeeModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="employeeForm" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modalTitle">Add Employee</h3>

                        <div class="space-y-4">
                            <!-- Allow Login -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="allow_login" name="allow_login" type="checkbox" value="1"
                                        onchange="toggleLoginFields()"
                                        class="focus:ring-pink-500 h-4 w-4 text-pink-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="allow_login" class="font-medium text-gray-700">Allow Login</label>
                                    <p class="text-gray-500">If checked, this employee will be able to login to the system.
                                    </p>
                                </div>
                            </div>

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" id="name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email <span
                                        id="email_required" class="text-red-500 hidden">*</span></label>
                                <input type="email" name="email" id="email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                            </div>

                            <!-- Login Fields -->
                            <div id="login_fields" class="hidden space-y-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password <span
                                            class="text-red-500">*</span></label>
                                    <input type="password" name="password" id="password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                                    <p class="text-xs text-gray-500 mt-1" id="password_help">Leave blank to keep current
                                        password when editing.</p>
                                </div>
                                <div>
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700">Confirm Password <span
                                            class="text-red-500">*</span></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                                </div>
                                <div>
                                    <label for="role_id" class="block text-sm font-medium text-gray-700">Role <span
                                            class="text-red-500">*</span></label>
                                    <select name="role_id" id="role_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                                        <option value="">Select a role</option>
                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($role->id); ?>"><?php echo e($role->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <!-- Services Assignment -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Assign Services</label>
                                <div class="border border-gray-300 rounded-md p-4">
                                    <div class="flex justify-between items-center mb-4">
                                        <input type="text" id="service_search" placeholder="Search services..."
                                            class="rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm w-1/2">
                                        <div class="space-x-2 text-xs">
                                            <button type="button" onclick="selectAllServices()"
                                                class="text-pink-600 hover:text-pink-500">Select All</button>
                                            <span class="text-gray-300">|</span>
                                            <button type="button" onclick="deselectAllServices()"
                                                class="text-gray-600 hover:text-gray-500">Deselect All</button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 gap-2 max-h-40 overflow-y-auto" id="services_container">
                                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="flex items-start service-item">
                                                <div class="flex items-center h-5">
                                                    <input id="service_<?php echo e($service->id); ?>" name="services[]" type="checkbox"
                                                        value="<?php echo e($service->id); ?>"
                                                        class="focus:ring-pink-500 h-4 w-4 text-pink-600 border-gray-300 rounded service-checkbox">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="service_<?php echo e($service->id); ?>"
                                                        class="font-medium text-gray-700 service-name"><?php echo e($service->name); ?></label>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-pink-600 text-base font-medium text-white hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Save
                        </button>
                        <button type="button" onclick="closeModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
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
            function openCreateModal() {
                document.getElementById('modalTitle').innerText = 'Add Employee';
                document.getElementById('employeeForm').action = "<?php echo e(route('admin.employees.store')); ?>";
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('employeeForm').reset();

                // Reset login fields
                document.getElementById('allow_login').checked = false;
                toggleLoginFields();

                // Clear service selection
                document.querySelectorAll('.service-checkbox').forEach(cb => cb.checked = false);

                document.getElementById('employeeModal').classList.remove('hidden');
            }

            function openEditModal(employee, serviceIds) {
                document.getElementById('modalTitle').innerText = 'Edit Employee';
                document.getElementById('employeeForm').action = `/admin/employees/${employee.id}`;
                document.getElementById('formMethod').value = 'PUT';

                document.getElementById('name').value = employee.name;
                document.getElementById('email').value = employee.email;
                document.getElementById('status').value = employee.status;

                // Handle login fields
                const hasLogin = employee.user_id !== null;
                document.getElementById('allow_login').checked = hasLogin;
                toggleLoginFields();

                if (hasLogin && employee.user && employee.user.roles && employee.user.roles.length > 0) {
                    document.getElementById('role_id').value = employee.user.roles[0].id;
                }

                // Handle services
                document.querySelectorAll('.service-checkbox').forEach(cb => {
                    cb.checked = serviceIds.includes(parseInt(cb.value));
                });

                document.getElementById('employeeModal').classList.remove('hidden');
            }

            function closeModal() {
                document.getElementById('employeeModal').classList.add('hidden');
            }

            function toggleLoginFields() {
                const allowLogin = document.getElementById('allow_login').checked;
                const loginFields = document.getElementById('login_fields');
                const emailRequired = document.getElementById('email_required');
                const passwordInput = document.getElementById('password');
                const passwordConfirmInput = document.getElementById('password_confirmation');
                const roleSelect = document.getElementById('role_id');
                const isEdit = document.getElementById('formMethod').value === 'PUT';

                if (allowLogin) {
                    loginFields.classList.remove('hidden');
                    emailRequired.classList.remove('hidden');
                    roleSelect.required = true;

                    if (!isEdit) {
                        passwordInput.required = true;
                        passwordConfirmInput.required = true;
                    } else {
                        passwordInput.required = false;
                        passwordConfirmInput.required = false;
                    }
                } else {
                    loginFields.classList.add('hidden');
                    emailRequired.classList.add('hidden');
                    passwordInput.required = false;
                    passwordConfirmInput.required = false;
                    roleSelect.required = false;
                }
            }

            // Service Search
            document.getElementById('service_search').addEventListener('input', function (e) {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.service-item').forEach(item => {
                    const name = item.querySelector('.service-name').textContent.toLowerCase();
                    if (name.includes(searchTerm)) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            });

            function selectAllServices() {
                document.querySelectorAll('.service-checkbox').forEach(checkbox => {
                    if (!checkbox.closest('.service-item').classList.contains('hidden')) {
                        checkbox.checked = true;
                    }
                });
            }

            function deselectAllServices() {
                document.querySelectorAll('.service-checkbox').forEach(checkbox => {
                    if (!checkbox.closest('.service-item').classList.contains('hidden')) {
                        checkbox.checked = false;
                    }
                });
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\employees\index.blade.php ENDPATH**/ ?>