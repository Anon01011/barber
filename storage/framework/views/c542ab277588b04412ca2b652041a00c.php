<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Salon Settings</h1>
            <?php if(auth()->user()->hasRole('salon_admin')): ?>
                <div>
                    <form action="<?php echo e(route('admin.saas.settings.clear-cache')); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-sync"></i> Clear Cache
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                            type="button" role="tab">
                            General
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments"
                            type="button" role="tab">
                            Appointments
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications"
                            type="button" role="tab">
                            Notifications
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments"
                            type="button" role="tab">
                            Payments & Tax
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="localization-tab" data-bs-toggle="tab" data-bs-target="#localization"
                            type="button" role="tab">
                            Localization
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="privacy-tab" data-bs-toggle="tab" data-bs-target="#privacy"
                            type="button" role="tab">
                            Privacy & Security
                        </button>
                    </li>
                    <?php if(auth()->user()->hasAnyRole(['salon_admin', 'manager'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('admin.saas.settings.mail')); ?>">
                                <i class="fas fa-envelope"></i> Mail Settings
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('admin.saas.settings.update')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="tab-content" id="settingsTabContent">
                        <!-- General Settings -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <?php echo $__env->make('admin.settings.partials.general', ['settings' => $settings], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                        <!-- Appointments Settings -->
                        <div class="tab-pane fade" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
                            <?php echo $__env->make('admin.settings.partials.appointments', ['settings' => $settings], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                        <!-- Notification Settings -->
                        <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                            <?php echo $__env->make('admin.settings.partials.notifications', ['settings' => $settings], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                        <!-- Payment & Tax Settings -->
                        <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                            <?php echo $__env->make('admin.settings.partials.payments', ['settings' => $settings], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                        <!-- Localization Settings -->
                        <div class="tab-pane fade" id="localization" role="tabpanel" aria-labelledby="localization-tab">
                            <?php echo $__env->make('admin.settings.partials.localization', ['settings' => $settings], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                        <!-- Privacy & Security Settings -->
                        <div class="tab-pane fade" id="privacy" role="tabpanel" aria-labelledby="privacy-tab">
                            <?php echo $__env->make('admin.settings.partials.privacy', ['settings' => $settings], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .color-preview {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            display: inline-block;
        }

        .bg-purple {
            background-color: #6f42c1;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function previewFavicon(input) {
            const preview = document.getElementById('favicon-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // SMS Gateway Switching Logic (Needed for notifications partial)
        document.addEventListener('DOMContentLoaded', function () {
            const enableSms = document.getElementById('enable_sms');
            const smsConfig = document.getElementById('sms_config_section');
            const gatewaySelect = document.getElementById('sms_gateway');
            const gatewayFields = document.querySelectorAll('.gateway-fields');

            if (enableSms) {
                const updateSmsConfig = () => {
                    if (smsConfig) smsConfig.style.display = enableSms.checked ? 'block' : 'none';
                };
                updateSmsConfig();
                enableSms.addEventListener('change', updateSmsConfig);
            }

            if (gatewaySelect) {
                const updateFields = () => {
                    const selected = gatewaySelect.value + '_fields';
                    gatewayFields.forEach(field => {
                        field.style.display = field.id === selected ? 'block' : 'none';
                    });
                };
                updateFields();
                gatewaySelect.addEventListener('change', updateFields);
            }
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\saas\settings\edit.blade.php ENDPATH**/ ?>