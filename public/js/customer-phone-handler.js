/**
 * Customer Phone Handler
 * Handles international phone number input with country code selection
 * Uses intl-tel-input library
 */

class CustomerPhoneHandler {
    constructor() {
        this.phoneInputs = [];
        this.defaultCountry = 'qa'; // Qatar
    }

    /**
     * Initialize intl-tel-input on all phone input fields
     */
    init() {
        // Initialize primary phone fields
        this.initPhoneInput('input[name="phone"]', 'input[name="country_code"]');

        // Initialize secondary phone fields
        this.initPhoneInput('input[name="secondary_number"]', 'input[name="secondary_country_code"]');

        // Initialize owner phone fields
        this.initPhoneInput('input[name="owner_phone"]', 'input[name="owner_country_code"]');
    }

    /**
     * Initialize intl-tel-input on a specific field
     * @param {string} phoneSelector - CSS selector for phone input
     * @param {string} countryCodeSelector - CSS selector for hidden country code input
     */
    initPhoneInput(phoneSelector, countryCodeSelector) {
        const phoneInputs = document.querySelectorAll(phoneSelector);

        phoneInputs.forEach(phoneInput => {
            if (!phoneInput || phoneInput.classList.contains('iti-initialized')) {
                return;
            }

            // Get or create hidden input for country code
            let countryCodeInput = phoneInput.closest('form')?.querySelector(countryCodeSelector);
            if (!countryCodeInput) {
                countryCodeInput = document.createElement('input');
                countryCodeInput.type = 'hidden';
                countryCodeInput.name = countryCodeSelector.replace('input[name="', '').replace('"]', '');
                phoneInput.parentNode.appendChild(countryCodeInput);
            }

            // Initialize intl-tel-input with IP-based auto country detection
            const iti = window.intlTelInput(phoneInput, {
                initialCountry: 'auto',
                geoIpLookup: function (success, failure) {
                    fetch('https://ipapi.co/json')
                        .then(res => res.json())
                        .then(data => success(data.country_code))
                        .catch(() => success('qa'));
                },
                preferredCountries: ['qa', 'in', 'ph', 'np', 'bd', 'lk', 'ae', 'sa'],
                separateDialCode: true,
                autoPlaceholder: 'polite',
                formatOnDisplay: true,
                nationalMode: false,
                utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js'
            });

            // Mark as initialized
            phoneInput.classList.add('iti-initialized');

            // Store reference
            this.phoneInputs.push({
                input: phoneInput,
                iti: iti,
                countryCodeInput: countryCodeInput
            });

            // Update country code on country change
            phoneInput.addEventListener('countrychange', () => {
                const selectedCountryData = iti.getSelectedCountryData();
                if (selectedCountryData && selectedCountryData.dialCode) {
                    countryCodeInput.value = '+' + selectedCountryData.dialCode;
                }
            });

            // When GeoIP completes, make sure we sync the country code hidden input
            iti.promise.then(() => {
                const selectedCountryData = iti.getSelectedCountryData();
                if (selectedCountryData && selectedCountryData.dialCode) {
                    countryCodeInput.value = '+' + selectedCountryData.dialCode;
                }
            });

            // Auto-detect country from phone number on input
            phoneInput.addEventListener('input', () => {
                this.autoDetectCountry(iti, phoneInput, countryCodeInput);
            });

            // Validate on blur
            phoneInput.addEventListener('blur', () => {
                this.validatePhoneNumber(iti, phoneInput);
            });
        });
    }

    /**
     * Auto-detect country from phone number
     */
    autoDetectCountry(iti, phoneInput, countryCodeInput) {
        let phoneNumber = phoneInput.value.trim();

        if (phoneNumber.startsWith('+')) {
            // Extract country code from number
            const matches = phoneNumber.match(/^\+(\d{1,4})/);
            if (matches) {
                const dialCode = matches[1];
                const country = this.getCountryFromDialCode(dialCode);
                if (country) {
                    iti.setCountry(country);
                    countryCodeInput.value = '+' + dialCode;
                }
            }
        }
    }

    /**
     * Validate phone number
     * @param {Object} iti - intl-tel-input instance
     * @param {HTMLElement} phoneInput - The phone input element
     * @param {boolean} showMessage - Whether to show the error message (default: true)
     */
    validatePhoneNumber(iti, phoneInput, showMessage = true) {
        if (!phoneInput.value.trim()) {
            return true;
        }

        const isValid = iti.isValidNumber();

        if (isValid) {
            phoneInput.classList.remove('is-invalid');
            phoneInput.classList.add('is-valid');

            // Remove any existing error message
            const errorMsg = phoneInput.parentNode.querySelector('.invalid-feedback');
            if (errorMsg) {
                errorMsg.style.display = 'none';
            }
        } else if (showMessage) {
            phoneInput.classList.remove('is-valid');
            phoneInput.classList.add('is-invalid');

            // Show error message
            let errorMsg = phoneInput.parentNode.querySelector('.invalid-feedback');
            if (!errorMsg) {
                errorMsg = document.createElement('div');
                errorMsg.className = 'invalid-feedback';
                phoneInput.parentNode.appendChild(errorMsg);
            }
            errorMsg.textContent = 'Please enter a valid phone number';
            errorMsg.style.display = 'block';
        } else {
            phoneInput.classList.remove('is-valid');
            phoneInput.classList.add('is-invalid');
        }

        return isValid;
    }

    /**
     * Get country code from dial code
     */
    getCountryFromDialCode(dialCode) {
        const countryData = window.intlTelInputGlobals.getCountryData();
        const country = countryData.find(c => c.dialCode === dialCode);
        return country ? country.iso2 : null;
    }

    /**
     * Get country from country code (e.g., +91 -> in)
     */
    getCountryFromCode(countryCode) {
        const dialCode = countryCode.replace('+', '');
        return this.getCountryFromDialCode(dialCode) || this.defaultCountry;
    }

    /**
     * Get full international number
     */
    getFullNumber(phoneInput) {
        const phoneData = this.phoneInputs.find(p => p.input === phoneInput);
        if (phoneData) {
            return phoneData.iti.getNumber();
        }
        return null;
    }

    /**
     * Validate all phone inputs in a form
     */
    validateForm(formElement) {
        let isValid = true;

        this.phoneInputs.forEach(phoneData => {
            if (phoneData.input.closest('form') === formElement) {
                if (!this.validatePhoneNumber(phoneData.iti, phoneData.input)) {
                    isValid = false;
                } else {
                    // Update input with full international number for backend
                    phoneData.input.value = phoneData.iti.getNumber();
                }
            }
        });

        return isValid;
    }

    /**
     * Clean phone number (remove country code for storage)
     */
    cleanPhoneNumber(phoneInput) {
        const phoneData = this.phoneInputs.find(p => p.input === phoneInput);
        if (phoneData) {
            const number = phoneData.iti.getNumber(window.intlTelInputUtils.numberFormat.NATIONAL);
            return number.replace(/\D/g, ''); // Remove all non-digits
        }
        return phoneInput.value;
    }

    /**
     * Destroy all instances
     */
    destroy() {
        this.phoneInputs.forEach(phoneData => {
            if (phoneData.iti) {
                phoneData.iti.destroy();
            }
        });
        this.phoneInputs = [];
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function () {
    // Create global instance
    window.customerPhoneHandler = new CustomerPhoneHandler();
    window.customerPhoneHandler.init();
});

// Re-initialize when modals are shown
document.addEventListener('shown.bs.modal', function (event) {
    if (window.customerPhoneHandler) {
        window.customerPhoneHandler.init();
    }
});
