@extends('layouts.app')

@section('title', 'Sale Details')

@push('styles')
    <style>
        .sale-header {
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .sale-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .detail-card {
            background: white;
            border-radius: 0.25rem;
            padding: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .detail-label {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-weight: 500;
            font-size: 1.1rem;
        }

        .badge-paid {
            background-color: #198754;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-cancelled {
            background-color: #dc3545;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">Sale Details</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.pos.index') }}">POS</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.pos.history') }}">Sales History</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">#{{ $sale->sale_number }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('admin.pos.history') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Back to History
                        </a>
                        <a href="{{ route('admin.pos.receipt', $sale->id) }}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-print me-1"></i> Print Receipt
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="sale-header">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Sale #{{ $sale->sale_number }}</h4>
                <div>
                    <span
                        class="badge rounded-pill {{ $sale->payment_status === 'paid' ? 'bg-success' : ($sale->payment_status === 'partially_paid' ? 'bg-warning text-dark' : 'bg-danger') }} me-2">
                        {{ ucfirst(str_replace('_', ' ', $sale->payment_status)) }}
                    </span>
                    <span class="badge rounded-pill {{ $sale->status === 'completed' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($sale->status) }}
                    </span>
                </div>
            </div>

            <div class="sale-details">
                <div class="detail-card">
                    <div class="detail-label">Date & Time</div>
                    <div class="detail-value">{{ format_datetime($sale->created_at) }}</div>
                </div>

                <div class="detail-card">
                    <div class="detail-label">Customer</div>
                    <div class="detail-value">
                        @if($sale->customer)
                            {{ $sale->customer->name }}
                            @if($sale->customer->phone)
                                <br><small class="text-muted">{{ $sale->customer->display_phone }}</small>
                            @endif
                        @else
                            Walk-in Customer
                        @endif
                    </div>
                </div>

                <div class="detail-card">
                    <div class="detail-label">Staff</div>
                    <div class="detail-value">{{ $sale->user->name }}</div>
                </div>

                <div class="detail-card">
                    <div class="detail-label">Payment Method</div>
                    <div class="detail-value">{{ ucfirst($sale->payment_method) }}</div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Items</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th class="text-end">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $groupedItems = $sale->items->groupBy(function ($item) {
                                    return $item->package_id ? 'package_' . $item->package_id : 'individual';
                                });
                            @endphp

                            @foreach($groupedItems as $groupKey => $items)
                                @if(str_starts_with($groupKey, 'package_'))
                                    {{-- Package Header --}}
                                    @php
                                        $mainPackageItem = $items->filter(function ($item) {
                                            return str_contains($item->item_type, 'Package');
                                        })->first();
                                    @endphp

                                    @if($mainPackageItem)
                                        <tr class="table-light">
                                            <td colspan="4">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="fas fa-gift fa-2x text-info"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $mainPackageItem->item_name }}</h6>
                                                        <small class="text-muted">Package</small>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        {{-- Package Price Row --}}
                                        <tr>
                                            <td class="ps-5">Package Price</td>
                                            <td class="text-end">{{ format_currency($mainPackageItem->price) }}</td>
                                            <td class="text-center">{{ $mainPackageItem->quantity }}</td>
                                            <td class="text-end">{{ format_currency($mainPackageItem->total) }}</td>
                                        </tr>
                                    @endif

                                    {{-- Package Services --}}
                                    @foreach($items as $item)
                                        @if(!str_contains($item->item_type, 'Package'))
                                            <tr>
                                                <td class="ps-5">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <i class="fas fa-spa text-muted"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $item->item->name }}</h6>
                                                            <small class="text-muted">
                                                                Service (in Package)
                                                                @if($item->item->duration)
                                                                    • {{ $item->item->duration }} min
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">{{ format_currency($item->price) }}</td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-end">{{ format_currency($item->total) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    {{-- Individual Items --}}
                                    @foreach($items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if(str_contains($item->item_type, 'Service'))
                                                            <i class="fas fa-spa fa-2x text-primary"></i>
                                                        @else
                                                            <i class="fas fa-box fa-2x text-white"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $item->item->name }}</h6>
                                                        <small class="text-muted">
                                                            {{ str_contains($item->item_type, 'Service') ? 'Service' : (str_contains($item->item_type, 'Package') ? 'Package' : 'Product') }}
                                                            @if($item->item_type === 'service' && $item->item->duration)
                                                                • {{ $item->item->duration }} min
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">{{ format_currency($item->price) }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end">{{ format_currency($item->total) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 offset-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>{{ format_currency($sale->subtotal) }}</span>
                        </div>
                        @if($sale->discount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Discount:</span>
                                <span class="text-danger">-{{ format_currency($sale->discount) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax ({{ config('pos.tax_rate', 10) }}%):</span>
                            <span>{{ format_currency($sale->tax) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total:</span>
                            <span>{{ format_currency($sale->total) }}</span>
                        </div>

                        @if($sale->payment_status === 'partially_paid')
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Amount Paid:</span>
                                <span class="text-white">{{ format_currency($sale->amount_paid) }}</span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Balance Due:</span>
                                <span class="text-danger">{{ format_currency($sale->total - $sale->amount_paid) }}</span>
                            </div>
                        @endif

                        @if($sale->notes)
                            <hr>
                            <div class="mt-3">
                                <h6>Notes:</h6>
                                <p class="text-muted">{{ $sale->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            @if($sale->payment_status !== 'paid')
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="fas fa-money-bill-wave me-1"></i> Add Payment
                </button>
            @else
                <div></div>
            @endif

            <div class="btn-group">
                <a href="{{ route('admin.pos.receipt', $sale->id) }}" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-print me-1"></i> Print Receipt
                </a>
                @if($sale->status !== 'cancelled')
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                        data-bs-target="#cancelSaleModal">
                        <i class="fas fa-times me-1"></i> Cancel Sale
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Payment Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.pos.add-payment', $sale->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Amount Due</label>
                            <input type="text" class="form-control"
                                value="{{ format_currency($sale->total - ($sale->amount_paid ?? 0)) }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount Received</label>
                            <input type="number" class="form-control" name="amount" min="0.01" step="0.01"
                                max="{{ $sale->total - ($sale->amount_paid ?? 0) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="card">Credit/Debit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Record Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Sale Modal -->
    <div class="modal fade" id="cancelSaleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.pos.cancel', $sale->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p>Are you sure you want to cancel this sale? This action cannot be undone.</p>
                        <div class="mb-3">
                            <label class="form-label">Reason for Cancellation</label>
                            <textarea class="form-control" name="cancellation_reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto-format currency input
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.addEventListener('change', function () {
                    const max = parseFloat(this.max);
                    const value = parseFloat(this.value);

                    if (value > max) {
                        this.value = max.toFixed(2);
                    } else if (value < 0) {
                        this.value = '0.01';
                    }
                });
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        </script>
    @endpush
@endsection