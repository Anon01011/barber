import { Calendar } from '@fullcalendar/core';
import resourceTimeGridPlugin from '@fullcalendar/resource-timegrid';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import resourcePlugin from '@fullcalendar/resource';

// Helper function to get browser's timezone
function getBrowserTimezone() {
    try {
        return Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch (e) {
        return 'local';
    }
}

// Helper function to get timezone with salon preference and browser fallback
function getCalendarTimezone(salonTimezone) {
    if (salonTimezone && salonTimezone.trim() !== '' && salonTimezone !== 'UTC') {
        try {
            Intl.DateTimeFormat(undefined, { timeZone: salonTimezone });
            return salonTimezone;
        } catch (e) {

        }
    }
    return getBrowserTimezone();
}

// Helper functions to format date and time in specific timezone
function formatDateInTimezone(date, timezone) {
    return new Intl.DateTimeFormat('en-CA', {
        timeZone: timezone,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).format(date);
}

function formatTimeInTimezone(date, timezone) {
    return new Intl.DateTimeFormat('en-GB', {
        timeZone: timezone,
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    }).format(date);
}

document.addEventListener('DOMContentLoaded', function () {
    // Check if jQuery is available
    if (typeof jQuery === 'undefined') {

        return;
    }


    const calendarEl = document.getElementById('calendar');
    const staffFilterEl = document.getElementById('staffFilter');
    let calendar = null;

    // Create modal HTML
    const modalHTML = `
        <div id="bookingModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="bookingForm">
                            <div class="form-group mb-3">
                                <label for="customer">Customer</label>
                                <select class="form-control" id="customer" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="service">Service</label>
                                <select class="form-control" id="service" name="service_id[]" multiple required>
                                    <option value="">Select Service(s)</option>
                                </select>
                                <small class="form-text text-muted">You can select multiple services. Each service will create a separate appointment.</small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="employee">Employee</label>
                                <select class="form-control" id="employee" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="date">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="time">Time</label>
                                <input type="time" class="form-control" id="time" name="time" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveBooking">Save Appointment</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Add modal to the page
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Initialize the modal
    const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));

    // Initialize Select2 on dropdowns for search functionality
    // Wait for jQuery to be available
    if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
        jQuery('#customer').select2({
            theme: 'bootstrap-5',
            dropdownParent: jQuery('#bookingModal'),
            placeholder: 'Search and select customer',
            allowClear: true,
            width: '100%'
        });

        jQuery('#service').select2({
            theme: 'bootstrap-5',
            dropdownParent: jQuery('#bookingModal'),
            placeholder: 'Search and select service(s)',
            allowClear: true,
            width: '100%',
            multiple: true,
            templateResult: formatServiceOption,
            templateSelection: formatServiceSelection
        });

        jQuery('#employee').select2({
            theme: 'bootstrap-5',
            dropdownParent: jQuery('#bookingModal'),
            placeholder: 'Search and select employee',
            allowClear: true,
            width: '100%'
        });
    }

    // Disable past time slots function
    function disablePastTimeSlots() {
        const now = new Date();
        const cutoffTime = new Date(now.getTime() - 1 * 60 * 1000); // current time minus 1 minute
        const timeSlots = document.querySelectorAll('.fc-timegrid-slot');

        timeSlots.forEach(slot => {
            const slotTimeStr = slot.getAttribute('data-time') || slot.textContent.trim();
            if (!slotTimeStr) return;

            const [hours, minutes] = slotTimeStr.split(':').map(Number);
            const slotDateTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), hours, minutes);

            if (slotDateTime < cutoffTime) {
                slot.classList.add('disabled-past-time');
                slot.style.pointerEvents = 'none';
                slot.style.opacity = '0.5';
            } else {
                slot.classList.remove('disabled-past-time');
                slot.style.pointerEvents = 'auto';
                slot.style.opacity = '1';
            }
        });
    }

    // Variables to store state
    let currentSelectedStaffId = null;
    let customersData = [];
    let servicesData = [];
    let appointmentBufferTime = 0;

    // Fetch staff, settings, customers, and services
    Promise.all([
        fetch('/admin/api/staff').then(response => {
            if (!response.ok) throw new Error('Failed to fetch staff');
            return response.json();
        }),
        fetch('/admin/api/appointment-settings').then(response => {
            if (!response.ok) return {
                working_hours_start: '08:00',
                working_hours_end: '20:00',
                slot_duration: 30,
                appointment_buffer_time: 0
            };
            return response.json();
        }),
        fetch('/admin/api/customers').then(response => response.json()),
        fetch('/admin/api/services').then(response => response.json())
    ])
        .then(([staffData, settings, customers, services]) => {
            if (staffData.status === 'error') {
                calendarEl.innerHTML = '<div class="alert alert-danger">Failed to load staff for calendar: ' + (staffData.error || staffData.message || 'Unknown backend error') + '</div>';
                return;
            }

            // Store data for later use
            customersData = customers;
            servicesData = services;
            appointmentBufferTime = parseInt(settings.appointment_buffer_time) || 0;

            // Populate modal dropdowns once
            const customerSelect = document.getElementById('customer');
            const serviceSelect = document.getElementById('service');
            const employeeSelect = document.getElementById('employee');

            customerSelect.innerHTML = '<option value="">Select Customer</option>';
            customersData.forEach(customer => {
                const option = document.createElement('option');
                option.value = customer.id;
                option.textContent = customer.name;
                customerSelect.appendChild(option);
            });

            serviceSelect.innerHTML = '<option value="">Select Service(s)</option>';
            servicesData.forEach(service => {
                const option = document.createElement('option');
                option.value = service.id;
                option.textContent = service.name;
                option.setAttribute('data-duration', service.duration || 60);
                serviceSelect.appendChild(option);
            });

            // Map staff data to resources
            const staffResources = staffData.map(staff => ({
                id: staff.id,
                title: staff.name
            }));

            // Populate employee dropdown
            employeeSelect.innerHTML = '<option value="">Select Employee</option>';
            staffResources.forEach(staff => {
                const option = document.createElement('option');
                option.value = staff.id;
                option.textContent = staff.title;
                employeeSelect.appendChild(option);
            });

            // Populate staff filter dropdown
            staffFilterEl.innerHTML = '<option value="">All Staff</option>';
            staffResources.forEach(staff => {
                const option = document.createElement('option');
                option.value = staff.id;
                option.textContent = staff.title;
                staffFilterEl.appendChild(option);
            });

            // Convert slot duration from minutes to HH:MM:SS format
            const slotDurationHours = Math.floor(settings.slot_duration / 60);
            const slotDurationMinutes = settings.slot_duration % 60;
            const snapDuration = `${String(slotDurationHours).padStart(2, '0')}:${String(slotDurationMinutes).padStart(2, '0')}:00`;

            // Get timezone: use salon timezone with fallback to browser timezone
            const calendarTimezone = getCalendarTimezone(settings.timezone);


            // Initialize FullCalendar with settings from database
            calendar = new Calendar(calendarEl, {
                plugins: [resourceTimeGridPlugin, dayGridPlugin, timeGridPlugin, listPlugin, resourcePlugin],
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
                initialView: 'resourceTimeGridDay',
                timeZone: calendarTimezone,
                views: {
                    resourceTimeGridDay: {
                        type: 'resourceTimeGrid',
                        duration: { days: 1 },
                        buttonText: 'Day'
                    },
                    resourceTimeGridWeek: {
                        type: 'resourceTimeGrid',
                        duration: { weeks: 1 },
                        buttonText: 'Week'
                    },
                    dayGridMonth: {
                        buttonText: 'Month'
                    },
                    listWeek: {
                        buttonText: 'List'
                    }
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'resourceTimeGridDay,resourceTimeGridWeek,dayGridMonth,listWeek'
                },
                resources: staffResources,
                slotMinTime: settings.working_hours_start + ':00',
                slotMaxTime: settings.working_hours_end + ':00',
                snapDuration: snapDuration,
                nowIndicator: true,
                allDaySlot: false,
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                eventResizableFromStart: true,
                resourceAreaWidth: '150px',
                resourceAreaHeaderContent: 'Staff',
                resourceLabelContent: function (arg) {
                    return arg.resource.title;
                },
                selectConstraint: 'businessHours',
                businessHours: {
                    daysOfWeek: [0, 1, 2, 3, 4, 5, 6],
                    startTime: settings.working_hours_start,
                    endTime: settings.working_hours_end,
                },
                selectAllow: function (selectInfo) {
                    return true;
                },
                dateClick: function (info) {
                    const clickedDate = info.date;
                    const now = new Date();

                    const clickedDateOnly = new Date(clickedDate.getFullYear(), clickedDate.getMonth(), clickedDate.getDate());
                    const nowDateOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());

                    if (clickedDateOnly < nowDateOnly) {
                        const pastTimeModalEl = document.getElementById('pastTimeModal');
                        if (pastTimeModalEl) {
                            const pastTimeModal = new bootstrap.Modal(pastTimeModalEl);
                            pastTimeModal.show();
                        }
                        return;
                    }

                    // Update current selected staff
                    currentSelectedStaffId = info.resource ? info.resource.id : null;
                    const start = info.startStr;

                    // Reset form
                    document.getElementById('bookingForm').reset();

                    // Set date and time using timezone-aware formatting
                    document.getElementById('date').value = formatDateInTimezone(clickedDate, calendarTimezone);
                    document.getElementById('time').value = formatTimeInTimezone(clickedDate, calendarTimezone);

                    // Pre-select employee if staff was clicked
                    if (currentSelectedStaffId && typeof jQuery !== 'undefined') {
                        jQuery('#employee').val(currentSelectedStaffId).trigger('change');
                    }

                    bookingModal.show();
                }
            });

            calendar.render();

            // Add click event listener to time slots after calendar is rendered
            setTimeout(() => {
                const timeSlots = document.querySelectorAll('.fc-timegrid-slot');
                timeSlots.forEach(slot => {
                    slot.style.cursor = 'pointer';
                    slot.addEventListener('click', function (e) {
                        if (slot.classList.contains('disabled-past-time')) {
                            const pastTimeModalEl = document.getElementById('pastTimeModal');
                            if (pastTimeModalEl) {
                                const pastTimeModal = new bootstrap.Modal(pastTimeModalEl);
                                pastTimeModal.show();
                            }
                        }
                    });
                });
            }, 1000);

            // Call disablePastTimeSlots after calendar render and on date changes
            calendar.on('datesSet', function () {
                disablePastTimeSlots();
            });

            // Also call after initial render
            disablePastTimeSlots();
        })
        .catch(error => {

            calendarEl.innerHTML = '<div class="alert alert-danger">Failed to load staff for calendar: ' + (error.message || 'Unknown error') + '</div>';
        });

    // Handle save button click - Defined ONCE outside dateClick
    document.getElementById('saveBooking').onclick = function () {
        const form = document.getElementById('bookingForm');
        const formData = new FormData(form);

        // Get selected services (multi-select)
        const serviceSelect = document.getElementById('service');
        const selectedServices = Array.from(serviceSelect.selectedOptions).map(option => ({
            id: option.value,
            name: option.text,
            duration: parseInt(option.getAttribute('data-duration')) || 60
        }));

        if (selectedServices.length === 0) {
            alert('Please select at least one service');
            return;
        }

        // Get employee_id from the form
        const employeeId = document.getElementById('employee').value;
        if (!employeeId) {
            alert('Please select an employee');
            return;
        }

        // Get customer_id
        const customerId = document.getElementById('customer').value;
        if (!customerId) {
            alert('Please select a customer');
            return;
        }

        // Get date and time
        const date = document.getElementById('date').value;
        const time = document.getElementById('time').value;
        const notes = document.getElementById('notes').value;

        if (!date || !time) {
            alert('Please select date and time');
            return;
        }

        // Create appointments for each selected service
        const appointments = [];
        let currentTime = new Date(`${date}T${time}`);

        selectedServices.forEach((service, index) => {
            const startTime = new Date(currentTime);
            const endTime = new Date(currentTime.getTime() + service.duration * 60000);

            appointments.push({
                customer_id: customerId,
                service_id: service.id,
                staff_id: employeeId,
                date: date,
                time: startTime.toTimeString().slice(0, 5),
                duration: service.duration,
                notes: notes,
                status: 'pending'
            });

            // Update current time for next service, including buffer time
            currentTime = new Date(endTime.getTime() + appointmentBufferTime * 60000);
        });

        // Show loading state
        const saveButton = document.getElementById('saveBooking');
        const originalText = saveButton.textContent;
        saveButton.disabled = true;
        saveButton.textContent = 'Creating appointments...';

        // Send all appointments to the server
        const promises = appointments.map(appointment =>
            fetch('/admin/api/bookings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(appointment)
            }).then(response => response.json())
        );

        Promise.all(promises)
            .then(results => {
                const errors = results.filter(r => r.error || r.message !== 'Booking created successfully');

                if (errors.length > 0) {
                    alert(`Created ${results.length - errors.length} appointments. ${errors.length} failed.`);
                } else {
                    alert(`Successfully created ${results.length} appointment(s)!`);
                }

                // Reset button state
                saveButton.disabled = false;
                saveButton.textContent = originalText;

                // Hide modal and refresh calendar
                bookingModal.hide();
                if (calendar) {
                    calendar.refetchEvents();
                }

                // Reset form
                form.reset();
                if (typeof jQuery !== 'undefined') {
                    jQuery('#customer').val(null).trigger('change');
                    jQuery('#service').val(null).trigger('change');
                    jQuery('#employee').val(null).trigger('change');
                }
            })
            .catch(error => {

                alert('Error creating appointments. Please try again.');
                saveButton.disabled = false;
                saveButton.textContent = originalText;
            });
    };

    // Refetch events when staff filter changes
    staffFilterEl.addEventListener('change', function () {
        if (calendar) {
            calendar.refetchEvents();
        }
    });
});

