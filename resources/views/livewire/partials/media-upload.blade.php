<div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">

    {{-- Grid for existing and new images --}}
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4 mb-4">
        {{-- Existing Images --}}
        @foreach($existingImages as $img)
            <div class="relative group">
                <img src="{{ $img['url'] }}" class="w-full h-24 object-cover rounded-md border border-gray-200">
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition flex items-center justify-center">
                    <button type="button" wire:click="removeExistingImage({{ $img['id'] }})"
                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white w-6 h-6 flex items-center justify-center rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                        ✕
                    </button>
                </div>
            </div>
        @endforeach

        {{-- New Files Preview --}}
        @foreach($newFiles as $i => $file)
            <div class="relative group">
                <img src="{{ $file->temporaryUrl() }}" class="w-full h-24 object-cover rounded-md border-2 border-green-400">
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition flex items-center justify-center">
                     <button type="button" wire:click="removeNewFile({{ $i }})"
                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white w-6 h-6 flex items-center justify-center rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                        ✕
                    </button>
                    <span class="absolute bottom-1 left-1 bg-green-600 text-white px-2 py-0.5 text-xs rounded-full">
                        New
                    </span>
                </div>
            </div>
        @endforeach

        {{-- "Add More" Button --}}
        @if(count($existingImages) > 0 || count($newFiles) > 0)
            <label for="file-upload" class="cursor-pointer w-full h-24 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-md flex flex-col items-center justify-center text-gray-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-xs mt-1">Add More</span>
            </label>
        @endif
    </div>

    {{-- Hidden File Input --}}
    <input id="file-upload" type="file" wire:model="newFiles" multiple class="hidden" accept="image/*" x-ref="fileInput">

    {{-- Empty State: Large upload area --}}
    @if(count($existingImages) === 0 && count($newFiles) === 0)
        <label for="file-upload-empty" class="cursor-pointer flex flex-col items-center justify-center text-gray-500 hover:text-green-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="mt-2 text-sm font-medium">Click to upload images</span>
            <input id="file-upload-empty" type="file" wire:model="newFiles" multiple class="sr-only" accept="image/*" x-ref="fileInput">
        </label>
    @endif

    <p class="text-xs text-gray-500 mt-2">Max {{ (new \App\Support\MediaConfig())->getUploadLimitDisplay('images') }} per image. PNG, JPG, GIF accepted.</p>
</div>
