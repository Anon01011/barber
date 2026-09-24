<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\SystemNotification;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        $notifications = collect();

        // 1. Get System Notifications (Announcements)
        if (Auth::check()) {
            $salonId = Auth::user()->salon_id;
            $systemNotifications = SystemNotification::active()
                ->forSalon($salonId)
                ->orderBy('created_at', 'desc')
                // ->take(5) // Removed limit as per user request
                ->get()
                ->map(function ($notification) {
                    // Normalize to standard structure
                    $notification->is_system = true;
                    return $notification;
                });

            $notifications = $notifications->merge($systemNotifications);

            // 2. Get User Specific Notifications (Database Channel)
            $userNotifications = Auth::user()->unreadNotifications()
                // ->take(5) // Removed limit as per user request
                ->get()
                ->map(function ($notification) {
                    // Normalize to standard structure
                    return (object) [
                        'id' => $notification->id,
                        'title' => $notification->data['title'] ?? 'Notification',
                        'message' => $notification->data['message'] ?? '',
                        'type' => $notification->data['type'] ?? 'info',
                        'created_at' => $notification->created_at,
                        'is_system' => false,
                        'read_at' => $notification->read_at,
                        'data' => $notification->data
                    ];
                });

            $notifications = $notifications->merge($userNotifications);
        }

        // Sort by date desc
        $notifications = $notifications->sortByDesc('created_at')->values();

        $view->with('system_notifications', $notifications);
    }
}
