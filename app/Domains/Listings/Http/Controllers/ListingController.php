<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Listings\Services\ServiceService;
use App\Domains\Listings\Services\OpenOfferService;
use App\Domains\Listings\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ListingController extends Controller
{
    public function __construct(
        private readonly ServiceService $serviceService,
        private readonly OpenOfferService $openOfferService,
        private readonly CategoryService $categoryService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->all();
        $type = $request->input('type', 'all');

        $perPage = 10;
        $currentPage = $request->input('page', 1);

        $services = collect();
        $offers = collect();

        // Get services only if applicable
        if ($type === 'all' || $type === 'service') {
            $services = $this->serviceService->getFilteredServices($filters)
                ->map(fn($service) => $this->attachThumbnail($service));
        }

        // Get offers only if applicable
        if ($type === 'all' || $type === 'offer') {
            $offers = $this->openOfferService->getFilteredOffers($filters)
                ->map(fn($offer) => $this->attachThumbnail($offer));
        }

        // Merge and sort by created_at descending
        $merged = $services->concat($offers)
            ->sortByDesc(fn($item) => $item->created_at)
            ->values();

        // Manual pagination
        $paginated = new LengthAwarePaginator(
            $merged->forPage($currentPage, $perPage),
            $merged->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $categories = $this->categoryService->listAllCategories();

        return view('browse', [
            'listings' => $paginated,
            'categories' => $categories,
        ]);
    }

    // Attach first media thumbnail safely
    private function attachThumbnail($model)
    {
        $model->thumbnail = $model->getMedia('thumbnail')->first();
        return $model;
    }
}
