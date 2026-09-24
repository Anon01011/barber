@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Stats Overview Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Today's Total</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $stats['total'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="fas fa-calendar-day text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Completed</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $stats['completed'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="fas fa-check-circle text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Pending</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $stats['pending'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="fas fa-clock text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Today's Revenue</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ currency_symbol() }}{{ number_format($stats['revenue'], 2) }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                    <i class="fas fa-coins text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-4 mb-4">
                    <div class="card-header pb-0 bg-transparent border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="font-weight-bolder mb-0">Appointments Overview</h6>
                                <p class="text-xs text-secondary mb-0">Manage your salon's daily schedule</p>
                            </div>
                            <div class="d-flex gap-3">
                                <div class="input-group input-group-outline" style="width: 200px;">
                                    <select id="staffFilter" class="form-select form-select-sm border-0 bg-light rounded-3">
                                        <option value="">All Staff</option>
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                                {{ $staff->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <a href="{{ route('admin.bookings.index') }}"
                                    class="btn btn-primary btn-sm rounded-3 px-3 mb-0">
                                    <i class="fas fa-calendar-alt me-2"></i>Calendar View
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-4 pb-2" style="min-height: calc(100vh - 400px);">
                        @php
                            $salonTimezone = salon_timezone();
                            $today = now($salonTimezone)->startOfDay();

                            $todayBookings = $bookings->filter(function ($booking) use ($salonTimezone, $today) {
                                return $booking->start_time->copy()->setTimezone($salonTimezone)->isToday();
                            });

                            $futureBookings = $bookings->filter(function ($booking) use ($salonTimezone, $today) {
                                return $booking->start_time->copy()->setTimezone($salonTimezone)->isAfter($today->copy()->endOfDay());
                            });

                            $ongoingBookings = $bookings->filter(function ($booking) {
                                return in_array($booking->status, ['confirmed', 'staff_completed', 'partially_completed']);
                            });
                        @endphp

                        <div class="px-4">
                            <ul class="nav nav-pills nav-fill p-1 bg-light rounded-4 mb-4" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active rounded-4 py-2" id="pills-today-tab"
                                        data-bs-toggle="pill" data-bs-target="#pills-today" type="button" role="tab">
                                        <i class="fas fa-calendar-day me-2"></i>Today ({{ $todayBookings->count() }})
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-4 py-2" id="pills-ongoing-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-ongoing" type="button" role="tab">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Active ({{ $ongoingBookings->count() }})
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-4 py-2" id="pills-future-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-future" type="button" role="tab">
                                        <i class="fas fa-calendar-plus me-2"></i>Future ({{ $futureBookings->count() }})
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-today" role="tabpanel">
                                    @include('admin.bookings.partials.booking_table', ['bookings' => $todayBookings])
                                </div>
                                <div class="tab-pane fade" id="pills-ongoing" role="tabpanel">
                                    @include('admin.bookings.partials.booking_table', ['bookings' => $ongoingBookings])
                                </div>
                                <div class="tab-pane fade" id="pills-future" role="tabpanel">
                                    @include('admin.bookings.partials.booking_table', ['bookings' => $futureBookings])
                                </div>
                            </div>
                        </div>

                        <div class="px-4 pt-4">
                            {{ $bookings->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Booking Details Modal -->
    @include('admin.bookings.partials.booking_details_modal')

    @include('admin.bookings.partials.payment_modal')

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Handle staff filter change
                const staffFilter = document.getElementById('staffFilter');
                if (staffFilter) {
                    staffFilter.addEventListener('change', function () {
                        const staffId = this.value;
                        window.location.href = window.location.pathname + (staffId ? `?staff_id=${staffId}` : '');
                    });
                }

                // Handle status change
                window.updateBookingStatus = function (bookingId, status) {
                    const statusText = status.replace('_', ' ');

                    Swal.fire({
                        title: 'Update Status?',
                        text: `Are you sure you want to mark this appointment as ${statusText}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, update it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`{{ url(auth()->user()->salon->slug) }}/admin/bookings/${bookingId}/status`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ status: status })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.requires_payment) {
                                        if (typeof showPaymentModal === 'function') {
                                            showPaymentModal(bookingId, data.amount);
                                        } else {
                                            Swal.fire('Error', 'Payment system not loaded', 'error');
                                        }
                                    } else if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Updated!',
                                            text: data.message,
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire('Error', data.message || 'Failed to update status', 'error');
                                    }
                                })
                                .catch(error => {

                                    Swal.fire('Error', 'An unexpected error occurred', 'error');
                                });
                        }
                    });
                };

                // Handle view booking details
                window.viewBookingDetails = function (bookingId) {
                    const viewBtn = document.querySelector(`.view-booking[data-booking-id="${bookingId}"]`);
                    if (viewBtn) {
                        viewBtn.click();
                    } else {
                        // Manual trigger if button not found in table
                        const tempBtn = document.createElement('button');
                        tempBtn.className = 'view-booking d-none';
                        tempBtn.setAttribute('data-booking-id', bookingId);
                        document.body.appendChild(tempBtn);
                        // The listener is attached to .view-booking on DOMContentLoaded, 
                        // so we need to trigger the fetch manually or re-init listeners.
                        // For now, let's assume the button exists in the table.
                    }
                };
            });
        </script>
    @endpush

    <style>
        .icon-shape {
            width: 48px;
            height: 48px;
            background-position: center;
            border-radius: 0.75rem;
        }

        .icon-shape i {
            top: 14px;
            position: relative;
        }

        .nav-pills .nav-link.active {
            background-color: #fff;
            color: #5e72e4;
            box-shadow: 0 4px 6px rgba(50, 50, 93, .11), 0 1px 3px rgba(0, 0, 0, .08);
            font-weight: 600;
        }

        .nav-pills .nav-link {
            color: #67748e;
            font-weight: 500;
        }

        .card .card-header {
            padding: 1.5rem;
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }
    </style>
@endsection