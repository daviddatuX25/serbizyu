<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Failed') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-center">
                        <div class="mb-4">
                            <svg class="w-16 h-16 text-red-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-2">Payment Failed</h3>
                        <p class="text-gray-600 mb-6">Unfortunately, your payment could not be processed. Please try again.</p>

                        @if($payment)
                            <div class="bg-red-50 rounded-lg p-4 mb-6 text-left">
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
                                    <p class="text-lg font-semibold text-red-600">{{ ucfirst($payment->status) }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="space-x-4">
                            <a href="{{ route('payments.checkout', $payment->order ?? '#') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                Try Again
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
