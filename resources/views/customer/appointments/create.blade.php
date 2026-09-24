@extends('layouts.app')

@section('content')
    <div class="booking-page bg-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-calendar-check text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="fw-bold mb-2">Book Your Appointment</h2>
                        <p class="text-muted">Choose your service and preferred time</p>
                    </div>

                    <!-- Booking Form Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <form method="POST" action="{{ route('customer.appointments.store') }}" id="bookingForm">
                                @csrf

                                <!-- Service Selection -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold mb-3">
                                        <i class="fas fa-cut text-primary me-2"></i>
                                        Select Service
                                    </label>
                                    <select name="service_id" id="service" class="form-select form-select-lg" required>
                                        <option value="">Choose your service...</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" data-duration="{{ $service->duration }}"
                                                data-price="{{ $service->price }}">
                                                {{ $service->name }} -
                                                {{ currency_symbol() }}{{ number_format($service->price, 2) }}
                                                ({{ $service->duration }} min)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Service Info Alert -->
                                <div class="alert alert-primary border-0 d-none" id="serviceInfo" role="alert">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-clock fa-2x text-primary me-3"></i>
                                                <div>
                                                    <small class="text-muted d-block">Duration</small>
                                                    <strong id="serviceDuration">0 min</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-dollar-sign fa-2x text-white me-3"></i>
                                                <div>
                                                    <small class="text-muted d-block">Price</small>
                                                    <strong id="servicePrice">{{ currency_symbol() }}0.00</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Date & Time Selection -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-calendar text-primary me-2"></i>
                                            Appointment Date
                                        </label>
                                        <input type="date" name="date" id="date" class="form-control form-control-lg"
                                            required min="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-clock text-primary me-2"></i>
                                            Appointment Time
                                        </label>
                                        <select name="time" id="time" class="form-select form-select-lg" required>
                                            <option value="">Choose time slot...</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-comment-dots text-primary me-2"></i>
                                        Additional Notes <span class="text-muted small">(Optional)</span>
                                    </label>
                                    <textarea name="notes" id="notes" class="form-control" rows="4"
                                        placeholder="Any special requests or requirements..."></textarea>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-calendar-check me-2"></i>
                                        Confirm Booking
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .booking-page {
                min-height: 100vh;
            }

            .form-select-lg,
            .form-control-lg {
                border-radius: 0.5rem;
            }

            .card {
                border-radius: 1rem;
            }

            .btn-primary {
                padding: 0.875rem 2rem;
                font-weight: 600;
            }

            @media (max-width: 768px) {
                .card-body {
                    padding: 1.5rem !important;
                }
            }
        </style>
    @endpush

    @push('styles')
        <style>
            /* Service Card Styles */
            .service-card {
                transition: all 0.2s ease-in-out;
                border: 2px solid transparent;
                cursor: pointer;
            }

            .service-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            }

            .service-card .form-check-input:checked+.form-check-label .card {
                border-color: #0d6efd;
            }

            .service-card .form-check-input:checked+.form-check-label {
                color: #0d6efd;
            }

            /* Form Styles */
            .form-control,
            .form-select {
                border-radius: 0.5rem;
                padding: 0.75rem 1rem;
            }

            .form-control:focus,
            .form-select:focus {
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            }

            /* Button Styles */
            .btn {
                padding: 0.75rem 1.5rem;
                border-radius: 0.5rem;
                font-weight: 500;
            }

            .btn-primary {
                background: linear-gradient(45deg, #0d6efd, #0a58ca);
                border: none;
            }

            .btn-primary:hover {
                background: linear-gradient(45deg, #0a58ca, #084298);
                transform: translateY(-1px);
            }

            /* Alert Styles */
            .alert {
                border-radius: 0.5rem;
                border: none;
            }

            .alert-primary {
                background-color: rgba(13, 110, 253, 0.1);
                color: #0d6efd;
            }

            /* Card Styles */
            .card {
                border-radius: 1rem;
                overflow: hidden;
            }

            /* Form Label Styles */
            .form-label {
                margin-bottom: 0.75rem;
            }

            /* Badge Styles */
            .badge {
                padding: 0.5em 0.75em;
                font-weight: 500;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const serviceSelect = document.getElementById('service');
                const dateInput = document.getElementById('date');
                const timeSelect = document.getElementById('time');
                const serviceInfo = document.getElementById('serviceInfo');

                // Check for date in URL
                const urlParams = new URLSearchParams(window.location.search);
                const dateParam = urlParams.get('date');
                if (dateParam) {
                    dateInput.value = dateParam;
                }

                // Service selection handler
                serviceSelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];

                    if (selectedOption.value) {
                        const duration = selectedOption.dataset.duration;
                        const price = selectedOption.dataset.price;

                        // Update service info card
                        document.getElementById('serviceDuration').textContent = duration + ' min';
                        document.getElementById('servicePrice').textContent = '$' + parseFloat(price).toFixed(2);
                        serviceInfo.classList.remove('d-none');

                        // Fetch available slots if date is selected
                        if (dateInput.value) {
                            fetchAvailableSlots(dateInput.value, selectedOption.value);
                        }
                    } else {
                        serviceInfo.classList.add('d-none');
                        timeSelect.innerHTML = '<option value="">Choose time slot...</option>';
                    }
                });

                // Date change handler
                dateInput.addEventListener('change', function () {
                    const selectedService = serviceSelect.value;
                    if (selectedService) {
                        fetchAvailableSlots(this.value, selectedService);
                    }
                });

                // Fetch available time slots
                function fetchAvailableSlots(date, serviceId) {
                    fetch(`{{ route('customer.api.appointments.available-slots') }}?date=${date}&service_id=${serviceId}`)
                        .then(response => response.json())
                        .then(data => {
                            timeSelect.innerHTML = '<option value="">Choose a time slot</option>';
                            data.forEach(slot => {
                                if (slot.available) {
                                    const option = document.createElement('option');
                                    option.value = slot.time;
                                    option.textContent = slot.time;
                                    timeSelect.appendChild(option);
                                }
                            });
                        })
                        .catch(error => {

                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Failed to fetch available time slots. Please try again.'
                            });
                        });
                }

                // Form submission handler
                document.getElementById('bookingForm').addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.href = '{{ route("customer.appointments.index") }}';
                                });
                            } else {
                                throw new Error('Failed to book appointment');
                            }
                        })
                        .catch(error => {

                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Failed to book appointment. Please try again.'
                            });
                        });
                });
            });
        </script>
    @endpush
@endsection