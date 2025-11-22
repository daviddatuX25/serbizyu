<div class="bg-white rounded-lg shadow">
    <form wire:submit.prevent="save" class="p-6 space-y-6">
        
        <!-- Global Error Display -->
        @if ($errors->has('save'))
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <p class="text-red-700 text-sm font-medium">{{ $errors->first('save') }}</p>
            </div>
        @endif

        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" wire:model.defer="title" id="title" 
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
            @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea wire:model.defer="description" id="description" rows="4" 
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Budget and Deadline -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="budget" class="block text-sm font-medium text-gray-700">Budget (PHP)</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm"> ₱ </span>
                    </div>
                    <input type="number" wire:model.defer="budget" id="budget" step="0.01"
                        class="block w-full pl-7 pr-12 border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="0.00">
                </div>
                @error('budget') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="deadline" class="block text-sm font-medium text-gray-700">Deadline (Optional)</label>
                <input type="date" wire:model.defer="deadline" id="deadline"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                @error('deadline') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Category and Address -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                <select wire:model.defer="category_id" id="category_id"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <option value="">Select a category...</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="address_id" class="block text-sm font-medium text-gray-700">Location (Optional)</label>
                <select wire:model.defer="address_id" id="address_id"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <option value="">Select a location...</option>
                    @foreach($addresses as $address)
                        <option value="{{ $address->id }}">
                            {{ $address->street }}, {{ $address->town }}
                            @if($address->is_primary) (Primary) @endif
                        </option>
                    @endforeach
                </select>
                @error('address_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Workflow Section -->
        <div>
            <label for="workflow_template_id" class="block text-sm font-medium text-gray-700 mb-1">Workflow (Optional)</label>
            <select wire:model.defer="workflow_template_id" id="workflow_template_id"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                <option value="">No workflow required</option>
                @foreach ($workflowTemplates as $template)
                    <option value="{{ $template['id'] }}">{{ $template['name'] }}</option>
                @endforeach
            </select>
            @error('workflow_template_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        
        <!-- Pay First Checkbox -->
        <div class="relative flex items-start">
            <div class="flex items-center h-5">
                <input type="checkbox" wire:model.defer="pay_first" id="pay_first" class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
            </div>
            <div class="ml-3 text-sm">
                <label for="pay_first" class="font-medium text-gray-700">Require payment before work begins</label>
                <p class="text-gray-500">If checked, the client must pay upfront before the workflow starts.</p>
            </div>
        </div>


        {{-- Media Upload Section --}}
        <div class="pt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Images (Optional)</label>
            @include('livewire.partials.media-upload')
        </div>


        <!-- Action Buttons -->
        <div class="flex justify-end items-center space-x-4 pt-6 border-t border-gray-200 mt-6">
            <a href="{{ route('creator.openoffers.index') }}" 
                class="bg-white border border-gray-300 rounded-md shadow-sm px-4 py-2 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancel
            </a>
            <button 
                type="submit"
                class="bg-green-600 border border-transparent rounded-md shadow-sm px-4 py-2 inline-flex justify-center text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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
</div>

