@extends('layouts.app')

@push('styles')
    <style>
        .template-editor-container {
            max-width: 100%;
            padding: 0 1rem;
        }

        .editor-wrapper {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: calc(100vh - 140px);
            /* Fit within viewport */
            overflow: hidden;
        }

        .editor-toolbar {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .editor-main {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .editor-content-area {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
        }

        .editor-sidebar {
            width: 300px;
            background: #f8fafc;
            border-left: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .variable-list {
            padding: 1rem;
        }

        .variable-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.85rem;
        }

        .variable-item:hover {
            border-color: #cbd5e1;
            background: #f1f5f9;
        }

        .variable-code {
            color: #6366f1;
            font-family: monospace;
            font-weight: 600;
            display: block;
            margin-bottom: 2px;
        }

        .form-control-compact {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }

        /* Code Editor Look for Textarea */
        textarea.code-editor {
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 14px;
            line-height: 1.5;
            color: #334155;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            resize: none;
            width: 100%;
            height: 100%;
        }

        .preview-modal-body {
            background: #f1f5f9;
            padding: 2rem;
            border-radius: 0 0 8px 8px;
        }

        .email-preview-frame {
            background: white;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .email-preview-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
        }

        .email-preview-content {
            padding: 2rem;
            line-height: 1.6;
            color: #334155;
        }
    </style>
@endpush

@section('content')
    <div class="template-editor-container py-3">
        <form action="{{ route('admin.saas.settings.email-templates.update', $template->type) }}" method="POST"
            id="templateForm">
            @csrf
            @method('PUT')

            <div class="editor-wrapper">
                <!-- Toolbar -->
                <div class="editor-toolbar">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.saas.settings.email-templates') }}"
                            class="btn btn-sm btn-outline-secondary me-3 border-0">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ ucwords(str_replace('_', ' ', $template->type)) }}</h6>
                            <span
                                class="badge {{ $template->is_active ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 {{ $template->is_active ? 'text-success' : 'text-secondary' }} rounded-pill"
                                style="font-size: 0.7rem;">
                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch me-2" title="Enable/Disable Template">
                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }}>
                            <label class="form-check-label small text-muted" for="isActive">Active</label>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="previewTemplate()">
                            <i class="fas fa-eye me-1"></i> Preview
                        </button>

                        <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">
                            <i class="fas fa-save me-1"></i> Save
                        </button>
                    </div>
                </div>

                <!-- Main Editor Area -->
                <div class="editor-main">
                    <!-- Left: Inputs -->
                    <div class="editor-content-area">
                        <div class="d-flex flex-column h-100">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-uppercase text-secondary mb-1">Subject</label>
                                <input type="text" name="subject" id="templateSubject"
                                    class="form-control form-control-compact fw-semibold"
                                    value="{{ old('subject', $template->subject) }}" placeholder="Email Subject Line"
                                    required>
                            </div>

                            <div class="flex-grow-1 d-flex flex-column">
                                <label class="form-label small fw-bold text-uppercase text-secondary mb-1">Content</label>
                                <textarea name="content" id="templateContent" class="code-editor p-3" spellcheck="false"
                                    required>{{ old('content', $template->content) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Variables Sidebar -->
                    <div class="editor-sidebar">
                        <div class="p-3 border-bottom bg-white">
                            <h6 class="fw-bold mb-0 small text-uppercase text-secondary"><i class="fas fa-code me-2"></i>
                                Variables</h6>
                        </div>
                        <div class="variable-list">
                            <p class="small text-muted mb-2">Click to copy variables.</p>
                            @if(!empty($template->variables))
                                @foreach($template->variables as $key => $value)
                                    @php
                                        $isIndexed = is_int($key);
                                        $variableName = $isIndexed ? $value : $key;
                                        $description = $isIndexed ? ucwords(str_replace('_', ' ', $value)) : $value;
                                    @endphp
                                    <div class="variable-item copy-variable" data-variable="{{ $variableName }}">
                                        <span class="variable-code">{{ $variableName }}</span>
                                        <span class="text-secondary small">{{ $description }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center p-3 text-muted small">
                                    No specific variables.
                                </div>
                            @endif
                        </div>

                        @if($template->salon_id)
                            <div class="mt-auto p-3 border-top bg-white">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100"
                                    onclick="if(confirm('Revert to system default?')) document.getElementById('deleteForm').submit();">
                                    <i class="fas fa-undo me-1"></i> Revert to Default
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        @if($template->salon_id)
            <form id="deleteForm" action="{{ route('admin.saas.settings.email-templates.delete', $template->type) }}"
                method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endif

        <!-- Preview Modal -->
        <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="height: 80vh;">
                    <div class="modal-header border-bottom py-2 bg-light">
                        <h5 class="modal-title fs-6 fw-bold"><i class="fas fa-eye me-2 text-primary"></i> Preview Template
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body preview-modal-body p-0 d-flex justify-content-center bg-gray-100">
                        <div class="w-100 h-100 overflow-auto p-4">
                            <div class="email-preview-frame">
                                <div class="email-preview-header">
                                    <div class="d-flex mb-2">
                                        <span class="text-muted small me-2" style="width: 60px;">Subject:</span>
                                        <span class="fw-bold text-dark" id="previewSubject"></span>
                                    </div>
                                    <div class="d-flex">
                                        <span class="text-muted small me-2" style="width: 60px;">To:</span>
                                        <span class="text-dark">customer@example.com</span>
                                    </div>
                                </div>
                                <div class="email-preview-content" id="previewContent">
                                    <!-- Content will be injected here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2 bg-light border-top">
                        <span class="small text-muted me-auto"><i class="fas fa-info-circle me-1"></i> Shown with sample
                            data.</span>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // Dummy Data for Preview
        const sampleData = {
            '@{{customer_name}}': 'Alice Johnson',
            '@{{salon_name}}': '{{ auth()->user()->salon->name ?? "Luxe Salon" }}',
            '@{{booking_date}}': 'October 25, 2026',
            '@{{booking_time}}': '2:00 PM',
            '@{{service_name}}': 'Hair Spa & Cut',
            '@{{staff_name}}': 'Sarah Stylist',
            '@{{amount}}': '$120.00',
            '@{{salon_address}}': '123 Fashion Ave, New York, NY',
            '@{{salon_phone}}': '(555) 123-4567',
            '@{{booking_status}}': 'Confirmed',
            '@{{old_date}}': 'October 24, 2026',
            '@{{old_time}}': '10:00 AM',
            '@{{invoice_number}}': 'INV-2026-001',
            '@{{date}}': 'October 25, 2026',
            '@{{total}}': '$120.00'
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Copy functionality
            document.querySelectorAll('.copy-variable').forEach(item => {
                item.addEventListener('click', function () {
                    const variable = this.getAttribute('data-variable');
                    const textarea = document.getElementById('templateContent');

                    // Wrap variable in braces
                    const textToInsert = '{' + '{' + variable + '}' + '}';

                    // Insert at cursor position
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const text = textarea.value;
                    textarea.value = text.substring(0, start) + textToInsert + text.substring(end);

                    // Move cursor
                    textarea.selectionStart = textarea.selectionEnd = start + textToInsert.length;
                    textarea.focus();

                    // Feedback
                    const originalBg = this.style.backgroundColor;
                    this.style.backgroundColor = '#dcfce7';
                    this.style.borderColor = '#86efac';
                    setTimeout(() => {
                        this.style.backgroundColor = '';
                        this.style.borderColor = '';
                    }, 300);
                });
            });
        });

        function previewTemplate() {
            let subject = document.getElementById('templateSubject').value;
            let content = document.getElementById('templateContent').value;

            // Replace placeholders
            Object.keys(sampleData).forEach(key => {
                const value = sampleData[key];
                // Regex to replace all occurrences, escaping the curly braces
                const regex = new RegExp(key.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'g');
                subject = subject.replace(regex, value);
                content = content.replace(regex, value);
            });

            // Fallback for any other variable placeholders
            content = content.replace(/\{\{[^}]+\}\}/g, '<span class="text-danger">[Unknown Variable]</span>');

            // Generate Text-Only content (for SMS/Whatsapp)
            let textContent = content
                .replace(/<br\s*\/?>/gi, '\n')
                .replace(/<\/p>/gi, '\n\n')
                .replace(/<[^>]+>/g, '');

            textContent = textContent.trim();

            // Render line breaks as <br> if plain text for HTML view
            // Simple HTML detection: if it doesn't have tags, auto-convert newlines
            if (!/<[a-z][\s\S]*>/i.test(content)) {
                content = content.replace(/\n/g, '<br>');
            }

            document.getElementById('previewSubject').innerText = subject;

            // Update modal body to have tabs or split view
            const modalBody = document.getElementById('previewContent');
            modalBody.innerHTML = `
                <ul class="nav nav-tabs mb-3" id="previewTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="email-tab" data-bs-toggle="tab" data-bs-target="#email-preview" type="button" role="tab" aria-selected="true"><i class="fas fa-envelope me-1"></i> Email (HTML)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sms-tab" data-bs-toggle="tab" data-bs-target="#sms-preview" type="button" role="tab" aria-selected="false"><i class="fab fa-whatsapp me-1"></i> SMS / WhatsApp</button>
                    </li>
                </ul>
                <div class="tab-content" id="previewTabsContent">
                    <div class="tab-pane fade show active border rounded p-3 bg-white" id="email-preview" role="tabpanel">
                        ${content}
                    </div>
                    <div class="tab-pane fade border rounded p-3 bg-white" id="sms-preview" role="tabpanel">
                        <div class="alert alert-info py-2 small mb-2">
                            <i class="fas fa-info-circle me-1"></i> HTML tags are stripped for SMS/WhatsApp.
                        </div>
                        <pre style="white-space: pre-wrap; font-family: inherit; margin: 0;">${textContent}</pre>
                    </div>
                </div>
            `;

            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        }
    </script>
@endpush