{{-- resources/views/listings/partials/service-card.blade.php --}}
<div class="w-full bg-white border border-gray-200 rounded-xl shadow-lg">
    <a href="{{ route('services.show', $service) }}">
        <div class="relative h-48 overflow-hidden rounded-t-xl">
            @if($service->media->isNotEmpty())
                <img src="{{ $service->media->first()->getUrl() }}" 
                     alt="{{ $service->title }}" 
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                    <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
            @endif
        </div>
    </a>
    <div class="p-4">
        <!-- Creator & Category -->
        <div class="flex justify-between items-center mb-2">
            <div class="flex items-center space-x-2">
                <img src="{{ $service->creator->profile_photo_url }}" alt="{{ $service->creator->name }}" class="w-8 h-8 rounded-full">
                <span class="text-sm font-medium text-gray-800">{{ $service->creator->name }}</span>
            </div>
            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                {{ $service->category->name ?? 'Service' }}
            </span>
        </div>

        <!-- Title -->
        <a href="{{ route('services.show', $service) }}">
            <h5 class="text-lg font-semibold tracking-tight text-gray-900 line-clamp-2 h-14">{{ $service->title }}</h5>
        </a>

        <!-- Rating -->
        <div class="flex items-center space-x-1 my-2">
            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z"/></svg>
            {{-- Repeat for rating --}}
            <span class="text-sm text-gray-500 ml-1">(0 Reviews)</span>
        </div>

        <!-- Price & Location -->
        <div class="flex items-center justify-between pt-3 border-t border-gray-200">
            <div>
                <span class="text-xs text-gray-600">Starting at</span>
                <p class="text-xl font-semibold text-gray-900">${{ number_format($service->price, 2) }}</p>
            </div>
            <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span class="truncate">{{ $service->address->town }}</span>
            </div>
        </div>
    </div>
</div>