<div class="flex h-screen bg-gray-100">
    <!-- Conversations List -->
    <div class="w-1/3 bg-white border-r border-gray-300 flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Messages</h2>
        </div>

        <div class="flex-1 overflow-y-auto">
            @forelse ($threads as $thread)
                <button
                    wire:click="selectThread({{ $thread['id'] }})"
                    class="w-full p-3 border-b border-gray-200 text-left hover:bg-gray-50 transition
                        {{ $selectedThread === $thread['id'] ? 'bg-blue-50' : '' }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $thread['title'] }}</h3>
                            <p class="text-sm text-gray-500 truncate">
                                @if(count($thread['messages']) > 0)
                                    {{ $thread['messages'][0]['content'] }}
                                @else
                                    No messages yet
                                @endif
                            </p>
                        </div>
                    </div>
                </button>
            @empty
                <div class="p-4 text-center text-gray-500">
                    <p>No conversations yet</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Messages View -->
    <div class="w-2/3 flex flex-col bg-white">
        @if ($selectedThread)
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $threads[array_key_first(array_filter($threads, fn($t) => $t['id'] === $selectedThread))]['title'] ?? 'Conversation' }}
                </h2>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3" data-messages>
                @forelse ($messages as $message)
                    <div class="flex {{ $message['is_mine'] ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs {{ $message['is_mine'] ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-900' }} rounded-lg px-4 py-2">
                            <p class="text-sm">{{ $message['content'] }}</p>
                            <p class="text-xs {{ $message['is_mine'] ? 'text-blue-100' : 'text-gray-600' }} mt-1">
                                {{ $message['created_at'] }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">
                        <p>No messages yet. Start the conversation!</p>
                    </div>
                @endforelse
            </div>

            <div class="p-4 border-t border-gray-200 bg-gray-50">
                <form wire:submit="sendMessage" class="flex gap-2">
                    <input
                        type="text"
                        wire:model="newMessage"
                        placeholder="Type a message..."
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <button
                        type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition font-semibold">
                        Send
                    </button>
                </form>
            </div>
        @else
            <div class="flex-1 flex items-center justify-center text-gray-500">
                <p>Select a conversation to start messaging</p>
            </div>
        @endif
    </div>
</div>
