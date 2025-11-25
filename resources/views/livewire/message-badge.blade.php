<div class="relative">
    <a href="{{ route('messages.index') }}" class="relative inline-flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
        <svg class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"/>
            <path fill-opacity=".5" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"/>
        </svg>
        <span class="text-sm font-medium text-gray-700">Messages</span>
        
        @if ($unreadCount > 0)
            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                {{ $unreadCount }}
            </span>
        @endif
    </a>
</div>
