<x-admin-layout>
    <x-slot name="header">
        {{ __('Payment #' . $payment->id) }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Payment Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Payment ID</label>
                                <p class="mt-1 text-gray-900">#{{ $payment->id }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Order ID</label>
                                <p class="mt-1">
                                    <a href="{{ route('admin.orders.show', $payment->order) }}" class="text-indigo-600 hover:text-indigo-900">
                                        #{{ $payment->order->id }}
                                    </a>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Status</label>
                                <p class="mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $payment->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $payment->status }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Payment Method</label>
                                <p class="mt-1 text-gray-900">{{ $payment->payment_method ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Amount Details -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Amount Breakdown</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-700">Service Amount:</span>
                                <span class="text-gray-900 font-medium">₱{{ number_format($payment->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-700">Platform Fee:</span>
                                <span class="text-gray-900 font-medium">₱{{ number_format($payment->platform_fee, 2) }}</span>
                            </div>
                            <div class="border-t pt-2 flex justify-between font-semibold">
                                <span class="text-gray-900">Total Amount:</span>
                                <span class="text-gray-900">₱{{ number_format($payment->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- User Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">User</h3>
                        <div>
                            <label class="text-sm font-medium text-gray-700">User</label>
                            <p class="mt-1 text-gray-900">
                                {{ $payment->user->firstname }} {{ $payment->user->lastname }}
                                <br><small class="text-gray-500">{{ $payment->user->email }}</small>
                            </p>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Dates -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Dates</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Created</label>
                                <p class="mt-1 text-gray-900">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Paid At</label>
                                <p class="mt-1 text-gray-900">{{ $payment->paid_at?->format('M d, Y H:i') ?? 'Not paid yet' }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Provider Information -->
                    @can('viewProviderDetails', $payment)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Provider Information</h3>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Provider Reference</label>
                                <p class="mt-1 text-gray-900">{{ $payment->provider_reference ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <hr class="my-6">
                    @endcan

                    <!-- Actions -->
                    <div class="flex gap-2">
                        @can('markAsPaid', $payment)
                            <form action="{{ route('admin.payments.mark-paid', $payment) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    Mark as Paid
                                </button>
                            </form>
                        @endcan
                        @can('markAsFailed', $payment)
                            <form action="{{ route('admin.payments.mark-failed', $payment) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                    Mark as Failed
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
