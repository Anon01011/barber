<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header -->
            <div class="modal-header bg-gradient-primary text-white border-0 position-relative py-3">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="modal-title fw-bold mb-0" id="addCustomerModalLabel">Add New Customer</h6>
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
                                        <i class="fas fa-user me-1 text-primary"></i>First Name <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="first_name" placeholder="John"
                                        required>
                                    <div class="invalid-feedback">Please enter first name</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-user me-1 text-primary"></i>Last Name
                                    </label>
                                    <input type="text" class="form-control" name="last_name" placeholder="Doe">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-venus-mars me-1 text-primary"></i>Gender
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
                                        <i class="fas fa-phone me-1 text-primary"></i>Mobile Number <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control" name="phone" id="modal_phone"
                                        placeholder="Enter Mobile Number" required>
                                    <input type="hidden" name="country_code" id="modal_country_code" value="+974">
                                    <div class="invalid-feedback">Please enter a valid phone number</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-phone-alt me-1 text-primary"></i>Secondary Number
                                    </label>
                                    <input type="tel" class="form-control" name="secondary_number"
                                        id="modal_secondary_number" placeholder="Alternate Contact">
                                    <input type="hidden" name="secondary_country_code" id="modal_secondary_country_code"
                                        value="+974">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" id="emailLabel">
                                        <i class="fas fa-envelope me-1 text-primary"></i>Email Address
                                    </label>
                                    <input type="email" class="form-control" name="email" id="emailInput"
                                        placeholder="customer@example.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-comments me-1 text-primary"></i>Preferred Contact <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" name="preferred_contact" id="preferredContactSelect"
                                        required>
                                        <option value="">Select preferred method</option>
                                        <option value="phone" selected>📞 Phone Call</option>
                                        <option value="email">📧 Email</option>
                                        <option value="sms">💬 SMS/Text</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a preferred contact method</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-map-marker-alt me-1 text-primary"></i>Location
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
                                        <i class="fas fa-birthday-cake me-1 text-primary"></i>Date of Birth
                                    </label>
                                    <input type="date" class="form-control" name="dob">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-heart me-1 text-primary"></i>Anniversary
                                    </label>
                                    <input type="date" class="form-control" name="anniversary">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-bullhorn me-1 text-primary"></i>Source
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
                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 bg-light p-3">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Fields marked with <span class="text-danger">*</span> are required
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" form="addCustomerForm" class="btn btn-primary btn-sm px-3">
                            <i class="fas fa-user-plus me-1"></i>Create
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views/components/modals/add-customer.blade.php ENDPATH**/ ?>