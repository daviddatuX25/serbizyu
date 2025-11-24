<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="text-red-800 font-semibold mb-2">Error:</h3>
                    <ul class="text-red-700 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Order Summary</h3>
                    <div class="mt-4 space-y-2">
                        <p><strong>Order ID:</strong> {{ $order->id }}</p>
                        <p><strong>Price:</strong> ${{ number_format($order->price, 2) }}</p>
                        @if($order->total_amount)
                            <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                        @endif
                        <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                    </div>

                    <form action="{{ route('payments.pay', $order) }}" method="POST" class="mt-6">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Pay Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
