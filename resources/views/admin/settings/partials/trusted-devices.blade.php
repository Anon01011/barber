<div class="trusted-devices-container">
    <!-- Header / Intro -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fas fa-shield-alt text-primary"></i>
                Trusted Devices & Quick PIN Login
            </h5>
            <p class="text-muted small mb-0">
                Manage browsers and devices authorized for fast 6-digit PIN login without entering your full email and password.
            </p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button type="button" class="btn btn-outline-success btn-sm d-none" id="btnTrustCurrentDevice">
                <i class="fas fa-shield-alt me-1"></i> Trust This Browser
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm" id="btnRevokeAllDevices">
                <i class="fas fa-ban me-1"></i> Revoke All Other Devices
            </button>
        </div>
    </div>

    <!-- PIN Management Section Card -->
    <div class="card border shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 52px; height: 52px; background: #eef2ff; color: #4f46e5; font-size: 22px;">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="fw-bold mb-0 text-dark">6-Digit Security PIN</h6>
                            <span id="pinStatusBadge" class="badge rounded-pill bg-secondary px-2.5 py-1">Checking...</span>
                        </div>
                        <div class="text-muted small mt-1">
                            Use this secure PIN on any of your verified trusted devices to sign in instantly.
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-shrink-0">
                    <button type="button" class="btn btn-primary btn-sm px-3" id="btnOpenChangePinModal">
                        <i class="fas fa-pen me-1" id="iconChangePin"></i>
                        <span id="textChangePin">Change PIN</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="btnTogglePin">
                        <i class="fas fa-toggle-on me-1" id="iconTogglePin"></i>
                        <span id="btnTogglePinText">Disable PIN</span>
                    </button>
                </div>
            </div>

            <!-- PIN Security Requirements Info -->
            <div class="mt-3 p-3 bg-light rounded-3 border">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="small fw-semibold text-dark mb-1">
                            <i class="fas fa-info-circle text-info me-1"></i> PIN Security Standard:
                        </div>
                        <div class="text-muted small">
                            Must be exactly 6 numeric digits. Predictable numbers (123456) and repeated sequences (000000, 112233) are automatically rejected.
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                        <span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1 small">
                            Argon2id / Bcrypt Hashed
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registered Devices Section -->
    <div class="card border shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="fas fa-laptop text-primary"></i>
                Registered Trusted Devices (<span id="deviceCount">0</span>)
            </h6>
            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-muted" id="btnRefreshDevices">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>

        <div class="card-body p-0">
            <!-- Loading state -->
            <div id="deviceLoading" class="text-center py-5">
                <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                <span class="text-muted small">Loading trusted devices...</span>
            </div>

            <!-- Empty state -->
            <div id="deviceEmpty" class="text-center py-5 d-none">
                <div class="text-muted mb-2" style="font-size: 32px;"><i class="fas fa-desktop"></i></div>
                <h6 class="fw-bold text-dark">No Trusted Devices Registered</h6>
                <p class="text-muted small mb-0">When you sign in on a browser and enable PIN login, it will appear here.</p>
            </div>

            <!-- Devices Table -->
            <div class="table-responsive d-none" id="deviceTableContainer">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-muted">
                        <tr>
                            <th class="ps-4">Device Name</th>
                            <th>Browser & OS</th>
                            <th>Salon Context</th>
                            <th>First Registered</th>
                            <th>Last Active</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="deviceTableBody" class="small">
                        <!-- Populated via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: Change / Set PIN (DIV-based to prevent nested form drops) -->
