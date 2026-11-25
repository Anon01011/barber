{{-- Inventory Data Table --}}
<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light border-bottom">
                <tr class="text-muted small text-uppercase fw-semibold">
                    <th class="ps-4 py-3" style="min-width: 260px;">Product Name & SKU</th>
                    <th class="py-3">Type</th>
                    <th class="py-3">Category</th>
                    <th class="py-3" style="min-width: 170px;">Stock Level</th>
                    <th class="py-3 text-end">Purchase Cost</th>
                    <th class="py-3 text-end">Selling Price</th>
                    <th class="pe-4 py-3 text-end text-nowrap" style="min-width: 130px; width: 130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        {{-- Product Name, Image/Icon & SKU --}}
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center">
                                @if($item->image_path)
                                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" 
                                        class="rounded-3 me-3 object-fit-cover shadow-sm" style="width: 42px; height: 42px;">
                                @else
                                    <div class="rounded-3 p-2 bg-light text-muted me-3 d-flex align-items-center justify-content-center shadow-sm" 
                                        style="width: 42px; height: 42px;">
                                        <i class="fas fa-{{ $item->category->icon ?? 'box' }} fa-sm"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold text-dark mb-0.5">{{ $item->name }}</div>
                                    <div class="d-flex align-items-center gap-2">
                                        <code class="text-muted small" style="font-size: 0.78rem;">{{ $item->sku }}</code>
                                        @if($item->barcode)
                                            <span class="text-muted small" style="font-size: 0.75rem;">
                                                <i class="fas fa-barcode me-0.5"></i>{{ $item->barcode }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Product Type Badge --}}
                        <td class="py-3">
                            @if($item->product_type === 'consumable')
                                <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1 small fw-semibold">
                                    <i class="fas fa-flask me-1 fa-xs"></i>Backbar
                                </span>
                            @elseif($item->product_type === 'dual')
                                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1 small fw-semibold">
                                    <i class="fas fa-arrows-split-up-and-left me-1 fa-xs"></i>Dual-Use
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 small fw-semibold">
                                    <i class="fas fa-tag me-1 fa-xs"></i>Retail
                                </span>
                            @endif
                        </td>

                        {{-- Category & Branch Tags --}}
                        <td class="py-3">
                            <span class="fw-medium text-dark small">{{ $item->category->name ?? 'Uncategorized' }}</span>
                            @if($branches->count() > 1 && $item->branches->count() > 0)
                                <div class="mt-1 d-flex flex-wrap gap-1">
                                    @foreach($item->branches as $branch)
                                        <span class="badge bg-light text-muted border rounded-pill px-2" style="font-size: 0.7rem;">
                                            {{ $branch->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>

                        {{-- Stock Level & Progress Bar --}}
                        <td class="py-3">
                            @php
                                $stock = (float) $item->quantity_in_stock;
                                $minStock = (float) $item->minimum_quantity;
                                $reorderLevel = (float) ($item->reorder_level ?? 0);
                                $unit = $item->unit_type ?? 'pcs';

                                $isOutOfStock = $stock <= 0;
                                $isLowStock = !$isOutOfStock && (($reorderLevel > 0 && $stock <= $reorderLevel) || ($minStock > 0 && $stock <= $minStock));
                                
                                $badgeClass = $isOutOfStock ? 'bg-danger-subtle text-danger' : ($isLowStock ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success');
                                $barClass = $isOutOfStock ? 'bg-danger' : ($isLowStock ? 'bg-warning' : 'bg-success');
                                $percent = $minStock > 0 ? min(100, ($stock / $minStock) * 100) : ($stock > 0 ? 100 : 0);
                            @endphp
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="fw-bold small {{ $isOutOfStock ? 'text-danger' : 'text-dark' }}">
                                    {{ rtrim(rtrim(number_format($stock, 2), '0'), '.') }} {{ $unit }}
                                </span>
                                <span class="badge {{ $badgeClass }} rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                    {{ $isOutOfStock ? 'Out of Stock' : ($isLowStock ? 'Low Stock' : 'In Stock') }}
                                </span>
                            </div>
                            <div class="progress" style="height: 5px; border-radius: 4px;">
                                <div class="progress-bar {{ $barClass }}" role="progressbar" style="width: {{ $percent }}%;"></div>
                            </div>
                        </td>

                        {{-- Purchase Price & Unit Breakdown --}}
                        <td class="py-3 text-end">
                            <div class="fw-semibold text-dark small">
                                {{ currency_symbol() }}{{ number_format($item->purchase_price, 2) }}
                            </div>
                            @if($item->pack_capacity > 1)
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    {{ currency_symbol() }}{{ number_format($item->cost_per_unit, 3) }}/{{ $item->base_unit ?? $item->unit_type }}
                                </small>
                            @endif
                        </td>

                        {{-- Selling Price (Retail) --}}
                        <td class="py-3 text-end">
                            @if($item->product_type === 'consumable')
                                <span class="text-muted small fst-italic">In-Service</span>
                            @else
                                <span class="fw-bold text-dark small">
                                    {{ currency_symbol() }}{{ number_format($item->selling_price, 2) }}
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="pe-4 py-3 text-end text-nowrap">
                            <div class="d-inline-flex align-items-center justify-content-end gap-1.5">
                                {{-- Direct Quick Edit Button --}}
                                <button type="button" class="btn btn-sm btn-action-icon text-primary"
                                    onclick="editItem({{ $item->id }}, this)"
                                    data-item="{{ json_encode($item) }}"
                                    title="Edit Product"
                                    aria-label="Edit Product">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>

                                {{-- Direct Quick Adjust Stock Button --}}
                                <button type="button" class="btn btn-sm btn-action-icon text-emerald adjust-stock-btn"
                                    data-item-id="{{ $item->id }}"
                                    data-item-name="{{ $item->name }}"
                                    data-stock="{{ $stock }}"
                                    data-unit="{{ $unit }}"
                                    data-unit-cost="{{ $item->purchase_price }}"
                                    title="Adjust Stock"
                                    aria-label="Adjust Stock">
                                    <i class="fas fa-boxes"></i>
                                </button>

                                {{-- More Actions Dropdown --}}
                                <div class="dropdown d-inline-block">
                                    <button type="button" class="btn btn-sm btn-action-icon text-secondary"
                                        data-bs-toggle="dropdown" aria-expanded="false" title="More Options" aria-label="More Options">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-light-subtle rounded-3 py-1.5" style="font-size: 0.85rem; min-width: 170px; z-index: 1050;">
                                        <li>
                                            <button class="dropdown-item py-1.5 d-flex align-items-center" type="button" 
                                                onclick="editItem({{ $item->id }}, this)"
                                                data-item="{{ json_encode($item) }}">
                                                <i class="fas fa-pencil-alt text-primary me-2.5 fa-fw"></i>
                                                <span>Edit Product</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item py-1.5 d-flex align-items-center adjust-stock-btn" type="button" 
                                                data-item-id="{{ $item->id }}"
                                                data-item-name="{{ $item->name }}"
                                                data-stock="{{ $stock }}"
                                                data-unit="{{ $unit }}"
                                                data-unit-cost="{{ $item->purchase_price }}">
                                                <i class="fas fa-boxes text-emerald me-2.5 fa-fw"></i>
                                                <span>Adjust Stock</span>
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form action="{{ route('admin.inventory.destroy', $item->id) }}" method="POST" 
                                                onsubmit="return confirm('Are you sure you want to delete {{ addslashes($item->name) }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger py-1.5 d-flex align-items-center">
                                                    <i class="fas fa-trash-alt me-2.5 fa-fw"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <i class="fas fa-box-open text-muted fa-3x mb-3 opacity-50"></i>
                                <h6 class="fw-bold text-dark mb-1">No Products Found</h6>
                                <p class="text-muted small mb-3">No products match your current filters or search term.</p>
                                <button class="btn btn-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                                    <i class="fas fa-plus me-1"></i> Add First Product
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Footer --}}
    @if($items->hasPages())
        <div class="card-footer bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
            <small class="text-muted">
                Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} items
            </small>
            <div>
                {{ $items->links() }}
            </div>
        </div>
    @endif
</div>

<style>
.btn-action-icon {
    width: 32px !important;
    height: 32px !important;
    min-width: 32px !important;
    min-height: 32px !important;
    padding: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
    transition: all 0.15s ease-in-out !important;
    font-size: 0.82rem !important;
}
.btn-action-icon:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08) !important;
    transform: translateY(-1px);
}
.btn-action-icon:active {
    transform: translateY(0);
}
.text-emerald {
    color: #059669 !important;
}
</style>
