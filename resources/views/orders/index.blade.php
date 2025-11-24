<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Orders') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">Your Orders</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Here you can view all your placed orders.
                    </p>

                    <div class="mt-6">
                        @if ($orders->isEmpty())
                            <p>You haven't placed any orders yet.</p>
                        @else
                            <ul role="list" class="divide-y divide-gray-100">
                                @foreach ($orders as $order)
                                    <li class="flex justify-between gap-x-6 py-5">
                                        <div class="flex min-w-0 gap-x-4">
                                            <div class="min-w-0 flex-auto">
                                                <p class="text-sm font-semibold leading-6 text-gray-900">Order #{{ $order->id }}</p>
                                                <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ $order->service->title ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                                            <p class="text-sm leading-6 text-gray-900">{{ $order->total_amount }}</p>
                                            <p class="mt-1 text-xs leading-5 text-gray-500">Status: {{ $order->status }}</p>
                                            <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-900">View Details</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
