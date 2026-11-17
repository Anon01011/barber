<!-- Website & CMS Settings Partial -->
<div class="settings-section-card mb-4">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-heading"></i></div>
        <h6>Landing & Welcome Page Content</h6>
    </div>

    <div class="settings-field-group mb-3">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Announcement Badge</label>
                <input type="text" class="form-control" name="hero_badge"
                    value="<?php echo e(old('hero_badge', $settings['hero_badge'] ?? '✨ Precision Grooming & Luxury Salon Care')); ?>"
                    placeholder="e.g. ✨ Precision Grooming & Luxury Salon Care">
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Main Hero Headline</label>
                <input type="text" class="form-control fw-bold" name="hero_title"
                    value="<?php echo e(old('hero_title', $settings['hero_title'] ?? 'Masterful Precision & Modern Luxury for Hair, Beard & Style')); ?>"
                    placeholder="Main headline">
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Hero Subtitle / Description</label>
                <textarea class="form-control" name="hero_subtitle" rows="3"><?php echo e(old('hero_subtitle', $settings['hero_subtitle'] ?? 'Experience seamless online bookings, VIP master barbering, artisan hair color, and bespoke treatments in one premier grooming destination.')); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Primary CTA Button Text</label>
                <input type="text" class="form-control" name="hero_primary_btn_text"
                    value="<?php echo e(old('hero_primary_btn_text', $settings['hero_primary_btn_text'] ?? 'Book Appointment')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Primary CTA URL / Target</label>
                <input type="text" class="form-control" name="hero_primary_btn_url"
                    value="<?php echo e(old('hero_primary_btn_url', $settings['hero_primary_btn_url'] ?? '/register-salon')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Secondary CTA Button Text</label>
                <input type="text" class="form-control" name="hero_secondary_btn_text"
                    value="<?php echo e(old('hero_secondary_btn_text', $settings['hero_secondary_btn_text'] ?? 'Explore Services')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Secondary CTA URL</label>
                <input type="text" class="form-control" name="hero_secondary_btn_url"
                    value="<?php echo e(old('hero_secondary_btn_url', $settings['hero_secondary_btn_url'] ?? '#services-experience')); ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Custom Hero Image</label>
                <input type="file" class="form-control" name="hero_image" accept="image/*">
                <?php if(!empty($settings['hero_image'])): ?>
                    <div class="mt-2">
                        <small class="text-muted d-block mb-1">Current Hero Image:</small>
                        <img src="<?php echo e(asset('storage/' . $settings['hero_image'])); ?>" style="max-height: 80px; border-radius: 8px;">
                    </div>
                <?php endif; ?>
            </div>

            <!-- Stats -->
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Stat 1 (Value & Label)</label>
                <input type="text" class="form-control mb-1 fw-bold" name="hero_stat_1_val" value="<?php echo e(old('hero_stat_1_val', $settings['hero_stat_1_val'] ?? '15k+')); ?>">
                <input type="text" class="form-control" name="hero_stat_1_lbl" value="<?php echo e(old('hero_stat_1_lbl', $settings['hero_stat_1_lbl'] ?? 'Satisfied Clients')); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Stat 2 (Value & Label)</label>
                <input type="text" class="form-control mb-1 fw-bold" name="hero_stat_2_val" value="<?php echo e(old('hero_stat_2_val', $settings['hero_stat_2_val'] ?? '4.9 ★')); ?>">
                <input type="text" class="form-control" name="hero_stat_2_lbl" value="<?php echo e(old('hero_stat_2_lbl', $settings['hero_stat_2_lbl'] ?? 'Average Rating')); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Stat 3 (Value & Label)</label>
                <input type="text" class="form-control mb-1 fw-bold" name="hero_stat_3_val" value="<?php echo e(old('hero_stat_3_val', $settings['hero_stat_3_val'] ?? '100%')); ?>">
                <input type="text" class="form-control" name="hero_stat_3_lbl" value="<?php echo e(old('hero_stat_3_lbl', $settings['hero_stat_3_lbl'] ?? 'Certified Hygiene')); ?>">
            </div>
        </div>
    </div>
</div>

