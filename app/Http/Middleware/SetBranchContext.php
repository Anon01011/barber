<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Branch;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class SetBranchContext
{
    /**
     * Handle an incoming request.
     *
     * Set the current branch context in the application container.
     * This allows models using BelongsToBranch trait to automatically filter by branch.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure salon context exists
        $salon = $request->current_salon ?? (app()->has('current_salon') ? app('current_salon') : null);

        if (!$salon) {
            // Only log if it's not an asset request or similar
            if (!$request->is('css/*', 'js/*', 'images/*', 'fonts/*')) {
                Log::warning('SetBranchContext: No salon context found for request.', ['url' => $request->fullUrl()]);
            }
            return $next($request);
        }

        $user = auth()->user();
        $isSuperAdmin = $user && $user->hasRole('super_admin');
        $branch = null;

        // Priority 1: Session value (when user manually switches branches)
        if (session()->has('current_branch_id')) {
            $branchId = session()->get('current_branch_id');
            $branch = Branch::where('salon_id', $salon->id)
                ->where('id', $branchId)
                ->where('is_active', true)
                ->first();

            if ($branch) {
                Log::info('SetBranchContext: Using session branch', ['branch_id' => $branch->id]);
                app()->instance('current_branch', $branch);
                view()->share('current_branch', $branch);
                // Mark if super admin is using explicit branch context
                if ($isSuperAdmin) {
                    app()->instance('super_admin_branch_override', true);
                }
                return $next($request);
            } else {
                Log::warning('SetBranchContext: Session branch ID not found or inactive, clearing session.', ['branch_id' => $branchId]);
                session()->forget('current_branch_id');
            }
        }

        // Priority 2: User's assigned branch (skip for super admin unless they have explicit assignment)
        if ($user && $user->branch_id && !$isSuperAdmin) {
            $branch = Branch::where('salon_id', $salon->id)
                ->where('id', $user->branch_id)
                ->where('is_active', true)
                ->first();

            if ($branch) {
                Log::info('SetBranchContext: Using user assigned branch', ['branch_id' => $branch->id]);
                app()->instance('current_branch', $branch);
                view()->share('current_branch', $branch);
                session()->put('current_branch_id', $branch->id);
                return $next($request);
            }
        }

        // Priority 3: First active branch of the salon (fallback)
        if (!$branch) {
            $branch = Branch::where('salon_id', $salon->id)
                ->where('is_active', true)
                ->orderBy('id')
                ->first();

            if ($branch) {
                Log::info('SetBranchContext: Using fallback branch', ['branch_id' => $branch->id]);
            }
        }

        if ($branch) {
            // Share branch with all views
            view()->share('current_branch', $branch);

            // Set in container
            app()->instance('current_branch', $branch);

            // Store in session for persistence
            session(['current_branch_id' => $branch->id]);

            // Mark that super admin is using default branch context if applicable
            if ($isSuperAdmin && !app()->has('super_admin_branch_override')) {
                app()->instance('super_admin_default_branch', true);
            }
        } else {
            // If branches module is disabled, this is normal behavior, so use info or debug
            $enabledModules = app(\App\Services\SettingsService::class)->get('enabled_modules', [], false, false);
            $isBranchEnabled = isset($enabledModules['branches']) ? (bool) $enabledModules['branches'] : false;

            if ($isBranchEnabled) {
                Log::notice('SetBranchContext: No active branch found for salon', ['salon_id' => $salon->id]);
            } else {
                Log::debug('SetBranchContext: Defaulting to salon context (branches module disabled)');
            }
        }

        return $next($request);
    }
}
