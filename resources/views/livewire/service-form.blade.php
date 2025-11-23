<div>
    <header class="flex justify-between items-center p-4 border-b">
        <h1 class="text-lg font-semibold">{{ $service->exists ? 'Edit Service' : 'Create a Service' }}</h1>
    </header>

    <form wire:submit.prevent="save" class="p-4 space-y-4">
        
        <!-- Global Error Display -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                <h3 class="text-red-800 font-semibold mb-2">Validation Errors:</h3>
                <ul class="list-disc list-inside text-red-700 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" wire:model.defer="title" id="title" 
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Price and Pay First -->
        <div class="flex space-x-4">
            <div class="w-1/2">
                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                <input type="number" wire:model.defer="price" id="price" step="0.01"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="w-1/2">
                <label for="pay-first" class="block text-sm font-medium text-gray-700">Pay First</label>
                <select wire:model.defer="pay_first" id="pay-first" 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
        </div>

        <!-- Category -->
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
            <select wire:model.defer="category_id" id="category"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select...</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
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

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea wire:model.defer="description" id="description" rows="4" 
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Workflow Section -->
        <div>
            <label for="workflow_template_id" class="block text-sm font-medium text-gray-700 mb-1">Workflow</label>
            <select wire:model.live="workflow_template_id" id="workflow_template_id"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select a workflow...</option>
                @foreach ($workflowTemplates as $template)
                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                @endforeach
            </select>
            @error('workflow_template_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            
            @if($workflow_template_id)
                @php
                    $selectedWorkflow = $workflowTemplates->firstWhere('id', $workflow_template_id);
                @endphp
                @if($selectedWorkflow)
                    <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <p class="text-sm text-gray-700"><strong>{{ $selectedWorkflow->name }}</strong></p>
                        <p class="text-xs text-gray-600 mt-1">{{ $selectedWorkflow->description }}</p>
                        @if ($selectedWorkflow->workTemplates->count() > 0)
                            <div class="mt-2">
                                <span class="text-xs font-semibold text-gray-700">Steps:</span>
                                <ul class="list-disc list-inside text-xs text-gray-600">
                                    @foreach ($selectedWorkflow->workTemplates as $work)
                                        <li>{{ $work->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            @endif
            
            <div class="mt-2 text-right">
                <a href="{{ route('creator.workflows.create') }}" target="_blank" class="text-xs text-gray-600 hover:text-blue-700">
                    + Create New Workflow
                </a>
            </div>
        </div>

        @include('livewire.partials.media-uploader')

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 pt-4 border-t mt-6">
            <a href="{{ route('creator.services.index') }}" 
                class="bg-gray-100 border border-gray-300 rounded-md px-4 py-2 text-sm hover:bg-gray-200 transition">
                Cancel
            </a>
            <button 
                type="submit"
                class="bg-blue-600 text-white rounded-md px-4 py-2 text-sm hover:bg-blue-700 transition">
                {{ $service->exists ? 'Update' : 'Publish' }}
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
                                {{-- Label Field --}}
                                <div>
                                    <label for="new_label" class="block text-sm font-medium text-gray-700">Address Label (e.g., Home, Office)</label>
                                    <input type="text" wire:model.defer="new_label" id="new_label" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('new_label') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                {{-- Region Dropdown --}}
                                <div>
                                    <label for="new_selectedRegion" class="block text-sm font-medium text-gray-700">Region</label>
                                    <select wire:model.live="new_selectedRegion" id="new_selectedRegion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Region</option>
                                        @foreach ($regions as $region)
                                            <option value="{{ $region['code'] }}">{{ $region['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_selectedRegion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="new_selectedRegion" class="text-sm text-gray-500">Loading provinces...</div>
                                </div>

                                {{-- Province Dropdown --}}
                                <div>
                                    <label for="new_selectedProvince" class="block text-sm font-medium text-gray-700">Province</label>
                                    <select wire:model.live="new_selectedProvince" id="new_selectedProvince" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ is_null($new_selectedRegion) || empty($provinces) ? 'disabled' : '' }}>
                                        <option value="">Select Province</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province['code'] }}">{{ $province['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_selectedProvince') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="new_selectedProvince" class="text-sm text-gray-500">Loading cities...</div>
                                </div>

                                {{-- City/Municipality Dropdown --}}
                                <div>
                                    <label for="new_selectedCity" class="block text-sm font-medium text-gray-700">City/Municipality</label>
                                    <select wire:model.live="new_selectedCity" id="new_selectedCity" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ is_null($new_selectedProvince) || empty($cities) ? 'disabled' : '' }}>
                                        <option value="">Select City/Municipality</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city['code'] }}">{{ $city['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_selectedCity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="new_selectedCity" class="text-sm text-gray-500">Loading barangays...</div>
                                </div>

                                {{-- Barangay Dropdown --}}
                                <div>
                                    <label for="new_selectedBarangay" class="block text-sm font-medium text-gray-700">Barangay</label>
                                    <select wire:model.live="new_selectedBarangay" id="new_selectedBarangay" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ is_null($new_selectedCity) || empty($barangays) ? 'disabled' : '' }}>
                                        <option value="">Select Barangay</option>
                                        @foreach ($barangays as $barangay)
                                            <option value="{{ $barangay['code'] }}">{{ $barangay['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_selectedBarangay') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                {{-- Street Address Input --}}
                                <div>
                                    <label for="new_street_address" class="block text-sm font-medium text-gray-700">House No., Street Name, Subdivision, etc.</label>
                                    <input type="text" wire:model.defer="new_street_address" id="new_street_address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('new_street_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model.defer="new_is_primary" id="new_is_primary" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <label for="new_is_primary" class="ml-2 block text-sm text-gray-900">Set as primary address</label>
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