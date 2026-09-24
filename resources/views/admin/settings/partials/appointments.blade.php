<!-- Guest Booking Settings -->
<div class="settings-section-card">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-user-clock"></i></div>
        <h6>Guest Booking Configuration</h6>
    </div>

    <div class="settings-field-group">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="form-check form-switch custom-switch pt-4">
                    <input class="form-check-input" type="checkbox" name="guest_booking_enabled" value="1"
                        id="guest_booking_enabled" {{ ($settings['guest_booking_enabled'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold small text-uppercase text-secondary"
                        for="guest_booking_enabled">Enable Guest Booking</label>
                </div>
                <p class="text-muted small mt-2 mb-0">Allow customers to book appointments without creating an account.
                </p>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-check-circle text-primary me-2"></i>Default Booking Status
                </label>
                <select class="form-select" name="default_guest_status">
                    <option value="pending" {{ ($settings['default_guest_status'] ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="confirmed" {{ ($settings['default_guest_status'] ?? 'pending') == 'confirmed' ? 'selected' : '' }}>Auto-Confirmed</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-hourglass-start text-success me-2"></i>Shift Start Time
                </label>
                <input type="time" name="working_hours_start" class="form-control"
                    value="{{ $settings['working_hours_start'] ?? '08:00' }}">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-hourglass-end text-danger me-2"></i>Shift End Time
                </label>
                <input type="time" name="working_hours_end" class="form-control"
                    value="{{ $settings['working_hours_end'] ?? '20:00' }}">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-calendar-alt text-info me-2"></i>Advance Booking Window
                </label>
                <div class="input-group">
                    <input type="number" name="advance_booking_days" class="form-control"
                        value="{{ $settings['advance_booking_days'] ?? 30 }}" min="1" max="365">
                    <span class="input-group-text bg-light">Days</span>
                </div>
                <small class="text-muted">Maximum days in advance customers can book.</small>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-stopwatch text-warning me-2"></i>Service Slot Duration
                </label>
                <div class="input-group">
                    <input type="number" name="slot_duration" class="form-control"
                        value="{{ $settings['slot_duration'] ?? 30 }}" min="5" max="120" step="5">
                    <span class="input-group-text bg-light">Minutes</span>
                </div>
                <small class="text-muted">Interval between available time slots.</small>
            </div>
        </div>
    </div>
</div>

<!-- General Logic -->
<div class="settings-section-card">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-sliders-h"></i></div>
        <h6>Booking Logic & Rules</h6>
    </div>

    <div class="settings-field-group">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-coffee text-secondary me-2"></i>Turnover / Buffer Time
                </label>
                <div class="input-group">
                    <input type="number" name="appointment_buffer_time" class="form-control"
                        value="{{ $settings['appointment_buffer_time'] ?? 15 }}" min="0" step="5">
                    <span class="input-group-text bg-light">Minutes</span>
                </div>
                <small class="text-muted">Preparation time required between any two appointments.</small>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-file-invoice-dollar text-danger me-2"></i>No-Show / Late Fee
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white">{{ $settings['currency_symbol'] ?? '$' }}</span>
                    <input type="number" name="no_show_fee" class="form-control"
                        value="{{ $settings['no_show_fee'] ?? 0 }}" min="0" step="0.01">
                </div>
                <small class="text-muted">Service charge for missed appointments.</small>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="allow_overlapping_bookings" value="1"
                        id="allow_overlapping_bookings" {{ ($settings['allow_overlapping_bookings'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                        for="allow_overlapping_bookings">Allow Parallel Bookings</label>
                </div>
                <p class="text-muted small mt-1">Allow multiple clients to be booked at the same time slot.</p>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="auto_confirm_bookings" value="1"
                        id="auto_confirm_bookings" {{ ($settings['auto_confirm_bookings'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                        for="auto_confirm_bookings">Global Auto-Confirm</label>
                </div>
                <p class="text-muted small mt-1">Skip manual approval for all incoming appointments.</p>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-gavel text-secondary me-2"></i>Cancellation & Terms Policy
                </label>
                <textarea name="cancellation_policy" class="form-control" rows="4"
                    placeholder="Describe your cancellation policy here...">{{ $settings['cancellation_policy'] ?? '' }}</textarea>
                <small class="text-muted">This will be shown to customers during the booking process.</small>
            </div>
        </div>
    </div>
</div>