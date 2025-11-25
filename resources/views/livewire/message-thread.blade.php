<div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
    <!-- Thread Header -->
    <div class="bg-blue-600 text-white p-4">
        <h2 class="text-lg font-bold">{{ $thread->title }}</h2>
    </div>

    <!-- Messages Container -->
    <div class="h-96 overflow-y-auto p-4 space-y-3 bg-gray-50" data-messages>
        @forelse ($messages as $message)
            <div class="flex {{ $message['is_mine'] ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-xs {{ $message['is_mine'] ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-900' }} rounded-lg px-4 py-2">
                    <p class="font-semibold text-xs mb-1">{{ $message['sender'] }}</p>
                    <p class="text-sm">{{ $message['content'] }}</p>
                    <p class="text-xs {{ $message['is_mine'] ? 'text-blue-100' : 'text-gray-600' }} mt-1">
                        {{ $message['created_at'] }}
                    </p>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-12">
                <p>No messages yet</p>
            </div>
        @endforelse
    </div>

    <!-- Input Area -->
    <form wire:submit="sendMessage" class="border-t border-gray-200 p-4 bg-white">
        <div class="flex gap-2">
            <input
                type="text"
                wire:model="newMessage"
                placeholder="Type your message..."
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <button
                type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                Send
            </button>
        </div>
        @error('newMessage')
            <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
        @enderror
    </form>
</div>
