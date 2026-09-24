@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Appointment Calendar') }}</div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div id="calendar"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">Book Appointment</div>
                                    <div class="card-body">
                                        <form id="bookingForm">
                                            <div class="form-group">
                                                <label for="service_id">Service</label>
                                                <select name="service_id" id="service_id" class="form-control" required>
                                                    <option value="">Select a service</option>
                                                    @foreach($services as $service)
                                                        <option value="{{ $service->id }}">{{ $service->name }} -
                                                            $ {{ $service->price }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="staff_id">Staff</label>
                                                <select name="staff_id" id="staff_id" class="form-control" required>
                                                    <option value="">Select a staff member</option>
                                                    <!-- Staff options will be populated via AJAX -->
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="date">Date</label>
                                                <input type="date" name="date" id="date" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="time">Time</label>
                                                <select name="time" id="time" class="form-control" required>
                                                    <option value="">Select a time</option>
                                                    <!-- Time slots will be populated via AJAX -->
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="notes">Notes (Optional)</label>
                                                <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Book Appointment</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
        @inject('settings', 'App\Services\SettingsService')
        <script>
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
                const calendarEl = document.getElementById('calendar');

                // Get timezone: use salon timezone with fallback to browser timezone
                const salonTimezone = '{{ $settings->get("timezone", "UTC") }}';
                const calendarTimezone = getCalendarTimezone(salonTimezone);


                // Store the calendar instance in a global variable
                window.calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    timeZone: calendarTimezone,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    slotMinTime: '{{ $settings->get("working_hours_start", "08:00") }}',
                    slotMaxTime: '{{ $settings->get("working_hours_end", "20:00") }}',
                    businessHours: {
                        daysOfWeek: [0, 1, 2, 3, 4, 5, 6],
                        startTime: '{{ $settings->get("working_hours_start", "08:00") }}',
                        endTime: '{{ $settings->get("working_hours_end", "20:00") }}',
                    },
                    events: {
                        url: '/customer/api/appointments',
                        method: 'GET',
                        failure: function () {
                            alert('There was an error fetching events!');
                        }
                    },
                    eventClick: function (info) {
                        showAppointmentDetails(info.event.id);
                    },
                    loading: function (isLoading) {
                        // Show/hide loading indicator if needed
                        if (isLoading) {
                            // Show loading
                        } else {
                            // Hide loading
                        }
                    }
                });

                // Render the calendar
                window.calendar.render();

                const serviceSelect = document.getElementById('service_id');
                const staffSelect = document.getElementById('staff_id');
                const dateInput = document.getElementById('date');
                const timeSelect = document.getElementById('time');

                // Fetch staff members when service is selected
                serviceSelect.addEventListener('change', function () {
                    const serviceId = this.value;
                    if (serviceId) {
                        fetch(`/customer/appointments/staff?service_id=${serviceId}`)
                            .then(response => response.json())
                            .then(data => {
                                staffSelect.innerHTML = '<option value="">Select a staff member</option>';
                                data.forEach(staff => {
                                    staffSelect.innerHTML += `<option value="${staff.id}">${staff.name}</option>`;
                                });
                            });
                    } else {
                        staffSelect.innerHTML = '<option value="">Select a staff member</option>';
                    }
                });

                // Fetch available time slots when date is selected
                dateInput.addEventListener('change', function () {
                    const date = this.value;
                    const serviceId = serviceSelect.value;
                    if (date && serviceId) {
                        fetch(`/customer/appointments/available-slots?date=${date}&service_id=${serviceId}`)
                            .then(response => response.json())
                            .then(data => {
                                timeSelect.innerHTML = '<option value="">Select a time</option>';
                                data.forEach(slot => {
                                    if (slot.available) {
                                        timeSelect.innerHTML += `<option value="${slot.time}">${slot.time}</option>`;
                                    }
                                });
                            });
                    } else {
                        timeSelect.innerHTML = '<option value="">Select a time</option>';
                    }
                });

                // Handle form submission
                document.getElementById('bookingForm').addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;

                    // Show loading state
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Booking...';

                    fetch('/customer/appointments', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                alert('Appointment booked successfully!');

                                // Reset the form
                                this.reset();

                                // Refresh the calendar to show the new appointment
                                if (window.calendar) {
                                    window.calendar.refetchEvents();
                                }

                                // If there's a redirect URL, use it
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                }
                            } else {
                                throw new Error(data.message || 'Failed to book appointment');
                            }
                        })
                        .catch(error => {

                            alert(error.message || 'An error occurred while booking the appointment');
                        })
                        .finally(() => {
                            // Reset button state
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalButtonText;
                        });
                });
            });
        </script>
    @endpush

@endsection