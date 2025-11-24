@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Refund Status</h1>
            <p class="mt-2 text-gray-600">Refund ID: #{{ $refund->id }}</p>
        </div>

        <!-- Status Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-gray-600 text-sm">Refund Status</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $refund->getStatusBadgeClass() }}">
                            {{ $refund->getStatusLabel() }}
                        </span>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-gray-600 text-sm">Refund Amount</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $refund->getFormattedAmount() }}</p>
                </div>
            </div>
        </div>

        <!-- Refund Details -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Refund Details</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600 text-sm">Order ID</p>
                        <a href="{{ route('orders.show', $refund->order) }}" class="text-blue-600 hover:text-blue-900">
                            Order #{{ $refund->order_id }}
                        </a>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Requested On</p>
                        <p class="text-gray-900 font-medium">{{ $refund->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-gray-600 text-sm">Reason</p>
                    <p class="text-gray-900 font-medium mt-1">{{ $refund->reason }}</p>
                </div>

                @if($refund->bank_details)
                    <div class="border-t pt-4">
                        <p class="text-gray-600 text-sm mb-2">Bank Details</p>
                        <div class="bg-gray-50 p-3 rounded text-sm">
                            <p><strong>Bank:</strong> {{ $refund->bank_details['bank_name'] ?? 'N/A' }}</p>
                            <p><strong>Account Number:</strong> {{ $refund->bank_details['account_number'] ?? 'N/A' }}</p>
                            <p><strong>Account Name:</strong> {{ $refund->bank_details['account_name'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                @endif

                @if($refund->isCompleted())
                    <div class="border-t pt-4">
                        <p class="text-gray-600 text-sm">Processed On</p>
                        <p class="text-gray-900 font-medium">{{ $refund->processed_at->format('M d, Y') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Status Timeline</h3>

            <div class="space-y-4">
                <!-- Requested -->
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                        <div class="w-0.5 h-12 bg-gray-300"></div>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Refund Requested</p>
                        <p class="text-gray-600 text-sm">{{ $refund->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <!-- Approved/Rejected -->
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 {{ $refund->isApproved() || $refund->isCompleted() ? 'bg-green-600' : ($refund->isRejected() ? 'bg-red-600' : 'bg-gray-300') }} rounded-full"></div>
                        <div class="w-0.5 h-12 bg-gray-300"></div>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">
                            @if($refund->isApproved() || $refund->isCompleted())
                                Refund Approved
                            @elseif($refund->isRejected())
                                Refund Rejected
                            @else
                                Awaiting Review
                            @endif
                        </p>
                        <p class="text-gray-600 text-sm">
                            @if($refund->isApproved() || $refund->isRejected() || $refund->isCompleted())
                                {{ $refund->updated_at->format('M d, Y H:i') }}
                            @else
                                Usually within 24-48 hours
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Completed -->
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 {{ $refund->isCompleted() ? 'bg-green-600' : 'bg-gray-300' }} rounded-full"></div>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Refund Processed</p>
                        <p class="text-gray-600 text-sm">
                            @if($refund->isCompleted())
                                {{ $refund->processed_at->format('M d, Y H:i') }}
                            @else
                                Pending approval
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-8">
            <a href="{{ route('orders.show', $refund->order) }}" class="text-blue-600 hover:text-blue-900">
                ← Back to Order
            </a>
        </div>
    </div>
</div>
@endsection
