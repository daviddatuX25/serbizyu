{{-- resources/views/creator/offers/index.blade.php --}}
<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Open Offers
        </h2>
    </x-slot>

    <div class="py-6">
        <a href="{{ route('creator.openoffers.create') }}" class="btn-primary">
            Create Open Offer
        </a>

        <div class="mt-4">
            @if ($offers->count() === 0)
                <p>No open offers yet.</p>
            @else
                <table class="table-auto w-full border">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border">Title</th>
                            <th class="px-4 py-2 border">Budget</th>
                            <th class="px-4 py-2 border">Category</th>
                            <th class="px-4 py-2 border">Deadline</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($offers as $offer)
                            <tr>
                                <td class="border px-4 py-2">{{ $offer->title }}</td>
                                <td class="border px-4 py-2">₱{{ number_format($offer->budget, 2) }}</td>
                                <td class="border px-4 py-2">{{ $offer->category?->name }}</td>
                                <td class="border px-4 py-2">{{ $offer->deadline?->format('M d, Y') }}</td>
                                <td class="border px-4 py-2">
                                    <a href="{{ route('openoffers.show', $offer) }}" class="text-blue-600">View</a>
                                    |
                                    <a href="{{ route('creator.openoffers.edit', $offer) }}" class="text-yellow-600">Edit</a>
                                    |
                                    <form method="POST" action="{{ route('creator.openoffers.destroy', $offer) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Delete this offer?')" class="text-red-600">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $offers->links() }}
                </div>

            @endif
        </div>
    </div>
</x-creator-layout>
