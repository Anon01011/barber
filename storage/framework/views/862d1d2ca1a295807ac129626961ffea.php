<!-- Add Category Modal (Luxury & Stack-Safe) -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true" style="z-index: 1075;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background: #ffffff;">
            <!-- Modal Header -->
            <div class="modal-header border-0 py-3 px-4 text-white"
                style="background: linear-gradient(135deg, #0b0f17 0%, #1a2232 100%); border-bottom: 1px solid rgba(197, 168, 128, 0.25) !important;">
                <div class="d-flex align-items-center">
                    <div class="me-3 p-2 rounded-2 d-flex align-items-center justify-content-center"
                        style="background: rgba(197, 168, 128, 0.18); width: 36px; height: 36px;">
                        <i class="fas fa-tags" style="color: #c5a880; font-size: 16px;"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="addCategoryModalLabel" style="font-size: 15px;">
                            New Inventory Category
                        </h6>
                        <small style="color: #94a3b8; font-size: 11px;">Create a product or consumable category</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?php echo e(route('admin.inventory.categories.store')); ?>" method="POST" id="categoryForm" novalidate>
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4">
                    <!-- Alert Box -->
                    <div id="categoryModalAlert" class="alert alert-danger d-none py-2 px-3 small rounded-3 mb-3" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        <span id="categoryModalAlertText"></span>
                    </div>

                    <!-- Category Name -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark mb-1">
                            Category Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fas fa-folder-plus text-primary"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-0" name="name" id="categoryNameInput"
                                placeholder="e.g., Hair Care, Styling, Beard & Shave" required autofocus maxlength="255">
                        </div>
                    </div>

                    <!-- Icon -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark mb-1">
                            Icon <span class="text-muted fw-normal">(FontAwesome)</span>
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fas fa-icons text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-0" name="icon" id="categoryIconInput"
                                placeholder="e.g., cut, box, pump-soap, flask" value="box">
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Optional icon name (defaults to 'box')</small>
                    </div>

                    <!-- Description -->
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-dark mb-1">Description</label>
                        <textarea class="form-control form-control-sm" name="description" id="categoryDescInput" rows="2"
                            placeholder="Brief description of products in this category..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 p-3 pt-0 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-3 py-1.5 small fw-semibold" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 py-1.5 small fw-semibold" id="categorySubmitBtn"
                        style="background: #0f172a; border-color: #0f172a;">
                        <i class="fas fa-plus me-1 text-warning"></i>
                        <span>Create Category</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Stacked Modal Safety: ensure Category modal is always above Product modals */
    #addCategoryModal {
        z-index: 1075 !important;
    }
    .modal-backdrop.category-stacked-backdrop {
        z-index: 1070 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const categoryModalEl = document.getElementById('addCategoryModal');
    const categoryForm = document.getElementById('categoryForm');
    const alertBox = document.getElementById('categoryModalAlert');
    const alertText = document.getElementById('categoryModalAlertText');
    const submitBtn = document.getElementById('categorySubmitBtn');

    if (!categoryModalEl) return;

    // Handle stacked modal z-index when opened from inside another modal
    categoryModalEl.addEventListener('show.bs.modal', function () {
        if (alertBox) alertBox.classList.add('d-none');
        const otherOpenModals = document.querySelectorAll('.modal.show:not(#addCategoryModal)');
        if (otherOpenModals.length > 0) {
            categoryModalEl.dataset.isStacked = 'true';
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 1) {
                    const topBackdrop = backdrops[backdrops.length - 1];
                    topBackdrop.classList.add('category-stacked-backdrop');
                }
            }, 10);
        } else {
            categoryModalEl.dataset.isStacked = 'false';
        }
    });

    // When Category modal is hidden, ensure body keeps modal-open if parent modal is still open
    categoryModalEl.addEventListener('hidden.bs.modal', function () {
        if (categoryForm) categoryForm.reset();
        if (alertBox) alertBox.classList.add('d-none');

        const remainingModals = document.querySelectorAll('.modal.show');
        if (remainingModals.length > 0) {
            document.body.classList.add('modal-open');
        }
    });

    // AJAX Submission handler
    if (categoryForm) {
        categoryForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const nameInput = document.getElementById('categoryNameInput');
            if (!nameInput || !nameInput.value.trim()) {
                if (alertBox && alertText) {
                    alertText.textContent = 'Please enter a category name.';
                    alertBox.classList.remove('d-none');
                }
                if (nameInput) nameInput.focus();
                return;
            }

            const formData = new FormData(categoryForm);
            const submitBtnContent = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';
            }
            if (alertBox) alertBox.classList.add('d-none');

            fetch(categoryForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: formData
            })
            .then(response => {
                return response.json().then(data => ({
                    status: response.status,
                    ok: response.ok,
                    data: data
                }));
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error(res.data.message || 'Failed to create category.');
                }

                const cat = res.data.category;

                // Update category dropdowns on the page
                const selectElements = document.querySelectorAll('select[name="category_id"], #editCategory');
                selectElements.forEach(select => {
                    // Check if option already exists
                    let exists = false;
                    for (let i = 0; i < select.options.length; i++) {
                        if (String(select.options[i].value) === String(cat.id)) {
                            exists = true;
                            break;
                        }
                    }
                    if (!exists) {
                        const newOption = new Option(cat.name, cat.id, true, true);
                        select.add(newOption);
                    }
                    select.value = cat.id;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                });

                // Close category modal
                const modalInstance = bootstrap.Modal.getInstance(categoryModalEl) || bootstrap.Modal.getOrCreateInstance(categoryModalEl);
                modalInstance.hide();

                // Show notification in parent modal or page
                const formAlert = document.getElementById('formAlert');
                const formAlertMsg = document.getElementById('formAlertMessage');
                if (formAlert && formAlertMsg) {
                    formAlertMsg.innerHTML = '<strong>Success!</strong> Category "' + cat.name + '" created and selected.';
                    formAlert.className = 'alert alert-success alert-dismissible fade show py-1.5 px-3 small';
                    formAlert.classList.remove('d-none');
                    setTimeout(() => formAlert.classList.add('d-none'), 5000);
                }
            })
            .catch(err => {
                if (alertBox && alertText) {
                    alertText.textContent = err.message || 'An error occurred while creating category.';
                    alertBox.classList.remove('d-none');
                }
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtnContent;
                }
            });
        });
    }
});
</script>
<?php /**PATH C:\Ketan\projects\barber\resources\views/inventory/partials/category-modal.blade.php ENDPATH**/ ?>