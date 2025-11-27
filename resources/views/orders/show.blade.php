<x-creator-layout title="Order Details">
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
                    <!-- Billing -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Billing</h2>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Price:</dt>
                                    <dd class="font-medium text-gray-800">₱{{ number_format($order->price, 2) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Platform Fee:</dt>
                                    <dd class="font-medium text-gray-800">₱{{ number_format($order->platform_fee, 2) }}</dd>
                                </div>
                                <div class="flex justify-between font-bold text-base pt-2 border-t border-gray-200">
                                    <dt class="text-gray-800">Total:</dt>
                                    <dd class="text-green-600">₱{{ number_format($order->total_amount, 2) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Work Progress -->
                    @if($order->workInstance)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Work Progress</h2>
                                    <a href="{{ route('orders.work.show', $order) }}" class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">
                                        View Work Details →
                                    </a>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        @php
                                            $totalSteps = $order->workInstance->workInstanceSteps->count();
                                            $currentStep = $order->workInstance->current_step_index;
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

                    <!-- Messages -->
                    @if($order->messageThread)
                        <livewire:order-chat :order="$order" />
                    @else
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-center">
                                <p class="text-gray-600">No messages yet. Start a conversation!</p>
                            </div>
                        </div>
                    @endif

                    <!-- Review Section (when order is complete) -->
                    @if($order->status === 'completed')
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Work Complete! ✓</h2>
                                <p class="text-gray-600 mb-4">The seller has completed all work steps. Would you like to leave a review?</p>

                                @if($canReview && Auth::user()->id === $order->buyer_id)
                                    <div class="space-y-2">
                                        @if(!$hasServiceReview)
                                            <button type="button" onclick="document.getElementById('serviceReviewModal').showModal()"
                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                                                Leave Service Review
                                            </button>
                                        @else
                                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                                <p class="text-green-800 font-medium">✓ You've already reviewed this service</p>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($canReview && Auth::user()->id === $order->seller_id)
                                    @if($buyerHasLeftServiceReview)
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                            <p class="text-green-800 font-medium">✓ Buyer has reviewed this service</p>
                                        </div>
                                    @else
                                        <p class="text-gray-600 italic">Waiting for buyer to leave a review...</p>
                                    @endif
                                @else
                                    <p class="text-gray-600 italic">You're not eligible to leave a review for this order.</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 overflow-hidden shadow-sm sm:rounded-lg border border-yellow-200">
                            <div class="p-6 bg-white">
                                <h2 class="text-lg font-semibold text-yellow-900 mb-2">📋 Work In Progress</h2>
                                <p class="text-yellow-800">Review options will appear once the seller completes all work steps.</p>
                            </div>
                        </div>
                    @endif

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
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white space-y-3">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase mb-3">Actions</h3>

                            @if($order->status === 'pending' && Auth::user()->id === $order->buyer_id)
                                <form action="{{ route('orders.cancel', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-700 font-medium py-2 px-4 rounded-lg transition" onclick="return confirm('Are you sure you want to cancel this order?')">
                                        Cancel Order
                                    </button>
                                </form>
                            @endif

                            <!-- Flag Button (available to any user) -->
                            <button type="button"
                                    @click="$dispatch('open-flag-modal', { id: {{ $order->id }}, title: 'Order #{{ $order->id }}' })"
                                    class="w-full bg-orange-50 hover:bg-orange-100 text-orange-700 font-medium py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4a6 6 0 016-6h4a6 6 0 016 6v4M9 9a3 3 0 100-6 3 3 0 000 6zm6 0a3 3 0 100-6 3 3 0 000 6z"></path>
                                </svg>
                                Report Issue
                            </button>
                        </div>
                    </div>

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
                </div>
            </div>
        </div>
    </div>
</x-creator-layout>

<!-- Service Review Modal -->
<dialog id="serviceReviewModal" class="backdrop:bg-gray-500/75 rounded-lg shadow-xl w-full max-w-md p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-900">Review the Service</h2>
        <button type="button" onclick="document.getElementById('serviceReviewModal').close()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <form id="serviceReviewForm" method="POST" action="{{ route('api.service-reviews.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="service_id" value="{{ $order->service_id }}">
        <input type="hidden" name="order_id" value="{{ $order->id }}">

        <!-- Rating -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
            <div class="flex space-x-2" id="serviceRatingSelector">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="service-rating-star text-3xl text-gray-300 hover:text-yellow-400 transition" data-rating="{{ $i }}">
                        ★
                    </button>
                @endfor
            </div>
            <input type="hidden" name="rating" id="serviceRating" value="0">
            <span id="serviceRatingError" class="text-xs text-red-600 hidden mt-1 block">Please select a rating</span>
        </div>

        <!-- Title -->
        <div>
            <label for="serviceTitle" class="block text-sm font-medium text-gray-700 mb-1">Review Title (Optional)</label>
            <input type="text" name="title" id="serviceTitle" maxlength="255"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Summarize your experience">
        </div>

        <!-- Comment -->
        <div>
            <label for="serviceComment" class="block text-sm font-medium text-gray-700 mb-1">Your Review</label>
            <textarea name="comment" id="serviceComment" rows="4" maxlength="2000" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Share your experience with this service..."></textarea>
            <p class="text-xs text-gray-500 mt-1"><span class="serviceCharCount">0</span>/2000</p>
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
            <button type="button" onclick="document.getElementById('serviceReviewModal').close()"
                class="flex-1 px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">
                Cancel
            </button>
            <button type="submit" class="submitReviewBtn flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                Submit Review
            </button>
        </div>
    </form>
</dialog>

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
        '.service-rating-star'
    );

    // Service character counter
    const serviceComment = document.getElementById('serviceComment');
    if (serviceComment) {
        serviceComment.addEventListener('input', () => {
            document.querySelector('.serviceCharCount').textContent = serviceComment.value.length;
        });
    }
}

function initializeStarRating(selectorId, inputId, errorId, starClass) {
    const container = document.getElementById(selectorId);
    if (!container) return;

    const input = document.getElementById(inputId);
    const errorMsg = document.getElementById(errorId);
    const stars = container.querySelectorAll(starClass);
    let selectedRating = 0;

    stars.forEach(star => {
        star.addEventListener('click', (e) => {
            e.preventDefault();
            selectedRating = parseInt(star.dataset.rating);
            input.value = selectedRating;
            if (errorMsg) errorMsg.classList.add('hidden');
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

// Handle form submission
document.getElementById('serviceReviewForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const ratingInput = document.getElementById('serviceRating');
    const errorMsg = document.getElementById('serviceRatingError');

    if (!ratingInput.value || ratingInput.value === '0') {
        if (errorMsg) errorMsg.classList.remove('hidden');
        return;
    }

    await submitForm(e.target, '{{ route("api.service-reviews.store") }}');
});

async function submitForm(form, endpoint) {
    const submitBtn = form.querySelector('.submitReviewBtn');
    submitBtn.disabled = true;
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Submitting...';

    try {
        const formData = new FormData(form);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
            credentials: 'same-origin'
        });

        if (!response.ok) {
            const error = await response.json();
            alert('Error: ' + (error.message || 'Failed to submit review'));
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            return;
        }

        alert('Review submitted successfully!');
        // Close the modal
        const modal = form.closest('dialog');
        if (modal) modal.close();
        location.reload();
    } catch (error) {
        console.error('Error:', error);
        alert('Error submitting review: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}
</script>

<!-- Flag Modal Component -->
<x-flag-modal contentType="Order" />
