<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    /**
     * Display a listing of the branches.
     */
    public function index()
    {
        $branches = Branch::latest()->paginate(10);
        return view('admin.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create()
    {
        return view('admin.branches.create');
    }

    /**
     * Store a newly created branch in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:branches,email',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $salon = auth()->user()->salon;

            // Check if salon's plan includes branch feature
            if ($salon && !$salon->hasBranchModule()) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Multi-branch support is not available in your current plan. Please upgrade to add branches.');
            }

            // Enforce branch limit based on salon plan
            if ($salon && !$salon->canAddBranch()) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Branch limit reached for your current plan. Please upgrade to add more branches.');
            }

            Branch::create($validated);

            DB::commit();

            return redirect()->route('admin.branches.index')
                ->with('success', 'Branch created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create branch. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified branch.
     */
    public function show(Branch $branch)
    {
        return view('admin.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    /**
     * Update the specified branch in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:branches,email,' . $branch->id,
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $branch->update($validated);

            DB::commit();

            return redirect()->route('admin.branches.index')
                ->with('success', 'Branch updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update branch. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified branch from storage.
     */
    public function destroy(Branch $branch)
    {
        try {
            DB::beginTransaction();

            $branch->delete();

            DB::commit();

            return redirect()->route('admin.branches.index')
                ->with('success', 'Branch deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete branch. ' . $e->getMessage());
        }
    }


    /**
     * Switch current branch context.
     */
    public function switchBranch(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);

        $user = auth()->user();

        // Check if user is allowed to switch branches
        // Only salon_admin, super_admin can switch freely
        // Staff/Managers are restricted to their assigned branch (unless we add a 'multi_branch_staff' role later)
        if (!$user->hasRole(['salon_admin', 'super_admin'])) {
            // If user is restricted, they can only "switch" to their assigned branch (which is redundant but safe)
            if ($request->branch_id != $user->branch_id) {
                return back()->with('error', 'You are not authorized to switch branches.');
            }
        }

        $branch = Branch::where('id', $request->branch_id)
            ->where('salon_id', $user->salon_id)
            ->where('is_active', true)
            ->firstOrFail();

        session(['current_branch_id' => $branch->id]);
        session(['current_branch_name' => $branch->name]);

        return back()->with('success', 'Switched to branch: ' . $branch->name);
    }
}
