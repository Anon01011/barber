<?php $__env->startSection('title', 'Edit Sale'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .edit-sale-card {
            border-radius: 15px;
            overflow: hidden;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: #0d6efd;
        }

        .form-label {
            color: #64748b;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            padding: 0.75rem 1rem;
            border-color: #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }

        .input-group-text {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            border-radius: 10px 0 0 10px;
            color: #64748b;
            font-weight: 600;
        }

        .summary-card {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .summary-row.total {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px dashed #e2e8f0;
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
        }

        .item-row:hover {
            background-color: #f8fafc;
        }

        /* Unpaid Sticker */
        .unpaid-sticker {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            border: 8px solid #ef4444;
            color: #ef4444;
            font-size: 4rem;
            font-weight: 900;
            padding: 1rem 2rem;
            text-transform: uppercase;
            opacity: 0.15;
            pointer-events: none;
            z-index: 10;
            border-radius: 15px;
            letter-spacing: 5px;
            white-space: nowrap;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4 py-4">
        <form action="<?php echo e(route('admin.pos.sales.update', $sale->id)); ?>" method="POST" id="edit-sale-form">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Main Edit Card -->
                    <div class="card edit-sale-card border-0 shadow-sm mb-4 position-relative">
                        <?php if($sale->payment_status !== 'paid'): ?>
                            <div class="unpaid-sticker">Payment Due</div>
                        <?php endif; ?>

                        <div class="card-header bg-white border-bottom-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">Edit Sale Details</h5>
                                <div class="badge bg-primary-soft text-primary px-3 py-2">
                                    <i class="fas fa-receipt me-1"></i> #<?php echo e($sale->invoice_number); ?>

                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <!-- Basic Information -->
                            <div class="section-title">
                                <i class="fas fa-info-circle"></i> Basic Information
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="invoice_number" class="form-label">Invoice Number</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['invoice_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="invoice_number" name="invoice_number"
                                        value="<?php echo e(old('invoice_number', $sale->invoice_number)); ?>" readonly required>
                                    <?php $__errorArgs = ['invoice_number'];
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
                                <div class="col-md-6 mb-3">
                                    <label for="sale_date" class="form-label">Sale Date & Time</label>
                                    <input type="datetime-local"
                                        class="form-control <?php $__errorArgs = ['sale_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="sale_date"
                                        name="sale_date"
                                        value="<?php echo e(old('sale_date', $sale->sale_date ? $sale->sale_date->format('Y-m-d\TH:i') : ($sale->created_at ? $sale->created_at->format('Y-m-d\TH:i') : ''))); ?>"
                                        required>
                                    <?php $__errorArgs = ['sale_date'];
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

                            <!-- Customer & Employee -->
                            <div class="section-title">
                                <i class="fas fa-user-friends"></i> Customer & Staff
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_id" class="form-label">Customer</label>
                                    <select class="form-select <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="customer_id"
                                        name="customer_id">
                                        <option value="">Walk-in Customer</option>
                                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($customer->id); ?>" <?php echo e(old('customer_id', $sale->customer_id) == $customer->id ? 'selected' : ''); ?>>
                                                <?php echo e($customer->name); ?> (<?php echo e($customer->phone); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['customer_id'];
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
                                <div class="col-md-6 mb-3">
                                    <label for="employee_id" class="form-label">Processed By (Cashier)</label>
                                    <select class="form-select <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="employee_id"
                                        name="employee_id" required>
                                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($employee->id); ?>" <?php echo e(old('employee_id', $sale->employee_id) == $employee->id ? 'selected' : ''); ?>>
                                                <?php echo e($employee->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['employee_id'];
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

                            <!-- Payment Information -->
                            <div class="section-title">
                                <i class="fas fa-credit-card"></i> Payment Status
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="payment_method" class="form-label">Primary Payment Method</label>
                                    <select class="form-select <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="payment_method" name="payment_method" required>
                                        <option value="cash" <?php echo e(old('payment_method', $sale->payment_method) == 'cash' ? 'selected' : ''); ?>>Cash</option>
                                        <option value="card" <?php echo e(old('payment_method', $sale->payment_method) == 'card' ? 'selected' : ''); ?>>Card</option>
                                        <option value="online" <?php echo e(old('payment_method', $sale->payment_method) == 'online' ? 'selected' : ''); ?>>Online</option>
                                        <option value="other" <?php echo e(old('payment_method', $sale->payment_method) == 'other' ? 'selected' : ''); ?>>Other</option>
                                        <option value="mixed" <?php echo e(old('payment_method', $sale->payment_method) == 'mixed' ? 'selected' : ''); ?>>Mixed</option>
                                        <option value="none" <?php echo e(old('payment_method', $sale->payment_method) == 'none' ? 'selected' : ''); ?>

                                            <?php if($hasFutureBooking): ?> disabled title="Not allowed for future appointments" <?php endif; ?>>
                                            Pay Later <?php if($hasFutureBooking): ?> (Not allowed for future appointments) <?php endif; ?>
                                        </option>
                                    </select>
                                    <?php $__errorArgs = ['payment_method'];
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
                                <div class="col-md-6 mb-3">
                                    <label for="payment_status" class="form-label">Payment Status</label>
                                    <select class="form-select <?php $__errorArgs = ['payment_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="payment_status" name="payment_status" required>
                                        <option value="paid" <?php echo e(old('payment_status', $sale->payment_status) == 'paid' ? 'selected' : ''); ?>>Paid</option>
                                        <option value="pending" <?php echo e(old('payment_status', $sale->payment_status) == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                        <option value="partial" <?php echo e(old('payment_status', $sale->payment_status) == 'partial' ? 'selected' : ''); ?>>Partial</option>
                                        <option value="refunded" <?php echo e(old('payment_status', $sale->payment_status) == 'refunded' ? 'selected' : ''); ?>>Refunded</option>
                                    </select>
                                    <?php $__errorArgs = ['payment_status'];
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

                            <div class="section-title">
                                <i class="fas fa-money-bill-wave"></i> Payment Amounts
                            </div>
                            <div class="row mb-4" id="payment-amounts-row">
                                <div class="col-md-3 mb-3 payment-col" data-method="cash">
                                    <label for="cash_amount" class="form-label">Cash</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                        <input type="number" step="0.01" class="form-control payment-input" id="cash_amount"
                                            name="cash_amount" value="<?php echo e(old('cash_amount', $sale->cash_amount)); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 payment-col" data-method="card">
                                    <label for="card_amount" class="form-label">Card</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                        <input type="number" step="0.01" class="form-control payment-input" id="card_amount"
                                            name="card_amount" value="<?php echo e(old('card_amount', $sale->card_amount)); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 payment-col" data-method="online">
                                    <label for="online_amount" class="form-label">Online</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                        <input type="number" step="0.01" class="form-control payment-input"
                                            id="online_amount" name="online_amount"
                                            value="<?php echo e(old('online_amount', $sale->online_amount)); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 payment-col" data-method="other">
                                    <label for="other_amount" class="form-label">Other</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-wallet"></i></span>
                                        <input type="number" step="0.01" class="form-control payment-input"
                                            id="other_amount" name="other_amount"
                                            value="<?php echo e(old('other_amount', $sale->other_amount)); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="tendered_amount" class="form-label">Tendered (Cash Given)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-hand-holding-usd"></i></span>
                                        <input type="number" step="0.01"
                                            class="form-control <?php $__errorArgs = ['tendered_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="tendered_amount" name="tendered_amount"
                                            value="<?php echo e(old('tendered_amount', $sale->tendered_amount)); ?>">
                                    </div>
                                    <?php $__errorArgs = ['tendered_amount'];
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
                                <div class="col-md-6 mb-3">
                                    <label for="change_amount" class="form-label">Change Return</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-coins"></i></span>
                                        <input type="number" step="0.01"
                                            class="form-control <?php $__errorArgs = ['change_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="change_amount" name="change_amount"
                                            value="<?php echo e(old('change_amount', $sale->change_amount)); ?>">
                                    </div>
                                    <?php $__errorArgs = ['change_amount'];
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

                            <div class="mb-0">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="notes" name="notes"
                                    rows="3"
                                    placeholder="Add any internal notes here..."><?php echo e(old('notes', $sale->notes)); ?></textarea>
                                <?php $__errorArgs = ['notes'];
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

                    <!-- Items Card -->
                    <div class="card edit-sale-card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom-0 py-3">
                            <h5 class="mb-0 fw-bold">Sale Items</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 border-0">ITEM</th>
                                            <th class="border-0">PRICE</th>
                                            <th class="border-0">QTY</th>
                                            <th class="text-end pe-4 border-0">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="item-row">
                                                <td class="ps-4 py-3">
                                                    <div class="fw-bold text-dark"><?php echo e($item->item_name); ?></div>
                                                    <?php if($item->staff): ?>
                                                        <small class="text-muted"><i
                                                                class="fas fa-user-tag me-1"></i><?php echo e($item->staff->name); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="py-3"><?php echo e(format_currency($item->unit_price)); ?></td>
                                                <td class="py-3"><?php echo e($item->quantity); ?></td>
                                                <td class="text-end pe-4 py-3 fw-bold text-dark">
                                                    <?php echo e(format_currency($item->total)); ?>

                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Summary & Actions -->
                    <div class="card edit-sale-card border-0 shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-header bg-white border-bottom-0 py-3">
                            <h5 class="mb-0 fw-bold">Financial Summary</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label for="discount" class="form-label">Discount Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    <input type="number" step="0.01"
                                        class="form-control <?php $__errorArgs = ['discount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="discount"
                                        name="discount" value="<?php echo e(old('discount', $sale->discount)); ?>">
                                </div>
                                <?php $__errorArgs = ['discount'];
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

                            <div class="mb-4">
                                <label for="tip" class="form-label">Tip / Gratuity</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-heart"></i></span>
                                    <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['tip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="tip" name="tip" value="<?php echo e(old('tip', $sale->tip)); ?>">
                                </div>
                                <?php $__errorArgs = ['tip'];
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

                            <div class="summary-card">
                                <div class="summary-row">
                                    <span>Subtotal</span>
                                    <span class="fw-bold"><?php echo e(format_currency($sale->subtotal)); ?></span>
                                </div>
                                <div class="summary-row text-danger">
                                    <span>Discount</span>
                                    <span class="fw-bold"
                                        id="summary-discount">-<?php echo e(format_currency($sale->discount)); ?></span>
                                </div>
                                <div class="summary-row">
                                    <span>Tax</span>
                                    <span class="fw-bold"><?php echo e(format_currency($sale->tax)); ?></span>
                                </div>
                                <div class="summary-row text-success">
                                    <span>Tip</span>
                                    <span class="fw-bold" id="summary-tip"><?php echo e(format_currency($sale->tip)); ?></span>
                                </div>
                                <div class="summary-row total">
                                    <span>Total</span>
                                    <span id="summary-total"><?php echo e(format_currency($sale->total)); ?></span>
                                </div>
                                <div class="summary-row mt-2 pt-2 border-top">
                                    <span>Paid</span>
                                    <span class="fw-bold text-success"
                                        id="summary-paid"><?php echo e(format_currency($sale->total - $sale->outstanding_amount)); ?></span>
                                </div>
                                <div class="summary-row">
                                    <span>Outstanding</span>
                                    <span class="fw-bold text-danger"
                                        id="summary-outstanding"><?php echo e(format_currency($sale->outstanding_amount)); ?></span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold mb-2 shadow-sm">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                                <a href="<?php echo e(route('admin.pos.sales.show', $sale->id)); ?>"
                                    class="btn btn-outline-secondary w-100 py-2 fw-bold">
                                    <i class="fas fa-times me-2"></i> Discard Changes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Payment Confirmation Modal (Partial) -->
    <div class="modal fade" id="paymentConfirmationModal" tabindex="-1" aria-labelledby="paymentConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold" id="paymentConfirmationModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i> Outstanding Balance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">The total payment amount entered is less than the sale total.</p>
                    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
                        <span>Total Amount:</span>
                        <span class="fw-bold" id="modal-total-amount"></span>
                    </div>
                    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
                        <span>Paid Amount:</span>
                        <span class="fw-bold text-success" id="modal-paid-amount"></span>
                    </div>
                    <div class="alert alert-danger d-flex justify-content-between align-items-center mb-0">
                        <span>Outstanding:</span>
                        <span class="fw-bold" id="modal-outstanding-amount"></span>
                    </div>
                    <p class="mt-3 mb-0 text-muted small">
                        You cannot save this as "Paid". Would you like to save it as a <strong>Partial Payment</strong>
                        instead?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmPartialPayment">
                        <i class="fas fa-check me-2"></i> Yes, Save as Partial
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tip Confirmation Modal (Overpayment) -->
    <div class="modal fade" id="tipConfirmationModal" tabindex="-1" aria-labelledby="tipConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title fw-bold" id="tipConfirmationModalLabel">
                        <i class="fas fa-coins me-2"></i> Excess Payment Detected
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">The total payment amount entered is greater than the sale total.</p>
                    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
                        <span>Payable Total:</span>
                        <span class="fw-bold" id="tip-modal-total-amount"></span>
                    </div>
                    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
                        <span>Entered Amount:</span>
                        <span class="fw-bold text-success" id="tip-modal-paid-amount"></span>
                    </div>
                    <div class="alert alert-info d-flex justify-content-between align-items-center mb-0">
                        <span>Excess Amount:</span>
                        <span class="fw-bold" id="tip-modal-excess-amount"></span>
                    </div>
                    <p class="mt-3 mb-0 text-muted small">
                        Would you like to add this excess amount as a <strong>Tip / Gratuity</strong>?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Edit Amount</button>
                    <button type="button" class="btn btn-primary" id="confirmExtraAsTip">
                        <i class="fas fa-magic me-2"></i> Yes, Add as Tip
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function () {
            const subtotal = <?php echo e($sale->subtotal); ?>;
            const tax = <?php echo e($sale->tax); ?>;
            <?php
                $currencyStr = format_currency(0);
                // Remove numbers, dots, commas, and spaces to get just the symbol
                $symbol = preg_replace('/[\d.,\s]+/', '', $currencyStr);
            ?>
            const currencySymbol = "<?php echo e($symbol); ?>";

            function formatNumber(num) {
                return num.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }

            function updateSummary() {
                const discount = parseFloat($('#discount').val()) || 0;
                const tip = parseFloat($('#tip').val()) || 0;
                const total = subtotal + tax - discount + tip;

                const cash = parseFloat($('#cash_amount').val()) || 0;
                const card = parseFloat($('#card_amount').val()) || 0;
                const online = parseFloat($('#online_amount').val()) || 0;
                const other = parseFloat($('#other_amount').val()) || 0;
                const paid = cash + card + online + other;
                const outstanding = Math.max(0, total - paid);

                $('#summary-discount').text('-' + currencySymbol + ' ' + formatNumber(discount));
                $('#summary-tip').text(currencySymbol + ' ' + formatNumber(tip));
                $('#summary-total').text(currencySymbol + ' ' + formatNumber(total));
                $('#summary-paid').text(currencySymbol + ' ' + formatNumber(paid));
                $('#summary-outstanding').text(currencySymbol + ' ' + formatNumber(outstanding));

                updateStatusAutomatically(paid, total);
            }

            $('#discount, #tip, .payment-input').on('input', updateSummary);

            $('#tendered_amount').on('input', function () {
                const tendered = parseFloat($(this).val()) || 0;
                const subtotal = <?php echo e($sale->subtotal); ?>;
                const tax = <?php echo e($sale->tax); ?>;
                const discount = parseFloat($('#discount').val()) || 0;
                const tip = parseFloat($('#tip').val()) || 0;
                const total = subtotal + tax - discount + tip;

                const method = $('#payment_method').val();
                if (method === 'cash') {
                    $('#cash_amount').val(total.toFixed(2));
                }

                const cashAmount = parseFloat($('#cash_amount').val()) || 0;
                const change = Math.max(0, tendered - cashAmount);
                $('#change_amount').val(change.toFixed(2));
                updateSummary();
            });

            function togglePaymentFields() {
                const method = $('#payment_method').val();
                if (method === 'mixed') {
                    $('.payment-col').show();
                    $('#payment-amounts-row').show();
                } else if (method === 'none') {
                    $('#payment-amounts-row').hide();
                } else {
                    $('#payment-amounts-row').show();
                    $('.payment-col').hide();
                    $(`.payment-col[data-method="${method}"]`).show();
                }
            }

            $('#payment_method').on('change', function () {
                const method = $(this).val();
                const subtotal = <?php echo e($sale->subtotal); ?>;
                const tax = <?php echo e($sale->tax); ?>;
                const discount = parseFloat($('#discount').val()) || 0;
                const tip = parseFloat($('#tip').val()) || 0;
                const total = subtotal + tax - discount + tip;

                if (method === 'none') {
                    $('#payment_status').val('pending');
                    $('.payment-input').val(0);
                } else if (method !== 'mixed') {
                    // Clear other fields if not mixed
                    $('.payment-input').each(function () {
                        if ($(this).attr('id') !== method + '_amount') {
                            $(this).val(0);
                        } else {
                            $(this).val(total.toFixed(2));
                        }
                    });

                    if (method === 'cash') {
                        const tendered = parseFloat($('#tendered_amount').val()) || 0;
                        if (tendered > 0) {
                            const change = Math.max(0, tendered - total);
                            $('#change_amount').val(change.toFixed(2));
                        }
                    }
                }
                togglePaymentFields();
                updateSummary();
            });

            $('#payment_status').on('change', function () {
                if ($(this).val() === 'pending') {
                    $('.payment-input').val(0);
                    updateSummary();
                }
            });

            function updateStatusAutomatically(paid, total) {
                if (paid >= total && total > 0) {
                    $('#payment_status').val('paid');
                } else if (paid > 0) {
                    $('#payment_status').val('partial');
                } else {
                    // Don't force pending if it was already something else like refunded
                    const currentStatus = $('#payment_status').val();
                    if (currentStatus !== 'refunded') {
                        $('#payment_status').val('pending');
                    }
                }
            }

            // Check if we have multiple payment methods used and set dropdown to mixed if needed
            const cash = parseFloat($('#cash_amount').val()) || 0;
            const card = parseFloat($('#card_amount').val()) || 0;
            const online = parseFloat($('#online_amount').val()) || 0;
            const other = parseFloat($('#other_amount').val()) || 0;

            let methodsCount = 0;
            if (cash > 0) methodsCount++;
            if (card > 0) methodsCount++;
            if (online > 0) methodsCount++;
            if (other > 0) methodsCount++;

            if (methodsCount > 1) {
                $('#payment_method').val('mixed');
            }


            // Form validation
            $('#edit-sale-form').on('submit', function (e) {
                const status = $('#payment_status').val();

                const subtotal = <?php echo e($sale->subtotal); ?>;
                const tax = <?php echo e($sale->tax); ?>;
                const discount = parseFloat($('#discount').val()) || 0;
                const currentTip = parseFloat($('#tip').val()) || 0;
                const total = subtotal + tax - discount + currentTip;

                const cash = parseFloat($('#cash_amount').val()) || 0;
                const card = parseFloat($('#card_amount').val()) || 0;
                const online = parseFloat($('#online_amount').val()) || 0;
                const other = parseFloat($('#other_amount').val()) || 0;
                const paid = parseFloat((cash + card + online + other).toFixed(2));



                // 1. Check for OVERPAYMENT (Convert excess to tip)
                if (paid > (total + 0.01)) {

                    e.preventDefault();

                    const excess = parseFloat((paid - total).toFixed(2));
                    const sym = typeof currencySymbol !== 'undefined' ? currencySymbol : '$';

                    $('#tip-modal-total-amount').text(sym + ' ' + formatNumber(total));
                    $('#tip-modal-paid-amount').text(sym + ' ' + formatNumber(paid));
                    $('#tip-modal-excess-amount').text(sym + ' ' + formatNumber(excess));

                    try {
                        const tipModalElement = document.getElementById('tipConfirmationModal');
                        if (tipModalElement) {
                            const tipModal = new bootstrap.Modal(tipModalElement);
                            tipModal.show();

                            $('#confirmExtraAsTip').off('click').on('click', function () {
                                const newTip = currentTip + excess;
                                $('#tip').val(newTip.toFixed(2));
                                tipModal.hide();
                                updateSummary();
                                // Re-trigger submit (it will pass the checks this time as paid == total)
                                $('#edit-sale-form').off('submit').submit();
                            });
                        } else {

                            alert('Excess payment of ' + sym + ' ' + excess + ' detected. Please check your amounts.');
                        }
                    } catch (err) {

                        alert('Excess payment of ' + sym + ' ' + excess + ' detected. Please check your amounts.');
                    }

                    return false;
                }

                // 2. Check for UNDERPAYMENT if status is 'paid'
                if (status === 'paid') {
                    // Allow small epsilon for float comparison
                    if (paid < (total - 0.01)) {
                        e.preventDefault();

                        // Show confirmation modal
                        const outstanding = total - paid;
                        $('#modal-total-amount').text(currencySymbol + ' ' + formatNumber(total));
                        $('#modal-paid-amount').text(currencySymbol + ' ' + formatNumber(paid));
                        $('#modal-outstanding-amount').text(currencySymbol + ' ' + formatNumber(outstanding));

                        const partialModal = new bootstrap.Modal(document.getElementById('paymentConfirmationModal'));
                        partialModal.show();

                        // Handle confirmation
                        $('#confirmPartialPayment').off('click').on('click', function () {
                            $('#payment_status').val('partial');
                            partialModal.hide();
                            $('#edit-sale-form').off('submit').submit();
                        });

                        return false;
                    }
                }
            });

            // Initial update
            togglePaymentFields();
            updateSummary();
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\pos\sales\edit.blade.php ENDPATH**/ ?>