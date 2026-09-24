@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Employee Bookings: {{ $employee->name }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.super.dashboard') }}"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Employees</a></li>
                        <li class="breadcrumb-item active">Bookings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-2">
                    <img src="{{ $employee->avatar ? asset('storage/' . $employee->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($employee->name) . '&background=random' }}" 
                         alt="{{ $employee->name }}" 
                         class="img-fluid rounded-circle" 
                         style="width: 100px; height: 100px; object-fit: cover;">
                </div>
                <div class="col-md-10">
                    <h4>{{ $employee->name }}</h4>
                    <p class="text-muted mb-0">{{ $employee->position ?? 'Employee' }}</p>
                    <p class="text-muted">{{ $employee->email }}</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-centered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date & Time</th>
                            <th>Customer</th>
                            <th>Services</th>
                            <th>Status</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $booking->id }}</td>
                            <td>
                                {{ format_date($booking->booking_date) }}<br>
                                <small class="text-muted">{{ format_time($booking->start_time) }} - {{ format_time($booking->end_time) }}</small>
                            </td>
                            <td>
                                {{ $booking->customer->name ?? 'N/A' }}<br>
                                <small class="text-muted">{{ $booking->customer->display_phone ?? '' }}</small>
                            </td>
                            <td>
                                @foreach($booking->services as $service)
                                    <span class="badge bg-primary mb-1">{{ $service->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($booking->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($booking->status == 'confirmed')
                                    <span class="badge bg-info">Confirmed</span>
                                @elseif($booking->status == 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($booking->status) }}</span>
                                @endif
                            </td>
                            <td>{{ currency_symbol() }}{{ number_format($booking->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No bookings found for this employee.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
