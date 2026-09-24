<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GuestBookingController extends Controller
{
    /**
     * Display the guest booking URL management page.
     */
    public function index()
    {
        $settingsService = app(\App\Services\SettingsService::class);
        $salon = auth()->user()->salon;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }

        $guestBookingUrl = route('booking.guest', ['salon_slug' => $salon->slug]);
        $guestBookingEnabled = app(\App\Services\SettingsService::class)->get('guest_booking_enabled', true, $salon->id);

        // Fetch recent guest bookings (limited to 5)
        $recentGuestBookings = \App\Models\Booking::where('salon_id', $salon->id)
            ->whereHas('customer', function ($query) {
                $query->where('is_guest', true);
            })
            ->with(['service', 'customer'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.guest-booking.index', compact(
            'salon',
            'guestBookingUrl',
            'guestBookingEnabled',
            'recentGuestBookings'
        ));
    }
}
