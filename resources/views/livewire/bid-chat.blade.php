@if($thread)
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <!-- Chat Header -->
            <div class="pb-4 border-b border-gray-200 mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Bid Discussion</h3>
                <p class="text-sm text-gray-600">
                    @if(Auth::user()->id === $bid->bidder_id)
                        Chat with {{ $bid->openOffer->creator->name }}
                    @else
                        Chat with bidder: {{ $bid->bidder->name }}
                    @endif
                </p>
            </div>

            <!-- Chat Interface Component -->
            <livewire:chat-interface :thread="$thread" />
        </div>
    </div>
@else
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-center">
            <p class="text-gray-600">No conversation yet. Message will appear here.</p>
        </div>
    </div>
@endif
