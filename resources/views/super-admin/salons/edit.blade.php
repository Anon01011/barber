@extends('layouts.super-admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">Edit Salon</h1>
                <p class="text-muted mb-0">Update details for <span class="fw-bold">{{ $salon->name }}</span></p>
            </div>
            <a href="{{ route('admin.salons.show', $salon) }}" class="btn btn-light border shadow-sm text-muted">
                <i class="fas fa-arrow-left me-2"></i> Back to Details
            </a>
        </div>

        <form method="POST" action="{{ route('admin.salons.update', $salon) }}" enctype="multipart/form-data"
            id="salonEditForm">
            @csrf
            @method('PUT')

            <div class="row g-4 justify-content-center">
                <!-- Main Content -->
                <div class="col-lg-8">

                    <!-- 1. Basic Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-store me-2"></i> Salon Information
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <!-- Logo Upload -->
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-medium text-dark">Salon Logo</label>
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="position-relative">
                                            <img src="{{ $salon->logo ? Storage::url($salon->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($salon->name) . '&background=random' }}"
                                                class="rounded-circle border shadow-sm"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                            <div class="form-text mt-1">Recommended: 512x512px. Max 2MB.</div>
                                        </div>
                                    </div>
                                    @error('logo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-medium text-dark">Salon Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $salon->name) }}" required>
                                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="business_type" class="form-label fw-medium text-dark">Business Type</label>
                                    <select name="business_type" id="business_type" class="form-select" required>
                                        <option value="salon" {{ old('business_type', $salon->business_type) == 'salon' ? 'selected' : '' }}>Salon / Spa</option>
                                        <option value="barber" {{ old('business_type', $salon->business_type) == 'barber' ? 'selected' : '' }}>Barber / Barbershop</option>
                                        <option value="both" {{ old('business_type', $salon->business_type) == 'both' ? 'selected' : '' }}>Both (Salon & Barber)</option>
                                    </select>
                                    @error('business_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-medium text-dark">Business Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $salon->email) }}" required>
                                    @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-medium text-dark">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        value="{{ old('phone', $salon->phone) }}">
                                    @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="website" class="form-label fw-medium text-dark">Website</label>
                                    <input type="url" class="form-control" id="website" name="website"
                                        value="{{ old('website', $salon->website) }}" placeholder="https://">
                                    @error('website') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label fw-medium text-dark">Address</label>
                                    <textarea class="form-control" id="address" name="address"
                                        rows="2">{{ old('address', $salon->address) }}</textarea>
                                    @error('address') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="timezone" class="form-label fw-medium text-dark">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone" required>
                                        @foreach(timezone_identifiers_list() as $timezone)
                                            <option value="{{ $timezone }}" {{ old('timezone', $salon->timezone) == $timezone ? 'selected' : '' }}>
                                                {{ $timezone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('timezone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="currency" class="form-label fw-medium text-dark">Currency</label>
                                    <select class="form-select" id="currency" name="currency" required>
                                        <option value="USD" {{ old('currency', $salon->currency) == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="EUR" {{ old('currency', $salon->currency) == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                        <option value="GBP" {{ old('currency', $salon->currency) == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                        <option value="INR" {{ old('currency', $salon->currency) == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                                        <option value="AED" {{ old('currency', $salon->currency) == 'AED' ? 'selected' : '' }}>AED (د.إ)</option>
                                        <option value="SAR" {{ old('currency', $salon->currency) == 'SAR' ? 'selected' : '' }}>SAR (﷼)</option>
                                        <option value="CAD" {{ old('currency', $salon->currency) == 'CAD' ? 'selected' : '' }}>CAD ($)</option>
                                        <option value="AUD" {{ old('currency', $salon->currency) == 'AUD' ? 'selected' : '' }}>AUD ($)</option>
                                    </select>
                                    @error('currency') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Owner Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-tie me-2"></i> Owner Information
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-warning border-0 bg-warning-subtle text-warning small mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Warning:</strong> Changing the email address will update the owner's login
                                credentials.
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="owner_name" class="form-label fw-medium text-dark">Owner Name</label>
                                    <input type="text" class="form-control" id="owner_name" name="owner_name"
                                        value="{{ old('owner_name', $salon->owner->name ?? '') }}" required>
                                    @error('owner_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="owner_email" class="form-label fw-medium text-dark">Owner Email</label>
                                    <input type="email" class="form-control" id="owner_email" name="owner_email"
                                        value="{{ old('owner_email', $salon->owner->email ?? '') }}" required>
                                    @error('owner_email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="owner_phone" class="form-label fw-medium text-dark">Owner Phone</label>
                                    <input type="text" class="form-control" id="owner_phone" name="owner_phone"
                                        value="{{ old('owner_phone', $salon->owner->phone ?? '') }}">
                                    @error('owner_phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Mail Settings (Collapsed by default? No, open is fine) -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div
                            class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-envelope me-2"></i> SMTP Configuration
                            </h6>
                            <span class="badge bg-light text-muted border">Advanced</span>
                        </div>
                        <div class="card-body p-4">
                            <p class="small text-muted mb-3">Configure custom email settings for this salon to send emails
                                from their own domain.</p>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="mail_driver" class="form-label fw-medium text-dark">Mail Driver</label>
                                    <select class="form-select" id="mail_driver" name="mail_driver">
                                        <option value="smtp" {{ old('mail_driver', $salon->mail_driver) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="mailgun" {{ old('mail_driver', $salon->mail_driver) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                        <option value="ses" {{ old('mail_driver', $salon->mail_driver) == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="mail_host" class="form-label fw-medium text-dark">Mail Host</label>
                                    <input type="text" class="form-control" id="mail_host" name="mail_host"
                                        value="{{ old('mail_host', $salon->mail_host) }}" placeholder="smtp.provider.com">
                                </div>
                                <div class="col-md-2">
                                    <label for="mail_port" class="form-label fw-medium text-dark">Port</label>
                                    <input type="number" class="form-control" id="mail_port" name="mail_port"
                                        value="{{ old('mail_port', $salon->mail_port) }}" placeholder="587">
                                </div>

                                <div class="col-md-6">
                                    <label for="mail_username" class="form-label fw-medium text-dark">Username</label>
                                    <input type="text" class="form-control" id="mail_username" name="mail_username"
                                        value="{{ old('mail_username', $salon->mail_username) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="mail_password" class="form-label fw-medium text-dark">Password</label>
                                    <input type="password" class="form-control" id="mail_password" name="mail_password"
                                        placeholder="••••••••">
                                    <small class="text-muted">Leave blank to keep unchanged</small>
                                </div>

                                <div class="col-md-4">
                                    <label for="mail_encryption" class="form-label fw-medium text-dark">Encryption</label>
                                    <select class="form-select" id="mail_encryption" name="mail_encryption">
                                        <option value="" {{ old('mail_encryption', $salon->mail_encryption) == '' ? 'selected' : '' }}>None</option>
                                        <option value="tls" {{ old('mail_encryption', $salon->mail_encryption) == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ old('mail_encryption', $salon->mail_encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="mail_from_address" class="form-label fw-medium text-dark">From Email</label>
                                    <input type="email" class="form-control" id="mail_from_address" name="mail_from_address"
                                        value="{{ old('mail_from_address', $salon->mail_from_address) }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="mail_from_name" class="form-label fw-medium text-dark">From Name</label>
                                    <input type="text" class="form-control" id="mail_from_name" name="mail_from_name"
                                        value="{{ old('mail_from_name', $salon->mail_from_name) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="col-lg-4">
                    <!-- Status & Plan -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-credit-card me-2"></i> Subscription
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label for="plan_id" class="form-label fw-medium text-dark">Current Plan</label>
                                <select class="form-select @error('plan_id') is-invalid @enderror" id="plan_id"
                                    name="plan_id">
                                    <option value="">-- No Plan --</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ old('plan_id', $salon->activeSubscription->plan_id ?? '') == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }}
                                            ({{ system_currency_symbol() }}{{ number_format($plan->price, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="trial_ends_at" class="form-label fw-medium text-dark">Trial Ends On</label>
                                <input type="date" class="form-control" id="trial_ends_at" name="trial_ends_at"
                                    value="{{ old('trial_ends_at', $salon->trial_ends_at ? $salon->trial_ends_at->format('Y-m-d') : '') }}">
                            </div>

                            <hr class="my-4">

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $salon->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark" for="is_active">Active Status</label>
                            </div>
                            <small class="text-muted d-block">Disable to suspend access for this salon immediately.</small>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary fw-bold shadow-sm">
                                    <i class="fas fa-save me-2"></i> Update Salon
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-danger mb-3">Danger Zone</h6>
                            <p class="small text-muted mb-3">Irreversible actions regarding this workspace.</p>
                            <button type="button" class="btn btn-outline-danger w-100" onclick="confirmDeactivate()">
                                <i class="fas fa-trash-alt me-2"></i> Delete Salon
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form id="deactivateForm" method="POST" action="{{ route('admin.salons.destroy', $salon) }}" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    </div>

    @push('scripts')
        <script>
            function confirmDeactivate() {
                Swal.fire({
                    title: 'Delete Salon?',
                    text: "You are about to permanently delete {{ $salon->name }}. This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Delete it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deactivateForm').submit();
                    }
                });
            }

            // Form validation
            document.getElementById('salonEditForm').addEventListener('submit', function (e) {
                if (window.customerPhoneHandler) {
                    if (!window.customerPhoneHandler.validateForm(this)) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Phone Number',
                            text: 'Please check the phone numbers and try again.'
                        });
                    }
                }
            });
        </script>
    @endpush
@endsection