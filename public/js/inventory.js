// Image preview functionality
function previewImage(input) {
    if (!input.files || !input.files[0]) return;

    const preview = document.getElementById('imagePreview');
    const previewContainer = document.querySelector('.image-preview');

    if (preview && previewContainer) {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        };

        reader.readAsDataURL(input.files[0]);
    }
}

// Global function to validate prices
window.validatePrices = function () {
    const purchasePrice = parseFloat(document.getElementById('purchase_price')?.value) || 0;
    const sellingPrice = parseFloat(document.getElementById('selling_price')?.value) || 0;
    const sellingPriceField = document.getElementById('selling_price');

    if (!sellingPriceField) return true;

    // Clear previous errors
    sellingPriceField.classList.remove('is-invalid');
    const existingError = sellingPriceField.parentNode.querySelector('.invalid-feedback');
    if (existingError) existingError.remove();

    if (sellingPrice < purchasePrice) {
        sellingPriceField.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = 'Selling price must be greater than or equal to purchase price';
        sellingPriceField.parentNode.appendChild(errorDiv);
        return false;
    }

    return true;
};

// Function to calculate profit margin and markup
function updatePriceCalculations() {
    const purchasePrice = parseFloat(document.getElementById('purchase_price')?.value) || 0;
    const sellingPrice = parseFloat(document.getElementById('selling_price')?.value) || 0;
    const taxRate = parseFloat(document.getElementById('tax_rate')?.value) || 0;

    // Calculate profit
    const profit = sellingPrice - purchasePrice;

    // Calculate profit margin (as percentage of selling price)
    const profitMargin = sellingPrice > 0 ? (profit / sellingPrice) * 100 : 0;

    // Calculate markup (as percentage of cost)
    const markup = purchasePrice > 0 ? (profit / purchasePrice) * 100 : 0;

    // Calculate tax amount
    const taxAmount = (sellingPrice * taxRate) / 100;

    // Update UI with null checks
    const updateIfExists = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    };

    updateIfExists('profit-amount', profit.toFixed(2));
    updateIfExists('profit-margin', `${profitMargin.toFixed(2)}%`);
    updateIfExists('markup', `${markup.toFixed(2)}%`);
    updateIfExists('tax-amount', taxAmount.toFixed(2));
    updateIfExists('total-price', (sellingPrice + taxAmount).toFixed(2));
}

