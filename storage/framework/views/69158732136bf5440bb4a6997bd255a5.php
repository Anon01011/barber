<div class="settings-section-card p-4 rounded-3 bg-white shadow-sm mb-4">
    <div class="settings-section-header border-bottom pb-3 mb-4">
        <h5 class="fw-bold text-dark mb-1">
            <i class="fas fa-brain text-warning me-2"></i>Smart AI & Machine Learning Settings
        </h5>
        <p class="text-muted small mb-0">Configure automated machine learning recommendations, customer churn prevention, visual consultation engine, and WhatsApp assistant.</p>
    </div>

    <div class="row g-4">
        <!-- Enable AI Insights -->
        <div class="col-md-6">
            <div class="form-check form-switch card p-3 border-0 bg-light">
                <input class="form-check-input ms-0 me-3" type="checkbox" id="enable_ai_insights" name="enable_ai_insights" value="1" <?php echo e(old('enable_ai_insights', $settings['enable_ai_insights'] ?? true) ? 'checked' : ''); ?>>
                <label class="form-check-label fw-bold text-dark ms-2" for="enable_ai_insights">
                    Enable AI Predictive Reports
                </label>
                <small class="text-muted d-block ms-2 mt-1">Generates 30-day demand forecasts, staff yield metrics, and executive summaries.</small>
            </div>
        </div>

        <!-- Visual Consultation Studio -->
        <div class="col-md-6">
            <div class="form-check form-switch card p-3 border-0 bg-light">
                <input class="form-check-input ms-0 me-3" type="checkbox" id="enable_ai_visual_consultation" name="enable_ai_visual_consultation" value="1" <?php echo e(old('enable_ai_visual_consultation', $settings['enable_ai_visual_consultation'] ?? true) ? 'checked' : ''); ?>>
                <label class="form-check-label fw-bold text-dark ms-2" for="enable_ai_visual_consultation">
                    AI Visual Consultation Studio
                </label>
                <small class="text-muted d-block ms-2 mt-1">Enables face shape analysis, haircut, hair color, and skin beauty recommendation consultations.</small>
            </div>
        </div>

        <!-- No-Show Prevention -->
        <div class="col-md-6">
            <div class="form-check form-switch card p-3 border-0 bg-light">
                <input class="form-check-input ms-0 me-3" type="checkbox" id="enable_ai_noshow_prevention" name="enable_ai_noshow_prevention" value="1" <?php echo e(old('enable_ai_noshow_prevention', $settings['enable_ai_noshow_prevention'] ?? true) ? 'checked' : ''); ?>>
                <label class="form-check-label fw-bold text-dark ms-2" for="enable_ai_noshow_prevention">
                    AI No-Show Risk Detection & Verifications
                </label>
                <small class="text-muted d-block ms-2 mt-1">Evaluates booking lead times and triggers automated WhatsApp verification requests for high-risk appointments.</small>
            </div>
        </div>

        <!-- Multi-Channel Marketing Generator -->
        <div class="col-md-6">
            <div class="form-check form-switch card p-3 border-0 bg-light">
                <input class="form-check-input ms-0 me-3" type="checkbox" id="enable_ai_marketing_generator" name="enable_ai_marketing_generator" value="1" <?php echo e(old('enable_ai_marketing_generator', $settings['enable_ai_marketing_generator'] ?? true) ? 'checked' : ''); ?>>
                <label class="form-check-label fw-bold text-dark ms-2" for="enable_ai_marketing_generator">
                    AI Marketing & Copywriting Engine
                </label>
                <small class="text-muted d-block ms-2 mt-1">Generates automated WhatsApp, SMS, Email, and Instagram promotional copy for targeted customer segments.</small>
            </div>
        </div>

        <!-- WhatsApp 24/7 Booking Assistant -->
        <div class="col-md-6">
            <div class="form-check form-switch card p-3 border-0 bg-light">
                <input class="form-check-input ms-0 me-3" type="checkbox" id="enable_ai_whatsapp_assistant" name="enable_ai_whatsapp_assistant" value="1" <?php echo e(old('enable_ai_whatsapp_assistant', $settings['enable_ai_whatsapp_assistant'] ?? true) ? 'checked' : ''); ?>>
                <label class="form-check-label fw-bold text-dark ms-2" for="enable_ai_whatsapp_assistant">
                    24/7 AI WhatsApp FAQ & Booking Bot
                </label>
                <small class="text-muted d-block ms-2 mt-1">Automatically answers customer inquiries about hours, service prices, stylist availability, and policies.</small>
            </div>
        </div>

        <!-- Churn Auto Marketing -->
        <div class="col-md-6">
            <div class="form-check form-switch card p-3 border-0 bg-light">
                <input class="form-check-input ms-0 me-3" type="checkbox" id="enable_ai_churn_auto_marketing" name="enable_ai_churn_auto_marketing" value="1" <?php echo e(old('enable_ai_churn_auto_marketing', $settings['enable_ai_churn_auto_marketing'] ?? true) ? 'checked' : ''); ?>>
                <label class="form-check-label fw-bold text-dark ms-2" for="enable_ai_churn_auto_marketing">
                    Automate Churn Prevention Offers
                </label>
                <small class="text-muted d-block ms-2 mt-1">Automatically send discount vouchers to high-risk churn customers idling over 45 days.</small>
            </div>
        </div>

        <!-- Churn Discount Percentage -->
        <div class="col-md-6">
            <label class="form-label fw-semibold text-dark">Churn Voucher Discount Percentage (%)</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-percent"></i></span>
                <input type="number" class="form-control" name="ai_churn_discount_percentage" value="<?php echo e(old('ai_churn_discount_percentage', $settings['ai_churn_discount_percentage'] ?? 15)); ?>" min="5" max="50">
            </div>
            <small class="text-muted">Discount rate applied for automated re-engagement vouchers.</small>
        </div>

        <!-- Inventory Stock Alerts -->
        <div class="col-md-6">
            <div class="form-check form-switch card p-3 border-0 bg-light">
                <input class="form-check-input ms-0 me-3" type="checkbox" id="enable_ai_inventory_alerts" name="enable_ai_inventory_alerts" value="1" <?php echo e(old('enable_ai_inventory_alerts', $settings['enable_ai_inventory_alerts'] ?? true) ? 'checked' : ''); ?>>
                <label class="form-check-label fw-bold text-dark ms-2" for="enable_ai_inventory_alerts">
                    Predictive Stock-Out Alerts
                </label>
                <small class="text-muted d-block ms-2 mt-1">Alerts salon admin when inventory items are predicted to deplete within 14 days.</small>
            </div>
        </div>

        <!-- Dynamic Yield Pricing -->
        <div class="col-md-6">
            <div class="form-check form-switch card p-3 border-0 bg-light">
                <input class="form-check-input ms-0 me-3" type="checkbox" id="enable_ai_dynamic_yield_pricing" name="enable_ai_dynamic_yield_pricing" value="1" <?php echo e(old('enable_ai_dynamic_yield_pricing', $settings['enable_ai_dynamic_yield_pricing'] ?? true) ? 'checked' : ''); ?>>
                <label class="form-check-label fw-bold text-dark ms-2" for="enable_ai_dynamic_yield_pricing">
                    Dynamic Off-Peak Yield Recommendations
                </label>
                <small class="text-muted d-block ms-2 mt-1">Analyzes booking peak hours and suggests morning/off-peak promotional discounts to maximize appointment throughput.</small>
            </div>
        </div>

        <!-- Floating AI Copilot Widget Toggle -->
        <div class="col-md-6">
            <div class="form-check form-switch card p-3 border-0 bg-light">
                <input class="form-check-input ms-0 me-3" type="checkbox" id="enable_ai_floating_widget" name="enable_ai_floating_widget" value="1" <?php echo e(old('enable_ai_floating_widget', $settings['enable_ai_floating_widget'] ?? true) ? 'checked' : ''); ?>>
                <label class="form-check-label fw-bold text-dark ms-2" for="enable_ai_floating_widget">
                    Floating AI Copilot Chat Button
                </label>
                <small class="text-muted d-block ms-2 mt-1">Displays a floating AI assistant button at the bottom-right of all admin pages for instant access to salon queries.</small>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\settings\partials\ai.blade.php ENDPATH**/ ?>