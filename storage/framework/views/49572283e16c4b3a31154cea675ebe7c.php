<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #<?php echo e($payment->transaction_id); ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            font-size: 14px;
            line-height: 24px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                Salon CMS
                            </td>
                            <td>
                                Invoice #: <?php echo e($payment->transaction_id); ?><br>
                                Created: <?php echo e($payment->created_at->format('M d, Y')); ?><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                Salon CMS, Inc.<br>
                                123 Admin Street<br>
                                New York, NY 10001
                            </td>
                            <td>
                                <?php echo e($payment->salon->name); ?><br>
                                <?php echo e($payment->salon->email); ?>

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Item</td>
                <td>Price</td>
            </tr>
            <tr class="item">
                <td><?php echo e($payment->subscription->plan->name); ?> Subscription</td>
                <td><?php echo e($payment->currency); ?> <?php echo e(number_format($payment->amount, 2)); ?></td>
            </tr>
            <tr class="total">
                <td></td>
                <td>Total: <?php echo e($payment->currency); ?> <?php echo e(number_format($payment->amount, 2)); ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
<?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\super-admin\invoices\pdf.blade.php ENDPATH**/ ?>