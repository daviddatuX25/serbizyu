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
use App\Domains\Common\Interfaces\AddressProviderInterface;
use App\Domains\Common\Services\AddressService;

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
    public string $deadline_option = '7';

    // For lists (categories, workflows) loaded for select inputs
    public array $categories = [];
    public array $workflowTemplates = [];
    public Collection $addresses;
    
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

    protected function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'required|numeric|min:1',
            'category_id' => 'required|integer|exists:categories,id',
            'workflow_template_id' => 'nullable|integer|exists:workflow_templates,id',
            'pay_first' => 'nullable|boolean',
            'address_id' => 'nullable|integer|exists:addresses,id',
            'deadline_option' => 'required|in:1,3,7,14,30',
            'newFiles' => 'nullable|array',
            'newFiles.*' => 'file|max:5120',
            'imagesToRemove' => 'nullable|array',
            'imagesToRemove.*' => 'integer',
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

    public function save()
    {
        if (empty($this->address_id) && !$this->showAddressModal) {
            $this->addError('address_id', 'Please select an address or add a new one.');
            return;
        }

        $this->validate();

        $openOfferService = app(\App\Domains\Listings\Services\OpenOfferService::class);

        $deadline = now()->addDays((int)$this->deadline_option);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'budget' => $this->budget,
            'category_id' => $this->category_id,
            'workflow_template_id' => $this->workflow_template_id,
            'pay_first' => $this->pay_first,
            'address_id' => $this->address_id,
            'deadline' => $deadline,
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
