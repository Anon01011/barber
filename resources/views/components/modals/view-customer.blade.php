<style>
    #viewCustomerModal .modal-header {
        border-bottom: none;
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #7c3aed, #5b21b6);
        position: relative;
        overflow: hidden;
    }

    #viewCustomerModal .modal-header:before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.5;
    }

    #viewCustomerModal .modal-title {
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
</style>

@component('components.modal', [
    'id' => 'viewCustomerModal',
    'title' => 'Customer Details',
    'size' => 'xl',
    'headerClass' => 'border-0',
    'titleClass' => 'fw-bold mb-0 text-white',
    'closeButtonClass' => 'btn-close btn-close-white opacity-100'
])
<div class="customer-details p-4" data-customer-id="">

    {{-- Header Section --}}
    <div class="d-flex align-items-center border-bottom pb-4 mb-4">
        <div class="avatar me-4 flex-shrink-0">
            <div class="avatar-title rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm overflow-hidden"
                style="width:90px; height:90px; min-width:90px; border-radius: 50% !important; font-size: 2.5rem;">
                <i class="fas fa-user"></i>
            </div>
        </div>
        <div class="flex-grow-1 m-2">
            <h4 class="mb-1" id="customerName">John Doe</h4>
            <div class="d-flex align-items-center small text-muted mb-2">
                <span class="badge bg-success-subtle text-success me-2" id="customerStatus">Active</span>
                <span class="me-3" id="customerSince"><i class="fas fa-calendar me-1"></i>Member since Jan 2025</span>
                <span id="customerIdDisplay"><i class="fas fa-id-card me-1"></i>ID: CUST-0001-000001</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="#" class="btn btn-sm btn-outline-primary" id="emailLink">
                    <i class="far fa-envelope me-1"></i>Email
                </a>
                <a href="#" class="btn btn-sm btn-outline-secondary" id="callLink">
                    <i class="fas fa-phone me-1"></i>Call
                </a>
                @php
                    $currentSalon = auth()->user()->salon;
                    $smsEnabled = $currentSalon && $currentSalon->canUseFeature('SMS Notifications') && app(\App\Services\SettingsService::class)->get('enable_sms', false, $currentSalon->id);
                @endphp
                @if($smsEnabled)
                    <a href="#" class="btn btn-sm btn-outline-info" id="smsLink">
                        <i class="fas fa-sms me-1"></i>SMS
                    </a>
                @endif
                <button type="button" class="btn btn-sm btn-outline-warning" id="viewUnpaidBillsBtn">
                    <i class="fas fa-exclamation-circle me-1"></i>Unpaid Bills
                </button>
                <button type="button" class="btn btn-sm btn-outline-success" id="viewBillHistoryBtn">
                    <i class="fas fa-file-invoice me-1"></i>Bill History
                </button>
            </div>
        </div>
    </div>

    {{-- Split Column Layout to fit screen height without scrolling --}}
    <div class="row g-3">
        {{-- Left Column: Customer details, stats, preferences and notes --}}
        <div class="col-lg-5 col-md-12 d-flex flex-column gap-3">
            {{-- Contact & Personal Details Card --}}
            <div class="border rounded p-3 bg-light-subtle">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-user-circle me-2"></i>Profile & Contact
                </h6>
                <div class="row g-2">
                    <div class="col-6 mb-2">
                        <span class="text-muted small d-block"><i class="fas fa-envelope me-1"></i>Email</span>
                        <span id="customerEmail" class="fw-semibold text-dark text-break">-</span>
                    </div>
                    <div class="col-6 mb-2">
                        <span class="text-muted small d-block"><i class="fas fa-phone me-1"></i>Phone</span>
                        <span id="customerPhone" class="fw-semibold text-dark">-</span>
                    </div>
                    <div class="col-6 mb-2" id="secondaryNumberSection" style="display:none;">
                        <span class="text-muted small d-block"><i class="fas fa-phone-alt me-1"></i>Secondary Phone</span>
                        <span id="customerSecondaryNumber" class="fw-semibold text-dark">-</span>
                    </div>
                    <div class="col-6 mb-2">
                        <span class="text-muted small d-block"><i class="fas fa-comments me-1"></i>Preferred Contact</span>
                        <span id="customerPreferredContact" class="fw-semibold text-dark">-</span>
                    </div>
                    <div class="col-6 mb-2" id="genderSection" style="display:none;">
                        <span class="text-muted small d-block"><i class="fas fa-venus-mars me-1"></i>Gender</span>
                        <span id="customerGender" class="fw-semibold text-dark">-</span>
                    </div>
                    <div class="col-6 mb-2" id="dobSection" style="display:none;">
                        <span class="text-muted small d-block"><i class="fas fa-birthday-cake me-1"></i>Date of Birth</span>
                        <span id="customerDob" class="fw-semibold text-dark">-</span>
                    </div>
                    <div class="col-6 mb-2" id="anniversarySection" style="display:none;">
                        <span class="text-muted small d-block"><i class="fas fa-heart me-1"></i>Anniversary</span>
                        <span id="customerAnniversary" class="fw-semibold text-dark">-</span>
                    </div>
                    <div class="col-6 mb-2" id="sourceSection" style="display:none;">
                        <span class="text-muted small d-block"><i class="fas fa-bullhorn me-1"></i>Source</span>
                        <span id="customerSource" class="fw-semibold text-dark">-</span>
                    </div>
                    <div class="col-12" id="locationSection" style="display:none;">
                        <span class="text-muted small d-block"><i class="fas fa-map-marker-alt me-1"></i>Location</span>
                        <span id="customerLocation" class="fw-semibold text-dark mb-0">-</span>
                    </div>
                </div>
            </div>

            {{-- Booking Stats & Preferences Card --}}
            <div class="border rounded p-3 bg-light-subtle">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-chart-line me-2"></i>Booking Stats & Preferences
                </h6>
                <div class="row g-2">
                    <div class="col-6 mb-2">
                        <span class="text-muted small d-block">Total Bookings</span>
                        <span id="totalBookings" class="fw-semibold text-dark">0</span>
                    </div>
                    <div class="col-6 mb-2">
                        <span class="text-muted small d-block">Total Visits</span>
                        <span id="totalVisits" class="fw-semibold text-dark">0</span>
                    </div>
                    <div class="col-6 mb-2">
                        <span class="text-muted small d-block">Last Visit</span>
                        <span id="lastVisit" class="fw-semibold text-dark">Never</span>
                    </div>
                    <div class="col-6 mb-2">
                        <span class="text-muted small d-block">Preferred Service</span>
                        <span id="preferredService" class="fw-semibold text-dark">-</span>
                    </div>
                    @if($smsEnabled)
                        <div class="col-6 mb-1">
                            <span class="text-muted small d-block">Promotional SMS</span>
                            <span id="promotionalSms" class="badge bg-success">Enabled</span>
                        </div>
                        <div class="col-6 mb-1">
                            <span class="text-muted small d-block">Transactional SMS</span>
                            <span id="transactionalSms" class="badge bg-success">Enabled</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Address & Notes Card --}}
            <div class="border rounded p-3 bg-light-subtle" id="notesCardSection" style="display:none;">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-sticky-note me-2"></i>Address & Notes
                </h6>
                <div id="addressSection" class="mb-2" style="display:none;">
                    <span class="text-muted small d-block"><i class="fas fa-map-marker-alt me-1"></i>Address</span>
                    <span id="customerAddress" class="fw-semibold text-dark">-</span>
                </div>
                <div id="notesSection" class="mb-2" style="display:none;">
                    <span class="text-muted small d-block"><i class="fas fa-sticky-note me-1"></i>Customer Notes</span>
                    <span id="customerNotes" class="fw-semibold text-dark">-</span>
                </div>
                <div id="medicalNotesSection" style="display:none;">
                    <span class="text-danger small d-block"><i class="fas fa-notes-medical me-1"></i>Medical Notes</span>
                    <span id="customerMedicalNotes" class="fw-semibold text-danger mb-0">-</span>
                </div>
            </div>
        </div>

        {{-- Right Column: Appointment History --}}
        <div class="col-lg-7 col-md-12">
            <div class="border rounded p-3 h-100 d-flex flex-column bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-primary mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Appointment History
                    </h6>
                    <a href="#" id="viewAllBookings" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i>View All
                    </a>
                </div>

                {{-- Tabs Navigation --}}
                <ul class="nav nav-tabs mb-3" id="bookingTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming"
                            type="button" role="tab" aria-controls="upcoming" aria-selected="true">
                            <i class="fas fa-calendar-check me-1"></i>Upcoming
                            <span id="upcomingCount" class="badge bg-primary ms-1">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button"
                            role="tab" aria-controls="past" aria-selected="false">
                            <i class="fas fa-history me-1"></i>Past
                        </button>
                    </li>
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content flex-grow-1" id="bookingTabsContent" style="max-height: 380px; overflow-y: auto;">
                    {{-- Upcoming Appointments Tab --}}
                    <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Service</th>
                                        <th>Staff</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="upcomingBookings">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            No upcoming appointments
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Past Appointments Tab --}}
                    <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Service</th>
                                        <th>Staff</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="pastBookings">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            No past appointments
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="text-end mt-3 border-top pt-3">
        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Close
        </button>
    </div>

