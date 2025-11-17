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
    public ?string $deadline = null;

    /** @var array|UploadedFile[] */
    public array $newFiles = []; // Renamed from new_images

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
            'deadline' => 'nullable|date|after_or_equal:today',
            'newFiles' => 'nullable|array', // Updated rule
            'newFiles.*' => 'file|max:5120', // Updated rule
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
            $this->deadline = $this->offer->deadline ? $this->offer->deadline->format('Y-m-d') : null;
        }
    }

    public function updatedNewFiles() // Renamed method
    {
        $this->validateOnly('newFiles'); // Updated validation
    }

    public function getExistingImagesProperty()
    {
        if (!$this->offer) {
            return collect();
        }

        return $this->offer->getMedia('images')->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => route('media.serve', ['payload' => encrypt(json_encode(['media_id' => $media->id]))]),
                'is_removed' => in_array($media->id, $this->images_to_remove),
            ];
        });
    }

    public function removeExistingMedia(int $mediaId)
    {
        if (in_array($mediaId, $this->images_to_remove)) {
            $this->images_to_remove = array_diff($this->images_to_remove, [$mediaId]);
        } else {
            $this->images_to_remove[] = $mediaId;
        }
    }

    public function removeNewFile(int $index)
    {
        array_splice($this->newFiles, $index, 1);
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
            'images_to_remove' => $this->images_to_remove,
        ];

        $tempPaths = [];
        if (!empty($this->newFiles)) { // Updated property
            foreach ($this->newFiles as $file) { // Updated property
                if ($file instanceof UploadedFile) {
                    $path = $file->store('livewire-temp/offers', 'local');
                    $tempPaths[] = $path;
                }
            }
        }

        try {
            if ($this->offer) {
                $updated = $openOfferService->updateOpenOffer($this->offer, $data, $tempPaths);
                $this->emit('openOfferSaved', $updated->id);
                session()->flash('success', 'Open offer updated successfully!');
                return redirect()->route('creator.offers.index');
            } else {
                $created = $openOfferService->createOpenOffer(auth()->user(), $data, $tempPaths);
                $this->emit('openOfferSaved', $created->id);
                session()->flash('success', 'Open offer created successfully!');
                return redirect()->route('creator.offers.index');
            }
        } catch (\Throwable $e) {
            foreach ($tempPaths as $p) {
                Storage::disk('local')->delete($p);
            }

            \Log::error('OpenOfferForm save failed: ' . $e->getMessage(), ['exception' => $e]);
            $this->addError('save', 'Failed to save open offer: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.open-offer-form');
    }
}
