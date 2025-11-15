{{-- resources/views/creator/offers/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ $offer->title }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">

        <div class="mb-4">
            <strong>Budget:</strong> ₱{{ number_format($offer->budget, 2) }}
        </div>

        <div class="mb-4">
            <strong>Category:</strong> {{ $offer->category?->name }}
        </div>

        @if ($offer->workflow_template_id)
            <div class="mb-4">
                <strong>Workflow:</strong> {{ $offer->workflowTemplate->name }}
            </div>
        @endif

        @if ($offer->address_id)
            <div class="mb-4">
                <strong>Address:</strong> ID {{ $offer->address_id }}
            </div>
        @endif

        <div class="mb-4">
            <strong>Description:</strong>
            <p>{{ $offer->description }}</p>
        </div>

        <div class="mb-4">
            <strong>Images:</strong>
            <div class="flex flex-wrap gap-3 mt-2">
                @foreach ($offer->getMedia('gallery') as $media)
                    <img src="{{ route('media.serve', ['payload' => encrypt(json_encode(['media_id' => $media->id]))]) }}"
                         class="w-32 h-32 object-cover border rounded" />
                @endforeach
            </div>
        </div>

        {{-- Placeholder for future bidding modal --}}
        <div class="mt-6">
            <p class="text-gray-500 italic">Bidding features will appear here…</p>
        </div>

    </div>
</x-app-layout>
