@props(['service'])

<article class="listing-card">
    <div class="card-top">
        <span class="badge-service">Service</span>
        <span class="rating">★★★★★</span> {{-- Placeholder --}}
    </div>
    <h3 class="card-title">{{ $service->title }}</h3>
    <p class="card-desc">{{ Str::limit($service->description, 80) }}</p>
    <p class="card-meta">Rate: ₱{{ number_format($service->price, 2) }}</p>
    <p class="card-meta">Location: {{ $service->address->city }}, {{ $service->address->province }}</p>
    <div class="card-footer">
        <span class="text-xs text-text-secondary">Verified Servicer</span> {{-- Placeholder --}}
        <div class="card-avatar">
            @if($service->creator->profileImage)
                <img src="{{ $service->creator->profileImage->path }}" alt="{{ $service->creator->name }}" class="w-full h-full object-cover">
            @else
                👤
            @endif
        </div>
    </div>
</article>
