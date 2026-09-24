/**
 * Inventory Item Creation Modal Script
 * Handles all interactions for the modern item creation modal
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Modal Elements
    const form = document.getElementById('createInventoryForm');
    const progressBar = document.getElementById('formProgress');

    // Navigation Elements
    const nextBtn = document.getElementById('nextTabBtn');
    const prevBtn = document.getElementById('prevTabBtn');
    const saveBtn = document.getElementById('saveProductBtn');

    // Tabs
    const tabs = ['basic', 'pricing', 'stock', 'details'];
    let currentTabIndex = 0;

    // Initialize Bootstrap Tabs
    const tabTriggerList = [].slice.call(document.querySelectorAll('#productTabs button[data-bs-toggle="pill"]'));

    // Navigation Logic
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            if (validateTab(currentTabIndex)) {
                if (currentTabIndex < tabs.length - 1) {
                    currentTabIndex++;
                    switchTab(currentTabIndex);
                }
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            if (currentTabIndex > 0) {
                currentTabIndex--;
                switchTab(currentTabIndex);
            }
        });
    }

    // Handle Tab Clicks (Manual Navigation)
    tabTriggerList.forEach((tabEl, index) => {
        tabEl.addEventListener('click', function (e) {
            e.preventDefault();
            // Validate before allowing jump forward
            if (index > currentTabIndex && !validateTab(currentTabIndex)) {
                e.stopPropagation();
                switchTab(currentTabIndex); // Revert
                return;
            }
            currentTabIndex = index;
            updateUI();
        });

        // Update index when tab is shown (in case triggered programmatically)
        tabEl.addEventListener('shown.bs.tab', function () {
            currentTabIndex = tabs.indexOf(this.getAttribute('data-bs-target').replace('#', ''));
            updateUI();
        });
    });

    function switchTab(index) {
        const tabId = tabs[index];
        const triggerEl = document.querySelector(`#tab-${tabId}`);
        if (triggerEl) {
            const tabInstance = bootstrap.Tab.getOrCreateInstance(triggerEl);
            tabInstance.show();
            updateUI();
        }
    }

    function updateUI() {
        // Update Progress
        const progress = ((currentTabIndex + 1) / tabs.length) * 100;
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);
        }

        // Update Buttons
        if (prevBtn) prevBtn.style.display = currentTabIndex === 0 ? 'none' : 'inline-block';

        if (currentTabIndex === tabs.length - 1) {
            if (nextBtn) nextBtn.style.display = 'none';
            if (saveBtn) saveBtn.style.display = 'inline-block';
        } else {
            if (nextBtn) nextBtn.style.display = 'inline-block';
            if (saveBtn) saveBtn.style.display = 'none';
        }
    }

    function validateTab(index) {
        const tabId = tabs[index];
        const pane = document.getElementById(tabId);
        if (!pane) return true;

        const inputs = pane.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');

                // Add listener to remove invalid class on input
                input.addEventListener('input', function () {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            }
        });

        if (!isValid) {
            showAlert('Please fill in all required fields marked with *', 'danger');
        }

        return isValid;
    }

    function validateAllTabs() {
        for (let i = 0; i < tabs.length; i++) {
            if (!validateTab(i)) {
                switchTab(i);
                return false;
            }
        }
        return true;
    }

    function showAlert(message, type = 'info') {
        const alertEl = document.getElementById('formAlert');
        const msgEl = document.getElementById('formAlertMessage');
        if (alertEl && msgEl) {
            msgEl.innerHTML = message;
            alertEl.className = `alert alert-dismissible fade show py-1 px-2 small alert-${type}`;
            alertEl.classList.remove('d-none');

            // Auto hide after 5 seconds
            setTimeout(() => {
                alertEl.classList.add('d-none');
                alertEl.classList.remove('show');
            }, 5000);
        } else {
            alert(message.replace(/<[^>]*>?/gm, ''));
        }
    }

    // Form Submission
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!validateAllTabs()) {
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...';

            const formData = new FormData(form);
            const csrfToken = document.querySelector('input[name="_token"]')?.value;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                .then(({ status, body }) => {
                    if (status >= 200 && status < 300) {
                        showAlert('Product created successfully!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        let errorMessage = body.message || 'An error occurred.';
                        if (body.errors) {
                            errorMessage = '<ul>';
                            for (const field in body.errors) {
                                errorMessage += `<li>${body.errors[field][0]}</li>`;
                                // Highlight field
                                const input = form.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    input.addEventListener('input', () => input.classList.remove('is-invalid'));
                                }
                            }
                            errorMessage += '</ul>';
                        }
                        showAlert(errorMessage, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An unexpected error occurred. Please try again.', 'danger');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
        });
    }

    // SKU Generation
    const generateSkuBtn = document.getElementById('generateSkuBtn');
    const skuInput = document.getElementById('productSku');
    const nameInput = document.getElementById('productName');

    if (generateSkuBtn && skuInput && nameInput) {
        generateSkuBtn.addEventListener('click', function () {
            const name = nameInput.value.trim();
            if (!name) {
                showAlert('Please enter a product name first', 'warning');
                nameInput.focus();
                return;
            }

            // Use backend route if available
            if (window.routes && window.routes.generateSku) {
                const originalContent = generateSkuBtn.innerHTML;
                generateSkuBtn.disabled = true;
                generateSkuBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                const csrfToken = document.querySelector('input[name="_token"]')?.value;

                fetch(window.routes.generateSku, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ name: name })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            skuInput.value = data.sku;
                            skuInput.classList.remove('is-invalid');
                        } else {
                            showAlert('Failed to generate SKU: ' + (data.message || 'Unknown error'), 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Failed to generate SKU. Please try again.', 'danger');
                    })
                    .finally(() => {
                        generateSkuBtn.disabled = false;
                        generateSkuBtn.innerHTML = originalContent;
                    });
            } else {
                // Fallback
                const prefix = name.substring(0, 3).toUpperCase();
                const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
                skuInput.value = `${prefix}-${random}`;
            }
        });

        // Auto-generate on blur if empty
        nameInput.addEventListener('blur', function () {
            if (this.value.trim() && !skuInput.value.trim()) {
                generateSkuBtn.click();
            }
        });
    }

    // Profit Margin Calculation
    const costInput = document.getElementById('costPrice');
    const priceInput = document.getElementById('sellingPrice');
    const profitDisplay = document.getElementById('profitDisplay');

    function calculateMargin() {
        const cost = parseFloat(costInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;

        if (price > 0) {
            const profit = price - cost;
            const margin = (profit / price) * 100;

            profitDisplay.textContent = `$${profit.toFixed(2)} (${margin.toFixed(1)}%)`;

            if (profit < 0) {
                profitDisplay.classList.remove('text-white');
                profitDisplay.classList.add('text-danger');
            } else {
                profitDisplay.classList.remove('text-danger');
                profitDisplay.classList.add('text-white');
            }
        } else {
            profitDisplay.textContent = '$0.00 (0%)';
        }
    }

    if (costInput && priceInput && profitDisplay) {
        costInput.addEventListener('input', calculateMargin);
        priceInput.addEventListener('input', calculateMargin);
    }

    // Image Upload Handling
    const imageUploadArea = document.getElementById('imageUploadArea');
    const productImageInput = document.getElementById('productImage');
    const browseImageBtn = document.getElementById('browseImage');
    const imagePreview = document.getElementById('imagePreview');
    const removeImageBtn = document.getElementById('removeImage');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');

    if (imageUploadArea && productImageInput) {
        // Trigger input on area click
        imageUploadArea.addEventListener('click', function (e) {
            // Don't trigger if clicking remove button
            if (e.target.closest('#removeImage')) return;
            productImageInput.click();
        });

        if (browseImageBtn) {
            browseImageBtn.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent double trigger
                productImageInput.click();
            });
        }

        // Drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            imageUploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            imageUploadArea.addEventListener(eventName, () => imageUploadArea.classList.add('bg-light'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            imageUploadArea.addEventListener(eventName, () => imageUploadArea.classList.remove('bg-light'), false);
        });

        imageUploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        productImageInput.addEventListener('change', function () {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        if (imagePreview) {
                            const img = imagePreview.querySelector('img');
                            if (img) img.src = e.target.result;
                            imagePreview.classList.remove('d-none');
                        }
                        if (uploadPlaceholder) uploadPlaceholder.classList.add('d-none');
                    }
                    reader.readAsDataURL(file);
                }
            }
        }

        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                productImageInput.value = '';
                if (imagePreview) {
                    const img = imagePreview.querySelector('img');
                    if (img) img.src = '#';
                    imagePreview.classList.add('d-none');
                }
                if (uploadPlaceholder) uploadPlaceholder.classList.remove('d-none');
            });
        }
    }

    // Variant Management
    const addVariantBtn = document.getElementById('addVariantBtn');
    const variantsContainer = document.getElementById('variantsContainer');
    const variantTemplate = document.getElementById('variantTemplate');
    let variantCount = 0;

    if (addVariantBtn && variantsContainer && variantTemplate) {
        addVariantBtn.addEventListener('click', function () {
            const noVariantsMsg = document.getElementById('noVariantsMsg');
            if (noVariantsMsg) {
                noVariantsMsg.style.display = 'none';
            }

            const clone = variantTemplate.content.cloneNode(true);
            const item = clone.querySelector('.variant-item');

            if (item) {
                // Update names with index
                item.innerHTML = item.innerHTML.replace(/INDEX/g, variantCount);

                variantsContainer.appendChild(item);
                variantCount++;

                // Add remove listener
                const removeBtn = item.querySelector('.remove-variant');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function () {
                        item.remove();
                        if (variantsContainer.children.length === 0 || (variantsContainer.children.length === 1 && variantsContainer.children[0].id === 'noVariantsMsg')) {
                            if (noVariantsMsg) noVariantsMsg.style.display = 'block';
                        }
                    });
                }
            }
        });
    }

    // Character Counters
    const nameInputCounter = document.getElementById('productName');
    const nameCounter = document.getElementById('nameCounter');
    if (nameInputCounter && nameCounter) {
        nameInputCounter.addEventListener('input', function () {
            nameCounter.textContent = this.value.length;
        });
    }

    const descInput = document.getElementById('productDescription');
    const descCounter = document.getElementById('descCounter');
    if (descInput && descCounter) {
        descInput.addEventListener('input', function () {
            descCounter.textContent = this.value.length;
        });
    }

    const notesInput = document.querySelector('textarea[name="notes"]');
    const notesCounter = document.getElementById('notesCounter');
    if (notesInput && notesCounter) {
        notesInput.addEventListener('input', function () {
            notesCounter.textContent = this.value.length;
        });
    }
});
