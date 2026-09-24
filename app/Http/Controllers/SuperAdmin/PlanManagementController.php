<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

use App\Services\SettingsService;

class PlanManagementController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function index()
    {
        $plans = Plan::withCount('subscriptions')->orderBy('sort_order')->get();
        return view('super-admin.plans.index', compact('plans'));
    }

    public function create()
    {
        $enabledModules = $this->settingsService->get('enabled_modules', [], null, null);
        return view('super-admin.plans.create', compact('enabledModules'));
    }

    public function store(Request $request)
    {
        \Log::info('=== PLAN CREATION STARTED ===', [
            'request_data' => $request->except(['_token'])
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:plans',
                'business_type' => 'required|string|in:salon,barber,both',
                'price' => 'required|numeric|min:0',
                'duration_in_days' => 'required|integer|min:1',
                'trial_days' => 'nullable|integer|min:0',
                'description' => 'nullable|string',
                'features' => 'nullable|json',
                'limits' => 'nullable|array',
                'limits.*' => 'nullable|integer|min:0',
                'max_users' => 'nullable|integer',
                'max_branches' => 'nullable|integer',
            ]);

            \Log::info('Validation passed', ['validated_data' => $validated]);

            // Handle limits - convert empty strings to null for unlimited
            if (isset($validated['limits'])) {
                foreach ($validated['limits'] as $key => $value) {
                    if ($value === '' || $value === null) {
                        $validated['limits'][$key] = null;
                    } else {
                        $validated['limits'][$key] = (int) $value;
                    }
                }
            }

            // Explicitly handle max_users and max_branches for empty string to null conversion
            $validated['max_users'] = ($request->max_users === '' || $request->max_users === null) ? null : (int) $request->max_users;
            $validated['max_branches'] = ($request->max_branches === '' || $request->max_branches === null) ? null : (int) $request->max_branches;


            // Default trial_days to 0 if null
            $validated['trial_days'] = $validated['trial_days'] ?? 0;

            // Check if branch feature is enabled in features
            $features = json_decode($validated['features'] ?? '[]', true);
            $hasBranchFeature = in_array('Multi-Branch Support', $features) ||
                in_array('multi_branch', $features) ||
                in_array('branches', $features);

            // Only set max_branches if branch feature is enabled, otherwise set to 0
            if (!isset($validated['max_branches'])) {
                $validated['max_branches'] = $hasBranchFeature ? 1 : 0;
            }

            // max_users will be null if not set, implying unlimited


            $plan = Plan::create($validated);

            \Log::info('Plan created successfully', ['plan_id' => $plan->id]);

            return redirect()->route('admin.plans.index')
                ->with('success', 'Plan created successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Plan validation failed', [
                'errors' => $e->errors(),
                'request' => $request->except(['_token'])
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Plan creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['_token'])
            ]);
            return back()->with('error', 'Plan creation failed: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        $enabledModules = $this->settingsService->get('enabled_modules', [], null, null);
        return view('super-admin.plans.edit', compact('plan', 'enabledModules'));
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        \Log::info('=== PLAN UPDATE STARTED ===', [
            'plan_id' => $id,
            'request_limits' => $request->input('limits'),
            'request_all' => $request->except(['_token', '_method'])
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'business_type' => 'required|string|in:salon,barber,both',
                'price' => 'required|numeric|min:0',
                'duration_in_days' => 'required|integer|min:1',
                'trial_days' => 'nullable|integer|min:0',
                'description' => 'nullable|string',
                'features' => 'nullable|json',
                'limits' => 'nullable|array',
                'limits.*' => 'nullable|min:0',  // Removed integer type to allow string numbers
                'max_users' => 'nullable|integer',
                'max_branches' => 'nullable|integer',
                'is_active' => 'nullable',
                'is_popular' => 'nullable',
            ]);

            \Log::info('Validation passed', ['validated_limits' => $validated['limits'] ?? null]);

            // Handle limits - convert empty strings to null for unlimited
            if (isset($validated['limits'])) {
                foreach ($validated['limits'] as $key => $value) {
                    if ($value === '' || $value === null || $value === '0') {
                        $validated['limits'][$key] = null;
                    } else {
                        $validated['limits'][$key] = (int) $value;
                    }
                }
            }

            // Explicitly handle max_users and max_branches for empty string to null conversion
            $validated['max_users'] = ($request->max_users === '' || $request->max_users === null) ? null : (int) $request->max_users;
            $validated['max_branches'] = ($request->max_branches === '' || $request->max_branches === null) ? null : (int) $request->max_branches;


            \Log::info('After processing limits', ['processed_limits' => $validated['limits'] ?? null]);

            // Handle checkboxes that might not be in request
            $validated['is_active'] = $request->has('is_active');
            $validated['is_popular'] = $request->has('is_popular');

            // Default trial_days to 0 if null
            $validated['trial_days'] = $validated['trial_days'] ?? 0;

            // Check if branch feature is enabled in features
            $features = json_decode($validated['features'] ?? '[]', true);
            $hasBranchFeature = in_array('Multi-Branch Support', $features) ||
                in_array('multi_branch', $features) ||
                in_array('branches', $features);

            // Only set max_branches if branch feature is enabled, otherwise set to 0
            if (!isset($validated['max_branches'])) {
                $validated['max_branches'] = $hasBranchFeature ? 1 : 0;
            }

            // max_users will be null if not set, implying unlimited


            $plan->update($validated);

            \Log::info('Plan updated successfully', [
                'plan_id' => $plan->id,
                'new_limits' => $plan->fresh()->limits
            ]);

            // Sync permissions for all salons on this plan
            \App\Jobs\SyncPlanPermissions::dispatch($plan);

            return redirect()->route('admin.plans.index')
                ->with('success', 'Plan updated successfully. Permissions are being synced for all subscribers.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Plan update validation failed', [
                'errors' => $e->errors(),
                'request' => $request->except(['_token', '_method'])
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Plan update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Plan update failed: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);

        if ($plan->subscriptions()->count() > 0) {
            return back()->with('error', 'Cannot delete plan with active subscriptions');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan deleted successfully');
    }

    /**
     * Duplicate an existing plan
     */
    public function duplicate($id)
    {
        $originalPlan = Plan::findOrFail($id);

        $newPlan = $originalPlan->replicate();
        $newPlan->name = $originalPlan->name . ' (Copy)';
        $newPlan->slug = $originalPlan->slug . '-copy-' . time();
        $newPlan->is_active = false; // Deactivate copy by default
        $newPlan->save();

        return redirect()->route('admin.plans.edit', $newPlan->id)
            ->with('success', 'Plan duplicated successfully. Please review and activate.');
    }
}
