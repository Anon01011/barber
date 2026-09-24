@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-0 text-gray-800">
                    Staff Commission Report
                    @if(isset($branch) && $branch)
                        <span class="badge bg-primary text-white ms-2"
                            style="font-size: 0.5em; vertical-align: middle;">{{ $branch->name }}</span>
                    @endif
                </h2>
                <p class="text-muted mb-0">Detailed commission breakdown by staff member</p>
            </div>

            <a href="{{ route('admin.reports.commissions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Overview
            </a>
        </div>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Options</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reports.commissions.by-staff') }}" method="GET"
                    class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="staff_id" class="form-label">Staff Member</label>
                        <select name="staff_id" id="staff_id" class="form-select select2">
                            <option value="">All Staff</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}" {{ $staffId == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Commission Details</h6>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    Total: {{ currency_symbol() }}{{ number_format($summaryTotal, 2) }}
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Staff</th>
                                <th>Item</th>
                                <th>Sale Amount</th>
                                <th>Tip</th>
                                <th>Commission</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commissions as $commission)
                                <tr>
                                    <td>
                                        {{ format_date($commission->created_at) }}<br>
                                        <small class="text-muted">{{ format_time($commission->created_at) }}</small>
                                    </td>
                                    <td>{{ $commission->staff->name }}</td>
                                    <td>
                                        @if($commission->item_type == 'service')
                                            <span class="badge bg-primary bg-opacity-10 text-primary">Service</span>
                                            @if($commission->booking_id)
                                                {{ $commission->booking->service->name ?? 'Unknown Service' }}
                                            @else
                                                @php
                                                    $service = \App\Models\Service::find($commission->item_id);
                                                @endphp
                                                {{ $service->name ?? 'POS Service' }}
                                            @endif
                                        @elseif($commission->item_type == 'product')
                                            <span class="badge bg-success bg-opacity-10 text-white">Product</span>
                                            @php
                                                $product = \App\Models\InventoryItem::find($commission->item_id);
                                            @endphp
                                            {{ $product->name ?? 'POS Product' }}
                                        @elseif($commission->item_type == 'package')
                                            <span class="badge bg-info bg-opacity-10 text-info">Package</span>
                                            @php
                                                $package = \App\Models\Package::find($commission->item_id);
                                            @endphp
                                            {{ $package->name ?? 'POS Package' }}
                                        @elseif($commission->item_type == 'membership')
                                            <span class="badge bg-warning bg-opacity-10 text-warning">Membership</span>
                                            @php
                                                $membership = \App\Models\Membership::find($commission->item_id);
                                            @endphp
                                            {{ $membership->name ?? 'POS Membership' }}
                                        @elseif($commission->item_type == 'tip')
                                            <span class="badge bg-warning bg-opacity-10 text-warning">Tip</span>
                                            POS Sale #{{ $commission->posSale->invoice_number ?? 'N/A' }}
                                        @elseif($commission->item_type == 'target')
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Target</span>
                                            Period: {{ $commission->period_start }} to {{ $commission->period_end }}
                                        @else
                                            {{ ucfirst($commission->item_type) }}
                                        @endif
                                    </td>
                                    <td>{{ currency_symbol() }}{{ number_format($commission->sale_amount, 2) }}</td>
                                    <td>
                                        @if($commission->booking && $commission->booking->tip_amount > 0)
                                            <span
                                                class="text-white">+{{ currency_symbol() }}{{ number_format($commission->booking->tip_amount, 2) }}</span>
                                        @elseif($commission->item_type == 'tip')
                                            <span
                                                class="text-white">{{ currency_symbol() }}{{ number_format($commission->sale_amount, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold text-white">
                                        {{ currency_symbol() }}{{ number_format($commission->commission_amount, 2) }}
                                    </td>
                                    <td>
                                        @if($commission->status == 'paid')
                                            <span class="badge bg-success">Paid</span>
                                            <div class="small text-muted mt-1">
                                                {{ $commission->paid_at ? format_date($commission->paid_at, 'M d') : '' }}
                                            </div>
                                        @elseif($commission->status == 'approved')
                                            <span class="badge bg-info">Approved</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">No commissions found matching your criteria</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted small">
                        Showing {{ $commissions->firstItem() ?? 0 }} to {{ $commissions->lastItem() ?? 0 }} of
                        {{ $commissions->total() }} entries
                    </div>
                    <div>
                        {{ $commissions->appends(request()->all())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('.select2').select2({
                    theme: 'bootstrap-5'
                });
            });
        </script>
    @endpush
@endsection