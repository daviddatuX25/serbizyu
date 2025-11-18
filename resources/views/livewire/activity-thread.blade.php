<div>
    <div class="flex flex-col space-y-4">
        @foreach ($this->messages as $message)
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($message->user->name) }}" alt="{{ $message->user->name }}">
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $message->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $message->content }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ $message->created_at->diffForHumans() }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <form wire:submit.prevent="sendMessage" class="mt-6">
        <label for="newMessage" class="sr-only">Your message</label>
        <textarea wire:model="newMessage" id="newMessage" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Type your message..."></textarea>
        <div class="mt-3 flex items-center justify-between">
            <input type="file" wire:model="attachments" multiple class="block w-full text-sm text-gray-500
              file:mr-4 file:py-2 file:px-4
              file:rounded-md file:border-0
              file:text-sm file:font-semibold
              file:bg-indigo-50 file:text-indigo-700
              hover:file:bg-indigo-100"/>
            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Send Message
            </button>
        </div>
    </form>
</div>
