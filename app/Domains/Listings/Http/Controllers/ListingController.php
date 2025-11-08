<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Listings\Services\ServiceService;
use App\Domains\Listings\Services\OpenOfferService;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function __construct(
        private readonly ServiceService $serviceService,
        private readonly OpenOfferService $openOfferService
    ) {
    }

    /**
     * Display a listing of all services and open offers.
     */
    public function index(Request $request)
    {
        // For simplicity, we'll fetch both collections and merge them.
        // A more advanced implementation might use a single polymorphic query or a search index.
        $services = $this->serviceService->getPaginatedServices($request->all());
        $openOffers = $this->openOfferService->getPaginatedOpenOffers($request->all());

        // Merge and sort the collections by creation date
        $listings = $services->concat($openOffers)->sortByDesc('created_at');

        // Manually paginate the merged collection
        $perPage = 20;
        $currentPage = $request->input('page', 1);
        $currentPageItems = $listings->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        $paginatedListings = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            count($listings),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('browse', ['listings' => $paginatedListings]);
    }
}