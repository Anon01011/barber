@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 fw-bold text-gray-800">Payment History</h1>
                <p class="text-muted mb-0">View all your subscription payments and invoices</p>
            </div>
            <a href="{{ route('admin.saas.subscription.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Subscription
            </a>
        </div>

        <!-- Payments Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @if($payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction ID</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $payment->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <code class="small">{{ $payment->transaction_id }}</code>
                                        </td>
                                        <td>
                                            @if($payment->subscription && $payment->subscription->plan)
                                                <span class="badge bg-primary">{{ $payment->subscription->plan->name }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold text-white">{{ system_currency_symbol() }}{{ number_format($payment->amount, 2) }}</span>
                                            <small class="text-muted">{{ strtoupper($payment->currency) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span>
                                        </td>
                                        <td>
                                            @if($payment->status === 'completed')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Completed
                                                </span>
                                            @elseif($payment->status === 'pending')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                </span>
                                            @elseif($payment->status === 'failed')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Failed
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.saas.payment.show', $payment->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            @if($payment->subscription_id)
                                                <a href="{{ route('admin.saas.subscription.invoice', $payment->subscription_id) }}"
                                                    class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-file-invoice me-1"></i>Invoice
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Payment History</h5>
                        <p class="text-muted">You haven't made any payments yet.</p>
                        <a href="{{ route('admin.saas.subscription.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-crown me-2"></i>View Plans
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection