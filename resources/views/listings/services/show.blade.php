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
                                            <img src="/images/{{ $media->filename }}.{{ $media->extension }}" alt="{{ $service->title }}" 
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
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Reviews</h3>
                        @auth
                            @if(Auth::id() !== $service->creator_id)
                                <button type="button" onclick="document.getElementById('reviewFormModal').showModal()" 
                                    class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">
                                    Write a Review
                                </button>
                            @endif
                        @endauth
                    </div>

                    @if($service->serviceReviews->count() > 0)
                        <div class="space-y-4">
                            @foreach($service->serviceReviews()->with('reviewer')->latest()->take(5)->get() as $review)
                                <div class="border-b pb-4 last:border-b-0">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center">
                                            <img src="{{ $review->reviewer->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->reviewer->name) }}" 
                                                alt="{{ $review->reviewer->name }}" 
                                                class="w-8 h-8 rounded-full mr-3 object-cover">
                                            <div>
                                                <span class="font-semibold text-sm text-gray-800">{{ $review->reviewer->name }}</span>
                                                @if($review->is_verified_purchase)
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        ✓ Verified Purchase
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-1 text-yellow-400">
                                            @for ($i = 0; $i < $review->rating; $i++)
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                            @endfor
                                            @for ($i = $review->rating; $i < 5; $i++)
                                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                            @endfor
                                        </div>
                                    </div>
                                    @if($review->title)
                                        <p class="font-semibold text-sm text-gray-900 pl-11 mb-1">{{ $review->title }}</p>
                                    @endif
                                    <p class="text-sm text-gray-600 pl-11">{{ $review->comment }}</p>
                                    <div class="flex justify-between items-center pl-11 mt-2">
                                        <p class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                                        @if($review->helpful_count > 0)
                                            <span class="text-xs text-gray-500">👍 {{ $review->helpful_count }} found this helpful</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($service->serviceReviews->count() > 5)
                            <div class="mt-4 text-center">
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View all {{ $service->serviceReviews->count() }} reviews</a>
                            </div>
                        @endif
                    @else
                        <p class="text-sm text-gray-500 text-center py-6">No reviews yet for this service. Be the first to review!</p>
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
                            Location: {{ $service->address->town }}
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
                                class="mt-2 w-full text-center text-gray-600 font-medium hover:text-green-700 transition">
                                Add to wishlist
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Form Modal -->
    @auth
        @if(Auth::id() !== $service->creator_id)
            <dialog id="reviewFormModal" class="backdrop:bg-gray-500/75 rounded-lg shadow-xl w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Write a Review</h2>
                    <button type="button" onclick="document.getElementById('reviewFormModal').close()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form id="reviewForm" method="POST" action="{{ route('api.service-reviews.store') }}" class="space-y-4">
                    @csrf

                    <input type="hidden" name="service_id" value="{{ $service->id }}">

                    <!-- Rating -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="flex space-x-2" id="ratingStars">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="rating-star text-3xl text-gray-300 hover:text-yellow-400 transition" data-rating="{{ $i }}">
                                    ★
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="0">
                        <span id="ratingError" class="text-xs text-red-600 hidden mt-1 block">Please select a rating</span>
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Review Title (Optional)</label>
                        <input type="text" name="title" id="title" maxlength="255" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Summarize your experience">
                    </div>

                    <!-- Comment -->
                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Your Review</label>
                        <textarea name="comment" id="comment" rows="4" maxlength="2000" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Share your experience with this service..."></textarea>
                        <p class="text-xs text-gray-500 mt-1"><span id="charCount">0</span>/2000</p>
                    </div>

                    <!-- Tags -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags (Optional)</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['Professional', 'Fast', 'Quality', 'Friendly', 'Reliable'] as $tag)
                                <label class="flex items-center">
                                    <input type="checkbox" name="tags[]" value="{{ $tag }}" 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $tag }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 pt-4">
                        <button type="button" onclick="document.getElementById('reviewFormModal').close()"
                            class="flex-1 px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Submit Review
                        </button>
                    </div>
                </form>
            </dialog>

            <script>
                const ratingStars = document.querySelectorAll('.rating-star');
                const ratingInput = document.getElementById('ratingInput');
                const ratingError = document.getElementById('ratingError');
                let currentRating = 0;

                ratingStars.forEach(star => {
                    star.addEventListener('click', function() {
                        currentRating = this.dataset.rating;
                        ratingInput.value = currentRating;
                        ratingError.classList.add('hidden');
                        updateStarDisplay();
                    });

                    star.addEventListener('mouseover', function() {
                        const hoverRating = this.dataset.rating;
                        ratingStars.forEach((s, index) => {
                            if(index < hoverRating) {
                                s.classList.add('text-yellow-400');
                                s.classList.remove('text-gray-300');
                            } else {
                                s.classList.remove('text-yellow-400');
                                s.classList.add('text-gray-300');
                            }
                        });
                    });
                });

                document.getElementById('ratingStars').addEventListener('mouseleave', updateStarDisplay);

                function updateStarDisplay() {
                    ratingStars.forEach((s, index) => {
                        if(index < currentRating) {
                            s.classList.add('text-yellow-400');
                            s.classList.remove('text-gray-300');
                        } else {
                            s.classList.remove('text-yellow-400');
                            s.classList.add('text-gray-300');
                        }
                    });
                }

                // Character count
                document.getElementById('comment').addEventListener('input', function() {
                    document.getElementById('charCount').textContent = this.value.length;
                });

                // Form submission
                document.getElementById('reviewForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    if(!ratingInput.value) {
                        ratingError.classList.remove('hidden');
                        return;
                    }

                    const formData = new FormData(this);
                    const data = Object.fromEntries(formData);
                    data.tags = formData.getAll('tags[]');

                    fetch('{{ route("api.service-reviews.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if(result.success) {
                            alert('Review submitted successfully!');
                            document.getElementById('reviewFormModal').close();
                            location.reload();
                        } else {
                            alert('Error: ' + (result.message || 'Failed to submit review'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
                });
            </script>
        @endif
    @endauth
</x-app-layout>