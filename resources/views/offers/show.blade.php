<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $offer->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Management Button for Owner --}}
                    @can('update', $offer)
                        <div class="mb-6 pb-4 border-b">
                            <a href="{{ route('creator.openoffers.edit', $offer) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                                Manage Your Offer
                            </a>
                            <p class="text-sm text-gray-600 mt-2">You are the creator of this offer. Click here to manage bids and settings.</p>
                        </div>
                    @endcan

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Description</h3>
                        <p>{{ $offer->description }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Budget</h3>
                        <p>${{ number_format($offer->budget, 2) }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Category</h3>
                        <p>{{ $offer->category->name }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Status</h3>
                        <p>{{ ucfirst($offer->status->value) }}</p>
                    </div>

                    @if ($offer->deadline)
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Deadline</h3>
                            <p>{{ $offer->deadline->format('M d, Y') }}</p>
                        </div>
                    @endif

                    @if ($offer->media->count() > 0)
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Images</h3>
                            <div class="grid grid-cols-3 gap-4 mt-2">
                                @foreach ($offer->media as $media)
                                    <img src="{{ $media->getUrl() }}" alt="{{ $media->alt }}" class="w-full h-32 object-cover rounded-lg shadow-md">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @php
                        $hasBid = auth()->check() && $offer->bids()->where('bidder_id', auth()->id())->exists();
                    @endphp

                    {{-- Bidding Section --}}
                    @if (!$hasBid)
                        @can('create', [App\Domains\Listings\Models\OpenOfferBid::class, $offer])
                            <div class="mt-8">
                                <h3 class="text-2xl font-semibold mb-4">Place Your Bid</h3>
                                <livewire:bid-form :open-offer="$offer" />
                            </div>
                        @else
                            @guest
                                <div class="mt-8 bg-gray-100 border-l-4 border-gray-500 text-gray-700 p-4" role="alert">
                                    <p class="font-bold">Want to place a bid?</p>
                                    <p>Please <a href="{{ route('login') }}" class="text-blue-600 hover:underline">log in</a> or <a href="{{ route('register') }}" class="text-blue-600 hover:underline">create an account</a> to get started.</p>
                                </div>
                            @endguest
                        @endcan
                    @endif

                    {{-- Bid List --}}
                    @can('viewAny', [App\Domains\Listings\Models\OpenOfferBid::class, $offer])
                        <div class="mt-8">
                            <h3 class="text-2xl font-semibold mb-4">Current Bids</h3>
                            <livewire:bid-list :open-offer="$offer" />
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


