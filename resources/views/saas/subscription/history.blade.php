@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 fw-bold text-gray-800">Subscription History</h1>
                <p class="text-muted mb-0">View your past subscriptions and payments</p>
            </div>
            <a href="{{ route('admin.saas.subscription.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Plans
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 py-3 ps-4">Plan</th>
                                <th class="border-0 py-3">Type</th>
                                <th class="border-0 py-3">Status</th>
                                <th class="border-0 py-3">Start Date</th>
                                <th class="border-0 py-3">End Date</th>
                                <th class="border-0 py-3">Amount</th>
                                <th class="border-0 py-3 pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $subscription)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $subscription->plan->name }}</div>
                                        <div class="small text-muted">{{ $subscription->plan->duration_in_days }} days</div>
                                    </td>
                                    <td>
                                        @if($loop->first && $subscription->status === 'active')
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3">Current</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">Past</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscription->status === 'active')
                                            <span class="badge bg-success-subtle text-white rounded-pill px-3">Active</span>
                                        @elseif($subscription->status === 'cancelled')
                                            <span class="badge bg-warning-subtle text-warning rounded-pill px-3">Cancelled</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Expired</span>
                                        @endif
                                    </td>
                                    <td>{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'Never' }}</td>
                                    <td class="fw-semibold">{{ system_format_currency($subscription->plan->price) }}</td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('admin.saas.subscription.invoice', $subscription->id) }}"
                                            class="btn btn-sm btn-outline-primary" title="View Invoice">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-history fa-3x mb-3 opacity-50"></i>
                                        <p class="mb-0">No subscription history found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .bg-success-subtle {
                background-color: #d1e7dd;
            }

            .bg-danger-subtle {
                background-color: #f8d7da;
            }

            .bg-warning-subtle {
                background-color: #fff3cd;
            }

            .bg-primary-subtle {
                background-color: #cfe2ff;
            }

            .bg-secondary-subtle {
                background-color: #e2e3e5;
            }
        </style>
    @endpush
@endsection