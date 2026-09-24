<?php $__env->startSection('content'); ?>
    <div class="container-fluidpy-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">System Configuration</h1>
                <p class="text-muted mb-0">Manage global application settings, payments, and integrations.</p>
            </div>

            <div class="d-flex gap-2">
                <form action="<?php echo e(route('admin.system-settings.clear-cache')); ?>" method="POST" class="d-inline"
                    onsubmit="return confirm('This will clear all system caches. Continue?');">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-white border shadow-sm text-danger fw-medium">
                        <i class="fas fa-sync-alt me-2"></i>Purge Cache
                    </button>
                </form>
                <button type="submit" form="settings-form" class="btn btn-primary shadow-sm fw-bold px-4">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>
        </div>

        <form action="<?php echo e(route('admin.system-settings.update')); ?>" method="POST" enctype="multipart/form-data"
            id="settings-form" novalidate>
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="row g-4">
                <!-- Navigation Sidebar -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 1.5rem; z-index: 1;">
                        <div class="card-body p-2">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                aria-orientation="vertical">
                                <button class="nav-link active d-flex align-items-center mb-1 p-3" id="v-pills-general-tab"
                                    data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab">
                                    <div class="icon-square bg-light text-dark rounded-3 me-3 p-2 d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px;">
                                        <i class="fas fa-sliders-h"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="fw-bold">General</div>
                                        <div class="small opacity-75">Branding & Localization</div>
                                    </div>
                                </button>

                                <button class="nav-link d-flex align-items-center mb-1 p-3" id="v-pills-payment-tab"
                                    data-bs-toggle="pill" data-bs-target="#payment" type="button" role="tab">
                                    <div class="icon-square bg-light text-dark rounded-3 me-3 p-2 d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px;">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="fw-bold">Payments</div>
                                        <div class="small opacity-75">Gateways & Currency</div>
                                    </div>
                                </button>

                                <button class="nav-link d-flex align-items-center mb-1 p-3" id="v-pills-email-tab"
                                    data-bs-toggle="pill" data-bs-target="#email" type="button" role="tab">
                                    <div class="icon-square bg-light text-dark rounded-3 me-3 p-2 d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px;">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="fw-bold">Email Config</div>
                                        <div class="small opacity-75">SMTP & Drivers</div>
                                    </div>
                                </button>

                                <button class="nav-link d-flex align-items-center mb-1 p-3" id="v-pills-automation-tab"
                                    data-bs-toggle="pill" data-bs-target="#automation" type="button" role="tab">
                                    <div class="icon-square bg-light text-dark rounded-3 me-3 p-2 d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px;">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="fw-bold">Automation</div>
                                        <div class="small opacity-75">Subscription Rules</div>
                                    </div>
                                </button>

                                <button class="nav-link d-flex align-items-center p-3" id="v-pills-integrations-tab"
                                    data-bs-toggle="pill" data-bs-target="#integrations" type="button" role="tab">
                                    <div class="icon-square bg-light text-dark rounded-3 me-3 p-2 d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px;">
                                        <i class="fas fa-plug"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="fw-bold">Integrations</div>
                                        <div class="small opacity-75">Analytics & SMS</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="col-lg-9">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="tab-content" id="v-pills-tabContent">

                                <!-- General Section -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel">
                                    <h5 class="fw-bold text-gray-800 mb-4">General Settings</h5>

                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Application
                                                Identity</label>
                                            <input type="text" class="form-control form-control-lg" name="app_name"
                                                value="<?php echo e(old('app_name', trim($settings['app_name'] ?? config('app.name'), '"'))); ?>"
                                                placeholder="e.g. Salon SaaS">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Logo</label>
                                            <div class="card bg-light border-0 text-center p-3">
                                                <?php if(isset($settings['app_logo'])): ?>
                                                    <img src="<?php echo e(asset('storage/' . $settings['app_logo'])); ?>"
                                                        class="mx-auto d-block mb-3" style="max-height: 60px;"
                                                        id="logo-preview">
                                                <?php else: ?>
                                                    <img src="" class="mx-auto d-block mb-3 d-none" style="max-height: 60px;"
                                                        id="logo-preview-new">
                                                    <div id="logo-placeholder" class="text-muted mb-3"><i
                                                            class="far fa-image fa-2x"></i></div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control form-control-sm" name="app_logo"
                                                    accept="image/*" onchange="previewImage(this, 'logo')">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label
                                                class="form-label small fw-bold text-muted text-uppercase">Favicon</label>
                                            <div class="card bg-light border-0 text-center p-3">
                                                <?php if(isset($settings['app_favicon'])): ?>
                                                    <img src="<?php echo e(asset('storage/' . $settings['app_favicon'])); ?>"
                                                        class="mx-auto d-block mb-3" style="max-height: 32px;"
                                                        id="favicon-preview">
                                                <?php else: ?>
                                                    <img src="" class="mx-auto d-block mb-3 d-none" style="max-height: 32px;"
                                                        id="favicon-preview-new">
                                                    <div id="favicon-placeholder" class="text-muted mb-3"><i
                                                            class="far fa-star fa-2x"></i></div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control form-control-sm" name="app_favicon"
                                                    accept="image/*" onchange="previewImage(this, 'favicon')">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <hr class="my-2">
                                        </div>

                                        <div class="col-md-4">
                                            <label
                                                class="form-label small fw-bold text-muted text-uppercase">Timezone</label>
                                            <select class="form-select" name="app_timezone">
                                                <option value="UTC" <?php echo e(($settings['app_timezone'] ?? 'UTC') == 'UTC' ? 'selected' : ''); ?>>UTC</option>
                                                <option value="America/New_York" <?php echo e(($settings['app_timezone'] ?? '') == 'America/New_York' ? 'selected' : ''); ?>>America/New York</option>
                                                <option value="Europe/London" <?php echo e(($settings['app_timezone'] ?? '') == 'Europe/London' ? 'selected' : ''); ?>>Europe/London</option>
                                                <option value="Asia/Kolkata" <?php echo e(($settings['app_timezone'] ?? '') == 'Asia/Kolkata' ? 'selected' : ''); ?>>Asia/Kolkata</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Date
                                                Format</label>
                                            <select class="form-select" name="date_format">
                                                <option value="Y-m-d" <?php echo e(($settings['date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : ''); ?>>YYYY-MM-DD</option>
                                                <option value="m/d/Y" <?php echo e(($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : ''); ?>>MM/DD/YYYY</option>
                                                <option value="d/m/Y" <?php echo e(($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : ''); ?>>DD/MM/YYYY</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Time
                                                Format</label>
                                            <select class="form-select" name="time_format">
                                                <option value="H:i" <?php echo e(($settings['time_format'] ?? 'H:i') == 'H:i' ? 'selected' : ''); ?>>24 Hour (14:30)</option>
                                                <option value="h:i A" <?php echo e(($settings['time_format'] ?? '') == 'h:i A' ? 'selected' : ''); ?>>12 Hour (02:30 PM)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Section -->
                                <div class="tab-pane fade" id="payment" role="tabpanel">
                                    <h5 class="fw-bold text-gray-800 mb-4">Payment & Currency</h5>

                                    <div class="row g-4 text-center mb-4">
                                        <div class="col-md-6">
                                            <div
                                                class="card h-100 <?php echo e(($settings['enable_online_payment'] ?? true) ? 'border-primary bg-primary-subtle' : 'border-light bg-light'); ?>">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-center align-items-center mb-3">
                                                        <i
                                                            class="fas fa-credit-card fa-2x mb-2 <?php echo e(($settings['enable_online_payment'] ?? true) ? 'text-primary' : 'text-muted'); ?>"></i>
                                                    </div>
                                                    <h6 class="fw-bold">Online Payments</h6>
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            name="enable_online_payment" value="1" <?php echo e(old('enable_online_payment', $settings['enable_online_payment'] ?? true) ? 'checked' : ''); ?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div
                                                class="card h-100 <?php echo e(($settings['enable_manual_payment'] ?? true) ? 'border-success bg-success-subtle' : 'border-light bg-light'); ?>">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-center align-items-center mb-3">
                                                        <i
                                                            class="fas fa-file-invoice-dollar fa-2x mb-2 <?php echo e(($settings['enable_manual_payment'] ?? true) ? 'text-success' : 'text-muted'); ?>"></i>
                                                    </div>
                                                    <h6 class="fw-bold">Manual Payments (Offline)</h6>
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            name="enable_manual_payment" value="1" <?php echo e(old('enable_manual_payment', $settings['enable_manual_payment'] ?? true) ? 'checked' : ''); ?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Currency
                                                Code</label>
                                            <select class="form-select" name="currency" id="currency">
                                                <option value="USD" <?php echo e(($settings['currency'] ?? '') == 'USD' ? 'selected' : ''); ?>>USD - US Dollar</option>
                                                <option value="EUR" <?php echo e(($settings['currency'] ?? '') == 'EUR' ? 'selected' : ''); ?>>EUR - Euro</option>
                                                <option value="GBP" <?php echo e(($settings['currency'] ?? '') == 'GBP' ? 'selected' : ''); ?>>GBP - British Pound</option>
                                                <option value="INR" <?php echo e(($settings['currency'] ?? '') == 'INR' ? 'selected' : ''); ?>>INR - Indian Rupee</option>
                                                <option value="AED" <?php echo e(($settings['currency'] ?? '') == 'AED' ? 'selected' : ''); ?>>AED - UAE Dirham</option>
                                                <option value="QAR" <?php echo e(($settings['currency'] ?? '') == 'QAR' ? 'selected' : ''); ?>>QAR - Qatari Riyal</option>
                                                <option value="SAR" <?php echo e(($settings['currency'] ?? '') == 'SAR' ? 'selected' : ''); ?>>SAR - Saudi Riyal</option>
                                                <option value="CAD" <?php echo e(($settings['currency'] ?? '') == 'CAD' ? 'selected' : ''); ?>>CAD - Canadian Dollar</option>
                                                <option value="AUD" <?php echo e(($settings['currency'] ?? '') == 'AUD' ? 'selected' : ''); ?>>AUD - Australian Dollar</option>
                                                <option value="JPY" <?php echo e(($settings['currency'] ?? '') == 'JPY' ? 'selected' : ''); ?>>JPY - Japanese Yen</option>
                                                <option value="CNY" <?php echo e(($settings['currency'] ?? '') == 'CNY' ? 'selected' : ''); ?>>CNY - Chinese Yuan</option>
                                                <option value="CHF" <?php echo e(($settings['currency'] ?? '') == 'CHF' ? 'selected' : ''); ?>>CHF - Swiss Franc</option>
                                                <option value="SGD" <?php echo e(($settings['currency'] ?? '') == 'SGD' ? 'selected' : ''); ?>>SGD - Singapore Dollar</option>
                                                <option value="ZAR" <?php echo e(($settings['currency'] ?? '') == 'ZAR' ? 'selected' : ''); ?>>ZAR - South African Rand</option>
                                                <option value="BRL" <?php echo e(($settings['currency'] ?? '') == 'BRL' ? 'selected' : ''); ?>>BRL - Brazilian Real</option>
                                                <option value="RUB" <?php echo e(($settings['currency'] ?? '') == 'RUB' ? 'selected' : ''); ?>>RUB - Russian Ruble</option>
                                                <option value="KRW" <?php echo e(($settings['currency'] ?? '') == 'KRW' ? 'selected' : ''); ?>>KRW - South Korean Won</option>
                                                <option value="TRY" <?php echo e(($settings['currency'] ?? '') == 'TRY' ? 'selected' : ''); ?>>TRY - Turkish Lira</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Symbol</label>
                                            <select class="form-select" name="currency_symbol" id="currency_symbol">
                                                <option value="$" <?php echo e(($settings['currency_symbol'] ?? '') == '$' ? 'selected' : ''); ?>>$ (Dollar/Peso)</option>
                                                <option value="€" <?php echo e(($settings['currency_symbol'] ?? '') == '€' ? 'selected' : ''); ?>>€ (Euro)</option>
                                                <option value="£" <?php echo e(($settings['currency_symbol'] ?? '') == '£' ? 'selected' : ''); ?>>£ (Pound)</option>
                                                <option value="₹" <?php echo e(($settings['currency_symbol'] ?? '') == '₹' ? 'selected' : ''); ?>>₹ (Rupee)</option>
                                                <option value="د.إ" <?php echo e(($settings['currency_symbol'] ?? '') == 'د.إ' ? 'selected' : ''); ?>>د.إ (AED)</option>
                                                <option value="QR" <?php echo e(($settings['currency_symbol'] ?? '') == 'QR' ? 'selected' : ''); ?>>QR (Qatari Riyal)</option>
                                                <option value="SR" <?php echo e(($settings['currency_symbol'] ?? '') == 'SR' ? 'selected' : ''); ?>>SR (Saudi Riyal)</option>
                                                <option value="¥" <?php echo e(($settings['currency_symbol'] ?? '') == '¥' ? 'selected' : ''); ?>>¥ (Yen/Yuan)</option>
                                                <option value="Fr" <?php echo e(($settings['currency_symbol'] ?? '') == 'Fr' ? 'selected' : ''); ?>>Fr (Franc)</option>
                                                <option value="S$" <?php echo e(($settings['currency_symbol'] ?? '') == 'S$' ? 'selected' : ''); ?>>S$ (SGD)</option>
                                                <option value="R" <?php echo e(($settings['currency_symbol'] ?? '') == 'R' ? 'selected' : ''); ?>>R (Rand)</option>
                                                <option value="R$" <?php echo e(($settings['currency_symbol'] ?? '') == 'R$' ? 'selected' : ''); ?>>R$ (Real)</option>
                                                <option value="₽" <?php echo e(($settings['currency_symbol'] ?? '') == '₽' ? 'selected' : ''); ?>>₽ (Ruble)</option>
                                                <option value="₩" <?php echo e(($settings['currency_symbol'] ?? '') == '₩' ? 'selected' : ''); ?>>₩ (Won)</option>
                                                <option value="₺" <?php echo e(($settings['currency_symbol'] ?? '') == '₺' ? 'selected' : ''); ?>>₺ (Lira)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Tax Rate
                                                (%)</label>
                                            <input type="number" step="0.01" class="form-control" name="tax_rate"
                                                value="<?php echo e($settings['tax_rate'] ?? 0); ?>">
                                        </div>

                                        <div class="col-12 mt-4 mb-2">
                                            <h6 class="fw-bold text-dark border-bottom pb-2">Stripe Configuration</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small">Publishable Key</label>
                                            <input type="text" class="form-control" name="stripe_publishable_key"
                                                value="<?php echo e($settings['stripe_publishable_key'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small">Secret Key</label>
                                            <input type="password" class="form-control" name="stripe_secret_key"
                                                value="<?php echo e($settings['stripe_secret_key'] ?? ''); ?>">
                                        </div>

                                        <div class="col-12 mt-4 mb-2">
                                            <h6 class="fw-bold text-dark border-bottom pb-2">PayPal Configuration</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small">Client ID</label>
                                            <input type="text" class="form-control" name="paypal_client_id"
                                                value="<?php echo e($settings['paypal_client_id'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small">Secret</label>
                                            <input type="password" class="form-control" name="paypal_secret"
                                                value="<?php echo e($settings['paypal_secret'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Email Section -->
                                <div class="tab-pane fade" id="email" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="fw-bold text-gray-800 mb-0">Email Configuration</h5>
                                        <button type="button" class="btn btn-sm btn-info text-white shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#testEmailModal">
                                            <i class="fas fa-paper-plane me-1"></i> Send Test
                                        </button>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Mail
                                                Driver</label>
                                            <select class="form-select form-select-lg" name="mail_driver" id="mail_driver"
                                                onchange="toggleMailFields()">
                                                <option value="smtp" <?php echo e(($settings['mail_driver'] ?? 'smtp') == 'smtp' ? 'selected' : ''); ?>>SMTP (Recommended)</option>
                                                <option value="mailgun" <?php echo e(($settings['mail_driver'] ?? '') == 'mailgun' ? 'selected' : ''); ?>>Mailgun</option>
                                                <option value="ses" <?php echo e(($settings['mail_driver'] ?? '') == 'ses' ? 'selected' : ''); ?>>Amazon SES</option>
                                                <option value="log" <?php echo e(($settings['mail_driver'] ?? '') == 'log' ? 'selected' : ''); ?>>Log File (Debug)</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Sender
                                                Email</label>
                                            <input type="email" class="form-control" name="mail_from_address"
                                                value="<?php echo e($settings['mail_from_address'] ?? config('mail.from.address')); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Sender
                                                Name</label>
                                            <input type="text" class="form-control" name="mail_from_name"
                                                value="<?php echo e($settings['mail_from_name'] ?? config('mail.from.name')); ?>">
                                        </div>

                                        <!-- SMTP Fields -->
                                        <div id="smtp_fields" class="driver-fields col-12 row g-3 mt-2">
                                            <div class="col-12">
                                                <h6 class="border-bottom pb-2 fw-bold">SMTP Settings</h6>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label text-muted small">Host</label>
                                                <input type="text" class="form-control" name="mail_host"
                                                    value="<?php echo e($settings['mail_host'] ?? ''); ?>"
                                                    placeholder="smtp.example.com">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-muted small">Port</label>
                                                <input type="number" class="form-control" name="mail_port"
                                                    value="<?php echo e(trim($settings['mail_port'] ?? '587', '"')); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted small">Username</label>
                                                <input type="text" class="form-control" name="mail_username"
                                                    value="<?php echo e($settings['mail_username'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted small">Password</label>
                                                <input type="password" class="form-control" name="mail_password"
                                                    value="<?php echo e($settings['mail_password'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted small">Encryption</label>
                                                <select class="form-select" name="mail_encryption">
                                                    <option value="tls" <?php echo e(($settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : ''); ?>>TLS</option>
                                                    <option value="ssl" <?php echo e(($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : ''); ?>>SSL</option>
                                                    <option value="" <?php echo e(($settings['mail_encryption'] ?? '') == '' ? 'selected' : ''); ?>>None</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Other drivers hidden for brevity but structure maintained -->
                                        <div id="mailgun_fields" class="driver-fields col-12 row g-3 mt-2"
                                            style="display: none;">
                                            <div class="col-12">
                                                <h6 class="border-bottom pb-2 fw-bold">Mailgun Settings</h6>
                                            </div>
                                            <div class="col-md-6"><input type="text" class="form-control"
                                                    name="mailgun_domain" placeholder="Domain"
                                                    value="<?php echo e($settings['mailgun_domain'] ?? ''); ?>"></div>
                                            <div class="col-md-6"><input type="password" class="form-control"
                                                    name="mailgun_secret" placeholder="Secret"
                                                    value="<?php echo e($settings['mailgun_secret'] ?? ''); ?>"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Automation Section -->
                                <div class="tab-pane fade" id="automation" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="fw-bold text-gray-800 mb-0">Automation Rules</h5>
                                        <a href="<?php echo e(route('admin.email-templates.index')); ?>"
                                            class="btn btn-sm btn-outline-primary shadow-sm">
                                            <i class="fas fa-edit me-1"></i> Templates
                                        </a>
                                    </div>

                                    <div class="row g-4">
                                        <!-- Expired Logic -->
                                        <div class="col-lg-12">
                                            <div class="card border border-danger-subtle bg-danger-subtle h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="bg-white p-2 rounded-circle text-danger shadow-sm">
                                                            <i class="fas fa-ban fa-lg"></i>
                                                        </div>
                                                        <div class="w-100">
                                                            <div class="form-check form-switch float-end">
                                                                <input class="form-check-input" type="checkbox"
                                                                    style="width: 3em; height: 1.5em;" role="switch"
                                                                    name="email_subscription_expired_enabled" value="1" <?php echo e(($settings['email_subscription_expired_enabled'] ?? true) ? 'checked' : ''); ?>>
                                                            </div>
                                                            <h6 class="fw-bold text-danger mb-1">Subscription Expiration
                                                                Handling</h6>
                                                            <p class="small text-muted mb-0">
                                                                Automatically deactivate salon and send "Expired" email on
                                                                end date.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Warnings Logic -->
                                        <div class="col-lg-12">
                                            <div class="card border border-warning-subtle bg-warning-subtle h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="bg-white p-2 rounded-circle text-warning shadow-sm">
                                                            <i class="fas fa-bell fa-lg"></i>
                                                        </div>
                                                        <div class="w-100">
                                                            <div class="form-check form-switch float-end">
                                                                <input class="form-check-input" type="checkbox"
                                                                    style="width: 3em; height: 1.5em;" role="switch"
                                                                    name="email_subscription_expiring_soon_enabled"
                                                                    value="1" <?php echo e(($settings['email_subscription_expiring_soon_enabled'] ?? true) ? 'checked' : ''); ?>>
                                                            </div>
                                                            <h6 class="fw-bold text-dark mb-3">Expiration Reminders</h6>

                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label small fw-bold opacity-75">1st
                                                                        Notice (Days Before)</label>
                                                                    <div class="input-group bg-white rounded shadow-sm">
                                                                        <span
                                                                            class="input-group-text border-0 bg-transparent"><i
                                                                                class="far fa-clock"></i></span>
                                                                        <input type="number" class="form-control border-0"
                                                                            name="email_subscription_expiring_soon_days_1"
                                                                            value="<?php echo e($settings['email_subscription_expiring_soon_days_1'] ?? 7); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label
                                                                        class="form-label small fw-bold opacity-75">Urgent
                                                                        Notice (Days Before)</label>
                                                                    <div class="input-group bg-white rounded shadow-sm">
                                                                        <span
                                                                            class="input-group-text border-0 bg-transparent text-danger"><i
                                                                                class="fas fa-exclamation-circle"></i></span>
                                                                        <input type="number" class="form-control border-0"
                                                                            name="email_subscription_expiring_soon_days_2"
                                                                            value="<?php echo e($settings['email_subscription_expiring_soon_days_2'] ?? 3); ?>">
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

                                <!-- Integrations Section -->
                                <div class="tab-pane fade" id="integrations" role="tabpanel">
                                    <h5 class="fw-bold text-gray-800 mb-4">Integrations</h5>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Google
                                                Analytics ID</label>
                                            <input type="text" class="form-control" name="google_analytics_id"
                                                value="<?php echo e($settings['google_analytics_id'] ?? ''); ?>"
                                                placeholder="G-XXXXXXXX">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Facebook Pixel
                                                ID</label>
                                            <input type="text" class="form-control" name="facebook_pixel_id"
                                                value="<?php echo e($settings['facebook_pixel_id'] ?? ''); ?>">
                                        </div>

                                        <div class="col-12">
                                            <hr>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="enable_sms" value="1" <?php echo e(($settings['enable_sms'] ?? false) ? 'checked' : ''); ?>>
                                                <label class="form-check-label fw-bold">Enable SMS Gateway (Twilio)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="twilio_sid" placeholder="SID"
                                                value="<?php echo e($settings['twilio_sid'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="password" class="form-control" name="twilio_token"
                                                placeholder="Auth Token" value="<?php echo e($settings['twilio_token'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="twilio_from"
                                                placeholder="From Number" value="<?php echo e($settings['twilio_from'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Test Email Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Send Test Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo e(route('admin.system-settings.test-email')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <p class="text-muted small">Send a test email to verify your SMTP configuration.</p>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="test_email" name="email"
                                placeholder="name@example.com" required>
                            <label for="test_email">Recipient Email</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function previewImage(input, type) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById(type + '-preview-new').src = e.target.result;
                    document.getElementById(type + '-preview-new').classList.remove('d-none');
                    if (document.getElementById(type + '-placeholder')) {
                        document.getElementById(type + '-placeholder').classList.add('d-none');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function toggleMailFields() {
            const driver = document.getElementById('mail_driver').value;
            document.querySelectorAll('.driver-fields').forEach(field => field.style.display = 'none');
            if (document.getElementById(driver + '_fields')) {
                document.getElementById(driver + '_fields').style.display = 'flex';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleMailFields();

            // Highlight invalid inputs and switch tab
            const invalidInput = document.querySelector('.is-invalid');
            if (invalidInput) {
                const tabPane = invalidInput.closest('.tab-pane');
                const tabId = tabPane.getAttribute('id');
                const tabButton = document.querySelector(`[data-bs-target="#${tabId}"]`);
                if (tabButton) {
                    const tab = new bootstrap.Tab(tabButton);
                    tab.show();
                }
            }

            // Auto Currency Symbol
            const currencySelect = document.getElementById('currency');
            const symbolSelect = document.getElementById('currency_symbol');

            if (currencySelect && symbolSelect) {
                currencySelect.addEventListener('change', function () {
                    const currency = this.value;
                    const symbolMap = {
                        'USD': '$', // Dollar
                        'EUR': '€', // Euro
                        'GBP': '£', // Pound
                        'INR': '₹', // Rupee
                        'AED': 'د.إ', // Dirham
                        'QAR': 'QR', // Riyal
                        'SAR': 'SR', // Saudi Riyal
                        'CAD': '$', // Canadian Dollar
                        'AUD': '$', // Australian Dollar
                        'JPY': '¥', // Yen
                        'CNY': '¥', // Yuan
                        'CHF': 'Fr', // Franc
                        'SGD': 'S$', // Singapore Dollar
                        'ZAR': 'R', // Rand
                        'BRL': 'R$', // Real
                        'RUB': '₽', // Ruble
                        'KRW': '₩', // Won
                        'TRY': '₺' // Lira
                    };

                    if (symbolMap[currency]) {
                        symbolSelect.value = symbolMap[currency];
                    }
                });
            }
        });
    </script>

    <style>
        .nav-pills .nav-link {
            color: #5a5c69;
            transition: all 0.2s;
            border-radius: 0.5rem;
        }

        .nav-pills .nav-link:hover {
            background-color: #f8f9fc;
            color: #4e73df;
        }

        .nav-pills .nav-link.active {
            background-color: #4e73df;
            color: white !important;
        }

        .nav-pills .nav-link.active .icon-square {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
        }

        .nav-pills .nav-link.active .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\super-admin\settings\index.blade.php ENDPATH**/ ?>