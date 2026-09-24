<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['customerId' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['customerId' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<style>
    #editCustomerModal {
        --primary-color: #7c3aed;
        --primary-light: #f5f3ff;
        --primary-dark: #6d28d9;
        --border-color: #e2e8f0;
        --text-color: #334155;
    }

    #editCustomerModal .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    #editCustomerModal .modal-header {
        border-bottom: none;
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        position: relative;
        overflow: hidden;
    }

    #editCustomerModal .modal-header:before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.08' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.5;
    }

    #editCustomerModal .modal-title {
        font-weight: 600;
        color: #fff !important;
        font-size: 1.25rem;
        letter-spacing: -0.3px;
        position: relative;
        display: flex;
        margin: 0;
        line-height: 1.2;
        align-items: center;
    }

    #editCustomerModal .nav-pills .nav-link {
        color: #64748b;
        font-weight: 500;
        padding: 0.5rem 1.25rem;
        border-radius: 8px;
        transition: all 0.15s ease;
        font-size: 0.875rem;
        border: 1px solid transparent;
    }

    #editCustomerModal .nav-pills .nav-link.active {
        background-color: var(--primary-color);
        color: #fff;
    }

    #editCustomerModal .nav-pills .nav-link:hover:not(.active) {
        background-color: #f1f5f9;
        color: #334155;
    }

    #editCustomerModal .form-label {
        font-weight: 500;
        font-size: 0.8rem;
        color: #475569;
        margin-bottom: 0.35rem;
        display: flex;
        align-items: center;
    }

    #editCustomerModal .form-label i {
        color: #64748b;
        font-size: 0.85rem;
    }

    #editCustomerModal .form-control,
    #editCustomerModal .form-select {
        height: 38px;
        font-size: 0.85rem;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.375rem 0.75rem;
        color: var(--text-color);
        transition: all 0.15s ease-in-out;
    }

    #editCustomerModal textarea.form-control {
        height: auto;
        min-height: 80px;
    }

    #editCustomerModal .form-control:focus,
    #editCustomerModal .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
        outline: none;
    }

    #editCustomerModal .iti {
        width: 100%;
    }

    #editCustomerModal .iti input[type="tel"] {
        padding-left: 90px !important;
    }

    #editCustomerModal .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    #editCustomerModal .btn {
        height: 38px;
        font-size: 0.875rem;
        font-weight: 500;
        padding: 0.375rem 1.25rem;
        border-radius: 8px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    #editCustomerModal .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    #editCustomerModal .btn-primary:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        transform: translateY(-1px);
    }

    #editCustomerModal .btn-outline-secondary {
        border: 1px solid #cbd5e1;
        color: #475569;
    }

    #editCustomerModal .btn-outline-secondary:hover {
        background-color: #f8fafc;
        color: #1e293b;
    }
</style>

