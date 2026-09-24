<?php $__env->startSection('content'); ?>
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-8">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Subscription Expired</h2>
            <p class="text-gray-600 mb-6">
                Your subscription has expired. Please renew your subscription to continue using our services.
            </p>

            <?php if(auth()->check() && auth()->user()->salon): ?>
                <div class="space-y-3">
                    <a href="<?php echo e(route('admin.saas.subscription.index', ['salon_slug' => auth()->user()->salon->slug])); ?>" 
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                        Renew Subscription
                    </a>
                    
                    <a href="<?php echo e(route('admin.saas.subscription.index', ['salon_slug' => auth()->user()->salon->slug])); ?>" 
                       class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-lg transition duration-200">
                        View Plans
                    </a>
                </div>

                <div class="mt-6 text-sm text-gray-500">
                    Need help? <a href="mailto:support@example.com" class="text-blue-600 hover:underline">Contact Support</a>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <a href="<?php echo e(route('login')); ?>" 
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                        Login to Renew
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\saas\subscription\expired.blade.php ENDPATH**/ ?>