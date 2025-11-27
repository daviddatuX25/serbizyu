<x-admin-layout>
    <x-slot name="header">
        {{ __('Refunds Management') }}
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
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('admin.refunds.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by refund ID..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                                <option value="">All Status</option>
                                <option value="requested" {{ request('status') === 'requested' ? 'selected' : '' }}>Requested</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Refunds Table -->
            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 responsive-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Refund ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buyer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested</th>
                                <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 md:divide-y-0">
                            @forelse ($refunds as $refund)
                                <tr>
                                    <td data-label="Refund ID" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $refund->id }}</td>
                                    <td data-label="Order" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $refund->order->id }}</td>
                                    <td data-label="Buyer" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $refund->order->buyer->firstname }} {{ $refund->order->buyer->lastname }}</td>
                                    <td data-label="Amount" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($refund->amount, 2) }}</td>
                                    <td data-label="Reason" class="px-6 py-4 text-sm text-gray-900 truncate">{{ Str::limit($refund->reason, 30) }}</td>
                                    <td data-label="Status" class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            {{ $refund->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $refund->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $refund->status === 'requested' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $refund->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                        ">
                                            {{ $refund->status }}
                                        </span>
                                    </td>
                                    <td data-label="Requested" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $refund->created_at->format('M d, Y') }}</td>
                                    <td data-label="Actions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.refunds.show', $refund) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">No refunds found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $refunds->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>