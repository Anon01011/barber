<div class="row">
    <div class="col-lg-7">
        <!-- Currency & Basic Finance -->
        <div class="settings-section-card">
            <div class="settings-section-header">
                <div class="icon-box"><i class="fas fa-coins"></i></div>
                <h6>Standard Currency & Gratuity</h6>
            </div>

            <div class="settings-field-group">
                <div class="row g-4">
                    <div class="col-md-7">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-coins text-warning me-2"></i>Operational Currency
                        </label>
                        <select name="currency_code" class="form-select" id="currencyCodeSelect"
                            onchange="updateCurrencySymbol(); updatePricePreview()">
                            <option value="USD" data-symbol="$" <?php echo e(($settings['currency_code'] ?? 'USD') == 'USD' ? 'selected' : ''); ?>>USD - US Dollar</option>
                            <option value="EUR" data-symbol="€" <?php echo e(($settings['currency_code'] ?? 'USD') == 'EUR' ? 'selected' : ''); ?>>EUR - Euro</option>
                            <option value="GBP" data-symbol="£" <?php echo e(($settings['currency_code'] ?? 'USD') == 'GBP' ? 'selected' : ''); ?>>GBP - British Pound</option>
                            <option value="INR" data-symbol="₹" <?php echo e(($settings['currency_code'] ?? 'USD') == 'INR' ? 'selected' : ''); ?>>INR - Indian Rupee</option>
                            <option value="AED" data-symbol="د.إ" <?php echo e(($settings['currency_code'] ?? 'USD') == 'AED' ? 'selected' : ''); ?>>AED - UAE Dirham</option>
                            <option value="SAR" data-symbol="﷼" <?php echo e(($settings['currency_code'] ?? 'USD') == 'SAR' ? 'selected' : ''); ?>>SAR - Saudi Riyal</option>
                            <option value="QAR" data-symbol="QR" <?php echo e(($settings['currency_code'] ?? 'USD') == 'QAR' ? 'selected' : ''); ?>>QAR - Qatari Riyal</option>
                            <option value="CUSTOM" <?php echo e(!in_array($settings['currency_code'] ?? 'USD', ['USD', 'EUR', 'GBP', 'INR', 'AED', 'SAR']) ? 'selected' : ''); ?>>Other / Custom</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-dollar-sign text-success me-2"></i>Display Symbol
                        </label>
                        <input type="text" name="currency_symbol" class="form-control"
                            value="<?php echo e($settings['currency_symbol'] ?? '$'); ?>" placeholder="e.g. $"
                            id="currencySymbolInput" oninput="updatePricePreview()">
                    </div>
                    <div class="col-md-12" id="customCurrencyCodeDiv" style="display: none;">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-code text-secondary me-2"></i>Custom ISO Code
                        </label>
                        <input type="text" name="custom_currency_code" class="form-control font-monospace"
                            value="<?php echo e($settings['custom_currency_code'] ?? ''); ?>" placeholder="INR" maxlength="3">
                    </div>
                </div>

                <div class="row g-4 border-top pt-4 mt-2">
                    <div class="col-md-6">
                        <div class="form-check form-switch custom-switch pt-4">
                            <input class="form-check-input" type="checkbox" name="tip_enabled" value="1"
                                id="tip_enabled" <?php echo e(($settings['tip_enabled'] ?? true) ? 'checked' : ''); ?>

                                onchange="updatePricePreview()">
                            <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                                for="tip_enabled">Accept Gratuity</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-hand-holding-usd text-info me-2"></i>Suggested Tip (%)
                        </label>
                        <div class="input-group">
                            <input type="number" name="default_tip_percentage" class="form-control" id="tipPercentInput"
                                value="<?php echo e($settings['default_tip_percentage'] ?? 15); ?>" min="0" max="100"
                                oninput="updatePricePreview()">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tax Settings -->
        <div class="settings-section-card">
            <div class="settings-section-header">
                <div class="icon-box"><i class="fas fa-percentage"></i></div>
                <h6>Taxation Rules</h6>
            </div>

            <div class="settings-field-group">
                <div class="row g-4 align-items-center mb-4">
                    <div class="col-md-6">
                        <div class="form-check form-switch custom-switch">
                            <input class="form-check-input" type="checkbox" name="tax_enabled" value="1"
                                id="tax_enabled" <?php echo e(($settings['tax_enabled'] ?? false) ? 'checked' : ''); ?>

                                onchange="updatePricePreview()">
                            <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                                for="tax_enabled">Enable Global Tax</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch custom-switch">
                            <input class="form-check-input" type="checkbox" name="tax_enabled_pos" value="1"
                                id="tax_enabled_pos" <?php echo e(($settings['tax_enabled_pos'] ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                                for="tax_enabled_pos">Apply to POS</label>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-tag text-secondary me-2"></i>Tax Label
                        </label>
                        <input type="text" name="tax_name" class="form-control" id="taxLabelInput"
                            value="<?php echo e($settings['tax_name'] ?? 'VAT'); ?>" placeholder="VAT"
                            oninput="updatePricePreview()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-percentage text-danger me-2"></i>Rate (%)
                        </label>
                        <div class="input-group">
                            <input type="number" name="tax_rate" class="form-control" id="taxRateInput"
                                value="<?php echo e($settings['tax_rate'] ?? 0); ?>" min="0" max="100" step="0.01"
                                oninput="updatePricePreview()">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-registered text-muted me-2"></i>Registration #
                        </label>
                        <input type="text" name="tax_number" class="form-control"
                            value="<?php echo e($settings['tax_number'] ?? ''); ?>" placeholder="TRN...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Refund Policy Settings -->
        <div class="settings-section-card">
            <div class="settings-section-header">
                <div class="icon-box"><i class="fas fa-undo"></i></div>
                <h6>Refund Policy</h6>
            </div>

            <div class="settings-field-group">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-hand-holding-usd text-danger me-2"></i>Refund Fee
                        </label>
                        <input type="number" name="refund_fee" class="form-control"
                            value="<?php echo e($settings['refund_fee'] ?? 0); ?>" min="0" step="0.01" placeholder="0.00">
                        <p class="text-muted small mt-1">Fee deducted from refund amount.</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase text-secondary">
                            <i class="fas fa-calculator text-primary me-2"></i>Fee Type
                        </label>
                        <select name="refund_fee_type" class="form-select">
                            <option value="fixed" <?php echo e(($settings['refund_fee_type'] ?? 'fixed') == 'fixed' ? 'selected' : ''); ?>>Fixed Amount</option>
                            <option value="percentage" <?php echo e(($settings['refund_fee_type'] ?? 'fixed') == 'percentage' ? 'selected' : ''); ?>>Percentage (%)</option>
                        </select>
                        <p class="text-muted small mt-1">How the fee is calculated.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <!-- Live Preview -->
        <div class="settings-section-card sticky-top" style="top: 1.5rem; z-index: 10;">
            <div class="settings-section-header">
                <div class="icon-box"><i class="fas fa-eye"></i></div>
                <h6>Pricing Preview</h6>
            </div>

            <div class="pricing-preview-card">
                <div class="pricing-preview-chip">SAMPLE SERVICE TICKET</div>
                <div class="pricing-preview-amount" id="previewTotalPrice">--</div>
                <div class="pricing-preview-label">Total for a <span id="previewCurrencyCode">USD</span> transaction
                </div>

                <div class="pricing-preview-breakdown">
                    <div class="pricing-preview-item">
                        <span>Base Service Cost</span>
                        <span id="previewBasePrice">--</span>
                    </div>
                    <div class="pricing-preview-item" id="previewTaxRow" style="display: none;">
                        <span id="previewTaxLabel">Tax (0%)</span>
                        <span id="previewTaxAmount">--</span>
                    </div>
                    <div class="pricing-preview-item" id="previewTipRow" style="display: none;">
                        <span>Suggested Tip (0%)</span>
                        <span id="previewTipAmount">--</span>
                    </div>
                    <div class="pricing-preview-total">
                        <span>Grand Total</span>
                        <span id="previewGrandTotal">--</span>
                    </div>
                </div>
            </div>

            <div class="settings-field-group mt-4 bg-light border-0">
                <p class="text-muted small mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    This preview uses a sample base price of <strong>100.00</strong> to demonstrate how fees and symbols
                    are displayed to your customers.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Accepted Methods -->
<div class="settings-section-card">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-wallet"></i></div>
        <h6>Payment Infrastructure</h6>
    </div>

    <div class="settings-field-group">
        <div class="row g-4">
            <?php $__currentLoopData = ['cash' => ['icon' => 'money-bill-wave', 'color' => 'success'], 'card' => ['icon' => 'credit-card', 'color' => 'primary'], 'online' => ['icon' => 'globe', 'color' => 'info']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4">
                    <div
                        class="settings-field-group h-100 d-flex flex-column align-items-center text-center p-3 border-dashed">
                        <i class="fas fa-<?php echo e($meta['icon']); ?> fa-2x mb-3 text-<?php echo e($meta['color']); ?> opacity-75"></i>
                        <div class="form-check form-switch custom-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="accept_<?php echo e($method); ?>" value="1"
                                id="accept_<?php echo e($method); ?>" <?php echo e(($settings['accept_' . $method] ?? true) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-bold"
                                for="accept_<?php echo e($method); ?>"><?php echo e(ucfirst($method)); ?></label>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<!-- Receipt Config -->
<div class="settings-section-card">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-receipt"></i></div>
        <h6>Terminal Receipt Policy</h6>
    </div>

    <div class="settings-field-group">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="form-check form-switch custom-switch">
                    <input class="form-check-input" type="checkbox" name="pos_receipt_arabic" value="1"
                        id="pos_receipt_arabic" <?php echo e(($settings['pos_receipt_arabic'] ?? true) ? 'checked' : ''); ?>>
                    <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                        for="pos_receipt_arabic">Bilingual Invoicing</label>
                </div>
                <p class="text-muted small mt-1">Automatic EN/AR translation for line items.</p>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch custom-switch">
                    <input class="form-check-input" type="checkbox" name="pos_receipt_arabic_button" value="1"
                        id="pos_receipt_arabic_button" <?php echo e(($settings['pos_receipt_arabic_button'] ?? true) ? 'checked' : ''); ?>>
                    <label class="form-check-label fw-semibold small text-uppercase text-secondary"
                        for="pos_receipt_arabic_button">Guest Choice Toggle</label>
                </div>
                <p class="text-muted small mt-1">Manual language switch at checkout.</p>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <script>
        function updateCurrencySymbol() {
            const selector = document.getElementById('currencyCodeSelect');
            const symbolInput = document.getElementById('currencySymbolInput');

            if (selector.value !== 'CUSTOM') {
                const selectedOption = selector.options[selector.selectedIndex];
                const symbol = selectedOption.dataset.symbol;
                if (symbol) {
                    symbolInput.value = symbol;
                }
            }
        }

        function updatePricePreview() {
            const base = 100.00;
            const selector = document.getElementById('currencyCodeSelect');
            // Always use the input value for the preview to ensure What You See Is What You Save
            const symbol = document.getElementById('currencySymbolInput').value;
            const code = selector.value === 'CUSTOM' ? document.querySelector('input[name="custom_currency_code"]').value || '???' : selector.value;

            const taxEnabled = document.getElementById('tax_enabled').checked;
            const taxRate = parseFloat(document.getElementById('taxRateInput').value) || 0;
            const taxLabel = document.getElementById('taxLabelInput').value || 'Tax';

            const tipEnabled = document.getElementById('tip_enabled').checked;
            const tipPercent = parseFloat(document.getElementById('tipPercentInput').value) || 0;

            const taxVal = taxEnabled ? (base * taxRate / 100) : 0;
            const tipVal = tipEnabled ? (base * tipPercent / 100) : 0;
            const total = base + taxVal + (tipEnabled ? 0 : 0);

            // Update DOM
            document.getElementById('previewCurrencyCode').textContent = code;
            document.getElementById('previewBasePrice').textContent = symbol + ' ' + base.toFixed(2);

            if (taxEnabled) {
                document.getElementById('previewTaxRow').style.display = 'flex';
                document.getElementById('previewTaxLabel').textContent = taxLabel + ' (' + taxRate + '%)';
                document.getElementById('previewTaxAmount').textContent = symbol + ' ' + taxVal.toFixed(2);
            } else {
                document.getElementById('previewTaxRow').style.display = 'none';
            }

            if (tipEnabled) {
                document.getElementById('previewTipRow').style.display = 'flex';
                document.querySelector('#previewTipRow span:first-child').textContent = 'Suggested Tip (' + tipPercent + '%)';
                document.getElementById('previewTipAmount').textContent = symbol + ' ' + tipVal.toFixed(2);
            } else {
                document.getElementById('previewTipRow').style.display = 'none';
            }

            document.getElementById('previewTotalPrice').textContent = symbol + ' ' + (base + taxVal).toFixed(2);
            document.getElementById('previewGrandTotal').textContent = symbol + ' ' + (base + taxVal).toFixed(2);

            // Handle custom div visibility
            document.getElementById('customCurrencyCodeDiv').style.display = selector.value === 'CUSTOM' ? 'block' : 'none';
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', updatePricePreview);
    </script>
<?php $__env->stopPush(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\settings\partials\payments.blade.php ENDPATH**/ ?>