<!-- Footer -->
<footer class="bg-slate-950 text-slate-300 pt-20 pb-10 border-t border-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-12 mb-16">
            <div class="col-span-1 md:col-span-2">
                <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3 mb-6">
                    <?php if($appLogo): ?>
                        <img src="<?php echo e($appLogo); ?>" alt="<?php echo e($appName); ?>" class="h-8 w-auto brightness-0 invert">
                    <?php else: ?>
                        <span class="font-bold text-2xl text-white tracking-tight"><?php echo e($appName); ?></span>
                    <?php endif; ?>
                </a>
                <p class="text-slate-400 leading-relaxed max-w-sm mb-6">
                    The ultimate management solution for forward-thinking salons. Beautifully designed, powerfully
                    built, and easy to use.
                </p>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 text-slate-400">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                            </path>
                        </svg>
                        <span>+974 66266581</span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-400">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span>info@fsqatar.com</span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-400">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Doha, Qatar</span>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="text-white font-bold mb-6">Product</h4>
                <ul class="space-y-4">
                    <li><a href="<?php echo e(route('home')); ?>#features" class="hover:text-white transition-colors">Features</a>
                    </li>
                    <li><a href="<?php echo e(route('home')); ?>#how-it-works" class="hover:text-white transition-colors">How it
                            Works</a></li>
                    <li><a href="<?php echo e(route('home')); ?>#pricing" class="hover:text-white transition-colors">Pricing</a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-6">Company</h4>
                <ul class="space-y-4">
                    <li><a href="<?php echo e(route('about')); ?>" class="hover:text-white transition-colors">About Us</a></li>
                    <li><a href="<?php echo e(route('contact')); ?>" class="hover:text-white transition-colors">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-slate-500">© <?php echo e(date('Y')); ?> <?php echo e($appName); ?>. All rights reserved.</p>
        </div>
    </div>
</footer><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\partials\saas-footer.blade.php ENDPATH**/ ?>