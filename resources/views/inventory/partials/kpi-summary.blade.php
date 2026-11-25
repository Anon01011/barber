{{-- Inventory Overview KPI Cards --}}
<div class="row g-3 mb-4">
    <!-- Total Valuation -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Stock Value</span>
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary">
                        <i class="fas fa-vault fa-sm"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">{{ currency_symbol() }}{{ number_format($kpiStats['total_valuation'] ?? 0, 2) }}</h4>
                <small class="text-muted d-block">{{ $kpiStats['total_items'] ?? 0 }} total products in catalog</small>
            </div>
        </div>
    </div>

    <!-- Retail Items -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Retail Shelf</span>
                    <div class="rounded-3 p-2 bg-success-subtle text-success">
                        <i class="fas fa-shopping-bag fa-sm"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">{{ $kpiStats['retail_items'] ?? 0 }} <span class="fs-6 fw-normal text-muted">SKUs</span></h4>
                <small class="text-success fw-medium"><i class="fas fa-check-circle me-1"></i>Available for POS Checkout</small>
            </div>
        </div>
    </div>

    <!-- Consumables / Backbar -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Backbar / In-Service</span>
                    <div class="rounded-3 p-2 bg-info-subtle text-info">
                        <i class="fas fa-flask fa-sm"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">{{ $kpiStats['consumable_items'] ?? 0 }} <span class="fs-6 fw-normal text-muted">SKUs</span></h4>
                <small class="text-muted">Used in salon & barber chair</small>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Low Stock Notice</span>
                    <div class="rounded-3 p-2 {{ ($kpiStats['low_stock_items'] ?? 0) > 0 ? 'bg-danger-subtle text-danger' : 'bg-light text-muted' }}">
                        <i class="fas fa-exclamation-triangle fa-sm"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 {{ ($kpiStats['low_stock_items'] ?? 0) > 0 ? 'text-danger' : 'text-dark' }}">
                    {{ $kpiStats['low_stock_items'] ?? 0 }} <span class="fs-6 fw-normal text-muted">Needs Reorder</span>
                </h4>
                @if(($kpiStats['low_stock_items'] ?? 0) > 0)
                    <a href="{{ route('admin.inventory.index', ['status' => 'low']) }}" class="small text-danger fw-semibold text-decoration-none">
                        View Reorder List <i class="fas fa-arrow-right fa-xs ms-1"></i>
                    </a>
                @else
                    <small class="text-muted">All stock levels healthy</small>
                @endif
            </div>
        </div>
    </div>
</div>
