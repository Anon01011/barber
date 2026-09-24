<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the employee dashboard with ratings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        // Get ratings received by this employee
        $ratings = Rating::where('staff_id', $user->id)
            ->with(['booking.customer', 'booking.package', 'service'])
            ->latest()
            ->paginate(10);

        // Calculate statistics
        $totalRatings = Rating::where('staff_id', $user->id)->count();
        $averageRating = Rating::where('staff_id', $user->id)->avg('rating') ?? 0;

        // Rating breakdown (count by star rating)
        $ratingBreakdown = Rating::where('staff_id', $user->id)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->pluck('count', 'rating')
            ->toArray();

        // Fill in missing ratings with 0
        for ($i = 1; $i <= 5; $i++) {
            if (!isset($ratingBreakdown[$i])) {
                $ratingBreakdown[$i] = 0;
            }
        }
        krsort($ratingBreakdown);

        // Recent ratings (last 5)
        $recentRatings = Rating::where('staff_id', $user->id)
            ->with(['booking.customer', 'booking.package', 'service'])
            ->latest()
            ->take(5)
            ->get();

        return view('employee.dashboard', compact(
            'ratings',
            'totalRatings',
            'averageRating',
            'ratingBreakdown',
            'recentRatings'
        ));
    }
}
