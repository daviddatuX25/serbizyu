{{-- resources/views/creator/bids/partials/bid-card.blade.php --}}
<div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
    <div class="p-6">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center">
            <div class="flex items-center mb-4 sm:mb-0">
                <img class="h-12 w-12 rounded-full object-cover" src="{{ $bid->bidder->profile_photo_url }}" alt="{{ $bid->bidder->name }}">
                <div class="ml-4">
                    <div class="text-sm font-semibold text-gray-900">{{ $bid->bidder->name }}</div>
                    <div class="text-xs text-gray-500">Placed on {{ $bid->created_at->format('M d, Y') }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Proposed Price</div>
                <div class="text-2xl font-bold text-green-600">₱{{ number_format($bid->proposed_price, 2) }}</div>
            </div>
        </div>

        @if($bid->message)
            <div class="mt-4 p-4 bg-gray-50 rounded-lg border">
                <p class="text-sm text-gray-600">{{ $bid->message }}</p>
            </div>
        @endif
    </div>

    <div class="px-6 py-3 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-2">
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                @switch($bid->status)
                    @case('pending') bg-yellow-100 text-yellow-800 @break
                    @case('accepted') bg-green-100 text-green-800 @break
                    @case('rejected') bg-red-100 text-red-800 @break
                @endswitch">
                {{ ucfirst($bid->status) }}
            </span>
        </div>
        
        @if($bid->status == 'pending')
            <div class="flex items-center space-x-3 mt-3 sm:mt-0">
                <form action="{{ route('creator.openoffers.bids.reject', ['openoffer' => $bid->openOffer, 'bid' => $bid]) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this bid?');">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-gray-600 hover:text-red-600 transition">
                        Reject
                    </button>
                </form>
                <form action="{{ route('creator.openoffers.bids.accept', ['openoffer' => $bid->openOffer, 'bid' => $bid]) }}" method="POST" onsubmit="return confirm('Accepting this bid will close the offer to new bids. Proceed?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-green-700">
                        Accept Bid
                    </button>
                </form>
            </div>
        @elseif($bid->status == 'accepted')
             <a href="#" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700">
                View Order
            </a>
        @endif
    </div>
</div>
