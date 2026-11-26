<?php
    $currentPreset = $preset ?? request('range', request('preset', 'last30'));
    $sDate = $startDate ?? request('start_date', now()->subDays(29)->format('Y-m-d'));
    $eDate = $endDate ?? request('end_date', now()->format('Y-m-d'));
    $formAction = $action ?? url()->current();
?>

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?php echo e($formAction); ?>" id="inventoryFilterForm" class="d-flex flex-column gap-3">
            <input type="hidden" name="range" id="filterRangePreset" value="<?php echo e($currentPreset); ?>">

            <!-- Presets Row -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    <span class="text-muted small fw-semibold me-2 d-none d-md-inline">
                        <i class="fas fa-calendar-alt me-1 text-primary"></i>Period:
                    </span>
                    <button type="button" class="btn btn-xs inv-preset-btn <?php echo e($currentPreset === 'today' ? 'active' : ''); ?>" data-range="today">Today</button>
                    <button type="button" class="btn btn-xs inv-preset-btn <?php echo e($currentPreset === 'yesterday' ? 'active' : ''); ?>" data-range="yesterday">Yesterday</button>
                    <button type="button" class="btn btn-xs inv-preset-btn <?php echo e($currentPreset === 'last7' ? 'active' : ''); ?>" data-range="last7">7 Days</button>
                    <button type="button" class="btn btn-xs inv-preset-btn <?php echo e($currentPreset === 'last30' ? 'active' : ''); ?>" data-range="last30">30 Days</button>
                    <button type="button" class="btn btn-xs inv-preset-btn <?php echo e($currentPreset === 'this_week' ? 'active' : ''); ?>" data-range="this_week">This Week</button>
                    <button type="button" class="btn btn-xs inv-preset-btn <?php echo e($currentPreset === 'this_month' ? 'active' : ''); ?>" data-range="this_month">This Month</button>
                    <button type="button" class="btn btn-xs inv-preset-btn <?php echo e($currentPreset === 'last_month' ? 'active' : ''); ?>" data-range="last_month">Last Month</button>
                    <button type="button" class="btn btn-xs inv-preset-btn <?php echo e($currentPreset === '3months' ? 'active' : ''); ?>" data-range="3months">3 Months</button>
                    <button type="button" class="btn btn-xs inv-preset-btn <?php echo e($currentPreset === '6months' ? 'active' : ''); ?>" data-range="6months">6 Months</button>
                    <button type="button" class="btn btn-xs inv-preset-btn <?php echo e($currentPreset === 'this_year' ? 'active' : ''); ?>" data-range="this_year">This Year</button>
                </div>

                <?php if(isset($exportUrl) && $exportUrl): ?>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <a href="<?php echo e($exportUrl); ?>" class="btn btn-outline-success btn-sm px-3">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Custom Date Controls & Extra Filters -->
            <div class="row g-2 align-items-end">
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
                    <input type="date" name="start_date" id="filterStartDate" class="form-control form-control-sm" value="<?php echo e($sDate); ?>">
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">To Date</label>
                    <input type="date" name="end_date" id="filterEndDate" class="form-control form-control-sm" value="<?php echo e($eDate); ?>">
                </div>

                
                <?php if(isset($extraFilters)): ?>
                    <?php echo $extraFilters; ?>

                <?php endif; ?>

                <div class="col-12 col-sm-4 col-md-2 d-flex gap-2 ms-auto">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-filter me-1"></i> Apply
                    </button>
                    <a href="<?php echo e($formAction); ?>" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.inv-preset-btn {
    font-size: 0.72rem;
    font-weight: 500;
    padding: 0.25rem 0.65rem;
    border-radius: 9999px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    transition: all 0.15s ease-in-out;
}
.inv-preset-btn:hover {
    background: #f1f5f9;
    color: #1e293b;
    border-color: #cbd5e1;
}
.inv-preset-btn.active {
    background: var(--theme-primary, #0d6efd);
    color: #ffffff;
    border-color: var(--theme-primary, #0d6efd);
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.25);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const presetBtns = document.querySelectorAll('.inv-preset-btn');
    const rangeInput = document.getElementById('filterRangePreset');
    const startInput = document.getElementById('filterStartDate');
    const endInput = document.getElementById('filterEndDate');
    const form = document.getElementById('inventoryFilterForm');

    function formatDate(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    presetBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const range = this.dataset.range;
            const now = new Date();
            let start = new Date();
            let end = new Date();

            switch (range) {
                case 'today':
                    break;
                case 'yesterday':
                    start.setDate(now.getDate() - 1);
                    end.setDate(now.getDate() - 1);
                    break;
                case 'last7':
                    start.setDate(now.getDate() - 6);
                    break;
                case 'last30':
                    start.setDate(now.getDate() - 29);
                    break;
                case 'this_week':
                    const day = now.getDay();
                    const diff = now.getDate() - day + (day === 0 ? -6 : 1);
                    start = new Date(now.setDate(diff));
                    end = new Date();
                    break;
                case 'this_month':
                    start = new Date(now.getFullYear(), now.getMonth(), 1);
                    end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                    break;
                case 'last_month':
                    start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                    end = new Date(now.getFullYear(), now.getMonth(), 0);
                    break;
                case '3months':
                    start.setMonth(now.getMonth() - 3);
                    break;
                case '6months':
                    start.setMonth(now.getMonth() - 6);
                    break;
                case 'this_year':
                    start = new Date(now.getFullYear(), 0, 1);
                    end = new Date(now.getFullYear(), 11, 31);
                    break;
            }

            if (startInput) startInput.value = formatDate(start);
            if (endInput) endInput.value = formatDate(end);
            if (rangeInput) rangeInput.value = range;

            presetBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            if (form) form.submit();
        });
    });

    [startInput, endInput].forEach(inp => {
        if (inp) {
            inp.addEventListener('change', function () {
                if (rangeInput) rangeInput.value = 'custom';
                presetBtns.forEach(b => b.classList.remove('active'));
            });
        }
    });
});
</script>
<?php /**PATH C:\Ketan\projects\barber\resources\views/inventory/reports/partials/date-filter.blade.php ENDPATH**/ ?>