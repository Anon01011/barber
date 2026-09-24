<?php $__env->startSection('title', 'Guest Booking URL'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4 py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1" style="color: #ec4899;">Guest Booking Management</h2>
                <p class="text-muted mb-0"><?php echo e($salon->name); ?> - Share booking link with customers</p>
            </div>
            <div>
                <?php if($guestBookingEnabled): ?>
                    <span class="badge bg-success px-3 py-2">
                        <i class="fas fa-check-circle me-1"></i>Guest Booking Enabled
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger px-3 py-2">
                        <i class="fas fa-times-circle me-1"></i>Guest Booking Disabled
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - URL and Info -->
            <div class="col-lg-8">
                <!-- Guest Booking URL Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0" style="color: #ec4899;">
                            <i class="fas fa-link me-2"></i>Shareable Booking Link
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted mb-2">Guest Booking URL</label>
                                <div class="input-group input-group-lg">
                                    <input type="text" class="form-control" id="guestBookingUrl"
                                        value="<?php echo e($guestBookingUrl); ?>" readonly
                                        style="font-family: monospace; background-color: #f8f9fa;">
                                    <button class="btn btn-primary" type="button" onclick="copyToClipboard()"
                                        style="background-color: #ec4899; border-color: #ec4899;">
                                        <i class="fas fa-copy me-2"></i>Copy URL
                                    </button>
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Share this link with your customers via email, SMS, or social media
                                </small>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted mb-3">Quick Actions</h6>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="<?php echo e($guestBookingUrl); ?>" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-2"></i>Preview Booking Page
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary" onclick="shareViaEmail()">
                                        <i class="fas fa-envelope me-2"></i>Share via Email
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="alert alert-info border-0 mb-0"
                            style="background-color: rgba(236, 72, 153, 0.1); border-left: 4px solid #ec4899 !important;">
                            <h6 class="alert-heading fw-bold" style="color: #ec4899;">
                                <i class="fas fa-lightbulb me-2"></i>How to Use
                            </h6>
                            <ul class="mb-0 ps-3">
                                <li>Copy the URL above using the "Copy URL" button</li>
                                <li>Share it with customers through your preferred communication channel</li>
                                <li>Customers can book appointments without creating an account</li>
                                <li>All guest bookings will appear in your bookings dashboard</li>
                                <li>Guest bookings are linked to <strong><?php echo e($salon->name); ?></strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Salon Info and Services -->
            <div class="col-lg-4">
                <!-- Salon Info Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0" style="color: #ec4899;">
                            <i class="fas fa-store me-2"></i>Salon Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="text-muted small fw-bold mb-1">Salon Name</label>
                            <p class="mb-0 fw-semibold"><?php echo e($salon->name); ?></p>
                        </div>
                        <?php if($salon->address): ?>
                            <div class="mb-3">
                                <label class="text-muted small fw-bold mb-1">Address</label>
                                <p class="mb-0 small"><?php echo e($salon->address); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if($salon->phone): ?>
                            <div class="mb-3">
                                <label class="text-muted small fw-bold mb-1">Phone</label>
                                <p class="mb-0 small"><?php echo e($salon->phone); ?></p>
                            </div>
                        <?php endif; ?>
                        <div>
                            <label class="text-muted small fw-bold mb-1">Guest Booking Status</label>
                            <p class="mb-0">
                                <?php if($guestBookingEnabled): ?>
                                    <span class="badge bg-success">Enabled</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Disabled</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recent Guest Bookings Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0" style="color: #ec4899;">
                            <i class="fas fa-users me-2"></i>Recent Guest Bookings
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if($recentGuestBookings->count() > 0): ?>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-history me-1"></i>
                                Latest 5 guest bookings
                            </p>

                            <ul class="list-group list-group-flush">
                                <?php $__currentLoopData = $recentGuestBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="list-group-item px-0 py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user-circle fa-lg text-secondary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 fw-bold text-dark"><?php echo e($booking->customer->name ?? 'Guest User'); ?></h6>
                                                <small class="text-muted d-block">
                                                    <?php echo e($booking->service->name ?? 'Unknown Service'); ?> 
                                                    <span class="mx-1">•</span> 
                                                    <?php echo e($booking->created_at->diffForHumans()); ?>

                                                </small>
                                            </div>
                                            <div class="text-end">
                                                 <span class="badge bg-<?php echo e($booking->status == 'confirmed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'secondary')); ?> bg-opacity-10 text-<?php echo e($booking->status == 'confirmed' ? 'success' : ($booking->status == 'pending' ? 'dark' : 'secondary')); ?> rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                                    <?php echo e(ucfirst($booking->status)); ?>

                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3 opacity-50"></i>
                                <p class="text-muted mb-0">No guest bookings found yet</p>
                                <small class="text-muted">Share the link to get bookings!</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $__env->startPush('scripts'); ?>
        <script>
            function copyToClipboard() {
                const urlInput = document.getElementById('guestBookingUrl');
                const textToCopy = urlInput.value;
                const btn = event.currentTarget || event.target.closest('button');
                const originalHTML = btn.innerHTML;

                function showSuccess() {
                    btn.innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-success');

                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-primary');
                    }, 2000);
                }

                function showError() {
                    alert('Failed to copy URL. Please copy manually.');
                }

                function fallbackCopy() {
                    try {
                        urlInput.select();
                        urlInput.setSelectionRange(0, 99999);
                        const successful = document.execCommand('copy');
                        if (successful) {
                            showSuccess();
                        } else {
                            showError();
                        }
                        window.getSelection().removeAllRanges();
                    } catch (err) {
                        showError();
                    }
                }

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(textToCopy).then(showSuccess).catch(fallbackCopy);
                } else {
                    fallbackCopy();
                }
            }

            function shareViaEmail() {
                const url = document.getElementById('guestBookingUrl').value;
                const subject = encodeURIComponent('Book Your Appointment');
                const body = encodeURIComponent(`Hello,\n\nYou can book your appointment using the following
                                link:\n\n${url}\n\nThank you!`);
                window.location.href = `mailto:?subject=${subject}&body=${body}`;
            }
        </script>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('styles'); ?>
        <style>
            .btn-primary:hover {
                background-color: #db2777 !important;
                border-color: #db2777 !important;
            }
        </style>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\guest-booking\index.blade.php ENDPATH**/ ?>