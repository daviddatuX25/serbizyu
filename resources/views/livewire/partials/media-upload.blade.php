<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Service Images</label>

    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
        <div class="space-y-1 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

            <div class="flex text-sm text-gray-600">
                <label class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                    <span>Upload files</span>
                    <input type="file" wire:model="newFiles" multiple class="sr-only">
                </label>
                <p class="pl-1">or drag and drop</p>
            </div>

            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
        </div>
    </div>

    {{-- Existing Images --}}
    @if(count($existingImages) > 0)
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">
            @foreach($existingImages as $img)
                <div class="relative {{ $img['is_removed'] ? 'opacity-50 grayscale' : '' }}">
                    <img src="{{ $img['url'] }}" class="w-full h-32 object-cover rounded border border-gray-200">
                    <button type="button" wire:click="removeExistingMedia({{ $img['id'] }})"
                        class="absolute top-1 right-1 text-white px-2 py-1 text-xs rounded shadow-md
                        {{ $img['is_removed'] ? 'bg-blue-600 hover:bg-blue-700' : 'bg-red-600 hover:bg-red-700' }}">
                        {{ $img['is_removed'] ? 'Undo' : '✕' }}
                    </button>
                    @if ($img['is_removed'])
                        <span class="absolute bottom-1 left-1 bg-yellow-600 text-white px-2 py-0.5 text-xs rounded">
                            Removed
                        </span>
                    @endif
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
</div>
