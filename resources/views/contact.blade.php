<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us - {{ $appName }}</title>
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

        .contact-card {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 30px -10px rgba(79, 70, 229, 0.1);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 overflow-x-hidden selection:bg-purple-500 selection:text-white">

    @include('partials.saas-header')

    <!-- Compact Hero -->
    <section class="pt-32 pb-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl lg:text-5xl font-bold text-slate-900 mb-4">Let's <span
                    class="gradient-text">Connect</span></h1>
            <p class="text-slate-600 max-w-2xl mx-auto">Have a question or need a demo? We're here to help you transform
                your salon business.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-[3rem] overflow-hidden shadow-2xl border border-slate-100">
                <div class="grid lg:grid-cols-2">
                    <!-- Contact Info & Image -->
                    <div
                        class="relative p-12 lg:p-16 flex flex-col justify-between text-white overflow-hidden bg-slate-900">
                        <div class="absolute inset-0 z-0">
                            <img src="https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
                                alt="Salon Interior" class="w-full h-full object-cover opacity-30">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-900/80 to-slate-900/90"></div>
                        </div>

                        <div class="relative z-10">
                            <h2 class="text-3xl font-bold mb-8">Contact Information</h2>
                            <div class="space-y-8">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-slate-400 text-sm mb-1">Call us</p>
                                        <p class="text-xl font-bold">+974 66266581</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-slate-400 text-sm mb-1">Email us</p>
                                        <p class="text-xl font-bold">info@fsqatar.com</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-slate-400 text-sm mb-1">Visit us</p>
                                        <p class="text-xl font-bold">Doha, Qatar</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 mt-12 pt-12 border-t border-white/10">
                            <p class="text-slate-400 mb-4">Follow us</p>
                            <div class="flex gap-4">
                                <a href="#"
                                    class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center hover:bg-purple-600 transition-colors"><svg
                                        class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                    </svg></a>
                                <a href="#"
                                    class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center hover:bg-purple-600 transition-colors"><svg
                                        class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                                    </svg></a>
                                <a href="#"
                                    class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center hover:bg-purple-600 transition-colors"><svg
                                        class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                    </svg></a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="p-12 lg:p-16 bg-white">
                        <h3 class="text-2xl font-bold text-slate-900 mb-8">Send us a message</h3>
                        <form action="#" method="POST" class="space-y-6">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="first_name" class="block text-sm font-bold text-slate-700 mb-2">First
                                        Name</label>
                                    <input type="text" name="first_name" id="first_name"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition-all"
                                        placeholder="Jane">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-bold text-slate-700 mb-2">Last
                                        Name</label>
                                    <input type="text" name="last_name" id="last_name"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition-all"
                                        placeholder="Doe">
                                </div>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email
                                    Address</label>
                                <input type="email" name="email" id="email"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition-all"
                                    placeholder="jane@example.com">
                            </div>
                            <div>
                                <label for="subject" class="block text-sm font-bold text-slate-700 mb-2">Subject</label>
                                <select name="subject" id="subject"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition-all">
                                    <option value="general">General Inquiry</option>
                                    <option value="support">Technical Support</option>
                                    <option value="billing">Billing Question</option>
                                    <option value="sales">Sales Inquiry</option>
                                </select>
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-bold text-slate-700 mb-2">Message</label>
                                <textarea name="message" id="message" rows="4"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition-all"
                                    placeholder="How can we help you?"></textarea>
                            </div>
                            <button type="submit"
                                class="w-full py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-purple-500/30 hover:shadow-purple-500/40 transform hover:-translate-y-1 transition-all">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.saas-footer')

</body>

</html>