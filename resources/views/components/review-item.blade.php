@props(['review', 'showVerifiedTag' => false, 'showHelpful' => false])

<div class="border-b pb-4 last:border-b-0">
    <div class="flex justify-between items-start mb-2">
        <div class="flex items-center">
            <img src="{{ $review->reviewer->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->reviewer->firstname . ' ' . $review->reviewer->lastname) }}"
                alt="{{ $review->reviewer->firstname }} {{ $review->reviewer->lastname }}"
                class="w-8 h-8 rounded-full mr-3 object-cover">
            <div>
                <span class="font-semibold text-sm text-gray-800">{{ $review->reviewer->firstname }} {{ $review->reviewer->lastname }}</span>
                @if($showVerifiedTag && $review->is_verified_purchase)
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
    </div>
</div>
