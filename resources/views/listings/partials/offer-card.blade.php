{{-- resources/views/listings/partials/offer-card.blade.php --}}
@php
    $bidCount = $offer->bids->count();
@endphp
<div class="w-full bg-white border border-gray-200 rounded-xl shadow-lg">
    <a href="{{ route('openoffers.show', $offer) }}">
        <div class="relative h-48 overflow-hidden rounded-t-xl">
            @if($offer->media->isNotEmpty())
                <img src="{{ $offer->media->first()->getUrl() }}" 
                     alt="{{ $offer->title }}" 
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                    <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
            @endif
        </div>
    </a>
    <div class="p-4">
        <!-- Creator & Type -->
        <div class="flex justify-between items-center mb-2">
            <div class="flex items-center space-x-2">
                <img src="{{ $offer->creator->profile_photo_url }}" alt="{{ $offer->creator->name }}" class="w-8 h-8 rounded-full">
                <span class="text-sm font-medium text-gray-800">{{ $offer->creator->name }}</span>
            </div>
            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                Open Offer
            </span>
        </div>

        <!-- Title -->
        <a href="{{ route('openoffers.show', $offer) }}">
            <h5 class="text-lg font-semibold tracking-tight text-gray-900 line-clamp-2 h-14">{{ $offer->title }}</h5>
        </a>

        <!-- Bids -->
        <div class="flex items-center my-2">
            <span class="text-sm text-gray-500">{{ $bidCount }} {{ Str::plural('Bid', $bidCount) }} so far</span>
        </div>

        <!-- Budget & Location -->
        <div class="flex items-center justify-between pt-3 border-t border-gray-200">
            <div>
                <span class="text-xs text-gray-600">Budget</span>
                <p class="text-xl font-semibold text-gray-900">${{ number_format($offer->budget, 2) }}</p>
            </div>
            @if($offer->address)
            <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span class="truncate">{{ $offer->address->town }}</span>
            </div>
            @endif
        </div>
    </div>
</div>
