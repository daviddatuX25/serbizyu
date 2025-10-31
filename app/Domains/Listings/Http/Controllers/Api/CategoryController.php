<?php

namespace App\Domains\Listings\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Listings\Http\Resources\CategoryResource;
use App\Domains\Listings\Http\Resources\CategoryCollection;
use App\Traits\ApiResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use App\Domains\Listings\Models\Category;


class CategoryController extends Controller
{
    use ApiResponses, AuthorizesRequests;

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories($request->all());
        return $this->success(new CategoryCollection($categories), 'Categories retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Category::class);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        $category = $this->categoryService->createCategory($validatedData);
        return $this->success(new CategoryResource($category), 'Category created successfully.', Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategory($id);
        return $this->success(new CategoryResource($category), 'Category retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $category = $this->categoryService->getCategory($id);
        $this->authorize('update', $category);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $updatedCategory = $this->categoryService->updateCategory($id, $validatedData);
        return $this->success(new CategoryResource($updatedCategory), 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategory($id);
        $this->authorize('delete', $category);

        $this->categoryService->deleteCategory($id);
        return $this->success(null, 'Category deleted successfully.', Response::HTTP_NO_CONTENT);
    }
}
