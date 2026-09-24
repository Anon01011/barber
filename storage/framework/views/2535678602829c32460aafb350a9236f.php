<!-- Modern Item Creation Modal -->
<div class="modal fade" id="addInventoryModal" tabindex="-1" aria-labelledby="addInventoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="background: rgba(255,255,255,0.95); border-radius: 1rem;">
            <!-- Modal Header with Gradient -->
            <div class="modal-header border-0 bg-gradient-primary text-white py-2"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="d-flex align-items-center">
                    <div class="modal-icon me-2">
                        <i class="fas fa-box-open fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0" id="addInventoryModalLabel">Create New Product</h6>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form action="<?php echo e(route('admin.inventory.store')); ?>" method="POST" id="createInventoryForm"
                enctype="multipart/form-data" class="needs-validation" novalidate>
                <?php echo csrf_field(); ?>

                <!-- Progress Bar -->
                <div class="progress" style="height: 3px; border-radius: 0;">
                    <div class="progress-bar bg-success" role="progressbar" id="formProgress" style="width: 25%;"
                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <!-- Alert Messages -->
                <div class="px-3 pt-2">
                    <div id="formAlert" class="alert alert-dismissible fade d-none py-1 px-2 small" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div id="formAlertMessage"></div>
                        </div>
                        <button type="button" class="btn-close btn-sm p-2" data-bs-dismiss="alert"></button>
                    </div>
                </div>

                <div class="modal-body px-3 py-2">
                    <!-- Modern Tab Navigation -->
                    <ul class="nav nav-pills nav-fill mb-3 bg-light rounded-3 p-1" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-3 py-1 small fw-semibold" id="tab-basic"
                                data-bs-toggle="pill" data-bs-target="#basic" type="button" role="tab">
                                <i class="fas fa-info-circle me-1"></i> Basic Info <span class="text-danger">*</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-1 small fw-semibold" id="tab-pricing"
                                data-bs-toggle="pill" data-bs-target="#pricing" type="button" role="tab">
                                <i class="fas fa-dollar-sign me-1"></i> Pricing <span class="text-danger">*</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-1 small fw-semibold" id="tab-stock"
                                data-bs-toggle="pill" data-bs-target="#stock" type="button" role="tab">
                                <i class="fas fa-boxes me-1"></i> Stock
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-1 small fw-semibold" id="tab-details"
                                data-bs-toggle="pill" data-bs-target="#details" type="button" role="tab">
                                <i class="fas fa-cog me-1"></i> Details
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="productTabsContent">
                        <!-- Tab 1: Basic Information -->
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <div class="row g-2">
                                <!-- Left Column: Form Fields -->
                                <div class="col-lg-8">
                                    <!-- Product Name -->
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold mb-1">Product Name <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0"><i
                                                    class="fas fa-box text-primary"></i></span>
                                            <input type="text" name="name" id="productName"
                                                class="form-control border-start-0 ps-0"
                                                placeholder="e.g., Professional Hair Shampoo" required maxlength="255"
                                                autofocus>
                                        </div>
                                        <div class="d-flex justify-content-end"><small class="text-muted"
                                                style="font-size: 0.7rem;"><span id="nameCounter">0</span>/255</small>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold mb-1">Description</label>
                                        <textarea name="description" id="productDescription"
                                            class="form-control form-control-sm" rows="2"
                                            placeholder="Product features..." maxlength="2000"></textarea>
                                        <div class="d-flex justify-content-end"><small class="text-muted"
                                                style="font-size: 0.7rem;"><span id="descCounter">0</span>/2000</small>
                                        </div>
                                    </div>

                                    <div class="row g-2">
                                        <!-- Category -->
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold mb-1">Category <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-tags text-primary"></i></span>
                                                <select name="category_id" class="form-select" required>
                                                    <option value="">Select Category</option>
                                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <button class="btn btn-outline-primary" type="button"
                                                    data-bs-toggle="modal" data-bs-target="#addCategoryModal"
                                                    title="Add New Category">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Brand -->
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold mb-1">Brand</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-tag text-primary"></i></span>
                                                <input type="text" name="brand" class="form-control"
                                                    placeholder="Brand name" maxlength="255">
                                            </div>
                                        </div>

                                        <!-- Manufacturer -->
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold mb-1">Manufacturer</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-industry text-primary"></i></span>
                                                <input type="text" name="manufacturer" class="form-control"
                                                    placeholder="Manufacturer name" maxlength="255">
                                            </div>
                                        </div>

                                        <!-- SKU -->
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold mb-1">SKU <i
                                                    class="fas fa-question-circle text-muted ms-1"
                                                    data-bs-toggle="tooltip"
                                                    title="Auto-generated if left blank"></i></label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light"><i
                                                        class="fas fa-barcode text-primary"></i></span>
                                                <input type="text" name="sku" id="productSku"
                                                    class="form-control text-uppercase" placeholder="Auto-generated"
                                                    maxlength="100" pattern="[A-Z0-9-]*">
                                                <button class="btn btn-outline-secondary" type="button"
                                                    id="generateSkuBtn" title="Generate SKU">
                                                    <i class="fas fa-magic"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Image & Status -->
                                <div class="col-lg-4">
                                    <!-- Product Image -->
                                    <div class="card border-0 shadow-sm mb-2">
                                        <div class="card-header bg-light border-0 py-1">
                                            <h6 class="mb-0 small fw-semibold"><i
                                                    class="fas fa-image me-1 text-primary"></i>Image</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="image-upload-area text-center p-2 border border-dashed rounded bg-white"
                                                id="imageUploadArea" style="cursor: pointer;">
                                                <div class="image-preview d-none" id="imagePreview">
                                                    <img src="#" alt="Preview" class="img-fluid rounded mb-1"
                                                        style="max-height: 100px;">
                                                    <button type="button" class="btn btn-xs btn-danger w-100"
                                                        id="removeImage">Remove</button>
                                                </div>
                                                <div class="upload-placeholder" id="uploadPlaceholder">
                                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-1"></i>
                                                    <p class="small text-muted mb-1" style="font-size: 0.7rem;">Drag &
                                                        Drop or Click</p>
                                                    <button type="button" class="btn btn-xs btn-outline-primary"
                                                        id="browseImage">Browse</button>
                                                </div>
                                                <input type="file" name="image" id="productImage" class="d-none"
                                                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Card -->
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light border-0 py-1">
                                            <h6 class="mb-0 small fw-semibold"><i
                                                    class="fas fa-toggle-on me-1 text-primary"></i>Status</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="form-check form-switch mb-1">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="isActive" name="is_active" value="1" checked>
                                                <label class="form-check-label small fw-semibold"
                                                    for="isActive">Active</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="isTaxable" name="is_taxable" value="1">
                                                <label class="form-check-label small fw-semibold"
                                                    for="isTaxable">Taxable</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Pricing & Tax -->
                        <div class="tab-pane fade" id="pricing" role="tabpanel">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body p-3">
                                            <h6 class="small fw-semibold mb-2 text-muted">Cost Price</h6>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light"><?php echo e(currency_symbol()); ?></span>
                                                <input type="number" name="purchase_price" id="costPrice"
                                                    class="form-control" placeholder="0.00" step="0.01" min="0"
                                                    max="999999.99" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100 border-primary"
                                        style="border-width: 1px !important;">
                                        <div class="card-body p-3">
                                            <h6 class="small fw-semibold mb-2 text-primary">Selling Price <span
                                                    class="text-danger">*</span></h6>
                                            <div class="input-group input-group-sm">
                                                <span
                                                    class="input-group-text bg-primary text-white"><?php echo e(currency_symbol()); ?></span>
                                                <input type="number" name="selling_price" id="sellingPrice"
                                                    class="form-control" placeholder="0.00" step="0.01" min="0"
                                                    max="999999.99" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="small fw-semibold text-muted">Profit Margin:</span>
                                                <span class="fw-bold text-white"
                                                    id="profitDisplay"><?php echo e(currency_symbol()); ?>0.00 (0%)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold mb-1">Tax Rate (%)</label>
                                    <input type="number" name="tax_rate" class="form-control form-control-sm"
                                        placeholder="0" step="0.01" min="0" max="100" value="0">
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Stock & Inventory -->
                        <div class="tab-pane fade" id="stock" role="tabpanel">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Quantity</label>
                                    <input type="number" name="quantity_in_stock" class="form-control form-control-sm"
                                        placeholder="0" step="0.0001" min="0" max="999999.9999" value="10000">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Unit Type</label>
                                    <select name="unit_type" class="form-select form-select-sm">
                                        <option value="pcs" selected>Pieces (pcs)</option>
                                        <option value="g">Grams (g)</option>
                                        <option value="kg">Kilograms (kg)</option>
                                        <option value="ml">Milliliters (ml)</option>
                                        <option value="l">Liters (l)</option>
                                        <option value="m">Meters (m)</option>
                                        <option value="cm">Centimeters (cm)</option>
                                        <option value="box">Box</option>
                                        <option value="pack">Pack</option>
                                        <option value="set">Set</option>
                                        <option value="pair">Pair</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Reorder Level</label>
                                    <input type="number" name="reorder_level" class="form-control form-control-sm"
                                        placeholder="0" step="0.0001" min="0" max="999999.9999" value="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Min Quantity</label>
                                    <input type="number" name="minimum_quantity" class="form-control form-control-sm"
                                        placeholder="0" step="0.0001" min="0" max="999999.9999" value="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Location</label>
                                    <input type="text" name="location" class="form-control form-control-sm"
                                        placeholder="e.g., Shelf A-1" maxlength="100">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Barcode</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="barcode" id="productBarcode" class="form-control"
                                            placeholder="Scan/Enter" maxlength="100">
                                        <button class="btn btn-outline-secondary" type="button" title="Scan Barcode">
                                            <i class="fas fa-camera"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold mb-1">Supplier</label>
                                    <select name="supplier_id" class="form-select form-select-sm">
                                        <option value="">Select Supplier</option>
                                        <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                
                                <?php if($branches->count() > 1): ?>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold mb-1">
                                            Assign to Branches <span class="text-danger">*</span>
                                        </label>
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-2">
                                                <div class="row g-2">
                                                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input branch-checkbox" type="checkbox"
                                                                    name="branch_ids[]" value="<?php echo e($branch->id); ?>"
                                                                    id="branch_create_<?php echo e($branch->id); ?>" <?php echo e($branch->id == auth()->user()->branch_id ? 'checked' : ''); ?>>
                                                                <label class="form-check-label small"
                                                                    for="branch_create_<?php echo e($branch->id); ?>">
                                                                    <i
                                                                        class="fas fa-map-marker-alt me-1 text-primary"></i><?php echo e($branch->name); ?>

                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">Select at least one branch where this product will be
                                            available</small>
                                        <div class="invalid-feedback" id="branchError"></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Tab 4: Additional Details -->
                        <div class="tab-pane fade" id="details" role="tabpanel">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Weight</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="weight" class="form-control" placeholder="0"
                                            step="0.001" min="0" max="999999.999">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Dimensions</label>
                                    <input type="text" name="dimensions" class="form-control form-control-sm"
                                        placeholder="L x W x H (cm)" maxlength="100">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Expiry Date</label>
                                    <input type="date" name="expiry_date" class="form-control form-control-sm"
                                        min="<?php echo e(date('Y-m-d')); ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-semibold mb-1">Notes</label>
                                    <textarea name="notes" class="form-control form-control-sm" rows="2"
                                        placeholder="Additional notes..." maxlength="1000"></textarea>
                                </div>

                                <!-- Product Variants Section -->
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div
                                            class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-1">
                                            <h6 class="mb-0 small fw-semibold"><i
                                                    class="fas fa-layer-group text-primary me-1"></i>Variants</h6>
                                            <button type="button" class="btn btn-xs btn-primary" id="addVariantBtn">
                                                <i class="fas fa-plus me-1"></i>Add
                                            </button>
                                        </div>
                                        <div class="card-body p-2">
                                            <div id="variantsContainer">
                                                <div class="text-center text-muted py-2" id="noVariantsMsg">
                                                    <small>No variants added yet</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-top-0 bg-light py-2">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="prevTabBtn"
                            style="display: none;">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </button>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-sm btn-light me-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-sm btn-primary" id="nextTabBtn">
                                Next <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                            <button type="submit" class="btn btn-sm btn-success" id="saveProductBtn"
                                style="display: none;">
                                <i class="fas fa-check me-1"></i> Save Product
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Variant Template -->
<template id="variantTemplate">
    <div class="variant-item border rounded-3 p-2 mb-2 bg-white position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-1 remove-variant" aria-label="Remove"
            style="font-size: 0.7rem;"></button>
        <div class="row g-1">
            <div class="col-md-4">
                <input type="text" name="variants[INDEX][name]" class="form-control form-control-sm"
                    placeholder="Name (e.g. Size)">
            </div>
            <div class="col-md-4">
                <input type="text" name="variants[INDEX][value]" class="form-control form-control-sm"
                    placeholder="Value (e.g. Red)">
            </div>
            <div class="col-md-4">
                <input type="number" name="variants[INDEX][price_adjustment]" class="form-control form-control-sm"
                    value="0" step="0.01" placeholder="Price Adj.">
            </div>
        </div>
    </div>
</template>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .modal-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 8px;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 2px 5px rgba(102, 126, 234, 0.4);
    }

    .image-upload-area:hover {
        background-color: #f8f9fa;
        border-color: #667eea !important;
    }

    .variant-item {
        animation: slideIn 0.2s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.2rem;
    }
</style><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\inventory\partials\create-item-modal.blade.php ENDPATH**/ ?>