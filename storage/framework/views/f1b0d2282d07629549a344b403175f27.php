
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
                <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center">
                                <?php if($item->image_path): ?>
                                    <img src="<?php echo e(asset('storage/' . $item->image_path)); ?>" alt="<?php echo e($item->name); ?>" 
                                        class="rounded-3 me-3 object-fit-cover shadow-sm" style="width: 42px; height: 42px;">
                                <?php else: ?>
                                    <div class="rounded-3 p-2 bg-light text-muted me-3 d-flex align-items-center justify-content-center shadow-sm" 
                                        style="width: 42px; height: 42px;">
                                        <i class="fas fa-<?php echo e($item->category->icon ?? 'box'); ?> fa-sm"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-semibold text-dark mb-0.5"><?php echo e($item->name); ?></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <code class="text-muted small" style="font-size: 0.78rem;"><?php echo e($item->sku); ?></code>
                                        <?php if($item->barcode): ?>
                                            <span class="text-muted small" style="font-size: 0.75rem;">
                                                <i class="fas fa-barcode me-0.5"></i><?php echo e($item->barcode); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        
                        <td class="py-3">
                            <?php if($item->product_type === 'consumable'): ?>
                                <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1 small fw-semibold">
                                    <i class="fas fa-flask me-1 fa-xs"></i>Backbar
                                </span>
                            <?php elseif($item->product_type === 'dual'): ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1 small fw-semibold">
                                    <i class="fas fa-arrows-split-up-and-left me-1 fa-xs"></i>Dual-Use
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 small fw-semibold">
                                    <i class="fas fa-tag me-1 fa-xs"></i>Retail
                                </span>
                            <?php endif; ?>
                        </td>

                        
                        <td class="py-3">
                            <span class="fw-medium text-dark small"><?php echo e($item->category->name ?? 'Uncategorized'); ?></span>
                            <?php if($branches->count() > 1 && $item->branches->count() > 0): ?>
                                <div class="mt-1 d-flex flex-wrap gap-1">
                                    <?php $__currentLoopData = $item->branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-light text-muted border rounded-pill px-2" style="font-size: 0.7rem;">
                                            <?php echo e($branch->name); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        
                        <td class="py-3">
                            <?php
                                $stock = (float) $item->quantity_in_stock;
                                $minStock = (float) $item->minimum_quantity;
                                $reorderLevel = (float) ($item->reorder_level ?? 0);
                                $unit = $item->unit_type ?? 'pcs';

                                $isOutOfStock = $stock <= 0;
                                $isLowStock = !$isOutOfStock && (($reorderLevel > 0 && $stock <= $reorderLevel) || ($minStock > 0 && $stock <= $minStock));
                                
                                $badgeClass = $isOutOfStock ? 'bg-danger-subtle text-danger' : ($isLowStock ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success');
                                $barClass = $isOutOfStock ? 'bg-danger' : ($isLowStock ? 'bg-warning' : 'bg-success');
                                $percent = $minStock > 0 ? min(100, ($stock / $minStock) * 100) : ($stock > 0 ? 100 : 0);
                            ?>
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="fw-bold small <?php echo e($isOutOfStock ? 'text-danger' : 'text-dark'); ?>">
                                    <?php echo e(rtrim(rtrim(number_format($stock, 2), '0'), '.')); ?> <?php echo e($unit); ?>

                                </span>
                                <span class="badge <?php echo e($badgeClass); ?> rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                    <?php echo e($isOutOfStock ? 'Out of Stock' : ($isLowStock ? 'Low Stock' : 'In Stock')); ?>

                                </span>
                            </div>
                            <div class="progress" style="height: 5px; border-radius: 4px;">
                                <div class="progress-bar <?php echo e($barClass); ?>" role="progressbar" style="width: <?php echo e($percent); ?>%;"></div>
                            </div>
                        </td>

                        
                        <td class="py-3 text-end">
                            <div class="fw-semibold text-dark small">
                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($item->purchase_price, 2)); ?>

                            </div>
                            <?php if($item->pack_capacity > 1): ?>
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($item->cost_per_unit, 3)); ?>/<?php echo e($item->base_unit ?? $item->unit_type); ?>

                                </small>
                            <?php endif; ?>
                        </td>

                        
                        <td class="py-3 text-end">
                            <?php if($item->product_type === 'consumable'): ?>
                                <span class="text-muted small fst-italic">In-Service</span>
                            <?php else: ?>
                                <span class="fw-bold text-dark small">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($item->selling_price, 2)); ?>

                                </span>
                            <?php endif; ?>
                        </td>

                        
                        <td class="pe-4 py-3 text-end text-nowrap">
                            <div class="d-inline-flex align-items-center justify-content-end gap-1.5">
                                
                                <button type="button" class="btn btn-sm btn-action-icon text-primary"
                                    onclick="editItem(<?php echo e($item->id); ?>, this)"
                                    data-item="<?php echo e(json_encode($item)); ?>"
                                    title="Edit Product"
                                    aria-label="Edit Product">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>

                                
                                <button type="button" class="btn btn-sm btn-action-icon text-emerald adjust-stock-btn"
                                    data-item-id="<?php echo e($item->id); ?>"
                                    data-item-name="<?php echo e($item->name); ?>"
                                    data-stock="<?php echo e($stock); ?>"
                                    data-unit="<?php echo e($unit); ?>"
                                    data-unit-cost="<?php echo e($item->purchase_price); ?>"
                                    title="Adjust Stock"
                                    aria-label="Adjust Stock">
                                    <i class="fas fa-boxes"></i>
                                </button>

                                
                                <div class="dropdown d-inline-block">
                                    <button type="button" class="btn btn-sm btn-action-icon text-secondary"
                                        data-bs-toggle="dropdown" aria-expanded="false" title="More Options" aria-label="More Options">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-light-subtle rounded-3 py-1.5" style="font-size: 0.85rem; min-width: 170px; z-index: 1050;">
                                        <li>
                                            <button class="dropdown-item py-1.5 d-flex align-items-center" type="button" 
                                                onclick="editItem(<?php echo e($item->id); ?>, this)"
                                                data-item="<?php echo e(json_encode($item)); ?>">
                                                <i class="fas fa-pencil-alt text-primary me-2.5 fa-fw"></i>
                                                <span>Edit Product</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item py-1.5 d-flex align-items-center adjust-stock-btn" type="button" 
                                                data-item-id="<?php echo e($item->id); ?>"
                                                data-item-name="<?php echo e($item->name); ?>"
                                                data-stock="<?php echo e($stock); ?>"
                                                data-unit="<?php echo e($unit); ?>"
                                                data-unit-cost="<?php echo e($item->purchase_price); ?>">
                                                <i class="fas fa-boxes text-emerald me-2.5 fa-fw"></i>
                                                <span>Adjust Stock</span>
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form action="<?php echo e(route('admin.inventory.destroy', $item->id)); ?>" method="POST" 
                                                onsubmit="return confirm('Are you sure you want to delete <?php echo e(addslashes($item->name)); ?>?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
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
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
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
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if($items->hasPages()): ?>
        <div class="card-footer bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
            <small class="text-muted">
                Showing <?php echo e($items->firstItem() ?? 0); ?> to <?php echo e($items->lastItem() ?? 0); ?> of <?php echo e($items->total()); ?> items
            </small>
            <div>
                <?php echo e($items->links()); ?>

            </div>
        </div>
    <?php endif; ?>
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
<?php /**PATH C:\Ketan\projects\barber\resources\views/inventory/partials/table.blade.php ENDPATH**/ ?>