<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <!-- Header -->
            <div class="modal-header border-0 position-relative py-3" style="background: linear-gradient(135deg, #0b0f17 0%, #1a2232 100%); border-bottom: 1px solid rgba(197, 168, 128, 0.25) !important;">
                <div class="d-flex align-items-center">
                    <div class="p-2 rounded-2 me-2 d-flex align-items-center justify-content-center" style="background: rgba(197, 168, 128, 0.15); width: 34px; height: 34px;">
                        <i class="fas fa-user-plus" style="color: #c5a880; font-size: 14px;"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="addCustomerModalLabel" style="font-size: 15px; letter-spacing: 0.2px;">Add New Customer</h6>
                        <small style="color: #94a3b8; font-size: 11px;">Create a new client profile and configure access</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-3" style="max-height: 70vh; overflow-y: auto;">
                <form id="addCustomerForm" class="needs-validation" novalidate>
                    <!-- Basic Information Section -->
                    <div class="card border-0 bg-light-subtle mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-2 me-2">
                                    <i class="fas fa-id-card text-primary"></i>
                                </div>
                                <h6 class="mb-0 fw-semibold text-dark">Basic Information</h6>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-user me-1" style="color:#c5a880;"></i>First Name <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="first_name" placeholder="John"
                                        required>
                                    <div class="invalid-feedback">Please enter first name</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-user me-1" style="color:#c5a880;"></i>Last Name
                                    </label>
                                    <input type="text" class="form-control" name="last_name" placeholder="Doe">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-venus-mars me-1" style="color:#c5a880;"></i>Gender
                                    </label>
                                    <select class="form-select" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="card border-0 bg-light-subtle mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 p-2 rounded-2 me-2">
                                    <i class="fas fa-phone text-success"></i>
                                </div>
                                <h6 class="mb-0 fw-semibold text-dark">Contact Information</h6>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-phone me-1" style="color:#c5a880;"></i>Mobile Number <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control" name="phone" id="modal_phone"
                                        placeholder="Enter Mobile Number" required>
                                    <input type="hidden" name="country_code" id="modal_country_code" value="+974">
                                    <div class="invalid-feedback">Please enter a valid phone number</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-phone-alt me-1" style="color:#c5a880;"></i>Secondary Number
                                    </label>
                                    <input type="tel" class="form-control" name="secondary_number"
                                        id="modal_secondary_number" placeholder="Alternate Contact">
                                    <input type="hidden" name="secondary_country_code" id="modal_secondary_country_code"
                                        value="+974">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" id="emailLabel">
                                        <i class="fas fa-envelope me-1" style="color:#c5a880;"></i>Email Address
                                    </label>
                                    <input type="email" class="form-control" name="email" id="emailInput"
                                        placeholder="customer@example.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-comments me-1" style="color:#c5a880;"></i>Preferred Contact <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" name="preferred_contact" id="preferredContactSelect"
                                        required>
                                        <option value="">Select preferred method</option>
                                        <option value="phone" selected>Phone Call</option>
                                        <option value="email">Email</option>
                                        <option value="sms">SMS / Text</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a preferred contact method</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-map-marker-alt me-1" style="color:#c5a880;"></i>Location
                                    </label>
                                    <input type="text" class="form-control" name="location" placeholder="City, State">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Details Section -->
                    <div class="card border-0 bg-light-subtle mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info bg-opacity-10 p-2 rounded-2 me-2">
                                    <i class="fas fa-birthday-cake text-info"></i>
                                </div>
                                <h6 class="mb-0 fw-semibold text-dark">Personal Details</h6>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-birthday-cake me-1" style="color:#c5a880;"></i>Date of Birth
                                    </label>
                                    <input type="date" class="form-control" name="dob">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-heart me-1" style="color:#c5a880;"></i>Anniversary
                                    </label>
                                    <input type="date" class="form-control" name="anniversary">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-bullhorn me-1" style="color:#c5a880;"></i>Source
                                    </label>
                                    <select class="form-select" name="source">
                                        <option value="">Select Source</option>
                                        <option value="walk-in">Walk-in</option>
                                        <option value="referral">Referral</option>
                                        <option value="online">Online</option>
                                        <option value="social-media">Social Media</option>
                                        <option value="advertisement">Advertisement</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SMS/WhatsApp Preferences -->
                    <div class="card border-0 bg-light-subtle mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 p-2 rounded-2 me-2">
                                    <i class="fas fa-sms text-warning"></i>
                                </div>
                                <h6 class="mb-0 fw-semibold text-dark">SMS/WhatsApp Preferences</h6>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="send_promotional_sms"
                                            value="1" id="sendPromotional" checked>
                                        <label class="form-check-label" for="sendPromotional">
                                            <i class="fas fa-bullhorn me-1"></i> Send Promotional SMS/WhatsApp
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="send_transactional_sms"
                                            value="1" id="sendTransactional" checked>
                                        <label class="form-check-label" for="sendTransactional">
                                            <i class="fas fa-receipt me-1"></i> Send Transactional SMS/WhatsApp
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="card border-0 bg-light-subtle mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-secondary bg-opacity-10 p-2 rounded-2 me-2">
                                    <i class="fas fa-info-circle text-secondary"></i>
                                </div>
                                <h6 class="mb-0 fw-semibold text-dark">Additional Information</h6>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-map-marker-alt me-1 text-secondary"></i>Address
                                    </label>
                                    <textarea class="form-control" name="address" rows="2"
                                        placeholder="Enter customer address (optional)"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-sticky-note me-1 text-secondary"></i>Customer Notes
                                    </label>
                                    <textarea class="form-control" name="notes" rows="2"
                                        placeholder="Add any special notes or preferences..."></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-notes-medical me-1 text-secondary"></i>Medical Notes
                                    </label>
                                    <textarea class="form-control" name="medical_notes" rows="2"
                                        placeholder="Any allergies, medical conditions, or special requirements..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Portal Login Access Section -->
                    <div class="card border-0 mb-3" style="border: 1px solid #e8dccb !important; border-radius: 12px; background: linear-gradient(135deg, #ffffff 0%, #faf8f5 100%); box-shadow: 0 4px 12px rgba(197, 168, 128, 0.08);">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-2 rounded-2 d-flex align-items-center justify-content-center" style="background: rgba(197, 168, 128, 0.18); width: 36px; height: 36px;">
                                        <i class="fas fa-shield-alt text-warning" style="color: #c5a880 !important;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold text-dark" style="font-size: 14px;">Client Portal Login Access</h6>
                                        <small class="text-muted">Allow this customer to sign in to book services, view history, and redeem points</small>
                                    </div>
                                </div>
                                <div class="form-check form-switch ms-2 mb-0">
                                    <input class="form-check-input" type="checkbox" name="grant_login_access"
                                        value="1" id="grantLoginToggle_ADD" style="width: 40px; height: 20px; cursor: pointer;" onchange="toggleLoginAccessSection('ADD')">
                                </div>
                            </div>

                            <div id="loginAccessFields_ADD" style="display:none;" class="mt-3 pt-3 border-top">
                                <div class="p-2 px-3 rounded-2 mb-3 d-flex align-items-center justify-content-between" style="background: rgba(197, 168, 128, 0.1); border: 1px dashed rgba(197, 168, 128, 0.4);">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-info-circle" style="color: #c5a880;"></i>
                                        <small class="text-muted">Username will be the customer's <strong>Email Address</strong>. Credentials will be sent automatically.</small>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-uppercase" style="font-size: 11px; letter-spacing: 0.5px; color: #64748b;">
                                            <i class="fas fa-lock me-1" style="color: #c5a880;"></i>Password Setup Mode
                                        </label>
                                        <div class="d-flex gap-4 mb-1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="password_mode" id="auto_pass_ADD" value="auto" checked onchange="togglePasswordMode('ADD')">
                                                <label class="form-check-label small fw-semibold text-dark" for="auto_pass_ADD">
                                                    <i class="fas fa-magic me-1 text-success"></i>Auto-generate &amp; send
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="password_mode" id="manual_pass_ADD" value="manual" onchange="togglePasswordMode('ADD')">
                                                <label class="form-check-label small fw-semibold text-dark" for="manual_pass_ADD">
                                                    <i class="fas fa-pen me-1" style="color: #c5a880;"></i>Set manually
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="manualPassField_ADD" style="display:none;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label fw-semibold small m-0">
                                                <i class="fas fa-key me-1" style="color: #c5a880;"></i>Password (Min. 8 chars)
                                            </label>
                                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" style="color: #b38e5d; font-size: 0.75rem;" onclick="generateAddPassword()">
                                                <i class="fas fa-random me-1"></i>Generate
                                            </button>
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <input type="password" class="form-control" name="portal_password"
                                                id="portalPasswordInput_ADD" placeholder="Min. 8 characters" autocomplete="new-password">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePortalPassVis('ADD')">
                                                <i class="fas fa-eye" id="portalPassEye_ADD"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">
                                            <i class="fas fa-paper-plane me-1" style="color: #c5a880;"></i>Send Credentials Via
                                        </label>
                                        <select class="form-select form-select-sm" name="credential_notify">
                                            <option value="email" selected>Email only</option>
                                            <option value="sms">SMS only</option>
                                            <option value="both">Email and SMS</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 bg-light p-3">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Fields marked with <span class="text-danger">*</span> are required
                    </div>
                    <div class="d-flex gap-2">
                        <x-btn type="button" variant="secondary" size="sm" icon="fas fa-times" data-bs-dismiss="modal">
                            Cancel
                        </x-btn>
                        <x-btn type="submit" form="addCustomerForm" variant="primary" size="sm" icon="fas fa-user-plus">
                            Create Customer
                        </x-btn>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleLoginAccessSection(suffix) {
    var toggle = document.getElementById('grantLoginToggle_' + suffix);
    var fields = document.getElementById('loginAccessFields_' + suffix);
    if (toggle && fields) { fields.style.display = toggle.checked ? 'block' : 'none'; }
}
function togglePasswordMode(suffix) {
    var mode = document.querySelector('input[name="password_mode"]:checked') || document.querySelector('input[name="password_mode_' + suffix + '"]:checked');
    var manualField = document.getElementById('manualPassField_' + suffix);
    if (mode && manualField) { manualField.style.display = mode.value === 'manual' ? 'block' : 'none'; }
}
function togglePortalPassVis(suffix) {
    var input = document.getElementById('portalPasswordInput_' + suffix);
    var icon  = document.getElementById('portalPassEye_' + suffix);
    if (!input) return;
    if (input.type === 'password') { input.type = 'text'; if (icon) icon.className = 'fas fa-eye-slash'; }
    else { input.type = 'password'; if (icon) icon.className = 'fas fa-eye'; }
}
function generateAddPassword() {
    var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%&*';
    var pass = '';
    for (var i = 0; i < 12; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    var input = document.getElementById('portalPasswordInput_ADD');
    if (input) {
        input.value = pass;
        input.type = 'text';
        var icon = document.getElementById('portalPassEye_ADD');
        if (icon) icon.className = 'fas fa-eye-slash';
    }
}
</script>