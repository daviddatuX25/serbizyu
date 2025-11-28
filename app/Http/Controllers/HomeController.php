<?php

namespace App\Http\Controllers;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Listings\Services\OpenOfferService;
use App\Domains\Listings\Services\ServiceService;
use App\Domains\Listings\Services\WorkflowBookmarkService;
use App\Domains\Listings\Services\WorkflowTemplateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly ServiceService $serviceService,
        private readonly OpenOfferService $openOfferService,
        private readonly CategoryService $categoryService,
        private readonly WorkflowTemplateService $workflowTemplateService,
        private readonly WorkflowBookmarkService $bookmarkService,
    ) {}

    public function index(): View
    {
        // Get 10 latest services and 10 latest offers
        $services = $this->serviceService->getLatestServices(10)
            ->map(fn ($service) => $this->attachThumbnail($service));

        $offers = $this->openOfferService->getLatestOffers(10)
            ->map(fn ($offer) => $this->attachThumbnail($offer));

        // Merge and sort by created_at
        $browseItems = $services->concat($offers)
            ->sortByDesc(fn ($item) => $item->created_at)
            ->take(10)
            ->values();

        // Get featured workflows
        $workflows = $this->workflowTemplateService->getFeaturedWorkflows(3);

        return view('home', [
            'browseItems' => $browseItems,
            'workflows' => $workflows,
        ]);
    }

    private function attachThumbnail($model)
    {
        $model->thumbnail = $model->getMedia('thumbnail')->first();

        return $model;
    }

    public function createServiceFromWorkflow(WorkflowTemplate $workflow)
    {
        // Check if user is authenticated
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // Bookmark the workflow for the user
        $this->bookmarkService->bookmarkWorkflow(Auth::user(), $workflow);

        // Redirect to create service page with workflow_id parameter
        return redirect()->route('creator.services.create', ['workflow_id' => $workflow->id]);
    }

    public function createOfferFromWorkflow(WorkflowTemplate $workflow)
    {
        // Check if user is authenticated
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // Bookmark the workflow for the user
        $this->bookmarkService->bookmarkWorkflow(Auth::user(), $workflow);

        // Redirect to create offer page with workflow_id parameter
        return redirect()->route('creator.openoffers.create', ['workflow_id' => $workflow->id]);
    }
}
