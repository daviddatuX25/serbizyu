@props(['service'])

<article class="listing-card">
    {{-- Top Thumbnail --}}
    @if($service->media->isNotEmpty())
        @php
            $thumbnail = $service->media->where('tag', 'thumbnail')->first();
        @endphp
        @if($thumbnail)
            <img src="{{ route('media.serve', ['encryptedPath' => Crypt::encryptString(json_encode(['media_id' => $thumbnail->id]))]) }}"
                alt="{{ $service->title }} Thumbnail"
                class="w-full h-48 object-cover rounded-lg mb-2">
        @endif
    @endif


    <div class="card-top flex justify-between items-center">
        <span class="badge-service">Service</span>
        {{-- Placeholder rating --}}
        <span class="rating text-sm text-yellow-500">★★★★★</span>
    </div>

    <h3 class="card-title">{{ $service->title }}</h3>
    <p class="card-desc">{{ Str::limit($service->description, 80) }}</p>

    <p class="card-meta">Rate: ₱{{ number_format($service->price, 2) }}</p>
    <p class="card-meta">
        Location: {{ $service->address?->city ?? 'N/A' }}, {{ $service->address?->province ?? 'N/A' }}
    </p>

    <div class="card-footer flex justify-between items-center mt-2">
        <span class="text-xs text-text-secondary">Verified Servicer</span>

        <div class="card-avatar w-8 h-8 rounded-full overflow-hidden">
            <img src="{{ $service->creator?->profile_image?->getUrl() ?? 'fallback.png' }}"
                alt="{{ $service->creator?->name ?? 'User' }}"
                class="w-full h-full object-cover">
        </div>

    </div>
</article>
