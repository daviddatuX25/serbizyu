<?php

namespace App\Domains\Search\Http\Controllers;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Services\CategoryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(private CategoryService $categoryService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->input('query');
        $addressId = $request->input('address_id');
        $categoryId = $request->input('category_id');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $services = Service::search($query)->with(['category', 'media']);

        if ($addressId) {
            $services->where('address_id', $addressId);
        }

        if ($categoryId) {
            $services->where('category_id', $categoryId);
        }

        if ($minPrice) {
            $services->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $services->where('price', '<=', $maxPrice);
        }

        $services->orderBy($sortBy, $sortDirection);

        $services = $services->paginate(10);
        $categories = $this->categoryService->listAllCategories();

        return view('search.index', compact('services', 'query', 'addressId', 'categoryId', 'minPrice', 'maxPrice', 'sortBy', 'sortDirection', 'categories'));
    }

    public function suggestions(Request $request)
    {
        $query = $request->input('query');
        $suggestions = Service::search($query)->take(5)->get()->pluck('title');

        return response()->json($suggestions);
    }
}
