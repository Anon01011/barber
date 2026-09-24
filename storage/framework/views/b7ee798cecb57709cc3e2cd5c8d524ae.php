<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <form action="<?php echo e(route('admin.notifications.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <!-- Header & Actions -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                         <div class="d-flex align-items-center gap-2 mb-1">
                            <a href="<?php echo e(route('admin.notifications.index')); ?>" class="text-muted text-decoration-none">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <h1 class="h3 mb-0 text-gray-900 fw-bold">New Announcement</h1>
                        </div>
                        <p class="text-muted mb-0 ms-4">Draft a system-wide broadcast or targeted message.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('admin.notifications.index')); ?>" class="btn btn-white border shadow-sm fw-medium text-muted">
                            Discard
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm fw-bold px-4">
                            <i class="fas fa-paper-plane me-2"></i> Publish Now
                        </button>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Left Column: Content Editor -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="m-0 fw-bold text-gray-800"><i class="fas fa-pen-nib me-2 text-primary"></i>Message Content</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <label for="title" class="form-label fw-bold text-dark">Subject Line</label>
                                    <input type="text" class="form-control form-control-lg bg-light border-0" id="title" name="title"
                                        value="<?php echo e(old('title')); ?>" placeholder="Enter a catchy headline..." required onkeyup="updatePreview()">
                                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="mb-0">
                                    <label for="message" class="form-label fw-bold text-dark">Body</label>
                                    <textarea class="form-control" id="message" name="message" rows="12"><?php echo e(old('message')); ?></textarea>
                                    <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Settings & Preview -->
                    <div class="col-lg-4">
                        <!-- Live Preview -->
                        <div class="card border-0 shadow-sm mb-4 bg-light">
                            <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0">
                                 <div class="text-uppercase text-muted fw-bold small">Preview</div>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info border-0 shadow-sm mb-0" id="preview-box">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fas fa-info-circle fa-2x" id="preview-icon"></i>
                                        </div>
                                        <div class="w-100">
                                            <h5 class="alert-heading fw-bold mb-1" id="preview-title">Headline</h5>
                                            <p class="mb-0 small opacity-75" id="preview-text">Your message content will appear here...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="m-0 fw-bold text-gray-800"><i class="fas fa-sliders-h me-2 text-muted"></i>Settings</h6>
                            </div>
                            <div class="card-body p-4">
                                <!-- Type Selection -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Severity Level</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="type" id="t_info" value="info" checked onchange="updatePreviewType('info')">
                                        <label class="btn btn-outline-light text-dark border hover-bg-light" for="t_info"><i class="fas fa-info-circle text-info"></i></label>

                                        <input type="radio" class="btn-check" name="type" id="t_success" value="success" onchange="updatePreviewType('success')">
                                        <label class="btn btn-outline-light text-dark border hover-bg-light" for="t_success"><i class="fas fa-check-circle text-success"></i></label>

                                        <input type="radio" class="btn-check" name="type" id="t_warning" value="warning" onchange="updatePreviewType('warning')">
                                        <label class="btn btn-outline-light text-dark border hover-bg-light" for="t_warning"><i class="fas fa-exclamation-triangle text-warning"></i></label>

                                        <input type="radio" class="btn-check" name="type" id="t_danger" value="danger" onchange="updatePreviewType('danger')">
                                        <label class="btn btn-outline-light text-dark border hover-bg-light" for="t_danger"><i class="fas fa-fire text-danger"></i></label>
                                    </div>
                                    <div class="form-text mt-2" id="type-desc">Standard informational message.</div>
                                </div>

                                <!-- Audience -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Target Audience</label>
                                    <select class="form-select" id="target_salon_id" name="target_salon_id">
                                        <option value="">🚀 Global (All Salons)</option>
                                        <optgroup label="Single Workspace">
                                            <?php $__currentLoopData = $salons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($salon->id); ?>"><?php echo e($salon->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </optgroup>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summernote -->
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

        <?php $__env->startPush('styles'); ?>
            <style>
                /* Custom Radio Button Group Styling */
                .btn-check:checked + .btn { background-color: #f8f9fa; border-color: #dee2e6; box-shadow: inset 0 3px 5px rgba(0,0,0,0.125); }
                .hover-bg-light:hover { background-color: #f8f9fa; }
            </style>
        <?php $__env->stopPush(); ?>

        <?php $__env->startPush('scripts'); ?>
            <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
            <script>
                $(document).ready(function () {
                    $('#message').summernote({
                        placeholder: 'Write your notification content...',
                        tabsize: 2,
                        height: 300,
                        toolbar: [
                            ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['insert', ['link']],
                            ['view', ['codeview']]
                        ],
                        callbacks: {
                            onKeyup: function(e) {
                                // Simple text preview (HTML stripped for safety in quick preview)
                                var content = $($('#message').summernote('code')).text();
                                if(content.length > 100) content = content.substring(0, 100) + '...';
                                if(content.length === 0) content = 'Your message content will appear here...';
                                $('#preview-text').text(content);
                            }
                        }
                    });
                });

                function updatePreview() {
                    const title = document.getElementById('title').value;
                    document.getElementById('preview-title').innerText = title || 'Headline';
                }

                function updatePreviewType(type) {
                    const box = document.getElementById('preview-box');
                    const icon = document.getElementById('preview-icon');
                    const desc = document.getElementById('type-desc');

                    // Reset classes
                    box.className = 'alert border-0 shadow-sm mb-0';
                    icon.className = 'fas fa-2x';

                    if (type === 'info') {
                        box.classList.add('alert-info');
                        icon.classList.add('fa-info-circle');
                        desc.innerText = "Info: General updates and news.";
                    } else if (type === 'success') {
                        box.classList.add('alert-success');
                        icon.classList.add('fa-check-circle');
                        desc.innerText = "Success: Postive confirmations or feature releases.";
                    } else if (type === 'warning') {
                        box.classList.add('alert-warning');
                        icon.classList.add('fa-exclamation-triangle');
                        desc.innerText = "Warning: Alerts, maintenance, or cautions.";
                    } else if (type === 'danger') {
                        box.classList.add('alert-danger');
                        icon.classList.add('fa-exclamation-circle');
                        desc.innerText = "Critical: Outages, urgent issues, or security alerts.";
                    }
                }
            </script>
        <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\super-admin\notifications\create.blade.php ENDPATH**/ ?>