<!-- Footer Section -->
<footer class="pub-footer">
    <div class="pub-container">
        <div class="pub-footer-grid">
            <!-- Brand & Philosophy Column -->
            <?php
                $footerLogo = !empty($appLogo) ? $appLogo : app(\App\Services\SettingsService::class)->getLogoUrl();
            ?>
            <div>
                <a href="<?php echo e(route('home')); ?>" style="display: inline-flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; text-decoration: none;">
                    <?php if(!empty($footerLogo)): ?>
                        <div style="background: rgba(255, 255, 255, 0.08); padding: 0.35rem 0.75rem; border-radius: 0.5rem; display: inline-flex; align-items: center; border: 1px solid rgba(255, 255, 255, 0.1);">
                            <img src="<?php echo e($footerLogo); ?>" alt="<?php echo e($appName); ?>" style="height: 2.25rem; width: auto; object-fit: contain;">
                        </div>
                    <?php else: ?>
                        <div class="pub-brand-badge">
                            <?php echo e(substr($appName ?? 'FS', 0, 1)); ?>

                        </div>
                        <span class="pub-footer-brand-title" style="margin-bottom: 0;"><?php echo e($appName); ?></span>
                    <?php endif; ?>
                </a>
                <p class="pub-footer-brand-desc">
                    The premier destination bridging traditional master barbering with contemporary luxury salon care. Precision fades, bespoke color, restorative treatments, and seamless digital booking.
                </p>

                <!-- Working Hours Badge -->
                <?php if(!empty($cms['contactHours'])): ?>
                    <div style="display: inline-flex; align-items: center; gap: 0.65rem; padding: 0.45rem 1rem; border-radius: 9999px; background: #131926; border: 1px solid #1f293d; font-size: 0.75rem; color: #cbd5e1; margin-bottom: 1.25rem;">
                        <span style="width: 0.5rem; height: 0.5rem; border-radius: 50%; background: #10b981;"></span>
                        <span style="font-weight: 700; color: #94a3b8;">Hours:</span>
                        <span style="color: #ffffff; font-weight: 600;"><?php echo e($cms['contactHours']); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Social Links -->
                <div class="pub-social-row">
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
                    <?php if(!empty($cms['socialYoutube'])): ?>
                        <a href="<?php echo e($cms['socialYoutube']); ?>" target="_blank" rel="noopener noreferrer" class="pub-social-btn" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Experience Links -->
            <div>
                <h4 class="pub-footer-col-title">Experience</h4>
                <ul class="pub-footer-links">
                    <li><a href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li><a href="<?php echo e(route('home')); ?>#services-experience">Barbershop & Salon</a></li>
                    <li><a href="<?php echo e(route('home')); ?>#services-menu">Services & Pricing</a></li>
                    <li><a href="<?php echo e(route('home')); ?>#the-craft">The Craft & Standards</a></li>
                    <li><a href="<?php echo e($cms['heroPrimaryBtnUrl'] ?? route('saas.register')); ?>">Book Appointment</a></li>
                </ul>
            </div>

            <!-- Studio & Legal Links -->
            <div>
                <h4 class="pub-footer-col-title">Studio</h4>
                <ul class="pub-footer-links">
                    <li><a href="<?php echo e(route('about')); ?>">Our Story & Heritage</a></li>
                    <li><a href="<?php echo e(route('contact')); ?>">Concierge & Location</a></li>
                    <li><a href="<?php echo e(route('terms')); ?>">Terms & Conditions</a></li>
                    <li><a href="<?php echo e(route('privacy')); ?>">Privacy Policy</a></li>
                    <li><a href="<?php echo e(route('login')); ?>">Staff & Client Sign In</a></li>
                </ul>
            </div>

            <!-- Direct Concierge / Reach Us -->
            <div>
                <h4 class="pub-footer-col-title">Concierge</h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php if(!empty($cms['contactPhone'])): ?>
                        <a href="tel:<?php echo e(preg_replace('/[^0-9+]/', '', $cms['contactPhone'])); ?>" style="display: flex; align-items: flex-start; gap: 0.75rem; color: #cbd5e1; text-decoration: none;">
                            <i class="fas fa-phone-alt" style="color: #c5a880; margin-top: 0.25rem;"></i>
                            <div>
                                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; font-weight: 700;">Direct Line</div>
                                <div style="font-weight: 700; color: #ffffff; font-size: 0.95rem;"><?php echo e($cms['contactPhone']); ?></div>
                            </div>
                        </a>
                    <?php endif; ?>

                    <?php if(!empty($cms['contactEmail'])): ?>
                        <a href="mailto:<?php echo e($cms['contactEmail']); ?>" style="display: flex; align-items: flex-start; gap: 0.75rem; color: #cbd5e1; text-decoration: none;">
                            <i class="fas fa-envelope" style="color: #c5a880; margin-top: 0.25rem;"></i>
                            <div>
                                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; font-weight: 700;">Reservations</div>
                                <div style="font-weight: 600; color: #ffffff; font-size: 0.9rem;"><?php echo e($cms['contactEmail']); ?></div>
                            </div>
                        </a>
                    <?php endif; ?>

                    <?php if(!empty($cms['contactAddress'])): ?>
                        <div style="display: flex; align-items: flex-start; gap: 0.75rem; color: #cbd5e1;">
                            <i class="fas fa-map-marker-alt" style="color: #c5a880; margin-top: 0.25rem;"></i>
                            <div>
                                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; font-weight: 700;">Location</div>
                                <div style="font-size: 0.85rem; color: #94a3b8; line-height: 1.5;"><?php echo e($cms['contactAddress']); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="pub-footer-bottom">
            <div>
                &copy; <?php echo e(date('Y')); ?> <?php echo e($cms['appName'] ?? $appName); ?>. All rights reserved.
            </div>
            <div class="pub-legal-links">
                <a href="<?php echo e(route('terms')); ?>">Terms</a>
                <span>&bull;</span>
                <a href="<?php echo e(route('privacy')); ?>">Privacy</a>
                <span>&bull;</span>
                <a href="<?php echo e(route('contact')); ?>">Support</a>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH C:\Ketan\projects\barber\resources\views/partials/saas-footer.blade.php ENDPATH**/ ?>