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
        return Http::withoutVerifying()->get("{$this->baseUrl}/regions/{$regionCode}/provinces/")->json() ?? [];
    }

    public function getCities(string $provinceCode): array
    {
        return Http::withoutVerifying()->get("{$this->baseUrl}/provinces/{$provinceCode}/cities-municipalities/")->json() ?? [];
    }

    public function getBarangays(string $cityCode): array
    {
        return Http::withoutVerifying()->get("{$this->baseUrl}/cities-municipalities/{$cityCode}/barangays/")->json() ?? [];
    }
}
