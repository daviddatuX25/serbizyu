<x-app-layout :jsFiles="['app.js', 'swiper-listings.js']">
    <div class="container mx-auto px-4 py-6 max-w-lg md:max-w-4xl">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-md overflow-hidden">
            <!-- Header (Mobile Only) -->
            <header class="flex justify-between items-center p-4 border-b md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </header>

            <!-- Image Carousel -->
            <div class="relative h-48 md:h-80 bg-gray-200">
                @if($service->media->isNotEmpty())
                    <div class="swiper serviceSwiper h-full">
                        <div class="swiper-wrapper">
                            @foreach($service->media as $media)
                                <div class="swiper-slide">
                                    <img src="{{ $media->getUrl() }}" alt="{{ $service->title }}" 
                                        class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                        <!-- Navigation arrows -->
                        <div class="swiper-button-prev text-white"></div>
                        <div class="swiper-button-next text-white"></div>
                        <!-- Pagination -->
                        <div class="swiper-pagination"></div>
                    </div>
                @else
                    <div class="flex items-center justify-center h-full">
                        <span class="text-gray-400 text-lg">No images available</span>
                    </div>
                @endif
            </div>

            <!-- Service Details -->
            <div class="p-4 space-y-3">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">{{ $service->title }}</h2>
                
                <!-- Rating -->
                <div class="flex items-center space-x-2">
                    <span class="text-yellow-400 text-lg">★★★★★</span>
                    <span class="text-sm text-gray-600">(0 reviews)</span>
                </div>

                <!-- Service Info -->
                <div class="text-sm md:text-base text-gray-700 space-y-2">
                    <p><strong>Rate:</strong> ${{ number_format($service->price, 2) }}/hr</p>
                    <p><strong>Location:</strong> {{ $service->address->town }}, {{ $service->address->province }}</p>
                    
                    <!-- Workflow - Collapsible -->
                    <div x-data="{ expanded: false }">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <strong>Schedule:</strong>
                                <span x-show="!expanded" class="ml-1">
                                    {{ $service->workflowTemplate->workTemplates->pluck('name')->implode(' > ') }}
                                </span>
                            </div>
                            @if($service->workflowTemplate->workTemplates->count() > 3)
                                <button @click="expanded = !expanded" class="text-blue-600 text-xs ml-2 flex-shrink-0">
                                    <span x-show="!expanded">Show more</span>
                                    <span x-show="expanded" x-cloak>Show less</span>
                                </button>
                            @endif
                        </div>
                        
                        <!-- Expanded list view -->
                        <div x-show="expanded" x-cloak class="mt-2 space-y-1 pl-4">
                            @foreach($service->workflowTemplate->workTemplates as $index => $step)
                                <p class="text-sm">{{ $index + 1 }}. {{ $step->name }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($service->description)
                    <div class="pt-3 border-t">
                        <h3 class="font-semibold text-gray-800 mb-2">About this service</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $service->description }}</p>
                    </div>
                @endif

                <!-- Creator Info -->
                <div class="pt-3 border-t">
                    <h3 class="font-semibold text-gray-800 mb-3">Service by</h3>
                    <div class="flex items-center space-x-3">
                        <img src="{{ $service->creator->profile_photo_url }}" 
                            alt="{{ $service->creator->name }}" 
                            class="w-12 h-12 rounded-full">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $service->creator->name }}</h4>
                            <p class="text-xs text-gray-500">Member since {{ $service->creator->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="pt-3 border-t">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Reviews</h3>
                    <div class="space-y-3">
                        <!-- Example review -->
                        <div class="border rounded-lg p-3 bg-gray-50">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-semibold text-sm text-gray-800">David Datu Sarmiento</span>
                                <span class="text-yellow-400 text-xs">★★★★★</span>
                            </div>
                            <p class="text-sm text-gray-600">
                                I found that transacting with serbisyo makes my work easier
                            </p>
                        </div>
                        
                        <!-- No reviews state -->
                        <p class="text-sm text-gray-500 text-center py-4">No reviews yet. Be the first to review!</p>
                    </div>
                </div>
            </div>

            {{-- change this actions if its the owner --}}
            
            <!-- Footer Actions -->
            <footer class="flex flex-col sm:flex-row justify-between items-center gap-3 p-4 border-t bg-gray-50">
                @can('update', $service)
                    <a href="{{ route('creator.services.manage', $service) }}" class="w-full sm:w-auto text-blue-600 font-medium hover:text-blue-700 transition order-2 sm:order-1">
                        Go to Manage
                    </a>
                @else
                    <button type="button" 
                        class="w-full sm:w-auto text-blue-600 font-medium hover:text-blue-700 transition order-2 sm:order-1">
                        Add to wishlist
                    </button>
                    <form action="{{ route('orders.store') }}" method="POST" class="w-full sm:w-auto order-1 sm:order-2">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <button type="submit" 
                            class="w-full sm:w-auto bg-blue-600 text-white rounded-lg px-6 py-3 font-semibold hover:bg-blue-700 transition shadow-md">
                            Proceed to Order
                        </button>
                    </form>
                @endcan
              
            </footer>
        </div>
    </div>
</x-app-layout>