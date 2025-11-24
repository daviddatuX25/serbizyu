<div class="space-y-6">

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 rounded-md p-3 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-200 text-red-800 rounded-md p-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div x-data="{ tab: 'owned' }">
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="tab = 'owned'"
                        :class="{ 'border-blue-500 text-blue-600': tab === 'owned', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'owned' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    My Workflows ({{ $ownedWorkflows->count() }})
                </button>
                <button @click="tab = 'bookmarked'"
                        :class="{ 'border-blue-500 text-blue-600': tab === 'bookmarked', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'bookmarked' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Bookmarked ({{ $bookmarkedWorkflows->count() }})
                </button>
            </nav>
        </div>

        <!-- Owned Workflows Panel -->
        <div x-show="tab === 'owned'" class="space-y-4">
            @forelse($ownedWorkflows as $workflow)
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-md font-semibold text-gray-800">{{ $workflow->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $workflow->workTemplates->count() }} steps</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('creator.workflows.edit', ['workflow' => $workflow->id]) }}" class="px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Edit</a>
                        <form action="{{ route('creator.workflows.duplicate', ['workflow' => $workflow->id]) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Duplicate</button>
                        </form>
                        <button wire:click="delete({{ $workflow->id }})" wire:confirm="Are you sure you want to delete this workflow?" class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Delete</button>
                    </div>
                </div>
            @empty
                <p>You haven't created any workflows yet.</p>
            @endforelse
        </div>

        <!-- Bookmarked Workflows Panel -->
        <div x-show="tab === 'bookmarked'" class="space-y-4" style="display: none;">
            @forelse($bookmarkedWorkflows as $workflow)
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-md font-semibold text-gray-800">{{ $workflow->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $workflow->workTemplates->count() }} steps. @if($workflow->is_public)<span class="text-xs text-green-600 bg-green-100 px-2 py-0.5 rounded-full">Public</span>@endif</p>
                    </div>
                    <div class="flex space-x-2">
                        <form action="{{ route('creator.workflows.duplicate', ['workflow' => $workflow->id]) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Copy to My Workflows</button>
                        </form>
                        <button wire:click="unbookmark({{ $workflow->id }})" class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200">Unbookmark</button>
                    </div>
                </div>
            @empty
                <p>You haven't bookmarked any public workflows yet. <a href="{{ route('workflows') }}" class="text-blue-600 hover:underline">Browse public workflows</a>.</p>
            @endforelse
        </div>
    </div>
</div>