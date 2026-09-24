@extends('layouts.app')

@section('title', 'Booking Calendar')

@section('content')
    <!-- Global Notification Container -->


    <div class="container-fluid px-3 py-2 d-flex flex-column" style="height: calc(100vh - 70px); overflow: hidden;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h5 mb-0 text-dark fw-semibold">Booking Calendar</h2>
            </div>
            <div class="d-flex gap-2 mt-3 mt-md-0 align-items-center">
                <a href="{{ route('admin.bookings.settings') }}" class="btn btn-outline-secondary"
                    title="Calendar Settings">
                    <i class="fas fa-cog"></i>
                </a>

                <div class="btn-group" role="group" aria-label="View Toggle">
                    <input type="radio" class="btn-check" name="viewType" id="calendarView" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="calendarView">
                        <i class="fas fa-calendar-alt me-1"></i> Calendar
                    </label>

                    <input type="radio" class="btn-check" name="viewType" id="listView" autocomplete="off">
                    <label class="btn btn-outline-primary" for="listView">
                        <i class="fas fa-list me-1"></i> List
                    </label>
                </div>

                <div class="dropdown ms-2" style="width: 250px;">
                    <button
                        class="btn btn-outline-secondary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center"
                        type="button" id="staffFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span id="staffFilterLabel">All Staff</span>
                    </button>
                    <ul class="dropdown-menu w-100 p-2" aria-labelledby="staffFilterDropdown"
                        style="max-height: 300px; overflow-y: auto;">
                        <li>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="all" id="staffFilterAll" checked>
                                <label class="form-check-label w-100 " for="staffFilterAll">
                                    Select All
                                </label>
                            </div>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        @foreach($staffMembers as $staff)
                            <li>
                                <div class="form-check">
                                    <input class="form-check-input staff-filter-checkbox" type="checkbox"
                                        value="{{ $staff->id }}" id="staffFilter{{ $staff->id }}" checked>
                                    <label class="form-check-label w-100" for="staffFilter{{ $staff->id }}">
                                        {{ $staff->name }}
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- List View Specific Filters -->
                <div id="listViewFilters" class="d-none d-flex gap-2 align-items-center">
                    <select id="statusFilter" class="form-select" style="width: 150px;">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="staff_completed">Staff Completed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="no_show">No Show</option>
                    </select>

                    <select id="sortFilter" class="form-select" style="width: 150px;">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="alpha_asc">Customer A-Z</option>
                        <option value="alpha_desc">Customer Z-A</option>
                    </select>
                </div>

                <div class="ms-2 d-none" id="datePickerContainer">
                    <input type="date" id="calendarDatePicker" class="form-control"
                        value="{{ \Carbon\Carbon::now(salon_timezone())->format('Y-m-d') }}">
                </div>
            </div>
        </div>

        <!-- Calendar View -->
        <div id="calendarViewContainer" class="flex-grow-1" style="overflow: hidden;">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-0 h-100">
                    <div id="calendar" class="h-100"></div>
                </div>
            </div>
        </div>

        <!-- List View -->
        <div id="listViewContainer" class="flex-grow-1" style="display: none; overflow: hidden;">
            <div class="card border-0 shadow-sm h-100 d-flex flex-column">
                <div class="card-body p-0 flex-grow-1 d-flex flex-column" style="overflow: hidden;">
                    <div class="table-responsive flex-grow-1" style="overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Appt. Nr</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Staff</th>
                                    <th>Date & Time</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bookingsList">
                                <!-- Bookings will be loaded here via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-3 border-top">
                        <div class="text-muted small" id="listViewCount">Showing 0 bookings</div>
                        <nav aria-label="List view pagination">
                            <ul class="pagination pagination-sm mb-0" id="listViewPagination">
                                <!-- Pagination will be added here via JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.past-time-modal')
    @include('admin.bookings.partials.payment_modal')

    @include('admin.bookings.partials.booking_details_modal')
    @include('admin.customers.partials.bill_activity_modal')
    <x-modals.view-customer />
    <x-modals.edit-customer />

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-body text-center p-4">
                    <div class="success-icon mb-3">
                        <i class="fas fa-check-circle" style="font-size: 3.5rem; color: #10b981;"></i>
                    </div>
                    <h5 class="fw-bold mb-2" id="successModalTitle">Success!</h5>
                    <p class="text-muted mb-3" id="successModalMessage">Operation completed successfully.</p>
                    <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">
                        <i class="fas fa-check me-2"></i>OK
                    </button>
                </div>
            </div>
        </div>
    </div>



    <style>
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        #bookingModal.show .modal-content {
            animation: slideIn 0.3s ease-out;
        }

        /* Calendar Date Navigation */
        #calendar-date-nav {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .date-nav-btn {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            font-weight: 500;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            line-height: 1.1;
            min-width: 55px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .date-nav-btn:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #334155;
        }

        .date-nav-btn.active {
            background-color: #eff6ff;
            border-color: #3b82f6;
            color: #2563eb;
            font-weight: 600;
            box-shadow: 0 0 0 1px #3b82f6;
        }

        .date-nav-btn .day-name {
            font-size: 0.65rem;
            text-transform: uppercase;
            margin-bottom: 1px;
            opacity: 0.7;
        }

        .date-nav-btn .day-date {
            font-size: 1rem;
        }

        /* FullCalendar Button Styling */
        .fc .fc-button {
            padding: 0.35rem 0.7rem !important;
            font-weight: 500 !important;
            border-radius: 0.5rem !important;
            text-transform: capitalize !important;
            font-size: 0.85rem !important;
            box-shadow: none !important;
        }

        .fc .fc-button-primary {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #64748b !important;
        }

        .fc .fc-button-primary:hover {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
            color: #ffffff !important;
        }

        .fc .fc-today-button {
            background-color: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
            color: #475569 !important;
            opacity: 1 !important;
            margin-right: 0.25rem !important;
        }

        .fc .fc-today-button:hover {
            background-color: #e2e8f0 !important;
        }

        /* Adjust FullCalendar Toolbar */
        .fc-header-toolbar {
            display: flex !important;
            flex-wrap: nowrap !important;
            gap: 0.5rem !important;
            margin-bottom: 0.5rem !important;
            align-items: center !important;
            background: #f8fafc;
            padding: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
        }

        .fc-toolbar-chunk {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }

        .fc-toolbar-chunk:first-child {
            flex: 1;
            justify-content: flex-start;
            overflow-x: auto;
            scrollbar-width: none;
            /* Hide scrollbar for Chrome/Safari */
        }

        .fc-toolbar-chunk:first-child::-webkit-scrollbar {
            display: none;
        }

        .fc-toolbar-title {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            white-space: nowrap;
        }

        /* Ensure everything stays in one row */
        .fc-button-group {
            flex-shrink: 0;
            display: flex !important;
            gap: 0.5rem !important;
            background: transparent !important;
            padding: 0 !important;
            border: none !important;
        }

        .fc .fc-button-group>.fc-button {
            flex: none !important;
            margin: 0 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
            background-color: #ffffff !important;
            color: #64748b !important;
            font-weight: 600 !important;
            transition: all 0.2s ease !important;
        }

        .fc .fc-button-group>.fc-button:hover {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        .fc .fc-button-group>.fc-button.fc-button-active {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2) !important;
        }

        /* Prev/Next Buttons */
        .fc .fc-prev-button,
        .fc .fc-next-button {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #64748b !important;
            width: 36px !important;
            height: 36px !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 0.5rem !important;
        }

        .fc .fc-prev-button:hover,
        .fc .fc-next-button:hover {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }

        .fc-toolbar-title {
            font-size: 1.15rem !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            letter-spacing: -0.01em !important;
            margin-left: 0.5rem !important;
        }

        #calendarDatePicker {
            width: 140px;
            height: auto;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            padding: 0.25rem 0.5rem;
            font-size: 0.85rem;
            color: #475569;
            background-color: #ffffff;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .fc-header-toolbar {
                flex-wrap: wrap !important;
            }

            .fc-toolbar-chunk:last-child {
                margin-top: 0.5rem;
                width: 100%;
                justify-content: center;
            }
        }

        /* Fix for Calendar Event Alignment/Overflow */
        .fc-timegrid-event {
            overflow: hidden !important;
            box-sizing: border-box !important;
            border-radius: 4px !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            /* Ensure it respects the column width */
            max-width: 100% !important;
        }

        .fc-event-main-frame {
            width: 100%;
            overflow: hidden;
            padding: 2px 4px;
        }

        .fc-event-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 0.8rem;
            line-height: 1.2;
        }

        .fc-event-time {
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 2px;
        }

        /* Hide License Message */
        .fc-license-message {
            display: none !important;
        }

        /* COMPACT CALENDAR STYLES */

        /* Reduce slot height */
        /* Reduce slot height */
        .fc-timegrid-slot {
            height: 7px !important;
            line-height: 7px !important;
            min-height: 0 !important;
            border-bottom: 1px solid #9e9e9eff !important;
        }

        /* Subtler grid lines */
        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #f1f5f9 !important;
        }

        /* Time axis styling */
        .fc-timegrid-axis-cushion,
        .fc-timegrid-slot-label-cushion {
            font-size: 0.75rem !important;
            color: #64748b !important;
            padding: 0 4px !important;
        }

        /* Header styling */
        .fc-col-header-cell-cushion {
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            color: #334155 !important;
            padding-top: 12px !important;
            padding-bottom: 12px !important;
        }

        /* Today highlight */
        .fc-day-today {
            background-color: #f8fafc !important;
        }

        /* Event styling refinement */
        .fc-timegrid-event {
            border: none !important;
            border-radius: 4px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
            margin: 1px !important;
        }

        .fc-event-main {
            padding: 2px 4px !important;
        }

        /* Compact toolbar further */
        .fc-header-toolbar {
            margin-bottom: 0.5rem !important;
            padding: 0.25rem 0.5rem !important;
            min-height: auto !important;
        }

        .fc-toolbar-title {
            font-size: 1rem !important;
        }

        .fc-button {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.8rem !important;
        }
    </style>

    <!-- Create Appointment Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <!-- Premium Gradient Header -->
                <div class="modal-header border-0 text-white py-3 px-3"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="modal-title d-flex align-items-center gap-2 mb-0 fw-semibold" id="bookingModalLabel">
                        <div class="icon-circle-create">
                            <i class="fas fa-plus" id="bookingModalIcon"></i>
                        </div>
                        <span id="bookingModalTitleText" style="font-size: 1.1rem;">Create New Appointment</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-3" style="background: #f8f9fa;">
                    <div class="row g-3">
                        <!-- Left Column: Appointment Form -->
                        <div class="col-lg-7">
                            <form id="bookingForm" class="needs-validation" novalidate>
                                @csrf
                                <input type="hidden" id="booking_id" name="booking_id">

                                <div class="row g-3">
                                    <!-- Customer Selection -->
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="customer"
                                                class="form-label-modern small fw-bold text-uppercase text-muted ls-1">
                                                <i class="fas fa-user text-primary me-2"></i>
                                                Customer
                                            </label>
                                            <div class="input-group">
                                                <select class="form-control-modern form-select" id="customer"
                                                    name="customer_id" required style="width: auto; flex: 1;">
                                                    <option value="">Search and select customer</option>
                                                </select>
                                                <button type="button" class="btn btn-outline-primary"
                                                    id="btnQuickAddCustomer" title="Add New Customer">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <!-- <button type="button" class="btn btn-outline-secondary" id="btnBillActivity"
                                                                                                                                                                title="View Bill Activity" disabled>
                                                                                                                                                                <i class="fas fa-file-invoice"></i>
                                                                                                                                                            </button> -->
                                            </div>
                                            <div class="invalid-feedback">Please select a customer.</div>
                                        </div>
                                    </div>

                                    <!-- Date -->
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="date"
                                                class="form-label-modern small fw-bold text-uppercase text-muted ls-1">
                                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                                Date
                                            </label>
                                            <input type="date" class="form-control-modern" id="date" name="date" required>
                                            <div class="invalid-feedback">Please select a date.</div>
                                        </div>
                                    </div>

                                    <!-- Service Selection -->
                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern small fw-bold text-uppercase text-muted ls-1">
                                                <i class="fas fa-cut text-primary me-2"></i>
                                                Service(s)
                                            </label>
                                            <div class="dropdown-search-wrapper position-relative">
                                                <input type="text" class="form-control-modern" id="serviceSearchInput"
                                                    placeholder="Select Service" autocomplete="off">
                                                <div class="dropdown-search-results shadow-sm border rounded"
                                                    id="serviceSearchResults"
                                                    style="display: none; position: absolute; width: 100%; max-height: 300px; overflow-y: auto; z-index: 1000; background: white;">
                                                    <!-- Services will be populated here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Package Selection -->
                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern small fw-bold text-uppercase text-muted ls-1">
                                                <i class="fas fa-box-open text-primary me-2"></i>
                                                Package(s)
                                            </label>
                                            <div class="dropdown-search-wrapper position-relative">
                                                <input type="text" class="form-control-modern" id="packageSearchInput"
                                                    placeholder="Select Package" autocomplete="off">
                                                <div class="dropdown-search-results shadow-sm border rounded"
                                                    id="packageSearchResults"
                                                    style="display: none; position: absolute; width: 100%; max-height: 300px; overflow-y: auto; z-index: 1000; background: white;">
                                                    <!-- Packages will be populated here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Available Package Services (Populated when customer is selected) -->
                                    <div class="col-md-12" id="availablePackagesContainer" style="display: none;">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern small fw-bold text-uppercase text-muted ls-1">
                                                <i class="fas fa-gift text-primary me-2"></i>
                                                Available Package Services
                                            </label>
                                            <div id="availablePackagesList" class="d-flex flex-wrap gap-2">
                                                <!-- Populated via JS -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Selected Services Details -->
                                    <div class="col-md-12" id="selectedServicesContainer" style="display: none;">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-2">
                                                <h6 class="mb-2 fw-bold text-dark small text-uppercase ls-1">
                                                    <i class="fas fa-calendar-check text-primary me-2"></i>
                                                    Schedule Services
                                                </h6>
                                                <div id="selectedServicesList">
                                                    <!-- Dynamic service rows will be added here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Selected Packages Details -->
                                    <div class="col-md-12" id="selectedPackagesContainer" style="display: none;">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-2">
                                                <h6 class="mb-2 fw-bold text-dark small text-uppercase ls-1">
                                                    <i class="fas fa-box-open text-primary me-2"></i>
                                                    Schedule Packages
                                                </h6>
                                                <div id="selectedPackagesList">
                                                    <!-- Dynamic package rows will be added here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Time (Hidden when services selected) -->
                                    <!-- Time (Hidden when services selected) -->
                                    <!-- Global time input removed in favor of dynamic service/package time selection -->

                                    <!-- Status (Visible only in Edit Mode) -->
                                    <div class="col-md-12" id="statusFieldContainer" style="display: none;">
                                        <div class="form-group-modern">
                                            <label for="status"
                                                class="form-label-modern small fw-bold text-uppercase text-muted ls-1">
                                                <i class="fas fa-info-circle text-primary me-2"></i>
                                                Status
                                            </label>
                                            <div class="status-selector">
                                                <select class="form-control-modern" id="status" name="status">
                                                    <option value="pending">Pending</option>
                                                    <option value="confirmed">Confirmed</option>
                                                    <option value="arrived">Arrived</option>
                                                    <option value="started">Employee Started</option>
                                                    <option value="staff_completed">Employee Completed</option>
                                                    <option value="no_show">No Show</option>
                                                    {{-- Completed status is only set via POS sale --}}
                                                    <option value="cancelled">Cancelled</option>
                                                    <option value="frozen">Frozen</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="col-12">
                                        <div class="form-group-modern">
                                            <label for="notes"
                                                class="form-label-modern small fw-bold text-uppercase text-muted ls-1">
                                                <i class="fas fa-sticky-note text-primary me-2"></i>
                                                Notes
                                            </label>
                                            <textarea class="form-control-modern" id="notes" name="notes" rows="3"
                                                placeholder="Add any additional notes..." style="resize: none;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Right Column: Customer Details Panel -->
                        <div class="col-lg-5">
                            <div id="customerDetailsPanel" class="customer-details-panel" style="display: none;">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-white border-0 pb-0">
                                        <h6 class="mb-0 fw-semibold text-dark">
                                            <i class="fas fa-user-circle text-primary me-2"></i>
                                            Customer Details
                                        </h6>
                                    </div>
                                    <div class="card-body p-3" style="max-height: 600px; overflow-y: auto;">
                                        <!-- Loading State -->
                                        <div id="customerDetailsLoading" class="text-center py-4" style="display: none;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="text-muted mt-2 mb-0">Loading customer details...</p>
                                        </div>

                                        <!-- Customer Details Content -->
                                        <div id="customerDetailsContent">
                                            <!-- Basic Information -->
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="avatar-circle me-2">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold" id="customerName">-</h6>
                                                        <small class="text-muted" id="customerStatus">-</small>
                                                        <div class="mt-1">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-primary py-0 px-2"
                                                                id="btnBillActivityDetails"
                                                                style="display: none; font-size: 0.75rem;">
                                                                <i class="fas fa-file-invoice me-1 p-2"></i>View Bill
                                                                Activity
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger py-1 px-2 ms-1"
                                                                id="btnUnpaidAmountDetails"
                                                                style="display: none; font-size: 0.75rem;">
                                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                                <span class="fw-semibold">Unpaid: </span>
                                                                <span
                                                                    id="unpaidAmountTextDetails">{{ currency_symbol() }}0.00</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Medical Notes - Moved to Top -->
                                            <div id="customerMedicalNotesContainer" class="mb-3" style="display: none;">
                                                <div class="alert alert-warning mb-0">
                                                    <h6 class="alert-heading mb-2">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Medical / Allergy Notes
                                                    </h6>
                                                    <p class="mb-0 small" id="customerMedicalNotes"></p>
                                                    <button type="button" class="btn btn-sm btn-warning mt-2"
                                                        id="editMedicalNotesBtn">
                                                        <i class="fas fa-edit me-1"></i>Edit Notes
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Add Medical Notes Button (Visible when no notes exist) -->
                                            <div id="addMedicalNotesContainer" class="mb-3" style="display: none;">
                                                <button type="button" class="btn btn-sm btn-outline-warning w-100"
                                                    id="addMedicalNotesBtn">
                                                    <i class="fas fa-plus-circle me-1"></i>Add Medical / Allergy Notes
                                                </button>
                                            </div>

                                            <!-- Contact Information -->
                                            <div class="info-section mb-3">
                                                <h6 class="section-title">Contact Information</h6>
                                                <div class="info-item">
                                                    <i class="fas fa-envelope text-muted me-2"></i>
                                                    <span id="customerEmail">-</span>
                                                </div>
                                                <div class="info-item">
                                                    <i class="fas fa-phone text-muted me-2"></i>
                                                    <span id="customerMobile">-</span>
                                                </div>
                                                <div class="info-item">
                                                    <i class="fas fa-birthday-cake text-muted me-2"></i>
                                                    <span>Birth Date: <span id="customerBirthDate">-</span></span>
                                                </div>
                                                <div class="info-item">
                                                    <i class="fas fa-heart text-muted me-2"></i>
                                                    <span>Anniversary: <span id="customerAnniversary">-</span></span>
                                                </div>
                                            </div>

                                            <!-- Favorite Staff (Based on Service Count > 5) -->
                                            <div class="info-section mb-3" id="customerFavoriteStaffSection"
                                                style="display: none;">
                                                <h6 class="section-title">Preferred Staff</h6>
                                                <div class="info-item">
                                                    <i class="fas fa-user-tie text-muted me-2"></i>
                                                    <span id="customerFavoriteStaff">-</span>
                                                </div>
                                            </div>

                                            <!-- Financial Stats -->
                                            <div class="info-section mb-3">
                                                <h6 class="section-title">Financial Overview</h6>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="stat-card">
                                                            <small class="text-muted d-block">Unpaid Balance</small>
                                                            <strong id="customerUnpaidBalance">{{ currency_symbol() }}
                                                                0.00</strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="stat-card">
                                                            <small class="text-muted d-block">Total Revenue</small>
                                                            <strong id="customerTotalRevenue">{{ currency_symbol() }}
                                                                0.00</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Visit History -->
                                            <div class="info-section mb-3">
                                                <h6 class="section-title">Visit History</h6>
                                                <div class="info-item">
                                                    <i class="fas fa-calendar-check text-muted me-2"></i>
                                                    <span>Last Visit: <span id="customerLastVisit">Never</span></span>
                                                </div>
                                                <div class="info-item">
                                                    <i class="fas fa-hashtag text-muted me-2"></i>
                                                    <span>Total Visits: <span id="customerTotalVisits">0</span></span>
                                                </div>
                                            </div>

                                            <!-- Upcoming Appointments -->
                                            <div class="info-section mb-3">
                                                <h6 class="section-title">Upcoming Appointments</h6>
                                                <div id="upcomingAppointmentsList" class="small">
                                                    <div class="text-muted">No upcoming appointments</div>
                                                </div>
                                            </div>

                                            <!-- Last Visits -->
                                            <div class="info-section mb-3">
                                                <h6 class="section-title">Recent Visits</h6>
                                                <div id="lastVisitsList" class="small">
                                                    <div class="text-muted">No visit history</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Placeholder when no customer selected -->
                            <div id="customerDetailsPlaceholder" class="customer-details-placeholder">
                                <div class="card border-0 shadow-sm h-100 d-flex align-items-center justify-content-center"
                                    style="min-height: 400px;">
                                    <div class="text-center p-4">
                                        <i class="fas fa-user-circle fa-4x text-muted mb-3"></i>
                                        <h6 class="text-muted">Select a customer to view details</h6>
                                        <p class="text-muted small mb-0">Customer information will appear here</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modern Footer -->
                <div class="modal-footer border-0 bg-white py-2 px-3">
                    <button type="button" class="btn btn-modern btn-light-modern" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        <span>Cancel</span>
                    </button>
                    <button type="button" class="btn btn-modern btn-primary-modern" id="createBooking">
                        <i class="fas fa-save me-1"></i>
                        <span id="createBookingText">Create Appointment</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Create Appointment Modal - Compact Modern Design */
        #bookingModal .icon-circle-create {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        #bookingModal .icon-circle-create i {
            font-size: 1.05rem;
        }

        /* Modern Form Groups */
        #bookingModal .form-group-modern {
            position: relative;
        }

        #bookingModal .form-label-modern {
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 0.85rem;
            color: #2d3748;
            margin-bottom: 0.4rem;
            letter-spacing: 0.01em;
        }

        #bookingModal .form-label-modern i {
            font-size: 0.85rem;
            opacity: 0.8;
        }

        /* Modern Form Controls */
        #bookingModal .form-control-modern {
            width: 100%;
            padding: 0.65rem 0.85rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: #2d3748;
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }

        #bookingModal .form-control-modern:focus {
            outline: none;
            border-color: #667eea;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1), 0 2px 6px rgba(102, 126, 234, 0.12);
            transform: translateY(-1px);
        }

        #bookingModal .form-control-modern:hover:not(:focus) {
            border-color: #cbd5e0;
        }

        #bookingModal .form-control-modern::placeholder {
            color: #a0aec0;
            font-weight: 400;
        }

        /* Select Dropdown Styling */
        #bookingModal select.form-control-modern {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23667eea' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.85rem center;
            padding-right: 2.25rem;
        }

        /* Textarea Specific */
        #bookingModal textarea.form-control-modern {
            font-family: inherit;
            line-height: 1.5;
        }

        /* Invalid Feedback */
        #bookingModal .invalid-feedback {
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 0.35rem;
            padding-left: 0.25rem;
        }

        /* Fix Select2 in Input Group */
        #bookingModal .input-group .select2-container {
            flex: 1 1 auto;
            width: 1% !important;
            /* This trick forces it to share space */
        }

        #bookingModal .input-group .select2-selection {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            height: 100%;
            display: flex;
            align-items: center;
        }

        #bookingModal .input-group .select2-selection__rendered {
            line-height: normal !important;
        }

        #bookingModal .input-group .select2-selection__arrow {
            height: 100% !important;
        }

        /* Modern Buttons */
        #bookingModal .btn-modern {
            padding: 0.6rem 1.4rem;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            letter-spacing: 0.01em;
        }

        #bookingModal .btn-light-modern {
            background: #f7fafc;
            color: #4a5568;
            border: 2px solid #e2e8f0;
        }

        #bookingModal .btn-light-modern:hover {
            background: #edf2f7;
            border-color: #cbd5e0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        #bookingModal .btn-primary-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        #bookingModal .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        #bookingModal .btn-primary-modern:active {
            transform: translateY(0);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            #bookingModal .modal-dialog {
                margin: 0.5rem;
            }

            #bookingModal .modal-header {
                padding: 0.85rem 0.75rem;
            }

            #bookingModal .modal-body {
                padding: 0.85rem 0.75rem;
            }

            #bookingModal .icon-circle-create {
                width: 34px;
                height: 34px;
            }

            #bookingModal .icon-circle-create i {
                font-size: 0.95rem;
            }

            #bookingModal .modal-title span {
                font-size: 1rem;
            }

            #bookingModal .btn-modern {
                padding: 0.55rem 1.1rem;
                font-size: 0.85rem;
            }
        }

        /* Animation */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #bookingModal.show .modal-content {
            animation: slideIn 0.3s ease-out;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* Customer Details Panel Styles */
        #bookingModal .customer-details-panel {
            height: 100%;
        }

        #bookingModal .avatar-circle {
            width: 48px;
            height: 48px;
            min-width: 48px;
            min-height: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }

        #bookingModal .info-section {
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }

        #bookingModal .info-section:last-child {
            border-bottom: none;
        }

        #bookingModal .section-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #bookingModal .info-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            font-size: 0.9rem;
            color: #2d3748;
        }

        #bookingModal .info-item i {
            width: 20px;
            font-size: 0.875rem;
        }

        #bookingModal .stat-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 0.75rem;
            text-align: center;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        #bookingModal .stat-card:hover {
            background: #edf2f7;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        #bookingModal .stat-card strong {
            display: block;
            font-size: 1rem;
            color: #2d3748;
            margin-top: 0.25rem;
        }

        #bookingModal .customer-details-placeholder {
            height: 100%;
        }

        /* Scrollbar styling for customer details */
        #bookingModal .card-body::-webkit-scrollbar {
            width: 6px;
        }

        #bookingModal .card-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #bookingModal .card-body::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        #bookingModal .card-body::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        /* Responsive adjustments for customer details panel */
        @media (max-width: 991px) {

            #bookingModal .col-lg-7,
            #bookingModal .col-lg-5 {
                width: 100%;
            }

            #bookingModal .customer-details-panel .card-body {
                max-height: 400px;
            }
        }
    </style>

    <!-- Edit Medical Notes Modal (from Booking Modal) -->
    <div class="modal fade" id="editCustomerMedicalNotesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning bg-opacity-10">
                    <h5 class="modal-title">
                        <i class="fas fa-notes-medical me-2"></i>
                        Edit Medical / Allergy Notes
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateCustomerMedicalNotesForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_customer_id" name="customer_id">
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Important:</strong> Record any allergies, medical conditions, or special precautions
                            staff should be aware of.
                        </div>
                        <div class="mb-3">
                            <label for="edit_medical_notes" class="form-label">Medical / Allergy Notes</label>
                            <textarea class="form-control" id="edit_medical_notes" name="medical_notes" rows="5"
                                placeholder="e.g., Allergic to certain hair products, skin sensitivity, etc."></textarea>
                            <small class="text-muted">This will be displayed prominently to alert staff</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i> Save Medical Notes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin.bookings.partials.payment_modal')

    <!-- Quick Create Customer Modal -->
    <!-- Quick Create Customer Modal -->
    <x-modals.add-customer />
    <!-- Grouped Booking Status Modal -->
    <div class="modal fade" id="groupedBookingStatusModal" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Unfinished Appointments
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="icon-box bg-warning bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                            <i class="fas fa-calendar-times text-warning fs-1"></i>
                        </div>
                        <h4 class="fw-bold">Group Appointments Not Finished</h4>
                        <p class="text-muted">This customer has other appointments today that are not yet marked as
                            completed. Please finish all appointments in the group before raising a sale.</p>
                    </div>
                    <div id="unfinishedBookingsList" class="list-group list-group-flush border rounded">
                        <!-- Unfinished bookings will be listed here -->
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnCheckStatusAgain">
                        <i class="fas fa-sync-alt me-1"></i>Check Status Again
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' rel='stylesheet' />
    <style>
        /* Calendar Container */
        #calendar {
            height: 800px;
            background: white;
            border-radius: 0.5rem;
            padding: 1rem;
        }

        /* Calendar Header */
        .fc .fc-toolbar {
            padding: 1rem;
            margin-bottom: 1rem !important;
        }

        .fc .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 600;
            color: #2c3e50;
        }

        .fc .fc-button {
            padding: 0.5rem 1rem;
            font-weight: 500;
            text-transform: capitalize;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }

        .fc .fc-button-primary {
            background-color: #4a90e2;
            border-color: #4a90e2;
        }

        .fc .fc-button-primary:hover {
            background-color: #357abd;
            border-color: #357abd;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #357abd;
            border-color: #357abd;
        }

        /* Calendar Grid */
        .fc .fc-timegrid-slot {
            height: 3rem !important;
            border-bottom: 1px solid #edf2f7;
        }

        .fc .fc-timegrid-slot-label {
            font-size: 0.875rem;
            color: #718096;
            padding: 0.5rem;
        }

        .fc .fc-timegrid-axis {
            padding: 0.5rem;
            font-size:
                0.875rem;
            color: #718096;
        }

        .fc .fc-col-header-cell {
            padding: 0.75rem 0;
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .fc .fc-col-header-cell-cushion {
            padding: 0.5rem;
            color: #2d3748;
            font-weight: 600;
            text-decoration: none;
        }

        /* Event Styles */
        .fc-event {
            border: none !important;
            border-radius: 0.375rem !important;
            padding: 0.25rem 0.5rem !important;
            margin: 0.125rem 0.25rem !important;
            font-size: 0.875rem !important;
            box-shadow:
                0 2px 4px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.2s ease !important;
        }

        .fc-event:hover {
            transform:
                translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        }

        .fc-event-main {
            padding: 0.25rem !important;
        }

        .fc-event-time {
            font-weight: 500;
            margin-right: 0.5rem;
            font-size: 0.75rem;
            opacity: 0.9;
        }

        .fc-event-title {
            font-weight: 500;
        }

        /* Enhanced Status Badge Styles */
        .detail-section .badge {
            font-size:
                0.875rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 2rem;
            text-transform: capitalize;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            color: #2d3748 !important;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .detail-section .badge i {
            font-size: 0.875rem;
        }

        /*
                                                                                                                                                                                                                                                            Status-specific styles */
        .detail-section .badge.bg-success {
            background:
                {{ $settings['confirmed_color'] ?? '#f0fdf4' }}
                !important;
            border: 1px solid
                {{ $settings['confirmed_border'] ?? '#86efac' }}
                !important;
            color:
                {{ $settings['confirmed_text'] ?? '#166534' }}
                !important;
        }

        .detail-section .badge.bg-warning {
            background:
                {{ $settings['pending_color'] ?? '#f8fafc' }}
                !important;
            border: 1px solid
                {{ $settings['pending_border'] ?? '#e2e8f0' }}
                !important;
            color:
                {{ $settings['pending_text'] ?? '#000000' }}
                !important;
        }

        .detail-section .badge.bg-info {
            background:
                {{ $settings['completed_color'] ?? '#eff6ff' }}
                !important;
            border: 1px solid
                {{ $settings['completed_border'] ?? '#93c5fd' }}
                !important;
            color:
                {{ $settings['completed_text'] ?? '#1e40af' }}
                !important;
        }

        .detail-section .badge.bg-danger {
            background:
                {{ $settings['cancelled_color'] ?? '#fef2f2' }}
                !important;
            border: 1px solid
                {{ $settings['cancelled_border'] ?? '#fca5a5' }}
                !important;
            color:
                {{ $settings['cancelled_text'] ?? '#991b1b' }}
                !important;
        }

        .detail-section .badge.bg-pending {
            background:
                {{ $settings['pending_color'] ?? '#f8fafc' }}
                !important;
            border: 1px solid
                {{ $settings['pending_border'] ?? '#e2e8f0' }}
                !important;
            color:
                {{ $settings['pending_text'] ?? '#000000' }}
                !important;
        }

        /* Event Status Styles */
        .fc-event-pending {
            background:
                {{ $settings['pending_color'] ?? '#f8fafc' }}
                !important;
            color:
                {{ $settings['pending_text'] ?? '#000000' }}
                !important;
            border: 1px solid
                {{ $settings['pending_border'] ?? '#e2e8f0' }}
                !important;
        }

        .fc-event-confirmed {
            background:
                {{ $settings['confirmed_color'] ?? '#f0fdf4' }}
                !important;
            color:
                {{ $settings['confirmed_text'] ?? '#166534' }}
                !important;
            border: 1px solid
                {{ $settings['confirmed_border'] ?? '#86efac' }}
                !important;
        }

        .fc-event-completed {
            background:
                {{ $settings['completed_color'] ?? '#eff6ff' }}
                !important;
            color:
                {{ $settings['completed_text'] ?? '#1e40af' }}
                !important;
            border: 1px solid
                {{ $settings['completed_border'] ?? '#93c5fd' }}
                !important;
        }

        .fc-event-cancelled {
            background:
                {{ $settings['cancelled_color'] ?? '#fef2f2' }}
                !important;
            color:
                {{ $settings['cancelled_text'] ?? '#991b1b' }}
                !important;
            border: 1px solid
                {{ $settings['cancelled_border'] ?? '#fca5a5' }}
                !important;
        }

        .fc-event-unpaid {
            background:
                {{ $settings['booking_color_unpaid'] ?? '#dc3545' }}
                !important;
            color:
                {{ $settings['booking_text_unpaid'] ?? '#ffffff' }}
                !important;
            border: 1px solid
                {{ $settings['booking_border_unpaid'] ?? '#dc3545' }}
                !important;
        }

        /* Event Content */
        .fc-event-main-frame {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .fc-event-title {
            flex: 1;
            min-width:
                0;
        }

        .fc-event-title .fw-medium {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fc-event-title .small {
            opacity: 0.9;
        }

        .fc-event-title .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        /* Today Column Highlight */
        .fc .fc-day-today {
            background: rgba(74, 144, 226, 0.03) !important;
        }

        /* Non-business hours styling */
        .fc-non-business {
            background-color: rgba(241, 245, 249, 0.5) !important;
            /* Very light grey/blue */
        }

        /* Past time background events (from JS) */
        .fc-bg-event {
            opacity: 0.6 !important;
        }

        /* Time Grid */
        .fc-timegrid-event {
            min-height: 2rem !important;
        }

        .fc-timegrid-event .fc-event-main {
            padding: 0.25rem 0.5rem !important;
        }

        /* Event Tooltip */
        .tooltip {
            font-size: 0.875rem;
        }

        .tooltip-inner {
            max-width: 300px;
            padding:
                0.5rem;
            background-color: #2d3748;
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /*
                                                                                                                                                                                                                                                            Responsive Adjustments */
        @media (max-width: 768px) {
            #calendar {
                height: 600px;
            }

            .fc .fc-toolbar {
                flex-direction:
                    column;
                gap: 1rem;
            }

            .fc .fc-toolbar-title {
                font-size: 1.25rem !important;
            }

            .fc-event {
                font-size: 0.75rem !important;
            }

            .fc-event-time {
                display: none;
            }

            .detail-section .badge {
                padding: 0.625rem 1.25rem;
                font-size:
                    0.8125rem;
            }
        }

        /* Modal Styles */
        .modal-content {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .modal-header.bg-gradient-primary {
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            border-radius: 1.25rem 1.25rem 0 0;
            padding: 1.75rem;
        }

        .modal-header .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .modal-header .modal-title i {
            font-size:
                1.75rem;
            color: white;
        }

        .modal-header .modal-title span {
            color: white;
            font-weight: 600;
        }

        .modal-header .btn-close-white {
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .modal-header .btn-close-white:hover {
            opacity: 1;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
        }

        /* Form Styles */
        .form-label {
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 0.375rem;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        /*
                                                                                                                                                                                                                                                            Button Styles */
        .btn {
            padding: 0.5rem 1rem;
            font-weight: 500;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #4a90e2;
            border-color: #4a90e2;
        }

        .btn-primary:hover {
            background-color: #357abd;
            border-color: #357abd;
        }

        .btn-light {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #4a5568;
        }

        .btn-light:hover {
            background-color: #edf2f7;
            border-color: #cbd5e0;
        }

        /* Loading State */
        .loading {
            position:
                relative;
            pointer-events: none;
            opacity: 0.7;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left:
                50%;
            width: 1.5rem;
            height: 1.5rem;
            margin: -0.75rem 0 0 -0.75rem;
            border: 2px solid #e2e8f0;
            border-top-color:
                #4a90e2;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform:
                    rotate(360deg);
            }
        }

        /* Notification System Styles */
        .notification-container {
            position: fixed;
            top: 1rem;
            right:
                1rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .notification {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 300px;
            max-width: 400px;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease-in-out;
        }

        .notification.show {
            opacity: 1;
            transform:
                translateX(0);
        }

        .notification-content {
            display: flex;
            align-items: center;
            padding: 1rem;
            gap: 0.75rem;
        }

        .notification-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content:
                center;
        }

        .notification-message {
            flex-grow: 1;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .notification-close {
            flex-shrink: 0;
            background: none;
            border: none;
            padding: 0.25rem;
            cursor: pointer;
            color: #6c757d;
            transition: color 0.2s;
        }

        .notification-close:hover {
            color: #343a40;
        }

        .notification-progress {
            height: 3px;
            background: rgba(0, 0,
                    0, 0.1);
            border-radius: 0 0 0.5rem 0.5rem;
            overflow: hidden;
        }

        .notification-progress-bar {
            height: 100%;
            background: currentColor;
            transition: width linear;
        }

        /* Notification Types */
        .notification.success {
            border-left:
                4px solid #28a745;
        }

        .notification.success .notification-icon {
            color: #28a745;
        }

        .notification.error {
            border-left:
                4px solid #dc3545;
        }

        .notification.error .notification-icon {
            color: #dc3545;
        }

        .notification.warning {
            border-left:
                4px solid #ffc107;
        }

        .notification.warning .notification-icon {
            color: #ffc107;
        }

        .notification.info {
            border-left:
                4px solid #17a2b8;
        }

        .notification.info .notification-icon {
            color: #17a2b8;
        }

        /* Appointment Details Modal Styles
                                                                                                                                                                                                                                                            */
        .appointment-details {
            padding: 0.5rem;
        }

        .detail-section {
            background: white;
            border-radius: 1rem;
            padding:
                1.5rem;
            border: 1px solid #e2e8f0;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .detail-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .detail-header {
            color: #2d3748;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom:
                1.25rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }

        .detail-header h6 {
            font-size:
                1.1rem;
            font-weight: 600;
            margin: 0;
            color: #2d3748;
            line-height: 1.2;
        }

        .icon-wrapper {
            width: 46px;
            height: 46px;
            min-width: 46px;
            min-height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(74, 144, 226, 0.12) 0%, rgba(53, 122, 189, 0.12) 100%);
            transition: all 0.3s ease;
            aspect-ratio: 1;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .icon-wrapper:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, rgba(74, 144, 226, 0.15) 0%, rgba(53, 122, 189, 0.15) 100%);
        }

        .icon-wrapper i {
            font-size: 1.25rem;
            color: #4a90e2;
            line-height: 1;
        }

        .icon-wrapper-sm {
            width: 36px;
            height: 36px;
            min-width: 36px;
            min-height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f8fafc;
            transition: all 0.3s ease;
            aspect-ratio: 1;
            flex-shrink: 0;
        }

        .icon-wrapper-sm:hover {
            background: #edf2f7;
            transform: scale(1.05);
        }

        .icon-wrapper-sm i {
            font-size: 1rem;
            color:
                #718096;
            line-height: 1;
        }

        .detail-content {
            padding-top: 0.75rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .detail-section .text-muted {
            color: #718096 !important;
        }

        .detail-section .text-primary {
            color: #4a90e2 !important;
        }

        .detail-section .badge {
            font-size: 0.875rem;
            padding: 0.75rem 1.5rem;
            font-weight:
                500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 2rem;
            text-transform: capitalize;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            color: #2d3748 !important;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .detail-section .badge i {
            font-size: 0.875rem;
        }

        .detail-section .badge.bg-success {
            background:
                #f0fdf4;
            border-color: #86efac;
            color: #166534;
        }

        .detail-section .badge.bg-warning {
            background: #fffbeb;
            border-color: #fcd34d;
            color: #92400e;
        }

        .detail-section .badge.bg-info {
            background: #eff6ff;
            border-color:
                #93c5fd;
            color: #1e40af;
        }

        .detail-section .badge.bg-danger {
            background: #fef2f2;
            border-color: #fca5a5;
            color:
                #991b1b;
        }

        .detail-section .badge.bg-pending {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #000000;
            font-weight: 600;
        }

        .detail-section .badge.bg-staff_completed {
            background: #eff6ff;
            border-color: #93c5fd;
            color: #1e40af;
        }

        .detail-section .badge.bg-no_show {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #718096;
        }

        .detail-section .badge.bg-partially_completed {
            background: #fdf2f8;
            border-color: #fbcfe8;
            color: #9d174d;
        }

        .modal-footer .btn {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            border: none;
        }

        .modal-footer .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(74, 144, 226, 0.2);
        }

        .modal-footer .btn-light {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color:
                #4a5568;
        }

        .modal-footer .btn-light:hover {
            background: #edf2f7;
            border-color: #cbd5e0;
        }

        /* Enhanced Schedule
                                                                                                                                                                                                                                                            Section */
        .schedule-item {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 0.75rem;
            background:
                #f8fafc;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .icon-wrapper {
                width: 42px;
                height: 42px;
                min-width: 42px;
                min-height: 42px;
            }

            .icon-wrapper i {
                font-size:
                    1.125rem;
            }

            .icon-wrapper-sm {
                width: 32px;
                height: 32px;
                min-width: 32px;
                min-height: 32px;
            }

            .icon-wrapper-sm i {
                font-size: 0.875rem;
            }

            .detail-section .badge {
                padding: 0.625rem 1.25rem;
                font-size: 0.8125rem;
            }
        }
    </style>
    <style>
        /* Calendar Grid Customization */
        .fc-theme-standard .fc-timegrid-slot {
            height: 12px !important;
            /* Reduced height for 5-minute slots */
            border-bottom: 1px solid #e2e8f0 !important;
            /* Solid line for major slots */
        }

        .fc-theme-standard .fc-timegrid-slot-minor {
            border-bottom: 1px dotted #e2e8f0 !important;
            /* Dotted line for minor (5-min) slots */
        }

        .fc-theme-standard .fc-timegrid-slot:empty {
            height: 12px !important;
        }

        /* Major slots (labeled ones) - typically every 15 mins based on slotLabelInterval */
        .fc-theme-standard .fc-timegrid-slot-label {
            border-bottom: 1px solid #cbd5e0 !important;
            /* Solid line for major slots */
        }

        /* Ensure the minor slots (inside the 15min block) are dotted */
        .fc-theme-standard .fc-timegrid-slot-minor {
            border-bottom-style: dotted !important;
            border-bottom-color: #e2e8f0 !important;
        }

        /* Adjust the axis labels to align */
        .fc-timegrid-axis-cushion,
        .fc-timegrid-slot-label-cushion {
            padding: 0 4px !important;
            font-size: 0.85rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.servicesData = @json($services ?? []);
        window.calendarSettings = @json($settings ?? []);
        window.packagesData = @json($packages ?? []);
        window.staffMembers = @json($staffMembers ?? []);
        window.userRoles = @json(auth()->user()->getRoleNames());
        window.currencySymbol = '{{ currency_symbol() }}';
    </script>
    @vite(['resources/js/calendar-init.js'])
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const customerSelect = document.getElementById('customer');
            const btnBillActivity = document.getElementById('btnBillActivity');
            const btnBillActivityDetails = document.getElementById('btnBillActivityDetails');

            function updateBillActivityButtons(customerId) {
                const btnUnpaidAmountDetails = document.getElementById('btnUnpaidAmountDetails');
                const unpaidAmountTextDetails = document.getElementById('unpaidAmountTextDetails');

                if (customerId) {
                    if (btnBillActivity) {
                        btnBillActivity.disabled = false;
                        btnBillActivity.onclick = () => openBillActivityModal(customerId);
                    }
                    if (btnBillActivityDetails) {
                        btnBillActivityDetails.style.display = 'inline-block';
                        btnBillActivityDetails.onclick = () => openBillActivityModal(customerId);
                    }

                    // Fetch and display unpaid amount
                    if (btnUnpaidAmountDetails && unpaidAmountTextDetails) {
                        const salonSlug = '{{ auth()->user()->salon->slug }}';
                        fetch(`/${salonSlug}/admin/customers/${customerId}/bill-activity`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.bills) {
                                    // Calculate unpaid amount
                                    let unpaidAmount = 0;
                                    data.bills.forEach(bill => {
                                        const status = bill.payment_status.toLowerCase();
                                        if (status === 'unpaid' || status === 'pending' || status === 'partial') {
                                            unpaidAmount += parseFloat(bill.outstanding_amount || 0);
                                        }
                                    });

                                    if (unpaidAmount > 0) {
                                        btnUnpaidAmountDetails.style.display = 'inline-block';
                                        unpaidAmountTextDetails.textContent = '{{ currency_symbol() }}' + unpaidAmount.toFixed(2);
                                        btnUnpaidAmountDetails.onclick = () => {
                                            if (typeof window.openBillActivityModal === 'function') {
                                                window.openBillActivityModal(customerId, 'unpaid');
                                            }
                                        };
                                    } else {
                                        btnUnpaidAmountDetails.style.display = 'none';
                                    }
                                }
                            })
                            .catch(error => {

                                btnUnpaidAmountDetails.style.display = 'none';
                            });
                    }
                } else {
                    if (btnBillActivity) {
                        btnBillActivity.disabled = true;
                    }
                    if (btnBillActivityDetails) {
                        btnBillActivityDetails.style.display = 'none';
                    }
                    if (btnUnpaidAmountDetails) {
                        btnUnpaidAmountDetails.style.display = 'none';
                    }
                }
            }

            // Listen for changes on the customer select
            if (customerSelect) {
                // Standard change event
                customerSelect.addEventListener('change', function () {
                    updateBillActivityButtons(this.value);
                });

                // Select2 event if used (jQuery)
                if (typeof jQuery !== 'undefined') {
                    jQuery('#customer').on('select2:select', function (e) {
                        updateBillActivityButtons(this.value);
                    });

                    // Also check on load if value exists
                    if (customerSelect.value) {
                        updateBillActivityButtons(customerSelect.value);
                    }
                }
            }
        });

        window.viewStaffDetails = function(staffId) {
            if (!staffId) return;

            // Show modal and loading state
            const modalElement = document.getElementById('staffDetailsModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();

            document.getElementById('staffDetailsLoading').style.display = 'block';
            document.getElementById('staffDetailsContent').style.display = 'none';

            @if(auth()->user()->salon)
                const salonSlug = '{{ auth()->user()->salon->slug }}';
            @else
                const pathParts = window.location.pathname.split('/');
                const salonSlug = pathParts[1] || 'admin';
            @endif

            fetch(`/${salonSlug}/admin/api/staff/${staffId}/details`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('staffDetailsLoading').style.display = 'none';
                    document.getElementById('staffDetailsContent').style.display = 'block';

                    document.getElementById('staffDetailAvatar').src = data.staff.avatar_url;
                    document.getElementById('staffDetailName').textContent = data.staff.name;
                    document.getElementById('staffDetailPosition').textContent = data.staff.position;
                    document.getElementById('staffDetailEmail').textContent = data.staff.email || 'N/A';
                    document.getElementById('staffDetailPhone').textContent = data.staff.phone || 'N/A';

                    const servicesBody = document.getElementById('staffDetailServicesBody');
                    servicesBody.innerHTML = '';

                    if (data.staff.services.length === 0) {
                        servicesBody.innerHTML = `
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3 small">
                                    No services assigned to this staff member.
                                </td>
                            </tr>
                        `;
                    } else {
                        data.staff.services.forEach(service => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="py-2 text-dark fw-medium small">${service.name}</td>
                                <td class="py-2 text-muted small">${service.category_name}</td>
                                <td class="text-end py-2 fw-semibold text-dark small">{{ currency_symbol() }}${parseFloat(service.price).toFixed(2)}</td>
                            `;
                            servicesBody.appendChild(row);
                        });
                    }
                } else {
                    window.notifications.show('Failed to load staff details', 'error');
                    modal.hide();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.notifications.show('Failed to load staff details', 'error');
                modal.hide();
            });
        };
    </script>

    {{-- Include Bill Activity Modal --}}
    @include('admin.customers.partials.bill_activity_modal')

    <!-- Staff Details Modal -->
    <div class="modal fade" id="staffDetailsModal" tabindex="-1" aria-hidden="true" style="z-index: 1095;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header border-0 text-white py-3 px-3" style="background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title d-flex align-items-center gap-2 mb-0 fw-semibold">
                        <i class="fas fa-user-tie me-2"></i> Staff Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Loading State -->
                    <div id="staffDetailsLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Loading staff details...</p>
                    </div>

                    <!-- Details Content -->
                    <div id="staffDetailsContent" style="display: none;">
                        <div class="d-flex align-items-center mb-4">
                            <img id="staffDetailAvatar" src="" class="rounded-circle border shadow-sm me-3" style="width: 65px; height: 65px; object-fit: cover;">
                            <div>
                                <h5 id="staffDetailName" class="mb-1 fw-bold text-dark">-</h5>
                                <span id="staffDetailPosition" class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill fw-medium small">-</span>
                            </div>
                        </div>

                        <div class="card border-0 bg-light p-3 mb-4" style="border-radius: 8px;">
                            <div class="row g-3">
                                <div class="col-6">
                                    <span class="text-muted small d-block">Email</span>
                                    <span id="staffDetailEmail" class="fw-semibold text-dark small">-</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small d-block">Phone</span>
                                    <span id="staffDetailPhone" class="fw-semibold text-dark small">-</span>
                                </div>
                            </div>
                        </div>

                        <h6 class="text-secondary fw-bold small text-uppercase mb-3" style="letter-spacing: 0.5px;">Assigned Services</h6>
                        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-borderless align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small border-bottom">
                                        <th class="pb-1 fw-normal">Service</th>
                                        <th class="pb-1 fw-normal">Category</th>
                                        <th class="pb-1 text-end fw-normal">Price</th>
                                    </tr>
                                </thead>
                                <tbody id="staffDetailServicesBody">
                                    <!-- Services list will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endpush