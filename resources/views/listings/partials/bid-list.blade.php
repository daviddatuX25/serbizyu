<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-xl font-semibold mb-4">Bids for this Offer</h3>

    @if ($bids->isEmpty())
        <div class="text-center py-6">
            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No bids yet</h3>
            <p class="mt-1 text-sm text-gray-500">Service providers can place bids on this open offer.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($bids as $bid)
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-lg font-bold text-gray-800">₱{{ number_format($bid->amount, 2) }}</p>
                            <p class="text-sm text-gray-600">
                                Bid by <span class="font-medium">{{ $bid->bidder->name }}</span> on {{ $bid->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @switch($bid->status)
                                    @case(\App\Enums\BidStatus::PENDING) bg-yellow-100 text-yellow-800 @break
                                    @case(\App\Enums\BidStatus::ACCEPTED) bg-green-100 text-green-800 @break
                                    @case(\App\Enums\BidStatus::REJECTED) bg-red-100 text-red-800 @break
                                @endswitch">
                                {{ ucfirst($bid->status->value) }}
                            </span>
                            @if($bid->status === \App\Enums\BidStatus::PENDING)
                                @can('accept', $bid)
                                    <form action="{{ route('creator.openoffers.bids.accept', ['openoffer' => $openoffer, 'bid' => $bid]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Accept
                                        </button>
                                    </form>
                                @endcan
                                @can('reject', $bid)
                                    <form action="{{ route('creator.openoffers.bids.reject', ['openoffer' => $openoffer, 'bid' => $bid]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Reject
                                        </button>
                                    </form>
                                @endcan
                                @can('update', $bid)
                                    <a href="{{ route('creator.openoffers.bids.edit', ['openoffer' => $openoffer, 'bid' => $bid]) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Edit
                                    </a>
                                @endcan
                                @can('delete', $bid)
                                <form action="{{ route('creator.openoffers.bids.destroy', ['openoffer' => $openoffer, 'bid' => $bid]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('Are you sure you want to delete this bid?')">
                                        Delete
                                    </button>
                                </form>
                                @endcan
                            @endif
                            
                        </div>
                    </div>
                    <p class="mt-2 text-gray-700">{{ $bid->message }}</p>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $bids->links() }}
        </div>
    @endif
</div>