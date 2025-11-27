<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Common\Interfaces\AddressProviderInterface;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Listings\Services\OpenOfferService;
use App\Domains\Listings\Services\ServiceService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ListingController extends Controller
{
    public function __construct(
        private readonly ServiceService $serviceService,
        private readonly OpenOfferService $openOfferService,
        private readonly CategoryService $categoryService,
        private readonly AddressProviderInterface $addressProvider
    ) {}

    public function index(Request $request)
    {
        $filters = $request->all();
        $type = $request->input('type', 'all');

        $perPage = 10;
        $currentPage = $request->input('page', 1);

        $services = collect();
        $offers = collect();

        if ($type === 'all' || $type === 'service') {
            $services = $this->serviceService->getFilteredServices($filters)
                ->map(fn ($service) => $this->attachThumbnail($service));
        }

        if ($type === 'all' || $type === 'offer') {
            $offers = $this->openOfferService->getFilteredOffers($filters)
                ->map(fn ($offer) => $this->attachThumbnail($offer));
        }

        $merged = $services->concat($offers)
            ->sortByDesc(fn ($item) => $item->created_at)
            ->values();

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

        $selectedRegion = $request->input('region', '');
        $selectedProvince = $request->input('province', '');
        $selectedCity = $request->input('city', '');

        $provinces = [];
        $cities = [];

        if ($selectedRegion) {
            $provinces = $this->addressProvider->getProvinces($selectedRegion);
        }

        $provinces = $selectedRegion ? $this->addressProvider->getProvinces($selectedRegion) : [];
        $cities = [];

        if ($selectedCity || $selectedProvince) {
            $cities = $this->addressProvider->getCities($selectedProvince ?: $selectedRegion);
        } elseif ($selectedProvince) {
            $cities = $this->addressProvider->getCities($selectedProvince);
        }

        return view('browse', [
            'listings' => $paginated,
            'categories' => $categories,
            'regions' => $this->addressProvider->getRegions(),
            'provinces' => $provinces,
            'cities' => $cities,
            'selectedRegion' => $selectedRegion,
            'selectedProvince' => $selectedProvince,
            'selectedCity' => $selectedCity,
        ]);
    }

    private function attachThumbnail($model)
    {
        $model->thumbnail = $model->getMedia('thumbnail')->first();

        return $model;
    }
}
