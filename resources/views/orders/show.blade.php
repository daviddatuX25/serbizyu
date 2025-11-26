<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
                            <p class="text-gray-600 mt-1">{{ $order->created_at->format('F j, Y, g:i a') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-600">Status</p>
                            <x-order-status-badge :status="$order->status" type="status" class="mt-1" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Service Details -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Service Details</h2>
                            <div class="space-y-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm text-gray-600">Service</p>
                                        <p class="text-lg font-medium text-gray-900">{{ $order->service->name }}</p>
                                    </div>
                                    <p class="text-2xl font-bold text-gray-900">${{ number_format($order->price, 2) }}</p>
                                </div>
                                @if($order->service->description)
                                    <div>
                                        <p class="text-sm text-gray-600">Description</p>
                                        <p class="text-gray-700">{{ $order->service->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700">Billing</h2>
                        <dl class="mt-2 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Price:</dt>
                                <dd class="font-medium text-gray-800">₱{{ number_format($order->price, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Platform Fee:</dt>
                                <dd class="font-medium text-gray-800">₱{{ number_format($order->platform_fee, 2) }}</dd>
                            </div>
                            <div class="flex justify-between font-bold text-base">
                                <dt class="text-gray-800">Total:</dt>
                                <dd class="text-green-600">₱{{ number_format($order->total_amount, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                    <!-- Participants -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Buyer -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-600 uppercase mb-3">Buyer</h3>
                                <div class="space-y-2">
                                    <p class="font-medium text-gray-900">{{ $order->buyer->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->buyer->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Seller -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-600 uppercase mb-3">Seller</h3>
                                <div class="space-y-2">
                                    <p class="font-medium text-gray-900">{{ $order->seller->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->seller->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Work Progress -->
                    @if($order->workInstance)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Work Progress</h2>
                                <div class="space-y-3">
                                    <div>
                                        @php
                                            $totalSteps = $order->workInstance->workInstanceSteps->count();
                                            $currentStep = $order->workInstance->current_step_index + 1;
                                            $progress = ($currentStep / $totalSteps) * 100;
                                        @endphp
                                        <p class="text-sm text-gray-600 mb-2">
                                            Progress: <span class="font-medium text-gray-900">{{ $currentStep }} / {{ $totalSteps }}</span>
                                        </p>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        Current Status: <span class="font-medium text-gray-900 capitalize">{{ $order->workInstance->status }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Payment Summary -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Summary</h3>
                            <div class="space-y-3 border-b border-gray-200 pb-4 mb-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Service Price</span>
                                    <span class="font-medium">${{ number_format($order->price, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Platform Fee (5%)</span>
                                    <span class="font-medium">${{ number_format($order->platform_fee, 2) }}</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-semibold text-gray-900">Total</span>
                                <span class="text-2xl font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Payment Status</p>
                                <x-order-status-badge :status="$order->payment_status" type="payment" class="w-full text-center" />
                            </div>

                            @if($order->payment_status === 'pending' && $order->status === 'pending')
                                <a href="{{ route('payments.checkout', $order) }}" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition text-center block">
                                    Proceed to Payment
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    @if($order->status === 'pending' && Auth::user()->id === $order->buyer_id)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white">
                                <h3 class="text-sm font-semibold text-gray-900 uppercase mb-3">Actions</h3>
                                <form action="{{ route('orders.cancel', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-700 font-medium py-2 px-4 rounded-lg transition" onclick="return confirm('Are you sure you want to cancel this order?')">
                                        Cancel Order
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Activity Log (if available) -->
                    @if(isset($order->activity))
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-900 uppercase mb-3">Recent Activity</h3>
                                <div class="space-y-2 text-sm">
                                    <p class="text-gray-600">Order created</p>
                                    <p class="text-gray-600">{{ $order->created_at->format('M d, Y \a\t g:i a') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Review Section (if order is completed) -->
                    @if($canReview)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-blue-200" x-data="{ showReviewModal: false, reviewType: null }">
                            <div class="p-6 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
                                <h3 class="text-lg font-semibold text-blue-900 mb-2">
                                    ⭐ Share Your Experience
                                </h3>
                                <p class="text-sm text-blue-700">
                                    Help others make informed decisions by leaving reviews about this service and transaction.
                                </p>
                            </div>

                            <div class="p-6 space-y-4">
                                <!-- Service Review Option -->
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Service Review</h4>
                                        <p class="text-sm text-gray-600">Review the quality and experience of the service</p>
                                        @if($hasServiceReview)
                                            <span class="inline-block mt-2 px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">✓ Reviewed</span>
                                        @endif
                                    </div>
                                    @if(!$hasServiceReview)
                                        <button @click="showReviewModal = true; reviewType = 'service'" 
                                            class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                                            Review Service
                                        </button>
                                    @else
                                        <span class="text-gray-500">Already reviewed</span>
                                    @endif
                                </div>

                                <!-- User Review Option -->
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">
                                            @if(auth()->id() === $order->buyer_id)
                                                Review {{ $order->seller->name }}
                                            @else
                                                Review {{ $order->buyer->name }}
                                            @endif
                                        </h4>
                                        <p class="text-sm text-gray-600">Share your experience working with this user</p>
                                        @if($hasUserReview)
                                            <span class="inline-block mt-2 px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">✓ Reviewed</span>
                                        @endif
                                    </div>
                                    @if(!$hasUserReview)
                                        <button @click="showReviewModal = true; reviewType = 'user'" 
                                            class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                                            Review User
                                        </button>
                                    @else
                                        <span class="text-gray-500">Already reviewed</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Review Modal -->
                        <dialog x-show="showReviewModal" @click="if($el === $event.target) showReviewModal = false"
                            class="w-full max-w-2xl backdrop:bg-gray-900/50 rounded-xl shadow-2xl p-0 open:flex flex-col"
                            x-transition>
                            <div class="bg-white rounded-t-xl p-6 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900" x-text="reviewType === 'service' ? 'Review This Service' : 'Review This User'"></h3>
                                    <button @click="showReviewModal = false" class="text-gray-400 hover:text-gray-600 transition">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="p-6 overflow-y-auto flex-1">
                                <!-- Service Review Form -->
                                <form id="serviceReviewForm" x-show="reviewType === 'service'" class="space-y-6">
                                    @csrf
                                    <input type="hidden" name="service_id" value="{{ $order->service_id }}">
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    <input type="hidden" id="serviceRating" name="rating" value="0">

                                    <!-- Star Rating -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Rating</label>
                                        <div class="flex gap-3" id="serviceRatingSelector">
                                            @for($i = 1; $i <= 5; $i++)
                                                <button type="button" data-rating="{{ $i }}"
                                                    class="service-rating-star text-4xl transition-transform hover:scale-110 text-gray-300">
                                                    ★
                                                </button>
                                            @endfor
                                        </div>
                                        <p class="mt-2 text-sm text-red-600 hidden" id="serviceRatingError">Please select a rating</p>
                                    </div>

                                    <!-- Title -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Title</label>
                                        <input type="text" name="title" placeholder="Sum up your experience"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <!-- Comment -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Your Review
                                            <span class="text-gray-500">(<span class="serviceCharCount">0</span>/500)</span>
                                        </label>
                                        <textarea name="comment" rows="4" maxlength="500"
                                            placeholder="Share details about your experience..."
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    </div>

                                    <!-- Tags -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Tags (select up to 3)</label>
                                        <div class="space-y-2">
                                            @foreach(['High Quality', 'Professional', 'Fast Delivery', 'Reliable', 'Great Communication', 'Worth It', 'Recommended'] as $tag)
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="tags[]" value="{{ $tag }}" 
                                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                    <span class="ml-2 text-sm text-gray-700">{{ $tag }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </form>

                                <!-- User Review Form -->
                                <form id="userReviewForm" x-show="reviewType === 'user'" class="space-y-6">
                                    @csrf
                                    <input type="hidden" name="reviewee_id" value="{{ auth()->id() === $order->buyer_id ? $order->seller_id : $order->buyer_id }}">
                                    <input type="hidden" id="userRating" name="rating" value="0">

                                    <!-- Star Rating -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Rating</label>
                                        <div class="flex gap-3" id="userRatingSelector">
                                            @for($i = 1; $i <= 5; $i++)
                                                <button type="button" data-rating="{{ $i }}"
                                                    class="user-rating-star text-4xl transition-transform hover:scale-110 text-gray-300">
                                                    ★
                                                </button>
                                            @endfor
                                        </div>
                                        <p class="mt-2 text-sm text-red-600 hidden" id="userRatingError">Please select a rating</p>
                                    </div>

                                    <!-- Title -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Title</label>
                                        <input type="text" name="title" placeholder="Sum up your experience"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <!-- Comment -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Your Review
                                            <span class="text-gray-500">(<span class="userCharCount">0</span>/500)</span>
                                        </label>
                                        <textarea name="comment" rows="4" maxlength="500"
                                            placeholder="Share details about your experience..."
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    </div>

                                    <!-- Tags -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Tags (select up to 3)</label>
                                        <div class="space-y-2">
                                            @foreach(['Professional', 'Responsive', 'Reliable', 'Skilled', 'Friendly', 'Punctual', 'Knowledgeable'] as $tag)
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
                                <button type="submit" class="submitReviewBtn px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition"
                                    @click.prevent="submitReview()">
                                    Submit Review
                                </button>
                            </div>
                        </dialog>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initializeOrderReviews();
        });

        function initializeOrderReviews() {
            // Service Review Star Rating
            initializeStarRating(
                'serviceRatingSelector',
                'serviceRating',
                'serviceRatingError',
                '.service-rating-star',
                '.serviceCharCount'
            );

            // User Review Star Rating
            initializeStarRating(
                'userRatingSelector',
                'userRating',
                'userRatingError',
                '.user-rating-star',
                '.userCharCount'
            );

            // Character counters
            document.querySelectorAll('#serviceReviewForm textarea').forEach(ta => {
                ta.addEventListener('input', () => {
                    document.querySelector('.serviceCharCount').textContent = ta.value.length;
                });
            });

            document.querySelectorAll('#userReviewForm textarea').forEach(ta => {
                ta.addEventListener('input', () => {
                    document.querySelector('.userCharCount').textContent = ta.value.length;
                });
            });
        }

        function initializeStarRating(selectorId, inputId, errorId, starClass, charCountClass) {
            const container = document.getElementById(selectorId);
            const input = document.getElementById(inputId);
            const errorMsg = document.getElementById(errorId);
            const stars = container.querySelectorAll(starClass);
            let selectedRating = 0;

            stars.forEach(star => {
                star.addEventListener('click', () => {
                    selectedRating = parseInt(star.dataset.rating);
                    input.value = selectedRating;
                    errorMsg.classList.add('hidden');
                    updateStars(stars, selectedRating);
                });

                star.addEventListener('mouseenter', () => {
                    const hoverRating = parseInt(star.dataset.rating);
                    updateStars(stars, hoverRating, true);
                });
            });

            container.addEventListener('mouseleave', () => {
                updateStars(stars, selectedRating, false);
            });
        }

        function updateStars(stars, rating, isHover = false) {
            stars.forEach(star => {
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

        function submitReview() {
            const modal = document.querySelector('dialog');
            const reviewType = modal._reviewType || 'service';
            const formId = reviewType === 'service' ? 'serviceReviewForm' : 'userReviewForm';
            const form = document.getElementById(formId);
            const ratingId = reviewType === 'service' ? 'serviceRating' : 'userRating';
            const ratingInput = document.getElementById(ratingId);
            const errorId = reviewType === 'service' ? 'serviceRatingError' : 'userRatingError';
            const errorMsg = document.getElementById(errorId);

            if (!ratingInput.value || ratingInput.value === '0') {
                errorMsg.classList.remove('hidden');
                return;
            }

            submitForm(form, reviewType === 'service' ? '/api/reviews/services' : '/api/reviews/users');
        }

        async function submitForm(form, endpoint) {
            const submitBtn = document.querySelector('.submitReviewBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            try {
                const formData = new FormData(form);
                const response = await fetch(endpoint, {
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
        }

        // Store review type in modal when clicking buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('review-trigger')) {
                const modal = document.querySelector('dialog');
                modal._reviewType = e.target.dataset.type;
            }
        });
    </script>
    @endpush