<x-app-layout title="Order Details">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 bg-gray-50 border-b">
                <h1 class="text-2xl font-bold text-gray-800">Order Details</h1>
                <p class="text-sm text-gray-600">Order #{{ $order->id }}</p>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700">Summary</h2>
                        <dl class="mt-2 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Status:</dt>
                                <dd class="font-medium text-gray-800">{{ Str::title($order->status) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Date:</dt>
                                <dd class="font-medium text-gray-800">{{ $order->created_at->format('F d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700">Billing</h2>
                        <dl class="mt-2 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Price:</dt>
                                <dd class="font-medium text-gray-800">₱{{ number_format($order->price, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Platform Fee:</dt>
                                <dd class="font-medium text-gray-800">₱{{ number_format($order->platform_fee, 2) }}</dd>
                            </div>
                            <div class="flex justify-between font-bold text-base">
                                <dt class="text-gray-800">Total:</dt>
                                <dd class="text-green-600">₱{{ number_format($order->total_amount, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h2 class="text-lg font-semibold text-gray-700">Order Item</h2>
                    <div class="mt-4 flex items-center">
                        {{-- Logic to display Service or OpenOfferBid details --}}
                        @if ($order->orderable)
                            <div>
                                <p class="font-semibold text-gray-800">{{ $order->orderable->title ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">
                                    @if($order->orderable_type === \App\Domains\Listings\Models\Service::class)
                                        Type: Service
                                    @elseif($order->orderable_type === \App\Domains\Listings\Models\OpenOfferBid::class)
                                        Type: Accepted Bid
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
