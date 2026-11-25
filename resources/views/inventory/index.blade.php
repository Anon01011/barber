@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold mb-1 text-dark">Inventory & Stock</h2>
            <p class="text-muted small mb-0">Manage retail products, salon & barber backbar consumables, and stock levels</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            @can('products.export')
                <a href="{{ route('admin.products.export') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Export CSV">
                    <i class="fas fa-file-csv me-1 text-success"></i> Export
                </a>
            @endcan

            @can('products.import')
                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#importProductModal">
                    <i class="fas fa-file-import me-1 text-primary"></i> Import
                </button>
            @endcan

            <button class="btn btn-outline-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-tags me-1"></i> New Category
            </button>

            <button class="btn btn-dark btn-sm rounded-pill px-3.5 py-1.5 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                <i class="fas fa-plus me-1 text-warning"></i> Add New Product
            </button>
        </div>
    </div>

    {{-- 1. Top Overview KPI Cards --}}
    @include('inventory.partials.kpi-summary')

    {{-- 2. Segment Tabs & Search Filter Bar --}}
    @include('inventory.partials.filter-bar')

    {{-- 3. Inventory Data Table --}}
    @include('inventory.partials.table')

    {{-- 4. Modals --}}
    @include('inventory.partials.create-item-modal')
    @include('inventory.partials.edit-item-modal')
    @include('inventory.partials.quick-stock-modal')
    @include('inventory.partials.category-modal')
    @include('inventory.partials.import-export-modal')
</div>
@endsection

@push('scripts')
<script>
    // Helper to populate fields in Edit Inventory Modal
    function populateEditModal(item) {
        if (!item) return;

        $('#editName').val(item.name || '');
        $('#editSku').val(item.sku || '');
        $('#editBarcode').val(item.barcode || '');
        $('#editCategory').val(item.category_id || '');
        $('#editBrand').val(item.brand || '');
        $('#editDescription').val(item.description || '');
        $('#editCostPrice').val(item.purchase_price !== null && item.purchase_price !== undefined ? parseFloat(item.purchase_price).toFixed(2) : '0.00');
        $('#editSellingPrice').val(item.selling_price !== null && item.selling_price !== undefined ? parseFloat(item.selling_price).toFixed(2) : '0.00');
        $('#editQuantity').val(item.quantity_in_stock || 0);
        $('#editReorderLevel').val(item.reorder_level || 0);
        $('#editPackCapacity').val(item.pack_capacity || 1);
        $('#editUnitType').val(item.unit_type || 'pcs');
        $('#editBaseUnit').val(item.base_unit || 'pcs');
        $('#editNotes').val(item.notes || '');
        $('#editStatus').prop('checked', !!item.is_active);

        // Set product_type radio
        const pType = item.product_type || 'retail';
        if (pType === 'consumable') {
            $('#editTypeConsumable').prop('checked', true);
        } else if (pType === 'dual') {
            $('#editTypeDual').prop('checked', true);
        } else {
            $('#editTypeRetail').prop('checked', true);
        }

        // Image preview
        if (item.image_url) {
            $('#productImagePreview').attr('src', item.image_url);
        } else {
            $('#productImagePreview').attr('src', 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiB2aWV3Qm94PSIwIDAgMjAwIDIwMCI+CiAgPHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNlZWVlZWUiLz4KICA8dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBhbGlnbm1lbnQtYmFzZWxpbmU9Im1pZGRsZSIgZmlsbD0iIzk5OSI+CiAgICBObyBJbWFnZQogIDwvdGV4dD4KPC9zdmc+');
        }

        // Multi-branch checkboxes
        $('.edit-branch-checkbox').prop('checked', false);
        if (item.branches && Array.isArray(item.branches)) {
            item.branches.forEach(function(b) {
                $('#branch_edit_' + b.id).prop('checked', true);
            });
        } else if (item.branch_id) {
            $('#branch_edit_' + item.branch_id).prop('checked', true);
        }
    }

    // Edit Item Modal Population
    function editItem(itemId, btn) {
        const modal = $('#editInventoryModal');
        const form = $('#editInventoryForm');

        const baseUrl = "{{ route('admin.inventory.index', ['salon_slug' => request()->route('salon_slug') ?? (auth()->user()?->salon?->slug ?? '')]) }}";
        form.attr('action', baseUrl + "/" + itemId);

        // Instant population from button data attribute if available
        if (btn) {
            try {
                const raw = btn.getAttribute('data-item');
                if (raw) {
                    const itemData = typeof raw === 'string' ? JSON.parse(raw) : raw;
                    populateEditModal(itemData);
                }
            } catch (e) {
                console.warn('Could not parse item data from attribute', e);
            }
        }

        modal.modal('show');

        // Fetch fresh details via AJAX
        $.ajax({
            url: baseUrl + "/" + itemId + "/edit",
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(data) {
                const item = data.item || data;
                populateEditModal(item);
            },
            error: function(xhr) {
                console.warn('AJAX sync warning (using cached data):', xhr.status);
            }
        });
    }

    $(document).ready(function() {
        // Auto-open Edit Item modal if passed via URL parameter (e.g. from alerts or direct link)
        const urlParams = new URLSearchParams(window.location.search);
        const autoEditId = urlParams.get('edit_item') || urlParams.get('edit');
        if (autoEditId) {
            setTimeout(function() {
                editItem(autoEditId);
            }, 300);
        }

        // Barcode generator helper
        $('#generateBarcodeBtn').click(function() {
            const randomCode = 'BAR' + Math.floor(100000 + Math.random() * 900000);
            $('#editBarcode').val(randomCode);
        });

        // Image upload trigger for Edit Modal
        const editImageInput = document.getElementById('imageUpload');
        const editImagePreview = document.getElementById('productImagePreview');
        const editUploadBtn = document.getElementById('uploadImageBtn');

        if (editUploadBtn && editImageInput) {
            editUploadBtn.addEventListener('click', () => editImageInput.click());
            editImageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        editImagePreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endpush
