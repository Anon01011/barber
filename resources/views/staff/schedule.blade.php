<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Schedule') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Today's Schedule</h3>
                        @if($todayAppointments->isEmpty())
                            <p class="text-gray-500">No appointments scheduled for today.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Time</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Customer</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Service</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($todayAppointments as $appointment)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $appointment->start_time->format('h:i A') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $appointment->customer->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div>{{ $appointment->service->name }}</div>
                                                    @if($appointment->package)
                                                        <div class="mt-1">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                                style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.7em;">
                                                                <i class="fas fa-box mr-1"></i>{{ $appointment->package->name }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                        @if($appointment->status === 'completed') bg-green-100 text-green-800
                                                                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                                                        @else bg-yellow-100 text-yellow-800
                                                                        @endif">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if(is_null($appointment->staff_id) && $appointment->staff_assignment_status === 'pending')
                                                        <button
                                                            class="accept-btn bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded mr-2"
                                                            data-id="{{ $appointment->id }}">Accept</button>
                                                        <button
                                                            class="reject-btn bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded"
                                                            data-id="{{ $appointment->id }}">Reject</button>
                                                    @elseif($appointment->staff_id === auth()->id() && $appointment->staff_assignment_status === 'assigned')
                                                        <button
                                                            class="complete-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded"
                                                            data-id="{{ $appointment->id }}">Complete</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Upcoming Appointments</h3>
                        @if($upcomingAppointments->isEmpty())
                            <p class="text-gray-500">No upcoming appointments scheduled.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Time</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Customer</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Service</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($upcomingAppointments as $appointment)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $appointment->start_time->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $appointment->start_time->format('h:i A') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $appointment->customer->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div>{{ $appointment->service->name }}</div>
                                                    @if($appointment->package)
                                                        <div class="mt-1">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                                style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.7em;">
                                                                <i class="fas fa-box mr-1"></i>{{ $appointment->package->name }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                        @if($appointment->status === 'completed') bg-green-100 text-green-800
                                                                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                                                        @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                                                        @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                                                        @elseif($appointment->status === 'no_show') bg-gray-100 text-gray-800
                                                                        @else bg-gray-100 text-gray-800
                                                                        @endif">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        @vite('resources/js/employee-dashboard.js')

        <script>
            // Helper function to get salon slug
            function getSalonSlug() {
                // Priority 1: Get from authenticated user's salon (most reliable)
                const userSalonSlug = '{{ auth()->user()->salon->slug ?? "" }}';
                if (userSalonSlug) return userSalonSlug;

                // Priority 2: Get from route parameter
                const bladeSlug = '{{ request()->route("salon_slug") ?? "" }}';
                if (bladeSlug) return bladeSlug;

                // Priority 3: Extract from current URL path
                const pathParts = window.location.pathname.split('/').filter(p => p);
                // If the first part is 'employee', then we are likely missing the slug in the URL
                // But if we are here, we are desperate.
                return pathParts[0] || '';
            }

            document.addEventListener('DOMContentLoaded', function () {
                const acceptButtons = document.querySelectorAll('.accept-btn');
                const rejectButtons = document.querySelectorAll('.reject-btn');

                acceptButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const bookingId = this.getAttribute('data-id');
                        const salonSlug = getSalonSlug();
                        if (!salonSlug) {
                            alert('Error: Could not determine salon URL. Please refresh the page.');
                            return;
                        }
                        fetch(`/${salonSlug}/employee/appointments/${bookingId}/accept`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Appointment accepted successfully.');
                                    location.reload();
                                } else {
                                    alert('Failed to accept appointment: ' + data.message);
                                }
                            })
                            .catch(error => {
                                alert('Error accepting appointment.');
                            });
                    });
                });

                rejectButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const bookingId = this.getAttribute('data-id');
                        const salonSlug = getSalonSlug();
                        if (!salonSlug) {
                            alert('Error: Could not determine salon URL. Please refresh the page.');
                            return;
                        }
                        fetch(`/${salonSlug}/employee/appointments/${bookingId}/reject`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Appointment rejected successfully.');
                                    location.reload();
                                } else {
                                    alert('Failed to reject appointment: ' + data.message);
                                }
                            })
                            .catch(error => {
                                alert('Error rejecting appointment.');
                            });
                    });
                });

                // Complete button handler
                const completeButtons = document.querySelectorAll('.complete-btn');
                completeButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const bookingId = this.getAttribute('data-id');
                        const salonSlug = getSalonSlug();
                        if (!salonSlug) {
                            alert('Error: Could not determine salon URL. Please refresh the page.');
                            return;
                        }

                        // First, try to complete the appointment (which will tell us if payment is required)
                        fetch(`/${salonSlug}/employee/appointments/${bookingId}/complete`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.requires_payment) {
                                    // Show payment modal
                                    showPaymentModal(bookingId, data.amount || 0);
                                } else if (data.success) {
                                    alert('Appointment completed successfully.');
                                    location.reload();
                                } else {
                                    alert('Failed to complete appointment: ' + data.message);
                                }
                            })
                            .catch(error => {

                                alert('Error completing appointment.');
                            });
                    });
                });
            });
        </script>
    @endpush

    @include('admin.bookings.partials.payment_modal')
</x-app-layout>