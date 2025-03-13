<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Get all categories with optional filtering
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Filtering by type (Income or Expense)
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filtering by user
        if (Auth::check()) {
            $query->where(function ($q) {
                $q->whereNull('user_id') // System default categories
                  ->orWhere('user_id', Auth::id()); // User's custom categories
            });
        }

        // Get paginated categories
        $categories = $query->with('subCategories')->get();

        return response()->json([
            'message' => 'Categories retrieved successfully.',
            'categories' => $categories
        ]);
    }

    /**
     * Create a new category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Income,Expense',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:10',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        $category = Category::create([
            'id' => Str::uuid(),
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type,
            'icon' => $request->icon,
            'color' => $request->color,
            'parent_id' => $request->parent_id
        ]);

        return response()->json([
            'message' => 'Category created successfully.',
            'category' => $category
        ], 201);
    }

    /**
     * Get a specific category
     */
    public function show($id)
    {
        $category = Category::with('subCategories')->findOrFail($id);

        return response()->json([
            'message' => 'Category retrieved successfully.',
            'category' => $category
        ]);
    }

    /**
     * Update a category
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:Income,Expense',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:10',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        $category = Category::findOrFail($id);

        if ($category->user_id !== Auth::id() && $category->user_id !== null) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $category->update($request->only(['name', 'type', 'icon', 'color', 'parent_id']));

        return response()->json([
            'message' => 'Category updated successfully.',
            'category' => $category
        ]);
    }

    /**
     * Delete a category
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->user_id !== Auth::id() && $category->user_id !== null) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        // Delete category and its subcategories
        $category->subCategories()->delete();
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.']);
    }

    /**
     * Get subcategories for a specific category
     */
    public function subcategories($id)
    {
        $subcategories = Category::where('parent_id', $id)->get();

        return response()->json([
            'message' => 'Subcategories retrieved successfully.',
            'subcategories' => $subcategories
        ]);
    }
}
