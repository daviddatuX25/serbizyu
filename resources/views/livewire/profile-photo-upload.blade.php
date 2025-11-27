<div class="card bg-white border border-gray-200 rounded-2xl shadow p-8">
    <h2 class="text-lg font-semibold mb-6 text-gray-800">Profile Photo</h2>

    <div class="flex flex-col gap-6">
        {{-- Current Photo Display --}}
        <div class="flex items-center gap-6">
            <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 flex-shrink-0 border-4 border-green-200">
                @if($existingMedia && $existingMedia->first())
                    <img src="{{ $existingMedia->first()->getUrl() }}" alt="Profile photo" class="w-full h-full object-cover">
                @else
                    <img src="{{ auth()->user()->media()->where('tag', 'profile_image')->first()?->getUrl() ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" alt="Profile photo" class="w-full h-full object-cover">
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-600">
                    @if($existingMedia && $existingMedia->first())
                        <span class="font-medium">Current photo</span>
                    @else
                        <span class="text-gray-500">No custom photo uploaded</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Upload Form --}}
        <form wire:submit="uploadPhoto" enctype="multipart/form-data" class="space-y-4">
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-green-400 transition cursor-pointer">
                <label for="photo-input" class="cursor-pointer">
                    <input
                        type="file"
                        id="photo-input"
                        wire:model="newFileUploads"
                        accept="image/*"
                        class="hidden"
                    >
                    <div class="text-center">
                        <i data-lucide="upload-cloud" class="w-8 h-8 mx-auto text-gray-400 mb-2"></i>
                        <p class="text-sm font-medium text-gray-700">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 5MB</p>
                    </div>
                </label>
            </div>

            {{-- File Preview --}}
            @if($newFiles && count($newFiles) > 0)
                <div class="flex items-center gap-4 p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                        <img src="{{ $newFiles[0]->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-700">{{ $newFiles[0]->getClientOriginalName() }}</p>
                        <p class="text-xs text-gray-500">{{ number_format($newFiles[0]->getSize() / 1024, 2) }} KB</p>
                    </div>
                </div>
            @endif

            {{-- Validation Errors --}}
            @error('newFileUploads')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            {{-- Session Messages --}}
            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-700 text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700 text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex gap-3 pt-2">
                @if($newFiles && count($newFiles) > 0)
                    <button type="submit" class="btn btn-primary px-6 py-2">
                        <span wire:loading.remove>Upload Photo</span>
                        <span wire:loading class="flex items-center gap-2">
                            <i class="animate-spin">↻</i> Uploading...
                        </span>
                    </button>
                @endif

                @if($existingMedia && $existingMedia->first())
                    <button
                        type="button"
                        wire:click="deletePhoto"
                        wire:confirm="Are you sure you want to remove your profile photo?"
                        class="btn border border-red-300 text-red-600 hover:bg-red-50 px-6 py-2">
                        Remove Photo
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
