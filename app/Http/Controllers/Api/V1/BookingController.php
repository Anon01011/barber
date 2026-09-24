<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Get the current authenticated user's salon_id.
     * Returns 403 JSON response if the user has no salon context.
     */
    private function getSalonId(): int
    {
        $salonId = auth()->user()->salon_id;
        if (!$salonId) {
            abort(response()->json(['error' => 'Unauthorized: No salon context.'], 403));
        }
        return $salonId;
    }

    public function index(Request $request)
    {
        try {
            $salonId = $this->getSalonId();

            Log::info('API: Fetching bookings', [
                'start' => $request->start,
                'end' => $request->end,
                'staff_id' => $request->staff_id
            ]);

            // SECURITY: Scope to authenticated user's salon
            $query = Booking::with(['customer', 'service', 'staff'])
                ->where('salon_id', $salonId);

            // Filter by date range
            if ($request->has('start') && $request->has('end')) {
                try {
                    $start = Carbon::parse($request->get('start'))->startOfDay();
                    $end = Carbon::parse($request->get('end'))->endOfDay();

                    $query->where(function ($q) use ($start, $end) {
                        $q->whereBetween('start_time', [$start, $end])
                            ->orWhereBetween('end_time', [$start, $end])
                            ->orWhere(function ($q) use ($start, $end) {
                                $q->where('start_time', '<=', $start)
                                    ->where('end_time', '>=', $end);
                            });
                    });
                } catch (\Exception $e) {
                    Log::error('API: Error parsing dates:', [
                        'error' => $e->getMessage(),
                        'start_input' => $request->get('start'),
                        'end_input' => $request->get('end')
                    ]);
                    return response()->json(['error' => 'Invalid date format'], 400);
                }
            }

            // Filter by staff — only staff belonging to this salon
            if ($request->has('staff_id') && !empty($request->get('staff_id'))) {
                $query->where('staff_id', $request->get('staff_id'))
                    ->where('salon_id', $salonId);
            }

            $bookings = $query->get();

            $formattedBookings = $bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->customer ? $booking->customer->name : 'Unknown Customer',
                    'start' => $booking->start_time->format('Y-m-d H:i:s'),
                    'end' => $booking->end_time->format('Y-m-d H:i:s'),
                    'service' => $booking->service ? $booking->service->name : 'Unknown Service',
                    'duration' => $booking->service ? $booking->service->duration . ' minutes' : 'Unknown Duration',
                    'staff' => $booking->staff ? $booking->staff->name : 'Unknown Staff',
                    'notes' => $booking->notes,
                    'status' => $booking->status,
                    'className' => 'fc-event-' . strtolower($booking->status)
                ];
            });

            return response()->json($formattedBookings);
        } catch (\Exception $e) {
            Log::error('API: Error fetching bookings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to fetch bookings'], 500);
        }
    }

    public function store(Request $request)
    {
        $salonId = $this->getSalonId();

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'notes' => 'nullable|string|max:1000'
        ]);

        // SECURITY: Verify customer belongs to this salon
        $customer = Customer::where('id', $validated['customer_id'])
            ->where('salon_id', $salonId)
            ->firstOrFail();

        // SECURITY: Verify service belongs to this salon
        $service = Service::where('id', $validated['service_id'])
            ->where('salon_id', $salonId)
            ->firstOrFail();

        // SECURITY: Verify staff belongs to this salon
        $staff = User::where('id', $validated['staff_id'])
            ->where('salon_id', $salonId)
            ->firstOrFail();

        // Check Booking Limit
        $salon = auth()->user()->salon;
        if (!$salon->canAddBooking()) {
            return response()->json([
                'error' => 'Booking limit reached for this month.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $startTime = $validated['date'] . ' ' . $validated['time'];
            $endTime = date('Y-m-d H:i:s', strtotime($startTime . ' + ' . $service->duration . ' minutes'));

            // Check for booking conflicts
            $conflict = Booking::where('staff_id', $validated['staff_id'])
                ->where('salon_id', $salonId)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                                ->where('end_time', '>=', $endTime);
                        });
                })
                ->exists();

            if ($conflict) {
                return response()->json([
                    'error' => 'This time slot is already booked for the selected staff member.'
                ], 422);
            }

            $booking = Booking::create([
                'salon_id' => $salonId,
                'created_by' => auth()->id(),
                'customer_id' => $validated['customer_id'],
                'service_id' => $validated['service_id'],
                'staff_id' => $validated['staff_id'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'amount' => $service->price,
                'notes' => $validated['notes'],
                'status' => 'pending',
                'source' => 'api'
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Booking created successfully',
                'booking' => $booking
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API: Error creating booking', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to create booking'], 500);
        }
    }

    /**
     * Display the specified booking.
     */
    public function show($id)
    {
        try {
            $salonId = $this->getSalonId();

            // SECURITY: Scope to current salon to prevent IDOR
            $booking = Booking::with(['customer', 'service', 'staff'])
                ->where('salon_id', $salonId)
                ->findOrFail($id);

            return response()->json([
                'id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'customer' => $booking->customer->name,
                'service_id' => $booking->service_id,
                'service' => $booking->service->name,
                'staff_id' => $booking->staff_id,
                'staff' => $booking->staff->name,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'duration' => $booking->duration,
                'status' => $booking->status,
                'notes' => $booking->notes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Booking not found'
            ], 404);
        }
    }

    public function update(Request $request, Booking $booking)
    {
        $salonId = $this->getSalonId();

        // SECURITY: Ensure booking belongs to the current salon
        if ($booking->salon_id !== $salonId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,confirmed,completed,cancelled'
        ]);

        // SECURITY: Verify customer, service, and staff belong to this salon
        Customer::where('id', $validated['customer_id'])->where('salon_id', $salonId)->firstOrFail();
        $service = Service::where('id', $validated['service_id'])->where('salon_id', $salonId)->firstOrFail();
        User::where('id', $validated['staff_id'])->where('salon_id', $salonId)->firstOrFail();

        try {
            DB::beginTransaction();

            $startTime = $validated['date'] . ' ' . $validated['time'];
            $endTime = date('Y-m-d H:i:s', strtotime($startTime . ' + ' . $service->duration . ' minutes'));

            // Check for booking conflicts
            $conflict = Booking::where('staff_id', $validated['staff_id'])
                ->where('salon_id', $salonId)
                ->where('id', '!=', $booking->id)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                                ->where('end_time', '>=', $endTime);
                        });
                })
                ->exists();

            if ($conflict) {
                return response()->json([
                    'error' => 'This time slot is already booked for the selected staff member.'
                ], 422);
            }

            $wasCompleted = $booking->status !== 'completed' && $validated['status'] === 'completed';

            if ($wasCompleted) {
                // Check if booking is in the future
                $salonTimezone = salon_timezone();
                if ($booking->start_time->isFuture() && !$booking->start_time->copy()->setTimezone($salonTimezone)->isToday()) {
                    return response()->json([
                        'error' => 'Bookings scheduled for the future cannot be completed. Only today\'s bookings can be completed today.'
                    ], 422);
                }
            }

            $booking->update([
                'customer_id' => $validated['customer_id'],
                'service_id' => $validated['service_id'],
                'staff_id' => $validated['staff_id'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'amount' => $service->price,
                'notes' => $validated['notes'],
                'status' => $validated['status']
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Booking updated successfully',
                'booking' => $booking
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API: Error updating booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to update booking'], 500);
        }
    }

    public function destroy(Booking $booking)
    {
        $salonId = $this->getSalonId();

        // SECURITY: Ensure booking belongs to the current salon before deletion
        if ($booking->salon_id !== $salonId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $booking->delete();
            return response()->json(['message' => 'Booking cancelled successfully']);
        } catch (\Exception $e) {
            Log::error('API: Error cancelling booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to cancel booking'], 500);
        }
    }
}