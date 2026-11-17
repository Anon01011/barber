/**
 * Guest Booking System JavaScript
 * Handles service selection, cart management, mobile cart drawer,
 * date/time slot selection, inline booking submission, and appointment lookup.
 */
document.addEventListener('DOMContentLoaded', function () {
    try {
        // --- Configuration & Server Context ---
        const config = window.guestBookingConfig || {};
        const currencySymbol = config.currencySymbol || '$';
        const advanceDays = parseInt(config.advanceBookingDays || 30, 10);
        const routes = config.routes || {};

        function getCsrfToken() {
            if (config.csrfToken) return config.csrfToken;
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta && meta.content) return meta.content;
            const tokenInput = document.querySelector('input[name="_token"]');
            if (tokenInput && tokenInput.value) return tokenInput.value;
            return '';
        }

        // --- State Variables ---
        let cart = [];
        let allServices = [];
        let selectedCategory = 'all';
        let currentStep = 1;
        let selectedStaff = '';
        let selectedDate = '';
        let selectedTime = '';
        let availableSlots = [];
        let datesRendered = 0;

        // --- Stepper Update ---
        function updateStepper(step) {
            currentStep = step;
            [1, 2, 3].forEach(n => {
                const el = document.getElementById('step-indicator-' + n);
                if (!el) return;
                el.classList.remove('active', 'completed');
                if (n < step) el.classList.add('completed');
                else if (n === step) el.classList.add('active');
            });
            // Show/hide mobile category pills only on step 1
            const pills = document.getElementById('mobile-category-pills');
            if (pills) pills.style.display = step === 1 ? '' : 'none';
        }

        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            if (!field) return;
            const errorDiv = field.parentNode.querySelector('.error-message') || document.createElement('div');
            errorDiv.className = 'error-message text-red-500 text-sm mt-1';
            errorDiv.textContent = message;
            if (!field.parentNode.querySelector('.error-message')) {
                field.parentNode.appendChild(errorDiv);
            }
            field.classList.add('border-red-500');
        }

        function clearError(fieldId) {
            const field = document.getElementById(fieldId);
            if (!field) return;
            const errorDiv = field.parentNode.querySelector('.error-message');
            if (errorDiv) errorDiv.remove();
            field.classList.remove('border-red-500');
        }

        // --- Mobile Cart Modal (Drawer) Controls ---
        window.openMobileCartModal = function () {
            // Never open on desktop screens
            if (window.innerWidth >= 1024) return;
            const modal = document.getElementById('mobile-cart-modal');
            if (!modal || cart.length === 0) return;

            modal.style.display = 'block';
            requestAnimationFrame(function () {
                modal.classList.add('is-open');
            });
            document.body.style.overflow = 'hidden';
            document.body.classList.add('mobile-modal-open');
        };

        window.closeMobileCartModal = function () {
            const modal = document.getElementById('mobile-cart-modal');
            if (modal) {
                modal.classList.remove('is-open');
                setTimeout(function () {
                    if (!modal.classList.contains('is-open')) {
                        modal.style.display = 'none';
                    }
                }, 260);
            }
            document.body.style.overflow = '';
            document.body.classList.remove('mobile-modal-open');
        };

        // Trigger open mobile cart modal
        const mobileCartTrigger = document.getElementById('mobile-cart-trigger');
        if (mobileCartTrigger) {
            mobileCartTrigger.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                window.openMobileCartModal();
            });
        }

        // Close mobile cart modal via close button
        const mobileCartClose = document.getElementById('mobile-cart-close');
        if (mobileCartClose) {
            mobileCartClose.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                window.closeMobileCartModal();
            });
        }

        // Close mobile cart modal via backdrop click
        const mobileCartBackdrop = document.getElementById('mobile-cart-backdrop');
        if (mobileCartBackdrop) {
            mobileCartBackdrop.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                window.closeMobileCartModal();
            });
        }

        // Prevent sheet clicks from bubbling up
        const mobileCartSheet = document.querySelector('.mobile-cart-sheet');
        if (mobileCartSheet) {
            mobileCartSheet.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                window.closeMobileCartModal();
            }
        });

        // Window resize listener: if desktop, close mobile modal and hide mobile bar
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) {
                window.closeMobileCartModal();
                const mobileBar = document.getElementById('mobile-cart-bar');
                if (mobileBar) mobileBar.classList.remove('has-items');
                document.body.classList.remove('has-mobile-cart-bar');
            } else {
                if (cart.length > 0 && currentStep === 1) {
                    const mobileBar = document.getElementById('mobile-cart-bar');
                    if (mobileBar) mobileBar.classList.add('has-items');
                    document.body.classList.add('has-mobile-cart-bar');
                }
            }
        });

        // Proceed from mobile cart modal to step 2
        const mobileModalProceed = document.getElementById('mobile-modal-proceed');
        if (mobileModalProceed) {
            mobileModalProceed.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (cart.length === 0) return;
                window.closeMobileCartModal();
                if (currentStep === 1) {
                    showBookingOptions();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else if (currentStep === 2) {
                    showGuestDetails();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        }

        // Mobile floating cart bar - Book Now
        const mobileProceed = document.getElementById('mobile-cart-proceed');
        if (mobileProceed) {
            mobileProceed.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (cart.length === 0) return;
                window.closeMobileCartModal();
                showBookingOptions();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // --- Cart Management ---
        function addToCart(service) {
            const existingItem = cart.find(item => item.id === service.id);
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ ...service, quantity: 1 });
            }
            updateCartDisplay();
        }

        window.changeQuantity = function (index, delta) {
            if (!cart[index]) return;
            cart[index].quantity += delta;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            updateCartDisplay();
        };

        window.removeFromCart = function (index) {
            if (!cart[index]) return;
            cart.splice(index, 1);
            updateCartDisplay();
        };

        function updateCartDisplay() {
            const cartEmpty = document.getElementById('cart-empty');
            const cartItems = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');

            if (!cartEmpty || !cartItems || !cartTotal) return;

            if (cart.length === 0) {
                cartEmpty.style.display = 'block';
                cartItems.style.display = 'none';
                cartTotal.style.display = 'none';

                // Hide mobile cart bar and close modal if empty
                const mobileBar = document.getElementById('mobile-cart-bar');
                if (mobileBar) {
                    mobileBar.classList.remove('has-items');
                    document.body.classList.remove('has-mobile-cart-bar');
                }
                if (typeof window.closeMobileCartModal === 'function') {
                    window.closeMobileCartModal();
                }
                return;
            }

            cartEmpty.style.display = 'none';
            cartItems.style.display = 'block';
            cartTotal.style.display = 'block';

            let total = 0;
            let desktopItemsHtml = '';
            let mobileItemsHtml = '';

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                // Desktop sidebar cart items
                desktopItemsHtml += `
                    <div class="cart-item">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <div class="cart-item-name">${item.name}</div>
                                <div class="cart-item-price">${currencySymbol} ${parseFloat(item.price).toFixed(2)} × ${item.quantity}</div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="quantity-controls">
                                <button type="button" class="quantity-btn" onclick="changeQuantity(${index}, -1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="quantity-value">${item.quantity}</span>
                                <button type="button" class="quantity-btn" onclick="changeQuantity(${index}, 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <button type="button" class="remove-btn" onclick="removeFromCart(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;

                // Mobile drawer cart items
                mobileItemsHtml += `
                    <div class="mobile-cart-item">
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex-1 min-w-0">
                                <h4 class="mobile-cart-item-name font-semibold text-gray-900 text-sm leading-snug truncate">${item.name}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs font-bold text-teal-600">${currencySymbol} ${parseFloat(item.price).toFixed(2)}</span>
                                    ${item.duration ? `<span class="text-xs text-gray-400">· ${item.duration} mins</span>` : ''}
                                </div>
                            </div>
                            <button type="button" class="mobile-cart-delete-btn" onclick="removeFromCart(${index})" title="Remove item" aria-label="Remove item">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <div class="flex justify-between items-center mt-3 pt-2 border-t border-gray-100">
                            <span class="text-xs text-gray-500 font-medium">Subtotal: <strong class="text-gray-800">${currencySymbol} ${itemTotal.toFixed(2)}</strong></span>
                            <div class="mobile-cart-qty-pill">
                                <button type="button" class="mobile-cart-qty-btn" onclick="changeQuantity(${index}, -1)" aria-label="Decrease quantity">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span class="mobile-cart-qty-num">${item.quantity}</span>
                                <button type="button" class="mobile-cart-qty-btn" onclick="changeQuantity(${index}, 1)" aria-label="Increase quantity">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            // Update desktop cart
            cartItems.innerHTML = desktopItemsHtml;
            const totalAmountEl = document.getElementById('total-amount');
            if (totalAmountEl) totalAmountEl.textContent = total.toFixed(2);

            const hiddenServicesEl = document.getElementById('hidden-services');
            if (hiddenServicesEl) hiddenServicesEl.value = JSON.stringify(cart);

            // Update mobile cart drawer content
            const mobileCartItems = document.getElementById('mobile-cart-items');
            if (mobileCartItems) {
                mobileCartItems.innerHTML = mobileItemsHtml;
            }
            const mobileModalTotal = document.getElementById('mobile-modal-total');
            if (mobileModalTotal) {
                mobileModalTotal.textContent = currencySymbol + ' ' + total.toFixed(2);
            }
            const mobileModalCount = document.getElementById('mobile-modal-item-count');
            if (mobileModalCount) {
                const totalItems = cart.reduce((s, i) => s + i.quantity, 0);
                mobileModalCount.textContent = totalItems + (totalItems === 1 ? ' item selected' : ' items selected');
            }

            // Update mobile floating cart bar
            const mobileBar = document.getElementById('mobile-cart-bar');
            const mobileCount = document.getElementById('mobile-cart-count');
            const mobileBadge = document.getElementById('mobile-cart-badge');
            const mobileTotal = document.getElementById('mobile-cart-total');
            if (mobileBar) {
                const totalItems = cart.reduce((s, i) => s + i.quantity, 0);
                if (mobileCount) mobileCount.textContent = totalItems + (totalItems === 1 ? ' item' : ' items');
                if (mobileBadge) mobileBadge.textContent = totalItems;
                if (mobileTotal) mobileTotal.textContent = currencySymbol + ' ' + total.toFixed(2);

                // Show only on mobile screens (< 1024px) during step 1
                if (currentStep === 1 && window.innerWidth < 1024) {
                    mobileBar.classList.add('has-items');
                    document.body.classList.add('has-mobile-cart-bar');
                } else {
                    mobileBar.classList.remove('has-items');
                    document.body.classList.remove('has-mobile-cart-bar');
                }
            }
        }

        // --- Booking Form Submission ---
        const bookingForm = document.getElementById('booking-form');
        if (bookingForm) {
            bookingForm.addEventListener('submit', function (e) {
                e.preventDefault();

                // Clear previous errors
                clearError('phone');
                clearError('email');

                const phoneEl = document.getElementById('phone');
                const emailEl = document.getElementById('email');
                const phone = phoneEl ? phoneEl.value.trim() : '';
                const email = emailEl ? emailEl.value.trim() : '';

                let hasError = false;

                // Validate phone
                if (!phone) {
                    showError('phone', 'Phone number is required.');
                    hasError = true;
                } else if (window.customerPhoneHandler && !window.customerPhoneHandler.validatePhoneNumber(window.itiPhone, phoneEl, false)) {
                    showError('phone', 'Please enter a valid phone number.');
                    hasError = true;
                } else if (window.customerPhoneHandler) {
                    phoneEl.value = window.customerPhoneHandler.getFullNumber(phoneEl);
                }

                // Validate email if provided
                if (email && !validateEmail(email)) {
                    showError('email', 'Please enter a valid email address.');
                    hasError = true;
                }

                // Validate date and time
                if (!selectedDate) {
                    alert('Date is required.');
                    hasError = true;
                }
                if (!selectedTime) {
                    alert('Time is required.');
                    hasError = true;
                }

                if (hasError) return;

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn ? submitBtn.innerHTML : 'Submit Booking';
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                    submitBtn.disabled = true;
                }

                // Update hidden fields before creating FormData
                const firstNameEl = document.getElementById('first_name');
                const lastNameEl = document.getElementById('last_name');
                const firstName = firstNameEl ? firstNameEl.value.trim() : '';
                const lastName = lastNameEl ? lastNameEl.value.trim() : '';
                const hiddenName = document.getElementById('hidden-name');
                if (hiddenName) hiddenName.value = `${firstName} ${lastName}`.trim();

                const hiddenDate = document.getElementById('hidden-date');
                const hiddenTime = document.getElementById('hidden-time');
                const hiddenStaff = document.getElementById('hidden-staff');
                const hiddenServices = document.getElementById('hidden-services');

                if (hiddenDate) hiddenDate.value = selectedDate;
                if (hiddenTime) hiddenTime.value = selectedTime;
                if (hiddenStaff) hiddenStaff.value = selectedStaff;
                if (hiddenServices) hiddenServices.value = JSON.stringify(cart);

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        } else if (response.status === 422) {
                            return response.json().then(errData => {
                                let message = errData.message || 'Validation failed';
                                if (errData.errors) {
                                    message = Object.values(errData.errors).flat().join('\n');
                                }
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        title: 'Please Check Your Input',
                                        text: message,
                                        icon: 'warning',
                                        confirmButtonColor: '#000'
                                    });
                                } else {
                                    alert('Booking Error: ' + message);
                                }
                                if (submitBtn) {
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = false;
                                }
                                return Promise.reject(new Error('Validation error'));
                            });
                        } else if (response.status === 403) {
                            return response.json().then(errData => {
                                const userMessage = "We are currently unable to accept new online bookings. Please contact the salon directly for assistance.";
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        title: 'Booking Unavailable',
                                        text: userMessage,
                                        icon: 'info',
                                        confirmButtonColor: '#000',
                                        confirmButtonText: 'Okay, I understand'
                                    });
                                } else {
                                    alert(userMessage);
                                }
                                if (submitBtn) {
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = false;
                                }
                                return Promise.reject(new Error('Booking limit reached'));
                            });
                        } else {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            // Hide modal if visible (desktop flow)
                            const bModal = document.getElementById('booking-modal');
                            if (bModal && bModal.style.display !== 'none') bModal.style.display = 'none';
                            if (data.booking_id) {
                                const idEl = document.getElementById('success-booking-id');
                                if (idEl) idEl.textContent = data.booking_id;
                            }
                            const successModal = document.getElementById('success-modal');
                            if (successModal) successModal.style.display = 'flex';
                            cart = [];
                            updateCartDisplay();
                            // Reset step3 button
                            const s3btn = document.getElementById('step3-submit-btn');
                            if (s3btn) {
                                s3btn.innerHTML = '<i class="fas fa-calendar-check mr-2"></i>Confirm Booking';
                                s3btn.disabled = false;
                            }
                            this.reset();
                            // Go back to step 1
                            showServices();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to create booking'));
                            if (submitBtn) {
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                            }
                            const s3btn = document.getElementById('step3-submit-btn');
                            if (s3btn) {
                                s3btn.innerHTML = '<i class="fas fa-calendar-check mr-2"></i>Confirm Booking';
                                s3btn.disabled = false;
                            }
                        }
                    })
                    .catch(error => {
                        if (!error.message.includes('Validation error') && !error.message.includes('Booking limit reached')) {
                            alert('Failed to create booking. Please try again.');
                            const s3btn = document.getElementById('step3-submit-btn');
                            if (s3btn) {
                                s3btn.innerHTML = '<i class="fas fa-calendar-check mr-2"></i>Confirm Booking';
                                s3btn.disabled = false;
                            }
                        }
                        if (submitBtn) {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    });
            });
        }

        // Initialize Intl Tel Input via CustomerPhoneHandler
        if (window.customerPhoneHandler) {
            window.customerPhoneHandler.init();

            const phoneInput = document.querySelector("#phone");
            const checkPhoneInput = document.querySelector("#check-phone");
            const reschedulePhoneInput = document.querySelector("#reschedule-phone");

            if (phoneInput) {
                const phoneData = window.customerPhoneHandler.phoneInputs.find(p => p.input === phoneInput);
                if (phoneData) window.itiPhone = phoneData.iti;
            }

            if (checkPhoneInput) {
                const checkPhoneData = window.customerPhoneHandler.phoneInputs.find(p => p.input === checkPhoneInput);
                if (checkPhoneData) window.itiCheckPhone = checkPhoneData.iti;
            }

            if (reschedulePhoneInput) {
                const reschedulePhoneData = window.customerPhoneHandler.phoneInputs.find(p => p.input === reschedulePhoneInput);
                if (reschedulePhoneData) window.itiReschedulePhone = reschedulePhoneData.iti;
            }
        }

        // --- Slider Functionality ---
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slider img');
        const totalSlides = slides.length;

        function showSlide(index) {
            if (totalSlides === 0) return;
            slides.forEach(slide => slide.classList.remove('active'));
            slides[index].classList.add('active');
        }

        function nextSlide() {
            if (totalSlides === 0) return;
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }

        if (totalSlides > 0) {
            setInterval(nextSlide, 4000);
            showSlide(0);
        }

        // --- Check Appointment Form Submission ---
        const checkAppointmentForm = document.getElementById('check-appointment-form');
        if (checkAppointmentForm) {
            checkAppointmentForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const phone = document.getElementById('check-phone').value.trim();
                const email = document.getElementById('check-email').value.trim();

                clearError('check-phone');
                clearError('check-email');

                let hasError = false;

                if (!phone && !email) {
                    showError('check-phone', 'Please enter at least a phone number or email address.');
                    hasError = true;
                }

                if (phone && window.customerPhoneHandler && !window.customerPhoneHandler.validatePhoneNumber(window.itiCheckPhone, document.getElementById('check-phone'), false)) {
                    showError('check-phone', 'Please enter a valid phone number.');
                    hasError = true;
                } else if (phone && window.customerPhoneHandler) {
                    document.getElementById('check-phone').value = window.customerPhoneHandler.getFullNumber(document.getElementById('check-phone'));
                }

                if (email && !validateEmail(email)) {
                    showError('check-email', 'Please enter a valid email address.');
                    hasError = true;
                }

                if (hasError) return;

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Searching...';
                submitBtn.disabled = true;

                const formData = new FormData();
                if (phone) formData.append('phone', phone);
                if (email) formData.append('email', email);

                const checkUrl = routes.check || '/booking/check';

                fetch(checkUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;

                        if (data.bookings && data.bookings.length > 0) {
                            displayAppointments(data.bookings, data.customer);
                        } else {
                            document.getElementById('appointment-results').style.display = 'none';
                            document.getElementById('no-appointments').style.display = 'block';
                        }
                    })
                    .catch(error => {
                        alert('Failed to search appointments. Please try again.');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });
        }

        // Display appointments function
        function displayAppointments(bookings, customer) {
            const appointmentsList = document.getElementById('appointments-list');
            if (!appointmentsList) return;
            appointmentsList.innerHTML = '';

            bookings.forEach(booking => {
                const statusColors = {
                    'pending': 'bg-yellow-100 text-yellow-800',
                    'confirmed': 'bg-green-100 text-green-800',
                    'completed': 'bg-blue-100 text-blue-800',
                    'cancelled': 'bg-red-100 text-red-800'
                };

                const appointmentCard = document.createElement('div');
                appointmentCard.className = 'bg-white rounded-xl p-6 mb-4 shadow-md hover:shadow-lg transition-all';
                appointmentCard.innerHTML = `
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">${booking.service}</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-user-tie mr-1"></i>Staff: ${booking.staff}
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColors[booking.status] || 'bg-gray-100 text-gray-800'}">
                            ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Date</p>
                            <p class="font-semibold text-gray-900">
                                <i class="fas fa-calendar mr-1 text-purple-600"></i>${booking.date}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Time</p>
                            <p class="font-semibold text-gray-900">
                                <i class="fas fa-clock mr-1 text-purple-600"></i>${booking.time}
                            </p>
                        </div>
                    </div>
                    ${booking.status === 'pending' || booking.status === 'confirmed' ? `
                        <button type="button" onclick="rescheduleAppointment(this)"
                            data-booking-id="${booking.id}"
                            data-phone="${customer.phone}"
                            data-country-code="${customer.country_code || ''}"
                            data-email="${customer.email || ''}"
                            data-service-id="${booking.service_id}"
                            data-date="${booking.date}"
                            data-time="${booking.time}"
                            data-staff-id="${booking.staff_id || ''}"
                            class="btn-secondary w-full">
                            <i class="fas fa-calendar-alt mr-2"></i>Reschedule
                        </button>
                    ` : ''}
                `;
                appointmentsList.appendChild(appointmentCard);
            });

            document.getElementById('appointment-results').style.display = 'block';
            document.getElementById('no-appointments').style.display = 'none';
        }

        // Reschedule appointment function
        window.rescheduleAppointment = function (button) {
            const bookingId = button.dataset.bookingId;
            const phone = button.dataset.phone;
            const countryCode = button.dataset.countryCode;
            const email = button.dataset.email;
            const serviceId = button.dataset.serviceId;
            const date = button.dataset.date;
            const time = button.dataset.time;
            const staffId = button.dataset.staffId;

            const reschedulePhoneInput = document.getElementById('reschedule-phone');
            if (window.itiReschedulePhone) {
                if (countryCode) {
                    window.itiReschedulePhone.setNumber(countryCode + phone);
                } else {
                    window.itiReschedulePhone.setNumber(phone);
                }
            } else if (reschedulePhoneInput) {
                reschedulePhoneInput.value = phone;
            }
            const emailEl = document.getElementById('reschedule-email');
            if (emailEl) emailEl.value = email;
            const srvEl = document.getElementById('reschedule-service');
            if (srvEl) srvEl.value = serviceId;
            const dtEl = document.getElementById('reschedule-date');
            if (dtEl) dtEl.value = date;
            const tmEl = document.getElementById('reschedule-time');
            if (tmEl) tmEl.value = time;
            const stfEl = document.getElementById('reschedule-staff');
            if (stfEl) stfEl.value = staffId || '';
            const bkIdEl = document.getElementById('reschedule-booking-id');
            if (bkIdEl) bkIdEl.value = bookingId;

            const reschedModal = document.getElementById('reschedule-modal');
            if (reschedModal) reschedModal.style.display = 'flex';
        };

        // Handle reschedule form submission
        const rescheduleForm = document.getElementById('reschedule-form');
        if (rescheduleForm) {
            rescheduleForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Rescheduling...';
                submitBtn.disabled = true;

                const reschedulePhoneEl = document.getElementById('reschedule-phone');
                if (window.customerPhoneHandler && !window.customerPhoneHandler.validatePhoneNumber(window.itiReschedulePhone, reschedulePhoneEl, false)) {
                    alert('Please enter a valid phone number.');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    return;
                } else if (window.customerPhoneHandler && reschedulePhoneEl) {
                    reschedulePhoneEl.value = window.customerPhoneHandler.getFullNumber(reschedulePhoneEl);
                }

                const formData = new FormData(this);
                const bookingId = document.getElementById('reschedule-booking-id').value;

                let rescheduleUrl = routes.rescheduleTemplate || '/booking/reschedule/PLACEHOLDER_ID';
                rescheduleUrl = rescheduleUrl.replace('PLACEHOLDER_ID', bookingId);

                fetch(rescheduleUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        } else if (response.status === 422) {
                            return response.json().then(errData => {
                                let message = errData.message || 'Validation failed';
                                if (errData.errors) {
                                    message = Object.values(errData.errors).flat().join('\n');
                                }
                                alert('Reschedule Error: ' + message);
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                                return Promise.reject(new Error('Validation error'));
                            });
                        } else {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            const reschedModal = document.getElementById('reschedule-modal');
                            if (reschedModal) reschedModal.style.display = 'none';

                            const succModal = document.getElementById('success-modal');
                            if (succModal) succModal.style.display = 'flex';

                            // Refresh appointments
                            const phone = document.getElementById('check-phone').value.trim();
                            const email = document.getElementById('check-email').value.trim();
                            const formDataRefresh = new FormData();
                            if (phone) formDataRefresh.append('phone', phone);
                            if (email) formDataRefresh.append('email', email);

                            const checkUrl = routes.check || '/booking/check';
                            fetch(checkUrl, {
                                method: 'POST',
                                body: formDataRefresh,
                                headers: {
                                    'X-CSRF-TOKEN': getCsrfToken(),
                                    'Accept': 'application/json',
                                }
                            })
                                .then(response => response.json())
                                .then(refreshData => {
                                    if (refreshData.bookings && refreshData.bookings.length > 0) {
                                        displayAppointments(refreshData.bookings, refreshData.customer);
                                    }
                                });
                        } else {
                            alert('Error: ' + (data.message || 'Failed to reschedule booking'));
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        if (!error.message.includes('Validation error')) {
                            alert('Failed to reschedule booking. Please try again.');
                        }
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });
        }

        // Close reschedule modal
        const closeReschedule = document.getElementById('close-reschedule');
        if (closeReschedule) {
            closeReschedule.addEventListener('click', function () {
                document.getElementById('reschedule-modal').style.display = 'none';
            });
        }

        const cancelReschedule = document.getElementById('cancel-reschedule');
        if (cancelReschedule) {
            cancelReschedule.addEventListener('click', function () {
                document.getElementById('reschedule-modal').style.display = 'none';
            });
        }

        // --- Load & Display Services ---
        loadServices();

        // Category click handlers (desktop sidebar)
        const categoryList = document.getElementById('category-list');
        if (categoryList) {
            categoryList.addEventListener('click', function (e) {
                const categoryItem = e.target.closest('.category-item');
                if (categoryItem) {
                    const catId = categoryItem.dataset.category;
                    loadServices(catId);
                }
            });
        }

        // Mobile category pills click handler
        const mobilePills = document.getElementById('mobile-category-pills');
        if (mobilePills) {
            mobilePills.addEventListener('click', function (e) {
                const pill = e.target.closest('.mobile-cat-pill');
                if (!pill) return;
                mobilePills.querySelectorAll('.mobile-cat-pill').forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                const catId = pill.dataset.category;
                loadServices(catId === 'all' ? 'all' : catId);
                // Sync desktop sidebar
                document.querySelectorAll('.category-item').forEach(c => c.classList.remove('active'));
                if (catId !== 'all') {
                    const desktopItem = document.querySelector(`.category-item[data-category="${catId}"]`);
                    if (desktopItem) desktopItem.classList.add('active');
                }
            });
        }

        function loadServices(categoryId = 'all') {
            if (categoryId !== 'all' && document.getElementById(`category-section-${categoryId}`)) {
                const targetSection = document.getElementById(`category-section-${categoryId}`);
                const yOffset = -80;
                const y = targetSection.getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({ top: y, behavior: 'smooth' });

                document.querySelectorAll('.category-item').forEach(c => c.classList.remove('active'));
                const activeItem = document.querySelector(`.category-item[data-category="${categoryId}"]`);
                if (activeItem) activeItem.classList.add('active');
                return;
            }

            const servicesSection = document.getElementById('services-section');
            if (!servicesSection) return;
            servicesSection.innerHTML = '<div class="spinner"></div>';

            const servicesUrl = (routes.services || '/booking/services') + '?category=all';

            fetch(servicesUrl, {
                headers: { 'Accept': 'application/json' }
            })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    displayServices(data);
                })
                .catch(error => {
                    servicesSection.innerHTML = '<p class="text-red-500 text-center">Error loading services. Please try again.</p>';
                });
        }

        function displayServices(services) {
            allServices = services;
            const servicesSection = document.getElementById('services-section');
            if (!servicesSection) return;
            servicesSection.innerHTML = '';

            if (services.length === 0) {
                servicesSection.innerHTML = '<p class="text-gray-500 text-center py-8">No services available.</p>';
                return;
            }

            const groupedServices = {};
            services.forEach(service => {
                const catName = service.category ? service.category.name : 'Other Services';
                const catId = service.category ? service.category.id : 'other';

                if (!groupedServices[catId]) {
                    groupedServices[catId] = {
                        name: catName,
                        services: []
                    };
                }
                groupedServices[catId].services.push(service);
            });

            Object.keys(groupedServices).forEach(catId => {
                const group = groupedServices[catId];

                const sectionDiv = document.createElement('div');
                sectionDiv.id = `category-section-${catId}`;
                sectionDiv.className = 'category-section mb-8';

                const header = document.createElement('h3');
                header.className = 'service-category-header';
                header.textContent = group.name;
                sectionDiv.appendChild(header);

                group.services.forEach((service, index) => {
                    const serviceItem = document.createElement('div');
                    serviceItem.className = 'service-list-item fade-in-up';
                    serviceItem.style.animationDelay = `${index * 0.05}s`;
                    serviceItem.innerHTML = `
                        <div class="service-info">
                            <h4 class="service-name">${service.name}</h4>
                            <div class="service-meta">
                                ${service.duration ? `${service.duration} Mins` : ''}
                            </div>
                        </div>
                        <div class="service-action">
                            <div class="service-price">${currencySymbol} ${parseFloat(service.price).toFixed(2)}</div>
                            <button type="button" class="add-btn" data-service-id="${service.id}">
                                Add
                            </button>
                        </div>
                    `;
                    sectionDiv.appendChild(serviceItem);
                });

                servicesSection.appendChild(sectionDiv);
            });

            document.querySelectorAll('.add-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const serviceId = this.dataset.serviceId;
                    const service = allServices.find(s => s.id == serviceId);
                    if (service) {
                        addToCart(service);
                    }
                });
            });

            initScrollSpy();
        }

        function initScrollSpy() {
            const sections = document.querySelectorAll('.category-section');
            const navItems = document.querySelectorAll('.category-item');

            const observerOptions = {
                root: null,
                rootMargin: '-20% 0px -70% 0px',
                threshold: 0
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const id = entry.target.id.replace('category-section-', '');
                        navItems.forEach(item => item.classList.remove('active'));
                        const activeItem = document.querySelector(`.category-item[data-category="${id}"]`);
                        if (activeItem) activeItem.classList.add('active');
                    }
                });
            }, observerOptions);

            sections.forEach(section => observer.observe(section));
        }

        // --- Step Navigation & View Switching ---
        const proceedBooking = document.getElementById('proceed-booking');
        if (proceedBooking) {
            proceedBooking.addEventListener('click', function () {
                if (cart.length === 0) {
                    alert('Please add services to your cart first.');
                    return;
                }
                if (currentStep === 1) {
                    showBookingOptions();
                } else if (currentStep === 2) {
                    if (!selectedDate) { alert('Please select a date.'); return; }
                    if (!selectedTime) { alert('Please select a time slot.'); return; }
                    showGuestDetails();
                } else if (currentStep === 3) {
                    const s3btn = document.getElementById('step3-submit-btn');
                    if (s3btn) s3btn.click();
                }
            });
        }

        const step2Back = document.getElementById('step2-back-btn');
        if (step2Back) step2Back.addEventListener('click', showServices);

        const step2Next = document.getElementById('step2-next-btn');
        if (step2Next) {
            step2Next.addEventListener('click', function () {
                if (!selectedDate) { alert('Please select a date.'); return; }
                if (!selectedTime) { alert('Please select a time slot.'); return; }
                showGuestDetails();
            });
        }

        const step3Back = document.getElementById('step3-back-btn');
        if (step3Back) step3Back.addEventListener('click', showBookingOptions);

        const step3Submit = document.getElementById('step3-submit-btn');
        if (step3Submit) {
            step3Submit.addEventListener('click', function () {
                const fn = document.getElementById('step3_first_name');
                const ph = document.getElementById('step3_phone');
                if (!fn || !fn.value.trim()) {
                    alert('First name is required.');
                    if (fn) fn.focus();
                    return;
                }
                if (!ph || !ph.value.trim()) {
                    alert('Phone number is required.');
                    if (ph) ph.focus();
                    return;
                }

                const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.value = val; };
                const firstName = fn.value.trim();
                const lastName = (document.getElementById('step3_last_name') || {}).value || '';
                setVal('first_name', firstName);
                setVal('last_name', lastName);
                setVal('hidden-name', (firstName + ' ' + lastName).trim());
                setVal('phone', ph.value.trim());
                const emailEl = document.getElementById('step3_email');
                setVal('email', emailEl ? emailEl.value.trim() : '');
                const addrEl = document.getElementById('step3_address');
                setVal('address', addrEl ? addrEl.value.trim() : '');
                const notesEl = document.getElementById('step3_notes');
                setVal('modal-notes', notesEl ? notesEl.value.trim() : '');

                setVal('hidden-date', selectedDate);
                setVal('hidden-time', selectedTime);
                setVal('hidden-staff', selectedStaff);
                setVal('hidden-services', JSON.stringify(cart));

                const bookingFrm = document.getElementById('booking-form');
                if (bookingFrm) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                    this.disabled = true;
                    bookingFrm.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
                }
            });
        }

        function updateCartBookingPreview() {
            const pStaff = document.getElementById('cart-preview-staff');
            const pDate = document.getElementById('cart-preview-date');
            const pTime = document.getElementById('cart-preview-time');

            if (pStaff) {
                const staffRadio = document.querySelector('input[name="staff_selection"]:checked');
                let staffLabel = 'Any Staff';
                if (staffRadio && staffRadio.value !== '') {
                    const optLabel = staffRadio.closest('label');
                    staffLabel = optLabel ? (optLabel.querySelector('.staff-name')?.textContent.trim() || optLabel.textContent.trim()) : 'Any Staff';
                }
                pStaff.textContent = staffLabel;
            }
            if (pDate) {
                if (selectedDate) {
                    try {
                        const d = new Date(selectedDate + 'T00:00:00');
                        pDate.textContent = d.toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' });
                    } catch (e) {
                        pDate.textContent = selectedDate;
                    }
                } else {
                    pDate.textContent = 'Select date';
                }
            }
            if (pTime) {
                if (selectedTime) {
                    try {
                        const [hours, minutes] = selectedTime.split(':');
                        const td = new Date();
                        td.setHours(parseInt(hours, 10));
                        td.setMinutes(parseInt(minutes, 10));
                        pTime.textContent = td.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    } catch (e) {
                        pTime.textContent = selectedTime;
                    }
                } else {
                    pTime.textContent = 'Select time';
                }
            }
        }

        function showBookingOptions() {
            updateStepper(2);

            const mainContainer = document.getElementById('main-content-container');
            if (mainContainer) {
                mainContainer.classList.remove('step-1-active', 'step-3-active');
                mainContainer.classList.add('step-2-active');
            }

            document.getElementById('services-section').style.display = 'none';
            document.getElementById('category-sidebar-container').style.display = 'none';
            if (document.getElementById('guest-details-section'))
                document.getElementById('guest-details-section').style.display = 'none';

            const bookingSection = document.getElementById('booking-options-section');
            if (bookingSection) {
                bookingSection.style.display = 'block';
                bookingSection.classList.remove('lg:col-span-6', 'lg:col-span-9');
                bookingSection.classList.add('lg:col-span-8');
            }

            const cartSection = document.querySelector('.cart-section');
            if (cartSection) {
                cartSection.classList.remove('lg:col-span-3');
                cartSection.classList.add('lg:col-span-4');
            }

            const cartPreview = document.getElementById('cart-booking-preview');
            if (cartPreview) {
                cartPreview.style.display = 'block';
            }
            updateCartBookingPreview();

            // Hide mobile cart bar on step 2
            const mobileBar = document.getElementById('mobile-cart-bar');
            if (mobileBar) {
                mobileBar.classList.remove('has-items');
                document.body.classList.remove('has-mobile-cart-bar');
            }

            filterStaffByServices();

            const proceedBtn = document.getElementById('proceed-booking');
            if (proceedBtn) {
                proceedBtn.innerHTML = '<i class="fas fa-arrow-right mr-2"></i>Next: Your Details';
            }

            if (document.getElementById('date-selection-container').children.length === 0) {
                renderDates();
            }

            const cartHeader = document.querySelector('.cart-header');
            if (cartHeader && !document.getElementById('back-to-services')) {
                const backBtn = document.createElement('button');
                backBtn.id = 'back-to-services';
                backBtn.className = 'text-xs text-teal-600 hover:text-teal-800 float-right font-semibold flex items-center gap-1';
                backBtn.innerHTML = '<i class="fas fa-chevron-left text-[10px]"></i> Change Services';
                backBtn.onclick = showServices;
                cartHeader.appendChild(backBtn);
            } else if (document.getElementById('back-to-services')) {
                document.getElementById('back-to-services').style.display = 'inline-flex';
            }
        }

        function filterStaffByServices() {
            const staffOptions = document.querySelectorAll('.staff-option');
            if (cart.length === 0) {
                staffOptions.forEach(opt => opt.style.display = 'flex');
                return;
            }

            let eligibleStaffIds = cart[0].staff ? cart[0].staff.map(s => s.id) : [];

            for (let i = 1; i < cart.length; i++) {
                const serviceStaffIds = cart[i].staff ? cart[i].staff.map(s => s.id) : [];
                eligibleStaffIds = eligibleStaffIds.filter(id => serviceStaffIds.includes(id));
            }

            staffOptions.forEach(opt => {
                const staffId = parseInt(opt.dataset.staffId, 10);
                if (eligibleStaffIds.includes(staffId)) {
                    opt.style.display = 'flex';
                } else {
                    opt.style.display = 'none';
                }
            });

            const currentSelection = document.querySelector('input[name="staff_selection"]:checked');
            if (currentSelection && currentSelection.value !== '') {
                const currentOption = currentSelection.closest('.staff-option');
                if (currentOption && currentOption.style.display === 'none') {
                    const anyStaffRadio = document.querySelector('input[name="staff_selection"][value=""]');
                    if (anyStaffRadio) anyStaffRadio.checked = true;
                    selectedStaff = '';
                    fetchInlineSlots();
                }
            }
        }

        function showServices() {
            updateStepper(1);

            const mainContainer = document.getElementById('main-content-container');
            if (mainContainer) {
                mainContainer.classList.remove('step-2-active', 'step-3-active');
                mainContainer.classList.add('step-1-active');
            }

            document.getElementById('services-section').style.display = 'grid';
            document.getElementById('category-sidebar-container').style.display = 'block';
            if (document.getElementById('guest-details-section'))
                document.getElementById('guest-details-section').style.display = 'none';

            const bookingSection = document.getElementById('booking-options-section');
            if (bookingSection) {
                bookingSection.style.display = 'none';
                bookingSection.classList.remove('lg:col-span-8', 'lg:col-span-9');
                bookingSection.classList.add('lg:col-span-6');
            }

            const cartSection = document.querySelector('.cart-section');
            if (cartSection) {
                cartSection.classList.remove('lg:col-span-4');
                cartSection.classList.add('lg:col-span-3');
            }

            const cartPreview = document.getElementById('cart-booking-preview');
            if (cartPreview) {
                cartPreview.style.display = 'none';
            }

            const proceedBtn = document.getElementById('proceed-booking');
            if (proceedBtn) {
                proceedBtn.innerHTML = '<i class="fas fa-calendar-check mr-2"></i>Select Staff, Date &amp; Time';
            }

            // Show mobile cart bar only on mobile
            if (cart.length > 0 && window.innerWidth < 1024) {
                const mobileBar = document.getElementById('mobile-cart-bar');
                if (mobileBar) {
                    mobileBar.classList.add('has-items');
                    document.body.classList.add('has-mobile-cart-bar');
                }
            }

            const backBtn = document.getElementById('back-to-services');
            if (backBtn) backBtn.style.display = 'none';
        }

        function showGuestDetails() {
            updateStepper(3);

            const mainContainer = document.getElementById('main-content-container');
            if (mainContainer) {
                mainContainer.classList.remove('step-1-active', 'step-2-active');
                mainContainer.classList.add('step-3-active');
            }

            document.getElementById('services-section').style.display = 'none';
            document.getElementById('category-sidebar-container').style.display = 'none';
            const bookingSection = document.getElementById('booking-options-section');
            if (bookingSection) {
                bookingSection.style.display = 'none';
                bookingSection.classList.remove('lg:col-span-8', 'lg:col-span-9');
                bookingSection.classList.add('lg:col-span-6');
            }

            const guestSection = document.getElementById('guest-details-section');
            if (guestSection) {
                guestSection.style.display = 'block';
                guestSection.classList.remove('lg:col-span-6');
                guestSection.classList.add('lg:col-span-8');
            }

            const cartSection = document.querySelector('.cart-section');
            if (cartSection) {
                cartSection.classList.remove('lg:col-span-3');
                cartSection.classList.add('lg:col-span-4');
            }

            const cartPreview = document.getElementById('cart-booking-preview');
            if (cartPreview) {
                cartPreview.style.display = 'block';
            }
            updateCartBookingPreview();

            const summaryServices = document.getElementById('summary-services');
            if (summaryServices) {
                const names = cart.map(i => i.name + (i.quantity > 1 ? ' ×' + i.quantity : '')).join(', ');
                summaryServices.textContent = names || '-';
            }
            const staffInput = document.querySelector('input[name="staff_selection"]:checked');
            const staffLabel = staffInput && staffInput.closest('label') ? staffInput.closest('label').textContent.trim() : 'Any Available';
            const summaryStaff = document.getElementById('summary-staff');
            if (summaryStaff) summaryStaff.textContent = staffLabel;

            const summaryDate = document.getElementById('summary-date');
            if (summaryDate) summaryDate.textContent = selectedDate || '-';

            const summaryTime = document.getElementById('summary-time');
            if (summaryTime) summaryTime.textContent = selectedTime || '-';

            const summaryTotal = document.getElementById('summary-total');
            if (summaryTotal) {
                const tot = cart.reduce((s, i) => s + i.price * i.quantity, 0);
                summaryTotal.textContent = currencySymbol + ' ' + tot.toFixed(2);
            }

            const proceedBtn = document.getElementById('proceed-booking');
            if (proceedBtn) {
                proceedBtn.innerHTML = '<i class="fas fa-calendar-check mr-2"></i>Confirm Booking';
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // --- Date & Time Slot Rendering ---
        function renderDates() {
            const container = document.getElementById('date-selection-container');
            if (!container) return;

            const today = new Date();
            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            const batchSize = 14;
            const remainingDays = advanceDays - (datesRendered || 0);
            const currentBatch = Math.min(batchSize, remainingDays);

            if (currentBatch <= 0) {
                const showMoreBtn = document.getElementById('show-more-dates');
                if (showMoreBtn) showMoreBtn.style.display = 'none';
                return;
            }

            for (let i = 0; i < currentBatch; i++) {
                const date = new Date(today);
                date.setDate(today.getDate() + (datesRendered || 0) + i);

                const dateString = date.toISOString().split('T')[0];
                const dayName = days[date.getDay()];
                const dayNum = date.getDate();
                const monthName = months[date.getMonth()];

                const card = document.createElement('div');
                card.className = 'date-card';
                card.dataset.date = dateString;
                card.innerHTML = `
                    <div class="date-day-name text-xs text-gray-500 uppercase mb-1">${dayName}</div>
                    <div class="date-day">${String(dayNum).padStart(2, '0')}</div>
                    <div class="date-month">${monthName}</div>
                `;

                card.addEventListener('click', function () {
                    document.querySelectorAll('.date-card').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    selectedDate = this.dataset.date;
                    const hiddenDateEl = document.getElementById('hidden-date');
                    if (hiddenDateEl) hiddenDateEl.value = selectedDate;

                    const dateSummary = document.getElementById('date-selected-summary');
                    const timeDateLabel = document.getElementById('time-card-date-label');
                    try {
                        const d = new Date(selectedDate + 'T00:00:00');
                        const formattedDate = d.toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' });
                        if (dateSummary) dateSummary.textContent = 'Selected: ' + formattedDate;
                        if (timeDateLabel) timeDateLabel.textContent = formattedDate;
                    } catch (e) {
                        if (dateSummary) dateSummary.textContent = selectedDate;
                        if (timeDateLabel) timeDateLabel.textContent = selectedDate;
                    }

                    updateCartBookingPreview();
                    fetchInlineSlots();
                });

                container.appendChild(card);
            }
            datesRendered += currentBatch;

            if (datesRendered >= advanceDays) {
                const showMoreBtn = document.getElementById('show-more-dates');
                if (showMoreBtn) showMoreBtn.style.display = 'none';
            }
        }

        // Hook up date horizontal scroll buttons
        const dateScrollLeft = document.getElementById('date-scroll-left');
        const dateScrollRight = document.getElementById('date-scroll-right');
        if (dateScrollLeft) {
            dateScrollLeft.addEventListener('click', function () {
                const c = document.getElementById('date-selection-container');
                if (c) c.scrollBy({ left: -260, behavior: 'smooth' });
            });
        }
        if (dateScrollRight) {
            dateScrollRight.addEventListener('click', function () {
                const c = document.getElementById('date-selection-container');
                if (c) c.scrollBy({ left: 260, behavior: 'smooth' });
            });
        }

        // Staff Selection Listener
        document.querySelectorAll('input[name="staff_selection"]').forEach(radio => {
            radio.addEventListener('change', function () {
                selectedStaff = this.value;
                const hiddenStaffEl = document.getElementById('hidden-staff');
                if (hiddenStaffEl) hiddenStaffEl.value = selectedStaff;

                // Toggle active card class
                document.querySelectorAll('.staff-card-option').forEach(opt => opt.classList.remove('active'));
                const parentCard = this.closest('.staff-card-option');
                if (parentCard) parentCard.classList.add('active');

                updateCartBookingPreview();

                if (selectedDate) {
                    fetchInlineSlots();
                }
            });
        });

        function fetchInlineSlots() {
            if (!selectedDate) return;

            const timeContainer = document.getElementById('time-selection-container');
            if (timeContainer) {
                timeContainer.innerHTML = '<div class="text-center py-6 bg-gray-50 rounded-xl"><i class="fas fa-spinner fa-spin text-2xl text-teal-600 mb-2 block"></i><span class="text-xs text-gray-500 font-medium">Checking available slots...</span></div>';
            }

            const params = new URLSearchParams();
            params.append('date', selectedDate);
            if (selectedStaff) params.append('staff_id', selectedStaff);

            cart.forEach((service, index) => {
                params.append(`services[${index}][id]`, service.id);
                params.append(`services[${index}][quantity]`, service.quantity);
            });

            const slotsUrl = (routes.slots || '/booking/slots') + `?${params.toString()}`;

            fetch(slotsUrl, {
                headers: { 'Accept': 'application/json' }
            })
                .then(res => res.json())
                .then(data => {
                    renderTimeSlots(data.slots || []);
                })
                .catch(err => {
                    if (timeContainer) {
                        timeContainer.innerHTML = '<div class="text-center text-red-500 py-6 bg-red-50 rounded-xl border border-red-100"><i class="fas fa-exclamation-circle text-xl mb-1 block"></i><span class="text-xs font-semibold">Unable to load time slots. Please try another date.</span></div>';
                    }
                });
        }

        function renderTimeSlots(slots) {
            const container = document.getElementById('time-selection-container');
            if (!container) return;
            container.innerHTML = '';

            if (slots.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-500 py-6 bg-gray-50 rounded-xl border border-dashed border-gray-200"><i class="far fa-calendar-times text-2xl text-gray-400 mb-2 block"></i><span class="text-sm font-medium">No available slots for this date. Please pick another day.</span></div>';
                return;
            }

            const earlySlots = slots.filter(time => parseInt(time.split(':')[0], 10) < 12);
            const lateSlots = slots.filter(time => parseInt(time.split(':')[0], 10) >= 12);

            let html = '';

            if (earlySlots.length > 0) {
                html += '<div class="time-subgroup-wrapper"><h5 class="time-section-label"><i class="fas fa-sun text-amber-500"></i> Morning Hours</h5><div class="time-grid">';
                earlySlots.forEach(time => {
                    html += createTimeButton(time);
                });
                html += '</div></div>';
            }

            if (lateSlots.length > 0) {
                html += '<div class="time-subgroup-wrapper"><h5 class="time-section-label"><i class="fas fa-moon text-indigo-500"></i> Afternoon &amp; Evening</h5><div class="time-grid">';
                lateSlots.forEach(time => {
                    html += createTimeButton(time);
                });
                html += '</div></div>';
            }

            container.innerHTML = html;

            container.querySelectorAll('.time-slot-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    selectedTime = this.dataset.time;
                    const hiddenTimeEl = document.getElementById('hidden-time');
                    if (hiddenTimeEl) hiddenTimeEl.value = selectedTime;

                    const slotDisplay = document.getElementById('selected-slot-display');
                    if (slotDisplay) {
                        try {
                            const [hours, minutes] = selectedTime.split(':');
                            const td = new Date();
                            td.setHours(parseInt(hours, 10));
                            td.setMinutes(parseInt(minutes, 10));
                            slotDisplay.textContent = td.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        } catch (e) {
                            slotDisplay.textContent = selectedTime;
                        }
                    }

                    updateCartBookingPreview();
                });
            });
        }

        function createTimeButton(time) {
            const [hours, minutes] = time.split(':');
            const date = new Date();
            date.setHours(parseInt(hours, 10));
            date.setMinutes(parseInt(minutes, 10));
            const formatted = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const isSelected = (selectedTime === time) ? ' active' : '';

            return `<button type="button" class="time-slot-btn${isSelected}" data-time="${time}">
                <i class="far fa-clock text-[11px] opacity-70"></i><span>${formatted}</span>
            </button>`;
        }

        // --- Modal Dismiss & Utility Listeners ---
        const closeSuccessBtn = document.getElementById('close-success');
        if (closeSuccessBtn) {
            closeSuccessBtn.addEventListener('click', function () {
                const sModal = document.getElementById('success-modal');
                if (sModal) sModal.style.display = 'none';
                window.location.reload();
            });
        }

        const showMoreDatesBtn = document.getElementById('show-more-dates');
        if (showMoreDatesBtn) {
            showMoreDatesBtn.addEventListener('click', function () {
                renderDates();
            });
        }

        const closeModalBtn = document.getElementById('close-modal');
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function () {
                const bModal = document.getElementById('booking-modal');
                if (bModal) bModal.style.display = 'none';
            });
        }

        const cancelBookingBtn = document.getElementById('cancel-booking');
        if (cancelBookingBtn) {
            cancelBookingBtn.addEventListener('click', function () {
                const bModal = document.getElementById('booking-modal');
                if (bModal) bModal.style.display = 'none';
            });
        }

        // Real-time field validation for booking form
        const phoneField = document.getElementById('phone');
        const emailField = document.getElementById('email');

        if (phoneField) {
            phoneField.addEventListener('blur', function () {
                const phone = this.value.trim();
                clearError('phone');
                if (phone && window.customerPhoneHandler && !window.customerPhoneHandler.validatePhoneNumber(window.itiPhone, this, false)) {
                    showError('phone', 'Please enter a valid phone number.');
                }
            });
        }

        if (emailField) {
            emailField.addEventListener('blur', function () {
                const email = this.value.trim();
                clearError('email');
                if (email && !validateEmail(email)) {
                    showError('email', 'Please enter a valid email address.');
                }
            });
        }

        // Close desktop booking modal on outside click
        const bookingModal = document.getElementById('booking-modal');
        if (bookingModal) {
            bookingModal.addEventListener('click', function (e) {
                if (e.target === this) {
                    this.style.display = 'none';
                }
            });
        }

        const successModal = document.getElementById('success-modal');
        if (successModal) {
            successModal.addEventListener('click', function (e) {
                if (e.target === this) {
                    this.style.display = 'none';
                    window.location.reload();
                }
            });
        }
    } catch (error) {
        console.error('Guest booking initialization error:', error);
    }
});
