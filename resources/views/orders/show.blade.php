<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Order #{{ $order->id }}</h3>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-{{ $order->status->getColor() }}-100 text-{{ $order->status->getColor() }}-800">
                            {{ $order->status->value }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Buyer:</p>
                            <p class="font-medium">{{ $order->buyer->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Seller:</p>
                            <p class="font-medium">{{ $order->seller->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Service:</p>
                            <p class="font-medium">{{ $order->service->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Price:</p>
                            <p class="font-medium">${{ number_format($order->price, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Platform Fee:</p>
                            <p class="font-medium">${{ number_format($order->platform_fee, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Amount:</p>
                            <p class="font-medium">${{ number_format($order->total_amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Payment Status:</p>
                            <p class="font-medium">{{ $order->payment_status->value }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Created At:</p>
                            <p class="font-medium">{{ $order->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        @if ($order->cancelled_at)
                            <div>
                                <p class="text-sm text-gray-500">Cancelled At:</p>
                                <p class="font-medium">{{ $order->cancelled_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Cancellation Reason:</p>
                                <p class="font-medium">{{ $order->cancellation_reason }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Order Status Timeline Component --}}
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Order Status History</h4>
                        <x-order-timeline :order="$order" />
                    </div>

                    @can('cancel', $order)
                        <div class="mt-6">
                            <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Cancel Order
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
