<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-dark fw-semibold">
                                <i class="fas fa-calendar-check me-2 text-primary"></i>
                                Appointment Details
                            </h5>
                            <div>
                                <?php if($booking->status === 'pending'): ?>
                                    <button type="button" class="btn btn-outline-danger me-2" onclick="cancelAppointment()">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </button>
                                <?php endif; ?>
                                <a href="<?php echo e(route('customer.appointments.index')); ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Status Banner -->
                        <div
                            class="alert alert-<?php echo e($booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'info' : ($booking->status === 'completed' ? 'success' : 'danger'))); ?> mb-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i
                                        class="fas fa-<?php echo e($booking->status === 'pending' ? 'clock' : ($booking->status === 'confirmed' ? 'check-circle' : ($booking->status === 'completed' ? 'check-double' : 'times-circle'))); ?> fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="alert-heading mb-1">
                                        <?php echo e(ucfirst($booking->status)); ?> Appointment
                                    </h6>
                                    <p class="mb-0">
                                        <?php if($booking->status === 'pending'): ?>
                                            Your appointment is pending confirmation. We'll notify you once it's confirmed.
                                        <?php elseif($booking->status === 'confirmed'): ?>
                                            Your appointment has been confirmed. We look forward to seeing you!
                                        <?php elseif($booking->status === 'completed'): ?>
                                            This appointment has been completed. Thank you for choosing our services!
                                        <?php else: ?>
                                            This appointment has been cancelled.
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- Service Details -->
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">
                                            <i class="fas fa-concierge-bell me-2"></i>
                                            Service Details
                                        </h6>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-lg bg-primary-subtle text-primary rounded">
                                                    <i class="fas fa-concierge-bell"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1"><?php echo e($booking->service->name); ?></h5>
                                                <p class="text-muted small mb-0"><?php echo e($booking->service->description); ?></p>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="text-muted small">Duration</span>
                                                <p class="mb-0"><?php echo e($booking->service->duration); ?> minutes</p>
                                            </div>
                                            <div class="text-end">
                                                <span class="text-muted small">Price</span>
                                                <p class="mb-0 text-primary fw-bold">
                                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($booking->service->price ?? 0, 2)); ?>

                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Staff Details -->
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">
                                            <i class="fas fa-user-tie me-2"></i>
                                            Staff Member
                                        </h6>
                                        <?php if($booking->staff): ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($booking->staff->name)); ?>&background=random"
                                                        alt="<?php echo e($booking->staff->name); ?>" class="rounded-circle"
                                                        style="width: 64px; height: 64px;">
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="mb-1"><?php echo e($booking->staff->name); ?></h5>
                                                    <p class="text-muted small mb-0">
                                                        <?php if($booking->staff->rating !== null): ?>
                                                            <i class="fas fa-star text-warning me-1"></i>
                                                            <?php echo e(number_format((float) $booking->staff->rating, 1)); ?>

                                                            <?php if($booking->staff->reviews_count > 0): ?>
                                                                (<?php echo e($booking->staff->reviews_count); ?>

                                                                <?php echo e($booking->staff->reviews_count == 1 ? 'review' : 'reviews'); ?>)
                                                            <?php else: ?>
                                                                (No reviews yet)
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">No rating yet</span>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted"
                                                        style="width: 64px; height: 64px;">
                                                        <i class="fas fa-user-clock fa-2x"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="mb-1 text-muted fst-italic">Pending Assignment</h5>
                                                    <p class="text-muted small mb-0">
                                                        A staff member will be assigned shortly.
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Appointment Time -->
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">
                                            <i class="fas fa-clock me-2"></i>
                                            Appointment Time
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-lg bg-success-subtle text-white rounded">
                                                            <i class="fas fa-calendar-day"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h5 class="mb-1"><?php echo e($booking->start_time->format('l, F j, Y')); ?>

                                                        </h5>
                                                        <p class="text-muted small mb-0">Date</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-lg bg-info-subtle text-info rounded">
                                                            <i class="fas fa-clock"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h5 class="mb-1"><?php echo e(format_time($booking->start_time)); ?></h5>
                                                        <p class="text-muted small mb-0">Time</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Notes -->
                            <?php if($booking->notes): ?>
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <h6 class="text-muted mb-3">
                                                <i class="fas fa-sticky-note me-2"></i>
                                                Additional Notes
                                            </h6>
                                            <p class="mb-0"><?php echo e($booking->notes); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Rating Section -->
                            <?php if($booking->status === 'completed'): ?>
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <h6 class="text-muted mb-3">
                                                <i class="fas fa-star me-2"></i>
                                                Service Rating
                                            </h6>
                                            <?php if($booking->ratings()->exists()): ?>
                                                <!-- Display existing rating -->
                                                <?php
                                                    $rating = $booking->ratings()->first();
                                                ?>
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-lg bg-warning-subtle text-warning rounded">
                                                            <i class="fas fa-star"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="mb-2">
                                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                                <i
                                                                    class="fas fa-star <?php echo e($i <= $rating->rating ? 'text-warning' : 'text-muted'); ?>"></i>
                                                            <?php endfor; ?>
                                                            <span class="ms-2 text-muted"><?php echo e($rating->rating); ?>/5</span>
                                                        </div>
                                                        <?php if($rating->comment): ?>
                                                            <p class="mb-0 text-muted"><?php echo e($rating->comment); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <!-- Rate service button -->
                                                <div class="text-center py-3">
                                                    <p class="text-muted mb-3">How was your experience?</p>
                                                    <button type="button" class="btn btn-warning"
                                                        onclick="showRatingModal(<?php echo e($booking->id); ?>)">
                                                        <i class="fas fa-star me-2"></i>Rate This Service
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Modal -->
    <div class="modal fade" id="ratingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="fas fa-star text-warning me-2"></i>Rate Your Experience</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <form id="ratingForm">
                        <input type="hidden" id="appointment_id" name="appointment_id">

                        <div class="text-center mb-4">
                            <p class="text-muted mb-3">How would you rate your experience?</p>
                            <div class="rating justify-content-center mb-2">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rating" value="<?php echo e($i); ?>" id="star<?php echo e($i); ?>">
                                    <label for="star<?php echo e($i); ?>" class="mx-1">
                                        <i class="fas fa-star" style="font-size: 2rem;"></i>
                                    </label>
                                <?php endfor; ?>
                            </div>
                            <div class="d-flex justify-content-between small text-muted w-75 mx-auto">
                                <span>Poor</span>
                                <span>Excellent</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label small text-muted">Share your experience
                                (optional)</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3"
                                placeholder="What did you like or dislike about the service?"></textarea>
                        </div>

                        <div id="ratingError" class="alert alert-danger py-2 d-none" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i> Please select a rating
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm" id="submitRatingBtn" onclick="submitRating()">
                        <i class="fas fa-paper-plane me-1"></i>Submit Rating
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
        <style>
            /* Rating stars */
            .rating {
                display: flex;
                flex-direction: row-reverse;
                justify-content: flex-start;
            }

            .rating input {
                display: none;
            }

            .rating label {
                cursor: pointer;
                font-size: 1.25rem;
                color: #e9ecef;
                padding: 0 0.1em;
                transition: color 0.2s;
            }

            .rating input:checked~label,
            .rating label:hover,
            .rating label:hover~label {
                color: #ffc107;
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            function cancelAppointment() {
                if (confirm('Are you sure you want to cancel this appointment?')) {
                    fetch('<?php echo e(route("customer.appointments.destroy", $booking->id)); ?>', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = '<?php echo e(route("customer.appointments.index")); ?>';
                            } else {
                                alert(data.message || 'Failed to cancel appointment');
                            }
                        })
                        .catch(error => {

                            alert('Failed to cancel appointment');
                        });
                }
            }

            function showRatingModal(appointmentId) {
                $('#appointment_id').val(appointmentId);
                // Reset form
                $('#ratingForm')[0].reset();
                $('.rating input').prop('checked', false);

                // Show modal
                var ratingModal = new bootstrap.Modal(document.getElementById('ratingModal'));
                ratingModal.show();
            }

            function submitRating() {
                const bookingId = $('#appointment_id').val();
                const rating = $('input[name="rating"]:checked').val();
                const comment = $('#comment').val();

                if (!rating) {
                    // Show error in modal instead of alert
                    $('#ratingError').removeClass('d-none');
                    return;
                }

                // Disable submit button and show loading state
                const submitBtn = $('#submitRatingBtn');
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...');

                // Clear any previous errors
                $('#ratingError').addClass('d-none');

                $.ajax({
                    url: `/<?php echo e(request()->current_salon->slug ?? 'demo-salon'); ?>/customer/appointments/${bookingId}/rate`,
                    method: 'POST',
                    data: {
                        rating: rating,
                        comment: comment,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function (response) {
                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('ratingModal')).hide();

                        // Reload page to show the rating
                        location.reload();
                    },
                    error: function (xhr) {
                        // Re-enable button on error
                        submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Submit Rating');

                        // Show error in modal
                        $('#ratingError').removeClass('d-none').text('Error submitting rating. Please try again.');
                    }
                });
            }
        </script>
    <?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\customer\appointments\show.blade.php ENDPATH**/ ?>