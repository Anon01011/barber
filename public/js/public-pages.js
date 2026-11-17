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

    // 5. Plans Carousel Dot Synchronization
    const plansTrack = document.getElementById('plans-track');
    const dots = document.querySelectorAll('.pub-carousel-dot');
    if (plansTrack && dots.length > 0) {
        plansTrack.addEventListener('scroll', function () {
            const scrollLeft = plansTrack.scrollLeft;
            const cardWidth = (plansTrack.querySelector('.pub-plan-card')?.offsetWidth || 340) + 32;
            const activeIndex = Math.min(Math.round(scrollLeft / cardWidth), dots.length - 1);
            dots.forEach(function (dot, idx) {
                if (idx === activeIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }, { passive: true });
    }
});

// Global FAQ Accordion Toggle
window.toggleFaq = function (index) {
    const item = document.getElementById('faq-item-' + index);
    if (!item) return;

    const answer = item.querySelector('.pub-faq-answer');
    const trigger = item.querySelector('.pub-faq-trigger');
    const isActive = item.classList.contains('active');

    // Close other open items for clean accordion UX
    document.querySelectorAll('.pub-faq-item').forEach(function (other) {
        if (other !== item && other.classList.contains('active')) {
            other.classList.remove('active');
            const otherAns = other.querySelector('.pub-faq-answer');
            const otherTrig = other.querySelector('.pub-faq-trigger');
            if (otherAns) {
                otherAns.style.maxHeight = '0px';
                otherAns.style.opacity = '0';
                otherAns.style.paddingTop = '0';
            }
            if (otherTrig) otherTrig.setAttribute('aria-expanded', 'false');
        }
    });

    if (isActive) {
        item.classList.remove('active');
        answer.style.maxHeight = '0px';
        answer.style.opacity = '0';
        answer.style.paddingTop = '0';
        trigger.setAttribute('aria-expanded', 'false');
    } else {
        item.classList.add('active');
        answer.style.maxHeight = (answer.scrollHeight + 40) + 'px';
        answer.style.opacity = '1';
        answer.style.paddingTop = '1rem';
        trigger.setAttribute('aria-expanded', 'true');
    }
};

// Global Plans Carousel Slider Control
window.slidePlans = function (direction) {
    const track = document.getElementById('plans-track');
    if (!track) return;

    const cards = track.querySelectorAll('.pub-plan-card');
    if (cards.length === 0) return;

    const cardWidth = cards[0].offsetWidth + 32;
    track.scrollBy({
        left: direction * cardWidth,
        behavior: 'smooth'
    });
};

window.jumpToPlan = function (index) {
    const track = document.getElementById('plans-track');
    if (!track) return;
    const cards = track.querySelectorAll('.pub-plan-card');
    if (cards[index]) {
        cards[index].scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'center'
        });
    }
};
