@extends('layouts.super-admin')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Subscription Management</h1>
        </div>

        <!-- Filter -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Subscriptions Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Subscriptions ({{ $subscriptions->total() }})</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Salon</th>
                                <th>Plan</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Started</th>
                                <th>Ends</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $subscription)
                                <tr>
                                    <td>{{ $subscription->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.salons.show', $subscription->salon ?? $subscription->salon_id) }}">
                                            {{ $subscription->salon->name }}
                                        </a>
                                    </td>
                                    <td>{{ $subscription->plan->name }}</td>
                                    <td>{{ system_currency_symbol() }} {{ number_format($subscription->plan->price, 2) }}</td>
                                    <td>
                                        @if($subscription->status === 'active')
                                            <span class="badge badge-success">Active</span>
                                        @elseif($subscription->status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($subscription->status === 'paused')
                                            <span class="badge badge-info">Paused</span>
                                        @elseif($subscription->status === 'cancelled')
                                            <span class="badge badge-danger">Cancelled</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($subscription->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $subscription->starts_at->format('M d, Y') }}</td>
                                    <td>{{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admin.subscriptions.show', $subscription->id) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($subscription->status === 'pending')
                                            <form action="{{ route('admin.subscriptions.approve', $subscription->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to approve this subscription?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve Subscription">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No subscriptions found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $subscriptions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection