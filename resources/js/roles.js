// Role Management JavaScript

// Initialize notification system
if (!window.notifications) {

}
const notifications = window.notifications;

// Role Management Class
class RoleManagement {
    constructor() {
        // Get base URL from container
        const container = document.getElementById('roleManagementContainer');
        this.baseUrl = container ? container.dataset.baseUrl : '/admin/roles';

        // Ensure notification system is initialized
        if (!window.notifications) {
            window.notifications = notifications;
        }
        this.boundHandleStatusToggle = this.handleStatusToggle.bind(this);
        this.initializeEventListeners();
        this.initializeFormValidation();
        this.initializeTooltips();
        this.initializeUserManagement();
        this.initializeModals();
        this.initializeRemoveUserModal();
        this.initializeTemplates();
        this.initializeSelectAll();
        this.initializeAddRoleForm();
        this.initializeEditRoleForm();
        this.initializeDeleteRoleModal();
    }

    initializeEventListeners() {
        // Edit role button
        document.querySelectorAll('.btn-action.edit').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleEditRole(e));
        });

        // Delete role button - Update this section
        document.querySelectorAll('.btn-action.delete').forEach(btn => {
            // Remove any existing listeners first
            btn.removeEventListener('click', this.handleDeleteRole);
            // Add the new listener
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.handleDeleteRole(e);
            });
        });

        // Manage users button
        document.querySelectorAll('.btn-action.manage-users').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleManageUsers(e));
        });

        // Role status toggle - using a single bound handler
        this.initializeStatusToggles();

        // Search functionality
        const searchInput = document.getElementById('roleSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.handleSearch(e));
        }

        // Status filter
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => this.handleStatusFilter(e));
        }

        // Add User to Role button
        const addUserBtn = document.getElementById('addUserToRole');
        if (addUserBtn) {
            addUserBtn.addEventListener('click', () => this.handleAddUserToRole());
        }

        // User search
        const userSearch = document.getElementById('userSearch');
        if (userSearch) {
            userSearch.addEventListener('input', (e) => this.handleUserSearch(e));
        }

        // Edit role form submit
        const editRoleForm = document.getElementById('editRoleForm');
        if (editRoleForm) {
            editRoleForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveRoleChanges();
            });
        }
    }

    initializeStatusToggles() {
        // Remove any existing listeners first
        document.querySelectorAll('.role-status-toggle').forEach(toggle => {
            toggle.removeEventListener('change', this.boundHandleStatusToggle);
            toggle.addEventListener('change', this.boundHandleStatusToggle);
        });

        // Set up observer for dynamically added toggles
        if (!this.statusToggleObserver) {
            this.statusToggleObserver = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.addedNodes.length) {
                        mutation.addedNodes.forEach(node => {
                            if (node.nodeType === 1) { // Element node
                                // Check if the node itself is a toggle or contains toggles
                                const toggles = node.classList && node.classList.contains('role-status-toggle') ? [node] :
                                    node.querySelectorAll('.role-status-toggle');

                                toggles.forEach(toggle => {
                                    // Remove any existing listeners
                                    toggle.removeEventListener('change', this.boundHandleStatusToggle);
                                    // Add the new listener
                                    toggle.addEventListener('change', this.boundHandleStatusToggle);
                                });
                            }
                        });
                    }
                });
            });

            // Start observing
            this.statusToggleObserver.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    initializeFormValidation() {
        // Form validation
        const forms = document.querySelectorAll('form[novalidate]');
        forms.forEach(form => {
            form.addEventListener('submit', (event) => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });

        // Real-time validation for role name
        const roleNameInputs = document.querySelectorAll('input[name="name"]');
        roleNameInputs.forEach(input => {
            input.addEventListener('input', () => {
                const value = input.value.trim();
                const isValid = /^[a-z0-9_]+$/.test(value);
                input.setCustomValidity(isValid ? '' : 'Only lowercase letters, numbers, and underscores are allowed');
            });
        });

        // Character counter for description
        const descriptionInputs = document.querySelectorAll('textarea[name="description"]');
        descriptionInputs.forEach(input => {
            const counter = document.createElement('div');
            counter.className = 'form-text text-end';
            input.parentNode.appendChild(counter);

            input.addEventListener('input', () => {
                const remaining = 1000 - input.value.length;
                counter.textContent = `${remaining} characters remaining`;
                counter.classList.toggle('text-warning', remaining < 100);
            });
        });
    }

    initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    initializeUserManagement() {
        // Handle Manage Users button click
        document.querySelectorAll('.manage-users').forEach(button => {
            button.addEventListener('click', () => {
                const roleId = button.dataset.roleId;
                const roleName = button.dataset.roleName;
                this.loadRoleUsers(roleId, roleName);
            });
        });
    }

    initializeModals() {
        const modals = [
            'addRoleModal',
            'editRoleModal',
            'roleTemplatesModal',
            'manageUsersModal',
            'addUserToRoleModal',
            'removeUserConfirmModal'
        ];

        modals.forEach(modalId => {
            const modalElement = document.getElementById(modalId);
            if (!modalElement) return;

            // Create modal with settings to prevent focus trap issues
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false,
                focus: false // Disable auto-focus to prevent focus trap issues
            });

            // Store the modal instance
            this[`${modalId}Instance`] = modal;

            // Clean up on modal hide
            modalElement.addEventListener('hidden.bs.modal', () => {
                // Clear any form data
                const form = modalElement.querySelector('form');
                if (form) {
                    form.reset();
                    form.classList.remove('was-validated');
                    this.updateSelectedCount(form);

                    // Reset filter dropdown and search inputs
                    if (form.id === 'editRoleForm') {
                        const dropdownButton = document.getElementById('filterDropdown');
                        if (dropdownButton) {
                            dropdownButton.innerHTML = `<i class="fas fa-filter me-1"></i> Filter`;
                        }
                        const filterOptions = form.querySelectorAll('.filter-option');
                        filterOptions.forEach(opt => opt.classList.remove('active'));
                        const allOption = form.querySelector('.filter-option[data-filter="all"]');
                        if (allOption) allOption.classList.add('active');

                        const searchInput = document.getElementById('permissionSearch');
                        if (searchInput) searchInput.value = '';
                        const clearSearchBtn = document.getElementById('clearSearch');
                        if (clearSearchBtn) clearSearchBtn.style.display = 'none';

                        this.applyFilters();
                    }
                }

                // Remove any error messages
                const errorDiv = modalElement.querySelector('.alert-danger');
                if (errorDiv) {
                    errorDiv.remove();
                }

                // Reset any loading states
                const buttons = modalElement.querySelectorAll('button[type="submit"]');
                buttons.forEach(btn => {
                    btn.disabled = false;
                    const spinner = btn.querySelector('.spinner-border');
                    if (spinner) {
                        spinner.classList.add('d-none');
                    }
                });

                // Clear any active tooltips
                const tooltipElements = modalElement.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltipElements.forEach(el => {
                    const tooltip = bootstrap.Tooltip.getInstance(el);
                    if (tooltip) {
                        tooltip.dispose();
                    }
                });

                // Blur any focused elements
                if (document.activeElement && modalElement.contains(document.activeElement)) {
                    document.activeElement.blur();
                }

                // Clean up backdrop
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });

            // Add event listener for shown event to handle focus manually
            modalElement.addEventListener('shown.bs.modal', () => {
                // Find the first focusable element in the modal
                const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
                const focusableContent = modalElement.querySelectorAll(focusableElements);

                if (focusableContent.length > 0) {
                    // Focus first element
                    focusableContent[0].focus();
                }

                // Add event listener for tab key to maintain focus within modal
                modalElement.addEventListener('keydown', (e) => {
                    if (e.key === 'Tab') {
                        const firstFocusableElement = focusableContent[0];
                        const lastFocusableElement = focusableContent[focusableContent.length - 1];

                        if (e.shiftKey) {
                            if (document.activeElement === firstFocusableElement) {
                                e.preventDefault();
                                lastFocusableElement.focus();
                            }
                        } else {
                            if (document.activeElement === lastFocusableElement) {
                                e.preventDefault();
                                firstFocusableElement.focus();
                            }
                        }
                    }
                });
            });
        });
    }

    initializeRemoveUserModal() {
        const modal = document.getElementById('removeUserConfirmModal');
        if (!modal) return;

        // Store modal instance
        this.removeUserConfirmModalInstance = new bootstrap.Modal(modal, {
            backdrop: 'static',
            keyboard: false
        });

        // Handle confirmation
        const confirmBtn = modal.querySelector('#confirmRemoveUser');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                const roleId = modal.dataset.roleId;
                const userId = modal.dataset.userId;
                if (roleId && userId) {
                    this.executeRemoveUser(roleId, userId);
                }
            });
        }

        // Handle modal hidden event
        modal.addEventListener('hidden.bs.modal', () => {
            // Clear stored IDs
            delete modal.dataset.roleId;
            delete modal.dataset.userId;

            // Clean up backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }

    initializeTemplates() {
        // Handle template card clicks
        document.querySelectorAll('.template-card').forEach(card => {
            const useTemplateBtn = card.querySelector('.use-template');
            if (useTemplateBtn) {
                useTemplateBtn.addEventListener('click', () => {
                    const template = card.dataset.template;
                    this.applyTemplate(template);
                });
            }
        });
    }

    initializeSelectAll() {
        // Initialize for both add and edit forms
        ['addRoleForm', 'editRoleForm'].forEach(formId => {
            const form = document.getElementById(formId);
            if (!form) return;

            // Main select all checkbox
            const mainSelectAll = form.querySelector('#addSelectAllPermissions, #selectAllPermissions, #editSelectAllPermissions');
            if (mainSelectAll) {
                mainSelectAll.addEventListener('change', (e) => {
                    const isChecked = e.target.checked;
                    form.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                    // Update module select all checkboxes
                    form.querySelectorAll('.module-select-all').forEach(moduleCheckbox => {
                        moduleCheckbox.checked = isChecked;
                    });
                    this.updateSelectedCount(form);
                    if (form.id === 'editRoleForm') {
                        this.applyFilters();
                    }
                });
            }

            // Module select all checkboxes
            form.querySelectorAll('.module-select-all').forEach(moduleCheckbox => {
                moduleCheckbox.addEventListener('change', (e) => {
                    const isChecked = e.target.checked;
                    const module = e.target.dataset.module;
                    if (module) {
                        form.querySelectorAll(`.permission-checkbox[data-module="${module}"]`).forEach(checkbox => {
                            checkbox.checked = isChecked;
                        });
                        this.updateMainSelectAll(form);
                    }
                });
            });

            // Individual permission checkboxes
            form.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    const module = checkbox.dataset.module;
                    if (module) {
                        this.updateModuleSelectAll(form, module);
                        this.updateMainSelectAll(form);
                    }
                });
            });
        });
    }

    updateModuleSelectAll(form, module) {
        const moduleCheckbox = form.querySelector(`.module-select-all[data-module="${module}"]`);
        if (moduleCheckbox) {
            const modulePermissions = form.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
            const moduleChecked = form.querySelectorAll(`.permission-checkbox[data-module="${module}"]:checked`);
            moduleCheckbox.checked = modulePermissions.length === moduleChecked.length;
        }
    }

    updateMainSelectAll(form) {
        const mainSelectAll = form.querySelector('#addSelectAllPermissions, #selectAllPermissions, #editSelectAllPermissions');
        if (mainSelectAll) {
            const allPermissions = form.querySelectorAll('.permission-checkbox');
            const allChecked = form.querySelectorAll('.permission-checkbox:checked');
            mainSelectAll.checked = allPermissions.length === allChecked.length;
        }
        this.updateSelectedCount(form);
        if (form.id === 'editRoleForm') {
            this.applyFilters();
        }
    }

    updateSelectedCount(form) {
        const selectedCountBadge = form.querySelector('#selectedCount, #addSelectedCount');
        if (selectedCountBadge) {
            const checkedCount = form.querySelectorAll('.permission-checkbox:checked').length;
            selectedCountBadge.textContent = checkedCount;
        }
    }

    async handleEditRole(e) {
        const roleId = e.currentTarget.dataset.roleId;
        if (!roleId) {
            notifications.show('Invalid role ID', 'error');
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}/${roleId}/edit`);
            if (!response.ok) {
                throw new Error(`Failed to fetch role data: ${response.statusText}`);
            }

            const data = await response.json();


            // Check if data is in the expected format
            if (!data || typeof data !== 'object') {
                throw new Error('Invalid response format: Expected an object');
            }

            // Check if we have the required role data
            if (!data.role) {
                throw new Error('Invalid response format: Missing role data');
            }

            // Use the role data from the response
            this.populateEditModal(data.role, data.is_editable);

            // Show the edit modal
            const editRoleModal = document.getElementById('editRoleModal');
            if (editRoleModal) {
                const bsModal = new bootstrap.Modal(editRoleModal);
                bsModal.show();
            } else {
                throw new Error('Edit role modal not found');
            }
        } catch (error) {

            notifications.show(error.message || 'Failed to load role data', 'error');
        }
    }

    initializeDeleteRoleModal() {
        const modal = document.getElementById('deleteRoleConfirmModal');
        if (!modal) {

            return;
        }



        // Store modal instance with proper settings
        this.deleteRoleConfirmModalInstance = new bootstrap.Modal(modal, {
            backdrop: 'static',
            keyboard: false
        });

        // Handle confirmation
        const confirmBtn = modal.querySelector('#confirmDeleteRole');
        if (confirmBtn) {
            // Remove any existing listeners
            confirmBtn.removeEventListener('click', this.handleDeleteConfirmation);
            // Add the new listener
            confirmBtn.addEventListener('click', () => {

                const roleId = modal.dataset.roleId;


                // Find the role row using the data-role-id attribute
                const roleRow = document.querySelector(`tr[data-role-id="${roleId}"]`);
                if (!roleRow) {

                    notifications.show('Could not find role information', 'error');
                    return;
                }

                if (roleId && roleRow) {
                    this.executeDeleteRole(roleId, roleRow);
                } else {

                }
                this.closeModal('deleteRoleConfirmModal');
            });
        } else {

        }

        // Handle modal hidden event
        modal.addEventListener('hidden.bs.modal', () => {

            // Clear stored data
            delete modal.dataset.roleId;
            delete modal.dataset.roleRow;

            // Clean up backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }

    async handleDeleteRole(e) {


        // Find the button element, whether the click was on the button or its child elements
        const button = e.target.closest('.btn-action.delete');
        if (!button) {

            return;
        }



        const roleId = button.dataset.roleId;


        if (!roleId) {
            notifications.show('Invalid role ID', 'error');
            return;
        }

        // Find the role row using the data-role-id attribute
        const roleRow = document.querySelector(`tr[data-role-id="${roleId}"]`);
        if (!roleRow) {

            notifications.show('Could not find role information', 'error');
            return;
        }

        const nameElement = roleRow.querySelector('h6');
        if (!nameElement) {
            notifications.show('Could not find role name', 'error');
            return;
        }
        const roleName = nameElement.textContent;


        try {
            // First check if the role has users
            const response = await fetch(`${this.baseUrl}/${roleId}/users`);
            if (!response.ok) {
                throw new Error('Failed to check role users');
            }

            const data = await response.json();


            if (data.users && data.users.length > 0) {
                // Role has users, show warning and open manage users modal
                notifications.show(
                    'Cannot delete role with assigned users. Please remove all users from this role first.',
                    'warning'
                );

                // Open the manage users modal
                const manageUsersBtn = document.querySelector(`.manage-users[data-role-id="${roleId}"]`);
                if (manageUsersBtn) {
                    manageUsersBtn.click();
                }
                return;
            }

            // No users in role, show delete confirmation modal
            const modal = document.getElementById('deleteRoleConfirmModal');
            if (modal) {
                // Set the role name in the modal
                const roleNameElement = modal.querySelector('#deleteRoleName');
                if (roleNameElement) {
                    roleNameElement.textContent = roleName;
                }
                // Store both role ID and row reference
                modal.dataset.roleId = roleId;
                modal.dataset.roleRow = roleRow.outerHTML; // Store row HTML for reference
                // Show the modal
                if (this.deleteRoleConfirmModalInstance) {
                    this.deleteRoleConfirmModalInstance.show();
                } else {

                }
            } else {

            }
        } catch (error) {

            notifications.show(error.message || 'Failed to check role users', 'error');
        }
    }

    async executeDeleteRole(roleId, roleRow) {
        try {
            // Double check if role has users before deletion
            const checkResponse = await fetch(`${this.baseUrl}/${roleId}/users`);
            if (!checkResponse.ok) {
                throw new Error('Failed to verify role users');
            }

            const checkData = await checkResponse.json();
            if (checkData.users && checkData.users.length > 0) {
                notifications.show(
                    'Cannot delete role with assigned users. Please remove all users from this role first.',
                    'warning'
                );
                // Open the manage users modal
                const manageUsersBtn = document.querySelector(`.manage-users[data-role-id="${roleId}"]`);
                if (manageUsersBtn) {
                    manageUsersBtn.click();
                }
                return;
            }

            // Proceed with deletion
            const response = await fetch(`${this.baseUrl}/${roleId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to delete role');
            }

            notifications.show('Role deleted successfully', 'success');

            // Remove role row from table with animation
            roleRow.style.opacity = '0';
            roleRow.style.transform = 'translateX(20px)';
            roleRow.style.transition = 'all 0.3s ease-out';

            setTimeout(() => {
                roleRow.remove();
                this.updateRoleCount();
            }, 300);

        } catch (error) {
            notifications.show(error.message || 'Failed to delete role', 'error');

        }
    }

    async handleStatusToggle(e) {
        const toggle = e.currentTarget;
        const roleId = toggle.dataset.roleId;
        const isActive = toggle.checked;
        const roleRow = toggle.closest('tr');

        if (!roleRow) {

            return;
        }

        const roleNameElement = roleRow.querySelector('h6');
        const roleName = roleNameElement ? roleNameElement.textContent : 'Unknown Role';

        // Store original state in case we need to revert
        const originalState = toggle.checked;

        try {
            // Disable the toggle while processing
            toggle.disabled = true;

            // Remove focus to prevent focus-related issues
            toggle.blur();

            const response = await fetch(`${this.baseUrl}/${roleId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: isActive })
            });

            const data = await response.json();

            // Check if the response indicates an error
            if (!response.ok || data.status === 'error') {
                // Use requestAnimationFrame to prevent layout thrashing
                requestAnimationFrame(() => {
                    toggle.checked = originalState;
                    window.notifications.show(
                        data.message || `Failed to ${isActive ? 'activate' : 'deactivate'} role "${roleName}"`,
                        'error'
                    );
                });
                return;
            }

            // Success case - update UI in the next animation frame
            requestAnimationFrame(() => {
                window.notifications.show(
                    `Role "${roleName}" ${isActive ? 'activated' : 'deactivated'} successfully`,
                    'success'
                );

                // Update the row's visual state if needed
                if (isActive) {
                    roleRow.classList.remove('table-secondary');
                    roleRow.classList.add('table-success');
                } else {
                    roleRow.classList.remove('table-success');
                    roleRow.classList.add('table-secondary');
                }

                // Update stats cards
                this.updateRoleCount();
            });

        } catch (error) {
            // Handle unexpected errors in the next frame
            requestAnimationFrame(() => {
                toggle.checked = originalState;
                window.notifications.show(
                    `An unexpected error occurred while updating role "${roleName}"`,
                    'error'
                );

            });
        } finally {
            // Re-enable the toggle in the next frame
            requestAnimationFrame(() => {
                toggle.disabled = false;
                // Ensure the toggle is not focused to prevent keyboard events
                if (document.activeElement === toggle) {
                    toggle.blur();
                }
            });
        }
    }

    async handleManageUsers(e) {
        const roleId = e.currentTarget.dataset.roleId;
        const roleName = e.currentTarget.dataset.roleName;

        try {
            const response = await fetch(`${this.baseUrl}/${roleId}/users`);
            if (!response.ok) {
                throw new Error('Failed to fetch role users');
            }

            const data = await response.json();
            this.populateManageUsersModal(data, roleName);

            if (this.manageUsersModalInstance) {
                this.manageUsersModalInstance.show();
            }
        } catch (error) {
            window.notifications.show('Failed to load role users', 'error');

        }
    }

    async saveRoleChanges() {
        const form = document.getElementById('editRoleForm');
        if (!form) {

            return;
        }

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('.spinner-border');
        const errorDiv = document.getElementById('editRoleFormErrors');

        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        if (errorDiv) {
            errorDiv.classList.add('d-none');
            errorDiv.innerHTML = '';
        }

        try {
            // Get role ID from form
            const roleIdInput = form.querySelector('input[name="role_id"]');
            const roleId = roleIdInput ? roleIdInput.value : null;

            // Get all form data including permissions
            const formData = new FormData(form);

            // Get all checked permission checkboxes (not status or other checkboxes)
            const permissionCheckboxes = form.querySelectorAll('input[name="permissions[]"]:checked');
            if (permissionCheckboxes.length === 0) {
                throw new Error('At least one permission is required');
            }

            // Add each permission to form data
            permissionCheckboxes.forEach(checkbox => {
                formData.append('permissions[]', checkbox.value);
            });

            // Ensure status is explicitly passed
            const statusCheckbox = form.querySelector('input[name="status"]');
            if (statusCheckbox) {
                // Determine true active state from logical checkbox checked property
                formData.set('status', statusCheckbox.checked ? '1' : '0');
            }

            // Submit the form
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const responseData = await response.json();


            if (!response.ok) {
                // Handle validation errors
                if (response.status === 422 && responseData.errors) {
                    const errorMessages = Object.values(responseData.errors).flat().join('<br>');
                    if (errorDiv) {
                        errorDiv.innerHTML = errorMessages;
                        errorDiv.classList.remove('d-none');
                    }
                    throw new Error('Validation failed');
                }
                throw new Error(responseData.message || 'Failed to update role');
            }

            if (responseData.status === 'success') {
                // Update the role row in the table
                const roleRow = document.querySelector(`tr[data-role-id="${roleId}"]`);
                if (roleRow && responseData.role) {
                    // Update role name
                    const nameCell = roleRow.querySelector('h6');
                    if (nameCell) nameCell.textContent = responseData.role.name;

                    // Update description (if exists)
                    const descCell = roleRow.querySelector('td:nth-child(2) span');
                    if (descCell && responseData.role.description) {
                        descCell.textContent = responseData.role.description;
                    }

                    // Update status toggle (if exists)
                    const statusToggle = roleRow.querySelector('.role-status-toggle');
                    if (statusToggle && responseData.role.status !== undefined) {
                        const isCurrentlyActive = responseData.role.status === '1' ||
                            responseData.role.status === 1 ||
                            responseData.role.status === true;

                        // Update toggle state if it differs from the saved state
                        if (statusToggle.checked !== isCurrentlyActive) {
                            statusToggle.checked = isCurrentlyActive;
                            // Trigger change event to update UI/stats but avoid manual deactivations
                            statusToggle.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }

                    // Update permissions badges
                    const permissionsCell = roleRow.querySelector('td:nth-child(3) .d-flex');
                    if (permissionsCell && responseData.role.permissions) {
                        const permissions = responseData.role.permissions;
                        permissionsCell.innerHTML = permissions.slice(0, 3).map(p => `
                            <span class="permission-badge" data-bs-toggle="tooltip" title="${p.description || ''}">
                                <i class="fas fa-${p.icon || 'check-circle'}"></i>
                                ${p.name}
                            </span>
                        `).join('');

                        if (permissions.length > 3) {
                            permissionsCell.innerHTML += `
                                <span class="permission-badge">
                                    <i class="fas fa-ellipsis-h"></i>
                                    +${permissions.length - 3} more
                                </span>
                            `;
                        }

                        // Reinitialize tooltips
                        const tooltipTriggerList = [].slice.call(permissionsCell.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
                    }

                    // Add a subtle animation to highlight the updated row
                    roleRow.style.transition = 'background-color 0.5s ease';
                    roleRow.style.backgroundColor = '#e8f5e9';
                    setTimeout(() => {
                        roleRow.style.backgroundColor = '';
                    }, 1000);
                }

                notifications.show('Role updated successfully', 'success');
                this.closeModal('editRoleModal');
            } else {
                throw new Error(responseData.message || 'Failed to update role');
            }
        } catch (error) {

            notifications.show(error.message || 'Failed to update role', 'error');

            // Show form errors if any
            const errorDiv = form.querySelector('#editRoleFormErrors');
            if (errorDiv) {
                errorDiv.textContent = error.message;
                errorDiv.classList.remove('d-none');
            }
        } finally {
            // Reset loading state
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        }
    }

    populateEditModal(data, isEditable = true) {


        const form = document.getElementById('editRoleForm');
        if (!form) {

            return;
        }

        // Update the form action with the correct role ID
        if (data.id) {
            form.action = form.action.replace('__ROLE_ID__', data.id);
        }

        // Set basic role data with null checks
        const roleIdInput = form.querySelector('input[name="role_id"]');
        const nameInput = form.querySelector('input[name="name"]');
        const descriptionInput = form.querySelector('textarea[name="description"]');
        const statusCheckbox = form.querySelector('input[name="status"]');

        // Role statistics elements
        const usersCountEl = document.getElementById('roleUsersCount');
        const permissionsCountEl = document.getElementById('rolePermissionsCount');
        const roleStatusBadge = document.getElementById('roleStatusBadge');

        // Set basic form values
        if (roleIdInput) roleIdInput.value = data.id || '';
        if (nameInput) nameInput.value = data.name || '';
        if (descriptionInput) descriptionInput.value = data.description || '';

        // Set status
        const isActive = data.status === 1 || data.status === '1' || data.status === true;
        if (statusCheckbox) {
            statusCheckbox.checked = isActive;
        }

        // Update statistics
        if (usersCountEl) {
            usersCountEl.textContent = data.users_count || 0;
        }

        if (permissionsCountEl && data.permissions) {
            permissionsCountEl.textContent = data.permissions.length || 0;
        }

        // Update status badge
        if (roleStatusBadge) {
            if (isActive) {
                roleStatusBadge.className = 'badge bg-success';
                roleStatusBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> Active';
            } else {
                roleStatusBadge.className = 'badge bg-secondary';
                roleStatusBadge.innerHTML = '<i class="fas fa-times-circle me-1"></i> Inactive';
            }
        }

        // Update role information section
        const roleInfoSection = document.querySelector('.role-info-section');
        if (roleInfoSection) {
            const usersCount = data.users_count || 0;
            const userText = usersCount === 1 ? 'user' : 'users';
            const manageUsersLink = `<a href="#" class="text-primary fw-medium manage-users-link" data-role-id="${data.id}">Manage users</a>`;

            roleInfoSection.innerHTML = `
                <i class="fas fa-info-circle me-2 mt-1 text-primary"></i>
                <div>
                    <h6 class="alert-heading mb-1">Role Information</h6>
                    <p class="mb-0 small">
                        ${usersCount} ${userText} have this role assigned. ${manageUsersLink}
                    </p>
                </div>
            `;

            // Add event listener to the manage users link
            const manageLink = roleInfoSection.querySelector('.manage-users-link');
            if (manageLink) {
                manageLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    const roleId = e.currentTarget.getAttribute('data-role-id');
                    const roleName = data.name || 'this role';
                    this.loadRoleUsers(roleId, roleName);
                });
            }
        }

        // Create a map of permission IDs for faster lookup
        const permissionIds = new Set((data.permissions || []).map(p => p.id));


        // Get all checkboxes
        const checkboxes = form.querySelectorAll('.permission-checkbox');


        // First, uncheck all checkboxes
        checkboxes.forEach(cb => {
            cb.checked = false;
        });

        // Check the appropriate checkboxes
        checkboxes.forEach(checkbox => {
            const permissionId = parseInt(checkbox.value);


            if (permissionIds.has(permissionId)) {
                checkbox.checked = true;
                // Trigger change event to update module select all
                const event = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(event);
            }
        });

        // Update module select all checkboxes
        const modules = new Set((data.permissions || []).map(p => p.module));
        modules.forEach(module => {
            const moduleCheckbox = form.querySelector(`.module-select-all[data-module="${module}"]`);
            if (moduleCheckbox) {
                const modulePermissions = form.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
                const checkedPermissions = form.querySelectorAll(`.permission-checkbox[data-module="${module}"]:checked`);
                const shouldBeChecked = modulePermissions.length > 0 && modulePermissions.length === checkedPermissions.length;

                moduleCheckbox.checked = shouldBeChecked;
                // Trigger change event to update main select all
                const event = new Event('change', { bubbles: true });
                moduleCheckbox.dispatchEvent(event);
            }
        });

        // Update main select all checkbox
        const mainSelectAll = form.querySelector('#addSelectAllPermissions, #selectAllPermissions, #editSelectAllPermissions');
        if (mainSelectAll) {
            const allPermissions = form.querySelectorAll('.permission-checkbox');
            const allChecked = form.querySelectorAll('.permission-checkbox:checked');
            const shouldBeChecked = allPermissions.length > 0 && allPermissions.length === allChecked.length;
            mainSelectAll.checked = shouldBeChecked;
        }

        // Handle Read-Only State
        const submitBtn = form.querySelector('button[type="submit"]');
        const inputs = form.querySelectorAll('input, textarea, select, button');
        const alertContainer = form.querySelector('.alert-container') || document.createElement('div');

        if (!form.querySelector('.alert-container')) {
            alertContainer.className = 'alert-container mb-3 px-4 pt-3';
            const modalBody = form.querySelector('.modal-body');
            if (modalBody) {
                modalBody.insertBefore(alertContainer, modalBody.firstChild);
            } else {
                form.insertBefore(alertContainer, form.firstChild);
            }
        }

        if (!isEditable) {
            // Disable all inputs
            inputs.forEach(input => {
                if (!input.classList.contains('btn-close')) { // Don't disable close button
                    input.disabled = true;
                }
            });

            // Show warning message
            alertContainer.style.display = 'block';
            alertContainer.innerHTML = `
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-lock me-2"></i>
                    <div>
                        <strong>Read Only:</strong> This role cannot be modified.
                    </div>
                </div>
            `;

            // Hide save button
            if (submitBtn) submitBtn.style.display = 'none';
        } else {
            // Enable all inputs
            inputs.forEach(input => input.disabled = false);
            alertContainer.innerHTML = '';
            alertContainer.style.display = 'none';
            if (submitBtn) submitBtn.style.display = 'block';
        }
    }

    updateRoleCard(role) {
        const roleCard = document.querySelector(`.role-card[data-role-id="${role.id}"]`);
        if (!roleCard) return;

        // Update role name
        roleCard.querySelector('.role-name').textContent = role.name;

        // Update role description
        roleCard.querySelector('.role-description').textContent = role.description;

        // Update permissions count
        const permissionsCount = roleCard.querySelector('.permissions-count');
        if (permissionsCount) {
            permissionsCount.textContent = `${role.permissions_count} Permissions`;
        }

        // Update permissions list
        const permissionsList = roleCard.querySelector('.permissions-list');
        if (permissionsList) {
            permissionsList.innerHTML = role.permissions
                .map(p => `<span class="permission-badge">${p.name}</span>`)
                .join('');
        }

        // Add animation
        roleCard.style.animation = 'none';
        roleCard.offsetHeight; // Trigger reflow
        roleCard.style.animation = 'fadeIn 0.5s ease';
    }

    handleSearch(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const roleName = row.querySelector('h6').textContent.toLowerCase();
            const roleDescription = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const permissions = Array.from(row.querySelectorAll('.permission-badge'))
                .map(badge => badge.textContent.toLowerCase());

            const isVisible = roleName.includes(searchTerm) ||
                roleDescription.includes(searchTerm) ||
                permissions.some(p => p.includes(searchTerm));

            row.style.display = isVisible ? '' : 'none';
        });
    }

    handleStatusFilter(e) {
        const status = e.target.value;
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const statusToggle = row.querySelector('.role-status-toggle');
            if (status === '') {
                row.style.display = '';
            } else {
                row.style.display = statusToggle.checked === (status === '1') ? '' : 'none';
            }
        });
    }

    updateRoleCount() {
        try {
            const totalRoles = document.querySelectorAll('tbody tr').length;
            const activeRoles = document.querySelectorAll('.role-status-toggle:checked').length;
            const inactiveRoles = totalRoles - activeRoles;

            // Safely update stats cards if they exist
            const totalElement = document.querySelector('.stats-card:nth-child(1) h2');
            const activeElement = document.querySelector('.stats-card:nth-child(2) h2');
            const inactiveElement = document.querySelector('.stats-card:nth-child(3) h2');

            if (totalElement) totalElement.textContent = totalRoles;
            if (activeElement) activeElement.textContent = activeRoles;
            if (inactiveElement) inactiveElement.textContent = inactiveRoles;

        } catch (error) {

            // Don't throw error, just log it
        }
    }

    handleUserSearch(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#availableUsersTable tbody tr');
        rows.forEach(row => {
            const name = row.cells[0].textContent.toLowerCase();
            const email = row.cells[1].textContent.toLowerCase();
            row.style.display = name.includes(searchTerm) || email.includes(searchTerm) ? '' : 'none';
        });
    }

    handleAddUserToRole() {
        const roleId = document.querySelector('#manageUsersModal').dataset.roleId;
        this.loadAvailableUsers(roleId);
        // Use the stored modal instance with safe check
        if (this.addUserToRoleModalInstance) {
            this.addUserToRoleModalInstance.show();
        }
    }

    loadRoleUsers(roleId, roleName) {
        const modal = document.getElementById('manageUsersModal');
        modal.dataset.roleId = roleId;
        modal.querySelector('.modal-title').textContent = `Manage Users - ${roleName}`;

        const tbody = modal.querySelector('#roleUsersTable tbody');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';

        fetch(`${this.baseUrl}/${roleId}/users`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch role users');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    tbody.innerHTML = data.users.map(user => `
                        <tr>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>
                                <span class="badge bg-${user.status === 'active' ? 'success' : 'danger'}">
                                    ${user.status}
                                </span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-danger btn-sm remove-user" 
                                        data-user-id="${user.id}">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');

                    tbody.querySelectorAll('.remove-user').forEach(button => {
                        button.addEventListener('click', () => {
                            const userId = button.dataset.userId;
                            this.removeUserFromRole(roleId, userId);
                        });
                    });
                } else {
                    throw new Error(data.message || 'Failed to load users');
                }
            })
            .catch(error => {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error loading users</td></tr>';
                window.notifications.show('Failed to load role users', 'error');

            });
    }

    loadAvailableUsers(roleId) {
        const tbody = document.querySelector('#availableUsersTable tbody');
        tbody.innerHTML = '<tr><td colspan="3" class="text-center">Loading...</td></tr>';

        fetch(`${this.baseUrl}/${roleId}/available-users`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch available users');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    tbody.innerHTML = data.users.map(user => `
                        <tr>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-primary btn-sm add-user" 
                                        data-user-id="${user.id}">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');

                    tbody.querySelectorAll('.add-user').forEach(button => {
                        button.addEventListener('click', () => {
                            const userId = button.dataset.userId;
                            this.addUserToRole(roleId, userId);
                        });
                    });
                } else {
                    throw new Error(data.message || 'Failed to load available users');
                }
            })
            .catch(error => {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error loading users</td></tr>';
                window.notifications.show('Failed to load available users', 'error');

            });
    }

    addUserToRole(roleId, userId) {
        fetch(`${this.baseUrl}/${roleId}/users`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ user_id: userId })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to add user to role');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    bootstrap.Modal.getInstance(document.getElementById('addUserToRoleModal')).hide();
                    this.loadRoleUsers(roleId, document.querySelector('#manageUsersModal').dataset.roleName);
                    window.notifications.show('User added to role successfully', 'success');
                } else {
                    throw new Error(data.message || 'Failed to add user to role');
                }
            })
            .catch(error => {
                window.notifications.show(error.message || 'Failed to add user to role', 'error');

            });
    }

    removeUserFromRole(roleId, userId) {
        const modal = document.getElementById('removeUserConfirmModal');
        if (!modal) return;

        // Store the IDs for the confirmation
        modal.dataset.roleId = roleId;
        modal.dataset.userId = userId;

        // Show the confirmation modal
        if (this.removeUserConfirmModalInstance) {
            this.removeUserConfirmModalInstance.show();
        }
    }

    executeRemoveUser(roleId, userId) {
        fetch(`${this.baseUrl}/${roleId}/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Failed to remove user from role');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    // Get the role name from the manage users modal
                    const manageUsersModal = document.getElementById('manageUsersModal');
                    const roleName = manageUsersModal.dataset.roleName;

                    // Reload the users list
                    this.loadRoleUsers(roleId, roleName);

                    notifications.show('User removed from role successfully', 'success');
                    this.closeModal('removeUserConfirmModal');
                } else {
                    throw new Error(data.message || 'Failed to remove user from role');
                }
            })
            .catch(error => {
                notifications.show(error.message || 'Failed to remove user from role', 'error');

            });
    }

    populateManageUsersModal(data, roleName) {
        const modal = document.getElementById('manageUsersModal');
        modal.dataset.roleName = roleName;
        modal.querySelector('.modal-title').textContent = `Manage Users - ${roleName}`;

        const tbody = modal.querySelector('#roleUsersTable tbody');
        if (data.users && data.users.length > 0) {
            tbody.innerHTML = data.users.map(user => `
                <tr>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>
                        <span class="badge bg-${user.status === 'active' ? 'success' : 'danger'}">
                            ${user.status}
                        </span>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-danger btn-sm remove-user" 
                                data-user-id="${user.id}">
                            <i class="fas fa-user-minus"></i>
                        </button>
                    </td>
                </tr>
            `).join('');

            // Add event listeners to remove buttons
            tbody.querySelectorAll('.remove-user').forEach(button => {
                button.addEventListener('click', () => {
                    const userId = button.dataset.userId;
                    this.removeUserFromRole(modal.dataset.roleId, userId);
                });
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">No users assigned to this role</td></tr>';
        }
    }

    applyTemplate(template) {
        // Get the add role form
        const form = document.getElementById('addRoleForm');
        if (!form) {

            return;
        }

        // Template configurations
        const templates = {
            manager: {
                name: 'manager',
                description: 'Full access to all system features with user management capabilities.',
                permissions: [
                    'system.view_dashboard',
                    'system.manage_settings',
                    'user.view',
                    'user.create',
                    'user.edit',
                    'user.delete',
                    'role.view',
                    'role.create',
                    'role.edit',
                    'role.delete'
                ]
            },
            staff: {
                name: 'staff',
                description: 'Standard access to core system features for daily operations.',
                permissions: [
                    'system.view_dashboard',
                    'inventory.view',
                    'inventory.view_reports',
                    'pos.access',
                    'pos.process_sales'
                ]
            },
            pos_staff: {
                name: 'pos_staff',
                description: 'Access to point of sale and basic inventory management.',
                permissions: [
                    'system.view_dashboard',
                    'pos.access',
                    'pos.process_sales',
                    'pos.view_reports',
                    'inventory.view',
                    'inventory.view_reports'
                ]
            },
            inventory_staff: {
                name: 'inventory_staff',
                description: 'Full access to inventory management and stock control.',
                permissions: [
                    'system.view_dashboard',
                    'inventory.view',
                    'inventory.create',
                    'inventory.edit',
                    'inventory.delete',
                    'inventory.manage_stock',
                    'inventory.manage_suppliers',
                    'inventory.view_reports',
                    'inventory.manage_categories'
                ]
            }
        };

        const templateData = templates[template];
        if (!templateData) {

            return;
        }

        // Hide the templates modal
        const templatesModal = document.getElementById('roleTemplatesModal');
        if (templatesModal) {
            const bsTemplatesModal = bootstrap.Modal.getInstance(templatesModal);
            if (bsTemplatesModal) {
                bsTemplatesModal.hide();
            }
        }

        // Get the add role modal
        const addRoleModal = document.getElementById('addRoleModal');
        if (!addRoleModal) {

            return;
        }

        // Create new modal instance if it doesn't exist
        let bsAddRoleModal = bootstrap.Modal.getInstance(addRoleModal);
        if (!bsAddRoleModal) {
            bsAddRoleModal = new bootstrap.Modal(addRoleModal);
        }

        // Populate form with template data
        form.querySelector('[name="name"]').value = templateData.name;
        form.querySelector('[name="description"]').value = templateData.description;
        form.querySelector('[name="status"]').value = '1'; // Set as active by default

        // Reset all permission checkboxes
        form.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Check permissions based on template
        templateData.permissions.forEach(permissionName => {
            const checkbox = form.querySelector(`.permission-checkbox[data-permission-name="${permissionName}"]`);
            if (checkbox) {
                checkbox.checked = true;
                // Trigger change event to update module select all
                const event = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(event);
            }
        });

        // Update module select all checkboxes
        form.querySelectorAll('.module-select-all').forEach(moduleCheckbox => {
            const module = moduleCheckbox.dataset.module;
            const modulePermissions = form.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
            const checkedPermissions = form.querySelectorAll(`.permission-checkbox[data-module="${module}"]:checked`);
            moduleCheckbox.checked = modulePermissions.length > 0 && modulePermissions.length === checkedPermissions.length;
        });

        // Update main select all checkbox
        const mainSelectAll = form.querySelector('#addSelectAllPermissions, #selectAllPermissions, #editSelectAllPermissions');
        if (mainSelectAll) {
            const allPermissions = form.querySelectorAll('.permission-checkbox');
            const allChecked = form.querySelectorAll('.permission-checkbox:checked');
            mainSelectAll.checked = allPermissions.length > 0 && allPermissions.length === allChecked.length;
        }

        // Show the add role modal
        bsAddRoleModal.show();
    }

    initializeAddRoleForm() {
        const form = document.getElementById('addRoleForm');
        if (!form) return;

        // Add Permission Search
        const searchInput = document.getElementById('addPermissionSearch');
        const clearSearchBtn = document.getElementById('addClearSearch');

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const permissionItems = form.querySelectorAll('.permission-item');
                const accordionItems = form.querySelectorAll('.accordion-item');

                // Show/hide clear button
                if (clearSearchBtn) {
                    clearSearchBtn.style.display = searchTerm ? 'block' : 'none';
                }

                permissionItems.forEach(item => {
                    const name = item.querySelector('.permission-name').textContent.toLowerCase();
                    const isVisible = name.includes(searchTerm);
                    item.closest('.col-md-6').style.display = isVisible ? '' : 'none';
                });

                // Handle empty groups
                accordionItems.forEach(group => {
                    const visiblePermissions = group.querySelectorAll('.col-md-6:not([style*="display: none"])');
                    group.style.display = visiblePermissions.length > 0 ? '' : 'none';

                    // Auto expand groups with matches
                    const collapse = group.querySelector('.accordion-collapse');
                    if (searchTerm && visiblePermissions.length > 0) {
                        collapse.classList.add('show');
                    } else if (!searchTerm) {
                        collapse.classList.remove('show');
                    }
                });
            });
        }

        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', () => {
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                }
            });
        }

        // Expand/Collapse All
        const expandAllBtn = document.getElementById('addExpandAll');
        const collapseAllBtn = document.getElementById('addCollapseAll');

        if (expandAllBtn) {
            expandAllBtn.addEventListener('click', () => {
                form.querySelectorAll('.accordion-collapse').forEach(collapse => {
                    collapse.classList.add('show');
                });
            });
        }

        if (collapseAllBtn) {
            collapseAllBtn.addEventListener('click', () => {
                form.querySelectorAll('.accordion-collapse').forEach(collapse => {
                    collapse.classList.remove('show');
                });
            });
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (!form.checkValidity()) {
                event.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const spinner = submitBtn.querySelector('.spinner-border');
            submitBtn.disabled = true;
            spinner.classList.remove('d-none');

            try {
                // Get form data
                const formData = new FormData(form);
                const statusCheckbox = form.querySelector('input[name="status"]');

                // Convert form data to JSON
                const data = {
                    name: formData.get('name'),
                    description: formData.get('description'),
                    status: statusCheckbox ? (statusCheckbox.checked ? '1' : '0') : '1',
                    permissions: Array.from(form.querySelectorAll('.permission-checkbox:checked')).map(cb => parseInt(cb.value))
                };



                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const responseData = await response.json();


                if (!response.ok) {
                    throw new Error(responseData.message || 'Failed to create role');
                }

                notifications.show('Role created successfully', 'success');
                this.closeModal('addRoleModal');

                // Refresh the roles list
                window.location.reload();

            } catch (error) {

                notifications.show(error.message || 'Failed to create role', 'error');

                // Show form errors if any
                const errorDiv = form.querySelector('#addRoleFormErrors');
                if (errorDiv) {
                    errorDiv.textContent = error.message;
                    errorDiv.classList.remove('d-none');
                }
            } finally {
                // Reset loading state
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
            }
        });
    }

    initializeEditRoleForm() {
        const form = document.getElementById('editRoleForm');
        if (!form) return;

        // Add Permission Search
        const searchInput = document.getElementById('permissionSearch');
        const clearSearchBtn = document.getElementById('clearSearch');

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                if (clearSearchBtn) {
                    clearSearchBtn.style.display = searchTerm ? 'block' : 'none';
                }
                this.applyFilters();
            });
        }

        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', () => {
                if (searchInput) {
                    searchInput.value = '';
                    clearSearchBtn.style.display = 'none';
                    this.applyFilters();
                }
            });
        }

        // Add Permission Filter Options
        const filterOptions = form.querySelectorAll('.filter-option');
        filterOptions.forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                filterOptions.forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');

                const dropdownButton = document.getElementById('filterDropdown');
                if (dropdownButton) {
                    dropdownButton.innerHTML = `<i class="fas fa-filter me-1"></i> Filter: ${option.textContent}`;
                }
                this.applyFilters();
            });
        });

        // Expand/Collapse All
        const expandAllBtn = document.getElementById('expandAll');
        const collapseAllBtn = document.getElementById('collapseAll');

        if (expandAllBtn) {
            expandAllBtn.addEventListener('click', () => {
                form.querySelectorAll('.accordion-collapse').forEach(collapse => {
                    collapse.classList.add('show');
                });
            });
        }

        if (collapseAllBtn) {
            collapseAllBtn.addEventListener('click', () => {
                form.querySelectorAll('.accordion-collapse').forEach(collapse => {
                    collapse.classList.remove('show');
                });
            });
        }
    }

    applyFilters() {
        const form = document.getElementById('editRoleForm');
        if (!form) return;

        const searchInput = document.getElementById('permissionSearch');
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

        const activeFilterOption = form.querySelector('.filter-option.active');
        const activeFilter = activeFilterOption ? activeFilterOption.dataset.filter : 'all';

        const permissionColElements = form.querySelectorAll('.col-md-6[data-group]');
        const accordionItems = form.querySelectorAll('.accordion-item');

        permissionColElements.forEach(col => {
            const checkbox = col.querySelector('.permission-checkbox');
            const isChecked = checkbox ? checkbox.checked : false;
            const name = col.querySelector('.permission-name').textContent.toLowerCase();
            const groupAttr = col.dataset.group;

            // Check Search Term
            const matchesSearch = name.includes(searchTerm);

            // Check Filter option
            let matchesFilter = false;
            if (activeFilter === 'all') {
                matchesFilter = true;
            } else if (activeFilter === 'selected') {
                matchesFilter = isChecked;
            } else if (activeFilter === 'unselected') {
                matchesFilter = !isChecked;
            } else if (activeFilter.startsWith('group-')) {
                matchesFilter = groupAttr === activeFilter;
            }

            const isVisible = matchesSearch && matchesFilter;
            col.style.display = isVisible ? '' : 'none';
        });

        // Hide empty accordion groups
        let visibleGroupsCount = 0;
        accordionItems.forEach(group => {
            const visiblePermissions = group.querySelectorAll('.col-md-6[data-group]:not([style*="display: none"])');
            const hasVisiblePermissions = visiblePermissions.length > 0;
            group.style.display = hasVisiblePermissions ? '' : 'none';
            if (hasVisiblePermissions) visibleGroupsCount++;

            // Auto expand groups with matches
            const collapse = group.querySelector('.accordion-collapse');
            if (collapse) {
                if ((searchTerm || activeFilter !== 'all') && hasVisiblePermissions) {
                    collapse.classList.add('show');
                } else if (!searchTerm && activeFilter === 'all') {
                    collapse.classList.remove('show');
                }
            }
        });

        // Show/hide no permissions found message
        const noPermissionsFound = document.getElementById('noPermissionsFound');
        if (noPermissionsFound) {
            noPermissionsFound.classList.toggle('d-none', visibleGroupsCount > 0);
        }
    }

    // Helper method to properly close modals
    closeModal(modalId) {
        const modalElement = document.getElementById(modalId);
        if (!modalElement) return;

        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
            // Clean up backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
    }
}

// Initialize role management when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.roleManagement = new RoleManagement();
});