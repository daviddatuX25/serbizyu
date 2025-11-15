<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Services\OpenOfferService;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Listings\Services\WorkflowTemplateService;
use Illuminate\Http\Request;
use Plank\Mediable\MediaUploader;

class OpenOfferController extends Controller
{
    public function __construct(
        private OpenOfferService $openOfferService,
        private CategoryService $categoryService,
        private WorkflowTemplateService $workflowTemplateService,
    ) {}

    /**
     * List all open offers by creator
     */
    public function index(Request $request)
    {
        $offers = OpenOffer::where('creator_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('creator.offers.index', compact('offers'));
    }

    /**
     * Show the creation form
     */
    public function create()
    {
        $categories = $this->categoryService->listAllCategories();
        $workflowTemplates = $this->workflowTemplateService->getWorkflowTemplatesByCreator(auth()->id());

        return view('creator.offers.create', compact('categories', 'workflowTemplates'));
    }

    /**
     * Store new Open Offer
     */
    public function store(Request $request, MediaUploader $uploader)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'required|numeric|min:1',
            'category_id' => 'required|integer|exists:categories,id',
            'workflow_template_id' => 'nullable|integer|exists:workflow_templates,id',
            'pay_first' => 'nullable|boolean',
            'address_id' => 'nullable|integer',
            'new_images' => 'nullable|array',
            'new_images.*' => 'string',
        ]);

        $data['creator_id'] = auth()->id();

        $offer = $this->openOfferService->createOpenOffer($data, $uploader);

        return redirect()
            ->route('creator.offers.index')
            ->with('success', 'Open offer created successfully!');
    }

    /**
     * Show single offer
     */
    public function show(OpenOffer $offer)
    {
        $this->authorize('view', $offer);

        $offer->loadMedia('gallery');

        return view('creator.offers.show', compact('offer'));
    }

    /**
     * Edit form
     */
    public function edit(OpenOffer $offer)
    {
        $this->authorize('update', $offer);

        $offer->loadMedia('gallery');
        $categories = $this->categoryService->listAllCategories();
        $workflowTemplates = $this->workflowTemplateService->getWorkflowTemplatesByCreator(auth()->id());

        return view('creator.offers.edit', compact('offer', 'categories', 'workflowTemplates'));
    }

    /**
     * Update existing offer
     */
    public function update(Request $request, OpenOffer $offer, MediaUploader $uploader)
    {
        $this->authorize('update', $offer);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'required|numeric|min:1',
            'category_id' => 'required|integer|exists:categories,id',
            'workflow_template_id' => 'nullable|integer|exists:workflow_templates,id',
            'pay_first' => 'nullable|boolean',
            'address_id' => 'nullable|integer',
            'new_images' => 'nullable|array',
            'new_images.*' => 'string',
            'images_to_remove' => 'nullable|array',
            'images_to_remove.*' => 'integer',
        ]);

        $this->openOfferService->updateOpenOffer($offer, $data, $uploader);

        return redirect()
            ->route('creator.offers.index')
            ->with('success', 'Open offer updated successfully!');
    }

    /**
     * Delete
     */
    public function destroy(OpenOffer $offer)
    {
        $this->authorize('delete', $offer);

        $offer->delete();

        return redirect()
            ->route('creator.offers.index')
            ->with('success', 'Open offer deleted.');
    }
}
