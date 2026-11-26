<!-- Notification Container -->
<div id="notificationContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 99999;"></div>

@push('styles')
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
@endpush

@push('scripts')
    <script>
        // ─── Shared AudioContext (singleton) ─────────────────────────────────────
        // Browsers require user interaction before allowing AudioContext playback.
        // We use a single shared context and resume it lazily on first user gesture.
        let _sharedAudioCtx = null;
        let _audioUnlocked = false;
        let _pendingChimes = [];

        function _getOrCreateAudioCtx() {
            const AC = window.AudioContext || window.webkitAudioContext;
            if (!AC) return null;
            if (!_sharedAudioCtx) {
                try { _sharedAudioCtx = new AC(); } catch (e) { return null; }
            }
            return _sharedAudioCtx;
        }

        function _unlockAudio() {
            if (_audioUnlocked) return;
            const ctx = _getOrCreateAudioCtx();
            if (!ctx) return;
            if (ctx.state === 'suspended') {
                ctx.resume().then(() => {
                    _audioUnlocked = true;
                    // Drain any queued chimes
                    _pendingChimes.forEach(t => _playChimeNow(t));
                    _pendingChimes = [];
                }).catch(() => {});
            } else {
                _audioUnlocked = true;
                _pendingChimes.forEach(t => _playChimeNow(t));
                _pendingChimes = [];
            }
        }

        // Unlock on any user interaction
        ['click', 'keydown', 'touchstart', 'mousedown'].forEach(ev => {
            document.addEventListener(ev, _unlockAudio, { once: false, passive: true });
        });

        function _playChimeNow(type) {
            try {
                const ctx = _getOrCreateAudioCtx();
                if (!ctx) return;

                const runChime = () => {
                    const now = ctx.currentTime;
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    if (type === 'success') {
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(523.25, now);
                        osc.frequency.setValueAtTime(659.25, now + 0.12);
                        gain.gain.setValueAtTime(0.14, now);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.45);
                        osc.start(now); osc.stop(now + 0.45);
                    } else if (type === 'error' || type === 'danger') {
                        osc.type = 'triangle';
                        osc.frequency.setValueAtTime(280, now);
                        gain.gain.setValueAtTime(0.16, now);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                        osc.start(now); osc.stop(now + 0.35);
                    } else if (type === 'warning') {
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(400, now);
                        osc.frequency.setValueAtTime(350, now + 0.1);
                        gain.gain.setValueAtTime(0.14, now);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                        osc.start(now); osc.stop(now + 0.35);
                    } else {
                        // info / default — pleasant double-ping
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(660, now);
                        osc.frequency.setValueAtTime(880, now + 0.08);
                        gain.gain.setValueAtTime(0.12, now);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                        osc.start(now); osc.stop(now + 0.3);
                    }
                };

                if (ctx.state === 'suspended') {
                    ctx.resume().then(runChime).catch(() => {});
                } else {
                    runChime();
                }
            } catch (e) { /* silent */ }
        }

        // Public API — plays immediately if unlocked, queues otherwise
        function playAppAudioChime(type) {
            if (_audioUnlocked) {
                _playChimeNow(type);
            } else {
                _pendingChimes.push(type);
                // Try to unlock in case context was created before any gesture
                _unlockAudio();
            }
        }

        // Also expose for inline callers
        window.playAppAudioChime = playAppAudioChime;

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
            @if(session('success'))
                window.notifications.show(@json(session('success')), 'success');
            @endif

            @if(session('error'))
                window.notifications.show(@json(session('error')), 'error');
            @endif

            @if(session('warning'))
                window.notifications.show(@json(session('warning')), 'warning');
            @endif

            @if(session('info'))
                window.notifications.show(@json(session('info')), 'info');
            @endif

            // ─── Real-Time Notification Polling ───────────────────────────────
            (function () {
                // Only run when the user is authenticated (page has the meta CSRF token)
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfMeta) return;

                let lastSeenCount = parseInt(
                    document.querySelector('[data-unread-count]')?.dataset.unreadCount ?? '0',
                    10
                );
                const authUserId = '{{ auth()->id() ?? "guest" }}';
                const storageKey = 'agy_last_notif_id_' + authUserId;
                let lastSeenId = localStorage.getItem(storageKey) || null;
                let pollFailures = 0;

                async function pollUnread() {
                    try {
                        const resp = await fetch('/notifications/unread', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin',
                        });

                        if (!resp.ok) { pollFailures++; return; }
                        pollFailures = 0;

                        const data = await resp.json();
                        if (!data.success) return;

                        // Update bell badge
                        const badge = document.getElementById('notif-unread-badge');
                        if (badge) {
                            if (data.count > 0) {
                                badge.textContent = data.count > 99 ? '99+' : data.count;
                                badge.style.display = '';
                            } else {
                                badge.style.display = 'none';
                            }
                        }

                        // Fire toast + chime for each genuinely new notification
                        if (data.items && data.items.length > 0) {
                            const newestId = data.items[0].id;
                            if (lastSeenId === null) {
                                // First poll — set baseline silently
                                lastSeenId = newestId;
                                localStorage.setItem(storageKey, newestId);
                                return;
                            }

                            if (newestId !== lastSeenId) {
                                // Show only genuinely new items (those we haven't seen yet)
                                const newItems = [];
                                for (const item of data.items) {
                                    if (item.id === lastSeenId) break;
                                    newItems.push(item);
                                }

                                // Show up to 3 toasts so we don't spam
                                const toShow = newItems.slice(0, 3).reverse();
                                toShow.forEach(item => {
                                    if (window.notifications && typeof window.notifications.show === 'function') {
                                        // Format clean, compact message for toast (not huge card dump)
                                        let compactMsg = '';
                                        const notifTitle = item.title || (item.data && item.data.title) || 'New Notification';
                                        if (item.message) {
                                            const tempD = document.createElement('div');
                                            tempD.innerHTML = item.message;
                                            const plain = tempD.textContent.trim().replace(/\s+/g, ' ');
                                            const snippet = plain.length > 65 ? plain.substring(0, 65) + '...' : plain;
                                            compactMsg = `<div class="fw-bold text-dark mb-0" style="font-size:0.86rem;">${notifTitle}</div><div class="small text-muted" style="font-size:0.75rem;">${snippet}</div>`;
                                        } else {
                                            compactMsg = `<div class="fw-bold text-dark mb-0">${notifTitle}</div>`;
                                        }

                                        const notifEl = window.notifications.show(compactMsg, item.type || 'info', 6000);

                                        // Allow clicking toast to open notification details!
                                        if (notifEl && typeof window.showNotificationDetail === 'function') {
                                            notifEl.style.cursor = 'pointer';
                                            notifEl.addEventListener('click', function(e) {
                                                if (e.target.closest('.notification-close')) return;
                                                window.showNotificationDetail(item);
                                            });
                                        }
                                    }
                                });

                                lastSeenId = newestId;
                                localStorage.setItem(storageKey, newestId);
                            }
                        }
                    } catch (err) {
                        pollFailures++;
                    }

                    // Back-off: if 5 consecutive failures, slow down to every 5 minutes
                    if (pollFailures > 5) {
                        setTimeout(pollUnread, 5 * 60 * 1000);
                    }
                }

                // Initial poll after 5 seconds (let page fully load), then every 30 seconds
                setTimeout(function () {
                    pollUnread();
                    setInterval(pollUnread, 30 * 1000);
                }, 5000);
            })();
            // ─────────────────────────────────────────────────────────────────
        });
    </script>
@endpush