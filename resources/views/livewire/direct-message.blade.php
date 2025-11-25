<div class="w-full max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-gray-200">
        <img src="{{ $recipient->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($recipient->name) }}" 
             alt="{{ $recipient->name }}" 
             class="w-12 h-12 rounded-full" />
        <div>
            <h2 class="text-lg font-bold text-gray-900">{{ $recipient->name }}</h2>
            <p class="text-xs text-gray-500">{{ $recipient->email }}</p>
        </div>
    </div>

    <!-- Messages -->
    <div class="my-4 h-96 overflow-y-auto space-y-4 p-4 bg-gray-50 rounded-lg" data-messages>
        @forelse ($messages as $message)
            <div class="flex {{ $message['is_mine'] ? 'justify-end' : 'justify-start' }}">
                <div class="flex gap-2 max-w-xs {{ $message['is_mine'] ? 'flex-row-reverse' : '' }}">
                    <img src="{{ $message['avatar'] }}" 
                         alt="{{ $message['sender_name'] }}" 
                         class="w-8 h-8 rounded-full flex-shrink-0" />
                    <div>
                        <p class="text-xs text-gray-600 px-3 py-1">
                            {{ $message['sender_name'] }}
                        </p>
                        <div class="bg-white rounded-lg px-4 py-2 shadow-sm">
                            <p class="text-sm text-gray-900">{{ $message['content'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $message['created_at'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex items-center justify-center h-full text-gray-500">
                <p>Start the conversation!</p>
            </div>
        @endforelse
    </div>

    <!-- Input -->
    <form wire:submit="sendMessage" class="flex gap-2">
        <input
            type="text"
            wire:model.lazy="newMessage"
            placeholder="Type a message..."
            @keydown.enter="$wire.sendMessage"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition font-semibold">
            ✓ Send
        </button>
    </form>
</div>
