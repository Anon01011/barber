<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Rate Your Experience - <?php echo e($booking->salon->name ?? 'Salon CMS'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .rating-container {
            max-width: 600px;
            width: 100%;
            margin: 20px;
        }

        .rating-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .card-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .card-body {
            padding: 40px;
        }

        .booking-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .booking-info h5 {
            color: #667eea;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
        }

        .info-value {
            color: #212529;
        }

        .star-rating {
            text-align: center;
            margin: 30px 0;
        }

        .star-rating h4 {
            margin-bottom: 20px;
            color: #333;
        }

        .stars {
            font-size: 50px;
            cursor: pointer;
            user-select: none;
        }

        .stars i {
            color: #ddd;
            transition: all 0.2s;
            margin: 0 5px;
        }

        .stars i.active,
        .stars i:hover,
        .stars i:hover~i {
            color: #ffc107;
            transform: scale(1.1);
        }

        .comment-section {
            margin: 30px 0;
        }

        .comment-section label {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: transform 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .alert {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .success-message {
            text-align: center;
            padding: 40px;
        }

        .success-message i {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }

        .success-message h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .error-message {
            text-align: center;
            padding: 40px;
        }

        .error-message i {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="rating-container">
        <div class="rating-card">
            <div class="card-header">
                <h1><i class="fas fa-star"></i> Rate Your Experience</h1>
            </div>

            <div class="card-body">
                <?php if(isset($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <h2>Oops!</h2>
                        <p class="text-muted"><?php echo e($error); ?></p>
                        <?php if(isset($booking)): ?>
                            <p class="mt-3">
                                <small>Booking ID: #<?php echo e($booking->id); ?></small>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php elseif(isset($success)): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <h2>Thank You!</h2>
                        <p class="text-muted"><?php echo e($success); ?></p>
                        <?php if(isset($booking)): ?>
                            <div class="booking-info mt-4">
                                <h5><i class="fas fa-info-circle"></i> Booking Details</h5>
                                <div class="info-row">
                                    <span class="info-label">Service:</span>
                                    <span class="info-value"><?php echo e($booking->service->name); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Staff:</span>
                                    <span class="info-value"><?php echo e($booking->staff->name); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Date:</span>
                                    <span class="info-value"><?php echo e($booking->start_time->format('F j, Y')); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="booking-info">
                        <h5><i class="fas fa-calendar-check"></i> Booking Details</h5>
                        <div class="info-row">
                            <span class="info-label">Service:</span>
                            <span class="info-value"><?php echo e($booking->service->name); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Staff Member:</span>
                            <span class="info-value"><?php echo e($booking->staff->name); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date & Time:</span>
                            <span class="info-value"><?php echo e($booking->start_time->format('F j, Y \a\t g:i A')); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Salon:</span>
                            <span class="info-value"><?php echo e($booking->salon->name ?? 'N/A'); ?></span>
                        </div>
                    </div>

                    <form id="ratingForm">
                        <?php echo csrf_field(); ?>
                        <div class="star-rating">
                            <h4>How would you rate your experience?</h4>
                            <div class="stars" id="starRating">
                                <i class="fas fa-star" data-rating="1"></i>
                                <i class="fas fa-star" data-rating="2"></i>
                                <i class="fas fa-star" data-rating="3"></i>
                                <i class="fas fa-star" data-rating="4"></i>
                                <i class="fas fa-star" data-rating="5"></i>
                            </div>
                            <input type="hidden" name="rating" id="ratingValue" required>
                            <div id="ratingError" class="text-danger mt-2" style="display: none;">
                                Please select a rating
                            </div>
                        </div>

                        <div class="comment-section">
                            <label for="comment">
                                <i class="fas fa-comment"></i> Share your thoughts (optional)
                            </label>
                            <textarea class="form-control" id="comment" name="comment" rows="4"
                                placeholder="Tell us about your experience with <?php echo e($booking->staff->name); ?>..."
                                maxlength="1000"></textarea>
                            <small class="text-muted">Maximum 1000 characters</small>
                        </div>

                        <div id="alertContainer"></div>

                        <button type="submit" class="btn btn-submit" id="submitBtn">
                            <i class="fas fa-paper-plane"></i> Submit Rating
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stars = document.querySelectorAll('.stars i');
            const ratingValue = document.getElementById('ratingValue');
            const ratingError = document.getElementById('ratingError');
            const form = document.getElementById('ratingForm');
            const submitBtn = document.getElementById('submitBtn');
            const alertContainer = document.getElementById('alertContainer');

            if (!form) return; // Exit if form doesn't exist (error/success state)

            let selectedRating = 0;

            // Star rating interaction
            stars.forEach(star => {
                star.addEventListener('click', function () {
                    selectedRating = parseInt(this.getAttribute('data-rating'));
                    ratingValue.value = selectedRating;
                    ratingError.style.display = 'none';

                    // Update star display
                    stars.forEach((s, index) => {
                        if (index < selectedRating) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });

                star.addEventListener('mouseenter', function () {
                    const hoverRating = parseInt(this.getAttribute('data-rating'));
                    stars.forEach((s, index) => {
                        if (index < hoverRating) {
                            s.style.color = '#ffc107';
                        } else {
                            s.style.color = '#ddd';
                        }
                    });
                });
            });

            document.querySelector('.stars').addEventListener('mouseleave', function () {
                stars.forEach((s, index) => {
                    if (index < selectedRating) {
                        s.style.color = '#ffc107';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });

            // Form submission
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                // Validate rating
                if (!selectedRating) {
                    ratingError.style.display = 'block';
                    return;
                }

                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

                // Get form data
                const formData = {
                    rating: selectedRating,
                    comment: document.getElementById('comment').value
                };

                // Submit via AJAX
                fetch('<?php echo e(route("ratings.guest.submit", ["token" => $token ?? ""])); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            document.querySelector('.card-body').innerHTML = `
                            <div class="success-message">
                                <i class="fas fa-check-circle"></i>
                                <h2>Thank You!</h2>
                                <p class="text-muted">${data.message}</p>
                                <p class="mt-4">
                                    <small class="text-muted">You can now close this window.</small>
                                </p>
                            </div>
                        `;
                        } else {
                            throw new Error(data.message || 'Failed to submit rating');
                        }
                    })
                    .catch(error => {
                        // Show error alert
                        alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> ${error.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;

                        // Re-enable submit button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Rating';
                    });
            });
        });
    </script>
</body>

</html><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\ratings\guest-rating.blade.php ENDPATH**/ ?>