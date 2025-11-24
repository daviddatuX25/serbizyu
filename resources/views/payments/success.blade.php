<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Successful') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-center">
                        <div class="mb-4">
                            <svg class="w-16 h-16 text-green-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-2">Payment Successful!</h3>
                        <p class="text-gray-600 mb-6">Your payment has been processed successfully.</p>

                        @if($payment)
                            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                                <div class="mb-3">
                                    <p class="text-sm text-gray-600">Payment ID</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $payment->id }}</p>
                                </div>
                                <div class="mb-3">
                                    <p class="text-sm text-gray-600">Amount</p>
                                    <p class="text-lg font-semibold text-gray-900">${{ number_format($payment->total_amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <p class="text-lg font-semibold text-green-600">{{ ucfirst($payment->status) }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="space-x-4">
                            <a href="{{ route('orders.show', $payment->order ?? '#') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                View Order
                            </a>
                            <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">
                                Back to Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
