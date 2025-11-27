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
                            <img src="{{ $service->creator->media()->where('tag', 'profile_image')->first()?->getUrl() ?? 'https://ui-avatars.com/api/?name=' . urlencode($service->creator->name) }}"
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
                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Reviews</h3>
                    </div>

                    @if($service->serviceReviews->count() > 0)
                        <div class="space-y-4">
                            @foreach($service->serviceReviews()->with('reviewer')->latest()->take(5)->get() as $review)
                                <x-review-item :review="$review" :showVerifiedTag="false" :showHelpful="false" />
                            @endforeach
                        </div>

                        @if($service->serviceReviews->count() > 5)
                            <div class="mt-4 text-center">
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View all {{ $service->serviceReviews->count() }} reviews</a>
                            </div>
                        @endif
                    @else
                        <p class="text-sm text-gray-500 text-center py-6">No reviews yet for this service.</p>
                    @endif
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
                                <span class="ml-1 text-sm font-medium">{{ number_format($service->serviceReviews()->avg('rating') ?? 0, 1) }}</span>
                            </span>
                            <span class="text-sm text-gray-500">({{ $service->serviceReviews()->count() }} reviews)</span>
                        </div>
                    </div>
                    <div class="px-6 pb-6 border-t">
                        <div class="flex justify-between items-baseline mt-4">
                            <span class="text-gray-600 font-medium">Service Rate</span>
                            <span class="text-2xl font-bold text-gray-900">₱{{ number_format($service->price, 2) }}</span>
                        </div>
                        <div class="mt-1 text-sm text-gray-500 text-right">
                            <div class="flex items-center justify-end space-x-1.5 text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ $service->address->full_address ?? 'Location not specified' }}</span>
                            </div>
                        </div>

                         @can('update', $service)
                            <a href="{{ route('creator.services.manage', $service) }}" class="mt-6 block w-full text-center bg-gray-200 text-gray-800 rounded-lg px-6 py-3 font-semibold hover:bg-gray-300 transition">
                                Go to Manage
                            </a>
                        @else
                            <form action="{{ route('services.checkout', $service) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="mt-6 w-full bg-green-600 text-white rounded-lg px-6 py-3 font-semibold hover:bg-green-700 transition shadow-md"
                                    @if(Auth::id() === $service->creator_id) disabled @endif>
                                    Proceed to Order
                                </button>
                            </form>
                            <button type="button"
                                @click="$dispatch('open-flag-modal', { id: {{ $service->id }}, title: '{{ $service->title }}' })"
                                class="mt-2 w-full text-center text-red-600 font-medium hover:text-red-700 hover:bg-red-50 py-2 rounded-lg transition">
                                Report This Service
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Flag Modal Components -->
<x-flag-modal contentType="Service" />
<x-flag-modal contentType="Review" />
