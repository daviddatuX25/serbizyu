@extends('components.layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-4">Cash Payment Verification</h1>

        <div class="space-y-4">
            <div class="p-4 bg-blue-50 border border-blue-200 rounded">
                <h2 class="font-semibold text-lg mb-2">Order #{{ $order->id }}</h2>
                <p class="text-gray-700">Amount: <span class="font-bold">₱{{ number_format($order->total_amount, 2) }}</span></p>
            </div>

            @if ($isBuyer)
                <div class="border-t pt-4">
                    <h3 class="font-semibold mb-3">Step 1: Confirm Payment</h3>
                    <p class="text-gray-600 mb-4">Click below once you have paid the seller</p>
                    <button 
                        @click="buyerConfirmPayment"
                        class="btn btn-primary w-full">
                        ✓ I Have Paid the Seller
                    </button>
                </div>
            @endif

            @if ($isSeller)
                <div class="border-t pt-4">
                    <h3 class="font-semibold mb-3">Step 2: Verify Payment Receipt</h3>
                    <p class="text-gray-600 mb-4">Did you receive payment from the buyer?</p>
                    <div class="flex gap-3">
                        <button 
                            @click="sellerVerify(true)"
                            class="btn btn-success flex-1">
                            ✓ Payment Received
                        </button>
                        <button 
                            @click="sellerVerify(false)"
                            class="btn btn-danger flex-1">
                            ✗ Payment Not Received
                        </button>
                    </div>
                </div>
            @endif

            <div class="bg-gray-50 p-4 rounded text-sm text-gray-600">
                <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $handshake['status'])) }}</p>
                <p class="mt-2">Buyer Paid: <span class="font-semibold">{{ $handshake['buyer_paid'] ? 'Yes' : 'No' }}</span></p>
                <p>Seller Accepted: <span class="font-semibold">{{ $handshake['seller_accepted'] ? 'Yes' : 'No' }}</span></p>
            </div>
        </div>
    </div>
</div>

<script>
function buyerConfirmPayment() {
    fetch(`/payments/cash/{{ $order->id }}/buyer-confirm`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        alert('Payment confirmation sent to seller');
        location.reload();
    });
}

function sellerVerify(accepted) {
    fetch(`/payments/cash/{{ $order->id }}/seller-verify`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ accepted })
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message);
        location.href = `/orders/{{ $order->id }}`;
    });
}
</script>
@endsection
