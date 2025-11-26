<div class="bg-white rounded-lg shadow">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Reviews</h3>

        {{-- Review Statistics --}}
        @if(isset($reviewStats) && $reviewStats['total_reviews'] > 0)
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($reviewStats['average_rating'], 1) }}</div>
                        <div class="text-xs text-gray-600 mt-1">Average Rating</div>
                    </div>
                    <div class="text-center border-l border-r border-gray-300">
                        <div class="text-2xl font-bold text-blue-600">{{ $reviewStats['total_reviews'] }}</div>
                        <div class="text-xs text-gray-600 mt-1">Total Reviews</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $reviewStats['verified_reviews'] }}</div>
                        <div class="text-xs text-gray-600 mt-1">Verified</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="space-y-4">
            @forelse($reviews as $review)
                <x-review-item :review="$review" :showVerifiedTag="false" :showHelpful="false" />
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No reviews yet</h3>
                    <p class="mt-1 text-sm text-gray-500">When a customer leaves a review, it will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
