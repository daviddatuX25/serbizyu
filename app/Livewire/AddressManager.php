<?php

namespace App\Livewire;

use App\Domains\Common\Services\AddressService;
use App\Domains\Common\Interfaces\AddressProviderInterface; // Added
use App\Domains\Common\Models\Address;
use Illuminate\Support\Collection;
use Livewire\Component;
use Illuminate\Validation\Rule;

class AddressManager extends Component
{
    public Collection $addresses;
    public bool $isOpen = false;
    public bool $isDeleteModalOpen = false;

    // Form fields for a new/edited address
    public ?int $addressId = null;
    public string $label = '';
    public string $street_address = ''; // For user to input house number, street name etc.
    public ?string $selectedRegion = null;
    public ?string $selectedProvince = null;
    public ?string $selectedCity = null;
    public ?string $selectedBarangay = null;
    public ?float $lat = null;
    public ?float $lng = null;
    public bool $is_primary = false;

    // Data collections for dropdowns
    public array $regions = [];
    public array $provinces = [];
    public array $cities = [];
    public array $barangays = [];

    // Loading states for UI feedback
    public bool $loadingProvinces = false;
    public bool $loadingCities = false;
    public bool $loadingBarangays = false;

    // To store the full address string from selected components + street_address
    public string $full_address = '';


    protected AddressService $addressService;
    protected AddressProviderInterface $addressProvider; // Added

    public function boot(AddressService $addressService, AddressProviderInterface $addressProvider) // Modified
    {
        $this->addressService = $addressService;
        $this->addressProvider = $addressProvider; // Added
    }

    public function mount()
    {
        $this->loadAddresses();
        $this->loadRegions(); // Load regions on mount
    }

    public function loadAddresses()
    {
        $this->addresses = $this->addressService->getAddressesForUser();
    }

    protected function rules(): array
    {
        return [
            'label' => 'nullable|string|max:255',
            'street_address' => 'required|string|max:255',
            'selectedRegion' => 'required|string',
            'selectedProvince' => 'required|string',
            'selectedCity' => 'required|string',
            'selectedBarangay' => 'required|string',
            'is_primary' => 'boolean',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ];
    }

    public function render()
    {
        return view('livewire.address-manager');
    }

    public function addAddress()
    {
        $this->resetForm();
        $this->isOpen = true;
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function openDeleteModal()
    {
        $this->isDeleteModalOpen = true;
    }

    public function closeDeleteModal()
    {
        $this->isDeleteModalOpen = false;
    }

    private function resetForm()
    {
        $this->addressId = null;
        $this->label = '';
        $this->street_address = '';
        $this->selectedRegion = null;
        $this->selectedProvince = null;
        $this->selectedCity = null;
        $this->selectedBarangay = null;
        $this->lat = null;
        $this->lng = null;
        $this->is_primary = false;
        $this->resetErrorBag();

        // Clear dependent data
        $this->provinces = [];
        $this->cities = [];
        $this->barangays = [];
    }

    public function save()
    {
        $validatedData = $this->validate();

        try {
            $fullAddress = $this->composeFullAddress();
            
            $dataToService = array_merge($validatedData, [
                'full_address' => $fullAddress,
                'api_source' => 'PSGC_API',
                'api_id' => $this->getApiIdFromSelection(),
                'lat' => $this->lat,
                'lng' => $this->lng,
            ]);

            if ($this->addressId) {
                $this->addressService->updateUserAddress($this->addressId, $dataToService);
                session()->flash('success', 'Address updated successfully.');
            } else {
                $this->addressService->createAddressForUser($dataToService);
                session()->flash('success', 'Address added successfully.');
            }

            $this->closeModal();
            $this->loadAddresses();
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        /** @var \App\Domains\Users\Models\User $user */
        $user = auth()->user();
        $address = $user->addresses()->withPivot('is_primary')->findOrFail($id);

        if ($address) {
            $this->addressId = $address->id;
            $this->label = $address->label ?? '';
            $this->full_address = $address->full_address;
            $this->street_address = $this->extractStreetAddressFromFullAddress($address->full_address);
            $this->deriveAddressComponentsFromFullAddress($address->full_address);
            $this->lat = $address->lat;
            $this->lng = $address->lng;
            $this->is_primary = (bool) $address->pivot->is_primary;
            $this->openModal();
        }
    }

    public function confirmDelete(int $id)
    {
        $this->addressId = $id;
        $this->openDeleteModal();
    }

    public function delete()
    {
        if ($this->addressId) {
            try {
                $this->addressService->deleteUserAddress($this->addressId);
                session()->flash('success', 'Address deleted successfully.');
                $this->loadAddresses();
            } catch (\Exception $e) {
                session()->flash('error', 'An error occurred: ' . $e->getMessage());
            }
        }
        $this->closeDeleteModal();
        $this->resetForm();
    }

    public function setPrimary(int $id)
    {
        try {
            $this->addressService->setPrimaryAddress($id);
            session()->flash('success', 'Primary address updated.');
            $this->loadAddresses();
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    // --- New API-fetching methods ---

    public function loadRegions(): void
    {
        $this->regions = $this->addressProvider->getRegions();
    }

    public function updatedSelectedRegion(?string $value): void
    {
        $this->reset(['selectedProvince', 'selectedCity', 'selectedBarangay', 'provinces', 'cities', 'barangays']);
        if ($value) {
            $this->loadingProvinces = true;
            $this->provinces = $this->addressProvider->getProvinces($value);
            $this->loadingProvinces = false;
        }
    }

    public function updatedSelectedProvince(?string $value): void
    {
        $this->reset(['selectedCity', 'selectedBarangay', 'cities', 'barangays']);
        if ($value) {
            $this->loadingCities = true;
            $this->cities = $this->addressProvider->getCities($value);
            $this->loadingCities = false;
        }
    }

    public function updatedSelectedCity(?string $value): void
    {
        $this->reset(['selectedBarangay', 'barangays']);
        if ($value) {
            $this->loadingBarangays = true;
            $this->barangays = $this->addressProvider->getBarangays($value);
            $this->loadingBarangays = false;
        }
    }

    // --- Helper methods for address composition/decomposition ---

    private function composeFullAddress(): string
    {
        $parts = [];
        if ($this->street_address) {
            $parts[] = $this->street_address;
        }
        if ($this->selectedBarangay) {
            $parts[] = $this->findNameByCode($this->barangays, $this->selectedBarangay);
        }
        if ($this->selectedCity) {
            $parts[] = $this->findNameByCode($this->cities, $this->selectedCity);
        }
        if ($this->selectedProvince) {
            $parts[] = $this->findNameByCode($this->provinces, $this->selectedProvince);
        }
        if ($this->selectedRegion) {
            $parts[] = $this->findNameByCode($this->regions, $this->selectedRegion);
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
            $this->selectedBarangay,
            $this->selectedCity,
            $this->selectedProvince,
            $this->selectedRegion,
        ]);
        return count($ids) > 0 ? implode('-', $ids) : null;
    }

    private function extractStreetAddressFromFullAddress(string $fullAddress): string
    {
        $parts = explode(',', $fullAddress, 2);
        return trim($parts[0]);
    }

    private function deriveAddressComponentsFromFullAddress(string $fullAddress): void
    {
        // Placeholder for future implementation
    }
}