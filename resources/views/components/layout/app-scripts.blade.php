    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>window.jQuery || document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"><\/script>')</script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Global Session Expiration & CSRF Auto-Reload Handler
        if (window.jQuery) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).ajaxError(function(event, jqXHR) {
                if (jqXHR.status === 401 || jqXHR.status === 419) {
                    console.warn('Session expired or unauthenticated. Auto-reloading to re-authenticate...');
                    if (!window.isRedirectingToLogin) {
                        window.isRedirectingToLogin = true;
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    }
                }
            });
        }

        // Responsive Sidebar & Drawer Logic
        const sidebar = document.getElementById('sidebar');
        const header = document.getElementById('header');
        const mainContent = document.getElementById('mainContent');
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');

        function isMobile() {
            return window.innerWidth < 992;
        }

        function openMobileSidebar() {
            if (sidebar) sidebar.classList.add('mobile-show');
            if (sidebarBackdrop) sidebarBackdrop.classList.add('show');
            document.body.classList.add('mobile-drawer-open');
        }

        function closeMobileSidebar() {
            if (sidebar) sidebar.classList.remove('mobile-show');
            if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
            document.body.classList.remove('mobile-drawer-open');
        }

        // Check if sidebar was previously collapsed on desktop
        if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
            if (sidebar) sidebar.classList.add('collapsed');
            if (header) header.classList.add('collapsed');
            if (mainContent) mainContent.classList.add('expanded');
        }

        if (toggleSidebar) {
            toggleSidebar.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (isMobile()) {
                    if (sidebar && sidebar.classList.contains('mobile-show')) {
                        closeMobileSidebar();
                    } else {
                        openMobileSidebar();
                    }
                } else {
                    if (sidebar) sidebar.classList.toggle('collapsed');
                    if (header) header.classList.toggle('collapsed');
                    if (mainContent) mainContent.classList.toggle('expanded');

                    localStorage.setItem('sidebarCollapsed', sidebar ? sidebar.classList.contains('collapsed') : 'false');
                    toggleTooltips();
                }
            });
        }

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                openMobileSidebar();
            });
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', closeMobileSidebar);
        }

        // Close mobile drawer when clicking a non-collapsible link
        if (sidebar) {
            sidebar.querySelectorAll('.sidebar-item:not([data-bs-toggle="collapse"])').forEach(item => {
                item.addEventListener('click', () => {
                    if (isMobile()) closeMobileSidebar();
                });
            });
        }

        // Responsive resize listener
        window.addEventListener('resize', () => {
            if (!isMobile() && sidebar && sidebar.classList.contains('mobile-show')) {
                closeMobileSidebar();
            }
        });

        // Initialize Bootstrap tooltips
        function initTooltips() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            return tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Toggle tooltips based on sidebar state
        function toggleTooltips() {
            if (!sidebar) return;
            const tooltipTriggerList = document.querySelectorAll('.sidebar-item[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(function (element) {
                const tooltip = bootstrap.Tooltip.getInstance(element);
                if (sidebar.classList.contains('collapsed') && !isMobile()) {
                    if (!tooltip) {
                        new bootstrap.Tooltip(element);
                    } else {
                        tooltip.enable();
                    }
                } else {
                    if (tooltip) {
                        tooltip.disable();
                    }
                }
            });
        }

        // Initialize tooltips on page load
        initTooltips();
        toggleTooltips();

        // User Dropdown
        const userDropdownToggle = document.getElementById('userDropdownToggle');
        const userDropdownMenu = document.getElementById('userDropdownMenu');

        if (userDropdownToggle && userDropdownMenu) {
            userDropdownToggle.addEventListener('click', () => {
                userDropdownMenu.classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                if (!userDropdownToggle.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                    userDropdownMenu.classList.remove('show');
                }
            });
        }

        // Set role-specific background as default
        @auth
                                                                                                                                                                        const userRole = '{{ auth()->user()->roles->first()->name ?? "" }}';
            if (userRole) {
                sidebar.classList.add(`bg-${userRole.replace('_', '-')}`);
            }
        @endauth

        // Apply saved theme color from localStorage (overrides role-based color)
        // Theme color has priority because it's user-selected
        const savedThemeColor = localStorage.getItem('salon_theme_color');
        if (savedThemeColor) {
            const themeColorMap = {
                'luxury': '#0b0f17',
                'barber': '#B45309',
                'pink': '#e83e8c',
                'blue': '#0d6efd',
                'purple': '#6f42c1',
                'green': '#198754'
            };

            if (sidebar && themeColorMap[savedThemeColor]) {
                // Use inline style with !important to override CSS class and gradients
                sidebar.style.setProperty('background', themeColorMap[savedThemeColor], 'important');
                sidebar.style.setProperty('background-color', themeColorMap[savedThemeColor], 'important');
            }
        }

        // Notification Detail Modal Logic
        function showNotificationDetail(notification) {
            // Populate modal content
            const headerEl = document.getElementById('detailModalHeader');
            const closeBtnEl = document.getElementById('detailModalCloseBtn');
            const titleEl = document.getElementById('detailModalHeading');
            const timeEl = document.getElementById('detailModalTime');
            const messageEl = document.getElementById('detailModalMessage');
            const iconEl = document.getElementById('detailModalIcon');
            const actionBtnEl = document.getElementById('detailModalActionBtn');

            if (titleEl) titleEl.innerText = notification.title;

            // Format date
            if (timeEl) {
                const date = new Date(notification.created_at);
                timeEl.innerText = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
            }

            // Use innerHTML for rich text content
            if (messageEl) messageEl.innerHTML = notification.message;

            // Color Map
            const colorMap = {
                'info': { bg: 'bg-info', text: 'text-white', icon: 'info-circle' },
                'success': { bg: 'bg-success', text: 'text-white', icon: 'check-circle' },
                'warning': { bg: 'bg-warning', text: 'text-dark', icon: 'exclamation-triangle' },
                'danger': { bg: 'bg-danger', text: 'text-white', icon: 'exclamation-circle' }
            };
            const style = colorMap[notification.type] || colorMap['info'];

            // Apply Header Styles
            if (headerEl) {
                headerEl.className = `modal-header border-bottom-0 py-3 ${style.bg} ${style.text}`;
            }

            // Apply Text Styles
            if (titleEl) titleEl.className = `modal-title fw-bold mb-0 ${style.text}`;
            if (timeEl) timeEl.className = `small ${style.text === 'text-white' ? 'text-white-50' : 'text-muted'}`;

            // Apply Close Button Style
            if (closeBtnEl) {
                if (style.text === 'text-white') {
                    closeBtnEl.classList.add('btn-close-white');
                } else {
                    closeBtnEl.classList.remove('btn-close-white');
                }
            }

            // Set icon
            if (iconEl) {
                const iconHtml = `<div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25 ${style.text}" style="width: 40px; height: 40px;">
                        <i class="fas fa-${style.icon} fa-lg"></i>
                    </div>`;
                iconEl.innerHTML = iconHtml;
            }

            // Handle Action Button
            if (actionBtnEl) {
                if (notification.link) {
                    actionBtnEl.href = notification.link;
                    actionBtnEl.style.display = 'inline-block';
                    actionBtnEl.className = `btn btn-${notification.type === 'warning' ? 'warning' : 'primary'}`;
                } else {
                    actionBtnEl.style.display = 'none';
                }
            }

            // Show modal
            const modalElement = document.getElementById('notificationDetailModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }

            // Mark as read in localStorage (client-side only for now)
            let readNotifications = JSON.parse(localStorage.getItem('read_notifications') || '[]');
            if (!readNotifications.includes(notification.id)) {
                readNotifications.push(notification.id);
                localStorage.setItem('read_notifications', JSON.stringify(readNotifications));

                // Update badge
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    const currentCount = parseInt(badge.innerText.trim());
                    if (!isNaN(currentCount) && currentCount > 1) {
                        badge.innerText = currentCount - 1;
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }

            // Update last seen for auto-popup logic
            localStorage.setItem('last_seen_notification_id', notification.id);
        }

        // Mark All as Read functionality
        const markAllReadBtn = document.getElementById('markAllReadBtn');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                fetch('{{ route("notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear all notifications from localStorage
                            localStorage.setItem('read_notifications', JSON.stringify([]));

                            // Hide badge
                            const badge = document.getElementById('notificationBadge');
                            if (badge) {
                                badge.style.display = 'none';
                            }

                            // Reload page to refresh notification list
                            location.reload();
                        }
                    })
                    .catch(error => {

                    });
            });
        }
    </script>

    <!-- Notification Detail Modal -->
    <div class="modal fade" id="notificationDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden">
                <div id="detailModalHeader" class="modal-header border-bottom-0 py-3">
                    <h5 class="modal-title fw-bold mb-0" id="detailModalHeading">Notification</h5>
                    <button type="button" class="btn-close" id="detailModalCloseBtn" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">

                    <div id="detailModalMessage" class="mb-4"></div>
                    <a href="#" id="detailModalActionBtn" class="btn btn-primary px-4" style="display: none;">View
                        Details</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Components -->
    @include('components.notifications')
    @stack('components')

    <!-- International Tel Input JS -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>

    <!-- Customer Phone Handler -->
    <script src="{{ asset('js/customer-phone-handler.js') }}?v={{ time() }}"></script>

    <!-- Global Helpers -->
    <script>
        function getSalonSlug() {
            // 1. Try meta tag (most reliable)
            const meta = document.querySelector('meta[name="salon-slug"]');
            if (meta && meta.getAttribute('content')) return meta.getAttribute('content');

            // 2. Try PHP variable injected by controller/middleware
            // Note: Use optional to avoid errors if object is missing
            const phpSlug = "{{ app()->bound('current_salon') ? app('current_salon')->slug : '' }}";
            if (phpSlug) return phpSlug;

            // 3. Fallback: Parse from URL
            const segments = window.location.pathname.split('/').filter(s => s);
            if (segments.length > 0) {
                // If the first segment is an admin/customer/staff prefix, it's not the slug
                const commonPrefixes = ['admin', 'customer', 'staff', 'employee', 'pos'];
                if (commonPrefixes.includes(segments[0])) return '';
                return segments[0];
            }
            return '';
        }
    </script>

    <!-- Floating AI Copilot Assistant Widget -->
    @if(auth()->check() && auth()->user()->salon && optional(auth()->user()->salon)->canUseFeature('AI Insights & Automation') && \App\Helpers\ModuleHelper::aiEnabled() && app(\App\Services\SettingsService::class)->get('enable_ai_floating_widget', true, auth()->user()->salon_id))
        <style>
            #floatingAiInput::placeholder {
                color: #94a3b8 !important;
                opacity: 1 !important;
            }
            .floating-action-pill {
                background: rgba(99, 102, 241, 0.18);
                color: #c7d2fe;
                border: 1px solid rgba(99, 102, 241, 0.35);
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.76rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .floating-action-pill:hover {
                background: #4f46e5;
                color: #ffffff;
                border-color: #4f46e5;
            }
            @media (max-width: 991.98px) {
                #floatingAiBtn {
                    bottom: calc(58px + 16px + env(safe-area-inset-bottom, 0px)) !important;
                    left: 20px !important;
                    right: auto !important;
                }
                #floatingAiModal {
                    bottom: calc(58px + 80px + env(safe-area-inset-bottom, 0px)) !important;
                    left: 12px !important;
                    right: 12px !important;
                    width: calc(100vw - 24px) !important;
                    max-width: 420px !important;
                }
            }
        </style>
        <button id="floatingAiBtn" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="position: fixed; bottom: 24px; right: 24px; width: 56px; height: 56px; z-index: 9999; border: 2px solid #818cf8; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); transition: transform 0.2s ease;" onclick="toggleFloatingAiModal()">
            <i class="fas fa-brain fa-lg text-white"></i>
        </button>

        <div id="floatingAiModal" class="card shadow-lg border-0 rounded-4 overflow-hidden" style="position: fixed; bottom: 90px; right: 24px; width: 420px; max-width: 92vw; height: 540px; z-index: 9999; display: none; background: #0f172a; color: #ffffff; border: 1px solid #334155; box-shadow: 0 20px 40px rgba(0,0,0,0.5)!important;">
            <div class="card-header bg-transparent border-bottom border-slate-700 py-3 px-4 d-flex justify-content-between align-items-center" style="background: #1e293b!important;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-brain text-indigo-400 me-2 fa-lg" style="color: #818cf8;"></i>
                    <h6 class="fw-bold mb-0 text-white" style="font-size: 0.95rem;">Salon AI Copilot</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" onclick="toggleFloatingAiModal()"></button>
            </div>
            <div class="card-body p-3 d-flex flex-column" style="height: calc(100% - 60px);">
                <div id="floatingChatBox" class="flex-grow-1 mb-3 p-3 rounded-3" style="background: rgba(15, 23, 42, 0.95); border: 1px solid #1e293b; overflow-y: auto; max-height: 400px;">
                    <div class="mb-2 p-3 rounded-3" style="background: #1e293b; border: 1px solid #334155; font-size: 0.88rem; color: #f8fafc;">
                        <div class="fw-bold text-white mb-1" style="color: #818cf8!important;"><i class="fas fa-microchip me-1"></i> Copilot Assistant</div>
                        <div>Ask anything about revenue, top staff, stock warnings, or customer retention:</div>
                    </div>
                </div>
                <form id="floatingAiForm" onsubmit="return handleFloatingAiSubmit(event)">
                    <div class="input-group">
                        <input type="text" id="floatingAiInput" class="form-control text-white" placeholder="Ask AI Copilot a question..." autocomplete="off" onkeydown="if(event.key==='Enter'||event.keyCode===13){handleFloatingAiSubmit(event);}" style="background-color: #1e293b !important; color: #ffffff !important; border: 1px solid #334155 !important; font-size: 0.9rem;">
                        <button type="submit" class="btn px-3" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; font-weight: 600;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            if (typeof escapeHtml !== 'function') {
                function escapeHtml(text) {
                    return text ? String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
                }
            }

            if (typeof escapeJsString !== 'function') {
                function escapeJsString(text) {
                    return text ? String(text).replace(/\\/g, '\\\\').replace(/'/g, "\\'") : '';
                }
            }

            if (typeof formatResponseText !== 'function') {
                function formatResponseText(text) {
                    if (!text) return '';
                    return text
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>')
                        .replace(/\n/g, '<br>');
                }
            }

            function toggleFloatingAiModal() {
                const modal = document.getElementById('floatingAiModal');
                if (modal.style.display === 'none' || !modal.style.display) {
                    modal.style.display = 'block';
                    setTimeout(() => {
                        const input = document.getElementById('floatingAiInput');
                        if (input) input.focus();
                    }, 100);
                } else {
                    modal.style.display = 'none';
                }
            }

            function handleFloatingAiSubmit(e) {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                const input = document.getElementById('floatingAiInput');
                if (!input) return false;

                const msg = input.value.trim();
                if (!msg) return false;

                const chatBox = document.getElementById('floatingChatBox');
                if (!chatBox) return false;

                chatBox.innerHTML += `<div class="mb-2 p-2 px-3 rounded-3 text-end" style="background: linear-gradient(135deg, #4f46e5, #6366f1); color:#fff; font-size:0.85rem; margin-left:auto; max-width:85%; box-shadow: 0 4px 10px rgba(79,70,229,0.3);"><strong>You:</strong> ${escapeHtml(msg)}</div>`;
                
                const loaderId = 'loader_' + Date.now();
                chatBox.innerHTML += `<div id="${loaderId}" class="mb-2 p-2 px-3 rounded-3 text-slate-400 small" style="background:#1e293b; border:1px solid #334155;"><i class="fas fa-spinner fa-spin me-2 text-indigo-400"></i>AI Copilot is thinking...</div>`;
                chatBox.scrollTop = chatBox.scrollHeight;
                
                input.value = '';

                const salonSlug = "{{ optional(auth()->user()->salon)->slug }}";
                const routeUrl = salonSlug ? `/${salonSlug}/admin/ai/copilot` : "{{ route('admin.ai.copilot', ['salon_slug' => optional(auth()->user()->salon)->slug ?? 'default']) }}";

                fetch(routeUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ message: msg })
                })
                .then(res => res.json())
                .then(data => {
                    const loader = document.getElementById(loaderId);
                    if (loader) loader.remove();

                    const responseText = data.response || "No response received.";
                    let respHtml = `<div class="mb-2 p-3 rounded-3" style="background:#1e293b; border:1px solid #334155; font-size:0.88rem; color:#f8fafc;"><div class="fw-bold mb-1" style="color:#818cf8!important;"><i class="fas fa-brain me-1"></i> Copilot Intelligence</div><div>${formatResponseText(responseText)}</div>`;
                    
                    if (data.suggested_actions && data.suggested_actions.length > 0) {
                        respHtml += `<div class="mt-2 d-flex flex-wrap gap-1">` + data.suggested_actions.map(act => 
                            `<button type="button" class="floating-action-pill" onclick="handleSuggestedAction('${act.action}', '${escapeJsString(act.label)}')"><i class="fas fa-bolt me-1"></i>${escapeHtml(act.label)}</button>`
                        ).join('') + `</div>`;
                    }

                    respHtml += `</div>`;
                    chatBox.innerHTML += respHtml;
                    chatBox.scrollTop = chatBox.scrollHeight;
                })
                .catch(err => {
                    const loader = document.getElementById(loaderId);
                    if (loader) loader.remove();

                    chatBox.innerHTML += `<div class="mb-2 p-2 rounded-3 text-danger small" style="background:#1e293b; border:1px solid #ef4444;"><i class="fas fa-exclamation-circle me-1"></i> Error querying AI Copilot. Please try again.</div>`;
                    chatBox.scrollTop = chatBox.scrollHeight;
                });

                return false;
            }

            if (typeof handleSuggestedAction !== 'function') {
                function handleSuggestedAction(actionName, labelText) {
                    const salonSlug = "{{ optional(auth()->user()->salon)->slug }}";
                    const navRoutes = {
                        'view_customers': `/${salonSlug}/admin/customers`,
                        'manage_services': `/${salonSlug}/admin/services/categories`,
                        'view_staff': `/${salonSlug}/admin/bookings`,
                        'view_staff_schedule': `/${salonSlug}/admin/bookings`,
                        'open_pos': `/${salonSlug}/admin/pos`,
                        'view_appointments': `/${salonSlug}/admin/bookings`
                    };

                    if (navRoutes[actionName]) {
                        window.location.href = navRoutes[actionName];
                        return;
                    }

                    if (actionName === 'top_staff' || actionName === 'revenue') {
                        const input = document.getElementById('floatingAiInput');
                        if (input) {
                            input.value = labelText || actionName;
                            handleFloatingAiSubmit();
                        }
                        return;
                    }

                    triggerAutomation(actionName);
                }
            }

            if (typeof triggerAutomation !== 'function') {
                function triggerAutomation(actionName) {
                    const salonSlug = "{{ optional(auth()->user()->salon)->slug }}";
                    const routeUrl = salonSlug ? `/${salonSlug}/admin/ai/automation` : "{{ route('admin.ai.automation', ['salon_slug' => optional(auth()->user()->salon)->slug ?? 'default']) }}";

                    const chatBox = document.getElementById('floatingChatBox');
                    if (chatBox) {
                        chatBox.innerHTML += `<div class="mb-2 p-2 rounded-3 text-indigo-300 small" style="background:#1e293b; border:1px solid #4f46e5;"><i class="fas fa-cog fa-spin me-1"></i> Executing AI Automation: ${escapeHtml(actionName)}...</div>`;
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }

                    fetch(routeUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ action: actionName })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (chatBox) {
                            const msg = (data.result && data.result.message) ? data.result.message : 'Workflow executed successfully.';
                            chatBox.innerHTML += `<div class="mb-2 p-2 px-3 rounded-3 text-success small" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3);"><i class="fas fa-check-circle me-1"></i> AI Automation Completed: ${escapeHtml(msg)}</div>`;
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    })
                    .catch(err => {
                        if (chatBox) {
                            chatBox.innerHTML += `<div class="mb-2 p-2 px-3 rounded-3 text-info small" style="background:#1e293b; border:1px solid #334155;"><i class="fas fa-info-circle me-1"></i> Automation request completed.</div>`;
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    });
                }
            }
        </script>
    @endif
