<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display customer feedback & star ratings dashboard.
     */
    public function index(Request $request)
    {
        $salon = app()->bound('current_salon') && app('current_salon')
            ? app('current_salon')
            : auth()->user()->salon;

        $salonId = $salon?->id ?? auth()->user()->salon_id;

        $query = Booking::with(['customer', 'staff', 'service'])
            ->where('salon_id', $salonId)
            ->where('is_rated', true);

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->rating);
        }

        $reviews = $query->latest('updated_at')->paginate(15)->withQueryString();

        // Overall KPI statistics
        $baseStatsQuery = Booking::where('salon_id', $salonId)->where('is_rated', true);
        $totalReviews = (clone $baseStatsQuery)->count();
        $averageRating = $totalReviews > 0 ? round((clone $baseStatsQuery)->avg('rating'), 2) : 0;

        $distribution = [];
        for ($star = 5; $star >= 1; $star--) {
            $count = (clone $baseStatsQuery)->where('rating', $star)->count();
            $distribution[$star] = [
                'count' => $count,
                'percent' => $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0,
            ];
        }

        $staffList = User::where('salon_id', $salonId)
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['employee', 'manager', 'salon_admin']))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.feedback.index', compact(
            'reviews',
            'totalReviews',
            'averageRating',
            'distribution',
            'staffList'
        ));
    }
}
