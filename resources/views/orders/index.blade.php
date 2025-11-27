<x-creator-layout title="My Orders">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Orders') }}
        </h2>
    </x-slot>

    <style>
        @media (max-width: 768px) {
            .responsive-table thead {
                display: none;
            }
            .responsive-table tbody,
            .responsive-table tr,
            .responsive-table td {
                display: block;
                width: 100%;
            }
            .responsive-table tr {
                margin-bottom: 1rem;
                border: 1px solid #e2e8f0;
                border-radius: 0.5rem;
                overflow: hidden;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            }
            .responsive-table td {
                padding-left: 50%;
                position: relative;
                text-align: left;
                white-space: normal;
                border-bottom: 1px solid #e2e8f0;
            }
            .responsive-table td:last-child {
                border-bottom: none;
            }
            .responsive-table td:before {
                position: absolute;
                left: 0.75rem;
                width: 45%;
                padding-right: 0.75rem;
                white-space: nowrap;
                font-weight: 600;
                content: attr(data-label);
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if($orders->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">No orders yet</p>
                            <a href="{{ route('browse') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">Browse Services</a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 responsive-table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Order ID
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Service
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Party
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Payment
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">View</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 md:divide-y-0">
                                    @foreach($orders as $order)
                                        <tr class="hover:bg-gray-50">
                                            <td data-label="Order ID" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                #{{ $order->id }}
                                            </td>
                                            <td data-label="Service" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $order->service->name }}
                                            </td>
                                            <td data-label="Party" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if(Auth::user()->id === $order->buyer_id)
                                                    <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded">Buyer</span>
                                                @else
                                                    <span class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded">Seller</span>
                                                @endif
                                            </td>
                                            <td data-label="Status" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <x-order-status-badge :status="$order->status" type="status" />
                                            </td>
                                            <td data-label="Payment" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <x-order-status-badge :status="$order->payment_status" type="payment" />
                                            </td>
                                            <td data-label="Total" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                ${{ number_format($order->total_amount, 2) }}
                                            </td>
                                            <td data-label="Date" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $order->created_at->format('M d, Y') }}
                                            </td>
                                            <td data-label="Actions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-creator-layout>