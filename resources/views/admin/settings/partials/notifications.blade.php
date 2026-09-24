<!-- Core Notifications -->
<div class="settings-section-card">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-bullhorn"></i></div>
        <h6>System & Client Alerts</h6>
    </div>

    <div class="settings-field-group">
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="form-check form-switch custom-switch pt-4">
                    <input class="form-check-input" type="checkbox" name="notify_new_booking" value="1"
                        id="notify_new_booking" {{ ($settings['notify_new_booking'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                        for="notify_new_booking">New Booking Alerts</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch custom-switch pt-4">
                    <input class="form-check-input" type="checkbox" name="notify_cancellation" value="1"
                        id="notify_cancellation" {{ ($settings['notify_cancellation'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                        for="notify_cancellation">Cancellation Notices</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch custom-switch pt-4">
                    <input class="form-check-input" type="checkbox" name="notify_low_stock" value="1"
                        id="notify_low_stock" {{ ($settings['notify_low_stock'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                        for="notify_low_stock">Low Stock Warnings</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch custom-switch pt-4">
                    <input class="form-check-input" type="checkbox" name="notify_new_customer" value="1"
                        id="notify_new_customer" {{ ($settings['notify_new_customer'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                        for="notify_new_customer">New Client Registration</label>
                </div>
            </div>
        </div>

        <div class="row g-4 border-top pt-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-envelope text-primary me-2"></i>Primary Notification Email
                </label>
                <input type="email" name="notification_email" class="form-control"
                    value="{{ $settings['notification_email'] ?? '' }}" placeholder="alerts@salon.com">
                <small class="text-muted">Defaults to business email if empty.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-chart-line text-info me-2"></i>Automated Daily Summary
                </label>
                <div class="input-group">
                    <div class="input-group-text bg-white">
                        <input class="form-check-input mt-0" type="checkbox" name="daily_summary_enabled" value="1" {{ ($settings['daily_summary_enabled'] ?? false) ? 'checked' : '' }}>
                    </div>
                    <input type="time" name="daily_summary_time" class="form-control"
                        value="{{ $settings['daily_summary_time'] ?? '18:00' }}">
                </div>
                <small class="text-muted">Receive a performance report every evening.</small>
            </div>
        </div>
    </div>
</div>

{{-- SMS Configuration --}}
@if (auth()->user()->salon->canUseFeature('SMS Notifications') && \App\Helpers\ModuleHelper::smsEnabled())
    <div class="settings-section-card" x-data="{ gateway: '{{ $settings['sms_gateway'] ?? 'twilio' }}' }">
        <div class="settings-section-header">
            <div class="icon-box"><i class="fas fa-sms"></i></div>
            <h6>SMS Outreach Gateway</h6>
        </div>

        <div class="settings-field-group">
            <div class="row g-4 align-items-center mb-4">
                <div class="col-md-6">
                    <div class="form-check form-switch custom-switch">
                        <input class="form-check-input" type="checkbox" name="enable_sms" value="1" id="enable_sms" {{ ($settings['enable_sms'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                            for="enable_sms">Active SMS Channel</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <select name="sms_gateway" class="form-select" x-model="gateway">
                        <option value="twilio">Twilio Cloud (Global)</option>
                        <option value="vonage">Vonage (Nexmo)</option>
                        <option value="messagebird">MessageBird</option>
                        <option value="custom">Custom API (HTTP Forwarding)</option>
                    </select>
                </div>
            </div>

            <div class="bg-light p-4 rounded-3 border">
                <!-- Twilio -->
                <div x-show="gateway === 'twilio'" x-transition>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-id-badge text-primary me-2"></i>Account SID</label>
                            <input type="text" name="twilio_sid" class="form-control font-monospace text-xs"
                                value="{{ $settings['twilio_sid'] ?? '' }}" placeholder="AC...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-key text-danger me-2"></i>Auth Token</label>
                            <input type="password" name="twilio_token" class="form-control font-monospace text-xs"
                                value="{{ $settings['twilio_token'] ?? '' }}" placeholder="••••••••">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-phone text-success me-2"></i>Sender # / ID</label>
                            <input type="text" name="twilio_from" class="form-control font-monospace text-xs"
                                value="{{ $settings['twilio_from'] ?? '' }}" placeholder="+1...">
                        </div>
                    </div>
                </div>

                <!-- Vonage -->
                <div x-show="gateway === 'vonage'" x-transition>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-id-badge text-primary me-2"></i>API Key</label>
                            <input type="text" name="vonage_api_key" class="form-control font-monospace text-xs"
                                value="{{ $settings['vonage_api_key'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-key text-danger me-2"></i>API Secret</label>
                            <input type="password" name="vonage_api_secret" class="form-control font-monospace text-xs"
                                value="{{ $settings['vonage_api_secret'] ?? '' }}" placeholder="••••••••">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-tower-broadcast text-info me-2"></i>From (Sender)</label>
                            <input type="text" name="vonage_from" class="form-control font-monospace text-xs"
                                value="{{ $settings['vonage_from'] ?? '' }}" placeholder="SalonName">
                        </div>
                    </div>
                </div>

                <!-- MessageBird -->
                <div x-show="gateway === 'messagebird'" x-transition>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-key text-primary me-2"></i>Access Key</label>
                            <input type="password" name="messagebird_access_key" class="form-control font-monospace text-xs"
                                value="{{ $settings['messagebird_access_key'] ?? '' }}" placeholder="••••••••">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-user-tag text-info me-2"></i>Originator</label>
                            <input type="text" name="messagebird_originator" class="form-control font-monospace text-xs"
                                value="{{ $settings['messagebird_originator'] ?? '' }}" placeholder="SalonName">
                        </div>
                    </div>
                </div>

                <!-- Custom Gateway -->
                <div x-show="gateway === 'custom'" x-transition>
                    <div class="row g-3">
                        <div class="col-md-9">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-link text-secondary me-2"></i>API Endpoint URL</label>
                            <input type="url" name="custom_sms_url" class="form-control text-xs"
                                value="{{ $settings['custom_sms_url'] ?? '' }}" placeholder="https://api.gateway.com/send">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-exchange-alt text-warning me-2"></i>Method</label>
                            <select name="custom_sms_method" class="form-select text-xs">
                                <option value="POST" {{ ($settings['custom_sms_method'] ?? 'POST') === 'POST' ? 'selected' : '' }}>POST</option>
                                <option value="GET" {{ ($settings['custom_sms_method'] ?? '') === 'GET' ? 'selected' : '' }}>
                                    GET</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-code text-dark me-2"></i>Auth Headers (JSON)</label>
                            <textarea name="custom_sms_headers" class="form-control font-monospace text-xs" rows="2"
                                placeholder='{"Authorization": "Bearer ..."}'>{{ $settings['custom_sms_headers'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-file-code text-muted me-2"></i>Static Payload (JSON)</label>
                            <textarea name="custom_sms_payload" class="form-control font-monospace text-xs" rows="2"
                                placeholder='{"route": "promo"}'>{{ $settings['custom_sms_payload'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-tag text-info me-2"></i>To Param</label>
                            <input type="text" name="custom_sms_to_key" class="form-control text-xs"
                                value="{{ $settings['custom_sms_to_key'] ?? 'to' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-comment-dots text-success me-2"></i>Msg Param</label>
                            <input type="text" name="custom_sms_message_key" class="form-control text-xs"
                                value="{{ $settings['custom_sms_message_key'] ?? 'message' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- WhatsApp Configuration --}}
@if (auth()->user()->salon->canUseFeature('WhatsApp Notifications') && \App\Helpers\ModuleHelper::whatsappEnabled())
    <div class="settings-section-card" x-data="{ wgateway: '{{ $settings['whatsapp_gateway'] ?? 'twilio' }}' }">
        <div class="settings-section-header">
            <div class="icon-box text-success" style="background: rgba(25, 135, 84, 0.1);"><i class="fab fa-whatsapp"></i>
            </div>
            <h6>WhatsApp Business Gateway</h6>
        </div>

        <div class="settings-field-group">
            <div class="row g-4 align-items-center mb-4">
                <div class="col-md-6">
                    <div class="form-check form-switch custom-switch-success">
                        <input class="form-check-input" type="checkbox" name="enable_whatsapp" value="1"
                            id="enable_whatsapp" {{ ($settings['enable_whatsapp'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                            for="enable_whatsapp">Active WhatsApp Channel</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <select name="whatsapp_gateway" class="form-select" x-model="wgateway">
                        <option value="twilio">Twilio Business API</option>
                        <option value="custom">Universal HTTP bridge</option>
                    </select>
                </div>
            </div>

            <div class="bg-light p-4 rounded-3 border">
                <!-- Twilio -->
                <div x-show="wgateway === 'twilio'" x-transition>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-id-badge text-success me-2"></i>Twilio SID</label>
                            <input type="text" name="whatsapp_twilio_sid" class="form-control font-monospace text-xs"
                                value="{{ $settings['whatsapp_twilio_sid'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-key text-success me-2"></i>Auth Token</label>
                            <input type="password" name="whatsapp_twilio_token" class="form-control font-monospace text-xs"
                                value="{{ $settings['whatsapp_twilio_token'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fab fa-whatsapp text-success me-2"></i>Sender # / ID</label>
                            <input type="text" name="whatsapp_twilio_from" class="form-control font-monospace text-xs"
                                value="{{ $settings['whatsapp_twilio_from'] ?? '' }}" placeholder="whatsapp:+...">
                        </div>
                    </div>
                </div>

                <!-- Custom WhatsApp -->
                <div x-show="wgateway === 'custom'" x-transition>
                    <div class="row g-3">
                        <div class="col-md-9">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-link text-secondary me-2"></i>Universal bridge URL</label>
                            <input type="url" name="whatsapp_custom_url" class="form-control text-xs"
                                value="{{ $settings['whatsapp_custom_url'] ?? '' }}"
                                placeholder="https://bridge.whatsapp.com/send">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-exchange-alt text-secondary me-2"></i>Method</label>
                            <select name="whatsapp_custom_method" class="form-select text-xs">
                                <option value="POST" {{ ($settings['whatsapp_custom_method'] ?? 'POST') === 'POST' ? 'selected' : '' }}>POST</option>
                                <option value="GET" {{ ($settings['whatsapp_custom_method'] ?? '') === 'GET' ? 'selected' : '' }}>GET</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-code text-secondary me-2"></i>Headers (JSON)</label>
                            <textarea name="whatsapp_custom_headers" class="form-control font-monospace text-xs" rows="2"
                                placeholder='{"X-API-KEY": "..."}'>{{ $settings['whatsapp_custom_headers'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-file-code text-secondary me-2"></i>Dynamic Payload (JSON)</label>
                            <textarea name="whatsapp_custom_payload" class="form-control font-monospace text-xs" rows="2"
                                placeholder='{"message": "{message}", "to": "{to}"}'>{{ $settings['whatsapp_custom_payload'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-tag text-secondary me-2"></i>To Key</label>
                            <input type="text" name="whatsapp_custom_to_key" class="form-control text-xs"
                                value="{{ $settings['whatsapp_custom_to_key'] ?? 'to' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-secondary"><i
                                    class="fas fa-key text-secondary me-2"></i>Msg Key</label>
                            <input type="text" name="whatsapp_custom_message_key" class="form-control text-xs"
                                value="{{ $settings['whatsapp_custom_message_key'] ?? 'message' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
    .text-xs {
        font-size: 0.8rem;
    }
</style>


<style>
    .custom-switch-success .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
</style>