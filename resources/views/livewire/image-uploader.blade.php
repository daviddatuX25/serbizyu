<div>
    {{-- Upload Section --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Service Images</label>

        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                <div class="flex text-sm text-gray-600 justify-center">
                    <label for="newFiles" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                        <span>Upload files</span>
                        <input id="newFiles" type="file" class="sr-only" wire:model="newFiles" multiple>
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>

                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>

                @error('newFiles.*')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Existing Images --}}
    @if(!empty($existingImages))
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Current Images</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach ($existingImages as $img)
                    <div class="relative group">
                        <img src="{{ $img['url'] }}" class="w-full h-32 object-cover rounded border border-gray-200">
                        <button type="button"
                                wire:click="removeExistingImage({{ $img['id'] }})"
                                class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white px-2 py-1 text-xs rounded shadow-md">
                            ✕
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- New Files Preview --}}
    @if(!empty($newFiles))
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">New Images (Not Yet Saved)</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach ($newFiles as $i => $file)
                    <div class="relative group">
                        <img src="{{ $file->temporaryUrl() }}" class="w-full h-32 object-cover rounded border border-green-200">
                        <button type="button"
                                wire:click="removeNewFile({{ $i }})"
                                class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white px-2 py-1 text-xs rounded shadow-md">
                            ✕
                        </button>
                        <span class="absolute bottom-1 left-1 bg-green-600 text-white px-2 py-0.5 text-xs rounded">New</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
