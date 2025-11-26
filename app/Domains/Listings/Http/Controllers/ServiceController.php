<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Common\Services\AddressService;
use App\Domains\Listings\Services\ServiceService;
use App\Domains\Listings\Services\WorkflowTemplateService;
use App\Domains\Orders\Services\OrderService;
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
        private AddressService $addressService,
        private OrderService $orderService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $services = $this->serviceService->getServicesForCreator(Auth::id(), $request->all());
        $categories = $this->categoryService->listAllCategories();
        return view('creator.services.index', compact('services', 'categories'));
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
        $this->authorize('view', $service);
        return view('listings.services.show', compact('service'));
    }

    /**
     * Handle the checkout process for a service.
     */
    public function checkout(Request $request, Service $service)
    {
        $this->authorize('purchase', $service); // Assuming a 'purchase' policy exists

        // Create the order
        $order = $this->orderService->createOrderFromService($service, Auth::user());

        // Determine payment method from the request (modal form) or default to the service setting
        $paymentMethod = $request->input('payment_method') ?? ($service->payment_method?->value ?? null);

        if ($service->pay_first) {
            if ($paymentMethod === 'cash') {
                return redirect()->route('payments.checkout', ['order' => $order, 'payment_method' => 'cash']);
            }
            return redirect()->route('payments.checkout', ['order' => $order, 'payment_method' => 'online']);
        }

        return redirect()->route('orders.show', $order)->with('info', 'Order created! Please proceed with payment to start work.');
    }


    public function manage(Service $service) {
        $this->authorize('update', $service);
        // Placeholder data as per the guide
            $analytics = [
                'total_revenue' => 0,
                'today_clicks' => 0,
                'wishlist_count' => 0,
            ];
            $orders = [];
            $reviews = [];
        return view('creator.services.show', compact('service', 'analytics', 'orders', 'reviews'));
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
