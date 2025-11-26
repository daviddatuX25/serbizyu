<div class="bg-white rounded-lg shadow">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Orders</h3>
        <div class="space-y-4">
            @forelse($orders ?? [] as $order)
                <div class="border rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold text-gray-800">Order #{{ $order->id }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $order->created_at->format('M d, Y') }} with <span class="font-medium">{{ $order->customer_name ?? 'Customer' }}</span>
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->status_color ?? 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($order->status ?? 'pending') }}
                        </span>
                    </div>
                    <div class="mt-2 text-right">
                        <p class="text-lg font-bold text-gray-900">₱{{ number_format($order->total_amount ?? 0, 2) }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No orders yet</h3>
                    <p class="mt-1 text-sm text-gray-500">When a customer places an order for this service, it will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
