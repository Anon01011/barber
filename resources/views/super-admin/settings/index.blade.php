@extends('layouts.super-admin')

@section('content')
    <div class="container-fluidpy-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">System Configuration</h1>
                <p class="text-muted mb-0">Manage global application settings, payments, and integrations.</p>
            </div>

            <div class="d-flex gap-2">
                <form action="{{ route('admin.system-settings.clear-cache') }}" method="POST" class="d-inline"
                    onsubmit="return confirm('This will clear all system caches. Continue?');">
                    @csrf
                    <button type="submit" class="btn btn-white border shadow-sm text-danger fw-medium">
                        <i class="fas fa-sync-alt me-2"></i>Purge Cache
                    </button>
                </form>
                <button type="submit" form="settings-form" class="btn btn-primary shadow-sm fw-bold px-4">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>
        </div>

        <form action="{{ route('admin.system-settings.update') }}" method="POST" enctype="multipart/form-data"
            id="settings-form" novalidate>
            @csrf
            @method('PUT')

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

                                <button class="nav-link d-flex align-items-center mb-1 p-3" id="v-pills-integrations-tab"
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

                                <button class="nav-link d-flex align-items-center p-3" id="v-pills-website-tab"
                                    data-bs-toggle="pill" data-bs-target="#website" type="button" role="tab">
                                    <div class="icon-square bg-light text-dark rounded-3 me-3 p-2 d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px;">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="fw-bold">Website & CMS</div>
                                        <div class="small opacity-75">Landing, About, Contact & Legal</div>
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
                                                value="{{ old('app_name', trim($settings['app_name'] ?? config('app.name'), '"')) }}"
                                                placeholder="e.g. Salon SaaS">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Logo</label>
                                            <div class="card bg-light border-0 text-center p-3">
                                                @if(isset($settings['app_logo']))
                                                    <img src="{{ asset('storage/' . $settings['app_logo']) }}"
                                                        class="mx-auto d-block mb-3" style="max-height: 60px;"
                                                        id="logo-preview">
                                                @else
                                                    <img src="" class="mx-auto d-block mb-3 d-none" style="max-height: 60px;"
                                                        id="logo-preview-new">
                                                    <div id="logo-placeholder" class="text-muted mb-3"><i
                                                            class="far fa-image fa-2x"></i></div>
                                                @endif
                                                <input type="file" class="form-control form-control-sm" name="app_logo"
                                                    accept="image/*" onchange="previewImage(this, 'logo')">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label
                                                class="form-label small fw-bold text-muted text-uppercase">Favicon</label>
                                            <div class="card bg-light border-0 text-center p-3">
                                                @if(isset($settings['app_favicon']))
                                                    <img src="{{ asset('storage/' . $settings['app_favicon']) }}"
                                                        class="mx-auto d-block mb-3" style="max-height: 32px;"
                                                        id="favicon-preview">
                                                @else
                                                    <img src="" class="mx-auto d-block mb-3 d-none" style="max-height: 32px;"
                                                        id="favicon-preview-new">
                                                    <div id="favicon-placeholder" class="text-muted mb-3"><i
                                                            class="far fa-star fa-2x"></i></div>
                                                @endif
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
                                                <option value="UTC" {{ ($settings['app_timezone'] ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                                <option value="America/New_York" {{ ($settings['app_timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>America/New York</option>
                                                <option value="Europe/London" {{ ($settings['app_timezone'] ?? '') == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                                <option value="Asia/Kolkata" {{ ($settings['app_timezone'] ?? '') == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Date
                                                Format</label>
                                            <select class="form-select" name="date_format">
                                                <option value="Y-m-d" {{ ($settings['date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                                <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                                <option value="d/m/Y" {{ ($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Time
                                                Format</label>
                                            <select class="form-select" name="time_format">
                                                <option value="H:i" {{ ($settings['time_format'] ?? 'H:i') == 'H:i' ? 'selected' : '' }}>24 Hour (14:30)</option>
                                                <option value="h:i A" {{ ($settings['time_format'] ?? '') == 'h:i A' ? 'selected' : '' }}>12 Hour (02:30 PM)</option>
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
                                                class="card h-100 {{ ($settings['enable_online_payment'] ?? true) ? 'border-primary bg-primary-subtle' : 'border-light bg-light' }}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-center align-items-center mb-3">
                                                        <i
                                                            class="fas fa-credit-card fa-2x mb-2 {{ ($settings['enable_online_payment'] ?? true) ? 'text-primary' : 'text-muted' }}"></i>
                                                    </div>
                                                    <h6 class="fw-bold">Online Payments</h6>
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            name="enable_online_payment" value="1" {{ old('enable_online_payment', $settings['enable_online_payment'] ?? true) ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div
                                                class="card h-100 {{ ($settings['enable_manual_payment'] ?? true) ? 'border-success bg-success-subtle' : 'border-light bg-light' }}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-center align-items-center mb-3">
                                                        <i
                                                            class="fas fa-file-invoice-dollar fa-2x mb-2 {{ ($settings['enable_manual_payment'] ?? true) ? 'text-success' : 'text-muted' }}"></i>
                                                    </div>
                                                    <h6 class="fw-bold">Manual Payments (Offline)</h6>
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            name="enable_manual_payment" value="1" {{ old('enable_manual_payment', $settings['enable_manual_payment'] ?? true) ? 'checked' : '' }}>
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
                                                <option value="USD" {{ ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                                <option value="EUR" {{ ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                                <option value="GBP" {{ ($settings['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                                <option value="INR" {{ ($settings['currency'] ?? '') == 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                                <option value="AED" {{ ($settings['currency'] ?? '') == 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                                                <option value="QAR" {{ ($settings['currency'] ?? '') == 'QAR' ? 'selected' : '' }}>QAR - Qatari Riyal</option>
                                                <option value="SAR" {{ ($settings['currency'] ?? '') == 'SAR' ? 'selected' : '' }}>SAR - Saudi Riyal</option>
                                                <option value="CAD" {{ ($settings['currency'] ?? '') == 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                                                <option value="AUD" {{ ($settings['currency'] ?? '') == 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                                                <option value="JPY" {{ ($settings['currency'] ?? '') == 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen</option>
                                                <option value="CNY" {{ ($settings['currency'] ?? '') == 'CNY' ? 'selected' : '' }}>CNY - Chinese Yuan</option>
                                                <option value="CHF" {{ ($settings['currency'] ?? '') == 'CHF' ? 'selected' : '' }}>CHF - Swiss Franc</option>
                                                <option value="SGD" {{ ($settings['currency'] ?? '') == 'SGD' ? 'selected' : '' }}>SGD - Singapore Dollar</option>
                                                <option value="ZAR" {{ ($settings['currency'] ?? '') == 'ZAR' ? 'selected' : '' }}>ZAR - South African Rand</option>
                                                <option value="BRL" {{ ($settings['currency'] ?? '') == 'BRL' ? 'selected' : '' }}>BRL - Brazilian Real</option>
                                                <option value="RUB" {{ ($settings['currency'] ?? '') == 'RUB' ? 'selected' : '' }}>RUB - Russian Ruble</option>
                                                <option value="KRW" {{ ($settings['currency'] ?? '') == 'KRW' ? 'selected' : '' }}>KRW - South Korean Won</option>
                                                <option value="TRY" {{ ($settings['currency'] ?? '') == 'TRY' ? 'selected' : '' }}>TRY - Turkish Lira</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Symbol</label>
                                            <select class="form-select" name="currency_symbol" id="currency_symbol">
                                                <option value="$" {{ ($settings['currency_symbol'] ?? '') == '$' ? 'selected' : '' }}>$ (Dollar/Peso)</option>
                                                <option value="€" {{ ($settings['currency_symbol'] ?? '') == '€' ? 'selected' : '' }}>€ (Euro)</option>
                                                <option value="£" {{ ($settings['currency_symbol'] ?? '') == '£' ? 'selected' : '' }}>£ (Pound)</option>
                                                <option value="₹" {{ ($settings['currency_symbol'] ?? '') == '₹' ? 'selected' : '' }}>₹ (Rupee)</option>
                                                <option value="د.إ" {{ ($settings['currency_symbol'] ?? '') == 'د.إ' ? 'selected' : '' }}>د.إ (AED)</option>
                                                <option value="QR" {{ ($settings['currency_symbol'] ?? '') == 'QR' ? 'selected' : '' }}>QR (Qatari Riyal)</option>
                                                <option value="SR" {{ ($settings['currency_symbol'] ?? '') == 'SR' ? 'selected' : '' }}>SR (Saudi Riyal)</option>
                                                <option value="¥" {{ ($settings['currency_symbol'] ?? '') == '¥' ? 'selected' : '' }}>¥ (Yen/Yuan)</option>
                                                <option value="Fr" {{ ($settings['currency_symbol'] ?? '') == 'Fr' ? 'selected' : '' }}>Fr (Franc)</option>
                                                <option value="S$" {{ ($settings['currency_symbol'] ?? '') == 'S$' ? 'selected' : '' }}>S$ (SGD)</option>
                                                <option value="R" {{ ($settings['currency_symbol'] ?? '') == 'R' ? 'selected' : '' }}>R (Rand)</option>
                                                <option value="R$" {{ ($settings['currency_symbol'] ?? '') == 'R$' ? 'selected' : '' }}>R$ (Real)</option>
                                                <option value="₽" {{ ($settings['currency_symbol'] ?? '') == '₽' ? 'selected' : '' }}>₽ (Ruble)</option>
                                                <option value="₩" {{ ($settings['currency_symbol'] ?? '') == '₩' ? 'selected' : '' }}>₩ (Won)</option>
                                                <option value="₺" {{ ($settings['currency_symbol'] ?? '') == '₺' ? 'selected' : '' }}>₺ (Lira)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Tax Rate
                                                (%)</label>
                                            <input type="number" step="0.01" class="form-control" name="tax_rate"
                                                value="{{ $settings['tax_rate'] ?? 0 }}">
                                        </div>

                                        <div class="col-12 mt-4 mb-2">
                                            <h6 class="fw-bold text-dark border-bottom pb-2">Stripe Configuration</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small">Publishable Key</label>
                                            <input type="text" class="form-control" name="stripe_publishable_key"
                                                value="{{ $settings['stripe_publishable_key'] ?? '' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small">Secret Key</label>
                                            <input type="password" class="form-control" name="stripe_secret_key"
                                                value="{{ $settings['stripe_secret_key'] ?? '' }}">
                                        </div>

                                        <div class="col-12 mt-4 mb-2">
                                            <h6 class="fw-bold text-dark border-bottom pb-2">PayPal Configuration</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small">Client ID</label>
                                            <input type="text" class="form-control" name="paypal_client_id"
                                                value="{{ $settings['paypal_client_id'] ?? '' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small">Secret</label>
                                            <input type="password" class="form-control" name="paypal_secret"
                                                value="{{ $settings['paypal_secret'] ?? '' }}">
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
                                                <option value="smtp" {{ ($settings['mail_driver'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP (Recommended)</option>
                                                <option value="mailgun" {{ ($settings['mail_driver'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                                <option value="ses" {{ ($settings['mail_driver'] ?? '') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                                <option value="log" {{ ($settings['mail_driver'] ?? '') == 'log' ? 'selected' : '' }}>Log File (Debug)</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Sender
                                                Email</label>
                                            <input type="email" class="form-control" name="mail_from_address"
                                                value="{{ $settings['mail_from_address'] ?? config('mail.from.address') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Sender
                                                Name</label>
                                            <input type="text" class="form-control" name="mail_from_name"
                                                value="{{ $settings['mail_from_name'] ?? config('mail.from.name') }}">
                                        </div>

                                        <!-- SMTP Fields -->
                                        <div id="smtp_fields" class="driver-fields col-12 row g-3 mt-2">
                                            <div class="col-12">
                                                <h6 class="border-bottom pb-2 fw-bold">SMTP Settings</h6>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label text-muted small">Host</label>
                                                <input type="text" class="form-control" name="mail_host"
                                                    value="{{ $settings['mail_host'] ?? '' }}"
                                                    placeholder="smtp.example.com">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-muted small">Port</label>
                                                <input type="number" class="form-control" name="mail_port"
                                                    value="{{ trim($settings['mail_port'] ?? '587', '"') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted small">Username</label>
                                                <input type="text" class="form-control" name="mail_username"
                                                    value="{{ $settings['mail_username'] ?? '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted small">Password</label>
                                                <input type="password" class="form-control" name="mail_password"
                                                    value="{{ $settings['mail_password'] ?? '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted small">Encryption</label>
                                                <select class="form-select" name="mail_encryption">
                                                    <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                                    <option value="" {{ ($settings['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>None</option>
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
                                                    value="{{ $settings['mailgun_domain'] ?? '' }}"></div>
                                            <div class="col-md-6"><input type="password" class="form-control"
                                                    name="mailgun_secret" placeholder="Secret"
                                                    value="{{ $settings['mailgun_secret'] ?? '' }}"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Automation Section -->
                                <div class="tab-pane fade" id="automation" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="fw-bold text-gray-800 mb-0">Automation Rules</h5>
                                        <a href="{{ route('admin.email-templates.index') }}"
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
                                                                    name="email_subscription_expired_enabled" value="1" {{ ($settings['email_subscription_expired_enabled'] ?? true) ? 'checked' : '' }}>
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
                                                                    value="1" {{ ($settings['email_subscription_expiring_soon_enabled'] ?? true) ? 'checked' : '' }}>
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
                                                                            value="{{ $settings['email_subscription_expiring_soon_days_1'] ?? 7 }}">
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
                                                                            value="{{ $settings['email_subscription_expiring_soon_days_2'] ?? 3 }}">
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
                                                value="{{ $settings['google_analytics_id'] ?? '' }}"
                                                placeholder="G-XXXXXXXX">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Facebook Pixel
                                                ID</label>
                                            <input type="text" class="form-control" name="facebook_pixel_id"
                                                value="{{ $settings['facebook_pixel_id'] ?? '' }}">
                                        </div>

                                        <div class="col-12">
                                            <hr>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="enable_sms" value="1" {{ ($settings['enable_sms'] ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold">Enable SMS Gateway (Twilio)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="twilio_sid" placeholder="SID"
                                                value="{{ $settings['twilio_sid'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="password" class="form-control" name="twilio_token"
                                                placeholder="Auth Token" value="{{ $settings['twilio_token'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="twilio_from"
                                                placeholder="From Number" value="{{ $settings['twilio_from'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Website & CMS Section -->
                                <div class="tab-pane fade" id="website" role="tabpanel">
                                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                                        <div>
                                            <h5 class="fw-bold text-gray-800 mb-1">Website & CMS Settings</h5>
                                            <p class="text-muted small mb-0">Control hero section, contact information, about us story, terms, and privacy policy.</p>
                                        </div>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold">
                                            <i class="fas fa-magic me-1"></i> Salon & Barber Design
                                        </span>
                                    </div>

                                    <!-- Section 1: Hero Banner -->
                                    <div class="card border mb-4 rounded-3 shadow-none">
                                        <div class="card-header bg-light py-3">
                                            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-heading me-2 text-primary"></i>Landing Page Hero Section</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Announcement Badge</label>
                                                    <input type="text" class="form-control" name="hero_badge"
                                                        value="{{ old('hero_badge', $settings['hero_badge'] ?? '✨ Precision Grooming & Luxury Salon Care') }}"
                                                        placeholder="e.g. ✨ Precision Grooming & Luxury Salon Care">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Hero Main Headline</label>
                                                    <input type="text" class="form-control form-control-lg fw-semibold" name="hero_title"
                                                        value="{{ old('hero_title', $settings['hero_title'] ?? 'Masterful Precision & Modern Luxury for Hair, Beard & Style') }}"
                                                        placeholder="Main catchy headline">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Hero Subtitle / Description</label>
                                                    <textarea class="form-control" name="hero_subtitle" rows="3"
                                                        placeholder="Brief summary highlighting salon and barber services">{{ old('hero_subtitle', $settings['hero_subtitle'] ?? 'Experience seamless online bookings, VIP master barbering, artisan hair color, and bespoke treatments in one premier grooming destination.') }}</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Primary CTA Button Text</label>
                                                    <input type="text" class="form-control" name="hero_primary_btn_text"
                                                        value="{{ old('hero_primary_btn_text', $settings['hero_primary_btn_text'] ?? 'Book Appointment') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Primary CTA URL / Route</label>
                                                    <input type="text" class="form-control" name="hero_primary_btn_url"
                                                        value="{{ old('hero_primary_btn_url', $settings['hero_primary_btn_url'] ?? '/register-salon') }}"
                                                        placeholder="e.g. /register-salon or #booking">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Secondary CTA Button Text</label>
                                                    <input type="text" class="form-control" name="hero_secondary_btn_text"
                                                        value="{{ old('hero_secondary_btn_text', $settings['hero_secondary_btn_text'] ?? 'Explore Services') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Secondary CTA URL</label>
                                                    <input type="text" class="form-control" name="hero_secondary_btn_url"
                                                        value="{{ old('hero_secondary_btn_url', $settings['hero_secondary_btn_url'] ?? '#services-experience') }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Custom Hero Image (Optional)</label>
                                                    <input type="file" class="form-control" name="hero_image" accept="image/*" onchange="previewImage(this, 'hero')">
                                                    <div class="form-text">Recommended size: 1600x1000px JPG/PNG/WebP. Leave empty to keep existing or default.</div>
                                                    @if(isset($settings['hero_image']) && !empty($settings['hero_image']))
                                                        <div class="mt-2">
                                                            <span class="small text-muted me-2">Current Image:</span>
                                                            <img src="{{ asset('storage/' . $settings['hero_image']) }}" class="img-thumbnail rounded" style="max-height: 80px;" id="hero-preview">
                                                        </div>
                                                    @endif
                                                    <img src="" class="img-thumbnail rounded mt-2 d-none" style="max-height: 80px;" id="hero-preview-new">
                                                </div>

                                                <!-- Highlight Stats -->
                                                <div class="col-12 mt-3 pt-3 border-top">
                                                    <label class="form-label small fw-bold text-muted text-uppercase d-block mb-2">Live Floating Counter Cards</label>
                                                    <div class="row g-2">
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" class="form-control fw-bold" name="hero_stat_1_val" placeholder="Value" value="{{ old('hero_stat_1_val', $settings['hero_stat_1_val'] ?? '15k+') }}">
                                                                <input type="text" class="form-control" name="hero_stat_1_lbl" placeholder="Label" value="{{ old('hero_stat_1_lbl', $settings['hero_stat_1_lbl'] ?? 'Satisfied Clients') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" class="form-control fw-bold" name="hero_stat_2_val" placeholder="Value" value="{{ old('hero_stat_2_val', $settings['hero_stat_2_val'] ?? '4.9 ★') }}">
                                                                <input type="text" class="form-control" name="hero_stat_2_lbl" placeholder="Label" value="{{ old('hero_stat_2_lbl', $settings['hero_stat_2_lbl'] ?? 'Average Rating') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" class="form-control fw-bold" name="hero_stat_3_val" placeholder="Value" value="{{ old('hero_stat_3_val', $settings['hero_stat_3_val'] ?? '100%') }}">
                                                                <input type="text" class="form-control" name="hero_stat_3_lbl" placeholder="Label" value="{{ old('hero_stat_3_lbl', $settings['hero_stat_3_lbl'] ?? 'Certified Hygiene') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section 1.5: Services & Dual Experience -->
                                    <div class="card border mb-4 rounded-3 shadow-none">
                                        <div class="card-header bg-light py-3">
                                            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-cut me-2 text-warning"></i>Services Menu & Dual Experience</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Services Menu Section Title</label>
                                                    <input type="text" class="form-control" name="services_title"
                                                        value="{{ old('services_title', $settings['services_title'] ?? 'Signature Grooming & Styling Menu') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Services Menu Subtitle</label>
                                                    <input type="text" class="form-control" name="services_subtitle"
                                                        value="{{ old('services_subtitle', $settings['services_subtitle'] ?? 'Indulge in precision haircutting, classic straight razor shaves, bespoke color formulations, and restorative therapies.') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="p-3 rounded border bg-light">
                                                        <label class="form-label fw-bold text-dark"><i class="fas fa-cut text-warning me-1"></i> Barbershop Title</label>
                                                        <input type="text" class="form-control mb-2" name="barber_heading"
                                                            value="{{ old('barber_heading', $settings['barber_heading'] ?? "The Gentlemen's Barber Lounge") }}">
                                                        <label class="form-label small text-muted">Barbershop Description</label>
                                                        <textarea class="form-control" name="barber_description" rows="3">{{ old('barber_description', $settings['barber_description'] ?? 'Classic hot-towel straight razor shaves, skin fades, precision beard sculpting, and revitalizing scalp rituals.') }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="p-3 rounded border bg-light">
                                                        <label class="form-label fw-bold text-dark"><i class="fas fa-spa text-primary me-1"></i> Salon Title</label>
                                                        <input type="text" class="form-control mb-2" name="salon_heading"
                                                            value="{{ old('salon_heading', $settings['salon_heading'] ?? 'The Haute Hair & Beauty Salon') }}">
                                                        <label class="form-label small text-muted">Salon Description</label>
                                                        <textarea class="form-control" name="salon_description" rows="3">{{ old('salon_description', $settings['salon_description'] ?? 'Bespoke hair design, dimensional balayage, keratin smoothing, and restorative botanical hair therapies.') }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Why Choose Us Headline</label>
                                                    <input type="text" class="form-control" name="why_title"
                                                        value="{{ old('why_title', $settings['why_title'] ?? 'The Standard of Excellence') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Why Choose Us Subtitle</label>
                                                    <input type="text" class="form-control" name="why_subtitle"
                                                        value="{{ old('why_subtitle', $settings['why_subtitle'] ?? 'Every ritual is crafted with obsessive attention to detail, uncompromising hygiene, and VIP hospitality.') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Bottom Banner Title</label>
                                                    <input type="text" class="form-control" name="cta_banner_title"
                                                        value="{{ old('cta_banner_title', $settings['cta_banner_title'] ?? 'Ready for Your Signature Style?') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Bottom Banner Subtitle</label>
                                                    <input type="text" class="form-control" name="cta_banner_subtitle"
                                                        value="{{ old('cta_banner_subtitle', $settings['cta_banner_subtitle'] ?? 'Reserve your appointment with our master barbers and visionary stylists today.') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Bottom Banner Button Text</label>
                                                    <input type="text" class="form-control" name="cta_banner_btn_text"
                                                        value="{{ old('cta_banner_btn_text', $settings['cta_banner_btn_text'] ?? 'Book Your Appointment') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Bottom Banner Button URL</label>
                                                    <input type="text" class="form-control" name="cta_banner_btn_url"
                                                        value="{{ old('cta_banner_btn_url', $settings['cta_banner_btn_url'] ?? '/register-salon') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section 2: Contact Information & Socials -->
                                    <div class="card border mb-4 rounded-3 shadow-none">
                                        <div class="card-header bg-light py-3">
                                            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-map-marked-alt me-2 text-success"></i>Contact Details & Social Media</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Contact Phone</label>
                                                    <input type="text" class="form-control" name="contact_phone"
                                                        value="{{ old('contact_phone', $settings['contact_phone'] ?? '+974 66266581') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Contact Email</label>
                                                    <input type="email" class="form-control" name="contact_email"
                                                        value="{{ old('contact_email', $settings['contact_email'] ?? 'info@fsqatar.com') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Physical Address</label>
                                                    <input type="text" class="form-control" name="contact_address"
                                                        value="{{ old('contact_address', $settings['contact_address'] ?? 'Salwa Road, Doha, State of Qatar') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Working Hours</label>
                                                    <input type="text" class="form-control" name="contact_working_hours"
                                                        value="{{ old('contact_working_hours', $settings['contact_working_hours'] ?? 'Mon – Sun: 8:00 AM – 9:00 PM') }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Google Maps Embed URL / Iframe Code (Optional)</label>
                                                    <input type="text" class="form-control" name="contact_map_iframe"
                                                        value="{{ old('contact_map_iframe', $settings['contact_map_iframe'] ?? '') }}"
                                                        placeholder="https://maps.google.com/maps?q=... or full iframe snippet">
                                                </div>

                                                <!-- Social Channels -->
                                                <div class="col-12 pt-3 border-top">
                                                    <label class="form-label small fw-bold text-muted text-uppercase d-block mb-2">Social Media Channels</label>
                                                    <div class="row g-2">
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text"><i class="fab fa-facebook-f text-primary"></i></span>
                                                                <input type="url" class="form-control" name="social_facebook" placeholder="Facebook URL" value="{{ old('social_facebook', $settings['social_facebook'] ?? 'https://facebook.com') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text"><i class="fab fa-instagram text-danger"></i></span>
                                                                <input type="url" class="form-control" name="social_instagram" placeholder="Instagram URL" value="{{ old('social_instagram', $settings['social_instagram'] ?? 'https://instagram.com') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text"><i class="fab fa-twitter text-info"></i></span>
                                                                <input type="url" class="form-control" name="social_twitter" placeholder="Twitter/X URL" value="{{ old('social_twitter', $settings['social_twitter'] ?? 'https://twitter.com') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text"><i class="fab fa-linkedin-in text-primary"></i></span>
                                                                <input type="url" class="form-control" name="social_linkedin" placeholder="LinkedIn URL" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? 'https://linkedin.com') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text"><i class="fab fa-youtube text-danger"></i></span>
                                                                <input type="url" class="form-control" name="social_youtube" placeholder="YouTube URL" value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section 3: About Us Page Content -->
                                    <div class="card border mb-4 rounded-3 shadow-none">
                                        <div class="card-header bg-light py-3">
                                            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-info-circle me-2 text-warning"></i>About Us Page Story & Mission</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">About Page Hero Title</label>
                                                    <input type="text" class="form-control" name="about_hero_title"
                                                        value="{{ old('about_hero_title', $settings['about_hero_title'] ?? 'Where Timeless Craftsmanship Meets Modern Luxury') }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">About Page Hero Subtitle</label>
                                                    <textarea class="form-control" name="about_hero_subtitle" rows="2">{{ old('about_hero_subtitle', $settings['about_hero_subtitle'] ?? 'Dedicated to elevating the grooming and styling ritual for gentlemen and ladies who demand distinction, artistry, and VIP hospitality.') }}</textarea>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Heritage / Story Headline</label>
                                                    <input type="text" class="form-control" name="about_story_title"
                                                        value="{{ old('about_story_title', $settings['about_story_title'] ?? 'Our Heritage & Philosophy') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Story Paragraph 1</label>
                                                    <textarea class="form-control" name="about_story_p1" rows="4">{{ old('about_story_p1', $settings['about_story_p1'] ?? 'Born out of a genuine passion for grooming and artistic flair, our sanctuary bridges classic barbering traditions with cutting-edge salon innovations. From handcrafted hot-towel straight razor shaves to bespoke color formulation and restorative hair treatments, every touchpoint is designed to renew and inspire confidence.') }}</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Story Paragraph 2</label>
                                                    <textarea class="form-control" name="about_story_p2" rows="4">{{ old('about_story_p2', $settings['about_story_p2'] ?? 'We believe true grooming is an art form. Our masters and stylists undergo continuous training in modern techniques, hygiene standards, and client satisfaction to ensure that whether you are in for a quick beard trim or a total transformation, you leave looking and feeling your absolute best.') }}</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Our Mission Statement</label>
                                                    <textarea class="form-control" name="about_mission" rows="3">{{ old('about_mission', $settings['about_mission'] ?? 'To deliver unmatched styling and grooming experiences through master craftsmanship, premium products, and frictionless technology, empowering every client to look confident and feel renewed.') }}</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Our Vision Statement</label>
                                                    <textarea class="form-control" name="about_vision" rows="3">{{ old('about_vision', $settings['about_vision'] ?? 'To stand as the gold standard in premier barber and salon care—a community-centric haven where heritage craftsmanship meets contemporary elegance.') }}</textarea>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">About Page Image (Optional)</label>
                                                    <input type="file" class="form-control" name="about_image" accept="image/*" onchange="previewImage(this, 'about')">
                                                    <div class="form-text">Recommended size: 1200x800px JPG/PNG/WebP. Leave empty to use default master barber & salon photo.</div>
                                                    @if(isset($settings['about_image']) && !empty($settings['about_image']))
                                                        <div class="mt-2">
                                                            <span class="small text-muted me-2">Current Image:</span>
                                                            <img src="{{ asset('storage/' . $settings['about_image']) }}" class="img-thumbnail rounded" style="max-height: 80px;" id="about-preview">
                                                        </div>
                                                    @endif
                                                    <img src="" class="img-thumbnail rounded mt-2 d-none" style="max-height: 80px;" id="about-preview-new">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section 4: Legal Pages -->
                                    <div class="card border rounded-3 shadow-none">
                                        <div class="card-header bg-light py-3">
                                            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-file-contract me-2 text-danger"></i>Terms & Conditions & Privacy Policy</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label small fw-bold text-muted text-uppercase mb-0">Terms & Conditions Content</label>
                                                        <small class="text-muted">HTML/Text supported. Leave blank to use full standard policy.</small>
                                                    </div>
                                                    <textarea class="form-control font-monospace small" name="terms_content" rows="6" placeholder="Enter custom Terms and Conditions here or leave empty to use default comprehensive salon/barber terms">{{ old('terms_content', $settings['terms_content'] ?? '') }}</textarea>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label small fw-bold text-muted text-uppercase mb-0">Privacy Policy Content</label>
                                                        <small class="text-muted">HTML/Text supported. Leave blank to use full GDPR-ready standard policy.</small>
                                                    </div>
                                                    <textarea class="form-control font-monospace small" name="privacy_content" rows="6" placeholder="Enter custom Privacy Policy here or leave empty to use default comprehensive privacy policy">{{ old('privacy_content', $settings['privacy_content'] ?? '') }}</textarea>
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
                <form action="{{ route('admin.system-settings.test-email') }}" method="POST">
                    @csrf
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
@endsection