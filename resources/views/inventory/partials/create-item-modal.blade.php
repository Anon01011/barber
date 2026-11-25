<!-- Modern Luxury Product Creation Modal -->
<div class="modal fade" id="addInventoryModal" tabindex="-1" aria-labelledby="addInventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-height: calc(100vh - 2.5rem);">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden d-flex flex-column" style="background: #ffffff; max-height: calc(100vh - 2.5rem); height: 100%;">
            <!-- Modal Header with Dark Luxury Gradient -->
            <div class="modal-header border-0 py-3.5 px-4 text-white flex-shrink-0"
                style="background: linear-gradient(135deg, #0b0f17 0%, #1a2232 100%); border-bottom: 1px solid rgba(197, 168, 128, 0.25) !important;">
                <div class="d-flex align-items-center">
                    <div class="modal-badge-icon me-3 p-2 rounded-3 d-flex align-items-center justify-content-center"
                        style="background: rgba(197, 168, 128, 0.18); width: 40px; height: 40px; border: 1px solid rgba(197, 168, 128, 0.35);">
                        <i class="fas fa-box-open" style="color: #c5a880; font-size: 18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="addInventoryModalLabel" style="font-size: 17px; letter-spacing: -0.2px;">
                            Create New Product
                        </h5>
                        <small style="color: #94a3b8; font-size: 12px;">Configure catalog specifications, pricing, packaging and inventory rules</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.inventory.store') }}" method="POST" id="createInventoryForm"
                enctype="multipart/form-data" class="needs-validation d-flex flex-column overflow-hidden flex-grow-1" style="min-height: 0; height: 100%;" novalidate>
                @csrf

                <!-- Interactive Progress Line -->
                <div class="progress flex-shrink-0" style="height: 3px; border-radius: 0; background: #e2e8f0;">
                    <div class="progress-bar" role="progressbar" id="formProgress"
                        style="width: 25%; background: linear-gradient(90deg, #5b5fc7, #10b981); transition: width 0.3s ease;"
                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <!-- Global Alert Notifications -->
                <div class="px-4 pt-2 pb-0 flex-shrink-0">
                    <div id="formAlert" class="alert alert-dismissible fade d-none py-2 px-3 small rounded-3" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2 fs-6"></i>
                            <div id="formAlertMessage"></div>
                        </div>
                        <button type="button" class="btn-close btn-sm p-2" data-bs-dismiss="alert"></button>
                    </div>
                </div>

                <div class="modal-body px-4 py-3 flex-grow-1" style="overflow-y: auto; min-height: 0;">
                    <!-- Luxury Step Navigation Tabs -->
                    <ul class="nav nav-pills nav-fill mb-3.5 product-tabs-nav" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-3 py-2 px-3 small fw-semibold" id="tab-basic"
                                data-bs-toggle="pill" data-bs-target="#basic" type="button" role="tab">
                                <i class="fas fa-info-circle me-1.5"></i>
                                <span>1. Basic Info</span>
                                <span class="text-danger ms-0.5">*</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-2 px-3 small fw-semibold" id="tab-pricing"
                                data-bs-toggle="pill" data-bs-target="#pricing" type="button" role="tab">
                                <i class="fas fa-tag me-1.5"></i>
                                <span>2. Pricing & Margin</span>
                                <span class="text-danger ms-0.5">*</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-2 px-3 small fw-semibold" id="tab-stock"
                                data-bs-toggle="pill" data-bs-target="#stock" type="button" role="tab">
                                <i class="fas fa-boxes me-1.5"></i>
                                <span>3. Stock & Packaging</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-2 px-3 small fw-semibold" id="tab-details"
                                data-bs-toggle="pill" data-bs-target="#details" type="button" role="tab">
                                <i class="fas fa-sliders-h me-1.5"></i>
                                <span>4. Specifications & Specs</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="productTabsContent">
                        <!-- ==================== TAB 1: BASIC INFO ==================== -->
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <div class="row g-3">
                                <!-- Left Column: Essential Details -->
                                <div class="col-lg-8">
                                    <!-- Operational Classification (3 Purpose Cards) -->
                                    <div class="p-3 rounded-3 bg-light border mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label small fw-bold text-dark mb-0">
                                                <i class="fas fa-layer-group me-1.5 text-primary"></i>Product Purpose & Classification <span class="text-danger">*</span>
                                            </label>
                                            <span class="badge bg-secondary-subtle text-secondary small" style="font-size: 0.72rem;">Defines POS & Service behavior</span>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="product_type" id="createTypeRetail" value="retail" checked>
                                                <label class="btn purpose-card purpose-btn-retail w-100 text-start p-2.5 h-100" for="createTypeRetail">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="purpose-icon-badge bg-primary text-white"><i class="fas fa-shopping-bag"></i></span>
                                                        <strong class="small text-dark">Retail Product</strong>
                                                    </div>
                                                    <small class="text-muted d-block lh-sm" style="font-size: 0.73rem;">Sold directly to clients at the front desk / POS counter.</small>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="product_type" id="createTypeConsumable" value="consumable">
                                                <label class="btn purpose-card purpose-btn-backbar w-100 text-start p-2.5 h-100" for="createTypeConsumable">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="purpose-icon-badge bg-emerald text-white"><i class="fas fa-flask"></i></span>
                                                        <strong class="small text-dark">Backbar Consumable</strong>
                                                    </div>
                                                    <small class="text-muted d-block lh-sm" style="font-size: 0.73rem;">Dispensed by barbers/stylists during services via recipes.</small>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="product_type" id="createTypeDual" value="dual">
                                                <label class="btn purpose-card purpose-btn-dual w-100 text-start p-2.5 h-100" for="createTypeDual">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="purpose-icon-badge bg-amber text-white"><i class="fas fa-sync-alt"></i></span>
                                                        <strong class="small text-dark">Dual-Use Hybrid</strong>
                                                    </div>
                                                    <small class="text-muted d-block lh-sm" style="font-size: 0.73rem;">Both sold as retail and used internally in backbar services.</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Product Name -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label small fw-semibold text-dark mb-0">
                                                Product Name <span class="text-danger">*</span>
                                            </label>
                                            <small class="text-muted" style="font-size: 0.72rem;"><span id="nameCounter">0</span>/255</small>
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-box text-primary"></i></span>
                                            <input type="text" name="name" id="productName" class="form-control border-start-0 ps-0"
                                                placeholder="e.g., Matte Clay Hair Pomade 100ml" required maxlength="255" autofocus>
                                        </div>
                                    </div>

                                    <!-- Category & Brand Row -->
                                    <div class="row g-3 mb-3">
                                        <!-- Category with Seamless Quick-Add -->
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-dark mb-1">
                                                Category <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-folder text-primary"></i></span>
                                                <select name="category_id" id="createProductCategory" class="form-select border-start-0 ps-0" required>
                                                    <option value="">Select Category...</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="btn btn-outline-primary px-3 fw-semibold d-flex align-items-center gap-1"
                                                    type="button" id="btnQuickAddCategory" title="Add New Category">
                                                    <i class="fas fa-plus"></i>
                                                    <span class="d-none d-sm-inline">New</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Brand -->
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-dark mb-1">Brand / Line</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-copyright text-primary"></i></span>
                                                <input type="text" name="brand" class="form-control border-start-0 ps-0" placeholder="e.g., Reuzel, Uppercut, Moroccanoil" maxlength="255">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Manufacturer & SKU / Barcode Row -->
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-dark mb-1">Manufacturer / Vendor</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-industry text-primary"></i></span>
                                                <input type="text" name="manufacturer" class="form-control border-start-0 ps-0" placeholder="e.g., L'Oréal Professional" maxlength="255">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="form-label small fw-semibold text-dark mb-0">SKU (Stock Keeping Unit)</label>
                                                <small class="text-muted" style="font-size: 0.7rem;">Auto-generates if left blank</small>
                                            </div>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-barcode text-primary"></i></span>
                                                <input type="text" name="sku" id="productSku" class="form-control border-start-0 border-end-0 ps-0 text-uppercase"
                                                    placeholder="e.g., POM-MAT-100" maxlength="100">
                                                <button class="btn btn-outline-secondary px-2.5" type="button" id="generateSkuBtn" title="Generate SKU">
                                                    <i class="fas fa-magic text-warning me-1"></i><small class="fw-semibold">Auto</small>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label small fw-semibold text-dark mb-0">Description & Key Benefits</label>
                                            <small class="text-muted" style="font-size: 0.72rem;"><span id="descCounter">0</span>/2000</small>
                                        </div>
                                        <textarea name="description" id="productDescription" class="form-control form-control-sm" rows="3"
                                            placeholder="Detailed description, fragrance profile, usage instructions, ingredients..." maxlength="2000"></textarea>
                                    </div>
                                </div>

                                <!-- Right Column: Image & Operational Toggles -->
                                <div class="col-lg-4">
                                    <!-- Product Image Card -->
                                    <div class="card border border-light-subtle rounded-3 shadow-none mb-3 overflow-hidden">
                                        <div class="card-header bg-light py-2 px-3 border-0">
                                            <h6 class="mb-0 small fw-bold text-dark"><i class="fas fa-image me-1.5 text-primary"></i>Product Image</h6>
                                        </div>
                                        <div class="card-body p-3 text-center">
                                            <div class="image-upload-dropzone p-3 border border-2 border-dashed rounded-3 text-center position-relative"
                                                id="imageUploadArea" style="cursor: pointer; background: #fafafa; transition: all 0.2s ease;">
                                                <div class="image-preview d-none text-center" id="imagePreview">
                                                    <img src="#" alt="Preview" class="img-fluid rounded-2 mb-2 shadow-sm" style="max-height: 140px; object-fit: contain;">
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" id="removeImage" style="font-size: 0.75rem;">
                                                            <i class="fas fa-trash-alt me-1"></i>Remove Photo
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="upload-placeholder py-3" id="uploadPlaceholder">
                                                    <div class="mb-2">
                                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light shadow-sm" style="width: 44px; height: 44px;">
                                                            <i class="fas fa-cloud-upload-alt text-primary fs-5"></i>
                                                        </span>
                                                    </div>
                                                    <p class="small text-dark fw-semibold mb-0">Click or Drag Image</p>
                                                    <small class="text-muted d-block" style="font-size: 0.72rem;">PNG, JPG, WEBP up to 5MB</small>
                                                    <button type="button" class="btn btn-xs btn-outline-primary mt-2 px-2.5" id="browseImage">
                                                        Browse File
                                                    </button>
                                                </div>
                                                <input type="file" name="image" id="productImage" class="d-none" accept="image/jpeg,image/png,image/jpg,image/webp">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status & Availability Card -->
                                    <div class="card border border-light-subtle rounded-3 shadow-none">
                                        <div class="card-header bg-light py-2 px-3 border-0">
                                            <h6 class="mb-0 small fw-bold text-dark"><i class="fas fa-toggle-on me-1.5 text-primary"></i>Status & Tax</h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="form-check form-switch mb-2.5">
                                                <input class="form-check-input" type="checkbox" role="switch" id="isActive" name="is_active" value="1" checked>
                                                <label class="form-check-label small fw-semibold text-dark" for="isActive">
                                                    Active Product
                                                    <small class="text-muted d-block fw-normal" style="font-size: 0.72rem;">Visible in POS & inventory counts</small>
                                                </label>
                                            </div>
                                            <hr class="my-2 border-light">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" id="isTaxable" name="is_taxable" value="1" checked>
                                                <label class="form-check-label small fw-semibold text-dark" for="isTaxable">
                                                    Apply Tax
                                                    <small class="text-muted d-block fw-normal" style="font-size: 0.72rem;">Include in sales tax calculation</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== TAB 2: PRICING & MARGIN ==================== -->
                        <div class="tab-pane fade" id="pricing" role="tabpanel">
                            <div class="row g-3">
                                <!-- Cost Price -->
                                <div class="col-md-6">
                                    <div class="card border border-light-subtle rounded-3 shadow-none h-100">
                                        <div class="card-body p-3">
                                            <label class="form-label small fw-bold text-muted mb-1 d-block">Cost Price (Purchase / Wholesaler)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light fw-bold text-muted">{{ currency_symbol() }}</span>
                                                <input type="number" name="purchase_price" id="costPrice" class="form-control form-control-lg fw-bold"
                                                    placeholder="0.00" step="0.01" min="0" max="999999.99" value="0.00">
                                            </div>
                                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Used for inventory valuation & COGS expense calculation.</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selling Price -->
                                <div class="col-md-6">
                                    <div class="card border border-primary-subtle rounded-3 shadow-sm h-100" style="background: #fdfefe;">
                                        <div class="card-body p-3">
                                            <label class="form-label small fw-bold text-primary mb-1 d-block">
                                                Selling Price (Retail / POS) <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-primary text-white fw-bold">{{ currency_symbol() }}</span>
                                                <input type="number" name="selling_price" id="sellingPrice" class="form-control form-control-lg fw-bold text-dark"
                                                    placeholder="0.00" step="0.01" min="0" max="999999.99" required>
                                            </div>
                                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Price charged to customer before discounts and taxes.</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Live Calculated Profit & Margin Banner -->
                                <div class="col-12">
                                    <div class="card border-0 rounded-3 p-3 shadow-none" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1px solid #bbf7d0 !important;">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="p-2 rounded-2 bg-success text-white"><i class="fas fa-chart-line"></i></span>
                                                <div>
                                                    <strong class="d-block text-dark small">Live Profit & Margin Projection</strong>
                                                    <small class="text-muted" style="font-size: 0.75rem;">Calculated per retail unit sold</small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <span class="h5 mb-0 fw-bold text-success d-block" id="profitDisplay">
                                                    {{ currency_symbol() }}0.00 (0.0% Margin)
                                                </span>
                                                <span class="badge bg-success-subtle text-success small" id="profitBadge">Breakeven / Ready</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tax Rate -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark mb-1">Custom Tax Rate (%)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="fas fa-percentage"></i></span>
                                        <input type="number" name="tax_rate" class="form-control" placeholder="0" step="0.01" min="0" max="100" value="0">
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Leave 0 to use salon default tax settings.</small>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== TAB 3: STOCK & PACKAGING ==================== -->
                        <div class="tab-pane fade" id="stock" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">
                                        Opening Stock Quantity <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="fas fa-boxes text-primary"></i></span>
                                        <input type="number" name="quantity_in_stock" class="form-control" placeholder="0"
                                            step="any" min="0" max="999999.9999" value="0" required>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Current inventory on hand</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Unit Packaging Type</label>
                                    <select name="unit_type" class="form-select">
                                        <option value="pcs" selected>Pieces (pcs)</option>
                                        <option value="bottle">Bottle</option>
                                        <option value="box">Box</option>
                                        <option value="pack">Pack</option>
                                        <option value="can">Can / Spray</option>
                                        <option value="tube">Tube</option>
                                        <option value="jar">Jar</option>
                                        <option value="ml">Milliliters (ml)</option>
                                        <option value="g">Grams (g)</option>
                                        <option value="kg">Kilograms (kg)</option>
                                        <option value="l">Liters (l)</option>
                                    </select>
                                    <small class="text-muted" style="font-size: 0.72rem;">Container physical packaging format</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Container Pack Capacity</label>
                                    <div class="input-group">
                                        <input type="number" name="pack_capacity" class="form-control" placeholder="e.g., 1000" step="any" min="0.01" value="1">
                                        <span class="input-group-text bg-light text-muted">units</span>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Total ml, grams or units in 1 container</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Base Dispensing Unit</label>
                                    <select name="base_unit" class="form-select">
                                        <option value="pcs" selected>pcs (count)</option>
                                        <option value="ml">ml (milliliters)</option>
                                        <option value="g">g (grams)</option>
                                        <option value="pump">pump</option>
                                        <option value="drops">drops</option>
                                    </select>
                                    <small class="text-muted" style="font-size: 0.72rem;">Unit deducted in service recipe consumptions</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Low Stock Alert Level</label>
                                    <input type="number" name="reorder_level" class="form-control" placeholder="0" step="any" min="0" max="999999.9999" value="5">
                                    <small class="text-muted" style="font-size: 0.72rem;">Triggers low stock notice when reached</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Storage / Shelf Location</label>
                                    <input type="text" name="location" class="form-control" placeholder="e.g., Aisle 2, Shelf B-4" maxlength="100">
                                    <small class="text-muted" style="font-size: 0.72rem;">Physical location in salon or backbar</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark mb-1">Barcode / EAN / UPC</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="fas fa-barcode"></i></span>
                                        <input type="text" name="barcode" id="productBarcode" class="form-control" placeholder="Scan or enter barcode" maxlength="100">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark mb-1">Preferred Supplier</label>
                                    <select name="supplier_id" class="form-select">
                                        <option value="">Select Supplier...</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Multi-Branch Allocation --}}
                                @if($branches->count() > 1)
                                    <div class="col-12 mt-2">
                                        <label class="form-label small fw-bold text-dark mb-1">
                                            <i class="fas fa-map-marker-alt text-primary me-1"></i>Branch Availability <span class="text-danger">*</span>
                                        </label>
                                        <div class="p-3 bg-light rounded-3 border">
                                            <div class="row g-2">
                                                @foreach($branches as $branch)
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input branch-checkbox" type="checkbox"
                                                                name="branch_ids[]" value="{{ $branch->id }}"
                                                                id="branch_create_{{ $branch->id }}" {{ $branch->id == auth()->user()->branch_id ? 'checked' : '' }}>
                                                            <label class="form-check-label small fw-semibold text-dark" for="branch_create_{{ $branch->id }}">
                                                                {{ $branch->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <small class="text-muted">Select at least one branch where this product will be stocked.</small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- ==================== TAB 4: SPECIFICATIONS & SPECS ==================== -->
                        <div class="tab-pane fade" id="details" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Net Weight</label>
                                    <div class="input-group">
                                        <input type="number" name="weight" class="form-control" placeholder="0" step="0.001" min="0" max="999999.999">
                                        <span class="input-group-text bg-light text-muted">kg</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Dimensions (L × W × H)</label>
                                    <input type="text" name="dimensions" class="form-control" placeholder="e.g., 10 x 5 x 20 cm" maxlength="100">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Batch / Expiry Date</label>
                                    <input type="date" name="expiry_date" class="form-control" min="{{ date('Y-m-d') }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-dark mb-1">Internal Warehouse / Salon Notes</label>
                                    <textarea name="notes" class="form-control form-control-sm" rows="3"
                                        placeholder="Internal notes for inventory management, reordering reminders or batch info..." maxlength="1000"></textarea>
                                </div>

                                <!-- Product Variants Section -->
                                <div class="col-12 mt-2">
                                    <div class="card border border-light-subtle rounded-3 shadow-none overflow-hidden">
                                        <div class="card-header bg-light py-2 px-3 border-0 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 small fw-bold text-dark">
                                                <i class="fas fa-layer-group text-primary me-1.5"></i>Product Variants <span class="text-muted fw-normal">(Optional)</span>
                                            </h6>
                                            <button type="button" class="btn btn-sm btn-outline-primary py-0.5 px-2" id="addVariantBtn" style="font-size: 0.78rem;">
                                                <i class="fas fa-plus me-1"></i>Add Variant
                                            </button>
                                        </div>
                                        <div class="card-body p-3">
                                            <div id="variantsContainer">
                                                <div class="text-center text-muted py-2" id="noVariantsMsg">
                                                    <i class="fas fa-cubes text-muted opacity-50 mb-1 d-block fs-5"></i>
                                                    <small>No variants added. Click "Add Variant" to create sizes, scents, or colors.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer Navigation -->
                <div class="modal-footer border-top bg-light py-2.5 px-4 d-flex justify-content-between align-items-center flex-shrink-0" style="position: sticky; bottom: 0; z-index: 1050; background: #ffffff !important;">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" id="prevTabBtn" style="display: none;">
                        <i class="fas fa-arrow-left me-1"></i> Previous
                    </button>
                    <button type="button" class="btn btn-sm btn-light px-3" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-next-step" id="nextTabBtn">
                            Next Step <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                        <button type="submit" class="btn btn-sm btn-save-step" id="saveProductBtn" style="display: inline-block !important;">
                            <i class="fas fa-check me-1"></i> Save Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Variant Template -->
<template id="variantTemplate">
    <div class="variant-item border rounded-3 p-2 mb-2 bg-white position-relative shadow-2xs">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-1.5 remove-variant" aria-label="Remove" style="font-size: 0.68rem;"></button>
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label small mb-0 text-muted" style="font-size: 0.72rem;">Variant Name</label>
                <input type="text" name="variants[INDEX][name]" class="form-control form-control-sm" placeholder="e.g. Size, Scent">
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-0 text-muted" style="font-size: 0.72rem;">Value</label>
                <input type="text" name="variants[INDEX][value]" class="form-control form-control-sm" placeholder="e.g. 250ml, Sandalwood">
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-0 text-muted" style="font-size: 0.72rem;">Price Adjustment</label>
                <input type="number" name="variants[INDEX][price_adjustment]" class="form-control form-control-sm" value="0.00" step="0.01" placeholder="+0.00">
            </div>
        </div>
    </div>
</template>

<style>
    /* Luxury Scoped Styling for Product Modal */
    #addInventoryModal .modal-dialog {
        max-height: calc(100vh - 2.5rem);
    }
    #addInventoryModal .modal-content {
        max-height: calc(100vh - 2.5rem);
        display: flex;
        flex-direction: column;
    }
    #addInventoryModal form {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden;
    }
    #addInventoryModal .modal-body {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 0;
        max-height: none !important;
    }
    #addInventoryModal .modal-footer {
        flex-shrink: 0;
        background: #ffffff !important;
        border-top: 1px solid #e2e8f0;
    }

    #addInventoryModal .product-tabs-nav {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 5px;
        gap: 6px;
    }
    #addInventoryModal .product-tabs-nav .nav-link {
        color: #475569 !important;
        background: transparent;
        border: none;
        border-radius: 9px;
        font-size: 0.84rem;
        font-weight: 600;
        padding: 8px 14px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #addInventoryModal .product-tabs-nav .nav-link:hover:not(.active) {
        background: #e2e8f0;
        color: #0f172a !important;
    }
    #addInventoryModal .product-tabs-nav .nav-link.active {
        background: #0f172a !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.25);
    }

    /* Purpose Card Selection */
    .purpose-card {
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        border-radius: 10px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .purpose-card:hover {
        border-color: #94a3b8;
        transform: translateY(-1px);
    }
    .purpose-icon-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 7px;
        font-size: 11px;
    }
    .bg-emerald {
        background-color: #059669 !important;
    }
    .bg-amber {
        background-color: #d97706 !important;
    }

    /* Purpose Radios Active States */
    .btn-check:checked + .purpose-btn-retail {
        border-color: #0f172a !important;
        background: #f8fafc !important;
        box-shadow: 0 0 0 2px rgba(15, 23, 42, 0.15);
    }
    .btn-check:checked + .purpose-btn-backbar {
        border-color: #059669 !important;
        background: #f0fdf4 !important;
        box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.15);
    }
    .btn-check:checked + .purpose-btn-dual {
        border-color: #d97706 !important;
        background: #fffbeb !important;
        box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.15);
    }

    /* Buttons */
    .btn-next-step {
        background: #c5a880;
        color: #0b0f17;
        border: 1px solid #b8986c;
        border-radius: 8px;
        padding: 6px 18px;
        font-weight: 700;
        font-size: 0.86rem;
        transition: all 0.18s ease;
    }
    .btn-next-step:hover {
        background: #b59265;
        color: #000000;
    }
    .btn-save-step {
        background: #10b981;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 6px 20px;
        font-weight: 700;
        font-size: 0.86rem;
        transition: all 0.18s ease;
    }
    .btn-save-step:hover {
        background: #059669;
        color: #ffffff;
    }
    .image-upload-dropzone:hover {
        border-color: #5b5fc7 !important;
        background: #f8faff !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('addInventoryModal');
    if (!modalEl) return;

    const tabs = ['basic', 'pricing', 'stock', 'details'];
    let currentTabIndex = 0;

    const nextBtn = document.getElementById('nextTabBtn');
    const prevBtn = document.getElementById('prevTabBtn');
    const saveBtn = document.getElementById('saveProductBtn');
    const progressBar = document.getElementById('formProgress');

    function updateTabUI() {
        if (prevBtn) prevBtn.style.display = currentTabIndex > 0 ? 'inline-block' : 'none';
        if (nextBtn) nextBtn.style.display = currentTabIndex < tabs.length - 1 ? 'inline-block' : 'none';
        if (saveBtn) saveBtn.style.display = 'inline-block';
        if (progressBar) {
            const pct = ((currentTabIndex + 1) / tabs.length) * 100;
            progressBar.style.width = pct + '%';
        }
    }

    function switchTab(index) {
        if (index < 0 || index >= tabs.length) return;
        currentTabIndex = index;
        const tabId = tabs[index];
        const triggerEl = document.querySelector(`#tab-${tabId}`);
        if (triggerEl) {
            const tabInstance = bootstrap.Tab.getOrCreateInstance(triggerEl);
            tabInstance.show();
        }
        updateTabUI();
    }

    // Direct tab pill clicks
    const tabTriggers = modalEl.querySelectorAll('#productTabs button[data-bs-toggle="pill"]');
    tabTriggers.forEach((tabEl) => {
        tabEl.addEventListener('shown.bs.tab', function () {
            const targetId = this.getAttribute('data-bs-target').replace('#', '');
            currentTabIndex = tabs.indexOf(targetId);
            if (currentTabIndex === -1) currentTabIndex = 0;
            updateTabUI();
        });
    });

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            if (currentTabIndex < tabs.length - 1) {
                switchTab(currentTabIndex + 1);
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            if (currentTabIndex > 0) {
                switchTab(currentTabIndex - 1);
            }
        });
    }

    // Reset when modal opens
    modalEl.addEventListener('show.bs.modal', function () {
        switchTab(0);
        const alertEl = document.getElementById('formAlert');
        if (alertEl) alertEl.classList.add('d-none');
    });

    // Quick Add Category Button (Stacked Modal Safe Handler)
    const quickCatBtn = document.getElementById('btnQuickAddCategory');
    if (quickCatBtn) {
        quickCatBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const catModalEl = document.getElementById('addCategoryModal');
            if (catModalEl) {
                const catModal = bootstrap.Modal.getOrCreateInstance(catModalEl);
                catModal.show();
            }
        });
    }

    // SKU Auto-generator
    const skuBtn = document.getElementById('generateSkuBtn');
    const skuInput = document.getElementById('productSku');
    if (skuBtn && skuInput) {
        skuBtn.addEventListener('click', function () {
            const prefix = 'SKU';
            const rand = Math.floor(100000 + Math.random() * 900000);
            skuInput.value = prefix + '-' + rand;
            skuInput.classList.remove('is-invalid');
        });
    }

    // Character counters
    const nameInput = document.getElementById('productName');
    const nameCounter = document.getElementById('nameCounter');
    if (nameInput && nameCounter) {
        nameInput.addEventListener('input', function () {
            nameCounter.textContent = this.value.length;
        });
    }

    const descInput = document.getElementById('productDescription');
    const descCounter = document.getElementById('descCounter');
    if (descInput && descCounter) {
        descInput.addEventListener('input', function () {
            descCounter.textContent = this.value.length;
        });
    }

    // Profit & Margin Live Calculator
    const costInput = document.getElementById('costPrice');
    const sellInput = document.getElementById('sellingPrice');
    const profitDisplay = document.getElementById('profitDisplay');
    const profitBadge = document.getElementById('profitBadge');

    function updateProfit() {
        if (!profitDisplay) return;
        const cost = parseFloat(costInput ? costInput.value : 0) || 0;
        const sell = parseFloat(sellInput ? sellInput.value : 0) || 0;
        const profit = sell - cost;
        const marginPct = sell > 0 ? ((profit / sell) * 100).toFixed(1) : (cost > 0 ? '-100' : '0.0');

        const currencySymbol = '{{ currency_symbol() }}';
        profitDisplay.textContent = currencySymbol + profit.toFixed(2) + ' (' + marginPct + '% Margin)';

        if (profit > 0) {
            profitDisplay.className = 'h5 mb-0 fw-bold text-success d-block';
            if (profitBadge) {
                profitBadge.className = 'badge bg-success-subtle text-success small';
                profitBadge.textContent = 'Profitable Unit';
            }
        } else if (profit === 0) {
            profitDisplay.className = 'h5 mb-0 fw-bold text-muted d-block';
            if (profitBadge) {
                profitBadge.className = 'badge bg-secondary-subtle text-secondary small';
                profitBadge.textContent = 'Breakeven';
            }
        } else {
            profitDisplay.className = 'h5 mb-0 fw-bold text-danger d-block';
            if (profitBadge) {
                profitBadge.className = 'badge bg-danger-subtle text-danger small';
                profitBadge.textContent = 'Negative Margin';
            }
        }
    }

    if (costInput) costInput.addEventListener('input', updateProfit);
    if (sellInput) sellInput.addEventListener('input', updateProfit);

    // Image Upload & Preview Handler
    const imageInput = document.getElementById('productImage');
    const imageUploadArea = document.getElementById('imageUploadArea');
    const imagePreview = document.getElementById('imagePreview');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const removeImageBtn = document.getElementById('removeImage');
    const browseImageBtn = document.getElementById('browseImage');

    if (imageUploadArea && imageInput) {
        imageUploadArea.addEventListener('click', function (e) {
            if (e.target !== removeImageBtn && (!removeImageBtn || !removeImageBtn.contains(e.target))) {
                imageInput.click();
            }
        });

        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = imagePreview.querySelector('img');
                    if (img) img.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                    uploadPlaceholder.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (imageInput) imageInput.value = '';
            if (imagePreview) imagePreview.classList.add('d-none');
            if (uploadPlaceholder) uploadPlaceholder.classList.remove('d-none');
        });
    }

    // Variants Management
    const addVariantBtn = document.getElementById('addVariantBtn');
    const variantsContainer = document.getElementById('variantsContainer');
    const noVariantsMsg = document.getElementById('noVariantsMsg');
    const variantTemplate = document.getElementById('variantTemplate');
    let variantIndex = 0;

    if (addVariantBtn && variantsContainer && variantTemplate) {
        addVariantBtn.addEventListener('click', function () {
            if (noVariantsMsg) noVariantsMsg.style.display = 'none';
            const clone = variantTemplate.content.cloneNode(true);
            const html = clone.firstElementChild.outerHTML.replace(/INDEX/g, variantIndex++);
            variantsContainer.insertAdjacentHTML('beforeend', html);
        });

        variantsContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-variant') || e.target.closest('.remove-variant')) {
                const item = e.target.closest('.variant-item');
                if (item) item.remove();
                if (variantsContainer.querySelectorAll('.variant-item').length === 0 && noVariantsMsg) {
                    noVariantsMsg.style.display = 'block';
                }
            }
        });
    }

    // Strict validation on submit
    const createForm = document.getElementById('createInventoryForm');
    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            let formValid = true;
            let firstInvalid = null;
            let firstInvalidTab = null;

            tabs.forEach((tabId, idx) => {
                const pane = document.getElementById(tabId);
                if (pane) {
                    const inputs = pane.querySelectorAll('input[required], select[required], textarea[required]');
                    inputs.forEach(input => {
                        if (!input.value.trim()) {
                            formValid = false;
                            input.classList.add('is-invalid');
                            if (!firstInvalid) {
                                firstInvalid = input;
                                firstInvalidTab = idx;
                            }
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });
                }
            });

            if (!formValid) {
                e.preventDefault();
                if (firstInvalidTab !== null) switchTab(firstInvalidTab);
                if (firstInvalid) firstInvalid.focus();
                const alertEl = document.getElementById('formAlert');
                const msgEl = document.getElementById('formAlertMessage');
                if (alertEl && msgEl) {
                    msgEl.textContent = 'Please fill in all required fields marked with * before saving.';
                    alertEl.className = 'alert alert-danger alert-dismissible fade show py-2 px-3 small rounded-3';
                    alertEl.classList.remove('d-none');
                }
            }
        });
    }
});
</script>
