<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4">
        <?php if($showNav): ?>
            <!-- Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 mb-1 fw-bold text-gray-800">My Subscription</h1>
                    <p class="text-muted mb-0">Manage your plan and view subscription details</p>
                </div>
                <a href="<?php echo e(route('admin.saas.subscription.history', ['salon_slug' => request()->route('salon_slug')])); ?>"
                    class="btn btn-outline-primary">
                    <i class="fas fa-history me-2"></i>View History
                </a>
            </div>
        <?php endif; ?>

        <!-- Pending Payment Alert -->
        <?php if(isset($pendingPayment) && $pendingPayment): ?>
            <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center" role="alert">
                <i class="fas fa-clock fa-2x me-3"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Payment Pending Approval</h5>
                    <p class="mb-0">
                        You have a pending payment request of
                        <strong><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($pendingPayment->amount, 2)); ?></strong>
                        via <?php echo e(ucfirst($pendingPayment->payment_method)); ?>.
                        Please wait for admin approval.
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Current Plan Status -->
        <?php if($subscription): ?>
            <div class="card border-0 shadow-sm mb-5 bg-primary text-white overflow-hidden position-relative">
                <div class="card-body p-4 position-relative" style="z-index: 1;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-white text-primary me-3">Current Plan</span>
                                <h2 class="mb-0 fw-bold"><?php echo e($subscription->plan->name); ?></h2>
                            </div>
                            <?php
                                $daysRemaining = $subscription->daysUntilExpiration();
                                // If no subscription expiration, check trial period
                                if ($daysRemaining === null && $salon->trial_ends_at) {
                                    $daysRemaining = (int) now()->diffInDays($salon->trial_ends_at, false);
                                    $isTrial = true;
                                } else {
                                    $daysRemaining = $daysRemaining !== null ? (int) $daysRemaining : null;
                                    $isTrial = false;
                                }
                            ?>
                            <p class="mb-0 opacity-75">
                                <?php if($daysRemaining !== null): ?>
                                    <?php if($daysRemaining > 0): ?>
                                        Your subscription is <strong>Active</strong> and <?php echo e($isTrial ? 'trial' : ''); ?> expires in
                                        <strong class="badge bg-white text-primary"><?php echo e($daysRemaining); ?>

                                            <?php echo e(Str::plural('day', $daysRemaining)); ?></strong>
                                        <?php if($subscription->ends_at): ?>
                                            on <?php echo e($subscription->ends_at->format('M d, Y')); ?>

                                        <?php elseif($isTrial && $salon->trial_ends_at): ?>
                                            on <?php echo e($salon->trial_ends_at->format('M d, Y')); ?>

                                        <?php endif; ?>
                                    <?php elseif($daysRemaining == 0): ?>
                                        Your <?php echo e($isTrial ? 'trial' : 'subscription'); ?> <strong class="badge bg-danger">expires
                                            today</strong>
                                        <?php if($subscription->ends_at): ?>
                                            on <?php echo e($subscription->ends_at->format('M d, Y')); ?>

                                        <?php elseif($isTrial && $salon->trial_ends_at): ?>
                                            on <?php echo e($salon->trial_ends_at->format('M d, Y')); ?>

                                        <?php endif; ?>
                                    <?php else: ?>
                                        Your subscription has <strong class="badge bg-danger">Expired</strong>
                                        <?php if($subscription->ends_at): ?>
                                            on <?php echo e($subscription->ends_at->format('M d, Y')); ?>

                                        <?php elseif($isTrial && $salon->trial_ends_at): ?>
                                            on <?php echo e($salon->trial_ends_at->format('M d, Y')); ?>

                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light text-primary fw-bold"
                                    onclick="document.getElementById('available-plans').scrollIntoView({behavior: 'smooth'})"
                                    <?php if(isset($pendingPayment) && $pendingPayment): ?> disabled <?php endif; ?>>
                                    <i class="fas fa-arrow-up me-2"></i>Upgrade Plan
                                </button>
                                <button type="button" class="btn btn-outline-light fw-bold" data-bs-toggle="modal"
                                    data-bs-target="#renewModal" <?php if(isset($pendingPayment) && $pendingPayment): ?> disabled <?php endif; ?>>
                                    <i class="fas fa-sync-alt me-2"></i>Renew Plan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="position-absolute top-0 end-0 translate-middle rounded-circle bg-white opacity-10"
                    style="width: 300px; height: 300px; margin-top: -50px; margin-right: -50px;"></div>
            </div>
        <?php elseif($salon->trial_ends_at): ?>
            <!-- Trial Period Status -->
            <div class="card border-0 shadow-sm mb-5 bg-info text-white overflow-hidden position-relative">
                <div class="card-body p-4 position-relative" style="z-index: 1;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-white text-info me-3">Trial Period</span>
                                <h2 class="mb-0 fw-bold">Free Trial</h2>
                            </div>
                            <?php
                                $trialDays = now()->diffInDays($salon->trial_ends_at, false);
                            ?>
                            <p class="mb-0 opacity-75">
                                Your trial period
                                <?php if($trialDays > 0): ?>
                                    expires in <strong class="badge bg-white text-info"><?php echo e($trialDays); ?>

                                        <?php echo e(Str::plural('day', $trialDays)); ?></strong> on
                                    <?php echo e($salon->trial_ends_at->format('M d, Y')); ?>

                                <?php else: ?>
                                    has <strong class="badge bg-danger">Expired</strong>
                                <?php endif; ?>.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light text-info fw-bold"
                                    onclick="document.getElementById('available-plans').scrollIntoView({behavior: 'smooth'})"
                                    <?php if(isset($pendingPayment) && $pendingPayment): ?> disabled <?php endif; ?>>
                                    <i class="fas fa-arrow-up me-2"></i>Upgrade Plan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="position-absolute top-0 end-0 translate-middle rounded-circle bg-white opacity-10"
                    style="width: 300px; height: 300px; margin-top: -50px; margin-right: -50px;"></div>
            </div>
        <?php else: ?>
            <!-- No Subscription Status -->
            <div class="card border-0 shadow-sm mb-5 bg-secondary text-white overflow-hidden position-relative">
                <div class="card-body p-4 position-relative" style="z-index: 1;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-white text-secondary me-3">No Plan</span>
                                <h2 class="mb-0 fw-bold">No Active Subscription</h2>
                            </div>
                            <p class="mb-0 opacity-75">
                                You do not have an active subscription plan. Please select a plan below to get started.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light text-secondary fw-bold"
                                    onclick="document.getElementById('available-plans').scrollIntoView({behavior: 'smooth'})"
                                    <?php if(isset($pendingPayment) && $pendingPayment): ?> disabled <?php endif; ?>>
                                    <i class="fas fa-arrow-up me-2"></i>Subscribe Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="position-absolute top-0 end-0 translate-middle rounded-circle bg-white opacity-10"
                    style="width: 300px; height: 300px; margin-top: -50px; margin-right: -50px;"></div>
            </div>
        <?php endif; ?>

        <!-- Available Plans -->
        <h4 id="available-plans" class="mb-4 fw-bold text-gray-800">Available Plans</h4>
        <div class="row g-4 justify-content-center">
            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm pricing-card position-relative">
                        <?php if($plan->is_popular): ?>
                            <div class="popular-badge">
                                <i class="fas fa-star me-1"></i> POPULAR
                            </div>
                        <?php endif; ?>

                        <!-- Card Header -->
                        <div class="card-header bg-light border-0 pt-4 pb-0 text-center">
                            <h5 class="card-title fw-bold mb-1"><?php echo e($plan->name); ?></h5>
                            <p class="text-muted small mb-3"><?php echo e(Str::limit($plan->description ?? '', 60)); ?></p>
                            <div class="pricing-header mb-3">
                                <h2 class="display-5 fw-bold text-primary mb-0">
                                    <?php echo e(system_currency_symbol()); ?><?php echo e(number_format($plan->price, 0)); ?>

                                </h2>
                                <span class="text-muted small">/ <?php echo e($plan->duration_in_days); ?> days</span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body bg-white d-flex flex-column">
                            <h6 class="font-weight-bold x-small text-uppercase text-muted mb-2">Features</h6>
                            <ul class="list-unstyled mb-3">
                                <?php
                                    $features = $plan->features;
                                    if (is_string($features)) {
                                        $features = json_decode($features, true) ?? [];
                                    }
                                    $features = is_array($features) ? $features : [];
                                    $visibleFeatures = array_slice($features, 0, 7);
                                    $hiddenFeatures = array_slice($features, 7);
                                ?>

                                <?php if(count($features) > 0): ?>
                                    <?php $__currentLoopData = $visibleFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="mb-1 d-flex align-items-start small">
                                            <i class="fas fa-check-circle text-white me-2 mt-1" style="font-size: 0.8rem;"></i>
                                            <span style="font-size: 0.9rem;"><?php echo e($feature); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <?php if(count($hiddenFeatures) > 0): ?>
                                        <div class="collapse" id="featuresCollapse<?php echo e($plan->id); ?>">
                                            <?php $__currentLoopData = $hiddenFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="mb-1 d-flex align-items-start small">
                                                    <i class="fas fa-check-circle text-white me-2 mt-1" style="font-size: 0.8rem;"></i>
                                                    <span style="font-size: 0.9rem;"><?php echo e($feature); ?></span>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <a class="text-primary text-decoration-none small collapse-toggle" data-bs-toggle="collapse"
                                            href="#featuresCollapse<?php echo e($plan->id); ?>" role="button" aria-expanded="false"
                                            aria-controls="featuresCollapse<?php echo e($plan->id); ?>">
                                            Show <?php echo e(count($hiddenFeatures)); ?> more...
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <li class="text-muted small fst-italic">No features defined</li>
                                <?php endif; ?>
                            </ul>

                            <div class="mt-auto">
                                <h6 class="font-weight-bold x-small text-uppercase text-muted mb-2">Limits</h6>
                                <div class="row g-1 mb-3">
                                    <?php
                                        $limits = [
                                            ['icon' => 'fas fa-users text-primary', 'label' => 'Staff', 'value' => $plan->max_users],
                                            ['icon' => 'fas fa-store text-info', 'label' => 'Branches', 'value' => $plan->max_branches],
                                            ['icon' => 'fas fa-user-friends text-secondary', 'label' => 'Customers', 'value' => $plan->limits['max_customers'] ?? 0],
                                            ['icon' => 'fas fa-cut text-white', 'label' => 'Services', 'value' => $plan->limits['max_services'] ?? 0],
                                            ['icon' => 'fas fa-box text-warning', 'label' => 'Products', 'value' => $plan->limits['max_products'] ?? 0],
                                            ['icon' => 'fas fa-gift text-purple', 'label' => 'Packages', 'value' => $plan->limits['max_packages'] ?? 0],
                                            ['icon' => 'fas fa-id-card text-teal', 'label' => 'Memberships', 'value' => $plan->limits['max_memberships'] ?? 0],
                                            ['icon' => 'fas fa-calendar-check text-danger', 'label' => 'Bookings/Mo', 'value' => $plan->limits['max_bookings_per_month'] ?? 0],
                                        ];
                                        $visibleLimits = array_slice($limits, 0, 2);
                                        $hiddenLimits = array_slice($limits, 2);
                                    ?>

                                    <?php $__currentLoopData = $visibleLimits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $limit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-6">
                                            <div
                                                class="p-1 bg-light rounded text-center border h-100 d-flex flex-column justify-content-center">
                                                <i class="<?php echo e($limit['icon']); ?> mb-1" style="font-size: 0.9rem;"></i>
                                                <div class="x-small fw-bold text-truncate"><?php echo e($limit['label']); ?></div>
                                                <div class="x-small text-muted">
                                                    <?php echo e(($limit['value'] === null || $limit['value'] === -1) ? 'Unlimited' : $limit['value']); ?>

                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <?php if(count($hiddenLimits) > 0): ?>
                                        <div class="col-12 collapse" id="limitsCollapse<?php echo e($plan->id); ?>">
                                            <div class="row g-1">
                                                <?php $__currentLoopData = $hiddenLimits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $limit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="col-6">
                                                        <div
                                                            class="p-1 bg-light rounded text-center border h-100 d-flex flex-column justify-content-center">
                                                            <i class="<?php echo e($limit['icon']); ?> mb-1" style="font-size: 0.9rem;"></i>
                                                            <div class="x-small fw-bold text-truncate"><?php echo e($limit['label']); ?></div>
                                                            <div class="x-small text-muted">
                                                                <?php echo e(($limit['value'] === null || $limit['value'] === -1) ? 'Unlimited' : $limit['value']); ?>

                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                            <a class="text-primary text-decoration-none small collapse-toggle"
                                                data-bs-toggle="collapse" href="#limitsCollapse<?php echo e($plan->id); ?>" role="button"
                                                aria-expanded="false" aria-controls="limitsCollapse<?php echo e($plan->id); ?>">
                                                Show <?php echo e(count($hiddenLimits)); ?> more...
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-primary w-100 fw-bold"
                                    onclick="setUpgradePlan('<?php echo e($plan->id); ?>', '<?php echo e($plan->name); ?>')" data-bs-toggle="modal"
                                    data-bs-target="#upgradeModal" <?php if(isset($pendingPayment) && $pendingPayment): ?> disabled
                                    <?php endif; ?>>
                                    <?php if(isset($pendingPayment) && $pendingPayment): ?>
                                        Request Pending
                                    <?php else: ?>
                                        Upgrade Plan
                                    <?php endif; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Upgrade Modal -->
        <div class="modal fade" id="upgradeModal" tabindex="-1" aria-labelledby="upgradeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form
                        action="<?php echo e(route('admin.saas.subscription.upgrade', ['salon_slug' => request()->route('salon_slug')])); ?>"
                        method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="plan_id" id="upgrade_plan_id">
                        <div class="modal-header">
                            <h5 class="modal-title" id="upgradeModalLabel">Upgrade Subscription</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>You are about to upgrade to <strong id="upgrade_plan_name"></strong>.</p>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Payment Method</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_manual"
                                        value="manual" checked>
                                    <label class="form-check-label" for="payment_manual">
                                        Manual Payment (Bank Transfer / Cash)
                                    </label>
                                    <div class="form-text text-muted small ms-4">
                                        Submit a request and pay manually. Admin approval required.
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_stripe"
                                        value="stripe" disabled>
                                    <label class="form-check-label text-muted" for="payment_stripe">
                                        Online Payment (Stripe) - <em>Coming Soon</em>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Confirm Upgrade</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Renew Modal -->
        <div class="modal fade" id="renewModal" tabindex="-1" aria-labelledby="renewModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form
                        action="<?php echo e(route('admin.saas.subscription.renew', ['salon_slug' => request()->route('salon_slug')])); ?>"
                        method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="modal-header">
                            <h5 class="modal-title" id="renewModalLabel">Renew Subscription</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>You are about to renew your current plan.</p>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Payment Method</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="renew_payment_manual" value="manual" checked>
                                    <label class="form-check-label" for="renew_payment_manual">
                                        Manual Payment (Bank Transfer / Cash)
                                    </label>
                                    <div class="form-text text-muted small ms-4">
                                        Submit a request and pay manually. Admin approval required.
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="renew_payment_stripe" value="stripe" disabled>
                                    <label class="form-check-label text-muted" for="renew_payment_stripe">
                                        Online Payment (Stripe) - <em>Coming Soon</em>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Confirm Renewal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php if($showNav ?? false): ?>
        </div>
        </div>
    <?php endif; ?>

    <?php $__env->startPush('styles'); ?>
        <style>
            .text-purple {
                color: #6f42c1;
            }

            .text-teal {
                color: #20c997;
            }

            .x-small {
                font-size: 0.75rem;
            }

            .popular-badge {
                position: absolute;
                top: 0.5rem;
                right: 0.5rem;
                background: #fff3cd;
                color: #856404;
                font-size: 0.65rem;
                font-weight: 700;
                padding: 0.2rem 0.5rem;
                border-radius: 1rem;
                z-index: 2;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .pricing-card {
                transition: transform 0.2s, box-shadow 0.2s;
            }

            .pricing-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            function setUpgradePlan(id, name) {
                document.getElementById('upgrade_plan_id').value = id;
                document.getElementById('upgrade_plan_name').textContent = name;
            }

            document.addEventListener('DOMContentLoaded', function () {
                var collapses = document.querySelectorAll('.collapse');
                collapses.forEach(function (collapse) {
                    collapse.addEventListener('shown.bs.collapse', function () {
                        var toggler = document.querySelector('[href="#' + this.id + '"]');
                        if (toggler) {
                            toggler.style.display = 'none';
                        }
                    });
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\saas\subscription\index.blade.php ENDPATH**/ ?>