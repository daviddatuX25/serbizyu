<x-app-layout :jsFiles="['app.js', 'swiper-listings.js']">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="lg:grid lg:grid-cols-3 lg:gap-8 max-w-7xl mx-auto">

            {{-- Left Column: Offer Details --}}
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Image Carousel -->
                    <div class="relative h-64 md:h-96 bg-gray-200">
                        @if($offer->media->isNotEmpty())
                            <div class="swiper serviceSwiper h-full">
                                <div class="swiper-wrapper">
                                    @foreach($offer->media as $media)
                                        <div class="swiper-slide bg-gray-100 flex items-center justify-center">
                                            <img src="{{ $media->getUrl() }}" alt="{{ $offer->title }}" 
                                                class="w-full h-full object-contain">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-button-prev text-white"></div>
                                <div class="swiper-button-next text-white"></div>
                                <div class="swiper-pagination"></div>
                            </div>
                        @else
                            <div class="flex items-center justify-center h-full">
                                <span class="text-gray-400 text-lg">No images available</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Management Banner for Owner --}}
                @can('update', $offer)
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    You are the creator of this offer. 
                                    <a href="{{ route('creator.openoffers.edit', $offer) }}" class="font-medium underline text-blue-700 hover:text-blue-600">Manage bids and settings here.</a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endcan

                <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">{{ $offer->category->name }}</p>
                        <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $offer->title }}</h1>
                        
                        @if ($offer->deadline)
                            <p class="text-sm text-gray-500 mt-2">
                                Offer closes on: <span class="font-medium text-gray-700">{{ $offer->deadline->format('M d, Y') }}</span>
                            </p>
                        @endif
                    </div>

                    <div class="pt-4 border-t">
                        <h3 class="font-semibold text-gray-800 mb-2">Description</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $offer->description }}</p>
                    </div>

                    @include('listings.partials.workflow-steps', ['workflowTemplate' => $offer->workflowTemplate])
                </div>
            </div>

            {{-- Right Column: Bidding and Info --}}
            <div class="lg:col-span-1 space-y-6">
                 <div class="sticky top-20">
                    {{-- Budget Card --}}
                    <div class="bg-white rounded-lg shadow-lg border p-6">
                        <p class="text-sm text-gray-500">Proposed Budget</p>
                        <p class="text-3xl font-bold text-green-600 mt-1">
                            ₱{{ number_format($offer->budget, 2) }}
                        </p>
                        <div class="mt-1 text-sm text-gray-500">
                            Status: <span class="font-medium text-gray-900">{{ ucfirst($offer->status->value) }}</span>
                        </div>
                    </div>

                    {{-- Bidding Section --}}
                    <div class="mt-6">
                        @php
                            $hasBid = auth()->check() && $offer->bids()->where('bidder_id', auth()->id())->exists();
                        @endphp

                        @if ($hasBid)
                             <div class="bg-green-50 text-center p-6 rounded-lg shadow-inner">
                                <h3 class="text-lg font-semibold text-green-800">You've placed a bid!</h3>
                                <p class="text-sm text-green-700 mt-1">The creator will be notified of your proposal.</p>
                            </div>
                        @else
                            @can('create', [App\Domains\Listings\Models\OpenOfferBid::class, $offer])
                                <h3 class="text-xl font-semibold mb-4">Place Your Bid</h3>
                                <livewire:bid-form :open-offer="$offer" />
                            @else
                                @guest
                                    <div class="bg-gray-100 border-l-4 border-gray-400 p-4">
                                        <p class="font-bold">Want to place a bid?</p>
                                        <p>Please <a href="{{ route('login') }}" class="text-blue-600 hover:underline">log in</a> or <a href="{{ route('register') }}" class="text-blue-600 hover:underline">create an account</a> to get started.</p>
                                    </div>
                                @endguest
                            @endcan
                        @endif
                    </div>
                 </div>
            </div>
            
            {{-- Bid List (Full Width Below) --}}
            @can('viewAny', [App\Domains\Listings\Models\OpenOfferBid::class, $offer])
                <div class="lg:col-span-3 mt-8">
                    <livewire:bid-list :open-offer="$offer" />
                </div>
            @endcan

        </div>
    </div>
</x-app-layout>
