<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Services\ServiceService;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Listings\Services\WorkflowTemplateService;
use App\Domains\Listings\Http\Requests\StoreServiceRequest;
use Plank\Mediable\MediaUploader;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(
        private ServiceService $serviceService,
        private CategoryService $categoryService,
        private WorkflowTemplateService $workflowTemplateService
    ) {}

    public function index()
    {
        $services = $this->serviceService->getServicesForCreator(
            auth()->id(),
            request()->only(['search', 'category', 'sort_by', 'sort_direction'])
        );

        return view('creator.services.index', compact('services'));
    }

    public function create()
    {
        $categories = $this->categoryService->listAllCategories();
        $workflowTemplates = $this->workflowTemplateService->getWorkflowTemplatesByCreator(auth()->id());

        return view('creator.services.create', compact('categories', 'workflowTemplates'));
    }

    public function store(StoreServiceRequest $request, MediaUploader $uploader)
    {
        $data = $request->validated();
        $data['creator_id'] = auth()->id();
        $data['address_id'] = $request->input('address_id', null);

        $service = $this->serviceService->createService($data, $uploader);

        return redirect()->route('creator.services.index')
            ->with('success', 'Service created successfully!');
    }

    public function show(Service $service)
    {
        $this->authorize('view', $service);
        
        $service = $this->serviceService->getService($service->id);
        
        return view('creator.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $this->authorize('update', $service);

        $service = $this->serviceService->getService($service->id)->loadMedia('gallery');
        $categories = $this->categoryService->listAllCategories();
        $workflowTemplates = $this->workflowTemplateService->getWorkflowTemplatesByCreator(auth()->id());

        return view('creator.services.edit', compact('service', 'categories', 'workflowTemplates'));
    }

    public function update(StoreServiceRequest $request, Service $service, MediaUploader $uploader)
    {
        $this->authorize('update', $service);

        $data = $request->validated();
        
        $this->serviceService->updateService($service, $data, $uploader);

        return redirect()->route('creator.services.index')
            ->with('success', 'Service updated successfully!');
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);

        $this->serviceService->deleteService($service);

        return redirect()->route('creator.services.index')
            ->with('success', 'Service deleted successfully!');
    }
}