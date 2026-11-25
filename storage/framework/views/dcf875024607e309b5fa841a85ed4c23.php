<!-- Notification Container -->
<div id="notificationContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 99999;"></div>

<?php $__env->startPush('styles'); ?>
    <style>
        .notification {
            min-width: 320px;
            max-width: 440px;
            background-color: #ffffff;
            border: 1px solid #e8dccb;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(11, 15, 23, 0.12), 0 8px 10px -6px rgba(11, 15, 23, 0.08);
            border-left: 5px solid;
            padding: 0.85rem 1rem;
            margin-bottom: 0.75rem;
            opacity: 0;
            transform: translateX(30px);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .notification.show {
            opacity: 1;
            transform: translateX(0);
        }

        .notification.success {
            border-left-color: #10b981;
        }

        .notification.error {
            border-left-color: #ef4444;
        }

        .notification.warning {
            border-left-color: #f59e0b;
        }

        .notification.info {
            border-left-color: #c5a880;
        }

        .notification-content {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .notification-icon {
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .notification.success .notification-icon {
            color: #10b981;
        }

        .notification.error .notification-icon {
            color: #ef4444;
        }

        .notification.warning .notification-icon {
            color: #f59e0b;
        }

        .notification.info .notification-icon {
            color: #c5a880;
        }

        .notification-message {
            flex-grow: 1;
            color: #0f172a;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.45;
        }

        .notification-close {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.25rem;
            opacity: 0.6;
            transition: opacity 0.2s, color 0.2s;
            flex-shrink: 0;
        }

        .notification-close:hover {
            opacity: 1;
            color: #0b0f17;
        }

        .notification-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            background: rgba(11, 15, 23, 0.05);
        }

        .notification-progress-bar {
            height: 100%;
            width: 100%;
            transition: width 3s linear;
        }

        .notification.success .notification-progress-bar {
            background: #10b981;
        }

        .notification.error .notification-progress-bar {
            background: #ef4444;
        }

        .notification.warning .notification-progress-bar {
            background: #f59e0b;
        }

        .notification.info .notification-progress-bar {
            background: linear-gradient(90deg, #c5a880, #b38e5d);
        }

        /* Custom SweetAlert2 Luxury Obsidian/Gold Styling */
        div.swal2-popup.swal2-luxury {
            border-radius: 16px !important;
            border: 1px solid #e8dccb !important;
            box-shadow: 0 25px 50px -12px rgba(11, 15, 23, 0.25) !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            padding: 2rem 1.5rem !important;
        }
        div.swal2-popup.swal2-luxury .swal2-title {
            font-family: 'Outfit', sans-serif !important;
            font-weight: 700 !important;
            color: #0b0f17 !important;
            font-size: 1.35rem !important;
        }
        div.swal2-popup.swal2-luxury .swal2-html-container {
            font-size: 0.925rem !important;
            color: #475569 !important;
            line-height: 1.55 !important;
        }
        div.swal2-popup.swal2-luxury .swal2-confirm {
            background: linear-gradient(135deg, #0b0f17 0%, #1e293b 100%) !important;
            color: #c5a880 !important;
            border-radius: 10px !important;
            font-weight: 700 !important;
            padding: 0.65rem 1.75rem !important;
            box-shadow: 0 4px 12px rgba(11, 15, 23, 0.2) !important;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Synthesized Web Audio API Chime (Zero External Files, Zero Latency)
        function playAppAudioChime(type) {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const now = ctx.currentTime;
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);

                if (type === 'success') {
                    // Soft dual-harmonic luxury chime: C5 (523.25Hz) -> E5 (659.25Hz)
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(523.25, now);
                    osc.frequency.setValueAtTime(659.25, now + 0.12);
                    gain.gain.setValueAtTime(0.14, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.45);
                    osc.start(now);
                    osc.stop(now + 0.45);
                } else if (type === 'error' || type === 'warning') {
                    // Gentle low alert tone
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(260, now);
                    gain.gain.setValueAtTime(0.18, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.32);
                    osc.start(now);
                    osc.stop(now + 0.32);
                } else {
                    // Soft subtle notification note
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(440, now);
                    gain.gain.setValueAtTime(0.1, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);
                    osc.start(now);
                    osc.stop(now + 0.25);
                }
            } catch (e) {
                // AudioContext autoplay rules or unsupported environment — silent fallback
            }
        }

        // Global Modal Alert (replaces native alert with luxury SweetAlert2 dialog)
        window.showModalAlert = function (message, type = 'info', title = null, callback = null) {
            playAppAudioChime(type);

            const iconMap = {
                'success': 'success',
                'error': 'error',
                'warning': 'warning',
                'info': 'info'
            };

            const defaultTitles = {
                'success': 'Success',
                'error': 'Attention',
                'warning': 'Notice',
                'info': 'Information'
            };

            const modalTitle = title || defaultTitles[type] || 'Notice';
            const modalIcon = iconMap[type] || 'info';

            if (window.Swal && typeof window.Swal.fire === 'function') {
                return window.Swal.fire({
                    title: modalTitle,
                    text: message,
                    icon: modalIcon,
                    confirmButtonText: 'Dismiss',
                    customClass: {
                        popup: 'swal2-luxury'
                    }
                }).then(result => {
                    if (typeof callback === 'function') {
                        callback(result);
                    }
                });
            } else if (window.notifications && typeof window.notifications.show === 'function') {
                window.notifications.show(message, type);
                if (typeof callback === 'function') callback();
            } else {
                console.warn('[ModalAlert intercepted]:', message);
                if (typeof callback === 'function') callback();
            }
        };

        // Intercept native window.alert across the entire application to render luxury modal alert instead of browser popup
        window.alert = function (message) {
            window.showModalAlert(message, 'info');
        };

        // Explicit modal alert helper for UI notifications
        window.notify = function (message, type = 'info') {
            window.showModalAlert(message, type);
        };

        // Global promise-based confirmation modal (replaces window.confirm)
        window.confirmModal = function (options = {}) {
            const title = options.title || 'Please Confirm';
            const text = options.text || options.message || 'Are you sure you want to proceed?';
            const confirmText = options.confirmText || options.confirmButtonText || 'Yes, proceed';
            const cancelText = options.cancelText || options.cancelButtonText || 'Cancel';
            const icon = options.type || options.icon || 'warning';

            if (window.Swal && typeof window.Swal.fire === 'function') {
                return window.Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: cancelText,
                    confirmButtonColor: '#2563eb',
                    customClass: {
                        popup: 'swal2-luxury'
                    }
                }).then(result => result.isConfirmed);
            } else {
                return Promise.resolve(window.confirm(text));
            }
        };

        if (!window.NotificationSystem) {
            window.NotificationSystem = class {
                constructor() {
                    this.container = document.getElementById('notificationContainer');
                }

                show(message, type = 'info', duration = 3500) {
                    if (!this.container) return;

                    // Play pleasant subtle chime
                    playAppAudioChime(type);

                    const notification = document.createElement('div');
                    notification.className = `notification ${type}`;

                    const icon = this.getIcon(type);

                    notification.innerHTML = `
                        <div class="notification-content">
                            <div class="notification-icon">
                                <i class="${icon}"></i>
                            </div>
                            <div class="notification-message">${message}</div>
                            <button class="notification-close" aria-label="Close notification">
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
                        error: 'fas fa-triangle-exclamation',
                        warning: 'fas fa-circle-exclamation',
                        info: 'fas fa-circle-info'
                    };
                    return icons[type] || icons.info;
                }
            };
        }

        // Initialize notification system
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.notifications && window.NotificationSystem) {
                window.notifications = new NotificationSystem();
            }

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
<?php $__env->stopPush(); ?><?php /**PATH C:\Ketan\projects\barber\resources\views/components/notifications.blade.php ENDPATH**/ ?>