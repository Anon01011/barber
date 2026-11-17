/**
 * Public Pages Interactive Scripts
 * Handles mobile navigation, sticky header transitions, and service category filtering
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile Menu Drawer Toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuBars = document.getElementById('menu-icon-bars');
    const menuClose = document.getElementById('menu-icon-close');

    if (mobileMenuBtn && mobileMenu) {
        function openMenu() {
            mobileMenu.classList.add('active');
            if (menuBars) menuBars.style.display = 'none';
            if (menuClose) menuClose.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            mobileMenu.classList.remove('active');
            if (menuBars) menuBars.style.display = 'block';
            if (menuClose) menuClose.style.display = 'none';
            document.body.style.overflow = '';
        }

        mobileMenuBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (mobileMenu.classList.contains('active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        // Close on backdrop click
        mobileMenu.addEventListener('click', function (e) {
            if (e.target === mobileMenu) {
                closeMenu();
            }
        });

        // Close on escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
                closeMenu();
            }
        });

        // Close when clicking any nav link inside mobile menu
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });
    }

    // 2. Sticky Header Elevation on Scroll
    const header = document.getElementById('site-header');
    if (header) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 20) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }, { passive: true });
    }

    // 3. Service Category Filtering
    const tabButtons = document.querySelectorAll('.pub-tab-pill');
    const serviceCards = document.querySelectorAll('.pub-service-card');

    if (tabButtons.length > 0 && serviceCards.length > 0) {
        tabButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const targetCat = this.getAttribute('data-category');

                tabButtons.forEach(function (b) {
                    b.classList.remove('active');
                });
                this.classList.add('active');

                serviceCards.forEach(function (card) {
                    const cardCat = card.getAttribute('data-category');
                    if (targetCat === 'all' || cardCat === targetCat) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }

    // 4. Smooth Anchor Scrolling
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId !== '#') {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
});
