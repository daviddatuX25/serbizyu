<x-app-layout :jsFiles="['app.js', 'swiper-listings.js']">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="lg:grid lg:grid-cols-3 lg:gap-8 max-w-7xl mx-auto">

            {{-- Left Column: Offer Details --}}
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Image Carousel -->
                    <div class="relative h-64 md:h-96 bg-gray-200"
                         data-loop="{{ $offer->media->count() > 1 ? 'true' : 'false' }}">
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

                        @if ($offer->address)
                            <p class="text-sm text-gray-500 mt-2 flex items-center space-x-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium text-gray-700">{{ $offer->address->full_address }}</span>
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
                 <div class="sticky top-20 mt-5">
                    {{-- Budget Card --}}
                    <div class="bg-white rounded-lg shadow-lg border p-6">
                        <p class="text-sm text-gray-500">Proposed Budget</p>
                        <p class="text-3xl font-bold text-green-600 mt-1">
                            ₱{{ number_format($offer->budget, 2) }}
                        </p>
                        <div class="mt-1 text-sm text-gray-500">
                            Status: <span class="font-medium text-gray-900">{{ ucfirst($offer->status->value) }}</span>
                        </div>

                        <!-- Flag Button -->
                        <button type="button"
                                @click="$dispatch('open-flag-modal', { id: {{ $offer->id }}, title: '{{ $offer->title }}' })"
                                class="mt-4 w-full bg-orange-50 hover:bg-orange-100 text-orange-700 font-medium py-2 px-4 rounded-lg transition flex items-center justify-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4a6 6 0 016-6h4a6 6 0 016 6v4M9 9a3 3 0 100-6 3 3 0 000 6zm6 0a3 3 0 100-6 3 3 0 000 6z"></path>
                            </svg>
                            Report Issue
                        </button>
                    </div>

                    {{-- Bidding Section --}}
                    <div class="mt-6">
                        @if (in_array($offer->status, [\App\Enums\OpenOfferStatus::CLOSED, \App\Enums\OpenOfferStatus::FULFILLED, \App\Enums\OpenOfferStatus::CANCELLED]))
                            <div class="bg-gray-100 text-center p-6 rounded-lg shadow-inner">
                                <h3 class="text-lg font-semibold text-gray-800">This offer is closed.</h3>
                                <p class="text-sm text-gray-700 mt-1">No new bids are being accepted.</p>
                            </div>
                        @elseif ($offer->status === \App\Enums\OpenOfferStatus::EXPIRED)
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                                <h3 class="text-lg font-semibold text-yellow-800">This offer has expired.</h3>
                                @can('renew', $offer)
                                    <form action="{{ route('creator.openoffers.renew', $offer) }}" method="POST" class="mt-2">
                                        @csrf
                                        <button type="submit" class="w-full btn-primary">Renew Offer</button>
                                    </form>
                                @endcan
                            </div>
                        @else
                            @php
                                $userBid = auth()->check() ? $offer->bids()->where('bidder_id', auth()->id())->where('status', '!=', \App\Enums\BidStatus::REJECTED)->first() : null;
                            @endphp

                            @if ($userBid)
                                <div class="bg-white p-6 rounded-lg shadow-md">
                                    <h3 class="text-lg font-semibold text-gray-800">Your Bid Status</h3>
                                    <div class="mt-2 flex items-center justify-between">
                                        <p class="text-2xl font-bold">₱{{ number_format($userBid->amount, 2) }}</p>
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                                            @switch($userBid->status)
                                                @case(\App\Enums\BidStatus::PENDING) bg-yellow-100 text-yellow-800 @break
                                                @case(\App\Enums\BidStatus::ACCEPTED) bg-green-100 text-green-800 @break
                                            @endswitch">
                                            {{ ucfirst($userBid->status->value) }}
                                        </span>
                                    </div>
                                    <p class="mt-4 text-sm text-gray-600">You have an active bid on this offer. You can edit your bid from your dashboard.</p>
                                </div>
                            @else
                                @can('create', [App\Domains\Listings\Models\OpenOfferBid::class, $offer])
                                    @include('listings.partials.bid-form', ['openoffer' => $offer])
                                @else
                                    @guest
                                        <div class="bg-gray-100 border-l-4 border-gray-400 p-4">
                                            <p class="font-bold">Want to place a bid?</p>
                                            <p>Please <a href="{{ route('auth.signin') }}" class="text-blue-600 hover:underline">log in</a> or <a href="{{ route('register') }}" class="text-blue-600 hover:underline">create an account</a> to get started.</p>
                                        </div>
                                    @endguest
                                @endcan
                            @endif
                        @endif
                    </div>
                 </div>
            </div>

            {{-- Bid List (Full Width Below) --}}
            @can('viewAny', [App\Domains\Listings\Models\OpenOfferBid::class, $offer])
                <div class="lg:col-span-3 mt-8">
                    @include('listings.partials.bid-list', ['bids' => $bids, 'openoffer' => $offer])
                </div>
            @endcan

        </div>
    </div>
</x-app-layout>

<!-- Flag Modal Component -->
<x-flag-modal contentType="OpenOffer" />
