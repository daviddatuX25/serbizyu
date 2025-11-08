@props(['offer'])

<article class="listing-card">
    <div class="card-top">
        <span class="badge-offer">Open Offer</span>
        <span class="text-sm text-text-secondary">Budget: ₱{{ number_format($offer->budget, 2) }}</span>
    </div>
    <h3 class="card-title">{{ $offer->title }}</h3>
    <p class="card-desc">{{ Str::limit($offer->description, 80) }}</p>
    <p class="card-meta">{{ $offer->address->city }}, {{ $offer->address->province }}</p>
    <div class="card-footer">
        <span class="text-xs text-text-secondary">Posted {{ $offer->created_at->diffForHumans() }}</span>
        <div class="card-avatar">
             @if($offer->creator->profileImage)
                <img src="{{ $offer->creator->profileImage->path }}" alt="{{ $offer->creator->name }}" class="w-full h-full object-cover">
            @else
                📝
            @endif
        </div>
    </div>
</article>
