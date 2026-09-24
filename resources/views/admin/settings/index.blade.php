@extends('layouts.app')

@section('content')
@hasanyrole('super_admin|salon_admin')
@push('styles')
    @vite(['resources/css/settings.css'])
    <style>
        /* Compact UI Adjustments */
        .form-control, .form-select {
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
            min-height: 38px;
        }

        .form-label {
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .settings-section-card {
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        
        .settings-section-header {
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
        }

        /* Smaller Toggle Switch */
        .form-check.form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
            margin-top: 0.15em;
        }

        /* Dropdown Option Spacing */
        .select2-container--bootstrap-5 .select2-results__option {
            padding: 8px 12px;
            margin-bottom: 2px;
        }

        /* Clean Look */
        .nav-pills .nav-link {
            border-radius: 8px;
            margin-bottom: 4px;
            font-size: 0.95rem;
        }
        
        .nav-pills .nav-link.active {
            box-shadow: 0 4px 6px -1px rgba(13, 110, 253, 0.1), 0 2px 4px -1px rgba(13, 110, 253, 0.06);
        }

        /* Button Styling */
        .btn-clear-cache {
            background-color: #ffc107;
            color: #000;
            font-weight: 500;
            border: none;
        }
        .btn-clear-cache:hover {
            background-color: #ffca2c;
            color: #000;
        }
    </style>
@endpush

<div class="settings-container">
    <div class="row">
        <!-- Dashboard Header -->
        <div class="col-12 mb-4">
            <div class="glass-card p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">Salon Settings</h4>
                    <p class="text-muted mb-0">Manage your business configuration, preferences, and notifications.</p>
                </div>
                <div class="d-flex gap-2">
                    @if(auth()->user()->hasRole('salon_admin'))
                    <form action="{{ route('admin.saas.settings.clear-cache', ['salon_slug' => auth()->user()->salon->slug]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-clear-cache shadow-sm">
                            <i class="fas fa-sync me-2"></i> Clear Cache
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="row">
                <!-- Navigation Sidebar (Moved Outside Form) -->
                <div class="col-lg-3">
                    <div class="nav flex-column nav-pills settings-nav" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="gen-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab">
                            <i class="fas fa-store-alt"></i> General Profile
                        </button>
                        <button class="nav-link" id="app-tab" data-bs-toggle="pill" data-bs-target="#appointments" type="button" role="tab">
                            <i class="fas fa-calendar-check"></i> Bookings & Schedule
                        </button>
                        <button class="nav-link" id="not-tab" data-bs-toggle="pill" data-bs-target="#notifications" type="button" role="tab">
                            <i class="fas fa-bell"></i> Notifications & Alerts
                        </button>
                        <button class="nav-link" id="pay-tab" data-bs-toggle="pill" data-bs-target="#payments" type="button" role="tab">
                            <i class="fas fa-credit-card"></i> Payments & Finance
                        </button>
                        <button class="nav-link" id="loc-tab" data-bs-toggle="pill" data-bs-target="#localization" type="button" role="tab">
                            <i class="fas fa-globe-americas"></i> Localization
                        </button>
                        <button class="nav-link" id="pri-tab" data-bs-toggle="pill" data-bs-target="#privacy" type="button" role="tab">
                            <i class="fas fa-shield-alt"></i> Security & Privacy
                        </button>
                        @if(optional(auth()->user()->salon)->canUseFeature('AI Insights & Automation'))
                        <button class="nav-link" id="ai-tab" data-bs-toggle="pill" data-bs-target="#ai-settings" type="button" role="tab">
                            <i class="fas fa-brain text-warning"></i> AI & Automation
                        </button>
                        @endif
                        @if(auth()->user()->hasAnyRole(['salon_admin', 'manager']))
                        <button class="nav-link" id="mail-tab" data-bs-toggle="pill" data-bs-target="#mail" type="button" role="tab">
                            <i class="fas fa-envelope-open-text"></i> Email Integration
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Content Area -->
                <div class="col-lg-9">
                    <!-- Main Form Wrapping All Tabs -->
                    @role('salon_admin')
                        <form action="{{ route('admin.salon-settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
                    @else
                        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
                    @endrole
                        @csrf
                        @method('PUT')

                        <div class="tab-content" id="v-pills-tabContent">
                            <!-- General Tab -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
                                @include('admin.settings.partials.general', ['settings' => $settings])
                            </div>

                            <!-- Appointments Tab -->
                            <div class="tab-pane fade" id="appointments" role="tabpanel">
                                @include('admin.settings.partials.appointments', ['settings' => $settings])
                            </div>

                            <!-- Notifications Tab -->
                            <div class="tab-pane fade" id="notifications" role="tabpanel">
                                @include('admin.settings.partials.notifications', ['settings' => $settings])
                            </div>

                            <!-- Payments Tab -->
                            <div class="tab-pane fade" id="payments" role="tabpanel">
                                @include('admin.settings.partials.payments', ['settings' => $settings])
                            </div>

                            <!-- Localization Tab -->
                            <div class="tab-pane fade" id="localization" role="tabpanel">
                                @include('admin.settings.partials.localization', ['settings' => $settings])
                            </div>

                            <!-- Security Tab -->
                            <div class="tab-pane fade" id="privacy" role="tabpanel">
                                @include('admin.settings.partials.privacy', ['settings' => $settings])
                            </div>

                            <!-- AI & Automation Tab -->
                            @if(optional(auth()->user()->salon)->canUseFeature('AI Insights & Automation'))
                            <div class="tab-pane fade" id="ai-settings" role="tabpanel">
                                @include('admin.settings.partials.ai', ['settings' => $settings])
                            </div>
                            @endif

                            <!-- Mail Tab -->
                            @if(auth()->user()->hasAnyRole(['salon_admin', 'manager']))
                            <div class="tab-pane fade" id="mail" role="tabpanel">
                                @include('admin.settings.partials.mail', ['settings' => $settings])
                            </div>
                            @endif
                        </div>

                        <!-- Sticky Footer Save -->
                        <div class="sticky-action-bar">
                            <div class="glass-card">
                                <span class="text-muted small d-none d-md-inline">
                                    <i class="fas fa-info-circle me-1"></i> Ensure all changes are verified before saving.
                                </span>
                                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                                    <i class="fas fa-cloud-upload-alt me-2"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Access Denied</h4>
                    <p class="text-muted">You do not have permission to access this page.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endhasanyrole

@push('styles')
<style>
    .color-preview {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        display: inline-block;
    }
    .bg-purple {
        background-color: #6f42c1;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: 600;
    }
    
    /* Smooth transition for theme color changes */
    .sidebar {
        transition: background-color 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Color mapping for theme colors
        const colorMap = {
            'pink': '#e83e8c',
            'blue': '#0d6efd',
            'purple': '#6f42c1',
            'green': '#198754',
            'barber': '#B45309'
        };
        
        // Function to update sidebar theme color
        function updateSidebarTheme(color) {
            const sidebar = document.getElementById('sidebar');
            
            if (sidebar && colorMap[color]) {
                // Use setProperty with !important to override role-based gradients
                sidebar.style.setProperty('background', colorMap[color], 'important');
                sidebar.style.setProperty('background-color', colorMap[color], 'important');

            }
        }
        
        // Apply saved color from localStorage or database on page load
        const savedColor = localStorage.getItem('salon_theme_color') || '{{ app(\App\Services\SettingsService::class)->getThemeColor() }}';
        if (savedColor) {
            updateSidebarTheme(savedColor);
        }
        
        // Listen for color radio button changes
        const colorRadios = document.querySelectorAll('input[name="theme_color"]');
        colorRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const selectedColor = this.value;
                
                // Update sidebar color immediately
                updateSidebarTheme(selectedColor);
                
                // Save to localStorage for persistence across pages
                localStorage.setItem('salon_theme_color', selectedColor);
                
                // Show feedback to user
                const feedbackMsg = document.createElement('div');
                feedbackMsg.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                feedbackMsg.style.zIndex = '9999';
                feedbackMsg.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    Theme color changed to ${selectedColor}! Don't forget to save your settings.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(feedbackMsg);
                
                // Auto-dismiss after 3 seconds
                setTimeout(() => {
                    feedbackMsg.remove();
                }, 3000);
            });
        });

        // Activate tab from URL query parameter (e.g. ?tab=mail)
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            const triggerEl = document.querySelector(`#${tab}-tab`);
            if (triggerEl) {
                const tabInstance = new bootstrap.Tab(triggerEl);
                tabInstance.show();
            }
        }
    });
</script>
@endpush
@endsection