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

use Illuminate\Support\Facades\Log;

class ServiceService
{

    public function __construct(
        private UserService $userService,
        private CategoryService $categoryService,
        private WorkflowTemplateService $workflowTemplateService,
        private AddressService $addressService
        ){}
        
    public function createService($data, \Plank\Mediable\MediaUploader $uploader): Service
    {
        Log::info('Creating service with data: ', $data);
        try {
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
            if (isset($data['images'])) {
                foreach ($data['images'] as $image) {
                    $media = $uploader->fromSource($image)
                        ->toDestination('public', 'services')
                        ->upload();
                    $service->attachMedia($media, 'gallery');
                }
            }

            return $service->loadMedia('gallery');
        } catch (\Exception $e) {
            Log::error('Error creating service: ' . $e->getMessage());
            throw $e;
        }
    }


    public function getService($id): Service
    {
        // get a servce
        // include images loaded  
        $service = Service::withMedia()->find($id);
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
        $services = Service::withMedia()->get();

        if ($services->isEmpty()) {
            throw new ResourceNotFoundException('No services found.');
        }

        if ($services->every->trashed()) {
            throw new ResourceNotFoundException('Services have all been deleted.');
        }
        
        return $services;
    }

    public function getFilteredServices(array $filters = [])
    {
        $query = Service::with(['creator.media', 'address', 'media']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            );
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        $sortable = ['created_at', 'price', 'title'];
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_direction'] ?? 'desc';

        if (in_array($sortBy, $sortable)) {
            $query->orderBy($sortBy, $sortDir);
        }

        return $query->get(); // return collection, pagination handled in controller
    }


    public function updateService(Service $service, array $data, \Plank\Mediable\MediaUploader $uploader): Service
    {
        // Step 1: Update the main service record
        $service->update(collect($data)->except(['images', 'images_to_remove'])->toArray());

        // Step 2: Sync images
        if (isset($data['images_to_remove'])) {
            $service->detachMedia($data['images_to_remove']);
        }

        if (isset($data['images'])) {
            foreach ($data['images'] as $image) {
                $media = $uploader->fromSource($image)
                    ->toDestination('public', 'services')
                    ->upload();
                $service->attachMedia($media, 'gallery');
            }
        }

        return $service->loadMedia('gallery');
    }

    public function deleteService(Service $service): bool
    {
        return $service->delete();
    }
}