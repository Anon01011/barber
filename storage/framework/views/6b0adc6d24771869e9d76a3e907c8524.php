<!-- Navbar -->
<nav class="fixed w-full z-50 glass-nav transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="flex items-center">
                <a href="<?php echo e(route('home')); ?>">
                    <?php if($appLogo): ?>
                        <img src="<?php echo e($appLogo); ?>" alt="<?php echo e($appName); ?>" class="h-12 w-auto object-contain">
                    <?php else: ?>
                        <span class="font-bold text-2xl tracking-tight text-slate-900"><?php echo e($appName); ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <div class="hidden md:flex items-center space-x-8">
                <a href="<?php echo e(route('home')); ?>#features"
                    class="text-sm font-semibold text-slate-600 hover:text-purple-600 transition-colors">Features</a>
                <a href="<?php echo e(route('home')); ?>#how-it-works"
                    class="text-sm font-semibold text-slate-600 hover:text-purple-600 transition-colors">How it
                    Works</a>
                <a href="<?php echo e(route('home')); ?>#pricing"
                    class="text-sm font-semibold text-slate-600 hover:text-purple-600 transition-colors">Pricing</a>
                <div class="h-6 w-px bg-slate-200"></div>
                <a href="<?php echo e(route('login')); ?>"
                    class="text-sm font-semibold text-slate-600 hover:text-purple-600 transition-colors">Log in</a>
                <a href="<?php echo e(route('saas.register')); ?>"
                    class="px-6 py-3 rounded-full bg-slate-900 text-white text-sm font-bold hover:bg-slate-800 transition-all transform hover:-translate-y-0.5 shadow-lg shadow-slate-900/20">
                    Get Started
                </a>
            </div>
            <!-- Mobile Menu Button -->
            <button class="md:hidden text-slate-600 p-2" id="mobile-menu-btn">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
        </div>
    </div>
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-slate-100 p-4 absolute w-full shadow-lg">
        <div class="flex flex-col space-y-4">
            <a href="<?php echo e(route('home')); ?>#features" class="text-slate-600 font-semibold p-2">Features</a>
            <a href="<?php echo e(route('home')); ?>#how-it-works" class="text-slate-600 font-semibold p-2">How it Works</a>
            <a href="<?php echo e(route('home')); ?>#pricing" class="text-slate-600 font-semibold p-2">Pricing</a>
            <a href="<?php echo e(route('login')); ?>" class="text-slate-600 font-semibold p-2">Log in</a>
            <a href="<?php echo e(route('saas.register')); ?>"
                class="text-center px-5 py-3 rounded-full bg-slate-900 text-white font-bold">Get Started</a>
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Navbar scroll effect
    window.addEventListener('scroll', () => {
        const nav = document.querySelector('nav');
        if (window.scrollY > 20) {
            nav.classList.add('shadow-md');
        } else {
            nav.classList.remove('shadow-md');
        }
    });
</script><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\partials\saas-header.blade.php ENDPATH**/ ?>