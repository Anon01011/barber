<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InventoryCategoryController extends Controller
{
    use AuthorizesRequests;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super_admin|inventory.view')->only(['index', 'show', 'getActiveCategories']);
        $this->middleware('permission:inventory.categories.create')->only(['create', 'store']);
        $this->middleware('permission:inventory.categories.edit')->only(['edit', 'update']);
        $this->middleware('permission:inventory.categories.delete')->only(['destroy']);
    }
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $query = InventoryCategory::withCount('items');

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->has('status') && $request->status !== null) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $categories = $query->latest()->paginate(15)->withQueryString();

        return view('inventory.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        \Log::info('Category creation request data:', $request->all());
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:inventory_categories,name',
                'description' => 'nullable|string',
                'is_active' => 'sometimes|boolean',
                'icon' => 'nullable|string|max:50',
            ]);
            
            \Log::info('Validated data:', $validated);

            DB::beginTransaction();
            
            $category = new InventoryCategory();
            $category->name = $validated['name'];
            $category->description = $validated['description'] ?? null;
            $category->is_active = $request->boolean('is_active', true);
            $category->icon = $validated['icon'] ?? 'fas fa-box';
            $category->save();
            
            DB::commit();
            
            \Log::info('Category created successfully:', ['id' => $category->id]);
            
            return redirect()
                ->route('admin.inventory.categories.index')
                ->with('success', 'Category created successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error during category creation:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating category: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->with('error', 'Failed to create category. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, InventoryCategory $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('inventory_categories', 'name')->ignore($category->id),
            ],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $validated['is_active'] = $request->has('is_active');
            $category->update($validated);
            
            DB::commit();
            
            return redirect()
                ->route('admin.inventory.categories.index')
                ->with('success', 'Category updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Request $request, InventoryCategory $category)
    {
        try {
            DB::beginTransaction();
            
            $hasItems = $category->items()->exists();
            
            if ($hasItems && $request->force_delete !== '1') {
                return back()->with([
                    'error' => 'This category contains items. Are you sure you want to delete it?',
                    'show_force_delete' => true,
                    'category_id' => $category->id,
                    'category_name' => $category->name,
                    'items_count' => $category->items_count ?? 0
                ]);
            }
            
            // If force deleting or no items
            $category->delete();
            
            DB::commit();
            
            return redirect()
                ->route('admin.inventory.categories.index')
                ->with('success', 'Category deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    /**
     * Get all active categories for dropdowns.
     */
    public function getActiveCategories()
    {
        try {
            $categories = InventoryCategory::active()
                ->orderBy('name')
                ->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories: ' . $e->getMessage()
            ], 500);
        }
    }
}
