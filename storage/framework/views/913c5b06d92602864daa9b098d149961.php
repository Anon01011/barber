<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Us - <?php echo e($cms['appName'] ?? $appName); ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(app(\App\Services\SettingsService::class)->getFaviconUrl()); ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Dedicated Stylesheet -->
    <link rel="stylesheet" href="<?php echo e(asset('css/public-pages.css')); ?>">

    <!-- Dedicated Scripts -->
    <script src="<?php echo e(asset('js/public-pages.js')); ?>" defer></script>
</head>

<body>

    <?php echo $__env->make('partials.saas-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- ==================== ABOUT HERO ==================== -->
    <section class="pub-inner-hero">
        <div class="pub-container">
            <div class="pub-inner-hero-content">
                <span class="pub-section-tag">Our Heritage &amp; Story</span>
                <h1 class="pub-inner-hero-title">
                    <?php echo e($cms['aboutHeroTitle'] ?? 'Where Timeless Craftsmanship Meets Modern Luxury'); ?>

                </h1>
                <p class="pub-inner-hero-desc">
                    <?php echo e($cms['aboutHeroSubtitle'] ?? 'Dedicated to elevating the grooming and styling ritual for gentlemen and ladies who demand distinction, artistry, and VIP hospitality.'); ?>

                </p>
            </div>
        </div>
    </section>

    <!-- ==================== STORY & IMAGE SECTION ==================== -->
    <section style="padding: 5rem 0; background: #ffffff;">
        <div class="pub-container">
            <div style="display: flex; flex-direction: column; gap: 3.5rem; align-items: center;" class="about-flex-row">
                <style>
                    @media (min-width: 992px) {
                        .about-flex-row { flex-direction: row !important; gap: 4rem !important; }
                        .about-col-image { flex: 1 1 45%; max-width: 500px; }
                        .about-col-text { flex: 1 1 55%; }
                    }
                </style>

                <!-- Left Visual Image -->
                <div class="about-col-image" style="width: 100%;">
                    <div style="border-radius: 1.25rem; overflow: hidden; position: relative; box-shadow: 0 20px 45px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; aspect-ratio: 4/5;">
                        <img src="<?php echo e(!empty($cms['aboutImage']) ? $cms['aboutImage'] : 'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80'); ?>"
                            alt="<?php echo e($cms['appName'] ?? $appName); ?> Salon & Barber Studio" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="pub-img-gradient-overlay"></div>
                        <div class="pub-hero-caption">
                            <div class="pub-hero-caption-tag">Uncompromising Quality</div>
                            <h3 class="pub-hero-caption-title">Master Barbers &amp; Visionary Stylists</h3>
                        </div>
                    </div>
                </div>

                <!-- Right Narrative Text -->
                <div class="about-col-text" style="width: 100%; display: flex; flex-direction: column; gap: 1.25rem;">
                    <span class="pub-section-tag" style="margin-bottom: 0;">The Philosophy</span>
                    <h2 style="font-size: 2.25rem; font-weight: 800; margin: 0; color: #0b0f17; letter-spacing: -0.02em;">
                        <?php echo e($cms['aboutStoryTitle'] ?? 'Our Heritage & Philosophy'); ?>

                    </h2>
                    <p style="color: #475569; font-size: 1.05rem; line-height: 1.7; margin: 0;">
                        <?php echo e($cms['aboutStoryP1'] ?? 'Born out of a genuine passion for grooming and artistic flair, our sanctuary bridges classic barbering traditions with cutting-edge salon innovations. From handcrafted hot-towel straight razor shaves to bespoke color formulation and restorative hair treatments, every touchpoint is designed to renew and inspire confidence.'); ?>

                    </p>
                    <p style="color: #475569; font-size: 1.05rem; line-height: 1.7; margin: 0;">
                        <?php echo e($cms['aboutStoryP2'] ?? 'We believe true grooming is an art form. Our masters and stylists undergo continuous training in modern techniques, hygiene standards, and client satisfaction to ensure that whether you are in for a quick beard trim or a total transformation, you leave looking and feeling your absolute best.'); ?>

                    </p>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                        <div style="padding: 1.25rem; border-radius: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div style="font-size: 1.85rem; font-weight: 800; color: #0b0f17; font-family: var(--font-heading);"><?php echo e($cms['heroStat1Val'] ?? '15k+'); ?></div>
                            <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-top: 0.25rem;">Clients Welcomed</div>
                        </div>
                        <div style="padding: 1.25rem; border-radius: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div style="font-size: 1.85rem; font-weight: 800; color: #926b38; font-family: var(--font-heading);"><?php echo e($cms['heroStat2Val'] ?? '4.9 ★'); ?></div>
                            <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-top: 0.25rem;">Verified Reviews</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== MISSION & VISION ==================== -->
    <section style="padding: 5rem 0; background: #f8fafc; border-top: 1px solid #e2e8f0;">
        <div class="pub-container">
            <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;" class="mission-grid">
                <style>
                    @media (min-width: 768px) {
                        .mission-grid { grid-template-columns: 1fr 1fr !important; }
                    }
                </style>

                <div style="background: #ffffff; padding: 2.75rem 2.25rem; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0,0,0,0.03);">
                    <div class="pub-why-icon-box">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 0.75rem; color: #0b0f17;">Our Mission</h3>
                    <p style="color: #64748b; line-height: 1.7; margin: 0; font-size: 0.95rem;">
                        <?php echo e($cms['aboutMission'] ?? 'To deliver unmatched styling and grooming experiences through master craftsmanship, premium products, and frictionless technology, empowering every client to look confident and feel renewed.'); ?>

                    </p>
                </div>

                <div style="background: #ffffff; padding: 2.75rem 2.25rem; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0,0,0,0.03);">
                    <div class="pub-why-icon-box">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 0.75rem; color: #0b0f17;">Our Vision</h3>
                    <p style="color: #64748b; line-height: 1.7; margin: 0; font-size: 0.95rem;">
                        <?php echo e($cms['aboutVision'] ?? 'To stand as the gold standard in premier barber and salon care—a community-centric haven where heritage craftsmanship meets contemporary elegance.'); ?>

                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== VIP CTA BANNER ==================== -->
    <section class="pub-cta-banner">
        <div class="pub-container">
            <div class="pub-cta-banner-content">
                <span class="pub-cta-banner-tag">Experience The Difference</span>
                <h2 class="pub-cta-banner-title">
                    Join Our Discerning Clientele
                </h2>
                <p class="pub-cta-banner-desc">
                    Schedule your bespoke hair, beard, or spa appointment with our master artisans.
                </p>
                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem;">
                    <a href="<?php echo e($cms['heroPrimaryBtnUrl'] ?? route('saas.register')); ?>" class="pub-btn-gold" style="padding: 1rem 2.5rem; font-size: 1rem;">
                        <span>Book Your Appointment</span>
                        <i class="fas fa-arrow-right" style="font-size: 0.85rem;"></i>
                    </a>
                    <a href="<?php echo e(route('contact')); ?>" class="pub-btn-outline" style="padding: 1rem 2.2rem; font-size: 1rem; color: #ffffff !important; border-color: rgba(255,255,255,0.25);">
                        <span>Contact Concierge</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php echo $__env->make('partials.saas-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>

</html>
<?php /**PATH C:\Ketan\projects\barber\resources\views/about.blade.php ENDPATH**/ ?>