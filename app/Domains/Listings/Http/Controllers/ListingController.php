<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Listings\Services\ServiceService;
use App\Domains\Listings\Services\OpenOfferService;
use App\Domains\Listings\Services\CategoryService;
use Barryvdh\Debugbar\Facade as Debugbar;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function __construct(
        private readonly ServiceService $serviceService,
        private readonly OpenOfferService $openOfferService,
        private readonly CategoryService $categoryService
    ) {
    }

    /**
     * Display a listing of all services and open offers.
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        $type = $request->input('type', 'all');

        $services = collect();
        $openOffers = collect();

        if ($type === 'all' || $type === 'service') {
            $services = $this->serviceService->getAllServicesFiltered($filters);
        }
        
        if ($type === 'all' || $type === 'offer') {
            $openOffers = $this->openOfferService->getAllOpenOffersFiltered($filters);
        }

        // Merge and sort the collections by creation date
        $listings = $services->concat($openOffers)->sortByDesc('created_at');

        // Manually paginate the merged collection
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $currentPageItems = $listings->slice(($currentPage - 1) * $perPage, $perPage)->all();

        
        $paginatedListings = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            count($listings),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categories = $this->categoryService->listAllCategories();

        return view('browse', [
            'listings' => $paginatedListings,
            'categories' => $categories,
        ]);
    }
}