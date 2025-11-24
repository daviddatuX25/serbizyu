<div class="space-y-6">
    <div class="flex justify-between items-center">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search public workflows..."
               class="form-input rounded-md shadow-sm mt-1 block w-1/3">
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 rounded-md p-3 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('info'))
        <div class="bg-blue-100 border border-blue-200 text-blue-800 rounded-md p-3 text-sm">
            {{ session('info') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-200 text-red-800 rounded-md p-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($publicWorkflows->isEmpty())
        <p class="text-gray-600">No public workflows found matching your criteria.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($publicWorkflows as $workflow)
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $workflow->name }}</h3>
                    <p class="text-sm text-gray-600 mt-2">{{ $workflow->description }}</p>

                    @if($workflow->workTemplates->count() > 0)
                        <div class="mt-4">
                            <span class="text-xs font-semibold text-gray-700">Steps:</span>
                            <ul class="list-disc list-inside text-xs text-gray-600 mt-1">
                                @foreach ($workflow->workTemplates as $work)
                                    <li>{{ $work->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-4 flex items-center justify-between">
                        @if(Auth::check() && $workflow->creator_id === Auth::id())
                            <span class="text-xs text-gray-500">Your public workflow</span>
                        @else
                            @if($this->isBookmarked($workflow->id))
                                <button wire:click="unbookmark({{ $workflow->id }})" wire:loading.attr="disabled"
                                        class="px-3 py-1 text-sm font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <span wire:loading.remove wire:target="unbookmark({{ $workflow->id }})">Unbookmark</span>
                                    <span wire:loading wire:target="unbookmark({{ $workflow->id }})">Unbookmarking...</span>
                                </button>
                            @else
                                <button wire:click="bookmark({{ $workflow->id }})" wire:loading.attr="disabled"
                                        class="px-3 py-1 text-sm font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <span wire:loading.remove wire:target="bookmark({{ $workflow->id }})">Bookmark</span>
                                    <span wire:loading wire:target="bookmark({{ $workflow->id }})">Bookmarking...</span>
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>