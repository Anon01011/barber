<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(app(\App\Services\SettingsService::class)->get('app_name', config('app.name', 'Laravel'))); ?> - Dashboard
    </title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(app(\App\Services\SettingsService::class)->getFaviconUrl()); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen" x-data="{ sidebarOpen: true }">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 bg-white shadow-lg max-h-screen w-64 transition-transform duration-300 transform"
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            <div class="flex items-center justify-center h-16 border-b">
                <img src="<?php echo e(app(\App\Services\SettingsService::class)->getLogoUrl()); ?>" alt="Logo" class="h-8">
            </div>
            <nav class="mt-5 px-2">
                <?php if(auth()->user()->hasRole('super_admin')): ?>
                    <a href="<?php echo e(route('dashboard')); ?>"
                        class="group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition ease-in-out duration-150">
                        <i class="fas fa-home mr-3 text-gray-500 group-hover:text-gray-500"></i>
                        Dashboard
                    </a>
                    <a href="<?php echo e(route('admin.appointments.index')); ?>"
                        class="group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition ease-in-out duration-150">
                        <i class="fas fa-calendar-alt mr-3 text-gray-500 group-hover:text-gray-500"></i>
                        Appointments
                    </a>
                    <a href="<?php echo e(route('admin.pos.index')); ?>"
                        class="group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition ease-in-out duration-150">
                        <i class="fas fa-cash-register mr-3 text-gray-500 group-hover:text-gray-500"></i>
                        POS
                    </a>
                    <a href="<?php echo e(route('admin.services.index')); ?>"
                        class="group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition ease-in-out duration-150">
                        <i class="fas fa-cut mr-3 text-gray-500 group-hover:text-gray-500"></i>
                        Services
                    </a>
                    <a href="<?php echo e(route('admin.inventory.index')); ?>"
                        class="group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition ease-in-out duration-150">
                        <i class="fas fa-boxes mr-3 text-gray-500 group-hover:text-gray-500"></i>
                        Inventory
                    </a>
                    <a href="<?php echo e(route('admin.staff.index')); ?>"
                        class="group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition ease-in-out duration-150">
                        <i class="fas fa-users mr-3 text-gray-500 group-hover:text-gray-500"></i>
                        Staff
                    </a>
                    <a href="<?php echo e(route('admin.roles.index')); ?>"
                        class="group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition ease-in-out duration-150">
                        <i class="fas fa-user-shield mr-3 text-gray-500 group-hover:text-gray-500"></i>
                        Roles & Permissions
                    </a>
                    <a href="<?php echo e(route('settings.index')); ?>"
                        class="group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition ease-in-out duration-150">
                        <i class="fas fa-cog mr-3 text-gray-500 group-hover:text-gray-500"></i>
                        Settings
                    </a>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col" :class="{'lg:ml-64': sidebarOpen}">
            <!-- Top Navigation -->
            <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:bg-gray-100 focus:text-gray-600 lg:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="flex-1 px-4 flex justify-between">
                    <div class="flex-1 flex">
                        <h1 class="text-2xl font-semibold text-gray-900 my-auto"><?php echo $__env->yieldContent('title'); ?></h1>
                    </div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <!-- Profile dropdown -->
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open"
                                    class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:shadow-outline">
                                    <img class="h-8 w-8 rounded-full"
                                        src="<?php echo e(auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name)); ?>"
                                        alt="<?php echo e(auth()->user()->name); ?>">
                                </button>
                            </div>
                            <div x-show="open" @click.away="open = false"
                                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg">
                                <div class="py-1 rounded-md bg-white shadow-xs">
                                    <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                        <div class="font-medium"><?php echo e(auth()->user()->name); ?></div>
                                        <div class="text-gray-500"><?php echo e(auth()->user()->email); ?></div>
                                    </div>
                                    <a href="<?php echo e(route('profile.edit')); ?>"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user-cog mr-2"></i> Profile Settings
                                    </a>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <?php if(isset($header)): ?>
                            <header class="bg-white shadow">
                                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                    <?php echo e($header); ?>

                                </div>
                            </header>
                        <?php endif; ?>

                        <!-- Page Heading -->
                        <?php if(isset($header)): ?>
                            <header class="bg-white shadow">
                                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                    <?php echo e($header); ?>

                                </div>
                            </header>
                        <?php endif; ?>

                        <!-- Flash Messages -->


                        <!-- Main Content -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <?php echo $__env->yieldContent('content'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php echo $__env->make('components.notifications', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\layouts\dashboard.blade.php ENDPATH**/ ?>