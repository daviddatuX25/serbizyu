<x-app-layout>
    <div class="min-h-screen bg-gray-50">
    <!-- Profile Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-6">
                    <!-- Profile Photo -->
                    <div class="flex-shrink-0">
                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                            class="h-32 w-32 rounded-full object-cover border-4 border-blue-500">
                    </div>

                    <!-- Profile Info -->
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                        
                        @if($user->bio)
                            <p class="mt-2 text-gray-600">{{ $user->bio }}</p>
                        @endif

                        <!-- Rating Display -->
                        <div class="mt-4 flex items-center space-x-4">
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($averageRating))
                                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @elseif($i - 0.5 <= $averageRating)
                                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <defs>
                                                    <linearGradient id="halfStar">
                                                        <stop offset="50%" stop-color="currentColor" />
                                                        <stop offset="50%" stop-color="#d1d5db" />
                                                    </linearGradient>
                                                </defs>
                                                <path fill="url(#halfStar)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="ml-2 text-lg font-semibold text-gray-900">
                                    {{ number_format($averageRating, 1) }}
                                </span>
                            </div>
                            <div class="text-gray-600">
                                <span class="font-medium">{{ $totalReviews }}</span>
                                <span>{{ Str::plural('review', $totalReviews) }}</span>
                            </div>
                        </div>

                        @if(Auth::check() && Auth::id() === $user->id)
                            <div class="mt-6">
                                <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                                    Edit Profile
                                </a>
                            </div>
                        @elseif($canReviewUser)
                            <div class="mt-6">
                                <button @click="showReviewModal = true" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Leave a Review
                                </button>
                            </div>
                        @elseif($hasExistingReview)
                            <div class="mt-6">
                                <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-600 font-medium rounded-lg">
                                    ✓ You've reviewed this user
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ showReviewModal: false }">
        <div class="space-y-8">
            <!-- Reviews Header -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Reviews Received</h2>

                @if($reviews->count() > 0)
                    <div class="space-y-6">
                        @foreach($reviews as $review)
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4">
                                        <!-- Reviewer Avatar -->
                                        <img src="{{ $review->reviewer->profile_photo_url }}" 
                                            alt="{{ $review->reviewer->name }}"
                                            class="h-12 w-12 rounded-full object-cover">
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2">
                                                <h3 class="font-semibold text-gray-900">
                                                    <a href="{{ route('profile.show', $review->reviewer) }}" 
                                                        class="hover:text-blue-600 transition">
                                                        {{ $review->reviewer->name }}
                                                    </a>
                                                </h3>
                                                
                                                <!-- Star Rating -->
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endif
                                    @endfor
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-2">
                                            @if($review->title)
                                                <h4 class="font-medium text-gray-900">{{ $review->title }}</h4>
                                            @endif
                                            <p class="mt-1 text-gray-600 text-sm">{{ $review->comment }}</p>
                                        </div>

                                        <!-- Tags -->
                                        @if($review->tags && is_array($review->tags) && count($review->tags) > 0)
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @foreach($review->tags as $tag)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $tag }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Footer -->
                                        <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                                            <span>{{ $review->created_at->format('M d, Y') }}</span>
                                            <button class="flex items-center space-x-1 hover:text-blue-600 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.646 7.23a2 2 0 01-1.789 1.106H5a2 2 0 01-2-2V8a2 2 0 012-2h6.4a2 2 0 011.368.505m0 0H9m6.4-.505L9 4" />
                                                </svg>
                                                <span>Helpful ({{ $review->helpful_count }})</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>        <!-- Review Modal -->
                @endif
        @if($canReviewUser)
            <dialog x-show="showReviewModal" @click="if($el === $event.target) showReviewModal = false"
                class="w-full max-w-2xl backdrop:bg-gray-900/50 rounded-xl shadow-2xl p-0 open:flex flex-col"
                x-transition>
                <div class="bg-white rounded-t-xl p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Review {{ $user->name }}
                        </h3>
                        <button @click="showReviewModal = false" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6 overflow-y-auto flex-1">
                    <form id="userReviewForm" class="space-y-6">
                        @csrf
                        
                        <!-- Hidden reviewee_id -->
                        <input type="hidden" name="reviewee_id" value="{{ $user->id }}">

                        <!-- Star Rating -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Rating</label>
                            <div class="flex gap-3" id="ratingSelector">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-rating="{{ $i }}"
                                        class="rating-star text-4xl transition-transform hover:scale-110 text-gray-300">
                                        ★
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" id="rating" name="rating" value="0">
                            <p class="mt-2 text-sm text-red-600 hidden" id="ratingError">Please select a rating</p>
                        </div>

                        <!-- Title -->
                        <div>
                            <label for="reviewTitle" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" id="reviewTitle" name="title" placeholder="Sum up your experience"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Comment -->
                        <div>
                            <label for="reviewComment" class="block text-sm font-medium text-gray-700">
                                Your Review
                                <span class="text-gray-500">(<span id="charCount">0</span>/500)</span>
                            </label>
                            <textarea id="reviewComment" name="comment" rows="4" maxlength="500"
                                placeholder="Share details about your experience..."
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <!-- Tags -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Tags (select up to 3)</label>
                            <div class="space-y-2">
                                @foreach(['Professional', 'Responsive', 'Reliable', 'Skilled', 'Friendly', 'Affordable', 'Punctual'] as $tag)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="tags[]" value="{{ $tag }}" 
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">{{ $tag }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-gray-50 rounded-b-xl p-6 border-t border-gray-200 flex justify-end gap-3">
                    <button @click="showReviewModal = false"
                        class="px-4 py-2 text-gray-700 font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" form="userReviewForm" id="submitReviewBtn"
                        class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                        Submit Review
                    </button>
                </div>
            </dialog>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        if (document.getElementById('userReviewForm')) {
            initializeReviewForm();
        }
    });

    function initializeReviewForm() {
        const ratingSelector = document.getElementById('ratingSelector');
        const ratingInput = document.getElementById('rating');
        const ratingStars = document.querySelectorAll('.rating-star');
        const commentField = document.getElementById('reviewComment');
        const charCount = document.getElementById('charCount');
        const form = document.getElementById('userReviewForm');
        const submitBtn = document.getElementById('submitReviewBtn');
        let selectedRating = 0;

        // Star rating interaction
        ratingStars.forEach(star => {
            star.addEventListener('click', () => {
                selectedRating = parseInt(star.dataset.rating);
                ratingInput.value = selectedRating;
                document.getElementById('ratingError').classList.add('hidden');
                updateStars(selectedRating);
            });

            star.addEventListener('mouseenter', () => {
                const hoverRating = parseInt(star.dataset.rating);
                updateStars(hoverRating, true);
            });
        });

        ratingSelector.addEventListener('mouseleave', () => {
            updateStars(selectedRating, false);
        });

        function updateStars(rating, isHover = false) {
            ratingStars.forEach(star => {
                const starRating = parseInt(star.dataset.rating);
                if (starRating <= rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.add('text-gray-300');
                    star.classList.remove('text-yellow-400');
                }
            });
        }

        // Character counter
        commentField.addEventListener('input', () => {
            charCount.textContent = commentField.value.length;
        });

        // Form submission
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (selectedRating === 0) {
                document.getElementById('ratingError').classList.remove('hidden');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            try {
                const formData = new FormData(form);
                const response = await fetch('/api/reviews/users', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                });

                if (!response.ok) {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Failed to submit review'));
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Review';
                    return;
                }

                alert('Review submitted successfully!');
                location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert('Error submitting review: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Review';
            }
        });
    }
</script>
    @endpush
</x-app-layout>