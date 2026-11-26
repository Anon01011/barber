/**
 * Customer Portal Dashboard Interactive Logic
 * Handles: Ratings, Quick Booking AJAX, and Real-time Slot Availability
 */

function showRatingModal(bookingId) {
    const apptInput = document.getElementById('appointment_id');
    const ratingForm = document.getElementById('ratingForm');
    const ratingError = document.getElementById('ratingError');

    if (ratingForm) ratingForm.reset();
    if (apptInput) apptInput.value = bookingId;
    if (ratingError) ratingError.classList.add('d-none');

    try {
        localStorage.setItem('feedback_modal_dismissed_' + bookingId, '1');
        sessionStorage.setItem('feedback_modal_dismissed_' + bookingId, '1');
    } catch(e) {}

    const modalEl = document.getElementById('ratingModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function submitRating() {
    const bookingId = document.getElementById('appointment_id')?.value;
    const ratingRadio = document.querySelector('input[name="rating"]:checked');
    const rating = ratingRadio ? ratingRadio.value : null;
    const comment = document.getElementById('comment')?.value || '';
    const ratingError = document.getElementById('ratingError');
    const submitBtn = document.getElementById('submitRatingBtn');

    if (!rating) {
        if (ratingError) {
            ratingError.classList.remove('d-none');
            ratingError.textContent = 'Please select a star rating.';
        }
        return;
    }

    try {
        localStorage.setItem('feedback_modal_dismissed_' + bookingId, '1');
        sessionStorage.setItem('feedback_modal_dismissed_' + bookingId, '1');
    } catch(e) {}

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...';
    }
    if (ratingError) ratingError.classList.add('d-none');

    const rateUrlTemplate = window.customerDashboardConfig?.rateUrlTemplate || '';
    const targetUrl = rateUrlTemplate.replace(':id', bookingId);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    fetch(targetUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            rating: parseInt(rating, 10),
            comment: comment
        })
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        if (status >= 400 || !body.success) {
            throw new Error(body.message || 'Error submitting rating.');
        }

        const modalEl = document.getElementById('ratingModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }

        if (window.notifications) {
            window.notifications.show('Thank you! Your rating and review have been submitted.', 'success');
        } else if (window.showModalAlert) {
            window.showModalAlert('Thank you! Your rating and review have been submitted.', 'success');
        } else {
            alert('Thank you! Your rating and review have been submitted.');
        }

        setTimeout(() => {
            window.location.reload();
        }, 1000);
    })
    .catch(error => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Submit Rating';
        }
        if (ratingError) {
            ratingError.classList.remove('d-none');
            ratingError.textContent = error.message || 'Error submitting rating. Please try again.';
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // Modal dismissal session tracking
    const ratingModalEl = document.getElementById('ratingModal');
    if (ratingModalEl) {
        ratingModalEl.addEventListener('hidden.bs.modal', function () {
            const apptId = document.getElementById('appointment_id')?.value;
            if (apptId) {
                try {
                    localStorage.setItem('feedback_modal_dismissed_' + apptId, '1');
                    sessionStorage.setItem('feedback_modal_dismissed_' + apptId, '1');
                } catch(e) {}
            }
        });
    }

    // Auto-prompt feedback modal if pending
    const pendingBookingId = window.customerDashboardConfig?.pendingFeedbackBookingId;
    if (pendingBookingId) {
        try {
            const feedbackDismissKey = 'feedback_modal_dismissed_' + pendingBookingId;
            if (!localStorage.getItem(feedbackDismissKey) && !sessionStorage.getItem(feedbackDismissKey)) {
                setTimeout(function () {
                    showRatingModal(pendingBookingId);
                }, 800);
            }
        } catch(e) {}
    }

    // Quick Book dynamic timeslots
    const quickBookForm = document.getElementById('quickBookForm');
    if (!quickBookForm) return;

    const dateInput = quickBookForm.querySelector('input[name="date"]');
    const timeSelect = quickBookForm.querySelector('select[name="time"]');
    const serviceSelect = quickBookForm.querySelector('select[name="service_id"]');

    function formatTime12(timeStr) {
        if (!timeStr) return '';
        const parts = timeStr.split(':');
        const hour = parseInt(parts[0], 10);
        const minute = parts[1] || '00';
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        return `${hour12}:${minute} ${ampm}`;
    }

    const slotsBaseUrl = window.customerDashboardConfig?.slotsBaseUrl || '';

    function loadTimeSlots() {
        if (!dateInput || !serviceSelect || !dateInput.value || !serviceSelect.value) return;

        timeSelect.disabled = true;
        timeSelect.innerHTML = '<option value="">Loading available slots...</option>';

        fetch(`${slotsBaseUrl}?date=${encodeURIComponent(dateInput.value)}&service_id=${encodeURIComponent(serviceSelect.value)}`)
            .then(response => response.json())
            .then(data => {
                timeSelect.innerHTML = '<option value="">Select a time</option>';

                if (!data || data.length === 0) {
                    timeSelect.innerHTML = '<option value="">No available slots for this date</option>';
                    return;
                }

                let availableCount = 0;
                data.forEach(slot => {
                    const slotTime = typeof slot === 'object' && slot !== null ? (slot.time || slot.slot) : slot;
                    const isAvailable = typeof slot === 'object' && slot !== null ? (slot.available !== false) : true;
                    if (isAvailable && slotTime) {
                        availableCount++;
                        const option = document.createElement('option');
                        option.value = slotTime;
                        option.textContent = formatTime12(slotTime);
                        timeSelect.appendChild(option);
                    }
                });

                if (availableCount === 0) {
                    timeSelect.innerHTML = '<option value="">No available slots on this date</option>';
                } else {
                    timeSelect.disabled = false;
                }
            })
            .catch(() => {
                timeSelect.innerHTML = '<option value="">Error loading slots</option>';
            });
    }

    if (dateInput && serviceSelect) {
        dateInput.addEventListener('change', loadTimeSlots);
        serviceSelect.addEventListener('change', function () {
            if (dateInput.value) {
                loadTimeSlots();
            }
        });
    }

    quickBookForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonText = submitButton ? submitButton.innerHTML : 'Book Appointment';

        const timeVal = formData.get('time');
        if (!timeVal) {
            alert('Please select an appointment time.');
            return;
        }

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Booking...';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                service_id: formData.get('service_id'),
                date: formData.get('date'),
                time: timeVal,
                notes: formData.get('notes')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modalEl = document.getElementById('quickBookModal');
                const modal = modalEl ? bootstrap.Modal.getInstance(modalEl) : null;
                if (modal) modal.hide();

                if (window.notifications) {
                    window.notifications.show('Appointment booked successfully!', 'success');
                } else if (window.showModalAlert) {
                    window.showModalAlert('Appointment booked successfully!', 'success');
                } else {
                    alert('Appointment booked successfully!');
                }

                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                throw new Error(data.message || 'Failed to book appointment');
            }
        })
        .catch(error => {
            const errMsg = error.message || 'Failed to book appointment. Please try again.';
            if (window.notifications) {
                window.notifications.show(errMsg, 'error');
            } else if (window.showModalAlert) {
                window.showModalAlert(errMsg, 'error');
            } else {
                alert(errMsg);
            }
        })
        .finally(() => {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    });
});
