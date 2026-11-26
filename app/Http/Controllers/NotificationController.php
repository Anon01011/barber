<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Get unread notifications as JSON for real-time polling.
     */
    public function getUnread(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            $unreadQuery = $user->unreadNotifications();
            $totalCount = $unreadQuery->count();
            $notifications = $unreadQuery->latest()->take(15)->get();

            return response()->json([
                'success' => true,
                'count'   => $totalCount,
                'items'   => $notifications->map(function ($n) {
                    return [
                        'id'         => $n->id,
                        'message'    => $n->data['message'] ?? ($n->data['title'] ?? 'New notification'),
                        'type'       => $n->data['type'] ?? 'info',
                        'created_at' => $n->created_at->diffForHumans(),
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch unread notifications: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not authenticated'
                    ], 401);
                }
                return redirect()->route('login');
            }

            // Mark all unread notifications as read
            $user->unreadNotifications->markAsRead();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'All notifications marked as read'
                ]);
            }

            return redirect()->back()->with('success', 'All notifications marked as read.');
        } catch (\Exception $e) {
            \Log::error('Failed to mark notifications as read: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark notifications as read'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to mark notifications as read.');
        }
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not authenticated'
                    ], 401);
                }
                return redirect()->route('login');
            }

            $notification = $user->notifications()->find($id);
            
            if (!$notification) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Notification not found'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Notification not found.');
            }

            $notification->markAsRead();

            $targetUrl = $notification->data['link'] ?? $notification->data['url'] ?? null;

            // Validate targetUrl against open redirects: only permit relative paths or same app origin
            if ($targetUrl) {
                $isRelative = str_starts_with($targetUrl, '/') && !str_starts_with($targetUrl, '//');
                $isSameOrigin = str_starts_with($targetUrl, config('app.url'));
                if (!$isRelative && !$isSameOrigin) {
                    $targetUrl = null;
                }
            }

            if ($request->ajax() || ($request->isJson() && $request->wantsJson())) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read',
                    'redirect' => $targetUrl,
                ]);
            }

            if ($targetUrl) {
                return redirect($targetUrl);
            }

            return redirect()->back()->with('success', 'Notification marked as read.');
        } catch (\Exception $e) {
            \Log::error('Failed to mark notification as read: ' . $e->getMessage());
            
            if ($request->ajax() || ($request->isJson() && $request->wantsJson())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark notification as read'
                ], 500);
            }

            return redirect()->back();
        }
    }
}
