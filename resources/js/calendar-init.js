import {
    Calendar
} from '@fullcalendar/core';
import timeGridPlugin from '@fullcalendar/timegrid';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import resourceTimeGridPlugin from '@fullcalendar/resource-timegrid';

// Helper function to get salon slug
function getSalonSlug() {
    // Try to get from meta tag
    const meta = document.querySelector('meta[name="salon-slug"]');
    if (meta) return meta.getAttribute('content');

    // Fallback: try to parse from URL
    const pathParts = window.location.pathname.split('/');
    // Assuming URL structure /salon-slug/...
    if (pathParts.length > 1) return pathParts[1];

    return '';
}

/**
 * Formats a phone number by adding spaces for readability.
 * Example: "1234567890" -> "123 456 7890"
 */
function formatPhoneNumber(phone) {
    if (!phone || phone === 'N/A' || phone === '-') return phone;
    const phoneStr = phone.toString();
    // If it already has spaces/formatting, just return it
    if (phoneStr.includes(' ')) return phoneStr;

    const cleaned = phoneStr.replace(/\D/g, '');
    const hasPlus = phoneStr.startsWith('+');

    // Formatting rules:
    // 10 digits: XXX 456 7890
    if (cleaned.length === 10) {
        return cleaned.replace(/(\d{3})(\d{3})(\d{4})/, '$1 $2 $3');
    }
    // 12 digits (with CC e.g. 91XXXXXXXXXX): XX XXXXX XXXXX
    if (cleaned.length === 12) {
        return (hasPlus ? '+' : '') + cleaned.replace(/(\d{2})(\d{5})(\d{5})/, '$1 $2 $3');
    }
    // 11 digits: X XXX XXX XXXX
    if (cleaned.length === 11) {
        return (hasPlus ? '+' : '') + cleaned.replace(/(\d{1})(\d{3})(\d{3})(\d{4})/, '$1 $2 $3 $4');
    }

    // Fallback: Group every 3-4 digits
    return phoneStr.replace(/(\d{3,4})(?=\d)/g, '$1 ').trim();
}

// Helper function to show notification
function showNotification(message, type = 'info') {
    // Check if there is a global notification system
    if (window.notifications) {
        window.notifications.show(message, type);
        return;
    }

    // Fallback to simple toast
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : 'success'} border-0 show`;
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    toastContainer.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1055';
    document.body.appendChild(container);
    return container;
}

// Helper function to get browser's timezone
function getBrowserTimezone() {
    try {
        // Use Intl.DateTimeFormat to get the IANA timezone name
        return Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch (e) {
        // Fallback: try to determine from offset
        const offset = -new Date().getTimezoneOffset() / 60;
        // This is a basic fallback - not perfect but better than nothing
        return 'local';
    }
}

// Helper function to get timezone with salon preference
function getCalendarTimezone(salonTimezone) {
    // If salon timezone is provided and valid, use it
    if (salonTimezone && salonTimezone.trim() !== '' && salonTimezone !== 'UTC') {
        try {
            Intl.DateTimeFormat(undefined, { timeZone: salonTimezone });
            return salonTimezone;
        } catch (e) {

        }
    }

    // Fallback to local browser timezone if salon timezone is not set
    // This ensures "now" matches the user's wall clock

    return 'local';
}



// Helper functions to format date and time in specific timezone
function formatDateInTimezone(date, timezone) {
    return new Intl.DateTimeFormat('en-CA', { // en-CA gives YYYY-MM-DD format
        timeZone: timezone === 'local' ? undefined : timezone,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).format(date);
}

function formatTimeInTimezone(date, timezone) {
    const use12Hour = (typeof window.calendarSettings !== 'undefined' && window.calendarSettings.time_format)
        ? (window.calendarSettings.time_format === '12h')
        : false;

    return new Intl.DateTimeFormat('en-GB', {
        timeZone: timezone === 'local' ? undefined : timezone,
        hour: '2-digit',
        minute: '2-digit',
        hour12: use12Hour
    }).format(date);
}

function formatTimeForBackend(date, timezone) {
    return new Intl.DateTimeFormat('en-GB', {
        timeZone: timezone === 'local' ? undefined : timezone,
        hour: '2-digit',
        minute: '2-digit',
        hour12: false // Always 24-hour for backend
    }).format(date);
}

function checkGroupedBookingStatus(bookingId) {
    const salonSlug = getSalonSlug();

    // Bypass validation and check if sale exists
    fetch(`/${salonSlug}/admin/pos/booking-details?booking_id=${bookingId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.sale_id && data.all_paid) {
                // Sale exists and is paid, redirect to View Sale
                window.location.href = `/${salonSlug}/admin/pos/sales/${data.sale_id}`;
            } else {
                // Always redirect to POS, regardless of booking status
                window.location.href = `/${salonSlug}/admin/pos?booking_id=${bookingId}`;
            }
        })
        .catch(error => {

            // Fallback to POS
            window.location.href = `/${salonSlug}/admin/pos?booking_id=${bookingId}`;
        });
}

