@extends('layouts.app')

@section('title', 'Staff Schedules')

@section('content')
    <div class="container-fluid px-4 py-5">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1" style="color: #ec4899;">Staff Schedules</h2>
                <p class="text-muted">View and manage working hours for all staff members</p>
            </div>
            <a href="{{ route('admin.staff.index') }}" class="btn btn-staff-outline">
                <i class="fas fa-arrow-left me-2"></i>Back to Staff
            </a>
        </div>




        <!-- Filter Controls -->
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.staff.schedules') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">View Type</label>
                        <select name="view_type" class="form-select" onchange="toggleDateInputs(this.value)">
                            <option value="week" {{ request('view_type', 'week') == 'week' ? 'selected' : '' }}>Week View
                            </option>
                            <option value="day" {{ request('view_type') == 'day' ? 'selected' : '' }}>Day View</option>
                            <option value="month" {{ request('view_type') == 'month' ? 'selected' : '' }}>Month View</option>
                            <option value="custom" {{ request('view_type') == 'custom' ? 'selected' : '' }}>Custom Range
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3" id="singleDateField"
                        style="display: {{ request('view_type') == 'day' ? 'block' : 'none' }};">
                        <label class="form-label">Select Date</label>
                        <input type="date" name="date" class="form-control"
                            value="{{ request('date', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3" id="monthField"
                        style="display: {{ request('view_type') == 'month' ? 'block' : 'none' }};">
                        <label class="form-label">Select Month</label>
                        <input type="month" name="month" class="form-control"
                            value="{{ request('month', now()->format('Y-m')) }}">
                    </div>
                    <div class="col-md-3" id="startDateField"
                        style="display: {{ request('view_type') == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3" id="endDateField"
                        style="display: {{ request('view_type') == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-staff me-2">
                            <i class="fas fa-filter me-1"></i>Apply Filter
                        </button>
                        <a href="{{ route('admin.staff.schedules') }}" class="btn btn-staff-outline">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 border-bottom" style="border-color: rgba(236, 72, 153, 0.1) !important;">
                <h5 class="card-title mb-0 text-staff">Schedule Overview</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 150px;">Staff Member</th>
                                @foreach($period as $date)
                                    <th class="text-center">
                                        {{ $date->format('D') }}<br>
                                        <small class="text-muted fw-normal">{{ $date->format('d/m') }}</small>
                                    </th>
                                @endforeach
                                <th class="text-center" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleTableBody">
                            @forelse($staff as $member)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <div class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                    {{ collect(explode(' ', $member->name))->map(fn($s) => strtoupper(substr($s, 0, 1)))->take(2)->join('') }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 small">{{ $member->name }}</h6>
                                                <small class="text-muted">{{ $member->roles->first()->name ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    @foreach($period as $date)
                                        @php
                                            $dayOfWeek = $date->dayOfWeek;
                                            $dateKey = $date->format('Y-m-d');
                                            $schedule = $schedules[$member->id][$dateKey] ?? null;

                                            // Check for absence
                                            $isAbsent = false;
                                            $absenceReason = '';
                                            if (isset($absences[$member->id])) {
                                                foreach ($absences[$member->id] as $absence) {
                                                    $start = \Carbon\Carbon::parse($absence->start_at);
                                                    $end = \Carbon\Carbon::parse($absence->end_at);
                                                    if ($date->between($start, $end) || $date->isSameDay($start) || $date->isSameDay($end)) {
                                                        $isAbsent = true;
                                                        $absenceReason = $absence->reason;
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <td class="text-center small schedule-cell {{ $isAbsent ? 'bg-danger-subtle' : '' }} {{ !$schedule ? 'empty-cell' : '' }}"
                                            style="vertical-align: middle; cursor: pointer;" data-staff-id="{{ $member->id }}"
                                            data-day="{{ $dayOfWeek }}" data-date="{{ $dateKey }}"
                                            data-is-working="{{ $schedule && $schedule->is_working ? '1' : '0' }}"
                                            data-start="{{ $schedule && $schedule->start_time ? substr($schedule->start_time, 0, 5) : '09:00' }}"
                                            data-end="{{ $schedule && $schedule->end_time ? substr($schedule->end_time, 0, 5) : '17:00' }}"
                                            data-allows-ot="{{ $schedule && $schedule->allows_overtime ? '1' : '0' }}"
                                            data-ot-start="{{ $schedule && $schedule->overtime_start ? substr($schedule->overtime_start, 0, 5) : '18:00' }}"
                                            data-ot-end="{{ $schedule && $schedule->overtime_end ? substr($schedule->overtime_end, 0, 5) : '20:00' }}"
                                            onclick="editCell(this)">

                                            @if($isAbsent)
                                                <span class="badge bg-danger text-white d-block mb-1">Absent</span>
                                                <small class="text-danger d-block">{{ $absenceReason ?: 'Time Off' }}</small>
                                            @elseif($schedule && $schedule->is_working)
                                                <span class="badge bg-success-subtle text-white d-block mb-1">Working</span>
                                                <small class="text-muted d-block">{{ substr($schedule->start_time, 0, 5) }} -
                                                    {{ substr($schedule->end_time, 0, 5) }}</small>
                                                @if($schedule->allows_overtime && $schedule->overtime_start && $schedule->overtime_end)
                                                    <small class="text-warning d-block mt-1">
                                                        <i class="fas fa-clock"></i> OT: {{ substr($schedule->overtime_start, 0, 5) }} -
                                                        {{ substr($schedule->overtime_end, 0, 5) }}
                                                    </small>
                                                @endif
                                            @elseif($schedule)
                                                <span class="badge bg-secondary-subtle text-secondary">Off</span>
                                            @else
                                                <span class="badge bg-light text-muted border">Empty</span>
                                                <small class="text-muted d-block" style="font-size: 0.7rem;">Click to add</small>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-light"
                                            onclick="quickEditSchedule({{ $member->id }}, '{{ $member->name }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($period) + 2 }}" class="text-center py-4">No staff members found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Edit Schedule Modal -->
    @component('components.modal', ['id' => 'quickEditModal', 'title' => 'Edit Schedule'])
    <div class="modal-body">
        <h5 id="quickEditStaffName" class="mb-3"></h5>
        <form id="quickEditForm">
            @csrf
            @method('PUT')
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Working?</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>OT?</th>
                            <th>OT Start</th>
                            <th>OT End</th>
                        </tr>
                    </thead>
                    <tbody id="quickEditTableBody">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="form-check" id="weekSpecificOption" style="display: none;">
                    <input class="form-check-input" type="checkbox" id="saveWeekSpecific">
                    <label class="form-check-label small" for="saveWeekSpecific">
                        Apply to <strong>this week only</strong> ({{ $period[0]->format('M d') }} -
                        {{ $period[count($period) - 1]->format('M d') }})
                    </label>
                </div>
                <div>
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-staff">Save Schedule</button>
                </div>
            </div>
        </form>
    </div>
    @endcomponent

    @push('styles')
        <style>
            .avatar {
                width: 35px;
                height: 35px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                vertical-align: middle;
            }

            .avatar-title {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
                font-weight: 600;
                padding: 2px;
                text-align: center;
                line-height: 1;
            }

            .bg-primary-subtle {
                background-color: rgba(13, 110, 253, 0.1);
            }

            .bg-success-subtle {
                background-color: rgba(25, 135, 84, 0.1);
            }

            .bg-secondary-subtle {
                background-color: rgba(108, 117, 125, 0.1);
            }

            .schedule-cell:hover {
                background-color: rgba(236, 72, 153, 0.05);
                border: 1px dashed #ec4899;
            }

            .text-staff {
                color: #ec4899 !important;
            }

            .btn-staff {
                background-color: #ec4899;
                border-color: #ec4899;
                color: white;
            }

            .btn-staff:hover {
                background-color: #db2777;
                border-color: #db2777;
                color: white;
            }

            .btn-staff-outline {
                border-color: #ec4899;
                color: #ec4899;
            }

            .btn-staff-outline:hover {
                background-color: #ec4899;
                border-color: #ec4899;
                color: white;
            }

            .schedule-cell.empty-cell {
                background-color: rgba(108, 117, 125, 0.05);
            }

            .schedule-cell.empty-cell:hover {
                background-color: rgba(236, 72, 153, 0.1);
                border: 1px dashed #ec4899;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const currentViewType = '{{ $viewType }}';
            const periodStartDate = '{{ $period[0]->format("Y-m-d") }}';
            let currentStaffId = null;

            function toggleDateInputs(viewType) {
                document.getElementById('singleDateField').style.display = viewType === 'day' ? 'block' : 'none';
                document.getElementById('monthField').style.display = viewType === 'month' ? 'block' : 'none';
                document.getElementById('startDateField').style.display = viewType === 'custom' ? 'block' : 'none';
                document.getElementById('endDateField').style.display = viewType === 'custom' ? 'block' : 'none';
            }

            function editCell(cell) {
                event.stopPropagation();
                const staffId = cell.dataset.staffId;
                const day = cell.dataset.day;
                const date = cell.dataset.date;
                const isWorking = cell.dataset.isWorking === '1';
                const start = cell.dataset.start;
                const end = cell.dataset.end;
                const allowsOt = cell.dataset.allowsOt === '1';
                const otStart = cell.dataset.otStart;
                const otEnd = cell.dataset.otEnd;

                // Create inline edit form
                const form = `
                                        <div class="p-2 bg-light rounded" style="min-width: 220px;">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="quick-working-${day}" ${isWorking ? 'checked' : ''} onchange="toggleQuickInputs(${day})">
                                                <label class="form-check-label small" for="quick-working-${day}"><strong>Working</strong></label>
                                            </div>
                                            <div id="quick-times-${day}" style="display: ${isWorking ? 'block' : 'none'};">
                                                <label class="form-label small mb-1">Start Time</label>
                                                <input type="time" class="form-control form-control-sm mb-2" id="quick-start-${day}" value="${start}">
                                                <label class="form-label small mb-1">End Time</label>
                                                <input type="time" class="form-control form-control-sm mb-2" id="quick-end-${day}" value="${end}">
                                                <div class="form-check form-switch mb-1">
                                                    <input class="form-check-input" type="checkbox" id="quick-ot-${day}" ${allowsOt ? 'checked' : ''} onchange="toggleQuickOT(${day})">
                                                    <label class="form-check-label small" for="quick-ot-${day}"><strong>Overtime</strong></label>
                                                </div>
                                                <div id="quick-ot-times-${day}" style="display: ${allowsOt ? 'block' : 'none'};">
                                                    <label class="form-label small mb-1">OT Start</label>
                                                    <input type="time" class="form-control form-control-sm mb-1" id="quick-ot-start-${day}" value="${otStart}">
                                                    <label class="form-label small mb-1">OT End</label>
                                                    <input type="time" class="form-control form-control-sm mb-2" id="quick-ot-end-${day}" value="${otEnd}">
                                                </div>
                                            </div>
                                            <div class="d-flex gap-1 mt-2">
                                                ${currentViewType === 'week'
                        ? `<button class="btn btn-sm btn-success flex-fill" onclick="saveQuickEdit(${staffId}, ${day})"><i class="fas fa-check"></i> Save (All Weeks)</button>`
                        : `<button class="btn btn-sm btn-success flex-fill" onclick="saveDateSchedule(${staffId}, '${date}', ${day})"><i class="fas fa-check"></i> Save (This Date)</button>`
                    }
                                                <button class="btn btn-sm btn-secondary flex-fill" onclick="cancelQuickEdit()">
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                            </div>
                                        </div>
                                    `;

                // Remove any existing edit forms
                document.querySelectorAll('.inline-edit-form').forEach(el => el.remove());

                // Add edit form
                const formDiv = document.createElement('div');
                formDiv.className = 'inline-edit-form';
                formDiv.innerHTML = form;
                cell.innerHTML = '';
                cell.appendChild(formDiv);

                // Prevent clicks inside the form from closing it
                formDiv.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            }

            function toggleQuickInputs(day) {
                const checkbox = document.getElementById(`quick-working-${day}`);
                const timesDiv = document.getElementById(`quick-times-${day}`);
                timesDiv.style.display = checkbox.checked ? 'block' : 'none';
            }

            function toggleQuickOT(day) {
                const checkbox = document.getElementById(`quick-ot-${day}`);
                const otDiv = document.getElementById(`quick-ot-times-${day}`);
                otDiv.style.display = checkbox.checked ? 'block' : 'none';
            }

            function cancelQuickEdit() {
                location.reload();
            }

            function saveDateSchedule(staffId, date, day) {
                const isWorking = document.getElementById(`quick-working-${day}`).checked;
                const start = document.getElementById(`quick-start-${day}`).value;
                const end = document.getElementById(`quick-end-${day}`).value;
                const allowsOt = document.getElementById(`quick-ot-${day}`).checked;
                const otStart = document.getElementById(`quick-ot-start-${day}`).value;
                const otEnd = document.getElementById(`quick-ot-end-${day}`).value;

                const data = {
                    date: date,
                    is_working: isWorking ? 1 : 0,
                    start_time: start,
                    end_time: end,
                    allows_overtime: allowsOt ? 1 : 0,
                    overtime_start: otStart,
                    overtime_end: otEnd
                };

                fetch(`{{ url('') }}/{{ request()->current_salon->slug ?? '' }}/admin/staff/${staffId}/schedule/date`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                    .then(res => res.json())
                    .then(data => {
                        location.reload();
                    })
                    .catch(err => {
                        alert('Error saving schedule');

                    });
            }

            function saveQuickEdit(staffId, day) {
                // Legacy function kept for compatibility if needed, but editCell now uses saveDateSchedule
                // ... (original implementation if needed, but we are replacing the block)
                // Since we are replacing the whole block, we can just omit it or keep it if other parts use it.
                // But wait, the "Quick Edit" button in the table row (not the cell click) calls quickEditSchedule -> quickEditModal -> saveQuickEdit.
                // So I MUST keep saveQuickEdit for the modal!

                const isWorking = document.getElementById(`quick-working-${day}`).checked;
                const start = document.getElementById(`quick-start-${day}`).value;
                const end = document.getElementById(`quick-end-${day}`).value;
                const allowsOt = document.getElementById(`quick-ot-${day}`).checked;
                const otStart = document.getElementById(`quick-ot-start-${day}`).value;
                const otEnd = document.getElementById(`quick-ot-end-${day}`).value;

                // Create schedule array with all 7 days (fetch current data first)
                fetch(`{{ url('') }}/{{ request()->current_salon->slug ?? '' }}/admin/staff/${staffId}/schedule`)
                    .then(res => res.json())
                    .then(currentSchedule => {
                        const scheduleMap = {};
                        currentSchedule.forEach(item => scheduleMap[item.day_of_week] = item);

                        const schedule = [];
                        for (let i = 0; i < 7; i++) {
                            if (i == day) {
                                schedule.push({
                                    day_of_week: parseInt(day),
                                    is_working: isWorking,
                                    start_time: start,
                                    end_time: end,
                                    allows_overtime: allowsOt,
                                    overtime_start: otStart,
                                    overtime_end: otEnd
                                });
                            } else {
                                const existing = scheduleMap[i] || { day_of_week: i, is_working: false, start_time: '09:00', end_time: '17:00', allows_overtime: false, overtime_start: '18:00', overtime_end: '20:00' };
                                schedule.push({
                                    day_of_week: i,
                                    is_working: existing.is_working || false,
                                    start_time: existing.start_time ? existing.start_time.substring(0, 5) : '09:00',
                                    end_time: existing.end_time ? existing.end_time.substring(0, 5) : '17:00',
                                    allows_overtime: existing.allows_overtime || false,
                                    overtime_start: existing.overtime_start ? existing.overtime_start.substring(0, 5) : '18:00',
                                    overtime_end: existing.overtime_end ? existing.overtime_end.substring(0, 5) : '20:00'
                                });
                            }
                        }

                        fetch(`{{ url('') }}/{{ request()->current_salon->slug ?? '' }}/admin/staff/${staffId}/schedule`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ schedule })
                        })
                            .then(res => res.json())
                            .then(data => {
                                location.reload();
                            })
                            .catch(err => {
                                alert('Error saving schedule');

                            });
                    });
            }

            function quickEditSchedule(staffId, staffName) {
                currentStaffId = staffId;
                document.getElementById('quickEditStaffName').textContent = `Schedule for ${staffName}`;

                // Reset checkbox if it exists
                const weekSpecificCheck = document.getElementById('saveWeekSpecific');
                if (weekSpecificCheck) {
                    weekSpecificCheck.checked = false;
                    // Only show this option in Week View
                    document.getElementById('weekSpecificOption').style.display = currentViewType === 'week' ? 'block' : 'none';
                }

                // Load Schedule
                fetch(`{{ url('') }}/{{ request()->current_salon->slug ?? '' }}/admin/staff/${staffId}/schedule`)
                    .then(res => res.json())
                    .then(data => {
                        const tbody = document.getElementById('quickEditTableBody');
                        tbody.innerHTML = '';

                        // Ensure we have entries for all days 0-6
                        const scheduleMap = {};
                        data.forEach(item => scheduleMap[item.day_of_week] = item);

                        for (let i = 0; i < 7; i++) {
                            const item = scheduleMap[i] || {
                                day_of_week: i,
                                is_working: false,
                                start_time: '09:00',
                                end_time: '17:00',
                                allows_overtime: false,
                                overtime_start: '18:00',
                                overtime_end: '20:00'
                            };
                            const isWorking = item.is_working ? 'checked' : '';
                            const allowsOT = item.allows_overtime ? 'checked' : '';
                            const startTime = item.start_time ? item.start_time.substring(0, 5) : '09:00';
                            const endTime = item.end_time ? item.end_time.substring(0, 5) : '17:00';
                            const otStart = item.overtime_start ? item.overtime_start.substring(0, 5) : '18:00';
                            const otEnd = item.overtime_end ? item.overtime_end.substring(0, 5) : '20:00';

                            const row = `
                                                    <tr>
                                                        <td>${daysOfWeek[i]}</td>
                                                        <td>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" name="schedule[${i}][is_working]" value="1" ${isWorking} onchange="toggleTimeInputs(this)">
                                                                <input type="hidden" name="schedule[${i}][day_of_week]" value="${i}">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control form-control-sm" name="schedule[${i}][start_time]" value="${startTime}" ${!item.is_working ? 'disabled' : ''}>
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control form-control-sm" name="schedule[${i}][end_time]" value="${endTime}" ${!item.is_working ? 'disabled' : ''}>
                                                        </td>
                                                        <td>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" name="schedule[${i}][allows_overtime]" value="1" ${allowsOT} onchange="toggleOTInputs(this)" ${!item.is_working ? 'disabled' : ''}>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control form-control-sm ot-input" name="schedule[${i}][overtime_start]" value="${otStart}" ${!item.allows_overtime || !item.is_working ? 'disabled' : ''}>
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control form-control-sm ot-input" name="schedule[${i}][overtime_end]" value="${otEnd}" ${!item.allows_overtime || !item.is_working ? 'disabled' : ''}>
                                                        </td>
                                                    </tr>
                                                `;
                            tbody.insertAdjacentHTML('beforeend', row);
                        }
                    });

                new bootstrap.Modal(document.getElementById('quickEditModal')).show();
            }

            function toggleTimeInputs(checkbox) {
                const row = checkbox.closest('tr');
                const inputs = row.querySelectorAll('input[type="time"]');
                const otCheckbox = row.querySelector('input[name*="allows_overtime"]');
                inputs.forEach(input => input.disabled = !checkbox.checked);
                if (otCheckbox) otCheckbox.disabled = !checkbox.checked;
                if (!checkbox.checked && otCheckbox) {
                    otCheckbox.checked = false;
                }
            }

            function toggleOTInputs(checkbox) {
                const row = checkbox.closest('tr');
                const otInputs = row.querySelectorAll('.ot-input');
                otInputs.forEach(input => input.disabled = !checkbox.checked);
            }

            document.getElementById('quickEditForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = { schedule: [] };

                const rawData = {};
                for (let [key, value] of formData.entries()) {
                    const match = key.match(/schedule\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const index = match[1];
                        const field = match[2];
                        if (!rawData[index]) rawData[index] = {};
                        rawData[index][field] = value;
                    }
                }

                data.schedule = Object.values(rawData).map(item => ({
                    day_of_week: parseInt(item.day_of_week),
                    is_working: item.is_working == '1',
                    start_time: item.start_time,
                    end_time: item.end_time,
                    allows_overtime: item.allows_overtime == '1',
                    overtime_start: item.overtime_start,
                    overtime_end: item.overtime_end
                }));

                // Determine endpoint based on checkbox
                const isWeekSpecific = document.getElementById('saveWeekSpecific').checked;
                let endpoint = `{{ url('') }}/{{ request()->current_salon->slug ?? '' }}/admin/staff/${currentStaffId}/schedule`;
                let method = 'PUT';

                if (isWeekSpecific) {
                    endpoint += '/week';
                    data.start_date = periodStartDate;
                }

                fetch(endpoint, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        location.reload();
                    })
                    .catch(err => alert('Error saving schedule'));
            });
        </script>
    @endpush
@endsection