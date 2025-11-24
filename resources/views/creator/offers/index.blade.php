{{-- resources/views/creator/offers/index.blade.php --}}
<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Open Offers
        </h2>
    </x-slot>

    <div class="py-6">
        <a href="{{ route('creator.openoffers.create') }}" 
        class="w-[200px] px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md flex items-center space-x-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Create Open Offer</span>
    </a>

        <div class="mt-4">
            @if ($offers->count() === 0)
                <p>No open offers yet.</p>
            @else
            <div class="hidden md:block bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Budget
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Deadline
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($offers as $offer)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $offer->title }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">₱{{ number_format($offer->budget, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $offer->category?->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $offer->deadline?->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $offer->status === \App\Enums\OpenOfferStatus::OPEN ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $offer->status->value }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('openoffers.show', $offer) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('creator.openoffers.edit', $offer) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
                                            <form method="POST" action="{{ route('creator.openoffers.destroy', $offer) }}" class="inline-block ml-4">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick="return confirm('Delete this offer?')" class="text-red-600 hover:text-red-900">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="md:hidden grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach($offers as $offer)
                    <x-resource-card
                        :title="$offer->title"
                        :status="$offer->status->value"
                        :statusClass="$offer->status === \App\Enums\OpenOfferStatus::OPEN ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                        :details="[
                            'Category' => $offer->category?->name,
                            'Budget' => '₱' . number_format($offer->budget, 2),
                            'Deadline' => $offer->deadline?->format('M d, Y'),
                        ]"
                        :actions="[
                            ['label' => 'View', 'url' => route('openoffers.show', $offer), 'class' => 'bg-gray-100 hover:bg-gray-200'],
                            ['label' => 'Edit', 'url' => route('creator.openoffers.edit', $offer), 'class' => 'bg-blue-600 hover:bg-blue-700 text-white'],
                        ]"
                    />
                @endforeach
            </div>
                <div class="mt-4">
                    {{ $offers->links() }}
                </div>

            @endif
        </div>
    </div>
</x-creator-layout>
