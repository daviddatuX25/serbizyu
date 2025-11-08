<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">Your Addresses</h3>
        <button wire:click="openModal()" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            Add New Address
        </button>
    </div>

    <!-- Address List -->
    <div class="space-y-4">
        @forelse ($addresses as $address)
            <div class="p-4 border rounded-lg flex justify-between items-center">
                <div>
                    <p class="font-semibold">{{ $address->address_line_1 }}, {{ $address->city }}</p>
                    <p class="text-sm text-gray-600">{{ $address->state }}, {{ $address->postal_code }}, {{ $address->country }}</p>
                    @if ($address->pivot->is_primary)
                        <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">Primary</span>
                    @endif
                </div>
                <div class="flex space-x-2">
                    <button wire:click="edit({{ $address->id }})" type="button" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Edit</button>
                    <button wire:click="confirmDelete({{ $address->id }})" type="button" class="text-sm font-medium text-red-600 hover:text-red-900">Delete</button>
                    @unless ($address->pivot->is_primary)
                        <button wire:click="setPrimary({{ $address->id }})" type="button" class="text-sm font-medium text-gray-600 hover:text-gray-900">Set as Primary</button>
                    @endunless
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
                                <!-- Form Fields -->
                                <div>
                                    <label for="address_line_1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                                    <input type="text" wire:model.defer="address_line_1" id="address_line_1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('address_line_1') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" wire:model.defer="city" id="city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700">State / Province</label>
                                    <input type="text" wire:model.defer="state" id="state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('state') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                                    <input type="text" wire:model.defer="postal_code" id="postal_code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('postal_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                                    <input type="text" wire:model.defer="country" id="country" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('country') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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