<!-- Services & Dual Experience Showcase -->
<div class="settings-section-card mb-4">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-cut"></i></div>
        <h6>Services & Dual Experience (Barbershop & Salon)</h6>
    </div>

    <div class="settings-field-group mb-3">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Services Menu Section Title</label>
                <input type="text" class="form-control" name="services_title"
                    value="<?php echo e(old('services_title', $settings['services_title'] ?? 'Signature Grooming & Styling Menu')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Services Menu Subtitle</label>
                <input type="text" class="form-control" name="services_subtitle"
                    value="<?php echo e(old('services_subtitle', $settings['services_subtitle'] ?? 'Indulge in precision haircutting, classic straight razor shaves, bespoke color formulations, and restorative therapies.')); ?>">
            </div>

            <!-- Barbershop Card -->
            <div class="col-md-6">
                <div class="p-3 rounded border bg-light">
                    <label class="form-label fw-bold text-dark"><i class="fas fa-cut text-warning me-1"></i> Barbershop Experience Title</label>
                    <input type="text" class="form-control mb-2" name="barber_heading"
                        value="<?php echo e(old('barber_heading', $settings['barber_heading'] ?? "The Gentlemen's Barber Lounge")); ?>">
                    <label class="form-label small text-secondary">Barbershop Description</label>
                    <textarea class="form-control" name="barber_description" rows="3"><?php echo e(old('barber_description', $settings['barber_description'] ?? 'Classic hot-towel straight razor shaves, skin fades, precision beard sculpting, and revitalizing scalp rituals.')); ?></textarea>
                </div>
            </div>

            <!-- Salon Card -->
            <div class="col-md-6">
                <div class="p-3 rounded border bg-light">
                    <label class="form-label fw-bold text-dark"><i class="fas fa-spa text-primary me-1"></i> Salon Experience Title</label>
                    <input type="text" class="form-control mb-2" name="salon_heading"
                        value="<?php echo e(old('salon_heading', $settings['salon_heading'] ?? 'The Haute Hair & Beauty Salon')); ?>">
                    <label class="form-label small text-secondary">Salon Description</label>
                    <textarea class="form-control" name="salon_description" rows="3"><?php echo e(old('salon_description', $settings['salon_description'] ?? 'Bespoke hair design, dimensional balayage, keratin smoothing, and restorative botanical hair therapies.')); ?></textarea>
                </div>
            </div>

            <!-- Why Choose Us -->
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Why Choose Us Headline</label>
                <input type="text" class="form-control" name="why_title"
                    value="<?php echo e(old('why_title', $settings['why_title'] ?? 'The Standard of Excellence')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Why Choose Us Subtitle</label>
                <input type="text" class="form-control" name="why_subtitle"
                    value="<?php echo e(old('why_subtitle', $settings['why_subtitle'] ?? 'Every ritual is crafted with obsessive attention to detail, uncompromising hygiene, and VIP hospitality.')); ?>">
            </div>

            <!-- CTA Banner -->
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Bottom CTA Banner Headline</label>
                <input type="text" class="form-control" name="cta_banner_title"
                    value="<?php echo e(old('cta_banner_title', $settings['cta_banner_title'] ?? 'Ready for Your Signature Style?')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Bottom CTA Banner Subtitle</label>
                <input type="text" class="form-control" name="cta_banner_subtitle"
                    value="<?php echo e(old('cta_banner_subtitle', $settings['cta_banner_subtitle'] ?? 'Reserve your appointment with our master barbers and visionary stylists today.')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Bottom CTA Button Text</label>
                <input type="text" class="form-control" name="cta_banner_btn_text"
                    value="<?php echo e(old('cta_banner_btn_text', $settings['cta_banner_btn_text'] ?? 'Book Your Appointment')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Bottom CTA Button URL</label>
                <input type="text" class="form-control" name="cta_banner_btn_url"
                    value="<?php echo e(old('cta_banner_btn_url', $settings['cta_banner_btn_url'] ?? '/register-salon')); ?>">
            </div>
        </div>
    </div>
</div>

<!-- Contact & Socials -->
<div class="settings-section-card mb-4">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-map-marked-alt"></i></div>
        <h6>Contact Information & Social Channels</h6>
    </div>

    <div class="settings-field-group mb-3">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Contact Phone</label>
                <input type="text" class="form-control" name="contact_phone"
                    value="<?php echo e(old('contact_phone', $settings['contact_phone'] ?? '+974 66266581')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Contact Email</label>
                <input type="email" class="form-control" name="contact_email"
                    value="<?php echo e(old('contact_email', $settings['contact_email'] ?? 'info@fsqatar.com')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Studio Address</label>
                <input type="text" class="form-control" name="contact_address"
                    value="<?php echo e(old('contact_address', $settings['contact_address'] ?? 'Salwa Road, Doha, State of Qatar')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Working Hours</label>
                <input type="text" class="form-control" name="contact_working_hours"
                    value="<?php echo e(old('contact_working_hours', $settings['contact_working_hours'] ?? 'Mon – Sun: 8:00 AM – 9:00 PM')); ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Google Maps Embed URL / Iframe</label>
                <input type="text" class="form-control" name="contact_map_iframe"
                    value="<?php echo e(old('contact_map_iframe', $settings['contact_map_iframe'] ?? '')); ?>"
                    placeholder="https://maps.google.com/maps?q=... or full iframe snippet">
            </div>

            <!-- Socials -->
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-uppercase text-secondary"><i class="fab fa-facebook-f text-primary me-1"></i>Facebook</label>
                <input type="url" class="form-control" name="social_facebook" value="<?php echo e(old('social_facebook', $settings['social_facebook'] ?? 'https://facebook.com')); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-uppercase text-secondary"><i class="fab fa-instagram text-danger me-1"></i>Instagram</label>
                <input type="url" class="form-control" name="social_instagram" value="<?php echo e(old('social_instagram', $settings['social_instagram'] ?? 'https://instagram.com')); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-uppercase text-secondary"><i class="fab fa-twitter text-info me-1"></i>Twitter / X</label>
                <input type="url" class="form-control" name="social_twitter" value="<?php echo e(old('social_twitter', $settings['social_twitter'] ?? 'https://twitter.com')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary"><i class="fab fa-linkedin-in text-primary me-1"></i>LinkedIn</label>
                <input type="url" class="form-control" name="social_linkedin" value="<?php echo e(old('social_linkedin', $settings['social_linkedin'] ?? 'https://linkedin.com')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary"><i class="fab fa-youtube text-danger me-1"></i>YouTube</label>
                <input type="url" class="form-control" name="social_youtube" value="<?php echo e(old('social_youtube', $settings['social_youtube'] ?? '')); ?>">
            </div>
        </div>
    </div>
