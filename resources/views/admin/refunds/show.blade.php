<x-admin-layout>
    <x-slot name="header">
        {{ __('Refund #' . $refund->id) }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Refund Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Refund Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Refund ID</label>
                                <p class="mt-1 text-gray-900">#{{ $refund->id }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Status</label>
                                <p class="mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $refund->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $refund->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $refund->status === 'requested' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $refund->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                    ">
                                        {{ $refund->status }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Order</label>
                                <p class="mt-1">
                                    <a href="{{ route('admin.orders.show', $refund->order) }}" class="text-indigo-600 hover:text-indigo-900">
                                        #{{ $refund->order->id }}
                                    </a>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Amount</label>
                                <p class="mt-1 font-semibold text-gray-900">₱{{ number_format($refund->amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Request Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Details</h3>
                        <div class="mb-4">
                            <label class="text-sm font-medium text-gray-700">Reason</label>
                            <p class="mt-1 text-gray-900">{{ $refund->reason }}</p>
                        </div>
                        @if ($refund->evidence)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Evidence</label>
                                <p class="mt-1 text-gray-900">{{ $refund->evidence }}</p>
                            </div>
                        @endif
                    </div>

                    <hr class="my-6">

                    <!-- Buyer Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Buyer Information</h3>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Buyer</label>
                            <p class="mt-1 text-gray-900">
                                {{ $refund->order->buyer->firstname }} {{ $refund->order->buyer->lastname }}
                                <br><small class="text-gray-500">{{ $refund->order->buyer->email }}</small>
                            </p>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Bank Details -->
                    @if ($refund->bank_details)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bank Details</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach ($refund->bank_details as $key => $value)
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">{{ Str::title(str_replace('_', ' ', $key)) }}</label>
                                            <p class="mt-1 text-gray-900">{{ $value }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <hr class="my-6">
                    @endif

                    <!-- Admin Notes -->
                    @if ($refund->admin_notes)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Notes</h3>
                            <p class="text-gray-900">{{ $refund->admin_notes }}</p>
                        </div>

                        <hr class="my-6">
                    @endif

                    <!-- Actions -->
                    <div class="space-y-4">
                        @if ($refund->status === 'requested')
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <h4 class="font-semibold text-yellow-900 mb-3">Review Refund Request</h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <form action="{{ route('admin.refunds.approve', $refund) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="approve_notes" class="block text-sm font-medium text-gray-700">Notes (optional)</label>
                                            <textarea name="admin_notes" id="approve_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"></textarea>
                                        </div>
                                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                            Approve Refund
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.refunds.reject', $refund) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="reject_notes" class="block text-sm font-medium text-gray-700">Notes (required)</label>
                                            <textarea name="admin_notes" id="reject_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200" required></textarea>
                                        </div>
                                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                            Reject Refund
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @elseif ($refund->status === 'approved')
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-semibold text-blue-900 mb-3">Mark as Completed</h4>
                                <form action="{{ route('admin.refunds.complete', $refund) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        Mark as Completed
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="text-gray-700">This refund request has been {{ $refund->status }}.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
