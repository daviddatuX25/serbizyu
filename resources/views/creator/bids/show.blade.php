<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bid Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Open Offer</h3>
                        <a href="{{ route('openoffers.show', $bid->openOffer) }}" class="text-blue-600 hover:underline">
                            {{ $bid->openOffer->title }}
                        </a>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Amount</h3>
                        <p>${{ number_format($bid->amount, 2) }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Message</h3>
                        <p>{{ $bid->message }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Status</h3>
                        <p>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @switch($bid->status)
                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                    @case('accepted') bg-green-100 text-green-800 @break
                                    @case('rejected') bg-red-100 text-red-800 @break
                                @endswitch">
                                {{ ucfirst($bid->status) }}
                            </span>
                        </p>
                    </div>

                    <div class="mt-6">
                        @can('update', $bid)
                            <a href="{{ route('creator.bids.edit', $bid) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                                Edit Bid
                            </a>
                        @endcan
                        <a href="{{ route('creator.bids.index') }}" class="ml-4 text-gray-600 hover:text-gray-900">
                            Back to My Bids
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


