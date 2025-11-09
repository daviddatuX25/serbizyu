<div>
    <div class="mb-4">
        <label for="images" class="block text-sm font-medium text-gray-700">Service Images</label>
        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="flex text-sm text-gray-600">
                    <label for="newImages" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                        <span>Upload files</span>
                        <input id="newImages" name="newImages" type="file" class="sr-only" wire:model="newImages" multiple>
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
            </div>
        </div>
        @error('newImages.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div wire:loading wire:target="newImages" class="text-sm text-gray-500">Uploading...</div>

    {{-- Previews --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Hidden inputs for images marked for removal --}}
        @foreach($imagesToRemove as $imageId)
            <input type="hidden" name="images_to_remove[]" value="{{ $imageId }}">
        @endforeach

        {{-- Existing Images --}}
        @foreach($existingImages as $image)
            <div class="relative">
                <img src="{{ $image->path }}" class="w-full h-32 object-cover rounded-md">
                <button wire:click.prevent="markForRemoval({{ $image->id }})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs">
                    &times;
                </button>
            </div>
        @endforeach

        {{-- New Image Previews --}}
        @foreach($newImages as $index => $image)
            <div class="relative">
                <img src="{{ $image->temporaryUrl() }}" class="w-full h-32 object-cover rounded-md">
                <button wire:click.prevent="removeNewImage({{ $index }})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs">
                    &times;
                </button>
            </div>
        @endforeach
    </div>
</div>