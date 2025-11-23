<?php

namespace App\Livewire;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Services\ServiceService;
use App\Domains\Common\Services\AddressService;
use Livewire\Attributes\On;

use App\Domains\Common\Interfaces\AddressProviderInterface;

class ServiceForm extends FormWithMedia
{
    public ?Service $service = null;

    // Livewire properties for Service
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

    // Properties for the new address modal
    public bool $showAddressModal = false;
    public string $new_label = '';
    public string $new_street_address = '';
    public ?string $new_selectedRegion = null;
    public ?string $new_selectedProvince = null;
    public ?string $new_selectedCity = null;
    public ?string $new_selectedBarangay = null;
    public ?float $new_lat = null;
    public ?float $new_lng = null;
    public bool $new_is_primary = false;
    
    public array $regions = [];
    public array $provinces = [];
    public array $cities = [];
    public array $barangays = [];
    
    public bool $loadingProvinces = false;
    public bool $loadingCities = false;
    public bool $loadingBarangays = false;
    
    protected AddressProviderInterface $addressProvider;

    public function boot(AddressProviderInterface $addressProvider)
    {
        $this->addressProvider = $addressProvider;
    }

    public function openAddressModal()
    {
        $this->resetNewAddressForm();
        $this->loadRegions();
        $this->showAddressModal = true;
    }

    public function closeAddressModal()
    {
        $this->showAddressModal = false;
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
            $rules['new_label'] = 'nullable|string|max:255';
            $rules['new_street_address'] = 'required|string|max:255';
            $rules['new_selectedRegion'] = 'required|string';
            $rules['new_selectedProvince'] = 'required|string';
            $rules['new_selectedCity'] = 'required|string';
            $rules['new_selectedBarangay'] = 'required|string';
            $rules['new_is_primary'] = 'boolean';
            $rules['new_lat'] = 'nullable|numeric';
            $rules['new_lng'] = 'nullable|numeric';
        }

        return $rules;
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
    }

    public function saveNewAddress(AddressService $addressService)
    {
        $validatedData = $this->validate([
            'new_label' => 'nullable|string|max:255',
            'new_street_address' => 'required|string|max:255',
            'new_selectedRegion' => 'required|string',
            'new_selectedProvince' => 'required|string',
            'new_selectedCity' => 'required|string',
            'new_selectedBarangay' => 'required|string',
            'new_is_primary' => 'boolean',
            'new_lat' => 'nullable|numeric',
            'new_lng' => 'nullable|numeric',
        ]);

        $fullAddress = $this->composeFullAddress();

        $dataToService = [
            'label' => $validatedData['new_label'],
            'full_address' => $fullAddress,
            'street_address' => $validatedData['new_street_address'],
            'api_source' => 'PSGC_API',
            'api_id' => $this->getApiIdFromSelection(),
            'lat' => $validatedData['new_lat'],
            'lng' => $validatedData['new_lng'],
            'is_primary' => $validatedData['new_is_primary'],
        ];

        try {
            $newAddress = $addressService->createAddressForUser($dataToService);
            $this->addresses = $addressService->getAddressesForUser();
            $this->address_id = $newAddress->id;
            $this->closeAddressModal();
            session()->flash('success', 'New address added and selected.');
        } catch (\Exception $e) {
            $this->addError('new_address', 'Failed to save new address: ' . $e->getMessage());
        }
    }
    
    public function loadRegions(): void
    {
        $this->regions = $this->addressProvider->getRegions();
    }

    public function updatedNewSelectedRegion(?string $value): void
    {
        $this->reset(['new_selectedProvince', 'new_selectedCity', 'new_selectedBarangay', 'provinces', 'cities', 'barangays']);
        if ($value) {
            $this->loadingProvinces = true;
            $this->provinces = $this->addressProvider->getProvinces($value);
            $this->loadingProvinces = false;
        }
    }

    public function updatedNewSelectedProvince(?string $value): void
    {
        $this->reset(['new_selectedCity', 'new_selectedBarangay', 'cities', 'barangays']);
        if ($value) {
            $this->loadingCities = true;
            $this->cities = $this->addressProvider->getCities($value);
            $this->loadingCities = false;
        }
    }

    public function updatedNewSelectedCity(?string $value): void
    {
        $this->reset(['new_selectedBarangay', 'barangays']);
        if ($value) {
            $this->loadingBarangays = true;
            $this->barangays = $this->addressProvider->getBarangays($value);
            $this->loadingBarangays = false;
        }
    }

    private function composeFullAddress(): string
    {
        $parts = [];
        if ($this->new_street_address) {
            $parts[] = $this->new_street_address;
        }
        if ($this->new_selectedBarangay) {
            $parts[] = $this->findNameByCode($this->barangays, $this->new_selectedBarangay);
        }
        if ($this->new_selectedCity) {
            $parts[] = $this->findNameByCode($this->cities, $this->new_selectedCity);
        }
        if ($this->new_selectedProvince) {
            $parts[] = $this->findNameByCode($this->provinces, $this->new_selectedProvince);
        }
        if ($this->new_selectedRegion) {
            $parts[] = $this->findNameByCode($this->regions, $this->new_selectedRegion);
        }
        return implode(', ', array_filter($parts));
    }

    private function findNameByCode(array $collection, string $code): string
    {
        foreach ($collection as $item) {
            if (($item['code'] ?? null) === $code) {
                return $item['name'] ?? $code;
            }
        }
        return $code;
    }

    private function getApiIdFromSelection(): ?string
    {
        $ids = array_filter([
            $this->new_selectedBarangay,
            $this->new_selectedCity,
            $this->new_selectedProvince,
            $this->new_selectedRegion,
        ]);
        return count($ids) > 0 ? implode('-', $ids) : null;
    }

    private function resetNewAddressForm()
    {
        $this->new_label = '';
        $this->new_street_address = '';
        $this->new_selectedRegion = null;
        $this->new_selectedProvince = null;
        $this->new_selectedCity = null;
        $this->new_selectedBarangay = null;
        $this->new_lat = null;
        $this->new_lng = null;
        $this->new_is_primary = false;
        $this->resetErrorBag();

        $this->provinces = [];
        $this->cities = [];
        $this->barangays = [];
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