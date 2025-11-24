<?php

namespace App\Http\Controllers\Api;

use App\Domains\Common\Interfaces\AddressProviderInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    protected AddressProviderInterface $addressProvider;

    public function __construct(AddressProviderInterface $addressProvider)
    {
        $this->addressProvider = $addressProvider;
    }

    public function regions(): JsonResponse
    {
        $regions = $this->addressProvider->getRegions();
        return response()->json($regions);
    }

    public function provinces(string $regionCode): JsonResponse
    {
        $provinces = $this->addressProvider->getProvinces($regionCode);
        return response()->json($provinces);
    }

    public function cities(string $provinceCode): JsonResponse
    {
        $cities = $this->addressProvider->getCities($provinceCode);
        return response()->json($cities);
    }

    public function barangays(string $cityCode): JsonResponse
    {
        $barangays = $this->addressProvider->getBarangays($cityCode);
        return response()->json($barangays);
    }
}
