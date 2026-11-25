<!-- Import Product Modal -->
<div class="modal fade" id="importProductModal" tabindex="-1" aria-labelledby="importProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header py-3 px-4 text-white" style="background: linear-gradient(135deg, #0b0f17 0%, #1a2232 100%); border-bottom: 1px solid rgba(197, 168, 128, 0.25);">
                <div class="d-flex align-items-center">
                    <div class="p-2 rounded-2 me-3 d-flex align-items-center justify-content-center" style="background: rgba(197, 168, 128, 0.15); width: 34px; height: 34px;">
                        <i class="fas fa-file-import" style="color: #c5a880; font-size: 15px;"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="importProductModalLabel" style="font-size: 15px;">
                            Import Products & Consumables via CSV
                        </h6>
                        <small style="color: #94a3b8; font-size: 11px;">Upload your product catalog or backbar consumables</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" id="importProductForm">
                @csrf
                <div class="modal-body px-4 py-4">
                    <p class="text-muted mb-3 small">
                        Upload a structured CSV to bulk import products into your salon & barber catalog. Backbar items can have cost prices and 0 selling prices.
                    </p>

                    <!-- Alert Box -->
                    <div id="importAlertBox" class="alert d-none py-2 px-3 mb-3 small rounded-3" role="alert">
                        <div class="d-flex align-items-start">
                            <i id="importAlertIcon" class="fas fa-info-circle me-2 mt-1"></i>
                            <div id="importAlertMessage"></div>
                        </div>
                    </div>

                    <!-- Drop Zone -->
                    <div class="upload-drop-zone border-2 border-dashed rounded-3 p-4 text-center mb-3"
                         id="csvDropZone" 
                         style="border-color: #cbd5e1; background-color: #f8fafc; cursor: pointer; transition: all 0.2s ease;">
                        <div id="dropZoneDefaultState">
                            <div class="upload-icon mb-2">
                                <i class="fas fa-cloud-upload-alt fa-3x text-primary opacity-75"></i>
                            </div>
                            <h6 class="fw-semibold mb-1 text-dark">Drag & drop your CSV file here</h6>
                            <p class="text-muted small mb-3">Accepts standard .csv files up to 4MB</p>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm" id="csvBrowseBtn">
                                <i class="fas fa-folder-open me-1"></i> Browse File
                            </button>
                        </div>
                        <div id="dropZoneSelectedState" class="d-none py-2">
                            <i class="fas fa-file-csv fa-3x text-success mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1" id="selectedFileName">filename.csv</h6>
                            <p class="text-muted small mb-2" id="selectedFileSize">0 KB</p>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" id="csvRemoveBtn">
                                <i class="fas fa-times me-1"></i> Remove File
                            </button>
                        </div>
                    </div>
                    <input type="file" class="d-none" id="csvFileInput" name="file" accept=".csv,text/csv">

                    <!-- Template Info -->
                    <div class="bg-light p-3 rounded-3 border">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
                            <div>
                                <span class="fw-semibold text-dark small d-block">
                                    <i class="fas fa-file-excel text-success me-1"></i> Need the sample CSV template?
                                </span>
                                <small class="text-muted">Includes pre-filled examples for Retail, Backbar, and Dual-use items</small>
                            </div>
                            <a href="{{ route('admin.products.template') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 text-nowrap">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="csvSubmitBtn" disabled>
                        <span class="spinner-border spinner-border-sm d-none me-1" id="csvSubmitSpinner"></span>
                        <i class="fas fa-upload me-1" id="csvSubmitIcon"></i> Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    function initImportModal() {
        const dropZone = document.getElementById('csvDropZone');
        const fileInput = document.getElementById('csvFileInput');
        const browseBtn = document.getElementById('csvBrowseBtn');
        const removeBtn = document.getElementById('csvRemoveBtn');
        const defaultState = document.getElementById('dropZoneDefaultState');
        const selectedState = document.getElementById('dropZoneSelectedState');
        const fileNameEl = document.getElementById('selectedFileName');
        const fileSizeEl = document.getElementById('selectedFileSize');
        const submitBtn = document.getElementById('csvSubmitBtn');
        const submitIcon = document.getElementById('csvSubmitIcon');
        const submitSpinner = document.getElementById('csvSubmitSpinner');
        const alertBox = document.getElementById('importAlertBox');
        const alertMsg = document.getElementById('importAlertMessage');
        const alertIcon = document.getElementById('importAlertIcon');
        const form = document.getElementById('importProductForm');

        if (!dropZone || !fileInput) return;

        function escapeHtml(text) {
            if (!text) return '';
            return String(text).replace(/[&<>"']/g, function(m) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
            });
        }

        function showAlert(msg, type) {
            if (!alertBox) return;
            alertBox.className = 'alert py-2 px-3 mb-3 small rounded-3 alert-' + (type === 'error' ? 'danger' : 'success');
            alertIcon.className = type === 'error' ? 'fas fa-exclamation-circle me-2 mt-1' : 'fas fa-check-circle me-2 mt-1';
            alertMsg.innerHTML = msg;
            alertBox.classList.remove('d-none');
        }

        function hideAlert() {
            if (alertBox) alertBox.classList.add('d-none');
        }

        function handleFile(file) {
            hideAlert();
            if (!file) return;

            if (!file.name.toLowerCase().endsWith('.csv')) {
                showAlert('Please select a valid CSV file (ending with .csv).', 'error');
                resetFile();
                return;
            }

            if (file.size > 4 * 1024 * 1024) {
                showAlert('File size exceeds the 4MB limit.', 'error');
                resetFile();
                return;
            }

            // Sync with file input via DataTransfer
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;

            fileNameEl.textContent = file.name;
            const sizeInKb = (file.size / 1024).toFixed(1);
            fileSizeEl.textContent = sizeInKb + ' KB';

            defaultState.classList.add('d-none');
            selectedState.classList.remove('d-none');
            submitBtn.disabled = false;
        }

        function resetFile() {
            fileInput.value = '';
            defaultState.classList.remove('d-none');
            selectedState.classList.add('d-none');
            submitBtn.disabled = true;
        }

        // Click on dropzone or browse button
        dropZone.addEventListener('click', function(e) {
            if (e.target.closest('#csvRemoveBtn')) {
                e.stopPropagation();
                resetFile();
                return;
            }
            fileInput.click();
        });

        if (browseBtn) {
            browseBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.click();
            });
        }

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                handleFile(this.files[0]);
            }
        });

        // Drag & Drop
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.style.borderColor = '#3b82f6';
                dropZone.style.backgroundColor = '#eff6ff';
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.style.borderColor = '#cbd5e1';
                dropZone.style.backgroundColor = '#f8fafc';
            }, false);
        });

        dropZone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                handleFile(dt.files[0]);
            }
        }, false);

        // AJAX Form Submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!fileInput.files || fileInput.files.length === 0) {
                showAlert('Please select a CSV file first.', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitIcon.classList.add('d-none');
            submitSpinner.classList.remove('d-none');
            hideAlert();

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    submitIcon.classList.remove('d-none');
                    submitSpinner.classList.add('d-none');
                    submitBtn.disabled = true; // Keep disabled while reloading
                    showAlert(data.message || 'Products imported successfully! Reloading...', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    submitBtn.disabled = false;
                    submitIcon.classList.remove('d-none');
                    submitSpinner.classList.add('d-none');
                    let errMsg = escapeHtml(data.message || 'Import failed.');
                    if (data.results && data.results.errors && data.results.errors.length > 0) {
                        errMsg += '<br><ul class="mb-0 mt-1 ps-3">';
                        data.results.errors.slice(0, 5).forEach(err => {
                            errMsg += `<li>Row ${escapeHtml(err.row)}: ${escapeHtml(err.message)}</li>`;
                        });
                        if (data.results.errors.length > 5) {
                            errMsg += `<li>...and ${data.results.errors.length - 5} more errors.</li>`;
                        }
                        errMsg += '</ul>';
                    }
                    showAlert(errMsg, 'error');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitIcon.classList.remove('d-none');
                submitSpinner.classList.add('d-none');
                showAlert('Network error or server exception during import: ' + err.message, 'error');
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initImportModal);
    } else {
        initImportModal();
    }
})();
</script>
