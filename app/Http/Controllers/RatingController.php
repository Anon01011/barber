<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RatingController extends Controller
{
    /**
     * Show the rating form for guest customers
     */
    public function showGuestRatingForm($token)
    {
        // Find booking by token
        $booking = Booking::where('rating_token', $token)
            ->with(['customer', 'service', 'staff', 'salon'])
            ->first();

        // Validate token exists
        if (!$booking) {
            return view('ratings.guest-rating', [
                'error' => 'Invalid rating link. Please contact the salon for assistance.'
            ]);
        }

        // Check if token is expired
        if (!$booking->isRatingTokenValid()) {
            return view('ratings.guest-rating', [
                'error' => 'This rating link has expired. Please contact the salon for a new link.',
                'booking' => $booking
            ]);
        }

        // Check if already rated
        if ($booking->is_rated) {
            return view('ratings.guest-rating', [
                'success' => 'Thank you! You have already rated this booking.',
                'booking' => $booking
            ]);
        }

        // Check if booking is completed
        if ($booking->status !== Booking::STATUS_COMPLETED) {
            return view('ratings.guest-rating', [
                'error' => 'This booking has not been completed yet.',
                'booking' => $booking
            ]);
        }

        return view('ratings.guest-rating', [
            'booking' => $booking,
            'token' => $token
        ]);
    }

    /**
     * Submit guest rating
     */
    public function submitGuestRating(Request $request, $token)
    {
        // Validate request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        // Find booking by token
        $booking = Booking::where('rating_token', $token)
            ->with(['customer', 'service', 'staff', 'salon'])
            ->first();

        // Validate token exists
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid rating link.'
            ], 404);
        }

        // Check if token is expired
        if (!$booking->isRatingTokenValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This rating link has expired.'
            ], 410);
        }

        // Check if already rated
        if ($booking->is_rated) {
            return response()->json([
                'success' => false,
                'message' => 'You have already rated this booking.'
            ], 422);
        }

        // Check if booking is completed
        if ($booking->status !== Booking::STATUS_COMPLETED) {
            return response()->json([
                'success' => false,
                'message' => 'This booking has not been completed yet.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create the rating
            $rating = new Rating([
                'salon_id' => $booking->salon_id,
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer->user_id ?? $booking->customer_id,
                'service_id' => $booking->service_id,
                'employee_id' => $booking->staff_id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]);

            $rating->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your rating! We appreciate your feedback.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error submitting guest rating', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id ?? null,
                'token' => $token
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit rating. Please try again.'
            ], 500);
        }
    }
}
