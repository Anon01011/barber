<?php
    $salonSlug = $salonSlug ?? request()->route('salon_slug') ?? optional($salon)->slug ?? optional(auth()->user()->salon)->slug ?? session('current_salon_slug') ?? 'default';
?>

<?php $__env->startSection('title', 'My Loyalty Points & Rewards'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/customer-dashboard.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Top Breadcrumb & Navigation -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('customer.dashboard', ['salon_slug' => $salonSlug])); ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Loyalty Points &amp; Rewards</li>
                </ol>
            </nav>
            <h2 class="h3 fw-bold mb-0 text-dark">My Rewards &amp; Point History</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('customer.appointments.create', ['salon_slug' => $salonSlug])); ?>" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> Book Appointment &amp; Earn
            </a>
            <a href="<?php echo e(route('customer.dashboard', ['salon_slug' => $salonSlug])); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Loyalty Hero Banner -->
    <div class="loyalty-hero p-4 p-md-5 mb-4 position-relative overflow-hidden shadow-sm">
        <div class="row align-items-center g-4 position-relative" style="z-index: 2;">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge <?php echo e($tierBadge); ?> px-3 py-1 rounded-pill text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="fas fa-crown me-1 text-warning"></i> <?php echo e($tier); ?>

                    </span>
                    <span class="badge rounded-pill small" style="background: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35) !important;">
                        <?php echo e(optional($salon)->name ?? 'Salon'); ?>

                    </span>
                </div>
                <h1 class="display-6 fw-bold mb-2 text-white">Your Reward Balance</h1>
                <p class="lead mb-3 text-white-50" style="font-size: 1.05rem;">
                    Earn points on every haircut, styling service, and product purchase. Redeem points for instant discounts during checkout!
                </p>
                <?php if($nextTierPoints): ?>
                    <?php
                        $needed = max(0, $nextTierPoints - $totalEarned);
                        $progress = min(100, round(($totalEarned / $nextTierPoints) * 100));
                    ?>
                    <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.08); max-width: 460px;">
                        <div class="d-flex justify-content-between align-items-center small mb-1 text-white-50">
                            <span>Progress to next tier</span>
                            <span class="fw-bold text-warning"><?php echo e($needed); ?> pts to upgrade</span>
                        </div>
                        <div class="progress" style="height: 6px; background: rgba(255, 255, 255, 0.2);">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo e($progress); ?>%;" aria-valuenow="<?php echo e($progress); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="d-inline-flex flex-column align-items-lg-end bg-white bg-opacity-10 p-4 rounded-4 backdrop-blur border border-white border-opacity-20 shadow">
                    <div class="text-uppercase small fw-bold text-white-50 mb-1" style="letter-spacing: 1px;">Available Balance</div>
                    <div class="display-5 fw-bold text-warning mb-1">
                        <i class="fas fa-coins me-1"></i><?php echo e(number_format($loyaltyPoints)); ?> <span class="fs-6 text-white-50">pts</span>
                    </div>
                    <div class="h5 fw-semibold text-white mb-2">
                        ≈ <?php echo e(currency_symbol()); ?><?php echo e(number_format($loyaltyValue, 2)); ?> <span class="small opacity-75">Cash Value</span>
                    </div>
                    <div class="small text-white-50">
                        <i class="fas fa-info-circle me-1"></i> Min. redemption: <?php echo e($minPointsToRedeem); ?> pts
                    </div>
                </div>
            </div>
        </div>
        <!-- Ambient ambient background circles -->
        <div class="position-absolute" style="top: -60px; right: -60px; width: 220px; height: 220px; background: rgba(255, 255, 255, 0.08); border-radius: 50%; pointer-events: none; filter: blur(40px);"></div>
        <div class="position-absolute" style="bottom: -60px; left: -60px; width: 180px; height: 180px; background: rgba(245, 158, 11, 0.12); border-radius: 50%; pointer-events: none; filter: blur(30px);"></div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white customer-stat-card">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 me-3 d-flex align-items-center justify-content-center flex-shrink-0" 
                         style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.18) !important;">
                        <i class="fas fa-wallet fa-lg" style="color: #92400e !important;"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Available Balance</div>
                        <div class="h4 mb-0 fw-bold text-dark"><?php echo e(number_format($loyaltyPoints)); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white customer-stat-card">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 me-3 d-flex align-items-center justify-content-center flex-shrink-0" 
                         style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.18) !important;">
                        <i class="fas fa-arrow-down fa-lg" style="color: #065f46 !important;"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Lifetime Earned</div>
                        <div class="h4 mb-0 fw-bold text-success">+<?php echo e(number_format($totalEarned)); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white customer-stat-card">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 me-3 d-flex align-items-center justify-content-center flex-shrink-0" 
                         style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.18) !important;">
                        <i class="fas fa-arrow-up fa-lg" style="color: #991b1b !important;"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Points Redeemed</div>
                        <div class="h4 mb-0 fw-bold text-danger">-<?php echo e(number_format($totalRedeemed)); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white customer-stat-card">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 me-3 d-flex align-items-center justify-content-center flex-shrink-0" 
                         style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.18) !important;">
                        <i class="fas fa-receipt fa-lg" style="color: #1e3a8a !important;"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Activity</div>
                        <div class="h4 mb-0 fw-bold text-primary"><?php echo e($transactions->total()); ?> <span class="fs-6 text-muted fw-normal">events</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions List & Explainer Row -->
    <div class="row g-4">
        <!-- Main Transaction Log Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-list-alt text-warning me-2"></i>Points Transaction Log
                    </h5>
                    <!-- Filter Pills -->
                    <div class="d-flex gap-1 flex-wrap">
                        <a href="<?php echo e(route('customer.points.index', ['salon_slug' => $salonSlug])); ?>"
                            class="btn btn-xs rounded-pill px-3 py-1 <?php echo e(!request('type') ? 'btn-dark' : 'btn-light text-muted'); ?>">
                            All
                        </a>
                        <a href="<?php echo e(route('customer.points.index', ['salon_slug' => $salonSlug, 'type' => 'earned'])); ?>"
                            class="btn btn-xs rounded-pill px-3 py-1 <?php echo e(request('type') === 'earned' ? 'btn-success' : 'btn-light text-muted'); ?>">
                            Earned
                        </a>
                        <a href="<?php echo e(route('customer.points.index', ['salon_slug' => $salonSlug, 'type' => 'redeemed'])); ?>"
                            class="btn btn-xs rounded-pill px-3 py-1 <?php echo e(request('type') === 'redeemed' ? 'btn-danger' : 'btn-light text-muted'); ?>">
                            Redeemed
                        </a>
                        <a href="<?php echo e(route('customer.points.index', ['salon_slug' => $salonSlug, 'type' => 'adjusted'])); ?>"
                            class="btn btn-xs rounded-pill px-3 py-1 <?php echo e(request('type') === 'adjusted' ? 'btn-primary' : 'btn-light text-muted'); ?>">
                            Adjustments
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if($transactions->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="text-uppercase text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                        <th class="ps-4 py-3">Date &amp; Time</th>
                                        <th class="py-3">Type</th>
                                        <th class="py-3">Details / Reference</th>
                                        <th class="py-3 text-end">Points</th>
                                        <th class="pe-4 py-3 text-end">Balance After</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $isPositive = $tx->points > 0;
                                            $badgeClass = match($tx->type) {
                                                'earned' => 'points-badge-earned',
                                                'redeemed' => 'points-badge-redeemed',
                                                'adjusted' => 'points-badge-adjusted',
                                                default => 'bg-light text-dark'
                                            };
                                        ?>
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="fw-semibold text-dark"><?php echo e($tx->created_at->format('M d, Y')); ?></div>
                                                <div class="small text-muted" style="font-size: 0.75rem;"><?php echo e($tx->created_at->format('h:i A')); ?></div>
                                            </td>
                                            <td class="py-3">
                                                <span class="badge <?php echo e($badgeClass); ?> rounded-pill px-2 py-1 text-uppercase" style="font-size: 0.68rem;">
                                                    <?php if($tx->type === 'earned'): ?>
                                                        <i class="fas fa-plus-circle me-1"></i> Earned
                                                    <?php elseif($tx->type === 'redeemed'): ?>
                                                        <i class="fas fa-minus-circle me-1"></i> Redeemed
                                                    <?php elseif($tx->type === 'adjusted'): ?>
                                                        <i class="fas fa-sliders-h me-1"></i> Adjusted
                                                    <?php else: ?>
                                                        <?php echo e(ucfirst($tx->type)); ?>

                                                    <?php endif; ?>
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                <div class="fw-medium text-dark"><?php echo e($tx->notes ?? ($isPositive ? 'Service Visit & Booking Reward' : 'Checkout Discount Redemption')); ?></div>
                                                <?php if($tx->booking): ?>
                                                    <div class="small text-muted" style="font-size: 0.75rem;">
                                                        <i class="fas fa-calendar-check me-1"></i> Booking #<?php echo e($tx->booking_id); ?>

                                                        <?php if($tx->booking->service): ?>
                                                            — <?php echo e($tx->booking->service->name); ?>

                                                        <?php endif; ?>
                                                    </div>
                                                <?php elseif($tx->posSale): ?>
                                                    <div class="small text-muted" style="font-size: 0.75rem;">
                                                        <i class="fas fa-receipt me-1"></i> Invoice #<?php echo e($tx->posSale->invoice_number ?? $tx->pos_sale_id); ?>

                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3 text-end">
                                                <span class="fs-6 fw-bold <?php echo e($isPositive ? 'text-success' : 'text-danger'); ?>">
                                                    <?php echo e($isPositive ? '+' : ''); ?><?php echo e(number_format($tx->points)); ?>

                                                </span>
                                            </td>
                                            <td class="pe-4 py-3 text-end">
                                                <span class="fw-semibold text-muted">
                                                    <?php echo e(number_format($tx->balance_after)); ?> pts
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl bg-light rounded-circle text-muted d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                                <i class="fas fa-coins fa-2x text-warning opacity-50"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">No Point Transactions Found</h5>
                            <p class="text-muted small mb-3">You haven't earned or redeemed any reward points yet. Book a salon service to start earning!</p>
                            <a href="<?php echo e(route('customer.appointments.create', ['salon_slug' => $salonSlug])); ?>" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-semibold">
                                <i class="fas fa-calendar-plus me-1"></i> Book Your Next Service
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if($transactions->hasPages()): ?>
                    <div class="card-footer bg-white border-top p-3">
                        <?php echo e($transactions->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar / Rewards Info Card -->
        <div class="col-lg-4">
            <!-- How it works card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fas fa-gift text-warning me-2"></i>How Rewards Work
                </h6>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex align-items-start gap-3 mb-3">
                        <div class="avatar avatar-xs bg-warning bg-opacity-15 text-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                            <i class="fas fa-cut small"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark small">Earn on Every Visit</div>
                            <div class="text-muted small" style="font-size: 0.78rem;">
                                Earn <?php echo e($pointsPerCurrency); ?> points for every <?php echo e(currency_symbol()); ?>1 spent on haircuts, treatments, and retail items.
                            </div>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-3 mb-3">
                        <div class="avatar avatar-xs bg-success bg-opacity-15 text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                            <i class="fas fa-tag small"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark small">Instant Bill Discount</div>
                            <div class="text-muted small" style="font-size: 0.78rem;">
                                100 points = <?php echo e(currency_symbol()); ?><?php echo e(number_format(100 * $pointRate, 2)); ?> discount applied automatically at the cash register.
                            </div>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-3">
                        <div class="avatar avatar-xs bg-primary bg-opacity-15 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                            <i class="fas fa-shield-alt small"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark small">Safe &amp; Protected</div>
                            <div class="text-muted small" style="font-size: 0.78rem;">
                                Points are tied securely to your verified customer profile and never expire while your account is active.
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Need Help Box -->
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                <div class="avatar avatar-md bg-white rounded-circle text-primary mx-auto mb-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                    <i class="fas fa-headset fa-lg"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Questions About Your Points?</h6>
                <p class="text-muted small mb-3">Ask our staff during your appointment or reach out to the salon desk anytime.</p>
                <?php if(optional($salon)->phone): ?>
                    <a href="tel:<?php echo e(optional($salon)->phone); ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-2 fw-semibold">
                        <i class="fas fa-phone-alt me-1"></i> Call Salon: <?php echo e(optional($salon)->phone); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Ketan\projects\barber\resources\views/customer/loyalty/index.blade.php ENDPATH**/ ?>