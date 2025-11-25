@props(['workflow'])

<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 flex flex-col justify-between h-full">
    <div>
        <h3 class="text-lg font-semibold text-gray-900">{{ $workflow->name }}</h3>
        <p class="text-sm text-gray-500 mt-1">{{ $workflow->category->name ?? 'Uncategorized' }}</p>
        <p class="text-sm text-gray-600 mt-2">{{ Str::limit($workflow->description, 100) }}</p>

        @if($workflow->workTemplates->count() > 0)
            <div class="mt-4" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center text-xs font-semibold text-gray-700">
                    <span>Steps: {{ $workflow->workTemplates->count() }}</span>
                    <x-icons.chevron-down class="w-4 h-4 ml-1 transform" x-bind:class="{ 'rotate-180': open }" />
                </button>
                <ul x-show="open" x-transition class="list-disc list-inside text-xs text-gray-600 mt-1 pl-4 space-y-1">
                    @foreach ($workflow->workTemplates as $work)
                        <li>{{ $work->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="mt-4 flex items-center justify-end">
        @auth
            @if($workflow->creator_id === Auth::id())
                <span class="text-xs text-gray-500">Your public workflow</span>
            @else
                @php
                    // This check is inefficient in a loop. A better approach would be to pass bookmarked IDs from the controller.
                    // For now, keeping it simple as per the refactoring direction.
                    $isBookmarked = Auth::user()->bookmarkedWorkflows->contains($workflow->id);
                @endphp
                @if($isBookmarked)
                    <form action="{{ route('workflows.unbookmark', $workflow) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1 text-sm font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200">
                            Unbookmark
                        </button>
                    </form>
                @else
                    <form action="{{ route('workflows.bookmark', $workflow) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1 text-sm font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200">
                            Bookmark
                        </button>
                    </form>
                @endif
            @endif
        @else
            <a href="{{ route('auth.signin') }}" class="text-sm text-blue-600 hover:underline">Login to bookmark</a>
        @endauth
    </div>
</div>
