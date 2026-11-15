@extends('layouts.guest')

@section('title', 'Book Appointment - Guest')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
    <link rel="stylesheet" href="{{ asset('css/guest-booking.css') }}?v={{ file_exists(public_path('css/guest-booking.css')) ? filemtime(public_path('css/guest-booking.css')) : time() }}">
@endpush

@section('content')
    @if(isset($guestBookingEnabled) && !$guestBookingEnabled)
        <!-- Booking Disabled Message -->
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 via-purple-600 to-pink-500">
            <div
                class="bg-white/95 backdrop-blur-lg rounded-2xl p-12 max-w-md mx-4 text-center shadow-2xl border border-white/20">
                <div class="text-6xl mb-6">📅</div>
                <h1 class="text-2xl font-bold text-gray-800 mb-4">Booking Unavailable</h1>
                <p class="text-gray-600 mb-6 leading-relaxed">Booking is not available for now. Please contact us directly to
                    schedule your appointment.</p>
                <div class="flex flex-col gap-3">
                    <a href="tel:{{ $business_phone ?? '+974 33102532' }}"
                        class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-3 rounded-full font-semibold hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                    <a href="mailto:{{ $business_email ?? 'info@salon.com' }}"
                        class="bg-white text-gray-700 px-6 py-3 rounded-full font-semibold border-2 border-gray-300 hover:border-gray-400 transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-envelope"></i> Email Us
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Enhanced Header Section -->
        <header class="salon-header text-white w-full">
            <div class="max-w-7xl mx-auto" style="padding: 1rem 1.25rem;">
                <div class="flex items-center gap-3">
                    <!-- Logo -->
                    <div class="salon-logo overflow-hidden flex items-center justify-center text-white font-bold flex-shrink-0">
                        @if(!empty($business_logo))
                            <img src="{{ asset('storage/' . $business_logo) }}" alt="Logo" class="w-full h-full object-cover">
                        @else
                            {{ substr($business_name ?? 'Salon', 0, 1) }}
                        @endif
                    </div>
                    <!-- Salon Info -->
                    <div class="salon-info flex-1 min-w-0">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold leading-tight truncate">{{ $business_name ?? 'Salon Name' }}</h1>
                        <p class="text-gray-300 text-xs sm:text-sm mt-0.5 truncate">{{ $business_address ?? 'Your Beauty Destination' }}</p>
                        <div class="flex flex-wrap gap-2 sm:gap-4 text-xs sm:text-sm mt-1.5">
                            @if(!empty($business_phone))
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-phone text-cyan-400 text-xs"></i>
                                <span>{{ $business_phone }}</span>
                            </div>
                            @endif
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-clock text-cyan-400 text-xs"></i>
                                <span>{{ $formatted_hours ?? '9:00 AM - 9:00 PM' }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Slider (hidden on mobile) -->
                    <div class="slider hidden lg:block" style="width: 340px; min-width: 340px; height: 200px;">
                        <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=800&h=600&fit=crop" alt="Salon Interior" class="active">
                        <img src="https://images.unsplash.com/photo-1559599101-f09722fb4948?w=800&h=600&fit=crop" alt="Hair Styling">
                        <img src="https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?w=800&h=600&fit=crop" alt="Beauty Treatment">
                        <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=800&h=600&fit=crop" alt="Spa Experience">
                    </div>
                </div>
            </div>

            <!-- Feature Badges - horizontal scrollable on mobile -->
            <div class="bg-white text-black py-3 px-4" style="overflow-x:auto; scrollbar-width:none;">
                <div class="feature-badges-bar flex gap-3 min-w-max mx-auto" style="max-width:600px; justify-content:center;">
                    <div class="feature-badge flex items-center gap-2 px-3 py-2">
                        <span class="text-lg">⚡</span>
                        <div class="text-left">
                            <strong class="block text-sm leading-tight">Instant Booking</strong>
                            <span class="text-gray-500 text-xs">No queue at store</span>
                        </div>
                    </div>
                    <div class="feature-badge flex items-center gap-2 px-3 py-2">
                        <span class="text-lg">📝</span>
                        <div class="text-left">
                            <strong class="block text-sm leading-tight">Book Now</strong>
                            <span class="text-gray-500 text-xs">Pay at store</span>
                        </div>
                    </div>
                    <div class="feature-badge flex items-center gap-2 px-3 py-2">
                        <span class="text-lg">👤</span>
                        <div class="text-left">
                            <strong class="block text-sm leading-tight">Guest Booking</strong>
                            <span class="text-gray-500 text-xs">No account needed</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Step Wizard Stepper -->
        <div class="booking-stepper" id="booking-stepper">
            <div class="step-item active" id="step-indicator-1">
                <div class="step-circle">1</div>
                <div class="step-label">Services</div>
            </div>
            <div class="step-item" id="step-indicator-2">
                <div class="step-circle">2</div>
                <div class="step-label">Date &amp; Slot</div>
            </div>
            <div class="step-item" id="step-indicator-3">
                <div class="step-circle">3</div>
                <div class="step-label">Your Details</div>
            </div>
        </div>

        <!-- Mobile Category Pills (shown on mobile only, step 1) -->
        <div class="mobile-category-pills" id="mobile-category-pills">
            <button class="mobile-cat-pill active" data-category="all">All</button>
            @foreach($serviceCategories as $category)
                <button class="mobile-cat-pill" data-category="{{ $category->id }}">{{ $category->name }}</button>
            @endforeach
        </div>

        <!-- Content -->
        <div class="content-container" id="main-content-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Category Sidebar (3 cols) - Desktop only -->
                <aside id="category-sidebar-container" class="lg:col-span-3 hidden lg:block">
                    <div class="category-sidebar sticky-sidebar">
                        <h3 class="text-xl font-bold mb-4 text-gray-900 border-b pb-2">
                            Services
                        </h3>
                        <ul class="space-y-1" id="category-list">
                            @foreach($serviceCategories as $category)
                                <li class="category-item" data-category="{{ $category->id }}">
                                    {{ $category->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>

                <!-- Services List (6 cols) -->
                <section class="lg:col-span-6" id="services-section">
                    <div class="spinner"></div>
                </section>

                <!-- Booking Options Section - Step 2 (Hidden by default) -->
                <section class="lg:col-span-6" id="booking-options-section" style="display: none;">
                    <!-- Staff Selection -->
                    <div class="booking-option-section">
                        <h4 class="booking-section-title">Select Staff</h4>
                        <div class="staff-radio-group" id="staff-selection-container">
                            <label class="staff-radio-label">
                                <input type="radio" name="staff_selection" value="" class="staff-radio-input" checked>
                                Any Staff
                            </label>
                            @foreach($staffMembers as $staff)
                                <label class="staff-radio-label staff-option" data-staff-id="{{ $staff->id }}">
                                    <input type="radio" name="staff_selection" value="{{ $staff->id }}" class="staff-radio-input">
                                    {{ $staff->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="booking-option-section">
                        <h4 class="booking-section-title">Select Date</h4>
                        <div class="date-scroll-container" id="date-selection-container">
                            <!-- Dates will be populated by JS -->
                        </div>
                        <button class="text-sm text-gray-500 mt-2 hover:text-teal-500 flex items-center gap-1"
                            id="show-more-dates">
                            Show More Dates <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <!-- Time Selection -->
                    <div class="booking-option-section">
                        <h4 class="booking-section-title">Select Time</h4>
                        <div id="time-selection-container">
                            <div class="text-center text-gray-500 py-4">Please select a date to view available times</div>
                        </div>
                    </div>

                    <!-- Step 2 Navigation Buttons -->
                    <div class="step-nav-btns" id="step2-nav-btns">
                        <button class="step-back-btn" id="step2-back-btn">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </button>
                        <button class="step-next-btn" id="step2-next-btn">
                            Next: Your Details <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </section>

                <!-- Step 3: Guest Details inline section (Hidden by default) -->
                <section class="lg:col-span-6" id="guest-details-section" style="display: none;">
                    <div class="guest-details-section">
                        <h3 class="text-lg font-bold mb-4 text-gray-800">
                            <i class="fas fa-user-circle mr-2 text-indigo-500"></i>Your Details
                        </h3>

                        <!-- Booking Summary Card -->
                        <div class="booking-summary-card mb-5" id="inline-booking-summary">
                            <h4 class="text-sm font-bold text-gray-600 mb-3 uppercase tracking-wide">Booking Summary</h4>
                            <div class="booking-summary-row">
                                <div class="booking-summary-icon"><i class="fas fa-cut"></i></div>
                                <div>
                                    <div class="booking-summary-label">Services</div>
                                    <div class="booking-summary-value" id="summary-services">-</div>
                                </div>
                            </div>
                            <div class="booking-summary-row">
                                <div class="booking-summary-icon"><i class="fas fa-user-tie"></i></div>
                                <div>
                                    <div class="booking-summary-label">Staff</div>
                                    <div class="booking-summary-value" id="summary-staff">Any Available</div>
                                </div>
                            </div>
                            <div class="booking-summary-row">
                                <div class="booking-summary-icon"><i class="fas fa-calendar"></i></div>
                                <div>
                                    <div class="booking-summary-label">Date</div>
                                    <div class="booking-summary-value" id="summary-date">-</div>
                                </div>
                            </div>
                            <div class="booking-summary-row">
                                <div class="booking-summary-icon"><i class="fas fa-clock"></i></div>
                                <div>
                                    <div class="booking-summary-label">Time</div>
                                    <div class="booking-summary-value" id="summary-time">-</div>
                                </div>
                            </div>
                            <div class="booking-summary-row">
                                <div class="booking-summary-icon" style="background: var(--success-gradient);"><i class="fas fa-tag"></i></div>
                                <div>
                                    <div class="booking-summary-label">Total</div>
                                    <div class="booking-summary-value" id="summary-total" style="font-size:1.1rem; color:#2dd4bf;">{{ $currency_symbol ?? '$' }} 0.00</div>
                                </div>
                            </div>
                        </div>

                        <!-- Guest Info Form (mirrors booking-form data) -->
                        <div class="grid grid-cols-1 gap-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="form-group">
                                    <label for="step3_first_name" class="form-label text-gray-600 text-sm">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" class="form-control" id="step3_first_name" placeholder="John" required>
                                </div>
                                <div class="form-group">
                                    <label for="step3_last_name" class="form-label text-gray-600 text-sm">Last Name</label>
                                    <input type="text" class="form-control" id="step3_last_name" placeholder="Doe">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="step3_phone" class="form-label text-gray-600 text-sm">Mobile Number <span class="text-red-500">*</span></label>
                                <input type="tel" class="form-control" id="step3_phone" placeholder="33102532" required>
                            </div>
                            <div class="form-group">
                                <label for="step3_email" class="form-label text-gray-600 text-sm">Email Address</label>
                                <input type="email" class="form-control" id="step3_email" placeholder="john@example.com">
                            </div>
                            <div class="form-group">
                                <label for="step3_address" class="form-label text-gray-600 text-sm">Address</label>
                                <textarea class="form-control resize-none" id="step3_address" rows="2" placeholder="Your address"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="step3_notes" class="form-label text-gray-600 text-sm">Booking Notes</label>
                                <textarea class="form-control resize-none" id="step3_notes" rows="2" placeholder="Any special requests?"></textarea>
                            </div>
                        </div>

                        <!-- Step 3 Navigation -->
                        <div class="step-nav-btns">
                            <button class="step-back-btn" id="step3-back-btn">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </button>
                            <button class="step-next-btn" id="step3-submit-btn">
                                <i class="fas fa-calendar-check mr-2"></i>Confirm Booking
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Cart Section (3 cols) - Sticky - Desktop only -->
                <aside class="lg:col-span-3 cart-section sticky-sidebar">
                    <h3 class="cart-header">
                        <i class="fas fa-shopping-cart mr-2"></i>Your Cart
                    </h3>
                    <div id="cart-empty" class="cart-empty">
                        <div class="cart-empty-icon">🛒</div>
                        <p class="font-semibold text-gray-700 mb-2">Your Cart is Empty</p>
                        <p class="text-gray-500 text-sm">Select services to get started</p>
                    </div>
                    <div id="cart-items" style="display: none;"></div>
                    <div id="cart-total" style="display: none;" class="cart-total">
                        <div class="flex justify-between items-center mb-4">
                            <span class="total-label">Total:</span>
                            <span class="total-amount">{{ $currency_symbol ?? '$' }} <span id="total-amount">0.00</span></span>
                        </div>
                        <button id="proceed-booking" class="proceed-btn">
                            <i class="fas fa-calendar-check mr-2"></i>Book Appointment
                        </button>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Mobile Floating Cart Bar (Mobile only, hidden on lg screens) -->
        <div class="mobile-cart-bar lg:hidden" id="mobile-cart-bar">
            <div class="mobile-cart-info flex items-center gap-2.5 flex-1 min-w-0" id="mobile-cart-trigger" role="button" tabindex="0" title="Tap to view and edit cart" style="cursor: pointer;">
                <div class="relative w-9 h-9 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shopping-bag text-sm"></i>
                    <span class="absolute -top-1 -right-1 bg-pink-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow" id="mobile-cart-badge">0</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <div class="text-[11px] text-teal-300 font-semibold tracking-wide flex items-center gap-1">
                        <span id="mobile-cart-count">0 items</span>
                        <span>•</span>
                        <span class="underline">View Cart <i class="fas fa-chevron-up text-[9px]"></i></span>
                    </div>
                    <span class="mobile-cart-total text-base font-extrabold text-white leading-tight" id="mobile-cart-total">{{ $currency_symbol ?? '$' }} 0.00</span>
                </div>
            </div>
            <button type="button" class="mobile-cart-btn shadow-md" id="mobile-cart-proceed">
                <span>Book Now</span> <i class="fas fa-arrow-right text-xs ms-1"></i>
            </button>
        </div>

        <!-- Mobile Cart Bottom Sheet Modal (Mobile only, hidden on lg screens) -->
        <div id="mobile-cart-modal" class="mobile-cart-modal lg:hidden" style="display: none;">
            <div class="mobile-cart-backdrop" id="mobile-cart-backdrop"></div>
            <div class="mobile-cart-sheet">
                <!-- Drag Handle -->
                <div class="mobile-cart-drag-handle">
                    <div class="mobile-cart-drag-bar"></div>
                </div>
                <!-- Header -->
                <div class="mobile-cart-sheet-header">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-teal-50 flex items-center justify-center text-teal-600">
                            <i class="fas fa-shopping-bag text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-base leading-tight">Your Cart</h3>
                            <span class="text-xs text-gray-500 font-medium" id="mobile-modal-item-count">0 items selected</span>
                        </div>
                    </div>
                    <button type="button" class="mobile-cart-close-btn" id="mobile-cart-close" aria-label="Close cart">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!-- Body -->
                <div class="mobile-cart-sheet-body" id="mobile-cart-items">
                    <!-- Populated dynamically via JS -->
                </div>
                <!-- Footer -->
                <div class="mobile-cart-sheet-footer">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-600 font-medium text-sm">Total:</span>
                        <span class="text-gray-900 font-extrabold text-xl" id="mobile-modal-total">{{ $currency_symbol ?? '$' }} 0.00</span>
                    </div>
                    <button type="button" class="proceed-btn w-full" id="mobile-modal-proceed">
                        <i class="fas fa-calendar-check mr-2"></i>Select Staff, Date &amp; Time
                    </button>
                </div>
            </div>
        </div>

        <!-- My Booking Section -->
        <div id="my-booking-section" class="max-w-3xl mx-auto" style="display: none;">
            <!-- Search Form -->
            <div class="bg-white rounded-2xl p-8 shadow-lg mb-6">
                <h2 class="text-2xl font-bold mb-6 text-center"
                    style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    <i class="fas fa-search mr-2"></i>Check Your Appointment
                </h2>
                <p class="text-gray-600 text-center mb-6">Enter your email or phone number to view your existing appointments
                </p>

                <form id="check-appointment-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="form-group">
                            <label for="check-phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="check-phone" name="phone" placeholder="33102532">
                            <input type="hidden" id="check_country_code" name="country_code">
                        </div>
                        <div class="form-group">
                            <label for="check-email" class="form-label">Email (Optional)</label>
                            <input type="email" class="form-control" id="check-email" name="email"
                                placeholder="john@example.com">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-search mr-2"></i>Search Appointments
                    </button>
                </form>
            </div>

            <!-- Search Results -->
            <div id="appointment-results" style="display: none;">
                <h3 class="text-xl font-bold mb-4"
                    style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    <i class="fas fa-calendar-alt mr-2"></i>Your Appointments
                </h3>
                <div id="appointments-list"></div>
            </div>

            <!-- No Results Message -->
            <div id="no-appointments" class="bg-white rounded-2xl p-8 text-center" style="display: none;">
                <div class="text-6xl mb-4 opacity-30">📅</div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">No Appointments Found</h3>
                <p class="text-gray-500">We couldn't find any appointments with the provided information.</p>
            </div>
        </div>

        <!-- Booking Modal -->
        <div id="booking-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">
                        <i class="fas fa-calendar-check mr-2"></i>Complete Your Booking
                    </h2>
                    <button id="close-modal" class="close-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="booking-form" action="{{ route('booking.guest.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="hidden-date" name="date">
                    <input type="hidden" id="hidden-time" name="time">
                    <input type="hidden" id="hidden-staff" name="staff_id">
                    <input type="hidden" id="hidden-name" name="name">
                    <input type="hidden" id="hidden-services" name="services_json">
                    <div class="modal-body">
                        <div class="form-section border-0 p-0">
                            <h3 class="text-xl font-bold mb-6 text-gray-800">Enter Your Details</h3>

                            <div class="grid grid-cols-1 gap-4">
                                <div class="form-group">
                                    <label for="first_name" class="form-label text-gray-600 text-sm">First Name</label>
                                    <input type="text"
                                        class="form-control border-b border-gray-200 focus:border-blue-500 px-0 rounded-none"
                                        id="first_name" name="first_name" placeholder="John" required>
                                </div>

                                <div class="form-group">
                                    <label for="last_name" class="form-label text-gray-600 text-sm">Last Name</label>
                                    <input type="text"
                                        class="form-control border-b border-gray-200 focus:border-blue-500 px-0 rounded-none"
                                        id="last_name" name="last_name" placeholder="Doe" required>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="form-label text-gray-600 text-sm">Email Address</label>
                                    <input type="email"
                                        class="form-control border-b border-gray-200 focus:border-blue-500 px-0 rounded-none"
                                        id="email" name="email" placeholder="john@example.com">
                                </div>

                                <div class="form-group">
                                    <label for="phone" class="form-label text-gray-600 text-sm">Mobile Number <span
                                            class="text-red-500">*</span></label>
                                    <input type="tel"
                                        class="form-control border-b border-gray-200 focus:border-blue-500 px-0 rounded-none"
                                        id="phone" name="phone" placeholder="3310253212" required>
                                    <input type="hidden" id="country_code" name="country_code">
                                </div>

                                <div class="form-group">
                                    <label for="address" class="form-label text-gray-600 text-sm">Address</label>
                                    <textarea
                                        class="form-control border-b border-gray-200 focus:border-blue-500 px-0 rounded-none resize-none"
                                        id="address" name="address" rows="2" placeholder="Your address"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="modal-notes" class="form-label text-gray-600 text-sm">Booking Notes</label>
                                    <textarea
                                        class="form-control border-b border-gray-200 focus:border-blue-500 px-0 rounded-none resize-none"
                                        id="modal-notes" name="notes" rows="2" placeholder="Any special requests?"></textarea>
                                </div>

                                @if(!empty($cancellation_policy))
                                <div class="mt-4 p-3 bg-amber-50 rounded-lg border border-amber-200 text-left">
                                    <p class="text-xs font-semibold text-amber-800 mb-1 flex items-center">
                                        <i class="fas fa-info-circle mr-1.5"></i>Cancellation Policy
                                    </p>
                                    <p class="text-xs text-amber-700 leading-relaxed">{{ $cancellation_policy }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-t-0 px-6 pb-6 pt-0">
                        <button type="submit"
                            class="btn-primary w-full rounded-full py-3 text-lg font-semibold shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                            Submit Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Success Modal -->
        <div id="success-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <div class="success-modal">
                    <div class="success-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <h2 class="success-title">Booking Confirmed!</h2>
                    <p class="success-message">Your appointment has been successfully booked. We'll contact you soon to confirm.
                    </p>
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-sm text-gray-500 mb-1">Booking Reference</p>
                        <p class="text-xl font-bold text-gray-800">#<span id="success-booking-id">-</span></p>
                    </div>
                    <button id="close-success" class="btn-primary">
                        <i class="fas fa-home mr-2"></i>Back to Home
                    </button>
                </div>
            </div>
        </div>

        <!-- Reschedule Modal -->
        <div id="reschedule-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">
                        <i class="fas fa-calendar-alt mr-2"></i>Reschedule Appointment
                    </h2>
                    <button id="close-reschedule" class="close-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="reschedule-form" action="" method="POST">
                    @csrf
                    <input type="hidden" id="reschedule-booking-id" name="booking_id">
                    <div class="modal-body">
                        <!-- Personal Information -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <div class="section-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                Contact Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label for="reschedule-phone" class="form-label">Phone Number <span
                                            class="required">*</span></label>
                                    <input type="tel" class="form-control" id="reschedule-phone" name="phone"
                                        placeholder="33102532" required>
                                    <input type="hidden" id="reschedule_country_code" name="country_code">
                                </div>
                                <div class="form-group">
                                    <label for="reschedule-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="reschedule-email" name="email"
                                        placeholder="john@example.com">
                                </div>
                            </div>
                        </div>

                        <!-- Service Selection -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <div class="section-icon">
                                    <i class="fas fa-cut"></i>
                                </div>
                                Service
                            </h3>
                            <div class="form-group">
                                <select class="form-select" id="reschedule-service" name="service_id" required>
                                    <option value="">Select a service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <div class="section-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                New Date & Time
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label for="reschedule-date" class="form-label">Date <span class="required">*</span></label>
                                    <input type="date" class="form-control" id="reschedule-date" name="date"
                                        min="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="reschedule-time" class="form-label">Time <span class="required">*</span></label>
                                    <input type="time" class="form-control" id="reschedule-time" name="time" required>
                                </div>
                            </div>
                        </div>

                        <!-- Staff Selection -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <div class="section-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                Preferred Staff
                            </h3>
                            <div class="form-group">
                                <select class="form-select" id="reschedule-staff" name="staff_id">
                                    <option value="">Any available staff</option>
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="cancel-reschedule" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-calendar-check mr-2"></i>Reschedule
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
            <script src="{{ asset('js/customer-phone-handler.js') }}?v={{ time() }}"></script>
            <script>
                window.guestBookingConfig = {
                    csrfToken: '{{ csrf_token() }}',
                    currencySymbol: @json($currency_symbol ?? '$'),
                    advanceBookingDays: {{ (int)($advance_booking_days ?? 30) }},
                    routes: {
                        services: @json(route('booking.guest.services')),
                        slots: @json(route('booking.guest.slots')),
                        check: @json(route('booking.guest.check')),
                        rescheduleTemplate: @json(route('guest.booking.reschedule', ['salon_slug' => app()->bound('current_salon') ? app('current_salon')->slug : 'PLACEHOLDER_SLUG', 'id' => 'PLACEHOLDER_ID']))
                    }
                };
            </script>
            <script src="{{ asset('js/guest-booking.js') }}?v={{ file_exists(public_path('js/guest-booking.js')) ? filemtime(public_path('js/guest-booking.js')) : time() }}"></script>
        @endpush

    @endif

@endsection