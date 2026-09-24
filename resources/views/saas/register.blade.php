<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Salon - {{ $appName }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ app(\App\Services\SettingsService::class)->getFaviconUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- International Tel Input CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
    <style>
        .iti {
            width: 100%;
        }

        .iti__flag-container {
            z-index: 2;
        }

        .iti__selected-flag {
            padding: 0 8px 0 12px;
        }

        .iti--separate-dial-code .iti__selected-dial-code {
            margin-left: 6px;
            color: #495057;
        }
    </style>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Outfit', sans-serif;
        }

        .gradient-overlay {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.9) 0%, rgba(124, 58, 237, 0.8) 100%);
        }

        .plan-option.selected {
            border-color: #7c3aed;
            background-color: #f5f3ff;
            box-shadow: 0 0 0 2px #7c3aed;
        }

        .plan-option:hover {
            border-color: #7c3aed;
            transform: translateY(-2px);
        }

        /* Business Type Selection Cards */
        .business-type-option {
            border: 2px solid #e2e8f0;
        }

        .business-type-option.selected {
            border-color: #7c3aed;
            background-color: #f5f3ff;
            box-shadow: 0 4px 12px -2px rgba(124, 58, 237, 0.15);
        }
        
        .business-type-option#option_barber.selected {
            border-color: #d97706;
            background-color: #fffbeb;
            box-shadow: 0 4px 12px -2px rgba(217, 119, 6, 0.15);
        }

        .business-type-option#option_both.selected {
            border-color: #4f46e5;
            background-color: #eef2ff;
            box-shadow: 0 4px 12px -2px rgba(79, 70, 229, 0.15);
        }

        .business-type-option.selected .indicator-dot {
            border-color: #7c3aed;
        }
        
        .business-type-option#option_barber.selected .indicator-dot {
            border-color: #d97706;
        }

        .business-type-option#option_both.selected .indicator-dot {
            border-color: #4f46e5;
        }

        .business-type-option.selected .indicator-dot-inner {
            display: block;
        }

        .business-type-option:hover {
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        /* Progress Modal Styles */
        .progress-modal-content {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .step-item.active {
            opacity: 1;
            transform: translateX(10px);
        }

        .step-item.completed .step-icon {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }

        /* Log Container */
        .log-container {
            font-family: 'Fira Code', monospace;
        }

        .log-entry {
            animation: fadeInLog 0.2s forwards;
        }

        @keyframes fadeInLog {
            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body class="bg-slate-50 h-screen w-full flex overflow-hidden">

    <!-- Left Side - Video & Brand -->
    <div class="hidden lg:flex w-5/12 relative overflow-hidden flex-col justify-between p-12 text-white h-full">
        <!-- Video Background -->
        <video autoplay muted loop playsinline class="absolute top-0 left-0 w-full h-full object-cover z-0">
            <source src="https://cdn.coverr.co/videos/coverr-woman-getting-hair-styled-at-salon-5647/1080p.mp4"
                type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- Gradient Overlay -->
        <div class="absolute top-0 left-0 w-full h-full gradient-overlay z-1"></div>

        <!-- Content -->
        <div class="relative z-10">
            <a href="{{ url('/') }}"
                class="flex items-center gap-2 text-white/80 hover:text-white transition-colors mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Back to Home
            </a>

            <h1 class="text-4xl font-bold mb-4 leading-tight">
                Start your journey <br> with {{ $appName }}
            </h1>
            <p class="text-lg text-purple-100 max-w-md leading-relaxed mb-6">
                Join thousands of salon owners who trust us to manage their business.
            </p>

            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="mt-1 w-5 h-5 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <p class="text-purple-100 text-sm">Free 14-day trial on all plans</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="mt-1 w-5 h-5 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <p class="text-purple-100 text-sm">No credit card required for trial</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="mt-1 w-5 h-5 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <p class="text-purple-100 text-sm">Cancel anytime, no hidden fees</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-xs text-purple-200/60">
            © {{ date('Y') }} {{ $appName }}. All rights reserved.
        </div>
    </div>

    <!-- Right Side - Registration Form -->
    <div class="w-full lg:w-7/12 ml-auto p-6 lg:p-8 overflow-y-auto h-full">
        <div class="max-w-xl mx-auto">
            <div class="text-center lg:text-left mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Create your account</h2>
                <p class="mt-1 text-slate-600 text-sm">Set up your salon in minutes</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-r-md mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-4 w-4 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <ul class="list-disc list-inside text-xs text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form id="registrationForm" method="POST" action="{{ route('saas.register') }}" class="space-y-6"
                autocomplete="off">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Salon Details -->
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                            <span
                                class="w-5 h-5 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-[10px]">1</span>
                            Salon Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="md:col">
                                <label for="salon_name" class="block text-xs font-medium text-slate-700 mb-1">Salon
                                    Name</label>
                                <input type="text" name="salon_name" id="salon_name" required
                                    class="block w-full rounded-lg border-slate-200 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5 text-sm transition-colors"
                                    placeholder="e.g. Glow Beauty Studio" value="{{ old('salon_name') }}">
                            </div>
                            <div>
                                <label for="salon_slug" class="block text-xs font-medium text-slate-700 mb-1">Salon
                                    URL</label>
                                <div class="flex rounded-lg shadow-sm">
                                    <span
                                        class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-200 bg-slate-50 text-slate-500 text-xs">
                                        {{ request()->getHost() }}/
                                    </span>
                                    <input type="text" name="salon_slug" id="salon_slug" required
                                        class="flex-1 block w-full rounded-none rounded-r-lg border-slate-200 focus:border-purple-500 focus:ring-purple-500 py-2.5 text-sm transition-colors"
                                        placeholder="glow-beauty" value="{{ old('salon_slug') }}">
                                </div>
                                <p class="mt-1 text-[10px] text-slate-500">This will be your unique address.</p>
                            </div>
                             <div class="md:col-span-2">
                                 <label class="block text-xs font-semibold text-slate-700 mb-2">Business Type <span class="text-danger">*</span></label>
                                 <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                     @php
                                         $oldBusinessType = old('business_type', 'salon');
                                     @endphp
                                     <!-- Salon Option -->
                                     <label class="business-type-option relative rounded-xl p-4 cursor-pointer transition-all duration-200 bg-white flex flex-col justify-between h-36 {{ $oldBusinessType == 'salon' ? 'selected' : '' }}" id="option_salon">
                                         <input type="radio" name="business_type" value="salon" {{ $oldBusinessType == 'salon' ? 'checked' : '' }} class="hidden">
                                         <!-- Top Row: Icon & Selector -->
                                         <div class="flex justify-between items-start w-full">
                                             <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #f5f3ff; color: #7c3aed;">
                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                                                 </svg>
                                             </div>
                                             <div class="w-4.5 h-4.5 rounded-full border border-slate-300 flex items-center justify-center flex-shrink-0 indicator-dot" style="width: 18px; height: 18px;">
                                                 <div class="w-2.5 h-2.5 rounded-full bg-purple-600 hidden indicator-dot-inner"></div>
                                             </div>
                                         </div>
                                         <!-- Bottom Row: Text -->
                                         <div class="mt-2">
                                             <p class="font-bold text-slate-800 text-sm">Salon / Spa</p>
                                             <p class="text-[10px] text-slate-500 leading-normal mt-0.5">Hair, skin, beauty services</p>
                                         </div>
                                     </label>

                                     <!-- Barber Option -->
                                     <label class="business-type-option relative rounded-xl p-4 cursor-pointer transition-all duration-200 bg-white flex flex-col justify-between h-36 {{ $oldBusinessType == 'barber' ? 'selected' : '' }}" id="option_barber">
                                         <input type="radio" name="business_type" value="barber" {{ $oldBusinessType == 'barber' ? 'checked' : '' }} class="hidden">
                                         <!-- Top Row: Icon & Selector -->
                                         <div class="flex justify-between items-start w-full">
                                             <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #fffbeb; color: #d97706;">
                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 11-4.243 4.243 3 3 0 014.243-4.243zm0-5.758a3 3 0 11-4.243-4.243 3 3 0 014.243 4.243z" />
                                                 </svg>
                                             </div>
                                             <div class="w-4.5 h-4.5 rounded-full border border-slate-300 flex items-center justify-center flex-shrink-0 indicator-dot" style="width: 18px; height: 18px;">
                                                 <div class="w-2.5 h-2.5 rounded-full bg-amber-600 hidden indicator-dot-inner"></div>
                                             </div>
                                         </div>
                                         <!-- Bottom Row: Text -->
                                         <div class="mt-2">
                                             <p class="font-bold text-slate-800 text-sm">Barber / Barbershop</p>
                                             <p class="text-[10px] text-slate-500 leading-normal mt-0.5">Haircuts, beard grooming</p>
                                         </div>
                                     </label>

                                     <!-- Both Option -->
                                     <label class="business-type-option relative rounded-xl p-4 cursor-pointer transition-all duration-200 bg-white flex flex-col justify-between h-36 {{ $oldBusinessType == 'both' ? 'selected' : '' }}" id="option_both">
                                         <input type="radio" name="business_type" value="both" {{ $oldBusinessType == 'both' ? 'checked' : '' }} class="hidden">
                                         <!-- Top Row: Icon & Selector -->
                                         <div class="flex justify-between items-start w-full">
                                             <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #eef2ff; color: #4f46e5;">
                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                 </svg>
                                             </div>
                                             <div class="w-4.5 h-4.5 rounded-full border border-slate-300 flex items-center justify-center flex-shrink-0 indicator-dot" style="width: 18px; height: 18px;">
                                                 <div class="w-2.5 h-2.5 rounded-full bg-indigo-600 hidden indicator-dot-inner"></div>
                                             </div>
                                         </div>
                                         <!-- Bottom Row: Text -->
                                         <div class="mt-2">
                                             <p class="font-bold text-slate-800 text-sm">Both (Salon & Barber)</p>
                                             <p class="text-[10px] text-slate-500 leading-normal mt-0.5">All-in-one hybrid store</p>
                                         </div>
                                     </label>
                                 </div>
                             </div>
                             <input type="hidden" name="currency" id="currency" value="{{ old('currency', 'USD') }}">
                             <input type="hidden" name="timezone" id="timezone" value="{{ old('timezone', 'UTC') }}">
                        </div>
                    </div>

                    <!-- Personal Details -->
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                            <span
                                class="w-5 h-5 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-[10px]">2</span>
                            Owner Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="md:col-span-2">
                                <label for="name" class="block text-xs font-medium text-slate-700 mb-1">Full
                                    Name</label>
                                <input type="text" name="name" id="name" required
                                    class="block w-full rounded-lg border-slate-200 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5 text-sm transition-colors"
                                    placeholder="John Doe" value="{{ old('name') }}">
                            </div>
                            <div>
                                <label for="email" class="block text-xs font-medium text-slate-700 mb-1">Email
                                    Address</label>
                                <input type="email" name="email" id="email" required
                                    class="block w-full rounded-lg border-slate-200 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5 text-sm transition-colors"
                                    placeholder="you@example.com" value="{{ old('email') }}">
                            </div>
                            <div>
                                <label for="phone" class="block text-xs font-medium text-slate-700 mb-1">Phone
                                    Number</label>
                                <input type="text" name="phone" id="phone" required
                                    class="block w-full rounded-lg border-slate-200 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5 text-sm transition-colors"
                                    placeholder="+1 234 567 8900" value="{{ old('phone') }}">
                            </div>
                            <div>
                                <label for="password"
                                    class="block text-xs font-medium text-slate-700 mb-1">Password</label>
                                <input type="password" name="password" id="password" required
                                    class="block w-full rounded-lg border-slate-200 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5 text-sm transition-colors"
                                    placeholder="••••••••">
                            </div>
                            <div>
                                <label for="password_confirmation"
                                    class="block text-xs font-medium text-slate-700 mb-1">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    class="block w-full rounded-lg border-slate-200 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5 text-sm transition-colors"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan Selection (Compact) -->
                <div>
                    <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span
                            class="w-5 h-5 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-[10px]">3</span>
                        Select your plan
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($plans as $plan)
                            @php
                                $isSelected = (request('plan_id') == $plan->id) || (!request('plan_id') && $loop->first);
                            @endphp
                            <label
                                class="plan-option relative border border-slate-200 rounded-lg p-3 cursor-pointer transition-all duration-200 bg-white {{ $isSelected ? 'selected' : '' }}"
                                data-business-type="{{ $plan->business_type }}">
                                @if($plan->is_popular)
                                    <div
                                        class="absolute -top-2 right-2 bg-purple-600 text-white text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide shadow-sm">
                                        Popular
                                    </div>
                                @endif
                                <input type="radio" name="plan_id" value="{{ $plan->id }}"
                                    data-trial-days="{{ $plan->trial_days }}" {{ $isSelected ? 'checked' : '' }}
                                    class="hidden">
                                <div class="text-center">
                                    <h3 class="font-bold text-slate-900 text-sm mb-1">{{ $plan->name }}</h3>
                                    <div class="mb-1">
                                        @if($plan->price > 0)
                                            <span
                                                class="text-xl font-bold text-purple-600">{{ system_currency_symbol() }}{{ number_format($plan->price, 0) }}</span>
                                            <span class="text-[10px] text-slate-500">/mo</span>
                                        @else
                                            <span class="text-xl font-bold text-purple-600">Free</span>
                                        @endif
                                    </div>
                                    <p
                                        class="text-[10px] text-slate-500 font-medium bg-slate-50 rounded py-0.5 px-1.5 inline-block">
                                        @if($plan->trial_days > 0)
                                            {{ $plan->trial_days }} Day Trial
                                        @else
                                            {{ $plan->duration_in_days }} Days
                                        @endif
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-base font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 hover:shadow-purple-500/30 hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Create My Salon
                    </button>
                    <p class="mt-3 text-center text-xs text-slate-500">
                        Already have an account? <a href="{{ route('login') }}"
                            class="font-medium text-purple-600 hover:text-purple-500">Sign in</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <!-- Progress Modal -->
    <div id="progressModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full progress-modal-content">
                <div
                    class="bg-gradient-to-r from-purple-600 to-indigo-600 px-4 py-8 sm:px-6 text-center relative overflow-hidden">
                    <div
                        class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
                    </div>
                    <div class="relative z-10">
                        <div
                            class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-white/20 mb-4 animate-bounce">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl leading-6 font-bold text-white" id="modal-title">Setting Up Your Salon</h3>
                        <p class="mt-2 text-purple-100">Please wait while we configure your workspace...</p>
                    </div>
                </div>
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4 bg-white">
                    <div class="space-y-4">
                        <div class="step-item flex items-center opacity-50 transition-all duration-300" id="step1">
                            <div
                                class="step-icon w-8 h-8 rounded-full border-2 border-slate-200 flex items-center justify-center mr-3 text-slate-400 font-bold text-sm transition-colors">
                                1</div>
                            <span class="text-slate-600 font-medium">Creating Account</span>
                        </div>
                        <div class="step-item flex items-center opacity-50 transition-all duration-300" id="step2">
                            <div
                                class="step-icon w-8 h-8 rounded-full border-2 border-slate-200 flex items-center justify-center mr-3 text-slate-400 font-bold text-sm transition-colors">
                                2</div>
                            <span class="text-slate-600 font-medium">Configuring Salon Settings</span>
                        </div>
                        <div class="step-item flex items-center opacity-50 transition-all duration-300" id="step3">
                            <div
                                class="step-icon w-8 h-8 rounded-full border-2 border-slate-200 flex items-center justify-center mr-3 text-slate-400 font-bold text-sm transition-colors">
                                3</div>
                            <span class="text-slate-600 font-medium">Finalizing Setup</span>
                        </div>
                    </div>

                    <!-- Terminal Log -->
                    <div class="mt-6 bg-slate-900 rounded-lg p-4 font-mono text-xs text-green-400 h-32 overflow-y-auto log-container shadow-inner"
                        id="logContainer">
                        <div class="log-entry">> Initializing setup process...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- International Tel Input JS -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
    <script src="{{ asset('js/customer-phone-handler.js') }}?v={{ time() }}"></script>
    <script>
        // Timezone and Currency Auto-detection
        (function () {
            const tzToCurrency = {
                'America/New_York': 'USD', 'America/Chicago': 'USD', 'America/Denver': 'USD', 'America/Los_Angeles': 'USD',
                'America/Toronto': 'CAD', 'America/Vancouver': 'CAD', 'America/Mexico_City': 'MXN', 'America/Sao_Paulo': 'BRL',
                'America/Argentina/Buenos_Aires': 'ARS', 'Europe/London': 'GBP', 'Europe/Dublin': 'EUR', 'Europe/Paris': 'EUR',
                'Europe/Berlin': 'EUR', 'Europe/Rome': 'EUR', 'Europe/Madrid': 'EUR', 'Europe/Amsterdam': 'EUR',
                'Europe/Brussels': 'EUR', 'Europe/Vienna': 'EUR', 'Europe/Zurich': 'CHF', 'Europe/Stockholm': 'SEK',
                'Europe/Oslo': 'NOK', 'Europe/Copenhagen': 'DKK', 'Europe/Helsinki': 'EUR', 'Europe/Moscow': 'RUB',
                'Asia/Kolkata': 'INR', 'Asia/Calcutta': 'INR', 'Asia/Dubai': 'AED', 'Asia/Riyadh': 'SAR', 'Asia/Qatar': 'QAR', 'Asia/Singapore': 'SGD',
                'Asia/Tokyo': 'JPY', 'Asia/Hong_Kong': 'HKD', 'Asia/Seoul': 'KRW', 'Asia/Shanghai': 'CNY', 'Asia/Bangkok': 'THB',
                'Asia/Jakarta': 'IDR', 'Asia/Manila': 'PHP', 'Asia/Ho_Chi_Minh': 'VND', 'Asia/Karachi': 'PKR',
                'Asia/Dhaka': 'BDT', 'Asia/Colombo': 'LKR', 'Asia/Kathmandu': 'NPR', 'Australia/Sydney': 'AUD',
                'Australia/Melbourne': 'AUD', 'Australia/Perth': 'AUD', 'Pacific/Auckland': 'NZD', 'Africa/Johannesburg': 'ZAR',
                'Africa/Cairo': 'EGP', 'Africa/Lagos': 'NGN', 'Africa/Nairobi': 'KES'
            };

            function detectLocalization() {
                const timezoneInput = document.getElementById('timezone');
                const currencyInput = document.getElementById('currency');

                if (!timezoneInput || !currencyInput) return;

                try {
                    let localTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;


                    // Normalize common aliases
                    if (localTimezone === 'Asia/Calcutta') {
                        localTimezone = 'Asia/Kolkata';

                    }

                    // Basic check for a valid-looking IANA timezone (contains a slash or is UTC)
                    if (localTimezone && (localTimezone.includes('/') || localTimezone === 'UTC')) {
                        timezoneInput.value = localTimezone;

                        const suggestedCurrency = tzToCurrency[localTimezone];
                        if (suggestedCurrency) {
                            currencyInput.value = suggestedCurrency;
                        }
                    } else {

                    }
                } catch (e) {

                }
            }

            // Run on load and expose to window for final check
            window.detectLocalization = detectLocalization;
            detectLocalization();
            document.addEventListener('DOMContentLoaded', detectLocalization);
            window.addEventListener('load', detectLocalization);
        })();

        // Plan Selection Logic
        const planOptions = document.querySelectorAll('.plan-option');
        planOptions.forEach(option => {
            option.addEventListener('click', () => {
                // Remove selected class from all
                planOptions.forEach(opt => opt.classList.remove('selected'));
                // Add selected class to clicked
                option.classList.add('selected');
                // Check the radio input
                const radio = option.querySelector('input[type="radio"]');
                radio.checked = true;
            });
        });

        // Business Type selection and plan filtering logic
        const businessTypeOptions = document.querySelectorAll('.business-type-option');
        function filterPlans() {
            const checkedInput = document.querySelector('input[name="business_type"]:checked');
            if (!checkedInput) return;
            const selectedType = checkedInput.value;
            let firstVisibleOption = null;
            let isAnyVisibleChecked = false;

            planOptions.forEach(option => {
                const planType = option.getAttribute('data-business-type');
                const radioInput = option.querySelector('input[type="radio"]');

                if (planType === 'both' || planType === selectedType) {
                    option.style.display = 'block';
                    if (!firstVisibleOption) {
                        firstVisibleOption = option;
                    }
                    if (radioInput.checked) {
                        isAnyVisibleChecked = true;
                    }
                } else {
                    option.style.display = 'none';
                    option.classList.remove('selected');
                    radioInput.checked = false;
                }
            });

            // If none of the visible options are currently checked, select the first visible option
            if (!isAnyVisibleChecked && firstVisibleOption) {
                firstVisibleOption.classList.add('selected');
                const radio = firstVisibleOption.querySelector('input[type="radio"]');
                radio.checked = true;
            }
        }

        if (businessTypeOptions.length > 0) {
            businessTypeOptions.forEach(option => {
                option.addEventListener('click', () => {
                    // Remove selected class from all options
                    businessTypeOptions.forEach(opt => opt.classList.remove('selected'));
                    // Add selected class to the clicked option
                    option.classList.add('selected');
                    // Check the hidden radio input
                    const radio = option.querySelector('input[type="radio"]');
                    radio.checked = true;
                    // Trigger plan filtering
                    filterPlans();
                });
            });

            // Run initially after DOM content is loaded
            document.addEventListener('DOMContentLoaded', filterPlans);
            window.addEventListener('load', filterPlans);
            filterPlans();
        }

        // Slug Generation
        const salonNameInput = document.getElementById('salon_name');
        const salonSlugInput = document.getElementById('salon_slug');

        if (salonNameInput && salonSlugInput) {
            salonNameInput.addEventListener('input', function () {
                if (!salonSlugInput.value || salonSlugInput.value === slugify(this.oldValue || '')) {
                    salonSlugInput.value = slugify(this.value);
                }
                this.oldValue = this.value;
            });
        }

        function slugify(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
        }

        // Form Submission & Progress Modal
        const form = document.getElementById('registrationForm');
        const modal = document.getElementById('progressModal');
        const logContainer = document.getElementById('logContainer');

        if (form) {
            form.addEventListener('submit', function (e) {
                // Prevent immediate submission to allow for progress animation
                e.preventDefault();

                // Validate phone number
                if (window.customerPhoneHandler && !window.customerPhoneHandler.validateForm(form)) {
                    return false;
                }

                // Show Modal
                if (modal) modal.classList.remove('hidden');

                // Final check for localization
                if (window.detectLocalization) {
                    window.detectLocalization();
                }

                // Simulate Progress
                simulateProgress();

                // Submit form after delay
                setTimeout(() => {
                    form.submit();
                }, 3000);
            });
        }

        function simulateProgress() {
            const steps = ['step1', 'step2', 'step3'];
            const logs = [
                'Validating user information...',
                'Creating secure environment...',
                'Setting up database schema...',
                'Configuring default settings...',
                'Generating API keys...',
                'Finalizing installation...'
            ];

            let stepIndex = 0;
            let logIndex = 0;

            // Step Animation
            const stepInterval = setInterval(() => {
                if (stepIndex < steps.length) {
                    const step = document.getElementById(steps[stepIndex]);
                    if (step) {
                        step.classList.remove('opacity-50');
                        step.classList.add('active');

                        const icon = step.querySelector('.step-icon');
                        if (icon) {
                            icon.classList.remove('border-slate-200', 'text-slate-400');
                            icon.classList.add('bg-green-500', 'border-green-500', 'text-white');
                            icon.innerHTML = '✓';
                        }
                    }
                    stepIndex++;
                } else {
                    clearInterval(stepInterval);
                }
            }, 800);

            // Log Animation
            const logInterval = setInterval(() => {
                if (logIndex < logs.length) {
                    const entry = document.createElement('div');
                    entry.className = 'log-entry mt-1';
                    entry.textContent = '> ' + logs[logIndex];
                    if (logContainer) {
                        logContainer.appendChild(entry);
                        logContainer.scrollTop = logContainer.scrollHeight;
                    }
                    logIndex++;
                } else {
                    clearInterval(logInterval);
                }
            }, 400);
        }
    </script>
</body>

</html>