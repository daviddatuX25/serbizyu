<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between md:items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manage Bids
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    For your open offer: <a href="{{ route('openoffers.show', $openoffer) }}" class="text-blue-600 hover:underline">{{ $openoffer->title }}</a>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    @if ($bids->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No bids yet</h3>
                            <p class="mt-1 text-sm text-gray-500">When a service provider places a bid on your offer, it will appear here.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($bids as $bid)
                                @include('creator.bids.partials.bid-card', ['bid' => $bid])
                            @endforeach
                        </div>
                        <div class="mt-6">
                            {{ $bids->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>