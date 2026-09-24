@extends('layouts.super-admin')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800 fw-bold">Payment Details</h1>
                <p class="text-muted mb-0">View and manage payment transaction #{{ $payment->id }}</p>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-light border shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Back to Payments
            </a>
        </div>

        <div class="row g-4">
            <!-- Left Column: Payment Information -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-file-invoice-dollar me-2"></i>Transaction Overview
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <!-- Amount and Status Hero Section -->
                        <div class="p-4 bg-light rounded-3 mb-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted small text-uppercase mb-1 fw-bold">
                                    {{ $payment->amount < 0 ? 'Credit Amount' : 'Total Amount' }}
                                </p>
                                <h2 class="mb-0 fw-bold {{ $payment->amount < 0 ? 'text-success' : 'text-gray-900' }}">
                                    {{ system_currency_symbol() }}{{ number_format(abs($payment->amount), 2) }}
                                    <span class="fs-6 text-muted fw-normal">{{ strtoupper(system_currency()) }}</span>
                                    @if($payment->amount < 0)
                                        <span class="badge bg-success fs-6 align-middle ms-2">CREDIT</span>
                                    @endif
                                </h2>
                            </div>
                            <div class="text-end">
                                <p class="text-muted small text-uppercase mb-1 fw-bold">Status</p>
                                @if($payment->status === 'completed')
                                    <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">
                                        <i class="fas fa-check-circle me-1"></i> Completed
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill">
                                        <i class="fas fa-clock me-1"></i> Pending
                                    </span>
                                @elseif($payment->status === 'failed')
                                    <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill">
                                        <i class="fas fa-times-circle me-1"></i> Failed
                                    </span>
                                @else
                                    <span class="badge bg-secondary fs-6 px-3 py-2 rounded-pill">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Proration Details (if available) -->
                        @if(isset($payment->metadata['proration']))
                            <div class="alert alert-info border-0 bg-info bg-opacity-10 mb-4">
                                <h6 class="alert-heading fw-bold text-info mb-3">
                                    <i class="fas fa-calculator me-2"></i>Proration Details
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="small text-muted text-uppercase fw-bold">Unused Time Credit</div>
                                        <div class="fw-bold text-success">
                                            -{{ system_currency_symbol() }}{{ number_format($payment->metadata['proration']['credit'] ?? 0, 2) }}
                                        </div>
                                        <div class="small text-muted">Credit for remaining days on old plan</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="small text-muted text-uppercase fw-bold">New Plan Charge</div>
                                        <div class="fw-bold text-danger">
                                            +{{ system_currency_symbol() }}{{ number_format($payment->metadata['proration']['charge'] ?? 0, 2) }}
                                        </div>
                                        <div class="small text-muted">Cost for remaining days on new plan</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="small text-muted text-uppercase fw-bold">Net Adjustment</div>
                                        <div class="fw-bold {{ $payment->amount < 0 ? 'text-success' : 'text-dark' }}">
                                            {{ system_currency_symbol() }}{{ number_format($payment->amount, 2) }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $payment->amount < 0 ? 'Credit applied to account' : 'Amount to pay' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Details Grid -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small text-uppercase fw-bold mb-1">Salon</label>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                            <i class="fas fa-store"></i>
                                        </div>
                                        <div>
                                            @if($payment->salon)
                                                <a href="{{ route('admin.salons.show', $payment->salon) }}"
                                                    class="fw-bold text-decoration-none text-dark stretched-link">
                                                    {{ $payment->salon->name }}
                                                </a>
                                                <div class="small text-muted">{{ $payment->salon->email }}</div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small text-uppercase fw-bold mb-1">Payment Method</label>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 me-3">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">
                                                {{ ucfirst($payment->payment_method ?? 'Manual') }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $payment->transaction_id ? 'Ref: ' . Str::limit($payment->transaction_id, 15) : 'No Reference' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small text-uppercase fw-bold mb-1">Date</label>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">
                                                {{ $payment->created_at->format('M d, Y') }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $payment->created_at->format('h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small text-uppercase fw-bold mb-1">Subscription Plan</label>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div>
                                            @if($payment->subscription && $payment->subscription->plan)
                                                <div class="fw-bold text-dark">{{ $payment->subscription->plan->name }}</div>
                                                <div class="small text-muted">
                                                    {{ ucfirst($payment->subscription->status) }}
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($payment->notes)
                            <hr class="my-4">
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase fw-bold mb-2">Notes</label>
                                <div class="bg-light p-3 rounded border">
                                    <p class="mb-0 text-gray-700">{{ $payment->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Actions & Meta -->
            <div class="col-lg-4">
                <!-- Actions Card -->
                @if($payment->status === 'pending')
                    <div class="card shadow-sm border-0 mb-4 border-start border-warning border-4">
                        <div class="card-body">
                            <h6 class="fw-bold text-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>Action Required
                            </h6>
                            <p class="small text-muted mb-4">
                                This payment is currently <strong>pending</strong>. Please review the details and approve or
                                reject the transaction.
                            </p>

                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-success w-100 py-2 fw-bold" data-bs-toggle="modal"
                                    data-bs-target="#approveModal">
                                    <i class="fas fa-check me-2"></i>Approve Payment
                                </button>

                                <button type="button" class="btn btn-outline-danger w-100 py-2 fw-bold" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                    <i class="fas fa-times me-2"></i>Reject Payment
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Meta Info Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="m-0 font-weight-bold text-gray-800">Metadata</h6>
                    </div>
                    <div class="card-body pt-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Transaction ID</span>
                                <span class="font-monospace small text-dark bg-light px-2 py-1 rounded">
                                    {{ Str::limit($payment->transaction_id ?? 'N/A', 20) }}
                                </span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Created</span>
                                <span class="small text-dark">{{ $payment->created_at->diffForHumans() }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Last Updated</span>
                                <span class="small text-dark">{{ $payment->updated_at->diffForHumans() }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle me-2"></i>Approve Payment
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            This action will mark the payment as completed and activate the subscription.
                        </div>
                        <p class="mb-0 text-center fs-5">Are you sure you want to approve this payment?</p>
                        <p class="text-muted text-center small mt-2">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4">Yes, Approve Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-times-circle me-2"></i>Reject Payment
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            This action will mark the payment as failed and notify the salon owner.
                        </div>
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label fw-bold">Reason for Rejection <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4"
                                placeholder="Please explain why this payment is being rejected..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4">Reject Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection