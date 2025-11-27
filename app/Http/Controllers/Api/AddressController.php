<?php

namespace App\Http\Controllers\Api;

use App\Domains\Common\Interfaces\AddressProviderInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    public function __construct(private AddressProviderInterface $addressProvider) {}

    public function hierarchy(): JsonResponse
    {
        return response()->json([
            'regions' => $this->addressProvider->getRegions(),
        ]);
    }

    public function provinces(string $regionCode): JsonResponse
    {
        return response()->json($this->addressProvider->getProvinces($regionCode));
    }

    public function cities(string $provinceCode): JsonResponse
    {
        return response()->json($this->addressProvider->getCities($provinceCode));
    }

    public function barangays(string $cityCode): JsonResponse
    {
        return response()->json($this->addressProvider->getBarangays($cityCode));
    }
}
