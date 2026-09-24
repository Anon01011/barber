<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $appName }} - The Operating System for Modern Salons</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ app(\App\Services\SettingsService::class)->getFaviconUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Outfit', sans-serif;
        }

        .gradient-text {
            background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-blob {
            position: absolute;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.4;
            animation: blob-bounce 10s infinite ease-in-out;
        }

        @keyframes blob-bounce {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(20px, -20px) scale(1.1);
            }
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -15px rgba(79, 70, 229, 0.1);
        }

        .step-number {
            background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 overflow-x-hidden selection:bg-purple-500 selection:text-white">

    @include('partials.saas-header')

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background Blobs -->
        <div class="hero-blob bg-purple-300 w-96 h-96 rounded-full top-0 right-0 -mr-20 -mt-20 mix-blend-multiply">
        </div>
        <div
            class="hero-blob bg-indigo-300 w-96 h-96 rounded-full bottom-0 left-0 -ml-20 -mb-20 mix-blend-multiply animation-delay-2000">
        </div>
        <div
            class="hero-blob bg-pink-300 w-80 h-80 rounded-full top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 mix-blend-multiply opacity-20">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="text-center lg:text-left">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-purple-100 text-purple-700 text-xs font-bold uppercase tracking-wide mb-8 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-purple-600 animate-pulse"></span>
                        New: AI-Powered Scheduling
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-bold text-slate-900 leading-[1.1] mb-6 tracking-tight">
                        The Operating System for <span class="gradient-text">Modern Salons</span>
                    </h1>
                    <p class="text-lg text-slate-600 mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Streamline bookings, manage staff, track inventory, and grow your business with the all-in-one
                        platform designed for beauty professionals.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('saas.register') }}"
                            class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-200 bg-gradient-to-r from-purple-600 to-indigo-600 border border-transparent rounded-full hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-600 shadow-xl shadow-purple-600/30 hover:-translate-y-1">
                            Start Free Trial
                        </a>
                        <a href="#how-it-works"
                            class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-slate-700 transition-all duration-200 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-200 shadow-sm">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Watch Demo
                        </a>
                    </div>
                    <div
                        class="mt-8 flex items-center justify-center lg:justify-start gap-6 text-sm text-slate-500 font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            No credit card required
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            14-day free trial
                        </div>
                    </div>
                </div>
                <div class="relative perspective-1000">
                    <div
                        class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white transform rotate-y-12 hover:rotate-y-0 transition-all duration-700 ease-out">
                        <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
                            alt="Salon Interior" class="w-full h-auto object-cover">

                        <!-- Floating Card 1 -->
                        <div class="absolute top-8 left-8 bg-white/95 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-white/50 animate-bounce"
                            style="animation-duration: 3s;">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-500 font-bold uppercase tracking-wider">Daily Revenue
                                    </div>
                                    <div class="text-lg font-bold text-slate-900">$2,450.00</div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Card 2 -->
                        <div class="absolute bottom-8 right-8 bg-white/95 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-white/50 animate-bounce"
                            style="animation-duration: 4s; animation-delay: 1s;">
                            <div class="flex items-center gap-3">
                                <div class="flex -space-x-2">
                                    <img class="w-8 h-8 rounded-full border-2 border-white"
                                        src="https://i.pravatar.cc/100?img=1" alt="">
                                    <img class="w-8 h-8 rounded-full border-2 border-white"
                                        src="https://i.pravatar.cc/100?img=2" alt="">
                                    <img class="w-8 h-8 rounded-full border-2 border-white"
                                        src="https://i.pravatar.cc/100?img=3" alt="">
                                </div>
                                <div>
                                    <div class="text-xs text-slate-500 font-bold uppercase tracking-wider">New Bookings
                                    </div>
                                    <div class="text-lg font-bold text-slate-900">+12 Today</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted By -->
    <section class="py-12 border-y border-slate-100 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-8">Trusted by 500+ top salons
                worldwide</p>
            <div
                class="flex flex-wrap justify-center items-center gap-12 opacity-40 grayscale hover:grayscale-0 transition-all duration-500">
                <h3 class="text-2xl font-bold text-slate-800">VOGUE</h3>
                <h3 class="text-2xl font-bold text-slate-800">ELLE</h3>
                <h3 class="text-2xl font-bold text-slate-800">GLAMOUR</h3>
                <h3 class="text-2xl font-bold text-slate-800">COSMOPOLITAN</h3>
                <h3 class="text-2xl font-bold text-slate-800">HARPER'S BAZAAR</h3>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="features" class="py-24 bg-slate-50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-purple-600 font-bold tracking-widest uppercase text-sm mb-3">Features</h2>
                <h3 class="text-4xl font-bold text-slate-900 mb-6 tracking-tight">Everything you need to run your salon.
                </h3>
                <p class="text-lg text-slate-600">Replace your disconnected tools with one powerful platform.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                @if(\App\Helpers\ModuleHelper::appointmentsEnabled())
                    <div
                        class="feature-card bg-white p-8 rounded-3xl shadow-sm border border-slate-100 transition-all duration-300">
                        <div
                            class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Smart Booking</h4>
                        <p class="text-slate-600 leading-relaxed">Drag-and-drop calendar that syncs in real-time. Reduce
                            no-shows with automated SMS reminders.</p>
                    </div>
                @endif

                <!-- Feature 2 -->
                @if(\App\Helpers\ModuleHelper::posEnabled())
                    <div
                        class="feature-card bg-white p-8 rounded-3xl shadow-sm border border-slate-100 transition-all duration-300">
                        <div class="w-14 h-14 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">POS & Payments</h4>
                        <p class="text-slate-600 leading-relaxed">Integrated checkout for services and products. Accept
                            cards, cash, and digital payments seamlessly.</p>
                    </div>
                @endif

                <!-- Feature 3 -->
                @if(\App\Helpers\ModuleHelper::staffEnabled())
                    <div
                        class="feature-card bg-white p-8 rounded-3xl shadow-sm border border-slate-100 transition-all duration-300">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Staff Management</h4>
                        <p class="text-slate-600 leading-relaxed">Manage rosters, track performance, and calculate
                            commissions automatically. Keep your team happy.</p>
                    </div>
                @endif

                <!-- Feature 4 -->
                @if(\App\Helpers\ModuleHelper::inventoryEnabled())
                    <div
                        class="feature-card bg-white p-8 rounded-3xl shadow-sm border border-slate-100 transition-all duration-300">
                        <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Inventory Control</h4>
                        <p class="text-slate-600 leading-relaxed">Track professional and retail stock. Get low stock alerts
                            and manage suppliers efficiently.</p>
                    </div>
                @endif

                <!-- Feature 5 -->
                @if(\App\Helpers\ModuleHelper::marketingEnabled() || \App\Helpers\ModuleHelper::smsEnabled() || \App\Helpers\ModuleHelper::whatsappEnabled())
                    <div
                        class="feature-card bg-white p-8 rounded-3xl shadow-sm border border-slate-100 transition-all duration-300">
                        <div
                            class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Marketing Tools</h4>
                        <p class="text-slate-600 leading-relaxed">Built-in email and SMS marketing to retain clients and
                            fill empty slots in your calendar.</p>
                    </div>
                @endif

                <!-- Feature 6 -->
                @if(\App\Helpers\ModuleHelper::reportsEnabled())
                    <div
                        class="feature-card bg-white p-8 rounded-3xl shadow-sm border border-slate-100 transition-all duration-300">
                        <div
                            class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Analytics & Reports</h4>
                        <p class="text-slate-600 leading-relaxed">Visual dashboards to track revenue, staff performance, and
                            client retention rates.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- How it Works Section -->
    <section id="how-it-works" class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-purple-600 font-bold tracking-widest uppercase text-sm mb-3">Process</h2>
                <h3 class="text-4xl font-bold text-slate-900 mb-6 tracking-tight">How it Works</h3>
                <p class="text-lg text-slate-600">Get your salon up and running in three simple steps.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-12 relative">
                <!-- Connecting Line (Desktop) -->
                <div class="hidden md:block absolute top-1/2 left-0 w-full h-0.5 bg-slate-100 -translate-y-1/2 z-0">
                </div>

                <!-- Step 1 -->
                <div class="relative z-10 text-center">
                    <div
                        class="w-20 h-20 rounded-full bg-white border-4 border-slate-50 shadow-xl flex items-center justify-center mx-auto mb-8 group hover:border-purple-100 transition-all duration-300">
                        <span class="text-3xl font-bold step-number">01</span>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-4">Register Your Salon</h4>
                    <p class="text-slate-600 leading-relaxed">Create your account and set up your salon profile in
                        minutes. Choose a plan that fits your needs.</p>
                </div>

                <!-- Step 2 -->
                <div class="relative z-10 text-center">
                    <div
                        class="w-20 h-20 rounded-full bg-white border-4 border-slate-50 shadow-xl flex items-center justify-center mx-auto mb-8 group hover:border-purple-100 transition-all duration-300">
                        <span class="text-3xl font-bold step-number">02</span>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-4">Configure Services</h4>
                    <p class="text-slate-600 leading-relaxed">Add your services, staff members, and working hours.
                        Customize your booking page to match your brand.</p>
                </div>

                <!-- Step 3 -->
                <div class="relative z-10 text-center">
                    <div
                        class="w-20 h-20 rounded-full bg-white border-4 border-slate-50 shadow-xl flex items-center justify-center mx-auto mb-8 group hover:border-purple-100 transition-all duration-300">
                        <span class="text-3xl font-bold step-number">03</span>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-4">Start Growing</h4>
                    <p class="text-slate-600 leading-relaxed">Open your doors to online bookings. Track your success
                        with real-time analytics and marketing tools.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Feature Section 1 -->
    <section class="py-24 bg-slate-50 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-20 items-center">
                <div class="order-2 lg:order-1">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-slate-200 group">
                        <div
                            class="absolute inset-0 bg-purple-600/10 group-hover:bg-transparent transition-colors duration-300 z-10">
                        </div>
                        <img src="https://images.unsplash.com/photo-1633681926022-84c23e8cb2d6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
                            alt="Booking Calendar"
                            class="w-full h-auto transform group-hover:scale-105 transition-transform duration-700">
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <div
                        class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-6 tracking-tight">Booking made
                        beautiful.</h2>
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        Give your clients the premium experience they deserve with a beautiful online booking page that
                        works 24/7.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                ✓</div>
                            <span class="text-slate-700 font-medium">Mobile-friendly booking page</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                ✓</div>
                            <span class="text-slate-700 font-medium">Accept deposits to secure slots</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                ✓</div>
                            <span class="text-slate-700 font-medium">Automated waitlist management</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Feature Section 2 -->
    <section class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-20 items-center">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-6 tracking-tight">Payments that just
                        work.</h2>
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        Handle transactions with ease. Split payments, sell gift cards, and manage packages all from one
                        screen.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                ✓</div>
                            <span class="text-slate-700 font-medium">Integrated card processing</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                ✓</div>
                            <span class="text-slate-700 font-medium">Automatic daily payouts</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                ✓</div>
                            <span class="text-slate-700 font-medium">Detailed financial reporting</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-slate-200 group">
                        <div
                            class="absolute inset-0 bg-pink-600/10 group-hover:bg-transparent transition-colors duration-300 z-10">
                        </div>
                        <img src="https://images.unsplash.com/photo-1556740758-90de374c12ad?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
                            alt="Payment Terminal"
                            class="w-full h-auto transform group-hover:scale-105 transition-transform duration-700">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-purple-600 font-bold tracking-widest uppercase text-sm mb-3">Pricing</h2>
                <h3 class="text-4xl font-bold text-slate-900 mb-6 tracking-tight">Simple, transparent pricing.</h3>
                <p class="text-lg text-slate-600">Choose the plan that fits your salon's needs. No hidden fees.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @if(isset($plans) && $plans->count() > 0)
                    @foreach($plans as $plan)
                        <div
                            class="relative bg-white rounded-3xl p-8 shadow-lg border transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 {{ $plan->is_popular ? 'border-2 border-purple-500 z-10 scale-105' : 'border-slate-100' }}">
                            @if($plan->is_popular)
                                <div
                                    class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-4 py-1 rounded-full text-xs font-bold tracking-wide shadow-lg uppercase">
                                    Most Popular
                                </div>
                            @endif
                            <h3 class="text-xl font-bold text-slate-900">{{ $plan->name }}</h3>
                            <p class="text-slate-500 text-sm mt-2 h-10">
                                {{ $plan->description ?? 'Perfect for getting started' }}
                            </p>
                            <div class="my-6">
                                <span
                                    class="text-4xl font-bold text-slate-900">{{ system_currency_symbol() }}{{ number_format($plan->price, 0) }}</span>
                                <span class="text-slate-500">/mo</span>
                            </div>
                            <a href="{{ route('saas.register', ['plan_id' => $plan->id]) }}"
                                class="block w-full py-3 px-6 rounded-xl {{ $plan->is_popular ? 'bg-purple-600 text-white shadow-lg shadow-purple-500/30 hover:bg-purple-700' : 'bg-slate-100 text-slate-900 hover:bg-slate-200' }} font-bold text-center transition-all mb-8">
                                {{ $plan->price == 0 ? 'Get Started Free' : 'Start Free Trial' }}
                            </a>
                            <ul class="space-y-4 text-sm text-slate-600">
                                @if($plan->features)
                                    @foreach(is_string($plan->features) ? json_decode($plan->features, true) : $plan->features as $feature)
                                        <li class="flex items-center gap-3">
                                            <div
                                                class="w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0 text-xs">
                                                ✓</div>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-3 text-center py-12 bg-white rounded-3xl border border-slate-200">
                        <p class="text-slate-500">No plans currently available. Please check back later.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 to-purple-900"></div>
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
        </div>

        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6 tracking-tight">Ready to transform your salon?
            </h2>
            <p class="text-xl text-purple-100 mb-10 max-w-2xl mx-auto leading-relaxed">Join thousands of salon owners
                who trust {{ $appName }} to manage their business and grow their revenue.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('saas.register') }}"
                    class="inline-block py-4 px-10 rounded-full bg-white text-slate-900 text-lg font-bold shadow-xl hover:bg-purple-50 hover:scale-105 transition-all duration-300">
                    Get Started for Free
                </a>
                <a href="#"
                    class="inline-block py-4 px-10 rounded-full bg-transparent border border-white text-white text-lg font-bold hover:bg-white/10 transition-all duration-300">
                    Contact Sales
                </a>
            </div>
            <p class="mt-8 text-sm text-purple-200 opacity-60">No credit card required • Cancel anytime • 24/7 Support
            </p>
        </div>
    </section>

    @include('partials.saas-footer')

    <script>
        // Mobile menu toggle
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>

</body>

</html>