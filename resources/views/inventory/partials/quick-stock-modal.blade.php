{{-- Quick Stock In/Out Adjustment Modal --}}
<div class="modal fade" id="quickStockModal" tabindex="-1" aria-labelledby="quickStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-2 bg-white bg-opacity-10 me-3">
                        <i class="fas fa-dolly fa-sm text-warning"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="quickStockModalLabel">Adjust Stock Level</h6>
                        <small class="text-white-50" id="quickStockItemName">Product Name</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="quickStockForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    {{-- Current Stock Badge --}}
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-3">
                        <span class="text-muted small fw-semibold">Current Stock on Hand:</span>
                        <span class="fw-bold fs-6 text-dark" id="quickStockCurrentDisplay">0 pcs</span>
                    </div>

                    {{-- Adjustment Type Radio Pill Group --}}
                    <label class="form-label small fw-bold text-dark mb-2">Adjustment Action</label>
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="type" id="stockTypeIn" value="purchase" checked onchange="updateStockProjected()">
                            <label class="btn btn-outline-success w-100 py-2 small fw-semibold rounded-3 text-nowrap" for="stockTypeIn">
                                <i class="fas fa-plus-circle me-1"></i> Stock In
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="type" id="stockTypeOut" value="damaged" onchange="updateStockProjected()">
                            <label class="btn btn-outline-danger w-100 py-2 small fw-semibold rounded-3 text-nowrap" for="stockTypeOut">
                                <i class="fas fa-minus-circle me-1"></i> Waste/Loss
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="type" id="stockTypeAdjust" value="adjustment" onchange="updateStockProjected()">
                            <label class="btn btn-outline-primary w-100 py-2 small fw-semibold rounded-3 text-nowrap" for="stockTypeAdjust">
                                <i class="fas fa-sync-alt me-1"></i> Audit Fix
                            </label>
                        </div>
                    </div>

                    {{-- Quantity Input --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Quantity</label>
                        <div class="input-group">
                            <input type="number" step="any" min="0.001" name="quantity" id="quickStockQty" 
                                class="form-control fw-bold fs-6" placeholder="0" required oninput="updateStockProjected()">
                            <span class="input-group-text bg-light text-muted" id="quickStockUnitLabel">pcs</span>
                        </div>
                    </div>

                    {{-- Cost price (optional/calculated) --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Unit Cost (Wholesale)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light">{{ currency_symbol() }}</span>
                            <input type="number" step="0.01" min="0" name="unit_cost" id="quickStockUnitCost" class="form-control" placeholder="0.00">
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">Optional. Leave blank to use existing purchase price.</small>
                    </div>

                    {{-- Reason / Notes --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Reason / Notes</label>
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="e.g., Supplier shipment, broken bottle, quarterly stock audit">
                    </div>

                    {{-- Projected Stock Preview --}}
                    <div class="p-2.5 rounded-3 border bg-light text-center">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">New Calculated Stock Level</small>
                        <span class="fw-bold fs-6" id="quickStockProjected">0 pcs</span>
                    </div>
                </div>

                <div class="modal-footer border-0 p-3 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Save Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentItemStock = 0;
    let currentItemUnit = 'pcs';
    const stockUrlTemplate = "{{ route('admin.inventory.stock.update', ['salon_slug' => request()->route('salon_slug') ?? (auth()->user()?->salon?->slug ?? ''), 'inventory' => '__ID__']) }}";

    function openQuickStockModal(itemId, itemName, currentStock, unit, unitCost) {
        currentItemStock = parseFloat(currentStock) || 0;
        currentItemUnit = unit || 'pcs';

        document.getElementById('quickStockItemName').textContent = itemName;
        document.getElementById('quickStockCurrentDisplay').textContent = currentItemStock + ' ' + currentItemUnit;
        document.getElementById('quickStockUnitLabel').textContent = currentItemUnit;
        document.getElementById('quickStockUnitCost').value = unitCost ? parseFloat(unitCost).toFixed(2) : '';
        document.getElementById('quickStockQty').value = '';
        document.getElementById('stockTypeIn').checked = true;
        
        const form = document.getElementById('quickStockForm');
        form.action = stockUrlTemplate.replace('__ID__', itemId);

        updateStockProjected();
        new bootstrap.Modal(document.getElementById('quickStockModal')).show();
    }

    function updateStockProjected() {
        const qtyInput = document.getElementById('quickStockQty');
        const isIn = document.getElementById('stockTypeIn').checked;
        const isOut = document.getElementById('stockTypeOut').checked;
        const isAdjust = document.getElementById('stockTypeAdjust').checked;

        if (isAdjust) {
            qtyInput.min = "0";
        } else {
            qtyInput.min = "0.001";
        }

        const qty = parseFloat(qtyInput.value) || 0;

        let projected = currentItemStock;
        if (isIn) {
            projected = currentItemStock + qty;
        } else if (isOut) {
            projected = Math.max(0, currentItemStock - qty);
        } else if (isAdjust) {
            projected = qty; // Direct count replacement
        }

        const projEl = document.getElementById('quickStockProjected');
        projEl.textContent = (Math.round(projected * 100) / 100) + ' ' + currentItemUnit;
        projEl.className = projected <= 0 ? 'fw-bold fs-6 text-danger' : 'fw-bold fs-6 text-success';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.adjust-stock-btn');
            if (btn) {
                openQuickStockModal(
                    btn.dataset.itemId,
                    btn.dataset.itemName,
                    btn.dataset.stock,
                    btn.dataset.unit,
                    btn.dataset.unitCost
                );
            }
        });
    });
</script>
