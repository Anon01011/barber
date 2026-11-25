<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Scopes\SalonScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RatingController extends Controller
{
    /**
     * Show the public guest rating form for a completed booking.
     */
    public function show(string $token)
    {
        $booking = Booking::withoutGlobalScope(SalonScope::class)
            ->with(['salon', 'staff', 'service', 'customer'])
            ->where('rating_token', $token)
            ->first();

        if (!$booking) {
            return view('ratings.status', [
                'status' => 'invalid',
                'title' => 'Invalid Review Link',
                'message' => 'This rating link is invalid or has already been used.',
            ]);
        }

        if ($booking->is_rated) {
            return view('ratings.status', [
                'status' => 'already_rated',
                'title' => 'Thank You!',
                'message' => 'You have already submitted your feedback for this appointment.',
                'salon' => $booking->salon,
            ]);
        }

        if (!$booking->isRatingTokenValid()) {
            return view('ratings.status', [
                'status' => 'expired',
                'title' => 'Link Expired',
                'message' => 'This rating link has expired. Please contact the salon if you would like to share feedback.',
                'salon' => $booking->salon,
            ]);
        }

        return view('ratings.form', [
            'booking' => $booking,
            'salon' => $booking->salon,
            'token' => $token,
        ]);
    }

    /**
     * Submit a customer rating and feedback.
     */
    public function store(Request $request, string $token)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::withoutGlobalScope(SalonScope::class)
            ->with(['salon'])
            ->where('rating_token', $token)
            ->first();

        if (!$booking || $booking->is_rated || !$booking->isRatingTokenValid() || !$booking->salon || !$booking->salon->hasFeedbackModule()) {
            return view('ratings.status', [
                'status' => 'invalid',
                'title' => 'Unable to Submit Review',
                'message' => 'This review link is no longer valid or feedback is currently unavailable.',
                'salon' => $booking?->salon,
            ]);
        }

        $updated = Booking::withoutGlobalScope(SalonScope::class)
            ->where('id', $booking->id)
            ->where('rating_token', $token)
            ->where('is_rated', false)
            ->update([
                'rating' => (int) $validated['rating'],
                'feedback' => $validated['feedback'] ?? null,
                'is_rated' => true,
                'rating_token' => null,
                'rating_token_expires_at' => null,
            ]);

        if ($updated === 0) {
            return view('ratings.status', [
                'status' => 'already_rated',
                'title' => 'Thank You!',
                'message' => 'You have already submitted your feedback for this appointment.',
                'salon' => $booking->salon,
            ]);
        }

        // Keep Rating model table in sync with Booking.rating / Booking.feedback
        if (class_exists(\App\Models\Rating::class)) {
            try {
                \App\Models\Rating::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'service_id' => $booking->service_id,
                    'employee_id' => $booking->staff_id,
                    'rating' => (int) $validated['rating'],
                    'comment' => $validated['feedback'] ?? null,
                ]);
            } catch (\Exception $e) {
                // Non-fatal if ratings table has different schema or constraint
            }
        }

        Log::info('Customer rating submitted', [
            'booking_id' => $booking->id,
            'salon_id' => $booking->salon_id,
            'rating' => $validated['rating'],
        ]);

        return view('ratings.status', [
            'status' => 'success',
            'title' => 'Thank You for Your Feedback!',
            'message' => 'Your ' . $validated['rating'] . '-star review has been recorded. We look forward to seeing you again!',
            'salon' => $booking->salon,
        ]);
    }
}
