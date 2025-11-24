<?php

namespace App\Livewire\Traits;

use App\Domains\Common\Interfaces\AddressProviderInterface;
use App\Domains\Common\Services\AddressService;
use Illuminate\Support\Collection;

trait Addressable
{
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
    
    // Properties for address data
    public array $regions = [];
    public array $provinces = [];
    public array $cities = [];
    public array $barangays = [];
    
    // Loading states
    public bool $loadingProvinces = false;
    public bool $loadingCities = false;
    public bool $loadingBarangays = false;

    public Collection $addresses; // New property
    
    protected AddressProviderInterface $addressProvider;

    public function mountAddressable(Collection $addresses): void // New method
    {
        $this->addresses = $addresses->map(function ($address) {
            $address->is_primary = $address->pivot->is_primary ?? false;
            return $address;
        });
    }

    public function bootAddressable(AddressProviderInterface $addressProvider): void
    {
        $this->addressProvider = $addressProvider;
    }

    public function openAddressModal(): void
    {
        $this->resetNewAddressForm();
        $this->loadRegions();
        $this->showAddressModal = true;
    }

    public function closeAddressModal(): void
    {
        $this->showAddressModal = false;
    }

    public function saveNewAddress(AddressService $addressService): void
    {
        $this->validate($this->addressValidationRules());

        $fullAddress = $this->composeFullAddress();

        $dataToService = [
            'label' => $this->new_label,
            'full_address' => $fullAddress,
            'street_address' => $this->new_street_address,
            'api_source' => 'PSGC_API',
            'api_id' => $this->getApiIdFromSelection(),
            'lat' => $this->new_lat,
            'lng' => $this->new_lng,
            'is_primary' => $this->new_is_primary,
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

    protected function addressValidationRules(): array
    {
        return [
            'new_label' => 'nullable|string|max:255',
            'new_street_address' => 'required|string|max:255',
            'new_selectedRegion' => 'required|string',
            'new_selectedProvince' => 'required|string',
            'new_selectedCity' => 'required|string',
            'new_selectedBarangay' => 'required|string',
            'new_is_primary' => 'boolean',
            'new_lat' => 'nullable|numeric',
            'new_lng' => 'nullable|numeric',
        ];
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

    private function resetNewAddressForm(): void
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
}
