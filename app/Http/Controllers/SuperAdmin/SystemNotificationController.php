<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemNotification;
use App\Models\Salon;
use Illuminate\Http\Request;

class SystemNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:super_admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = SystemNotification::with('salon')
            ->latest()
            ->paginate(20);

        return view('super-admin.notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $salons = Salon::where('is_active', true)->get();
        $selectedSalonId = $request->get('salon_id');
        return view('super-admin.notifications.create', compact('salons', 'selectedSalonId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,danger',
            'target_salon_id' => 'nullable|exists:salons,id',
        ]);

        SystemNotification::create($validated);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemNotification $notification)
    {
        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    /**
     * Toggle notification active status
     */
    public function toggle(SystemNotification $notification)
    {
        $notification->update(['is_active' => !$notification->is_active]);

        return back()->with('success', 'Notification status updated.');
    }
}
