<!-- Logo & Branding -->
<div class="settings-section-card">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-image"></i></div>
        <h6>Business Identity & Branding</h6>
    </div>

    <div class="settings-field-group mb-3">
        <div class="logo-upload-container">
            <div class="logo-preview-box">
                <img src="<?php echo e(asset('storage/' . ($settings['logo'] ?? 'default-logo.png'))); ?>" alt="Current Logo"
                    id="logoPreview">
            </div>
            <div class="flex-grow-1">
                <label class="form-label fw-semibold small text-uppercase text-secondary d-block">
                    <i class="fas fa-cloud-upload-alt text-primary me-2"></i>Public Logo
                </label>
                <div class="input-group">
                    <input type="file" name="logo" class="form-control" accept="image/*"
                        onchange="document.getElementById('logoPreview').src = window.URL.createObjectURL(this.files[0])">
                </div>
                <small class="text-muted mt-2 d-block">Recommended size: 512x512px. Max 2MB.</small>
            </div>
        </div>
    </div>

    <div class="settings-field-group">
        <label class="form-label fw-semibold small text-uppercase text-secondary mb-3">
            <i class="fas fa-palette text-purple me-2"></i>Accent Theme color
        </label>
        <div class="color-radio-group">
            <?php $__currentLoopData = ['pink', 'blue', 'purple', 'green', 'barber']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="color-option" title="<?php echo e($color === 'barber' ? 'Barber Shop' : ucfirst($color)); ?>">
                    <input type="radio" name="theme_color" value="<?php echo e($color); ?>" <?php echo e(app(\App\Services\SettingsService::class)->getThemeColor() == $color ? 'checked' : ''); ?>>
                    <div class="color-check bg-<?php echo e($color); ?>"></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <small class="text-muted mt-2 d-block">This color will be used for your sidebar and primary UI elements.</small>
    </div>
    <div class="settings-field-group mt-3">
        <label class="form-label fw-semibold small text-uppercase text-secondary d-block mb-2">
            <i class="fas fa-columns text-info me-2"></i>Interface Preferences
        </label>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="enable_language_toggle" name="enable_language_toggle"
                value="1" <?php echo e(($settings['enable_language_toggle'] ?? 1) ? 'checked' : ''); ?>>
            <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                for="enable_language_toggle">Show Language Toggle in App Bar</label>
        </div>
        <small class="text-muted mt-1 d-block">Enable this to allow users to switch between English and Arabic from the
            top
            bar.</small>
    </div>
</div>

<!-- Business Information -->
<div class="settings-section-card">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-building"></i></div>
        <h6>Core Business Details</h6>
    </div>

    <div class="settings-field-group">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-store text-primary me-2"></i>Business Display Name
                </label>
                <input type="text" name="business_name" class="form-control"
                    value="<?php echo e($settings['business_name'] ?? ''); ?>" placeholder="e.g. Elegant Glow Salon">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-envelope text-success me-2"></i>Official Contact Email
                </label>
                <input type="email" name="business_email" class="form-control"
                    value="<?php echo e(trim($settings['business_email'] ?? '', '\"\'')); ?>" placeholder="hello@business.com">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-phone text-warning me-2"></i>Primary Phone Number
                </label>
                <input type="text" name="business_phone" class="form-control"
                    value="<?php echo e($settings['business_phone'] ?? ''); ?>" placeholder="+1 234 567 890">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-map-marker-alt text-danger me-2"></i>Operational Address
                </label>
                <input type="text" name="business_address" class="form-control"
                    value="<?php echo e($settings['business_address'] ?? ''); ?>" placeholder="Street, City, State, ZIP">
            </div>
        </div>
    </div>
</div>

<!-- Social Presence -->
<div class="settings-section-card">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-share-alt"></i></div>
        <h6>Social Media Presence</h6>
    </div>

    <div class="settings-field-group">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fab fa-facebook text-primary me-2"></i>Facebook Page
                </label>
                <input type="url" name="facebook_url" class="form-control" value="<?php echo e($settings['facebook_url'] ?? ''); ?>"
                    placeholder="https://facebook.com/yoursalon">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fab fa-instagram text-danger me-2"></i>Instagram Profile
                </label>
                <input type="url" name="instagram_url" class="form-control"
                    value="<?php echo e($settings['instagram_url'] ?? ''); ?>" placeholder="https://instagram.com/yoursalon">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fab fa-twitter text-info me-2"></i>Twitter / X
                </label>
                <input type="url" name="twitter_url" class="form-control" value="<?php echo e($settings['twitter_url'] ?? ''); ?>"
                    placeholder="https://twitter.com/yoursalon">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fab fa-linkedin text-primary me-2"></i>LinkedIn Company
                </label>
                <input type="url" name="linkedin_url" class="form-control" value="<?php echo e($settings['linkedin_url'] ?? ''); ?>"
                    placeholder="https://linkedin.com/company/yoursalon">
            </div>
        </div>
    </div>
</div><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\settings\partials\general.blade.php ENDPATH**/ ?>