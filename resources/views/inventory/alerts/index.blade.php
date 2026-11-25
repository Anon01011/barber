@extends('layouts.app')

@section('title', 'Stock Alerts')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">Stock Alerts</h1>
                    <button type="button" class="btn btn-primary rounded-3 px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#confirmGenerateAlertsModal">
                        <i class="fas fa-sync me-1.5"></i> Generate Alerts
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Total Alerts</p>
                                <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-bell text-primary fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Unresolved</p>
                                <h3 class="mb-0 text-warning">{{ $stats['unresolved'] }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Critical</p>
                                <h3 class="mb-0 text-danger">{{ $stats['critical'] }}</h3>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i class="fas fa-exclamation-circle text-danger fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Out of Stock</p>
                                <h3 class="mb-0 text-danger">{{ $stats['out_of_stock'] }}</h3>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i class="fas fa-box-open text-danger fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="unresolved" {{ request('status') == 'unresolved' ? 'selected' : '' }}>Unresolved
                            </option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="low_stock" {{ request('type') == 'low_stock' ? 'selected' : '' }}>Low Stock
                            </option>
                            <option value="out_of_stock" {{ request('type') == 'out_of_stock' ? 'selected' : '' }}>Out of
                                Stock</option>
                            <option value="expiring_soon" {{ request('type') == 'expiring_soon' ? 'selected' : '' }}>Expiring
                                Soon</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="severity" class="form-select" onchange="this.form.submit()">
                            <option value="">All Severities</option>
                            <option value="info" {{ request('severity') == 'info' ? 'selected' : '' }}>Info</option>
                            <option value="warning" {{ request('severity') == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.inventory.alerts.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Alerts Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Message</th>
                                <th>Severity</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alerts as $alert)
                                <tr>
                                    <td>
                                        @if($alert->inventoryItem)
                                            <div class="d-flex align-items-center">
                                                @if(!empty($alert->inventoryItem->image_url))
                                                    <img src="{{ $alert->inventoryItem->image_url }}"
                                                        alt="{{ $alert->inventoryItem->name }}" class="rounded me-2"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-box text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <a href="{{ route('admin.inventory.index', ['edit_item' => $alert->inventory_item_id]) }}" class="text-decoration-none">
                                                        <div class="fw-medium text-dark">{{ $alert->inventoryItem->name }}</div>
                                                    </a>
                                                    <small class="text-muted">{{ $alert->inventoryItem->sku }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">Item no longer exists</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $alert->type == 'out_of_stock' ? 'danger' : ($alert->type == 'low_stock' ? 'warning' : 'info') }}">
                                            {{ str_replace('_', ' ', ucfirst($alert->type)) }}
                                        </span>
                                    </td>
                                    <td>{{ $alert->message }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $alert->severity == 'critical' ? 'danger' : ($alert->severity == 'warning' ? 'warning' : 'info') }}">
                                            {{ ucfirst($alert->severity) }}
                                        </span>
                                    </td>
                                    <td>{{ $alert->created_at->diffForHumans() }}</td>
                                    <td>
                                        @if($alert->is_resolved)
                                            <span class="badge bg-success">Resolved</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(!$alert->is_resolved)
                                            <form action="{{ route('admin.inventory.alerts.resolve', $alert) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" title="Resolve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($alert->inventoryItem)
                                            <a href="{{ route('admin.inventory.index', ['edit_item' => $alert->inventory_item_id]) }}"
                                                class="btn btn-sm btn-primary" title="View / Edit Product">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        No alerts found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($alerts->hasPages())
                <div class="card-footer bg-white">
                    {{ $alerts->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Confirmation Modal: Generate Stock Alerts -->
    <div class="modal fade" id="confirmGenerateAlertsModal" tabindex="-1" aria-labelledby="confirmGenerateAlertsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 py-3 px-4 text-white" style="background: linear-gradient(135deg, #0b0f17 0%, #1a2232 100%);">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="background: rgba(197, 168, 128, 0.2); width: 40px; height: 40px;">
                            <i class="fas fa-bell" style="color: #c5a880; font-size: 18px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-white" id="confirmGenerateAlertsModalLabel" style="font-size: 16px;">Generate Stock Alerts</h5>
                            <small class="text-white-50" style="font-size: 12px;">Threshold scan across all branches</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="p-2.5 rounded-circle bg-warning bg-opacity-10 text-warning flex-shrink-0" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Generate alerts for all inventory items?</h6>
                            <p class="text-muted small mb-0">
                                This action will evaluate current stock quantities against reorder levels for all items and branches, automatically flagging items that are low or out of stock.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light px-3 rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4 rounded-3 fw-semibold" id="btnExecuteGenerateAlerts" onclick="executeGenerateAlerts()">
                        <i class="fas fa-sync me-1.5"></i> Generate Alerts
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function executeGenerateAlerts() {
                const modalEl = document.getElementById('confirmGenerateAlertsModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                const btn = document.getElementById('btnExecuteGenerateAlerts');

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5"></span> Generating...';

                fetch('{{ route("admin.inventory.alerts.generate") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    modal.hide();
                    if (data.success) {
                        if (typeof window.showModalAlert === 'function') {
                            window.showModalAlert(data.message || 'Stock alerts generated successfully!', 'success', 'Success', () => {
                                location.reload();
                            });
                        } else {
                            location.reload();
                        }
                    } else {
                        const errMsg = data.message || 'Failed to generate stock alerts.';
                        if (typeof window.showModalAlert === 'function') {
                            window.showModalAlert(errMsg, 'error', 'Error');
                        }
                    }
                })
                .catch(err => {
                    modal.hide();
                    if (typeof window.showModalAlert === 'function') {
                        window.showModalAlert('A network error occurred while generating stock alerts.', 'error', 'Error');
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-sync me-1.5"></i> Generate Alerts';
                });
            }
        </script>
    @endpush
@endsection