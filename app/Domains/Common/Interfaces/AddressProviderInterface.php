<?php

namespace App\Domains\Common\Interfaces;

interface AddressProviderInterface
{
    public function getRegions(): array;

    public function getProvinces(string $regionCode): array;

    public function getCities(string $provinceCode): array;

    public function getBarangays(string $cityCode): array;
}
