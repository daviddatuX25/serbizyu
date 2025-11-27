{{-- resources/views/listings/partials/offer-card.blade.php --}}
@php
    $bidCount = $offer->bids->count();
@endphp
<div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 ease-in-out group">
    <a href="{{ route('openoffers.show', $offer) }}" class="block">
        <div class="relative h-48">
            {{-- Image --}}
            @if($offer->media->isNotEmpty())
                <img src="/images/{{ $offer->media->first()->filename }}.{{ $offer->media->first()->extension }}" 
                     alt="{{ $offer->title }}" 
                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
            @else
                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                    <svg class="h-12 w-12 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
            {{-- Type Badge --}}
            <div class="absolute top-2 right-2">
                <span class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                    OPEN OFFER
                </span>
            </div>
        </div>
        <div class="p-4">
            {{-- Category --}}
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $offer->category->name }}</p>
            
            {{-- Title --}}
            <h3 class="mt-2 text-lg font-bold text-gray-800 line-clamp-2 h-14 group-hover:text-green-600 transition-colors">
                {{ $offer->title }}
            </h3>

            {{-- Creator Info --}}
            <div class="flex items-center mt-3 text-sm text-gray-600">
                <img src="{{ $offer->creator->profile_photo_url }}" alt="{{ $offer->creator->firstname }}" class="w-6 h-6 rounded-full mr-2">
                <span>by {{ $offer->creator->firstname }}</span>
                @if($offer->creator->verification && $offer->creator->verification->status === 'approved')
                    <span class="ml-1.5 text-green-500" title="Verified Creator">
                        <x-icons.shield-check class="h-4 w-4" />
                    </span>
                @endif
            </div>

            {{-- Meta Info --}}
            <div class="mt-4 flex items-center justify-between text-sm">
                {{-- Bids --}}
                <div class="flex items-center text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a1 1 0 011-1h14a1 1 0 110 2H3a1 1 0 01-1-1z" />
                    </svg>
                    <span>{{ $bidCount }} {{ Str::plural('Bid', $bidCount) }}</span>
                </div>
                {{-- Location --}}
                @if($offer->address)
                    <div class="flex items-center text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        <span class="truncate">{{ $offer->address->town }}</span>
                    </div>
                @endif
            </div>

            {{-- Budget --}}
            <div class="mt-4 pt-4 border-t">
                <p class="text-xs text-gray-500">Budget</p>
                <p class="text-2xl font-bold text-green-600">
                    ₱{{ number_format($offer->budget, 2) }}
                </p>
            </div>
        </div>
    </a>
</div>
