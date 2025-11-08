<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\Category;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function getAllCategories(array $filters = [])
    {
        $query = Category::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // include soft-deleted categories if specified or maybe just have the options of all or only deleted or only active.
        if (!empty($filters['include_deleted']) && $filters['include_deleted'] == true) {
            $query->withTrashed();
        }

        if (!empty($filters['only_deleted']) && $filters['only_deleted'] == true) {
            $query->onlyTrashed();
        }

        if (!empty($filters['only_active']) && $filters['only_active'] == true) {
            $query->whereNull('deleted_at');
        }

        $perPage = $filters['per_page'] ?? 15;
        $categories = $query->paginate($perPage);

        if ($categories->isEmpty()) {
            throw new ResourceNotFoundException('No categories found matching the criteria.');
        }

        return $categories;
    }

    public function getCategory(int $id)
    {
        $category = Category::find($id);

        if ($category == null) {
            throw new ResourceNotFoundException('Category not found');
        }
        if($category->trashed())
        {
            throw new ResourceNotFoundException('Category does not exist.');
        }
        return $category;
    }

    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }

    public function updateCategory(int $id, array $data): ?Category
    {
        $category = $this->getCategory($id);
        $category->update($data);
        return $category;
    }

    public function deleteCategory(int $id): bool
    {
        $category = $this->getCategory($id);
        // options for soft delete or hard delete can be implemented here
        return $category->forceDelete();
        // return $category->delete();
    }

    public function listAllCategories(): Collection
    {
        return Category::orderBy('name')->get();
    }
}