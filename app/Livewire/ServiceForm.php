<?php

namespace App\Livewire;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Services\ServiceService;
use Barryvdh\Debugbar\Facades\Debugbar;

class ServiceForm extends FormWithMedia
{
    public ?Service $service = null;

    // Livewire properties
    public ?string $title = null;
    public ?string $description = null;
    public float|string|null $price = null;
    public int|string|null $category_id = null;
    public int|string|null $workflow_template_id = null;
    public ?bool $pay_first = null;
    public ?int $address_id = null;

    public $categories;
    public $workflowTemplates;
    public $addresses;

    protected $rules = [
        'title' => 'required|string|min:3|max:255',
        'description' => 'nullable|string|min:10',
        'price' => 'required|numeric|min:0.01',
        'category_id' => 'required|exists:categories,id',
        'workflow_template_id' => 'required|integer|exists:workflow_templates,id',
        'address_id' => 'required|exists:addresses,id',
        'pay_first' => 'boolean',
        'newFiles.*' => 'nullable|image|max:2048', // Uses MediaConfig.UPLOAD_LIMITS['images']
    ];

    public function updatedWorkflowTemplateId($value)
    {
        Debugbar::info('updatedWorkflowTemplateId called', [
            'value' => $value,
            'type' => gettype($value)
        ]);
        
        if (is_string($value) && is_numeric($value)) {
            $this->workflow_template_id = (int) $value;
            Debugbar::info('Casted to integer', ['new_value' => $this->workflow_template_id]);
        }
    }

    public function mount(
        ?Service $service = null,
        $categories = [],
        $workflowTemplates = [],
        $addresses = []
    ) {
        $this->service = $service;
        $this->categories = collect($categories);
        $this->workflowTemplates = collect($workflowTemplates);
        $this->addresses = collect($addresses)->map(function ($address) {
            $address->is_primary = $address->pivot->is_primary ?? false;
            return $address;
        });

        Debugbar::info('Addresses', ['addresses' => $this->addresses]);

        // Default values
        $this->title = '';
        $this->description = '';
        $this->price = '';
        $this->category_id = '';
        $this->workflow_template_id = null;
        $this->pay_first = false;
        $this->address_id = $this->addresses->firstWhere('pivot.is_primary', true)->id ?? null;

        if ($service && $service->exists) {
            // Edit mode – populate with existing data
            $this->title = $service->title;
            $this->description = $service->description;
            $this->price = $service->price;
            $this->category_id = $service->category_id;
            $this->workflow_template_id = $service->workflow_template_id;
            $this->pay_first = $service->pay_first ?? false;
            $this->address_id = $service->address_id;

            // IMPORTANT: Load existing media for display
            // This captures current state without destructive behavior
            $this->loadExistingMedia($this->service);
        } else {
            // Create mode – set default primary address if exists
            $primary = $this->addresses->firstWhere('pivot.is_primary', true);
            $this->address_id = $primary?->id ?? null;
        }
        
        Debugbar::info('Mount completed', [
            'service_exists' => $service->exists,
            'existing_media_count' => count($this->existingImages),
        ]);
    }

    /**
     * Save service with non-destructive media handling
     * 
     * Workflow:
     * 1. Validate all form data
     * 2. Prepare service data (excluding media info)
     * 3. Prepare media data (uploads + removals)
     * 4. Call service layer
     * 5. Reset and redirect
     */
    public function save(ServiceService $serviceService)
    {
        Debugbar::info('=== SAVE METHOD CALLED ===');

        try {
            // Step 1: Validate
            $this->validate();
            Debugbar::info('Validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Debugbar::error('Validation failed', $e->errors());
            throw $e;
        }

        // Step 2: Prepare service data (media handled separately)
        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'workflow_template_id' => $this->workflow_template_id,
            'address_id' => $this->address_id,
            'pay_first' => $this->pay_first,
            'creator_id' => auth()->id(),
        ];

        // Step 3: Get uploaded files and removal list
        $uploadedFiles = $this->getUploadedFiles();
        $imagesToRemove = $this->imagesToRemove;

        // Step 4: Include media instructions in data array
        $data['images_to_remove'] = $imagesToRemove;

        Debugbar::info('Prepared save data', [
            'files_to_add' => count($uploadedFiles),
            'files_to_remove' => count($imagesToRemove),
            'total_existing' => count($this->existingImages),
        ]);

        try {
            if ($this->service && $this->service->exists) {
                // UPDATE: Pass both uploads and removals
                $serviceService->updateService(
                    $this->service,
                    $data,
                    $uploadedFiles
                );
                
                Debugbar::info('Service updated', ['id' => $this->service->id]);
            } else {
                // CREATE: Pass uploads (no removals on create)
                $serviceService->createService($data, $uploadedFiles);
                Debugbar::info('Service created');
            }

            // Step 5: Success
            $this->resetMediaForm();
            session()->flash('success', 'Service saved successfully!');
            return redirect()->route('creator.services.index');

        } catch (\Throwable $e) {
            Debugbar::error('Service save failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->addError('save', 'Failed to save service: ' . $e->getMessage());
        }
    }

    /**
     * Restore removed image (user changed their mind)
     */
    public function restoreImage(int $mediaId)
    {
        $this->restoreExistingImage($mediaId, $this->service);
    }

    public function render()
    {
        return view('livewire.service-form');
    }
}