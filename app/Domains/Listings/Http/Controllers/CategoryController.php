<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Services\CategoryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    /**
     * Display a listing of categories with filters
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'per_page' => $request->input('per_page', 15),
        ];

        // Handle status filter
        $status = $request->input('status', 'active');
        if ($status === 'all') {
            $filters['include_deleted'] = true;
        } elseif ($status === 'deleted') {
            $filters['only_deleted'] = true;
        } else {
            $filters['only_active'] = true;
        }

        try {
            $categories = $this->categoryService->getAllCategories($filters);
            return view('creator.categories.index', compact('categories'));
        } catch (\Exception $e) {
            return view('creator.categories.index', [
                'categories' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15)
            ]);
        }
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $this->categoryService->createCategory($validated);
            return redirect()
                ->route('creator.categories.index')
                ->with('success', 'Category created successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $this->categoryService->updateCategory($category->id, $validated);
            return redirect()
                ->route('creator.categories.index')
                ->with('success', 'Category updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category (soft or force delete)
     */
    public function destroy(Request $request, $id)
    {
        $deleteType = $request->input('delete_type', 'soft');

        try {
            if ($deleteType === 'force') {
                // Force delete (permanent)
                $category = Category::withTrashed()->findOrFail($id);
                $category->forceDelete();
                $message = 'Category permanently deleted!';
            } else {
                // Soft delete
                $category = Category::findOrFail($id);
                $category->delete();
                $message = 'Category deleted successfully! You can restore it later.';
            }

            return redirect()
                ->route('creator.categories.index')
                ->with('success', $message)
                ->withInput([
                    'status' => $request->input('status', $request->query('status', 'all')),
                ]);
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted category
     */
    public function restore( Request $request, $id)
    {
        try {
            $category = Category::withTrashed()->findOrFail($id);
            $category->restore();

            return redirect()
                ->route('creator.categories.index')
                ->with('success', 'Category restored successfully!')
                ->withInput(['status' => $request->input('status', $request->query('status', 'active'))]);
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to restore category: ' . $e->getMessage());
        }
    }
}