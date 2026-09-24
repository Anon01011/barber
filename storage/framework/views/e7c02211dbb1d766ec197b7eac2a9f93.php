<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800 fw-bold">WhatsApp Setup Wizard</h1>
                <p class="text-muted mb-0">Enable WhatsApp notifications to improve customer engagement and reduce no-shows.
                </p>
            </div>
            <a href="<?php echo e(route('admin.salon-settings.index', ['salon_slug' => $salon->slug])); ?>"
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
                                <span class="small fw-bold text-success text-uppercase">WhatsApp Configuration</span>
                                <span class="small fw-bold text-success" id="progress-text">Step 1 of 3</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                    id="setup-progress" role="progressbar" style="width: 33%"></div>
                            </div>
                        </div>

                        <!-- Step 1: Gateway Selection -->
                        <div id="step-1" class="setup-step p-4 p-md-5"
                            x-data="{ gateway: '<?php echo e($settings['whatsapp_gateway'] ?? 'twilio'); ?>' }">
                            <div class="text-center mb-5">
                                <div class="bg-success-subtle text-success p-4 rounded-circle d-inline-block mb-4">
                                    <i class="fab fa-whatsapp fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">Select Your WhatsApp Provider</h4>
                                <p class="text-muted mx-auto" style="max-width: 500px;">
                                    Connect your WhatsApp gateway to start sending notifications.
                                </p>
                            </div>

                            <div class="row g-3 justify-content-center mb-5">
                                <div class="col-md-5">
                                    <label
                                        class="gateway-option p-4 border rounded text-center d-block cursor-pointer transition-all"
                                        :class="gateway === 'twilio' ? 'border-success bg-success-subtle shadow-sm' : 'hover-bg-light'">
                                        <input type="radio" name="wizard_gateway" value="twilio" class="d-none"
                                            x-model="gateway">
                                        <i class="fas fa-sms d-block mb-3 fa-2x text-primary"></i>
                                        <span class="fw-bold d-block">Twilio WhatsApp</span>
                                        <small class="text-muted">Official Meta API Provider</small>
                                    </label>
                                </div>
                                <div class="col-md-5">
                                    <label
                                        class="gateway-option p-4 border rounded text-center d-block cursor-pointer transition-all"
                                        :class="gateway === 'custom' ? 'border-success bg-success-subtle shadow-sm' : 'hover-bg-light'">
                                        <input type="radio" name="wizard_gateway" value="custom" class="d-none"
                                            x-model="gateway">
                                        <i class="fas fa-code d-block mb-3 fa-2x text-secondary"></i>
                                        <span class="fw-bold d-block">Custom HTTP Gateway</span>
                                        <small class="text-muted">Any 3rd Party API</small>
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-success btn-lg px-5 shadow-sm next-step" data-next="2">
                                    Continue Setup <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Configuration -->
                        <div id="step-2" class="setup-step p-4 p-md-5 d-none">
                            <div class="text-center mb-5">
                                <h4 class="fw-bold">Enter Your Credentials</h4>
                                <p class="text-muted">Provide the API details from your provider dashboard.</p>
                            </div>

                            <form id="whatsapp-config-form">
                                <!-- Twilio -->
                                <template x-if="gateway === 'twilio'">
                                    <div class="gateway-fields p-3 bg-light rounded border mb-4">
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small">Account SID</label>
                                            <input type="text" id="wizard_whatsapp_twilio_sid" class="form-control"
                                                placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxx"
                                                value="<?php echo e($settings['whatsapp_twilio_sid'] ?? ''); ?>">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small">Auth Token</label>
                                            <input type="password" id="wizard_whatsapp_twilio_token" class="form-control"
                                                placeholder="Enter Token"
                                                value="<?php echo e($settings['whatsapp_twilio_token'] ?? ''); ?>">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small">Twilio WhatsApp Number (From)</label>
                                            <input type="text" id="wizard_whatsapp_twilio_from" class="form-control"
                                                placeholder="+1234567890 (or 'whatsapp:+1234567890')"
                                                value="<?php echo e($settings['whatsapp_twilio_from'] ?? ''); ?>">
                                            <small class="text-muted">Include the country code. Twilio usually requires the
                                                "whatsapp:" prefix.</small>
                                        </div>
                                    </div>
                                </template>

                                <!-- Custom -->
                                <template x-if="gateway === 'custom'">
                                    <div class="gateway-fields p-3 bg-light rounded border mb-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">API Endpoint URL</label>
                                            <input type="url" id="wizard_whatsapp_custom_url" class="form-control"
                                                value="<?php echo e($settings['whatsapp_custom_url'] ?? ''); ?>"
                                                placeholder="https://api.whatsapp-provider.com/send">
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">HTTP Method</label>
                                                <select id="wizard_whatsapp_custom_method" class="form-select">
                                                    <option value="POST" <?php echo e(($settings['whatsapp_custom_method'] ?? 'POST') === 'POST' ? 'selected' : ''); ?>>POST</option>
                                                    <option value="GET" <?php echo e(($settings['whatsapp_custom_method'] ?? '') === 'GET' ? 'selected' : ''); ?>>GET</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Recipient Key (e.g., 'to',
                                                    'phone')</label>
                                                <input type="text" id="wizard_whatsapp_custom_to_key" class="form-control"
                                                    value="<?php echo e($settings['whatsapp_custom_to_key'] ?? 'to'); ?>">
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Message Key (e.g., 'message',
                                                    'text')</label>
                                                <input type="text" id="wizard_whatsapp_custom_message_key"
                                                    class="form-control"
                                                    value="<?php echo e($settings['whatsapp_custom_message_key'] ?? 'message'); ?>">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Custom Headers (JSON)</label>
                                            <textarea id="wizard_whatsapp_custom_headers"
                                                class="form-control form-control-sm" rows="2"
                                                placeholder='{"Authorization": "Bearer ...", "Content-Type": "application/json"}'><?php echo e($settings['whatsapp_custom_headers'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Additional Payload (JSON)</label>
                                            <textarea id="wizard_whatsapp_custom_payload"
                                                class="form-control form-control-sm" rows="2"
                                                placeholder='{"instance_id": "123", "access_token": "abc"}'><?php echo e($settings['whatsapp_custom_payload'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </template>

                                <div class="d-flex justify-content-between mt-5">
                                    <button type="button" class="btn btn-light px-4 prev-step" data-prev="1">Back</button>
                                    <button type="button" class="btn btn-success btn-lg px-5 shadow-sm next-step"
                                        data-next="3">
                                        Next: Verify & Test
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Step 3: Test & Save -->
                        <div id="step-3" class="setup-step p-4 p-md-5 d-none">
                            <div class="text-center mb-5">
                                <div class="bg-primary-subtle text-primary p-4 rounded-circle d-inline-block mb-4">
                                    <i class="fas fa-paper-plane fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">Verify Your Setup</h4>
                                <p class="text-muted">Send a test WhatsApp message to your own number.</p>
                            </div>

                            <div class="mb-5 mx-auto" style="max-width: 400px;">
                                <label class="form-label fw-bold">Your WhatsApp Number</label>
                                <div class="input-group">
                                    <input type="text" id="test_phone" class="form-control form-control-lg"
                                        placeholder="+1234567890">
                                    <button type="button" class="btn btn-primary px-4" id="btn-test-whatsapp">
                                        Send Test
                                    </button>
                                </div>
                                <div id="test-result" class="mt-3 small"></div>
                            </div>

                            <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                                <button type="button" class="btn btn-light px-4 prev-step" data-prev="2">Back</button>
                                <button type="button" class="btn btn-success btn-lg px-5 shadow-sm" id="btn-save-wizard">
                                    Complete Setup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
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
                        payload.whatsapp_twilio_sid = document.getElementById('wizard_whatsapp_twilio_sid')?.value;
                        payload.whatsapp_twilio_token = document.getElementById('wizard_whatsapp_twilio_token')?.value;
                        payload.whatsapp_twilio_from = document.getElementById('wizard_whatsapp_twilio_from')?.value;
                    } else if (gateway === 'custom') {
                        payload.whatsapp_custom_url = document.getElementById('wizard_whatsapp_custom_url')?.value;
                        payload.whatsapp_custom_method = document.getElementById('wizard_whatsapp_custom_method')?.value;
                        payload.whatsapp_custom_headers = document.getElementById('wizard_whatsapp_custom_headers')?.value;
                        payload.whatsapp_custom_to_key = document.getElementById('wizard_whatsapp_custom_to_key')?.value;
                        payload.whatsapp_custom_message_key = document.getElementById('wizard_whatsapp_custom_message_key')?.value;
                        payload.whatsapp_custom_payload = document.getElementById('wizard_whatsapp_custom_payload')?.value;
                    }
                    return payload;
                }

                // Test WhatsApp
                document.getElementById('btn-test-whatsapp').addEventListener('click', async function () {
                    const btn = this;
                    const resultDiv = document.getElementById('test-result');
                    const payload = getGatewayPayload();

                    if (!payload.phone) {
                        Swal.fire('Error', 'Please enter a test phone number.', 'error');
                        return;
                    }

                    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

                    try {
                        const res = await fetch('<?php echo e(route('admin.settings.whatsapp-setup.test')); ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
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
                    payload.enable_whatsapp = 1;
                    payload.whatsapp_gateway = payload.gateway;

                    const btn = this; btn.disabled = true;
                    try {
                        const res = await fetch('<?php echo e(route('admin.salon-settings.update')); ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
                            body: JSON.stringify(payload)
                        });
                        if (res.ok) {
                            Swal.fire({ icon: 'success', title: 'Saved!', text: 'Your WhatsApp settings have been updated.' })
                                .then(() => window.location.href = '<?php echo e(route('admin.salon-settings.index', ['salon_slug' => $salon->slug])); ?>');
                        } else { alert('Error saving settings.'); }
                    } catch (e) { alert('System error.'); }
                    finally { btn.disabled = false; }
                });
            });
        </script>

        <style>
            .gateway-option:hover {
                border-color: #198754 !important;
                background-color: #f8f9fa;
            }

            .transition-all {
                transition: all 0.3s ease;
            }

            .cursor-pointer {
                cursor: pointer;
            }
        </style>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\settings\whatsapp_setup.blade.php ENDPATH**/ ?>