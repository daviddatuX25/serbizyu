<x-app-layout>
    <div class="container mx-auto px-4 py-6 max-w-lg md:max-w-4xl">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-md overflow-hidden">
            <!-- Header -->
            <header class="flex justify-between items-center p-4 border-b">
                <h1 class="text-lg font-semibold">My Service</h1>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </header>

            <!-- Status and Actions -->
            <nav class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-sm font-medium text-gray-600 border-b bg-gray-50">
                <div class="flex items-center space-x-2">
                    @if($service->is_flagged ?? false)
                        <span class="flex items-center text-orange-600">
                            <span class="h-2 w-2 bg-orange-500 rounded-full mr-2"></span>Flagged
                        </span>
                    @else
                        <span class="flex items-center text-green-600">
                            <span class="h-2 w-2 bg-green-500 rounded-full mr-2"></span>Active
                        </span>
                    @endif
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('creator.services.edit', $service) }}" 
                        class="hover:text-gray-900 transition">Edit</a>
                    <form action="{{ route('creator.services.destroy', $service) }}" method="POST" class="inline" 
                        onsubmit="return confirm('Are you sure you want to delete this service?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 transition">Delete</button>
                    </form>
                </div>
            </nav>

            <!-- Analytics Chart Placeholder -->
            <div class="p-4 space-y-4">
                <div class="h-32 bg-gray-100 rounded-lg flex items-center justify-center border">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-xs text-gray-500">Total revenue</p>
                        <p class="text-2xl font-bold text-gray-800">
                            ${{ number_format($analytics['total_revenue'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Today</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $analytics['today_clicks'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Clicks</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Total</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $analytics['wishlist_count'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Wishlist adds</p>
                    </div>
                </div>
            </div>

            <!-- Tabs Section -->
            <div class="px-4" x-data="{ activeTab: 'orders' }">
                <div class="flex border-b">
                    <button @click="activeTab = 'orders'" 
                        :class="activeTab === 'orders' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'"
                        class="flex-1 py-3 text-center font-medium transition">
                        Orders
                    </button>
                    <button @click="activeTab = 'reviews'" 
                        :class="activeTab === 'reviews' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'"
                        class="flex-1 py-3 text-center font-medium transition">
                        Reviews
                    </button>
                </div>

                <!-- Orders Tab -->
                <div x-show="activeTab === 'orders'" class="py-4 space-y-3">
                    @forelse($orders ?? [] as $order)
                        <div class="border rounded-lg p-3 hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-semibold text-sm">Order #{{ $order->id }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $order->status_color ?? 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($order->status ?? 'pending') }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $order->customer_name ?? 'Customer' }}</p>
                            <p class="text-sm font-semibold text-gray-800">${{ number_format($order->amount ?? 0, 2) }}</p>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-sm text-gray-500 mt-2">No orders yet</p>
                        </div>
                    @endforelse
                </div>

                <!-- Reviews Tab -->
                <div x-show="activeTab === 'reviews'" x-cloak class="py-4 space-y-3">
                    @forelse($reviews ?? [] as $review)
                        <div class="border rounded-lg p-3 bg-gray-50">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-semibold text-sm">{{ $review->reviewer_name }}</span>
                                <span class="text-yellow-400 text-sm">{{ str_repeat('★', $review->rating) }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $review->comment }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <p class="text-sm text-gray-500 mt-2">No reviews yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Preview Section -->
            <div class="p-4 border-t">
                <a href="{{ route('services.show', $service) }}" 
                    class="block w-full bg-gray-100 hover:bg-gray-200 text-center py-3 rounded-lg font-medium text-gray-700 transition">
                    Preview as Customer
                </a>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>
