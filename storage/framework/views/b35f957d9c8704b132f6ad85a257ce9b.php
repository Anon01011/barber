<?php
    $arabicEnabled = $settings->get('pos_receipt_arabic', true, $booking->salon_id);

    // Calculate totals
    $subtotal = $bookings->sum('amount');
    $totalTip = $bookings->sum('tip_amount');
    $total = $subtotal + $totalTip;

    // Calculate outstanding for partial payments
    $totalPaid = $bookings->sum(function ($b) {
        return ($b->cash_amount ?? 0) + ($b->card_amount ?? 0) + ($b->online_amount ?? 0) + ($b->other_amount ?? 0);
    });
    $outstanding = max(0, $total - $totalPaid);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?php echo e($booking->id); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #000000;
            line-height: 1.4;
            background: #ffffff;
            padding: 20px;
            margin: 0;
            font-size: 14px;
        }

        .receipt-wrapper {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 0 10px;
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
            max-height: 60px;
            width: auto;
            margin-bottom: 10px;
            object-fit: contain;
        }

        .dashed-line {
            border-bottom: 1px dashed #000;
            margin: 15px 0;
        }

        .solid-line {
            border-bottom: 2px solid #000;
            margin: 10px 0;
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
            font-size: 13px;
        }

        th {
            text-align: left;
            padding-bottom: 8px;
            border-bottom: 1px solid #000;
            font-weight: 700;
        }

        td {
            padding: 8px 0;
            vertical-align: top;
        }

        .dual-display {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .dual-display.text-center {
            align-items: center;
        }

        .dual-display.text-left {
            align-items: flex-start;
        }

        .dual-display.text-right {
            align-items: flex-end;
        }

        .dual-display .ar {
            font-size: 0.85em;
            color: #444;
            margin-top: 1px;
            font-family: 'Inter', sans-serif;
        }

        .no-print {
            margin-top: 20px;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            margin: 5px;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #000;
        }

        .unpaid-sticker {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            border: 4px solid #ef4444;
            color: #ef4444;
            font-size: 2.5rem;
            font-weight: 900;
            padding: 0.5rem 1rem;
            text-transform: uppercase;
            opacity: 0.2;
            pointer-events: none;
            z-index: 1000;
            border-radius: 8px;
            letter-spacing: 2px;
            white-space: nowrap;
            font-family: 'Inter', sans-serif;
        }

        @media print {
            .unpaid-sticker {
                opacity: 0.15;
                border-color: #000;
                color: #000;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .receipt-wrapper {
                max-width: 100%;
                margin: 0 auto;
                padding: 0 8mm;
            }

            .no-print {
                display: none;
            }

            @page {
                margin: 0;
                size: auto;
            }
        }
    </style>
</head>

<body>
    <div class="receipt-wrapper" style="position: relative;">
        <?php if($outstanding > 0): ?>
            <div class="unpaid-sticker" style="border-color: #f59e0b; color: #f59e0b; opacity: 0.2;">PARTIAL PAYMENT</div>
        <?php elseif($booking->payment_status !== 'paid'): ?>
            <div class="unpaid-sticker">UNPAID</div>
        <?php endif; ?>

        <div class="text-center mb-20">
            <?php if(!empty($salonData['logo'])): ?>
                <img src="<?php echo e($salonData['logo']); ?>" alt="<?php echo e($salonData['name']); ?>" class="logo">
            <?php endif; ?>
            <h2 class="extra-bold uppercase mb-5" style="font-size: 1.4rem; margin-top: 5px;">
                <?php echo e($salonData['name'] ?? 'SALON'); ?>

            </h2>
            <?php if(!empty($salonData['address'])): ?>
                <p style="font-size: 0.9rem; margin: 0; color: #333;"><?php echo e($salonData['address']); ?></p>
            <?php endif; ?>
            <?php if(!empty($salonData['phone'])): ?>
                <p style="font-size: 0.9rem; margin: 2px 0 0 0; color: #333;">Tel: <?php echo e($salonData['phone']); ?></p>
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
                    if (!$arabicEnabled)
                        return $eng;
                    $boldStyle = $isBold ? 'font-weight: 700;' : '';
                    return "
                                                                                    <div class=\"dual-display {$align}\" style=\"{$boldStyle}\">
                                                                                        <div class=\"eng\">{$eng}</div>
                                                                                        <div class=\"ar\">{$ar}</div>
                                                                                    </div>
                                                                                ";
                }
            }

            if (!function_exists('renderDualNumber')) {
                function renderDualNumber($number, $arabicEnabled, $align = 'text-left', $isBold = false)
                {
                    if (!$arabicEnabled)
                        return $number;
                    $arabic = toArabicNumerals($number);
                    return renderDual($number, $arabic, true, $align, $isBold);
                }
            }
        ?>

        <div class="mb-15" style="font-size: 0.9rem;">
            <div class="flex-row">
                <div>
                    <?php echo renderDual('Booking No:', 'رقم الحجز:', $arabicEnabled, 'text-left', true); ?>

                </div>
                <?php echo renderDualNumber('#' . $booking->id, $arabicEnabled, 'text-right', true); ?>

            </div>
            <div class="flex-row">
                <div>
                    <?php echo renderDual('Date:', 'التاريخ:', $arabicEnabled, 'text-left', true); ?>

                </div>
                <?php echo renderDual($booking->created_at->format('M d, Y H:i'), toArabicNumerals($booking->created_at->format('Y-m-d H:i')), $arabicEnabled, 'text-right'); ?>

            </div>
            <div class="flex-row">
                <div>
                    <?php echo renderDual('Customer:', 'العميل:', $arabicEnabled, 'text-left', true); ?>

                </div>
                <span><?php echo e($booking->customer->name ?? 'Guest'); ?></span>
            </div>
            <div class="flex-row">
                <span class="bold">Cashier:</span>
                <span><?php echo e($booking->creator->name ?? 'System'); ?></span>
            </div>
            <div class="flex-row">
                <span class="bold">Payment:</span>
                <span class="uppercase">
                    <?php echo e($booking->payment_method ? ucfirst(str_replace('_', ' ', $booking->payment_method)) : 'N/A'); ?>

                </span>
            </div>
        </div>

        <div class="dashed-line"></div>

        <table>
            <thead>
                <tr>
                    <th style="width: 55%;">
                        <?php echo renderDual('SERVICE', 'الخدمة', $arabicEnabled, 'text-left', true); ?>

                    </th>
                    <th class="text-center" style="width: 15%;">
                        <?php echo renderDual('TIME', 'الوقت', $arabicEnabled, 'text-center', true); ?>

                    </th>
                    <th class="text-right" style="width: 30%;">
                        <?php echo renderDual('AMT', 'المبلغ', $arabicEnabled, 'text-right', true); ?>

                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <?php echo renderDual($item->service->name, '', $arabicEnabled, 'text-left', true); ?>

                            <?php if($item->staff): ?>
                                <div style="font-size: 0.75rem; color: #666;">Staff: <?php echo e($item->staff->name); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center small">
                            <?php echo e(\Carbon\Carbon::parse($item->start_time)->format('H:i')); ?>

                        </td>
                        <td class="text-right">
                            <?php echo renderDualNumber(format_currency($item->amount), $arabicEnabled, 'text-right', true); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div class="dashed-line"></div>

        <div class="mb-15">
            <div class="flex-row">
                <div>
                    <?php echo renderDual('Subtotal', 'المجموع الفرعي', $arabicEnabled, 'text-left', true); ?>

                </div>
                <?php echo renderDualNumber(format_currency($subtotal), $arabicEnabled, 'text-right', true); ?>

            </div>

            <?php if($totalTip > 0): ?>
                <div class="flex-row" style="font-size: 0.9rem;">
                    <span>Tip</span>
                    <?php echo renderDualNumber(format_currency($totalTip), $arabicEnabled, 'text-right'); ?>

                </div>
            <?php endif; ?>

            <div class="solid-line"></div>

            <div class="flex-row extra-bold" style="font-size: 1.2rem; margin-top: 5px;">
                <div>
                    <?php echo renderDual('TOTAL', 'الإجمالي', $arabicEnabled, 'text-left', true); ?>

                </div>
                <?php echo renderDualNumber(format_currency($total), $arabicEnabled, 'text-right', true); ?>

            </div>

            <?php if($outstanding > 0): ?>
                <div class="dashed-line" style="margin: 10px 0;"></div>
                <div class="flex-row extra-bold" style="font-size: 1.1rem; color: #ef4444;">
                    <div>
                        <?php echo renderDual('OUTSTANDING', 'المتبقي', $arabicEnabled, 'text-left', true); ?>

                    </div>
                    <?php echo renderDualNumber(format_currency($outstanding), $arabicEnabled, 'text-right', true); ?>

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

        <div class="no-print">
            <button onclick="window.print()" class="btn">Print Receipt</button>
            <button onclick="window.close()" class="btn btn-secondary">Close</button>
        </div>
    </div>
    <script>
        window.onload = function () {
            // Auto print when loaded
            window.print();
        }
    </script>
</body>

</html><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\bookings\receipt.blade.php ENDPATH**/ ?>