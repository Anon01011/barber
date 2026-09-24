<?php $__env->startPush('components'); ?>
    <?php if (isset($component)) { $__componentOriginal19f69035b0f068003ea022c8543de37b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal19f69035b0f068003ea022c8543de37b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modals.edit-customer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modals.edit-customer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal19f69035b0f068003ea022c8543de37b)): ?>
<?php $attributes = $__attributesOriginal19f69035b0f068003ea022c8543de37b; ?>
<?php unset($__attributesOriginal19f69035b0f068003ea022c8543de37b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal19f69035b0f068003ea022c8543de37b)): ?>
<?php $component = $__componentOriginal19f69035b0f068003ea022c8543de37b; ?>
<?php unset($__componentOriginal19f69035b0f068003ea022c8543de37b); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginalfb3baf888dedf2e6e0b6af1f18b4c4a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfb3baf888dedf2e6e0b6af1f18b4c4a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modals.view-customer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modals.view-customer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfb3baf888dedf2e6e0b6af1f18b4c4a1)): ?>
<?php $attributes = $__attributesOriginalfb3baf888dedf2e6e0b6af1f18b4c4a1; ?>
<?php unset($__attributesOriginalfb3baf888dedf2e6e0b6af1f18b4c4a1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfb3baf888dedf2e6e0b6af1f18b4c4a1)): ?>
<?php $component = $__componentOriginalfb3baf888dedf2e6e0b6af1f18b4c4a1; ?>
<?php unset($__componentOriginalfb3baf888dedf2e6e0b6af1f18b4c4a1); ?>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('title', 'Customer Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="min-vh-100 bg-light">


        <!-- Header Section -->
        <div class="bg-white border-bottom">
            <div class="container-fluid px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-gradient-primary p-2 rounded-2 me-3">
                            <i class="fas fa-users text-white fs-5"></i>
                        </div>
                        <div>
                            <h1 class="h3 mb-0 text-dark fw-bold">Customers</h1>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm" id="exportBtn">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                            data-bs-target="#importCustomerModal">
                            <i class="fas fa-file-import me-2"></i>Import
                        </button>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                            <i class="fas fa-plus me-2"></i>Add Customer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-4 py-4">

            <!-- Customer Segmentation Cards -->
            <div class="row g-4 mb-4">
                <!-- Total Customers -->
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm segment-card active" data-segment=""
                        style="border-top: 4px solid #667eea !important; background-color: #f8f9fa;">
                        <div class="card-body text-center py-4">
                            <h2 class="display-4 fw-bold mb-1" style="color: #333;"><?php echo e($counts['total'] ?? 0); ?></h2>
                            <p class="text-muted mb-0 fw-semibold">Total Customers</p>
                        </div>
                    </div>
                </div>

                <!-- Active Customers -->
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm segment-card" data-segment="active"
                        style="border-top: 4px solid #198754 !important; background-color: #e8f5e9;">
                        <div class="card-body text-center py-4">
                            <h2 class="display-4 fw-bold mb-1" style="color: #333;"><?php echo e($counts['active'] ?? 0); ?></h2>
                            <p class="text-success mb-0 fw-semibold">Active Customers</p>
                        </div>
                    </div>
                </div>

                <!-- Churn Prediction -->
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm segment-card" data-segment="churn"
                        style="border-top: 4px solid #ffc107 !important; background-color: #fffde7;">
                        <div class="card-body text-center py-4">
                            <h2 class="display-4 fw-bold mb-1" style="color: #333;"><?php echo e($counts['churn'] ?? 0); ?></h2>
                            <p class="text-warning mb-0 fw-semibold">Churn Prediction</p>
                            <small class="text-muted" style="font-size: 0.75rem;">(Likely to be inactive)</small>
                        </div>
                    </div>
                </div>

                <!-- Defected Customers -->
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm segment-card" data-segment="defected"
                        style="border-top: 4px solid #dc3545 !important; background-color: #fbe9eb;">
                        <div class="card-body text-center py-4">
                            <h2 class="display-4 fw-bold mb-1" style="color: #333;"><?php echo e($counts['defected'] ?? 0); ?></h2>
                            <p class="text-danger mb-0 fw-semibold">Defected Customers</p>
                            <small class="text-muted" style="font-size: 0.75rem;">(Inactive customers)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Filters -->
            <div class="bg-white rounded-3 shadow-sm p-3 mb-4">
                <div class="row g-3 align-items-end">
                    <!-- Search Box -->
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-semibold text-dark mb-2">Search Customers</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-0 bg-light ps-0"
                                placeholder="Search by name, email, or phone..." id="searchCustomers">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-lg-2 col-md-3">
                        <label for="statusFilter" class="form-label fw-semibold text-dark mb-2">Status</label>
                        <select class="form-select border-0 bg-light" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="col-lg-3 col-md-3">
                        <label class="form-label fw-semibold text-dark mb-2">Sort By</label>
                        <select class="form-select border-0 bg-light" id="sortBy">
                            <option value="name_asc">Name (A-Z)</option>
                            <option value="name_desc">Name (Z-A)</option>
                            <option value="recent">Recently Added</option>
                            <option value="oldest">Oldest First</option>
                            <option value="bookings_desc">Most Bookings</option>
                            <option value="bookings_asc">Least Bookings</option>
                            <option value="last_visit_desc">Latest Visit</option>
                            <option value="last_visit_asc">Oldest Visit</option>
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <div class="col-lg-3 col-md-12">
                        <button class="btn btn-primary w-100" id="filterBtn">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Customers List -->
            <div class="bg-white rounded-3 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="border-0 fw-semibold text-dark ps-4 py-2 sortable" data-sort="name">
                                    Customer <i class="fas fa-sort ms-1 opacity-50"></i>
                                </th>
                                <th class="border-0 fw-semibold text-dark py-2">Contact</th>
                                <th class="border-0 fw-semibold text-dark py-2 sortable" data-sort="bookings">
                                    Bookings <i class="fas fa-sort ms-1 opacity-50"></i>
                                </th>
                                <th class="border-0 fw-semibold text-dark py-2 sortable" data-sort="last_visit">
                                    Last Visit <i class="fas fa-sort ms-1 opacity-50"></i>
                                </th>
                                <th class="border-0 fw-semibold text-dark py-2">Status</th>
                                <th class="border-0 fw-semibold text-dark text-end pe-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="customersList">
                            <!-- Data will be loaded dynamically -->
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="spinner-border text-primary mb-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="text-muted">Loading customers...</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="d-flex justify-content-between align-items-center p-3 bg-light border-top">
                    <div class="text-muted small">
                        <span class="fw-semibold">Showing</span>
                        <span id="listStart" class="fw-bold text-primary">0</span> to
                        <span id="listEnd" class="fw-bold text-primary">0</span> of
                        <span id="listTotal" class="fw-bold text-primary">0</span> customers
                    </div>
                    <nav aria-label="Customers pagination">
                        <ul class="pagination pagination-sm mb-0" id="customersPagination">
                            <!-- Pagination will be loaded dynamically -->
                        </ul>
                    </nav>
                </div>
            </div>

        </div>
    </div>

    <!-- Add Customer Modal -->
    <?php if (isset($component)) { $__componentOriginalcffcbf9b0c9f796771385fbc4b740d47 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcffcbf9b0c9f796771385fbc4b740d47 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modals.add-customer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modals.add-customer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcffcbf9b0c9f796771385fbc4b740d47)): ?>
