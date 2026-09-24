@push('styles')
    <style>
        .mail-settings-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .mail-settings-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .mail-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            background: linear-gradient(to right, rgba(249, 250, 251, 0.5), rgba(255, 255, 255, 0));
            border-radius: 12px 12px 0 0;
        }

        .mail-card-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            font-size: 1.1rem;
        }

        .mail-card-title {
            margin: 0;
            font-weight: 600;
            color: #1f2937;
            font-size: 1rem;
        }

        .mail-card-body {
            padding: 24px;
        }

        /* Test Connection Card */
        .test-connection-card {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            border: none;
        }

        .test-connection-card .mail-card-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: transparent;
        }

        .test-connection-card .mail-card-title {
            color: white;
        }

        .test-connection-card .mail-card-icon {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .test-input {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: white !important;
        }

        .test-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .test-input:focus {
            background: rgba(255, 255, 255, 0.15) !important;
            border-color: rgba(255, 255, 255, 0.5) !important;
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
        }

        /* Tips Card */
        .tips-card {
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
        }

        .tips-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .tips-list li {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .tips-list li i {
            margin-top: 3px;
            margin-right: 10px;
            color: #0d6efd;
            flex-shrink: 0;
        }

        .provider-badge {
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 12px;
            background: #e9ecef;
            color: #495057;
            font-weight: 600;
            margin-right: 6px;
        }
    </style>
@endpush

<div class="row g-4">
    <!-- Main Configuration Column -->
    <div class="col-lg-8">
        <!-- Server Details -->
        <div class="mail-settings-card mb-4">
            <div class="mail-card-header">
                <div class="mail-card-icon"><i class="fas fa-server"></i></div>
                <h6 class="mail-card-title">SMTP Server Configuration</h6>
            </div>
            <div class="mail-card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-microchip text-primary me-2"></i>Mail Driver
                        </label>
                        <select name="mail_driver" class="form-select @error('mail_driver') is-invalid @enderror"
                            id="mail_driver">
                            <option value="smtp" {{ old('mail_driver', $settings['mail_driver'] ?? ($salon->mail_driver ?? 'smtp')) == 'smtp' ? 'selected' : '' }}>SMTP (Recommended)</option>
                            <option value="sendmail" {{ old('mail_driver', $settings['mail_driver'] ?? ($salon->mail_driver ?? 'smtp')) == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="log" {{ old('mail_driver', $settings['mail_driver'] ?? ($salon->mail_driver ?? 'smtp')) == 'log' ? 'selected' : '' }}>Log File (Testing)</option>
                        </select>
                    </div>

                    <div class="col-md-6" id="host_field">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-network-wired text-info me-2"></i>SMTP Host
                        </label>
                        <input type="text" name="mail_host"
                            class="form-control @error('mail_host') is-invalid @enderror"
                            value="{{ old('mail_host', $settings['mail_host'] ?? ($salon->mail_host ?? '')) }}"
                            placeholder="e.g., smtp.gmail.com">
                    </div>

                    <div class="col-md-6" id="port_field">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-plug text-warning me-2"></i>Port
                        </label>
                        <input type="number" name="mail_port"
                            class="form-control @error('mail_port') is-invalid @enderror"
                            value="{{ old('mail_port', $settings['mail_port'] ?? ($salon->mail_port ?? 587)) }}"
                            placeholder="587">
                    </div>

                    <div class="col-md-6" id="encryption_field">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-shield-alt text-success me-2"></i>Encryption
                        </label>
                        <select name="mail_encryption"
                            class="form-select @error('mail_encryption') is-invalid @enderror">
                            <option value="tls" {{ old('mail_encryption', $settings['mail_encryption'] ?? ($salon->mail_encryption ?? 'tls')) == 'tls' ? 'selected' : '' }}>TLS (Recommended)
                            </option>
                            <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption'] ?? ($salon->mail_encryption ?? 'tls')) == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="" {{ old('mail_encryption', $settings['mail_encryption'] ?? ($salon->mail_encryption ?? 'tls')) == '' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Authentication & Identity -->
        <div class="mail-settings-card">
            <div class="mail-card-header">
                <div class="mail-card-icon"><i class="fas fa-shield-alt"></i></div>
                <h6 class="mail-card-title">Authentication & Identity</h6>
            </div>
            <div class="mail-card-body">
                <div class="row g-4">
                    <div class="col-md-6" id="username_field">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-user-circle text-primary me-2"></i>Username
                        </label>
                        <input type="text" name="mail_username"
                            class="form-control @error('mail_username') is-invalid @enderror"
                            value="{{ old('mail_username', $settings['mail_username'] ?? ($salon->mail_username ?? '')) }}"
                            placeholder="email@provider.com">
                    </div>

                    <div class="col-md-6" id="password_field">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-key text-danger me-2"></i>Password
                            <span class="text-muted fw-normal ms-1 text-lowercase" style="font-size: 0.7em;">(leave
                                empty to keep current)</span>
                        </label>
                        <input type="password" name="mail_password"
                            class="form-control @error('mail_password') is-invalid @enderror" placeholder="••••••••••••"
                            autocomplete="new-password">
                    </div>

                    <div class="col-12">
                        <hr class="my-0 text-muted opacity-25">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-id-card text-info me-2"></i>From Name
                        </label>
                        <input type="text" name="mail_from_name"
                            class="form-control @error('mail_from_name') is-invalid @enderror"
                            value="{{ old('mail_from_name', $settings['mail_from_name'] ?? ($salon->mail_from_name ?? '')) }}"
                            placeholder="My Salon Name">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-at text-success me-2"></i>From Address
                        </label>
                        <input type="email" name="mail_from_address"
                            class="form-control @error('mail_from_address') is-invalid @enderror"
                            value="{{ old('mail_from_address', $settings['mail_from_address'] ?? ($salon->mail_from_address ?? '')) }}"
                            placeholder="noreply@mysalon.com">
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Templates -->
        <div class="mail-settings-card mt-4">
            <div class="mail-card-header">
                <div class="mail-card-icon"><i class="fas fa-envelope-open-text"></i></div>
                <h6 class="mail-card-title">Email Templates</h6>
            </div>
            <div class="mail-card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Customize Templates</h6>
                        <p class="text-muted small mb-0">Personalize the automated emails sent to your customers (e.g.,
                            Booking Confirmations, Reminders).</p>
                    </div>
                    <a href="{{ route('admin.saas.settings.email-templates') }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-edit me-2"></i> Manage Templates
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Column -->
    <div class="col-lg-4">
        <!-- Test Connection (Sticky) -->
        <div class="sticky-top" style="top: 100px; z-index: 1;">

            <!-- Test Card -->
            <div class="mail-settings-card test-connection-card mb-4">
                <div class="mail-card-header">
                    <div class="mail-card-icon"><i class="fas fa-paper-plane"></i></div>
                    <h6 class="mail-card-title">Test Connection</h6>
                </div>
                <div class="mail-card-body">
                    <p class="small text-white-50 mb-3">Send a test email to verify your settings are correct before
                        saving.</p>

                    <div class="mb-3">
                        <label class="form-label text-white-50 small text-uppercase fw-bold">Recipient Email</label>
                        <input type="email" id="testEmailInput" class="form-control test-input"
                            placeholder="you@example.com" onkeydown="return event.key != 'Enter';">
                    </div>

                    <button type="button" class="btn btn-primary w-100 fw-bold shadow-sm" id="sendTestMailBtn">
                        <i class="fas fa-bolt me-2"></i> Send Test Email
                    </button>

                    <div id="testEmailFeedback" class="mt-3 small rounded p-2" style="display:none;"></div>
                </div>
            </div>

            <!-- Configuration Tips -->
            <div class="mail-settings-card tips-card">
                <div class="mail-card-header bg-transparent border-bottom-0 pb-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        <h6 class="mail-card-title text-secondary" style="font-size: 0.9rem;">Quick Configuration Tips
                        </h6>
                    </div>
                </div>
                <div class="mail-card-body pt-2">
                    <ul class="tips-list">
                        <li>
                            <i class="fab fa-google"></i>
                            <div>
                                <strong class="d-block text-dark">Gmail / G-Suite</strong>
                                <span>Host: smtp.gmail.com</span><br>
                                <span>Port: 587 (TLS)</span>
                            </div>
                        </li>
                        <li>
                            <i class="fab fa-microsoft"></i>
                            <div>
                                <strong class="d-block text-dark">Outlook / Office 365</strong>
                                <span>Host: smtp.office365.com</span><br>
                                <span>Port: 587 (TLS)</span>
                            </div>
                        </li>
                    </ul>
                    <div class="alert alert-info py-2 px-3 small border-0 bg-opacity-10 mb-0 mt-3">
                        <i class="fas fa-info-circle me-1"></i>
                        For Gmail, you may need an <strong>App Password</strong> if 2FA is enabled.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const driverSelect = document.getElementById('mail_driver');
            const smtpFields = ['host_field', 'port_field', 'username_field', 'password_field', 'encryption_field'];

            function toggleSmtpFields() {
                const isSmtp = driverSelect.value === 'smtp';
                smtpFields.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.style.display = isSmtp ? 'block' : 'none';
                });
            }

            if (driverSelect) {
                driverSelect.addEventListener('change', toggleSmtpFields);
                toggleSmtpFields(); // Init
            }

            // Test Email Logic
            const testBtn = document.getElementById('sendTestMailBtn');
            if (testBtn) {
                testBtn.addEventListener('click', function () {
                    const emailInput = document.getElementById('testEmailInput');
                    const feedback = document.getElementById('testEmailFeedback');
                    const email = emailInput.value;

                    feedback.style.display = 'block';
                    if (!email) {
                        feedback.className = 'bg-danger bg-opacity-25 text-white';
                        feedback.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i> Please enter an email address.';
                        return;
                    }

                    const originalHtml = testBtn.innerHTML;
                    testBtn.disabled = true;
                    testBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
                    feedback.className = '';
                    feedback.innerHTML = '';

                    fetch('{{ route("admin.saas.settings.mail.test") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ test_email: email })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                feedback.className = 'bg-success bg-opacity-25 text-white';
                                feedback.innerHTML = '<i class="fas fa-check-circle me-1"></i> ' + (data.message || 'Sent successfully!');
                            } else {
                                feedback.className = 'bg-danger bg-opacity-25 text-white';
                                feedback.innerHTML = '<i class="fas fa-times-circle me-1"></i> ' + (data.message || 'Failed to send.');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            feedback.className = 'bg-danger bg-opacity-25 text-white';
                            feedback.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Error occurred.';
                        })
                        .finally(() => {
                            testBtn.disabled = false;
                            testBtn.innerHTML = originalHtml;
                        });
                });
            }
        });
    </script>
@endpush