</div>
@endcomponent

@push('scripts')
    <script>
        function viewCustomer(id) {
            if (!id) {

                if (window.notifications) window.notifications.show('Error: No customer ID provided', 'error');
                return;
            }

            // Show loading state
            const modal = document.getElementById('viewCustomerModal');
            if (!modal) {

                if (window.notifications) window.notifications.show('Error: Could not open customer details', 'error');
                return;
            }

            const modalBody = modal.querySelector('.modal-body');
            if (!modalBody) {

                return;
            }

            const originalContent = modalBody.innerHTML;

            // Show loading state
            modalBody.innerHTML = `
                                            <div class="text-center py-5">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <div class="mt-2">Loading customer details...</div>
                                            </div>
                                        `;

            let modalInstance = bootstrap.Modal.getInstance(modal);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modal, {
                    keyboard: true,
                    backdrop: true
                });
            }
            modalInstance.show();

            // Fetch customer data
            fetch(`{{ route('admin.customers.index') }}/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        return response.json().then(data => {
                            if (!response.ok) {
                                throw new Error(data.message || `HTTP error! status: ${response.status}`);
                            }
                            return data;
                        });
                    } else {
                        return response.text().then(text => {

                            throw new Error(`Server returned non-JSON response (Status: ${response.status}).`);
                        });
                    }
                })
                .then(data => {
                    if (!data) {
                        throw new Error('No data received from server');
                    }

                    if (data.success && data.data) {
                        const customer = data.data;

                        // Restore original modal body content
                        modalBody.innerHTML = originalContent;

                        // Set customer ID in the data attribute for bill buttons (after restoring content)
                        const detailContainer = modal.querySelector('.customer-details');
                        if (detailContainer) {
                            detailContainer.dataset.customerId = customer.id;
                        }

                        // Helper function to safely set text content
                        const setTextContent = (selector, text, fallback = 'N/A') => {
                            const element = document.getElementById(selector);
                            if (element) {
                                element.textContent = text !== null && text !== undefined && text !== '' ? text : fallback;
                            }
                        };

                        // Helper function to show/hide sections
                        const toggleSection = (sectionId, show) => {
                            const section = document.getElementById(sectionId);
                            if (section) {
                                section.style.display = show ? 'block' : 'none';
                            }
                        };

                        // Generate initials for avatar
                        const getInitials = (name) => {
                            if (!name) return '?';
                            const parts = name.trim().split(' ');
                            if (parts.length >= 2) {
                                return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
                            }
                            return name.substring(0, 2).toUpperCase();
                        };

                        // Update avatar with initials
                        const avatarElement = modal.querySelector('.avatar-title');
                        if (avatarElement) {
                            const initials = getInitials(customer.name || customer.first_name + ' ' + customer.last_name);
                            avatarElement.innerHTML = `<span style="font-size: 1.5rem; font-weight: 600;">${initials}</span>`;
                        }

                        // Set basic info
                        setTextContent('customerName', customer.full_name || customer.name);
                        setTextContent('customerEmail', customer.email, 'No email provided');
                        setTextContent('customerPhone', customer.phone);

                        // Customer ID
                        const customerIdDisplay = document.getElementById('customerIdDisplay');
                        if (customerIdDisplay) {
                            customerIdDisplay.innerHTML = `<i class="fas fa-id-card me-1"></i>ID: ${customer.customer_id || 'Not assigned'}`;
                        }

                        // Contact Information
                        setTextContent('customerPreferredContact', customer.preferred_contact ? customer.preferred_contact.charAt(0).toUpperCase() + customer.preferred_contact.slice(1) : 'Phone');

                        // Secondary Number
                        if (customer.secondary_number) {
                            toggleSection('secondaryNumberSection', true);
                            setTextContent('customerSecondaryNumber', customer.secondary_number);
                        } else {
                            toggleSection('secondaryNumberSection', false);
                        }

                        // Location
                        if (customer.location) {
                            toggleSection('locationSection', true);
                            setTextContent('customerLocation', customer.location);
                        } else {
                            toggleSection('locationSection', false);
                        }

                        // Personal Details
                        if (customer.gender) {
                            toggleSection('genderSection', true);
                            setTextContent('customerGender', customer.gender.charAt(0).toUpperCase() + customer.gender.slice(1));
                        } else {
                            toggleSection('genderSection', false);
                        }

                        if (customer.dob) {
                            toggleSection('dobSection', true);
                            try {
                                const dobDate = new Date(customer.dob);
                                setTextContent('customerDob', dobDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }));
                            } catch (e) {
                                setTextContent('customerDob', customer.dob);
                            }
                        } else {
                            toggleSection('dobSection', false);
                        }

                        if (customer.anniversary) {
                            toggleSection('anniversarySection', true);
                            try {
                                const anniversaryDate = new Date(customer.anniversary);
                                setTextContent('customerAnniversary', anniversaryDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }));
                            } catch (e) {
                                setTextContent('customerAnniversary', customer.anniversary);
                            }
                        } else {
                            toggleSection('anniversarySection', false);
                        }

                        if (customer.source) {
                            toggleSection('sourceSection', true);
                            setTextContent('customerSource', customer.source.charAt(0).toUpperCase() + customer.source.slice(1));
                        } else {
                            toggleSection('sourceSection', false);
                        }

                        // SMS/WhatsApp Preferences
                        const promotionalSms = document.getElementById('promotionalSms');
                        if (promotionalSms) {
                            if (customer.send_promotional_sms) {
                                promotionalSms.textContent = 'Enabled';
                                promotionalSms.className = 'badge bg-success';
                            } else {
                                promotionalSms.textContent = 'Disabled';
                                promotionalSms.className = 'badge bg-secondary';
                            }
                        }

                        const transactionalSms = document.getElementById('transactionalSms');
                        if (transactionalSms) {
                            if (customer.send_transactional_sms) {
                                transactionalSms.textContent = 'Enabled';
                                transactionalSms.className = 'badge bg-success';
                            } else {
                                transactionalSms.textContent = 'Disabled';
                                transactionalSms.className = 'badge bg-secondary';
                            }
                        }

                        // Address
                        if (customer.address) {
                            toggleSection('addressSection', true);
                            setTextContent('customerAddress', customer.address);
                        } else {
                            toggleSection('addressSection', false);
                        }

                        // Notes
                        if (customer.notes) {
                            toggleSection('notesSection', true);
                            setTextContent('customerNotes', customer.notes);
                        } else {
                            toggleSection('notesSection', false);
                        }

                        // Medical Notes
                        if (customer.medical_notes) {
                            toggleSection('medicalNotesSection', true);
                            setTextContent('customerMedicalNotes', customer.medical_notes);
                        } else {
                            toggleSection('medicalNotesSection', false);
                        }

                        // Toggle entire notes card section visibility
                        const hasAddressOrNotes = customer.address || customer.notes || customer.medical_notes;
                        toggleSection('notesCardSection', hasAddressOrNotes);

                        // Status Badge
                        const statusBadge = document.getElementById('customerStatus');
                        if (statusBadge) {
                            const statusText = customer.status
                                ? customer.status.charAt(0).toUpperCase() + customer.status.slice(1)
                                : 'N/A';
                            statusBadge.textContent = statusText;
                            statusBadge.className = `badge ${customer.status === 'active'
                                ? 'bg-success-subtle text-success'
                                : 'bg-danger-subtle text-danger'}`;
                        }

                        // Activity
                        setTextContent('totalBookings', customer.total_bookings, '0');
                        setTextContent('totalVisits', customer.total_visits, '0');
                        setTextContent('lastVisit', customer.last_visit, 'Never');
                        setTextContent('preferredService', customer.preferred_service, 'Not specified');

                        // Format member since date
                        const customerSince = document.getElementById('customerSince');
                        if (customerSince && customer.created_at) {
                            try {
                                const joinDate = new Date(customer.created_at);
                                customerSince.innerHTML = `<i class="fas fa-calendar me-1"></i>Member since ${joinDate.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                })}`;
                            } catch (e) {

                                customerSince.innerHTML = '<i class="fas fa-calendar me-1"></i>Member since N/A';
                            }
                        }

                        // Set up action buttons
                        const setupActionButton = (buttonId, href, enabled) => {
                            const button = document.getElementById(buttonId);
                            if (button) {
                                if (enabled) {
                                    button.href = href;
                                    button.classList.remove('disabled');
                                    button.onclick = null;
                                } else {
                                    button.href = '#';
                                    button.classList.add('disabled');
                                    button.onclick = (e) => e.preventDefault();
                                }
                            }
                        };

                        // Email button
                        setupActionButton('emailLink', `mailto:${customer.email}`, !!customer.email);

                        // Phone and SMS buttons
                        if (customer.phone && typeof customer.phone === 'string') {
                            try {
                                const phoneNumber = customer.phone.replace(/[^\d+]/g, '');
                                setupActionButton('callLink', `tel:${phoneNumber}`, true);
                                setupActionButton('smsLink', `sms:${phoneNumber}`, true);
                            } catch (e) {

                                setupActionButton('callLink', '#', false);
                                setupActionButton('smsLink', '#', false);
                            }
                        } else {
                            setupActionButton('callLink', '#', false);
                            setupActionButton('smsLink', '#', false);
                        }

                        // Helper function to render bookings
                        const renderBookings = (bookings, containerId, emptyMessage) => {
                            const container = document.getElementById(containerId);
                            if (!container) return;

                            if (bookings && bookings.length > 0) {
                                let bookingsHtml = '';

                                bookings.forEach(booking => {
                                    try {
                                        const serviceName = (booking.service && booking.service.name)
                                            ? booking.service.name
                                            : (booking.service_name || 'Service N/A');

                                        let staffName = 'Unassigned';
                                        if (booking.staff && booking.staff.name) {
                                            staffName = booking.staff.name;
                                        } else if (booking.staff_name) {
                                            staffName = booking.staff_name;
                                        }

                                        const status = booking.status
                                            ? booking.status.charAt(0).toUpperCase() + booking.status.slice(1)
                                            : 'Pending';

                                        let statusClass = 'warning';
                                        if (booking.status === 'completed') statusClass = 'success';
                                        else if (['cancelled', 'no_show', 'rejected'].includes(booking.status)) statusClass = 'danger';
                                        else if (['confirmed', 'approved', 'paid', 'arrived'].includes(booking.status)) statusClass = 'primary';

                                        let bookingDate = 'N/A';
                                        let bookingTime = '';

                                        const dateValue = booking.start_time || booking.booking_date || booking.date || booking.formatted_date || booking.created_at;

                                        if (dateValue) {
                                            if (typeof dateValue === 'string' && dateValue.includes(',')) {
                                                const [datePart, timePart] = dateValue.split(',').map(s => s.trim());
                                                bookingDate = datePart;
                                                bookingTime = timePart;
                                            } else {
                                                const date = new Date(dateValue);
                                                if (!isNaN(date.getTime())) {
                                                    bookingDate = date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                                                    bookingTime = date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                                                }
                                            }
                                        }

                                        let finalPrice = parseFloat(booking.amount || (booking.service && booking.service.price) || booking.price || 0);
                                        const price = finalPrice.toFixed(2);
                                        const currencySymbol = customer.currency_symbol || '$';

                                        bookingsHtml += `
                                                                            <tr>
                                                                                <td>${bookingDate} <small class="text-muted">${bookingTime}</small></td>
                                                                                <td>${serviceName}</td>
                                                                                <td>${staffName}</td>
                                                                                <td><span class="badge bg-${statusClass}-subtle text-${statusClass}">${status}</span></td>
                                                                                <td>${currencySymbol}${price}</td>
                                                                            </tr>`;
                                    } catch (e) {

                                    }
                                });

                                container.innerHTML = bookingsHtml;
                            } else {
                                container.innerHTML = `
                                                                    <tr>
                                                                        <td colspan="5" class="text-center text-muted py-3">
                                                                            ${emptyMessage}
                                                                        </td>
                                                                    </tr>`;
                            }
                        };

                        // Load upcoming bookings
                        renderBookings(customer.upcoming_bookings, 'upcomingBookings', 'No upcoming appointments');

                        // Update upcoming count badge
                        const upcomingCount = document.getElementById('upcomingCount');
                        if (upcomingCount) {
                            const count = customer.upcoming_bookings ? customer.upcoming_bookings.length : 0;
                            upcomingCount.textContent = count;
                        }

                        // Load past bookings
                        renderBookings(customer.past_bookings, 'pastBookings', 'No past appointments');

                        // Add click handler for View All button
                        const viewAllBtn = document.getElementById('viewAllBookings');
                        if (viewAllBtn) {
                            viewAllBtn.href = `{{ route('admin.customers.index') }}/${customer.id}/details`;
                            viewAllBtn.onclick = function (e) {
                                e.preventDefault();
                                window.location.href = this.href;
                            };
                        }
                    } else {
                        throw new Error(data.message || 'Failed to load customer data');
                    }
                })
                .catch(error => {

                    modalBody.innerHTML = `
                                                    <div class="text-center py-5">
                                                        <div class="text-danger mb-3">
                                                            <i class="fas fa-exclamation-circle fa-3x"></i>
                                                        </div>
                                                        <h5 class="mb-3">Error Loading Customer Data</h5>
                                                        <p class="text-muted">${error.message || 'An error occurred while loading customer details.'}</p>
                                                        <button type="button" class="btn btn-outline-secondary mt-3" data-bs-dismiss="modal">
                                                            Close
                                                        </button>
                                                    </div>
                                                `;
                });
        }

        // Use event delegation for bill buttons since innerHTML is replaced
        document.addEventListener('click', function (e) {
            const unpaidBtn = e.target.closest('#viewUnpaidBillsBtn');
            const historyBtn = e.target.closest('#viewBillHistoryBtn');

            if (unpaidBtn || historyBtn) {
                const detailContainer = e.target.closest('.customer-details');
                const customerId = detailContainer ? detailContainer.dataset.customerId : null;

                if (customerId && typeof window.openBillActivityModal === 'function') {
                    // Close the view customer modal first
                    const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewCustomerModal'));
                    if (viewModal) viewModal.hide();

                    // Open bill activity modal
                    if (unpaidBtn) {
                        window.openBillActivityModal(customerId, 'unpaid');
                    } else {
                        window.openBillActivityModal(customerId);
                    }
                }
            }
        });
    </script>
@endpush