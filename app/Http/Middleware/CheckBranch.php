<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Branch;
use Symfony\Component\HttpFoundation\Response;

class CheckBranch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return $next($request);
        }

        // Get current salon from app container (set by CheckSalonSlug middleware)
        $salon = app()->has('current_salon') ? app('current_salon') : $user->salon;
        
        if (!$salon) {
            return $next($request);
        }

        // Check if salon has multi-branch support
        if (!$salon->canUseFeature('Multi-Branch Support')) {
            // Single branch salon - use user's default branch or first branch
            $branch = $user->branch ?? $salon->branches()->first();
            
            if ($branch) {
                app()->instance('current_branch', $branch);
                $request->merge(['current_branch' => $branch]);
                session(['current_branch_id' => $branch->id]);
            }
            
            return $next($request);
        }

        // Multi-branch salon - get branch from session or user's default
        $branchId = session('current_branch_id', $user->branch_id);
        
        // If user is NOT a salon admin/owner, they must be restricted to their assigned branch
        // Assuming 'salon_admin' role can access all branches
        // We need to check if the user has a role that allows global access
        $canAccessAllBranches = $user->hasRole(['salon_admin', 'super_admin']);

        if (!$canAccessAllBranches && $user->branch_id) {
            // Strict check: User MUST be in their assigned branch
            // If they try to switch (via session), we overwrite it with their assigned branch
            $branchId = $user->branch_id;
        }

        if ($branchId) {
            $branch = Branch::where('id', $branchId)
                ->where('salon_id', $salon->id)
                ->where('is_active', true)
                ->first();
                
            if ($branch) {
                // Additional check: If user is restricted, ensure the branch matches their assignment
                if (!$canAccessAllBranches && $user->branch_id && $branch->id !== $user->branch_id) {
                     // This should theoretically be covered by the $branchId override above, but safety first
                     $branch = $user->branch; // Revert to assigned branch
                }

                if ($branch && $branch->is_active) {
                    app()->instance('current_branch', $branch);
                    $request->merge(['current_branch' => $branch]);
                    // Only update session if it's different to avoid unnecessary writes
                    if (session('current_branch_id') !== $branch->id) {
                        session(['current_branch_id' => $branch->id]);
                    }
                }
            } else {
                // Branch not found or inactive
                // If user is restricted, they are stuck (maybe show error or handle gracefully)
                // If user is admin, fall back to first active
                if ($canAccessAllBranches) {
                     $branch = $salon->branches()->active()->first();
                     if ($branch) {
                        app()->instance('current_branch', $branch);
                        $request->merge(['current_branch' => $branch]);
                        session(['current_branch_id' => $branch->id]);
                     }
                }
            }
        } else {
            // No branch in session or user default
            // If admin, pick first active
            if ($canAccessAllBranches) {
                $branch = $salon->branches()->active()->first();
                if ($branch) {
                    app()->instance('current_branch', $branch);
                    $request->merge(['current_branch' => $branch]);
                    session(['current_branch_id' => $branch->id]);
                }
            }
            // If regular staff has no branch_id, they might be "floating" or unassigned? 
            // For now, let's assume they need a branch_id to work.
        }

        return $next($request);
    }
}
