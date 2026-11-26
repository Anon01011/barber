<?php
    $salonId = $sale->salon_id;
    $defaultPrinterWidth = $settings->get('pos_receipt_printer_width', '80mm', $salonId);
    $printerWidth = request()->query('width', $defaultPrinterWidth);
    if (!in_array($printerWidth, ['80mm', '58mm', 'a4'])) {
        $printerWidth = '80mm';
    }
    $arabicEnabled = request()->has('arabic') 
        ? (bool)request()->query('arabic') 
        : $settings->get('pos_receipt_arabic', true, $salonId);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?php echo e($sale->invoice_number); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #000000;
            line-height: 1.35;
            background: #f1f5f9;
            padding: 24px 10px;
            margin: 0;
            font-size: 13px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .receipt-wrapper {
            width: 100%;
            <?php if($printerWidth === '58mm'): ?>
                max-width: 250px;
                font-size: 11.5px;
                padding: 14px 10px;
            <?php elseif($printerWidth === 'a4'): ?>
                max-width: 680px;
                font-size: 14px;
                padding: 30px;
            <?php else: ?>
                max-width: 340px;
                font-size: 12.5px;
                padding: 18px 14px;
            <?php endif; ?>
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            position: relative;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .bold {
            font-weight: 700;
        }

        .extra-bold {
            font-weight: 800;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .mb-5 {
            margin-bottom: 5px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mb-15 {
            margin-bottom: 15px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .logo {
            max-height: 52px;
            width: auto;
            margin-bottom: 8px;
            object-fit: contain;
        }

        .dashed-line {
            border-bottom: 1px dashed #333333;
            margin: 12px 0;
        }

        .solid-line {
            border-bottom: 2px solid #000000;
            margin: 8px 0;
        }

        .flex-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            align-items: flex-start;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: inherit;
        }

        th {
            text-align: left;
            padding-bottom: 6px;
            border-bottom: 1px solid #000;
            font-weight: 700;
        }

        td {
            padding: 6px 0;
            vertical-align: top;
        }

        .dual-display {
            display: flex;
            flex-direction: column;
            line-height: 1.25;
        }

        .dual-display.text-center {
            align-items: center;
            text-align: center;
        }

        .dual-display.text-left {
            align-items: flex-start;
            text-align: left;
        }

        .dual-display.text-right {
            align-items: flex-end;
            text-align: right;
        }

        .dual-display .ar,
        .ar-text {
            font-size: 0.88em;
            color: #475569;
            margin-top: 1px;
            font-family: 'Tajawal', 'Inter', Tahoma, sans-serif;
            direction: rtl !important;
            unicode-bidi: isolate;
            text-align: inherit;
        }

        .dual-display .ar[dir="ltr"] {
            direction: ltr !important;
            unicode-bidi: embed;
        }

        .no-print {
            margin-bottom: 18px;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 8px 18px;
            background: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 50px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .unpaid-sticker {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            border: 4px solid #ef4444;
            color: #ef4444;
            font-size: 2.2rem;
            font-weight: 900;
            padding: 0.4rem 0.8rem;
            text-transform: uppercase;
            opacity: 0.2;
            pointer-events: none;
            z-index: 1000;
            border-radius: 8px;
            letter-spacing: 2px;
            white-space: nowrap;
        }

        @media print {
            .unpaid-sticker {
                opacity: 0.15;
                border-color: #000;
                color: #000;
            }

            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
                width: 100% !important;
            }

            .receipt-wrapper {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 auto !important;
                <?php if($printerWidth === '58mm'): ?>
                    width: 58mm !important;
                    max-width: 54mm !important;
                    padding: 2mm 1mm !important;
                    font-size: 10.5px !important;
                <?php elseif($printerWidth === '80mm'): ?>
                    width: 80mm !important;
                    max-width: 76mm !important;
                    padding: 2mm 2mm !important;
                    font-size: 11.5px !important;
                <?php else: ?>
                    width: 100% !important;
                    max-width: 100% !important;
                    padding: 8mm !important;
                    font-size: 13px !important;
                <?php endif; ?>
            }

            .no-print {
                display: none !important;
            }

            @page {
                margin: 0;
                <?php if($printerWidth === '58mm'): ?>
                    size: 58mm auto;
                <?php elseif($printerWidth === '80mm'): ?>
                    size: 80mm auto;
                <?php else: ?>
                    size: auto;
                <?php endif; ?>
            }
        }
    </style>
    <?php if(isset($isPdf) && $isPdf): ?>
        <style>
            body {
                font-family: 'DejaVu Sans', sans-serif !important;
            }

            .ar {
                font-family: 'DejaVu Sans', sans-serif !important;
            }
        </style>
    <?php endif; ?>
</head>

<body>
    <!-- Top Action & Paper Format Switcher Toolbar (Screen Only) -->
    <div class="no-print" style="max-width: 680px; width: 100%; display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; align-items: center;">
        <button onclick="window.print()" class="btn" style="background: #0f172a; color: #ffffff; padding: 7px 18px; font-weight: 700; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
            <i class="fas fa-print"></i> 🖨️ Print Receipt
        </button>

        <!-- Thermal Paper Width Switcher -->
        <div style="display: inline-flex; background: #e2e8f0; border-radius: 50px; padding: 3px; gap: 2px;">
            <a href="?width=80mm<?php echo e(request()->has('arabic') ? '&arabic='.request('arabic') : ''); ?>" 
               style="padding: 5px 12px; border-radius: 50px; text-decoration: none; font-size: 11.5px; font-weight: 600; color: <?php echo e($printerWidth === '80mm' ? '#ffffff' : '#475569'); ?>; background: <?php echo e($printerWidth === '80mm' ? '#0f172a' : 'transparent'); ?>;">
                80mm Thermal
            </a>
            <a href="?width=58mm<?php echo e(request()->has('arabic') ? '&arabic='.request('arabic') : ''); ?>" 
               style="padding: 5px 12px; border-radius: 50px; text-decoration: none; font-size: 11.5px; font-weight: 600; color: <?php echo e($printerWidth === '58mm' ? '#ffffff' : '#475569'); ?>; background: <?php echo e($printerWidth === '58mm' ? '#0f172a' : 'transparent'); ?>;">
                58mm Mini
            </a>
            <a href="?width=a4<?php echo e(request()->has('arabic') ? '&arabic='.request('arabic') : ''); ?>" 
               style="padding: 5px 12px; border-radius: 50px; text-decoration: none; font-size: 11.5px; font-weight: 600; color: <?php echo e($printerWidth === 'a4' ? '#ffffff' : '#475569'); ?>; background: <?php echo e($printerWidth === 'a4' ? '#0f172a' : 'transparent'); ?>;">
                A4 Sheet
            </a>
        </div>

        <!-- Bilingual Toggle -->
        <div style="display: inline-flex; background: #e2e8f0; border-radius: 50px; padding: 3px; gap: 2px;">
            <a href="?arabic=1<?php echo e(request()->has('width') ? '&width='.request('width') : ''); ?>" 
               style="padding: 5px 12px; border-radius: 50px; text-decoration: none; font-size: 11.5px; font-weight: 600; color: <?php echo e($arabicEnabled ? '#ffffff' : '#475569'); ?>; background: <?php echo e($arabicEnabled ? '#2563eb' : 'transparent'); ?>;">
                Bilingual (EN+AR)
            </a>
            <a href="?arabic=0<?php echo e(request()->has('width') ? '&width='.request('width') : ''); ?>" 
               style="padding: 5px 12px; border-radius: 50px; text-decoration: none; font-size: 11.5px; font-weight: 600; color: <?php echo e(!$arabicEnabled ? '#ffffff' : '#475569'); ?>; background: <?php echo e(!$arabicEnabled ? '#2563eb' : 'transparent'); ?>;">
                English Only
            </a>
        </div>

        <button onclick="window.close()" class="btn btn-secondary" style="padding: 7px 14px;">
            Close
        </button>
    </div>

    <div class="receipt-wrapper">
        <?php
            $totalPaid = ($sale->cash_amount ?? 0) + ($sale->card_amount ?? 0) + ($sale->online_amount ?? 0) + ($sale->other_amount ?? 0);
            $outstanding = max(0, $sale->total - $totalPaid);
        ?>
        <?php if($outstanding > 0): ?>
            <div class="unpaid-sticker" style="border-color: #f59e0b; color: #f59e0b; opacity: 0.2;">PARTIAL PAYMENT</div>
        <?php elseif($sale->payment_status !== 'paid' && $sale->payment_status !== 'refunded' && $sale->status !== 'voided'): ?>
            <div class="unpaid-sticker">UNPAID</div>
        <?php elseif($sale->payment_method === 'package'): ?>
            <div class="unpaid-sticker" style="border-color: #10b981; color: #10b981; opacity: 0.2;">PREPAID</div>
        <?php endif; ?>
        <div class="text-center mb-15">
            <?php if(!empty($salonData['logo'])): ?>
                <img src="<?php echo e($salonData['logo']); ?>" alt="<?php echo e($salonData['name']); ?>" class="logo">
            <?php endif; ?>
            <h2 class="extra-bold uppercase mb-5" style="font-size: 1.25rem; margin-top: 4px; letter-spacing: 0.5px;">
                <?php echo e($salonData['name'] ?? 'SALON'); ?>

            </h2>
            <?php if(!empty($salonData['address'])): ?>
                <p style="font-size: 0.85rem; margin: 0; color: #475569;"><?php echo e($salonData['address']); ?></p>
            <?php endif; ?>
            <?php if(!empty($salonData['phone'])): ?>
                <p style="font-size: 0.85rem; margin: 2px 0 0 0; color: #475569;">Tel: <?php echo e($salonData['phone']); ?></p>
            <?php endif; ?>
        </div>

        <div class="solid-line"></div>

        <?php
            if (!function_exists('toArabicNumerals')) {
                function toArabicNumerals($number)
                {
                    $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                    $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
                    return str_replace($western, $eastern, (string) $number);
                }
            }

            if (!function_exists('renderDual')) {
                function renderDual($eng, $ar, $arabicEnabled, $align = 'text-left', $isBold = false)
                {
                    $eng = e($eng);
                    $ar  = e($ar);
                    if (!$arabicEnabled)
                        return $eng;
                    $boldStyle = $isBold ? 'font-weight: 700;' : '';
                    $align = htmlspecialchars($align, ENT_QUOTES, 'UTF-8');
                    return "
                        <div class=\"dual-display {$align}\" style=\"{$boldStyle}\">
                            <div class=\"eng\">{$eng}</div>
                            <div class=\"ar\" dir=\"rtl\" lang=\"ar\" style=\"direction: rtl !important; unicode-bidi: isolate; text-align: inherit;\">{$ar}</div>
                        </div>
                    ";
                }
            }

            if (!function_exists('renderDualNumber')) {
                function renderDualNumber($number, $arabicEnabled, $align = 'text-left', $isBold = false)
                {
                    $number = e($number);
                    if (!$arabicEnabled)
                        return $number;
                    $arabic = toArabicNumerals($number);
                    $boldStyle = $isBold ? 'font-weight: 700;' : '';
                    $align = htmlspecialchars($align, ENT_QUOTES, 'UTF-8');
                    return "
                        <div class=\"dual-display {$align}\" style=\"{$boldStyle}\">
                            <div class=\"eng\">{$number}</div>
                            <div class=\"ar\" dir=\"ltr\" style=\"direction: ltr !important; unicode-bidi: embed; text-align: inherit;\">{$arabic}</div>
                        </div>
                    ";
                }
            }
        ?>

        <div class="mb-15" style="font-size: 0.9rem;">
            <div class="flex-row">
                <div>
                    <?php echo renderDual('Invoice No:', 'رقم الفاتورة:', $arabicEnabled, 'text-left', true); ?>

                </div>
                <?php echo renderDualNumber('#' . $sale->invoice_number, $arabicEnabled, 'text-right', true); ?>

            </div>
            <div class="flex-row">
                <div>
                    <?php echo renderDual('Date:', 'التاريخ:', $arabicEnabled, 'text-left', true); ?>

                </div>
                <?php echo renderDual($sale->created_at->format('M d, Y H:i'), toArabicNumerals($sale->created_at->format('Y-m-d H:i')), $arabicEnabled, 'text-right'); ?>

            </div>
            <div class="flex-row">
                <div>
                    <?php echo renderDual('Customer:', 'العميل:', $arabicEnabled, 'text-left', true); ?>

                </div>
                <span><?php echo e($sale->customer->name ?? 'Walk-in'); ?></span>
            </div>
            <div class="flex-row">
                <div>
                    <?php echo renderDual('Cashier:', 'الكاشير:', $arabicEnabled, 'text-left', true); ?>

                </div>
                <span><?php echo e($sale->employee->name ?? 'N/A'); ?></span>
            </div>
            <div class="flex-row">
                <div>
                    <?php echo renderDual('Payment:', 'طريقة الدفع:', $arabicEnabled, 'text-left', true); ?>

                </div>
                <span class="uppercase">
                    <?php if(($sale->cash_amount > 0 ? 1 : 0) + ($sale->card_amount > 0 ? 1 : 0) + ($sale->online_amount > 0 ? 1 : 0) + ($sale->other_amount > 0 ? 1 : 0) > 1): ?>
                        Split / Multiple
                    <?php else: ?>
                        <?php echo e($sale->payment_method ?? 'N/A'); ?>

                    <?php endif; ?>
                </span>
            </div>
            <?php if($sale->status === 'voided'): ?>
                <div class="text-center extra-bold" style="margin-top: 10px; font-size: 1.1rem;">*** VOIDED ***</div>
            <?php elseif($sale->payment_status !== 'paid' && $sale->payment_status !== 'refunded'): ?>
                <div class="text-center extra-bold" style="margin-top: 10px; font-size: 1.1rem;">*** UNPAID ***</div>
            <?php endif; ?>
        </div>

        <div class="dashed-line"></div>

        <table>
            <thead>
                <tr>
                    <th style="width: 55%;">
                        <?php echo renderDual('ITEM', 'البند', $arabicEnabled, 'text-left', true); ?>

                    </th>
                    <th class="text-center" style="width: 15%;">
                        <?php echo renderDual('QTY', 'الكمية', $arabicEnabled, 'text-center', true); ?>

                    </th>
                    <th class="text-right" style="width: 30%;">
                        <?php echo renderDual('AMT', 'المبلغ', $arabicEnabled, 'text-right', true); ?>

                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Group items by package_id
                    $groupedItems = $sale->items->groupBy('package_id');
                ?>

                <?php $__currentLoopData = $groupedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $packageId => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($packageId): ?>
                        
                        <?php
                            // Find the main package item (usually the one with price > 0 or type Package)
                            $mainItem = $items->first(function ($i) {
                                return $i->price > 0 || str_contains($i->item_type, 'Package');
                            });
                            // If no main item found (unlikely), use the first one
                            if (!$mainItem)
                                $mainItem = $items->first();

                            // Filter out the main item to get the sub-items (services)
                            $subItems = $items->filter(function ($i) use ($mainItem) {
                                return $i->id !== $mainItem->id;
                            });
                        ?>

                        <tr>
                            <td>
                                <?php echo renderDual($mainItem->item_name, optional($mainItem->item)->name_ar, $arabicEnabled, 'text-left', true); ?>

                                <div class="small text-muted">Package</div>
                                <?php if($mainItem->staff): ?>
                                    <div style="font-size: 0.75rem; color: #666;">Staff: <?php echo e($mainItem->staff->name); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php echo renderDualNumber((float) $mainItem->quantity, $arabicEnabled, 'text-center'); ?>

                            </td>
                            <td class="text-right">
                                <?php echo renderDualNumber(number_format($mainItem->total, 2), $arabicEnabled, 'text-right', true); ?>

                            </td>
                        </tr>
                        <?php $__currentLoopData = $subItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="padding-left: 15px;">
                                    <div class="small text-muted">
                                        <i class="fas fa-level-up-alt fa-rotate-90 mr-1"></i>
                                        <?php echo renderDual($subItem->item_name, optional($subItem->item)->name_ar, $arabicEnabled, 'text-left'); ?>

                                        <?php if($subItem->staff): ?>
                                            <div style="font-size: 0.7rem; color: #888;">Staff: <?php echo e($subItem->staff->name); ?></div>
                                        <?php endif; ?>
                                        <?php if($subItem->consumables && $subItem->consumables->isNotEmpty()): ?>
                                            <div style="font-size: 0.65rem; color: #888; margin-top: 1px;">
                                                <i class="fas fa-flask" style="font-size: 0.60rem;"></i> Consumables: 
                                                <?php echo e($subItem->consumables->map(fn($c) => ($c->inventoryItem->name ?? 'Item') . ' (' . (float)$c->quantity_used . ($c->unit ? ' '.$c->unit : '') . ')' . ((float)($c->billed_amount ?? 0) > 0 ? ' +'.format_currency($c->billed_amount) : ''))->implode(', ')); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center small text-muted">
                                    <?php echo renderDualNumber((float) $subItem->quantity, $arabicEnabled, 'text-center'); ?>

                                </td>
                                <td class="text-right small text-muted">-</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php echo renderDual($item->item_name, optional($item->item)->name_ar, $arabicEnabled, 'text-left', true); ?>

                                    <?php if($item->staff): ?>
                                        <div style="font-size: 0.75rem; color: #666;">Staff: <?php echo e($item->staff->name); ?></div>
                                    <?php endif; ?>
                                    <?php if($item->consumables && $item->consumables->isNotEmpty()): ?>
                                        <div style="font-size: 0.68rem; color: #666; margin-top: 2px;">
                                            <i class="fas fa-flask" style="font-size: 0.65rem;"></i> Consumables:
                                            <?php echo e($item->consumables->map(fn($c) => ($c->inventoryItem->name ?? 'Item') . ' (' . (float)$c->quantity_used . ($c->unit ? ' '.$c->unit : '') . ')' . ((float)($c->billed_amount ?? 0) > 0 ? ' +'.format_currency($c->billed_amount) : ''))->implode(', ')); ?>

                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo renderDualNumber((float) $item->quantity, $arabicEnabled, 'text-center'); ?>

                                </td>
                                <td class="text-right">
                                    <?php echo renderDualNumber(number_format($item->total, 2), $arabicEnabled, 'text-right', true); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div class="dashed-line"></div>

        <div class="mb-15">
            <div class="flex-row">
                <div>
                    <?php echo renderDual('Subtotal', 'المجموع الفرعي', $arabicEnabled, 'text-left', true); ?>

                </div>
                <?php echo renderDualNumber(number_format($sale->subtotal, 2), $arabicEnabled, 'text-right', true); ?>

            </div>
            <?php if($sale->tax > 0): ?>
                <div class="flex-row" style="font-size: 0.9rem;">
                    <span>Tax (<?php echo e($sale->tax_rate ?? 0); ?>%)</span>
                    <?php echo renderDualNumber(number_format($sale->tax, 2), $arabicEnabled, 'text-right'); ?>

                </div>
            <?php endif; ?>
            <?php if($sale->discount > 0): ?>
                <div class="flex-row" style="font-size: 0.9rem;">
                    <div>
                        <?php echo renderDual('Discount', 'الخصم', $arabicEnabled, 'text-left'); ?>

                    </div>
                    <?php echo renderDualNumber('-' . number_format($sale->discount, 2), $arabicEnabled, 'text-right'); ?>

                </div>
            <?php endif; ?>
            <?php if($sale->tip > 0): ?>
                <div class="flex-row" style="font-size: 0.9rem;">
                    <span>Tip</span>
                    <?php echo renderDualNumber(number_format($sale->tip, 2), $arabicEnabled, 'text-right'); ?>

                </div>
            <?php endif; ?>

            <div class="solid-line"></div>

            <div class="flex-row extra-bold" style="font-size: 1.2rem; margin-top: 5px;">
                <div>
                    <?php echo renderDual('TOTAL', 'الإجمالي', $arabicEnabled, 'text-left', true); ?>

                </div>
                <?php echo renderDualNumber(number_format($sale->total, 2), $arabicEnabled, 'text-right', true); ?>

            </div>

            <div class="dashed-line" style="margin: 10px 0;"></div>

            
            <?php if($sale->cash_amount > 0): ?>
                <div class="flex-row" style="font-size: 0.9rem;">
                    <span>Cash:</span>
                    <?php echo renderDualNumber(number_format($sale->cash_amount, 2), $arabicEnabled, 'text-right'); ?>

                </div>
            <?php endif; ?>
            <?php if($sale->card_amount > 0): ?>
                <div class="flex-row" style="font-size: 0.9rem;">
                    <span>Card:</span>
                    <?php echo renderDualNumber(number_format($sale->card_amount, 2), $arabicEnabled, 'text-right'); ?>

                </div>
            <?php endif; ?>
            <?php if($sale->online_amount > 0): ?>
                <div class="flex-row" style="font-size: 0.9rem;">
                    <span>Online:</span>
                    <?php echo renderDualNumber(number_format($sale->online_amount, 2), $arabicEnabled, 'text-right'); ?>

                </div>
            <?php endif; ?>
            <?php if($sale->other_amount > 0): ?>
                <div class="flex-row" style="font-size: 0.9rem;">
                    <span>Other:</span>
                    <?php echo renderDualNumber(number_format($sale->other_amount, 2), $arabicEnabled, 'text-right'); ?>

                </div>
            <?php endif; ?>

            <?php if($sale->cash_amount > 0): ?>
                <div class="flex-row" style="font-size: 0.9rem; margin-top: 5px;">
                    <span>Tendered:</span>
                    <?php echo renderDualNumber(number_format($sale->tendered_amount, 2), $arabicEnabled, 'text-right'); ?>

                </div>
                <div class="flex-row" style="font-size: 0.9rem;">
                    <span>Change:</span>
                    <?php echo renderDualNumber(number_format($sale->change_amount, 2), $arabicEnabled, 'text-right'); ?>

                </div>
            <?php endif; ?>

            <?php if($outstanding > 0): ?>
                <div class="dashed-line" style="margin: 10px 0;"></div>
                <div class="flex-row extra-bold" style="font-size: 1.1rem; color: #ef4444;">
                    <div>
                        <?php echo renderDual('OUTSTANDING', 'المتبقي', $arabicEnabled, 'text-left', true); ?>

                    </div>
                    <?php echo renderDualNumber(number_format($outstanding, 2), $arabicEnabled, 'text-right', true); ?>

                </div>
            <?php endif; ?>
        </div>

        <div class="dashed-line"></div>

        <div class="text-center" style="font-size: 0.9rem; color: #333;">
            <p class="bold mb-5">Thank You!</p>
            <p class="mb-5">Please come again</p>
            <?php if(!empty($salonData['website'])): ?>
                <p><?php echo e($salonData['website']); ?></p>
            <?php endif; ?>
        </div>

        <div class="no-print" style="margin-top: 15px; text-align: center;">
            <button onclick="window.print()" class="btn" style="background: #0f172a; color: #fff; padding: 6px 14px; font-size: 12px;">🖨️ Print Receipt</button>
            <button onclick="window.close()" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Close</button>
        </div>
    </div>
    <script>
        window.onload = function () {
            // Auto print only if requested
            if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
                window.print();
            }
        }
    </script>
</body>

</html><?php /**PATH C:\Ketan\projects\barber\resources\views/pos/receipt.blade.php ENDPATH**/ ?>