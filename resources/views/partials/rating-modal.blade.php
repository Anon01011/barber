<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="ratingModalLabel">
                    <i class="fas fa-star me-2"></i>Rate Your Experience
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="ratingForm">
                @csrf
                <input type="hidden" id="bookingId" name="booking_id">

                <div class="modal-body">
                    <!-- Booking Info -->
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                            <div>
                                <h6 class="mb-1" id="ratingServiceName"></h6>
                                <small id="ratingStaffName"></small><br>
                                <small id="ratingDate"></small>
                            </div>
                        </div>
                    </div>

                    <!-- Star Rating -->
                    <div class="text-center mb-4">
                        <h6 class="mb-3">How would you rate your experience?</h6>
                        <div class="star-rating-input" id="starRating">
                            <i class="fas fa-star star" data-rating="1"></i>
                            <i class="fas fa-star star" data-rating="2"></i>
                            <i class="fas fa-star star" data-rating="3"></i>
                            <i class="fas fa-star star" data-rating="4"></i>
                            <i class="fas fa-star star" data-rating="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="ratingValue" required>
                        <div id="ratingError" class="text-danger mt-2" style="display: none;">
                            Please select a rating
                        </div>
                    </div>

                    <!-- Comment -->
                    <div class="mb-3">
                        <label for="ratingComment" class="form-label">
                            <i class="fas fa-comment me-1"></i>Share your thoughts (optional)
                        </label>
                        <textarea class="form-control" id="ratingComment" name="comment" rows="4"
                            placeholder="Tell us about your experience..." maxlength="1000"></textarea>
                        <small class="text-muted">Maximum 1000 characters</small>
                    </div>

                    <div id="ratingAlertContainer"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitRatingBtn">
                        <i class="fas fa-paper-plane me-1"></i>Submit Rating
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .star-rating-input {
        font-size: 40px;
        cursor: pointer;
        user-select: none;
    }

    .star-rating-input .star {
        color: #ddd;
        transition: all 0.2s;
        margin: 0 5px;
    }

    .star-rating-input .star:hover,
    .star-rating-input .star.active {
        color: #ffc107;
        transform: scale(1.1);
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ratingModal = document.getElementById('ratingModal');
        const ratingForm = document.getElementById('ratingForm');
        const stars = document.querySelectorAll('.star-rating-input .star');
        const ratingValue = document.getElementById('ratingValue');
        const ratingError = document.getElementById('ratingError');
        const submitBtn = document.getElementById('submitRatingBtn');
        const alertContainer = document.getElementById('ratingAlertContainer');

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

        document.querySelector('.star-rating-input').addEventListener('mouseleave', function () {
            stars.forEach((s, index) => {
                if (index < selectedRating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });

        // Form submission
        if (ratingForm) {
            ratingForm.addEventListener('submit', function (e) {
                e.preventDefault();

                // Validate rating
                if (!selectedRating) {
                    ratingError.style.display = 'block';
                    return;
                }

                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Submitting...';

                const bookingId = document.getElementById('bookingId').value;
                const formData = {
                    rating: selectedRating,
                    comment: document.getElementById('ratingComment').value
                };

                // Submit via AJAX
                fetch(`/{{ $salon_slug ?? '' }}/customer/appointments/${bookingId}/rate`, {
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
                            alertContainer.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;

                            // Close modal after 2 seconds
                            setTimeout(() => {
                                const modal = bootstrap.Modal.getInstance(ratingModal);
                                if (modal) modal.hide();

                                // Reload page to update UI
                                window.location.reload();
                            }, 2000);
                        } else {
                            throw new Error(data.message || 'Failed to submit rating');
                        }
                    })
                    .catch(error => {
                        // Show error alert
                        alertContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>${error.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;

                        // Re-enable submit button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Submit Rating';
                    });
            });
        }

        // Reset modal when closed
        if (ratingModal) {
            ratingModal.addEventListener('hidden.bs.modal', function () {
                ratingForm.reset();
                selectedRating = 0;
                stars.forEach(s => s.classList.remove('active'));
                ratingError.style.display = 'none';
                alertContainer.innerHTML = '';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Submit Rating';
            });
        }
    });

    // Function to open rating modal (call this from dashboard)
    function openRatingModal(booking) {
        document.getElementById('bookingId').value = booking.id;
        document.getElementById('ratingServiceName').textContent = booking.service_name;
        document.getElementById('ratingStaffName').textContent = 'with ' + booking.staff_name;
        document.getElementById('ratingDate').textContent = booking.date;

        const modal = new bootstrap.Modal(document.getElementById('ratingModal'));
        modal.show();
    }
</script>