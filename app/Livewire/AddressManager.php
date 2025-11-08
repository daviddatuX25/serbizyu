<?php

namespace App\Livewire;

use App\Domains\Users\Services\AddressService;
use App\Domains\Common\Models\Address;
use Illuminate\Support\Collection;
use Livewire\Component;
use Illuminate\Validation\Rule;

class AddressManager extends Component
{
    public Collection $addresses;
    public bool $isOpen = false;
    public bool $isDeleteModalOpen = false;

    // Form fields
    public ?int $addressId = null;
    public string $address_line_1 = '';
    public string $address_line_2 = '';
    public string $city = '';
    public string $state = '';
    public string $postal_code = '';
    public string $country = '';
    public bool $is_primary = false;

    protected AddressService $addressService;

    public function boot(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function mount()
    {
        $this->loadAddresses();
    }

    public function loadAddresses()
    {
        $this->addresses = $this->addressService->getAddressesForUser();
    }

    protected function rules(): array
    {
        return [
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'is_primary' => 'nullable|boolean',
        ];
    }

    public function render()
    {
        return view('livewire.address-manager');
    }

    public function openModal()
    {
        $this->resetForm();
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
        $this->address_line_1 = '';
        $this->address_line_2 = '';
        $this->city = '';
        $this->state = '';
        $this->postal_code = '';
        $this->country = '';
        $this->is_primary = false;
        $this->resetErrorBag();
    }

    public function save()
    {
        $validatedData = $this->validate();

        try {
            if ($this->addressId) {
                // Update
                $this->addressService->updateUserAddress($this->addressId, $validatedData);
                session()->flash('success', 'Address updated successfully.');
            } else {
                // Create
                $this->addressService->createAddressForUser($validatedData);
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
        $address = $this->addresses->firstWhere('id', $id);
        if ($address) {
            $this->addressId = $address->id;
            $this->address_line_1 = $address->address_line_1;
            $this->address_line_2 = $address->address_line_2 ?? '';
            $this->city = $address->city;
            $this->state = $address->state;
            $this->postal_code = $address->postal_code;
            $this->country = $address->country;
            $this->is_primary = $address->pivot->is_primary;
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
}