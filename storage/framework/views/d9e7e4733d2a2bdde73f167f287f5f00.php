<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Completed</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .content {
            padding: 30px;
        }

        .booking-details {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .booking-details h3 {
            margin-top: 0;
            color: #667eea;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }

        .detail-value {
            color: #212529;
        }

        .rating-section {
            text-align: center;
            margin: 30px 0;
            padding: 30px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 8px;
            color: white;
        }

        .rating-section h2 {
            margin-top: 0;
            font-size: 24px;
        }

        .rating-section p {
            font-size: 16px;
            margin: 15px 0;
        }

        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: white;
            color: #f5576c;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 16px;
            margin-top: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .stars {
            font-size: 30px;
            margin: 10px 0;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }

        .thank-you {
            text-align: center;
            color: #667eea;
            font-size: 18px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>✨ Thank You for Visiting! ✨</h1>
        </div>

        <div class="content">
            <p>Dear <?php echo e($booking->customer->name); ?>,</p>

            <p>Thank you for choosing <strong><?php echo e($booking->salon->name ?? 'our salon'); ?></strong>! We hope you enjoyed
                your experience with us.</p>

            <div class="booking-details">
                <h3>📋 Booking Summary</h3>
                <div class="detail-row">
                    <span class="detail-label">Service:</span>
                    <span class="detail-value"><?php echo e($booking->service->name); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Staff Member:</span>
                    <span class="detail-value"><?php echo e($booking->staff->name); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date & Time:</span>
                    <span class="detail-value"><?php echo e($booking->start_time->format('F j, Y \a\t g:i A')); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value"><?php echo e(currency_symbol()); ?><?php echo e(number_format($booking->amount, 2)); ?></span>
                </div>
            </div>

            <div class="rating-section">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <h2>How Was Your Experience?</h2>
                <p>We'd love to hear your feedback! Your rating helps us improve our services and helps
                    <?php echo e($booking->staff->name); ?> grow professionally.</p>
                <p>It only takes a minute!</p>
                <a href="<?php echo e($ratingUrl); ?>" class="btn">Rate Your Experience</a>
            </div>

            <p class="thank-you">We look forward to serving you again soon! 💜</p>

            <p>If you have any questions or concerns, please don't hesitate to contact us.</p>

            <p>Best regards,<br>
                <strong><?php echo e($booking->salon->name ?? 'Salon CMS'); ?> Team</strong>
            </p>
        </div>

        <div class="footer">
            <p>This is an automated email. Please do not reply directly to this message.</p>
            <?php if($booking->customer->is_guest): ?>
                <p style="font-size: 12px; margin-top: 10px;">
                    This rating link will expire in 30 days.
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\emails\bookings\completed.blade.php ENDPATH**/ ?>