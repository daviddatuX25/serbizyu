<div class="bg-white rounded-lg shadow">
    <form wire:submit.prevent="save" class="p-6 space-y-6">

        @if ($errors->has('save'))
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <p class="text-red-700 text-sm font-medium">{{ $errors->first('save') }}</p>
            </div>
        @endif

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" wire:model="title" id="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
            @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea wire:model="description" id="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="budget" class="block text-sm font-medium text-gray-700">Budget (PHP)</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm"> ₱ </span>
                    </div>
                    <input type="number" wire:model="budget" id="budget" step="0.01" class="block w-full pl-7 pr-12 border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="0.00">
                </div>
                @error('budget') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Expiration</label>
                <div class="mt-2 space-y-2">
                    <div class="flex flex-wrap gap-2">
                        @foreach([1, 3, 7, 14, 30] as $days)
                            <label class="flex items-center">
                                <input type="radio" value="{{ $days }}" wire:model="deadline_option" class="form-radio">
                                <span class="ml-2">{{ $days }} day{{ $days > 1 ? 's' : '' }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @error('deadline_option') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                <select wire:model="category_id" id="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <option value="">Select a category...</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <!-- Address Selection -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Address</label>
                @if(session()->has('success'))
                    <div class="bg-green-100 border border-green-200 text-green-800 rounded-md p-3 text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="border rounded-md p-3 space-y-3">
                    @forelse($addresses as $addr)
                        <label for="address_{{ $addr->id }}" class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-50">
                            <input type="radio" wire:model="address_id" id="address_{{ $addr->id }}" value="{{ $addr->id }}" class="text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">
                                {{ $addr->full_address }}
                                @if($addr->is_primary)
                                    <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-0.5 rounded-full">Primary</span>
                                @endif
                            </span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">You have no addresses. Please add one.</p>
                    @endforelse
                </div>
                @error('address_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                
                <button type="button" wire:click="openAddressModal" class="text-xs text-blue-600 hover:text-blue-700 mt-1 inline-block">+ Add new address</button>
            </div>
        </div>

        <div>
            <label for="workflow_template_id" class="block text-sm font-medium text-gray-700 mb-1">Workflow (Optional)</label>
            <select wire:model="workflow_template_id" id="workflow_template_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                <option value="">No workflow required</option>
                @foreach ($workflowTemplates as $template)
                    <option value="{{ $template['id'] }}">{{ $template['name'] }}</option>
                @endforeach
            </select>
            @error('workflow_template_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        
        <div class="relative flex items-start">
            <div class="flex items-center h-5">
                <input type="checkbox" wire:model="pay_first" id="pay_first" class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
            </div>
            <div class="ml-3 text-sm">
                <label for="pay_first" class="font-medium text-gray-700">Require payment before work begins</label>
                <p class="text-gray-500">If checked, the client must pay upfront before the workflow starts.</p>
            </div>
        </div>

        <div class="pt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Images (Optional)</label>
            @include('livewire.partials.media-uploader')
        </div>

        <div class="flex justify-end items-center space-x-4 pt-6 border-t border-gray-200 mt-6">
            <a href="{{ route('creator.openoffers.index') }}" class="bg-white border border-gray-300 rounded-md shadow-sm px-4 py-2 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancel
            </a>
            <button type="submit" class="bg-green-600 border border-transparent rounded-md shadow-sm px-4 py-2 inline-flex justify-center text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <div wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <span>{{ $offer && $offer->exists ? 'Update Offer' : 'Create Offer' }}</span>
            </button>
        </div>
    </form>

    <!-- Add New Address Modal -->
    @if ($showAddressModal)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeAddressModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="saveNewAddress">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Add New Address
                            </h3>
                            @error('new_address') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
                            <div class="mt-4 space-y-4">
                                {{-- Form fields from address manager --}}
                                <div>
                                    <label for="new_label" class="block text-sm font-medium text-gray-700">Address Label</label>
                                    <input type="text" wire:model.defer="new_label" id="new_label" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('new_label') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="new_selectedRegion" class="block text-sm font-medium text-gray-700">Region</label>
                                    <select wire:model.live="new_selectedRegion" id="new_selectedRegion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Region</option>
                                        @foreach ($regions as $region)
                                            <option value="{{ $region['code'] }}">{{ $region['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_selectedRegion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="new_selectedRegion">Loading...</div>
                                </div>
                                <div>
                                    <label for="new_selectedProvince" class="block text-sm font-medium text-gray-700">Province</label>
                                    <select wire:model.live="new_selectedProvince" id="new_selectedProvince" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ empty($provinces) ? 'disabled' : ''}}>
                                        <option value="">Select Province</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province['code'] }}">{{ $province['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_selectedProvince') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="new_selectedProvince">Loading...</div>
                                </div>
                                <div>
                                    <label for="new_selectedCity" class="block text-sm font-medium text-gray-700">City/Municipality</label>
                                    <select wire:model.live="new_selectedCity" id="new_selectedCity" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ empty($cities) ? 'disabled' : ''}}>
                                        <option value="">Select City/Municipality</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city['code'] }}">{{ $city['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_selectedCity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="new_selectedCity">Loading...</div>
                                </div>
                                <div>
                                    <label for="new_selectedBarangay" class="block text-sm font-medium text-gray-700">Barangay</label>
                                    <select wire:model.live="new_selectedBarangay" id="new_selectedBarangay" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ empty($barangays) ? 'disabled' : ''}}>
                                        <option value="">Select Barangay</option>
                                        @foreach ($barangays as $barangay)
                                            <option value="{{ $barangay['code'] }}">{{ $barangay['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_selectedBarangay') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="new_street_address" class="block text-sm font-medium text-gray-700">Street Address</label>
                                    <input type="text" wire:model.defer="new_street_address" id="new_street_address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('new_street_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model.defer="new_is_primary" id="new_is_primary" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <label for="new_is_primary" class="ml-2 block text-sm text-gray-900">Set as primary</label>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                                Save Address
                            </button>
                            <button wire:click="closeAddressModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

