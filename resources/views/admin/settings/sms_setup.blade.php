@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800 fw-bold">SMS Setup Wizard</h1>
                <p class="text-muted mb-0">Follow these steps to enable SMS notifications for your salon.</p>
            </div>
            <a href="{{ route('admin.salon-settings.index', ['salon_slug' => $salon->slug]) }}"
                class="btn btn-light border shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Return to Settings
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-0">
                        <!-- Progress Bar -->
                        <div class="p-4 bg-light border-bottom rounded-top">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small fw-bold text-primary text-uppercase">Setup Progress</span>
                                <span class="small fw-bold text-primary" id="progress-text">Step 1 of 3</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" id="setup-progress"
                                    role="progressbar" style="width: 33%"></div>
                            </div>
                        </div>

                        <!-- Step 1: Gateway Selection -->
                        <div id="step-1" class="setup-step p-4 p-md-5"
                            x-data="{ gateway: '{{ $settings['sms_gateway'] ?? 'twilio' }}' }">
                            <div class="text-center mb-5">
                                <div class="bg-primary-subtle text-primary p-4 rounded-circle d-inline-block mb-4">
                                    <i class="fas fa-server fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">Select Your SMS Provider</h4>
                                <p class="text-muted mx-auto" style="max-width: 500px;">
                                    Choose the SMS gateway you want to use. We support global leaders and custom HTTP
                                    endpoints.
                                </p>
                            </div>

                            <div class="row g-3 justify-content-center mb-5">
                                <div class="col-md-3">
                                    <label class="gateway-option p-3 border rounded text-center d-block cursor-pointer"
                                        :class="gateway === 'twilio' ? 'border-primary bg-primary-subtle' : ''">
                                        <input type="radio" name="wizard_gateway" value="twilio" class="d-none"
                                            x-model="gateway">
                                        <i class="fas fa-sms d-block mb-2 fa-2x"></i>
                                        Twilio
                                    </label>
                                </div>
                                <div class="col-md-3">
                                    <label class="gateway-option p-3 border rounded text-center d-block cursor-pointer"
                                        :class="gateway === 'vonage' ? 'border-primary bg-primary-subtle' : ''">
                                        <input type="radio" name="wizard_gateway" value="vonage" class="d-none"
                                            x-model="gateway">
                                        <i class="fas fa-phone d-block mb-2 fa-2x"></i>
                                        Vonage
                                    </label>
                                </div>
                                <div class="col-md-3">
                                    <label class="gateway-option p-3 border rounded text-center d-block cursor-pointer"
                                        :class="gateway === 'messagebird' ? 'border-primary bg-primary-subtle' : ''">
                                        <input type="radio" name="wizard_gateway" value="messagebird" class="d-none"
                                            x-model="gateway">
                                        <i class="fas fa-dove d-block mb-2 fa-2x"></i>
                                        Bird
                                    </label>
                                </div>
                                <div class="col-md-3">
                                    <label class="gateway-option p-3 border rounded text-center d-block cursor-pointer"
                                        :class="gateway === 'custom' ? 'border-primary bg-primary-subtle' : ''">
                                        <input type="radio" name="wizard_gateway" value="custom" class="d-none"
                                            x-model="gateway">
                                        <i class="fas fa-code d-block mb-2 fa-2x"></i>
                                        Custom
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-primary btn-lg px-5 shadow-sm next-step" data-next="2">
                                    Continue Setup <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Configuration -->
                        <div id="step-2" class="setup-step p-4 p-md-5 d-none">
                            <div class="text-center mb-5">
                                <h4 class="fw-bold">Enter Your Credentials</h4>
                                <p class="text-muted">Enter the details for your selected provider.</p>
                            </div>

                            <form id="sms-config-form">
                                <!-- Twilio -->
                                <template x-if="gateway === 'twilio'">
                                    <div class="gateway-fields">
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small">Account SID</label>
                                            <input type="text" id="wizard_twilio_sid" class="form-control"
                                                placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxx"
                                                value="{{ $settings['twilio_sid'] ?? '' }}">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small">Auth Token</label>
                                            <input type="password" id="wizard_twilio_token" class="form-control"
                                                placeholder="Enter Token" value="{{ $settings['twilio_token'] ?? '' }}">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small">From Number</label>
                                            <input type="text" id="wizard_twilio_from" class="form-control"
                                                placeholder="+1234567890" value="{{ $settings['twilio_from'] ?? '' }}">
                                        </div>
                                    </div>
                                </template>

                                <!-- Vonage -->
                                <template x-if="gateway === 'vonage'">
                                    <div class="gateway-fields">
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small">API Key</label>
                                            <input type="text" id="wizard_vonage_api_key" class="form-control"
                                                value="{{ $settings['vonage_api_key'] ?? '' }}">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small">API Secret</label>
                                            <input type="password" id="wizard_vonage_api_secret" class="form-control"
                                                value="{{ $settings['vonage_api_secret'] ?? '' }}">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small">From Name/Number</label>
                                            <input type="text" id="wizard_vonage_from" class="form-control"
                                                value="{{ $settings['vonage_from'] ?? '' }}">
                                        </div>
                                    </div>
                                </template>

                                <!-- Bird -->
                                <template x-if="gateway === 'messagebird'">
                                    <div class="gateway-fields">
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small">Access Key</label>
                                            <input type="password" id="wizard_messagebird_access_key" class="form-control"
                                                value="{{ $settings['messagebird_access_key'] ?? '' }}">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small">Originator</label>
                                            <input type="text" id="wizard_messagebird_originator" class="form-control"
                                                value="{{ $settings['messagebird_originator'] ?? '' }}">
                                        </div>
                                    </div>
                                </template>

                                <!-- Custom -->
                                <template x-if="gateway === 'custom'">
                                    <div class="gateway-fields">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Endpoint URL</label>
                                            <input type="url" id="wizard_custom_sms_url" class="form-control"
                                                value="{{ $settings['custom_sms_url'] ?? '' }}"
                                                placeholder="https://api.provider.com/sms">
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">HTTP Method</label>
                                                <select id="wizard_custom_sms_method" class="form-select">
                                                    <option value="POST" {{ ($settings['custom_sms_method'] ?? 'POST') === 'POST' ? 'selected' : '' }}>POST</option>
                                                    <option value="GET" {{ ($settings['custom_sms_method'] ?? '') === 'GET' ? 'selected' : '' }}>GET</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Phone Key</label>
                                                <input type="text" id="wizard_custom_sms_to_key" class="form-control"
                                                    value="{{ $settings['custom_sms_to_key'] ?? 'to' }}">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Headers (JSON)</label>
                                            <textarea id="wizard_custom_sms_headers" class="form-control form-control-sm"
                                                rows="2"
                                                placeholder='{"Authorization": "Bearer ..."}'>{{ $settings['custom_sms_headers'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </template>

                                <div class="d-flex justify-content-between mt-5">
                                    <button type="button" class="btn btn-light px-4 prev-step" data-prev="1">Back</button>
                                    <button type="button" class="btn btn-primary btn-lg px-5 shadow-sm next-step"
                                        data-next="3">
                                        Next: Verify & Test
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Step 3: Test & Save -->
                        <div id="step-3" class="setup-step p-4 p-md-5 d-none">
                            <div class="text-center mb-5">
                                <div class="bg-success-subtle text-success p-4 rounded-circle d-inline-block mb-4">
                                    <i class="fas fa-paper-plane fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">Verify Your Setup</h4>
                                <p class="text-muted">Send a test SMS to ensure everything is working correctly.</p>
                            </div>

                            <div class="mb-5 mx-auto" style="max-width: 400px;">
                                <label class="form-label fw-bold">Your Phone Number</label>
                                <div class="input-group">
                                    <input type="text" id="test_phone" class="form-control form-control-lg"
                                        placeholder="+1234567890">
                                    <button type="button" class="btn btn-success px-4" id="btn-test-sms">
                                        Send Test
                                    </button>
                                </div>
                                <div id="test-result" class="mt-3 small"></div>
                            </div>

                            <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                                <button type="button" class="btn btn-light px-4 prev-step" data-prev="2">Back</button>
                                <button type="button" class="btn btn-primary btn-lg px-5 shadow-sm" id="btn-save-wizard">
                                    Complete Setup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const steps = document.querySelectorAll('.setup-step');
                const progressBar = document.getElementById('setup-progress');
                const progressText = document.getElementById('progress-text');

                // Navigation
                document.querySelectorAll('.next-step').forEach(btn => {
                    btn.addEventListener('click', function () { showStep(this.getAttribute('data-next')); });
                });
                document.querySelectorAll('.prev-step').forEach(btn => {
                    btn.addEventListener('click', function () { showStep(this.getAttribute('data-prev')); });
                });

                function showStep(stepNum) {
                    steps.forEach(s => s.classList.add('d-none'));
                    document.getElementById('step-' + stepNum).classList.remove('d-none');
                    progressBar.style.width = (stepNum / 3) * 100 + '%';
                    progressText.innerText = 'Step ' + stepNum + ' of 3';
                }

                function getGatewayPayload() {
                    const gatewayInput = document.querySelector('input[name="wizard_gateway"]:checked');
                    const gateway = gatewayInput ? gatewayInput.value : 'twilio';
                    const phone = document.getElementById('test_phone').value;
                    let payload = { phone: phone, gateway: gateway };

                    if (gateway === 'twilio') {
                        payload.twilio_sid = document.getElementById('wizard_twilio_sid')?.value;
                        payload.twilio_token = document.getElementById('wizard_twilio_token')?.value;
                        payload.twilio_from = document.getElementById('wizard_twilio_from')?.value;
                    } else if (gateway === 'vonage') {
                        payload.vonage_api_key = document.getElementById('wizard_vonage_api_key')?.value;
                        payload.vonage_api_secret = document.getElementById('wizard_vonage_api_secret')?.value;
                        payload.vonage_from = document.getElementById('wizard_vonage_from')?.value;
                    } else if (gateway === 'messagebird') {
                        payload.messagebird_access_key = document.getElementById('wizard_messagebird_access_key')?.value;
                        payload.messagebird_originator = document.getElementById('wizard_messagebird_originator')?.value;
                    } else if (gateway === 'custom') {
                        payload.custom_sms_url = document.getElementById('wizard_custom_sms_url')?.value;
                        payload.custom_sms_method = document.getElementById('wizard_custom_sms_method')?.value;
                        payload.custom_sms_headers = document.getElementById('wizard_custom_sms_headers')?.value;
                        payload.custom_sms_to_key = document.getElementById('wizard_custom_sms_to_key')?.value;
                        payload.custom_sms_message_key = 'message';
                    }
                    return payload;
                }

                // Test SMS
                document.getElementById('btn-test-sms').addEventListener('click', async function () {
                    const btn = this;
                    const resultDiv = document.getElementById('test-result');
                    const payload = getGatewayPayload();

                    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

                    try {
                        const res = await fetch('{{ route('admin.settings.sms-setup.test') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify(payload)
                        });
                        const data = await res.json();
                        resultDiv.className = 'mt-3 small ' + (data.success ? 'text-success fw-bold' : 'text-danger fw-bold');
                        resultDiv.innerText = data.message;
                    } catch (e) {
                        resultDiv.innerText = 'Error checking connection.';
                    } finally { btn.disabled = false; btn.innerText = 'Send Test'; }
                });

                // Save All
                document.getElementById('btn-save-wizard').addEventListener('click', async function () {
                    const payload = getGatewayPayload();
                    payload._method = 'PUT';
                    payload.enable_sms = 1;
                    payload.sms_gateway = payload.gateway;

                    const btn = this; btn.disabled = true;
                    try {
                        const res = await fetch('{{ route('admin.salon-settings.update') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify(payload)
                        });
                        if (res.ok) {
                            Swal.fire({ icon: 'success', title: 'Saved!', text: 'Your SMS settings have been updated.' })
                                .then(() => window.location.href = '{{ route('admin.salon-settings.index', ['salon_slug' => $salon->slug]) }}');
                        } else { alert('Error saving settings.'); }
                    } catch (e) { alert('System error.'); }
                    finally { btn.disabled = false; }
                });
            });
        </script>
    @endpush
@endsection