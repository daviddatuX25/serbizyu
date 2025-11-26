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
