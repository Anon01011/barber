@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Create Subscription</h2>
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Subscriptions
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('admin.subscriptions.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="salon_id" class="form-label">Salon *</label>
                                <select class="form-select @error('salon_id') is-invalid @enderror" id="salon_id"
                                    name="salon_id" required>
                                    <option value="">Select Salon</option>
                                    @foreach($salons as $salon)
                                        <option value="{{ $salon->id }}" {{ (old('salon_id', $selectedSalonId) == $salon->id) ? 'selected' : '' }}>
                                            {{ $salon->name }} ({{ $salon->slug }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('salon_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="plan_id" class="form-label">Plan *</label>
                                <select class="form-select @error('plan_id') is-invalid @enderror" id="plan_id"
                                    name="plan_id" required>
                                    <option value="">Select Plan</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}
                                            data-price="{{ $plan->price }}" data-duration="{{ $plan->duration_in_days }}">
                                            {{ $plan->name }} -
                                            {{ system_currency_symbol() }}{{ number_format($plan->price, 2) }}/{{ $plan->duration_in_days }}
                                            days
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status"
                                        name="status" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                        <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="trial_days" class="form-label">Trial Days</label>
                                    <input type="number" class="form-control @error('trial_days') is-invalid @enderror"
                                        id="trial_days" name="trial_days" value="{{ old('trial_days', 0) }}" min="0">
                                    <small class="text-muted">0 = No trial period</small>
                                    @error('trial_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="starts_at" class="form-label">Start Date</label>
                                <input type="date" class="form-control @error('starts_at') is-invalid @enderror"
                                    id="starts_at" name="starts_at" value="{{ old('starts_at', now()->format('Y-m-d')) }}">
                                <small class="text-muted">Leave blank for today</small>
                                @error('starts_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>End Date:</strong> Will be automatically calculated based on plan duration
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Subscription
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">Subscription Details</h6>
                    </div>
                    <div class="card-body">
                        <div id="subscription-preview">
                            <p class="text-muted">Select a plan to see details</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Show plan details when selected
            document.getElementById('plan_id').addEventListener('change', function (e) {
                const option = e.target.options[e.target.selectedIndex];
                const price = option.dataset.price;
                const duration = option.dataset.duration;
                const trialDays = document.getElementById('trial_days').value || 0;
                const startsAt = document.getElementById('starts_at').value || new Date().toISOString().split('T')[0];

                if (price && duration) {
                    const startDate = new Date(startsAt);
                    const endDate = new Date(startDate);
                    endDate.setDate(endDate.getDate() + parseInt(duration));

                    const trialEndDate = new Date(startDate);
                    trialEndDate.setDate(trialEndDate.getDate() + parseInt(trialDays));

                    document.getElementById('subscription-preview').innerHTML = `
                            <dl class="row mb-0">
                                <dt class="col-sm-5">Plan:</dt>
                                <dd class="col-sm-7">${option.text.split(' - ')[0]}</dd>

                                <dt class="col-sm-5">Price:</dt>
                                <dd class="col-sm-7">{{ system_currency_symbol() }}${price}</dd>

                                <dt class="col-sm-5">Duration:</dt>
                                <dd class="col-sm-7">${duration} days</dd>

                                <dt class="col-sm-5">Starts:</dt>
                                <dd class="col-sm-7">${startDate.toLocaleDateString()}</dd>

                                ${trialDays > 0 ? `
                                <dt class="col-sm-5">Trial Ends:</dt>
                                <dd class="col-sm-7">${trialEndDate.toLocaleDateString()}</dd>
                                ` : ''}

                                <dt class="col-sm-5">Ends:</dt>
                                <dd class="col-sm-7">${endDate.toLocaleDateString()}</dd>
                            </dl>
                        `;
                }
            });

            // Update preview when trial days or start date changes
            document.getElementById('trial_days').addEventListener('input', function () {
                document.getElementById('plan_id').dispatchEvent(new Event('change'));
            });

            document.getElementById('starts_at').addEventListener('change', function () {
                document.getElementById('plan_id').dispatchEvent(new Event('change'));
            });
        </script>
    @endpush
@endsection