@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
                        <li class="breadcrumb-item active" aria-current="page">New Booking</li>
                    </ol>
                </nav>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">New Booking</h5>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Bookings
                        </a>
                    </div>
                    <div class="card-body">
                        <form id="createBookingForm" action="{{ route('admin.bookings.store') }}" method="POST">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="customer_id" class="form-label">Customer *</label>
                                    <div class="d-flex gap-2">
                                        <select class="form-select @error('customer_id') is-invalid @enderror"
                                            id="customer_id" name="customer_id" required style="flex: 1">
                                            <option value="">Select Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }} ({{ $customer->display_phone }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" id="billActivityBtn"
                                            onclick="openBillActivityModal(document.getElementById('customer_id').value)"
                                            disabled title="Select a customer to view bill activity">
                                            <i class="fas fa-file-invoice"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger position-relative" id="unpaidAmountBtn"
                                            onclick="openUnpaidBillsModal(document.getElementById('customer_id').value)"
                                            disabled title="Select a customer to view unpaid bills" style="display: none;">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle" id="unpaidAmountBadge">
                                                {{ currency_symbol() }}0
                                            </span>
                                        </button>
                                    </div>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="service_id" class="form-label">Service *</label>
                                    <select class="form-select @error('service_id') is-invalid @enderror" id="service_id"
                                        name="service_id" required>
                                        <option value="">Select Service</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }} ({{ $service->duration }} min) -
                                                {{ currency_symbol() }}{{ number_format($service->price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="staff_id" class="form-label">Staff Member *</label>
                                    <select class="form-select @error('staff_id') is-invalid @enderror" id="staff_id"
                                        name="staff_id" required>
                                        <option value="">Select Staff Member</option>
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->id }}" {{ old('staff_id') == $staff->id ? 'selected' : '' }}>
                                                {{ $staff->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('staff_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status"
                                        name="status" required>
                                        @foreach(['pending', 'confirmed', 'in_progress', 'cancelled', 'no_show'] as $status)
                                            <option value="{{ $status }}" {{ old('status', 'pending') === $status ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="start_time" class="form-label">Start Time *</label>
                                    <input type="datetime-local"
                                        class="form-control @error('start_time') is-invalid @enderror" id="start_time"
                                        name="start_time" value="{{ old('start_time', now()->format('Y-m-d\TH:i')) }}"
                                        required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="end_time" class="form-label">End Time *</label>
                                    <input type="datetime-local"
                                        class="form-control @error('end_time') is-invalid @enderror" id="end_time"
                                        name="end_time" value="{{ old('end_time') }}" required>
                                    <div class="form-text text-info" id="effective_end_time_display" style="display: none;">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Effective End Time (with buffer): <span id="effective_end_time_value"></span>
                                    </div>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                    rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary me-md-2">
                                    <i class="fas fa-save me-1"></i> Create Booking
                                </button>
                                <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bill Activity Modal -->
    @include('admin.customers.partials.bill_activity_modal')

    @push('scripts')
        <script>
            // Enable/disable Bill Activity button based on customer selection
            document.getElementById('customer_id').addEventListener('change', function () {
                const billActivityBtn = document.getElementById('billActivityBtn');
                const unpaidAmountBtn = document.getElementById('unpaidAmountBtn');
                const customerId = this.value;
                
                if (customerId) {
                    billActivityBtn.disabled = false;
                    billActivityBtn.title = 'View customer bill activity';
                    
                    // Fetch customer's unpaid amount
                    const salonSlug = '{{ auth()->user()->salon->slug }}';
                    fetch(`/${salonSlug}/admin/customers/${customerId}/bill-activity`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.bills) {
                            // Calculate unpaid amount
                            let unpaidAmount = 0;
                            data.bills.forEach(bill => {
                                const status = bill.payment_status.toLowerCase();
                                if (status === 'unpaid' || status === 'pending' || status === 'partial') {
                                    unpaidAmount += parseFloat(bill.grand_total || 0);
                                }
                            });
                            
                            if (unpaidAmount > 0) {
                                unpaidAmountBtn.style.display = 'inline-block';
                                unpaidAmountBtn.disabled = false;
                                unpaidAmountBtn.title = 'View unpaid bills';
                                document.getElementById('unpaidAmountBadge').textContent = 
                                    '{{ currency_symbol() }}' + unpaidAmount.toFixed(2);
                            } else {
                                unpaidAmountBtn.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => {

                        unpaidAmountBtn.style.display = 'none';
                    });
                } else {
                    billActivityBtn.disabled = true;
                    billActivityBtn.title = 'Select a customer to view bill activity';
                    unpaidAmountBtn.style.display = 'none';
                }
            });

            // Auto-calculate end time based on service duration
            document.getElementById('service_id').addEventListener('change', function () {
                const serviceId = this.value;
                const startTimeInput = document.getElementById('start_time');
                const bufferTime = {{ (int) app(\App\Services\SettingsService::class)->get('appointment_buffer_time', 15) }};

                if (serviceId && startTimeInput.value) {
                    // Get the selected service's duration
                    const selectedOption = this.options[this.selectedIndex];
                    const durationMatch = selectedOption.text.match(/(\d+)\s*min/);

                    if (durationMatch) {
                        const duration = parseInt(durationMatch[1]);
                        const startTime = new Date(startTimeInput.value);
                        const endTime = new Date(startTime.getTime() + duration * 60000);

                        // Format the end time for the datetime-local input
                        // Adjust for timezone offset to keep local time correct
                        const offset = endTime.getTimezoneOffset() * 60000;
                        const endTimeStr = new Date(endTime.getTime() - offset).toISOString().slice(0, 16);
                        document.getElementById('end_time').value = endTimeStr;

                        // Calculate and show effective end time
                        if (bufferTime > 0) {
                            const effectiveEndTime = new Date(endTime.getTime() + bufferTime * 60000);
                            const effectiveEndTimeStr = effectiveEndTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                            document.getElementById('effective_end_time_value').textContent = effectiveEndTimeStr;
                            const displayEl = document.getElementById('effective_end_time_display');
                            if (displayEl) displayEl.style.display = 'block';
                        } else {
                            const displayEl = document.getElementById('effective_end_time_display');
                            if (displayEl) displayEl.style.display = 'none';
                        }
                    }
                }
            });

            // Update end time when start time changes
            document.getElementById('start_time').addEventListener('change', function () {
                const serviceId = document.getElementById('service_id').value;
                if (serviceId) {
                    // Trigger the service change handler to update end time
                    document.getElementById('service_id').dispatchEvent(new Event('change'));
                }
            });

            // Update effective end time when manual end time changes
            document.getElementById('end_time').addEventListener('change', function () {
                const endTimeValue = this.value;
                const bufferTime = {{ (int) app(\App\Services\SettingsService::class)->get('appointment_buffer_time', 15) }};

                if (endTimeValue && bufferTime > 0) {
                    const endTime = new Date(endTimeValue);
                    const effectiveEndTime = new Date(endTime.getTime() + bufferTime * 60000);
                    const effectiveEndTimeStr = effectiveEndTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    document.getElementById('effective_end_time_value').textContent = effectiveEndTimeStr;
                    const displayEl = document.getElementById('effective_end_time_display');
                    if (displayEl) displayEl.style.display = 'block';
                }
            });
            </script>
    @endpush
@endsection