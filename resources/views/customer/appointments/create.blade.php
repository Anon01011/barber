@extends('layouts.app')

@php
    $salonSlug = $salonSlug ?? request()->route('salon_slug') ?? app('current_salon')?->slug ?? 'default';
@endphp

@section('title', 'Book Appointment')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer-dashboard.css') }}">
<style>
    /* ── Booking Page Theme ─────────────────────────── */
    :root {
        --accent: var(--theme-accent, #c5a880);
        --accent-hover: var(--theme-accent-hover, #a8865e);
        --dark-bg: #0b0f17;
    }

    .booking-wizard {
        background: linear-gradient(160deg, #f8fafc 0%, #eef2f7 100%);
        min-height: 100vh;
        padding: 2rem 0 6rem;
    }

    /* Step indicator */
    .wizard-steps { display: flex; align-items: center; gap: 0; margin-bottom: 2rem; }
    .wizard-step  { flex: 1; text-align: center; position: relative; }
    .wizard-step::after {
        content: '';
        position: absolute;
        top: 20px; left: 50%; width: 100%; height: 2px;
        background: #d1d5db; z-index: 0;
    }
    .wizard-step:last-child::after { display: none; }
    .step-bubble {
        width: 40px; height: 40px;
        border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        background: #e2e8f0; color: #64748b;
        font-weight: 700; font-size: 0.9rem;
        position: relative; z-index: 1;
        border: 2px solid #d1d5db;
        transition: all .25s ease;
    }
    .wizard-step.active  .step-bubble { background: var(--accent); color: #fff; border-color: var(--accent); }
    .wizard-step.done    .step-bubble { background: #10b981; color: #fff; border-color: #10b981; }
    .wizard-step.active::after, .wizard-step.done::after { background: var(--accent); }
    .step-label { display: block; margin-top: 6px; font-size: 0.75rem; font-weight: 600; color: #64748b; }
    .wizard-step.active .step-label, .wizard-step.done .step-label { color: #0f172a; }

    /* Service card checkboxes */
    .service-card-wrap input[type=checkbox] { position: absolute; opacity: 0; width: 0; height: 0; }
    .service-card-label {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 14px 16px; border-radius: 12px;
        border: 2px solid #e2e8f0;
        background: #fff;
        cursor: pointer;
        transition: all .2s ease;
    }
    .service-card-label:hover { border-color: var(--accent); background: rgba(197,168,128,.06); }
    .service-card-wrap input:checked + .service-card-label {
        border-color: var(--accent); background: rgba(197,168,128,.12);
    }
    .service-card-wrap input:checked + .service-card-label .svc-check-icon {
        background: var(--accent); color: #fff; border-color: var(--accent);
    }
    .svc-check-icon {
        width: 24px; height: 24px; border-radius: 6px;
        border: 2px solid #cbd5e1;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; margin-top: 2px; font-size: 0.75rem;
        transition: all .2s;
    }
    .svc-name  { font-weight: 600; font-size: 0.95rem; color: #0f172a; }
    .svc-meta  { font-size: 0.8rem; color: #64748b; margin-top: 2px; }
    .svc-price { font-weight: 700; font-size: 0.95rem; color: #0f172a; white-space: nowrap; }

    /* Staff card radio */
    .staff-card-wrap input[type=radio] { position: absolute; opacity: 0; width: 0; height: 0; }
    .staff-card-label {
        display: flex; flex-direction: column; align-items: center; gap: 6px;
        padding: 14px 10px; border-radius: 12px;
        border: 2px solid #e2e8f0; background: #fff;
        cursor: pointer; text-align: center;
        transition: all .2s ease;
        min-height: 90px;
    }
    .staff-card-label:hover { border-color: var(--accent); background: rgba(197,168,128,.06); }
    .staff-card-wrap input:checked + .staff-card-label { border-color: var(--accent); background: rgba(197,168,128,.12); }
    .staff-avatar {
        width: 44px; height: 44px; border-radius: 50%;
        background: linear-gradient(135deg,#c5a880,#9b7a50);
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 1rem;
        flex-shrink: 0;
    }
    .staff-name { font-size: 0.82rem; font-weight: 600; color: #0f172a; line-height: 1.2; }

    /* Scrollable Services Area: Only services scroll in background */
    .services-scroll-area {
        max-height: 52vh;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 4px 8px 16px 2px;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }
    .services-scroll-area::-webkit-scrollbar {
        width: 6px;
    }
    .services-scroll-area::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .services-scroll-area::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .services-scroll-area::-webkit-scrollbar-thumb:hover {
        background: var(--accent);
    }

    /* Summary banner & Sticky Bottom Floating Pill */
    #selectionSummary {
        position: sticky !important;
        bottom: 16px !important;
        z-index: 1040 !important;
        margin-top: -30px !important;
        width: 100% !important;
        pointer-events: none;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        justify-content: center;
    }
    #selectionSummary.d-none {
        display: none !important;
    }
    .summary-bar {
        pointer-events: auto;
        width: 100%;
        max-width: 820px;
        background: linear-gradient(135deg, #0b0f17 0%, #1e293b 100%);
        border-radius: 50px;
        padding: 12px 24px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        box-shadow: 0 16px 40px -4px rgba(11, 15, 23, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(14px);
    }
    .summary-chip {
        background: rgba(255,255,255,.1); border-radius: 50px;
        padding: 6px 14px; color: #fff; font-size: 0.82rem; font-weight: 500;
    }
    .summary-chip span { color: var(--accent); font-weight: 700; }

    /* Sticky Bottom Actions Pill Container */
    .sticky-action-pill-container {
        position: sticky;
        bottom: 20px;
        z-index: 1025;
        margin-top: 2rem !important;
    }
    .sticky-action-pill {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 50px;
        padding: 10px 18px;
        box-shadow: 0 12px 30px -4px rgba(11, 15, 23, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.04);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }

    @media (max-width: 768px) {
        .services-scroll-area {
            max-height: 48vh;
        }
        #selectionSummary {
            bottom: 12px !important;
            margin-top: -35px !important;
        }
        .summary-bar {
            border-radius: 24px;
            padding: 10px 14px;
            gap: 6px;
        }
        .summary-chip {
            padding: 4px 10px;
            font-size: 0.75rem;
        }
        #nextToStaffBtn {
            font-size: 0.82rem;
            padding: 6px 14px;
        }
        .sticky-action-pill-container {
            position: fixed;
            left: 12px;
            right: 12px;
            bottom: 14px;
            margin: 0 !important;
        }
        .sticky-action-pill {
            border-radius: 20px;
            padding: 10px 14px;
        }
        .sticky-action-pill .btn {
            width: 100%;
        }
    }

    /* Time slot pills */
    .slot-pill input[type=radio] { position: absolute; opacity: 0; width: 0; height: 0; }
    .slot-pill label {
        display: inline-block; padding: 6px 14px; border-radius: 50px;
        border: 1.5px solid #d1d5db; cursor: pointer; font-size: 0.82rem;
        font-weight: 600; color: #374151; background: #fff;
        transition: all .15s ease;
    }
    .slot-pill label:hover { border-color: var(--accent); background: rgba(197,168,128,.08); }
    .slot-pill input:checked + label { background: var(--accent); color: #fff; border-color: var(--accent); }

    /* Section header */
    .section-title { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; margin-bottom: 12px; }

    /* Tabs for categories */
    .cat-tab-btn {
        border: none; background: #e2e8f0; border-radius: 50px;
        padding: 5px 14px; font-size: 0.8rem; font-weight: 600; color: #475569;
        cursor: pointer; transition: all .15s ease; white-space: nowrap;
    }
    .cat-tab-btn.active { background: var(--accent); color: #fff; }
    .cat-tab-btn:hover:not(.active) { background: #cbd5e1; }
</style>
@endpush

@section('content')
<div class="booking-wizard">
    <div class="container py-2" style="max-width: 860px;">

        {{-- Back link --}}
        <div class="mb-3">
            <a href="{{ route('customer.appointments.index', ['salon_slug' => $salonSlug]) }}"
               class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> My Appointments
            </a>
        </div>

        {{-- Page Header --}}
        <div class="text-center mb-4">
            <div class="mb-2" style="font-size: 2.5rem;">✂️</div>
            <h2 class="fw-bold text-dark mb-1">Book Your Appointment</h2>
            <p class="text-muted small">Choose services, staff & your preferred time slot</p>
            @if(isset($salonTimezone))
            <span class="badge bg-light text-muted border small">
                <i class="fas fa-clock me-1"></i>Times shown in {{ $salonTimezone }}
            </span>
            @endif
        </div>

        {{-- Wizard step indicator --}}
        <div class="wizard-steps mb-4 px-2">
            <div class="wizard-step active" id="wizStep1">
                <div class="step-bubble"><i class="fas fa-scissors"></i></div>
                <span class="step-label">Services</span>
            </div>
            <div class="wizard-step" id="wizStep2">
                <div class="step-bubble"><i class="fas fa-user-tie"></i></div>
                <span class="step-label">Staff</span>
            </div>
            <div class="wizard-step" id="wizStep3">
                <div class="step-bubble"><i class="fas fa-calendar-alt"></i></div>
                <span class="step-label">Date & Time</span>
            </div>
            <div class="wizard-step" id="wizStep4">
                <div class="step-bubble"><i class="fas fa-check"></i></div>
                <span class="step-label">Confirm</span>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="alert alert-danger rounded-3 mb-4">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form id="bookingForm" method="POST" action="{{ route('customer.appointments.store', ['salon_slug' => $salonSlug]) }}" novalidate>
            @csrf

            {{-- ══ STEP 1: SERVICE SELECTION ══ --}}
            <div id="step1Panel" class="card border-0 shadow-sm mb-3" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-scissors me-2" style="color: var(--accent);"></i>Select Services</h5>
                        <div class="text-muted small mt-1">You can select multiple services</div>
                    </div>
                    <span class="badge rounded-pill" id="selectedCountBadge"
                          style="background: rgba(197,168,128,.15); color: #92400e; font-size: 0.8rem; padding: 6px 12px;">
                        0 selected
                    </span>
                </div>

                <div class="card-body p-4 position-relative">
                    {{-- Category filter tabs --}}
                    @if($categories->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 mb-4" id="catTabs">
                        <button type="button" class="cat-tab-btn active" data-cat="all">All Services</button>
                        @foreach($categories as $cat)
                            <button type="button" class="cat-tab-btn" data-cat="{{ $cat->id }}">{{ $cat->name }}</button>
                        @endforeach
                    </div>
                    @endif

                    {{-- Services Grid (Scrollable: only services scroll in background) --}}
                    @if($services->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-scissors fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No services available at this time.</p>
                        </div>
                    @else
                    <div class="services-scroll-area" id="servicesScrollArea">
                        <div class="row g-3" id="servicesGrid">
                            @foreach($services as $service)
                            <div class="col-md-6 service-card-wrap" data-cat="{{ $service->category_id ?? 'uncategorised' }}">
                                <input type="checkbox"
                                       name="service_ids[]"
                                       id="svc_{{ $service->id }}"
                                       value="{{ $service->id }}"
                                       data-duration="{{ $service->duration }}"
                                       data-price="{{ $service->price }}"
                                       data-name="{{ $service->name }}">
                                <label class="service-card-label w-100" for="svc_{{ $service->id }}">
                                    <div class="svc-check-icon"><i class="fas fa-check" style="font-size:.65rem;"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="svc-name">{{ $service->name }}</div>
                                        <div class="svc-meta">
                                            <i class="fas fa-clock me-1 opacity-50"></i>{{ $service->duration }} min
                                            @if($service->category)
                                                &nbsp;·&nbsp;<i class="fas fa-tag me-1 opacity-50"></i>{{ $service->category->name }}
                                            @endif
                                            @if($service->description)
                                                &nbsp;·&nbsp;{{ Str::limit($service->description, 40) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="svc-price">{{ currency_symbol() }}{{ number_format($service->price, 2) }}</div>
                                </label>
                            </div>
                            @endforeach
                        </div>

                        {{-- Spacer so bottom cards aren't covered by sticky pill --}}
                        <div id="selectionSpacer" class="d-none" style="height: 70px;"></div>
                    </div>
                    @endif

                    {{-- Selection summary (Sticky over bottom of card, services scroll behind it) --}}
                    <div id="selectionSummary" class="d-none">
                        <div class="summary-bar">
                            <span class="summary-chip"><i class="fas fa-scissors me-2 opacity-75"></i><span id="summaryServices">0</span> service(s)</span>
                            <span class="summary-chip"><i class="fas fa-clock me-2 opacity-75"></i><span id="summaryDuration">0</span> min total</span>
                            <span class="summary-chip"><i class="fas fa-tag me-2 opacity-75"></i>{{ currency_symbol() }}<span id="summaryPrice">0.00</span></span>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-sm btn-warning fw-bold rounded-pill px-4" id="nextToStaffBtn">
                                    Next: Choose Staff <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- No selection alert (shown before step 2) --}}
                    <div id="noServiceAlert" class="alert alert-warning small rounded-3 d-none mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>Please select at least one service to continue.
                    </div>
                </div>
            </div>

            {{-- ══ STEP 2: STAFF SELECTION ══ --}}
            <div id="step2Panel" class="card border-0 shadow-sm mb-3 d-none" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-user-tie me-2" style="color: var(--accent);"></i>Choose Your Professional</h5>
                        <div class="text-muted small mt-1">Select a specific staff member or let us assign the best available</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="backToServicesBtn">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                </div>
                <div class="card-body p-4 position-relative">
                    <div class="row g-3">
                        {{-- Any available --}}
                        <div class="col-6 col-md-3 staff-card-wrap">
                            <input type="radio" name="staff_id" id="staff_any" value="" checked>
                            <label class="staff-card-label w-100" for="staff_any">
                                <div class="staff-avatar" style="background: linear-gradient(135deg,#64748b,#334155);">
                                    <i class="fas fa-random fa-sm"></i>
                                </div>
                                <div class="staff-name">Any Available</div>
                                <small class="text-muted" style="font-size:0.7rem;">Best match</small>
                            </label>
                        </div>
                        @foreach($staffMembers as $staff)
                        <div class="col-6 col-md-3 staff-card-wrap">
                            <input type="radio" name="staff_id" id="staff_{{ $staff->id }}" value="{{ $staff->id }}">
                            <label class="staff-card-label w-100" for="staff_{{ $staff->id }}">
                                <div class="staff-avatar">{{ strtoupper(substr($staff->name, 0, 1)) }}</div>
                                <div class="staff-name">{{ $staff->name }}</div>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <div class="sticky-action-pill-container">
                        <div class="sticky-action-pill">
                            <button type="button" class="btn btn-warning fw-bold rounded-pill px-5 shadow-sm" id="nextToDateBtn">
                                Next: Pick Date & Time <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ STEP 3: DATE & TIME ══ --}}
            <div id="step3Panel" class="card border-0 shadow-sm mb-3 d-none" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-alt me-2" style="color: var(--accent);"></i>Pick Date & Time</h5>
                        <div class="text-muted small mt-1">All times shown in {{ $salonTimezone ?? 'local timezone' }}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="backToStaffBtn">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                </div>
                <div class="card-body p-4 position-relative">
                    <div class="row g-4">
                        {{-- Date picker --}}
                        <div class="col-md-5">
                            <label class="section-title"><i class="fas fa-calendar me-2"></i>Select Date</label>
                            <input type="date"
                                   name="date"
                                   id="dateInput"
                                   class="form-control form-control-lg"
                                   min="{{ $todayInSalonTz ?? date('Y-m-d') }}"
                                   max="{{ $maxDateInSalonTz ?? date('Y-m-d', strtotime('+30 days')) }}"
                                   required>
                            <div class="text-muted small mt-2">
                                <i class="fas fa-info-circle me-1 opacity-50"></i>Book up to {{ $advanceBookingDays ?? 30 }} days ahead
                            </div>
                        </div>

                        {{-- Time slots --}}
                        <div class="col-md-7">
                            <label class="section-title"><i class="fas fa-clock me-2"></i>Available Time Slots</label>
                            <div id="slotContainer">
                                <div class="text-center py-4 text-muted" id="slotPlaceholder">
                                    <i class="fas fa-calendar-day fa-2x mb-3 opacity-25"></i>
                                    <p class="small mb-0">Choose a date to see available time slots</p>
                                </div>
                                <div id="slotLoading" class="text-center py-4 d-none">
                                    <div class="spinner-border spinner-border-sm text-warning me-2"></div>
                                    <span class="text-muted small">Loading available times...</span>
                                </div>
                                <div id="slotGrid" class="d-flex flex-wrap gap-2 d-none"></div>
                                <div id="slotEmpty" class="text-center py-4 text-muted d-none">
                                    <i class="fas fa-calendar-times fa-2x mb-3 opacity-25"></i>
                                    <p class="small mb-0">No slots available on this date. Try another date.</p>
                                </div>
                                {{-- Hidden input that holds the selected time --}}
                                <input type="hidden" name="time" id="timeInput" required>
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="mt-4">
                        <label class="section-title"><i class="fas fa-comment-dots me-2"></i>Special Requests <span class="text-muted fw-normal text-lowercase">(optional)</span></label>
                        <textarea name="notes" id="notesInput" class="form-control" rows="3"
                                  placeholder="Any special requests, allergies, preferences..."></textarea>
                    </div>

                    <div class="sticky-action-pill-container">
                        <div class="sticky-action-pill">
                            <button type="button" class="btn btn-warning fw-bold rounded-pill px-5 shadow-sm" id="nextToConfirmBtn">
                                Review Booking <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ STEP 4: CONFIRMATION ══ --}}
            <div id="step4Panel" class="card border-0 shadow-sm mb-3 d-none" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-check-circle me-2" style="color: var(--accent);"></i>Confirm Booking</h5>
                        <div class="text-muted small mt-1">Review your selections before confirming</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="backToDateBtn">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                </div>
                <div class="card-body p-4 position-relative">
                    {{-- Summary card --}}
                    <div class="rounded-3 p-4 mb-4" style="background: #f8fafc; border: 1.5px solid #e2e8f0;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small fw-semibold text-uppercase mb-1">Services</div>
                                <div id="confirmServices" class="fw-semibold text-dark">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small fw-semibold text-uppercase mb-1">Staff</div>
                                <div id="confirmStaff" class="fw-semibold text-dark">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small fw-semibold text-uppercase mb-1">Date & Time</div>
                                <div id="confirmDateTime" class="fw-semibold text-dark">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small fw-semibold text-uppercase mb-1">Total Duration</div>
                                <div id="confirmDuration" class="fw-semibold text-dark">—</div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small fw-semibold text-uppercase mb-1">Estimated Total</div>
                                <div id="confirmPrice" class="h4 fw-bold mb-0" style="color: var(--accent);">—</div>
                            </div>
                            <div class="col-12" id="confirmNotesRow">
                                <div class="text-muted small fw-semibold text-uppercase mb-1">Notes</div>
                                <div id="confirmNotes" class="text-dark small fst-italic">—</div>
                            </div>
                        </div>
                    </div>

                    <div class="sticky-action-pill-container">
                        <div class="sticky-action-pill flex-column w-100 p-2">
                            <button type="submit" class="btn btn-lg fw-bold rounded-pill py-3 w-100 shadow" id="submitBtn"
                                    style="background: var(--accent); color: #fff; font-size: 1.05rem;">
                                <i class="fas fa-calendar-check me-2"></i>Confirm & Book Appointment
                            </button>
                        </div>
                        <div class="text-center text-muted small mt-2">
                            <i class="fas fa-info-circle me-1 opacity-50"></i>
                            Your booking is pending confirmation by our team unless auto-confirmation is enabled.
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── State ────────────────────────────────────── */
    const state = {
        services: [],   // [{id, name, duration, price}]
        staffId:  '',
        staffName:'Any Available Professional',
        date:     '',
        time:     '',
    };

    const currencySymbol = @json(currency_symbol());
    const salonSlug      = @json($salonSlug);
    const slotsUrl       = "{{ route('customer.api.appointments.available-slots') }}";

    /* ── DOM refs ──────────────────────────────────── */
    const $ = id => document.getElementById(id);

    const steps   = [1,2,3,4];
    const panels  = {1:'step1Panel',2:'step2Panel',3:'step3Panel',4:'step4Panel'};
    const wizards = {1:'wizStep1',2:'wizStep2',3:'wizStep3',4:'wizStep4'};
    let currentStep = 1;

    function showStep(n) {
        currentStep = n;
        steps.forEach(s => {
            $(panels[s]).classList.toggle('d-none', s !== n);
            const w = $(wizards[s]);
            w.classList.remove('active','done');
            if (s < n)  w.classList.add('done');
            if (s === n) w.classList.add('active');
        });
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    /* ── Category filter ──────────────────────────── */
    document.querySelectorAll('.cat-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.cat-tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const cat = btn.dataset.cat;
            document.querySelectorAll('.service-card-wrap').forEach(card => {
                if (cat === 'all' || card.dataset.cat == cat) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    /* ── Service checkbox change ──────────────────── */
    function updateSelectionSummary() {
        const checked = [...document.querySelectorAll('input[name="service_ids[]"]:checked')];
        state.services = checked.map(el => ({
            id:       el.value,
            name:     el.dataset.name,
            duration: parseInt(el.dataset.duration, 10) || 0,
            price:    parseFloat(el.dataset.price) || 0,
        }));

        const totalDuration = state.services.reduce((s, x) => s + x.duration, 0);
        const totalPrice    = state.services.reduce((s, x) => s + x.price, 0);
        const count         = state.services.length;

        $('selectedCountBadge').textContent = count + ' selected';
        $('summaryServices').textContent   = count;
        $('summaryDuration').textContent   = totalDuration;
        $('summaryPrice').textContent      = totalPrice.toFixed(2);

        const summary = $('selectionSummary');
        const spacer  = $('selectionSpacer');
        const noAlert = $('noServiceAlert');
        if (count > 0) {
            summary.classList.remove('d-none');
            if (spacer) spacer.classList.remove('d-none');
            noAlert.classList.add('d-none');
        } else {
            summary.classList.add('d-none');
            if (spacer) spacer.classList.add('d-none');
        }
    }

    document.querySelectorAll('input[name="service_ids[]"]').forEach(cb => {
        cb.addEventListener('change', updateSelectionSummary);
    });

    /* ── Step navigation buttons ──────────────────── */
    $('nextToStaffBtn').addEventListener('click', () => {
        if (state.services.length === 0) {
            $('noServiceAlert').classList.remove('d-none');
            return;
        }
        showStep(2);
    });

    $('backToServicesBtn').addEventListener('click', () => showStep(1));

    $('nextToDateBtn').addEventListener('click', () => {
        // Capture staff selection
        const staffRadio = document.querySelector('input[name="staff_id"]:checked');
        state.staffId   = staffRadio ? staffRadio.value : '';
        const staffLabel = staffRadio ? staffRadio.closest('.staff-card-wrap').querySelector('.staff-name').textContent : 'Any Available';
        state.staffName  = state.staffId ? staffLabel : 'Any Available Professional';
        showStep(3);
    });

    $('backToStaffBtn').addEventListener('click', () => showStep(2));

    $('nextToConfirmBtn').addEventListener('click', () => {
        if (!state.date) {
            Swal.fire({ icon:'warning', title:'No date', text:'Please choose an appointment date.' });
            return;
        }
        if (!state.time) {
            Swal.fire({ icon:'warning', title:'No time slot', text:'Please select a time slot.' });
            return;
        }
        populateConfirmation();
        showStep(4);
    });

    $('backToDateBtn').addEventListener('click', () => showStep(3));

    /* ── Date change → load slots ─────────────────── */
    $('dateInput').addEventListener('change', function () {
        state.date = this.value;
        state.time = '';
        $('timeInput').value = '';
        if (state.date) loadSlots(state.date);
    });

    function loadSlots(date) {
        $('slotPlaceholder').classList.add('d-none');
        $('slotLoading').classList.remove('d-none');
        $('slotGrid').classList.add('d-none');
        $('slotEmpty').classList.add('d-none');
        $('slotGrid').innerHTML = '';

        const totalDuration = state.services.reduce((s, x) => s + x.duration, 0);
        const staffId       = state.staffId || '';
        const primarySvc    = state.services[0]?.id || '';

        // Build query params: pass primary service_id + total duration + optional staff_id
        const params = new URLSearchParams({ date, service_id: primarySvc, duration: totalDuration, staff_id: staffId });

        fetch(slotsUrl + '?' + params.toString())
            .then(r => r.json())
            .then(data => {
                $('slotLoading').classList.add('d-none');
                const available = Array.isArray(data) ? data.filter(s => s.available) : [];
                if (available.length === 0) {
                    $('slotEmpty').classList.remove('d-none');
                    return;
                }
                available.forEach(slot => {
                    const wrap = document.createElement('div');
                    wrap.className = 'slot-pill';
                    const rid = 'slot_' + slot.time.replace(':', '');
                    wrap.innerHTML = `<input type="radio" name="_slot_ui" id="${rid}" value="${slot.time}">
                                      <label for="${rid}">${slot.time}</label>`;
                    wrap.querySelector('input').addEventListener('change', function () {
                        state.time = this.value;
                        $('timeInput').value = this.value;
                    });
                    $('slotGrid').appendChild(wrap);
                });
                $('slotGrid').classList.remove('d-none');
            })
            .catch(() => {
                $('slotLoading').classList.add('d-none');
                $('slotEmpty').classList.remove('d-none');
            });
    }

    /* ── Populate confirmation panel ─────────────── */
    function populateConfirmation() {
        const totalDuration = state.services.reduce((s, x) => s + x.duration, 0);
        const totalPrice    = state.services.reduce((s, x) => s + x.price, 0);

        $('confirmServices').textContent = state.services.map(s => s.name).join(', ') || '—';
        $('confirmStaff').textContent    = state.staffName;
        $('confirmDateTime').textContent = formatDate(state.date) + ' at ' + state.time;
        $('confirmDuration').textContent = totalDuration + ' min';
        $('confirmPrice').textContent    = currencySymbol + totalPrice.toFixed(2);
        const notes = $('notesInput').value.trim();
        $('confirmNotes').textContent = notes || '(none)';
    }

    function formatDate(d) {
        if (!d) return '—';
        const dt = new Date(d + 'T00:00:00');
        return dt.toLocaleDateString(undefined, { weekday:'short', year:'numeric', month:'short', day:'numeric' });
    }

    /* ── Form submit ─────────────────────────────── */
    $('bookingForm').addEventListener('submit', function (e) {
        e.preventDefault();

        if (state.services.length === 0) {
            Swal.fire({ icon:'error', title:'No Services', text:'Please select at least one service.' });
            showStep(1); return;
        }
        if (!state.time) {
            Swal.fire({ icon:'error', title:'No Time', text:'Please select a time slot.' });
            showStep(3); return;
        }

        const submitBtn = $('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Booking...';

        const formData = new FormData(this);
        // staff_id already in form via radio; service_ids[] via checkboxes
        // ensure date and time are set
        formData.set('date', state.date);
        formData.set('time', state.time);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success || data.message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Booking Submitted!',
                    text: data.message || 'Your appointment has been submitted successfully.',
                    confirmButtonText: 'View My Appointments',
                    confirmButtonColor: '#c5a880'
                }).then(() => {
                    window.location.href = data.redirect || `/${salonSlug}/customer/appointments`;
                });
            } else {
                throw new Error(data.message || 'Booking failed');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-calendar-check me-2"></i>Confirm & Book Appointment';
            Swal.fire({ icon:'error', title:'Booking Failed', text: err.message || 'Please try again.' });
        });
    });

})();
</script>
@endpush
