<?php

namespace App\Livewire;

use App\Domains\Common\Services\AddressService;
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
    public string $house_no = '';
    public string $street = '';
    public string $barangay = '';
    public string $town = '';
    public string $province = '';
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
            'house_no' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'town' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'is_primary' => 'boolean',
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
        $this->house_no = '';
        $this->street = '';
        $this->barangay = '';
        $this->town = '';
        $this->province = '';
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
        /** @var \App\Domains\Users\Models\User $user */
        $user = auth()->user();
        $address = $user->addresses()->withPivot('is_primary')->findOrFail($id);

        if ($address) {
            $this->addressId = $address->id;
            $this->house_no = $address->house_no ?? '';
            $this->street = $address->street ?? '';
            $this->barangay = $address->barangay ?? '';
            $this->town = $address->town;
            $this->province = $address->province;
            $this->country = $address->country;
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
}