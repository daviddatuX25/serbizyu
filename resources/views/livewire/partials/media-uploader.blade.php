<!-- Featured Photos Section -->
<div class="border rounded-lg p-4">
    <div class="flex justify-between items-center mb-3">
        <h3 class="font-semibold text-sm">Featured photos</h3>
        @if(count($existingImages) > 0 || count($newFiles) > 0)
            <div class="flex space-x-3 text-sm">
                <button type="button" wire:click="selectAllImages" class="text-blue-600 hover:text-blue-700">
                    Select all
                </button>
                <button type="button" wire:click="deleteSelected" class="text-red-600 hover:text-red-700">
                    Delete
                </button>
            </div>
        @endif
    </div>

    @if(count($existingImages) > 0 || count($newFiles) > 0)
        <!-- Filled State: Horizontal scroll with images -->
        <div class="flex space-x-3 overflow-x-auto pb-2">
            <!-- Upload Button -->
            <div class="flex-shrink-0 flex items-center justify-center w-24 h-24 border-2 border-dashed border-gray-300 rounded-md hover:border-blue-400 transition cursor-pointer">
                <label for="file-upload" class="cursor-pointer">
                    <span class="text-4xl text-gray-400">+</span>
                    <input id="file-upload" type="file" wire:model="newFiles" multiple class="sr-only" accept="image/*">
                </label>
            </div>

            <!-- Existing Images -->
            @foreach($existingImages as $img)
                <div class="flex-shrink-0 w-24 h-24 relative group">
                    <img src="{{ $img['url'] }}" class="w-full h-full object-cover rounded-md border border-gray-200">
                    <button type="button" wire:click="removeExistingImage({{ $img['id'] }})"
                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white w-6 h-6 rounded-full text-xs opacity-0 group-hover:opacity-100 transition">
                        ×
                    </button>
                </div>
            @endforeach

            <!-- New Files Preview -->
            @foreach($newFiles as $i => $file)
                <div class="flex-shrink-0 w-24 h-24 relative group">
                    <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover rounded-md border-2 border-green-300">
                    <button type="button" wire:click="removeNewFile({{ $i }})"
                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white w-6 h-6 rounded-full text-xs opacity-0 group-hover:opacity-100 transition">
                        ×
                    </button>
                    <span class="absolute bottom-1 left-1 bg-green-600 text-white px-2 py-0.5 text-xs rounded">
                        New
                    </span>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State: Large upload area -->
        <div class="mt-2 flex items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-md hover:border-blue-400 transition cursor-pointer">
            <label for="file-upload-empty" class="cursor-pointer">
                <span class="text-4xl text-gray-400">+</span>
                <input id="file-upload-empty" type="file" wire:model="newFiles" multiple class="sr-only" accept="image/*">
            </label>
        </div>
    @endif

    @error('newFiles.*') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF up to 2MB</p>
</div>
