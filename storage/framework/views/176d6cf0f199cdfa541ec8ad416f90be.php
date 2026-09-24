<style>
    #billActivityModal {
        --primary-color: #7c3aed;
        --primary-light: #f5f3ff;
        --primary-dark: #6d28d9;
        --border-color: #e2e8f0;
    }
    #billActivityModal .modal-content {
        border-radius: 12px;
    }
    #billActivityModal .card {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        background-color: #ffffff;
    }
    #billActivityModal .card-header {
        background-color: #f8fafc;
        border-bottom: 1px solid var(--border-color);
        color: #334155;
        font-weight: 600;
        font-size: 0.85rem;
    }
    #billActivityModal .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    #billActivityModal .table th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        background-color: #f1f5f9;
        color: #475569;
        border-bottom: 1px solid #cbd5e1;
        padding: 0.75rem 0.5rem;
    }
    #billActivityModal .table td {
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
    }
    #billActivityModal .badge-success-subtle {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    #billActivityModal .badge-warning-subtle {
        background-color: #fef9c3;
        color: #a16207;
        border: 1px solid #fef08a;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    #billActivityModal .badge-danger-subtle {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    #billActivityModal .badge-secondary-subtle {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    @media (min-width: 768px) {
        #billActivityModal .modal-body {
            display: flex !important;
            flex-direction: column !important;
            height: 600px !important; /* Increased height to 600px to ensure details fit perfectly */
            max-height: calc(100vh - 120px) !important;
            overflow: hidden !important;
        }
        #billActivityModal .bill-activity-container {
            display: flex !important;
            flex-wrap: nowrap !important;
            flex-grow: 1 !important;
            min-height: 0 !important;
        }
        #billActivityModal .bill-list-side {
            flex: 0 0 54% !important;
            width: 54% !important;
            max-height: 100% !important;
            height: 100% !important;
            overflow-y: auto !important;
            background-color: #f8fafc; /* Shaded background for visual split */
        }
        #billActivityModal .bill-details-side {
            flex: 0 0 46% !important;
            width: 46% !important;
            max-height: 100% !important;
            height: 100% !important;
            overflow-y: auto !important;
            background-color: #ffffff; /* Contrast white background */
        }
    }
</style>

