<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\Service;    
use App\Exceptions\BusinessRuleException;
use App\Exceptions\AuthorizationException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use App\Domains\Users\Services\UserService;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Listings\Services\WorkflowTemplateService;
use App\Domains\Common\Services\AddressService;

use App\Domains\Common\Services\ImageService;

class ServiceService
{

    public function __construct(
        private UserService $userService,
        private CategoryService $categoryService,
        private WorkflowTemplateService $workflowTemplateService,
        private AddressService $addressService,
        private ImageService $imageService
        ){}
        
    public function createService($data): Service
    {
        if ($data['price'] <= 0) {
            throw new BusinessRuleException('Price must be greater than 0.');
        }

        $category = $this->categoryService->getCategory($data['category_id']);

        $workflow = $this->workflowTemplateService->getWorkflowTemplate($data['workflow_template_id']);
        if (!$workflow->is_public && $workflow->creator() != $data['creator_id']) 
        {
            throw new AuthorizationException('Workflow does not belong to creator.');
        }

        $creator = $this->userService->getUser($data['creator_id']);
        if ($creator == null) {
            throw new ResourceNotFoundException('Creator does not exist.');
        }
        
        if ($creator->trashed()) {
            throw new ResourceNotFoundException('Creator has been deleted.');
        }

        // address
        if ($data['address_id']) {
            $address = $this->addressService->getAddress($data['address_id']);
            if ($address == null) {
                throw new ResourceNotFoundException('Address does not exist.');
            }
        } else {
            $address = $creator->addresses()->where('is_primary', true)->first();
            if ($address == null) {
                throw new ResourceNotFoundException('Creator does not have a primary address.');
            }
            $data['address_id'] = $address->id;
        }

        // Step 1: Create the service record first
        $service = Service::create(collect($data)->except(['images', 'images_to_remove'])->toArray());

        // Step 2: Sync images
        $this->imageService->sync(
            $service,
            'gallery',
            $data['images_to_remove'] ?? [],
            $data['images'] ?? []
        );

        return $service->load('images');
    }


    public function getService($id): Service
    {
        // get a servce
        // include images loaded  
        $service = Service::with('images', 'category', 'creator', 'address', 'workflowTemplate.workTemplates')->find($id);
        if ($service == null) {
            throw new ResourceNotFoundException('Service does not exist.');
        }
        if ($service->trashed()) {
            throw new ResourceNotFoundException('Service has been deleted.');
        }
        return $service;
    }

    public function getAllServices(): Collection
    {
        $services = Service::with('category', 'creator', 'address', 'workflowTemplate.workTemplates', 'thumbnail')->get();

        if ($services->isEmpty()) {
            throw new ResourceNotFoundException('No services found.');
        }

        if ($services->every->trashed()) {
            throw new ResourceNotFoundException('Services have all been deleted.');
        }
        
        return $services;
    }

    public function getPaginatedServices(array $filters = [])
    {
        $query = Service::with('category', 'creator', 'address', 'thumbnail');

        // Apply search filter
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Apply category filter
        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        // Validate sort_by to prevent arbitrary column sorting
        $sortableColumns = ['created_at', 'price', 'title'];
        if (in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage)->withQueryString();
    }

    public function updateService(Service $service, array $data): Service
    {
        // Step 1: Update the main service record
        $service->update(collect($data)->except(['images', 'images_to_remove'])->toArray());

        // Step 2: Sync images
        $this->imageService->sync(
            $service,
            'gallery',
            $data['images_to_remove'] ?? [],
            $data['images'] ?? []
        );

        return $service->load('images');
    }

    public function deleteService(Service $service): bool
    {
        return $service->delete();
    }
}