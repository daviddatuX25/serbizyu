<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Order #{{ $order->id }}</h1>

                    <form action="{{ route('orders.update', $order) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Order Details (Read-only) -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Details</h2>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Order ID</p>
                                    <p class="font-medium text-gray-900">#{{ $order->id }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <p class="font-medium text-gray-900 capitalize">{{ $order->status }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Total Amount</p>
                                    <p class="font-medium text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Created</p>
                                    <p class="font-medium text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cancellation Reason (only if applicable) -->
                        @if($order->status === 'pending' || $order->status === 'cancelled')
                            <div>
                                <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cancellation Reason
                                </label>
                                <textarea
                                    id="cancellation_reason"
                                    name="cancellation_reason"
                                    rows="4"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Provide a reason for cancellation (if applicable)"
                                >{{ $order->cancellation_reason }}</textarea>
                                @error('cancellation_reason')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex gap-4">
                            <a href="{{ route('orders.show', $order) }}" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
