/**
 * SaaS Salon Registration Logic & Real-time Validation
 */
document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------
    // 1. Timezone & Currency Auto-Detection
    // -------------------------------------------------------------
    (function () {
        const timezoneInput = document.getElementById('timezone');
        const currencyInput = document.getElementById('currency');
        if (!timezoneInput || !currencyInput) return;

        const tzToCurrency = {
            'Asia/Kolkata': 'INR',
            'Asia/Calcutta': 'INR',
            'America/New_York': 'USD',
            'America/Chicago': 'USD',
            'America/Denver': 'USD',
            'America/Los_Angeles': 'USD',
            'America/Phoenix': 'USD',
            'America/Anchorage': 'USD',
            'Pacific/Honolulu': 'USD',
            'Europe/London': 'GBP',
            'Europe/Paris': 'EUR',
            'Europe/Berlin': 'EUR',
            'Europe/Rome': 'EUR',
            'Europe/Madrid': 'EUR',
            'Asia/Dubai': 'AED',
            'Asia/Riyadh': 'SAR',
            'Asia/Qatar': 'QAR',
            'Asia/Singapore': 'SGD',
            'Australia/Sydney': 'AUD',
            'Australia/Melbourne': 'AUD',
            'America/Toronto': 'CAD',
            'America/Vancouver': 'CAD'
        };

        function detectLocalization() {
            try {
                let localTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                if (localTimezone === 'Asia/Calcutta') {
                    localTimezone = 'Asia/Kolkata';
                }

                if (localTimezone && (localTimezone.includes('/') || localTimezone === 'UTC')) {
                    timezoneInput.value = localTimezone;
                    const suggestedCurrency = tzToCurrency[localTimezone];
                    if (suggestedCurrency) {
                        currencyInput.value = suggestedCurrency;
                    }
                }
            } catch (e) {
                // Ignore detection errors, default values in hidden fields will be used
            }
        }

        window.detectLocalization = detectLocalization;
        detectLocalization();
    })();

    // -------------------------------------------------------------
    // 2. Plan Selection & Business Type Filtering
    // -------------------------------------------------------------
    const planOptions = document.querySelectorAll('.plan-option');
    const businessTypeOptions = document.querySelectorAll('.business-type-option');

    planOptions.forEach(option => {
        option.addEventListener('click', () => {
            planOptions.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');
            const radio = option.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });

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
                if (radioInput && radioInput.checked) {
                    isAnyVisibleChecked = true;
                }
            } else {
                option.style.display = 'none';
                option.classList.remove('selected');
                if (radioInput) radioInput.checked = false;
            }
        });

        if (!isAnyVisibleChecked && firstVisibleOption) {
            firstVisibleOption.classList.add('selected');
            const radio = firstVisibleOption.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        }
    }

    if (businessTypeOptions.length > 0) {
        businessTypeOptions.forEach(option => {
            option.addEventListener('click', () => {
                businessTypeOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                const radio = option.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;
                filterPlans();
            });
        });
        filterPlans();
    }

    // -------------------------------------------------------------
    // 3. Password Show/Hide Toggle (SVG Icons)
    // -------------------------------------------------------------
    const eyeOpenSvg = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
    const eyeSlashSvg = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.253 0 2.426.235 3.521.657m-.943 2.29A3 3 0 1012 15h.01M3 3l18 18" />`;

    const passwordInput = document.getElementById('password');
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', function () {
            const isPwd = passwordInput.type === 'password';
            passwordInput.type = isPwd ? 'text' : 'password';
            const svg = document.getElementById('eyeIconPassword');
            if (svg) svg.innerHTML = isPwd ? eyeSlashSvg : eyeOpenSvg;
        });
    }

    const passwordConfirmInput = document.getElementById('password_confirmation');
    const togglePasswordConfirmBtn = document.getElementById('togglePasswordConfirmBtn');
    if (togglePasswordConfirmBtn && passwordConfirmInput) {
        togglePasswordConfirmBtn.addEventListener('click', function () {
            const isPwd = passwordConfirmInput.type === 'password';
            passwordConfirmInput.type = isPwd ? 'text' : 'password';
            const svg = document.getElementById('eyeIconConfirm');
            if (svg) svg.innerHTML = isPwd ? eyeSlashSvg : eyeOpenSvg;
        });
    }

    // -------------------------------------------------------------
    // 4. Form Elements & Remote Validation Setup
    // -------------------------------------------------------------
    const form = document.getElementById('registrationForm');
    const salonNameInput = document.getElementById('salon_name');
    const salonSlugInput = document.getElementById('salon_slug');
    const salonNameError = document.getElementById('salon_name_error');
    const salonSlugError = document.getElementById('salon_slug_error');
    const salonSlugSuccess = document.getElementById('salon_slug_success');
    const salonSlugHint = document.getElementById('salon_slug_hint');

    const nameInput = document.getElementById('name');
    const nameError = document.getElementById('name_error');

    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('email_error');
    const emailSuccess = document.getElementById('email_success');
    const emailSpinner = document.getElementById('email_spinner');

    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phone_error');
    const phoneSuccess = document.getElementById('phone_success');
    const phoneSpinner = document.getElementById('phone_spinner');

    const passwordError = document.getElementById('password_error');
    const passwordCriteriaBox = document.getElementById('password_criteria');
    const passwordConfirmError = document.getElementById('password_confirmation_error');
    const passwordConfirmSuccess = document.getElementById('password_confirmation_success');

    let isEmailAvailable = null;
    let isPhoneAvailable = null;
    let isSlugAvailable = null;

    let emailDebounceTimer = null;
    let phoneDebounceTimer = null;
    let slugDebounceTimer = null;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
        document.querySelector('input[name="_token"]')?.value;
    const availabilityUrl = form?.getAttribute('data-availability-url') || '/register-salon/check-availability';

    // -------------------------------------------------------------
    // 5. Visual State UI Helpers
    // -------------------------------------------------------------
    function setFieldInvalid(input, errorEl, successEl, message) {
        if (!input) return;
        input.classList.remove('reg-input-success', 'border-slate-200', 'border-emerald-500', 'focus:border-purple-500', 'focus:ring-purple-500');
        input.classList.add('reg-input-error');

        if (errorEl) {
            errorEl.innerHTML = `<i class="fa-solid fa-circle-exclamation text-xs"></i> <span>${message}</span>`;
            errorEl.classList.remove('hidden');
        }
        if (successEl) {
            successEl.classList.add('hidden');
        }
        if (input === salonSlugInput && salonSlugHint) {
            salonSlugHint.classList.add('hidden');
        }
    }

    function setFieldValid(input, errorEl, successEl, message) {
        if (!input) return;
        input.classList.remove('reg-input-error', 'border-slate-200', 'border-rose-500', 'focus:border-rose-500', 'focus:ring-rose-500');
        input.classList.add('reg-input-success');

        if (errorEl) {
            errorEl.classList.add('hidden');
        }
        if (successEl) {
            if (message) {
                successEl.innerHTML = `<i class="fa-solid fa-circle-check text-xs"></i> <span>${message}</span>`;
                successEl.classList.remove('hidden');
            } else {
                successEl.classList.add('hidden');
            }
        }
        if (input === salonSlugInput && salonSlugHint) {
            salonSlugHint.classList.add('hidden');
        }
    }

    function resetFieldState(input, errorEl, successEl) {
        if (!input) return;
        input.classList.remove('reg-input-error', 'reg-input-success', 'border-rose-500', 'border-emerald-500', 'focus:border-rose-500', 'focus:ring-rose-500');
        input.classList.add('border-slate-200');

        if (errorEl) errorEl.classList.add('hidden');
        if (successEl) successEl.classList.add('hidden');

        if (input === salonSlugInput && salonSlugHint) {
            salonSlugHint.classList.remove('hidden');
        }
    }

    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }

    // -------------------------------------------------------------
    // 6. Salon Name & Slug Validation
    // -------------------------------------------------------------
    function validateSalonName(isBlur = false) {
        if (!salonNameInput) return true;
        const val = salonNameInput.value.trim();

        if (!val) {
            if (isBlur) {
                setFieldInvalid(salonNameInput, salonNameError, null, 'Salon name is required.');
            } else {
                resetFieldState(salonNameInput, salonNameError, null);
            }
            return false;
        }

        if (val.length < 2) {
            setFieldInvalid(salonNameInput, salonNameError, null, 'Salon name must be at least 2 characters.');
            return false;
        }

        setFieldValid(salonNameInput, salonNameError, null, '');
        return true;
    }

    function validateSalonSlug(isBlur = false) {
        if (!salonSlugInput) return true;
        const val = slugify(salonSlugInput.value);
        salonSlugInput.value = val;

        clearTimeout(slugDebounceTimer);

        if (!val) {
            isSlugAvailable = false;
            if (isBlur) {
                setFieldInvalid(salonSlugInput, salonSlugError, salonSlugSuccess, 'Salon URL is required.');
            } else {
                resetFieldState(salonSlugInput, salonSlugError, salonSlugSuccess);
            }
            return false;
        }

        if (val.length < 2) {
            isSlugAvailable = false;
            setFieldInvalid(salonSlugInput, salonSlugError, salonSlugSuccess, 'Salon URL must be at least 2 characters.');
            return false;
        }

        resetFieldState(salonSlugInput, salonSlugError, salonSlugSuccess);

        slugDebounceTimer = setTimeout(() => {
            fetch(availabilityUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: 'salon_slug', value: val })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.available) {
                        isSlugAvailable = true;
                        setFieldValid(salonSlugInput, salonSlugError, salonSlugSuccess, 'Salon URL is available!');
                    } else {
                        isSlugAvailable = false;
                        setFieldInvalid(salonSlugInput, salonSlugError, salonSlugSuccess, data.message || 'This salon URL is already taken.');
                    }
                })
                .catch(() => {
                    isSlugAvailable = true;
                });
        }, 150);

        return true;
    }

    if (salonNameInput) {
        salonNameInput.addEventListener('input', function () {
            validateSalonName();
            if (salonSlugInput && (!salonSlugInput.value || salonSlugInput.value === slugify(this.oldValue || ''))) {
                salonSlugInput.value = slugify(this.value);
                validateSalonSlug();
            }
            this.oldValue = this.value;
        });
        salonNameInput.addEventListener('blur', function () {
            validateSalonName(true);
        });
    }

    if (salonSlugInput) {
        salonSlugInput.addEventListener('input', function () {
            validateSalonSlug();
        });
        salonSlugInput.addEventListener('blur', function () {
            validateSalonSlug(true);
        });
    }

    // -------------------------------------------------------------
    // 7. Owner Full Name Validation
    // -------------------------------------------------------------
    function validateName(isBlur = false) {
        if (!nameInput) return true;
        const val = nameInput.value.trim();

        if (!val) {
            if (isBlur) {
                setFieldInvalid(nameInput, nameError, null, 'Owner full name is required.');
            } else {
                resetFieldState(nameInput, nameError, null);
            }
            return false;
        }

        if (val.length < 2) {
            setFieldInvalid(nameInput, nameError, null, 'Full name must be at least 2 characters.');
            return false;
        }

        setFieldValid(nameInput, nameError, null, '');
        return true;
    }

    if (nameInput) {
        nameInput.addEventListener('input', function () {
            validateName();
        });
        nameInput.addEventListener('blur', function () {
            validateName(true);
        });
    }

    // -------------------------------------------------------------
    // 8. Email Address Validation with 150ms Availability Check
    // -------------------------------------------------------------
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    function validateEmail(isBlur = false) {
        if (!emailInput) return true;
        const val = emailInput.value.trim();
        clearTimeout(emailDebounceTimer);

        if (!val) {
            isEmailAvailable = false;
            if (emailSpinner) emailSpinner.classList.add('hidden');
            if (isBlur) {
                setFieldInvalid(emailInput, emailError, emailSuccess, 'Email address is required.');
            } else {
                resetFieldState(emailInput, emailError, emailSuccess);
            }
            return false;
        }

        if (!emailRegex.test(val)) {
            isEmailAvailable = false;
            if (emailSpinner) emailSpinner.classList.add('hidden');
            setFieldInvalid(emailInput, emailError, emailSuccess, 'Please enter a valid email address (e.g. name@domain.com).');
            return false;
        }

        resetFieldState(emailInput, emailError, emailSuccess);
        if (emailSpinner) emailSpinner.classList.remove('hidden');

        emailDebounceTimer = setTimeout(() => {
            fetch(availabilityUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: 'email', value: val })
            })
                .then(res => res.json())
                .then(data => {
                    if (emailSpinner) emailSpinner.classList.add('hidden');
                    if (data.available) {
                        isEmailAvailable = true;
                        setFieldValid(emailInput, emailError, emailSuccess, 'Email is available!');
                    } else {
                        isEmailAvailable = false;
                        setFieldInvalid(emailInput, emailError, emailSuccess, data.message || 'This email address is already registered.');
                    }
                    validatePassword();
                })
                .catch(() => {
                    if (emailSpinner) emailSpinner.classList.add('hidden');
                    isEmailAvailable = true;
                });
        }, 150);

        return true;
    }

    if (emailInput) {
        emailInput.addEventListener('input', function () {
            validateEmail();
        });
        emailInput.addEventListener('blur', function () {
            validateEmail(true);
        });
    }

    // -------------------------------------------------------------
    // 9. Phone Number Validation with 150ms Availability Check
    // -------------------------------------------------------------
    function validatePhone(isBlur = false) {
        if (!phoneInput) return true;
        const val = phoneInput.value.trim();
        clearTimeout(phoneDebounceTimer);

        if (!val) {
            isPhoneAvailable = false;
            if (phoneSpinner) phoneSpinner.classList.add('hidden');
            if (isBlur) {
                setFieldInvalid(phoneInput, phoneError, phoneSuccess, 'Phone number is required.');
            } else {
                resetFieldState(phoneInput, phoneError, phoneSuccess);
            }
            return false;
        }

        const digitsOnly = val.replace(/\D/g, '');
        if (digitsOnly.length < 7) {
            isPhoneAvailable = false;
            if (phoneSpinner) phoneSpinner.classList.add('hidden');
            setFieldInvalid(phoneInput, phoneError, phoneSuccess, 'Please enter a valid phone number with country code.');
            return false;
        }

        resetFieldState(phoneInput, phoneError, phoneSuccess);
        if (phoneSpinner) phoneSpinner.classList.remove('hidden');

        phoneDebounceTimer = setTimeout(() => {
            fetch(availabilityUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: 'phone', value: val })
            })
                .then(res => res.json())
                .then(data => {
                    if (phoneSpinner) phoneSpinner.classList.add('hidden');
                    if (data.available) {
                        isPhoneAvailable = true;
                        setFieldValid(phoneInput, phoneError, phoneSuccess, 'Phone number is available!');
                    } else {
                        isPhoneAvailable = false;
                        setFieldInvalid(phoneInput, phoneError, phoneSuccess, data.message || 'This phone number is already registered.');
                    }
                })
                .catch(() => {
                    if (phoneSpinner) phoneSpinner.classList.add('hidden');
                    isPhoneAvailable = true;
                });
        }, 150);

        return true;
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            validatePhone();
        });
        phoneInput.addEventListener('blur', function () {
            validatePhone(true);
        });
    }

    // -------------------------------------------------------------
    // 10. Password Validation & Requirements Checklist
    // -------------------------------------------------------------
    function togglePasswordCriteriaVisibility() {
        if (!passwordCriteriaBox || !passwordInput) return;
        const isFocused = document.activeElement === passwordInput;
        const hasValue = passwordInput.value.length > 0;
        if (isFocused || hasValue) {
            passwordCriteriaBox.classList.remove('hidden');
        } else {
            passwordCriteriaBox.classList.add('hidden');
        }
    }

    function updateCriterion(elId, passed) {
        const el = document.getElementById(elId);
        if (!el) return;
        const badge = el.querySelector('.pwd-rule-badge');
        if (passed) {
            el.classList.remove('is-invalid');
            el.classList.add('is-valid');
            if (badge) badge.textContent = '✓';
        } else {
            el.classList.remove('is-valid');
            el.classList.add('is-invalid');
            if (badge) badge.textContent = '✕';
        }
    }

    function validatePassword(isBlur = false) {
        if (!passwordInput) return true;
        togglePasswordCriteriaVisibility();

        const val = passwordInput.value;
        const emailVal = (emailInput?.value || '').trim().toLowerCase();

        if (val.length === 0) {
            if (isBlur) {
                setFieldInvalid(passwordInput, passwordError, null, 'Password is required.');
            } else {
                resetFieldState(passwordInput, passwordError, null);
            }
            validatePasswordConfirmation();
            return false;
        }

        const passLen = val.length >= 8;
        const passChars = /[a-zA-Z]/.test(val) && /[0-9]/.test(val);
        const passDiff = emailVal === '' || val.toLowerCase() !== emailVal;

        updateCriterion('pwd_len', passLen);
        updateCriterion('pwd_chars', passChars);
        updateCriterion('pwd_diff', passDiff);

        const allPassed = passLen && passChars && passDiff;

        if (!allPassed) {
            let msg = 'Password must meet all criteria below.';
            if (!passLen) msg = 'Password must be at least 8 characters.';
            else if (!passChars) msg = 'Password must contain both letters and numbers.';
            else if (!passDiff) msg = 'Password cannot match your email address.';

            setFieldInvalid(passwordInput, passwordError, null, msg);
            validatePasswordConfirmation();
            return false;
        }

        setFieldValid(passwordInput, passwordError, null, '');
        validatePasswordConfirmation();
        return true;
    }

    if (passwordInput) {
        passwordInput.addEventListener('focus', function () {
            togglePasswordCriteriaVisibility();
        });
        passwordInput.addEventListener('input', function () {
            validatePassword();
        });
        passwordInput.addEventListener('blur', function () {
            togglePasswordCriteriaVisibility();
            validatePassword(true);
        });
    }

    // -------------------------------------------------------------
    // 11. Confirm Password Real-time Matching
    // -------------------------------------------------------------
    function validatePasswordConfirmation(isBlur = false) {
        if (!passwordConfirmInput || !passwordInput) return true;
        const pwd = passwordInput.value;
        const conf = passwordConfirmInput.value;

        if (!conf) {
            if (isBlur) {
                setFieldInvalid(passwordConfirmInput, passwordConfirmError, passwordConfirmSuccess, 'Please confirm your password.');
            } else {
                resetFieldState(passwordConfirmInput, passwordConfirmError, passwordConfirmSuccess);
            }
            return false;
        }

        if (pwd !== conf) {
            setFieldInvalid(passwordConfirmInput, passwordConfirmError, passwordConfirmSuccess, 'Passwords do not match.');
            return false;
        }

        setFieldValid(passwordConfirmInput, passwordConfirmError, passwordConfirmSuccess, 'Passwords match!');
        return true;
    }

    if (passwordConfirmInput) {
        passwordConfirmInput.addEventListener('input', function () {
            validatePasswordConfirmation();
        });
        passwordConfirmInput.addEventListener('blur', function () {
            validatePasswordConfirmation(true);
        });
    }

    // -------------------------------------------------------------
    // 12. Form Submission & Animated Progress
    // -------------------------------------------------------------
    const modal = document.getElementById('progressModal');
    const logContainer = document.getElementById('logContainer');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Run client-side validations
            const isNameValid = validateSalonName(true);
            const isSlugValid = validateSalonSlug(true);
            const isOwnerValid = validateName(true);
            const isEmailValid = validateEmail(true);
            const isPhoneValid = validatePhone(true);
            const isPwdValid = validatePassword(true);
            const isPwdConfValid = validatePasswordConfirmation(true);

            const businessTypeChecked = document.querySelector('input[name="business_type"]:checked');
            const planChecked = document.querySelector('input[name="plan_id"]:checked');

            let firstInvalidEl = null;

            if (!isNameValid && !firstInvalidEl) firstInvalidEl = salonNameInput;
            if ((!isSlugValid || isSlugAvailable === false) && !firstInvalidEl) firstInvalidEl = salonSlugInput;
            if (!isOwnerValid && !firstInvalidEl) firstInvalidEl = nameInput;
            if ((!isEmailValid || isEmailAvailable === false) && !firstInvalidEl) firstInvalidEl = emailInput;
            if ((!isPhoneValid || isPhoneAvailable === false) && !firstInvalidEl) firstInvalidEl = phoneInput;
            if (!isPwdValid && !firstInvalidEl) firstInvalidEl = passwordInput;
            if (!isPwdConfValid && !firstInvalidEl) firstInvalidEl = passwordConfirmInput;

            if (firstInvalidEl) {
                firstInvalidEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalidEl.focus();
                return false;
            }

            if (!businessTypeChecked) {
                alert('Please select a business type (Salon, Barber, or Both).');
                return false;
            }

            if (!planChecked) {
                alert('Please select a subscription plan to continue.');
                return false;
            }

            // International phone handler validation
            if (window.customerPhoneHandler && !window.customerPhoneHandler.validateForm(form)) {
                if (phoneInput) {
                    setFieldInvalid(phoneInput, phoneError, phoneSuccess, 'Please enter a valid phone number with country code.');
                    phoneInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    phoneInput.focus();
                }
                return false;
            }

            // Show progress modal
            if (modal) modal.classList.remove('hidden');

            if (window.detectLocalization) {
                window.detectLocalization();
            }

            simulateProgress();

            setTimeout(() => {
                form.submit();
            }, 2200);
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
        }, 600);

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
        }, 300);
    }
});
