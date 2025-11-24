<x-app-layout :jsFiles="['app.js', 'swiper-listings.js']">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="lg:grid lg:grid-cols-3 lg:gap-8 max-w-7xl mx-auto">
            
            {{-- Left Column: Image Gallery and Creator Info --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Image Carousel -->
                    <div class="relative h-64 md:h-96 bg-gray-200">
                        @if($service->media->isNotEmpty())
                            <div class="swiper serviceSwiper h-full">
                                <div class="swiper-wrapper">
                                    @foreach($service->media as $media)
                                        <div class="swiper-slide bg-gray-100 flex items-center justify-center">
                                            <img src="{{ $media->getUrl() }}" alt="{{ $service->title }}" 
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

                <!-- Description & Details (Desktop) -->
                <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
                    <div>
                        <h3 class="font-bold text-xl text-gray-800 mb-3">About this service</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $service->description ?? 'No description provided.' }}</p>
                    </div>

                     <!-- Workflow -->
                    @include('listings.partials.workflow-steps', ['workflowTemplate' => $service->workflowTemplate])

                    <!-- Creator Info -->
                    <div class="pt-4 border-t">
                        <h3 class="font-bold text-xl text-gray-800 mb-4">About the provider</h3>
                        <div class="flex items-center space-x-4">
                            <img src="{{ $service->creator->profile_photo_url }}" 
                                alt="{{ $service->creator->name }}" 
                                class="w-16 h-16 rounded-full">
                            <div>
                                <h4 class="font-semibold text-lg text-gray-900">{{ $service->creator->name }}</h4>
                                <p class="text-sm text-gray-500">Member since {{ $service->creator->created_at->format('M Y') }}</p>
                                {{-- Add rating here if available --}}
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- Reviews Section -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Reviews</h3>
                    <div class="space-y-4">
                        {{-- Example review --}}
                        <div class="border-b pb-4">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center">
                                    <img src="https://i.pravatar.cc/150?u=david" alt="David" class="w-8 h-8 rounded-full mr-3">
                                    <span class="font-semibold text-sm text-gray-800">David Datu Sarmiento</span>
                                </div>
                                <div class="flex items-center space-x-1 text-yellow-400">
                                    @for ($i = 0; $i < 5; $i++)
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 pl-11">
                                I found that transacting with serbisyo makes my work easier
                            </p>
                        </div>
                        
                        {{-- No reviews state --}}
                        <p class="text-sm text-gray-500 text-center py-6">No reviews yet for this service.</p>
                    </div>
                </div>
            </div>

            {{-- Right Column: Action Card --}}
            <div class="lg:col-span-1">
                <div class="sticky top-20 bg-white rounded-lg shadow-lg border">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $service->title }}</h2>
                        <div class="flex items-center space-x-2 mt-2">
                            <span class="text-yellow-400 flex items-center">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                <span class="ml-1 text-sm font-medium">5.0</span>
                            </span>
                            <span class="text-sm text-gray-500">(0 reviews)</span>
                        </div>
                    </div>
                    <div class="px-6 pb-6 border-t">
                        <div class="flex justify-between items-baseline mt-4">
                            <span class="text-gray-600 font-medium">Service Rate</span>
                            <span class="text-2xl font-bold text-gray-900">₱{{ number_format($service->price, 2) }}</span>
                        </div>
                        <div class="mt-1 text-sm text-gray-500 text-right">
                            Location: {{ $service->address->town }}
                        </div>

                         @can('update', $service)
                            <a href="{{ route('creator.services.manage', $service) }}" class="mt-6 block w-full text-center bg-gray-200 text-gray-800 rounded-lg px-6 py-3 font-semibold hover:bg-gray-300 transition">
                                Go to Manage
                            </a>
                        @else
                            <button type="button" 
                                class="mt-6 w-full bg-green-600 text-white rounded-lg px-6 py-3 font-semibold hover:bg-green-700 transition shadow-md">
                                Proceed to Order
                            </button>
                            <button type="button" 
                                class="mt-2 w-full text-center text-gray-600 font-medium hover:text-green-700 transition">
                                Add to wishlist
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>