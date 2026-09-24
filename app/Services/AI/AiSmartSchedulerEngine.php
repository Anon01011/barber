<?php

namespace App\Services\AI;

use App\Models\Salon;
use App\Models\User;
use App\Models\Service;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AiSmartSchedulerEngine
{
    public function recommendBalancedSlots(Salon $salon): array
    {
        $firstService = Service::where('salon_id', $salon->id)->first();
        if (!$firstService) {
            return [
                'status' => 'info',
                'message' => 'No active services available for load balancing analysis.',
            ];
        }

        $res = $this->recommendSlots($salon, $firstService->id);
        if (isset($res['error'])) {
            return [
                'status' => 'info',
                'message' => $res['error'],
            ];
        }

        $topStaffName = is_array($res['recommended_staff'] ?? null)
            ? ($res['recommended_staff']['name'] ?? 'Staff')
            : (is_string($res['recommended_staff'] ?? null) ? $res['recommended_staff'] : ($res['top_recommendation']['staff_name'] ?? 'Staff'));

        return [
            'status' => 'success',
            'message' => 'AI Schedule Rebalancer: Recommended load-balanced slot assignment for ' . $topStaffName . ' on ' . ($res['target_date'] ?? 'tomorrow') . '.',
            'details' => $res,
        ];
    }

    /**
     * AI Recommender: Find optimal staff member and time slot recommendations for a customer and service.
     */
    public function recommendSlots(Salon $salon, int $serviceId = 0, ?int $customerId = null, ?string $preferredDate = null): array
    {
        $targetDate = $preferredDate ? Carbon::parse($preferredDate) : Carbon::tomorrow();
        $service = Service::where('salon_id', $salon->id)->find($serviceId);

        if (!$service) {
            return ['error' => 'Service not found for this salon.'];
        }

        $durationMinutes = (int) ($service->duration ?? 30);

        // Fetch active salon staff across employee, staff, and stylist roles
        $staffMembers = User::where('salon_id', $salon->id)
            ->where(function ($q) {
                $q->whereHas('roles', function ($r) {
                    $r->whereIn('name', ['employee', 'staff', 'stylist', 'barber']);
                })->orWhereNotNull('staff_id');
            })
            ->where(function ($q) {
                $q->where('status', 'active')
                  ->orWhere('status', '1')
                  ->orWhereNull('status');
            })
            ->get();

        if ($staffMembers->isEmpty()) {
            $staffMembers = User::where('salon_id', $salon->id)->take(5)->get();
        }

        if ($staffMembers->isEmpty()) {
            return ['error' => 'No active staff available in this salon.'];
        }

        // Analyze staff load for target date
        $staffScores = [];

        foreach ($staffMembers as $staff) {
            $existingBookingsCount = Booking::where('salon_id', $salon->id)
                ->where('staff_id', $staff->id)
                ->whereDate('start_time', $targetDate)
                ->where('status', '!=', 'cancelled')
                ->count();

            // Check if customer had previous 5-star or repeated bookings with this staff
            $pastCustomerVisits = 0;
            if ($customerId) {
                $pastCustomerVisits = Booking::where('salon_id', $salon->id)
                    ->where('customer_id', $customerId)
                    ->where('staff_id', $staff->id)
                    ->count();
            }

            // AI Match Score Formula
            $loadPenalty = $existingBookingsCount * 15;
            $affinityBonus = $pastCustomerVisits * 25;
            $matchScore = max(10, min(100, 80 - $loadPenalty + $affinityBonus));

            $staffScores[] = [
                'staff_id' => $staff->id,
                'staff_name' => $staff->name,
                'current_bookings_count' => $existingBookingsCount,
                'past_customer_affinity' => $pastCustomerVisits > 0,
                'ai_match_score' => $matchScore,
                'suggested_time_slots' => $this->generateAvailableSlotsForStaff($salon, $staff, $targetDate, $durationMinutes),
            ];
        }

        // Sort staff by highest AI match score
        usort($staffScores, fn($a, $b) => $b['ai_match_score'] <=> $a['ai_match_score']);

        return [
            'service_name' => $service->name,
            'target_date' => $targetDate->format('Y-m-d'),
            'recommended_staff' => $staffScores[0]['staff_name'] ?? 'Best Available Staff',
            'top_recommendation' => $staffScores[0] ?? null,
            'all_staff_options' => $staffScores,
        ];
    }

    /**
     * Generate available slot times for staff on date.
     */
    private function generateAvailableSlotsForStaff(Salon $salon, User $staff, Carbon $date, int $durationMinutes): array
    {
        $slots = [];
        $startHour = 9;  // 9:00 AM
        $endHour = 18;  // 6:00 PM

        $bookedSlots = Booking::where('salon_id', $salon->id)
            ->where('staff_id', $staff->id)
            ->whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->get();

        for ($h = $startHour; $h < $endHour; $h++) {
            $slotStart = (clone $date)->setTime($h, 0, 0);
            $slotEnd = (clone $slotStart)->addMinutes($durationMinutes);

            // Check collision with existing bookings
            $isOverlap = false;
            foreach ($bookedSlots as $b) {
                $bStart = Carbon::parse($b->start_time);
                $bEnd = Carbon::parse($b->end_time);

                if ($slotStart < $bEnd && $slotEnd > $bStart) {
                    $isOverlap = true;
                    break;
                }
            }

            if (!$isOverlap) {
                $slots[] = $slotStart->format('g:i A');
            }
        }

        return array_slice($slots, 0, 4); // Top 4 open slots
    }
}
