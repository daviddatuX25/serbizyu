<?php

namespace App\Domains\Listings\Services\Contracts;

use App\DTO\ServiceResponseDto;
interface ServiceInterface
{
    public function createService($data): ServiceResponseDto;

    public function updateService($id, array $data): ServiceResponseDto;
    
    public function deleteService($id): ServiceResponseDto;

    public function getService($id): ServiceResponseDto;

    public function getServicesBySlug($slug): ServiceResponseDto;

    public function getAllServices(): ServiceResponseDto;
}