function renderDateNav(calendar) {
    const toolbarChunk = document.querySelector('.fc-toolbar-chunk');
    if (!toolbarChunk) return;

    let navContainer = document.getElementById('calendar-date-nav');
    if (!navContainer) {
        navContainer = document.createElement('div');
        navContainer.id = 'calendar-date-nav';
        const todayBtn = toolbarChunk.querySelector('.fc-today-button');
        if (todayBtn) {
            todayBtn.after(navContainer);
        } else {
            toolbarChunk.appendChild(navContainer);
        }
    }

    // Move date picker into toolbar if it exists and is not already there
    const datePicker = document.getElementById('calendarDatePicker');
    const datePickerContainer = document.getElementById('datePickerContainer');
    if (datePicker && datePickerContainer && datePickerContainer.parentElement.id !== 'calendar-date-nav' && !navContainer.nextElementSibling?.querySelector('#calendarDatePicker')) {
        datePickerContainer.classList.remove('d-none');
        navContainer.after(datePickerContainer);
    }

    // Get current date in salon timezone
    const tz = calendar.getOption('timeZone') || 'local';
    const now = new Date();

    // Format "today" in salon timezone to get YYYY-MM-DD
    const todayStr = new Intl.DateTimeFormat('en-CA', {
        timeZone: tz === 'local' ? undefined : tz,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).format(now);

    const today = new Date(todayStr);
    today.setHours(0, 0, 0, 0);

    const currentCalendarDate = calendar.getDate();
    // Normalize current calendar date to start of day for comparison
    const currentCalStr = new Intl.DateTimeFormat('en-CA', {
        timeZone: tz === 'local' ? undefined : tz,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).format(currentCalendarDate);
    const normalizedCalDate = new Date(currentCalStr);
    normalizedCalDate.setHours(0, 0, 0, 0);

    // Sync date picker value
    if (datePicker) {
        datePicker.value = currentCalStr;
    }

    navContainer.innerHTML = '';

    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    // Start from tomorrow (i = 1) and show next 5 days
    for (let i = 1; i <= 5; i++) {
        // Parse the today string to get year, month, day
        const dateParts = todayStr.split('-');
        const year = parseInt(dateParts[0]);
        const month = parseInt(dateParts[1]) - 1; // JS months are 0-indexed
        const day = parseInt(dateParts[2]);

        // Create date in local time, then add days
        // We'll format it back to the salon timezone to get the correct string
        const tempDate = new Date(year, month, day);
        tempDate.setDate(tempDate.getDate() + i);

        // Format this date in the salon timezone to get YYYY-MM-DD
        const dateStr = new Intl.DateTimeFormat('en-CA', {
            timeZone: tz === 'local' ? undefined : tz,
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).format(tempDate);

        // Get day name and date for display using the same timezone
        const dayName = new Intl.DateTimeFormat('en-US', {
            timeZone: tz === 'local' ? undefined : tz,
            weekday: 'short'
        }).format(tempDate);

        const dayDate = new Intl.DateTimeFormat('en-US', {
            timeZone: tz === 'local' ? undefined : tz,
            day: 'numeric'
        }).format(tempDate);

        const btn = document.createElement('button');
        btn.className = 'date-nav-btn';

        // Check if this date matches the current calendar date
        if (dateStr === currentCalStr) {
            btn.classList.add('active');
        }

        btn.innerHTML = `
            <span class="day-name">${dayName}</span>
            <span class="day-date">${dayDate}</span>
        `;

        btn.onclick = () => {
            calendar.gotoDate(dateStr);
            if (datePicker) {
                datePicker.value = dateStr;
            }
        };

        navContainer.appendChild(btn);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    let calendar;
    let calendarSettings = {
        working_hours_start: '08:00',
        working_hours_end: '20:00',
        slot_duration: 30,
        appointment_buffer_time: 0
    };

    if (window.calendarSettings) {
        calendarSettings = {
            ...calendarSettings,
            ...window.calendarSettings
        };
    }

    const calendarEl = document.getElementById('calendar');

    // Early return if calendar element doesn't exist
    if (!calendarEl) {

        return;
    }

    // const staffFilterEl = document.getElementById('staffFilter'); // Removed in favor of multi-select
    const customerSelect = document.getElementById('customer');
    const bookingForm = document.getElementById('bookingForm');
    const createButton = document.getElementById('createBooking');
    const editButton = createButton; // Unified button
    const calendarViewEl = document.getElementById('calendarView');
    const listViewEl = document.getElementById('listView');
    const calendarViewContainer = document.getElementById('calendarViewContainer');
    const listViewContainer = document.getElementById('listViewContainer');
    const bookingsList = document.getElementById('bookingsList');
    const listViewPagination = document.getElementById('listViewPagination');
    const listViewCount = document.getElementById('listViewCount');

    // Function to update customer details panel
    async function updateCustomerDetails(customerId) {
        const panel = document.getElementById('customerDetailsPanel');
        const placeholder = document.getElementById('customerDetailsPlaceholder');
        const loading = document.getElementById('customerDetailsLoading');
        const content = document.getElementById('customerDetailsContent');

        if (!customerId) {
            if (panel) panel.style.display = 'none';
            if (placeholder) placeholder.style.display = 'block';
            return;
        }

        if (panel) panel.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
        if (loading) loading.style.display = 'block';
        if (content) content.style.display = 'none';

        try {
            const response = await fetch(`/${getSalonSlug()}/admin/customers/${customerId}/details`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));

                throw new Error(errorData.message || 'Failed to fetch customer details');
            }
            const data = await response.json();

            // Update UI with customer data
            document.getElementById('customerName').textContent = data.name || '-';
            document.getElementById('customerStatus').textContent = data.status || 'Active';
            document.getElementById('customerEmail').textContent = data.email || 'No email';
            document.getElementById('customerMobile').textContent = formatPhoneNumber(data.phone) || 'No phone';
            document.getElementById('customerBirthDate').textContent = data.dob || 'Not set';
            document.getElementById('customerAnniversary').textContent = data.anniversary || 'Not set';

            // Medical Notes
            const medicalNotesContainer = document.getElementById('customerMedicalNotesContainer');
            const addMedicalNotesContainer = document.getElementById('addMedicalNotesContainer');
            const medicalNotesText = document.getElementById('customerMedicalNotes');
            const editCustomerIdInput = document.getElementById('edit_customer_id');

            if (editCustomerIdInput) editCustomerIdInput.value = customerId;

            if (data.medical_notes) {
                if (medicalNotesText) medicalNotesText.textContent = data.medical_notes;
                if (medicalNotesContainer) medicalNotesContainer.style.display = 'block';
                if (addMedicalNotesContainer) addMedicalNotesContainer.style.display = 'none';
            } else {
                if (medicalNotesContainer) medicalNotesContainer.style.display = 'none';
                if (addMedicalNotesContainer) addMedicalNotesContainer.style.display = 'block';
            }

            // Financial Stats
            document.getElementById('customerUnpaidBalance').textContent = `${window.currencySymbol || '₹'} ${parseFloat(data.unpaid_balance || 0).toFixed(2)}`;
            document.getElementById('customerTotalRevenue').textContent = `${window.currencySymbol || '₹'} ${parseFloat(data.total_revenue || 0).toFixed(2)}`;

            // Visit History
            document.getElementById('customerLastVisit').textContent = data.last_visit || 'Never';
            document.getElementById('customerTotalVisits').textContent = data.total_visits || '0';

            // Favorite Staff
            const favoriteStaffSection = document.getElementById('customerFavoriteStaffSection');
            if (data.favorite_staff) {
                document.getElementById('customerFavoriteStaff').textContent = data.favorite_staff;
                if (favoriteStaffSection) favoriteStaffSection.style.display = 'block';
            } else {
                if (favoriteStaffSection) favoriteStaffSection.style.display = 'none';
            }

            // Upcoming Appointments
            const upcomingList = document.getElementById('upcomingAppointmentsList');
            if (upcomingList) {
                if (data.upcoming_appointments && data.upcoming_appointments.length > 0) {
                    upcomingList.innerHTML = data.upcoming_appointments.map(app => `
                        <div class="mb-2 p-2 bg-light rounded">
                            <div class="fw-bold">${app.date} at ${app.time}</div>
                            <div class="small text-muted">${app.service_name} with ${app.staff_name}</div>
                        </div>
                    `).join('');
                } else {
                    upcomingList.innerHTML = '<div class="text-muted">No upcoming appointments</div>';
                }
            }

            // Recent Visits
            const recentList = document.getElementById('lastVisitsList');
            if (recentList) {
                if (data.recent_visits && data.recent_visits.length > 0) {
                    recentList.innerHTML = data.recent_visits.map(visit => `
                        <div class="mb-2 p-2 bg-light rounded">
                            <div class="fw-bold">${visit.date}</div>
                            <div class="small text-muted">${visit.service_name} - ${window.currencySymbol || '₹'}${visit.price}</div>
                        </div>
                    `).join('');
                } else {
                    recentList.innerHTML = '<div class="text-muted">No visit history</div>';
                }
            }

            if (loading) loading.style.display = 'none';
            if (content) content.style.display = 'block';

        } catch (error) {

            if (loading) loading.style.display = 'none';
            if (placeholder) {
                placeholder.style.display = 'block';
                placeholder.querySelector('h6').textContent = 'Error loading details';
            }
        }
    }

    // Wire up customer select change event
    if (customerSelect) {
        // Handle Select2 if it's used
        if (typeof jQuery !== 'undefined' && jQuery(customerSelect).data('select2')) {
            customerSelect.addEventListener('change', function () {
                if (this.value) updateCustomerDetails(this.value);
            });

            jQuery('#customer').on('select2:select', function (e) {
                if (this.value) updateCustomerDetails(this.value);
            });
        } else {
            customerSelect.addEventListener('change', function () {
                if (this.value) updateCustomerDetails(this.value);
            });
        }
    }

    // Expose updateCustomerDetails to window so it can be called from other places if needed
    window.updateCustomerDetails = updateCustomerDetails;

    // Quick Add Customer Logic
    const btnQuickAddCustomer = document.getElementById('btnQuickAddCustomer');
    if (btnQuickAddCustomer) {
        btnQuickAddCustomer.addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('addCustomerModal'));
            modal.show();
        });
    }

    const addCustomerForm = document.getElementById('addCustomerForm');
    if (addCustomerForm) {
        addCustomerForm.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!this.checkValidity()) {
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"][form="addCustomerForm"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Creating...';
            submitBtn.disabled = true;

            fetch(`/${getSalonSlug()}/admin/customers`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(async response => {
                    const result = await response.json();

                    if (response.ok && (result.status === 'success' || result.id)) {
                        const customer = result.customer || result;

                        // Update Select2 or Select
                        if (typeof jQuery !== 'undefined') {
                            const displayText = `${customer.name || customer.first_name + ' ' + (customer.last_name || '')} (${formatPhoneNumber(customer.phone)})`;
                            const newOption = new Option(displayText, customer.id, true, true);
                            jQuery('#customer').append(newOption).trigger('change.select2');
                        } else {
                            const select = document.getElementById('customer');
                            if (select) {
                                const displayText = `${customer.name || customer.first_name + ' ' + (customer.last_name || '')} (${formatPhoneNumber(customer.phone)})`;
                                const option = new Option(displayText, customer.id, true, true);
                                select.add(option, undefined);
                                select.dispatchEvent(new Event('change'));
                            }
                        }

                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addCustomerModal'));
                        if (modal) modal.hide();

                        // Reset form
                        this.reset();
                        this.classList.remove('was-validated');

                        showNotification('Customer created successfully', 'success');
                    } else {
                        // Handle validation errors
                        let errorMessage = result.message || 'Failed to create customer';
                        if (result.errors) {
                            const firstError = Object.values(result.errors)[0];
                            errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                        }
                        throw new Error(errorMessage);
                    }
                })
                .catch(error => {

                    showNotification(error.message, 'error');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });
    }

    // Current page for list view
    let currentPage = 1;

    // Handle staff filter changes (Multi-select)
    const staffFilterAll = document.getElementById('staffFilterAll');
    const staffCheckboxes = document.querySelectorAll('.staff-filter-checkbox');
    const staffFilterLabel = document.getElementById('staffFilterLabel');

    // Flag to prevent circular updates
    let isUpdatingProgrammatically = false;

    function getSelectedStaffIds() {
        try {
            const selected = [];
            // Re-query to ensure we have the latest elements and avoid stale references
            const currentCheckboxes = document.querySelectorAll('.staff-filter-checkbox');

            if (currentCheckboxes && currentCheckboxes.length > 0) {
                currentCheckboxes.forEach((cb, index) => {
                    if (cb) {
                        // Check if checked and value exists
                        if (cb.checked && cb.value !== null && cb.value !== undefined) {
                            selected.push(cb.value);
                        }
                    }
                });
            }
            return selected;
        } catch (error) {

            return [];
        }
    }

    function updateStaffFilterUI() {
        const selectedIds = getSelectedStaffIds();
        const currentCheckboxes = document.querySelectorAll('.staff-filter-checkbox');
        const totalStaff = currentCheckboxes.length;

        // Update "All" checkbox
        if (staffFilterAll) {
            isUpdatingProgrammatically = true;
            staffFilterAll.checked = selectedIds.length === totalStaff && totalStaff > 0;
            staffFilterAll.indeterminate = selectedIds.length > 0 && selectedIds.length < totalStaff;
            isUpdatingProgrammatically = false;
        }

        // Update Label
        if (staffFilterLabel) {
            if (selectedIds.length === totalStaff && totalStaff > 0) {
                staffFilterLabel.textContent = 'All Staff';
            } else if (selectedIds.length === 0) {
                staffFilterLabel.textContent = 'Select Staff';
            } else if (selectedIds.length === 1) {
                // Find the name of the selected staff
                const selectedCb = Array.from(currentCheckboxes).find(cb => cb && cb.checked);
                if (selectedCb) {
                    const label = document.querySelector(`label[for="${selectedCb.id}"]`);
                    staffFilterLabel.textContent = label ? label.textContent.trim() : '1 Selected';
                } else {
                    staffFilterLabel.textContent = '1 Selected';
                }
            } else {
                staffFilterLabel.textContent = `${selectedIds.length} Selected`;
            }
        }

        // Update URL
        const url = new URL(window.location);
        if (selectedIds.length > 0 && selectedIds.length < totalStaff) {
            url.searchParams.set('staff_id', selectedIds.join(','));
        } else {
            url.searchParams.delete('staff_id');
        }
        window.history.pushState({}, '', url);

        // Refresh Calendar
        if (calendar) {
            calendar.refetchResources();
            calendar.refetchEvents();
        }

        // Refresh List View if active
        if (listViewEl && listViewEl.checked) {
            loadListView(1);
        }
    }

    if (staffFilterAll) {
        staffFilterAll.addEventListener('change', function () {
            // Only process if this is a user-initiated change
            if (!isUpdatingProgrammatically) {
                const currentCheckboxes = document.querySelectorAll('.staff-filter-checkbox');
                currentCheckboxes.forEach(cb => cb.checked = this.checked);
                updateStaffFilterUI();
            }
        });

        // Prevent dropdown from closing when clicking the "All" checkbox
        staffFilterAll.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }

    staffCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateStaffFilterUI);

        // Prevent dropdown from closing when clicking individual checkboxes
        cb.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });

    // Initialize filter state from URL if present
    const urlParams = new URLSearchParams(window.location.search);
    const initialStaffIds = urlParams.get('staff_id');
    if (initialStaffIds) {
        const ids = initialStaffIds.split(',');
        if (staffFilterAll) staffFilterAll.checked = false;
        staffCheckboxes.forEach(cb => {
            if (cb) {
                cb.checked = ids.includes(cb.value);
            }
        });
    } else {
        // No URL param means all staff selected (default state from blade template)
        // Ensure "All" checkbox is checked
        if (staffFilterAll) staffFilterAll.checked = true;
    }
    // Don't call updateStaffFilterUI here to avoid updating URL on initial load



    // Fetch appointment settings
    fetch(`/${getSalonSlug()}/admin/api/appointment-settings`)
        .then(response => {
            if (!response.ok) {

                // Return default settings with browser timezone fallback
                return {
                    working_hours_start: '08:00',
                    working_hours_end: '20:00',
                    slot_duration: 30,
                    appointment_buffer_time: 0,
                    timezone: null // Will trigger fallback to browser timezone
                };
            }
            return response.json();
        })
        .catch(error => {

            // Return default settings on error
            return {
                working_hours_start: '08:00',
                working_hours_end: '20:00',
                slot_duration: 30,
                appointment_buffer_time: 0,
                timezone: null
            };
        })
        .then(settings => {
            if (!settings) {

                settings = {
                    working_hours_start: '08:00',
                    working_hours_end: '20:00',
                    slot_duration: 30,
                    appointment_buffer_time: 0,
                    timezone: null
                };
            }
            // Convert salon's configured slot duration from minutes to HH:MM:SS format
            const slotDurationHours = Math.floor(settings.slot_duration / 60);
            const slotDurationMinutes = settings.slot_duration % 60;
            const salonSlotDuration = `${String(slotDurationHours).padStart(2, '0')}:${String(slotDurationMinutes).padStart(2, '0')}:00`;

            // Update global settings
            calendarSettings = settings;

            // Fetch calendar view preference
            const defaultView = settings.calendar_default_view || 'timeGridWeek';

            // Get timezone: use salon timezone with fallback to browser timezone
            const calendarTimezone = getCalendarTimezone(settings.timezone);


            // Get current time for scrollTime
            const now = new Date();
            const currentHours = String(now.getHours()).padStart(2, '0');
            const currentMinutes = String(now.getMinutes()).padStart(2, '0');
            const currentScrollTime = `${currentHours}:${currentMinutes}:00`;

            // Initialize the calendar
            calendar = new Calendar(calendarEl, {
                height: '100%',
                plugins: [timeGridPlugin, dayGridPlugin, interactionPlugin, resourceTimeGridPlugin],
                initialView: defaultView,
                timeZone: calendarTimezone,
                scrollTime: currentScrollTime, // Scroll to current time
                // Use salon time format if available
                slotLabelFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true,
                    meridiem: 'short'
                },
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true,
                    meridiem: 'short'
                },
                headerToolbar: {
                    left: 'prev,next today timeGridWeek,resourceTimeGridDay',
                    center: '',
                    right: 'title'
                },
                editable: true, // Enable drag and drop
                eventResourceEditable: true, // Allow dragging between resources (staff)
                resources: function (fetchInfo, successCallback, failureCallback) {
                    // Get the current date from the calendar if it exists, otherwise use today
                    const currentDate = calendar ? calendar.getDate().toISOString().split('T')[0] : new Date().toISOString().split('T')[0];

                    fetch(`/${getSalonSlug()}/admin/api/staff?date=${currentDate}`)
                        .then(response => response.json())
                        .then(staffData => {
                            if (!Array.isArray(staffData)) {

                                successCallback([]);
                                return;
                            }

                            // Filter based on selection; if nothing selected, show all working staff
                            const selectedIds = getSelectedStaffIds();

                            const filteredStaff = (selectedIds && selectedIds.length > 0)
                                ? staffData.filter(staff => selectedIds.includes(String(staff.id)))
                                : staffData;

                            // Map to FullCalendar resource objects
                            const resources = filteredStaff
                                .filter(staff => staff.is_working !== false) // Filter out non-working staff
                                .map(staff => {
                                    const resource = {
                                        id: String(staff.id),
                                        title: staff.name
                                    };

                                    // Add business hours if available
                                    if (staff.working_hours) {
                                        resource.businessHours = {
                                            startTime: staff.working_hours.start,
                                            endTime: staff.working_hours.end,
                                            daysOfWeek: [0, 1, 2, 3, 4, 5, 6] // Applied to the specific date view
                                        };
                                    } else if (staff.is_working === false) {
                                        // If not working, set business hours to empty to grey out the whole day
                                        // Or we can rely on the global business hours and let the background event handle it?
                                        // Better to set empty business hours so it looks "closed"
                                        resource.businessHours = [];
                                    }

                                    return resource;
                                });


                            successCallback(resources);
                        })
                        .catch(error => {

                            failureCallback(error);
                        });
                },
                datesSet: function (info) {
                    // Refetch resources when the date range changes
                    if (calendar) {

                        calendar.refetchResources();
                        renderDateNav(calendar);
                    }
                },
                slotMinTime: (settings.working_hours_start.length === 5) ? settings.working_hours_start + ':00' : settings.working_hours_start,
                slotMaxTime: (settings.working_hours_end.length === 5) ? settings.working_hours_end + ':00' : settings.working_hours_end,
                slotDuration: '00:05:00', // 5-minute cells (the "grid" lines)
                slotLabelInterval: salonSlotDuration, // Labels follow the salon's slot duration (e.g. 15 or 30 mins)
                snapDuration: '00:05:00', // Allow snapping to 5-minute cells
                selectConstraint: 'businessHours', // Prevent selection outside working hours
                businessHours: {
                    daysOfWeek: [0, 1, 2, 3, 4, 5, 6],
                    startTime: settings.working_hours_start,
                    endTime: settings.working_hours_end,
                },
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                nowIndicator: true, // Show current time indicator
                // selectAllow removed to allow click events on past times

                // Configure day header format to show date (e.g., "Sat Dec 28")
                dayHeaderFormat: { weekday: 'short', month: 'short', day: 'numeric', omitCommas: true },

                // Configure title format to show a clean range or single date
                titleFormat: { year: 'numeric', month: 'short', day: 'numeric' },

                // eventTimeFormat is already defined above
                eventContent: function (arg) {
                    const event = arg.event;

                    // Skip custom rendering for background events
                    if (event.display === 'background') {
                        return null;
                    }

                    const props = event.extendedProps;

                    // Create event content
                    const content = document.createElement('div');
                    content.className = 'fc-event-main-frame';



                    // Add time
                    const time = document.createElement('div');
                    time.className = 'fc-event-time';
                    time.textContent = arg.timeText;

                    // Add title with customer and service
                    const title = document.createElement('div');
                    title.className = 'fc-event-title';
                    title.innerHTML = `
                                        <div class="fw-medium">#${props.bookingId || arg.event.id} - ${props.customer}</div>
                                        <div class="small opacity-75">${props.service}</div>
                                    `;

                    // Add staff if available
                    if (props.staff) {
                        const staff = document.createElement('div');
                        staff.className = 'small opacity-75';
                        staff.innerHTML = `<i class="fas fa-user me-1"></i>${props.staff}`;
                        title.appendChild(staff);
                    }

                    // Add status badge (Priority: Unpaid Alert > Regular Status)
                    const payStatus = (props.payment_status || 'Pending').toLowerCase();
                    const isUnpaid = ['unpaid', 'partially_paid', 'pay_later', 'pending'].includes(payStatus);
                    const isSignificant = (props.pos_sale_id || ['completed', 'staff_completed'].includes((props.status || '').toLowerCase()));

                    if (isUnpaid && isSignificant) {
                        const statusDiv = document.createElement('div');
                        // Use dynamic color from settings
                        const bgColor = arg.event.backgroundColor || '#dc3545';
                        const txtColor = arg.event.textColor || '#ffffff';
                        const borColor = arg.event.borderColor || bgColor;

                        statusDiv.innerHTML = `<span class="badge" style="background-color: ${bgColor}; color: ${txtColor}">Unpaid</span>`;
                        title.appendChild(statusDiv);
                    } else if (props.status) {
                        const now = new Date();
                        const eventEnd = new Date(arg.event.end);
                        let badgeClass = getStatusClass(props.status);

                        // Highlight past pending appointments
                        if (props.status.toLowerCase() === 'pending' && eventEnd < now) {
                            badgeClass = 'danger'; // red badge
                        }
                        const status = document.createElement('div');
                        status.innerHTML = `<span class="badge bg-${badgeClass}">${props.status}</span>`;
                        title.appendChild(status);
                    }

                    content.appendChild(time);
                    content.appendChild(title);

                    return {
                        domNodes: [content]
                    };
                },
                eventDidMount: function (info) {
                    // Force the wrapper background color to match the event color
                    // This is the correct way to color the entire card
                    if (info.event.backgroundColor) {
                        info.el.style.backgroundColor = info.event.backgroundColor;
                    }
                    if (info.event.borderColor) {
                        info.el.style.borderColor = info.event.borderColor;
                    }
                },
                eventClick: function (info) {
                    // Show appointment details in modal
                    const event = info.event;
                    const props = event.extendedProps;
                    const bookingId = props.bookingId || event.id;

                    // Get the modal element
                    const modalElement = document.getElementById('bookingDetailsModal');
                    if (!modalElement) return;

                    // Store booking ID
                    modalElement.setAttribute('data-booking-id', bookingId);

                    // Helper to safely set text content
                    const setText = (id, text) => {
                        const el = document.getElementById(id);
                        if (el) el.textContent = text || '-';
                    };

                    // Helper to safely set HTML content
                    const setHtml = (id, html) => {
                        const el = document.getElementById(id);
                        if (el) el.innerHTML = html || '-';
                    };

                    // Populate Customer Info - matching the user's screenshot format request
                    setText('bookingCustomerHeader', `#${bookingId} - ${props.customer}`); // Removed Service Name

                    const formattedPhone = formatPhoneNumber(props.customer_phone);
                    const phoneText = (formattedPhone && formattedPhone !== 'N/A') ? `(${formattedPhone})` : '';

                    // Determine Status Badge (Priority: Unpaid Alert > Regular Status)
                    let statusHtml = '';
                    const payStatusHeader = (props.payment_status || 'Pending').toLowerCase();
                    const isUnpaidHeader = ['unpaid', 'partially_paid', 'pay_later', 'pending'].includes(payStatusHeader);
                    const isSignifHeader = (props.pos_sale_id || ['completed', 'staff_completed'].includes((props.status || '').toLowerCase()));

                    if (isUnpaidHeader && isSignifHeader) {
                        statusHtml = `<span class="badge bg-danger ms-2">Unpaid</span>`;
                    } else if (props.status) {
                        let statusClass = 'secondary';
                        // Simple fallback map
                        const map = { 'completed': 'success', 'pending': 'warning', 'confirmed': 'info', 'cancelled': 'danger', 'no_show': 'dark', 'arrived': 'warning', 'started': 'primary', 'frozen': 'secondary', 'staff_completed': 'success' };
                        statusClass = map[props.status.toLowerCase()] || 'secondary';

                        const statusLabel = props.status.charAt(0).toUpperCase() + props.status.slice(1).replace('_', ' ');
                        statusHtml = `<span class="badge bg-${statusClass} ms-2">${statusLabel}</span>`;
                    }



                    setHtml('bookingCustomerPhoneHeader', phoneText + statusHtml);
                    setText('bookingIdDisplay', `#${bookingId}`);

                    // Update Status Badge in Header (Optional, but user asked for status next to phone, so this might be redundant but safe to keep/hide)
                    const statusBadge = document.getElementById('bookingStatusBadge');
                    if (statusBadge) {
                        statusBadge.style.display = 'none'; // Hiding since we added it to the header text
                    }

                    // Populate Details Column
                    setText('bookingService', props.service);
                    setHtml('bookingServicePrice', (props.price !== undefined && props.price !== null) ? `${window.currencySymbol || '₹'}${props.price}` : '-');

                    // Format time range
                    const tz = calendar.getOption('timeZone') || 'local';
                    const startDate = new Date(event.startStr);
                    const endDate = new Date(event.endStr);
                    const timeRange = `${formatTimeInTimezone(startDate, tz)} - ${formatTimeInTimezone(endDate, tz)}`;
                    setHtml('bookingTimeRange', `<span class="badge bg-light text-dark border">${timeRange}</span>`);

                    setText('bookingServiceDuration', (props.duration !== undefined && props.duration !== null) ? `${props.duration} mins` : '-');
                    setText('bookingStaff', props.staff);
                    setText('bookingType', props.source === 'online' ? 'Online Booking' : 'In-App Booking');

                    // Payment Status
                    let paymentStatusRaw = props.payment_status || 'Pending';
                    const paymentStatus = paymentStatusRaw; // Fix: Ensure paymentStatus is available for downstream logic (Raise Sale button)
                    const isPaid = paymentStatusRaw.toLowerCase() === 'paid';
                    const isUnpaid = ['unpaid', 'partially_paid', 'pay_later', 'pending'].includes(paymentStatusRaw.toLowerCase());
                    const isSignificant = (props.pos_sale_id || ['completed', 'staff_completed'].includes(props.status));

                    let paymentClass = 'warning';
                    let paymentLabel = paymentStatusRaw.charAt(0).toUpperCase() + paymentStatusRaw.slice(1);

                    if (isPaid) {
                        paymentClass = 'success';
                    } else if (isUnpaid && isSignificant) {
                        paymentClass = 'danger'; // Red for Unpaid + Significant
                        paymentLabel = 'Unpaid'; // Force label to Unpaid
                    }

                    setHtml('bookingPaymentStatus', `<span class="badge bg-${paymentClass}">${paymentLabel}</span>`);

                    // Payment Method
                    const paymentMethodRow = document.getElementById('bookingPaymentMethodRow');
                    if (paymentMethodRow) {
                        if (props.payment_method) {
                            // Format payment method label (e.g. 'cash' -> 'Cash')
                            const methodLabel = props.payment_method.charAt(0).toUpperCase() + props.payment_method.slice(1);
                            setText('bookingPaymentMethod', methodLabel);
                            paymentMethodRow.classList.remove('d-none');
                        } else {
                            paymentMethodRow.classList.add('d-none');
                        }
                    }

                    // Handle Print Receipt Button
                    const printReceiptBtn = document.getElementById('printReceiptBtn');
                    if (printReceiptBtn) {
                        if (props.status && props.status.toLowerCase() === 'completed') {
                            printReceiptBtn.classList.remove('d-none');
                            printReceiptBtn.onclick = function () {
                                // Open receipt in new window
                                const receiptUrl = `/${getSalonSlug()}/admin/bookings/${bookingId}/receipt`;
                                window.open(receiptUrl, '_blank');
                            };
                        } else {
                            printReceiptBtn.classList.add('d-none');
                        }
                    }

                    setText('bookingNotes', props.notes || '-Nil-');
                    setText('bookingCreatedDate', props.created_at || '-');
                    setText('bookingCreatedBy', props.created_by || 'Salon Staff');
                    setText('bookingAddress', props.customer_address || '-');

                    // Update Status Buttons
                    const currentStatus = props.status || 'pending';
                    document.querySelectorAll('.status-btn').forEach(btn => {
                        btn.classList.remove('active');
                        btn.disabled = false;

                        // Highlight current status
                        if (btn.dataset.status === currentStatus) {
                            btn.classList.add('active');
                        }
                    });

                    // Setup Edit Button
                    const editBtn = document.getElementById('editBookingBtn');
                    if (editBtn) {
                        editBtn.classList.remove('d-none');
                        editBtn.onclick = function () {
                            // Close details modal
                            const detailsModal = bootstrap.Modal.getInstance(modalElement);
                            if (detailsModal) detailsModal.hide();

                            // Open edit modal (using existing logic if available, or redirect)
                            const editUrl = `/${getSalonSlug()}/admin/bookings?edit_booking_id=${bookingId}`;
                            window.location.href = editUrl;
                        };
                    }

                    // Setup Cancel Button
                    const cancelBtn = document.getElementById('cancelBookingBtn');
                    if (cancelBtn) {
                        if (currentStatus === 'completed' || currentStatus === 'cancelled') {
                            cancelBtn.classList.add('d-none');
                        } else {
                            cancelBtn.classList.remove('d-none');
                            cancelBtn.onclick = function () {
                                updateBookingStatus(bookingId, 'cancelled');
                            };
                        }
                    }

                    // Setup Freeze Button
                    const freezeBtn = document.getElementById('freezeBookingBtn');
                    if (freezeBtn) {
                        if (currentStatus === 'completed' || currentStatus === 'cancelled') {
                            freezeBtn.classList.add('d-none');
                        } else {
                            freezeBtn.classList.remove('d-none');
                            freezeBtn.onclick = function () {
                                updateBookingStatus(bookingId, 'frozen');
                            };
                        }
                    }



                    // Setup Raise Sale Button
                    let raiseSaleBtn = document.getElementById('raiseSaleBtn');
                    if (raiseSaleBtn) {
                        // Clone button to remove any existing event listeners
                        const newRaiseSaleBtn = raiseSaleBtn.cloneNode(true);
                        raiseSaleBtn.parentNode.replaceChild(newRaiseSaleBtn, raiseSaleBtn);
                        raiseSaleBtn = newRaiseSaleBtn;

                        const saleId = props.pos_sale_id;
                        const salonSlug = getSalonSlug();
                        const isPaid = paymentStatus.toLowerCase() === 'paid';

                        if (saleId && isPaid) {
                            // Sale exists and is paid -> View Sale
                            raiseSaleBtn.innerHTML = '<i class="fas fa-receipt me-1"></i> View Sale';
                            raiseSaleBtn.classList.remove('btn-success', 'btn-primary');
                            raiseSaleBtn.classList.add('btn-info');
                            raiseSaleBtn.onclick = function () {
                                window.location.href = `/${salonSlug}/admin/pos/sales/${saleId}`;
                            };
                            raiseSaleBtn.classList.remove('d-none');
                        } else {
                            // No sale OR sale exists but is NOT paid -> Raise Sale
                            raiseSaleBtn.innerHTML = '<i class="fas fa-cash-register me-1"></i> Raise Sale';
                            raiseSaleBtn.classList.remove('btn-info', 'btn-primary');
                            raiseSaleBtn.classList.add('btn-success');

                            if (isPaid && !saleId) {
                                // If paid but no sale ID, hide to prevent duplicate
                                raiseSaleBtn.classList.add('d-none');
                            } else {
                                raiseSaleBtn.onclick = function () {
                                    checkGroupedBookingStatus(bookingId);
                                };
                                raiseSaleBtn.classList.remove('d-none');
                            }
                        }
                    }

                    // Show the modal
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                },
                events: function (info, successCallback, failureCallback) {
                    try {
                        // Get the selected staff IDs from the filter
                        const selectedStaffIds = getSelectedStaffIds();
                        const staffIdParam = selectedStaffIds.length > 0 ? selectedStaffIds.join(',') : '';

                        // Format dates to YYYY-MM-DD HH:mm:ss format
                        const formatDate = (date) => {
                            if (!date) return '';
                            return date.toISOString().replace('T', ' ').split('.')[0];
                        };

                        // Fetch events from the server
                        const salonSlug = getSalonSlug();
                        const fetchUrl = `/${salonSlug}/admin/api/bookings?start=${formatDate(info.start)}&end=${formatDate(info.end)}${staffIdParam ? `&staff_id=${staffIdParam}` : ''}`;


                        fetch(fetchUrl)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Failed to fetch bookings');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (!Array.isArray(data)) {

                                    failureCallback(new Error('Invalid data format'));
                                    return;
                                }

                                // Filter out any invalid bookings (only require core fields)
                                const validBookings = data.filter(booking =>
                                    booking &&
                                    booking.id &&
                                    booking.start &&
                                    booking.end
                                );

                                const events = validBookings.map(booking => ({
                                    id: booking.id,
                                    title: booking.title || (booking.extendedProps && booking.extendedProps.service)
                                        ? `${booking.title || ''}${booking.extendedProps && booking.extendedProps.service ? ` - ${booking.extendedProps.service}` : ''}`
                                        : `#${booking.id}`,
                                    // Backend now returns times in salon timezone with offset (e.g., 2025-12-14T18:00:00+05:30)
                                    // Pass directly to FullCalendar - it will handle the timezone conversion
                                    start: booking.start,
                                    end: booking.end,
                                    resourceId: booking.extendedProps.staff_id ? String(booking.extendedProps.staff_id) : null, // Convert to string
                                    backgroundColor: booking.backgroundColor,
                                    borderColor: booking.borderColor,
                                    textColor: booking.textColor,
                                    extendedProps: {
                                        customer: booking.extendedProps.customer || booking.title, // Fix: Use raw customer name
                                        service: booking.extendedProps.service,
                                        staff: booking.extendedProps.staff,
                                        notes: booking.extendedProps.notes,
                                        status: booking.extendedProps.status,
                                        bookingId: booking.id,
                                        customer_id: booking.extendedProps.customer_id,
                                        service_id: booking.extendedProps.service_id,
                                        staff_id: booking.extendedProps.staff_id,
                                        customer_dob: booking.extendedProps.customer_dob,
                                        customer_anniversary: booking.extendedProps.customer_anniversary,
                                        customer_phone: booking.extendedProps.customer_phone,
                                        price: booking.extendedProps.price,
                                        duration: booking.extendedProps.duration,
                                        created_at: booking.extendedProps.created_at,
                                        created_by: booking.extendedProps.created_by,
                                        source: booking.extendedProps.source,
                                        pos_sale_id: booking.extendedProps.pos_sale_id, // Fix: Pass POS Sale ID
                                        payment_status: booking.extendedProps.payment_status,
                                        payment_method: booking.extendedProps.payment_method
                                    },
                                    className: booking.className || `fc-event-${booking.extendedProps.status}`,
                                    editable: !['completed', 'staff_completed', 'cancelled'].includes(booking.extendedProps.status),
                                    startEditable: !['completed', 'staff_completed', 'cancelled'].includes(booking.extendedProps.status),
                                    durationEditable: !['completed', 'staff_completed', 'cancelled'].includes(booking.extendedProps.status)
                                }));

                                // Add background event for past times
                                // Use a more robust "now" that respects the calendar's timezone if possible
                                const now = new Date();
                                events.push({
                                    start: info.startStr,
                                    end: now,
                                    display: 'background',
                                    color: '#f1f5f9', // Very light grey/blue for past times
                                    overlap: true,
                                    editable: false,
                                    allDay: false
                                });




                                // Log any filtered out bookings
                                if (validBookings.length !== data.length) {

                                }

                                // Fetch staff data to add overtime background events
                                const currentDate = info.start.toISOString().split('T')[0];
                                fetch(`/${salonSlug}/admin/api/staff?date=${currentDate}`)
                                    .then(r => r.json())
                                    .then(staffData => {
                                        if (Array.isArray(staffData)) {
                                            staffData.forEach(staff => {
                                                if (staff.overtime && staff.overtime.start && staff.overtime.end) {
                                                    const viewStart = new Date(info.start);
                                                    const viewEnd = new Date(info.end);

                                                    for (let d = new Date(viewStart); d < viewEnd; d.setDate(d.getDate() + 1)) {
                                                        const dateStr = d.toISOString().split('T')[0];
                                                        events.push({
                                                            id: `overtime-${staff.id}-${dateStr}`,
                                                            resourceId: String(staff.id),
                                                            start: `${dateStr}T${staff.overtime.start}`,
                                                            end: `${dateStr}T${staff.overtime.end}`,
                                                            display: 'background',
                                                            backgroundColor: 'rgba(255, 193, 7, 0.15)',
                                                            borderColor: 'transparent'
                                                        });
                                                    }
                                                }
                                            });

                                        }
                                        successCallback(events);
                                    })
                                    .catch(err => {

                                        successCallback(events);
                                    });
                            })
                            .catch(error => {

                                failureCallback(error);
                            });
                    } catch (error) {

                        failureCallback(error);
                    }
                },

                eventDidMount: function (info) {
                    // Skip tooltip for background events
                    if (info.event.display === 'background') {
                        return;
                    }

                    // Add tooltip
                    const event = info.event;
                    const props = event.extendedProps;

                    const tooltip = `
                                        <div class="p-2">
                                            <div class="fw-medium mb-1">${props.customer}</div>
                                            <div class="small mb-1">${props.service}</div>
                                            <div class="small mb-1"><i class="fas fa-user me-1"></i>${props.staff}</div>
                                            <div class="small mb-1"><i class="fas fa-clock me-1"></i>${calendar.formatRange(event.start, event.end, {
                        hour: '2-digit',
                        minute: '2-digit',
                        meridiem: 'short',
                        timeZone: calendar.getOption('timeZone')
                    })}</div>
                                            ${props.notes ? `<div class="small"><i class="fas fa-sticky-note me-1"></i>${props.notes}</div>` : ''}
                                        </div>
                                    `;

                    // Initialize tooltip
                    const tooltipInstance = new bootstrap.Tooltip(info.el, {
                        title: tooltip,
                        html: true,
                        placement: 'top',
                        trigger: 'hover',
                        container: 'body'
                    });

                    // Auto-hide tooltip after 4 seconds
                    info.el.addEventListener('shown.bs.tooltip', function () {
                        setTimeout(function () {
                            tooltipInstance.hide();
                        }, 4000);
                    });

                    // Add click event listener to past time slots to show modal
                    const eventEnd = new Date(info.event.end);
                    const now = new Date();
                    if (eventEnd < now) {
                        info.el.style.cursor = 'pointer';
                        info.el.addEventListener('click', () => {
                            const pastTimeModal = new bootstrap.Modal(document.getElementById('pastTimeModal'));
                            pastTimeModal.show();
                        });
                    }
                },
                select: function (info) {


                    // Prevent selection of past time cells
                    const selectedTime = new Date(info.startStr);
                    const now = new Date();
                    if (selectedTime < now) {
                        // Open the past time modal to inform the user
                        const pastTimeModal = new bootstrap.Modal(document.getElementById('pastTimeModal'));
                        pastTimeModal.show();
                        return; // Prevent further processing
                    }

                    // Get the modal element first
                    const bookingModal = document.getElementById('bookingModal');
                    if (!bookingModal) {

                        return;
                    }

                    // Reset form if it exists
                    const bookingForm = document.getElementById('bookingForm');
                    if (bookingForm) {
                        bookingForm.reset();
                    }

                    // Initialize and show modal
                    try {
                        // Create modal instance with proper options
                        const modalOptions = {
                            backdrop: true,
                            keyboard: true,
                            focus: true
                        };
                        const modal = new bootstrap.Modal(bookingModal, modalOptions);

                        // Show modal immediately
                        modal.show();

                        // Reset modal for Create Mode
                        const modalTitle = document.getElementById('bookingModalTitleText');
                        const modalIcon = document.getElementById('bookingModalIcon');
                        const submitBtnText = document.getElementById('createBookingText');
                        const statusField = document.getElementById('statusFieldContainer');
                        const bookingIdInput = document.getElementById('booking_id');

                        if (modalTitle) modalTitle.textContent = 'Create New Appointment';
                        if (modalIcon) {
                            modalIcon.classList.remove('fa-edit');
                            modalIcon.classList.add('fa-plus');
                        }
                        if (submitBtnText) submitBtnText.textContent = 'Create Appointment';
                        if (statusField) statusField.style.display = 'none';
                        if (bookingIdInput) bookingIdInput.value = '';

                        // Clear dynamic rows
                        const servicesList = document.getElementById('selectedServicesList');
                        const packagesList = document.getElementById('selectedPackagesList');
                        if (servicesList) servicesList.innerHTML = '';
                        if (packagesList) packagesList.innerHTML = '';
                        const servicesContainer = document.getElementById('selectedServicesContainer');
                        const packagesContainer = document.getElementById('selectedPackagesContainer');
                        if (servicesContainer) servicesContainer.style.display = 'none';
                        if (packagesContainer) packagesContainer.style.display = 'none';

                        // Format the time for the form using startStr to respect timezone
                        // info.startStr is ISO8601 in the calendar's timezone (e.g. 2025-12-09T10:00:00+05:30)
                        // We need to extract the date and time parts exactly as they appear in the string
                        // because that represents the time slot the user clicked on in the calendar view.

                        const startStr = info.startStr;
                        let formattedDate = '';
                        let formattedTime = '00:00';

                        if (startStr.includes('T')) {
                            // It's a date-time string (e.g., 2025-12-09T14:00:00+05:30)
                            const parts = startStr.split('T');
                            formattedDate = parts[0];
                            // Take the time part up to the timezone offset or end of string
                            // The time part usually looks like 14:00:00+05:30 or 14:00:00
                            const timePart = parts[1];
                            // Extract HH:mm
                            formattedTime = timePart.substring(0, 5);
                        } else {
                            // It's just a date string (e.g., 2025-12-09) - likely from Month view
                            formattedDate = startStr;
                            // Default to start of working hours if available, else 09:00
                            formattedTime = calendarSettings.working_hours_start || '09:00';
                        }



                        // Store selected time and staff in modal dataset for auto-fill in dynamic rows
                        bookingModal.dataset.selectedTime = formattedTime;
                        bookingModal.dataset.selectedStaff = info.resource ? info.resource.id : '';

                        // Set the form values
                        const dateInput = document.getElementById('date');

                        if (dateInput) dateInput.value = formattedDate;

                        // Show loading state (but don't disable date input as it's already set)
                        if (bookingForm) {
                            bookingForm.classList.add('loading');
                            Array.from(bookingForm.elements).forEach(element => {
                                // Don't disable date input - it's already populated
                                if (element.id !== 'date') {
                                    element.disabled = true;
                                }
                            });
                        }

                        // Fetch and populate staff dropdowns
                        fetch(`/${getSalonSlug()}/admin/api/staff?date=${formattedDate}`)
                            .then(r => r.json())
                            .then(employees => {
                                // Store employees globally for row addition
                                window.staffMembers = employees;

                                // Handle date change to refresh staff availability
                                const dateInput = document.getElementById('date');
                                if (dateInput) {
                                    // Remove existing listener to avoid duplicates if any
                                    const newDateInput = dateInput.cloneNode(true);
                                    dateInput.parentNode.replaceChild(newDateInput, dateInput);

                                    newDateInput.addEventListener('change', function () {
                                        const newDate = this.value;
                                        if (!newDate) return;



                                        fetch(`/${getSalonSlug()}/admin/api/staff?date=${newDate}`)
                                            .then(r => r.json())
                                            .then(updatedStaff => {

                                                employees = updatedStaff; // Update the closure variable
                                                window.staffMembers = updatedStaff; // Keep global staff list in sync

                                                // Refresh all existing staff dropdowns
                                                document.querySelectorAll('.service-staff, .package-service-staff').forEach(select => {
                                                    if (!select) return;

                                                    const currentValue = select.value;
                                                    const row = select.closest('.service-row, .package-service-row');
                                                    let allowedStaffIds = null;

                                                    if (row) {
                                                        const serviceId = row.getAttribute('data-service-id');
                                                        if (serviceId) {
                                                            let serviceMeta = null;

                                                            // Try to find service in global services data
                                                            if (Array.isArray(window.servicesData)) {
                                                                serviceMeta = window.servicesData.find(s => String(s.id) === String(serviceId));
                                                            }

                                                            // If not found, try within packages
                                                            if (!serviceMeta && Array.isArray(window.packagesData)) {
                                                                window.packagesData.some(pkg => {
                                                                    if (!pkg.services) return false;
                                                                    const found = pkg.services.find(s => String(s.id) === String(serviceId));
                                                                    if (found) {
                                                                        serviceMeta = found;
                                                                        return true;
                                                                    }
                                                                    return false;
                                                                });
                                                            }

                                                            if (serviceMeta && Array.isArray(serviceMeta.staff_ids) && serviceMeta.staff_ids.length > 0) {
                                                                allowedStaffIds = serviceMeta.staff_ids.map(String);
                                                            }
                                                        }
                                                    }

                                                    select.innerHTML = '<option value="">Select Staff</option>';
                                                    updatedStaff.forEach(staff => {
                                                        const staffIdStr = String(staff.id);

                                                        // If this service has assigned staff, only show those staff
                                                        if (allowedStaffIds && !allowedStaffIds.includes(staffIdStr)) {
                                                            return;
                                                        }

                                                        const option = document.createElement('option');
                                                        option.value = staff.id;
                                                        option.textContent = staff.name + (staff.availability_reason ? ` ${staff.availability_reason}` : '');
                                                        if (staff.is_working === false) {
                                                            option.disabled = true;
                                                            option.style.color = '#999';
                                                        }
                                                        select.appendChild(option);
                                                    });

                                                    // Restore selection if possible
                                                    select.value = currentValue;
                                                });
                                            })

                                    });
                                }

                                // Initialize Select2 on customer dropdown
                                if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                                    jQuery('#customer').select2({
                                        theme: 'bootstrap-5',
                                        dropdownParent: jQuery('#bookingModal'),
                                        placeholder: 'Search and select customer',
                                        allowClear: true,
                                        width: '100%',
                                        ajax: {
                                            url: `/${getSalonSlug()}/admin/api/customers`,
                                            dataType: 'json',
                                            delay: 250,
                                            data: function (params) {
                                                return {
                                                    term: params.term, // search term
                                                    page: params.page
                                                };
                                            },
                                            processResults: function (data, params) {
                                                params.page = params.page || 1;
                                                return {
                                                    results: data.results,
                                                    pagination: {
                                                        more: data.pagination.more
                                                    }
                                                };
                                            },
                                            cache: true
                                        },
                                        minimumInputLength: 0
                                    });

                                    // Handle customer selection to show details
                                    jQuery('#customer').on('change', function () {
                                        const customerId = this.value;
                                        const customerDetailsPanel = document.getElementById('customerDetailsPanel');
                                        const customerDetailsPlaceholder = document.getElementById('customerDetailsPlaceholder');
                                        const customerDetailsLoading = document.getElementById('customerDetailsLoading');
                                        const customerDetailsContent = document.getElementById('customerDetailsContent');

                                        if (!customerId) {
                                            if (customerDetailsPanel) {
                                                customerDetailsPanel.style.display = 'none';
                                            }
                                            if (customerDetailsPlaceholder) {
                                                customerDetailsPlaceholder.style.display = 'block';
                                            }
                                            const availablePackagesContainer = document.getElementById('availablePackagesContainer');
                                            if (availablePackagesContainer) availablePackagesContainer.style.display = 'none';
                                            return;
                                        }

                                        // Fetch available package balances
                                        fetchCustomerPackageBalances(customerId);

                                        // Show loading state
                                        if (customerDetailsPanel) {
                                            customerDetailsPanel.style.display = 'block';
                                        }
                                        if (customerDetailsPlaceholder) {
                                            customerDetailsPlaceholder.style.display = 'none';
                                        }
                                        if (customerDetailsLoading) {
                                            customerDetailsLoading.style.display = 'block';
                                        }
                                        if (customerDetailsContent) {
                                            customerDetailsContent.style.display = 'none';
                                        }

                                        // Fetch customer details
                                        fetch(`/${getSalonSlug()}/admin/customers/${customerId}/details`, {
                                            method: 'GET',
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json',
                                                'Content-Type': 'application/json'
                                            }
                                        })
                                            .then(r => {
                                                if (!r.ok) {
                                                    throw new Error(`HTTP error! status: ${r.status}`);
                                                }
                                                return r.json();
                                            })
                                            .then(data => {


                                                // Hide loading, show content
                                                if (customerDetailsLoading) {
                                                    customerDetailsLoading.style.display = 'none';
                                                }
                                                if (customerDetailsContent) {
                                                    customerDetailsContent.style.display = 'block';
                                                }

                                                if (data && customerDetailsPanel) {
                                                    // Update customer info
                                                    document.getElementById('customerName').textContent = data.name || '-';
                                                    document.getElementById('customerStatus').textContent = data.status || 'Active Customer';
                                                    document.getElementById('customerEmail').textContent = data.email || '-';
                                                    document.getElementById('customerMobile').textContent = formatPhoneNumber(data.phone) || '-';
                                                    document.getElementById('customerBirthDate').textContent = data.dob || '-';
                                                    document.getElementById('customerAnniversary').textContent = data.anniversary || '-';
                                                    document.getElementById('customerLastVisit').textContent = data.last_visit || 'Never';

                                                    // Show favorite/preferred staff only if they served customer more than 5 times
                                                    const favoriteStaffSection = document.getElementById('customerFavoriteStaffSection');
                                                    const favoriteStaffElement = document.getElementById('customerFavoriteStaff');

                                                    if (data.favorite_staff && data.favorite_staff_count && data.favorite_staff_count > 5) {
                                                        if (favoriteStaffElement) {
                                                            favoriteStaffElement.textContent = `${data.favorite_staff} (${data.favorite_staff_count} services)`;
                                                        }
                                                        if (favoriteStaffSection) {
                                                            favoriteStaffSection.style.display = 'block';
                                                        }
                                                    } else {
                                                        if (favoriteStaffSection) {
                                                            favoriteStaffSection.style.display = 'none';
                                                        }
                                                    }

                                                    // Update financial stats (only unpaid balance and total revenue)
                                                    document.getElementById('customerUnpaidBalance').textContent = `${window.currencySymbol || '₹'} ${data.unpaid_balance || '0.00'}`;
                                                    document.getElementById('customerTotalRevenue').textContent = `${window.currencySymbol || '₹'} ${data.total_revenue || '0.00'}`;
                                                    document.getElementById('customerTotalVisits').textContent = data.total_visits || 0;

                                                    // Update upcoming appointments
                                                    const upcomingList = document.getElementById('upcomingAppointmentsList');
                                                    if (upcomingList) {
                                                        if (data.upcoming_appointments && data.upcoming_appointments.length > 0) {
                                                            upcomingList.innerHTML = data.upcoming_appointments.map(apt => `
                                                                <div class="border-bottom pb-1 mb-1">
                                                                    <div>${apt.service_name || '-'} - ${apt.date || ''}${apt.time ? ` (${apt.time})` : ''}</div>
                                                                    <small class="text-muted">${apt.staff_name || '-'}</small>
                                                                </div>
                                                            `).join('');
                                                        } else {
                                                            upcomingList.innerHTML = '<div class="text-muted">No upcoming appointments</div>';
                                                        }
                                                    }

                                                    // Update last visits / recent visits - show only completed appointments (last 5)
                                                    const lastVisitsList = document.getElementById('lastVisitsList');
                                                    if (lastVisitsList) {
                                                        const visits = (data.recent_visits && data.recent_visits.length > 0)
                                                            ? data.recent_visits
                                                            : (data.last_visits || []);

                                                        if (visits.length > 0) {
                                                            // Filter only completed visits and take last 5 when status is available
                                                            const completedVisits = visits
                                                                .filter(visit => !visit.status || visit.status === 'completed')
                                                                .slice(0, 5);

                                                            if (completedVisits.length > 0) {
                                                                lastVisitsList.innerHTML = completedVisits.map(visit => `
                                                                    <div class="border-bottom pb-2 mb-2">
                                                                        <div class="fw-semibold">${visit.service_name || '-'}</div>
                                                                        <small class="text-muted d-block">
                                                                            <i class="fas fa-calendar me-1"></i>${visit.date || ''}
                                                                        </small>
                                                                        <small class="text-muted d-block">
                                                                            <i class="fas fa-user-tie me-1"></i>${visit.staff_name || '-'}</i>
                                                                        </small>
                                                                    </div>
                                                                `).join('');
                                                            } else {
                                                                lastVisitsList.innerHTML = '<div class="text-muted">No completed visits</div>';
                                                            }
                                                        } else {
                                                            lastVisitsList.innerHTML = '<div class="text-muted">No visit history</div>';
                                                        }
                                                    }

                                                    // Show medical notes if available, otherwise show Add button
                                                    const medicalNotesContainer = document.getElementById('customerMedicalNotesContainer');
                                                    const addMedicalNotesContainer = document.getElementById('addMedicalNotesContainer');
                                                    const medicalNotesText = document.getElementById('customerMedicalNotes');
                                                    const editMedicalNotesBtn = document.getElementById('editMedicalNotesBtn');
                                                    const addMedicalNotesBtn = document.getElementById('addMedicalNotesBtn');

                                                    if (data.medical_notes && data.medical_notes.trim() !== '') {
                                                        medicalNotesText.textContent = data.medical_notes;
                                                        if (medicalNotesContainer) medicalNotesContainer.style.display = 'block';
                                                        if (addMedicalNotesContainer) addMedicalNotesContainer.style.display = 'none';
                                                    } else {
                                                        if (medicalNotesContainer) medicalNotesContainer.style.display = 'none';
                                                        if (addMedicalNotesContainer) addMedicalNotesContainer.style.display = 'block';
                                                    }

                                                    // Handle edit medical notes button
                                                    if (editMedicalNotesBtn) {
                                                        editMedicalNotesBtn.onclick = function () {
                                                            const editCustomerId = document.getElementById('edit_customer_id');
                                                            if (editCustomerId) editCustomerId.value = customerId;
                                                            const editMedicalNotes = document.getElementById('edit_medical_notes');
                                                            if (editMedicalNotes) editMedicalNotes.value = data.medical_notes || '';
                                                            const editModal = new bootstrap.Modal(document.getElementById('editCustomerMedicalNotesModal'));
                                                            editModal.show();
                                                        };
                                                    }

                                                    // Handle add medical notes button
                                                    if (addMedicalNotesBtn) {
                                                        addMedicalNotesBtn.onclick = function () {
                                                            const editCustomerId = document.getElementById('edit_customer_id');
                                                            if (editCustomerId) editCustomerId.value = customerId;
                                                            const editMedicalNotes = document.getElementById('edit_medical_notes');
                                                            if (editMedicalNotes) editMedicalNotes.value = ''; // Clear for new note
                                                            const editModal = new bootstrap.Modal(document.getElementById('editCustomerMedicalNotesModal'));
                                                            editModal.show();
                                                        };
                                                    }
                                                }
                                            })
                                            .catch(error => {

                                                // Hide loading and content, show placeholder
                                                if (customerDetailsLoading) {
                                                    customerDetailsLoading.style.display = 'none';
                                                }
                                                if (customerDetailsContent) {
                                                    customerDetailsContent.style.display = 'none';
                                                }
                                                if (customerDetailsPanel) {
                                                    customerDetailsPanel.style.display = 'none';
                                                }
                                                if (customerDetailsPlaceholder) {
                                                    customerDetailsPlaceholder.style.display = 'block';
                                                }
                                            });
                                    });
                                }

                                // Enable the form
                                if (bookingForm) {
                                    bookingForm.classList.remove('loading');
                                    Array.from(bookingForm.elements).forEach(element => {
                                        element.disabled = false;
                                    });
                                }

                                // Add event listener for "Show Slots" buttons
                                document.addEventListener('click', function (e) {
                                    if (e.target.closest('.show-slots-btn')) {
                                        const btn = e.target.closest('.show-slots-btn');
                                        const serviceRow = btn.closest('.service-row, .package-service-row');
                                        const staffSelect = serviceRow.querySelector('.service-staff, .package-service-staff');
                                        const timeInput = serviceRow.querySelector('.service-time, .package-service-time');
                                        const dateInput = document.getElementById('date');

                                        if (!staffSelect || !staffSelect.value) {
                                            showNotification('Please select a staff member first', 'warning');
                                            return;
                                        }

                                        if (!dateInput || !dateInput.value) {
                                            showNotification('Please select a date first', 'warning');
                                            return;
                                        }

                                        // Get available slots from the dropdown
                                        const slots = Array.from(timeInput.options)
                                            .filter(opt => !opt.disabled && opt.value)
                                            .map(opt => opt.value);

                                        if (slots.length === 0) {
                                            showNotification('No available slots for this staff member', 'info');
                                            return;
                                        }

                                        showSlotsModal(slots, timeInput);
                                    }
                                });



                                // Function to show slots modal
                                function showSlotsModal(slots, timeInput) {
                                    // Create modal
                                    const modalDiv = document.createElement('div');
                                    modalDiv.className = 'modal fade';
                                    modalDiv.id = 'slotsModal';
                                    modalDiv.innerHTML = `
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-clock text-primary me-2"></i>
                                                    Available Time Slots
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-2">
                                                    ${slots.map(slot => `
                                                        <div class="col-4 col-md-3">
                                                            <button type="button" class="btn btn-outline-primary w-100 select-slot"
                                                                    data-time="${slot}">
                                                                ${slot}
                                                            </button>
                                                        </div>
                                                    `).join('')}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;

                                    document.body.appendChild(modalDiv);
                                    const bsModal = new bootstrap.Modal(modalDiv);
                                    bsModal.show();

                                    // Handle slot selection
                                    modalDiv.querySelectorAll('.select-slot').forEach(btn => {
                                        btn.addEventListener('click', function () {
                                            timeInput.value = this.getAttribute('data-time');
                                            bsModal.hide();
                                        });
                                    });

                                    // Remove modal from DOM after hiding
                                    modalDiv.addEventListener('hidden.bs.modal', function () {
                                        modalDiv.remove();
                                    });
                                }

                                // Re-enable form elements after successful data load
                                if (bookingForm) {
                                    bookingForm.classList.remove('loading');
                                    Array.from(bookingForm.elements).forEach(element => {
                                        element.disabled = false;
                                    });
                                }
                            }).catch(error => {

                                alert('Error loading form data. Please try again.');
                                if (bookingForm) {
                                    bookingForm.classList.remove('loading');
                                    Array.from(bookingForm.elements).forEach(element => {
                                        element.disabled = false;
                                    });
                                }
                            });
                    } catch (error) {

                        alert('Error opening the booking form. Please try again.');
                    }
                },
                eventDrop: function (info) {
                    // Handle drag and drop rescheduling
                    const event = info.event;
                    const bookingId = event.extendedProps.bookingId || event.id;

                    // Prevent rescheduling for completed or cancelled bookings
                    if (['completed', 'cancelled'].includes(event.extendedProps.status)) {
                        info.revert();
                        return;
                    }

                    // Get new start time
                    const newStart = event.start;

                    // Prevent rescheduling to the past
                    if (newStart < new Date()) {
                        info.revert();
                        alert('Cannot reschedule appointments to the past.');
                        return;
                    }

                    // Get new staff ID
                    let newStaffId = null;

                    // 1. Check if dropped on a specific resource (Resource View)
                    if (info.newResource) {
                        newStaffId = info.newResource.id;
                    }
                    // 2. Check if event has resources assigned (Resource View)
                    else if (event.getResources() && event.getResources().length > 0 && event.getResources()[0]) {
                        newStaffId = event.getResources()[0].id;
                    }
                    // 3. Fallback to existing staff_id from props
                    else {
                        newStaffId = event.extendedProps.staff_id;
                    }

                    // 4. Last resort: check old event props
                    if (!newStaffId && info.oldEvent && info.oldEvent.extendedProps) {
                        newStaffId = info.oldEvent.extendedProps.staff_id;
                    }



                    if (!newStaffId) {

                        info.revert();
                        alert('Cannot reschedule: No staff member assigned. Please edit the appointment to assign a staff member first.');
                        return;
                    }

                    // Send ISO8601 time - use startStr to preserve the timezone offset as seen on the calendar
                    // This prevents the 6-hour shift caused by toISOString() which converts to UTC.
                    const isoDateTime = info.event.startStr;
                    const duration = (event.end - event.start) / (1000 * 60); // duration in minutes

                    const data = {
                        customer_id: event.extendedProps.customer_id,
                        service_id: event.extendedProps.service_id,
                        staff_id: newStaffId,
                        datetime: isoDateTime,
                        duration: duration,
                        status: event.extendedProps.status,
                        notes: event.extendedProps.notes || ''
                    };



                    // Get CSRF token
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Send update request
                    fetch(`/${getSalonSlug()}/admin/bookings/${bookingId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                        .then(async response => {
                            const responseData = await response.json();
                            if (!response.ok) {
                                throw new Error(responseData.message || responseData.error || 'Failed to reschedule appointment');
                            }
                            return responseData;
                        })
                        .then(data => {
                            // Show success modal
                            const successModalEl = document.getElementById('successModal');
                            if (successModalEl) {
                                document.getElementById('successModalTitle').textContent = 'Rescheduled!';
                                document.getElementById('successModalMessage').textContent = 'Appointment rescheduled successfully.';
                                const successModal = new bootstrap.Modal(successModalEl);
                                successModal.show();
                            } else {
                                // Fallback if modal doesn't exist
                                alert('Appointment rescheduled successfully!');
                            }

                            // Refresh calendar to show updated data
                            calendar.refetchEvents();
                        })
                        .catch(error => {

                            alert(error.message || 'Failed to reschedule appointment. Please try again.');

                            // Revert the event to its original position
                            info.revert();
                        });
                },

                eventResize: function (info) {
                    // Handle duration changes via resizing
                    const event = info.event;
                    const bookingId = event.extendedProps.bookingId || event.id;

                    // Prevent resizing for completed or cancelled bookings
                    if (['completed', 'cancelled'].includes(event.extendedProps.status)) {
                        info.revert();
                        return;
                    }

                    // Use startStr to preserve the timezone offset as seen on the calendar
                    const isoDateTime = info.event.startStr;
                    const duration = (event.end - event.start) / (1000 * 60);

                    const data = {
                        customer_id: event.extendedProps.customer_id,
                        service_id: event.extendedProps.service_id,
                        staff_id: event.extendedProps.staff_id,
                        datetime: isoDateTime,
                        duration: duration,
                        status: event.extendedProps.status,
                        notes: event.extendedProps.notes || ''
                    };



                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch(`/${getSalonSlug()}/admin/bookings/${bookingId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                        .then(async response => {
                            const responseData = await response.json();
                            if (!response.ok) {
                                throw new Error(responseData.message || responseData.error || 'Failed to update duration');
                            }
                            return responseData;
                        })
                        .then(data => {
                            // Show success notification
                            if (typeof showNotification === 'function') {
                                showNotification('success', 'Duration updated successfully.');
                            } else {
                                alert('Duration updated successfully!');
                            }
                            calendar.refetchEvents();
                        })
                        .catch(error => {

                            alert(error.message || 'Failed to update duration. Please try again.');
                            info.revert();
                        });
                }
            });

            // Initialize view toggle early so it works even if calendar fails
            try {
                // Initialize view toggle
                setupViewToggle();
            } catch (e) {
            }

            try {
                calendar.render();
                renderDateNav(calendar);
                window.calendar = calendar;
            } catch (error) {
                showNotification('Error loading calendar view', 'error');
            }

            // Date Picker Listener
            const datePicker = document.getElementById('calendarDatePicker');
            if (datePicker) {
                datePicker.addEventListener('change', function () {
                    if (this.value) {
                        calendar.gotoDate(this.value);
                    }
                });
            }

            // Handle edit_booking_id from URL
            const urlParams = new URLSearchParams(window.location.search);
            const editBookingId = urlParams.get('edit_booking_id');
            if (editBookingId) {
                // Wait a bit for everything to be ready
                setTimeout(() => {
                    if (typeof showEditModal === 'function') {
                        showEditModal(editBookingId);
                        // Clean up URL without refreshing
                        const newUrl = window.location.pathname;
                        window.history.replaceState({}, document.title, newUrl);
                    }
                }, 500);
            }

            // Add modal reset handler
            const bookingModal = document.getElementById('bookingModal');
            if (bookingModal) {
                bookingModal.addEventListener('hidden.bs.modal', function () {
                    // Reset form
                    if (bookingForm) {
                        bookingForm.reset();
                        bookingForm.classList.remove('was-validated');
                    }

                    // Clear Select2 selections
                    if (typeof jQuery !== 'undefined') {
                        jQuery('#customer').val(null).trigger('change');
                        jQuery('#service').val(null).trigger('change');
                        jQuery('#package').val(null).trigger('change');
                    }

                    // Clear dynamic service and package rows
                    const servicesList = document.getElementById('selectedServicesList');
                    const packagesList = document.getElementById('selectedPackagesList');
                    if (servicesList) servicesList.innerHTML = '';
                    if (packagesList) packagesList.innerHTML = '';

                    // Hide containers
                    const servicesContainer = document.getElementById('selectedServicesContainer');
                    const packagesContainer = document.getElementById('selectedPackagesContainer');
                    if (servicesContainer) servicesContainer.style.display = 'none';
                    if (packagesContainer) packagesContainer.style.display = 'none';

                    // Show global time container
                    const globalTimeContainer = document.getElementById('globalTimeContainer');
                    if (globalTimeContainer) globalTimeContainer.style.display = 'block';

                    // Clear dataset
                    delete bookingModal.dataset.selectedTime;
                    delete bookingModal.dataset.selectedStaff;

                    // Remove loading state if stuck
                    if (bookingForm) {
                        bookingForm.classList.remove('loading');
                        Array.from(bookingForm.elements).forEach(element => {
                            element.disabled = false;
                        });
                    }
                });
            }

            // Update available times logic in catch block



            // Check if required buttons exist
            if (!createButton || !editButton) {

                return;
            }

            // Initialize global notification system if not already present
            if (!window.notifications) {

                // app.js should handle this, but adding a safe check.
            }

            // View toggle functionality
            function setupViewToggle() {
                if (!calendarViewEl || !listViewEl) return;

                // Set initial view from URL or default to calendar
                const urlParams = new URLSearchParams(window.location.search);
                const view = urlParams.get('view') || 'calendar';

                if (view === 'list') {
                    listViewEl.checked = true;
                    calendarViewContainer.style.display = 'none';
                    listViewContainer.style.display = 'block';
                    document.getElementById('listViewFilters')?.classList.remove('d-none');
                    loadListView();
                } else {
                    calendarViewEl.checked = true;
                    calendarViewContainer.style.display = 'block';
                    listViewContainer.style.display = 'none';
                    document.getElementById('listViewFilters')?.classList.add('d-none');
                }

                // Add event listeners for view toggle
                calendarViewEl.addEventListener('change', function () {
                    if (this.checked) {
                        calendarViewContainer.style.display = 'block';
                        listViewContainer.style.display = 'none';
                        document.getElementById('listViewFilters')?.classList.add('d-none');
                        updateUrl('calendar');
                        calendar.render();
                    }
                });

                listViewEl.addEventListener('change', function () {
                    if (this.checked) {
                        calendarViewContainer.style.display = 'none';
                        listViewContainer.style.display = 'block';
                        document.getElementById('listViewFilters')?.classList.remove('d-none');
                        updateUrl('list');
                        loadListView();
                    }
                });

                // Status and Sort Filter Listeners
                document.getElementById('statusFilter')?.addEventListener('change', () => loadListView(1));
                document.getElementById('sortFilter')?.addEventListener('change', () => loadListView(1));
            }

            // Update URL with view parameter
            function updateUrl(view) {
                const url = new URL(window.location);
                if (view === 'calendar') {
                    url.searchParams.delete('view');
                } else {
                    url.searchParams.set('view', view);
                }
                window.history.pushState({}, '', url);
            }

            // Load list view data
            function loadListView(page = 1) {
                const selectedStaffIds = getSelectedStaffIds();
                const staffId = selectedStaffIds.length > 0 ? selectedStaffIds.join(',') : '';
                const url = new URL(window.location);

                // Show loading state
                bookingsList.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>`;

                // Build query parameters
                const statusFilterVal = document.getElementById('statusFilter')?.value || '';
                const sortByVal = document.getElementById('sortFilter')?.value || 'newest';

                const params = new URLSearchParams({
                    view: 'list',
                    staff_id: staffId || '',
                    status: statusFilterVal,
                    sort_by: sortByVal,
                    page: page,
                    per_page: 10
                });

                // Add any additional filters from URL
                if (url.searchParams.get('status')) {
                    params.set('status', url.searchParams.get('status'));
                }
                if (url.searchParams.get('start_date')) {
                    params.set('start_date', url.searchParams.get('start_date'));
                }
                if (url.searchParams.get('end_date')) {
                    params.set('end_date', url.searchParams.get('end_date'));
                }

                // Fetch data
                fetch(`${window.location.pathname}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update bookings list
                            bookingsList.innerHTML = data.html;

                            // Update pagination
                            listViewPagination.innerHTML = data.pagination;

                            // Update count
                            const start = (data.current_page - 1) * 10 + 1;
                            const end = Math.min(data.current_page * 10, data.total);
                            listViewCount.textContent = `Showing ${start} to ${end} of ${data.total} bookings`;

                            // Add event listeners to pagination links
                            document.querySelectorAll('#listViewPagination .page-link').forEach(link => {
                                link.addEventListener('click', function (e) {
                                    e.preventDefault();
                                    const page = this.getAttribute('data-page') || this.textContent.trim();
                                    loadListView(page);
                                });
                            });

                            // Add event listeners to action buttons
                            setupListEventListeners();
                        }
                    })
                    .catch(error => {

                        bookingsList.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Error loading bookings. Please try again.
                    </td>
                </tr>`;
                    });
            }


            // Setup event listeners for list view actions
            function setupListEventListeners() {
                // Expose showEditModal globally so inline table row cells can trigger it
                window.showEditModal = showEditModal;

                // Edit booking
                document.querySelectorAll('.edit-booking').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        const bookingId = this.getAttribute('data-booking-id');
                        showEditModal(bookingId);
                    });
                });

                // Change status
                document.querySelectorAll('.change-status').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        const bookingId = this.getAttribute('data-booking-id');
                        const status = this.getAttribute('data-status');
                        updateBookingStatus(bookingId, status);
                    });
                });
            }

            // Show edit modal with booking data using the unified bookingModal
            async function showEditModal(bookingId) {
                const bookingModal = document.getElementById('bookingModal');
                if (!bookingModal) return;

                const modal = new bootstrap.Modal(bookingModal);
                modal.show();

                // Update modal for Edit Mode
                const modalTitle = document.getElementById('bookingModalTitleText');
                const modalIcon = document.getElementById('bookingModalIcon');
                const submitBtnText = document.getElementById('createBookingText');
                const statusField = document.getElementById('statusFieldContainer');
                const bookingIdInput = document.getElementById('booking_id');

                if (modalTitle) modalTitle.textContent = 'Edit Appointment';
                if (modalIcon) {
                    modalIcon.classList.remove('fa-plus');
                    modalIcon.classList.add('fa-edit');
                }
                if (submitBtnText) submitBtnText.textContent = 'Save Changes';
                if (statusField) statusField.style.display = 'block';
                if (bookingIdInput) bookingIdInput.value = bookingId;

                // Get form elements
                const form = document.getElementById('bookingForm');
                const customerSelect = document.getElementById('customer');
                const dateInput = document.getElementById('date');
                const statusSelect = document.getElementById('status');
                const notesInput = document.getElementById('notes');

                // Show loading state
                if (form) {
                    form.classList.add('loading');
                    Array.from(form.elements).forEach(element => {
                        element.disabled = true;
                    });
                }

                try {
                    const response = await fetch(`/${getSalonSlug()}/admin/api/bookings/${bookingId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!response.ok) throw new Error('Failed to fetch booking details');
                    const data = await response.json();

                    // FIX: Ensure staff members are loaded for this specific booking date
                    // This is critical because addServiceRow relies on window.staffMembers to populate the staff dropdown
                    if (data.start_time) {
                        try {
                            const bookingDate = data.start_time.split('T')[0];
                            const staffResponse = await fetch(`/${getSalonSlug()}/admin/api/staff?date=${bookingDate}`);
                            if (staffResponse.ok) {
                                const staffData = await staffResponse.json();
                                if (Array.isArray(staffData)) {
                                    window.staffMembers = staffData;

                                }
                            }
                        } catch (e) {

                        }
                    }

                    // Populate basic fields
                    if (document.getElementById('customer')) {
                        const customerSelect = jQuery('#customer');
                        const option = new Option(data.customer.name, data.customer.id, true, true);
                        customerSelect.append(option).trigger('change');
                    }
                    if (document.getElementById('date')) document.getElementById('date').value = data.start_time.split('T')[0];
                    if (document.getElementById('notes')) document.getElementById('notes').value = data.notes || '';
                    if (document.getElementById('status')) document.getElementById('status').value = data.status;

                    // Clear existing rows
                    const servicesList = document.getElementById('selectedServicesList');
                    const packagesList = document.getElementById('selectedPackagesList');
                    if (servicesList) servicesList.innerHTML = '';
                    if (packagesList) packagesList.innerHTML = '';

                    // Populate services
                    if (data.services && data.services.length > 0) {
                        data.services.forEach(s => {
                            // Ensure service exists in window.servicesData (handle inactive services)
                            if (window.servicesData && !window.servicesData.find(ws => ws.id == s.id)) {

                                window.servicesData.push({
                                    id: s.id,
                                    name: s.name,
                                    duration: s.duration,
                                    price: s.price,
                                    category_id: s.category_id,
                                    staff_ids: [] // Allow all staff since we don't have accurate constraints for inactive services
                                });
                            }
                            window.addServiceRow(s.id);
                            // Find the newly added row and set staff/time
                            const rows = document.querySelectorAll('.service-row');
                            const lastRow = rows[rows.length - 1];
                            if (lastRow) {
                                const staffSelect = lastRow.querySelector('.service-staff');
                                const timeSelect = lastRow.querySelector('.service-time');
                                if (staffSelect) staffSelect.value = s.pivot.staff_id;
                                if (timeSelect) {
                                    const time = s.pivot.start_time.split('T')[1].substring(0, 5);
                                    // Set data-desired-value for fetchAvailableTimeSlots to pick up
                                    timeSelect.setAttribute('data-desired-value', time);
                                    // Trigger slot fetch for this row
                                    const dateEl = document.getElementById('date');
                                    const date = dateEl ? dateEl.value : null;
                                    if (staffSelect && staffSelect.value && date) {
                                        fetchAvailableTimeSlots(staffSelect.value, date, s.id, timeSelect, s.booking_id, data.booking_group_id);
                                    }
                                }
                            }
                        });
                    }

                    // Populate packages
                    if (data.packages && data.packages.length > 0) {
                        data.packages.forEach(p => {
                            window.addPackageRow(p.id);
                            // Find the newly added package group
                            const groups = document.querySelectorAll('.package-group');
                            const lastGroup = groups[groups.length - 1];
                            if (lastGroup) {
                                const serviceRows = lastGroup.querySelectorAll('.package-service-row');

                                // First, uncheck all (we will check only those that are in the booking)
                                serviceRows.forEach(row => {
                                    const checkbox = row.querySelector('.package-service-checkbox');
                                    if (checkbox) {
                                        checkbox.checked = false;
                                        checkbox.dispatchEvent(new Event('change'));
                                    }
                                });

                                p.services.forEach(bookedService => {
                                    // Find the row for this service
                                    const row = Array.from(serviceRows).find(r => r.getAttribute('data-service-id') == bookedService.id);
                                    if (row) {
                                        const checkbox = row.querySelector('.package-service-checkbox');
                                        const staffSelect = row.querySelector('.service-staff');
                                        const timeSelect = row.querySelector('.service-time');

                                        if (checkbox) {
                                            checkbox.checked = true;
                                            checkbox.dispatchEvent(new Event('change'));
                                        }

                                        if (staffSelect) staffSelect.value = bookedService.pivot.staff_id;
                                        if (timeSelect) {
                                            const time = bookedService.pivot.start_time.split('T')[1].substring(0, 5);
                                            timeSelect.setAttribute('data-desired-value', time);

                                            const dateEl = document.getElementById('date');
                                            const date = dateEl ? dateEl.value : null;
                                            if (staffSelect && staffSelect.value && date) {
                                                fetchAvailableTimeSlots(staffSelect.value, date, bookedService.id, timeSelect, bookedService.booking_id, data.booking_group_id);
                                            }
                                        }
                                    }
                                });
                            }
                        });
                    }

                    // Enable form
                    if (form) {
                        form.classList.remove('loading');
                        Array.from(form.elements).forEach(element => {
                            element.disabled = false;
                        });
                    }

                    // Update customer details panel
                    if (typeof updateCustomerDetails === 'function') {
                        updateCustomerDetails(data.customer_id);
                    }

                } catch (error) {

                    showNotification('Failed to load booking details', 'error');

                    // Clear loading state
                    if (form) {
                        form.classList.remove('loading');
                        Array.from(form.elements).forEach(element => {
                            element.disabled = false;
                        });
                    }

                    bootstrap.Modal.getInstance(bookingModal).hide();
                }
            }


            // Update booking status
            function updateBookingStatus(bookingId, status) {
                const performUpdate = () => {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch(`/${getSalonSlug()}/admin/bookings/${bookingId}/status`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            status: status
                        })
                    })
                        .then(async response => {
                            const data = await response.json();
                            if (!response.ok) {
                                throw new Error(data.message || 'Failed to update booking status');
                            }
                            return data;
                        })
                        .then(data => {
                            if (data.success) {
                                showNotification(`Booking has been marked as ${status}`, 'success');

                                // Auto close the details modal on cancellation
                                if (status === 'cancelled') {
                                    const detailsModalEl = document.getElementById('bookingDetailsModal');
                                    if (detailsModalEl) {
                                        const detailsModal = bootstrap.Modal.getInstance(detailsModalEl);
                                        if (detailsModal) {
                                            detailsModal.hide();
                                        }
                                    }
                                }

                                // Refresh the current view
                                if (listViewEl && listViewEl.checked) {
                                    loadListView(currentPage);
                                } else {
                                    calendar.refetchEvents();
                                }
                            } else if (data.requires_payment) {
                                // If payment is required, show the payment modal
                                if (typeof window.showPaymentModal === 'function') {
                                    window.showPaymentModal(bookingId, data.amount);
                                } else {
                                    showNotification(data.message || 'Payment information required', 'info');
                                }
                            } else {
                                showNotification(data.message || 'Failed to update booking status', 'error');
                            }
                        })
                        .catch(error => {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Error',
                                    text: error.message || 'Failed to update booking status',
                                    icon: 'error'
                                });
                            } else {
                                showNotification(error.message || 'Failed to update booking status', 'error');
                            }
                        });
                };

                // Determine confirmation details based on status
                let title = 'Are you sure?';
                let text = `Do you want to mark this booking as ${status.replace('_', ' ')}?`;
                let confirmText = 'Yes, update it!';
                let confirmBtnColor = '#4a90e2';

                if (status === 'cancelled') {
                    title = 'Cancel Appointment?';
                    text = 'Are you sure you want to cancel this appointment?';
                    confirmText = 'Yes, cancel it!';
                    confirmBtnColor = '#dc3545';
                } else if (status === 'frozen') {
                    title = 'Freeze Appointment?';
                    text = 'Are you sure you want to freeze this appointment?';
                    confirmText = 'Yes, freeze it!';
                    confirmBtnColor = '#6c757d';
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: title,
                        text: text,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: confirmBtnColor,
                        cancelButtonColor: '#cbd5e0',
                        confirmButtonText: confirmText,
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            performUpdate();
                        }
                    });
                } else {
                    if (confirm(text)) {
                        performUpdate();
                    }
                }
            }

            // Initialize notification system
            const notifications = new NotificationSystem();

            // Initialize searchable dropdowns for multi-service booking
            initSearchableDropdowns();



            // setupViewToggle() moved to before calendar.render()

            // Function to show notification
            function showNotification(message, type = 'info') {
                notifications.show(message, type);
            }

            // Function to show error in form
            function showError(message) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mt-3';
                errorDiv.textContent = message;

                // Remove any existing error messages
                const existingError = document.querySelector('.alert-danger');
                if (existingError) {
                    existingError.remove();
                }

                // Add the error message to the form
                const form = document.querySelector('.modal-body form');
                if (form) {
                    form.insertBefore(errorDiv, form.firstChild);

                    // Scroll to the error message
                    errorDiv.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                } else {
                    showNotification(message, 'error');
                }
            }

            // --- Multi-Service/Package Logic ---

            function initSearchableDropdowns() {
                const serviceInput = document.getElementById('serviceSearchInput');
                const packageInput = document.getElementById('packageSearchInput');
                const serviceResults = document.getElementById('serviceSearchResults');
                const packageResults = document.getElementById('packageSearchResults');

                // Close dropdowns when clicking outside
                document.addEventListener('click', function (e) {
                    if (!e.target.closest('.dropdown-search-wrapper')) {
                        if (serviceResults) serviceResults.style.display = 'none';
                        if (packageResults) packageResults.style.display = 'none';
                    }
                });

                if (serviceInput && window.servicesData) {
                    serviceInput.addEventListener('focus', () => filterServices(''));
                    serviceInput.addEventListener('input', (e) => filterServices(e.target.value));
                }

                if (packageInput && window.packagesData) {
                    packageInput.addEventListener('focus', () => filterPackages(''));
                    packageInput.addEventListener('input', (e) => filterPackages(e.target.value));
                }
            }

            function filterServices(query) {
                const resultsContainer = document.getElementById('serviceSearchResults');
                if (!resultsContainer) return;

                const services = window.servicesData || [];
                const filtered = services.filter(s => s.name.toLowerCase().includes(query.toLowerCase()));

                if (filtered.length === 0) {
                    resultsContainer.innerHTML = '<div class="p-2 text-muted small">No services found</div>';
                } else {
                    resultsContainer.innerHTML = filtered.map(s => `
                        <div class="p-2 border-bottom cursor-pointer hover-bg-light" onclick="addServiceRow(${s.id})">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold">${s.name}</span>
                                <span class="badge bg-secondary text-white" style="font-size: 0.65rem;">${s.category_name || 'Uncategorized'}</span>
                            </div>
                            <div class="small text-muted">${s.duration} min - ${window.currencySymbol || '₹'}${s.price}</div>
                        </div>
                    `).join('');
                }
                resultsContainer.style.display = 'block';
            }

            function filterPackages(query) {
                const resultsContainer = document.getElementById('packageSearchResults');
                if (!resultsContainer) return;

                const packages = window.packagesData || [];
                const filtered = packages.filter(p => p.name.toLowerCase().includes(query.toLowerCase()));

                if (filtered.length === 0) {
                    resultsContainer.innerHTML = '<div class="p-2 text-muted small">No packages found</div>';
                } else {
                    resultsContainer.innerHTML = filtered.map(p => {
                        // Determine package type badge
                        const typeLabel = p.type === 'customizable' ? 'Customizable' : 'Fixed';
                        const typeBadgeClass = p.type === 'customizable' ? 'bg-info' : 'bg-success';

                        return `
                        <div class="p-2 border-bottom cursor-pointer hover-bg-light" onclick="addPackageRow(${p.id})">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold">${p.name}</span>
                                <span class="badge ${typeBadgeClass} text-white" style="font-size: 0.65rem;">${typeLabel}</span>
                            </div>
                            <div class="small text-muted">${window.currencySymbol || '₹'}${p.price}</div>
                        </div>
                    `;
                    }).join('');
                }
                resultsContainer.style.display = 'block';
            }

            // Helper function to get all currently selected time slots in the form
            function getSelectedTimeSlots() {
                const selectedSlots = [];

                // Get all service rows
                document.querySelectorAll('.service-row, .package-service-row').forEach(row => {
                    const timeSelect = row.querySelector('.service-time, .package-service-time');
                    const staffSelect = row.querySelector('.service-staff, .package-service-staff');
                    const duration = parseInt(row.getAttribute('data-service-duration')) || 60;

                    if (timeSelect && timeSelect.value && staffSelect && staffSelect.value) {
                        const checkData = {
                            staffId: staffSelect.value,
                            time: timeSelect.value,
                            duration: duration,
                            element: timeSelect // Store reference to avoid blocking itself
                        };
                        selectedSlots.push(checkData);
                    }
                });

                return selectedSlots;
            }

            // Helper function to check if a time slot overlaps with selected slots
            function isTimeSlotBlocked(slot, staffId, currentElement, selectedSlots) {
                // Convert slot time to minutes for comparison
                const [slotHours, slotMinutes] = slot.split(':').map(Number);
                const slotTimeInMinutes = slotHours * 60 + slotMinutes;

                for (const selected of selectedSlots) {
                    // Skip if it's the same element (don't block itself)
                    if (selected.element === currentElement) continue;

                    // CHECK: We should block overlapping slots REGARDLESS of staff,
                    // because the CUSTOMER is the one being booked and cannot be in two places.
                    // Removed: if (selected.staffId !== staffId) continue;

                    const [selectedHours, selectedMinutes] = selected.time.split(':').map(Number);
                    const selectedTimeInMinutes = selectedHours * 60 + selectedMinutes;
                    const selectedEndTime = selectedTimeInMinutes + selected.duration;

                    // Check if the slot overlaps with the selected time + duration
                    if (slotTimeInMinutes >= selectedTimeInMinutes && slotTimeInMinutes < selectedEndTime) {
                        return true;
                    }

                    // Also check reverse overlap: if the slot + duration overlaps with selected start
                    // (But here we are checking a single point 'slot' as start time. 
                    // To be safe, we assume this slot needs to be free for 'duration' minutes? 
                    // The current logic only checks if 'slot' start time is INSIDE an existing booking.
                    // This is sufficient to prevent starting a new service in the middle of another.)
                }

                return false;
            }

            function fetchCustomerPackageBalances(customerId) {
                const container = document.getElementById('availablePackagesContainer');
                const list = document.getElementById('availablePackagesList');
                if (!container || !list) return;

                list.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div>';
                container.style.display = 'block';



                fetch(`/${getSalonSlug()}/admin/api/customers/${customerId}/package-balances`)
                    .then(r => r.json())
                    .then(packages => {


                        if (!packages || packages.length === 0) {

                            container.style.display = 'none';
                            return;
                        }

                        list.innerHTML = '';
                        let totalAvailableServices = 0;
                        packages.forEach(pkg => {


                            // Strict filter: Only show services that have remaining balance > 0
                            const availableServices = pkg.services.filter(s => {
                                const isAvailable = s.quantity_remaining && s.quantity_remaining > 0;

                                return isAvailable;
                            });



                            if (availableServices.length === 0) {

                                return; // Skip this package if no services are available
                            }

                            availableServices.forEach(service => {
                                const badge = document.createElement('div');
                                badge.className = 'badge bg-info p-2 cursor-pointer hover-shadow d-flex align-items-center gap-2 mb-1 me-1';
                                badge.style.fontSize = '0.85rem';
                                badge.title = `Click to add ${service.name} from ${pkg.name} package`;

                                badge.innerHTML = `
                                    <i class="fas fa-gift"></i>
                                    <span>${pkg.name}: ${service.name} (${service.quantity_remaining} left)</span>
                                    <i class="fas fa-plus-circle ms-1"></i>
                                `;
                                badge.onclick = () => {

                                    // Pass the full package but mark which services are available
                                    const packageToLoad = JSON.parse(JSON.stringify(pkg));
                                    addPackageRow(packageToLoad, service.id, service.package_balance_id); // Pass the specific service ID and balance ID to add
                                };
                                list.appendChild(badge);
                                totalAvailableServices++;
                            });
                        });



                        // Hide container if no services were added
                        if (totalAvailableServices === 0) {

                            container.style.display = 'none';
                        }
                    })
                    .catch(err => {

                        container.style.display = 'none';
                    });
            }

            // Function to fetch available time slots from API
            function fetchAvailableTimeSlots(staffId, date, serviceId, timeSelectElement, bookingId = null, bookingGroupId = null) {
                // Preserve current selection BEFORE clearing the dropdown
                const currentValue = timeSelectElement.value;
                const desiredValue = timeSelectElement.getAttribute('data-desired-value'); // Get desired value from attribute



                // Show loading state
                timeSelectElement.innerHTML = '<option value="">Loading...</option>';
                timeSelectElement.disabled = true;

                const params = new URLSearchParams({
                    staff_id: staffId,
                    date: date
                });

                if (desiredValue) {
                    params.append('desired_time', desiredValue);
                }

                if (serviceId) {
                    params.append('service_id', serviceId);
                }

                if (bookingId) {
                    params.append('booking_id', bookingId);
                }

                if (bookingGroupId) {
                    params.append('booking_group_id', bookingGroupId);
                }

                fetch(`/${getSalonSlug()}/admin/api/available-time-slots?${params.toString()}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        timeSelectElement.innerHTML = '<option value="">Select Time</option>';

                        if (data.message) {
                            // Staff not working or absent
                            timeSelectElement.innerHTML += `<option value="" disabled>${data.message}</option>`;
                            timeSelectElement.disabled = true;
                            return;
                        }

                        const availableSlots = data.available_slots || [];
                        const bookedSlots = data.booked_slots || [];
                        const unavailableSlots = data.unavailable_slots || [];

                        // Ensure desiredValue is in availableSlots to respect user selection from calendar
                        if (desiredValue && !availableSlots.includes(desiredValue)) {
                            // Check if it's not explicitly blocked (optional, but let's be permissive to allow backend validation on save)
                            availableSlots.push(desiredValue);
                            availableSlots.sort();
                        }

                        // Get currently selected time slots in the form
                        const selectedSlots = getSelectedTimeSlots();

                        // Add available slots, but check against form selections
                        availableSlots.forEach(slot => {
                            const isBlockedByForm = isTimeSlotBlocked(slot, staffId, timeSelectElement, selectedSlots);

                            if (!isBlockedByForm) {
                                const option = document.createElement('option');
                                option.value = slot;
                                option.textContent = formatTime(slot);
                                timeSelectElement.appendChild(option);
                            } else {
                                // Still show selected slots but disabled, so user knows why they can't pick it again
                                const option = document.createElement('option');
                                option.value = slot;
                                option.textContent = `${formatTime(slot)} (Selected)`;
                                option.disabled = true;
                                option.style.color = '#999';
                                timeSelectElement.appendChild(option);
                            }
                        });




                        // Booked and Unavailable slots are hidden as requested by user

                        // Restore previous selection if it's still available
                        if (currentValue && Array.from(timeSelectElement.options).some(opt => opt.value === currentValue && !opt.disabled)) {
                            timeSelectElement.value = currentValue;
                            timeSelectElement.setAttribute('data-last-valid-time', currentValue); // Save valid time
                        } else if (desiredValue) {
                            // Try to find exact match or closest available slot
                            const [dHours, dMinutes] = desiredValue.split(':').map(Number);
                            const desiredTotalMinutes = dHours * 60 + dMinutes;

                            let closestOption = null;
                            let minDiff = Infinity;

                            Array.from(timeSelectElement.options).forEach(opt => {
                                if (!opt.value || opt.disabled) return;

                                const [oHours, oMinutes] = opt.value.split(':').map(Number);
                                const optionTotalMinutes = oHours * 60 + oMinutes;
                                const diff = Math.abs(optionTotalMinutes - desiredTotalMinutes);

                                // Use <= to prefer later slots in case of tie (since options are sorted ascending)
                                if (diff <= minDiff) {
                                    minDiff = diff;
                                    closestOption = opt;
                                }
                            });

                            if (closestOption) {
                                timeSelectElement.value = closestOption.value;
                                timeSelectElement.setAttribute('data-last-valid-time', closestOption.value); // Save valid time
                            } else {
                                timeSelectElement.value = '';
                            }
                        } else {
                            timeSelectElement.value = ''; // Clear if previous selection is no longer valid
                        }

                        // Clear the attribute
                        timeSelectElement.removeAttribute('data-desired-value');

                        timeSelectElement.disabled = false;

                        if (availableSlots.length === 0) {
                            timeSelectElement.innerHTML = '<option value="">No available slots</option>';
                            timeSelectElement.disabled = true;
                        }
                    })
                    .catch(error => {

                        timeSelectElement.innerHTML = '<option value="">Error loading slots</option>';
                        timeSelectElement.disabled = true;
                    });
            }

            // Helper function to format time (HH:MM to 12-hour format)
            function formatTime(time) {
                const [hours, minutes] = time.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const displayHour = hour % 12 || 12;
                return `${displayHour}:${minutes} ${ampm}`;
            }

            // Helper function to calculate end time
            function calculateEndTime(startTime, duration) {
                if (!startTime) return null;
                const [hours, minutes] = startTime.split(':').map(Number);
                const date = new Date();
                date.setHours(hours, minutes, 0, 0);
                date.setMinutes(date.getMinutes() + parseInt(duration));
                return date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
            }

            // Helper function to get the last scheduled row
            function getLastScheduledRow() {
                const rows = document.querySelectorAll('.service-row, .package-service-row');
                return rows.length > 0 ? rows[rows.length - 1] : null;
            }

            // Function to refresh all time slot dropdowns
            function refreshAllTimeSlots() {
                const dateInput = document.getElementById('date');
                if (!dateInput || !dateInput.value) return;

                const allRows = document.querySelectorAll('.service-row, .package-service-row');
                let previousEndTime = null;

                allRows.forEach((row) => {
                    const staffSelect = row.querySelector('.service-staff, .package-service-staff');
                    const timeSelect = row.querySelector('.service-time, .package-service-time');
                    const serviceId = row.getAttribute('data-service-id');
                    const duration = parseInt(row.getAttribute('data-service-duration')) || 60;

                    if (staffSelect && staffSelect.value && timeSelect) {
                        // Capture current value BEFORE it gets cleared by fetchAvailableTimeSlots
                        let currentStartTime = timeSelect.value;

                        // Fallback to last valid time if current value is empty (e.g., coming from a "No slots" state)
                        if (!currentStartTime && timeSelect.hasAttribute('data-last-valid-time')) {
                            currentStartTime = timeSelect.getAttribute('data-last-valid-time');
                        }

                        // If we have a previous end time, set it as the desired value for this row
                        if (previousEndTime) {
                            timeSelect.setAttribute('data-desired-value', previousEndTime);
                            // If this row is being auto-updated to follow previous row, its "start time" 
                            // for calculation purposes should be the desired time (previous end time)
                            currentStartTime = previousEndTime;
                        } else if (currentStartTime) {
                            // If no upstream constraint, preserve existing value as desired value
                            timeSelect.setAttribute('data-desired-value', currentStartTime);
                        } else if (timeSelect.getAttribute('data-desired-value')) {
                            // If no value but attribute exists (e.g. from initial load), use that
                            currentStartTime = timeSelect.getAttribute('data-desired-value');
                        }

                        const bookingId = document.getElementById('booking_id')?.value;
                        const bookingGroupId = document.getElementById('booking_group_id')?.value;

                        fetchAvailableTimeSlots(staffSelect.value, dateInput.value, serviceId, timeSelect, bookingId, bookingGroupId);

                        if (currentStartTime) {
                            previousEndTime = calculateEndTime(currentStartTime, duration);
                        } else {
                            previousEndTime = null;
                        }
                    } else {
                        previousEndTime = null;
                    }
                });
            }

            // Expose these to global scope so onclick works
            window.addServiceRow = function (serviceId) {
                const service = window.servicesData.find(s => s.id == serviceId);
                if (!service) return;

                const container = document.getElementById('selectedServicesList');
                const wrapper = document.getElementById('selectedServicesContainer');

                if (container && wrapper) {
                    wrapper.style.display = 'block';
                    const rowId = 'service-row-' + Date.now();

                    // Generate staff options (only staff assigned to this service, if defined)
                    const assignedStaffIds = Array.isArray(service.staff_ids) && service.staff_ids.length > 0
                        ? service.staff_ids.map(String)
                        : null;

                    const staffOptions = (window.staffMembers || [])
                        .filter(s => {
                            if (!assignedStaffIds || assignedStaffIds.length === 0) {
                                return true;
                            }
                            return assignedStaffIds.includes(String(s.id));
                        })
                        .map(s => {
                            const disabled = s.is_working === false ? 'disabled style="color: #999;"' : '';
                            const reason = s.availability_reason ? ` (${s.availability_reason})` : '';
                            return `<option value="${s.id}" ${disabled}>${s.name}${reason}</option>`;
                        }).join('');

                    const html = `
                        <div class="service-row row g-2 mb-2 align-items-end" id="${rowId}" data-service-id="${service.id}" data-service-duration="${service.duration}">
                            <div class="col-md-4">
                                <label class="small text-muted">Service</label>
                                <input type="text" class="form-control form-control-sm" value="${service.name}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted">Staff</label>
                                <select class="form-select form-select-sm service-staff" required>
                                    <option value="">Select Staff</option>
                                    ${staffOptions}
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted">Start Time</label>
                                <div class="input-group input-group-sm">
                                    <select class="form-select form-select-sm service-time" required>
                                        <option value="">Select Time</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary show-slots-btn">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeRow('${rowId}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);

                    // Add event listeners to fetch available slots
                    const row = document.getElementById(rowId);
                    const staffSelect = row.querySelector('.service-staff');
                    const timeSelect = row.querySelector('.service-time');

                    // Auto-select staff and time
                    const allRows = document.querySelectorAll('.service-row, .package-service-row');
                    const currentIndex = Array.from(allRows).indexOf(row);
                    const prevRow = currentIndex > 0 ? allRows[currentIndex - 1] : null;
                    const bookingModal = document.getElementById('bookingModal');

                    // Helper to select best staff
                    const selectBestStaff = () => {
                        if (service.staff_ids && service.staff_ids.length > 0) {
                            // 1. Try to find an assigned staff member who is working
                            const workingAssignedStaff = service.staff_ids.find(id => {
                                const staff = (window.staffMembers || []).find(s => s.id == id);
                                return staff && staff.is_working !== false;
                            });

                            if (workingAssignedStaff) {
                                staffSelect.value = workingAssignedStaff;
                            } else {
                                // 2. Fallback to the first assigned staff member
                                // Ensure the staff member actually exists in the dropdown
                                const firstStaffId = service.staff_ids[0];
                                const exists = (window.staffMembers || []).some(s => s.id == firstStaffId);
                                if (exists) {
                                    staffSelect.value = firstStaffId;
                                }
                            }
                        }
                    };

                    if (prevRow) {
                        // Sequential: Auto-select time based on previous row
                        const lastTimeSelect = prevRow.querySelector('.service-time, .package-service-time');
                        const lastDuration = prevRow.getAttribute('data-service-duration');

                        // Check value OR data-desired-value (in case it's still loading)
                        let lastTime = lastTimeSelect ? lastTimeSelect.value : null;
                        if (!lastTime && lastTimeSelect && lastTimeSelect.getAttribute('data-desired-value')) {
                            lastTime = lastTimeSelect.getAttribute('data-desired-value');
                        }

                        if (lastTime && lastDuration) {
                            const nextStartTime = calculateEndTime(lastTime, lastDuration);
                            if (nextStartTime) {
                                timeSelect.setAttribute('data-desired-value', nextStartTime);
                            }
                        }

                        // Auto-select staff
                        selectBestStaff();
                    } else {

                        // First Row: Check for pre-selected time/staff from calendar click
                        if (bookingModal && bookingModal.dataset.selectedTime) {

                            timeSelect.setAttribute('data-desired-value', bookingModal.dataset.selectedTime);
                        } else {

                        }

                        if (bookingModal && bookingModal.dataset.selectedStaff) {
                            // Prioritize the staff member clicked on the calendar if they are in the options
                            const preselectedStaffId = bookingModal.dataset.selectedStaff;
                            const optionExists = staffSelect.querySelector(`option[value="${preselectedStaffId}"]`);

                            if (optionExists) {
                                staffSelect.value = preselectedStaffId;
                            } else {
                                // Fallback if the clicked staff can't perform this service

                                selectBestStaff();
                            }
                        } else {
                            // Auto-select staff
                            selectBestStaff();
                        }

                    }

                    const fetchSlots = () => {
                        const dateInput = document.getElementById('date');
                        const staffId = staffSelect ? staffSelect.value : null;
                        const date = dateInput?.value;
                        const bookingId = document.getElementById('booking_id')?.value;
                        const bookingGroupId = document.getElementById('booking_group_id')?.value;

                        // Preserve current time selection if available
                        if (timeSelect.value) {
                            timeSelect.setAttribute('data-desired-value', timeSelect.value);
                        }

                        if (staffId && date) {
                            fetchAvailableTimeSlots(staffId, date, service.id, timeSelect, bookingId, bookingGroupId);
                        }
                    };

                    staffSelect.addEventListener('change', () => {
                        refreshAllTimeSlots();
                    });

                    // When time is selected, refresh all other time slots
                    timeSelect.addEventListener('change', () => {
                        if (timeSelect.value) {
                            timeSelect.setAttribute('data-last-valid-time', timeSelect.value);
                        }
                        refreshAllTimeSlots();
                    });

                    // Also listen to date changes
                    const dateInput = document.getElementById('date');
                    if (dateInput) {
                        dateInput.addEventListener('change', () => {
                            if (staffSelect.value) {
                                fetchSlots();
                            }
                        });
                    }

                    // Initial fetch if staff is auto-selected
                    if (staffSelect.value) {
                        fetchSlots();
                    }

                    // Hide dropdown
                    document.getElementById('serviceSearchResults').style.display = 'none';
                    const serviceSearchInput = document.getElementById('serviceSearchInput');
                    if (serviceSearchInput) serviceSearchInput.value = '';
                }
            };

            window.addPackageRow = function (packageData, targetServiceId = null, targetBalanceId = null) {
                let pkg;
                if (typeof packageData === 'object') {
                    pkg = packageData;
                } else {
                    pkg = window.packagesData.find(p => p.id == packageData);
                }

                if (!pkg) return;

                const container = document.getElementById('selectedPackagesList');
                const wrapper = document.getElementById('selectedPackagesContainer');

                if (container && wrapper) {
                    wrapper.style.display = 'block';
                    const packageRowId = 'package-row-' + Date.now();

                    // Check if package is customizable or fixed
                    const isCustomizable = pkg.type === 'customizable';

                    // Filter services to only show those with balance (if balance is tracked)
                    let availableServices = (pkg.services || []).filter(s => s.quantity_remaining === undefined || s.quantity_remaining > 0);

                    // If a specific service was clicked from the balance list, only show that one
                    if (targetBalanceId) {
                        availableServices = availableServices.filter(s => s.package_balance_id == targetBalanceId);
                    } else if (targetServiceId) {
                        availableServices = availableServices.filter(s => s.id == targetServiceId);
                    }

                    let html = `
                        <div class="package-group mb-3 border rounded p-2 bg-white shadow-sm" id="${packageRowId}" data-package-id="${pkg.id}" data-package-type="${pkg.type || 'fixed'}" data-service-limit="${pkg.service_limit || ''}" data-is-new-purchase="${!targetBalanceId}">
                            <!-- Package Header -->
                            <div class="row g-2 mb-2 align-items-center bg-light p-2 rounded mx-0">
                                <div class="col-md-${isCustomizable ? '8' : '10'}">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary rounded-circle p-1 me-2 text-white">
                                            <i class="fas fa-box-open fa-xs"></i>
                                        </div>
                                        <span class="fw-bold text-dark">${pkg.name}</span>
                                        <span class="badge ${isCustomizable ? 'bg-info' : 'bg-success'} text-white" style="font-size: 0.65rem;">
                                            ${isCustomizable ? 'Customizable' : 'Fixed'}
                                        </span>
                                        ${isCustomizable && pkg.service_limit ? `<span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Limit: ${pkg.service_limit}</span>` : ''}
                                    </div>
                                </div>
                                ${isCustomizable ? `
                                <div class="col-md-2 text-end">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input select-all-package-services" type="checkbox" ${isCustomizable && pkg.service_limit && availableServices.length > pkg.service_limit ? '' : 'checked'} id="select-all-${packageRowId}">
                                        <label class="form-check-label small fw-bold" for="select-all-${packageRowId}">All</label>
                                    </div>
                                </div>
                                ` : ''}
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeRow('${packageRowId}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Services within package -->
                            <div class="package-services-list px-2">
                    `;


                    if (availableServices.length > 0) {
                        availableServices.forEach((service, index) => {
                            const serviceRowId = `${packageRowId}-service-${service.id}-${index}`;
                            const isAvailable = true;
                            const balanceText = service.quantity_remaining !== undefined ? ` (${service.quantity_remaining} left)` : '';
                            const isDisabled = '';
                            const isChecked = (isCustomizable && pkg.service_limit && index >= pkg.service_limit) ? '' : 'checked'; // Respect limit by default

                            // Generate staff options for this service only
                            const assignedStaffIds = Array.isArray(service.staff_ids) && service.staff_ids.length > 0
                                ? service.staff_ids.map(String)
                                : null;

                            const staffOptions = (window.staffMembers || [])
                                .filter(s => {
                                    if (!assignedStaffIds || assignedStaffIds.length === 0) {
                                        return true;
                                    }
                                    return assignedStaffIds.includes(String(s.id));
                                })
                                .map(s => {
                                    const disabled = s.is_working === false ? 'disabled style="color: #999;"' : '';
                                    const reason = s.availability_reason ? ` (${s.availability_reason})` : '';
                                    return `<option value="${s.id}" ${disabled}>${s.name}${reason}</option>`;
                                }).join('');

                            html += `
                                <div class="row g-2 mb-2 align-items-end package-service-row pb-2 ${index < availableServices.length - 1 ? 'border-bottom' : ''} ${isDisabled}"
                                     id="${serviceRowId}"
                                     data-package-id="${pkg.id}"
                                     data-service-id="${service.id}"
                                     data-service-name="${service.name}"
                                     data-service-duration="${service.duration || 60}"
                                     data-service-price="${service.price || 0}">
                                    <div class="col-md-4">
                                        <label class="small text-muted fw-bold">${service.name}${balanceText}</label>
                                        <div class="small text-muted">${service.duration || 60} min</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small text-muted fw-bold">Staff</label>
                                        <select class="form-select form-select-sm service-staff package-service-staff" required ${!isAvailable ? 'disabled' : ''}>
                                            <option value="">Select Staff</option>
                                            ${staffOptions}
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small text-muted fw-bold mb-1">Start Time</label>
                                        <div class="input-group input-group-sm">
                                            <select class="form-select form-select-sm service-time package-service-time" required ${!isAvailable ? 'disabled' : ''}>
                                                <option value="">Select Time</option>
                                            </select>
                                            <div class="input-group-text bg-white">
                                                <input class="form-check-input package-service-checkbox mt-0" type="checkbox" ${isChecked} ${!isAvailable ? 'disabled' : ''} value="${service.id}" id="check-${serviceRowId}">
                                            </div>
                                            <button type="button" class="btn btn-outline-primary show-slots-btn" ${!isAvailable ? 'disabled' : ''}>
                                                <i class="fas fa-clock"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }

                    html += `
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);

                    // Add event listeners for each service in the package
                    if (availableServices.length > 0) {
                        const selectAllCheckbox = document.getElementById(`select-all-${packageRowId}`);
                        const serviceCheckboxes = document.querySelectorAll(`#${packageRowId} .package-service-checkbox`);

                        if (selectAllCheckbox) {
                            selectAllCheckbox.addEventListener('change', function () {
                                const isChecked = this.checked;

                                if (isChecked && isCustomizable && pkg.service_limit && availableServices.length > pkg.service_limit) {
                                    this.checked = false;
                                    showNotification(`This package has a limit of ${pkg.service_limit} services. Please select them individually.`, 'info');
                                    return;
                                }

                                serviceCheckboxes.forEach(cb => {
                                    if (!cb.disabled) {
                                        cb.checked = isChecked;
                                        cb.dispatchEvent(new Event('change'));
                                    }
                                });
                            });
                        }

                        availableServices.forEach((service, index) => {
                            const serviceRowId = `${packageRowId}-service-${service.id}-${index}`;
                            const serviceRow = document.getElementById(serviceRowId);
                            if (!serviceRow) return;

                            const staffSelect = serviceRow.querySelector('.service-staff');
                            const timeSelect = serviceRow.querySelector('.service-time');
                            const serviceCheckbox = serviceRow.querySelector('.package-service-checkbox');

                            const fetchSlots = () => {
                                const dateInput = document.getElementById('date');
                                const staffId = staffSelect ? staffSelect.value : null;
                                const date = dateInput?.value;
                                const bookingId = document.getElementById('booking_id')?.value;
                                const bookingGroupId = document.getElementById('booking_group_id')?.value;

                                // Preserve current time selection if available
                                if (timeSelect.value) {
                                    timeSelect.setAttribute('data-desired-value', timeSelect.value);
                                }

                                if (staffId && date) {
                                    fetchAvailableTimeSlots(staffId, date, service.id, timeSelect, bookingId, bookingGroupId);
                                }
                            };

                            // Handle checkbox change
                            serviceCheckbox.addEventListener('change', function () {
                                const isChecked = this.checked;

                                // Enforce service limit for customizable packages
                                if (isCustomizable && pkg.service_limit && isChecked) {
                                    const checkedCount = Array.from(serviceCheckboxes).filter(cb => cb.checked).length;
                                    if (checkedCount > pkg.service_limit) {
                                        this.checked = false;
                                        showNotification(`You can only select up to ${pkg.service_limit} services for this package.`, 'warning');
                                        return;
                                    }
                                }

                                staffSelect.disabled = !isChecked;
                                timeSelect.disabled = !isChecked;
                                staffSelect.required = isChecked;
                                timeSelect.required = isChecked;

                                if (!isChecked) {
                                    staffSelect.value = '';
                                    timeSelect.value = '';
                                    // Remove error classes if any
                                    staffSelect.classList.remove('is-invalid');
                                    timeSelect.classList.remove('is-invalid');
                                } else {
                                    // Re-fetch slots if enabled
                                    if (staffSelect.value) fetchSlots();
                                }

                                // Update select all checkbox state
                                if (selectAllCheckbox) {
                                    const enabledCheckboxes = Array.from(serviceCheckboxes).filter(cb => !cb.disabled);
                                    const allChecked = enabledCheckboxes.length > 0 && enabledCheckboxes.every(cb => cb.checked);
                                    selectAllCheckbox.checked = allChecked;
                                }
                            });

                            // Auto-select staff and time
                            const allRows = document.querySelectorAll('.service-row, .package-service-row');
                            const currentIndex = Array.from(allRows).indexOf(serviceRow);
                            const prevRow = currentIndex > 0 ? allRows[currentIndex - 1] : null;
                            const bookingModal = document.getElementById('bookingModal');

                            // Helper to select best staff
                            const selectBestStaff = () => {
                                if (service.staff_ids && service.staff_ids.length > 0) {
                                    // 1. Try to find an assigned staff member who is working
                                    const workingAssignedStaff = service.staff_ids.find(id => {
                                        const staff = (window.staffMembers || []).find(s => s.id == id);
                                        return staff && staff.is_working !== false;
                                    });

                                    if (workingAssignedStaff) {
                                        staffSelect.value = workingAssignedStaff;
                                    } else {
                                        // 2. Fallback to the first assigned staff member
                                        staffSelect.value = service.staff_ids[0];
                                    }
                                }
                            };

                            if (prevRow) {
                                // Sequential: Auto-select time based on previous row
                                const lastTimeSelect = prevRow.querySelector('.service-time, .package-service-time');
                                const lastDuration = prevRow.getAttribute('data-service-duration');

                                // Check value OR data-desired-value (in case it's still loading)
                                let lastTime = lastTimeSelect ? lastTimeSelect.value : null;
                                if (!lastTime && lastTimeSelect && lastTimeSelect.getAttribute('data-desired-value')) {
                                    lastTime = lastTimeSelect.getAttribute('data-desired-value');
                                }

                                if (lastTime && lastDuration) {
                                    const nextStartTime = calculateEndTime(lastTime, lastDuration);
                                    if (nextStartTime) {
                                        timeSelect.setAttribute('data-desired-value', nextStartTime);
                                    }
                                }

                                // Auto-select staff
                                selectBestStaff();
                            } else {
                                // First Row: Check for pre-selected time/staff from calendar click
                                if (bookingModal && bookingModal.dataset.selectedTime) {
                                    timeSelect.setAttribute('data-desired-value', bookingModal.dataset.selectedTime);
                                }


                                if (bookingModal && bookingModal.dataset.selectedStaff) {
                                    // Prioritize the staff member clicked on the calendar if valid
                                    const preselectedStaffId = bookingModal.dataset.selectedStaff;
                                    const optionExists = staffSelect.querySelector(`option[value="${preselectedStaffId}"]`);

                                    if (optionExists) {
                                        staffSelect.value = preselectedStaffId;
                                    } else {
                                        selectBestStaff();
                                    }
                                } else {
                                    // Auto-select staff
                                    selectBestStaff();
                                }

                            }



                            staffSelect.addEventListener('change', () => {
                                refreshAllTimeSlots();
                            });

                            // When time is selected, refresh all other time slots
                            timeSelect.addEventListener('change', () => {
                                if (timeSelect.value) {
                                    timeSelect.setAttribute('data-last-valid-time', timeSelect.value);
                                }
                                refreshAllTimeSlots();
                            });

                            // Also listen to date changes
                            const dateInput = document.getElementById('date');
                            if (dateInput) {
                                dateInput.addEventListener('change', () => {
                                    if (staffSelect.value) {
                                        fetchSlots();
                                    }
                                });
                            }

                            // Initial fetch if staff is auto-selected
                            if (staffSelect.value) {
                                fetchSlots();
                            }
                        });
                    }

                    document.getElementById('packageSearchResults').style.display = 'none';
                    const packageSearchInput = document.getElementById('packageSearchInput');
                    if (packageSearchInput) packageSearchInput.value = '';
                }
            };

            window.removeRow = function (rowId) {
                const row = document.getElementById(rowId);
                if (row) {
                    const container = row.parentElement;
                    row.remove();
                    // Hide container if empty
                    if (container.children.length === 0) {
                        container.parentElement.parentElement.parentElement.style.display = 'none';
                    }
                }
            };

            // Function to handle form submission
            function handleFormSubmit() {
                const bookingIdInput = document.getElementById('booking_id');
                const bookingId = bookingIdInput?.value;
                const isEditMode = !!bookingId;

                const form = document.getElementById('bookingForm');
                if (!form) {

                    return;
                }

                // Get form elements
                const customerSelect = document.getElementById('customer');
                const dateInput = document.getElementById('date');
                const statusSelect = document.getElementById('status');
                const notesInput = document.getElementById('notes');

                // Validate required fields
                let isValid = true;
                let errorMessage = '';

                if (!customerSelect || !customerSelect.value) {
                    errorMessage += 'Please select a Customer\n';
                    isValid = false;
                }

                if (!dateInput || !dateInput.value) {
                    errorMessage += 'Please select a Date\n';
                    isValid = false;
                }

                // Get selected services from dynamic rows
                const serviceRows = document.querySelectorAll('.service-row');
                const packageServiceRows = document.querySelectorAll('.package-service-row');
                const hasServiceRows = serviceRows.length > 0;
                const hasPackageServiceRows = packageServiceRows.length > 0;

                if (!hasServiceRows && !hasPackageServiceRows) {
                    errorMessage += 'Please select at least one Service or Package\n';
                    isValid = false;
                }

                // Validate each row
                if (hasServiceRows) {
                    serviceRows.forEach((row, index) => {
                        const staffSelect = row.querySelector('.service-staff');
                        const timeInput = row.querySelector('.service-time');
                        if (!staffSelect || !staffSelect.value) {
                            errorMessage += `Please select staff for service ${index + 1}\n`;
                            isValid = false;
                        }
                        if (!timeInput || !timeInput.value) {
                            errorMessage += `Please select time for service ${index + 1}\n`;
                            isValid = false;
                        }
                    });
                }

                if (hasPackageServiceRows) {
                    packageServiceRows.forEach((row, index) => {
                        const checkbox = row.querySelector('.package-service-checkbox');
                        if (checkbox && !checkbox.checked) return; // Skip deselected services

                        const staffSelect = row.querySelector('.package-service-staff');
                        const timeInput = row.querySelector('.package-service-time');
                        if (!staffSelect || !staffSelect.value) {
                            errorMessage += `Please select staff for package service ${index + 1}\n`;
                            isValid = false;
                        }
                        if (!timeInput || !timeInput.value) {
                            errorMessage += `Please select time for package service ${index + 1}\n`;
                            isValid = false;
                        }
                    });
                }

                if (!isValid) {
                    showNotification(errorMessage, 'warning');
                    return;
                }

                // Create appointments array
                const appointments = [];
                const now = new Date();

                if (hasServiceRows) {
                    serviceRows.forEach(row => {
                        const serviceId = row.getAttribute('data-service-id');
                        const serviceDuration = parseInt(row.getAttribute('data-service-duration')) || 60;
                        const staffSelect = row.querySelector('.service-staff');
                        const timeInput = row.querySelector('.service-time');

                        appointments.push({
                            customer_id: customerSelect ? customerSelect.value : null,
                            service_id: serviceId,
                            staff_id: staffSelect ? staffSelect.value : null,
                            date: dateInput ? dateInput.value : null,
                            time: timeInput ? timeInput.value : null,
                            duration: serviceDuration,
                            notes: notesInput?.value || '',
                            status: statusSelect?.value || 'pending'
                        });
                    });
                }

                if (hasPackageServiceRows) {
                    packageServiceRows.forEach(row => {
                        const checkbox = row.querySelector('.package-service-checkbox');
                        if (checkbox && !checkbox.checked) return; // Skip deselected services

                        const packageId = row.getAttribute('data-package-id');
                        const serviceId = row.getAttribute('data-service-id');
                        const serviceDuration = parseInt(row.getAttribute('data-service-duration')) || 60;
                        const staffSelect = row.querySelector('.package-service-staff');
                        const timeInput = row.querySelector('.package-service-time');

                        appointments.push({
                            customer_id: customerSelect ? customerSelect.value : null,
                            service_id: serviceId,
                            package_id: packageId,
                            staff_id: staffSelect ? staffSelect.value : null,
                            date: dateInput ? dateInput.value : null,
                            time: timeInput ? timeInput.value : null,
                            duration: serviceDuration,
                            notes: notesInput?.value || '',
                            status: statusSelect?.value || 'pending',
                            package_service_status: row.closest('.package-group')?.getAttribute('data-is-new-purchase') === 'true' ? 2 : 0
                        });
                    });
                }

                // Show loading state
                form.classList.add('loading');
                Array.from(form.elements).forEach(element => {
                    element.disabled = true;
                });

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const endpoint = isEditMode ? `/${getSalonSlug()}/admin/bookings/${bookingId}` : `/${getSalonSlug()}/admin/bookings`;
                const method = isEditMode ? 'PUT' : 'POST';

                // Send all appointments in the group
                const payload = { bookings: appointments };

                fetch(endpoint, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                    .then(response => response.json().then(data => {
                        if (!response.ok) return Promise.reject(data);
                        return data;
                    }))
                    .then(data => {
                        // Hide modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
                        if (modal) modal.hide();

                        // Reset form
                        form.reset();
                        form.classList.remove('loading');
                        form.classList.remove('was-validated');
                        Array.from(form.elements).forEach(element => {
                            element.disabled = false;
                        });

                        // Clear dynamic rows
                        document.getElementById('selectedServicesList').innerHTML = '';
                        document.getElementById('selectedPackagesList').innerHTML = '';
                        document.getElementById('selectedServicesContainer').style.display = 'none';
                        document.getElementById('selectedPackagesContainer').style.display = 'none';

                        // Refresh calendar
                        calendar.refetchEvents();

                        // Show success message
                        const successModalTitle = document.getElementById('successModalTitle');
                        const successModalMessage = document.getElementById('successModalMessage');
                        if (successModalTitle) successModalTitle.textContent = 'Success!';
                        if (successModalMessage) successModalMessage.textContent = data.message || (isEditMode ? 'Appointment updated successfully!' : 'Appointments created successfully!');
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();
                    })
                    .catch(error => {

                        showNotification(error.message || 'Error saving appointment. Please try again.', 'error');
                        form.classList.remove('loading');
                        Array.from(form.elements).forEach(element => {
                            element.disabled = false;
                        });
                    });
            }

            // Add click handlers for the unified booking button
            if (createButton) {
                createButton.addEventListener('click', () => handleFormSubmit());
            }

            // Handle staff filter changes - already handled above in the first event listener
            // This duplicate listener has been removed to prevent double-firing

            // Helper function to get status badge class
            function getStatusClass(status) {
                return status.toLowerCase();
            }
        });
});

