    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $service ? 'Edit Service' : 'Create Service' }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form wire:submit.prevent="save" class="space-y-6">

            <div>
                <label class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" wire:model.defer="title"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea wire:model.defer="description"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" wire:model.defer="price" step="0.01"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select wire:model.defer="category_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">--Select--</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Workflow Template</label>
                <select wire:model.defer="workflow_template_id"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">--Select--</option>
                    @foreach ($workflowTemplates as $template)
                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                    @endforeach
                </select>
                @error('workflow_template_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Media Upload Section --}}
            @include('livewire.partials.media-upload', [
                'newFiles' => $newFiles,
                'existingImages' => $existingImages
            ])

            <div class="flex items-center space-x-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.defer="is_active" class="form-checkbox">
                    <span class="ml-2">Active</span>
                </label>
            </div>

            <div>
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow">
                    {{ $service ? 'Update' : 'Create' }} Service
                </button>
            </div>

        </form>
    </div>
