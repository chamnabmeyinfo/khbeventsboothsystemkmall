<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display category management page
     */
    public function index(Request $request)
    {
        $query = Category::with(['parent', 'children', 'booths']);

        // Filter by parent (main categories only)
        if ($request->filled('parent_only') && $request->parent_only == '1') {
            $query->where('parent_id', 0);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('name')->get();

        // Statistics
        $stats = [
            'total_categories' => Category::where('parent_id', 0)->count(),
            'total_subcategories' => Category::where('parent_id', '!=', 0)->count(),
            'active_categories' => Category::where('status', 1)->count(),
        ];

        return view('categories.index', compact('categories', 'stats'));
    }

    /**
     * Create a new category (AJAX)
     * Matches Yii actionCreateCategory
     */
    public function createCategory(Request $request)
    {
        if (!$request->has('name') || !$request->has('limit')) {
            return response()->json(['error' => 'Missing required fields'], 400);
        }
        
        if (empty($request->name) || empty($request->limit)) {
            return response()->json(['error' => 'Name and limit are required'], 400);
        }
        
        $category = Category::create([
            'name' => $request->name,
            'limit' => $request->limit,
            'parent_id' => $request->parent_id ?? 0,
            'status' => $request->status ?? 1,
        ]);
        
        return response('success');
    }

    /**
     * Update a category (AJAX)
     * Matches Yii actionUpdateCategory
     */
    public function updateCategory(Request $request)
    {
        if (!$request->has('id') || !$request->has('name') || !$request->has('limit')) {
            return response()->json(['error' => 'Missing required fields'], 400);
        }
        
        if (empty($request->id) || empty($request->name) || empty($request->limit)) {
            return response()->json(['error' => 'ID, name and limit are required'], 400);
        }
        
        $category = Category::findOrFail($request->id);
        $category->update([
            'name' => $request->name,
            'limit' => $request->limit,
            'parent_id' => $request->parent_id ?? $category->parent_id,
            'status' => $request->status ?? $category->status,
        ]);
        
        return response('success');
    }

    /**
     * Delete a category (AJAX)
     * Matches Yii actionDeleteCategory
     */
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        
        return response('success');
    }

    /**
     * Create a sub-category (AJAX)
     * Matches Yii actionCreateSubCategory
     */
    public function createSubCategory(Request $request)
    {
        if (!$request->has('name') || !$request->has('limit') || !$request->has('parent_id')) {
            return response()->json(['error' => 'Missing required fields'], 400);
        }
        
        if (empty($request->name) || empty($request->limit) || empty($request->parent_id)) {
            return response()->json(['error' => 'Name, limit and parent_id are required'], 400);
        }
        
        $category = Category::create([
            'name' => $request->name,
            'limit' => $request->limit,
            'parent_id' => $request->parent_id,
            'status' => $request->status ?? 1,
        ]);
        
        return response('success');
    }

    /**
     * Update a sub-category (AJAX)
     * Matches Yii actionUpdateSubCategory
     */
    public function updateSubCategory(Request $request)
    {
        if (!$request->has('id') || !$request->has('name') || !$request->has('limit')) {
            return response()->json(['error' => 'Missing required fields'], 400);
        }
        
        if (empty($request->id) || empty($request->name) || empty($request->limit)) {
            return response()->json(['error' => 'ID, name and limit are required'], 400);
        }
        
        $category = Category::findOrFail($request->id);
        $category->update([
            'name' => $request->name,
            'limit' => $request->limit,
            'parent_id' => $request->parent_id ?? $category->parent_id,
            'status' => $request->status ?? $category->status,
        ]);
        
        return response('success');
    }

    /**
     * Standard CRUD methods for admin interface
     */
    public function create()
    {
        $parents = Category::where('parent_id', 0)->orderBy('name')->get();
        return view('categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:category,id',
            'limit' => 'nullable|integer|min:0',
            'status' => 'required|integer|in:0,1',
        ]);

        $category = Category::create($validated);

        // Return JSON if request expects JSON (for AJAX/modal requests)
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'parent_id' => $category->parent_id,
                    'limit' => $category->limit,
                    'status' => $category->status,
                ]
            ], 200);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $parents = Category::where('parent_id', 0)->where('id', '!=', $category->id)->orderBy('name')->get();
        return view('categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:category,id',
            'limit' => 'nullable|integer|min:0',
            'status' => 'required|integer|in:0,1',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}

