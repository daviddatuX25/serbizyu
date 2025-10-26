<?php
namespace App\Domains\Listings\Services;
use App\Domains\Users\Services\UserService;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Listings\Services\WorkflowTemplateService;
use App\Domains\Common\Services\AddressService;

use App\Domains\Listings\Models\Service;

use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\AuthorizationException;
use App\Exceptions\BusinessRuleException;
use Illuminate\Database\Eloquent\Collection;

class ServiceService
{


    public function __construct(
        private UserService $userService,
        private CategoryService $categoryService,
        private WorkflowTemplateService $workflowTemplateService
    )
    {}
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
            $address = app(AddressService::class)->getAddress($data['address_id']);
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

        return Service::create($data);
    }

    public function getService($id): Service
    {
        // get a servce
        $service = Service::with('category', 'creator', 'workflow')->find($id);
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
        $services = Service::with('category', 'creator', 'workflow')->get();

        if ($services->isEmpty()) {
            throw new ResourceNotFoundException('No services found.');
        }

        if ($services->every->trashed()) {
            throw new ResourceNotFoundException('Services have all been deleted.');
        }
        
        return $services;
    }
}