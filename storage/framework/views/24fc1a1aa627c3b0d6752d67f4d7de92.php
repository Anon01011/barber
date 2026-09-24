<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 p-4 text-white"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="paymentModalLabel">
                        <i class="fas fa-wallet me-2"></i>Complete Payment
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-100" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form id="paymentForm">
                    <input type="hidden" id="payment_booking_id" name="booking_id">

                    <!-- Payment Method Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase small text-muted mb-3">Payment Method</label>
                        <div class="row g-3">
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="payment_method" id="payment_online"
                                    value="online" required>
                                <label
                                    class="payment-card card h-100 border-0 shadow-sm cursor-pointer position-relative py-3"
                                    for="payment_online">
                                    <div class="card-body text-center p-3">
                                        <i class="fas fa-globe text-primary mb-2" style="font-size: 2.5rem;"></i>
                                        <span class="fw-semibold d-block">Online</span>
                                    </div>
                                    <div class="check-indicator"><i class="fas fa-check-circle text-primary"></i>
                                    </div>
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="payment_method" id="payment_cash"
                                    value="cash">
                                <label
                                    class="payment-card card h-100 border-0 shadow-sm cursor-pointer position-relative py-3"
                                    for="payment_cash">
                                    <div class="card-body text-center p-3">
                                        <i class="fas fa-money-bill-wave text-success mb-2"
                                            style="font-size: 2.5rem;"></i>
                                        <span class="fw-semibold d-block">Cash</span>
                                    </div>
                                    <div class="check-indicator"><i class="fas fa-check-circle text-success"></i>
                                    </div>
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="payment_method" id="payment_card"
                                    value="card">
                                <label
                                    class="payment-card card h-100 border-0 shadow-sm cursor-pointer position-relative py-3"
                                    for="payment_card">
                                    <div class="card-body text-center p-3">
                                        <i class="fas fa-credit-card text-info mb-2" style="font-size: 2.5rem;"></i>
                                        <span class="fw-semibold d-block">Card</span>
                                    </div>
                                    <div class="check-indicator"><i class="fas fa-check-circle text-info"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Tip Amount -->
                    <div class="mb-4">
                        <label for="tip_amount" class="form-label fw-bold text-uppercase small text-muted mb-2">Tip
                            (Optional)</label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden bg-white">
                            <span
                                class="input-group-text border-0 bg-transparent text-muted ps-3"><?php echo e(currency_symbol()); ?></span>
                            <input type="number" class="form-control border-0 bg-transparent fw-bold" id="tip_amount"
                                name="tip_amount" min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>

                    <!-- Booking Summary -->
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Service Amount</span>
                                <span class="fw-bold" id="booking_amount"><?php echo e(currency_symbol()); ?>0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tip</span>
                                <span class="fw-bold text-success" id="display_tip">+ <?php echo e(currency_symbol()); ?>0.00</span>
                            </div>
                            <div class="border-top border-dashed my-2"></div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold fs-5 text-primary"
                                    id="total_amount"><?php echo e(currency_symbol()); ?>0.00</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 bg-light p-4 pt-0">
                <button type="button" class="btn btn-light flex-grow-1 rounded-3 fw-medium py-2"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary flex-grow-1 rounded-3 fw-bold shadow-sm py-2"
                    id="confirmPaymentBtn"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    Complete <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-card {
        transition: all 0.2s ease;
        border: 2px solid transparent !important;
    }

    .payment-card:hover {
        transform: translateY(-2px);
        background-color: #fff;
    }

    .check-indicator {
        position: absolute;
        top: 8px;
        right: 8px;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.2s ease;
        font-size: 1.2rem;
    }

    .btn-check:checked+.payment-card {
        background-color: #fff;
        border-color: currentColor !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12) !important;
    }

    .btn-check:checked+.payment-card .check-indicator {
        opacity: 1;
        transform: scale(1);
    }

    .btn-check:checked+.payment-card[for="payment_online"] {
        color: var(--bs-primary);
        border-color: var(--bs-primary) !important;
    }

    .btn-check:checked+.payment-card[for="payment_cash"] {
        color: var(--bs-success);
        border-color: var(--bs-success) !important;
    }

    .btn-check:checked+.payment-card[for="payment_card"] {
        color: var(--bs-info);
        border-color: var(--bs-info) !important;
    }

    .border-dashed {
        border-top-style: dashed !important;
    }
</style>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <div class="mx-auto bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-lg"
                        style="width: 60px; height: 60px; font-size: 2rem;">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Success!</h5>
                <p class="text-muted small mb-3">Payment recorded.</p>
                <button type="button" class="btn btn-dark btn-sm w-100 rounded-3 py-2 fw-medium"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Update tip display and total when tip amount changes
    document.getElementById('tip_amount').addEventListener('input', function () {
        const tipAmount = parseFloat(this.value) || 0;
        const bookingAmount = parseFloat(document.getElementById('booking_amount').textContent.replace('<?php echo e(currency_symbol()); ?>', '').trim()) || 0;

        document.getElementById('display_tip').textContent = '<?php echo e(currency_symbol()); ?>' + tipAmount.toFixed(2);
        document.getElementById('total_amount').textContent = '<?php echo e(currency_symbol()); ?>' + (bookingAmount + tipAmount).toFixed(2);
    });

    // Show payment modal with booking details
    function showPaymentModal(bookingId, bookingAmount) {
        document.getElementById('payment_booking_id').value = bookingId;
        document.getElementById('booking_amount').textContent = '<?php echo e(currency_symbol()); ?>' + parseFloat(bookingAmount).toFixed(2);
        document.getElementById('total_amount').textContent = '<?php echo e(currency_symbol()); ?>' + parseFloat(bookingAmount).toFixed(2);
        document.getElementById('tip_amount').value = '';
        document.getElementById('display_tip').textContent = '<?php echo e(currency_symbol()); ?>0.00';

        // Reset payment method selection
        document.querySelectorAll('input[name="payment_method"]').forEach(input => input.checked = false);

        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
    }

    // Handle payment confirmation
    document.getElementById('confirmPaymentBtn').addEventListener('click', function () {
        const form = document.getElementById('paymentForm');
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');

        if (!paymentMethod) {
            Swal.fire({
                icon: 'warning',
                title: 'Payment Method Required',
                text: 'Please select a payment method to continue.'
            });
            return;
        }

        const bookingId = document.getElementById('payment_booking_id').value;
        const tipAmount = document.getElementById('tip_amount').value;

        // Disable button to prevent double submission
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';

        // Get salon slug and determine context (admin or employee)
        const pathParts = window.location.pathname.split('/').filter(p => p);
        const salonSlug = pathParts[0];
        const isEmployee = pathParts[1] === 'employee';

        // Build appropriate endpoint
        const endpoint = isEmployee
            ? `/${salonSlug}/employee/appointments/${bookingId}/complete-payment`
            : `/${salonSlug}/admin/bookings/${bookingId}/complete-payment`;

        // Submit payment
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                payment_method: paymentMethod.value,
                tip_amount: tipAmount || null
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide payment modal
                    bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();

                    // Show confirmation modal
                    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
                    confirmationModal.show();

                    // Reload page after confirmation
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to complete booking'
                    });
                }
            })
            .catch(error => {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while processing payment'
                });
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
                this.innerHTML = 'Complete <i class="fas fa-arrow-right ms-1"></i>';
            });
    });
</script><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views/admin/bookings/partials/payment_modal.blade.php ENDPATH**/ ?>