<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.bookings.index')); ?>">Bookings</a></li>
                        <li class="breadcrumb-item active" aria-current="page">New Booking</li>
                    </ol>
                </nav>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">New Booking</h5>
                        <a href="<?php echo e(route('admin.bookings.index')); ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Bookings
                        </a>
                    </div>
                    <div class="card-body">
                        <form id="createBookingForm" action="<?php echo e(route('admin.bookings.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="customer_id" class="form-label">Customer *</label>
                                    <div class="d-flex gap-2">
                                        <select class="form-select <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="customer_id" name="customer_id" required style="flex: 1">
                                            <option value="">Select Customer</option>
                                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($customer->id); ?>" <?php echo e(old('customer_id') == $customer->id ? 'selected' : ''); ?>>
                                                    <?php echo e($customer->name); ?> (<?php echo e($customer->display_phone); ?>)
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" id="billActivityBtn"
                                            onclick="openBillActivityModal(document.getElementById('customer_id').value)"
                                            disabled title="Select a customer to view bill activity">
                                            <i class="fas fa-file-invoice"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger position-relative" id="unpaidAmountBtn"
                                            onclick="openUnpaidBillsModal(document.getElementById('customer_id').value)"
                                            disabled title="Select a customer to view unpaid bills" style="display: none;">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle" id="unpaidAmountBadge">
                                                <?php echo e(currency_symbol()); ?>0
                                            </span>
                                        </button>
                                    </div>
                                    <?php $__errorArgs = ['customer_id'];
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
                                    <label for="service_id" class="form-label">Service *</label>
                                    <select class="form-select <?php $__errorArgs = ['service_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="service_id"
                                        name="service_id" required>
                                        <option value="">Select Service</option>
                                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($service->id); ?>" <?php echo e(old('service_id') == $service->id ? 'selected' : ''); ?>>
                                                <?php echo e($service->name); ?> (<?php echo e($service->duration); ?> min) -
                                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($service->price, 2)); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['service_id'];
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

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="staff_id" class="form-label">Staff Member *</label>
                                    <select class="form-select <?php $__errorArgs = ['staff_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="staff_id"
                                        name="staff_id" required>
                                        <option value="">Select Staff Member</option>
                                        <?php $__currentLoopData = $staffMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($staff->id); ?>" <?php echo e(old('staff_id') == $staff->id ? 'selected' : ''); ?>>
                                                <?php echo e($staff->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['staff_id'];
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
                                        <?php $__currentLoopData = ['pending', 'confirmed', 'in_progress', 'cancelled', 'no_show']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($status); ?>" <?php echo e(old('status', 'pending') === $status ? 'selected' : ''); ?>>
                                                <?php echo e(ucfirst(str_replace('_', ' ', $status))); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="start_time" class="form-label">Start Time *</label>
                                    <input type="datetime-local"
                                        class="form-control <?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="start_time"
                                        name="start_time" value="<?php echo e(old('start_time', now()->format('Y-m-d\TH:i'))); ?>"
                                        required>
                                    <?php $__errorArgs = ['start_time'];
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
                                    <label for="end_time" class="form-label">End Time *</label>
                                    <input type="datetime-local"
                                        class="form-control <?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="end_time"
                                        name="end_time" value="<?php echo e(old('end_time')); ?>" required>
                                    <div class="form-text text-info" id="effective_end_time_display" style="display: none;">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Effective End Time (with buffer): <span id="effective_end_time_value"></span>
                                    </div>
                                    <?php $__errorArgs = ['end_time'];
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
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="notes" name="notes"
                                    rows="3"><?php echo e(old('notes')); ?></textarea>
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

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary me-md-2">
                                    <i class="fas fa-save me-1"></i> Create Booking
                                </button>
                                <a href="<?php echo e(route('admin.bookings.index')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bill Activity Modal -->
    <?php echo $__env->make('admin.customers.partials.bill_activity_modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // Enable/disable Bill Activity button based on customer selection
            document.getElementById('customer_id').addEventListener('change', function () {
                const billActivityBtn = document.getElementById('billActivityBtn');
                const unpaidAmountBtn = document.getElementById('unpaidAmountBtn');
                const customerId = this.value;
                
                if (customerId) {
                    billActivityBtn.disabled = false;
                    billActivityBtn.title = 'View customer bill activity';
                    
                    // Fetch customer's unpaid amount
                    const salonSlug = '<?php echo e(auth()->user()->salon->slug); ?>';
                    fetch(`/${salonSlug}/admin/customers/${customerId}/bill-activity`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.bills) {
                            // Calculate unpaid amount
                            let unpaidAmount = 0;
                            data.bills.forEach(bill => {
                                const status = bill.payment_status.toLowerCase();
                                if (status === 'unpaid' || status === 'pending' || status === 'partial') {
                                    unpaidAmount += parseFloat(bill.grand_total || 0);
                                }
                            });
                            
                            if (unpaidAmount > 0) {
                                unpaidAmountBtn.style.display = 'inline-block';
                                unpaidAmountBtn.disabled = false;
                                unpaidAmountBtn.title = 'View unpaid bills';
                                document.getElementById('unpaidAmountBadge').textContent = 
                                    '<?php echo e(currency_symbol()); ?>' + unpaidAmount.toFixed(2);
                            } else {
                                unpaidAmountBtn.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => {

                        unpaidAmountBtn.style.display = 'none';
                    });
                } else {
                    billActivityBtn.disabled = true;
                    billActivityBtn.title = 'Select a customer to view bill activity';
                    unpaidAmountBtn.style.display = 'none';
                }
            });

            // Auto-calculate end time based on service duration
            document.getElementById('service_id').addEventListener('change', function () {
                const serviceId = this.value;
                const startTimeInput = document.getElementById('start_time');
                const bufferTime = <?php echo e((int) app(\App\Services\SettingsService::class)->get('appointment_buffer_time', 15)); ?>;

                if (serviceId && startTimeInput.value) {
                    // Get the selected service's duration
                    const selectedOption = this.options[this.selectedIndex];
                    const durationMatch = selectedOption.text.match(/(\d+)\s*min/);

                    if (durationMatch) {
                        const duration = parseInt(durationMatch[1]);
                        const startTime = new Date(startTimeInput.value);
                        const endTime = new Date(startTime.getTime() + duration * 60000);

                        // Format the end time for the datetime-local input
                        // Adjust for timezone offset to keep local time correct
                        const offset = endTime.getTimezoneOffset() * 60000;
                        const endTimeStr = new Date(endTime.getTime() - offset).toISOString().slice(0, 16);
                        document.getElementById('end_time').value = endTimeStr;

                        // Calculate and show effective end time
                        if (bufferTime > 0) {
                            const effectiveEndTime = new Date(endTime.getTime() + bufferTime * 60000);
                            const effectiveEndTimeStr = effectiveEndTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                            document.getElementById('effective_end_time_value').textContent = effectiveEndTimeStr;
                            const displayEl = document.getElementById('effective_end_time_display');
                            if (displayEl) displayEl.style.display = 'block';
                        } else {
                            const displayEl = document.getElementById('effective_end_time_display');
                            if (displayEl) displayEl.style.display = 'none';
                        }
                    }
                }
            });

            // Update end time when start time changes
            document.getElementById('start_time').addEventListener('change', function () {
                const serviceId = document.getElementById('service_id').value;
                if (serviceId) {
                    // Trigger the service change handler to update end time
                    document.getElementById('service_id').dispatchEvent(new Event('change'));
                }
            });

            // Update effective end time when manual end time changes
            document.getElementById('end_time').addEventListener('change', function () {
                const endTimeValue = this.value;
                const bufferTime = <?php echo e((int) app(\App\Services\SettingsService::class)->get('appointment_buffer_time', 15)); ?>;

                if (endTimeValue && bufferTime > 0) {
                    const endTime = new Date(endTimeValue);
                    const effectiveEndTime = new Date(endTime.getTime() + bufferTime * 60000);
                    const effectiveEndTimeStr = effectiveEndTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    document.getElementById('effective_end_time_value').textContent = effectiveEndTimeStr;
                    const displayEl = document.getElementById('effective_end_time_display');
                    if (displayEl) displayEl.style.display = 'block';
                }
            });
            </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\bookings\create.blade.php ENDPATH**/ ?>