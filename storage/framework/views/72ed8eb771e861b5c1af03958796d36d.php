<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Us - <?php echo e($appName); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }

        .gradient-text {
            background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 overflow-x-hidden selection:bg-purple-500 selection:text-white">

    <?php echo $__env->make('partials.saas-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Compact Hero Section -->
    <section class="relative pt-32 pb-16 lg:pt-40 lg:pb-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl lg:text-6xl font-bold text-slate-900 leading-tight mb-6 tracking-tight">
                    The Story Behind <span class="gradient-text"><?php echo e($appName); ?></span>
                </h1>
                <p class="text-lg text-slate-600 leading-relaxed">
                    We're redefining salon management with technology that feels like magic. Our mission is to empower beauty professionals to focus on their art while we handle the rest.
                </p>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="relative">
                    <div class="rounded-3xl overflow-hidden shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Modern Salon" class="w-full h-auto">
                    </div>
                    <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-purple-600 rounded-3xl -z-10 opacity-20 blur-2xl"></div>
                </div>
                <div class="space-y-10">
                    <div>
                        <h2 class="text-3xl font-bold text-slate-900 mb-4">Our Mission</h2>
                        <p class="text-slate-600 leading-relaxed">
                            To provide the world's most intuitive and powerful platform for salon owners, enabling them to grow their business, delight their customers, and reclaim their time.
                        </p>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-slate-900 mb-4">Our Vision</h2>
                        <p class="text-slate-600 leading-relaxed">
                            To become the global standard for beauty industry management, fostering a community where creativity and business efficiency coexist seamlessly.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900">Our Core Values</h2>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Innovation</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">We push boundaries to bring the latest tech to your fingertips.</p>
                </div>
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Empathy</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">We listen to our users and build solutions that truly matter.</p>
                </div>
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Integrity</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Transparency and trust are the foundation of our company.</p>
                </div>
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Simplicity</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">We believe that complex problems deserve simple solutions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SaaS Platform Features -->
    <section class="py-20 bg-slate-900 text-white overflow-hidden relative">
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-600/20 blur-3xl rounded-full -mr-48 -mt-48"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold mb-6">Built for the Modern Salon</h2>
                    <p class="text-slate-400 text-lg mb-8">
                        <?php echo e($appName); ?> isn't just a booking tool. It's a complete operating system designed to scale with your business, whether you have one chair or a hundred locations.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center flex-shrink-0">✓</div>
                            <span class="text-slate-300">AI-Powered Smart Scheduling</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center flex-shrink-0">✓</div>
                            <span class="text-slate-300">Integrated POS & Inventory Management</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center flex-shrink-0">✓</div>
                            <span class="text-slate-300">Automated Marketing & Loyalty Programs</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center flex-shrink-0">✓</div>
                            <span class="text-slate-300">Real-time Analytics & Financial Reporting</span>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="rounded-3xl overflow-hidden shadow-2xl border border-slate-800">
                        <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="SaaS Dashboard" class="w-full h-auto opacity-80">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Journey (Timeline) -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900">Our Journey</h2>
            </div>
            <div class="max-w-4xl mx-auto">
                <div class="space-y-12">
                    <div class="flex gap-8 items-start">
                        <div class="flex-shrink-0 w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center font-bold text-slate-900">2021</div>
                        <div>
                            <h4 class="text-xl font-bold text-slate-900 mb-2">The Spark</h4>
                            <p class="text-slate-600 leading-relaxed">Founded with a vision to solve the scheduling chaos in local salons.</p>
                        </div>
                    </div>
                    <div class="flex gap-8 items-start">
                        <div class="flex-shrink-0 w-16 h-16 rounded-2xl bg-purple-100 flex items-center justify-center font-bold text-purple-600">2022</div>
                        <div>
                            <h4 class="text-xl font-bold text-slate-900 mb-2">Going Global</h4>
                            <p class="text-slate-600 leading-relaxed">Expanded our platform to support multi-location salon chains across Europe.</p>
                        </div>
                    </div>
                    <div class="flex gap-8 items-start">
                        <div class="flex-shrink-0 w-16 h-16 rounded-2xl bg-indigo-100 flex items-center justify-center font-bold text-indigo-600">2023</div>
                        <div>
                            <h4 class="text-xl font-bold text-slate-900 mb-2">AI Integration</h4>
                            <p class="text-slate-600 leading-relaxed">Launched our revolutionary AI assistant to automate client communications.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php echo $__env->make('partials.saas-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>

</html><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\about.blade.php ENDPATH**/ ?>