<!-- ============================================================== -->
<div class="modal fade" id="modalChangePin" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-key text-primary"></i>
                    <span id="modalChangePinTitle">Change Security PIN</span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="alert alert-danger d-none py-2 px-3 small" id="changePinError"></div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Current Account Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control form-control-sm" id="inputCurrentPassword" placeholder="Enter your current password to verify identity" autocomplete="current-password">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">6-Digit PIN <span class="text-danger">*</span></label>
                    <input type="password" class="form-control form-control-sm text-center fw-bold" id="inputNewPin" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="••••••" style="letter-spacing: 6px; font-size: 18px;" autocomplete="off">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Confirm 6-Digit PIN <span class="text-danger">*</span></label>
                    <input type="password" class="form-control form-control-sm text-center fw-bold" id="inputConfirmPin" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="••••••" style="letter-spacing: 6px; font-size: 18px;" autocomplete="off">
                </div>

                <div class="p-2.5 bg-light rounded-3 border small text-muted">
                    <strong>Security Requirements:</strong> Must be exactly 6 numeric digits. Avoid trivial codes like 123456, 000000, 112233.
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-2.5 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm px-3" id="btnSubmitChangePin">
                    <span id="btnSubmitChangePinText">Save PIN</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: Rename Device -->
<!-- ============================================================== -->
<div class="modal fade" id="modalRenameDevice" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom px-3 py-2.5">
                <h6 class="modal-title fw-bold text-dark small">Rename Device</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <input type="hidden" id="renameDeviceId" value="">
                <div class="alert alert-danger d-none py-1.5 px-2.5 small" id="renameDeviceError"></div>
                <label class="form-label small fw-semibold text-secondary">Device Label</label>
                <input type="text" class="form-control form-control-sm" id="inputRenameDeviceName" maxlength="150" placeholder="e.g. Front Desk Terminal">
            </div>
            <div class="modal-footer border-top px-3 py-2 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm px-3" id="btnSubmitRename">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: Toggle / Disable / Enable PIN Confirmation -->
