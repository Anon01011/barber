@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-percent text-primary me-2"></i>Edit Commission Profile
            </h1>
            <p class="text-muted mb-0">Modify commission structures by target slab or individual items</p>
        </div>
        <a href="{{ route('admin.commissions.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.commissions.update', $profile) }}" method="POST" id="commissionForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Profile Information Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Profile Information</h6>
                    </div>
                    <div class="card-body">
                        <!-- Type selector (Radios matching target screenshot design) -->
                        <div class="mb-4">
                            <label class="form-label d-block font-weight-bold">Profile Type <span class="text-danger">*</span></label>
                            <div class="form-check form-check-inline me-4">
                                <input class="form-check-input type-radio" type="radio" name="type" id="type_target" value="by_target" {{ old('type', $profile->type) == 'by_target' ? 'checked' : '' }}>
                                <label class="form-check-label font-weight-bold" for="type_target">
                                    Commission by target <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Commission is calculated based on employee's total sales during the calculation interval."></i>
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input type-radio" type="radio" name="type" id="type_item" value="by_item" {{ old('type', $profile->type) == 'by_item' ? 'checked' : '' }}>
                                <label class="form-check-label font-weight-bold" for="type_item">
                                    Commission by item <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Commission is calculated per item sold."></i>
                                </label>
                            </div>
                        </div>

                        <!-- Profile fields row -->
                        <div class="row align-items-end">
                            <div class="col-md-4 mb-3">
                                <label for="name" class="form-label font-weight-bold">Profile Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $profile->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3" id="calculationIntervalCol">
                                <label for="calculation_interval" class="form-label font-weight-bold">Calculation Interval <span class="text-danger">*</span></label>
                                <select class="form-select @error('calculation_interval') is-invalid @enderror" 
                                        id="calculation_interval" name="calculation_interval">
                                    <option value="">Select Interval</option>
                                    <option value="daily" {{ old('calculation_interval', $profile->calculation_interval) == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ old('calculation_interval', $profile->calculation_interval) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('calculation_interval', $profile->calculation_interval) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                                @error('calculation_interval')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3" id="qualifyingItemCol">
                                <label for="qualifying_item" class="form-label font-weight-bold">Qualifying Item <span class="text-danger">*</span></label>
                                <select class="form-select @error('qualifying_item') is-invalid @enderror" 
                                        id="qualifying_item" name="qualifying_item">
                                    <option value="">Select Items</option>
                                    <option value="all" {{ old('qualifying_item', $profile->qualifying_item) == 'all' ? 'selected' : '' }}>All Items</option>
                                    <option value="service" {{ old('qualifying_item', $profile->qualifying_item) == 'service' ? 'selected' : '' }}>Services Only</option>
                                    <option value="product" {{ old('qualifying_item', $profile->qualifying_item) == 'product' ? 'selected' : '' }}>Products Only</option>
                                    <option value="membership" {{ old('qualifying_item', $profile->qualifying_item) == 'membership' ? 'selected' : '' }}>Memberships Only</option>
                                    <option value="package" {{ old('qualifying_item', $profile->qualifying_item) == 'package' ? 'selected' : '' }}>Packages Only</option>
                                </select>
                                @error('qualifying_item')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Options Checkboxes -->
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="include_tax" 
                                           id="include_tax" value="1" {{ old('include_tax', $profile->include_tax) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold text-dark" for="include_tax">
                                        Include tax amount in commission calculation
                                    </label>
                                </div>
                                <div class="form-check mb-2" id="cascadeCol">
                                    <input class="form-check-input" type="checkbox" name="is_cascade" 
                                           id="is_cascade" value="1" {{ old('is_cascade', $profile->is_cascade) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold text-dark" for="is_cascade">
                                        Cascading Commission
                                        <i class="fas fa-info-circle text-info ms-1" id="cascadeTooltipBtn" style="cursor: pointer;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                                           title="Cascading Commission Explanation"
                                           data-bs-content="Enabling cascade will use the commission percent of highest slab met by an employee to calculate commissions to all slabs.<br>Not enabling cascade will apply the commission percent of corresponding slab to the slabs met by the employee.<br><br><strong>Example (Target met: {{ currency_symbol() }}65,000):</strong><br>• 0 - 40,000 (0%)<br>• 40,000 - 60,000 (10%)<br>• 60,000 - 80,000 (20%)<br>• 80,000 - 100,000 (30%)<br><br><strong>Cascading:</strong> 20% x 65,000 = 13,000<br><strong>No Cascading:</strong> 0% x 40,000 + 10% x 20,000 + 20% x 5,000 = 3,000"></i>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" 
                                           id="is_active" value="1" {{ old('is_active', $profile->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold text-dark" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Rules Card (by_item) -->
                <div class="card shadow mb-4" id="itemRulesCard">
                    <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">Commission Rules</h6>
                        <button type="button" class="btn btn-sm btn-light" id="addRuleBtn">
                            <i class="fas fa-plus"></i> Add Rule
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="rulesContainer">
                            <!-- Rules will be added here dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Target Tiers Card (by_target) -->
                <div class="card shadow mb-4" id="targetTiersCard">
                    <div class="card-header py-3 bg-info text-white d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">Target Tiers</h6>
                        <button type="button" class="btn btn-sm btn-light" id="addTargetRuleBtn">
                            <i class="fas fa-plus"></i> Add Target Tier
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="targetRulesContainer">
                            <!-- Target rules will be added here dynamically -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Profile Stats -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-info text-white">
                        <h6 class="m-0 font-weight-bold">Profile Stats</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Assigned Staff:</strong> {{ $profile->staff()->count() }}</p>
                        <p class="mb-2"><strong>Created:</strong> {{ format_date($profile->created_at) }}</p>
                        <p class="mb-0"><strong>Last Updated:</strong> {{ format_date($profile->updated_at) }}</p>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                        <a href="{{ route('admin.commissions.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Item Rule Template -->
<template id="ruleTemplate">
    <div class="rule-item card mb-3 border-left-primary">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 font-weight-bold">Rule #<span class="rule-number"></span></h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-rule">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Item Type <span class="text-danger">*</span></label>
                    <select class="form-select item-type" name="rules[INDEX][item_type]" required>
                        <option value="">Select Type</option>
                        <option value="service">Service</option>
                        <option value="product">Product</option>
                        <option value="membership">Membership</option>
                        <option value="package">Package</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Specific Item (Optional)</label>
                    <select class="form-select item-id" name="rules[INDEX][item_id]">
                        <option value="">All Items</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label font-weight-bold">Commission Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="rules[INDEX][commission_type]" required>
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label font-weight-bold">Commission Value <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="rules[INDEX][commission_value]" 
                           step="0.01" min="0" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label font-weight-bold">Target Amount (Optional)</label>
                    <input type="number" class="form-control" name="rules[INDEX][target_amount]" 
                           step="0.01" min="0">
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Target Rule Template (Tiers) -->
<template id="targetRuleTemplate">
    <div class="target-rule-item card mb-3 border-left-info">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 font-weight-bold text-info">Slab #<span class="slab-number"></span></h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-target-rule">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>

            <div class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label class="form-label font-weight-bold">From ({{ currency_symbol() }}) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control target-from" name="rules[INDEX][target_from]" step="0.01" min="0" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label font-weight-bold">To ({{ currency_symbol() }})</label>
                    <input type="number" class="form-control target-to" name="rules[INDEX][target_to]" step="0.01" min="0">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label font-weight-bold">Calculation <span class="text-danger">*</span></label>
                    <select class="form-select commission-type-select" name="rules[INDEX][commission_type]" required>
                        <option value="percentage">By Percent (%)</option>
                        <option value="fixed">By Fixed Value</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label font-weight-bold">Value <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control commission-value" name="rules[INDEX][commission_value]" step="0.01" min="0" required>
                        <span class="input-group-text value-unit-addon">%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
let ruleIndex = 0;
const items = {
    service: @json($services),
    product: @json($products),
    membership: @json($memberships),
    package: @json($packages)
};
const existingRules = @json($profile->rules);
const profileType = '{{ $profile->type }}';

$(document).ready(function() {
    // Initialize tooltips and popovers
    $('[data-bs-toggle="tooltip"]').tooltip();
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });

    // Load existing rules or add default
    if (profileType === 'by_target') {
        if (existingRules && existingRules.length > 0) {
            existingRules.forEach(rule => {
                addTargetRule(rule);
            });
        } else {
            addTargetRule();
        }
        addRule(); // for switching
    } else {
        if (existingRules && existingRules.length > 0) {
            existingRules.forEach(rule => {
                addRule(rule);
            });
        } else {
            addRule();
        }
        addTargetRule(); // for switching
    }

    // Set initial display
    const typeValue = $('input[name="type"]:checked').val() || 'by_item';
    toggleCommissionType(typeValue);

    // Type radio selection change
    $('.type-radio').change(function() {
        toggleCommissionType($(this).val());
    });

    // Add rules buttons
    $('#addRuleBtn').click(function() {
        addRule();
    });

    $('#addTargetRuleBtn').click(function() {
        addTargetRule();
    });

    // Remove rules
    $(document).on('click', '.remove-rule', function() {
        if ($('.rule-item').length > 1) {
            $(this).closest('.rule-item').remove();
            updateRuleNumbers();
        } else {
            alert('At least one rule is required.');
        }
    });

    $(document).on('click', '.remove-target-rule', function() {
        if ($('.target-rule-item').length > 1) {
            $(this).closest('.target-rule-item').remove();
            updateTargetRuleNumbers();
        } else {
            alert('At least one slab is required.');
        }
    });

    // Item type change
    $(document).on('change', '.item-type', function() {
        const itemType = $(this).val();
        const itemSelect = $(this).closest('.rule-item').find('.item-id');
        const currentValue = itemSelect.data('current-value');
        itemSelect.html('<option value="">All Items</option>');
        
        if (itemType && items[itemType]) {
            items[itemType].forEach(item => {
                const selected = currentValue == item.id ? 'selected' : '';
                itemSelect.append(`<option value="${item.id}" ${selected}>${item.name}</option>`);
            });
        }
    });

    // Commission type select inside target rules change
    $(document).on('change', '.commission-type-select', function() {
        const type = $(this).val();
        const addon = $(this).closest('.target-rule-item').find('.value-unit-addon');
        if (type === 'percentage') {
            addon.text('%');
        } else {
            addon.text('{{ currency_symbol() }}');
        }
    });
});

function toggleCommissionType(type) {
    if (type === 'by_target') {
        $('#calculationIntervalCol, #qualifyingItemCol, #cascadeCol, #targetTiersCard').show();
        $('#itemRulesCard').hide();
        
        $('#targetTiersCard').find('input, select').prop('disabled', false);
        $('#itemRulesCard').find('input, select').prop('disabled', true);
        
        $('#calculation_interval, #qualifying_item').prop('required', true);
    } else {
        $('#calculationIntervalCol, #qualifyingItemCol, #cascadeCol, #targetTiersCard').hide();
        $('#itemRulesCard').show();
        
        $('#itemRulesCard').find('input, select').prop('disabled', false);
        $('#targetTiersCard').find('input, select').prop('disabled', true);
        
        $('#calculation_interval, #qualifying_item').prop('required', false);
    }
}

function addRule(existingRule = null) {
    const template = document.getElementById('ruleTemplate');
    const clone = template.content.cloneNode(true);
    const ruleHtml = clone.querySelector('.rule-item').outerHTML.replace(/INDEX/g, ruleIndex);
    
    $('#rulesContainer').append(ruleHtml);
    
    const ruleElement = $('.rule-item').last();
    
    if (existingRule) {
        ruleElement.find('.item-type').val(existingRule.item_type).trigger('change');
        ruleElement.find('.item-id').data('current-value', existingRule.item_id);
        ruleElement.find('[name*="commission_type"]').val(existingRule.commission_type);
        ruleElement.find('[name*="commission_value"]').val(existingRule.commission_value);
        ruleElement.find('[name*="target_amount"]').val(existingRule.target_amount || '');
        
        setTimeout(() => {
            ruleElement.find('.item-type').trigger('change');
        }, 100);
    }
    
    ruleIndex++;
    updateRuleNumbers();
}

function addTargetRule(existingRule = null) {
    const template = document.getElementById('targetRuleTemplate');
    const clone = template.content.cloneNode(true);
    const ruleHtml = clone.querySelector('.target-rule-item').outerHTML.replace(/INDEX/g, ruleIndex);
    
    $('#targetRulesContainer').append(ruleHtml);
    
    const ruleElement = $('.target-rule-item').last();
    
    if (existingRule) {
        ruleElement.find('.target-from').val(existingRule.target_from);
        ruleElement.find('.target-to').val(existingRule.target_to || '');
        ruleElement.find('.commission-type-select').val(existingRule.commission_type).trigger('change');
        ruleElement.find('.commission-value').val(existingRule.commission_value);
    }
    
    ruleIndex++;
    updateTargetRuleNumbers();
}

function updateRuleNumbers() {
    $('.rule-item').each(function(index) {
        $(this).find('.rule-number').text(index + 1);
    });
}

function updateTargetRuleNumbers() {
    $('.target-rule-item').each(function(index) {
        $(this).find('.slab-number').text(index + 1);
    });
}
</script>
@endpush
@endsection
