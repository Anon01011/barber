<?php $__env->startSection('title', 'Create Package'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1 class="h3 mb-1 fw-bold text-gray-800">Create New Package</h1>
                <p class="text-muted mb-0">Design a new service package for your customers.</p>
            </div>
            <div class="col-auto">
                <a href="<?php echo e(route('admin.packages.index')); ?>" class="btn btn-light text-secondary fw-medium">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </div>

        <form action="<?php echo e(route('admin.packages.store')); ?>" method="POST" class="needs-validation" novalidate>
            <?php echo csrf_field(); ?>
            <div class="row g-4">
                <!-- Left Column: Form Inputs -->
                <div class="col-lg-8">

                    <!-- Basic Info -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold mb-4 text-primary"><i class="fas fa-info-circle me-2"></i>Basic
                                Details</h5>

                            <div class="mb-4">
                                <label for="name" class="form-label text-uppercase text-muted small fw-bold">Package Name
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg bg-light border-0" id="name"
                                    name="name" value="<?php echo e(old('name')); ?>" placeholder="e.g., Summer Glow Package" required>
                                <div class="invalid-feedback">Please provide a package name.</div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label class="form-label text-uppercase text-muted small fw-bold mb-3">Package Type
                                        <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check custom-card-radio flex-fill">
                                            <input class="form-check-input" type="radio" name="type" id="fixed"
                                                value="fixed" <?php echo e(old('type', 'fixed') == 'fixed' ? 'checked' : ''); ?> required>
                                            <label
                                                class="form-check-label p-3 border rounded-3 w-100 d-flex align-items-center cursor-pointer transition-all"
                                                for="fixed">
                                                <div
                                                    class="icon-box bg-primary-subtle text-primary rounded-circle me-3 p-2">
                                                    <i class="fas fa-lock fa-lg"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block fw-bold text-dark">Fixed Package</span>
                                                    <small class="text-muted">Pre-defined set of services.</small>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="form-check custom-card-radio flex-fill">
                                            <input class="form-check-input" type="radio" name="type" id="customizable"
                                                value="customizable" <?php echo e(old('type') == 'customizable' ? 'checked' : ''); ?>

                                                required>
                                            <label
                                                class="form-check-label p-3 border rounded-3 w-100 d-flex align-items-center cursor-pointer transition-all"
                                                for="customizable">
                                                <div
                                                    class="icon-box bg-success-subtle text-success rounded-circle me-3 p-2">
                                                    <i class="fas fa-sliders-h fa-lg"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block fw-bold text-dark">Customizable</span>
                                                    <small class="text-muted">Customer chooses services.</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4" id="service_limit_container" style="display: none;">
                                <label for="service_limit"
                                    class="form-label text-uppercase text-muted small fw-bold">Service Limit</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i
                                            class="fas fa-list-ol text-primary"></i></span>
                                    <input type="number" class="form-control form-control-lg bg-light border-0"
                                        id="service_limit" name="service_limit" value="<?php echo e(old('service_limit')); ?>"
                                        placeholder="Max services customer can pick (e.g., 2)" min="1">
                                </div>
                                <small class="text-muted">For customizable packages, specify how many services a customer
                                    can choose.</small>
                            </div>

                            <div class="mb-0">
                                <label for="description"
                                    class="form-label text-uppercase text-muted small fw-bold">Description</label>
                                <textarea class="form-control bg-light border-0" id="description" name="description"
                                    rows="3"
                                    placeholder="Describe what's included in this package..."><?php echo e(old('description')); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Services Selection -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-visible" style="z-index: 10;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="card-title fw-bold mb-0 text-primary"><i class="fas fa-list-ul me-2"></i>Included
                                    Services</h5>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2"
                                    id="service-count-badge">0 Selected</span>
                            </div>

                            <div class="position-relative">
                                <div class="input-group input-group-lg mb-3 shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-0 ps-3"><i
                                            class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control border-0" id="service-search-trigger"
                                        placeholder="Search and add services..." autocomplete="off">
                                    <button class="btn btn-primary px-4" type="button"
                                        id="browse-services-btn">Browse</button>
                                </div>

                                <!-- Custom Dropdown Menu -->
                                <div class="custom-dropdown-menu shadow-lg rounded-4 p-0 border-0" id="services-dropdown"
                                    style="display: none; position: absolute; width: 100%; top: 100%; left: 0; z-index: 1000; background: white; max-height: 400px; overflow: hidden;">
                                    <div class="p-3 border-bottom bg-light">
                                        <small class="text-muted fw-bold text-uppercase">Available Services</small>
                                    </div>
                                    <div class="services-list-container custom-scrollbar"
                                        style="max-height: 300px; overflow-y: auto;">
                                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="service-option p-3 border-bottom cursor-pointer hover-bg-light transition-all"
                                                data-id="<?php echo e($service->id); ?>" data-name="<?php echo e($service->name); ?>"
                                                data-price="<?php echo e($service->price); ?>"
                                                data-formatted-price="<?php echo e(format_currency($service->price)); ?>">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold text-dark"><?php echo e($service->name); ?></div>
                                                        <small
                                                            class="text-muted"><?php echo e($service->category->name ?? 'General'); ?></small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span
                                                            class="badge bg-light text-dark border"><?php echo e(format_currency($service->price)); ?></span>
                                                        <i
                                                            class="fas fa-plus-circle text-primary ms-2 opacity-0 icon-add transition-all"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($services->isEmpty()): ?>
                                            <div class="p-4 text-center text-muted">No services found.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div id="services-error" class="text-danger small mt-2 d-none"><i
                                    class="fas fa-exclamation-circle me-1"></i> Please select at least one service.</div>
                        </div>
                    </div>

                    <!-- Validity & Status -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold mb-4 text-primary"><i class="fas fa-clock me-2"></i>Validity &
                                Status</h5>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label text-uppercase text-muted small fw-bold">Duration <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-lg bg-light border-0"
                                            id="validity_value" name="validity_value"
                                            value="<?php echo e(old('validity_value', 30)); ?>" min="1" required>
                                        <select class="form-select form-select-lg bg-light border-0" id="validity_unit"
                                            name="validity_unit" style="max-width: 120px;">
                                            <option value="days" <?php echo e(old('validity_unit') == 'days' ? 'selected' : ''); ?>>Days
                                            </option>
                                            <option value="weeks" <?php echo e(old('validity_unit') == 'weeks' ? 'selected' : ''); ?>>
                                                Weeks</option>
                                            <option value="months" <?php echo e(old('validity_unit') == 'months' ? 'selected' : ''); ?>>
                                                Months</option>
                                            <option value="years" <?php echo e(old('validity_unit') == 'years' ? 'selected' : ''); ?>>
                                                Years</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-uppercase text-muted small fw-bold">Status</label>
                                    <div class="form-check form-switch p-3 bg-light rounded-3 border-0">
                                        <input class="form-check-input ms-0 me-3" type="checkbox" id="is_active"
                                            name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

                                            style="width: 3em; height: 1.5em;">
                                        <label class="form-check-label fw-bold pt-1" for="is_active">Active Package</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Summary & Pricing -->
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 20px; z-index: 5;">

                        <!-- Selected Services List -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                            <div class="card-header bg-white border-bottom p-3">
                                <h6 class="fw-bold mb-0 text-uppercase small text-muted">Package Contents</h6>
                            </div>
                            <div class="card-body p-0">
                                <div id="selected-services-list" class="custom-scrollbar"
                                    style="max-height: 400px; overflow-y: auto;">
                                    <!-- Empty State -->
                                    <div id="empty-state" class="text-center py-5 px-4">
                                        <div class="mb-3 text-muted opacity-50">
                                            <i class="fas fa-box-open fa-3x"></i>
                                        </div>
                                        <h6 class="fw-bold text-muted">No Services Added</h6>
                                        <p class="small text-muted mb-0">Search and add services to build your package.</p>
                                    </div>
                                    <!-- Items will be injected here -->
                                </div>
                            </div>
                            <div class="card-footer bg-light p-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small fw-bold text-uppercase">Total Value</span>
                                    <span class="fw-bold fs-5 text-dark"
                                        id="calculated-total"><?php echo e(format_currency(0)); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Configuration -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-4 text-primary"><i class="fas fa-tag me-2"></i>Pricing</h5>

                                <div class="mb-3">
                                    <label for="special_price"
                                        class="form-label text-uppercase text-muted small fw-bold">Package Price</label>
                                    <div class="input-group input-group-lg">
                                        <span
                                            class="input-group-text border-0 bg-primary text-white fw-bold"><?php echo e(currency_symbol()); ?></span>
                                        <input type="number" step="0.01" class="form-control border-0 bg-light fw-bold"
                                            id="special_price" name="special_price" value="<?php echo e(old('special_price')); ?>"
                                            placeholder="0.00">
                                    </div>
                                    <div id="price-warning" class="text-danger small mt-1 d-none">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Price cannot exceed total value (<span id="max-price-hint">0.00</span>)
                                    </div>
                                    <div class="form-text small">Leave empty to use calculated total.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label text-uppercase text-muted small fw-bold">Tax
                                        Rate <span class="text-danger">*</span></label>
                                    <select class="form-select border-0 bg-light" id="tax_rate" name="tax_rate" required>
                                        <option value="">Select Tax...</option>
                                        <?php $__currentLoopData = $taxRates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($rate); ?>" <?php echo e(old('tax_rate') == $rate ? 'selected' : ''); ?>>
                                                <?php echo e($rate); ?>%
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <input type="hidden" id="price" name="price" value="0">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="btn btn-primary btn-lg w-100 py-3 rounded-3 fw-bold shadow-sm hover-scale transition-all">
                            <i class="fas fa-check-circle me-2"></i> Create Package
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php $__env->startPush('styles'); ?>
        <style>
            /* Custom Scrollbar */
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #d1d5db;
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }

            /* Radio Card Styling */
            .custom-card-radio .form-check-input {
                display: none;
            }

            .custom-card-radio .form-check-label {
                transition: all 0.2s ease;
                border: 2px solid transparent !important;
                background-color: #fff;
            }

            .custom-card-radio .form-check-input:checked+.form-check-label {
                border-color: var(--bs-primary) !important;
                background-color: var(--bs-primary-bg-subtle);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            /* Service Option Hover */
            .service-option:hover {
                background-color: #f8f9fa;
            }

            .service-option:hover .icon-add {
                opacity: 1 !important;
            }

            /* Animations */
            .hover-scale:hover {
                transform: translateY(-2px);
            }

            .transition-all {
                transition: all 0.2s ease-in-out;
            }

            /* Input Styling */
            .form-control:focus,
            .form-select:focus {
                box-shadow: none;
                border-color: var(--bs-primary);
                background-color: #fff !important;
            }

            /* Selected Service Item Animation */
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            .selected-service-item {
                animation: slideIn 0.3s ease-out forwards;
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Elements
                const serviceSearchTrigger = document.getElementById('service-search-trigger');
                const servicesDropdown = document.getElementById('services-dropdown');
                const serviceOptions = document.querySelectorAll('.service-option');
                const selectedServicesList = document.getElementById('selected-services-list');
                const emptyState = document.getElementById('empty-state');
                const calculatedTotalEl = document.getElementById('calculated-total');
                const serviceCountBadge = document.getElementById('service-count-badge');
                const form = document.querySelector('form.needs-validation');

                // State
                let selectedServices = [];
                let isDropdownOpen = false;

                // Toggle Dropdown
                function toggleDropdown(show) {
                    isDropdownOpen = show;
                    servicesDropdown.style.display = show ? 'block' : 'none';
                }

                // Search Filter
                serviceSearchTrigger.addEventListener('input', function (e) {
                    const term = e.target.value.toLowerCase();
                    toggleDropdown(true);

                    let hasResults = false;
                    serviceOptions.forEach(option => {
                        const name = option.dataset.name.toLowerCase();
                        if (name.includes(term)) {
                            option.style.display = 'block';
                            hasResults = true;
                        } else {
                            option.style.display = 'none';
                        }
                    });
                });

                serviceSearchTrigger.addEventListener('focus', () => toggleDropdown(true));
                document.getElementById('browse-services-btn').addEventListener('click', () => {
                    serviceSearchTrigger.focus();
                    toggleDropdown(true);
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function (e) {
                    if (!e.target.closest('.position-relative')) {
                        toggleDropdown(false);
                    }
                });

                // Add Service
                serviceOptions.forEach(option => {
                    option.addEventListener('click', function () {
                        const id = this.dataset.id;
                        const name = this.dataset.name;
                        const price = parseFloat(this.dataset.price);
                        const formattedPrice = this.dataset.formattedPrice;

                        addService(id, name, price, formattedPrice);
                        // toggleDropdown(false); // Keep open for multiple selection
                        serviceSearchTrigger.value = '';
                        serviceSearchTrigger.focus(); // Keep focus
                        // Reset search
                        serviceOptions.forEach(opt => opt.style.display = 'block');
                    });
                });

                function addService(id, name, price, formattedPrice) {
                    // Check if already exists
                    if (selectedServices.find(s => s.id === id)) {
                        // Flash existing item?
                        return;
                    }

                    const service = { id, name, price, formattedPrice, quantity: 1 };
                    selectedServices.push(service);
                    renderServices();
                }

                function removeService(id) {
                    selectedServices = selectedServices.filter(s => s.id !== id);
                    renderServices();
                }

                function updateQuantity(id, qty) {
                    const service = selectedServices.find(s => s.id === id);
                    if (service) {
                        service.quantity = parseInt(qty) || 1;
                        renderServices(false); // Don't full re-render, just update totals
                    }
                }

                function renderServices(fullRender = true) {
                    if (fullRender) {
                        selectedServicesList.innerHTML = '';

                        if (selectedServices.length === 0) {
                            selectedServicesList.appendChild(emptyState);
                            emptyState.style.display = 'block';
                        } else {
                            emptyState.style.display = 'none';

                            selectedServices.forEach((service, index) => {
                                const item = document.createElement('div');
                                item.className = 'selected-service-item p-3 border-bottom bg-white position-relative';
                                item.innerHTML = `
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="fw-bold text-dark text-truncate pe-2" style="max-width: 70%;">${service.name}</div>
                                                    <div class="text-primary fw-bold small">${service.formattedPrice}</div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <label class="small text-muted me-2">Qty:</label>
                                                        <input type="number" class="form-control form-control-sm border-light bg-light text-center p-1 quantity-input" 
                                                               value="${service.quantity}" min="1" max="50" style="width: 60px;" data-id="${service.id}">
                                                    </div>
                                                    <button type="button" class="btn btn-link text-danger p-0 text-decoration-none small remove-btn" data-id="${service.id}">
                                                        <i class="fas fa-trash-alt"></i> Remove
                                                    </button>
                                                </div>
                                            `;
                                selectedServicesList.appendChild(item);
                            });
                        }
                    }

                    // Update Totals
                    const total = selectedServices.reduce((sum, s) => sum + (s.price * s.quantity), 0);
                    const currencySymbol = "<?php echo e(currency_symbol()); ?>";
                    calculatedTotalEl.textContent = currencySymbol + total.toFixed(2);

                    // Update Badge
                    serviceCountBadge.textContent = `${selectedServices.length} Selected`;

                    // Update Hidden Inputs
                    updateHiddenInputs();

                    // Bind Events for new elements
                    if (fullRender) {
                        document.querySelectorAll('.remove-btn').forEach(btn => {
                            btn.addEventListener('click', function () {
                                removeService(this.dataset.id);
                            });
                        });
                        document.querySelectorAll('.quantity-input').forEach(input => {
                            input.addEventListener('change', function () {
                                updateQuantity(this.dataset.id, this.value);
                            });
                        });
                    }
                }

                function updateHiddenInputs() {
                    // Clear old inputs
                    document.querySelectorAll('.service-hidden-input').forEach(el => el.remove());

                    const container = form;
                    selectedServices.forEach((service, index) => {
                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = `services[${index}]`;
                        idInput.value = service.id;
                        idInput.className = 'service-hidden-input';
                        container.appendChild(idInput);

                        const qtyInput = document.createElement('input');
                        qtyInput.type = 'hidden';
                        qtyInput.name = `quantity[${index}]`;
                        qtyInput.value = service.quantity;
                        qtyInput.className = 'service-hidden-input';
                        container.appendChild(qtyInput);
                    });

                    // Update price hidden input
                    const total = selectedServices.reduce((sum, s) => sum + (s.price * s.quantity), 0);
                    const specialPriceInput = document.getElementById('special_price');
                    const specialPrice = parseFloat(specialPriceInput.value) || 0;
                    document.getElementById('price').value = specialPrice > 0 ? specialPrice : total;

                    // Frontend validation: check if special price > total
                    const priceWarning = document.getElementById('price-warning');
                    const maxPriceHint = document.getElementById('max-price-hint');

                    if (specialPrice > total && total > 0) {
                        priceWarning.classList.remove('d-none');
                        maxPriceHint.textContent = total.toFixed(2);
                        specialPriceInput.setCustomValidity("Price cannot be higher than services total");
                    } else {
                        priceWarning.classList.add('d-none');
                        specialPriceInput.setCustomValidity("");
                    }
                }

                // Form Validation
                form.addEventListener('submit', function (e) {
                    if (selectedServices.length === 0) {
                        e.preventDefault();
                        document.getElementById('services-error').classList.remove('d-none');
                        return false;
                    }

                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }

                    form.classList.add('was-validated');
                });

                // Toggle service limit field based on package type
                const typeRadios = document.querySelectorAll('input[name="type"]');
                const serviceLimitContainer = document.getElementById('service_limit_container');

                function toggleServiceLimit() {
                    const customizable = document.getElementById('customizable').checked;
                    serviceLimitContainer.style.display = customizable ? 'block' : 'none';
                    if (!customizable) {
                        document.getElementById('service_limit').value = '';
                    }
                }

                typeRadios.forEach(radio => {
                    radio.addEventListener('change', toggleServiceLimit);
                });

                // Initial check
                toggleServiceLimit();

                // Price Update
                document.getElementById('special_price').addEventListener('input', updateHiddenInputs);

            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\packages\create.blade.php ENDPATH**/ ?>