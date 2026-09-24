@inject('settings', 'App\Services\SettingsService')
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1 fw-bold text-gray-800">
                                    <i class="fas fa-check-double me-2 text-success"></i>Completed Appointments
                                </h4>
                                <p class="text-muted mb-0">History of your completed services</p>
                            </div>
                            <div class="btn-group shadow-sm">
                                <a href="{{ route('employee.appointments.index', ['salon_slug' => optional(auth()->user()->salon)->slug]) }}"
                                    class="btn btn-outline-primary bg-white">
                                    <i class="fas fa-list me-2"></i>All
                                </a>
                                <a href="{{ route('employee.appointments.today', ['salon_slug' => optional(auth()->user()->salon)->slug]) }}"
                                    class="btn btn-outline-primary bg-white">
                                    <i class="fas fa-calendar-day me-2"></i>Today
                                </a>
                                <a href="{{ route('employee.appointments.upcoming', ['salon_slug' => optional(auth()->user()->salon)->slug]) }}"
                                    class="btn btn-outline-primary bg-white">
                                    <i class="fas fa-calendar-week me-2"></i>Upcoming
                                </a>
                                <a href="{{ route('employee.appointments.completed', ['salon_slug' => optional(auth()->user()->salon)->slug]) }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-check-circle me-2"></i>Completed
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments List -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        @if($appointments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th
                                                class="px-4 py-3 border-0 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                Date & Time</th>
                                            <th
                                                class="py-3 border-0 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                Customer</th>
                                            <th
                                                class="py-3 border-0 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                Service</th>
                                            <th
                                                class="py-3 border-0 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                Payment</th>
                                            <th
                                                class="py-3 border-0 text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-end px-4">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($appointments as $appointment)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="d-flex flex-column">
                                                        <span
                                                            class="fw-bold text-dark">{{ $appointment->start_time->format('M d, Y') }}</span>
                                                        <span
                                                            class="text-muted small">{{ $appointment->start_time->format('h:i A') }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($appointment->customer->name ?? 'Guest') }}&background=random&size=32"
                                                            alt="{{ $appointment->customer->name ?? 'Guest' }}"
                                                            class="rounded-circle me-2 shadow-sm"
                                                            style="width: 32px; height: 32px;">
                                                        <div>
                                                            <div class="fw-bold text-dark">
                                                                {{ $appointment->customer->name ?? 'Guest' }}</div>
                                                            <div class="text-muted small">{{ $appointment->customer->email ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold text-dark">{{ $appointment->service->name }}</span>
                                                        @if($appointment->package)
                                                            <span
                                                                class="badge bg-purple-subtle text-purple-emphasis rounded-pill mt-1 w-auto align-self-start"
                                                                style="background-color: #f3e8ff; color: #7c3aed; font-size: 0.7em;">
                                                                <i class="fas fa-box me-1"></i>{{ $appointment->package->name }}
                                                            </span>
                                                        @endif
                                                        <span class="text-muted small mt-1">{{ currency_symbol() }}
                                                            {{ number_format($appointment->amount, 2) }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span
                                                        class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                        Paid
                                                    </span>
                                                </td>
                                                <td class="py-3 text-end px-4">
                                                    <button type="button" class="btn btn-sm btn-light text-primary hover-shadow"
                                                        onclick="viewAppointment({{ $appointment->id }})" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light-subtle">
                                <div class="text-muted small">
                                    Showing {{ $appointments->firstItem() ?? 0 }} to {{ $appointments->lastItem() ?? 0 }} of
                                    {{ $appointments->total() }} entries
                                </div>
                                <div>
                                    {{ $appointments->withQueryString()->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <div class="avatar avatar-xl bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center"
                                        style="width: 80px; height: 80px;">
                                        <i class="fas fa-check-circle fa-3x text-muted opacity-50"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted fw-normal">No completed appointments found</h5>
                                <p class="text-muted small">Your history will appear here.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Appointment Modal -->
    <div class="modal fade" id="viewAppointmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Appointment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="appointmentModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .hover-shadow:hover {
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                transform: translateY(-1px);
            }

            .text-xs {
                font-size: 0.75rem;
            }

            .bg-purple-subtle {
                background-color: #f3e8ff !important;
            }

            .text-purple-emphasis {
                color: #7c3aed !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function getSalonSlug() {
                const userSalonSlug = '{{ auth()->user()->salon->slug ?? "" }}';
                if (userSalonSlug) return userSalonSlug;
                const pathParts = window.location.pathname.split('/').filter(p => p);
                return pathParts[0] || '';
            }

            const currencySymbol = "{{ $settings->get('currency_symbol', '$', auth()->user()->salon_id) }}";

            // View Appointment Details
            function viewAppointment(id) {
                const modal = new bootstrap.Modal(document.getElementById('viewAppointmentModal'));
                const modalBody = document.getElementById('appointmentModalBody');

                modalBody.innerHTML = `
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading details...</p>
                            </div>
                        `;

                modal.show();
                const salonSlug = getSalonSlug();

                fetch(`/${salonSlug}/employee/appointments/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(res => res.json())
                    .then(res => {
                        if (!res.success) throw new Error(res.message || 'Failed to load');

                        const data = res.data;
                        const serviceName = data.service?.name || 'N/A';
                        const customerName = data.customer?.name || 'N/A';
                        const startTime = new Date(data.start_time).toLocaleString();

                        const html = `
                                <div class="row g-4">
                                    <div class="col-12 text-center pb-3 border-bottom">
                                        <div class="mb-2">
                                            <span class="badge bg-success rounded-pill fs-6 px-3 py-2">COMPLETED</span>
                                        </div>
                                        <h4 class="mb-0 fw-bold">${serviceName}</h4>
                                        <p class="text-muted mb-0">${startTime}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="card-subtitle mb-3 text-muted text-uppercase small fw-bold">Customer</h6>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-md bg-white rounded-circle shadow-sm p-2 me-3 text-success d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                                        <i class="fas fa-user-check"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold fs-5">${customerName}</div>
                                                        <div class="text-muted small">${data.customer?.email || ''}</div>
                                                        <div class="text-muted small">${data.customer?.phone || ''}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="card-subtitle mb-3 text-muted text-uppercase small fw-bold">Service Details</h6>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="mb-2 d-flex justify-content-between">
                                                        <span class="text-muted">Price:</span>
                                                        <span class="fw-bold">${currencySymbol}${parseFloat(data.service?.price || 0).toFixed(2)}</span>
                                                    </li>
                                                    <li class="mb-2 d-flex justify-content-between">
                                                        <span class="text-muted">Duration:</span>
                                                        <span class="fw-bold">${data.service?.duration || 0} mins</span>
                                                    </li>
                                                    <li class="d-flex justify-content-between">
                                                        <span class="text-muted">Total Amount:</span>
                                                        <span class="fw-bold text-success">${currencySymbol}${parseFloat(data.amount || 0).toFixed(2)}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="card-subtitle mb-2 text-muted text-uppercase small fw-bold">Notes</h6>
                                                <p class="mb-0 text-muted fst-italic">${data.notes || 'No notes provided.'}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        modalBody.innerHTML = html;
                    })
                    .catch(err => {
                        modalBody.innerHTML = `
                                <div class="text-center text-danger py-4">
                                    <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                                    <p>Failed to load details. Please try again.</p>
                                </div>
                            `;
                    });
            }
        </script>
    @endpush
@endsection