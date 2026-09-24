@extends('layouts.app')

@section('title', 'Sale Details')
@inject('settings', 'App\Services\SettingsService')

@push('styles')
    <style>
        .unpaid-sticker {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            border: 8px solid #ef4444;
            color: #ef4444;
            font-size: 5rem;
            font-weight: 900;
            padding: 1rem 2rem;
            text-transform: uppercase;
            opacity: 0.15;
            pointer-events: none;
            z-index: 1000;
            border-radius: 15px;
            letter-spacing: 5px;
            white-space: nowrap;
            font-family: 'Arial Black', sans-serif;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header {{ $sale->status === 'voided' ? 'bg-light' : '' }}">
                        <h3 class="card-title">
                            Sale #{{ $sale->invoice_number ?? 'N/A' }}
                            @if($sale->status === 'voided')
                                <span class="badge bg-secondary ml-2">VOIDED</span>
                            @endif
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.pos.sales.index') }}" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Sales
                            </a>
                            <a href="{{ route('admin.pos.sales.edit', $sale->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit Sale
                            </a>
                            <a href="{{ route('admin.pos.receipt', $sale->id) }}" class="btn btn-info btn-sm"
                                target="_blank">
                                <i class="fas fa-print"></i> Print Receipt
                            </a>
                            @if($posReceiptArabicButton ?? true)
                                <a href="{{ route('admin.pos.receipt.arabic', $sale->id) }}" class="btn btn-primary btn-sm"
                                    target="_blank">
                                    <i class="fas fa-print"></i> Arabic Receipt
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body position-relative">
                        @if($sale->payment_status !== 'paid' && $sale->payment_status !== 'refunded' && $sale->status !== 'voided')
                            <div class="unpaid-sticker">Payment Due</div>
                        @elseif($sale->payment_method === 'package')
                            <div class="unpaid-sticker" style="border-color: #10b981; color: #10b981; opacity: 0.2;">Prepaid
                            </div>
                        @endif
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Customer Information</h5>
                                <p class="mb-1">
                                    <strong>Name:</strong> {{ $sale->customer->name ?? 'Walk-in Customer' }}
                                </p>
                                @if($sale->customer)
                                    <p class="mb-1">
                                        <strong>Email:</strong> {{ $sale->customer->display_email }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Phone:</strong> {{ $sale->customer->display_phone }}
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-6 text-md-right">
                                <h5>Sale Information</h5>
                                <p class="mb-1">
                                    <strong>Date:</strong> {{ format_datetime($sale->created_at) }}
                                </p>
                                <p class="mb-1">
                                    <strong>Status:</strong>
                                    @if($sale->payment_status === 'paid' && (empty($sale->status) || $sale->status === 'completed'))
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($sale->status === 'refunded' || $sale->payment_status === 'refunded')
                                        <span class="badge bg-danger">Refunded</span>
                                    @elseif($sale->status === 'partially_refunded')
                                        <span class="badge bg-warning">Partially Refunded</span>
                                    @elseif($sale->status === 'voided')
                                        <span class="badge bg-secondary">Voided</span>
                                    @elseif(!empty($sale->status))
                                        <span class="badge bg-info">{{ ucfirst($sale->status) }}</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </p>
                                <p class="mb-1">
                                    <strong>Payment Method:</strong>
                                    @if(($sale->cash_amount > 0 ? 1 : 0) + ($sale->card_amount > 0 ? 1 : 0) + ($sale->online_amount > 0 ? 1 : 0) + ($sale->other_amount > 0 ? 1 : 0) > 1)
                                            <span class="badge bg-info">Split / Multiple</span>
                                        <div class="mt-2 ps-2 border-start border-3 border-info">
                                            @if($sale->cash_amount > 0)
                                                <div class="d-flex justify-content-between" style="max-width: 200px;">
                                                    <span class="text-muted small">Cash:</span>
                                                    <span class="fw-bold">{{ format_currency($sale->cash_amount) }}</span>
                                                </div>
                                            @endif
                                            @if($sale->card_amount > 0)
                                                <div class="d-flex justify-content-between" style="max-width: 200px;">
                                                    <span class="text-muted small">Card:</span>
                                                    <span class="fw-bold">{{ format_currency($sale->card_amount) }}</span>
                                                </div>
                                            @endif
                                            @if($sale->online_amount > 0)
                                                <div class="d-flex justify-content-between" style="max-width: 200px;">
                                                    <span class="text-muted small">Online:</span>
                                                    <span class="fw-bold">{{ format_currency($sale->online_amount) }}</span>
                                                </div>
                                            @endif
                                            @if($sale->other_amount > 0)
                                                <div class="d-flex justify-content-between" style="max-width: 200px;">
                                                    <span class="text-muted small">Other:</span>
                                                    <span class="fw-bold">{{ format_currency($sale->other_amount) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                    {{ $sale->payment_method ? ucfirst(str_replace('_', ' ', $sale->payment_method)) : 'N/A' }}
                                @endif
                                </p>
                                <p class="mb-1">
                                    <strong>Payment Status:</strong>
                                    {{ $sale->payment_status ? ucfirst(str_replace('_', ' ', $sale->payment_status)) : 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Type</th>
                                        <th>Staff</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Discount</th>
                                        <th>Tax</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $item->item_name ?? 'N/A' }}
                                                @if(!empty($item->description))
                                                    <br><small class="text-muted">{{ $item->description }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $type = 'Product';
                                                    if ($item->item_type) {
                                                        // Extract just the class name if it's a full model path
                                                        $type = $item->item_type;
                                                        if (str_contains($type, '\\')) {
                                                            $parts = explode('\\', $type);
                                                            $type = end($parts);
                                                        }
                                                        $type = ucfirst(strtolower($type));
                                                    } elseif (str_contains(strtolower($item->item_name ?? ''), 'service') || $item->service_id) {
                                                        $type = 'Service';
                                                    } elseif (str_contains(strtolower($item->item_name ?? ''), 'package') || $item->package_id) {
                                                        $type = 'Package';
                                                    }
                                                @endphp
                                                {{ $type }}
                                            </td>
                                            <td>
                                                @if($item->staff)
                                                    <span class="badge bg-primary">{{ $item->staff->name }}</span>
                                                @elseif($sale->employee)
                                                    <span class="badge bg-secondary">{{ $sale->employee->name }}</span>
                                                @else
                                                    <span class="badge bg-light text-dark">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ format_currency($item->unit_price) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ format_currency($item->discount_amount) }}</td>
                                            <td>{{ format_currency($item->tax_amount) }}</td>
                                            <td>{{ format_currency($item->total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7" class="text-right"><strong>Subtotal:</strong></td>
                                        <td colspan="2" class="text-right">{{ format_currency($sale->subtotal) }}</td>
                                    </tr>
                                    @php
                                        $taxRate = $sale->tax_rate ?? $settings->get('tax_rate', 0, $sale->salon_id);
                                        $taxName = $settings->get('tax_name', 'Tax', $sale->salon_id);
                                    @endphp
                                    <tr>
                                        <td colspan="7" class="text-right">
                                            <strong>{{ $taxName }} ({{ number_format($taxRate, 2) + 0 }}%):</strong>
                                        </td>
                                        <td colspan="2" class="text-right">{{ format_currency($sale->tax) }}</td>
                                    </tr>
                                    @if($sale->discount > 0)
                                        <tr>
                                            <td colspan="7" class="text-right"><strong>Discount:</strong></td>
                                            <td colspan="2" class="text-right text-danger">
                                                -{{ format_currency($sale->discount) }}</td>
                                        </tr>
                                    @endif
                                    @if($sale->tip > 0)
                                        <tr>
                                            <td colspan="7" class="text-right"><strong>Tip:</strong></td>
                                            <td colspan="2" class="text-right">{{ format_currency($sale->tip) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="7" class="text-right"><strong>Total:</strong></td>
                                        <td colspan="2" class="text-right">
                                            <strong>{{ format_currency($sale->total) }}</strong>
                                        </td>
                                    </tr>
                                    @if($sale->payment_method === 'cash')
                                        <tr>
                                            <td colspan="7" class="text-right"><strong>Cash Tendered:</strong></td>
                                            <td colspan="2" class="text-right">{{ format_currency($sale->tendered_amount) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="text-right"><strong>Change Return:</strong></td>
                                            <td colspan="2" class="text-right">{{ format_currency($sale->change_amount) }}</td>
                                        </tr>
                                    @endif
                                    @if($sale->card_amount > 0)
                                        <tr>
                                            <td colspan="7" class="text-right"><strong>Card Paid:</strong></td>
                                            <td colspan="2" class="text-right">{{ format_currency($sale->card_amount) }}</td>
                                        </tr>
                                    @endif
                                    @if($sale->online_amount > 0)
                                        <tr>
                                            <td colspan="7" class="text-right"><strong>Online Paid:</strong></td>
                                            <td colspan="2" class="text-right">{{ format_currency($sale->online_amount) }}</td>
                                        </tr>
                                    @endif
                                    @if($sale->other_amount > 0)
                                        <tr>
                                            <td colspan="7" class="text-right"><strong>Other Paid:</strong></td>
                                            <td colspan="2" class="text-right">{{ format_currency($sale->other_amount) }}</td>
                                        </tr>
                                    @endif
                                    @if($sale->outstanding_amount > 0)
                                        <tr class="table-danger">
                                            <td colspan="7" class="text-right"><strong>Balance Due:</strong></td>
                                            <td colspan="2" class="text-right">
                                                <strong>{{ format_currency($sale->outstanding_amount) }}</strong>
                                            </td>
                                        </tr>
                                    @endif
                                </tfoot>
                            </table>
                        </div>

                        @if($sale->notes)
                            <div class="mt-4">
                                <h5>Notes</h5>
                                <div class="p-3 bg-light rounded">
                                    {!! nl2br(e($sale->notes)) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                @if($sale->status === 'voided')
                                    <div class="alert alert-warning d-inline-flex align-items-center" role="alert">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <div>
                                            This sale was voided
                                            @if($sale->voided_at)
                                                on {{ format_datetime($sale->voided_at) }}
                                                @if($sale->voidedBy)
                                                    by {{ $sale->voidedBy->name }}
                                                @endif
                                            @endif
                                            @if($sale->void_reason)
                                                <br><strong>Reason:</strong> {{ $sale->void_reason }}
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    @if(in_array($sale->status, ['completed', 'partially_refunded']) && $sale->payment_status !== 'refunded')
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#refundModal">
                                            <i class="fas fa-undo"></i> Process Refund
                                        </button>
                                    @endif

                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#voidModal">
                                        <i class="fas fa-ban"></i> Void Sale
                                    </button>
                                @endif
                            </div>
                            <div class="col-md-6 text-right">
                                <span class="text-muted">
                                    Created by: {{ $sale->user->name ?? 'System' }} on
                                    {{ format_datetime($sale->created_at) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(in_array($sale->status, ['completed', 'partially_refunded']) && $sale->payment_status !== 'refunded')
        <!-- Refund Modal -->
        <div class="modal fade" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="refundModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('admin.pos.sales.refund', $sale->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="refundModalLabel">Process Refund</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="amount">Refund Amount</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ currency_symbol() }}</span>
                                    </div>
                                    <input type="number" step="0.01" min="0.01" max="{{ $sale->total - $sale->total_refunded }}"
                                        class="form-control" id="amount" name="amount"
                                        value="{{ $sale->total - $sale->total_refunded }}" required>
                                </div>
                                <small class="form-text text-muted">
                                    Maximum refundable amount: {{ format_currency($sale->total - $sale->total_refunded) }}
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="reason">Reason for Refund</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="restock" name="restock" value="1" checked>
                                <label class="form-check-label" for="restock">Restock items to inventory</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Process Refund</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Void Modal -->
    <div class="modal fade" id="voidModal" tabindex="-1" role="dialog" aria-labelledby="voidModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.pos.sales.void', $sale->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="voidModalLabel">Void Sale</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> Are you sure you want to void this sale? This action cannot be
                            undone and will restore any inventory items to stock.
                        </div>
                        <div class="form-group">
                            <label for="void_reason">Reason for Voiding <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="void_reason" name="void_reason" rows="3" required
                                placeholder="Please enter the reason for voiding this sale..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Void</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize any necessary JavaScript here
        });
    </script>
@endpush