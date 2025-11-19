<div class="border rounded-lg p-4">
    <div class="flex justify-between items-center mb-3">
        <h3 class="font-semibold text-sm">Featured photos</h3>
    </div>

    {{-- Existing Images --}}
    @if(count($existingImages) > 0)
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">
            @foreach($existingImages as $img)
                <div class="relative">
                    <img src="{{ $img['url'] }}" class="w-full h-32 object-cover rounded border border-gray-200">
                    <button type="button" wire:click="removeExistingImage({{ $img['id'] }})"
                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white px-2 py-1 text-xs rounded shadow-md">
                        ✕
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    {{-- New Files Preview --}}
    @if(count($newFiles) > 0)
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">
            @foreach($newFiles as $i => $file)
                <div class="relative">
                    <img src="{{ $file->temporaryUrl() }}" class="w-full h-32 object-cover rounded border border-green-200">
                    <button type="button" wire:click="removeNewFile({{ $i }})"
                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white px-2 py-1 text-xs rounded shadow-md">
                        ✕
                    </button>
                    <span class="absolute bottom-1 left-1 bg-green-600 text-white px-2 py-0.5 text-xs rounded">
                        New
                    </span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Empty State: Large upload area --}}
    @if(count($existingImages) === 0 && count($newFiles) === 0)
        <div class="mt-2 flex items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-md hover:border-blue-400 transition cursor-pointer"
            wire:click="$refs.fileInput.click()">
            <label for="file-upload-empty" class="cursor-pointer">
                <span class="text-4xl text-gray-400">+</span>
                <input id="file-upload-empty" type="file" wire:model="newFiles" multiple class="sr-only" accept="image/*" x-ref="fileInput">
            </label>
        </div>
    @endif
    
    <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF up to 2MB</p>
</div>
