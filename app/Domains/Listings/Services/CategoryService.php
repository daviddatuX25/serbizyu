<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\Category;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function getAllCategories(): Collection
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            throw new ResourceNotFoundException('No categories found.');
        }

        if ($categories->every->trashed()) {
            throw new ResourceNotFoundException('Categories have all been deleted.');
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
}