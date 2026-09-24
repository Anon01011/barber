<?php $__env->startSection('title', 'Edit Discount Membership'); ?>


<?php $__env->startSection('content'); ?>
<div class="container-fluid membership-container py-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                <h1 class="h4 fw-bold text-primary">
                    <i class="fas fa-id-card me-2"></i> Edit Discount Membership
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.memberships.index')); ?>">Memberships</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
            <span class="text-muted small">Edit real-time discounts; discount value is automatically calculated during billing.</span>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg">
            <form action="<?php echo e(route('admin.memberships.update', $membership)); ?>" method="POST" class="needs-validation" novalidate>
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <!-- Basic Information Section -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-info-circle me-2"></i> Membership Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-2">
                            <div class="col-md-4">
                                <label for="name" class="form-label fw-semibold">Membership Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg <?php if($errors->has('name')): ?> is-invalid <?php endif; ?>" id="name" name="name" value="<?php echo e(old('name', $membership->name)); ?>" placeholder="E.g. Salon VIP" required>
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
                                <div id="name-error" class="invalid-feedback d-none">Please enter a membership name.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="discount_value" class="form-label fw-semibold">Discount Value <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><?php echo e($globalSettings['currency_symbol']); ?></span>
                                <input type="number" step="1" class="form-control <?php if($errors->has('discount_value')): ?> is-invalid <?php endif; ?>" id="discount_value" name="discount_value" value="<?php echo e(old('discount_value', $membership->discount_value)); ?>" min="0" placeholder="0.00" required>
                                </div>
                                <?php $__errorArgs = ['discount_value'];
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
                            <div class="col-md-4">
                                <label for="is_taxable" class="form-label fw-semibold">Tax <span class="text-danger">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['is_taxable'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="is_taxable" name="is_taxable" required>
                                    <option value="0" <?php echo e(old('is_taxable', $membership->is_taxable) == 0 ? 'selected' : ''); ?>>Not Applicable</option>
                                    <option value="1" <?php echo e(old('is_taxable', $membership->is_taxable) == 1 ? 'selected' : ''); ?>>Taxable</option>
                                </select>
                                <?php $__errorArgs = ['is_taxable'];
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
                            <div class="col-md-6">
                                <label for="validity_value" class="form-label fw-semibold">Validity Duration <span class="text-danger">*</span></label>
                                <input type="number" class="form-control <?php $__errorArgs = ['validity_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="validity_value" name="validity_value" value="<?php echo e(old('validity_value', $membership->validity_value)); ?>" min="1" max="3650" required>
                                <?php $__errorArgs = ['validity_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div id="validity-value-error" class="invalid-feedback d-none">Please enter validity duration.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="validity_unit" class="form-label fw-semibold">Validity Unit <span class="text-danger">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['validity_unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="validity_unit" name="validity_unit" required>
                                    <option value="days" <?php echo e(old('validity_unit', $membership->validity_unit) == 'days' ? 'selected' : ''); ?>>Days</option>
                                    <option value="weeks" <?php echo e(old('validity_unit', $membership->validity_unit) == 'weeks' ? 'selected' : ''); ?>>Weeks</option>
                                    <option value="months" <?php echo e(old('validity_unit', $membership->validity_unit) == 'months' ? 'selected' : ''); ?>>Months</option>
                                    <option value="years" <?php echo e(old('validity_unit', $membership->validity_unit) == 'years' ? 'selected' : ''); ?>>Years</option>
                                </select>
                                <?php $__errorArgs = ['validity_unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div id="validity-unit-error" class="invalid-feedback d-none">Please select validity unit.</div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4 pt-1">
                                    <input class="form-check-input <?php $__errorArgs = ['is_active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" type="checkbox" id="is_active" name="is_active" value="1" <?php echo e(old('is_active', $membership->is_active) ? 'checked' : ''); ?>>
                                    <label class="form-check-label fw-semibold" for="is_active">
                                        Active Membership
                                    </label>
                                    <div class="form-text">Uncheck to create inactive membership</div>
                                    <?php $__errorArgs = ['is_active'];
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
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="description" name="description" rows="3" placeholder="Describe the membership"><?php echo e(old('description', $membership->description)); ?></textarea>
                            <?php $__errorArgs = ['description'];
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
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Membership Type <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-auto">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="membership_type" id="membership-type-all" value="all" <?php echo e(old('membership_type', $membership->membership_type) == 'all' ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="membership-type-all">
                                            All Items (Services & Products)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="membership_type" id="membership-type-services" value="services" <?php echo e(old('membership_type', $membership->membership_type) == 'services' ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="membership-type-services">
                                            Services Only
                                        </label>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="membership_type" id="membership-type-products" value="products" <?php echo e(old('membership_type', $membership->membership_type) == 'products' ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="membership-type-products">
                                            Products Only
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <?php $__errorArgs = ['membership_type'];
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
                    </div>
                </div>

                <!-- Membership Items Section -->
                <div class="card mb-4 shadow-sm" id="items-card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold"><i class="fas fa-list-ul me-2"></i> Membership Items (Services & Products) <span class="text-danger">*</span></h5>
                            <div class="d-flex align-items-center gap-2">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="service-dropdown-btn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="width: 250px;">
                                        Select services...
                                    </button>
                                    <div class="dropdown-menu p-3" id="service-dropdown-menu" style="width: 300px; max-height: 300px; overflow-y: auto;">
                                        <div class="mb-2">
                                            <input type="text" class="form-control form-control-sm" id="service-search" placeholder="Search services...">
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="select-all-services">
                                            <label class="form-check-label fw-semibold" for="select-all-services">
                                                Select All Services
                                            </label>
                                        </div>
                                        <hr class="dropdown-divider">
                                        <div id="service-list">
                                            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="form-check service-item" data-name="<?php echo e(strtolower($service->name)); ?>" data-type="service">
                                                    <input class="form-check-input service-checkbox" type="checkbox" id="service-<?php echo e($service->id); ?>" value="<?php echo e($service->id); ?>" data-price="<?php echo e($service->price); ?>" data-name="<?php echo e($service->name); ?>" data-type="service">
                                                    <label class="form-check-label" for="service-<?php echo e($service->id); ?>">
                                                        <?php echo e($service->name); ?> - <?php echo e($globalSettings['currency_symbol']); ?> <?php echo e(number_format($service->price, 2)); ?>

                                                    </label>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="product-dropdown-btn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="width: 250px;">
                                        Select products...
                                    </button>
                                    <div class="dropdown-menu p-3" id="product-dropdown-menu" style="width: 300px; max-height: 300px; overflow-y: auto;">
                                        <div class="mb-2">
                                            <input type="text" class="form-control form-control-sm" id="product-search" placeholder="Search products...">
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="select-all-products">
                                            <label class="form-check-label fw-semibold" for="select-all-products">
                                                Select All Products
                                            </label>
                                        </div>
                                        <hr class="dropdown-divider">
                                        <div id="product-list">
                                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="form-check product-item" data-name="<?php echo e(strtolower($product->name)); ?>" data-type="product">
                                                    <input class="form-check-input product-checkbox" type="checkbox" id="product-<?php echo e($product->id); ?>" value="<?php echo e($product->id); ?>" data-price="<?php echo e($product->selling_price); ?>" data-name="<?php echo e($product->name); ?>" data-type="product">
                                                    <label class="form-check-label" for="product-<?php echo e($product->id); ?>">
                                                        <?php echo e($product->name); ?> - <?php echo e($globalSettings['currency_symbol']); ?> <?php echo e(number_format($product->selling_price, 2)); ?>

                                                    </label>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" id="add-item-btn">
                                    <i class="fas fa-plus"></i> Add Item(s)
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-3">
                        <div id="items-error" class="invalid-feedback d-none mb-2">Add at least one item (service or product).</div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle mb-0 membership-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Item</th>
                                        <th>Value</th>
                                        <th>Qty</th>
                                        <th>%</th>
                                        <th>Discount</th>
                                        <th>Membership Price</th>
                                        <th style="width: 80px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $membership->services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="item-row" data-row-index="<?php echo e($loop->index); ?>">
                                            <td><span class="item-type fw-semibold">Service</span></td>
                                            <td>
                                                <span class="item-name fw-semibold"><?php echo e($service->name); ?></span>
                                                <input type="hidden" class="item-id-input" name="items[]" value="<?php echo e($service->id); ?>">
                                                <input type="hidden" class="item-type-input" name="item_types[]" value="service">
                                            </td>
                                            <td><input type="text" class="form-control form-control-sm original-price" value="<?php echo e($globalSettings['currency_symbol']); ?> <?php echo e(number_format($service->price, 2)); ?>" readonly></td>
                                            <td><input type="number" class="form-control form-control-sm quantity-input" value="<?php echo e($service->pivot->quantity); ?>" min="1" name="quantity[]"></td>
                                            <td>
                                                <select class="form-select form-select-sm discount-type-select" style="width:85px;">
                                                    <option value="percent" <?php echo e($service->pivot->discount_type == 'percent' ? 'selected' : ''); ?>>%</option>
                                                    <option value="amount" <?php echo e($service->pivot->discount_type == 'amount' ? 'selected' : ''); ?>>Value</option>
                                                </select>
                                                <input type="hidden" class="discount-type-input" name="discount_type[]" value="<?php echo e($service->pivot->discount_type); ?>">
                                            </td>
                                            <td>
                                                <label class="form-label dynamic-label mb-1 small"><?php echo e($service->pivot->discount_type == 'percent' ? 'Discount %' : 'Discount ' . $globalSettings['currency_symbol']); ?></label>
                                                <input type="number" class="form-control form-control-sm discount-input" value="<?php echo e($service->pivot->discount_value); ?>" min="0" step="0.01" <?php echo e($service->pivot->discount_type == 'percent' ? 'max="100"' : ''); ?>>
                                                <input type="hidden" class="row-discount-value" name="item_discount_value[]" value="<?php echo e($service->pivot->discount_value); ?>">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm membership-price" value="<?php echo e($globalSettings['currency_symbol']); ?> <?php echo e(number_format($service->pivot->membership_price, 2)); ?>" readonly>
                                                <input type="hidden" class="row-membership-price" name="membership_price[]" value="<?php echo e($service->pivot->membership_price); ?>">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php $__currentLoopData = $membership->inventoryItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="item-row" data-row-index="<?php echo e($loop->index + $membership->services->count()); ?>">
                                            <td><span class="item-type fw-semibold">Product</span></td>
                                            <td>
                                                <span class="item-name fw-semibold"><?php echo e($product->name); ?></span>
                                                <input type="hidden" class="item-id-input" name="items[]" value="<?php echo e($product->id); ?>">
                                                <input type="hidden" class="item-type-input" name="item_types[]" value="product">
                                            </td>
                                            <td><input type="text" class="form-control form-control-sm original-price" value="<?php echo e($globalSettings['currency_symbol']); ?> <?php echo e(number_format($product->selling_price, 2)); ?>" readonly></td>
                                            <td><input type="number" class="form-control form-control-sm quantity-input" value="<?php echo e($product->pivot->quantity); ?>" min="1" name="quantity[]"></td>
                                            <td>
                                                <select class="form-select form-select-sm discount-type-select" style="width:85px;">
                                                    <option value="percent" <?php echo e($product->pivot->discount_type == 'percent' ? 'selected' : ''); ?>>%</option>
                                                    <option value="amount" <?php echo e($product->pivot->discount_type == 'amount' ? 'selected' : ''); ?>>Value</option>
                                                </select>
                                                <input type="hidden" class="discount-type-input" name="discount_type[]" value="<?php echo e($product->pivot->discount_type); ?>">
                                            </td>
                                            <td>
                                                <label class="form-label dynamic-label mb-1 small"><?php echo e($product->pivot->discount_type == 'percent' ? 'Discount %' : 'Discount ' . $globalSettings['currency_symbol']); ?></label>
                                                <input type="number" class="form-control form-control-sm discount-input" value="<?php echo e($product->pivot->discount_value); ?>" min="0" step="0.01" <?php echo e($product->pivot->discount_type == 'percent' ? 'max="100"' : ''); ?>>
                                                <input type="hidden" class="row-discount-value" name="item_discount_value[]" value="<?php echo e($product->pivot->discount_value); ?>">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm membership-price" value="<?php echo e($globalSettings['currency_symbol']); ?> <?php echo e(number_format($product->pivot->membership_price, 2)); ?>" readonly>
                                                <input type="hidden" class="row-membership-price" name="membership_price[]" value="<?php echo e($product->pivot->membership_price); ?>">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Bulk apply controls -->
                        <div class="pt-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-auto fw-semibold">Apply All Discount</div>
                                <div class="col-auto">
                                    <select class="form-select form-select-sm" id="bulk-discount-type" style="width: 90px;">
                                        <option value="percent">%</option>
                                        <option value="amount">Value</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <input type="number" class="form-control form-control-sm" id="bulk-discount-value" placeholder="0" min="0" step="0.01" style="width:100px;">
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="apply-all-btn"><i class="fas fa-magic"></i> Apply All</button>
                                </div>
                                <div class="col">
                                    <span class="form-text small" id="bulk-apply-summary" style="display: none;">Applied to <span id="applied-count">0</span> item(s).</span>
                                </div>
                            </div>
                        </div>
                        <?php $__errorArgs = ['items'];
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
                </div>

                <!-- Row Template (hidden) for JS cloning -->
                <table style="display:none;">
                    <tbody id="item-row-template">
                        <tr class="item-row" data-row-index="">
                            <td><span class="item-type fw-semibold"></span></td>
                            <td>
                                <span class="item-name fw-semibold"></span>
                                <input type="hidden" class="item-id-input" name="items[]">
                                <input type="hidden" class="item-type-input" name="item_types[]">
                            </td>
                            <td><input type="text" class="form-control form-control-sm original-price" readonly></td>
                            <td><input type="number" class="form-control form-control-sm quantity-input" value="1" min="1" name="quantity[]"></td>
                            <td>
                                <select class="form-select form-select-sm discount-type-select" style="width:85px;">
                                    <option value="percent">%</option>
                                    <option value="amount">Value</option>
                                </select>
                                <input type="hidden" class="discount-type-input" name="discount_type[]">
                            </td>
                            <td>
                                <label class="form-label dynamic-label mb-1 small">Discount %</label>
                                <input type="number" class="form-control form-control-sm discount-input" value="0" min="0" step="0.01">
                                <input type="hidden" class="row-discount-value" name="item_discount_value[]">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm membership-price" readonly>
                                <input type="hidden" class="row-membership-price" name="membership_price[]">
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="card mt-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('admin.memberships.index')); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Cancel</a>
                            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i> Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const servicesData = <?php echo json_encode($services->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'price' => $s->price])->values()->toArray()) ?>;
const productsData = <?php echo json_encode($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'price' => $p->selling_price])->values()->toArray()) ?>;

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: '<?php echo e($globalSettings['currency_code']); ?>',
        minimumFractionDigits: 2
    }).format(amount).replace('<?php echo e($globalSettings['currency_code']); ?>', '<?php echo e($globalSettings['currency_symbol']); ?>').trim();
}

