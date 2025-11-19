<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Offer: {{ $offer->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Description</h3>
                        <p>{{ $offer->description }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Budget</h3>
                        <p>${{ number_format($offer->budget, 2) }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Category</h3>
                        <p>{{ $offer->category->name }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Status</h3>
                        <p>{{ ucfirst($offer->status->value) }}</p>
                    </div>

                    @if ($offer->deadline)
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Deadline</h3>
                            <p>{{ $offer->deadline->format('M d, Y') }}</p>
                        </div>
                    @endif

                    @if ($offer->getMedia('images')->count() > 0)
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Images</h3>
                            <div class="grid grid-cols-3 gap-4 mt-2">
                                @foreach ($offer->getMedia('images') as $media)
                                    <img src="{{ route('media.serve', ['payload' => encrypt(json_encode(['media_id' => $media->id]))]) }}" alt="{{ $media->alt }}" class="w-full h-32 object-cover rounded-lg shadow-md">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 flex space-x-4 border-b pb-6">
                        <a href="{{ route('creator.openoffers.edit', $offer) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Edit Offer
                        </a>
                        @if ($offer->status === \App\Enums\OpenOfferStatus::OPEN)
                            <form action="{{ route('creator.openoffers.close', $offer) }}" method="POST" onsubmit="return confirm('Are you sure you want to close this offer? This cannot be undone.');">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Close Offer
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('creator.openoffers.destroy', $offer) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this offer? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Delete Offer
                            </button>
                        </form>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-2xl font-semibold mb-4">Bids Received</h3>
                        <livewire:bid-list :openOffer="$offer" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

