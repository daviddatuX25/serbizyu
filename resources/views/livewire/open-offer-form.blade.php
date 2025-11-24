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
        </div>

        @include('livewire.partials._address-form-fields')

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
</div>

