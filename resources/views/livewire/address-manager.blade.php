<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">Your Addresses</h3>
        <button wire:click="addAddress()" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            Add New Address
        </button>
    </div>

    <!-- Address List -->
    <div class="space-y-4">
        @forelse ($addresses as $address)
            <div class="p-4 border rounded-lg flex justify-between items-center">
                <div>
                    {{-- Display the full address --}}
                    <p class="font-semibold">{{ $address->full_address }}</p>
                    @if ($address->pivot && $address->pivot->is_primary)
                        <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">Primary</span>
                    @endif
                </div>
                <div class="flex space-x-2">
                    <button wire:click="edit({{ $address->id }})" type="button" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Edit</button>
                    <button wire:click="confirmDelete({{ $address->id }})" type="button" class="text-sm font-medium text-red-600 hover:text-red-900">Delete</button>
                    @if (!$address->pivot || !$address->pivot->is_primary)
                        <button wire:click="setPrimary({{ $address->id }})" type="button" class="text-sm font-medium text-gray-600 hover:text-gray-900">Set as Primary</button>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-500">You have no saved addresses.</p>
        @endforelse
    </div>

    <!-- Add/Edit Modal -->
    @if ($isOpen)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="save">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                {{ $addressId ? 'Edit Address' : 'Add New Address' }}
                            </h3>
                            <div class="mt-4 space-y-4">
                                {{-- Label Field --}}
                                <div>
                                    <label for="label" class="block text-sm font-medium text-gray-700">Address Label (e.g., Home, Office)</label>
                                    <input type="text" wire:model.defer="label" id="label" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('label') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                {{-- Region Dropdown --}}
                                <div>
                                    <label for="selectedRegion" class="block text-sm font-medium text-gray-700">Region</label>
                                    <select wire:model.live="selectedRegion" id="selectedRegion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Region</option>
                                        @foreach ($regions as $region)
                                            <option value="{{ $region['code'] }}">{{ $region['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedRegion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="selectedRegion" class="text-sm text-gray-500">Loading provinces...</div>
                                </div>

                                {{-- Province Dropdown --}}
                                <div>
                                    <label for="selectedProvince" class="block text-sm font-medium text-gray-700">Province</label>
                                    <select wire:model.live="selectedProvince" id="selectedProvince" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ is_null($selectedRegion) || empty($provinces) ? 'disabled' : '' }}>
                                        <option value="">Select Province</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province['code'] }}">{{ $province['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedProvince') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="selectedProvince" class="text-sm text-gray-500">Loading cities...</div>
                                </div>

                                {{-- City/Municipality Dropdown --}}
                                <div>
                                    <label for="selectedCity" class="block text-sm font-medium text-gray-700">City/Municipality</label>
                                    <select wire:model.live="selectedCity" id="selectedCity" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ is_null($selectedProvince) || empty($cities) ? 'disabled' : '' }}>
                                        <option value="">Select City/Municipality</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city['code'] }}">{{ $city['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedCity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="selectedCity" class="text-sm text-gray-500">Loading barangays...</div>
                                </div>

                                {{-- Barangay Dropdown --}}
                                <div>
                                    <label for="selectedBarangay" class="block text-sm font-medium text-gray-700">Barangay</label>
                                    <select wire:model.live="selectedBarangay" id="selectedBarangay" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ is_null($selectedCity) || empty($barangays) ? 'disabled' : '' }}>
                                        <option value="">Select Barangay</option>
                                        @foreach ($barangays as $barangay)
                                            <option value="{{ $barangay['code'] }}">{{ $barangay['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedBarangay') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                {{-- Street Address Input --}}
                                <div>
                                    <label for="street_address" class="block text-sm font-medium text-gray-700">House No., Street Name, Subdivision, etc.</label>
                                    <input type="text" wire:model.defer="street_address" id="street_address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('street_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model.defer="is_primary" id="is_primary" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <label for="is_primary" class="ml-2 block text-sm text-gray-900">Set as primary address</label>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                                Save
                            </button>
                            <button wire:click="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($isDeleteModalOpen)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Address</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Are you sure you want to delete this address? This action cannot be undone.</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="delete()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <button wire:click="closeDeleteModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>