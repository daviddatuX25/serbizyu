<x-admin-layout>
    <x-slot name="header">
        {{ __('Order #' . $order->id) }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Order Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Order ID</label>
                                <p class="mt-1 text-gray-900">#{{ $order->id }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Status</label>
                                <p class="mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $order->status }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Created</label>
                                <p class="mt-1 text-gray-900">{{ $order->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Updated</label>
                                <p class="mt-1 text-gray-900">{{ $order->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Parties Involved -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Parties</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Buyer</label>
                                <p class="mt-1 text-gray-900">
                                    {{ $order->buyer->firstname }} {{ $order->buyer->lastname }}
                                    <br><small class="text-gray-500">{{ $order->buyer->email }}</small>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Seller</label>
                                <p class="mt-1 text-gray-900">
                                    {{ $order->seller->firstname }} {{ $order->seller->lastname }}
                                    <br><small class="text-gray-500">{{ $order->seller->email }}</small>
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Service Details -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Service</h3>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Service Name</label>
                            <p class="mt-1 text-gray-900">{{ $order->service->title ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Payment Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Amount</label>
                                <p class="mt-1 text-gray-900">₱{{ number_format($order->price, 2) }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Platform Fee</label>
                                <p class="mt-1 text-gray-900">₱{{ number_format($order->platform_fee, 2) }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Total Amount</label>
                                <p class="mt-1 font-semibold text-gray-900">₱{{ number_format($order->total_amount, 2) }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Payment Status</label>
                                <p class="mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $order->payment_status }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Actions -->
                    <div class="flex gap-2">
                        @can('updateStatus', $order)
                            <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                                    <option value="">Change Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <button type="submit" class="ml-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                    Update
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
