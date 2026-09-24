@extends('layouts.super-admin')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Email Template</h1>
            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            {{ ucwords(str_replace('_', ' ', str_replace('subscription_', '', $template->type))) }} Template
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.email-templates.update', $template->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject"
                                    name="subject" value="{{ old('subject', $template->subject) }}" required>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <div class="alert alert-info">
                                    <small><i class="fas fa-info-circle"></i> This content supports Markdown and
                                        HTML.</small>
                                </div>
                                <textarea class="form-control @error('content') is-invalid @enderror" id="content"
                                    name="content" rows="15" required>{{ old('content', $template->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Available Variables</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">You can use these variables in the subject and content. Click to copy.
                        </p>
                        <div class="list-group">
                            @if($template->variables)
                                @foreach($template->variables as $variable)
                                    <button type="button"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                        data-clipboard-text="{{ '{' . '{ $' . $variable . ' }' . '}' }}"
                                        onclick="copyToClipboard(this.dataset.clipboardText)">
                                        <code>{{ '{' . '{ $' . $variable . ' }' . '}' }}</code>
                                        <i class="far fa-copy text-gray-400"></i>
                                    </button>
                                @endforeach
                            @else
                                <div class="alert alert-warning">No variables defined for this template.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function copyToClipboard(text) {
                function showSuccess() {

                }

                function showError() {
                    alert('Failed to copy text. Please copy manually.');
                }

                function fallbackCopy() {
                    try {
                        const textArea = document.createElement("textarea");
                        textArea.value = text;
                        document.body.appendChild(textArea);
                        textArea.select();
                        const successful = document.execCommand('copy');
                        document.body.removeChild(textArea);
                        if (successful) {
                            showSuccess();
                        } else {
                            showError();
                        }
                    } catch (err) {
                        showError();
                    }
                }

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(showSuccess).catch(fallbackCopy);
                } else {
                    fallbackCopy();
                }
            }
        </script>
    @endpush
@endsection