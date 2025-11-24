<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Simulate Accepted Bid') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Simulating Accepted Bid #{{ $bid->id }}</h3>
                    <p class="mb-4">This page simulates a scenario where your bid has been accepted by the seller. You can now proceed to create an order based on this accepted bid.</p>

                    <a href="{{ route('orders.create', ['open_offer_bid_id' => $bid->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Proceed to Create Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
