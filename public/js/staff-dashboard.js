/**
 * Staff / Stylist Workspace Dashboard Logic
 * Fully decoupled script with secure DOM encoding and fallback routing.
 */

(function () {
    'use strict';

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getSalonSlug() {
        if (window.staffDashboardConfig && window.staffDashboardConfig.salonSlug) {
            return window.staffDashboardConfig.salonSlug;
        }
        const pathParts = window.location.pathname.split('/').filter(Boolean);
        return pathParts[0] || 'default';
    }

    function getCurrencySymbol() {
        return (window.staffDashboardConfig && window.staffDashboardConfig.currencySymbol) || '$';
    }

    function getStatusColor(status) {
        const colors = {
            'pending': 'warning',
            'confirmed': 'success',
            'completed': 'info',
            'cancelled': 'danger'
        };
        return colors[(status || '').toLowerCase()] || 'secondary';
    }

    window.viewAppointment = function (id) {
        const modalEl = document.getElementById('viewAppointmentModal');
        if (!modalEl) return;

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const modalBody = document.getElementById('appointmentModalBody');

        if (modalBody) {
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading appointment details...</p>
                </div>
            `;
        }

        modal.show();
        const salonSlug = getSalonSlug();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        fetch(`/${encodeURIComponent(salonSlug)}/employee/appointments/${encodeURIComponent(id)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(res => res.json())
            .then(res => {
                if (!res.success) throw new Error(res.message || 'Failed to load details');

                const data = res.data || {};
                const serviceName = escapeHtml(data.service?.name || 'N/A');
                const customerName = escapeHtml(data.customer?.name || 'N/A');
                const startTime = escapeHtml(new Date(data.start_time).toLocaleString());
                const status = escapeHtml(data.status || 'unknown');
                const statusBadge = getStatusColor(data.status);
                const duration = parseInt(data.service?.duration || 0, 10);
                const currency = escapeHtml(getCurrencySymbol());
                const price = parseFloat(data.service?.price || 0).toFixed(2);
                const total = parseFloat(data.amount || 0).toFixed(2);

                const html = `
                    <div class="row g-4">
                        <div class="col-12 text-center pb-3 border-bottom">
                            <div class="mb-2">
                                <span class="badge bg-${statusBadge} rounded-pill fs-6 px-3 py-2 text-uppercase">
                                    ${status}
                                </span>
                            </div>
                            <h4 class="mb-0 fw-bold">${serviceName}</h4>
                            <p class="text-muted mb-0 small">${startTime}</p>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent ps-0">
                                    <span class="text-muted small text-uppercase fw-bold">Customer</span>
                                    <span class="fw-bold text-dark">${customerName}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent ps-0">
                                    <span class="text-muted small text-uppercase fw-bold">Service Time</span>
                                    <span class="fw-bold text-dark">${duration} mins</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent ps-0">
                                    <span class="text-muted small text-uppercase fw-bold">Price</span>
                                    <span class="fw-bold text-dark">${currency}${price}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent ps-0">
                                    <span class="text-muted small text-uppercase fw-bold">Total</span>
                                    <span class="fw-bold text-primary">${currency}${total}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                `;
                if (modalBody) modalBody.innerHTML = html;
            })
            .catch(err => {
                if (modalBody) {
                    modalBody.innerHTML = `
                        <div class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                            <p class="mb-0">Failed to load details. Please try again.</p>
                        </div>
                    `;
                }
            });
    };
})();