document.addEventListener('DOMContentLoaded', function() {
    // Service dropdown elements
    const selectAllServicesCheckbox = document.getElementById('select-all-services');
    const serviceSearch = document.getElementById('service-search');
    const serviceDropdownBtn = document.getElementById('service-dropdown-btn');
    const serviceItems = document.querySelectorAll('.service-item');
    const serviceCheckboxes = document.querySelectorAll('.service-checkbox');

    // Product dropdown elements
    const selectAllProductsCheckbox = document.getElementById('select-all-products');
    const productSearch = document.getElementById('product-search');
    const productDropdownBtn = document.getElementById('product-dropdown-btn');
    const productItems = document.querySelectorAll('.product-item');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');

    // Membership type elements
    const membershipTypeAll = document.getElementById('membership-type-all');
    const membershipTypeServices = document.getElementById('membership-type-services');
    const membershipTypeProducts = document.getElementById('membership-type-products');
    const serviceDropdown = document.getElementById('service-dropdown-btn').closest('.dropdown');
    const productDropdown = document.getElementById('product-dropdown-btn').closest('.dropdown');

    // Function to toggle dropdown visibility based on membership type
    function toggleDropdowns() {
        const selectedType = document.querySelector('input[name="membership_type"]:checked').value;
        if (selectedType === 'all') {
            serviceDropdown.style.display = 'block';
            productDropdown.style.display = 'block';
        } else if (selectedType === 'services') {
            serviceDropdown.style.display = 'block';
            productDropdown.style.display = 'none';
        } else if (selectedType === 'products') {
            serviceDropdown.style.display = 'none';
            productDropdown.style.display = 'block';
        }
    }

    // Initial toggle
    toggleDropdowns();

    // Add event listeners to membership type radios
    membershipTypeAll.addEventListener('change', toggleDropdowns);
    membershipTypeServices.addEventListener('change', toggleDropdowns);
    membershipTypeProducts.addEventListener('change', toggleDropdowns);

    // Common elements
    const addItemBtn = document.getElementById('add-item-btn');
    const tbody = document.querySelector('.membership-table tbody');
    const itemTemplate = document.getElementById('item-row-template').querySelector('tr');
    const bulkDiscountType = document.getElementById('bulk-discount-type');
    const bulkDiscountValue = document.getElementById('bulk-discount-value');
    const applyAllBtn = document.getElementById('apply-all-btn');
    const bulkApplySummary = document.getElementById('bulk-apply-summary');
    const appliedCount = document.getElementById('applied-count');
    const form = document.querySelector('form.needs-validation');
    const itemsError = document.getElementById('items-error');
    const nameInput = document.getElementById('name');
    const nameError = document.getElementById('name-error');
    const discountValueInput = document.getElementById('discount_value');
    const validityValueInput = document.getElementById('validity_value');
    const validityValueError = document.getElementById('validity-value-error');
    const validityUnitInput = document.getElementById('validity_unit');
    const validityUnitError = document.getElementById('validity-unit-error');
    const isTaxableInput = document.getElementById('is_taxable');

    let rowIndex = 0;
    let rows = [];
    let selectedItemIds = new Set(); // Set of 'type-id' e.g., 'service-1', 'product-2'

    // Function to update service dropdown button text
    function updateServiceDropdownButton() {
        const selectedCount = document.querySelectorAll('.service-checkbox:checked').length;
        if (selectedCount === 0) {
            serviceDropdownBtn.textContent = 'Select services...';
        } else {
            serviceDropdownBtn.textContent = `${selectedCount} service${selectedCount > 1 ? 's' : ''} selected`;
        }
    }

    // Function to update product dropdown button text
    function updateProductDropdownButton() {
        const selectedCount = document.querySelectorAll('.product-checkbox:checked').length;
        if (selectedCount === 0) {
            productDropdownBtn.textContent = 'Select products...';
        } else {
            productDropdownBtn.textContent = `${selectedCount} product${selectedCount > 1 ? 's' : ''} selected`;
        }
    }

    // Function to filter items based on search
    function filterItems(searchTerm, items, checkboxes, selectAllCheckbox, updateButton) {
        items.forEach(item => {
            const name = item.dataset.name;
            if (name.includes(searchTerm.toLowerCase())) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
                // Uncheck hidden items
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox.checked) {
                    checkbox.checked = false;
                }
            }
        });
        updateSelectAllState(checkboxes, selectAllCheckbox);
        updateButton();
    }

    // Function to update select all state based on visible items
    function updateSelectAllState(checkboxes, selectAllCheckbox) {
        const visibleCheckboxes = Array.from(checkboxes).filter(cb => cb.closest('.form-check').style.display !== 'none');
        const visibleChecked = visibleCheckboxes.filter(cb => cb.checked).length;
        const visibleTotal = visibleCheckboxes.length;

        if (visibleTotal === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
            return;
        }

        selectAllCheckbox.checked = visibleChecked === visibleTotal;
        selectAllCheckbox.indeterminate = visibleChecked > 0 && visibleChecked < visibleTotal;
    }

    // Service search functionality
    serviceSearch.addEventListener('input', function() {
        const searchTerm = this.value;
        filterItems(searchTerm, serviceItems, serviceCheckboxes, selectAllServicesCheckbox, updateServiceDropdownButton);
    });

    // Product search functionality
    productSearch.addEventListener('input', function() {
        const searchTerm = this.value;
        filterItems(searchTerm, productItems, productCheckboxes, selectAllProductsCheckbox, updateProductDropdownButton);
    });

    // Select All Services checkbox functionality (only visible)
    selectAllServicesCheckbox.addEventListener('change', function() {
        const visibleCheckboxes = Array.from(serviceCheckboxes).filter(cb => cb.closest('.service-item').style.display !== 'none');
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateServiceDropdownButton();
    });

    // Select All Products checkbox functionality (only visible)
    selectAllProductsCheckbox.addEventListener('change', function() {
        const visibleCheckboxes = Array.from(productCheckboxes).filter(cb => cb.closest('.product-item').style.display !== 'none');
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateProductDropdownButton();
    });

    // Individual service checkbox functionality
    serviceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState(serviceCheckboxes, selectAllServicesCheckbox);
            updateServiceDropdownButton();
        });
    });

    // Individual product checkbox functionality
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState(productCheckboxes, selectAllProductsCheckbox);
            updateProductDropdownButton();
        });
    });

    // Prevent dropdown from closing when clicking inside
    document.getElementById('service-dropdown-menu').addEventListener('click', function(e) {
        e.stopPropagation();
    });
    document.getElementById('product-dropdown-menu').addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // When service dropdown is shown, check services that are already added
    serviceDropdownBtn.addEventListener('show.bs.dropdown', function() {
        serviceCheckboxes.forEach(checkbox => {
            const itemId = `service-${checkbox.value}`;
            if (selectedItemIds.has(itemId)) {
                checkbox.checked = true;
            }
        });
        updateSelectAllState(serviceCheckboxes, selectAllServicesCheckbox);
        updateServiceDropdownButton();
    });

    // When product dropdown is shown, check products that are already added
    productDropdownBtn.addEventListener('show.bs.dropdown', function() {
        productCheckboxes.forEach(checkbox => {
            const itemId = `product-${checkbox.value}`;
            if (selectedItemIds.has(itemId)) {
                checkbox.checked = true;
            }
        });
        updateSelectAllState(productCheckboxes, selectAllProductsCheckbox);
        updateProductDropdownButton();
    });

    addItemBtn.addEventListener('click', function() {
        const selectedServiceCheckboxes = document.querySelectorAll('.service-checkbox:checked');
        const selectedProductCheckboxes = document.querySelectorAll('.product-checkbox:checked');
        const totalSelected = selectedServiceCheckboxes.length + selectedProductCheckboxes.length;

        if (totalSelected === 0) {
            alert('Please select at least one item (service or product).');
            return;
        }

        let addedCount = 0;

        // Add selected services
        selectedServiceCheckboxes.forEach(checkbox => {
            const item = {
                id: checkbox.value,
                name: checkbox.dataset.name,
                price: parseFloat(checkbox.dataset.price),
                type: 'service'
            };

            const itemId = `service-${item.id}`;
            if (!selectedItemIds.has(itemId)) {
                addItemRow(item);
                addedCount++;
            }
        });

        // Add selected products
        selectedProductCheckboxes.forEach(checkbox => {
            const item = {
                id: checkbox.value,
                name: checkbox.dataset.name,
                price: parseFloat(checkbox.dataset.price),
                type: 'product'
            };

            const itemId = `product-${item.id}`;
            if (!selectedItemIds.has(itemId)) {
                addItemRow(item);
                addedCount++;
            }
        });

        // Deselect added items
        selectedServiceCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        selectedProductCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        selectAllServicesCheckbox.checked = false;
        selectAllServicesCheckbox.indeterminate = false;
        selectAllProductsCheckbox.checked = false;
        selectAllProductsCheckbox.indeterminate = false;
        serviceSearch.value = '';
        productSearch.value = '';
        filterItems('', serviceItems, serviceCheckboxes, selectAllServicesCheckbox, updateServiceDropdownButton);
        filterItems('', productItems, productCheckboxes, selectAllProductsCheckbox, updateProductDropdownButton);

        // Close dropdowns
        const serviceDropdown = bootstrap.Dropdown.getInstance(serviceDropdownBtn);
        if (serviceDropdown) {
            serviceDropdown.hide();
        }
        const productDropdown = bootstrap.Dropdown.getInstance(productDropdownBtn);
        if (productDropdown) {
            productDropdown.hide();
        }

        if (addedCount === 0) {
            alert('All selected items are already added.');
        }
    });

    function addItemRow(item) {
        const row = itemTemplate.cloneNode(true);
        row.dataset.rowIndex = rowIndex;
        const itemTypeSpan = row.querySelector('.item-type');
        const itemNameSpan = row.querySelector('.item-name');
        const itemIdHidden = row.querySelector('.item-id-input');
        const itemTypeHidden = row.querySelector('.item-type-input');
        const quantityInput = row.querySelector('.quantity-input');
        const discountTypeSelect = row.querySelector('.discount-type-select');
        const discountTypeHidden = row.querySelector('.discount-type-input');
        const dynamicLabel = row.querySelector('.dynamic-label');
        const discountInput = row.querySelector('.discount-input');
        const originalPriceInput = row.querySelector('.original-price');
        const membershipPriceInput = row.querySelector('.membership-price');
        const rowDiscountHidden = row.querySelector('.row-discount-value');
        const rowMembershipHidden = row.querySelector('.row-membership-price');

        itemTypeSpan.textContent = item.type.charAt(0).toUpperCase() + item.type.slice(1);
        itemNameSpan.textContent = item.name;
        itemIdHidden.value = item.id;
        itemTypeHidden.value = item.type;
        quantityInput.value = 1;
        discountTypeSelect.value = 'percent';
        discountTypeHidden.value = 'percent';
        dynamicLabel.textContent = 'Discount %';
        discountInput.max = 100;
        discountInput.placeholder = '0';
        originalPriceInput.value = formatCurrency(item.price);
        membershipPriceInput.value = formatCurrency(item.price);
        rowDiscountHidden.value = 0;
        rowMembershipHidden.value = item.price;

        // Add to selected item IDs set
        selectedItemIds.add(`${item.type}-${item.id}`);

        quantityInput.addEventListener('input', () => calculateRow(row));
        discountTypeSelect.addEventListener('change', function() {
            const isPercent = this.value === 'percent';
            dynamicLabel.textContent = isPercent ? 'Discount %' : 'Discount <?php echo e($globalSettings['currency_symbol']); ?>';
            discountInput.max = isPercent ? 100 : '';
            discountInput.placeholder = isPercent ? '0' : '0.00';
            discountTypeHidden.value = this.value;
            calculateRow(row);
        });
        discountInput.addEventListener('input', () => calculateRow(row));
        row.querySelector('.remove-row').addEventListener('click', () => {
            selectedItemIds.delete(`${item.type}-${item.id}`);
            rows = rows.filter(r => r !== row);
            row.remove();
            if (rows.length === 0) {
                itemsError.classList.remove('d-none');
            }
        });

        tbody.appendChild(row);
        rows.push(row);
        rowIndex++;
        itemsError.classList.add('d-none');
        calculateRow(row);
    }

    function calculateRow(row) {
        const original = parseFloat(row.querySelector('.original-price').value.replace(/[^\d.]/g, '')) || 0;
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 1;
        const type = row.querySelector('.discount-type-select').value;
        const disc = parseFloat(row.querySelector('.discount-input').value) || 0;

        let discounted;
        if (type === 'percent') {
            discounted = Math.max(0, original * (1 - Math.min(disc / 100, 1)));
        } else {
            discounted = Math.max(0, original - disc);
        }

        const totalDiscounted = discounted * quantity;

        row.querySelector('.membership-price').value = formatCurrency(totalDiscounted);
        row.querySelector('.row-discount-value').value = disc;
        row.querySelector('.row-membership-price').value = totalDiscounted;
        
        calculateTotalDiscountValue();
    }

    function calculateTotalDiscountValue() {
        let total = 0;
        document.querySelectorAll('.row-membership-price').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        discountValueInput.value = total.toFixed(2);
        discountValueInput.classList.remove('is-invalid');
    }

    applyAllBtn.addEventListener('click', function() {
        if (rows.length === 0) return;
        const type = bulkDiscountType.value;
        const value = parseFloat(bulkDiscountValue.value) || 0;
        rows.forEach(row => {
            row.querySelector('.discount-type-select').value = type;
            row.querySelector('.discount-type-input').value = type;
            row.querySelector('.discount-input').value = value;
            const isPercent = type === 'percent';
            row.querySelector('.dynamic-label').textContent = isPercent ? 'Discount %' : 'Discount <?php echo e($globalSettings['currency_symbol']); ?>';
            row.querySelector('.discount-input').max = isPercent ? 100 : '';
            row.querySelector('.discount-input').placeholder = isPercent ? '0' : '0.00';
            calculateRow(row);
        });
        appliedCount.textContent = rows.length;
        bulkApplySummary.style.display = 'block';
        setTimeout(() => bulkApplySummary.style.display = 'none', 3000);
        bulkDiscountValue.value = '';
    });

    form.addEventListener('submit', function(e) {
        if (rows.length === 0) {
            e.preventDefault();
            itemsError.classList.remove('d-none');
            return false;
        }
        if (!nameInput.value.trim()) {
            e.preventDefault();
            nameError.classList.remove('d-none');
            nameInput.classList.add('is-invalid');
            return false;
        }
        if (!discountValueInput.value) {
            discountValueInput.classList.add('is-invalid');
            e.preventDefault();
            return false;
        }
        if (!validityValueInput.value) {
            validityValueError.classList.remove('d-none');
            validityValueInput.classList.add('is-invalid');
            e.preventDefault();
            return false;
        }
        if (!validityUnitInput.value) {
            validityUnitError.classList.remove('d-none');
            validityUnitInput.classList.add('is-invalid');
            e.preventDefault();
            return false;
        }
        if (!isTaxableInput.value) {
            isTaxableInput.classList.add('is-invalid');
            e.preventDefault();
            return false;
        }
    });
    nameInput.addEventListener('input', () => {
        nameError.classList.add('d-none');
        nameInput.classList.remove('is-invalid');
    });
    discountValueInput.addEventListener('input', () => discountValueInput.classList.remove('is-invalid'));
    validityValueInput.addEventListener('input', () => {
        validityValueError.classList.add('d-none');
        validityValueInput.classList.remove('is-invalid');
    });
    validityUnitInput.addEventListener('change', () => {
        validityUnitError.classList.add('d-none');
        validityUnitInput.classList.remove('is-invalid');
    });
    isTaxableInput.addEventListener('change', () => isTaxableInput.classList.remove('is-invalid'));

    // Initialize existing rows
    const existingRows = document.querySelectorAll('.item-row');
    existingRows.forEach((row, index) => {
        const itemId = row.querySelector('.item-id-input').value;
        const itemType = row.querySelector('.item-type-input').value;
        selectedItemIds.add(`${itemType}-${itemId}`);
        rows.push(row);
        rowIndex = Math.max(rowIndex, parseInt(row.dataset.rowIndex) + 1);

        // Add event listeners to existing rows
        const quantityInput = row.querySelector('.quantity-input');
        const discountTypeSelect = row.querySelector('.discount-type-select');
        const discountTypeHidden = row.querySelector('.discount-type-input');
        const dynamicLabel = row.querySelector('.dynamic-label');
        const discountInput = row.querySelector('.discount-input');
        const removeBtn = row.querySelector('.remove-row');

        quantityInput.addEventListener('input', () => calculateRow(row));
        discountTypeSelect.addEventListener('change', function() {
            const isPercent = this.value === 'percent';
            dynamicLabel.textContent = isPercent ? 'Discount %' : 'Discount <?php echo e($globalSettings['currency_symbol']); ?>';
            discountInput.max = isPercent ? 100 : '';
            discountInput.placeholder = isPercent ? '0' : '0.00';
            discountTypeHidden.value = this.value;
            calculateRow(row);
        });
        discountInput.addEventListener('input', () => calculateRow(row));
        removeBtn.addEventListener('click', () => {
            selectedItemIds.delete(`${itemType}-${itemId}`);
            rows = rows.filter(r => r !== row);
            row.remove();
            if (rows.length === 0) {
                itemsError.classList.remove('d-none');
            }
        });
    });

    // Hide items error if there are existing rows
    if (existingRows.length > 0) {
        itemsError.classList.add('d-none');
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\memberships\edit.blade.php ENDPATH**/ ?>