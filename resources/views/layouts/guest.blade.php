<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $salon->name ?? config('app.name', 'Salon') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon"
        href="{{ app(\App\Services\SettingsService::class)->getFaviconUrl($salon->id ?? null) }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/welcome.css'])

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Header Button Styles -->
    <style>
        .nav-link,
        .btn-primary,
        .btn-secondary {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 500;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #d63384, #e91e63);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #c02569, #d63384);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(214, 51, 132, 0.2);
        }

        .btn-secondary {
            background: transparent;
            color: #d63384;
            border: 1px solid #d63384;
        }

        .btn-secondary:hover {
            background: #d63384;
            color: white;
            transform: translateY(-1px);
        }

        .nav-link {
            color: #333;
            padding: 0.5rem 0.75rem;
        }

        .nav-link:hover {
            color: #d63384;
            background: rgba(214, 51, 132, 0.05);
        }

        /* Card Styles for Dashboard */
        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: box-shadow 0.2s ease;
        }

        .dashboard-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-confirmed {
            background-color: #10b981;
            color: white;
        }

        .status-pending {
            background-color: #f59e0b;
            color: white;
        }

        /* Image Slider Styles */
        .image-slider {
            position: relative;
            width: 200px;
            height: 60px;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .slider-container {
            display: flex;
            width: 600px;
            /* 3 images * 200px */
            animation: slide 9s infinite;
        }

        .slider-container img {
            width: 200px;
            height: 60px;
            object-fit: cover;
            flex-shrink: 0;
        }

        @keyframes slide {
            0% {
                transform: translateX(0);
            }

            33.33% {
                transform: translateX(-200px);
            }

            66.66% {
                transform: translateX(-400px);
            }

            100% {
                transform: translateX(0);
            }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])

    <!-- Additional Styles -->
    @stack('styles')

    <!-- Integrations -->
    @php
        $settingsService = app(\App\Services\SettingsService::class);
        $googleAnalyticsId = $settingsService->get('google_analytics_id');
        $facebookPixelId = $settingsService->get('facebook_pixel_id');
    @endphp

    @if($googleAnalyticsId)
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '{{ $googleAnalyticsId }}');
        </script>
    @endif

    @if($facebookPixelId)
        <!-- Facebook Pixel -->
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq) return; n = f.fbq = function () {
                    n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
                n.queue = []; t = b.createElement(e); t.async = !0;
                t.src = v; s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $facebookPixelId }}');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id={{ $facebookPixelId }}&ev=PageView&noscript=1" /></noscript>
    @endif
</head>

<body class="antialiased">
    <!-- Header -->
    <header class="bg-white shadow-sm fixed w-full z-50 top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Left: Logo -->
                <div class="flex items-center">
                    @php
                        $logoUrl = app(\App\Services\SettingsService::class)->getLogoUrl($salon->id ?? null);
                        $salonName = \Illuminate\Support\Str::title($salon->name ?? config('app.name', 'Salon Name'));
                    @endphp
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ $logoUrl }}" alt="{{ $salonName }}" class="h-10 w-auto object-contain"
                            onerror="this.style.display='none'; document.getElementById('header-salon-name').style.display='block';">
                        <span id="header-salon-name" class="text-2xl font-playfair text-pink-600"
                            style="display: none;">{{ $salonName }}</span>
                    </a>
                </div>


                <!-- Right: Profile -->
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <div class="relative">
                                <button id="profile-toggle"
                                    class="flex items-center space-x-2 hover:bg-gray-100 px-3 py-2 rounded-lg">
                                    <i class="fas fa-user-circle text-2xl text-gray-600"></i>
                                    <span class="text-sm font-medium">{{ Auth::user()->name ?? 'User' }}</span>
                                    <i class="fas fa-chevron-down text-sm text-gray-400"></i>
                                </button>
                                <!-- Dropdown -->
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden"
                                    id="profile-dropdown">
                                    <a href="{{ url('/dashboard') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('booking.guest') }}" class="btn-primary">Book Now</a>
                            <a href="{{ route('login') }}" class="btn-secondary">Login</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-secondary">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-16 min-h-screen bg-gray-50">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="mb-4">
                        <img src="{{ $logoUrl }}" alt="{{ $salonName }}" class="h-12 w-auto object-contain"
                            onerror="this.style.display='none'; document.getElementById('footer-salon-name').style.display='block';">
                        <h3 id="footer-salon-name" class="text-xl font-bold" style="display: none;">{{ $salonName }}
                        </h3>
                    </div>
                    <p class="text-gray-400">Your beauty journey starts here. Experience luxury and excellence in every
                        service.</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition">Home</a>
                        </li>
                        <li><a href="#services" class="text-gray-400 hover:text-white transition">Services</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition">About Us</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-gray-400">
                        @if(isset($salon->address) && $salon->address)
                            <li><i class="fas fa-map-marker-alt mr-2"></i>{{ $salon->address }}</li>
                        @endif
                        @if(isset($salon->phone) && $salon->phone)
                            <li><i class="fas fa-phone mr-2"></i>{{ $salon->phone }}</li>
                        @endif
                        @if(isset($salon->email) && $salon->email)
                            <li><i class="fas fa-envelope mr-2"></i>{{ $salon->email }}</li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $salonName }}. All rights
                    reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    @stack('scripts')

    <script>
        // Profile Dropdown Toggle
        document.addEventListener('DOMContentLoaded', function () {
            const profileToggle = document.getElementById('profile-toggle');
            if (profileToggle) {
                profileToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const dropdown = document.getElementById('profile-dropdown');
                    dropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', function () {
                    document.getElementById('profile-dropdown')?.classList.add('hidden');
                });
            }
        });
    </script>

</html>