<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us - <?php echo e($cms['appName'] ?? $appName); ?></title>
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

    <!-- ==================== HERO ==================== -->
    <section class="pub-inner-hero">
        <div class="pub-container">
            <div class="pub-inner-hero-content">
                <span class="pub-section-tag">Direct Concierge</span>
                <h1 class="pub-inner-hero-title">
                    Let's Connect
                </h1>
                <p class="pub-inner-hero-desc">
                    Have an inquiry about appointments, group reservations, or bespoke treatments? Our concierge team is here to assist you.
                </p>
            </div>
        </div>
    </section>

    <!-- ==================== CONTACT DETAILS & FORM ==================== -->
    <section style="padding: 4rem 0 6rem; background: #f8fafc;">
        <div class="pub-container">
            <!-- Flash Message Banner -->
            <?php if(session('success')): ?>
                <div style="margin-bottom: 2rem; padding: 1.25rem 1.5rem; border-radius: 0.75rem; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; display: flex; align-items: center; gap: 1rem;">
                    <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #059669; flex-shrink: 0;"></i>
                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 800; margin: 0 0 0.2rem; color: #065f46;">Message Received!</h4>
                        <p style="font-size: 0.85rem; margin: 0;"><?php echo e(session('success')); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(isset($errors) && $errors->any()): ?>
                <div style="margin-bottom: 2rem; padding: 1.25rem 1.5rem; border-radius: 0.75rem; background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; display: flex; align-items: flex-start; gap: 1rem;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: #e11d48; margin-top: 0.15rem; flex-shrink: 0;"></i>
                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 800; margin: 0 0 0.2rem; color: #9f1239;">Please check the form for errors:</h4>
                        <ul style="font-size: 0.85rem; margin: 0; padding-left: 1.25rem;">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <div class="pub-contact-card">
                <!-- Left: Contact Details Column -->
                <div class="pub-contact-sidebar">
                    <div>
                        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.12em; color: var(--gold); font-weight: 800; display: block; margin-bottom: 0.5rem;">
                            Reception &amp; Location
                        </span>
                        <h2 style="font-size: 1.85rem; font-weight: 800; color: #ffffff; margin: 0 0 0.5rem;">Direct Information</h2>
                        <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin-bottom: 2.5rem;">
                            Walk-ins are welcomed subject to artisan availability; reservations are recommended for an unhurried, VIP experience.
                        </p>

                        <div class="pub-contact-item">
                            <div class="pub-contact-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <div class="pub-contact-item-title">Telephone</div>
                                <a href="tel:<?php echo e(preg_replace('/[^0-9+]/', '', $cms['contactPhone'] ?? '')); ?>" class="pub-contact-item-val" style="text-decoration: underline;">
                                    <?php echo e($cms['contactPhone'] ?? '+974 66266581'); ?>

                                </a>
                            </div>
                        </div>

                        <div class="pub-contact-item">
                            <div class="pub-contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <div class="pub-contact-item-title">Email Inquiries</div>
                                <a href="mailto:<?php echo e($cms['contactEmail'] ?? 'info@fsqatar.com'); ?>" class="pub-contact-item-val">
                                    <?php echo e($cms['contactEmail'] ?? 'info@fsqatar.com'); ?>

                                </a>
                            </div>
                        </div>

                        <div class="pub-contact-item">
                            <div class="pub-contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <div class="pub-contact-item-title">Studio Location</div>
                                <p class="pub-contact-item-val" style="line-height: 1.5; font-size: 0.95rem;">
                                    <?php echo e($cms['contactAddress'] ?? 'Salwa Road, Doha, State of Qatar'); ?>

                                </p>
                            </div>
                        </div>

                        <div class="pub-contact-item">
                            <div class="pub-contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="pub-contact-item-title">Operating Hours</div>
                                <p class="pub-contact-item-val" style="line-height: 1.5; font-size: 0.95rem;">
                                    <?php echo e($cms['contactHours'] ?? 'Mon – Sun: 8:00 AM – 9:00 PM'); ?>

                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Links -->
                    <div style="padding-top: 2rem; border-top: 1px solid #1f293d;">
                        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; font-weight: 700; display: block; margin-bottom: 0.85rem;">
                            Follow Our Craft
                        </span>
                        <div class="pub-social-row" style="margin-top: 0;">
                            <?php if(!empty($cms['socialFacebook'])): ?>
                                <a href="<?php echo e($cms['socialFacebook']); ?>" target="_blank" rel="noopener noreferrer" class="pub-social-btn" aria-label="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            <?php endif; ?>
                            <?php if(!empty($cms['socialInstagram'])): ?>
                                <a href="<?php echo e($cms['socialInstagram']); ?>" target="_blank" rel="noopener noreferrer" class="pub-social-btn" aria-label="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            <?php endif; ?>
                            <?php if(!empty($cms['socialTwitter'])): ?>
                                <a href="<?php echo e($cms['socialTwitter']); ?>" target="_blank" rel="noopener noreferrer" class="pub-social-btn" aria-label="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            <?php endif; ?>
                            <?php if(!empty($cms['socialLinkedin'])): ?>
                                <a href="<?php echo e($cms['socialLinkedin']); ?>" target="_blank" rel="noopener noreferrer" class="pub-social-btn" aria-label="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Right: Interactive Inquiry Form -->
                <div class="pub-contact-main">
                    <div style="margin-bottom: 2rem;">
                        <span class="pub-section-tag" style="margin-bottom: 0.25rem;">Online Inquiries</span>
                        <h3 style="font-size: 1.75rem; font-weight: 800; color: #0b0f17; margin: 0 0 0.5rem;">Send Us a Message</h3>
                        <p style="color: #64748b; font-size: 0.95rem; margin: 0;">
                            Fill in your details below and our concierge desk will reply within 24 hours.
                        </p>
                    </div>

                    <form action="<?php echo e(route('contact.submit')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div style="display: grid; grid-template-columns: 1fr; gap: 1.25rem;" class="contact-form-grid">
                            <style>
                                @media (min-width: 640px) {
                                    .contact-form-grid { grid-template-columns: 1fr 1fr !important; }
                                    .contact-span-2 { grid-column: span 2 !important; }
                                }
                            </style>

                            <!-- Name -->
                            <div class="pub-form-group">
                                <label class="pub-form-label">Full Name *</label>
                                <input type="text" name="name" class="pub-form-input" required
                                    value="<?php echo e(old('name')); ?>" placeholder="e.g. John Doe">
                            </div>

                            <!-- Email -->
                            <div class="pub-form-group">
                                <label class="pub-form-label">Email Address *</label>
                                <input type="email" name="email" class="pub-form-input" required
                                    value="<?php echo e(old('email')); ?>" placeholder="name@example.com">
                            </div>

                            <!-- Phone -->
                            <div class="pub-form-group">
                                <label class="pub-form-label">Phone Number</label>
                                <input type="tel" name="phone" class="pub-form-input"
                                    value="<?php echo e(old('phone')); ?>" placeholder="+974 ...">
                            </div>

                            <!-- Service Interest -->
                            <div class="pub-form-group">
                                <label class="pub-form-label">Service Interest</label>
                                <select name="service_interest" class="pub-form-select">
                                    <option value="General Inquiry" <?php echo e(old('service_interest') == 'General Inquiry' ? 'selected' : ''); ?>>General Inquiry</option>
                                    <option value="Master Barbershop & Fade" <?php echo e(old('service_interest') == 'Master Barbershop & Fade' ? 'selected' : ''); ?>>Master Barbershop &amp; Fade</option>
                                    <option value="Hot Towel Straight Razor Shave" <?php echo e(old('service_interest') == 'Hot Towel Straight Razor Shave' ? 'selected' : ''); ?>>Hot Towel Straight Razor Shave</option>
                                    <option value="Haute Haircut & Blowout" <?php echo e(old('service_interest') == 'Haute Haircut & Blowout' ? 'selected' : ''); ?>>Haute Haircut &amp; Blowout</option>
                                    <option value="French Balayage & Color Artistry" <?php echo e(old('service_interest') == 'French Balayage & Color Artistry' ? 'selected' : ''); ?>>French Balayage &amp; Color Artistry</option>
                                    <option value="Keratin & Scalp Spa Therapy" <?php echo e(old('service_interest') == 'Keratin & Scalp Spa Therapy' ? 'selected' : ''); ?>>Keratin &amp; Scalp Spa Therapy</option>
                                    <option value="VIP Bridal / Group Reservation" <?php echo e(old('service_interest') == 'VIP Bridal / Group Reservation' ? 'selected' : ''); ?>>VIP Bridal / Group Reservation</option>
                                </select>
                            </div>

                            <!-- Subject -->
                            <div class="pub-form-group contact-span-2">
                                <label class="pub-form-label">Subject</label>
                                <input type="text" name="subject" class="pub-form-input"
                                    value="<?php echo e(old('subject')); ?>" placeholder="How can we assist you?">
                            </div>

                            <!-- Message -->
                            <div class="pub-form-group contact-span-2">
                                <label class="pub-form-label">Message *</label>
                                <textarea name="message" class="pub-form-textarea" rows="4" required
                                    placeholder="Please share any preferred dates, questions, or specific treatment details..."><?php echo e(old('message')); ?></textarea>
                            </div>
                        </div>

                        <div style="margin-top: 1.5rem;">
                            <button type="submit" class="pub-btn-primary" style="padding: 0.95rem 2.2rem; font-size: 0.95rem;">
                                <span>Send Message</span>
                                <i class="fas fa-paper-plane" style="font-size: 0.85rem; color: #c5a880;"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Optional Google Map Embed Container -->
            <?php if(!empty($cms['contactMap'])): ?>
                <?php
                    $rawMap = trim($cms['contactMap']);
                    $mapSrc = $rawMap;
                    if (preg_match('/src=["\']([^"\']+)["\']/', $rawMap, $matches)) {
                        $mapSrc = $matches[1];
                    }
                ?>
                <?php if(filter_var($mapSrc, FILTER_VALIDATE_URL)): ?>
                    <div style="margin-top: 3.5rem; border-radius: 1.25rem; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.05); aspect-ratio: 21/9; min-height: 340px;">
                        <iframe src="<?php echo e($mapSrc); ?>" width="100%" height="100%" style="border:0; min-height: 340px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <?php echo $__env->make('partials.saas-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>

</html>
<?php /**PATH C:\Ketan\projects\barber\resources\views/contact.blade.php ENDPATH**/ ?>