</div>

<!-- About Us Page Content -->
<div class="settings-section-card mb-4">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-info-circle"></i></div>
        <h6>About Us Page Story & Mission</h6>
    </div>

    <div class="settings-field-group mb-3">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label fw-semibold small text-uppercase text-secondary">About Page Hero Title</label>
                <input type="text" class="form-control" name="about_hero_title"
                    value="<?php echo e(old('about_hero_title', $settings['about_hero_title'] ?? 'Where Timeless Craftsmanship Meets Modern Luxury')); ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold small text-uppercase text-secondary">About Page Hero Subtitle</label>
                <textarea class="form-control" name="about_hero_subtitle" rows="2"><?php echo e(old('about_hero_subtitle', $settings['about_hero_subtitle'] ?? 'Dedicated to elevating the grooming and styling ritual for gentlemen and ladies who demand distinction, artistry, and VIP hospitality.')); ?></textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Story Headline</label>
                <input type="text" class="form-control" name="about_story_title"
                    value="<?php echo e(old('about_story_title', $settings['about_story_title'] ?? 'Our Heritage & Philosophy')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Story Paragraph 1</label>
                <textarea class="form-control" name="about_story_p1" rows="4"><?php echo e(old('about_story_p1', $settings['about_story_p1'] ?? 'Born out of a genuine passion for grooming and artistic flair, our sanctuary bridges classic barbering traditions with cutting-edge salon innovations. From handcrafted hot-towel straight razor shaves to bespoke color formulation and restorative hair treatments, every touchpoint is designed to renew and inspire confidence.')); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Story Paragraph 2</label>
                <textarea class="form-control" name="about_story_p2" rows="4"><?php echo e(old('about_story_p2', $settings['about_story_p2'] ?? 'We believe true grooming is an art form. Our masters and stylists undergo continuous training in modern techniques, hygiene standards, and client satisfaction to ensure that whether you are in for a quick beard trim or a total transformation, you leave looking and feeling your absolute best.')); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Mission Statement</label>
                <textarea class="form-control" name="about_mission" rows="3"><?php echo e(old('about_mission', $settings['about_mission'] ?? 'To deliver unmatched styling and grooming experiences through master craftsmanship, premium products, and frictionless technology, empowering every client to look confident and feel renewed.')); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Vision Statement</label>
                <textarea class="form-control" name="about_vision" rows="3"><?php echo e(old('about_vision', $settings['about_vision'] ?? 'To stand as the gold standard in premier barber and salon care—a community-centric haven where heritage craftsmanship meets contemporary elegance.')); ?></textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold small text-uppercase text-secondary">About Page Custom Image</label>
                <input type="file" class="form-control" name="about_image" accept="image/*">
                <?php if(!empty($settings['about_image'])): ?>
                    <div class="mt-2">
                        <small class="text-muted d-block mb-1">Current About Image:</small>
                        <img src="<?php echo e(asset('storage/' . $settings['about_image'])); ?>" style="max-height: 80px; border-radius: 8px;">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Legal Pages -->
<div class="settings-section-card mb-4">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-file-contract"></i></div>
        <h6>Legal Policies (Terms & Conditions / Privacy Policy)</h6>
    </div>

    <div class="settings-field-group mb-3">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Terms & Conditions Content</label>
                <textarea class="form-control font-monospace small" name="terms_content" rows="6" placeholder="Leave blank to use default professional salon & barber terms"><?php echo e(old('terms_content', $settings['terms_content'] ?? '')); ?></textarea>
                <small class="text-muted">Supports HTML or multi-paragraph text.</small>
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold small text-uppercase text-secondary">Privacy Policy Content</label>
                <textarea class="form-control font-monospace small" name="privacy_content" rows="6" placeholder="Leave blank to use default GDPR-compliant privacy policy"><?php echo e(old('privacy_content', $settings['privacy_content'] ?? '')); ?></textarea>
                <small class="text-muted">Supports HTML or multi-paragraph text.</small>
            </div>
        </div>
    </div>
</div>

<?php /**PATH C:\Ketan\projects\barber\resources\views/admin/settings/partials/website_cms.blade.php ENDPATH**/ ?>