<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    /**
     * Display the specified appointment.
     */
    public function show($id)
    {
        try {
            $user = auth()->user();

            // Verify employee belongs to a salon
            if (!$user->salon_id) {
                Log::error('Employee has no salon_id', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Employee account is not associated with a salon.'
                ], 403);
            }

            // First, get the booking without relationships to verify it exists
            $booking = Booking::findOrFail($id);

            // SECURITY: Verify booking belongs to employee's salon
            if ($booking->salon_id !== $user->salon_id) {
                Log::warning('Employee attempted to access booking from different salon', [
                    'user_id' => $user->id,
                    'user_salon_id' => $user->salon_id,
                    'booking_id' => $booking->id,
                    'booking_salon_id' => $booking->salon_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this appointment.'
                ], 403);
            }

            // Now load relationships one by one with error handling
            $data = [
                'id' => $booking->id,
                'start_time' => $booking->start_time->setTimezone(salon_timezone())->toIso8601String(),
                'end_time' => $booking->end_time->setTimezone(salon_timezone())->toIso8601String(),
                'amount' => $booking->amount,
                'status' => $booking->status,
                'notes' => $booking->notes,
                'created_at' => $booking->created_at,
            ];

            // Load customer if exists
            if ($booking->customer) {
                $data['customer'] = [
                    'id' => $booking->customer->id,
                    'name' => $booking->customer->name,
                    'email' => \App\Helpers\CustomerDataHelper::getMaskedEmail($booking->customer),
                    'phone' => \App\Helpers\CustomerDataHelper::getMaskedPhone($booking->customer)
                ];
            }

            // Load service if exists
            if ($booking->service) {
                $data['service'] = [
                    'id' => $booking->service->id,
                    'name' => $booking->service->name,
                    'duration' => $booking->service->duration,
                    'price' => $booking->service->price,
                ];
            }

            // Load staff if exists (using staff() relationship from Booking model)
            if ($booking->staff) {
                $data['staff'] = [
                    'id' => $booking->staff->id,
                    'name' => $booking->staff->name,
                    'email' => $booking->staff->email
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching appointment: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load appointment details.'
            ], 500);
        }
    }
    public function checkSlot($id)
    {
        try {
            $user = auth()->user();

            // Verify employee belongs to a salon
            if (!$user->salon_id) {
                return response()->json([
                    'available' => false,
                    'message' => 'Employee account is not associated with a salon.'
                ], 403);
            }

            $booking = Booking::findOrFail($id);

            // SECURITY: Verify booking belongs to employee's salon
            if ($booking->salon_id !== $user->salon_id) {
                Log::warning('Employee attempted to check slot from different salon', [
                    'user_id' => $user->id,
                    'user_salon_id' => $user->salon_id,
                    'booking_id' => $booking->id,
                    'booking_salon_id' => $booking->salon_id
                ]);
                return response()->json([
                    'available' => false,
                    'message' => 'Unauthorized access to this appointment.'
                ], 403);
            }

            // Check if the booking is still pending and not assigned
            if ($booking->status !== 'pending' || !in_array($booking->staff_assignment_status, ['pending', 'rejected'])) {
                return response()->json([
                    'available' => false,
                    'message' => 'This appointment is no longer available for assignment.'
                ]);
            }

            // Check if the booking time is in the future
            if ($booking->start_time->isPast()) {
                return response()->json([
                    'available' => false,
                    'message' => 'This appointment time has already passed.'
                ]);
            }

            // Check for conflicts with existing appointments
            $conflict = Booking::where('staff_id', auth()->id())
                ->where('id', '!=', $booking->id)
                ->where(function ($query) use ($booking) {
                    $query->whereBetween('start_time', [$booking->start_time, $booking->end_time])
                        ->orWhereBetween('end_time', [$booking->start_time, $booking->end_time])
                        ->orWhere(function ($q) use ($booking) {
                            $q->where('start_time', '<=', $booking->start_time)
                                ->where('end_time', '>=', $booking->end_time);
                        });
                })
                ->exists();

            if ($conflict) {
                return response()->json([
                    'available' => false,
                    'message' => 'You have another appointment scheduled during this time.'
                ]);
            }

            return response()->json([
                'available' => true,
                'message' => 'Slot is available for assignment.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking slot availability', [
                'booking_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'available' => false,
                'message' => 'Failed to check slot availability.'
            ], 500);
        }
    }

    public function accept($id)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();

            // Verify employee belongs to a salon
            if (!$user->salon_id) {
                DB::rollBack();
                Log::error('Employee has no salon_id during accept', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Employee account is not associated with a salon.'
                ], 403);
            }

            $booking = Booking::findOrFail($id);

            // SECURITY: Verify booking belongs to employee's salon
            if ($booking->salon_id !== $user->salon_id) {
                DB::rollBack();
                Log::warning('Employee attempted to accept booking from different salon', [
                    'user_id' => $user->id,
                    'user_salon_id' => $user->salon_id,
                    'booking_id' => $booking->id,
                    'booking_salon_id' => $booking->salon_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this appointment.'
                ], 403);
            }

            // Verify the booking is still available for assignment
            if ($booking->status !== 'pending' || !in_array($booking->staff_assignment_status, ['pending', 'rejected'])) {
                throw new \Exception('This appointment is no longer available for assignment.');
            }

            // Check for conflicts again (in case someone else took it)
            $conflict = Booking::where('staff_id', auth()->id())
                ->where('id', '!=', $booking->id)
                ->where(function ($query) use ($booking) {
                    $query->whereBetween('start_time', [$booking->start_time, $booking->end_time])
                        ->orWhereBetween('end_time', [$booking->start_time, $booking->end_time])
                        ->orWhere(function ($q) use ($booking) {
                            $q->where('start_time', '<=', $booking->start_time)
                                ->where('end_time', '>=', $booking->end_time);
                        });
                })
                ->exists();

            if ($conflict) {
                throw new \Exception('You have another appointment scheduled during this time.');
            }

            // Assign the booking to the staff member
            $amount = $booking->amount;
            if (is_null($amount) || $amount == 0) {
                $amount = $booking->service ? $booking->service->price : 0;
            }
            $booking->update([
                'staff_id' => auth()->id(),
                'status' => 'confirmed',
                'staff_assignment_status' => 'assigned',
                'staff_assigned_at' => now(),
                'amount' => $amount
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment accepted successfully.',
                'status_label' => 'Confirmed'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error accepting appointment', [
                'booking_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept appointment. Please try again.'
            ], 500);
        }
    }

    public function reject($id)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();

            // Verify employee belongs to a salon
            if (!$user->salon_id) {
                DB::rollBack();
                Log::error('Employee has no salon_id during reject', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Employee account is not associated with a salon.'
                ], 403);
            }

            $booking = Booking::findOrFail($id);

            // SECURITY: Verify booking belongs to employee's salon
            if ($booking->salon_id !== $user->salon_id) {
                DB::rollBack();
                Log::warning('Employee attempted to reject booking from different salon', [
                    'user_id' => $user->id,
                    'user_salon_id' => $user->salon_id,
                    'booking_id' => $booking->id,
                    'booking_salon_id' => $booking->salon_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this appointment.'
                ], 403);
            }

            // Allow rejection even if assigned
            if ($booking->status !== 'pending') {
                throw new \Exception('This appointment is no longer available for rejection.');
            }

            \Log::info('Rejecting booking', [
                'booking_id' => $booking->id,
                'staff_id_before' => $booking->staff_id,
                'rejected_by_staff_id' => auth()->id(),
            ]);
            // Clear staff assignment to make booking available for others and set rejected_by_staff_id
            $booking->rejected_by_staff_id = auth()->id();
            $booking->staff_id = null;
            $booking->staff_assignment_status = 'rejected';
            $booking->staff_assigned_at = null;
            if (!$booking->save()) {
                \Log::error('Failed to save booking rejection', ['booking_id' => $booking->id]);
                throw new \Exception('Failed to update booking rejection status.');
            }

            \Log::info('Booking rejected and updated', [
                'booking_id' => $booking->id,
                'staff_id_after' => $booking->staff_id,
                'rejected_by_staff_id_after' => $booking->rejected_by_staff_id,
            ]);

            // Additional debug log to confirm status update
            \Log::info('Booking status after rejection', [
                'booking_id' => $booking->id,
                'status' => $booking->status,
                'staff_assignment_status' => $booking->staff_assignment_status,
                'rejected_by_staff_id' => $booking->rejected_by_staff_id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment rejected successfully and made available for other employees.',
                'status_label' => 'Rejected'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting appointment', [
                'booking_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject appointment. Please try again.'
            ], 500);
        }
    }

    public function complete($id)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();

            // Verify employee belongs to a salon
            if (!$user->salon_id) {
                DB::rollBack();
                Log::error('Employee has no salon_id during complete', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Employee account is not associated with a salon.'
                ], 403);
            }

            $booking = Booking::findOrFail($id);

            // SECURITY: Verify booking belongs to employee's salon
            if ($booking->salon_id !== $user->salon_id) {
                DB::rollBack();
                Log::warning('Employee attempted to complete booking from different salon', [
                    'user_id' => $user->id,
                    'user_salon_id' => $user->salon_id,
                    'booking_id' => $booking->id,
                    'booking_salon_id' => $booking->salon_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this appointment.'
                ], 403);
            }

            // Verify the booking belongs to this staff member
            if ($booking->staff_id !== auth()->id()) {
                throw new \Exception('You are not assigned to this appointment.');
            }

            // Verify the booking is in confirmed status
            if ($booking->status !== 'confirmed') {
                throw new \Exception('Only confirmed appointments can be marked as completed.');
            }

            // Check if booking is in the future
            $salonTimezone = salon_timezone();
            if ($booking->start_time->isFuture() && !$booking->start_time->copy()->setTimezone($salonTimezone)->isToday()) {
                throw new \Exception('Appointments scheduled for the future cannot be completed. Only today\'s bookings can be completed today.');
            }

            // Mark as staff completed
            $booking->staffComplete();

            return response()->json([
                'success' => true,
                'message' => 'Appointment marked as completed from your side. Final completion with payment will be handled by the admin/manager.',
                'booking' => $booking->fresh(['customer.user', 'service', 'staff'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing appointment', [
                'booking_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete appointment. Please try again.'
            ], 500);
        }
    }
}