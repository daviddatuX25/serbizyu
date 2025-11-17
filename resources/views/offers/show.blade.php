<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $openOffer->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Description</h3>
                        <p>{{ $openOffer->description }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Budget</h3>
                        <p>${{ number_format($openOffer->budget, 2) }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Category</h3>
                        <p>{{ $openOffer->category->name }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Status</h3>
                        <p>{{ ucfirst($openOffer->status->value) }}</p> {{-- Access enum value --}}
                    </div>

                    @if ($openOffer->deadline)
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Deadline</h3>
                            <p>{{ $openOffer->deadline->format('M d, Y') }}</p>
                        </div>
                    @endif

                    @if ($openOffer->workflowTemplate)
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Workflow Template</h3>
                            <p>{{ $openOffer->workflowTemplate->name }}</p>
                        </div>
                    @endif

                    @if ($openOffer->address)
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Address</h3>
                            <p>{{ $openOffer->address->full_address }}</p>
                        </div>
                    @endif

                    @if ($openOffer->getMedia('images')->count() > 0)
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Images</h3>
                            <div class="grid grid-cols-3 gap-4 mt-2">
                                @foreach ($openOffer->getMedia('images') as $media)
                                    <img src="{{ route('media.serve', ['payload' => encrypt(json_encode(['media_id' => $media->id]))]) }}" alt="{{ $media->alt }}" class="w-full h-32 object-cover rounded-lg shadow-md">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 flex space-x-4">
                        @can('update', $openOffer)
                            <a href="{{ route('creator.offers.edit', $openOffer) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Edit Offer
                            </a>
                        @endcan

                        @can('close', $openOffer)
                            @if ($openOffer->status === \App\Enums\OpenOfferStatus::OPEN) {{-- Check enum value --}}
                                <form action="{{ route('creator.offers.close', $openOffer) }}" method="POST" onsubmit="return confirm('Are you sure you want to close this offer? This cannot be undone.');">
                                    @csrf
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Close Offer
                                    </button>
                                </form>
                            @endif
                        @endcan

                        @can('delete', $openOffer)
                            <form action="{{ route('creator.offers.destroy', $openOffer) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this offer? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Delete Offer
                                </button>
                            </form>
                        @endcan
                    </div>

                    @can('create', [App\Domains\Listings\Models\OpenOfferBid::class, $openOffer])
                        <div class="mt-8">
                            <livewire:bid-form :openOffer="$openOffer" />
                        </div>
                    @endcan

                    <div class="mt-8">
                        <livewire:bid-list :openOffer="$openOffer" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
