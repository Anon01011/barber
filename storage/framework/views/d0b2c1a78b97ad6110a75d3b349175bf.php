<!-- Notification Container -->
<div id="notificationContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>

<?php $__env->startPush('styles'); ?>
    <style>
        .notification {
            min-width: 300px;
            background-color: white;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-left: 4px solid;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            opacity: 0;
            transform: translateX(20px);
            transition: all 0.3s ease-out;
        }

        .notification.show {
            opacity: 1;
            transform: translateX(0);
        }

        .notification.success {
            border-left-color: #198754;
        }

        .notification.error {
            border-left-color: #dc3545;
        }

        .notification.warning {
            border-left-color: #ffc107;
        }

        .notification.info {
            border-left-color: #0dcaf0;
        }

        .notification-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .notification-icon {
            font-size: 1.25rem;
        }

        .notification.success .notification-icon {
            color: #198754;
        }

        .notification.error .notification-icon {
            color: #dc3545;
        }

        .notification.warning .notification-icon {
            color: #ffc107;
        }

        .notification.info .notification-icon {
            color: #0dcaf0;
        }

        .notification-message {
            flex-grow: 1;
            color: #333;
        }

        .notification-close {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 0.25rem;
            opacity: 0.5;
            transition: opacity 0.2s;
        }

        .notification-close:hover {
            opacity: 0.75;
        }

        .notification-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            background: rgba(0, 0, 0, 0.1);
        }

        .notification-progress-bar {
            height: 100%;
            width: 100%;
            transition: width 3s linear;
        }

        .notification.success .notification-progress-bar {
            background-color: #198754;
        }

        .notification.error .notification-progress-bar {
            background-color: #dc3545;
        }

        .notification.warning .notification-progress-bar {
            background-color: #ffc107;
        }

        .notification.info .notification-progress-bar {
            background-color: #0dcaf0;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        class NotificationSystem {
            constructor() {
                this.container = document.getElementById('notificationContainer');
                if (!this.container) {

                    return;
                }
            }

            show(message, type = 'info', duration = 3000) {
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;

                const icon = this.getIcon(type);

                notification.innerHTML = `
                                    <div class="notification-content">
                                        <div class="notification-icon">
                                            <i class="${icon}"></i>
                                        </div>
                                        <div class="notification-message">${message}</div>
                                        <button class="notification-close">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="notification-progress">
                                        <div class="notification-progress-bar"></div>
                                    </div>
                                `;

                this.container.appendChild(notification);

                // Trigger animation
                requestAnimationFrame(() => {
                    notification.classList.add('show');
                });

                // Progress bar animation
                const progressBar = notification.querySelector('.notification-progress-bar');
                progressBar.style.width = '100%';
                progressBar.style.transition = `width ${duration}ms linear`;

                // Start progress bar
                requestAnimationFrame(() => {
                    progressBar.style.width = '0%';
                });

                // Close button
                const closeBtn = notification.querySelector('.notification-close');
                closeBtn.addEventListener('click', () => {
                    this.close(notification);
                });

                // Auto close
                if (duration > 0) {
                    setTimeout(() => {
                        this.close(notification);
                    }, duration);
                }

                return notification;
            }

            close(notification) {
                notification.classList.remove('show');
                notification.addEventListener('transitionend', () => {
                    notification.remove();
                });
            }

            getIcon(type) {
                const icons = {
                    success: 'fas fa-check-circle',
                    error: 'fas fa-times-circle',
                    warning: 'fas fa-exclamation-circle',
                    info: 'fas fa-info-circle'
                };
                return icons[type] || icons.info;
            }
        }

        // Initialize notification system
        document.addEventListener('DOMContentLoaded', function () {
            window.notifications = new NotificationSystem();

            // Show any session notifications after initialization
            <?php if(session('success')): ?>
                window.notifications.show(<?php echo json_encode(session('success'), 15, 512) ?>, 'success');
            <?php endif; ?>

            <?php if(session('error')): ?>
                window.notifications.show(<?php echo json_encode(session('error'), 15, 512) ?>, 'error');
            <?php endif; ?>

            <?php if(session('warning')): ?>
                window.notifications.show(<?php echo json_encode(session('warning'), 15, 512) ?>, 'warning');
            <?php endif; ?>

            <?php if(session('info')): ?>
                window.notifications.show(<?php echo json_encode(session('info'), 15, 512) ?>, 'info');
            <?php endif; ?>
                        });
    </script>
<?php $__env->stopPush(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views/components/notifications.blade.php ENDPATH**/ ?>