<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Common\Services\AddressService;
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
        private ServiceService $serviceService,
        private CategoryService $categoryService,
        private WorkflowTemplateService $workflowTemplateService,
        private AddressService $addressService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $services = $this->serviceService->getPaginatedServices($request->all());
        return view('creator.services.index', compact('services'));
    }   

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->categoryService->listAllCategories();
        $workflowTemplates = $this->workflowTemplateService->getWorkflowTemplatesByCreator(auth()->id());
        $addresses = $this->addressService->getAddressesForUser();
        
        return view('creator.services.create', compact('categories', 'workflowTemplates', 'addresses'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        // The getService method eager loads all necessary relationships
        $service = $this->serviceService->getService($service->id);
        return view('listings.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $this->authorize('update', $service);

        $categories = $this->categoryService->listAllCategories();
        $workflowTemplates = $this->workflowTemplateService->getWorkflowTemplatesByCreator(auth()->id());
        $addresses = $this->addressService->getAddressesForUser();

        return view('creator.services.edit', compact('service', 'categories', 'workflowTemplates', 'addresses'));
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
