@props(['customerId' => null])

<style>
    #editCustomerModal {
        --primary-color: var(--theme-accent, #c5a880);
        --primary-light: rgba(197, 168, 128, 0.12);
        --primary-dark: var(--theme-accent-hover, #b38e5d);
        --border-color: #e2e8f0;
        --text-color: #1e293b;
    }

    #editCustomerModal .modal-content {
        border-radius: 16px;
        border: none;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
    }

    #editCustomerModal .modal-header {
        background: linear-gradient(135deg, #0b0f17 0%, #1e2638 50%, #0f172a 100%);
        border-bottom: 1px solid rgba(197, 168, 128, 0.3) !important;
        padding: 1.25rem 1.75rem;
        position: relative;
    }

    #editCustomerModal .modal-title {
        font-weight: 700;
        color: #fff !important;
        font-size: 1.2rem;
        letter-spacing: -0.2px;
        margin: 0;
        line-height: 1.3;
    }

    #editCustomerModal .header-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(197, 168, 128, 0.15);
        border: 1px solid rgba(197, 168, 128, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(197, 168, 128, 0.15);
    }

    #editCustomerModal .nav-pills {
        background: #f1f5f9;
        padding: 4px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }

    #editCustomerModal .nav-pills .nav-link {
        color: #64748b;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        font-size: 0.85rem;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    #editCustomerModal .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #c5a880 0%, #b38e5d 100%);
        color: #fff;
        box-shadow: 0 4px 10px rgba(197, 168, 128, 0.35);
    }

    #editCustomerModal .nav-pills .nav-link:hover:not(.active) {
        background-color: rgba(255, 255, 255, 0.8);
        color: #0f172a;
    }

    #editCustomerModal .form-label {
        font-weight: 600;
        font-size: 0.8rem;
        color: #475569;
        margin-bottom: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    #editCustomerModal .form-control,
    #editCustomerModal .form-select {
        height: 40px;
        font-size: 0.875rem;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.45rem 0.85rem;
        color: var(--text-color);
        transition: all 0.15s ease-in-out;
        background-color: #fff;
    }

    #editCustomerModal textarea.form-control {
        height: auto;
        min-height: 80px;
    }

    #editCustomerModal .form-control:focus,
    #editCustomerModal .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(197, 168, 128, 0.22);
        outline: none;
    }

    #editCustomerModal .portal-card {
        border: 1px solid #e8dccb !important;
        border-radius: 12px;
        background: linear-gradient(135deg, #ffffff 0%, #faf8f5 100%);
        box-shadow: 0 4px 12px rgba(197, 168, 128, 0.08);
        transition: all 0.2s ease;
    }

    #editCustomerModal .portal-card.active-portal {
        border-color: #c5a880 !important;
        box-shadow: 0 6px 16px rgba(197, 168, 128, 0.18);
    }

    #editCustomerModal .selection-pill {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.65rem 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #fff;
    }

    #editCustomerModal .selection-pill:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    #editCustomerModal .selection-pill.active {
        border-color: var(--primary-color);
        background: rgba(197, 168, 128, 0.08);
    }

    #editCustomerModal .pass-strength-bar {
        height: 4px;
        border-radius: 2px;
        transition: all 0.3s ease;
        background: #e2e8f0;
    }

    #editCustomerModal .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    #editCustomerModal .btn-save-luxury {
        background: linear-gradient(135deg, #c5a880 0%, #b38e5d 100%);
        color: #fff;
        border: none;
        font-weight: 600;
        padding: 0.5rem 1.4rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(197, 168, 128, 0.35);
        transition: all 0.2s ease;
    }

    #editCustomerModal .btn-save-luxury:hover {
        background: linear-gradient(135deg, #b38e5d 0%, #9f7947 100%);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(197, 168, 128, 0.45);
    }
</style>

