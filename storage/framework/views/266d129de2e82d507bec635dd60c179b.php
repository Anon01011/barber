<!-- Customer Data Privacy -->
<div class="settings-section-card">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-user-shield"></i></div>
        <h6>Customer Data Privacy & Masking</h6>
    </div>

    <div class="settings-field-group">
        <div class="alert alert-soft-info border-0 p-3 mb-4 rounded-3 d-flex align-items-center">
            <i class="fas fa-info-circle me-3 fa-lg opacity-50"></i>
            <div class="small">
                <strong>Data Masking Strategy:</strong> Safeguard sensitive client information. When enabled, phone
                numbers and emails are obfuscated (e.g., ***-***-1234) for non-authorized personnel.
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="form-check form-switch custom-switch mb-4">
                    <input type="checkbox" class="form-check-input" id="customer_data_masking_enabled"
                        name="customer_data_masking_enabled" value="1" <?php echo e(old('customer_data_masking_enabled', $settings['customer_data_masking_enabled'] ?? false) ? 'checked' : ''); ?>>
                    <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                        for="customer_data_masking_enabled">Enable Master Data Masking</label>
                </div>
            </div>

            <div id="masking-options" class="col-12 border-top pt-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="settings-field-group p-3 mb-0">
                            <div class="form-check form-switch custom-switch">
                                <input type="checkbox" class="form-check-input" id="mask_customer_phone"
                                    name="mask_customer_phone" value="1" <?php echo e(old('mask_customer_phone', $settings['mask_customer_phone'] ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                                    for="mask_customer_phone">Mask Phone Numbers</label>
                            </div>
                            <small class="text-muted d-block mt-2">Example: +1 *** *** 5678</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="settings-field-group p-3 mb-0">
                            <div class="form-check form-switch custom-switch">
                                <input type="checkbox" class="form-check-input" id="mask_customer_email"
                                    name="mask_customer_email" value="1" <?php echo e(old('mask_customer_email', $settings['mask_customer_email'] ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                                    for="mask_customer_email">Mask Email Addresses</label>
                            </div>
                            <small class="text-muted d-block mt-2">Example: joh***@domain.com</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Access Control -->
<div class="settings-section-card">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-key"></i></div>
        <h6>Exempted Personnel</h6>
    </div>

    <div class="settings-field-group">
        <div class="mb-3">
            <label class="form-label fw-semibold small text-uppercase text-secondary">
                <i class="fas fa-users-cog text-primary me-2"></i>Authorized Users (Full Access)
            </label>
            <p class="text-muted small">Select users who are allowed to see unmasked data. Note: Salon Admins always
                have full visibility.</p>
        </div>

        <div class="dropdown w-100">
            <button
                class="btn btn-outline-secondary dropdown-toggle w-100 text-start py-2 d-flex justify-content-between align-items-center"
                type="button" id="unmasked_data_users_dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <span id="selected-users-text">Search and select users...</span>
                <i class="fas fa-chevron-down opacity-50 small"></i>
            </button>
            <div class="dropdown-menu w-100 p-3 shadow-lg border-0 rounded-3 mt-1"
                aria-labelledby="unmasked_data_users_dropdown" style="max-height: 400px; overflow-y: auto;">
                <?php
                    $unmaskedDataSetting = $settings['unmasked_data_users'] ?? '[]';
                    $allowedUsers = is_array($unmaskedDataSetting) ? $unmaskedDataSetting : (json_decode($unmaskedDataSetting, true) ?: []);
                    $salonUsers = \App\Models\User::where('salon_id', auth()->user()->salon_id)
                        ->whereHas('roles', function ($q) {
                            $q->whereIn('name', ['employee', 'staff', 'manager', 'salon_admin']);
                        })
                        ->orderBy('name')
                        ->get();
                ?>

                <div class="mb-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 ps-0" id="user-search"
                            placeholder="Filter by name or email...">
                    </div>
                </div>

                <div id="users-list">
                    <?php $__empty_1 = true; $__currentLoopData = $salonUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="form-check user-item p-2 rounded-2" data-name="<?php echo e(strtolower($user->name ?? '')); ?>"
                            data-email="<?php echo e(strtolower($user->email ?? '')); ?>">
                            <input class="form-check-input user-checkbox" type="checkbox" name="unmasked_data_users[]"
                                value="<?php echo e($user->id); ?>" id="user_<?php echo e($user->id); ?>" <?php echo e(in_array($user->id, $allowedUsers) ? 'checked' : ''); ?>>
                            <label class="form-check-label ms-2" for="user_<?php echo e($user->id); ?>">
                                <div class="fw-bold"><?php echo e($user->name); ?></div>
                                <div class="small text-muted"><?php echo e($user->email); ?></div>
                                <span class="badge bg-light text-dark font-monospace x-small"
                                    style="font-size: 0.7rem;"><?php echo e(strtoupper($user->roles->pluck('name')->join(', '))); ?></span>
                            </label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted text-center py-3 mb-0">No eligible users found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .alert-soft-info {
        background-color: rgba(59, 130, 246, 0.08);
        color: #1e40af;
    }

    .user-item {
        transition: background 0.2s;
        cursor: pointer;
    }

    .user-item:hover {
        background-color: #f1f5f9;
    }

    .x-small {
        font-size: 0.75rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const maskingEnabled = document.getElementById('customer_data_masking_enabled');
        const maskingOptions = document.getElementById('masking-options');
        const dropdownButton = document.getElementById('unmasked_data_users_dropdown');
        const selectedText = document.getElementById('selected-users-text');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const searchInput = document.getElementById('user-search');
        const userItems = document.querySelectorAll('.user-item');

        document.querySelector('.dropdown-menu').addEventListener('click', e => e.stopPropagation());

        function updateSelectedText() {
            const selected = Array.from(userCheckboxes).filter(cb => cb.checked);
            if (selected.length === 0) {
                selectedText.textContent = 'Search and select users...';
            } else if (selected.length === 1) {
                const label = document.querySelector(`label[for="${selected[0].id}"] .fw-bold`);
                selectedText.textContent = label.textContent.trim();
            } else {
                selectedText.textContent = `${selected.length} users exempted from masking`;
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const term = this.value.toLowerCase();
                userItems.forEach(item => {
                    const match = item.dataset.name.includes(term) || item.dataset.email.includes(term);
                    item.style.display = match ? 'block' : 'none';
                });
            });
        }

        function toggleMaskingOptions() {
            const enabled = maskingEnabled.checked;
            maskingOptions.style.opacity = enabled ? '1' : '0.4';
            maskingOptions.style.pointerEvents = enabled ? 'auto' : 'none';
            maskingOptions.querySelectorAll('input').forEach(i => i.disabled = !enabled);
            if (dropdownButton) dropdownButton.disabled = !enabled;
        }

        userCheckboxes.forEach(cb => cb.addEventListener('change', updateSelectedText));
        maskingEnabled.addEventListener('change', toggleMaskingOptions);

        updateSelectedText();
        toggleMaskingOptions();
    });
</script><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\settings\partials\privacy.blade.php ENDPATH**/ ?>