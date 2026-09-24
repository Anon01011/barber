@extends('layouts.app')

@section('title', 'Sales History')

@push('styles')
    <style>
        .sale-card {
            transition: all 0.2s ease-in-out;
        }

        .sale-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Sales History</h2>
                    <div>
                        <a href="{{ route('admin.pos.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to POS
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="salesTable">
                        <thead>
                            <tr>
                                <th>Sale #</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th class="text-end">Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td>{{ $sale->sale_number }}</td>
                                    <td>{{ format_datetime($sale->created_at) }}</td>
                                    <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                    <td>{{ $sale->items->sum('quantity') }} items</td>
                                    <td class="text-end">{{ format_currency($sale->total) }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $sale->payment_status === 'paid' ? 'success' : ($sale->payment_status === 'partially_paid' ? 'warning' : 'danger') }}">
                                            {{ ucfirst(str_replace('_', ' ', $sale->payment_status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.pos.show', $sale->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.pos.receipt', $sale->id) }}" target="_blank"
                                                class="btn btn-sm btn-outline-secondary" title="Print Receipt">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-receipt fa-3x mb-3"></i>
                                            <p class="mb-0">No sales records found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Table is using Laravel pagination, no DataTables needed

            });
        </script>
    @endpush
@endsection