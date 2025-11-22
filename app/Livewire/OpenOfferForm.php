<?php

namespace App\Livewire;

use App\Domains\Listings\Models\OpenOffer;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Plank\Mediable\MediaUploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class OpenOfferForm extends FormWithMedia
{
    use WithFileUploads;

    public ?OpenOffer $offer = null;

    public string $title = '';
    public ?string $description = null;
    public ?float $budget = null;
    public ?int $category_id = null;
    public ?int $workflow_template_id = null;
    public bool $pay_first = false;
    public ?int $address_id = null;
    public ?string $deadline = null;

    // For lists (categories, workflows) loaded for select inputs
    public array $categories = [];
    public array $workflowTemplates = [];
    public Collection $addresses;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'required|numeric|min:1',
            'category_id' => 'required|integer|exists:categories,id',
            'workflow_template_id' => 'nullable|integer|exists:workflow_templates,id',
            'pay_first' => 'nullable|boolean',
            'address_id' => 'nullable|integer|exists:addresses,id',
            'deadline' => 'nullable|date|after_or_equal:today',
            'newFiles' => 'nullable|array',
            'newFiles.*' => 'file|max:5120',
            'imagesToRemove' => 'nullable|array',
            'imagesToRemove.*' => 'integer',
        ];
    }

    protected $listeners = [
        // you can listen to custom events if needed
    ];

    public function mount(?OpenOffer $offer = null, ?Collection $addresses = null)
    {
        $this->offer = $offer;
        $this->addresses = $addresses ? $addresses->map(function ($address) {
            $address->is_primary = $address->pivot->is_primary ?? false;
            return $address;
        }) : new Collection();

        // Load selects (you can replace with services or inject them)
        $this->categories = app(\App\Domains\Listings\Services\CategoryService::class)->listAllCategories()->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray();

        $this->workflowTemplates = app(\App\Domains\Listings\Services\WorkflowTemplateService::class)
            ->getWorkflowTemplatesByCreator(auth()->id())
            ->map(fn($w) => ['id' => $w->id, 'name' => $w->name])->toArray();
            
        if ($this->offer && $this->offer->exists) {
            $this->title = $this->offer->title;
            $this->description = $this->offer->description;
            $this->budget = $this->offer->budget;
            $this->category_id = $this->offer->category_id;
            $this->workflow_template_id = $this->offer->workflow_template_id;
            $this->pay_first = (bool)$this->offer->pay_first;
            $this->address_id = $this->offer->address_id;
            $this->deadline = $this->offer->deadline ? $this->offer->deadline->format('Y-m-d') : null;

            $this->loadExistingMedia($this->offer);
        }
    }

    public function save()
    {
        $this->validate();

        $openOfferService = app(\App\Domains\Listings\Services\OpenOfferService::class);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'budget' => $this->budget,
            'category_id' => $this->category_id,
            'workflow_template_id' => $this->workflow_template_id,
            'pay_first' => $this->pay_first,
            'address_id' => $this->address_id,
            'deadline' => $this->deadline,
            'images_to_remove' => $this->imagesToRemove,
        ];

        $uploadedFiles = $this->getUploadedFiles();

        try {
            if ($this->offer && $this->offer->exists) {
                $updated = $openOfferService->updateOpenOffer($this->offer, $data, $uploadedFiles);
                $this->dispatch('openOfferSaved', $updated->id);
                session()->flash('success', 'Open offer updated successfully!');
                return redirect()->route('creator.openoffers.index');
            } else {
                $created = $openOfferService->createOpenOffer(auth()->user(), $data, $uploadedFiles);
                $this->dispatch('openOfferSaved', $created->id);
                session()->flash('success', 'Open offer created successfully!');
                return redirect()->route('creator.openoffers.index');
            }
        }
        catch (\Throwable $e) {
            \Log::error('OpenOfferForm save failed: ' . $e->getMessage(), ['exception' => $e]);
            $this->addError('save', 'Failed to save open offer: ' . $e->getMessage());
        }
    }

    public function close(OpenOffer $openoffer){
        $this->authorize('close', $openoffer);

        $this->openOfferService->closeOpenOffer($openoffer);

        return back()->with('success', 'Open Offer closed successfully!');
    }

    public function render()
    {
        return view('livewire.open-offer-form');
    }
}


