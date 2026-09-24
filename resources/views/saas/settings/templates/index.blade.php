@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Email Templates</h4>
                <p class="text-muted mb-0">Manage and customize your automated email notifications.</p>
            </div>
            <a href="{{ route('admin.salon-settings.index', ['tab' => 'mail']) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Settings
            </a>
        </div>

        <div class="row g-4">
            @foreach($templateTypes as $type)
                @php
                    $template = $templates->get($type)?->first();
                    $isActive = $template ? $template->is_active : false;
                    $isCustomized = $template && $template->salon_id == $salon->id;
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 me-3">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title fw-bold mb-0">{{ ucwords(str_replace('_', ' ', $type)) }}</h6>
                                        <span
                                            class="badge {{ $isCustomized ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }} rounded-pill"
                                            style="font-size: 0.7rem;">
                                            {{ $isCustomized ? 'Customized' : 'System Default' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <p class="card-text text-muted small mb-4">
                                Customize the content and subject line for the {{ str_replace('_', ' ', $type) }} email
                                notification.
                            </p>

                            <div class="d-grid">
                                <a href="{{ route('admin.saas.settings.email-templates.edit', ['type' => $type]) }}"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit me-2"></i> Edit Template
                                </a>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 pt-0 pb-3">
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="text-muted">Status:</span>
                                <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $isActive ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .transition-all {
            transition: all .3s ease-in-out;
        }
    </style>
@endpush