<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Luxury Header -->
            <div class="modal-header text-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon-box">
                        <i class="fas fa-user-edit fa-lg" style="color: #c5a880;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer Profile</h5>
                            <span class="badge" id="headerCustomerCode" style="background: rgba(197, 168, 128, 0.2); color: #e8dccb; font-size: 0.75rem; border: 1px solid rgba(197, 168, 128, 0.3);"></span>
                        </div>
                        <p class="m-0 text-white-50" style="font-size: 0.78rem;">Manage client details, contact information, and portal login credentials</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                {{-- Tabs Navigation --}}
                <ul class="nav nav-pills mb-4 d-flex justify-content-center gap-1" id="editCustomerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="basic-tab" data-bs-toggle="pill" data-bs-target="#basic-pane"
                            type="button" role="tab" aria-controls="basic-pane" aria-selected="true">
                            <i class="fas fa-user"></i> Profile & Basic
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="pill" data-bs-target="#contact-pane"
                            type="button" role="tab" aria-controls="contact-pane" aria-selected="false">
                            <i class="fas fa-address-book"></i> Contact & Address
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="portal-tab" data-bs-toggle="pill" data-bs-target="#portal-pane"
                            type="button" role="tab" aria-controls="portal-pane" aria-selected="false">
                            <i class="fas fa-key"></i> Portal & Security
                            <span class="badge rounded-pill bg-warning text-dark ms-1 d-none" id="portalTabBadge" style="font-size: 0.65rem;">Active</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="preferences-tab" data-bs-toggle="pill" data-bs-target="#preferences-pane"
                            type="button" role="tab" aria-controls="preferences-pane" aria-selected="false">
                            <i class="fas fa-sliders-h"></i> Preferences & Notes
                        </button>
                    </li>
                </ul>

                <form id="customerEditForm">
                    @csrf
                    @method('PUT')

                    <div class="tab-content" id="editCustomerTabsContent">
                        {{-- Tab 1: Profile & Basic Info --}}
                        <div class="tab-pane fade show active" id="basic-pane" role="tabpanel" aria-labelledby="basic-tab">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="editFirstName" class="form-label">
                                        <i class="fas fa-user text-muted"></i> First Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="editFirstName" name="first_name" required placeholder="e.g. Ketan">
                                </div>
                                <div class="col-md-6">
                                    <label for="editLastName" class="form-label">
                                        <i class="fas fa-user text-muted"></i> Last Name
                                    </label>
                                    <input type="text" class="form-control" id="editLastName" name="last_name" placeholder="e.g. Mhatre">
                                </div>
                                <div class="col-md-6">
                                    <label for="editCustomerId" class="form-label">
                                        <i class="fas fa-id-card text-muted"></i> Customer ID
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light font-monospace" id="editCustomerId" readonly disabled style="font-weight: 600; color: #475569;">
                                        <button class="btn btn-outline-secondary" type="button" title="Copy Customer ID" onclick="copyCustomerId()">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="editGender" class="form-label">
                                        <i class="fas fa-venus-mars text-muted"></i> Gender
                                    </label>
                                    <select class="form-select" id="editGender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="editDob" class="form-label">
                                        <i class="fas fa-birthday-cake text-muted"></i> Date of Birth
                                    </label>
                                    <input type="date" class="form-control" id="editDob" name="dob">
                                </div>
                                <div class="col-md-4">
                                    <label for="editAnniversary" class="form-label">
                                        <i class="fas fa-heart text-muted"></i> Anniversary Date
                                    </label>
                                    <input type="date" class="form-control" id="editAnniversary" name="anniversary">
                                </div>
                                <div class="col-md-4">
                                    <label for="editSource" class="form-label">
                                        <i class="fas fa-bullhorn text-muted"></i> Client Source
                                    </label>
                                    <select class="form-select" id="editSource" name="source">
                                        <option value="">Select Source</option>
                                        <option value="walk-in">Walk-in</option>
                                        <option value="referral">Referral / Word of Mouth</option>
                                        <option value="online">Online Booking</option>
                                        <option value="social-media">Instagram / Social Media</option>
                                        <option value="advertisement">Advertisement</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Contact & Location --}}
                        <div class="tab-pane fade" id="contact-pane" role="tabpanel" aria-labelledby="contact-tab">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="editPhone" class="form-label">
                                        <i class="fas fa-phone text-muted"></i> Mobile Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control" id="editPhone" name="phone" required placeholder="e.g. +974 66266581">
                                </div>
                                <div class="col-md-6">
                                    <label for="editSecondaryNumber" class="form-label">
                                        <i class="fas fa-phone-alt text-muted"></i> Secondary / Alternate Phone
                                    </label>
                                    <input type="tel" class="form-control" id="editSecondaryNumber" name="secondary_number" placeholder="Optional alternate number">
                                </div>
                                <div class="col-md-6">
                                    <label for="editEmail" class="form-label" id="editEmailLabel">
                                        <i class="fas fa-envelope text-muted"></i> Email Address
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="fas fa-at"></i></span>
                                        <input type="email" class="form-control" id="editEmail" name="email" placeholder="client@example.com" oninput="syncPortalEmailPreview()">
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Required if Client Portal login access is enabled</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="editPreferredContact" class="form-label">
                                        <i class="fas fa-comments text-muted"></i> Preferred Contact Method
                                    </label>
                                    <select class="form-select" id="editPreferredContact" name="preferred_contact" required>
                                        <option value="phone">Phone Call</option>
                                        <option value="email">Email</option>
                                        <option value="sms">SMS / Text Message</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="editLocation" class="form-label">
                                        <i class="fas fa-map-marker-alt text-muted"></i> City / Area
                                    </label>
                                    <input type="text" class="form-control" id="editLocation" name="location" placeholder="e.g., Doha, Qatar">
                                </div>
                                <div class="col-md-6">
                                    <label for="editAddress" class="form-label">
                                        <i class="fas fa-home text-muted"></i> Full Street Address
                                    </label>
                                    <textarea class="form-control" id="editAddress" name="address" rows="2" placeholder="Street, Building, Flat / Villa No..."></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 3: Dedicated Portal & Security Tab --}}
                        <div class="tab-pane fade" id="portal-pane" role="tabpanel" aria-labelledby="portal-tab">
                            <div class="portal-card p-3 mb-3" id="portalMainCard">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="p-2 rounded-3 d-flex align-items-center justify-content-center" style="background: rgba(197, 168, 128, 0.2); width: 42px; height: 42px;">
                                            <i class="fas fa-shield-alt fa-lg" style="color: #c5a880;"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="mb-0 fw-bold text-dark">Client Portal Login Access</h6>
                                                <span id="portalActiveStatusPill" class="badge" style="font-size: 0.72rem; padding: 4px 8px;"></span>
                                            </div>
                                            <small class="text-muted">Allows this client to sign in, view appointments, book services, and redeem loyalty points</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="grant_login_access" value="1" id="grantLoginToggle_EDIT" style="width: 44px; height: 22px; cursor: pointer;" onchange="togglePortalSection()">
                                    </div>
                                </div>

                                {{-- Warning if Email Missing --}}
                                <div id="portalMissingEmailWarning" class="alert alert-warning py-2 px-3 mb-3 d-none align-items-center justify-content-between" style="border-radius: 8px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-exclamation-triangle text-warning"></i>
                                        <span class="small">An email address is required to activate portal login access.</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-warning text-dark py-0 px-2" onclick="switchToContactTab()">
                                        Add Email
                                    </button>
                                </div>

                                {{-- Portal Fields (revealed when toggle is ON) --}}
                                <div id="portalConfigFields" style="display: none;">
                                    {{-- Info Banner --}}
                                    <div class="p-2 px-3 rounded-2 mb-3 d-flex align-items-center justify-content-between" style="background: rgba(197, 168, 128, 0.1); border: 1px dashed rgba(197, 168, 128, 0.4);">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-info-circle" style="color: #c5a880;"></i>
                                            <span class="small text-muted">Login Username: <strong class="text-dark font-monospace" id="portalUsernamePreview">—</strong></span>
                                        </div>
                                        <span class="badge bg-light text-muted border font-monospace small" style="font-size: 0.7rem;">Role: Customer</span>
                                    </div>

                                    {{-- Password Mode Selection --}}
                                    <label class="form-label mb-2 fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px; color: #64748b;">
                                        <i class="fas fa-lock text-muted"></i> Choose Password Setup Mode
                                    </label>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <div class="selection-pill active d-flex align-items-start gap-2" id="pillAutoPass" onclick="selectPasswordMode('auto')">
                                                <input class="form-check-input mt-1" type="radio" name="password_mode" id="auto_pass_EDIT" value="auto" checked onchange="handlePasswordModeChange()">
                                                <div>
                                                    <div class="fw-bold small text-dark"><i class="fas fa-magic text-success me-1"></i> Auto-Generate &amp; Send</div>
                                                    <small class="text-muted d-block" style="font-size: 0.72rem;">Generates a secure password and delivers it via chosen notification method.</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="selection-pill d-flex align-items-start gap-2" id="pillManualPass" onclick="selectPasswordMode('manual')">
                                                <input class="form-check-input mt-1" type="radio" name="password_mode" id="manual_pass_EDIT" value="manual" onchange="handlePasswordModeChange()">
                                                <div>
                                                    <div class="fw-bold small text-dark"><i class="fas fa-key text-warning me-1"></i> Set Password Manually</div>
                                                    <small class="text-muted d-block" style="font-size: 0.72rem;">Type a specific password or generate one right here to share with the client.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Manual Password Field (hidden by default) --}}
                                    <div id="manualPasswordField" class="mb-3 p-3 rounded-2" style="background: #ffffff; border: 1px solid #cbd5e1; display: none;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="portalPasswordInput_EDIT" class="form-label m-0 fw-semibold">
                                                <i class="fas fa-key text-warning me-1"></i> Enter New Password (Min. 8 chars)
                                            </label>
                                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" style="color: #b38e5d; font-size: 0.78rem;" onclick="generateStrongPassword()">
                                                <i class="fas fa-random me-1"></i> Generate Strong Password
                                            </button>
                                        </div>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="portal_password" id="portalPasswordInput_EDIT" placeholder="Type or generate password..." autocomplete="new-password" oninput="checkPassStrength(this.value)">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassVisibility()">
                                                <i class="fas fa-eye" id="passToggleIcon"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" type="button" title="Copy Password" onclick="copyPassword()">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                        <div class="mt-2">
                                            <div class="pass-strength-bar" id="passStrengthBar"></div>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <small class="text-muted" style="font-size: 0.7rem;" id="passStrengthLabel">Enter at least 8 characters</small>
                                                <span class="badge d-none" id="passCopiedBadge" style="background: #10b981; font-size: 0.68rem;">Copied to clipboard!</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Notification Method --}}
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-paper-plane text-muted"></i> Deliver Credentials Via
                                            </label>
                                            <select class="form-select" name="credential_notify" id="credentialNotifySelect">
                                                <option value="email" selected>Email only (to customer's email)</option>
                                                <option value="sms">SMS only (to mobile number)</option>
                                                <option value="both">Both Email and SMS</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-external-link-alt text-muted"></i> Portal Access Link
                                            </label>
                                            <input type="text" class="form-control bg-light font-monospace text-muted" value="{{ route('login') }}" readonly style="font-size: 0.78rem;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 4: Preferences & Notes --}}
                        <div class="tab-pane fade" id="preferences-pane" role="tabpanel" aria-labelledby="preferences-tab">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="editStatus" class="form-label">
                                        <i class="fas fa-user-shield text-muted"></i> Account Status
                                    </label>
                                    <select class="form-select" id="editStatus" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label mb-2">
                                        <i class="fas fa-bell text-muted"></i> Communication &amp; Marketing Preferences
                                    </label>
                                    <div class="d-flex gap-4 p-2 bg-light rounded-2 border">
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="editSendPromotional" name="send_promotional_sms" value="1">
                                            <label class="form-check-label small text-dark" for="editSendPromotional">Promotional Messages (Discounts, News)</label>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="editSendTransactional" name="send_transactional_sms" value="1">
                                            <label class="form-check-label small text-dark" for="editSendTransactional">Transactional Alerts (Confirmations, Reminders)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="editNotes" class="form-label">
                                        <i class="fas fa-sticky-note text-muted"></i> Customer Preferences &amp; Notes
                                    </label>
                                    <textarea class="form-control" id="editNotes" name="notes" rows="3" placeholder="Styling preferences, favorite drink, personal notes..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="editMedicalNotes" class="form-label text-danger">
                                        <i class="fas fa-notes-medical text-danger"></i> Medical, Skin &amp; Allergy Notes
                                    </label>
                                    <textarea class="form-control border-danger-subtle" id="editMedicalNotes" name="medical_notes" rows="3" placeholder="Skin allergies, scalp sensitivity, chemical reactions..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer actions --}}
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i> Fields marked with <span class="text-danger">*</span> are required
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-save-luxury" id="saveEditCustomerBtn">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Make sure the function is available globally
        if (typeof window.loadEditCustomerModal !== 'function') {
            function loadEditCustomerModal(customerId) {
                const modalElement = document.getElementById('editCustomerModal');
                if (!modalElement) {
                    if (window.notifications) window.notifications.show('Error: Could not open edit form', 'error');
                    return;
                }

                let modal = bootstrap.Modal.getInstance(modalElement);
                if (!modal) {
                    modal = new bootstrap.Modal(modalElement);
                }

                const form = document.getElementById('customerEditForm');
                if (!form) {
                    if (window.notifications) window.notifications.show('Error: Could not open edit form', 'error');
                    return;
                }

                const submitButton = document.getElementById('saveEditCustomerBtn');

                // Reset and show the modal
                form.reset();
                modal.show();

                // Reset tabs to first tab (Basic Info tab) by default on open
                const firstTabEl = document.getElementById('basic-tab');
                if (firstTabEl) {
                    const firstTab = new bootstrap.Tab(firstTabEl);
                    firstTab.show();
                }

                // Reset portal state
                resetPortalUi();

                // Load customer data
                fetch(`{{ route('admin.customers.index') }}/${customerId}/edit`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to load customer data');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.data) {
                            const customer = data.data;
                            document.getElementById('editFirstName').value = customer.first_name || '';
                            document.getElementById('editLastName').value = customer.last_name || '';
                            document.getElementById('editCustomerId').value = customer.customer_id || 'Not assigned';
                            
                            const headerCode = document.getElementById('headerCustomerCode');
                            if (headerCode) {
                                headerCode.textContent = customer.customer_id || '';
                            }

                            document.getElementById('editGender').value = customer.gender || '';
                            document.getElementById('editDob').value = customer.dob || '';
                            document.getElementById('editAnniversary').value = customer.anniversary || '';
                            document.getElementById('editPhone').value = customer.phone || '';
                            document.getElementById('editSecondaryNumber').value = customer.secondary_number || '';
                            document.getElementById('editEmail').value = customer.email || '';
                            document.getElementById('editLocation').value = customer.location || '';
                            document.getElementById('editSource').value = customer.source || '';
                            document.getElementById('editPreferredContact').value = customer.preferred_contact || 'phone';
                            document.getElementById('editStatus').value = customer.status || 'active';
                            document.getElementById('editAddress').value = customer.address || '';
                            document.getElementById('editNotes').value = customer.notes || '';
                            document.getElementById('editMedicalNotes').value = customer.medical_notes || '';
                            document.getElementById('editSendPromotional').checked = customer.send_promotional_sms || false;
                            document.getElementById('editSendTransactional').checked = customer.send_transactional_sms || false;

                            // Portal status setup
                            const portalToggle = document.getElementById('grantLoginToggle_EDIT');
                            const portalBadge = document.getElementById('portalTabBadge');
                            const statusPill = document.getElementById('portalActiveStatusPill');

                            if (customer.has_portal_access) {
                                if (portalBadge) portalBadge.classList.remove('d-none');
                                if (statusPill) {
                                    statusPill.className = 'badge bg-success text-white';
                                    statusPill.innerHTML = '<i class="fas fa-check-circle me-1"></i>Portal Active';
                                }
                                if (portalToggle) {
                                    portalToggle.checked = true;
                                }
                            } else {
                                if (portalBadge) portalBadge.classList.add('d-none');
                                if (statusPill) {
                                    statusPill.className = 'badge bg-secondary text-white';
                                    statusPill.innerHTML = '<i class="fas fa-times-circle me-1"></i>Not Active';
                                }
                                if (portalToggle) {
                                    portalToggle.checked = false;
                                }
                            }
                            togglePortalSection();
                            syncPortalEmailPreview();

                            // Handle conditional email requirement
                            updateEmailRequirement();

                            // Set form action
                            form.action = `{{ route('admin.customers.index') }}/${customer.id}`;
                        } else {
                            throw new Error('Could not load customer data');
                        }
                    })
                    .catch(error => {
                        window.notifications ? window.notifications.show(error.message || 'Failed to load customer details', 'error') : alert(error.message);
                        modal.hide();
                    });

                // Form submission
                form.onsubmit = function (e) {
                    e.preventDefault();

                    const formData = new FormData(form);
                    formData.set('_method', 'PUT');

                    const portalToggle = document.getElementById('grantLoginToggle_EDIT');
                    const passInput = document.getElementById('portalPasswordInput_EDIT');
                    const isManual = document.getElementById('manual_pass_EDIT')?.checked;
                    const hasPassword = passInput && passInput.value.trim().length > 0;

                    if (portalToggle && portalToggle.checked) {
                        formData.set('grant_login_access', '1');
                    }

                    if (isManual && hasPassword) {
                        formData.set('password_mode', 'manual');
                        formData.set('portal_password', passInput.value.trim());
                        formData.set('grant_login_access', '1');
                    } else if (isManual) {
                        formData.set('password_mode', 'manual');
                    } else {
                        formData.set('password_mode', 'auto');
                        formData.delete('portal_password');
                    }

                    submitButton.disabled = true;
                    const originalBtnHtml = submitButton.innerHTML;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving Changes...';

                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(async response => {
                            const data = await response.json().catch(() => null);
                            if (!response.ok) {
                                let errorMsg = 'Failed to update customer';
                                if (data && data.errors) {
                                    const firstKey = Object.keys(data.errors)[0];
                                    const firstVal = data.errors[firstKey];
                                    errorMsg = Array.isArray(firstVal) ? firstVal[0] : firstVal;

                                    // Auto-navigate to tab containing error
                                    if (['first_name', 'last_name', 'gender', 'dob'].includes(firstKey)) {
                                        new bootstrap.Tab(document.getElementById('basic-tab')).show();
                                    } else if (['email', 'phone', 'secondary_number', 'location', 'address'].includes(firstKey)) {
                                        new bootstrap.Tab(document.getElementById('contact-tab')).show();
                                    } else if (['portal_password', 'grant_login_access', 'password_mode'].includes(firstKey)) {
                                        new bootstrap.Tab(document.getElementById('portal-tab')).show();
                                    } else if (['status', 'notes', 'medical_notes'].includes(firstKey)) {
                                        new bootstrap.Tab(document.getElementById('preferences-tab')).show();
                                    }
                                } else if (data && data.message) {
                                    errorMsg = data.message;
                                }
                                throw new Error(errorMsg);
                            }
                            return data;
                        })
                        .then(data => {
                            if (data && (data.success || data.status === 'success')) {
                                if (window.notifications && typeof window.notifications.show === 'function') {
                                    window.notifications.show(data.message || 'Customer updated successfully', 'success');
                                } else {
                                    alert(data.message || 'Customer updated successfully');
                                }
                                modal.hide();

                                if (typeof loadCustomers === 'function') {
                                    loadCustomers();
                                }
                                if (window.jQuery && $('#customersTable').length && $.fn.DataTable && $.fn.DataTable.isDataTable('#customersTable')) {
                                    $('#customersTable').DataTable().ajax.reload(null, false);
                                }
                                window.dispatchEvent(new CustomEvent('customer-updated', { detail: data.data || data.customer }));
                            } else {
                                throw new Error(data ? data.message : 'Failed to update customer');
                            }
                        })
                        .catch(error => {
                            if (window.notifications && typeof window.notifications.show === 'function') {
                                window.notifications.show(error.message || 'Failed to update customer', 'error');
                            } else {
                                alert(error.message || 'Failed to update customer');
                            }
                        })
                        .finally(() => {
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalBtnHtml;
                        });
                };
            }

            window.loadEditCustomerModal = loadEditCustomerModal;
        }

        // Helper functions
        function resetPortalUi() {
            const toggle = document.getElementById('grantLoginToggle_EDIT');
            const config = document.getElementById('portalConfigFields');
            const manualField = document.getElementById('manualPasswordField');
            if (toggle) toggle.checked = false;
            if (config) config.style.display = 'none';
            if (manualField) manualField.style.display = 'none';
            selectPasswordMode('auto');
        }

        function togglePortalSection() {
            const toggle = document.getElementById('grantLoginToggle_EDIT');
            const config = document.getElementById('portalConfigFields');
            const card = document.getElementById('portalMainCard');
            const email = document.getElementById('editEmail').value.trim();
            const warning = document.getElementById('portalMissingEmailWarning');

            if (!toggle || !config) return;

            if (toggle.checked) {
                config.style.display = 'block';
                if (card) card.classList.add('active-portal');
                if (!email && warning) {
                    warning.classList.remove('d-none');
                    warning.classList.add('d-flex');
                } else if (warning) {
                    warning.classList.add('d-none');
                    warning.classList.remove('d-flex');
                }
            } else {
                config.style.display = 'none';
                if (card) card.classList.remove('active-portal');
                if (warning) {
                    warning.classList.add('d-none');
                    warning.classList.remove('d-flex');
                }
            }
        }

        function syncPortalEmailPreview() {
            const email = document.getElementById('editEmail').value.trim();
            const preview = document.getElementById('portalUsernamePreview');
            const warning = document.getElementById('portalMissingEmailWarning');
            const toggle = document.getElementById('grantLoginToggle_EDIT');

            if (preview) {
                preview.textContent = email || '(Enter email in Contact tab)';
            }
            if (toggle && toggle.checked && warning) {
                if (!email) {
                    warning.classList.remove('d-none');
                    warning.classList.add('d-flex');
                } else {
                    warning.classList.add('d-none');
                    warning.classList.remove('d-flex');
                }
            }
        }

        function selectPasswordMode(mode) {
            const autoRadio = document.getElementById('auto_pass_EDIT');
            const manualRadio = document.getElementById('manual_pass_EDIT');
            const pillAuto = document.getElementById('pillAutoPass');
            const pillManual = document.getElementById('pillManualPass');
            const manualField = document.getElementById('manualPasswordField');

            if (mode === 'manual') {
                if (manualRadio) manualRadio.checked = true;
                if (pillManual) pillManual.classList.add('active');
                if (pillAuto) pillAuto.classList.remove('active');
                if (manualField) $(manualField).slideDown(180);
            } else {
                if (autoRadio) autoRadio.checked = true;
                if (pillAuto) pillAuto.classList.add('active');
                if (pillManual) pillManual.classList.remove('active');
                if (manualField) $(manualField).slideUp(180);
            }
        }

        function handlePasswordModeChange() {
            const isManual = document.getElementById('manual_pass_EDIT').checked;
            selectPasswordMode(isManual ? 'manual' : 'auto');
        }

        function togglePassVisibility() {
            const input = document.getElementById('portalPasswordInput_EDIT');
            const icon = document.getElementById('passToggleIcon');
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                if (icon) icon.className = 'fas fa-eye';
            }
        }

        function generateStrongPassword() {
            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%&*';
            let pass = '';
            for (let i = 0; i < 12; i++) {
                pass += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            const input = document.getElementById('portalPasswordInput_EDIT');
            if (input) {
                input.value = pass;
                input.type = 'text';
                const icon = document.getElementById('passToggleIcon');
                if (icon) icon.className = 'fas fa-eye-slash';
                checkPassStrength(pass);
            }
            copyPassword();
        }

        function copyPassword() {
            const input = document.getElementById('portalPasswordInput_EDIT');
            if (!input || !input.value) return;
            navigator.clipboard.writeText(input.value).then(() => {
                const badge = document.getElementById('passCopiedBadge');
                if (badge) {
                    badge.classList.remove('d-none');
                    setTimeout(() => badge.classList.add('d-none'), 2500);
                }
            });
        }

        function checkPassStrength(val) {
            const bar = document.getElementById('passStrengthBar');
            const lbl = document.getElementById('passStrengthLabel');
            if (!bar || !lbl) return;

            if (!val || val.length < 8) {
                bar.style.width = '25%';
                bar.style.backgroundColor = '#ef4444';
                lbl.textContent = 'Too short (minimum 8 characters)';
                lbl.className = 'text-danger small';
            } else if (val.length < 10 || !/[0-9]/.test(val) || !/[A-Z]/.test(val)) {
                bar.style.width = '60%';
                bar.style.backgroundColor = '#f59e0b';
                lbl.textContent = 'Medium strength';
                lbl.className = 'text-warning small';
            } else {
                bar.style.width = '100%';
                bar.style.backgroundColor = '#10b981';
                lbl.textContent = 'Strong password';
                lbl.className = 'text-success small';
            }
        }

        function copyCustomerId() {
            const input = document.getElementById('editCustomerId');
            if (!input || !input.value) return;
            navigator.clipboard.writeText(input.value).then(() => {
                if (window.notifications) window.notifications.show('Customer ID copied to clipboard!', 'info');
            });
        }

        function switchToContactTab() {
            const tabBtn = document.getElementById('contact-tab');
            if (tabBtn) {
                new bootstrap.Tab(tabBtn).show();
                setTimeout(() => {
                    const emailInput = document.getElementById('editEmail');
                    if (emailInput) emailInput.focus();
                }, 200);
            }
        }

        function updateEmailRequirement() {
            const preferredContact = document.getElementById('editPreferredContact').value;
            const emailInput = document.getElementById('editEmail');
            const emailLabel = document.getElementById('editEmailLabel');
            if (!emailInput || !emailLabel) return;

            if (preferredContact === 'email') {
                emailInput.setAttribute('required', 'required');
                emailLabel.innerHTML = '<i class="fas fa-envelope text-muted"></i> Email Address <span class="text-danger">*</span>';
            } else {
                emailInput.removeAttribute('required');
                emailLabel.innerHTML = '<i class="fas fa-envelope text-muted"></i> Email Address';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const pref = document.getElementById('editPreferredContact');
            if (pref) pref.addEventListener('change', updateEmailRequirement);
        });
    </script>
@endpush
