<div wire:poll.5000ms="refreshMessages">
    <div class="flex flex-col h-full bg-gray-100 rounded-lg shadow-md p-4">
        {{-- Messages Display --}}
        <div class="flex-1 overflow-y-auto mb-4 p-2 bg-white rounded-md" x-data="{ scrollToBottom() { setTimeout(() => $el.scrollTop = $el.scrollHeight, 100) } }" x-effect="scrollToBottom()">
            @forelse ($messages as $message)
                <div class="mb-2 {{ $message['sender_id'] === auth()->id() ? 'text-right' : 'text-left' }}">
                    <span class="text-xs text-gray-500">{{ ($message['sender']['firstname'] ?? 'Unknown') . ' ' . ($message['sender']['lastname'] ?? 'User') }} - {{ \Carbon\Carbon::parse($message['created_at'])->format('M d, H:i') }}</span>
                    <div class="p-2 rounded-lg inline-block {{ $message['sender_id'] === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-800' }}">
                        {{ $message['content'] }}
                    </div>
                    @if (isset($message['attachments']) && count($message['attachments']) > 0)
                        <div class="mt-1 text-xs text-gray-600">
                            Attachments:
                            @foreach ($message['attachments'] as $attachment)
                                <a href="{{ Storage::url($attachment['file_path']) }}" target="_blank" class="text-blue-400 hover:underline">{{ $attachment['file_type'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-center text-gray-500">No messages yet.</p>
            @endforelse
        </div>

        {{-- Message Input --}}
        <form wire:submit.prevent="sendMessage" enctype="multipart/form-data" class="flex items-center gap-2 mt-2">
            <input type="text" wire:model.lazy="newMessage" placeholder="Type your message..." class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Send
            </button>
        </form>
    </div>
</div>
