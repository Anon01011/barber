<style>
/* ====== Product Purpose & Classification Cards ====== */
.purpose-icon-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    font-size: 15px;
    flex-shrink: 0;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.purpose-icon-badge.bg-primary  { background: #2563eb !important; color: #fff !important; box-shadow: 0 2px 8px rgba(37,99,235,0.35); }
.purpose-icon-badge.bg-emerald  { background: #059669 !important; color: #fff !important; box-shadow: 0 2px 8px rgba(5,150,105,0.35); }
.purpose-icon-badge.bg-amber    { background: #d97706 !important; color: #fff !important; box-shadow: 0 2px 8px rgba(217,119,6,0.35); }

/* Card label as flex column */
.purpose-card {
    display: flex !important;
    flex-direction: column !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 10px !important;
    background: #fff !important;
    transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
    cursor: pointer;
    padding: 10px 12px !important;
}
.purpose-card:hover {
    border-color: #94a3b8 !important;
    background: #f8fafc !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
}
.purpose-card:hover .purpose-icon-badge {
    transform: translateY(-1px);
}

/* Retail — blue accent when selected */
.btn-check:checked + .purpose-btn-retail {
    border-color: #2563eb !important;
    background: #eff6ff !important;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.15) !important;
}
/* Backbar — green accent when selected */
.btn-check:checked + .purpose-btn-backbar {
    border-color: #059669 !important;
    background: #ecfdf5 !important;
    box-shadow: 0 0 0 3px rgba(5,150,105,0.15) !important;
}
/* Dual-Use — amber accent when selected */
.btn-check:checked + .purpose-btn-dual {
    border-color: #d97706 !important;
    background: #fffbeb !important;
    box-shadow: 0 0 0 3px rgba(217,119,6,0.15) !important;
}
</style>

<!-- Modern Luxury Item Edit Modal -->
<div class="modal fade" id="editInventoryModal" tabindex="-1" aria-labelledby="editInventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-height: calc(100vh - 2.5rem);">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden d-flex flex-column" style="background: #ffffff; max-height: calc(100vh - 2.5rem); height: 100%;">
            <!-- Modal Header with Dark Luxury Gradient -->
            <div class="modal-header border-0 py-3.5 px-4 text-white flex-shrink-0"
                style="background: linear-gradient(135deg, #0b0f17 0%, #1a2232 100%); border-bottom: 1px solid rgba(197, 168, 128, 0.25) !important;">
                <div class="d-flex align-items-center">
                    <div class="modal-badge-icon me-3 p-2 rounded-3 d-flex align-items-center justify-content-center"
                        style="background: rgba(197, 168, 128, 0.18); width: 40px; height: 40px; border: 1px solid rgba(197, 168, 128, 0.35);">
                        <i class="fas fa-edit" style="color: #c5a880; font-size: 18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="editInventoryModalLabel" style="font-size: 17px; letter-spacing: -0.2px;">
                            Edit Product
                        </h5>
                        <small style="color: #94a3b8; font-size: 12px;">Update product catalog details, classification, pricing, packaging and stock levels</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editInventoryForm" method="POST" enctype="multipart/form-data" class="needs-validation d-flex flex-column overflow-hidden flex-grow-1" style="min-height: 0; height: 100%;" novalidate>
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <!-- Interactive Progress Line -->
                <div class="progress flex-shrink-0" style="height: 3px; border-radius: 0; background: #e2e8f0;">
                    <div class="progress-bar" role="progressbar" id="editFormProgress"
                        style="width: 25%; background: linear-gradient(90deg, #5b5fc7, #10b981); transition: width 0.3s ease;"
                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <!-- Alert Messages -->
                <div class="px-4 pt-2 pb-0 flex-shrink-0">
                    <div id="formStatus" class="alert alert-dismissible fade d-none py-2 px-3 small rounded-3" role="alert"></div>
                </div>

                <div class="modal-body px-4 py-3 flex-grow-1" style="overflow-y: auto; min-height: 0;">
                    <!-- Luxury Step Navigation Tabs -->
                    <ul class="nav nav-pills nav-fill mb-3.5 product-tabs-nav" id="editProductTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-3 py-2 px-3 small fw-semibold" id="edit-tab-basic"
                                data-bs-toggle="pill" data-bs-target="#edit-basic" type="button" role="tab">
                                <i class="fas fa-info-circle me-1.5"></i>
                                <span>1. Basic Info</span>
                                <span class="text-danger ms-0.5">*</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-2 px-3 small fw-semibold" id="edit-tab-pricing"
                                data-bs-toggle="pill" data-bs-target="#edit-pricing" type="button" role="tab">
                                <i class="fas fa-tag me-1.5"></i>
                                <span>2. Pricing & Margin</span>
                                <span class="text-danger ms-0.5">*</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-2 px-3 small fw-semibold" id="edit-tab-stock"
                                data-bs-toggle="pill" data-bs-target="#edit-stock" type="button" role="tab">
                                <i class="fas fa-boxes me-1.5"></i>
                                <span>3. Stock & Packaging</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-2 px-3 small fw-semibold" id="edit-tab-details"
                                data-bs-toggle="pill" data-bs-target="#edit-details" type="button" role="tab">
                                <i class="fas fa-sliders-h me-1.5"></i>
                                <span>4. Specifications & Specs</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="editProductTabsContent">
                        <!-- ==================== TAB 1: BASIC INFO ==================== -->
                        <div class="tab-pane fade show active" id="edit-basic" role="tabpanel">
                            <div class="row g-3">
                                <!-- Left Column: Form Fields -->
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
                                                <input type="radio" class="btn-check" name="product_type" id="editTypeRetail" value="retail">
                                                <label class="btn purpose-card purpose-btn-retail w-100 text-start p-2.5 h-100" for="editTypeRetail">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="purpose-icon-badge bg-primary text-white"><i class="fas fa-shopping-bag"></i></span>
                                                        <strong class="small text-dark">Retail Product</strong>
                                                    </div>
                                                    <small class="text-muted d-block lh-sm" style="font-size: 0.73rem;">Sold directly to clients at the front desk / POS counter.</small>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="product_type" id="editTypeConsumable" value="consumable">
                                                <label class="btn purpose-card purpose-btn-backbar w-100 text-start p-2.5 h-100" for="editTypeConsumable">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="purpose-icon-badge bg-emerald text-white"><i class="fas fa-flask"></i></span>
                                                        <strong class="small text-dark">Backbar Consumable</strong>
                                                    </div>
                                                    <small class="text-muted d-block lh-sm" style="font-size: 0.73rem;">Dispensed by barbers/stylists during services via recipes.</small>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="product_type" id="editTypeDual" value="dual">
                                                <label class="btn purpose-card purpose-btn-dual w-100 text-start p-2.5 h-100" for="editTypeDual">
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
                                            <small class="text-muted" style="font-size: 0.72rem;"><span id="editNameCounter">0</span>/255</small>
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-box text-primary"></i></span>
                                            <input type="text" name="name" id="editName" class="form-control border-start-0 ps-0"
                                                placeholder="e.g., Professional Hair Shampoo" required maxlength="255">
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
                                                <select name="category_id" id="editCategory" class="form-select border-start-0 ps-0" required>
                                                    <option value="">Select Category...</option>
                                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <button class="btn btn-outline-primary px-3 fw-semibold d-flex align-items-center gap-1"
                                                    type="button" id="btnEditQuickAddCategory" title="Add New Category">
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
                                                <input type="text" name="brand" id="editBrand" class="form-control border-start-0 ps-0" placeholder="Brand name" maxlength="255">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Manufacturer & SKU Row -->
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-dark mb-1">Manufacturer / Vendor</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-industry text-primary"></i></span>
                                                <input type="text" name="manufacturer" id="editManufacturer" class="form-control border-start-0 ps-0" placeholder="Manufacturer name" maxlength="255">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-dark mb-1">SKU (Stock Keeping Unit)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-barcode text-primary"></i></span>
                                                <input type="text" name="sku" id="editSku" class="form-control border-start-0 ps-0 text-uppercase" placeholder="Auto-generated" maxlength="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label small fw-semibold text-dark mb-0">Description & Features</label>
                                            <small class="text-muted" style="font-size: 0.72rem;"><span id="editDescCounter">0</span>/2000</small>
                                        </div>
                                        <textarea name="description" id="editDescription" class="form-control form-control-sm" rows="3"
                                            placeholder="Product features, directions, ingredients..." maxlength="2000"></textarea>
                                    </div>
                                </div>

                                <!-- Right Column: Image & Status -->
                                <div class="col-lg-4">
                                    <!-- Image Preview & Upload Card -->
                                    <div class="card border border-light-subtle rounded-3 shadow-none mb-3 overflow-hidden">
                                        <div class="card-header bg-light py-2 px-3 border-0 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 small fw-bold text-dark"><i class="fas fa-image me-1.5 text-primary"></i>Product Image</h6>
                                            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" id="removeImageBtn" style="display: none; font-size: 0.75rem;">
                                                <i class="fas fa-trash-alt me-1"></i>Remove
                                            </button>
                                        </div>
                                        <div class="card-body p-3 text-center">
                                            <div class="image-upload-dropzone p-3 border border-2 border-dashed rounded-3 text-center position-relative"
                                                id="editImageUploadArea" style="cursor: pointer; background: #fafafa;">
                                                <div class="text-center">
                                                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiB2aWV3Qm94PSIwIDAgMjAwIDIwMCI+CiAgPHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNlZWVlZWUiLz4KICA8dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBhbGlnbm1lbnQtYmFzZWxpbmU9Im1pZGRsZSIgZmlsbD0iIzk5OSI+CiAgICBObyBJbWFnZQogIDwvdGV4dD4KPC9zdmc+"
                                                        class="img-fluid rounded-2 mb-2 shadow-sm" id="productImagePreview" style="max-height: 140px; object-fit: contain;">
                                                </div>
                                                <input type="file" class="d-none" id="editImageInput" name="image" accept="image/jpeg,image/png,image/jpg,image/webp">
                                                <button type="button" class="btn btn-xs btn-outline-primary mt-1 px-2.5" id="uploadImageBtn">
                                                    <i class="fas fa-camera me-1"></i>Change Photo
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Card -->
                                    <div class="card border border-light-subtle rounded-3 shadow-none">
                                        <div class="card-header bg-light py-2 px-3 border-0">
                                            <h6 class="mb-0 small fw-bold text-dark"><i class="fas fa-toggle-on me-1.5 text-primary"></i>Product Availability</h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="form-check form-switch mb-2.5">
                                                <input type="hidden" name="is_active" value="0">
                                                <input class="form-check-input" type="checkbox" role="switch" id="editStatus" name="is_active" value="1">
                                                <label class="form-check-label small fw-semibold text-dark" for="editStatus">
                                                    Active Product
                                                    <small class="text-muted d-block fw-normal" style="font-size: 0.72rem;">Available in POS and service recipes</small>
                                                </label>
                                            </div>
                                            <hr class="my-2 border-light">
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="is_taxable" value="0">
                                                <input class="form-check-input" type="checkbox" role="switch" id="editIsTaxable" name="is_taxable" value="1">
                                                <label class="form-check-label small fw-semibold text-dark" for="editIsTaxable">
                                                    Apply Tax
                                                    <small class="text-muted d-block fw-normal" style="font-size: 0.72rem;">Calculate sales tax on checkout</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== TAB 2: PRICING & MARGIN ==================== -->
                        <div class="tab-pane fade" id="edit-pricing" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card border border-light-subtle rounded-3 shadow-none h-100">
                                        <div class="card-body p-3">
                                            <label class="form-label small fw-bold text-muted mb-1 d-block">Cost Price (Purchase / Wholesaler)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light fw-bold text-muted"><?php echo e(currency_symbol()); ?></span>
                                                <input type="number" step="0.01" min="0" max="999999.99" class="form-control form-control-lg fw-bold"
                                                    name="purchase_price" id="editCostPrice" placeholder="0.00">
                                            </div>
                                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Used for inventory valuation & COGS expense calculation.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border border-primary-subtle rounded-3 shadow-sm h-100" style="background: #fdfefe;">
                                        <div class="card-body p-3">
                                            <label class="form-label small fw-bold text-primary mb-1 d-block">
                                                Selling Price (Retail / POS) <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-primary text-white fw-bold"><?php echo e(currency_symbol()); ?></span>
                                                <input type="number" step="0.01" min="0" max="999999.99" class="form-control form-control-lg fw-bold text-dark"
                                                    name="selling_price" id="editSellingPrice" placeholder="0.00" required>
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
                                                <span class="h5 mb-0 fw-bold text-success d-block" id="editProfitDisplay">
                                                    <?php echo e(currency_symbol()); ?>0.00 (0.0% Margin)
                                                </span>
                                                <span class="badge bg-success-subtle text-success small" id="editProfitBadge">Ready</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark mb-1">Custom Tax Rate (%)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="fas fa-percentage"></i></span>
                                        <input type="number" step="0.01" min="0" max="100" class="form-control" name="tax_rate" id="editTaxRate" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== TAB 3: STOCK & PACKAGING ==================== -->
                        <div class="tab-pane fade" id="edit-stock" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Stock Quantity</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="fas fa-boxes text-primary"></i></span>
                                        <input type="number" step="any" min="0" max="999999.9999" class="form-control" name="quantity_in_stock" id="editQuantity">
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Current inventory on hand</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Unit Packaging Type</label>
                                    <select class="form-select" name="unit_type" id="editUnitType">
                                        <option value="pcs">Pieces (pcs)</option>
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
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Container Pack Capacity</label>
                                    <div class="input-group">
                                        <input type="number" step="any" min="0.01" class="form-control" name="pack_capacity" id="editPackCapacity" placeholder="e.g., 1000">
                                        <span class="input-group-text bg-light text-muted">units</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Base Dispensing Unit</label>
                                    <select class="form-select" name="base_unit" id="editBaseUnit">
                                        <option value="pcs">pcs (count)</option>
                                        <option value="ml">ml (milliliters)</option>
                                        <option value="g">g (grams)</option>
                                        <option value="pump">pump</option>
                                        <option value="drops">drops</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Low Stock Alert Level</label>
                                    <input type="number" step="any" min="0" max="999999.9999" class="form-control" name="reorder_level" id="editReorderLevel">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark mb-1">Barcode / EAN</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="fas fa-barcode"></i></span>
                                        <input type="text" class="form-control" name="barcode" id="editBarcode" placeholder="Scan or enter barcode" maxlength="100">
                                        <button class="btn btn-outline-secondary" type="button" id="generateBarcodeBtn" title="Generate Barcode">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    </div>
                                </div>

                                
                                <?php if(isset($branches) && $branches->count() > 1): ?>
                                    <div class="col-12 mt-2">
                                        <label class="form-label small fw-bold text-dark mb-1">
                                            <i class="fas fa-map-marker-alt text-primary me-1"></i>Branch Availability
                                        </label>
                                        <div class="p-3 bg-light rounded-3 border">
                                            <div class="row g-2">
                                                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input edit-branch-checkbox" type="checkbox"
                                                                name="branch_ids[]" value="<?php echo e($branch->id); ?>" id="branch_edit_<?php echo e($branch->id); ?>">
                                                            <label class="form-check-label small fw-semibold text-dark" for="branch_edit_<?php echo e($branch->id); ?>">
                                                                <?php echo e($branch->name); ?>

                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- ==================== TAB 4: SPECIFICATIONS & SPECS ==================== -->
                        <div class="tab-pane fade" id="edit-details" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-dark mb-1">Internal Warehouse / Salon Notes</label>
                                    <textarea class="form-control form-control-sm" name="notes" id="editNotes" rows="3"
                                        placeholder="Internal notes for warehouse, staff or restocking..." maxlength="1000"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-top bg-light py-2.5 px-4 d-flex justify-content-between align-items-center flex-shrink-0" style="position: sticky; bottom: 0; z-index: 1050; background: #ffffff !important;">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" id="editPrevTabBtn" style="display: none;">
                        <i class="fas fa-arrow-left me-1"></i> Previous
                    </button>
                    <button type="button" class="btn btn-sm btn-light px-3" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-next-step" id="editNextTabBtn">
                            Next Step <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                        <button type="submit" class="btn btn-sm btn-save-step" id="editSaveProductBtn" style="display: inline-block !important;">
                            <i class="fas fa-check me-1"></i> Update Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Luxury Scoped Styling for Edit Product Modal */
    #editInventoryModal .modal-dialog {
        max-height: calc(100vh - 2.5rem);
    }
    #editInventoryModal .modal-content {
        max-height: calc(100vh - 2.5rem);
        display: flex;
        flex-direction: column;
    }
    #editInventoryModal form {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden;
    }
    #editInventoryModal .modal-body {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 0;
        max-height: none !important;
    }
    #editInventoryModal .modal-footer {
        flex-shrink: 0;
        background: #ffffff !important;
        border-top: 1px solid #e2e8f0;
    }

    #editInventoryModal .product-tabs-nav {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 5px;
        gap: 6px;
    }
    #editInventoryModal .product-tabs-nav .nav-link {
        color: #475569 !important;
        background: transparent;
        border: none;
        border-radius: 9px;
        font-size: 0.84rem;
        font-weight: 600;
        padding: 8px 14px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #editInventoryModal .product-tabs-nav .nav-link:hover:not(.active) {
        background: #e2e8f0;
        color: #0f172a !important;
    }
    #editInventoryModal .product-tabs-nav .nav-link.active {
        background: #0f172a !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.25);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editInventoryModal');
    if (!editModal) return;

    const editTabs = ['edit-basic', 'edit-pricing', 'edit-stock', 'edit-details'];
    let editTabIndex = 0;

    const editNextBtn = document.getElementById('editNextTabBtn');
    const editPrevBtn = document.getElementById('editPrevTabBtn');
    const editSaveBtn = document.getElementById('editSaveProductBtn');
    const editProgressBar = document.getElementById('editFormProgress');

    function updateEditTabUI() {
        if (editPrevBtn) editPrevBtn.style.display = editTabIndex > 0 ? 'inline-block' : 'none';
        if (editNextBtn) editNextBtn.style.display = editTabIndex < editTabs.length - 1 ? 'inline-block' : 'none';
        if (editSaveBtn) editSaveBtn.style.display = 'inline-block';
        if (editProgressBar) {
            const pct = ((editTabIndex + 1) / editTabs.length) * 100;
            editProgressBar.style.width = pct + '%';
        }
    }

    if (editNextBtn) {
        editNextBtn.addEventListener('click', function () {
            if (editTabIndex < editTabs.length - 1) {
                editTabIndex++;
                switchEditTab(editTabIndex);
            }
        });
    }

    if (editPrevBtn) {
        editPrevBtn.addEventListener('click', function () {
            if (editTabIndex > 0) {
                editTabIndex--;
                switchEditTab(editTabIndex);
            }
        });
    }

    const editTabTriggers = editModal.querySelectorAll('#editProductTabs button[data-bs-toggle="pill"]');
    editTabTriggers.forEach((tabEl) => {
        tabEl.addEventListener('shown.bs.tab', function () {
            editTabIndex = editTabs.indexOf(this.getAttribute('data-bs-target').replace('#', ''));
            if (editTabIndex === -1) editTabIndex = 0;
            updateEditTabUI();
        });
    });

    function switchEditTab(idx) {
        const tabTarget = '#' + editTabs[idx];
        const trigger = editModal.querySelector(`#editProductTabs button[data-bs-target="${tabTarget}"]`);
        if (trigger) {
            bootstrap.Tab.getOrCreateInstance(trigger).show();
            updateEditTabUI();
        }
    }

    // Reset to first tab whenever modal is opened
    editModal.addEventListener('show.bs.modal', function () {
        editTabIndex = 0;
        switchEditTab(0);
        updateEditProfit();
    });

    // Quick Add Category Handler for Edit Modal
    const editQuickCatBtn = document.getElementById('btnEditQuickAddCategory');
    if (editQuickCatBtn) {
        editQuickCatBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const catModalEl = document.getElementById('addCategoryModal');
            if (catModalEl) {
                const catModal = bootstrap.Modal.getOrCreateInstance(catModalEl);
                catModal.show();
            }
        });
    }

    // Text counters for Edit modal
    const editNameIn = document.getElementById('editName');
    const editNameCount = document.getElementById('editNameCounter');
    if (editNameIn && editNameCount) {
        editNameIn.addEventListener('input', function() {
            editNameCount.textContent = this.value.length;
        });
    }

    const editDescIn = document.getElementById('editDescription');
    const editDescCount = document.getElementById('editDescCounter');
    if (editDescIn && editDescCount) {
        editDescIn.addEventListener('input', function() {
            editDescCount.textContent = this.value.length;
        });
    }

    // Edit Profit & Margin Live Calculator
    const editCostIn = document.getElementById('editCostPrice');
    const editSellIn = document.getElementById('editSellingPrice');
    const editProfitDisp = document.getElementById('editProfitDisplay');
    const editProfitBdg = document.getElementById('editProfitBadge');

    function updateEditProfit() {
        if (!editProfitDisp) return;
        const cost = parseFloat(editCostIn ? editCostIn.value : 0) || 0;
        const sell = parseFloat(editSellIn ? editSellIn.value : 0) || 0;
        const profit = sell - cost;
        const marginPct = sell > 0 ? ((profit / sell) * 100).toFixed(1) : (cost > 0 ? '-100' : '0.0');

        const currencySymbol = '<?php echo e(currency_symbol()); ?>';
        editProfitDisp.textContent = currencySymbol + profit.toFixed(2) + ' (' + marginPct + '% Margin)';

        if (profit > 0) {
            editProfitDisp.className = 'h5 mb-0 fw-bold text-success d-block';
            if (editProfitBdg) {
                editProfitBdg.className = 'badge bg-success-subtle text-success small';
                editProfitBdg.textContent = 'Profitable Unit';
            }
        } else if (profit === 0) {
            editProfitDisp.className = 'h5 mb-0 fw-bold text-muted d-block';
            if (editProfitBdg) {
                editProfitBdg.className = 'badge bg-secondary-subtle text-secondary small';
                editProfitBdg.textContent = 'Breakeven';
            }
        } else {
            editProfitDisp.className = 'h5 mb-0 fw-bold text-danger d-block';
            if (editProfitBdg) {
                editProfitBdg.className = 'badge bg-danger-subtle text-danger small';
                editProfitBdg.textContent = 'Negative Margin';
            }
        }
    }

    if (editCostIn) editCostIn.addEventListener('input', updateEditProfit);
    if (editSellIn) editSellIn.addEventListener('input', updateEditProfit);

    // Image upload trigger for Edit modal
    const editImgInput = document.getElementById('editImageInput');
    const editUpBtn = document.getElementById('uploadImageBtn');
    const editDropArea = document.getElementById('editImageUploadArea');
    if (editImgInput) {
        if (editUpBtn) {
            editUpBtn.addEventListener('click', function(e) {
                e.preventDefault();
                editImgInput.click();
            });
        }
        if (editDropArea) {
            editDropArea.addEventListener('click', function(e) {
                if (!e.target.closest('#removeImageBtn') && !e.target.closest('#uploadImageBtn')) {
                    editImgInput.click();
                }
            });
        }
        editImgInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('productImagePreview');
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    const rmBtn = document.getElementById('removeImageBtn');
                    if (rmBtn) rmBtn.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Remove image handler
    const rmBtn = document.getElementById('removeImageBtn');
    if (rmBtn) {
        rmBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (editImgInput) editImgInput.value = '';
            const preview = document.getElementById('productImagePreview');
            if (preview) preview.src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiB2aWV3Qm94PSIwIDAgMjAwIDIwMCI+CiAgPHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNlZWVlZWUiLz4KICA8dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBhbGlnbm1lbnQtYmFzZWxpbmU9Im1pZGRsZSIgZmlsbD0iIzk5OSI+CiAgICBObyBJbWFnZQogIDwvdGV4dD4KPC9zdmc+';
            this.style.display = 'none';
        });
    }
});
</script>
<?php /**PATH C:\Ketan\projects\barber\resources\views/inventory/partials/edit-item-modal.blade.php ENDPATH**/ ?>