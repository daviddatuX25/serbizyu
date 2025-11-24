@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Request Refund</h1>
            <p class="mt-2 text-gray-600">Request a refund for this order</p>
        </div>

        <!-- Order Info Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600 text-sm">Service</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ $order->service->title }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Amount to Refund</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ $payment->getFormattedTotal() }}</p>
                </div>
            </div>
        </div>

        <!-- Refund Request Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('refunds.store', $order) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Refund Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-900">Reason for Refund <span class="text-red-500">*</span></label>
                    <textarea name="reason" id="reason" required rows="4"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Please explain why you're requesting a refund (minimum 10 characters)"></textarea>
                    @error('reason')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    <p class="text-gray-500 text-xs mt-2">Minimum 10 characters required</p>
                </div>

                <!-- Bank Details (Optional) -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Refund Bank Details (Optional)</h3>
                    <p class="text-gray-600 text-sm mb-4">If you paid via bank transfer and want the refund to go to a different account, provide details below:</p>

                    <!-- Bank Name -->
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-900">Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g., BDO, BPI, Metrobank">
                    </div>

                    <!-- Account Number -->
                    <div class="mt-4">
                        <label for="account_number" class="block text-sm font-medium text-gray-900">Account Number</label>
                        <input type="text" name="account_number" id="account_number"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Your account number">
                    </div>

                    <!-- Account Name -->
                    <div class="mt-4">
                        <label for="account_name" class="block text-sm font-medium text-gray-900">Account Holder Name</label>
                        <input type="text" name="account_name" id="account_name"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Full name on bank account">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4 pt-6 border-t">
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        Submit Refund Request
                    </button>
                    <a href="{{ route('orders.show', $order) }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium py-2 px-4 rounded-lg text-center transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Important Notice -->
        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-900 text-sm">
                <strong>Important:</strong> Refunds can only be requested before work has started. Once work begins, you'll need to contact support if there are any issues.
            </p>
        </div>
    </div>
</div>
@endsection
