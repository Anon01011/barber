<?php $__env->startSection('title', 'Calendar Settings'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-semibold">Calendar Settings</h2>
                <p class="text-muted mb-0">Customize your booking calendar appearance and behavior</p>
            </div>
            <a href="<?php echo e(route('admin.bookings.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Calendar
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <form action="<?php echo e(route('admin.bookings.settings.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <!-- Tabs -->
                    <ul class="nav nav-tabs px-4 pt-3" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="view-tab" data-bs-toggle="tab" data-bs-target="#view"
                                type="button" role="tab">
                                <i class="fas fa-eye me-2"></i>View Settings
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="appointment-tab" data-bs-toggle="tab" data-bs-target="#appointment"
                                type="button" role="tab">
                                <i class="fas fa-calendar-check me-2"></i>Appointment Settings
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="colors-tab" data-bs-toggle="tab" data-bs-target="#colors"
                                type="button" role="tab">
                                <i class="fas fa-palette me-2"></i>Event Colors
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-4" id="settingsTabContent">
                        <!-- View Settings Tab -->
                        <div class="tab-pane fade show active" id="view" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Default Calendar View</label>
                                    <select class="form-select" name="calendar_default_view">
                                        <option value="timeGridWeek" <?php echo e(($settings['calendar_default_view'] ?? 'timeGridWeek') == 'timeGridWeek' ? 'selected' : ''); ?>>Week View</option>
                                        <option value="timeGridDay" <?php echo e(($settings['calendar_default_view'] ?? 'timeGridWeek') == 'timeGridDay' ? 'selected' : ''); ?>>Day View</option>
                                        <option value="resourceTimeGridDay" <?php echo e(($settings['calendar_default_view'] ?? 'timeGridWeek') == 'resourceTimeGridDay' ? 'selected' : ''); ?>>Staff Day View
                                        </option>
                                    </select>
                                    <small class="text-muted">Choose the default view when opening the calendar</small>
                                </div>
                            </div>
                        </div>

                        <!-- Appointment Settings Tab -->
                        <div class="tab-pane fade" id="appointment" role="tabpanel">
                            <div class="row g-4">
                                <!-- Guest Booking Section -->
                                <div class="col-12">
                                    <h6 class="fw-semibold mb-3"><i class="fas fa-user-clock me-2"></i>Guest Booking</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="guest_booking_enabled"
                                                    value="1" id="guest_booking_enabled" <?php echo e(($settings['guest_booking_enabled'] ?? true) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="guest_booking_enabled">Enable Guest
                                                    Booking</label>
                                            </div>
                                            <small class="text-muted">Allow customers to book without an account</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Default Guest Booking Status</label>
                                            <select class="form-select" name="default_guest_status">
                                                <option value="pending" <?php echo e(($settings['default_guest_status'] ?? 'pending') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                                <option value="confirmed" <?php echo e(($settings['default_guest_status'] ?? 'pending') == 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Working Hours Section -->
                                <div class="col-12">
                                    <h6 class="fw-semibold mb-3"><i class="fas fa-clock me-2"></i>Working Hours</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Start Time</label>
                                            <input type="time" name="working_hours_start" class="form-control"
                                                value="<?php echo e($settings['working_hours_start'] ?? '08:00'); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Time</label>
                                            <input type="time" name="working_hours_end" class="form-control"
                                                value="<?php echo e($settings['working_hours_end'] ?? '20:00'); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Slot Duration (minutes)</label>
                                            <select name="slot_duration" class="form-select">
                                                <option value="5" <?php echo e(($settings['slot_duration'] ?? 30) == 5 ? 'selected' : ''); ?>>5 Minutes</option>
                                                <option value="10" <?php echo e(($settings['slot_duration'] ?? 30) == 10 ? 'selected' : ''); ?>>10 Minutes</option>
                                                <option value="15" <?php echo e(($settings['slot_duration'] ?? 30) == 15 ? 'selected' : ''); ?>>15 Minutes</option>
                                                <option value="20" <?php echo e(($settings['slot_duration'] ?? 30) == 20 ? 'selected' : ''); ?>>20 Minutes</option>
                                                <option value="30" <?php echo e(($settings['slot_duration'] ?? 30) == 30 ? 'selected' : ''); ?>>30 Minutes</option>
                                                <option value="45" <?php echo e(($settings['slot_duration'] ?? 30) == 45 ? 'selected' : ''); ?>>45 Minutes</option>
                                                <option value="60" <?php echo e(($settings['slot_duration'] ?? 30) == 60 ? 'selected' : ''); ?>>60 Minutes</option>
                                            </select>
                                            <small class="text-muted">Duration of each time slot</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Advance Booking Days</label>
                                            <input type="number" name="advance_booking_days" class="form-control"
                                                value="<?php echo e($settings['advance_booking_days'] ?? 30); ?>" min="1" max="365">
                                            <small class="text-muted">Maximum days in advance customers can book</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- General Settings Section -->
                                <div class="col-12">
                                    <h6 class="fw-semibold mb-3"><i class="fas fa-cog me-2"></i>General Settings</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Buffer Time (minutes)</label>
                                            <select name="appointment_buffer_time" class="form-select">
                                                <option value="0" <?php echo e(($settings['appointment_buffer_time'] ?? 0) == 0 ? 'selected' : ''); ?>>No Buffer</option>
                                                <option value="5" <?php echo e(($settings['appointment_buffer_time'] ?? 0) == 5 ? 'selected' : ''); ?>>5 Minutes</option>
                                                <option value="10" <?php echo e(($settings['appointment_buffer_time'] ?? 0) == 10 ? 'selected' : ''); ?>>10 Minutes</option>
                                                <option value="15" <?php echo e(($settings['appointment_buffer_time'] ?? 0) == 15 ? 'selected' : ''); ?>>15 Minutes</option>
                                                <option value="20" <?php echo e(($settings['appointment_buffer_time'] ?? 0) == 20 ? 'selected' : ''); ?>>20 Minutes</option>
                                                <option value="30" <?php echo e(($settings['appointment_buffer_time'] ?? 0) == 30 ? 'selected' : ''); ?>>30 Minutes</option>
                                            </select>
                                            <small class="text-muted">Time gap between appointments</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">No-Show Fee</label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text"><?php echo e($settings['currency_symbol'] ?? '$'); ?></span>
                                                <input type="number" name="no_show_fee" class="form-control"
                                                    value="<?php echo e($settings['no_show_fee'] ?? 0); ?>" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    name="allow_overlapping_bookings" value="1"
                                                    id="allow_overlapping_bookings" <?php echo e(($settings['allow_overlapping_bookings'] ?? false) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="allow_overlapping_bookings">Allow
                                                    Overlapping Bookings</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="auto_confirm_bookings"
                                                    value="1" id="auto_confirm_bookings" <?php echo e(($settings['auto_confirm_bookings'] ?? false) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="auto_confirm_bookings">Auto-confirm
                                                    Bookings</label>
                                            </div>
                                            <small class="text-muted">Automatically confirm bookings upon creation</small>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Cancellation Policy</label>
                                            <textarea name="cancellation_policy" class="form-control"
                                                rows="3"><?php echo e($settings['cancellation_policy'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Event Colors Tab -->
                        <div class="tab-pane fade" id="colors" role="tabpanel">
                            <div class="row g-3">
                                <?php
                                    $statuses = [
                                        'pending' => ['label' => 'Pending', 'icon' => 'clock'],
                                        'confirmed' => ['label' => 'Confirmed', 'icon' => 'check-circle'],
                                        'arrived' => ['label' => 'Arrived', 'icon' => 'walking'],
                                        'started' => ['label' => 'Employee Started', 'icon' => 'cut'],
                                        'completed' => ['label' => 'Completed', 'icon' => 'check-double'],
                                        'cancelled' => ['label' => 'Cancelled', 'icon' => 'times-circle'],
                                        'no_show' => ['label' => 'No Show', 'icon' => 'user-slash'],
                                        'staff_completed' => ['label' => 'Employee Completed', 'icon' => 'check-circle'],
                                        'frozen' => ['label' => 'Frozen', 'icon' => 'snowflake'],
                                        'unpaid' => ['label' => 'Unpaid / Pay Later', 'icon' => 'exclamation-triangle'],
                                    ];
                                ?>

                                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0 fw-semibold">
                                                    <i class="fas fa-<?php echo e($config['icon']); ?> me-2"></i><?php echo e($config['label']); ?>

                                                </h6>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="row g-2">
                                                    <div class="col-4">
                                                        <label class="form-label small mb-1">Background</label>
                                                        <input type="color" class="form-control form-control-color w-100"
                                                            name="booking_color_<?php echo e($status); ?>"
                                                            value="<?php echo e($settings['booking_color_' . $status]); ?>"
                                                            title="Background color">
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="form-label small mb-1">Border</label>
                                                        <input type="color" class="form-control form-control-color w-100"
                                                            name="booking_border_<?php echo e($status); ?>"
                                                            value="<?php echo e($settings['booking_border_' . $status]); ?>"
                                                            title="Border color">
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="form-label small mb-1">Text</label>
                                                        <input type="color" class="form-control form-control-color w-100"
                                                            name="booking_text_<?php echo e($status); ?>"
                                                            value="<?php echo e($settings['booking_text_' . $status]); ?>"
                                                            title="Text color">
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <div class="preview-badge p-2 rounded border text-center small fw-medium"
                                                        data-status="<?php echo e($status); ?>"
                                                        style="background-color: <?php echo e($settings['booking_color_' . $status]); ?>; 
                                                                                                border-color: <?php echo e($settings['booking_border_' . $status]); ?> !important; 
                                                                                                color: <?php echo e($settings['booking_text_' . $status]); ?>;">
                                                        <?php echo e($config['label']); ?> Booking
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Tip:</strong> Choose colors with good contrast for better visibility. Changes apply
                                immediately to the calendar.
                            </div>
                        </div>
                    </div>

                    <!-- Footer with Save Button -->
                    <div class="card-footer bg-light border-top px-4 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-save me-1"></i>Settings are saved per salon
                            </small>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Update preview when color changes
                document.querySelectorAll('.form-control-color').forEach(input => {
                    input.addEventListener('input', function () {
                        const card = this.closest('.card-body');
                        const preview = card.querySelector('.preview-badge');
                        const name = this.name;

                        if (name.includes('_color_')) {
                            preview.style.backgroundColor = this.value;
                        } else if (name.includes('_border_')) {
                            preview.style.borderColor = this.value;
                        } else if (name.includes('_text_')) {
                            preview.style.color = this.value;
                        }
                    });
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\bookings\settings.blade.php ENDPATH**/ ?>