<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;

class BranchController extends Controller
{
    /**
     * Switch the current branch context
     */
    public function switch(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);

        $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
        
        // Verify branch belongs to current salon
        $branch = Branch::where('id', $request->branch_id)
            ->where('salon_id', $salon->id)
            ->where('is_active', true)
            ->firstOrFail();

        // Set in session
        session()->put('current_branch_id', $branch->id);

        return redirect()->back()->with('success', 'Switched to ' . $branch->name);
    }

    /**
     * Get current branch info
     */
    public function current()
    {
        $branch = app()->has('current_branch') ? app('current_branch') : null;
        
        return response()->json([
            'branch' => $branch
        ]);
    }
}