<div class="modal fade" id="billActivityModal" tabindex="-1" aria-hidden="true" style="z-index: 1100;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 95%;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px; overflow: hidden;">
            <div class="modal-header border-0 text-white py-3 px-3"
                style="background: linear-gradient(135deg, var(--primary-color) 0%, #5b21b6 100%); border-top-left-radius: 8px; border-top-right-radius: 8px;">
                <h5 class="modal-title d-flex align-items-center gap-2 mb-0 fw-semibold">
                    <i class="fas fa-file-invoice me-2"></i> Bill Activity
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="max-height: calc(75vh - 100px); height: auto; overflow-y: auto;">
                <div class="row g-0 h-100 bill-activity-container">
                    <!-- Left Side - Bill List -->
                    <div class="col-md-7 border-end bill-list-side" style="overflow-y: auto; height: 100%">
                        <div class="p-3">
                            <h6 class="text-secondary mb-3 small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Bill History</h6>
                            <div id="billListContainer">
                                <!-- Loading State -->
                                <div class="text-center py-5" id="billListLoading">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted mt-2">Loading bills...</p>
                                </div>

                                <!-- Bill List Table -->
                                <div id="billListTable" style="display: none">
                                    <table class="table table-hover table-sm mb-0">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Bill No</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th class="text-end">Total</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="billListBody">
                                            <!-- Bills will be populated here -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Empty State -->
                                <div class="text-center py-5" id="billListEmpty" style="display: none">
                                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No bills found for this customer.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Bill Details -->
                    <div class="col-md-5 bill-details-side" style="overflow-y: auto; height: 100%">
                        <div class="p-3">
                            <!-- Initial State -->
                            <div id="billDetailsInitial" class="text-center py-5">
                                <i class="fas fa-hand-pointer fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Select a bill to view details</p>
                            </div>

                            <!-- Bill Details Content -->
                            <div id="billDetailsContent" style="display: none">
                                <!-- Bill Details Card -->
                                <div class="card border mb-3">
                                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-semibold"><i class="fas fa-info-circle text-primary me-2"></i>Bill Details</h6>
                                        <button type="button" class="btn btn-sm btn-success" id="payInPosBtn" style="display: none;">
                                            <i class="fas fa-cash-register me-1"></i>Pay in POS
                                        </button>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <span class="text-muted small d-block">Bill No</span>
                                                <span class="fw-semibold text-dark" id="detailBillNo">-</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted small d-block">Date & Time</span>
                                                <span class="fw-semibold text-dark" id="detailDate">-</span>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <span class="text-muted small d-block">Status</span>
                                                <div class="mt-1"><span id="detailPaymentStatus" class="badge">-</span></div>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <span class="text-muted small d-block">Method</span>
                                                <span class="fw-semibold text-dark" id="detailPaymentMethod">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Summary Card -->
                                <div class="card border mb-3" id="paymentSummaryCard" style="display: none;">
                                    <div class="card-header py-2">
                                        <h6 class="mb-0 fw-semibold"><i class="fas fa-file-invoice-dollar text-success me-2"></i>Payment Summary</h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted small">Total Amount</span>
                                            <span class="fw-bold text-dark fs-6" id="detailTotalAmount">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted small">Paid Amount</span>
                                            <span class="fw-semibold text-success" id="detailPaidAmount">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top text-danger" id="detailBalanceRow">
                                            <span class="fw-semibold">Balance Due</span>
                                            <span class="fw-bold fs-6" id="detailBalanceAmount">-</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Service Details Card -->
                                <div class="card border mb-3">
                                    <div class="card-header py-2">
                                        <h6 class="mb-0 fw-semibold"><i class="fas fa-cut text-info me-2"></i>Service Details</h6>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless mb-0">
                                                <thead>
                                                    <tr class="text-muted small border-bottom">
                                                        <th class="pb-1 fw-normal">Service</th>
                                                        <th class="pb-1 fw-normal">Staff</th>
                                                        <th class="pb-1 text-end fw-normal">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="detailServicesBody">
                                                    <!-- Services will be populated here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Feedback Details Card -->
                                <div class="card border mb-3">
                                    <div class="card-header py-2">
                                        <h6 class="mb-0 fw-semibold"><i class="fas fa-star text-warning me-2"></i>Feedback Details</h6>
                                    </div>
                                    <div class="card-body p-3" id="detailFeedbackBody">
                                        <!-- Feedback will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Bill Activity Modal JavaScript
    window.billActivityData = window.billActivityData || null;
    window.selectedCustomerId = window.selectedCustomerId || null;

    window.openBillActivityModal = function (customerId, filter = null) {
        selectedCustomerId = customerId;
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('billActivityModal'));
        modal.show();

        // Reset modal state
        document.getElementById('billListLoading').style.display = 'block';
        document.getElementById('billListTable').style.display = 'none';
        document.getElementById('billListEmpty').style.display = 'none';
        document.getElementById('billDetailsInitial').style.display = 'block';
        document.getElementById('billDetailsContent').style.display = 'none';

        // Fetch bill activity
        fetchBillActivity(customerId, filter);
    }

    window.openUnpaidBillsModal = function (customerId) {
        window.openBillActivityModal(customerId, 'unpaid');
    }

    function fetchBillActivity(customerId, filter = null) {
        <?php if(auth()->user()->salon): ?>
            const salonSlug = '<?php echo e(auth()->user()->salon->slug); ?>';
        <?php else: ?>
            // Fallback for Super Admin or users without a direct salon relationship
            const pathParts = window.location.pathname.split('/');
            const salonSlug = pathParts[1] || 'admin';
        <?php endif; ?>
        const url = `/${salonSlug}/admin/customers/${customerId}/bill-activity`;

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    billActivityData = data;

                    // Filter bills if filter is provided
                    let billsToDisplay = data.bills;
                    if (filter === 'unpaid') {
                        billsToDisplay = data.bills.filter(bill => {
                            const status = bill.payment_status.toLowerCase();
                            return status === 'unpaid' || status === 'pending' || status === 'partial';
                        });
                    }

                    displayBillList(billsToDisplay);
                } else {
                    showError('Failed to load bill activity');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('An error occurred while loading bills');
            });
    }

    function displayBillList(bills) {
        const billListBody = document.getElementById('billListBody');
        const billListLoading = document.getElementById('billListLoading');
        const billListTable = document.getElementById('billListTable');
        const billListEmpty = document.getElementById('billListEmpty');

        billListLoading.style.display = 'none';

        if (bills.length === 0) {
            billListEmpty.style.display = 'block';
            return;
        }

        billListTable.style.display = 'block';
        billListBody.innerHTML = '';

        bills.forEach((bill, index) => {
            const row = document.createElement('tr');
            row.style.cursor = 'pointer';
            row.style.transition = 'all 0.2s ease';

            // Store the bill data directly on the row
            row.dataset.billData = JSON.stringify(bill);

            // Determine badge class based on payment status
            let badgeClass = 'badge-secondary-subtle';
            const status = bill.payment_status.toLowerCase();
            if (status === 'paid' || status === 'completed') {
                badgeClass = 'badge-success-subtle';
            } else if (status === 'pending') {
                badgeClass = 'badge-warning-subtle';
            } else if (status === 'unpaid' || status === 'partial') {
                badgeClass = 'badge-danger-subtle';
            }

            row.innerHTML = `
                <td class="py-2 fw-medium text-dark" style="font-size: 0.85rem;">${bill.bill_no}</td>
                <td class="py-2 text-muted" style="font-size: 0.8rem;">${bill.date}</td>
                <td class="py-2">
                    <span class="${badgeClass}">
                        ${bill.payment_status}
                    </span>
                </td>
                <td class="text-end py-2 fw-bold text-dark" style="font-size: 0.85rem;">
                    <?php echo e(currency_symbol()); ?>${parseFloat(bill.grand_total).toFixed(2)}
                    ${(status === 'partial' || status === 'unpaid' || status === 'pending') ?
                    `<div class="text-danger small fw-normal" style="font-size: 0.7rem;">Bal: <?php echo e(currency_symbol()); ?>${parseFloat(bill.outstanding_amount || 0).toFixed(2)}</div>` : ''}
                </td>
                <td class="text-center py-2">
                    <button class="btn btn-sm btn-outline-primary view-bill-btn" style="font-size: 0.75rem; padding: 0.2rem 0.4rem; border-radius: 6px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            `;

            // Add hover effect
            row.addEventListener('mouseenter', function () {
                if (!this.classList.contains('table-active')) {
                    this.style.backgroundColor = '#f1f5f9';
                }
            });

            row.addEventListener('mouseleave', function () {
                if (!this.classList.contains('table-active')) {
                    this.style.backgroundColor = '';
                }
            });

            billListBody.appendChild(row);

            // Make entire row clickable except the button
            row.addEventListener('click', (e) => {
                if (!e.target.closest('button')) {
                    const billData = JSON.parse(row.dataset.billData);
                    viewBillDetailsFromData(billData, row);
                }
            });
        });

        // Add event listeners to view buttons
        document.querySelectorAll('.view-bill-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const row = this.closest('tr');
                const billData = JSON.parse(row.dataset.billData);
                viewBillDetailsFromData(billData, row);
            });
        });
    }

    window.viewBillDetails = function (billIndex) {
        const bill = billActivityData.bills[billIndex];
        viewBillDetailsFromData(bill, null);
    }

    function viewBillDetailsFromData(bill, selectedRow) {
        // Show details panel
        document.getElementById('billDetailsInitial').style.display = 'none';
        document.getElementById('billDetailsContent').style.display = 'block';

        // Populate bill header
        document.getElementById('detailBillNo').textContent = bill.bill_no;
        document.getElementById('detailDate').textContent = bill.datetime;

        // Populate Payment Status with Badge
        const statusSpan = document.getElementById('detailPaymentStatus');
        const status = bill.payment_status.toLowerCase();
        let badgeClass = 'badge-secondary-subtle';

        if (status === 'paid' || status === 'completed') {
            badgeClass = 'badge-success-subtle';
        } else if (status === 'pending') {
            badgeClass = 'badge-warning-subtle';
        } else if (status === 'unpaid' || status === 'partial') {
            badgeClass = 'badge-danger-subtle';
        }

        statusSpan.className = badgeClass;
        statusSpan.textContent = bill.payment_status;

        // Show/hide Pay in POS button based on payment status
        const payInPosBtn = document.getElementById('payInPosBtn');
        if (status === 'unpaid' || status === 'pending' || status === 'partial') {
            payInPosBtn.style.display = 'inline-block';
            payInPosBtn.onclick = function () {
                <?php if(auth()->user()->salon): ?>
                    const salonSlug = '<?php echo e(auth()->user()->salon->slug); ?>';
                <?php else: ?>
                    const pathParts = window.location.pathname.split('/');
                    const salonSlug = pathParts[1];
                <?php endif; ?>
                const paramName = (bill.type === 'pos') ? 'sale_id' : 'booking_id';
                window.location.href = `/${salonSlug}/admin/pos?${paramName}=${bill.id}`;
            };
        } else {
            payInPosBtn.style.display = 'none';
        }

        // Populate Payment Method
        document.getElementById('detailPaymentMethod').textContent = bill.payment_method;

        // Populate Payment Summary
        const paymentSummaryCard = document.getElementById('paymentSummaryCard');
        const detailTotalAmount = document.getElementById('detailTotalAmount');
        const detailPaidAmount = document.getElementById('detailPaidAmount');
        const detailBalanceRow = document.getElementById('detailBalanceRow');
        const detailBalanceAmount = document.getElementById('detailBalanceAmount');

        if (detailTotalAmount) {
            paymentSummaryCard.style.display = 'block';
            detailTotalAmount.textContent = '<?php echo e(currency_symbol()); ?>' + parseFloat(bill.grand_total).toFixed(2);
            detailPaidAmount.textContent = '<?php echo e(currency_symbol()); ?>' + parseFloat(bill.paid_amount || 0).toFixed(2);

            if (parseFloat(bill.outstanding_amount || 0) > 0) {
                detailBalanceRow.style.display = 'flex';
                detailBalanceAmount.textContent = '<?php echo e(currency_symbol()); ?>' + parseFloat(bill.outstanding_amount).toFixed(2);
            } else {
                detailBalanceRow.style.display = 'none';
            }
        }

        // Populate services
        const servicesBody = document.getElementById('detailServicesBody');
        servicesBody.innerHTML = '';
        bill.services.forEach(service => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="py-2 text-dark fw-medium">
                    ${service.name}
                </td>
                <td class="py-2 text-muted">
                    <i class="far fa-user me-1"></i> ${service.staff}
                </td>
                <td class="text-end py-2 fw-semibold text-dark"><?php echo e(currency_symbol()); ?>${parseFloat(service.total).toFixed(2)}</td>
            `;
            servicesBody.appendChild(row);
        });

        // Populate feedback
        const detailFeedbackBody = document.getElementById('detailFeedbackBody');
        if (bill.feedback_details) {
            if (bill.feedback_details.rating) {
                detailFeedbackBody.innerHTML = `
                    <div class="mb-2">
                        <strong>Rating:</strong>
                        ${generateStars(bill.feedback_details.rating)}
                    </div>
                    ${bill.feedback_details.review ? `<div><strong>Review:</strong><p class="mb-0 text-muted small">${bill.feedback_details.review}</p></div>` : ''}
                `;
            } else {
                detailFeedbackBody.innerHTML = '<p class="text-muted mb-0 small">No Feedback</p>';
            }
        } else {
            detailFeedbackBody.innerHTML = '<p class="text-muted mb-0 small">No Feedback</p>';
        }

        // Update active row highlighting
        const allRows = document.querySelectorAll('#billListBody tr');
        allRows.forEach(r => {
            r.classList.remove('table-active');
            r.style.backgroundColor = '';
            r.style.borderLeft = '';
        });

        if (selectedRow) {
            selectedRow.classList.add('table-active');
            selectedRow.style.backgroundColor = 'var(--primary-light)';
            selectedRow.style.borderLeft = '4px solid var(--primary-color)';
        }
    }

    function generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += '<i class="fas fa-star text-warning"></i> ';
            } else {
                stars += '<i class="far fa-star text-warning"></i> ';
            }
        }
        return stars;
    }

    function showError(message) {
        document.getElementById('billListLoading').style.display = 'none';
        document.getElementById('billListEmpty').style.display = 'block';
        document.getElementById('billListEmpty').innerHTML = `
            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
            <p class="text-danger">${message}</p>
        `;
    }
</script><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\customers\partials\bill_activity_modal.blade.php ENDPATH**/ ?>