// Handle medical notes form submission from booking modal
document.addEventListener('DOMContentLoaded', function () {
    const medicalNotesForm = document.getElementById('updateCustomerMedicalNotesForm');
    if (medicalNotesForm) {
        medicalNotesForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const editCustomerIdEl = document.getElementById('edit_customer_id');
            const editMedicalNotesEl = document.getElementById('edit_medical_notes');

            if (!editCustomerIdEl || !editMedicalNotesEl) return;

            const customerId = editCustomerIdEl ? editCustomerIdEl.value : null;
            const medicalNotes = editMedicalNotesEl ? editMedicalNotesEl.value : '';
            const submitButton = medicalNotesForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;

            try {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

                const response = await fetch(`/${getSalonSlug()}/admin/customers/${customerId}/update-notes`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: JSON.stringify({
                        medical_notes: medicalNotes
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // Update the medical notes display
                    const medicalNotesText = document.getElementById('customerMedicalNotes');
                    const medicalNotesContainer = document.getElementById('customerMedicalNotesContainer');
                    const addMedicalNotesContainer = document.getElementById('addMedicalNotesContainer');

                    if (medicalNotes && medicalNotes.trim() !== '') {
                        if (medicalNotesText) medicalNotesText.textContent = medicalNotes;
                        if (medicalNotesContainer) medicalNotesContainer.style.display = 'block';
                        if (addMedicalNotesContainer) addMedicalNotesContainer.style.display = 'none';
                    } else {
                        if (medicalNotesContainer) medicalNotesContainer.style.display = 'none';
                        if (addMedicalNotesContainer) addMedicalNotesContainer.style.display = 'block';
                    }

                    // Show success message
                    showNotification('Medical notes updated successfully', 'success');

                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editCustomerMedicalNotesModal'));
                    if (modal) modal.hide();
                } else {
                    throw new Error(data.message || 'Failed to update medical notes');
                }
            } catch (error) {

                showNotification(error.message || 'An error occurred while updating medical notes', 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    }
});