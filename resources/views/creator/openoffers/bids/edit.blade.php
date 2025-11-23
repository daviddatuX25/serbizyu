<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Bid') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">
                        You are editing your bid for the offer: 
                        <a href="{{ route('openoffers.show', $openoffer) }}" class="text-blue-600 hover:underline">
                            {{ $openoffer->title }}
                        </a>
                    </h3>

                    @if($bid->status === \App\Enums\BidStatus::PENDING && $bid->openOffer->status === \App\Enums\OpenOfferStatus::OPEN)
                        <form action="{{ route('creator.openoffers.bids.update', ['openoffer' => $openoffer, 'bid' => $bid]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Amount -->
                            <div class="mb-4">
                                <label for="amount" class="block text-sm font-medium text-gray-700">Bid Amount</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="amount" id="amount" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" value="{{ old('amount', $bid->amount) }}" placeholder="0.00" step="0.01" required>
                                </div>
                                @error('amount')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Message -->
                            <div class="mb-4">
                                <label for="message" class="block text-sm font-medium text-gray-700">Message (Optional)</label>
                                <textarea name="message" id="message" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('message', $bid->message) }}</textarea>
                                @error('message')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <a href="{{ route('creator.openoffers.bids.index', $openoffer) }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                    Cancel
                                </a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                                    Update Bid
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="bg-gray-100 border-l-4 border-gray-400 p-4">
                            <h3 class="font-bold text-gray-800">Cannot Edit Bid</h3>
                            <p class="text-sm text-gray-700 mt-1">This bid cannot be edited because it is no longer pending or the associated open offer is closed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-creator-layout>
