<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Confirm Order') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Review and Confirm Your Order</h3>

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Service:</p>
                            <p class="font-medium">{{ $bid->service->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Seller:</p>
                            <p class="font-medium">{{ $bid->openOffer->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Your Bid Price:</p>
                            <p class="font-medium">${{ number_format($bid->price, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Platform Fee ({{ config('fees.platform_percentage', 5) }}%):</p>
                            <p class="font-medium">${{ number_format(($bid->price * config('fees.platform_percentage', 5)) / 100, 2) }}</p>
                        </div>
                        <div class="md:col-span-2 border-t pt-4 mt-4">
                            <p class="text-lg text-gray-500">Total Amount:</p>
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($bid->price + (($bid->price * config('fees.platform_percentage', 5)) / 100), 2) }}</p>
                        </div>
                    </div>

                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="open_offer_bid_id" value="{{ $bid->id }}">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Confirm and Create Order
                        </button>
                        <a href="{{ route('browse') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
