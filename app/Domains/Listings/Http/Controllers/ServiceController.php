<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Listings\Services\ServiceService;
use App\Domains\Listings\Services\WorkflowTemplateService;
use App\Http\Controllers\Controller;
use App\Domains\Listings\Http\Requests\StoreServiceRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly ServiceService $serviceService,
        private readonly CategoryService $categoryService,
        private readonly WorkflowTemplateService $workflowTemplateService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = $this->serviceService->getPaginatedServices();
        return view('creator.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->categoryService->listAllCategories();
        $workflowTemplates = $this->workflowTemplateService->getWorkflowTemplatesByCreator(Auth::id());

        return view('creator.services.create', compact('categories', 'workflowTemplates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['creator_id'] = Auth::id();
        $validatedData['is_active'] = $request->has('is_active');

        try {
            $this->serviceService->createService($validatedData);
            return redirect()->route('creator.services.index')->with('success', 'Service created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create service: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $this->authorize('update', $service);

        $categories = $this->categoryService->listAllCategories();
        $workflowTemplates = $this->workflowTemplateService->getWorkflowTemplatesByCreator(Auth::id());

        return view('creator.services.edit', compact('service', 'categories', 'workflowTemplates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreServiceRequest $request, Service $service)
    {
        $this->authorize('update', $service);

        $validatedData = $request->validated();
        $validatedData['is_active'] = $request->has('is_active');

        try {
            $this->serviceService->updateService($service, $validatedData);
            return redirect()->route('creator.services.index')->with('success', 'Service updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update service: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);

        try {
            $this->serviceService->deleteService($service);
            return redirect()->route('creator.services.index')->with('success', 'Service deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete service: ' . $e->getMessage());
        }
    }
}
