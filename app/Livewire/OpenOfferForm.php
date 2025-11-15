<?php

namespace App\Livewire;

use App\Domains\Listings\Models\OpenOffer;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Plank\Mediable\MediaUploader;
use Illuminate\Http\UploadedFile;

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

    /** @var array|UploadedFile[] */
    public array $new_images = [];

    /** @var int[] IDs of media to remove (when editing) */
    public array $images_to_remove = [];

    // For lists (categories, workflows) loaded for select inputs
    public array $categories = [];
    public array $workflowTemplates = [];

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'required|numeric|min:1',
            'category_id' => 'required|integer|exists:categories,id',
            'workflow_template_id' => 'nullable|integer|exists:workflow_templates,id',
            'pay_first' => 'nullable|boolean',
            'address_id' => 'nullable|integer',
            'new_images' => 'nullable|array',
            'new_images.*' => 'file|max:5120', // 5MB per file guard; adjust as needed
            'images_to_remove' => 'nullable|array',
            'images_to_remove.*' => 'integer',
        ];
    }

    protected $listeners = [
        // you can listen to custom events if needed
    ];

    public function mount(?OpenOffer $offer = null)
    {
        $this->offer = $offer;

        // Load selects (you can replace with services or inject them)
        $this->categories = app(\App\Domains\Listings\Services\CategoryService::class)->listAllCategories()->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray();

        $this->workflowTemplates = app(\App\Domains\Listings\Services\WorkflowTemplateService::class)
            ->getWorkflowTemplatesByCreator(auth()->id())
            ->map(fn($w) => ['id' => $w->id, 'name' => $w->name])->toArray();

        if ($this->offer) {
            $this->title = $this->offer->title;
            $this->description = $this->offer->description;
            $this->budget = $this->offer->budget;
            $this->category_id = $this->offer->category_id;
            $this->workflow_template_id = $this->offer->workflow_template_id;
            $this->pay_first = (bool)$this->offer->pay_first;
            $this->address_id = $this->offer->address_id;
            // existing media loaded for display in blade via $offer->getMedia('gallery') or ->loadMedia('gallery')
        }
    }

    public function updatedNewImages()
    {
        // Optional: limit number of files, validate individually, etc.
        $this->validateOnly('new_images');
    }

    public function save()
    {
        $this->validate();

        $openOfferService = app(\App\Domains\Listings\Services\OpenOfferService::class);
        $uploader = app(MediaUploader::class);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'budget' => $this->budget,
            'category_id' => $this->category_id,
            'workflow_template_id' => $this->workflow_template_id,
            'pay_first' => $this->pay_first,
            'address_id' => $this->address_id,
            'creator_id' => auth()->id(),
            'images_to_remove' => $this->images_to_remove,
        ];

        // Prepare new_images as local disk paths (service expects strings to locate files)
        $tempPaths = [];
        if (!empty($this->new_images)) {
            foreach ($this->new_images as $file) {
                if ($file instanceof UploadedFile) {
                    // store to local disk - folder "livewire-temp/offers"
                    $path = $file->store('livewire-temp/offers', 'local');
                    $tempPaths[] = $path;
                }
            }
            $data['new_images'] = $tempPaths;
        }

        try {
            if ($this->offer) {
                $updated = $openOfferService->updateOpenOffer($this->offer, $data, $uploader);
                $this->emit('openOfferSaved', $updated->id);
                session()->flash('success', 'Open offer updated successfully.');
                return redirect()->route('creator.offers.index');
            } else {
                $created = $openOfferService->createOpenOffer($data, $uploader);
                $this->emit('openOfferSaved', $created->id);
                session()->flash('success', 'Open offer created successfully.');
                return redirect()->route('creator.offers.index');
            }
        } catch (\Throwable $e) {
            // Clean up temp files on failure
            foreach ($tempPaths as $p) {
                Storage::disk('local')->delete($p);
            }

            // Log and show generic error
            \Log::error('OpenOfferForm save failed: ' . $e->getMessage(), ['exception' => $e]);
            $this->addError('save', 'Failed to save open offer: ' . $e->getMessage());
        }
    }

    public function removeExistingMedia(int $mediaId)
    {
        // Mark for removal - frontend can call this to add to images_to_remove
        if (!in_array($mediaId, $this->images_to_remove)) {
            $this->images_to_remove[] = $mediaId;
        }
    }

    public function render()
    {
        return view('livewire.open-offer-form');
    }
}
