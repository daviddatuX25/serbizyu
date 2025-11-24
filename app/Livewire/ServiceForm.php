<?php

namespace App\Livewire;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Services\ServiceService;
use App\Domains\Common\Services\AddressService;
use Livewire\Attributes\On;
use App\Livewire\Traits\Addressable; // Import the trait
use App\Livewire\Traits\Workflowable; // Import the trait

use App\Domains\Common\Interfaces\AddressProviderInterface;

class ServiceForm extends FormWithMedia
{
    use Addressable; // Use the trait
    use Workflowable; // Use the trait

    public ?Service $service = null;

    // Livewire properties for Service
    public ?string $title = null;
    public ?string $description = null;
    public float|string|null $price = null;
    public int|string|null $category_id = null;
    public ?bool $pay_first = null;
    public ?int $address_id = null;

    public $categories;

    public function boot(AddressProviderInterface $addressProvider)
    {
        $this->bootAddressable($addressProvider); // Call the trait's boot method
    }

    protected function rules()
    {
        $rules = [
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|min:10',
            'price' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'workflow_template_id' => 'required|integer|exists:workflow_templates,id',
            'address_id' => 'nullable|exists:addresses,id',
            'pay_first' => 'boolean',
            'newFiles.*' => 'nullable|image|max:2048',
        ];

        if ($this->showAddressModal) {
            $rules = array_merge($rules, $this->addressValidationRules()); // Merge address rules
        }

        return $rules;
    }

    public function mount(
        ?Service $service = null,
        $categories = [],
        $addresses = []
    ) {
        $this->service = $service;
        $this->categories = collect($categories);
        $this->mountAddressable(collect($addresses)); // Call mountAddressable
        $this->mountWorkflowable($service); // Call mountWorkflowable

        // Default values
        $this->title = '';
        $this->description = '';
        $this->price = '';
        $this->category_id = '';
        $this->pay_first = false;
        $this->address_id = $this->addresses->firstWhere('pivot.is_primary', true)->id ?? null;

        if ($service && $service->exists) {
            // Edit mode – populate with existing data
            $this->title = $service->title;
            $this->description = $service->description;
            $this->price = $service->price;
            $this->category_id = $service->category_id;
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
        if (empty($this->address_id) && !$this->showAddressModal) {
            $this->addError('address_id', 'Please select an address or add a new one.');
            return;
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
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

        try {
            if ($this->service && $this->service->exists) {
                // UPDATE: Pass both uploads and removals
                $serviceService->updateService(
                    $this->service,
                    $data,
                    $uploadedFiles
                );
            } else {
                // CREATE: Pass uploads (no removals on create)
                $serviceService->createService($data, $uploadedFiles);
            }

            // Step 5: Success
            $this->resetMediaForm();
            session()->flash('success', 'Service saved successfully!');
            return redirect()->route('creator.services.index');

        } catch (\Throwable $e) {
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