document.addEventListener('DOMContentLoaded', function () {
    // Define the tab IDs in order (will be populated from DOM)
    let tabs = [];
    const progressBar = document.querySelector('.progress-bar');

    // Initialize price calculation event listeners
    const priceInputs = ['purchase_price', 'selling_price', 'tax_rate'];

    // Initialize tabs array based on tab panes within the inventory form
    let tabPanes = null;
    const tabContent = document.getElementById('inventoryFormTabsContent');
    if (tabContent) {
        tabPanes = tabContent.querySelectorAll('.tab-pane');
        tabPanes.forEach(pane => {
            if (pane.id) {
                tabs.push(pane.id);
            }
        });
    }

    // Function to update progress bar
    function updateProgress(currentTabId) {
        if (!progressBar) return;

        const currentIndex = tabs.indexOf(currentTabId);
        if (currentIndex === -1) return;

        const progress = ((currentIndex + 1) / tabs.length) * 100;
        progressBar.style.width = `${progress}%`;
        progressBar.setAttribute('aria-valuenow', progress);
    }

    // Function to update navigation button visibility
    function updateNavigationButtons() {
        const tabContent = document.getElementById('inventoryFormTabsContent');
        if (!tabContent) return;

        const activeTab = tabContent.querySelector('.tab-pane.active');
        if (!activeTab) return;

        const activeTabId = activeTab.id;
        const currentIndex = tabs.indexOf(activeTabId);
        if (currentIndex === -1) return;

        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const saveBtn = document.getElementById('saveBtn');

        if (!prevBtn || !nextBtn || !saveBtn) return;

        // Show/hide Previous button
        if (currentIndex === 0) {
            prevBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'inline-block';
            prevBtn.setAttribute('data-prev-tab', tabs[currentIndex - 1]);
        }

        // Show/hide Next/Save buttons
        if (currentIndex === tabs.length - 1) {
            // Last tab: show Save, hide Next
            nextBtn.style.display = 'none';
            saveBtn.style.display = 'inline-block';
        } else {
            // Other tabs: show Next, hide Save
            nextBtn.style.display = 'inline-block';
            nextBtn.setAttribute('data-next-tab', tabs[currentIndex + 1]);
            saveBtn.style.display = 'none';
        }
    }

    priceInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', function () {
                if (typeof validatePrices === 'function') validatePrices();
                if (typeof updatePriceCalculations === 'function') updatePriceCalculations();
            });
        }
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize image preview for main image
    const mainImageInput = document.getElementById('main_image');
    if (mainImageInput) {
        mainImageInput.addEventListener('change', function () {
            previewImage(this);
        });
    }

    // Tab navigation elements
    const tabTriggers = document.querySelectorAll('[data-bs-toggle="pill"], [data-bs-toggle="tab"]');

    // Function to show a specific tab using Bootstrap's tab system
    function showTab(tabId) {
        const tabTrigger = document.querySelector(`[data-bs-target="#${tabId}"], [href="#${tabId}"]`);
        if (!tabTrigger) return false;

        const bsTab = new bootstrap.Tab(tabTrigger);
        bsTab.show();

        // Update progress and buttons after show
        setTimeout(() => {
            updateProgress(tabId);
            updateNavigationButtons();

            // Scroll to top of the form
            const modalBody = document.querySelector('.modal-body');
            if (modalBody) modalBody.scrollTop = 0;

            // Focus on first input in the new tab
            const nextTab = document.getElementById(tabId);
            const firstInput = nextTab ? nextTab.querySelector('input, select, textarea') : null;
            if (firstInput) {
                firstInput.focus();
            }
        }, 100);

        return true;
    }

    // Function to validate current tab fields
    function validateCurrentTab(tabId) {
        const tabElement = document.getElementById(tabId);
        if (!tabElement) return true;

        let isValid = true;
        const requiredFields = tabElement.querySelectorAll('[required]');

        // Reset all invalid states first
        tabElement.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        tabElement.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        // Validate each required field
        requiredFields.forEach(field => {
            const value = field.value ? field.value.trim() : '';
            let fieldIsValid = true;

            // Check if field is empty
            if (!value) {
                fieldIsValid = false;
            }
            // Additional validation for email fields
            else if (field.type === 'email' && !isValidEmail(value)) {
                fieldIsValid = false;
            }

            if (!fieldIsValid) {
                field.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';

                if (field.type === 'email' && value) {
                    errorDiv.textContent = 'Please enter a valid email address';
                } else {
                    errorDiv.textContent = field.dataset.error || 'This field is required';
                }

                field.parentNode.insertBefore(errorDiv, field.nextSibling);
                isValid = false;
            }
        });

        // Special validation for pricing tab
        if (tabId === 'pricing') {
            if (!window.validatePrices()) {
                isValid = false;
            }
        }

        // If there are validation errors, scroll to the first one
        if (!isValid) {
            const firstError = tabElement.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }

            // Show error message in the form status
            const formStatus = document.getElementById('formStatus');
            if (formStatus) {
                formStatus.classList.remove('d-none', 'alert-success');
                formStatus.classList.add('alert-danger');
                formStatus.innerHTML = `
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Please fill in all required fields correctly.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
            }
        }

        return isValid;
    }

    // Function to get the next tab ID
    function getNextTabId(currentTabId) {
        const currentIndex = tabs.indexOf(currentTabId);
        if (currentIndex < tabs.length - 1) {
            return tabs[currentIndex + 1];
        }
        return null;
    }

    // Function to get the previous tab ID
    function getPrevTabId(currentTabId) {
        const currentIndex = tabs.indexOf(currentTabId);
        if (currentIndex > 0) {
            return tabs[currentIndex - 1];
        }
        return null;
    }

    // Handle next/previous tab buttons
    document.addEventListener('click', function (e) {
        // Handle next button
        if (e.target.closest('.next-tab')) {
            e.preventDefault();
            e.stopPropagation();

            const button = e.target.closest('.next-tab');
            const currentTab = document.querySelector('.tab-pane.active');
            const currentTabId = currentTab ? currentTab.id : tabs[0];
            const nextTabId = button.getAttribute('data-next-tab') || getNextTabId(currentTabId);

            if (nextTabId) {
                // Validate current tab before proceeding
                const isValid = validateCurrentTab(currentTabId);

                if (isValid) {
                    // Clear any previous error messages
                    const formStatus = document.getElementById('formStatus');
                    if (formStatus) {
                        formStatus.classList.add('d-none');
                        formStatus.classList.remove('alert-danger');
                    }

                    // Show the next tab
                    if (showTab(nextTabId)) {
                        // Focus on first input in the new tab for better UX
                        const nextTab = document.getElementById(nextTabId);
                        const firstInput = nextTab ? nextTab.querySelector('input, select, textarea') : null;
                        if (firstInput) {
                            setTimeout(() => firstInput.focus(), 100);
                        }
                    }
                }
            }
        }

        // Handle previous button
        if (e.target.closest('.prev-tab')) {
            e.preventDefault();
            e.stopPropagation();

            const button = e.target.closest('.prev-tab');
            const currentTab = document.querySelector('.tab-pane.active');
            const currentTabId = currentTab ? currentTab.id : tabs[0];
            const prevTabId = button.getAttribute('data-prev-tab') || getPrevTabId(currentTabId);

            if (prevTabId) {
                // Clear any error messages
                const formStatus = document.getElementById('formStatus');
                if (formStatus) {
                    formStatus.classList.add('d-none');
                    formStatus.classList.remove('alert-danger');
                }

                // Show the previous tab
                showTab(prevTabId);
            }
        }
    });

    // Initialize first tab as active if none is active
    if (tabPanes && tabPanes.length > 0 && !document.querySelector('.tab-pane.active')) {
        const firstTabId = tabs[0];
        showTab(firstTabId);
    }

    // Initialize navigation buttons when modal is shown
    const addInventoryModal = document.getElementById('addInventoryModal');
    if (addInventoryModal) {
        addInventoryModal.addEventListener('shown.bs.modal', function () {
            updateNavigationButtons();
        });
    }

    // Initialize form validation on submit
    const inventoryForm = document.getElementById('inventoryForm');
    if (inventoryForm) {
        // Submit handler
        // Submit handler
        inventoryForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validate all tabs before form submission
            let firstInvalidTab = null;
            tabs.forEach(tabId => {
                if (!validateCurrentTab(tabId)) {
                    if (!firstInvalidTab) {
                        firstInvalidTab = tabId;
                    }
                }
            });

            if (firstInvalidTab) {
                e.stopPropagation();
                showTab(firstInvalidTab);

                // Show error message
                const formStatus = document.getElementById('formStatus');
                if (formStatus) {
                    formStatus.classList.remove('d-none');
                    formStatus.classList.add('alert-danger');
                    formStatus.innerHTML = `
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Please fix the errors in the form before submitting.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    formStatus.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                return;
            }

            // Prepare for AJAX submission
            const form = this;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            const formStatus = document.getElementById('formStatus');

            // Disable button and show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Creating...';

            // Clear previous status
            if (formStatus) {
                formStatus.classList.add('d-none');
                formStatus.classList.remove('alert-success', 'alert-danger');
                formStatus.innerHTML = '';
            }

            // Send AJAX request
            $.ajax({
                url: form.action,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                success: function (response) {
                    if (formStatus) {
                        formStatus.classList.remove('d-none');
                        formStatus.classList.add('alert-success');
                        formStatus.innerHTML = `
                            <i class="fas fa-check-circle me-2"></i>
                            ${response.message || 'Product created successfully!'}
                        `;
                        formStatus.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }

                    // Reset form and close modal after delay
                    setTimeout(() => {
                        form.reset();
                        // Close modal if using Bootstrap
                        const modalEl = document.getElementById('addInventoryModal');
                        if (modalEl) {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) {
                                try {
                                    modal.hide();
                                } catch (e) {
                                    console.warn('Failed to hide modal:', e);
                                }
                            }
                        }
                        // Reload page to show new product
                        window.location.reload();
                    }, 1500);
                },
                error: function (xhr) {
                    let errorMessage = 'An error occurred while creating the product.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            errorMessage = '<ul>';
                            for (const field in errors) {
                                errorMessage += `<li>${errors[field][0]}</li>`;
                                // Highlight field
                                const input = form.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    // Find tab containing this input and switch to it if needed
                                    const tabPane = input.closest('.tab-pane');
                                    if (tabPane && !tabPane.classList.contains('active')) {
                                        showTab(tabPane.id);
                                    }
                                }
                            }
                            errorMessage += '</ul>';
                        } else if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                    }

                    if (formStatus) {
                        formStatus.classList.remove('d-none');
                        formStatus.classList.add('alert-danger');
                        formStatus.innerHTML = `
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ${errorMessage}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        formStatus.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                },
                complete: function () {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
        });

        // Real-time validation for required fields
        inventoryForm.querySelectorAll('[required]').forEach(field => {
        });
    }


    // Show field error message
    function showFieldError(field, message) {
        let errorDiv = field.nextElementSibling;

        if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            field.parentNode.insertBefore(errorDiv, field.nextSibling);
        }

        errorDiv.textContent = message;
        field.classList.add('is-invalid');
    }

    // Show alert message
    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        const formStatus = document.getElementById('formStatus');
        formStatus.className = `alert alert-${type} alert-dismissible fade show`;
        formStatus.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        formStatus.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Image Upload Functionality
    const mainImageUpload = {
        init() {
            this.dropZone = document.getElementById('mainDropZone');
            this.preview = document.getElementById('mainImagePreview');
            this.previewContainer = document.querySelector('.main-image-preview');
            this.fileInput = document.getElementById('mainImageInput');
            this.browseBtn = document.getElementById('browseMainImage');
            this.removeBtn = document.getElementById('removeMainImage');
            this.uploadArea = document.querySelector('.main-upload-area');
            this.uploadContent = document.querySelector('.main-upload-content');
            this.uploadProgress = document.querySelector('.upload-progress');
            this.progressBar = this.uploadProgress?.querySelector('.progress-bar');
            this.percentage = this.uploadProgress?.querySelector('.upload-percentage');
            this.errorDiv = document.getElementById('mainImageError');

            if (!this.dropZone) return;

            this.setupEventListeners();
        },

        setupEventListeners() {
            // Click to select file
            this.browseBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                this.fileInput.click();
            });
            this.uploadArea?.addEventListener('click', (e) => {
                if (e.target === this.uploadArea || e.target === this.uploadContent) {
                    e.preventDefault();
                    this.fileInput.click();
                }
            });

            // Handle file selection - ensure single listener
            this.fileInput.addEventListener('change', (e) => {
                if (e.target.files && e.target.files[0]) {
                    this.handleFileSelect(e.target.files[0]);
                }
            });

            // Drag and drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                this.dropZone.addEventListener(eventName, this.preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                this.dropZone.addEventListener(eventName, () => this.highlight(), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                this.dropZone.addEventListener(eventName, () => this.unhighlight(), false);
            });

            this.dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const file = dt.files[0];
                this.handleFileSelect(file);
            });

            // Remove image
            this.removeBtn?.addEventListener('click', () => this.removeImage());
        },

        handleFileSelect(file) {
            if (!file) return;

            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                this.showError('Invalid file type. Please upload a JPG, PNG, or WebP image.');
                return;
            }

            // Validate file size (5MB max)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                this.showError('File is too large. Maximum size is 5MB.');
                return;
            }

            // Update the file input with the selected file
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            this.fileInput.files = dataTransfer.files;

            // Show preview
            this.showPreview(file);

            // Simulate upload progress for UX (file is actually uploaded with form)
            this.uploadFile(file);
        },

        showPreview(file) {
            const reader = new FileReader();

            reader.onload = (e) => {
                this.preview.src = e.target.result;
                this.previewContainer.style.display = 'block';
                this.uploadArea.style.display = 'none';
                this.clearError();
            };

            reader.readAsDataURL(file);
        },

        uploadFile(file) {
            // In a real app, you would upload the file to your server here
            // This is a mock implementation
            this.showUploadProgress();

            // Simulate upload progress
            let progress = 0;
            const interval = setInterval(() => {
                progress += 5 + Math.random() * 10;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                    this.uploadComplete(file);
                }
                this.updateProgress(progress);
            }, 100);
        },

        showUploadProgress() {
            this.uploadProgress.style.display = 'block';
            this.updateProgress(0);
        },

        updateProgress(percent) {
            if (this.progressBar) this.progressBar.style.width = `${percent}%`;
            if (this.percentage) this.percentage.textContent = `${Math.round(percent)}%`;
        },

        uploadComplete(file) {
            // In a real app, you would get the URL from the server response
            const mockFileUrl = URL.createObjectURL(file);
            document.getElementById('mainImageUrl').value = mockFileUrl;

            // Hide progress bar after a short delay
            setTimeout(() => {
                this.uploadProgress.style.display = 'none';
            }, 500);

            showAlert('Image uploaded successfully!', 'success');
        },

        removeImage() {
            this.preview.src = '#';
            this.previewContainer.style.display = 'none';
            this.uploadArea.style.display = 'block';
            this.fileInput.value = '';
            // Clear any stored image URL
            const imageUrlField = document.getElementById('mainImageUrl') || document.getElementById('image_path');
            if (imageUrlField) imageUrlField.value = '';
            this.clearError();
        },

        highlight() {
            this.dropZone.classList.add('border-primary', 'bg-light');
        },

        unhighlight() {
            this.dropZone.classList.remove('border-primary', 'bg-light');
        },

        showError(message) {
            this.errorDiv.textContent = message;
            this.errorDiv.classList.remove('d-none');
            this.errorDiv.classList.add('d-block');
        },

        clearError() {
            this.errorDiv.textContent = '';
            this.errorDiv.classList.remove('d-block');
            this.errorDiv.classList.add('d-none');
        },

        preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
    };

    // Initialize image upload
    mainImageUpload.init();

    // Additional Images Functionality
    const additionalImages = {
        init() {
            this.container = document.getElementById('additionalImagesList');
            this.addButton = document.getElementById('addMoreImages');
            this.fileInput = document.getElementById('additionalImagesInput');
            this.template = document.getElementById('additionalImageTemplate');

            if (!this.container) return;

            this.setupEventListeners();
        },

        setupEventListeners() {
            if (this.addButton && this.fileInput) {
                this.addButton.addEventListener('click', () => this.fileInput.click());
                this.fileInput.addEventListener('change', (e) => this.handleFiles(e.target.files));
            }

            // Handle remove button clicks using event delegation
            if (this.container) {
                this.container.addEventListener('click', (e) => {
                    if (e.target.closest('.remove-additional-image')) {
                        e.preventDefault();
                        const item = e.target.closest('.additional-image-item');
                        if (item) this.removeImage(item);
                    }
                });
            }
        },

        handleFiles(files) {
            if (!files || !files.length) return;

            // Convert FileList to array and process each file
            Array.from(files).forEach(file => {
                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    showAlert(`Skipped ${file.name}: Invalid file type. Please upload JPG, PNG, or WebP images.`, 'warning');
                    return;
                }

                // Validate file size (5MB max)
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    showAlert(`Skipped ${file.name}: File is too large. Maximum size is 5MB.`, 'warning');
                    return;
                }

                this.addImagePreview(file);
            });

            // Reset file input
            if (this.fileInput) {
                this.fileInput.value = '';
            }
        },

        addImagePreview(file) {
            if (!this.template || !this.template.content) return;

            const clone = this.template.content.cloneNode(true);
            const img = clone.querySelector('img');
            const fileName = clone.querySelector('small');
            const hiddenInput = clone.querySelector('input[type="hidden"]');

            if (!img || !fileName || !hiddenInput) return;

            // Set file name
            fileName.textContent = file.name.length > 15
                ? file.name.substring(0, 12) + '...' + file.name.split('.').pop()
                : file.name;

            // Create preview
            const reader = new FileReader();
            reader.onload = (e) => {
                img.src = e.target.result;
                // In a real app, you would upload the file and set the URL here
                hiddenInput.value = URL.createObjectURL(file);

                // Show the additional images container if it's hidden
                const container = document.getElementById('additionalImagesContainer');
                if (container) container.classList.remove('d-none');
            };
            reader.readAsDataURL(file);

            // Add to the container
            if (this.container) {
                this.container.appendChild(clone);
            }
        },

        removeImage(item) {
            if (!item || !this.container) return;

            item.remove();

            // Hide the additional images container if no images left
            if (this.container.children.length === 0) {
                const container = document.getElementById('additionalImagesContainer');
                if (container) container.classList.add('d-none');
            }
        }
    };

    // Initialize additional images
    additionalImages.init();

    // Real-time validation for required fields
    document.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
        field.addEventListener('blur', function () {
            const tabId = this.closest('.tab-pane')?.id;
            if (tabId) {
                validateCurrentTab(tabId);
            }
        });
    });

    // Helper function for phone validation
    function isValidPhone(phone) {
        const re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
        return re.test(String(phone).toLowerCase());
    }

    // ===== EDIT MODAL HANDLERS =====
    // Handle Edit Inventory Modal
    window.currentItemData = null;

    // When edit button is clicked, store the data
    $(document).on('click', 'button[data-bs-target="#editInventoryModal"]', function () {
        window.currentItemData = $(this).data();
    });

    // Populate form when modal is shown
    $(document).on('shown.bs.modal', '#editInventoryModal', function () {
        if (!window.currentItemData) return;

        const itemData = window.currentItemData;
        const form = $('#editInventoryForm');
        const salonSlug = window.salonSlug || '';

        // Set form action URL
        form.attr('action', `/${salonSlug}/admin/inventory/${itemData.id}`);

        // Format price values to 2 decimal places
        const formatPrice = (price) => {
            if (!price) return '';
            const num = parseFloat(price);
            return isNaN(num) ? '' : num.toFixed(2);
        };

        // Clear previous values
        form[0].reset();
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();

        // Populate form fields from data attributes
        form.find('input[name="name"]').val(itemData.name || '');
        form.find('input[name="sku"]').val(itemData.sku || '');
        form.find('select[name="category_id"]').val(itemData.category || '').trigger('change');
        form.find('textarea[name="description"]').val(itemData.description || '');

        // Handle price fields with proper formatting
        form.find('#editCostPrice').val(formatPrice(itemData.purchasePrice));
        form.find('#editSellingPrice').val(formatPrice(itemData.sellingPrice));

        // Format and handle other numeric fields
        form.find('input[name="quantity_in_stock"]').val(parseFloat(itemData.quantity) || 0);
        form.find('input[name="minimum_quantity"]').val(parseFloat(itemData.minimumQuantity) || 0);
        form.find('input[name="reorder_level"]').val(parseFloat(itemData.reorderLevel) || 0);

        // Handle unit type
        const unitType = itemData.unit || itemData.unitType || 'pcs';
        const unitSelect = $('#editUnitType');
        if (unitSelect.find(`option[value="${unitType}"]`).length > 0) {
            unitSelect.val(unitType).trigger('change');
        } else {
            unitSelect.val(unitSelect.find('option:first').val()).trigger('change');
        }

        // Set other fields
        form.find('input[name="barcode"]').val(itemData.barcode || '');
        form.find('input[name="location"]').val(itemData.location || '');
        form.find('input[name="manufacturer"]').val(itemData.manufacturer || '');
        form.find('input[name="notes"]').val(itemData.notes || '');
        form.find('input[name="expiry_date"]').val(itemData.expiryDate || '');

        // Handle image preview
        const placeholderSrc = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiB2aWV3Qm94PSIwIDAgMjAwIDIwMCI+CiAgPHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNlZWVlZWUiLz4KICA8dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBhbGlnbm1lbnQtYmFzZWxpbmU9Im1pZGRsZSIgZmlsbD0iIzk5OSI+CiAgICBObyBJbWFnZQogIDwvdGV4dD4KPC9zdmc+';

        if (itemData.image && itemData.image.trim() !== '' && !itemData.image.startsWith('data:image/svg+xml;base64,')) {
            $('#productImagePreview').attr('src', itemData.image).show();
            $('#removeImageBtn').show();
            $('.image-upload-area').hide();
        } else {
            $('#productImagePreview').attr('src', placeholderSrc).show();
            $('#removeImageBtn').hide();
            $('.image-upload-area').show();
        }

        // Handle status
        const isActive = itemData.status === 'active' || itemData.status === '1' || itemData.isActive === '1' || itemData.isActive === true;
        form.find('#editStatus').prop('checked', isActive);

        // Initialize Select2 if used
        if ($.fn.select2 && form.find('.select2').length > 0) {
            form.find('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        // Calculate and display profit margin
        updateProfitMargin();

        // Populate variants
        populateVariants(itemData);

        console.log('Edit modal data:', itemData);
    });

    // Calculate and update profit margin
    function updateProfitMargin() {
        const costPrice = parseFloat($('#editCostPrice').val()) || 0;
        const sellingPrice = parseFloat($('#editSellingPrice').val()) || 0;

        let margin = 0;
        let percentage = 0;

        if (sellingPrice > 0) {
            margin = sellingPrice - costPrice;
            percentage = costPrice > 0 ? (margin / costPrice) * 100 : 100;
        }

        const marginText = `$${margin.toFixed(2)} (${percentage.toFixed(2)}%)`;
        const marginClass = margin >= 0 ? 'text-white' : 'text-danger';

        $('#profitMarginDisplay')
            .removeClass('text-white text-danger')
            .addClass(marginClass)
            .text(marginText);
    }

    // Handle price changes
    $(document).on('input', '#editCostPrice, #editSellingPrice', function () {
        updateProfitMargin();
    });

    // Handle edit form submission
    $(document).on('submit', '#editInventoryForm', function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);
        const submitBtn = form.find('button[type="submit"]');
        const formStatus = form.find('#formStatus');

        // Clear previous errors
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();

        // Add CSRF token and method override
        if (!formData.has('_token')) {
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        }
        formData.append('_method', 'PUT');

        // Show loading state
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Updating...');

        // Clear any previous status messages
        formStatus.addClass('d-none').removeClass('alert-success alert-danger').html('');

        // Submit the form via AJAX
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                formStatus.removeClass('d-none alert-danger').addClass('alert-success')
                    .html('<i class="fas fa-check-circle me-2"></i> ' + (response.message || 'Product updated successfully!'));

                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            },
            error: function (xhr) {
                console.error('Error submitting form:', xhr);

                let errorMessage = 'An error occurred while updating the product. Please try again.';

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = '<ul class="mb-0">';

                    for (const [field, messages] of Object.entries(errors)) {
                        const input = form.find(`[name="${field}"]`);
                        if (input.length) {
                            input.addClass('is-invalid');
                            input.after(`<div class="invalid-feedback">${messages[0]}</div>`);
                        }
                        errorMessage += `<li>${messages[0]}</li>`;
                    }
                    errorMessage += '</ul>';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                formStatus.removeClass('d-none alert-success').addClass('alert-danger')
                    .html(`<i class="fas fa-exclamation-circle me-2"></i> ${errorMessage}`);

                const firstError = form.find('.is-invalid').first();
                if (firstError.length) {
                    $('html, body').animate({
                        scrollTop: firstError.offset().top - 100
                    }, 500);
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Update Product');
            }
        });
    });

    // Reset form when modal is closed
    $(document).on('hidden.bs.modal', '#editInventoryModal', function () {
        const form = $('#editInventoryForm');
        form[0].reset();
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        form.find('#formStatus').addClass('d-none').removeClass('alert-success alert-danger').html('');
        $('#editVariantsTableBody .variant-row').remove();
        $('#editVariantsTableBody .no-variants-row').show();
    });

    // ===== VARIANT MANAGEMENT =====
    function getVariantRowHtml(index, data = {}) {
        const name = data.name || '';
        const value = data.value || '';
        const sku = data.sku || '';
        const price = data.price_adjustment || 0;
        const stock = data.stock_quantity || 0;
        const id = data.id || '';
        const idInput = id ? `<input type="hidden" name="variants[${index}][id]" value="${id}">` : '';

        return `
            <tr class="variant-row">
                <td>
                    ${idInput}
                    <input type="text" name="variants[${index}][name]" class="form-control form-control-sm" placeholder="Size" value="${name}" required>
                </td>
                <td>
                    <input type="text" name="variants[${index}][value]" class="form-control form-control-sm" placeholder="Small" value="${value}" required>
                </td>
                <td>
                    <input type="text" name="variants[${index}][sku]" class="form-control form-control-sm" placeholder="SKU" value="${sku}">
                </td>
                <td>
                    <input type="number" step="0.01" name="variants[${index}][price_adjustment]" class="form-control form-control-sm" placeholder="0.00" value="${price}">
                </td>
                <td>
                    <input type="number" step="0.0001" name="variants[${index}][stock_quantity]" class="form-control form-control-sm" placeholder="0" value="${stock}">
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    // Add Variant Button (Create Modal)
    let variantIndex = 0;
    $(document).on('click', '#addVariantBtn', function () {
        $('#variantsTableBody .no-variants-row').hide();
        $('#variantsTableBody').append(getVariantRowHtml(variantIndex++));
    });

    // Add Variant Button (Edit Modal)
    $(document).on('click', '#editAddVariantBtn', function () {
        $('#editVariantsTableBody .no-variants-row').hide();
        const newIdx = 2000 + $('#editVariantsTableBody tr.variant-row').length;
        $('#editVariantsTableBody').append(getVariantRowHtml(newIdx));
    });

    // Remove Variant Button
    $(document).on('click', '.remove-variant-btn', function () {
        $(this).closest('tr').remove();
        if ($('#variantsTableBody tr.variant-row').length === 0) {
            $('#variantsTableBody .no-variants-row').show();
        }
        if ($('#editVariantsTableBody tr.variant-row').length === 0) {
            $('#editVariantsTableBody .no-variants-row').show();
        }
    });

    // Populate Variants in Edit Modal
    function populateVariants(itemData) {
        $('#editVariantsTableBody .variant-row').remove();
        $('#editVariantsTableBody .no-variants-row').show();

        let variants = [];
        if (itemData.variants) {
            try {
                variants = typeof itemData.variants === 'string'
                    ? JSON.parse(itemData.variants)
                    : itemData.variants;
            } catch (e) {
                console.error('Failed to parse variants:', e);
                variants = [];
            }
        }

        if (variants && variants.length > 0) {
            $('#editVariantsTableBody .no-variants-row').hide();
            variants.forEach((variant, idx) => {
                $('#editVariantsTableBody').append(getVariantRowHtml(idx + 1000, variant));
            });
        }
    }

    // Generate barcode for edit modal
    $(document).on('click', '#generateBarcodeBtn', function () {
        const randomBarcode = 'BC' + Math.floor(100000 + Math.random() * 900000);
        $('#editBarcode').val(randomBarcode);
    });

    // Generate barcode for create modal
    $(document).on('click', '#generateBarcode', function () {
        const randomBarcode = 'BC' + Math.floor(100000 + Math.random() * 900000);
        $('#barcode').val(randomBarcode);
    });

    // ===== IMPORT MODAL HANDLERS =====
    console.log('=== Import Handler Initialization ===');

    let selectedFile = null;

    function showUploadState(state) {
        $('#initialUploadState, #progressUploadState, #resultsUploadState').addClass('d-none');
        $('#' + state + 'UploadState').removeClass('d-none');
    }

    // Drag and drop handlers - delegated
    $(document).on('dragover', '#dropZone', function (e) {
        console.log('Dragover event triggered');
        e.preventDefault();
        e.stopPropagation();
        $(this).css({
            'border-color': '#6366f1',
            'background-color': '#eef2ff'
        });
    });

    $(document).on('dragleave', '#dropZone', function (e) {
        console.log('Dragleave event triggered');
        e.preventDefault();
        e.stopPropagation();
        $(this).css({
            'border-color': '#dee2e6',
            'background-color': '#f8f9fa'
        });
    });

    $(document).on('drop', '#dropZone', function (e) {
        console.log('Drop event triggered');
        e.preventDefault();
        e.stopPropagation();
        $(this).css({
            'border-color': '#dee2e6',
            'background-color': '#f8f9fa'
        });

        const files = e.originalEvent.dataTransfer.files;
        console.log('Files dropped:', files.length);
        if (files.length > 0) {
            handleFileSelection(files[0]);
        }
    });

    // Click to browse - delegated
    $(document).on('click', '#dropZone', function (e) {
        console.log('DropZone clicked');
        // Don't trigger if clicking the remove button or the file input itself
        if (!$(e.target).closest('#removeFileBtn').length && !$(e.target).is('#importFile')) {
            $('#importFile').click();
        }
    });

    // Prevent recursion when file input is clicked
    $(document).on('click', '#importFile', function (e) {
        e.stopPropagation();
    });

    // Browse button click - delegated
    $(document).on('click', '#browseFileBtn', function (e) {
        console.log('Browse button clicked');
        e.stopPropagation();
        $('#importFile').click();
    });

    $(document).on('change', '#importFile', function () {
        console.log('File input changed, files:', this.files.length);
        if (this.files.length > 0) {
            handleFileSelection(this.files[0]);
        }
    });

    $(document).on('click', '#removeFileBtn', function (e) {
        e.stopPropagation();
        resetFileSelection();
    });

    function handleFileSelection(file) {
        if (!file.name.endsWith('.csv')) {
            showImportError('Please select a valid CSV file.');
            return;
        }

        const maxSize = 4 * 1024 * 1024;
        if (file.size > maxSize) {
            showImportError('File size exceeds 4MB limit. Please select a smaller file.');
            return;
        }

        selectedFile = file;

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        $('#importFile')[0].files = dataTransfer.files;

        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        $('#dropZone').html(`
            <div class="py-3">
                <i class="fas fa-file-csv fa-3x text-primary mb-3"></i>
                <h6 class="fw-semibold mb-1">${file.name}</h6>
                <p class="text-muted small mb-2">${fileSize} MB</p>
                <button type="button" class="btn btn-sm btn-outline-danger" id="removeFileBtn">
                    <i class="fas fa-times me-1"></i> Remove
                </button>
            </div>
        `);

        $('#importSubmitBtn').prop('disabled', false);
    }

    function resetFileSelection() {
        selectedFile = null;
        $('#importFile').val('');
        $('#dropZone').html(`
            <div class="upload-icon mb-2">
                <i class="fas fa-cloud-upload-alt fa-2x text-primary"></i>
            </div>
            <h6 class="fw-semibold mb-1">Drag CSV here</h6>
            <p class="text-muted small mb-2">or click to browse</p>
            <button type="button" class="btn btn-sm btn-primary" id="browseFileBtn">
                Browse Files
            </button>
        `);
        $('#importSubmitBtn').prop('disabled', true);
    }

    function updateCircularProgress(percent) {
        const circle = document.getElementById('progressCircle');
        if (circle) {
            const circumference = 2 * Math.PI * 70;
            const offset = circumference - (percent / 100) * circumference;

            circle.style.strokeDashoffset = offset;
            $('#progressPercentText').text(percent + '%');
            $('#linearProgressBar').css('width', percent + '%');
        }
    }

    $(document).on('submit', '#importProductForm', function (e) {
        e.preventDefault();

        console.log('Form submitted - AJAX handler triggered');

        if (!selectedFile) {
            showImportError('Please select a CSV file to import.');
            return;
        }

        const formData = new FormData(this);

        // Explicitly append the file to ensure it's sent
        if (selectedFile) {
            formData.set('file', selectedFile);
            console.log('File explicitly appended to FormData:', selectedFile.name);
        }

        // Update file info in progress state
        const fileSize = (selectedFile.size / 1024 / 1024).toFixed(2);
        $('#uploadingFileName').text(selectedFile.name);
        $('#uploadingFileSize').text('(' + fileSize + ' MB)');

        showUploadState('progress');
        updateCircularProgress(0);

        $('#importSubmitBtn').prop('disabled', true);
        $('#discardBtn').prop('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function () {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        updateCircularProgress(percentComplete);
                    }
                }, false);
                return xhr;
            },
            success: function (response) {
                console.log('Import Response:', response);
                updateCircularProgress(100);

                setTimeout(function () {
                    showUploadState('results');
                    handleImportResponse(response);
                    resetImportButton();
                }, 500);
            },
            error: function (xhr) {
                console.log('Import Error:', xhr);
                showUploadState('results');

                let errorMsg = 'An error occurred during import.';

                try {
                    if (xhr.responseJSON) {
                        handleImportResponse(xhr.responseJSON);
                    } else if (xhr.responseText) {
                        errorMsg = 'Server error: ' + xhr.statusText;
                        showImportError(errorMsg);
                    } else {
                        showImportError(errorMsg);
                    }
                } catch (e) {
                    showImportError(errorMsg);
                }

                resetImportButton();
            }
        });
    });

    function handleImportResponse(response) {
        console.log('Handling response:', response);

        let message = '';
        let isError = false;

        if (response.results) {
            const results = response.results;

            if (results.success > 0) {
                message += `<div class="mb-2"><i class="fas fa-check-circle text-white me-1"></i><strong>${results.success} product(s)</strong> imported successfully!</div>`;
            }

            if (results.failed > 0) {
                message += `<div class="mb-2"><i class="fas fa-exclamation-triangle text-warning me-1"></i><strong>${results.failed} product(s)</strong> failed to import.</div>`;
                isError = true;
            }

            if (results.errors && results.errors.length > 0) {
                message += '<div class="mt-2"><strong>Error Details:</strong><div class="mt-1 small" style="max-height: 200px; overflow-y: auto;">';

                results.errors.forEach((error) => {
                    let errorText = '';

                    if (typeof error === 'object' && error.message) {
                        const rowInfo = error.row ? `<strong>Row ${error.row}:</strong> ` : '';
                        let errorMsg = error.message;

                        if (errorMsg.includes('Duplicate entry')) {
                            const match = errorMsg.match(/Duplicate entry '([^']+)' for key '([^']+)'/);
                            if (match) {
                                const value = match[1];
                                const field = match[2].includes('sku') ? 'SKU' : 'field';
                                errorMsg = `Duplicate ${field} "${value}" - This product already exists`;
                            }
                        } else if (errorMsg.includes('SQLSTATE')) {
                            const sqlMatch = errorMsg.match(/SQLSTATE\[[\w]+\]: (.+?) \(Connection:/);
                            if (sqlMatch) {
                                errorMsg = sqlMatch[1];
                            }
                        }

                        errorText = `${rowInfo}${errorMsg}`;
                    } else if (typeof error === 'string') {
                        errorText = error;
                    } else {
                        errorText = JSON.stringify(error);
                    }

                    message += `<div class="p-2 mb-1 bg-light rounded border-start border-danger border-3">${errorText}</div>`;
                });

                message += '</div></div>';
                isError = true;
            }

            if (results.success === 0 && results.failed === 0 && (!results.errors || results.errors.length === 0)) {
                message = '<i class="fas fa-info-circle me-1"></i>No products were found in the file or all rows were empty.';
                isError = true;
            }
        } else if (response.message) {
            message = response.message;
            isError = !response.success;
        } else {
            message = 'Unknown response format. Please check the console for details.';
            isError = true;
        }

        if (isError) {
            showImportError(message);
        } else {
            showImportSuccess(message);
            if (response.results && response.results.success > 0) {
                setTimeout(function () {
                    location.reload();
                }, 2500);
            }
        }
    }

    function showImportError(message) {
        $('#importError').removeClass('d-none');
        $('#importErrorMessage').html(message);
        $('#importSuccess').addClass('d-none');
    }

    function showImportSuccess(message) {
        $('#importSuccess').removeClass('d-none');
        $('#importSuccessMessage').html(message);
        $('#importError').addClass('d-none');
    }

    function hideImportMessages() {
        $('#importError').addClass('d-none');
        $('#importSuccess').addClass('d-none');
    }

    function resetImportButton() {
        $('#importSubmitBtn').prop('disabled', false);
        $('#discardBtn').prop('disabled', false);
    }

    $(document).on('shown.bs.modal', '#importProductModal', function () {
        showUploadState('initial');
        hideImportMessages();
        updateCircularProgress(0);
        resetImportButton();
        resetFileSelection();
    });

    $(document).on('hidden.bs.modal', '#importProductModal', function () {
        showUploadState('initial');
        hideImportMessages();
        updateCircularProgress(0);
        resetImportButton();
        resetFileSelection();
    });

    $(document).on('click', '#supportLink', function () {
        alert('For support, please contact your system administrator or visit the help documentation.');
    });

});
