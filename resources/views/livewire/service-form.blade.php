<div class="max-w-lg mx-auto w-full bg-white rounded-2xl border border-gray-300 shadow-md overflow-hidden">
    <header class="flex justify-between items-center p-4 border-b">
        <h1 class="text-lg font-semibold">{{ $service->exists ? 'Edit Service' : 'Create a Service' }}</h1>
    </header>

    <form wire:submit.prevent="save" class="p-4 space-y-4">
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

        <!-- Category and Address -->
        <div class="flex space-x-4">
            <div class="w-1/2">
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
            <div class="w-1/2">
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <select wire:model="address_id" id="address"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select...</option>
                    @foreach($addresses as $address)
                        <option value="{{ $address->id }}">
                            {{ $address->street }}, {{ $address->city }}
                            @if($address->is_primary) (Primary) @endif
                        </option>
                    @endforeach
                </select>
                @error('address_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                <a href="{{ route('profile.edit') }}" class="text-xs text-blue-600 hover:text-blue-700 mt-1 inline-block">+ Add new address</a>
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea wire:model.defer="description" id="description" rows="4" 
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Workflow Section -->
        @if($workflow_template_id && $service->exists)
            <!-- Filled State: Show selected workflow -->
            <div class="border rounded-lg p-4 space-y-3" x-data="{ expanded: false }">
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-sm">Workflow Steps</h3>
                    <button type="button" wire:click="$set('workflow_template_id', '')" class="text-red-600 text-sm hover:text-red-700">
                        Change
                    </button>
                </div>
                
                <!-- Compact View -->
                <div class="text-sm text-gray-600" x-show="!expanded">
                    @php
                        $template = $workflowTemplates->firstWhere('id', $workflow_template_id);
                    @endphp
                    @if($template)
                        {{ $template->workTemplates->pluck('name')->implode(' > ') }}
                    @endif
                </div>
                
                <!-- Expanded List View -->
                <div class="space-y-2" x-show="expanded" x-cloak>
                    @if($template)
                        @foreach($template->workTemplates as $step)
                            <p class="block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm">
                                {{ $step->name }}
                            </p>
                        @endforeach
                    @endif
                </div>
                
                <button type="button" @click="expanded = !expanded" class="text-xs text-blue-600 hover:text-blue-700">
                    <span x-show="!expanded">Show as list</span>
                    <span x-show="expanded" x-cloak>Show compact</span>
                </button>
            </div>
        @else
            <!-- Empty State: Selection buttons -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Workflow</label>
                <div class="flex space-x-2">
                    <button type="button" wire:click="$dispatch('openWorkflowSelector')"
                        class="flex-1 bg-gray-100 border border-gray-300 rounded-md p-2 text-sm hover:bg-gray-200 transition">
                        Select existing
                    </button>
                    <a href="{{ route('creator.workflows.create') }}" 
                        class="flex-1 bg-gray-800 text-white text-center rounded-md p-2 text-sm hover:bg-gray-900 transition">
                        Create new workflow
                    </a>
                </div>
                @error('workflow_template_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        @endif

        @include('livewire.partials.media-uploader')

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 pt-4 border-t mt-6">
            <a href="{{ route('creator.services.index') }}" 
                class="bg-gray-100 border border-gray-300 rounded-md px-4 py-2 text-sm hover:bg-gray-200 transition">
                Cancel
            </a>
            <button type="submit" 
                class="bg-blue-600 text-white rounded-md px-4 py-2 text-sm hover:bg-blue-700 transition">
                {{ $service->exists ? 'Update' : 'Publish' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Workflow selector modal listener
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('openWorkflowSelector', () => {
            // Open modal with workflow templates list
            // Implementation depends on your modal system
            console.log('Open workflow selector');
        });
    });
</script>
@endpush

