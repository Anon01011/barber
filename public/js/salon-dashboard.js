/**
 * Salon Admin & Tenant Dashboard Interactive Scripts
 * Handles: Date filtering, Period pills, and Responsive Range Toggles
 */

function setDateRange(range) {
    const input = document.getElementById('date_range_input');
    const form = document.getElementById('dateFilterForm');
    if (input && form) {
        input.value = range;
        if (range !== 'custom') {
            form.submit();
        }
    }
}

function toggleCustomRange() {
    const customDiv = document.getElementById('custom-range-integrated');
    const input = document.getElementById('date_range_input');
    if (customDiv && input) {
        const isShown = customDiv.classList.contains('show');
        if (isShown) {
            customDiv.classList.remove('show');
            input.value = 'today';
        } else {
            customDiv.classList.add('show');
            input.value = 'custom';
            const startInput = document.getElementById('start_date');
            if (startInput) startInput.focus();
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Tooltip initialization
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
