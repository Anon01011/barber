@php
    $currentSalon = app()->bound('current_salon') && app('current_salon') ? app('current_salon') : auth()->user()?->salon;
    $isOnTrial = $currentSalon && method_exists($currentSalon, 'isOnTrial') && $currentSalon->isOnTrial();
    $hasLoyaltyPlan = \App\Helpers\ModuleHelper::loyaltyEnabled() && $currentSalon && ($currentSalon->hasLoyaltyProgram() || $isOnTrial);
    $hasFeedbackPlan = \App\Helpers\ModuleHelper::feedbackEnabled() && $currentSalon && ($currentSalon->hasFeedbackModule() || $isOnTrial);
    $sym = currency_symbol();
@endphp

@if($hasLoyaltyPlan)
<!-- Loyalty Program Card -->
<div class="card settings-section-card rounded-3 border-0 mb-4">
    <div class="card-body p-4">
        <div class="settings-section-header border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="fas fa-coins text-warning me-2"></i>Loyalty Points &amp; Rewards Program
                </h5>
                <p class="text-muted small mb-0">Configure points earning rates and POS redemption thresholds for your customers.</p>
            </div>
        </div>

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="loyalty_enabled" name="loyalty_enabled" value="1"
                {{ ($settings['loyalty_enabled'] ?? false) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-dark ms-2" for="loyalty_enabled">
                Enable Loyalty Points Program
            </label>
            <div class="text-muted small ms-2">Customers automatically earn points at POS checkout and can redeem them for discounts.</div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="loyalty_points_per_currency" class="form-label fw-semibold">
                    Points Earned per {{ $sym }}1 Spent
                </label>
                <input type="number" class="form-control" id="loyalty_points_per_currency" name="loyalty_points_per_currency"
                    min="1" max="1000" value="{{ old('loyalty_points_per_currency', $settings['loyalty_points_per_currency'] ?? 1) }}">
                <small class="text-muted">Example: <code>1</code> means a {{ $sym }}50 bill earns 50 points.</small>
            </div>

            <div class="col-md-6">
                <label for="loyalty_point_redemption_rate" class="form-label fw-semibold">
                    Cash Value of 1 Point ({{ $sym }})
                </label>
                <input type="number" step="0.001" class="form-control" id="loyalty_point_redemption_rate" name="loyalty_point_redemption_rate"
                    min="0.001" max="1000" value="{{ old('loyalty_point_redemption_rate', $settings['loyalty_point_redemption_rate'] ?? 0.05) }}">
                <small class="text-muted">Example: <code>0.05</code> means 100 points = {{ $sym }}5.00 discount.</small>
            </div>

            <div class="col-md-6">
                <label for="loyalty_min_points_to_redeem" class="form-label fw-semibold">
                    Minimum Points Required to Redeem
                </label>
                <input type="number" class="form-control" id="loyalty_min_points_to_redeem" name="loyalty_min_points_to_redeem"
                    min="0" max="100000" value="{{ old('loyalty_min_points_to_redeem', $settings['loyalty_min_points_to_redeem'] ?? 50) }}">
                <small class="text-muted">Customers must reach at least this balance before redeeming.</small>
            </div>

            <div class="col-md-6">
                <label for="loyalty_max_discount_percentage" class="form-label fw-semibold">
                    Max Bill Discount from Points (%)
                </label>
                <input type="number" class="form-control" id="loyalty_max_discount_percentage" name="loyalty_max_discount_percentage"
                    min="1" max="100" value="{{ old('loyalty_max_discount_percentage', $settings['loyalty_max_discount_percentage'] ?? 50) }}">
                <small class="text-muted">Maximum percentage of an invoice payable with loyalty points.</small>
            </div>

            <div class="col-md-6">
                <label for="loyalty_points_expiry_months" class="form-label fw-semibold">
                    Points Expiry Duration (Months)
                </label>
                <input type="number" class="form-control" id="loyalty_points_expiry_months" name="loyalty_points_expiry_months"
                    min="0" max="120" value="{{ old('loyalty_points_expiry_months', $settings['loyalty_points_expiry_months'] ?? 12) }}">
                <small class="text-muted">Number of months before earned points expire. Enter <code>0</code> for points that never expire.</small>
            </div>
        </div>
    </div>
</div>
@endif

@if($hasFeedbackPlan)
<!-- Customer Feedback & Reviews Card -->
<div class="card settings-section-card rounded-3 border-0">
    <div class="card-body p-4">
        <div class="settings-section-header border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="fas fa-star text-warning me-2"></i>Customer Feedback &amp; Star Reviews
                </h5>
                <p class="text-muted small mb-0">Collect verified client ratings and service reviews after completed appointments.</p>
            </div>
        </div>

        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch" id="feedback_enabled" name="feedback_enabled" value="1"
                {{ ($settings['feedback_enabled'] ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-dark ms-2" for="feedback_enabled">
                Enable Customer Feedback &amp; Rating Links
            </label>
            <div class="text-muted small ms-2">Generates unique tokenized rating links for completed appointments.</div>
        </div>

        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="feedback_auto_send_email" name="feedback_auto_send_email" value="1"
                {{ ($settings['feedback_auto_send_email'] ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-dark ms-2" for="feedback_auto_send_email">
                Include Rating Link in Completed Appointment Notifications
            </label>
            <div class="text-muted small ms-2">Automatically invites clients to rate their barber after checkout.</div>
        </div>
    </div>
</div>
@endif
