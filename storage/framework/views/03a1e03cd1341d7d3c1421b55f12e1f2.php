<?php $__env->startSection('title', 'Inventory'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4 py-5">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Inventory</h2>
                <p class="text-muted">Manage salon products and stock</p>
            </div>
            <div class="d-flex gap-2">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.export')): ?>
                    <a href="<?php echo e(route('admin.products.export')); ?>" class="btn btn-info text-white" title="Export Products">
                        <i class="fas fa-file-csv me-1"></i>Export
                    </a>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.import')): ?>
                    <button class="btn btn-cyan text-white" data-bs-toggle="modal" data-bs-target="#importProductModal"
                        title="Import Products" style="background-color: #17a2b8; border-color: #17a2b8;">
                        <i class="fas fa-file-import me-1"></i>Import
                    </button>
                    <a href="<?php echo e(route('admin.products.template')); ?>" class="btn btn-outline-info" title="Download Template">
                        <i class="fas fa-download me-1"></i>Template
                    </a>
                <?php endif; ?>

                <a href="<?php echo e(route('admin.inventory.categories.index')); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-2"></i>View Categories
                </a>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-tags me-2"></i>New Category
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                    <i class="fas fa-plus me-2"></i>New Item
                </button>
            </div>
        </div>



        <!-- Main Content -->
        <div class="row g-4">
            <!-- Inventory List -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <h5 class="card-title mb-0 text-primary">Inventory List</h5>
                            <form action="<?php echo e(route('admin.inventory.index')); ?>" method="GET"
                                class="d-flex flex-column flex-md-row gap-2 w-100" style="max-width: 600px;">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0"
                                        placeholder="Search by name, SKU or barcode..." value="<?php echo e(request('search')); ?>">
                                </div>
                                <select name="category" class="form-select" style="width: auto;">
                                    <option value="">All Categories</option>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                                            <?php echo e($category->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <select name="status" class="form-select" style="width: auto;">
                                    <option value="">All Status</option>
                                    <option value="low" <?php echo e(request('status') === 'low' ? 'selected' : ''); ?>>Low Stock</option>
                                    <option value="out" <?php echo e(request('status') === 'out' ? 'selected' : ''); ?>>Out of Stock
                                    </option>
                                </select>
                                <?php if($branches->count() > 1): ?>
                                    <select name="branch" class="form-select" style="width: auto;">
                                        <option value="">All Branches</option>
                                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch') == $branch->id ? 'selected' : ''); ?>>
                                                <?php echo e($branch->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                                <?php if(request()->has('search') || request()->has('category') || request()->has('status') || request()->has('branch')): ?>
                                    <a href="<?php echo e(route('admin.inventory.index')); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> Clear
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0">Name</th>
                                        <th class="border-0">SKU</th>
                                        <th class="border-0">Category</th>
                                        <th class="border-0">Stock</th>
                                        <th class="border-0">Price</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i
                                                        class="fas fa-<?php echo e($item->category->icon ?? 'box'); ?> text-primary me-2"></i>
                                                    <div>
                                                        <div class="fw-medium"><?php echo e($item->name); ?></div>
                                                        <?php if($item->barcode): ?>
                                                            <small class="text-muted">Barcode: <?php echo e($item->barcode); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code><?php echo e($item->sku); ?></code>
                                            </td>
                                            <td>
                                                <?php if($item->category): ?>
                                                    <span class="badge bg-light text-dark"><?php echo e($item->category->name); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">Uncategorized</span>
                                                <?php endif; ?>
                                                <?php if($branches->count() > 1 && $item->branches->count() > 0): ?>
                                                    <div class="mt-1">
                                                        <?php $__currentLoopData = $item->branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <span class="badge bg-info text-white me-1" style="font-size: 0.7rem;">
                                                                <i class="fas fa-map-marker-alt me-1"></i><?php echo e($branch->name); ?>

                                                            </span>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                    $branchId = request('branch');
                                                    $branchPivot = null;
                                                    if ($branchId && $item->branches) {
                                                        $branchPivot = $item->branches->firstWhere('id', $branchId)->pivot ?? null;
                                                    }
                                                    
                                                    $displayStock = $branchPivot ? $branchPivot->quantity : $item->quantity_in_stock;
                                                    $displayMin = $branchPivot ? $branchPivot->minimum_quantity : $item->minimum_quantity;
                                                    
                                                    // Calculate stock status
                                                    $statusText = 'In Stock';
                                                    $statusClass = 'bg-success-subtle text-success';
                                                    $progressClass = 'bg-success';
                                                    
                                                    if ($displayStock <= 0) {
                                                        $statusText = 'Out of Stock';
                                                        $statusClass = 'bg-danger-subtle text-danger';
                                                        $progressClass = 'bg-danger';
                                                    } else {
                                                        $isLow = false;
                                                        if ($branchPivot) {
                                                            $isLow = $displayMin > 0 && $displayStock <= $displayMin;
                                                        } else {
                                                            $isLow = ($item->reorder_level > 0 && $displayStock <= $item->reorder_level) ||
                                                                    ($displayMin > 0 && $displayStock <= $displayMin);
                                                        }
                                                        
                                                        if ($isLow) {
                                                            $statusText = 'Low Stock';
                                                            $statusClass = 'bg-warning-subtle text-warning';
                                                            $progressClass = 'bg-warning';
                                                        }
                                                    }
                                                    
                                                    $percentage = $displayMin > 0
                                                        ? min(100, ($displayStock / $displayMin) * 100)
                                                        : ($displayStock > 0 ? 100 : 0);
                                                ?>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2"><?php echo e(number_format($displayStock, 0)); ?> <?php echo e($item->unit_type); ?></span>
                                                    <div class="progress flex-grow-1" style="height: 6px; width: 80px;">
                                                        <div class="progress-bar <?php echo e($progressClass); ?>"
                                                            style="width: <?php echo e($percentage); ?>%"
                                                            title="Min: <?php echo e($displayMin); ?> <?php echo e($item->unit_type); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap">
                                                    <div class="fw-medium">
                                                        <?php echo e(currency_symbol()); ?><?php echo e(number_format($item->selling_price, 2)); ?>

                                                    </div>
                                                    <small class="text-muted">Cost:
                                                        <?php echo e(currency_symbol()); ?><?php echo e(number_format($item->purchase_price, 2)); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo e($statusClass); ?>">
                                                    <?php echo e($statusText); ?>

                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-light me-1" data-bs-toggle="modal"
                                                    data-bs-target="#editInventoryModal" data-id="<?php echo e($item->id); ?>"
                                                    data-name="<?php echo e($item->name); ?>" data-category="<?php echo e($item->category_id); ?>"
                                                    data-sku="<?php echo e($item->sku); ?>" data-barcode="<?php echo e($item->barcode); ?>"
                                                    data-description="<?php echo e($item->description); ?>"
                                                    data-purchase-price="<?php echo e($item->purchase_price); ?>"
                                                    data-selling-price="<?php echo e($item->selling_price); ?>"
                                                    data-quantity="<?php echo e($item->quantity_in_stock); ?>"
                                                    data-minimum-quantity="<?php echo e($item->minimum_quantity); ?>"
                                                    data-reorder-level="<?php echo e($item->reorder_level); ?>"
                                                    data-unit="<?php echo e($item->unit_type); ?>" data-location="<?php echo e($item->location); ?>"
                                                    data-manufacturer="<?php echo e($item->manufacturer); ?>"
                                                    data-notes="<?php echo e($item->notes); ?>"
                                                    data-expiry-date="<?php echo e($item->expiry_date ? $item->expiry_date->format('Y-m-d') : ''); ?>"
                                                    data-image="<?php echo e($item->image_url); ?>"
                                                    data-variants="<?php echo e(json_encode($item->variants)); ?>"
                                                    data-branches="<?php echo e(json_encode($item->branches)); ?>"
                                                    data-status="<?php echo e($item->is_active ? 'active' : 'inactive'); ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-light me-1" data-bs-toggle="modal"
                                                    data-bs-target="#updateStockModal" data-id="<?php echo e($item->id); ?>"
                                                    data-name="<?php echo e($item->name); ?>" data-quantity="<?php echo e($item->quantity_in_stock); ?>"
                                                    data-unit="<?php echo e($item->unit_type); ?>"
                                                    data-branches="<?php echo e(json_encode($item->branches)); ?>" title="Update Stock">
                                                    <i class="fas fa-boxes"></i>
                                                </button>
                                                <form action="<?php echo e(route('admin.inventory.destroy', $item)); ?>" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this item? This action cannot be undone.');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-light text-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <p class="mb-0">No inventory items found</p>
                                                    <p class="small">Click the 'New Item' button to add your first inventory
                                                        item</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if($items->hasPages()): ?>
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-flex justify-content-end">
                                    <?php echo e($items->withQueryString()->links()); ?>

                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Product Modal -->
    <div class="modal fade" id="importProductModal" tabindex="-1" aria-labelledby="importProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importProductModalLabel">Import Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('admin.products.import')); ?>" method="POST" enctype="multipart/form-data"
                    id="importProductForm">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body px-4 py-4">
                        <p class="text-muted mb-4">Upload a CSV to import product data to your CMS.</p>

                        <!-- Upload States Container -->
                        <div id="uploadStatesContainer">

                            <!-- Initial Upload State -->
                            <div id="initialUploadState" class="upload-state">
                                <!-- Drag & Drop Zone -->
                                <div class="upload-drop-zone border-2 border-dashed rounded-3 p-3 text-center mb-3"
                                    id="dropZone"
                                    style="border-color: #dee2e6; background-color: #f8f9fa; cursor: pointer; transition: all 0.3s ease;">
                                    <div class="upload-icon mb-2">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-1">Drag CSV here</h6>
                                    <p class="text-muted small mb-2">or click to browse</p>
                                    <button type="button" class="btn btn-sm btn-primary" id="browseFileBtn">
                                        Browse Files
                                    </button>
                                </div>

                                <!-- File Input (Moved outside dropzone) -->
                                <input type="file" class="d-none" id="importFile" name="file" accept=".csv" required>
                            </div>

                            <!-- Progress State (Circular Progress) -->
                            <div id="progressUploadState" class="upload-state d-none">
                                <div class="text-center py-5">
                                    <!-- Circular Progress -->
                                    <div class="position-relative d-inline-block mb-4">
                                        <svg width="160" height="160" viewBox="0 0 160 160">
                                            <!-- Background Circle -->
                                            <circle cx="80" cy="80" r="70" fill="none" stroke="#e9ecef" stroke-width="8" />
                                            <!-- Progress Circle -->
                                            <circle id="progressCircle" cx="80" cy="80" r="70" fill="none" stroke="#6366f1"
                                                stroke-width="8" stroke-linecap="round" stroke-dasharray="439.6"
                                                stroke-dashoffset="439.6" transform="rotate(-90 80 80)"
                                                style="transition: stroke-dashoffset 0.3s ease;" />
                                        </svg>
                                        <!-- Percentage Text -->
                                        <div class="position-absolute top-50 start-50 translate-middle">
                                            <h2 class="mb-0 fw-bold" id="progressPercentText" style="color: #6366f1;">0%
                                            </h2>
                                        </div>
                                    </div>

                                    <!-- File Info -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                            <i class="fas fa-file-csv text-primary"></i>
                                            <span class="fw-medium" id="uploadingFileName">products.csv</span>
                                            <span class="text-muted small" id="uploadingFileSize">(1.6 MB)</span>
                                        </div>
                                        <div class="progress" style="height: 4px; max-width: 400px; margin: 0 auto;">
                                            <div class="progress-bar bg-primary" role="progressbar" id="linearProgressBar"
                                                style="width: 0%"></div>
                                        </div>
                                        <p class="text-muted small mt-2 mb-0" id="progressStatusText">Uploading and
                                            processing...</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Results State -->
                            <div id="resultsUploadState" class="upload-state d-none">
                                <!-- Success Message -->
                                <div id="importSuccess" class="alert alert-success d-none mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-check-circle me-2 mt-1"></i>
                                        <div id="importSuccessMessage"></div>
                                    </div>
                                </div>

                                <!-- Error Message -->
                                <div id="importError" class="alert alert-danger d-none mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                                        <div id="importErrorMessage"></div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Help Text -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-info-circle text-muted mt-1"></i>
                                <div class="small text-muted">
                                    <p class="mb-1">Some data formats, such as dates, numbers, and colors, may not be
                                        recognized.
                                        <a href="<?php echo e(route('admin.products.template')); ?>"
                                            class="text-decoration-none">Download template</a> for proper format.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-link text-muted text-decoration-none" id="supportLink">
                            <i class="fas fa-question-circle me-1"></i> Support
                        </button>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                                id="discardBtn">Discard</button>
                            <button type="submit" class="btn btn-primary" id="importSubmitBtn"
                                style="background-color: #6366f1; border-color: #6366f1;">
                                <i class="fas fa-file-import me-1"></i> Import
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modern Item Creation Modal -->
    <?php echo $__env->make('inventory.partials.create-item-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    <!-- Edit Inventory Modal -->
    <div class="modal fade" id="editInventoryModal" tabindex="-1" aria-labelledby="editInventoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white py-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-edit me-2"></i>
                        <h5 class="modal-title mb-0" id="editInventoryModalLabel">Edit Product</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <form id="editInventoryForm" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div id="formStatus" class="alert d-none m-3"></div>

                        <div class="row g-0">
                            <!-- Left Column - Image Upload -->
                            <div class="col-lg-4 border-end">
                                <div class="p-3">
                                    <div class="text-center mb-3">
                                        <div class="position-relative d-inline-block mb-2" id="imagePreviewContainer">
                                            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiB2aWV3Qm94PSIwIDAgMjAwIDIwMCI+CiAgPHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNlZWVlZWUiLz4KICA8dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBhbGlnbm1lbnQtYmFzZWxpbmU9Im1pZGRsZSIgZmlsbD0iIzk5OSI+CiAgICBObyBJbWFnZQogIDwvdGV4dD4KPC9zdmc+"
                                                class="img-fluid rounded border" id="productImagePreview"
                                                style="max-height: 200px; width: auto; object-fit: cover;">
                                            <div class="position-absolute top-0 end-0 p-1">
                                                <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm"
                                                    id="removeImageBtn" style="display: none;">
                                                    <i class="fas fa-times text-danger"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <input type="file" class="d-none" id="imageUpload" name="image"
                                                accept="image/*">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                id="uploadImageBtn">
                                                <i class="fas fa-upload me-1"></i> Change Image
                                            </button>
                                            <div class="form-text small text-muted">JPG, PNG or GIF (Max 2MB)</div>
                                            <div class="invalid-feedback small" id="imageError"></div>
                                        </div>
                                    </div>

                                    <div class="card border-0 shadow-sm mb-3">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 small fw-medium"><i class="fas fa-tag me-1"></i> Product Status
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="editStatus" name="is_active" value="1" checked>
                                                <label class="form-check-label small fw-medium"
                                                    for="editStatus">Active</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="editTrackInventory" name="track_inventory" value="1" checked>
                                                <label class="form-check-label small fw-medium"
                                                    for="editTrackInventory">Track Inventory</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 small fw-medium"><i class="fas fa-info-circle me-1"></i>
                                                Additional Info</h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="mb-2">
                                                <label class="form-label small fw-medium text-muted mb-1">SKU</label>
                                                <input type="text" class="form-control form-control-sm" name="sku"
                                                    id="editSku" required>
                                                <div class="invalid-feedback small" id="skuError"></div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-medium text-muted mb-1">Barcode</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control form-control-sm" name="barcode"
                                                        id="editBarcode">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        id="generateBarcodeBtn">
                                                        <i class="fas fa-barcode"></i>
                                                    </button>
                                                </div>
                                                <div class="invalid-feedback small" id="barcodeError"></div>
                                            </div>
                                            <div class="mb-2 d-none">
                                                <label class="form-label small fw-medium text-muted mb-1">Expiry
                                                    Date</label>
                                                <input type="date" class="form-control form-control-sm" name="expiry_date"
                                                    id="editExpiryDate">
                                                <div class="invalid-feedback small" id="expiry_dateError"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Form Fields -->
                            <div class="col-lg-8">
                                <div class="p-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Product Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" name="name" id="editName"
                                            required>
                                        <div class="invalid-feedback small" id="nameError"></div>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-medium text-muted mb-1">Category <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select form-select-sm" name="category_id" id="editCategory"
                                                required>
                                                <option value="">Select Category</option>
                                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <div class="invalid-feedback small" id="category_idError"></div>
                                        </div>
                                        <div class="col-md-6 d-none">
                                            <label class="form-label small fw-medium text-muted mb-1">Manufacturer</label>
                                            <input type="text" class="form-control form-control-sm" name="manufacturer"
                                                id="editManufacturer">
                                            <div class="invalid-feedback small" id="manufacturerError"></div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-medium text-muted mb-1">Description</label>
                                        <textarea class="form-control form-control-sm" name="description"
                                            id="editDescription" rows="3"></textarea>
                                        <div class="invalid-feedback small" id="descriptionError"></div>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-header bg-light py-2">
                                                    <h6 class="mb-0 small fw-medium"><i class="fas fa-tags me-1"></i>
                                                        Pricing</h6>
                                                </div>
                                                <div class="card-body p-3">
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-medium text-muted mb-1">Cost Price
                                                            ($)</label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text"><?php echo e(currency_symbol()); ?></span>
                                                            <input type="number" step="0.01"
                                                                class="form-control form-control-sm" name="purchase_price"
                                                                id="editCostPrice">
                                                        </div>
                                                        <div class="invalid-feedback small" id="purchase_priceError"></div>
                                                    </div>
                                                    <div>
                                                        <label class="form-label small fw-medium text-muted mb-1">Selling
                                                            Price ($) <span class="text-danger">*</span></label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text"><?php echo e(currency_symbol()); ?></span>
                                                            <input type="number" step="0.01"
                                                                class="form-control form-control-sm" name="selling_price"
                                                                id="editSellingPrice" required>
                                                        </div>
                                                        <div class="invalid-feedback small" id="selling_priceError"></div>
                                                        <div class="small text-muted mt-1">
                                                            Profit: <span id="profitMarginDisplay"
                                                                class="fw-medium"><?php echo e(currency_symbol()); ?>0.00
                                                                (0%)</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-header bg-light py-2">
                                                    <h6 class="mb-0 small fw-medium"><i class="fas fa-boxes me-1"></i>
                                                        Inventory</h6>
                                                </div>
                                                <div class="card-body p-3">
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-medium text-muted mb-1">In
                                                            Stock</label>
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" step="0.0001" min="0"
                                                                class="form-control form-control-sm"
                                                                name="quantity_in_stock" id="editQuantity">
                                                            <select name="unit_type" class="form-select form-select-sm"
                                                                style="max-width: 100px;" id="editUnitType">
                                                                <?php $__currentLoopData = \App\Models\InventoryItem::$unitTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <option value="<?php echo e($key); ?>"><?php echo e($key); ?></option>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </select>
                                                        </div>
                                                        <div class="invalid-feedback small" id="quantity_in_stockError">
                                                        </div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-medium text-muted mb-1">Reorder
                                                            Level</label>
                                                        <input type="number" step="0.0001" min="0"
                                                            class="form-control form-control-sm" name="reorder_level"
                                                            id="editReorderLevel">
                                                        <div class="invalid-feedback small" id="reorder_levelError"></div>
                                                    </div>
                                                    <div>
                                                        <label class="form-label small fw-medium text-muted mb-1">Minimum
                                                            Qty</label>
                                                        <input type="number" step="0.0001" min="0"
                                                            class="form-control form-control-sm" name="minimum_quantity"
                                                            id="editMinimumQuantity">
                                                        <div class="invalid-feedback small" id="minimum_quantityError">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <?php if($branches->count() > 1): ?>
                                        <div class="mb-3">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-light py-2">
                                                    <h6 class="mb-0 small fw-medium"><i class="fas fa-map-marker-alt me-1"></i>
                                                        Branch Assignment</h6>
                                                </div>
                                                <div class="card-body p-3">
                                                    <div class="row g-2">
                                                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input edit-branch-checkbox"
                                                                        type="checkbox" name="branch_ids[]"
                                                                        value="<?php echo e($branch->id); ?>"
                                                                        id="branch_edit_<?php echo e($branch->id); ?>">
                                                                    <label class="form-check-label small"
                                                                        for="branch_edit_<?php echo e($branch->id); ?>">
                                                                        <i
                                                                            class="fas fa-map-marker-alt me-1 text-primary"></i><?php echo e($branch->name); ?>

                                                                    </label>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                    <small class="text-muted mt-2 d-block">Select branches where this product
                                                        will be available</small>
                                                    <div class="invalid-feedback" id="editBranchError"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="card border-0 shadow-sm mb-3">
                                        <div
                                            class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 small fw-medium"><i class="fas fa-layer-group me-1"></i>
                                                Variants</h6>
                                            <button type="button" class="btn btn-sm btn-primary py-0" id="editAddVariantBtn"
                                                style="font-size: 0.75rem;">
                                                <i class="fas fa-plus me-1"></i> Add
                                            </button>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover mb-0" id="editVariantsTable"
                                                    style="font-size: 0.85rem;">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Value</th>
                                                            <th>SKU</th>
                                                            <th>Price Adj.</th>
                                                            <th>Stock</th>
                                                            <th class="text-end">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="editVariantsTableBody">
                                                        <!-- Variants will be added here -->
                                                        <tr class="no-variants-row">
                                                            <td colspan="6" class="text-center text-muted py-2 small">
                                                                No variants.
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border-0 shadow-sm mb-3">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 small fw-medium"><i class="fas fa-sticky-note me-1"></i> Notes
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <textarea class="form-control form-control-sm" name="notes" id="editNotes"
                                                rows="2" placeholder="Add any additional notes here..."></textarea>
                                            <div class="invalid-feedback small" id="notesError"></div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-save me-1"></i> Save Changes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Script -->
    <?php $__env->startPush('scripts'); ?>
        <script>
            $(document).ready(function () {
                // Image upload preview
                const imageInput = document.getElementById('imageUpload');
                const imagePreview = document.getElementById('productImagePreview');
                const removeImageBtn = document.getElementById('removeImageBtn');
                const uploadImageBtn = document.getElementById('uploadImageBtn');

                // Handle image upload button click
                uploadImageBtn.addEventListener('click', () => imageInput.click());

                // Handle image selection
                imageInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) {
                            alert('File size must be less than 2MB');
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function (e) {
                            imagePreview.src = e.target.result;
                            removeImageBtn.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                });

                // Handle remove image
                removeImageBtn.addEventListener('click', function () {
                    imagePreview.src = '<?php echo e(asset('images/placeholder-product.png')); ?>';
                    imageInput.value = '';
                    removeImageBtn.style.display = 'none';
                });

                // ===== EDIT MODAL IMAGE UPLOAD =====
                // Handle image upload in edit modal
                const editImageInput = document.getElementById('imageUpload');
                const editImagePreview = document.getElementById('productImagePreview');
                const editUploadBtn = document.getElementById('uploadImageBtn');
                const editRemoveBtn = document.getElementById('removeImageBtn');

                if (editImageInput && editImagePreview && editUploadBtn) {
                    // Click upload button to browse
                    editUploadBtn.addEventListener('click', () => editImageInput.click());

                    // Handle file selection
                    editImageInput.addEventListener('change', function (e) {
                        const file = e.target.files[0];
                        if (file) {
                            // Validate file size (2MB for edit modal)
                            if (file.size > 2 * 1024 * 1024) {
                                alert('File size must be less than 2MB');
                                this.value = '';
                                return;
                            }

                            // Validate file type
                            if (!file.type.match('image/(jpeg|png|jpg|gif|webp)')) {
                                alert('Please select a valid image file (JPG, PNG, GIF, or WEBP)');
                                this.value = '';
                                return;
                            }

                            // Show preview
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                editImagePreview.src = e.target.result;
                                if (editRemoveBtn) {
                                    editRemoveBtn.style.display = 'block';
                                }
                            };
                            reader.onerror = function () {
                                alert('Error reading file. Please try again.');
                            };
                            reader.readAsDataURL(file);
                        }
                    });

                    // Handle remove image in edit modal
                    if (editRemoveBtn) {
                        editRemoveBtn.addEventListener('click', function (e) {
                            e.stopPropagation();
                            const placeholderSrc = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiB2aWV3Qm94PSIwIDAgMjAwIDIwMCI+CiAgPHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNlZWVlZWUiLz4KICA8dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBhbGlnbm1lbnQtYmFzZWxpbmU9Im1pZGRsZSIgZmlsbD0iIzk5OSI+CiAgICBObyBJbWFnZQogIDwvdGV4dD4KPC9zdmc+';
                            editImagePreview.src = placeholderSrc;
                            editImageInput.value = '';
                            this.style.display = 'none';
                        });
                    }
                }

                // Generate barcode
                $('#generateBarcodeBtn').click(function () {
                    // Simple random barcode for demo
                    const randomBarcode = 'BC' + Math.floor(100000 + Math.random() * 900000);
                    $('#editBarcode').val(randomBarcode);
                });

                // Calculate profit margin
                function calculateProfit() {
                    const cost = parseFloat($('#editCostPrice').val()) || 0;
                    const selling = parseFloat($('#editSellingPrice').val()) || 0;

                    if (cost > 0 && selling > 0) {
                        const profit = selling - cost;
                        const margin = (profit / cost) * 100;
                        $('#profitMarginDisplay').text(`$${profit.toFixed(2)} (${margin.toFixed(1)}%)`);
                    } else {
                        $('#profitMarginDisplay').text('$0.00 (0%)');
                    }
                }

                $('#editCostPrice, #editSellingPrice').on('input', calculateProfit);

                // ===== TAB NAVIGATION =====
                // Handle Next button clicks to navigate between tabs
                $(document).on('click', '.next-tab', function () {
                    const nextTab = $(this).data('next-tab');
                    if (nextTab) {
                        const nextTabButton = $(`#${nextTab}-tab`);
                        if (nextTabButton.length) {
                            nextTabButton.tab('show');
                            // Update progress bar
                            updateProgressBar(nextTab);
                        }
                    }
                });

                // Handle Previous button clicks to navigate between tabs
                $(document).on('click', '.prev-tab', function () {
                    const prevTab = $(this).data('prev-tab');
                    if (prevTab) {
                        const prevTabButton = $(`#${prevTab}-tab`);
                        if (prevTabButton.length) {
                            prevTabButton.tab('show');
                            // Update progress bar
                            updateProgressBar(prevTab);
                        }
                    }
                });

                // Update progress bar based on active tab
                function updateProgressBar(tabName) {
                    const progressMap = {
                        'basic-info': 25,
                        'pricing': 50,
                        'inventory': 75,
                        'variants': 85,
                        'shipping': 100
                    };
                    const progress = progressMap[tabName] || 25;
                    $('.progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
                }

                // Update progress bar when tabs are clicked directly
                $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
                    const tabId = $(e.target).attr('id').replace('-tab', '');
                    updateProgressBar(tabId);
                });

                // ===== IMAGE PREVIEW FOR CREATE MODAL =====
                // Handle main image upload in create modal
                const mainImageInput = document.getElementById('mainImageInput');
                const mainImagePreview = document.getElementById('mainImagePreview');
                const mainDropZone = document.getElementById('mainDropZone');
                const browseMainImage = document.getElementById('browseMainImage');
                const removeMainImage = document.getElementById('removeMainImage');

                if (mainImageInput && mainImagePreview) {
                    // Click to browse
                    if (browseMainImage) {
                        browseMainImage.addEventListener('click', () => mainImageInput.click());
                    }
                    if (mainDropZone) {
                        mainDropZone.addEventListener('click', () => mainImageInput.click());
                    }

                    // Handle file selection
                    mainImageInput.addEventListener('change', function (e) {
                        const file = e.target.files[0];
                        if (file) {
                            // Validate file size (5MB)
                            if (file.size > 5 * 1024 * 1024) {
                                alert('File size must be less than 5MB');
                                this.value = '';
                                return;
                            }

                            // Validate file type
                            if (!file.type.match('image/(jpeg|png|webp)')) {
                                alert('Please select a valid image file (JPG, PNG, or WEBP)');
                                this.value = '';
                                return;
                            }

                            // Show preview
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                mainImagePreview.src = e.target.result;
                                $('.main-image-preview').show();
                                $('.main-upload-area').hide();
                            };
                            reader.onerror = function () {
                                alert('Error reading file. Please try again.');
                            };
                            reader.readAsDataURL(file);
                        }
                    });

                    // Handle remove image
                    if (removeMainImage) {
                        removeMainImage.addEventListener('click', function (e) {
                            e.stopPropagation();
                            mainImagePreview.src = '#';
                            mainImageInput.value = '';
                            $('.main-image-preview').hide();
                            $('.main-upload-area').show();
                        });
                    }

                    // Drag and drop support
                    if (mainDropZone) {
                        mainDropZone.addEventListener('dragover', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            this.classList.add('border-primary');
                        });

                        mainDropZone.addEventListener('dragleave', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            this.classList.remove('border-primary');
                        });

                        mainDropZone.addEventListener('drop', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            this.classList.remove('border-primary');

                            const files = e.dataTransfer.files;
                            if (files.length > 0) {
                                mainImageInput.files = files;
                                mainImageInput.dispatchEvent(new Event('change'));
                            }
                        });
                    }
                }
            });
        </script>
    <?php $__env->stopPush(); ?>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content"
                style="backdrop-filter: blur(10px); background: rgba(255,255,255,0.95); border-radius: 1rem;">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo e(route('admin.inventory.categories.store')); ?>" method="POST" id="categoryForm">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-tag text-muted"></i>
                                </span>
                                <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="e.g., Hair Care, Skin Care" value="<?php echo e(old('name')); ?>" required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                rows="2" placeholder="Optional description"><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Icon Class (Font Awesome)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-icons text-muted"></i>
                                </span>
                                <input type="text" name="icon" class="form-control" placeholder="fas fa-spa (optional)">
                            </div>
                            <small class="text-muted">Leave empty to use default icon. Use <a
                                    href="https://fontawesome.com/icons" target="_blank">Font Awesome</a> icon
                                classes.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"
                                placeholder="Category description (optional)"></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
        <style>
            .bg-success-subtle {
                background-color: rgba(25, 135, 84, 0.1);
            }

            .bg-danger-subtle {
                background-color: rgba(220, 53, 69, 0.1);
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('js/inventory.js')); ?>"></script>
        <script src="<?php echo e(asset('js/create-item-modal.js')); ?>"></script>

        <script>
            $(document).ready(function () {
                // Set routes and data for external scripts
                window.routes = {
                    generateSku: '<?php echo e(route("admin.inventory.generate-name-sku")); ?>'
                };
                window.salonSlug = "<?php echo e(request()->current_salon->slug ?? ''); ?>";

                // ===== EDIT MODAL POPULATION =====
                $('#editInventoryModal').on('show.bs.modal', function (e) {
                    const button = $(e.relatedTarget);
                    const modal = $(this);

                    // Populate basic fields
                    modal.find('#editName').val(button.data('name'));
                    modal.find('#editCategory').val(button.data('category'));
                    modal.find('#editSku').val(button.data('sku'));
                    modal.find('#editBarcode').val(button.data('barcode'));
                    modal.find('#editDescription').val(button.data('description'));
                    modal.find('#editCostPrice').val(button.data('purchase-price'));
                    modal.find('#editSellingPrice').val(button.data('selling-price'));
                    modal.find('#editQuantity').val(button.data('quantity'));
                    modal.find('#editMinimumQuantity').val(button.data('minimum-quantity'));
                    modal.find('#editReorderLevel').val(button.data('reorder-level'));
                    modal.find('#editUnitType').val(button.data('unit'));
                    modal.find('#editLocation').val(button.data('location'));
                    modal.find('#editManufacturer').val(button.data('manufacturer'));
                    modal.find('#editNotes').val(button.data('notes'));
                    modal.find('#editExpiryDate').val(button.data('expiry-date'));
                    modal.find('#editStatus').prop('checked', button.data('status') === 'active');

                    // Set form action
                    const itemId = button.data('id');
                    const form = modal.find('#editInventoryForm');
                    form.attr('action', `/admin/inventory/${itemId}`);

                    // Handle image
                    const imageUrl = button.data('image');
                    const imagePreview = modal.find('#productImagePreview');
                    const removeBtn = modal.find('#removeImageBtn');

                    if (imageUrl && imageUrl !== '') {
                        imagePreview.attr('src', imageUrl);
                        removeBtn.show();
                    } else {
                        const placeholderSrc = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiB2aWV3Qm94PSIwIDAgMjAwIDIwMCI+CiAgPHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNlZWVlZWUiLz4KICA8dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBhbGlnbm1lbnQtYmFzZWxpbmU9Im1pZGRsZSIgZmlsbD0iIzk5OSI+CiAgICBObyBJbWFnZQogIDwvdGV4dD4KPC9zdmc+';
                        imagePreview.attr('src', placeholderSrc);
                        removeBtn.hide();
                    }

                    // Handle variants
                    const variants = button.data('variants');
                    const variantsTableBody = modal.find('#editVariantsTableBody');
                    variantsTableBody.empty();

                    if (variants && variants.length > 0) {
                        modal.find('.no-variants-row').remove();
                        variants.forEach(variant => {
                            const row = `
                                                    <tr>
                                                        <td><input type="text" class="form-control form-control-sm" name="variants[${variant.id}][name]" value="${variant.name || ''}" required></td>
                                                        <td><input type="text" class="form-control form-control-sm" name="variants[${variant.id}][value]" value="${variant.value || ''}" required></td>
                                                        <td><input type="text" class="form-control form-control-sm" name="variants[${variant.id}][sku]" value="${variant.sku || ''}"></td>
                                                        <td><input type="number" step="0.01" class="form-control form-control-sm" name="variants[${variant.id}][price_adjustment]" value="${variant.price_adjustment || 0}"></td>
                                                        <td><input type="number" step="0.01" class="form-control form-control-sm" name="variants[${variant.id}][stock_quantity]" value="${variant.stock_quantity || 0}"></td>
                                                        <td class="text-end">
                                                            <button type="button" class="btn btn-sm btn-danger remove-variant-btn"><i class="fas fa-trash"></i></button>
                                                            <input type="hidden" name="variants[${variant.id}][id]" value="${variant.id}">
                                                        </td>
                                                    </tr>
                                                `;
                            variantsTableBody.append(row);
                        });
                    } else {
                        variantsTableBody.html('<tr class="no-variants-row"><td colspan="6" class="text-center text-muted py-2 small">No variants.</td></tr>');
                    }

                    // Handle branch checkboxes - populate with item's assigned branches
                    const itemBranches = button.data('branches') || [];
                    modal.find('.edit-branch-checkbox').each(function () {
                        const branchId = parseInt($(this).val());
                        // Check if this branch is in the item's branches array
                        const isAssigned = itemBranches.some(b => parseInt(b.id) === branchId);
                        $(this).prop('checked', isAssigned);
                    });

                    // Calculate profit on load
                    if (typeof calculateProfit === 'function') {
                        calculateProfit();
                    }
                });

                // ===== BRANCH VALIDATION =====
                // Validate branch selection on create form submit
                // Only validate if branch checkboxes are visible (multi-branch salon)
                $('#createInventoryForm').on('submit', function (e) {
                    const branchCheckboxes = $('.branch-checkbox');
                    // Only validate if checkboxes exist AND are visible
                    if (branchCheckboxes.length > 0 && branchCheckboxes.first().is(':visible')) {
                        const checkedCount = branchCheckboxes.filter(':checked').length;
                        if (checkedCount === 0) {
                            e.preventDefault();
                            $('#branchError').text('Please select at least one branch').addClass('d-block');
                            // Scroll to branch section
                            const branchSection = $('[name="branch_ids[]"]').first().closest('.col-12');
                            if (branchSection.length) {
                                branchSection.get(0).scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                            return false;
                        } else {
                            $('#branchError').removeClass('d-block').text('');
                        }
                    }
                });

                // Validate branch selection on edit form submit
                // Only validate if branch checkboxes are visible (multi-branch salon)
                $('#editInventoryForm').on('submit', function (e) {
                    const branchCheckboxes = $('.edit-branch-checkbox');
                    // Only validate if checkboxes exist AND are visible
                    if (branchCheckboxes.length > 0 && branchCheckboxes.first().is(':visible')) {
                        const checkedCount = branchCheckboxes.filter(':checked').length;
                        if (checkedCount === 0) {
                            e.preventDefault();
                            $('#editBranchError').text('Please select at least one branch').addClass('d-block');
                            // Scroll to branch section
                            const branchCard = $('.edit-branch-checkbox').first().closest('.card');
                            if (branchCard.length) {
                                branchCard.get(0).scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                            return false;
                        } else {
                            $('#editBranchError').removeClass('d-block').text('');
                        }
                    }

                    e.preventDefault();

                    const form = $(this);
                    const formData = new FormData(this);
                    const submitBtn = form.find('button[type="submit"]');
                    const originalText = submitBtn.html();

                    // Disable submit button
                    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success) {
                                // Show success message
                                toastr.success(response.message || 'Product updated successfully!');

                                // Reload page after short delay
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1500);
                            }
                        },
                        error: function (xhr) {
                            let errorMessage = 'An error occurred while updating the product.';

                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.errors) {
                                    errorMessage = '<ul class="mb-0">';
                                    $.each(xhr.responseJSON.errors, function (field, messages) {
                                        errorMessage += '<li>' + messages[0] + '</li>';
                                    });
                                    errorMessage += '</ul>';
                                } else if (xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                            }

                            toastr.error(errorMessage);

                            // Re-enable submit button
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    });
                });

                // Clear branch error when checkbox is clicked
                $(document).on('change', '.branch-checkbox, .edit-branch-checkbox', function () {
                    const container = $(this).hasClass('branch-checkbox') ? '#branchError' : '#editBranchError';
                    $(container).removeClass('d-block').text('');
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
    <!-- Update Stock Modal -->
    <div class="modal fade" id="updateStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStockForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Product</label>
                            <div class="fw-bold" id="stockItemName"></div>
                            <div class="small text-muted">Current Global Stock: <span id="stockItemQuantity"></span> <span
                                    id="stockItemUnit"></span></div>
                        </div>

                        <?php if($branches->count() > 1): ?>
                            <div class="mb-3" id="stockBranchContainer">
                                <label class="form-label">Branch <span class="text-danger">*</span></label>
                                <select name="branch_id" class="form-select" id="stockBranchSelect" required>
                                    <option value="">Select Branch</option>
                                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <div class="form-text small">Select which branch to update stock for.</div>
                            </div>
                        <?php endif; ?>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Transaction Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="purchase">Purchase (Add)</option>
                                    <option value="return">Return (Add)</option>
                                    <option value="adjustment">Adjustment (Set/Add/Sub)</option>
                                    <option value="damaged">Damaged (Remove)</option>
                                    <option value="lost">Lost (Remove)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity</label>
                                <input type="number" step="0.0001" min="0.0001" name="quantity" class="form-control"
                                    required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Unit Cost (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><?php echo e(currency_symbol()); ?></span>
                                <input type="number" step="0.01" min="0" name="unit_cost" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stockModal = document.getElementById('updateStockModal');
            if (stockModal) {
                stockModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const quantity = button.getAttribute('data-quantity');
                    const unit = button.getAttribute('data-unit');
                    const branches = JSON.parse(button.getAttribute('data-branches') || '[]');

                    const modal = this;
                    modal.querySelector('#stockItemName').textContent = name;
                    modal.querySelector('#stockItemQuantity').textContent = quantity;
                    modal.querySelector('#stockItemUnit').textContent = unit || 'pcs';

                    const form = modal.querySelector('#updateStockForm');
                    // Use Blade to generate the route template, then replace the placeholder ID
                    const routeTemplate = "<?php echo e(route('admin.inventory.stock.update', ':id')); ?>";
                    form.action = routeTemplate.replace(':id', id);

                    // Branch handling
                    const branchSelect = modal.querySelector('#stockBranchSelect');
                    // Use session branch ID if available (switched context), otherwise user's assigned branch
                    const currentBranchId = "<?php echo e(session('current_branch_id') ?? auth()->user()->branch_id); ?>";
                    const userAssignedBranchId = "<?php echo e(auth()->user()->branch_id); ?>";

                    if (branchSelect) {
                        // If user is restricted to a branch OR has switched to a specific branch context
                        if (currentBranchId) {
                            branchSelect.value = currentBranchId;

                            // If user is strictly assigned (Staff/Manager), lock it.
                            // If Salon Admin switched context, we might want to allow changing? 
                            // User request says: "i am in alrady in branch then why showing select branch"
                            // So if context is set, we should probably lock it or at least default it.
                            // Let's lock it to respect the "current branch" context.
                            branchSelect.disabled = true;

                            // Ensure we submit the value even if disabled
                            let hiddenInput = form.querySelector('input[name="branch_id"]');
                            if (!hiddenInput) {
                                hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'branch_id';
                                form.appendChild(hiddenInput);
                            }
                            hiddenInput.value = currentBranchId;

                            // Hide the container
                            const container = modal.querySelector('#stockBranchContainer');
                            if (container) container.style.display = 'none';

                        } else {
                            // Global Context (Salon Admin with no specific branch selected/switched?)
                            // Actually SetBranchContext middleware forces a branch if one exists.
                            // But if we are here, maybe we want to allow selection.

                            // Filter options based on item assignment BUT allow selecting any branch to add new stock (assigning it)
                            Array.from(branchSelect.options).forEach(opt => {
                                if (opt.value) {
                                    // We allow selecting any branch. If it's not assigned, the backend should handle it.
                                    // But maybe we want to highlight assigned ones?
                                    // For now, let's SHOW all branches so user can add stock to a new branch.
                                    const isAssigned = branches.some(b => b.id == opt.value);
                                    // opt.text = opt.text + (isAssigned ? '' : ' (New)'); 
                                    opt.hidden = false;
                                    opt.disabled = false;
                                }
                            });

                            // If only one branch assigned, select it
                            if (branches.length === 1) {
                                branchSelect.value = branches[0].id;
                            } else {
                                branchSelect.value = '';
                            }
                        }
                    }
                });
            }
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\inventory\index.blade.php ENDPATH**/ ?>