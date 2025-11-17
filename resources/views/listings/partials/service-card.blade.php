{{-- resources/views/listings/partials/service-card.blade.php --}}
<a href="{{ route('services.show', $service) }}" 
    class="block bg-white rounded-2xl border border-gray-300 shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
    
    <!-- Service Image -->
    <div class="relative h-48 bg-gray-200 overflow-hidden">
        @if($service->media->isNotEmpty())
            <img src="{{ $service->media->first()->getUrl() }}" 
                alt="{{ $service->title }}" 
                class="w-full h-full object-cover">
        @else
            <div class="flex items-center justify-center h-full">
                <svg class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif
        
        <!-- Category Badge -->
        <div class="absolute top-2 left-2">
            <span class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full shadow">
                {{ $service->category->name ?? 'Service' }}
            </span>
        </div>

        <!-- Pay First Badge -->
        @if($service->pay_first)
            <div class="absolute top-2 right-2">
                <span class="px-3 py-1 bg-green-600 text-white text-xs font-semibold rounded-full shadow">
                    Pay First
                </span>
            </div>
        @endif
    </div>

    <!-- Service Details -->
    <div class="p-4 space-y-2">
        <h3 class="text-lg font-bold text-gray-800 line-clamp-2">{{ $service->title }}</h3>
        
        <!-- Rating -->
        <div class="flex items-center space-x-2">
            <span class="text-yellow-400 text-sm">★★★★★</span>
            <span class="text-xs text-gray-500">(0)</span>
        </div>

        <!-- Creator Info -->
        <div class="flex items-center space-x-2 pt-1">
            <img src="{{ $service->creator->profile_photo_url }}" 
                alt="{{ $service->creator->name }}" 
                class="w-8 h-8 rounded-full">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-700 truncate">{{ $service->creator->name }}</p>
            </div>
        </div>

        <!-- Location -->
        <div class="flex items-center text-sm text-gray-600">
            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="truncate">{{ $service->address->town }}, {{ $service->address->province }}</span>
        </div>

        <!-- Price -->
        <div class="pt-2 border-t flex justify-between items-center">
            <div>
                <span class="text-xs text-gray-500">Starting at</span>
                <p class="text-xl font-bold text-gray-800">${{ number_format($service->price, 2) }}</p>
            </div>
            <button type="button" 
                onclick="event.preventDefault(); event.stopPropagation(); addToWishlist({{ $service->id }})"
                class="p-2 hover:bg-gray-100 rounded-full transition">
                <svg class="w-6 h-6 text-gray-400 hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </button>
        </div>
    </div>
</a>

@once
@push('scripts')
<script>
    function addToWishlist(serviceId) {
        // Implement wishlist functionality
        console.log('Adding service', serviceId, 'to wishlist');
        // You can make an AJAX call here
    }
</script>
@endpush
@endonce