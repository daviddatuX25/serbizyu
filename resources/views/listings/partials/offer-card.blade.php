@props(['offer'])

<article class="listing-card">
    {{-- Top Thumbnail --}}
    @if($offer->media->isNotEmpty())
        @php
            $thumbnail = $offer->media->where('tag', 'thumbnail')->first();
        @endphp
        @if($thumbnail)
            <img src="{{ route('media.serve', ['encryptedPath' => Crypt::encryptString(json_encode(['media_id' => $thumbnail->id]))]) }}"
                alt="{{ $offer->title }} Thumbnail"
                class="w-full h-48 object-cover rounded-lg mb-2">
        @endif
    @endif


    <div class="card-top flex justify-between items-center">
        <span class="badge-offer">Open Offer</span>
        <span class="text-sm text-text-secondary">Budget: ₱{{ number_format($offer->budget, 2) }}</span>
    </div>

    <h3 class="card-title">{{ $offer->title }}</h3>
    <p class="card-desc">{{ Str::limit($offer->description, 80) }}</p>

    <p class="card-meta">
        Location: {{ $offer->address?->city ?? 'N/A' }}, {{ $offer->address?->province ?? 'N/A' }}
    </p>

    <div class="card-footer flex justify-between items-center mt-2">
        <span class="text-xs text-text-secondary">Posted {{ $offer->created_at->diffForHumans() }}</span>

        <div class="card-avatar w-8 h-8 rounded-full overflow-hidden">
            <img src="{{ $offer->creator?->profile_image?->getUrl() ?? 'fallback.png' }}"
                alt="{{ $offer->creator?->name ?? 'User' }}"
                class="w-full h-full object-cover">
        </div>
    </div>
</article>
