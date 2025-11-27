<?php

namespace App\Domains\Common\Services;

use App\Domains\Common\Interfaces\AddressProviderInterface;
use Illuminate\Support\Facades\Http;

class PhilippineAddressProvider implements AddressProviderInterface
{
    protected string $baseUrl = 'https://psgc.gitlab.io/api';

    public function getRegions(): array
    {
        return Http::withoutVerifying()->get("{$this->baseUrl}/regions/")->json() ?? [];
    }

    public function getProvinces(string $regionCode): array
    {
        // Get all provinces and filter by regionCode
        try {
            $allProvinces = Http::withoutVerifying()->get("{$this->baseUrl}/provinces/")->json() ?? [];

            // Filter provinces that belong to this region
            $filteredProvinces = array_filter($allProvinces, function ($province) use ($regionCode) {
                return $province['regionCode'] === $regionCode;
            });

            // Special case: NCR (130000000) doesn't have provinces, it has cities directly
            // So if no provinces found and this is NCR, return cities as "provinces" for the UI
            if (empty($filteredProvinces) && $regionCode === '130000000') {
                $allCities = Http::withoutVerifying()->get("{$this->baseUrl}/cities-municipalities/")->json() ?? [];
                $ncrCities = array_filter($allCities, function ($city) {
                    return $city['regionCode'] === '130000000' && $city['provinceCode'] === false;
                });

                return array_values($ncrCities);
            }

            return array_values($filteredProvinces);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getCities(string $provinceCode): array
    {
        // Get all cities and filter by provinceCode
        try {
            $allCities = Http::withoutVerifying()->get("{$this->baseUrl}/cities-municipalities/")->json() ?? [];

            // Filter cities that belong to this province
            return array_values(array_filter($allCities, function ($city) use ($provinceCode) {
                return $city['provinceCode'] === $provinceCode;
            }));
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getBarangays(string $cityCode): array
    {
        // Get all barangays and filter by cityCode
        try {
            $allBarangays = Http::withoutVerifying()->get("{$this->baseUrl}/barangays/")->json() ?? [];

            // Filter barangays that belong to this city
            return array_values(array_filter($allBarangays, function ($barangay) use ($cityCode) {
                return $barangay['cityCode'] === $cityCode;
            }));
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Normalize region code from 9 digits to 6 digits for database matching
     * The external PSGC API uses 9-digit codes, but our database stores 6-digit codes for regions
     * This method is used internally when filtering database records
     */
    public function normalizeRegionCode(string $code): string
    {
        // If the code is 9 digits, take the first 6
        if (strlen($code) === 9) {
            return substr($code, 0, 6);
        }

        return $code;
    }
}
