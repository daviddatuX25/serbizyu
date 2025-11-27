@extends('layouts.app')

@section('content')
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
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Earnings Dashboard</h1>
            <p class="mt-2 text-gray-600">Manage your earnings and request disbursements</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Earnings -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">Total Earnings</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">₱{{ number_format($totalEarnings, 2) }}</p>
                    </div>
                    <div class="text-green-500 text-4xl">💰</div>
                </div>
            </div>

            <!-- Pending Balance -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">Pending Balance</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-2">₱{{ number_format($pendingBalance, 2) }}</p>
                    </div>
                    <div class="text-yellow-500 text-4xl">⏳</div>
                </div>
            </div>

            <!-- Completed Balance -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">Completed Balance</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">₱{{ number_format($completedBalance, 2) }}</p>
                    </div>
                    <div class="text-green-500 text-4xl">✓</div>
                </div>
            </div>
        </div>

        <!-- Disbursements Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Disbursements</h2>
            </div>

            @if($disbursements->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 responsive-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 md:divide-y-0">
                            @foreach($disbursements as $disbursement)
                                <tr>
                                    <td data-label="Order" class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('orders.show', $disbursement->order) }}" class="text-blue-600 hover:text-blue-900">
                                            Order #{{ $disbursement->order_id }}
                                        </a>
                                    </td>
                                    <td data-label="Amount" class="px-6 py-4 whitespace-nowrap">{{ $disbursement->getFormattedAmount() }}</td>
                                    <td data-label="Fee" class="px-6 py-4 whitespace-nowrap">{{ $disbursement->getFormattedFee() }}</td>
                                    <td data-label="Status" class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $disbursement->getStatusBadgeClass() }}">
                                            {{ $disbursement->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td data-label="Actions" class="px-6 py-4 whitespace-nowrap">
                                        @if($disbursement->isPending())
                                            <a href="{{ route('disbursements.show', $disbursement) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                                Request Payout
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $disbursements->links() }}
                </div>
            @else
                <div class="px-6 py-8 text-center">
                    <p class="text-gray-600">No disbursements yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection