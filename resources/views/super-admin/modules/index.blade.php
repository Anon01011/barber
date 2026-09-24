@extends('layouts.super-admin')

@section('content')
    <div class="container-fluid ">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 fw-bold">System Capacities</h1>
            <div class="mt-3 mt-sm-0">
                <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-server me-1"></i> SaaS Infrastructure Control
                </span>
            </div>
        </div>

        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between border-bottom">
                <h6 class="m-0 font-weight-bold text-primary">Global Module Management</h6>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-success border-0 fw-bold" onclick="toggleAllModules(true)">
                        <i class="fas fa-check-double me-1"></i>Enable All
                    </button>
                    <span class="text-gray-300 mx-2">|</span>
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 fw-bold" onclick="toggleAllModules(false)">
                        <i class="fas fa-ban me-1"></i>Disable All
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-light border-start border-4 border-info shadow-sm mb-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-info fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="fw-bold mb-1">Administrative Action Required</h6>
                            <p class="mb-0 small text-muted">Disabling a module here instantly removes access for all salons across the entire platform. Data remains intact but hidden until re-enabled.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.modules.update') }}" method="POST" id="modulesForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        @foreach($availableModules as $key => $module)
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card h-100 module-card {{ isset($enabledModules[$key]) && $enabledModules[$key] ? 'border-primary-left' : 'border-gray-left' }} shadow-sm transition-all border-0">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="icon-circle shadow-sm {{ isset($enabledModules[$key]) && $enabledModules[$key] ? 'bg-primary text-white' : 'bg-light text-muted' }}">
                                                    <i class="fas {{ $module['icon'] }}"></i>
                                                </div>
                                            </div>
                                            <div class="col ps-0">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $module['name'] }}</h6>
                                                    <div class="form-check form-switch p-0">
                                                        <input class="form-check-input module-toggle ms-0" 
                                                               type="checkbox" 
                                                               role="switch"
                                                               id="module_{{ $key }}" 
                                                               name="enabled_modules[{{ $key }}]" 
                                                               value="1"
                                                               {{ (isset($enabledModules[$key]) && $enabledModules[$key]) ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                                <p class="text-muted small mb-3 mt-1 line-clamp">{{ $module['description'] }}</p>

                                                <div class="d-flex align-items-center justify-content-between bg-light rounded-pill px-3 py-1">
                                                    <label for="status_{{ $key }}" class="label-tiny text-uppercase mb-0">Status:</label>
                                                    <select class="form-select status-select-minimal border-0 bg-transparent py-0 h-auto" id="status_{{ $key }}" name="module_statuses[{{ $key }}]">
                                                        <option value="stable" {{ ($moduleStatuses[$key] ?? 'stable') == 'stable' ? 'selected' : '' }}>Stable</option>
                                                        <option value="beta" {{ ($moduleStatuses[$key] ?? 'stable') == 'beta' ? 'selected' : '' }}>Beta</option>
                                                        <option value="alpha" {{ ($moduleStatuses[$key] ?? 'stable') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                                        <option value="development" {{ ($moduleStatuses[$key] ?? 'stable') == 'development' ? 'selected' : '' }}>In-Dev</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white py-2 border-top-0 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="status-indicator-dot {{ isset($enabledModules[$key]) && $enabledModules[$key] ? 'bg-success' : 'bg-danger' }}"></span>
                                            <small class="module-status-label fw-bold {{ isset($enabledModules[$key]) && $enabledModules[$key] ? 'text-success' : 'text-danger' }} text-uppercase" style="font-size: 0.65rem;">
                                                {{ (isset($enabledModules[$key]) && $enabledModules[$key]) ? 'Online' : 'Offline' }}
                                            </small>
                                        </div>
                                        @php $stage = $moduleStatuses[$key] ?? 'stable'; @endphp
                                        <span class="badge {{ $stage == 'stable' ? 'bg-success-light text-success' : ($stage == 'beta' ? 'bg-warning-light text-warning' : 'bg-info-light text-info') }} border-0 px-2 py-1" style="font-size: 0.6rem;">
                                            {{ strtoupper($stage) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="sticky-save-bar card shadow-lg border-0 d-none" id="saveBar">
                        <div class="card-body py-3 d-flex align-items-center justify-content-between bg-white rounded">
                            <div class="d-flex align-items-center text-primary fw-bold">
                                <i class="fas fa-exclamation-triangle pulse me-3"></i>
                                Configuration Modified - Save Required
                            </div>
                            <div>
                                <button type="button" class="btn btn-light btn-sm me-2 rounded-pill px-4 fw-bold" onclick="location.reload()">Reset</button>
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-bold">Update Global Config</button>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow hover-scale fw-bold">
                            <i class="fas fa-save me-2"></i>Finalise Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-shield-alt me-2 text-primary"></i>Operational Insights</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 border-end">
                        <h6 class="fw-bold mb-2">Scope of Impact</h6>
                        <p class="small text-muted mb-0">Changes applied here affect all existing and future salons. Access to routes, APIs, and navigation menus are restricted in real-time.</p>
                    </div>
                    <div class="col-md-4 border-end">
                        <h6 class="fw-bold mb-2">Data Persistence</h6>
                        <p class="small text-muted mb-0">Feature suspension does not delete record data. All database entries are preserved and will reactivate upon module restoration.</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="fw-bold mb-2">Environment Stages</h6>
                        <p class="small text-muted mb-0">Use stages to communicate developmental readiness to salon owners and managers through the system dashboard.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Color Palette & Utilities */
    :root {
        --primary-main: #4e73df;
        --success-main: #1cc88a;
        --danger-main: #e74a3b;
        --bg-gray: #f8f9fc;
    }

    body {
        background-color: var(--bg-gray) !important;
    }

    .bg-success-light { background-color: rgba(28, 200, 138, 0.1); }
    .bg-warning-light { background-color: rgba(246, 194, 62, 0.1); }
    .bg-info-light { background-color: rgba(54, 185, 204, 0.1); }

    /* Card Enhancements */
    .module-card {
        transition: all 0.2s cubic-bezier(.25,.8,.25,1);
        position: relative;
        border: 1px solid rgba(0,0,0,.03) !important;
    }

    .module-card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
        transform: translateY(-2px);
    }

    .border-primary-left { border-left: 5px solid var(--primary-main) !important; }
    .border-gray-left { border-left: 5px solid #d1d3e2 !important; }

    /* Icon Styles */
    .icon-circle {
        height: 3.5rem;
        width: 3.5rem;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Switch customization */
    .form-check-input {
        width: 3em !important;
        height: 1.5em !important;
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: var(--success-main) !important;
        border-color: var(--success-main) !important;
    }

    /* Typography */
    .label-tiny {
        font-size: 0.6rem;
        font-weight: 800;
        color: #858796;
    }

    .status-select-minimal {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--primary-main);
        width: auto !important;
    }

    .status-select-minimal:focus {
        box-shadow: none;
    }

    .line-clamp {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.5rem;
    }

    /* Indicator Dot */
    .status-indicator-dot {
        height: 8px;
        width: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    /* Sticky Save Bar */
    .sticky-save-bar {
        position: fixed;
        bottom: 2rem;
        left: 20%;
        right: 5%;
        z-index: 1000;
        animation: slideUp 0.4s ease-out;
    }

    @keyframes slideUp {
        from { transform: translateY(100px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .pulse { animation: pulse 2s infinite; }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }

    .hover-scale { transition: transform 0.2s ease; }
    .hover-scale:hover { transform: scale(1.03); }

    @media (max-width: 768px) {
        .sticky-save-bar { left: 5%; bottom: 1rem; }
    }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkBoxes = document.querySelectorAll('.module-toggle');
            const selects = document.querySelectorAll('.status-select-minimal');
            const saveBar = document.getElementById('saveBar');

            function markDirty() {
                saveBar.classList.remove('d-none');
            }

            checkBoxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const card = this.closest('.module-card');
                    const dot = card.querySelector('.status-indicator-dot');
                    const label = card.querySelector('.module-status-label');
                    const iconCircle = card.querySelector('.icon-circle');

                    if (this.checked) {
                        card.classList.add('border-primary-left');
                        card.classList.remove('border-gray-left');
                        dot.classList.add('bg-success');
                        dot.classList.remove('bg-danger');
                        label.classList.add('text-success');
                        label.classList.remove('text-danger');
                        label.textContent = 'Online';
                        iconCircle.classList.add('bg-primary', 'text-white');
                        iconCircle.classList.remove('bg-light', 'text-muted');
                    } else {
                        card.classList.remove('border-primary-left');
                        card.classList.add('border-gray-left');
                        dot.classList.remove('bg-success');
                        dot.classList.add('bg-danger');
                        label.classList.remove('text-success');
                        label.classList.add('text-danger');
                        label.textContent = 'Offline';
                        iconCircle.classList.remove('bg-primary', 'text-white');
                        iconCircle.classList.add('bg-light', 'text-muted');
                    }
                    markDirty();
                });
            });

            selects.forEach(s => s.addEventListener('change', markDirty));
        });

        function toggleAllModules(enable) {
            document.querySelectorAll('.module-toggle').forEach(cb => {
                if (cb.checked !== enable) {
                    cb.checked = enable;
                    cb.dispatchEvent(new Event('change'));
                }
            });
        }
    </script>
@endpush