<?php $attributes = $__attributesOriginalcffcbf9b0c9f796771385fbc4b740d47; ?>
<?php unset($__attributesOriginalcffcbf9b0c9f796771385fbc4b740d47); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcffcbf9b0c9f796771385fbc4b740d47)): ?>
<?php $component = $__componentOriginalcffcbf9b0c9f796771385fbc4b740d47; ?>
<?php unset($__componentOriginalcffcbf9b0c9f796771385fbc4b740d47); ?>
<?php endif; ?>



    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this customer? This action cannot be undone.</p>
                    <p class="text-muted small mb-0">All associated data will be permanently removed.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash-alt me-1"></i> Delete Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Customer Modal -->
    <div class="modal fade" id="importCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-primary text-white border-0">
                    <h5 class="modal-title">
                        <i class="fas fa-file-import me-2"></i>Import Customers
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Upload a CSV file with customer data.
                        <a href="<?php echo e(route('admin.customers.import-template')); ?>" class="alert-link">Download template</a>
                    </div>

                    <form id="importCustomerForm" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="importFile" class="form-label fw-semibold">CSV File</label>
                            <input type="file" class="form-control" id="importFile" name="file" accept=".csv" required>
                            <small class="form-text text-muted">
                                Required columns: Name, Email, Phone
                            </small>
                        </div>

                        <div id="importProgress" class="d-none">
                            <div class="progress mb-2">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                    style="width: 100%"></div>
                            </div>
                            <p class="text-center text-muted small mb-0">Importing customers...</p>
                        </div>

                        <div id="importResults" class="mt-3" style="display:none;"></div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" form="importCustomerForm" class="btn btn-primary" id="importBtn">
                        <i class="fas fa-upload me-1"></i>Import
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* Modern Customer Management Styles */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        /* Header Styles */
        .bg-white {
            background-color: #ffffff !important;
        }

        /* Avatar Styles */
        .avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
        }

        .avatar-sm {
            width: 2rem;
            height: 2rem;
            font-size: 0.875rem;
        }

        .avatar-lg {
            width: 3rem;
            height: 3rem;
            font-size: 1.25rem;
        }

        /* Badge Styles */
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .bg-primary-subtle {
            background-color: #e3f2fd !important;
            color: #0d6efd !important;
        }

        .bg-success-subtle {
            background-color: #d1e7dd !important;
            color: #198754 !important;
        }

        .bg-danger-subtle {
            background-color: #f8d7da !important;
            color: #dc3545 !important;
        }

        .bg-warning-subtle {
            background-color: #fff3cd !important;
            color: #856404 !important;
        }

        /* Table Styles */
        .table> :not(caption)>*>* {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            border-bottom: 2px solid #e9ecef;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Button Styles */
        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-outline-secondary {
            border-color: #e9ecef;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #495057;
        }

        /* Form Styles */
        .form-control,
        .form-select {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 0.75rem 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            background-color: #ffffff;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            color: #6c757d;
        }

        /* Card Styles */
        .rounded-3 {
            border-radius: 8px !important;
        }

        .shadow-sm {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        }

        /* Loading States */
        .spinner-border {
            width: 2rem;
            height: 2rem;
        }

        /* Pagination */
        .pagination .page-link {
            border-radius: 4px;
            border: 1px solid #e9ecef;
            color: #6c757d;
            margin: 0 2px;
        }

        .pagination .page-link:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #495057;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
        }

        /* Form Styles */
        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
        }

        /* Validation Styles */
        .was-validated .form-control:invalid,
        .form-control.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .was-validated .form-control:valid,
        .form-control.is-valid {
            border-color: #198754;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73 3.42-3.42c.39-.39 1.03-.39 1.42 0s.39 1.03 0 1.42L3.15 8.85c-.39.39-1.03.39-1.42 0L.29 6.15c-.39-.39-.39-1.03 0-1.42s1.03-.39 1.42 0z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .was-validated .form-select:invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
        }

        .was-validated .form-select:valid,
        .form-select.is-valid {
            border-color: #198754;
        }

        /* Error message styling */
        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        /* Modal Enhancements */
        .modal-content {
            border-radius: 1rem;
            overflow: hidden;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .modal-header {
            background-size: cover;
            background-position: center;
        }

        .modal-body {
            background: #f8f9fa;
        }

        /* Input Group Enhancements */
        .input-group-text {
            border-radius: 0.5rem 0 0 0.5rem;
            border-right: none;
        }

        .input-group .form-control,
        .input-group .form-select {
            border-radius: 0 0.5rem 0.5rem 0;
            border-left: none;
        }

        .input-group .form-control:focus,
        .input-group .form-select:focus {
            border-left: 1px solid #667eea;
        }

        /* Card Enhancements */
        .card {
            border-radius: 0.5rem;
        }

        /* Button Enhancements */
        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        /* Progress Bar Enhancement */
        .progress {
            border-radius: 2px;
            background-color: rgba(0, 0, 0, 0.1);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .table-responsive {
                margin: 0 -1rem;
            }

            .table> :not(caption)>*>* {
                padding: 0.75rem;
            }

            .avatar {
                width: 2rem;
                height: 2rem;
            }

            .avatar-lg {
                width: 2.5rem;
                height: 2.5rem;
            }

            .modal-dialog {
                margin: 0.5rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .input-group {
                flex-direction: column;
            }

            .input-group-text {
                border-radius: 0.375rem 0.375rem 0 0;
                border-bottom: none;
                border-right: 1px solid #dee2e6;
            }

            .input-group .form-control,
            .input-group .form-select {
                border-radius: 0 0 0.375rem 0.375rem;
                border-left: 1px solid #dee2e6;
                border-top: none;
            }
        }

        /* Segment Card Styles */
        .segment-card {
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .segment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .segment-card.active {
            ring: 2px solid #667eea;
            transform: translateY(-2px);
        }

        th.sortable {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        th.sortable:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        th.sortable i {
            font-size: 0.8rem;
        }

        th.sortable.active {
            color: #667eea !important;
        }

        th.sortable.active i {
            opacity: 1 !important;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    let currentPage = 1;
    let searchQuery = '';
    let statusFilter = '';
    let sortBy = 'name_asc';
    let segmentFilter = '';

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize loading state
        const tbody = document.getElementById('customersList');
        tbody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2 text-muted">Loading customers...</div>
            </td>
        </tr>
    `;

        loadCustomers();
        initializeEventListeners();
    });

    function initializeEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('searchCustomers');
        searchInput.addEventListener('input', debounce(function (e) {
            searchQuery = e.target.value;
            currentPage = 1;
            loadCustomers();
        }, 300));

        // Filter functionality
        document.getElementById('filterBtn').addEventListener('click', function () {
            applyFilters();
        });

        // Instant Filter on Dropdown Change
        document.getElementById('statusFilter').addEventListener('change', function () {
            applyFilters();
        });

        document.getElementById('sortBy').addEventListener('change', function () {
            applyFilters();
        });

        // Table Header Sorting
        document.querySelectorAll('th.sortable').forEach(th => {
            th.addEventListener('click', function () {
                const sortKey = this.dataset.sort;
                let newSort = '';

                // Toggle logic
                if (sortKey === 'name') {
                    newSort = sortBy === 'name_asc' ? 'name_desc' : 'name_asc';
                } else if (sortKey === 'bookings') {
                    newSort = sortBy === 'bookings_desc' ? 'bookings_asc' : 'bookings_desc';
                } else if (sortKey === 'last_visit') {
                    newSort = sortBy === 'last_visit_desc' ? 'last_visit_asc' : 'last_visit_desc';
                }

                if (newSort) {
                    sortBy = newSort;
                    document.getElementById('sortBy').value = sortBy;
                    applyFilters();
                }
            });
        });

        function applyFilters() {
            statusFilter = document.getElementById('statusFilter').value;
            sortBy = document.getElementById('sortBy').value;

            // Update table header active states
            updateHeaderSortIcons();

            currentPage = 1;
            loadCustomers();
        }

        function updateHeaderSortIcons() {
            document.querySelectorAll('th.sortable').forEach(th => {
                th.classList.remove('active');
                const icon = th.querySelector('i');
                icon.className = 'fas fa-sort ms-1 opacity-50';

                const sortKey = th.dataset.sort;
                if ((sortKey === 'name' && (sortBy === 'name_asc' || sortBy === 'name_desc')) ||
                    (sortKey === 'bookings' && (sortBy === 'bookings_asc' || sortBy === 'bookings_desc')) ||
                    (sortKey === 'last_visit' && (sortBy === 'last_visit_asc' || sortBy === 'last_visit_desc'))) {

                    th.classList.add('active');
                    icon.classList.remove('opacity-50');

                    if (sortBy.endsWith('_asc')) {
                        icon.className = 'fas fa-sort-up ms-1';
                    } else if (sortBy.endsWith('_desc')) {
                        icon.className = 'fas fa-sort-down ms-1';
                    }
                }
            });
        }

        // Segment Card functionality
        document.querySelectorAll('.segment-card').forEach(card => {
            card.addEventListener('click', function () {
                // Remove active class from all cards
                document.querySelectorAll('.segment-card').forEach(c => c.classList.remove('active'));

                // Add active class to clicked card
                this.classList.add('active');

                // Update filter
                segmentFilter = this.dataset.segment;
                currentPage = 1;
                loadCustomers();
            });
        });

        // Export functionality
        document.getElementById('exportBtn').addEventListener('click', function () {
            window.location.href = '<?php echo e(route("admin.customers.export")); ?>';
        });

        // Import functionality
        document.getElementById('importCustomerForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const importBtn = document.getElementById('importBtn');
            const importProgress = document.getElementById('importProgress');
            const importResults = document.getElementById('importResults');

            importBtn.disabled = true;
            importProgress.classList.remove('d-none');
            importResults.style.display = 'none';

            fetch('<?php echo e(route("admin.customers.import")); ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    importProgress.classList.add('d-none');
                    importBtn.disabled = false;

                    if (data.success) {
                        let html = `<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>${data.message}</div>`;

                        if (data.errors && data.errors.length > 0) {
                            html += `<div class="alert alert-warning"><strong>Errors:</strong><ul class="mb-0 mt-2">`;
                            data.errors.slice(0, 10).forEach(error => {
                                html += `<li>${error}</li>`;
                            });
                            if (data.errors.length > 10) {
                                html += `<li><em>... and ${data.errors.length - 10} more errors</em></li>`;
                            }
                            html += `</ul></div>`;
                        }

                        importResults.innerHTML = html;
                        importResults.style.display = 'block';

                        setTimeout(() => {
                            loadCustomers();
                            document.getElementById('importFile').value = '';
                            setTimeout(() => {
                                const modal = bootstrap.Modal.getInstance(document.getElementById('importCustomerModal'));
                                if (modal) modal.hide();
                                importResults.style.display = 'none';
                            }, 1500);
                        }, 2000);
                    } else {
                        importResults.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>${data.message}</div>`;
                        importResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    importProgress.classList.add('d-none');
                    importBtn.disabled = false;
                    importResults.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Import failed. Please try again.</div>`;
                    importResults.style.display = 'block';
                });
        });

        // Form submissions
        document.getElementById('addCustomerForm').addEventListener('submit', function (e) {
            e.preventDefault();
            if (this.checkValidity()) {
                addCustomer(this);
            } else {
                e.stopPropagation();
            }
            this.classList.add('was-validated');
        });

        // Handle conditional email requirement based on preferred_contact
        const preferredContactSelect = document.getElementById('preferredContactSelect');
        const emailInput = document.getElementById('emailInput');
        const emailLabel = document.getElementById('emailLabel');

        if (preferredContactSelect && emailInput) {
            preferredContactSelect.addEventListener('change', function () {
                if (this.value === 'email') {
                    emailInput.setAttribute('required', 'required');
                    emailLabel.innerHTML = '\u003ci class="fas fa-envelope me-1 text-primary"\u003e\u003c/i\u003eEmail Address \u003cspan class="text-danger"\u003e*\u003c/span\u003e';
                } else {
                    emailInput.removeAttribute('required');
                    emailLabel.innerHTML = '\u003ci class="fas fa-envelope me-1 text-primary"\u003e\u003c/i\u003eEmail Address';
                }
            });
        }
    }

    function loadCustomers() {
        // Show loading state
        const tbody = document.getElementById('customersList');
        tbody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2 text-muted">Loading customers...</div>
            </td>
        </tr>
    `;

        const url = new URL('<?php echo e(route("admin.customers.index")); ?>', window.location.origin);
        url.searchParams.append('page', currentPage);
        if (searchQuery) url.searchParams.append('search', searchQuery);
        if (statusFilter) url.searchParams.append('status', statusFilter);
        if (sortBy) url.searchParams.append('sort', sortBy);
        if (segmentFilter) url.searchParams.append('segment', segmentFilter);

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) {

            tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Security token not found. Please refresh the page.
                    </div>
                </td>
            </tr>
        `;
            return;
        }



        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token.content
            },
            credentials: 'same-origin'
        })
            .then(async response => {


                if (!response.ok) {
                    if (response.status === 401) {
                        window.location.href = '/login';
                        throw new Error('Please log in to continue');
                    }
                    if (response.status === 403) {
                        throw new Error('You do not have permission to access this page');
                    }

                    // Try to get the error message from the response
                    const text = await response.text();

                    throw new Error(text || 'Network response was not ok');
                }

                // Check content type
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {

                    throw new Error('Server returned invalid content type');
                }

                return response.json();
            })
            .then(data => {


                if (data.success) {
                    updateCustomersList(data.data);
                    updatePagination(data.data);
                } else {
                    throw new Error(data.message || 'Failed to load customers');
                }
            })
            .catch(error => {

                tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${error.message || 'Failed to load customers. Please try again.'}
                    </div>
                </td>
            </tr>
        `;
            });
    }

    function updateCustomersList(data) {
        const tbody = document.getElementById('customersList');
        tbody.innerHTML = '';

        if (!data.data || data.data.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        No customers found.
                    </div>
                </td>
            </tr>
        `;
            return;
        }

        data.data.forEach(customer => {
            // Generate initials
            const getInitials = (name) => {
                if (!name) return '?';
                const parts = name.trim().split(' ');
                if (parts.length >= 2) {
                    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
                }
                return name.substring(0, 2).toUpperCase();
            };

            const initials = getInitials(customer.name);

            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td class="ps-4">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3 text-white fw-bold">
                        ${initials}
                    </div>
                    <div>
                        <h6 class="mb-0">
                            ${escapeHtml(customer.name)}
                            ${customer.is_guest ? '<span class="badge bg-info text-dark ms-1">Guest</span>' : ''}
                        </h6>
                        <small class="text-muted">Member since ${new Date(customer.created_at).toLocaleDateString('en-US', { month: 'short', year: 'numeric' })}</small>
                    </div>
                </div>
            </td>
            <td>
                <div>
                    <div class="text-muted mb-1">
                        <i class="fas fa-envelope me-2"></i>${escapeHtml(customer.email)}
                    </div>
                    <div class="text-muted">
                        <i class="fas fa-phone me-2"></i>${escapeHtml(customer.phone)}
                    </div>
                </div>
            </td>
            <td>
                <span class="badge bg-primary-subtle text-primary">${customer.total_bookings || 0} bookings</span>
            </td>
            <td>
                <div class="text-muted">${customer.last_visit || 'Never'}</div>
            </td>
            <td>
                <span class="badge bg-${customer.status === 'active' ? 'success' : 'danger'}-subtle text-${customer.status === 'active' ? 'success' : 'danger'}">
                    ${customer.status.charAt(0).toUpperCase() + customer.status.slice(1)}
                </span>
            </td>
            <td class="text-end pe-4">
                <div class="btn-group">
                    <button class="btn btn-sm btn-light" onclick="viewCustomer(${customer.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-light" onclick="loadEditCustomerModal(${customer.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${customer.total_bookings > 0
                    ? `<button class="btn btn-sm btn-light text-muted" disabled title="Cannot delete customer with existing bookings">
                               <i class="fas fa-trash"></i>
                           </button>`
                    : `<button class="btn btn-sm btn-light text-danger" onclick="showDeleteConfirmation(${customer.id}, this)">
                               <i class="fas fa-trash"></i>
                           </button>`
                }
                </div>
            </td>
        `;
            tbody.appendChild(tr);
        });
    }

    function updatePagination(data) {
        const pagination = document.getElementById('customersPagination');
        pagination.innerHTML = '';

        // Update pagination info
        document.getElementById('listStart').textContent = data.from || 0;
        document.getElementById('listEnd').textContent = data.to || 0;
        document.getElementById('listTotal').textContent = data.total || 0;

        if (data.last_page <= 1) {
            return;
        }

        // Create pagination links
        for (let i = 1; i <= data.last_page; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === data.current_page ? 'active' : ''}`;
            li.innerHTML = `
            <button class="page-link" onclick="changePage(${i})">${i}</button>
        `;
            pagination.appendChild(li);
        }
    }

    function changePage(page) {
        currentPage = page;
        loadCustomers();
    }

    function addCustomer(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        fetch('<?php echo e(route("admin.customers.store")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    if (response.status === 422) {
                        // Handle validation errors
                        const errors = data.errors;
                        let errorMessage = 'Please fix the following errors:\n';
                        for (const field in errors) {
                            errorMessage += `\n${field}: ${errors[field].join(', ')}`;
                        }
                        throw new Error(errorMessage);
                    }
                    throw new Error(data.message || 'Failed to add customer');
                }
                return data;
            })
            .then(data => {
                if (data.status === 'success') {
                    window.notifications.show('Customer added successfully', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('addCustomerModal')).hide();
                    form.reset();
                    loadCustomers();
                } else {
                    window.notifications.show(data.message || 'Failed to add customer', 'error');
                }
            })
            .catch(error => {

                window.notifications.show(error.message || 'Failed to add customer', 'error');
            });
    }



    // Edit customer function now uses the component modal
    // The function is now handled by the edit-customer component

    let customerToDelete = null;
    let deleteButton = null;

    function showDeleteConfirmation(id, button) {
        customerToDelete = id;
        deleteButton = button;
        const modalElement = document.getElementById('deleteConfirmationModal');
        let modal = bootstrap.Modal.getInstance(modalElement);
        if (!modal) {
            modal = new bootstrap.Modal(modalElement);
        }
        modal.show();
    }

    function deleteCustomer() {
        if (!customerToDelete) return;

        // Show loading state on the delete button in modal
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const originalBtnText = confirmBtn.innerHTML;
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Deleting...';

        fetch(`<?php echo e(route('admin.customers.index')); ?>/${customerToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
            .then(async response => {
                const text = await response.text();
                try {
                    // Try to parse the response as JSON
                    const data = text ? JSON.parse(text) : {};

                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to delete customer');
                    }

                    return data;
                } catch (e) {
                    // If not valid JSON but the response was successful, return empty data
                    if (response.ok) {
                        return {};
                    }
                    // If there was an error and we couldn't parse the response
                    throw new Error(text || 'Failed to delete customer');
                }
            })
            .then(data => {
                // Close the confirmation modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmationModal'));
                if (modal) modal.hide();

                // Show success message
                window.notifications.show('Customer deleted successfully', 'success');

                // Close any open customer detail modals
                const detailsModal = bootstrap.Modal.getInstance(document.getElementById('customerDetailsModal'));
                if (detailsModal) detailsModal.hide();

                // Refresh the customers list
                loadCustomers();
            })
            .catch(error => {

                window.notifications.show(error.message || 'Failed to delete customer. Please try again.', 'error');
            })
            .finally(() => {
                // Reset the modal button state
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = originalBtnText;
                }

                // Reset the original delete button state if it exists
                if (deleteButton) {
                    deleteButton.disabled = false;
                    deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
                }

                // Reset variables
                customerToDelete = null;
                deleteButton = null;
            });
    }

    // Add event listener for the confirmation button
    document.addEventListener('DOMContentLoaded', function () {
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', deleteCustomer);
        }
    });


    // Helper function for debouncing
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Helper function to escape HTML and prevent XSS
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow">
            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <div class="w-100 text-center">
                    <h5 class="modal-title fw-bold">SALON NAME</h5>
                    <p class="text-muted small mb-0">123 Beauty Street, City</p>
                    <p class="text-muted small">Phone: (123) 456-7890</p>
                </div>
                <button type="button" class="btn-close position-absolute" style="right: 1rem; top: 1rem;"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4">
                <!-- Receipt Info -->
                <div class="text-center mb-4">
                    <p class="mb-1"><small class="text-muted">Receipt #</small> <span id="receiptNumber"
                            class="fw-semibold">RCPT-001234</span></p>
                    <p class="mb-0"><small class="text-muted" id="receiptDate">October 8, 2025 09:42 PM</small></p>
                    <hr class="my-3">
                </div>

                <!-- Customer Info -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Customer:</span>
                        <span id="receiptCustomer" class="fw-medium">John Doe</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Contact:</span>
                        <span id="receiptContact">(555) 123-4567</span>
                    </div>
                </div>

                <!-- Services -->
                <h6 class="fw-bold border-bottom pb-2 mb-3">Services</h6>
                <div id="receiptServices">
                    <!-- Service items will be added here dynamically -->
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <span class="d-block">Haircut</span>
                            <small class="text-muted">30 min</small>
                        </div>
                        <span><?php echo e(currency_symbol()); ?>35.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <span class="d-block">Hair Coloring</span>
                            <small class="text-muted">1h 30min</small>
                        </div>
                        <span><?php echo e(currency_symbol()); ?>85.00</span>
                    </div>
                </div>

                <!-- Totals -->
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span><?php echo e(currency_symbol()); ?>120.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax (10%):</span>
                        <span><?php echo e(currency_symbol()); ?>12.00</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold fs-5 mt-3">
                        <span>Total:</span>
                        <span><?php echo e(currency_symbol()); ?>132.00</span>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Payment Method:</span>
                        <span>Credit Card</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Card:</span>
                        <span>•••• 4242</span>
                    </div>
                </div>

                <!-- Thank You -->
                <div class="text-center mt-4 pt-3 border-top">
                    <p class="mb-2">Thank you for your visit!</p>
                    <p class="small text-muted mb-0">We hope to see you again soon</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="printReceiptBtn">
                    <i class="fas fa-print me-1"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle receipt printing -->
<?php $__env->startPush('scripts'); ?>
    <script>
        // Function to show receipt modal
        function showReceipt(bookingId) {
            // Here you would typically fetch the booking details
            // For now, we'll just show the modal with sample data

            // Set current date and time
            const now = new Date();
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            };
            document.getElementById('receiptDate').textContent = now.toLocaleDateString('en-US', options);

            // Show the modal
            const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
            receiptModal.show();

            // Handle print button click
            document.getElementById('printReceiptBtn').onclick = function () {
                window.print();
            };
        }
    </script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('admin.customers.partials.bill_activity_modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\customers\index.blade.php ENDPATH**/ ?>