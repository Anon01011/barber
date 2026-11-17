<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($cms['appName'] ?? $appName); ?> - Salon & Barbershop Management Software</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(app(\App\Services\SettingsService::class)->getFaviconUrl()); ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Dedicated Production Stylesheet -->
    <link rel="stylesheet" href="<?php echo e(asset('css/public-pages.css')); ?>">

    <!-- Dedicated Production Scripts -->
    <script src="<?php echo e(asset('js/public-pages.js')); ?>" defer></script>
</head>

<body>

    <?php echo $__env->make('partials.saas-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- ==================== HERO SECTION ==================== -->
    <section class="pub-hero-wrap">
        <div class="pub-container">
            <div class="pub-hero-inner">
                <!-- Left Content Column -->
                <div class="pub-hero-content">
                    <!-- Announcement Badge -->
                    <div class="pub-hero-badge">
                        <span class="pub-badge-dot"></span>
                        <span><?php echo e($cms['heroBadge'] ?? '✨ The All-In-One Cloud Platform for Salons & Barbershops'); ?></span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="pub-hero-title">
                        Supercharge Your
                        <span class="pub-hero-title-accent">Salon &amp; Barbershop</span>
                        Operations &amp; Growth
                    </h1>

                    <!-- Subtitle -->
                    <p class="pub-hero-subtitle">
                        <?php echo e($cms['heroSubtitle'] ?? 'Effortless 24/7 online appointments, omnichannel cloud POS, automated client retention, staff commissions, and multi-branch control built for modern beauty & grooming leaders.'); ?>

                    </p>

                    <!-- CTA Actions -->
                    <div class="pub-hero-actions">
                        <a href="<?php echo e(route('saas.register')); ?>" class="pub-btn-primary" style="padding: 1rem 2.25rem; font-size: 1rem;">
                            <span>Start 14-Day Free Trial</span>
                            <i class="fas fa-arrow-right" style="font-size: 0.85rem; color: #c5a880;"></i>
                        </a>
                        <a href="#plans-section" class="pub-btn-outline" style="padding: 1rem 2rem; font-size: 1rem;">
                            <i class="fas fa-tags" style="color: #c5a880; font-size: 0.95rem;"></i>
                            <span>View Pricing Plans</span>
                        </a>
                    </div>

                    <!-- Trust Signals -->
                    <div class="pub-trust-row">
                        <div class="pub-trust-item">
                            <i class="fas fa-check-circle pub-trust-icon"></i>
                            <span>No Credit Card Required</span>
                        </div>
                        <div class="pub-trust-item">
                            <i class="fas fa-bolt pub-trust-icon"></i>
                            <span>Instant Setup in 2 Minutes</span>
                        </div>
                        <div class="pub-trust-item">
                            <i class="fas fa-shield-halved pub-trust-icon"></i>
                            <span>PCI-DSS Bank Grade Security</span>
                        </div>
                    </div>
                </div>

                <!-- Right Visual Column -->
                <div class="pub-hero-visual">
                    <div class="pub-hero-img-box">
                        <img src="<?php echo e(!empty($cms['heroImage']) ? $cms['heroImage'] : 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1400&q=80'); ?>"
                            alt="<?php echo e($cms['appName'] ?? $appName); ?> SaaS Platform">
                        <div class="pub-img-gradient-overlay"></div>
                        <div class="pub-hero-caption">
                            <div class="pub-hero-caption-tag">Enterprise SaaS</div>
                            <h3 class="pub-hero-caption-title">Point of Sale, Scheduling &amp; Analytics</h3>
                        </div>
                    </div>

                    <!-- Floating Verified Review Card -->
                    <div class="pub-hero-float-card">
                        <div class="pub-float-icon-box">
                            <i class="fas fa-star" style="color: #d97706;"></i>
                        </div>
                        <div>
                            <div style="font-size: 1.15rem; font-weight: 800; color: #0b0f17; line-height: 1;">
                                <?php echo e($cms['heroStat2Val'] ?? '4.9 ★'); ?>

                            </div>
                            <div style="font-size: 0.725rem; font-weight: 700; color: #64748b; margin-top: 0.2rem;">
                                <?php echo e($cms['heroStat2Lbl'] ?? 'Average Rating'); ?> (15k+ Salons)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== METRICS STRIP ==================== -->
    <section class="pub-stats-strip">
        <div class="pub-container">
            <div class="pub-stats-grid">
                <div class="pub-stat-box">
                    <div class="pub-stat-num"><?php echo e($cms['heroStat1Val'] ?? '15k+'); ?></div>
                    <div class="pub-stat-lbl">Active Salons &amp; Barbers</div>
                </div>
                <div class="pub-stat-box">
                    <div class="pub-stat-num gold"><?php echo e($cms['heroStat2Val'] ?? '4.9 ★'); ?></div>
                    <div class="pub-stat-lbl">Software Satisfaction</div>
                </div>
                <div class="pub-stat-box">
                    <div class="pub-stat-num">99.9%</div>
                    <div class="pub-stat-lbl">Cloud Uptime SLA</div>
                </div>
                <div class="pub-stat-box">
                    <div class="pub-stat-num" style="color: #059669;"><?php echo e($cms['heroStat3Val'] ?? '100%'); ?></div>
                    <div class="pub-stat-lbl">PCI-DSS Compliant</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== PLATFORM FEATURES ==================== -->
    <section id="features" class="pub-why-section" style="background: #ffffff; border-bottom: 1px solid #e2e8f0;">
        <div class="pub-container">
            <div class="pub-section-header">
                <span class="pub-section-tag">Complete Operating System</span>
                <h2 class="pub-section-title">Everything Needed to Run &amp; Scale Your Business</h2>
                <p class="pub-section-desc">
                    Eliminate fragmented apps and spreadsheets. Manage bookings, POS, team payroll, customer loyalty, and multi-location growth under one intuitive dashboard.
                </p>
            </div>

            <div class="pub-why-grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
                <!-- Feature 1 -->
                <div class="pub-why-box">
                    <div class="pub-why-icon-box">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>24/7 Smart Online Bookings</h3>
                    <p>Frictionless branded booking portal for your clients. Eliminate double bookings, send automated confirmations, and accept deposits upfront.</p>
                </div>

                <!-- Feature 2 -->
                <div class="pub-why-box">
                    <div class="pub-why-icon-box">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    <h3>Omnichannel POS &amp; Split Pay</h3>
                    <p>Lightning-fast register built for touchscreens. Accept card, cash, tips, gift vouchers, and split tenders with instant receipt printing.</p>
                </div>

                <!-- Feature 3 -->
                <div class="pub-why-box">
                    <div class="pub-why-icon-box">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h3>Staff Shifts &amp; Commissions</h3>
                    <p>Set custom commission tiers, track stylist production in real time, automate payroll reports, and manage individual shift schedules.</p>
                </div>

                <!-- Feature 4 -->
                <div class="pub-why-box">
                    <div class="pub-why-icon-box">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <h3>Automated SMS &amp; Retention</h3>
                    <p>Cut no-shows by up to 80% with automated 24-hour reminder texts, birthday promotions, and targeted marketing campaigns.</p>
                </div>

                <!-- Feature 5 -->
                <div class="pub-why-box">
                    <div class="pub-why-icon-box">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <h3>Smart Inventory &amp; Stock Alerts</h3>
                    <p>Barcode scanning, supplier purchase orders, automatic deduction on service usage, and automated low-stock warnings.</p>
                </div>

                <!-- Feature 6 -->
                <div class="pub-why-box">
                    <div class="pub-why-icon-box">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <h3>Multi-Branch Management</h3>
                    <p>Scale effortlessly from a single chair to a nationwide chain. Centralized reporting, unified customer history, and branch permissions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== TAILORED FOR BARBERS & SALONS ==================== -->
    <section id="salon-barber" class="pub-dual-section">
        <div class="pub-container">
            <div class="pub-section-header">
                <span class="pub-section-tag">Engineered For Your Craft</span>
                <h2 class="pub-section-title">Built Specially for Barbershops &amp; Haute Salons</h2>
                <p class="pub-section-desc">
                    <?php echo e($cms['whySubtitle'] ?? 'Purpose-built workflows tailored to the unique rhythms of fast-paced barbershop rotations and high-touch luxury salon rituals.'); ?>

                </p>
            </div>

            <div class="pub-dual-grid">
                <!-- Barbershop Card -->
                <div class="pub-card-barber">
                    <div>
                        <div class="pub-card-header">
                            <span class="pub-card-pill gold">For Modern Barbershops</span>
                            <div class="pub-card-icon dark">
                                <i class="fas fa-cut"></i>
                            </div>
                        </div>

                        <h3 class="pub-card-title"><?php echo e($cms['barberHeading'] ?? "The Barbershop Edition"); ?></h3>
                        <p class="pub-card-desc">
                            Designed for high-speed turnover, chair rotations, walk-in management, and razor-sharp checkout efficiency.
                        </p>

                        <div class="pub-check-list">
                            <div class="pub-check-row">
                                <div class="pub-check-circle gold">✓</div>
                                <div class="pub-check-text">
                                    <h4>Walk-In Queue &amp; Chair Rotation</h4>
                                    <p>Live waiting lounge monitor and next-available barber dispatch system.</p>
                                </div>
                            </div>
                            <div class="pub-check-row">
                                <div class="pub-check-circle gold">✓</div>
                                <div class="pub-check-text">
                                    <h4>Fast 5-Second Cash &amp; Card POS</h4>
                                    <p>Quick-tap service charging, custom tip percentages, and split payments.</p>
                                </div>
                            </div>
                            <div class="pub-check-row">
                                <div class="pub-check-circle gold">✓</div>
                                <div class="pub-check-text">
                                    <h4>Single-Use &amp; Sanitization Tracking</h4>
                                    <p>Hygiene compliance logs and consumable razor/towel inventory tracking.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="<?php echo e(route('saas.register')); ?>" class="pub-btn-gold" style="width: 100%; text-align: center; justify-content: center; padding: 0.95rem;">
                        <span>Register Barbershop</span>
                        <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
                    </a>
                </div>

                <!-- Salon Card -->
                <div class="pub-card-salon">
                    <div>
                        <div class="pub-card-header">
                            <span class="pub-card-pill salon">For Haute Beauty Salons</span>
                            <div class="pub-card-icon light">
                                <i class="fas fa-spa" style="color: #926b38;"></i>
                            </div>
                        </div>

                        <h3 class="pub-card-title"><?php echo e($cms['salonHeading'] ?? 'The Luxury Salon Edition'); ?></h3>
                        <p class="pub-card-desc">
                            Built for complex service chaining, color formulation cards, assistant assignment, and VIP high-ticket packages.
                        </p>

                        <div class="pub-check-list">
                            <div class="pub-check-row">
                                <div class="pub-check-circle dark">✓</div>
                                <div class="pub-check-text">
                                    <h4>Multi-Service Appointment Chaining</h4>
                                    <p>Coordinate processing time, washing basin slots, and junior assistant tasks.</p>
                                </div>
                            </div>
                            <div class="pub-check-row">
                                <div class="pub-check-circle dark">✓</div>
                                <div class="pub-check-text">
                                    <h4>Client Color &amp; Treatment Cards</h4>
                                    <p>Store exact formula grams, patch test dates, and before/after transformation photos.</p>
                                </div>
                            </div>
                            <div class="pub-check-row">
                                <div class="pub-check-circle dark">✓</div>
                                <div class="pub-check-text">
                                    <h4>Packages &amp; VIP Memberships</h4>
                                    <p>Sell multi-session series, seasonal promotions, and recurring monthly beauty passes.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="<?php echo e(route('saas.register')); ?>" class="pub-btn-primary" style="width: 100%; text-align: center; justify-content: center; padding: 0.95rem;">
                        <span>Register Beauty Salon</span>
                        <i class="fas fa-arrow-right" style="font-size: 0.75rem; color: #c5a880;"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== PRICING & PLANS ONLY SECTION ==================== -->
    <section id="plans-section" class="pub-pricing-section" style="padding: 6.5rem 0; background: #ffffff;">
        <div class="pub-container">
            <div class="pub-section-header">
                <span class="pub-section-tag">Transparent SaaS Pricing</span>
                <h2 class="pub-section-title">Choose the Ideal Plan for Your Salon</h2>
                <p class="pub-section-desc">
                    Simple, transparent pricing designed to grow with your business. All plans include full onboarding assistance, free software updates, and unlimited customer support.
                </p>
            </div>

            <!-- Beautiful Luxury Pricing Grid -->
            <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; max-width: 1100px; margin: 0 auto;" class="plans-grid-wrap">
                <style>
                    @media (min-width: 768px) {
                        .plans-grid-wrap {
                            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)) !important;
                            align-items: stretch !important;
                        }
                    }
                </style>

                <?php if(isset($plans) && $plans->isNotEmpty()): ?>
                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $featuresList = [];
                            if (is_array($plan->features)) {
                                $featuresList = $plan->features;
                            } elseif (is_string($plan->features)) {
                                $decoded = json_decode($plan->features, true);
                                $featuresList = is_array($decoded) ? $decoded : explode(',', $plan->features);
                            }
                            $isFeatured = $plan->is_popular || $loop->first;
                        ?>
                        <div class="pub-plan-card <?php echo e($isFeatured ? 'featured' : ''); ?>" style="position: relative; border-radius: 1.5rem; border: 2px solid <?php echo e($isFeatured ? '#0b0f17' : '#e2e8f0'); ?>; padding: 3rem 2.25rem; display: flex; flex-direction: column; justify-content: space-between; background: #ffffff; box-shadow: <?php echo e($isFeatured ? '0 20px 45px rgba(11, 15, 23, 0.12)' : '0 10px 30px rgba(0, 0, 0, 0.04)'); ?>;">
                            <?php if($isFeatured): ?>
                                <span class="pub-plan-badge" style="position: absolute; top: -0.9rem; right: 2rem; background: #c5a880; color: #ffffff; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.4rem 1.15rem; border-radius: 9999px;">
                                    Recommended
                                </span>
                            <?php endif; ?>

                            <div>
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                    <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #926b38;">
                                        <?php echo e(ucfirst($plan->business_type ?? 'Salon & Barber')); ?>

                                    </span>
                                    <?php if($plan->trial_days > 0): ?>
                                        <span style="font-size: 0.725rem; font-weight: 700; color: #059669; background: #ecfdf5; padding: 0.25rem 0.65rem; border-radius: 9999px; border: 1px solid #a7f3d0;">
                                            <?php echo e($plan->trial_days); ?>-Day Free Trial
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <h3 class="pub-plan-name" style="font-size: 1.85rem; font-weight: 800; color: #0b0f17; margin: 0 0 0.5rem;">
                                    <?php echo e($plan->name); ?>

                                </h3>

                                <div class="pub-plan-price-wrap" style="display: flex; align-items: baseline; gap: 0.35rem; margin: 1.25rem 0 0.5rem;">
                                    <span class="pub-plan-price" style="font-size: 3.25rem; font-weight: 800; color: #0b0f17; font-family: var(--font-heading); line-height: 1;">
                                        <?php echo e($currencySymbol); ?><?php echo e(number_format($plan->price, 0)); ?>

                                    </span>
                                    <span class="pub-plan-period" style="font-size: 0.9rem; color: #64748b; font-weight: 600;">
                                        / <?php echo e($plan->duration_in_days ? ($plan->duration_in_days >= 365 ? 'year' : 'month') : 'month'); ?>

                                    </span>
                                </div>

                                <p style="font-size: 0.9rem; color: #64748b; line-height: 1.6; margin-bottom: 2rem;">
                                    <?php echo e(!empty($plan->description) ? $plan->description : 'Complete professional access to all platform features, point-of-sale, and booking systems.'); ?>

                                </p>

                                <!-- Features Checklist -->
                                <ul class="pub-plan-features" style="list-style: none; padding: 0; margin: 0 0 2.5rem; display: flex; flex-direction: column; gap: 0.85rem;">
                                    <?php if(!empty($featuresList)): ?>
                                        <?php $__currentLoopData = $featuresList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="pub-plan-feature-item" style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.925rem; color: #334155;">
                                                <i class="fas fa-check-circle" style="color: #059669; font-size: 0.95rem; flex-shrink: 0;"></i>
                                                <span><?php echo e(is_string($feat) ? $feat : json_encode($feat)); ?></span>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Unlimited Online Bookings</li>
                                        <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> High-Speed POS &amp; Split Payments</li>
                                        <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Staff Commissions &amp; Payroll</li>
                                        <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Automated SMS &amp; Email Reminders</li>
                                        <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Inventory &amp; Stock Barcode Scanning</li>
                                    <?php endif; ?>
                                </ul>
                            </div>

                            <a href="<?php echo e(route('saas.register', ['plan_id' => $plan->id])); ?>" class="<?php echo e($isFeatured ? 'pub-btn-primary' : 'pub-btn-gold'); ?>" style="width: 100%; text-align: center; justify-content: center; padding: 1rem; font-size: 0.95rem; border-radius: 0.5rem;">
                                <span><?php echo e($plan->trial_days > 0 ? 'Start ' . $plan->trial_days . '-Day Free Trial' : 'Register Salon'); ?></span>
                                <i class="fas fa-arrow-right" style="font-size: 0.8rem; margin-left: 0.5rem;"></i>
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <!-- Fallback Luxury SaaS Tier Cards -->
                    <!-- Starter Plan -->
                    <div class="pub-plan-card">
                        <div>
                            <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #926b38;">Boutique &amp; Solo</span>
                            <h3 class="pub-plan-name" style="font-size: 1.85rem; font-weight: 800; margin: 0.25rem 0 0.5rem;">Starter Studio</h3>
                            <div class="pub-plan-price-wrap" style="margin: 1.25rem 0 0.5rem;">
                                <span class="pub-plan-price"><?php echo e($currencySymbol); ?>29</span>
                                <span class="pub-plan-period">/ month</span>
                            </div>
                            <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 2rem;">Perfect for independent stylists and boutique 1-3 chair studios.</p>
                            <ul class="pub-plan-features">
                                <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Up to 3 Staff Members</li>
                                <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Unlimited Online Bookings</li>
                                <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Cloud POS &amp; Card Processing</li>
                                <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Automated SMS Appointment Reminders</li>
                            </ul>
                        </div>
                        <a href="<?php echo e(route('saas.register')); ?>" class="pub-btn-gold" style="width: 100%; text-align: center; justify-content: center; padding: 1rem;">
                            <span>Start 14-Day Free Trial</span>
                            <i class="fas fa-arrow-right" style="font-size: 0.8rem;"></i>
                        </a>
                    </div>

                    <!-- Pro Plan (Featured) -->
                    <div class="pub-plan-card featured">
                        <span class="pub-plan-badge">Most Popular</span>
                        <div>
                            <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #926b38;">Growing Salon</span>
                            <h3 class="pub-plan-name" style="font-size: 1.85rem; font-weight: 800; margin: 0.25rem 0 0.5rem;">Professional Salon</h3>
                            <div class="pub-plan-price-wrap" style="margin: 1.25rem 0 0.5rem;">
                                <span class="pub-plan-price"><?php echo e($currencySymbol); ?>59</span>
                                <span class="pub-plan-period">/ month</span>
                            </div>
                            <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 2rem;">Comprehensive platform for growing salons and busy barbershops.</p>
                            <ul class="pub-plan-features">
                                <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Up to 15 Staff Members</li>
                                <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Unlimited Bookings &amp; Walk-Ins</li>
                                <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Advanced Staff Commissions &amp; Tips</li>
                                <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Inventory &amp; Barcode Management</li>
                                <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> WhatsApp &amp; SMS Automation</li>
                            </ul>
                        </div>
                        <a href="<?php echo e(route('saas.register')); ?>" class="pub-btn-primary" style="width: 100%; text-align: center; justify-content: center; padding: 1rem;">
                            <span>Start 14-Day Free Trial</span>
                            <i class="fas fa-arrow-right" style="font-size: 0.8rem; color: #c5a880;"></i>
                        </a>
                    </div>

                    <!-- Enterprise Plan -->
                    <div class="pub-plan-card">
                        <div>
                            <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #926b38;">Chains &amp; Luxury</span>
                            <h3 class="pub-plan-name" style="font-size: 1.85rem; font-weight: 800; margin: 0.25rem 0 0.5rem;">Enterprise Chain</h3>
                            <div class="pub-plan-price-wrap" style="margin: 1.25rem 0 0.5rem;">
                                <span class="pub-plan-price"><?php echo e($currencySymbol); ?>119</span>
                                <span class="pub-plan-period">/ month</span>
                            </div>
                            <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 2rem;">Multi-branch management with dedicated account manager.</p>
                            <ul class="pub-plan-features">
                                <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Unlimited Staff &amp; Chairs</li>
                                <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Multi-Branch &amp; Centralized HQ</li>
                                <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Custom API &amp; Webhook Access</li>
                                <li class="pub-plan-feature-item"><i class="fas fa-check-circle" style="color: #059669;"></i> Dedicated 24/7 Account Concierge</li>
                            </ul>
                        </div>
                        <a href="<?php echo e(route('saas.register')); ?>" class="pub-btn-gold" style="width: 100%; text-align: center; justify-content: center; padding: 1rem;">
                            <span>Contact Enterprise</span>
                            <i class="fas fa-arrow-right" style="font-size: 0.8rem;"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ==================== SALON OWNER TESTIMONIALS ==================== -->
    <section class="pub-reviews-section">
        <div class="pub-container">
            <div class="pub-section-header">
                <span class="pub-section-tag">Owner Success</span>
                <h2 class="pub-section-title">Trusted by Salon Owners &amp; Master Barbers</h2>
                <p class="pub-section-desc">See how top salons and barbershops transformed their revenue and client retention with our cloud platform.</p>
            </div>

            <div class="pub-reviews-grid">
                <div class="pub-review-card">
                    <div>
                        <div class="pub-stars-row">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="pub-review-quote">
                            "Switching to this platform cut our no-shows from 20% down to under 2%. The automated SMS reminders and quick POS checkout saved my reception team 15 hours every week."
                        </p>
                    </div>
                    <div class="pub-client-row">
                        <div class="pub-client-avatar">DK</div>
                        <div>
                            <h4 class="pub-client-name">David Kingston</h4>
                            <p class="pub-client-service">Owner, Kingston Barbershop Lounge</p>
                        </div>
                    </div>
                </div>

                <div class="pub-review-card">
                    <div>
                        <div class="pub-stars-row">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="pub-review-quote">
                            "The color formula notes and staff commission calculations are extraordinary. Our stylists love seeing their daily tips and commissions in real time on their own phones."
                        </p>
                    </div>
                    <div class="pub-client-row">
                        <div class="pub-client-avatar">EM</div>
                        <div>
                            <h4 class="pub-client-name">Elena Moretti</h4>
                            <p class="pub-client-service">Founder, Haute Beauty &amp; Hair Spa</p>
                        </div>
                    </div>
                </div>

                <div class="pub-review-card">
                    <div>
                        <div class="pub-stars-row">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="pub-review-quote">
                            "We expanded from 2 branches to 5 branches within 18 months because this software made inventory transfers and centralized reporting completely effortless."
                        </p>
                    </div>
                    <div class="pub-client-row">
                        <div class="pub-client-avatar">RH</div>
                        <div>
                            <h4 class="pub-client-name">Rashid Al-Hassan</h4>
                            <p class="pub-client-service">Managing Partner, The Gentlemen Club Chain</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== VIP REGISTRATION BANNER ==================== -->
    <section class="pub-cta-banner">
        <div class="pub-container">
            <div class="pub-cta-banner-content">
                <span class="pub-cta-banner-tag">Transform Your Business</span>
                <h2 class="pub-cta-banner-title">
                    Ready to Modernize Your Salon Operations?
                </h2>
                <p class="pub-cta-banner-desc">
                    Join over 15,000 salon and barbershop owners who trust us with their bookings, payments, and client relationships.
                </p>
                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem;">
                    <a href="<?php echo e(route('saas.register')); ?>" class="pub-btn-gold" style="padding: 1rem 2.5rem; font-size: 1rem;">
                        <span>Register Salon Now</span>
                        <i class="fas fa-arrow-right" style="font-size: 0.85rem;"></i>
                    </a>
                    <a href="<?php echo e(route('contact')); ?>" class="pub-btn-outline" style="padding: 1rem 2.2rem; font-size: 1rem; color: #ffffff !important; border-color: rgba(255,255,255,0.25);">
                        <span>Talk to Sales</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php echo $__env->make('partials.saas-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>

</html>
<?php /**PATH C:\Ketan\projects\barber\resources\views/welcome.blade.php ENDPATH**/ ?>