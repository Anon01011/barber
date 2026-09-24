@extends('layouts.super-admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">Financials</h1>
                <p class="text-muted mb-0">Monitor revenue streams and transaction history.</p>
            </div>
            <div class="d-flex gap-2">
                <!-- Export button could go here -->
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="position-absolute top-0 end-0 p-3 opacity-10">
                            <i class="fas fa-wallet fa-3x text-success"></i>
                        </div>
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Total Revenue</h6>
                        <h3 class="fw-bold text-dark mb-0">
                            {{ system_currency_symbol() }}{{ number_format($stats['total_revenue'], 2) }}
                        </h3>
                        <div class="mt-2 text-success small fw-medium">
                            <i class="fas fa-arrow-up me-1"></i> Lifetime Earnings
                        </div>
                    </div>
                    <div class="card-footer bg-success-subtle border-0 py-1"></div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="position-absolute top-0 end-0 p-3 opacity-10">
                            <i class="fas fa-clock fa-3x text-warning"></i>
                        </div>
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Pending</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ $stats['pending_payments'] }}</h3>
                        <div class="mt-2 text-muted small">
                            Transactions awaiting approval
                        </div>
                    </div>
                    <div class="card-footer bg-warning-subtle border-0 py-1"></div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="position-absolute top-0 end-0 p-3 opacity-10">
                            <i class="fas fa-file-invoice-dollar fa-3x text-info"></i>
                        </div>
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Pending Amount</h6>
                        <h3 class="fw-bold text-dark mb-0">
                             {{ system_currency_symbol() }}{{ number_format($stats['pending_amount'], 2) }}
                        </h3>
                        <div class="mt-2 text-muted small">
                            Potential revenue
                        </div>
                    </div>
                    <div class="card-footer bg-info-subtle border-0 py-1"></div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="position-absolute top-0 end-0 p-3 opacity-10">
                            <i class="fas fa-receipt fa-3x text-secondary"></i>
                        </div>
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Total Transactions</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ $stats['total_payments'] }}</h3>
                        <div class="mt-2 text-muted small">
                             All time volume
                        </div>
                    </div>
                    <div class="card-footer bg-secondary-subtle border-0 py-1"></div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3 align-items-center">
                    <div class="col-md-3">
                         <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                placeholder="Transaction ID..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="salon_id" class="form-select">
                            <option value="">All Salons</option>
                            @foreach($salons as $salon)
                                <option value="{{ $salon->id }}" {{ request('salon_id') == $salon->id ? 'selected' : '' }}>
                                    {{ $salon->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-medium">
                            Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-light w-100 text-muted bg-white border">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase text-muted small fw-bold">ID</th>
                                <th class="py-3 text-uppercase text-muted small fw-bold">Salon</th>
                                <th class="py-3 text-uppercase text-muted small fw-bold">Detail</th>
                                <th class="py-3 text-uppercase text-muted small fw-bold">Amount</th>
                                <th class="py-3 text-uppercase text-muted small fw-bold">Status</th>
                                <th class="py-3 text-uppercase text-muted small fw-bold">Date</th>
                                <th class="pe-4 py-3 text-end text-uppercase text-muted small fw-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td class="ps-4 py-3 font-monospace small text-muted">#{{ $payment->id }}</td>
                                    <td class="py-3">
                                        @if($payment->salon)
                                            <a href="{{ route('admin.salons.show', $payment->salon) }}" class="text-dark fw-bold text-decoration-none">
                                                {{ $payment->salon->name }}
                                            </a>
                                            <div class="small text-muted">{{ $payment->salon->email }}</div>
                                        @else
                                            <span class="text-muted fst-italic">Unknown Salon</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($payment->subscription && $payment->subscription->plan)
                                            <span class="badge bg-light text-dark border">
                                                {{ $payment->subscription->plan->name }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                        <div class="small text-muted mt-1">
                                            via {{ ucfirst($payment->payment_method ?? 'N/A') }}
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-bold {{ $payment->amount < 0 ? 'text-success' : 'text-dark' }}">
                                            {{ system_currency_symbol() }}{{ number_format(abs($payment->amount), 2) }}
                                        </div>
                                        @if($payment->amount < 0)
                                            <span class="badge bg-success-subtle text-success small">Refund</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($payment->status === 'completed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Completed</span>
                                        @elseif($payment->status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">
                                                <i class="fas fa-clock me-1"></i> Pending
                                            </span>
                                        @elseif($payment->status === 'failed')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">Failed</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3">{{ ucfirst($payment->status ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-muted small">
                                        {{ $payment->created_at->format('M d, Y') }}<br>
                                        {{ $payment->created_at->format('h:i A') }}
                                    </td>
                                    <td class="pe-4 py-3 text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-light border" title="View Details">
                                                <i class="fas fa-eye text-muted"></i>
                                            </a>
                                            
                                            @if($payment->status === 'pending')
                                                <button type="button" class="btn btn-sm btn-success" onclick="approvePayment({{ $payment->id }})" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="showRejectModal({{ $payment->id }})" title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted mb-2">
                                            <i class="fas fa-receipt fa-3x opacity-25"></i>
                                        </div>
                                        <p class="text-muted fw-medium mb-0">No transactions found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($payments->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        {{ $payments->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reject Payment Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-circle me-2"></i> Reject Payment</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="4" required maxlength="500" 
                                placeholder="E.g. Incorrect amount, duplicate transaction..."></textarea>
                            <div class="form-text">Maximum 500 characters. This will be visible to the salon owner.</div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger fw-bold">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function approvePayment(paymentId) {
                Swal.fire({
                    title: 'Approve Payment?',
                    text: "This will capture the revenue and activate the subscription plan immediately.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Approve'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/payments/${paymentId}/approve`;
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            function showRejectModal(paymentId) {
                const form = document.getElementById('rejectForm');
                form.action = `/admin/payments/${paymentId}/reject`;
                const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
                modal.show();
            }
        </script>
    @endpush
@endsection