<!-- ============================================================== -->
<div class="modal fade" id="modalTogglePinConfirm" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <h6 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                    <i class="fas fa-exclamation-triangle" id="iconTogglePinModal"></i>
                    <span id="titleTogglePinModal">Disable PIN Login</span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="alert alert-danger d-none py-2 px-3 small" id="togglePinModalError"></div>
                <p class="text-muted small mb-3" id="descTogglePinModal">
                    Disabling PIN login means you will need to enter your full email address and password whenever you log into any trusted device.
                </p>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-secondary">Confirm with Account Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control form-control-sm" id="inputTogglePinPassword" placeholder="Enter your current password">
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-2.5 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm px-3" id="btnSubmitTogglePinAction">
                    <span id="btnSubmitTogglePinActionText">Disable PIN Login</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const csrfToken = '{{ csrf_token() }}';
        let pinData = { has_pin: false, is_enabled: false };

        // Helper to show modal safely
        function showModal(modalId) {
            const el = document.getElementById(modalId);
            if (!el) return;
            if (window.bootstrap && window.bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(el).show();
            } else if (window.jQuery && typeof window.jQuery(el).modal === 'function') {
                window.jQuery(el).modal('show');
            } else {
                el.classList.add('show');
                el.style.display = 'block';
            }
        }

        // Helper to hide modal safely
        function hideModal(modalId) {
            const el = document.getElementById(modalId);
            if (!el) return;
            if (window.bootstrap && window.bootstrap.Modal) {
                const inst = bootstrap.Modal.getInstance(el);
                if (inst) inst.hide();
                else bootstrap.Modal.getOrCreateInstance(el).hide();
            } else if (window.jQuery && typeof window.jQuery(el).modal === 'function') {
                window.jQuery(el).modal('hide');
            } else {
                el.classList.remove('show');
                el.style.display = 'none';
            }
        }

        function openChangePinDialog(isSetup) {
            const errBox = document.getElementById('changePinError');
            if (errBox) errBox.classList.add('d-none');

            const p1 = document.getElementById('inputCurrentPassword');
            const p2 = document.getElementById('inputNewPin');
            const p3 = document.getElementById('inputConfirmPin');
            if (p1) p1.value = '';
            if (p2) p2.value = '';
            if (p3) p3.value = '';

            const titleEl = document.getElementById('modalChangePinTitle');
            const btnText = document.getElementById('btnSubmitChangePinText');

            if (isSetup || !pinData.has_pin) {
                if (titleEl) titleEl.textContent = 'Setup 6-Digit Security PIN';
                if (btnText) btnText.textContent = 'Set PIN & Enable';
            } else {
                if (titleEl) titleEl.textContent = 'Change Security PIN';
                if (btnText) btnText.textContent = 'Save New PIN';
            }

            showModal('modalChangePin');
        }

        function submitChangePin() {
            const errBox = document.getElementById('changePinError');
            if (errBox) errBox.classList.add('d-none');

            const currentPass = (document.getElementById('inputCurrentPassword')?.value || '');
            const newPin = (document.getElementById('inputNewPin')?.value || '').trim();
            const confirmPin = (document.getElementById('inputConfirmPin')?.value || '').trim();

            if (!currentPass) {
                if (errBox) {
                    errBox.textContent = 'Please enter your current account password to authorize this change.';
                    errBox.classList.remove('d-none');
                }
                return;
            }

            if (!newPin || newPin.length !== 6 || !/^\d{6}$/.test(newPin)) {
                if (errBox) {
                    errBox.textContent = 'PIN must be exactly 6 numeric digits.';
                    errBox.classList.remove('d-none');
                }
                return;
            }

            if (newPin !== confirmPin) {
                if (errBox) {
                    errBox.textContent = 'New PIN and Confirm PIN do not match.';
                    errBox.classList.remove('d-none');
                }
                return;
            }

            const btn = document.getElementById('btnSubmitChangePin');
            if (btn) btn.disabled = true;

            fetch('{{ route("security.pin.change") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    current_password: currentPass,
                    pin: newPin,
                    pin_confirmation: confirmPin
                })
            })
            .then(res => res.json())
            .then(data => {
                if (btn) btn.disabled = false;
                if (data.success) {
                    hideModal('modalChangePin');
                    alert(data.message || 'PIN successfully saved!');
                    loadDevices();
                } else {
                    if (errBox) {
                        errBox.textContent = data.message || 'Failed to update PIN.';
                        errBox.classList.remove('d-none');
                    }
                }
            })
            .catch(() => {
                if (btn) btn.disabled = false;
                if (errBox) {
                    errBox.textContent = 'An unexpected network error occurred.';
                    errBox.classList.remove('d-none');
                }
            });
        }

        function loadDevices() {
            const loader = document.getElementById('deviceLoading');
            const empty = document.getElementById('deviceEmpty');
            const table = document.getElementById('deviceTableContainer');

            if (loader) loader.classList.remove('d-none');
            if (empty) empty.classList.add('d-none');
            if (table) table.classList.add('d-none');

            fetch('{{ route("security.devices.index") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (loader) loader.classList.add('d-none');
                if (!data.success) return;

                pinData = data.pin || { has_pin: false, is_enabled: false };
                updatePinStatusUI(pinData);

                const btnTrustCurrent = document.getElementById('btnTrustCurrentDevice');
                if (btnTrustCurrent) {
                    if (data.is_current_device_trusted) {
                        btnTrustCurrent.classList.add('d-none');
                    } else {
                        btnTrustCurrent.classList.remove('d-none');
                    }
                }

                const devices = data.devices || [];
                const countEl = document.getElementById('deviceCount');
                if (countEl) countEl.textContent = devices.length;

                if (devices.length === 0) {
                    if (empty) empty.classList.remove('d-none');
                    return;
                }

                if (table) table.classList.remove('d-none');
                const tbody = document.getElementById('deviceTableBody');
                if (!tbody) return;
                tbody.innerHTML = '';

                devices.forEach(dev => {
                    const tr = document.createElement('tr');
                    
                    let statusBadge = '';
                    if (dev.is_revoked) {
                        statusBadge = '<span class="badge bg-secondary">Revoked</span>';
                    } else if (dev.is_expired) {
                        statusBadge = '<span class="badge bg-warning text-dark">Expired</span>';
                    } else if (dev.is_current) {
                        statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle fw-bold"><i class="fas fa-check-circle me-1"></i>Current Browser</span>';
                    } else {
                        statusBadge = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle">Trusted</span>';
                    }

                    tr.innerHTML = `
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-${dev.browser.toLowerCase().includes('safari') ? 'apple' : 'desktop'} text-secondary"></i>
                                <span class="fw-semibold text-dark">${escapeHtml(dev.device_name)}</span>
                            </div>
                        </td>
                        <td>
                            <div class="text-dark">${escapeHtml(dev.browser)}</div>
                            <div class="text-muted small" style="font-size: 11px;">${escapeHtml(dev.operating_system)}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">${escapeHtml(dev.salon_name)}</span>
                        </td>
                        <td class="text-muted">${dev.first_registered}</td>
                        <td class="text-muted">${dev.last_active}</td>
                        <td>${statusBadge}</td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 btn-rename-device"
                                        data-id="${dev.id}" data-name="${escapeHtml(dev.device_name)}" title="Rename">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${!dev.is_revoked ? `
                                    <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 btn-revoke-device"
                                            data-id="${dev.id}" data-name="${escapeHtml(dev.device_name)}" title="Revoke Device">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(() => {
                if (loader) loader.classList.add('d-none');
            });
        }

        function updatePinStatusUI(pin) {
            const badge = document.getElementById('pinStatusBadge');
            const toggleBtn = document.getElementById('btnTogglePin');
            const toggleText = document.getElementById('btnTogglePinText');
            const textChange = document.getElementById('textChangePin');

            if (!pin.has_pin) {
                if (badge) {
                    badge.className = 'badge rounded-pill bg-warning text-dark px-2.5 py-1';
                    badge.textContent = 'Not Configured';
                }
                if (textChange) textChange.textContent = 'Setup PIN';
                if (toggleText) toggleText.textContent = 'Setup PIN';
                if (toggleBtn) {
                    toggleBtn.className = 'btn btn-outline-primary btn-sm px-3';
                }
            } else if (pin.is_enabled) {
                if (badge) {
                    badge.className = 'badge rounded-pill bg-success px-2.5 py-1';
                    badge.textContent = 'Active & Enabled';
                }
                if (textChange) textChange.textContent = 'Change PIN';
                if (toggleText) toggleText.textContent = 'Disable PIN';
                if (toggleBtn) {
                    toggleBtn.className = 'btn btn-outline-secondary btn-sm px-3';
                }
            } else {
                if (badge) {
                    badge.className = 'badge rounded-pill bg-secondary px-2.5 py-1';
                    badge.textContent = 'Disabled';
                }
                if (textChange) textChange.textContent = 'Change PIN';
                if (toggleText) toggleText.textContent = 'Enable PIN';
                if (toggleBtn) {
                    toggleBtn.className = 'btn btn-outline-success btn-sm px-3';
                }
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function initTrustedDevices() {
            // CRITICAL FIX: Teleport modals to document.body so they are top-level.
            // This prevents nested <form> dropping when included inside #settingsForm,
            // and eliminates CSS overflow/transform modal positioning bugs.
            ['modalChangePin', 'modalRenameDevice', 'modalTogglePinConfirm'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el && el.parentElement !== document.body) {
                    document.body.appendChild(el);
                }
            });

            // Initial data fetch
            loadDevices();

            // Refresh button
            const btnRefresh = document.getElementById('btnRefreshDevices');
            if (btnRefresh) {
                btnRefresh.addEventListener('click', loadDevices);
            }

            // Trust Current Browser button
            const btnTrustCurrent = document.getElementById('btnTrustCurrentDevice');
            if (btnTrustCurrent) {
                btnTrustCurrent.addEventListener('click', function () {
                    btnTrustCurrent.disabled = true;
                    fetch('{{ route("security.devices.trust-current") }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        btnTrustCurrent.disabled = false;
                        if (data.success) {
                            alert(data.message);
                            loadDevices();
                        } else {
                            alert(data.message || 'Failed to trust this browser.');
                        }
                    })
                    .catch(() => {
                        btnTrustCurrent.disabled = false;
                        alert('Network error while trusting browser.');
                    });
                });
            }

            // Open Change / Setup PIN
            const btnOpenChange = document.getElementById('btnOpenChangePinModal');
            if (btnOpenChange) {
                btnOpenChange.addEventListener('click', function (e) {
                    e.preventDefault();
                    openChangePinDialog(!pinData.has_pin);
                });
            }

            // Submit Change PIN button
            const btnSubmitPin = document.getElementById('btnSubmitChangePin');
            if (btnSubmitPin) {
                btnSubmitPin.addEventListener('click', submitChangePin);
            }

            // Enter key on inputs in PIN modal
            ['inputCurrentPassword', 'inputNewPin', 'inputConfirmPin'].forEach(id => {
                const inp = document.getElementById(id);
                if (inp) {
                    inp.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            submitChangePin();
                        }
                    });
                }
            });

            // Toggle PIN Button (Disable / Enable / Setup)
            const btnToggle = document.getElementById('btnTogglePin');
            if (btnToggle) {
                btnToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (!pinData.has_pin) {
                        openChangePinDialog(true);
                        return;
                    }

                    const errBox = document.getElementById('togglePinModalError');
                    if (errBox) errBox.classList.add('d-none');
                    const passInput = document.getElementById('inputTogglePinPassword');
                    if (passInput) passInput.value = '';

                    const iconEl = document.getElementById('iconTogglePinModal');
                    const titleEl = document.getElementById('titleTogglePinModal');
                    const descEl = document.getElementById('descTogglePinModal');
                    const submitBtn = document.getElementById('btnSubmitTogglePinAction');
                    const submitText = document.getElementById('btnSubmitTogglePinActionText');

                    if (pinData.is_enabled) {
                        // Disabling
                        if (iconEl) iconEl.className = 'fas fa-exclamation-triangle text-danger';
                        if (titleEl) {
                            titleEl.className = 'modal-title fw-bold text-danger d-flex align-items-center gap-2';
                            titleEl.textContent = 'Disable PIN Login';
                        }
                        if (descEl) descEl.textContent = 'Disabling PIN login means you will need to enter your full email address and password whenever you log into any trusted device.';
                        if (submitBtn) submitBtn.className = 'btn btn-danger btn-sm px-3';
                        if (submitText) submitText.textContent = 'Disable PIN Login';
                    } else {
                        // Enabling
                        if (iconEl) iconEl.className = 'fas fa-shield-alt text-success';
                        if (titleEl) {
                            titleEl.className = 'modal-title fw-bold text-success d-flex align-items-center gap-2';
                            titleEl.textContent = 'Enable PIN Login';
                        }
                        if (descEl) descEl.textContent = 'Enter your current account password to re-enable 6-digit PIN login on your trusted devices.';
                        if (submitBtn) submitBtn.className = 'btn btn-success btn-sm px-3';
                        if (submitText) submitText.textContent = 'Enable PIN Login';
                    }

                    showModal('modalTogglePinConfirm');
                });
            }

            // Submit Toggle (Disable/Enable) PIN Action
            const btnSubmitToggleAction = document.getElementById('btnSubmitTogglePinAction');
            if (btnSubmitToggleAction) {
                btnSubmitToggleAction.addEventListener('click', function () {
                    const errBox = document.getElementById('togglePinModalError');
                    if (errBox) errBox.classList.add('d-none');

                    const password = (document.getElementById('inputTogglePinPassword')?.value || '');
                    if (!password) {
                        if (errBox) {
                            errBox.textContent = 'Please enter your password.';
                            errBox.classList.remove('d-none');
                        }
                        return;
                    }

                    btnSubmitToggleAction.disabled = true;
                    const endpoint = pinData.is_enabled ? '{{ route("security.pin.disable") }}' : '{{ route("security.pin.enable") }}';

                    fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ current_password: password })
                    })
                    .then(res => res.json())
                    .then(data => {
                        btnSubmitToggleAction.disabled = false;
                        if (data.success) {
                            hideModal('modalTogglePinConfirm');
                            alert(data.message);
                            loadDevices();
                        } else {
                            if (errBox) {
                                errBox.textContent = data.message || 'Incorrect password.';
                                errBox.classList.remove('d-none');
                            }
                        }
                    })
                    .catch(() => {
                        btnSubmitToggleAction.disabled = false;
                        if (errBox) {
                            errBox.textContent = 'An unexpected error occurred.';
                            errBox.classList.remove('d-none');
                        }
                    });
                });
            }

            // Delegate Rename Device Click
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-rename-device');
                if (btn) {
                    const idInput = document.getElementById('renameDeviceId');
                    const nameInput = document.getElementById('inputRenameDeviceName');
                    const errBox = document.getElementById('renameDeviceError');

                    if (idInput) idInput.value = btn.dataset.id;
                    if (nameInput) nameInput.value = btn.dataset.name;
                    if (errBox) errBox.classList.add('d-none');

                    showModal('modalRenameDevice');
                }
            });

            // Handle Rename Submit Button
            const btnSubmitRename = document.getElementById('btnSubmitRename');
            if (btnSubmitRename) {
                btnSubmitRename.addEventListener('click', function () {
                    const id = document.getElementById('renameDeviceId')?.value;
                    const name = (document.getElementById('inputRenameDeviceName')?.value || '').trim();
                    const errBox = document.getElementById('renameDeviceError');
                    if (errBox) errBox.classList.add('d-none');

                    if (!name) {
                        if (errBox) {
                            errBox.textContent = 'Please enter a device name.';
                            errBox.classList.remove('d-none');
                        }
                        return;
                    }

                    btnSubmitRename.disabled = true;
                    fetch(`/security/devices/${id}/rename`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ device_name: name })
                    })
                    .then(res => res.json())
                    .then(data => {
                        btnSubmitRename.disabled = false;
                        if (data.success) {
                            hideModal('modalRenameDevice');
                            loadDevices();
                        } else {
                            if (errBox) {
                                errBox.textContent = data.message || 'Failed to rename device.';
                                errBox.classList.remove('d-none');
                            }
                        }
                    })
                    .catch(() => {
                        btnSubmitRename.disabled = false;
                        if (errBox) {
                            errBox.textContent = 'An unexpected error occurred.';
                            errBox.classList.remove('d-none');
                        }
                    });
                });
            }

            // Delegate Revoke Device Click
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-revoke-device');
                if (btn) {
                    const id = btn.dataset.id;
                    const name = btn.dataset.name;

                    if (confirm(`Are you sure you want to revoke trust for "${name}"? PIN login on that device will be disabled immediately.`)) {
                        fetch(`/security/devices/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                loadDevices();
                            } else {
                                alert(data.message || 'Failed to revoke device.');
                            }
                        })
                        .catch(() => alert('Network error while revoking device.'));
                    }
                }
            });

            // Revoke All Other Devices Click
            const btnRevokeAll = document.getElementById('btnRevokeAllDevices');
            if (btnRevokeAll) {
                btnRevokeAll.addEventListener('click', function () {
                    if (confirm('Revoke all other devices? All other browsers will require full email and password credentials to log in again.')) {
                        fetch('{{ route("security.devices.revoke-all") }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                loadDevices();
                            } else {
                                alert(data.message || 'Failed to revoke devices.');
                            }
                        })
                        .catch(() => alert('Network error while revoking devices.'));
                    }
                });
            }
        }

        // Initialize immediately if ready or on DOMContentLoaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTrustedDevices);
        } else {
            initTrustedDevices();
        }
    })();
</script>
