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

        <!-- Payment Method -->
        <div>
            <label for="payment-method" class="block text-sm font-medium text-gray-700">Accepted Payment Methods</label>
            <select wire:model.defer="payment_method" id="payment-method"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @foreach ($paymentMethods as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">
                <span id="payment-help">Choose which payment methods you accept</span>
            </p>
            @error('payment_method') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
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

        @include('livewire.partials._address-form-fields')

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea wire:model.defer="description" id="description" rows="4" 
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        @include('livewire.partials._workflow-form-fields', ['is_optional' => false])

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