<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Modern Header with Gradient and fixed transparent icon bg -->
            <div class="modal-header text-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.15);">
                        <i class="fas fa-user-edit fa-lg text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title m-0">Edit Customer</h5>
                        <p class="m-0" style="font-size: 0.75rem; opacity: 0.8; line-height: 1.2;">Update customer details & preferences</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-100" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                
                <ul class="nav nav-pills mb-4 d-flex justify-content-center gap-2" id="editCustomerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="basic-tab" data-bs-toggle="pill" data-bs-target="#basic-pane"
                            type="button" role="tab" aria-controls="basic-pane" aria-selected="true">
                            <i class="fas fa-user me-2"></i>Profile & Basic
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="pill" data-bs-target="#contact-pane"
                            type="button" role="tab" aria-controls="contact-pane" aria-selected="false">
                            <i class="fas fa-phone me-2"></i>Contact & Address
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="preferences-tab" data-bs-toggle="pill" data-bs-target="#preferences-pane"
                            type="button" role="tab" aria-controls="preferences-pane" aria-selected="false">
                            <i class="fas fa-cog me-2"></i>Preferences & Notes
                        </button>
                    </li>
                </ul>

                <form id="customerEditForm">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="tab-content" id="editCustomerTabsContent">
                        
                        <div class="tab-pane fade show active" id="basic-pane" role="tabpanel" aria-labelledby="basic-tab">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="editFirstName" class="form-label">
                                        <i class="fas fa-user me-2"></i>First Name *
                                    </label>
                                    <input type="text" class="form-control" id="editFirstName" name="first_name" required placeholder="Enter first name">
                                </div>
                                <div class="col-md-6">
                                    <label for="editLastName" class="form-label">
                                        <i class="fas fa-user me-2"></i>Last Name
                                    </label>
                                    <input type="text" class="form-control" id="editLastName" name="last_name" placeholder="Enter last name">
                                </div>
                                <div class="col-md-6">
                                    <label for="editCustomerId" class="form-label">
                                        <i class="fas fa-id-card me-2"></i>Customer ID
                                    </label>
                                    <input type="text" class="form-control bg-light" id="editCustomerId" readonly disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="editGender" class="form-label">
                                        <i class="fas fa-venus-mars me-2"></i>Gender
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
                                        <i class="fas fa-calendar-alt me-2"></i>Date of Birth
                                    </label>
                                    <input type="date" class="form-control" id="editDob" name="dob">
                                </div>
                                <div class="col-md-4">
                                    <label for="editAnniversary" class="form-label">
                                        <i class="fas fa-heart me-2"></i>Anniversary Date
                                    </label>
                                    <input type="date" class="form-control" id="editAnniversary" name="anniversary">
                                </div>
                                <div class="col-md-4">
                                    <label for="editSource" class="form-label">
                                        <i class="fas fa-bullhorn me-2"></i>Source
                                    </label>
                                    <select class="form-select" id="editSource" name="source">
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

                        
                        <div class="tab-pane fade" id="contact-pane" role="tabpanel" aria-labelledby="contact-tab">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="editPhone" class="form-label">
                                        <i class="fas fa-phone me-2"></i>Mobile Number *
                                    </label>
                                    <input type="tel" class="form-control" id="editPhone" name="phone" required placeholder="Enter mobile number">
                                </div>
                                <div class="col-md-6">
                                    <label for="editSecondaryNumber" class="form-label">
                                        <i class="fas fa-phone-alt me-2"></i>Secondary Phone
                                    </label>
                                    <input type="tel" class="form-control" id="editSecondaryNumber" name="secondary_number" placeholder="Enter alternate number">
                                </div>
                                <div class="col-md-6">
                                    <label for="editEmail" class="form-label" id="editEmailLabel">
                                        <i class="fas fa-envelope me-2"></i>Email Address
                                    </label>
                                    <input type="email" class="form-control" id="editEmail" name="email" placeholder="email@example.com">
                                </div>
                                <div class="col-md-6">
                                    <label for="editPreferredContact" class="form-label">
                                        <i class="fas fa-comments me-2"></i>Preferred Contact Method
                                    </label>
                                    <select class="form-select" id="editPreferredContact" name="preferred_contact" required>
                                        <option value="phone">Phone</option>
                                        <option value="email">Email</option>
                                        <option value="sms">SMS</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="editLocation" class="form-label">
                                        <i class="fas fa-map-marker-alt me-2"></i>Location (City/State)
                                    </label>
                                    <input type="text" class="form-control" id="editLocation" name="location" placeholder="e.g., Doha, Qatar">
                                </div>
                                <div class="col-md-6">
                                    <label for="editAddress" class="form-label">
                                        <i class="fas fa-home me-2"></i>Full Address
                                    </label>
                                    <textarea class="form-control" id="editAddress" name="address" rows="1" placeholder="Enter address details..."></textarea>
                                </div>
                            </div>
                        </div>

                        
                        <div class="tab-pane fade" id="preferences-pane" role="tabpanel" aria-labelledby="preferences-tab">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="editStatus" class="form-label">
                                        <i class="fas fa-user-shield me-2"></i>Account Status
                                    </label>
                                    <select class="form-select" id="editStatus" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-8 d-flex flex-column justify-content-center">
                                    <label class="form-label mb-2">
                                        <i class="fas fa-sms me-2"></i>SMS/WhatsApp Preferences
                                    </label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="editSendPromotional" name="send_promotional_sms" value="1">
                                            <label class="form-check-label text-muted" for="editSendPromotional">Promotional Messages</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="editSendTransactional" name="send_transactional_sms" value="1">
                                            <label class="form-check-label text-muted" for="editSendTransactional">Transactional Messages</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="editNotes" class="form-label">
                                        <i class="fas fa-sticky-note me-2"></i>Customer Notes
                                    </label>
                                    <textarea class="form-control" id="editNotes" name="notes" rows="3" placeholder="Add customer preferences or notes..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="editMedicalNotes" class="form-label text-danger">
                                        <i class="fas fa-notes-medical me-2"></i>Medical Notes
                                    </label>
                                    <textarea class="form-control" id="editMedicalNotes" name="medical_notes" rows="3" placeholder="Add allergies, medical conditions..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i> Fields marked with * are required
                        </div>
                        <div class="d-flex">
                            <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Make sure the function is available globally
        if (typeof window.loadEditCustomerModal !== 'function') {
            function loadEditCustomerModal(customerId) {
                const modalElement = document.getElementById('editCustomerModal');
                if (!modalElement) {
                    window.notifications.show('Error: Could not open edit form', 'error');
                    return;
                }

                // Initialize modal if not already initialized
                let modal = bootstrap.Modal.getInstance(modalElement);
                if (!modal) {
                    modal = new bootstrap.Modal(modalElement);
                }

                const form = document.getElementById('customerEditForm');
                if (!form) {
                    window.notifications.show('Error: Could not open edit form', 'error');
                    return;
                }

                // Get form elements
                const submitButton = form.querySelector('button[type="submit"]');
                const spinner = submitButton.querySelector('.spinner-border');

                // Reset and show the modal
                form.reset();
                modal.show();

                // Reset tabs to first tab (Basic Info tab) by default on open
                const firstTabEl = document.getElementById('basic-tab');
                if (firstTabEl) {
                    const firstTab = new bootstrap.Tab(firstTabEl);
                    firstTab.show();
                }

                // Load customer data
                fetch(`<?php echo e(route('admin.customers.index')); ?>/${customerId}/edit`, {
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
                        // Populate form with customer data
                        if (data.success && data.data) {
                            const customer = data.data;
                            document.getElementById('editFirstName').value = customer.first_name || '';
                            document.getElementById('editLastName').value = customer.last_name || '';
                            document.getElementById('editCustomerId').value = customer.customer_id || 'Not assigned';
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

                            // Handle conditional email requirement
                            updateEmailRequirement();

                            // Set form action
                            form.action = `<?php echo e(route('admin.customers.index')); ?>/${customer.id}`;

                            // Ensure CSRF token is set
                            if (!form.querySelector('input[name="_token"]')) {
                                const token = document.querySelector('meta[name="csrf-token"]');
                                if (token) {
                                    const csrfInput = document.createElement('input');
                                    csrfInput.type = 'hidden';
                                    csrfInput.name = '_token';
                                    csrfInput.value = token.content;
                                    form.appendChild(csrfInput);
                                }
                            }
                        } else {
                            throw new Error('Could not load customer data');
                        }
                    })
                    .catch(error => {
                        window.notifications.show(error.message || 'Failed to load customer details', 'error');
                        modal.hide();
                    });

                // Handle conditional email requirement
                function updateEmailRequirement() {
                    const preferredContact = document.getElementById('editPreferredContact').value;
                    const emailInput = document.getElementById('editEmail');
                    const emailLabel = document.getElementById('editEmailLabel');

                    if (preferredContact === 'email') {
                        emailInput.setAttribute('required', 'required');
                        emailLabel.innerHTML = '<i class="fas fa-envelope me-2"></i>Email Address <span class="text-danger">*</span>';
                    } else {
                        emailInput.removeAttribute('required');
                        emailLabel.innerHTML = '<i class="fas fa-envelope me-2"></i>Email Address';
                    }
                }

                // Add event listener for preferred contact change
                document.getElementById('editPreferredContact').addEventListener('change', updateEmailRequirement);

                // Handle form submission
                form.onsubmit = function (e) {
                    e.preventDefault();

                    const formData = new FormData(form);

                    // Show loading state
                    submitButton.disabled = true;
                    spinner.classList.remove('d-none');

                    // Submit the form
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success || data.status === 'success') {
                                window.notifications.show(data.message || 'Customer updated successfully', 'success');
                                modal.hide();

                                // Refresh the customers list if the function exists
                                if (typeof loadCustomers === 'function') {
                                    loadCustomers();
                                }
                            } else {
                                throw new Error(data.message || 'Failed to update customer');
                            }
                        })
                        .catch(error => {
                            window.notifications.show(error.message || 'Failed to update customer', 'error');
                        })
                        .finally(() => {
                            submitButton.disabled = false;
                            spinner.classList.add('d-none');
                        });
                };
            }

            // Make the function available globally
            window.loadEditCustomerModal = loadEditCustomerModal;
        }
    </script>
<?php $__env->stopPush(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\components\modals\edit-customer.blade.php ENDPATH**/ ?>