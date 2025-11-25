@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Request Disbursement</h1>
            <p class="mt-2 text-gray-600">Provide your bank details to receive your earnings</p>
        </div>

        <!-- Disbursement Info Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600 text-sm">Amount to Receive</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $disbursement->getNetAmount() }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Platform Fee</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $disbursement->getFormattedFee() }}</p>
                </div>
            </div>
        </div>

        <!-- Disbursement Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('disbursements.request', $disbursement) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Bank Name -->
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-gray-900">Bank Name</label>
                    <input type="text" name="bank_name" id="bank_name" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g., BDO, BPI, Metrobank">
                    @error('bank_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Account Number -->
                <div>
                    <label for="account_number" class="block text-sm font-medium text-gray-900">Account Number</label>
                    <input type="text" name="account_number" id="account_number" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Your account number">
                    @error('account_number')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Account Name -->
                <div>
                    <label for="account_name" class="block text-sm font-medium text-gray-900">Account Holder Name</label>
                    <input type="text" name="account_name" id="account_name" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Full name on bank account">
                    @error('account_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        Submit Disbursement Request
                    </button>
                    <a href="{{ route('earnings.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium py-2 px-4 rounded-lg text-center transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Alert -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-blue-900 text-sm">
                <strong>Processing Time:</strong> Your disbursement request will be reviewed and processed within 2-3 business days. You'll receive a confirmation email once the payment is sent.
            </p>
        </div>
    </div>
</div>
@endsection
