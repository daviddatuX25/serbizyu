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

        $categories = $query->get();

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
        return $category->delete();
    }
}