<x-app-layout>
<div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Role Indicator Banner -->
    <div class="mb-6">
        @if ($isBuyer)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <p class="text-blue-800 font-semibold">
                    <span class="text-lg">👤</span> Your Role: <strong>BUYER</strong>
                </p>
                <p class="text-sm text-blue-700 mt-1">You are paying for this order</p>
            </div>
        @elseif ($isSeller)
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                <p class="text-green-800 font-semibold">
                    <span class="text-lg">👤</span> Your Role: <strong>SELLER</strong>
                </p>
                <p class="text-sm text-green-700 mt-1">You are receiving payment for this order</p>
            </div>
        @else
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <p class="text-red-800 font-semibold">Unauthorized Access</p>
                <p class="text-sm text-red-700 mt-1">You are not a participant in this payment</p>
            </div>
        @endif
    </div>

    <!-- BUYER VIEW -->
    @if ($isBuyer)
        <div class="grid gap-6" x-data="cashHandshakeListener('{{ $handshakeId }}', {{ json_encode($handshakeData) }})" @load="init()" @beforeunload="destroy()">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold mb-4">Payment Confirmation</h1>

                <div class="bg-gray-50 p-4 rounded border border-gray-200 mb-4">
                    <h2 class="font-semibold text-lg mb-3">Order Details</h2>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                        <p><strong>Amount:</strong> <span class="text-xl font-bold text-blue-600">₱{{ number_format($order->total_amount, 2) }}</span></p>
                        <p><strong>Payment Method:</strong> <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">Cash Payment</span></p>
                    </div>
                </div>

                <!-- Payment Status -->
                <div class="bg-blue-50 border border-blue-200 p-4 rounded mb-4">
                    <h3 class="font-semibold mb-2">Payment Status</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center">
                            <span class="inline-block w-6 h-6 mr-2 rounded-full text-white text-center text-xs leading-6" :class="data.buyer_claimed_at ? 'bg-green-500' : 'bg-gray-300'">
                                <span x-show="data.buyer_claimed_at">✓</span>
                            </span>
                            <span :class="data.buyer_claimed_at ? 'text-green-700' : 'text-gray-600'">Payment Claimed by You</span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-block w-6 h-6 mr-2 rounded-full text-white text-center text-xs leading-6" :class="data.seller_response_at ? (data.status === 'seller_confirmed' ? 'bg-green-500' : 'bg-red-500') : 'bg-yellow-500'">
                                <span x-show="!data.seller_response_at">⏳</span>
                                <span x-show="data.seller_response_at && data.status === 'seller_confirmed'">✓</span>
                                <span x-show="data.seller_response_at && data.status === 'seller_rejected'">✗</span>
                            </span>
                            <span :class="!data.seller_response_at ? 'text-yellow-700' : (data.status === 'seller_confirmed' ? 'text-green-700' : 'text-red-700')">
                                <span x-show="!data.seller_response_at">Waiting for Seller Response...</span>
                                <span x-show="data.seller_response_at && data.status === 'seller_confirmed'">Seller Confirmed Receipt ✓</span>
                                <span x-show="data.seller_response_at && data.status === 'seller_rejected'">Seller Rejected Payment ✗</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Steps -->
                <div class="border-t pt-4">
                    <h3 class="font-semibold mb-3 text-lg">Step 1: Confirm Payment Sent</h3>
                    <p class="text-gray-600 mb-4">Click the button below after you have transferred ₱{{ number_format($order->total_amount, 2) }} to the seller:</p>

                    <button
                        @click="buyerConfirmPayment()"
                        :disabled="data.status !== 'pending'"
                        :class="{ 'opacity-50 cursor-not-allowed': data.status !== 'pending' }"
                        class="w-full py-3 px-4 rounded-lg font-semibold text-white transition-all"
                        :class="data.status === 'pending' ? 'bg-blue-600 hover:bg-blue-700 active:scale-95' : 'bg-gray-400 cursor-not-allowed'">
                        <span x-show="data.status === 'pending'">✓ I Have Sent Payment to Seller</span>
                        <span x-show="data.status !== 'pending'">Payment Claimed - Waiting for Seller Response</span>
                    </button>

                    <p class="text-xs text-gray-500 mt-2" x-show="data.buyer_claimed_at">
                        Payment claimed at: <span x-text="new Date(data.buyer_claimed_at).toLocaleString()"></span>
                    </p>
                </div>

                <!-- Rejection Message -->
                <div class="bg-red-50 border border-red-200 p-4 rounded mt-4" x-show="data.status === 'seller_rejected'">
                    <h4 class="font-semibold text-red-800 mb-2">Payment Rejected</h4>
                    <p class="text-sm text-red-700 mb-3">The seller did not receive your payment.</p>
                    <p x-show="data.rejection_reason" class="text-sm text-red-700 mb-3">
                        <strong>Reason:</strong> <span x-text="data.rejection_reason"></span>
                    </p>
                    <p class="text-sm text-red-700">Please contact the seller to resolve this issue.</p>
                </div>
            </div>
        </div>

    <!-- SELLER VIEW -->
    @elseif ($isSeller)
        <div class="grid gap-6" x-data="cashHandshakeListener('{{ $handshakeId }}', {{ json_encode($handshakeData) }})" @load="init()" @beforeunload="destroy()">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold mb-4">Payment Verification</h1>

                <div class="bg-gray-50 p-4 rounded border border-gray-200 mb-4">
                    <h2 class="font-semibold text-lg mb-3">Order Details</h2>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                        <p><strong>Expected Amount:</strong> <span class="text-xl font-bold text-green-600">₱{{ number_format($order->total_amount, 2) }}</span></p>
                        <p><strong>Payment Method:</strong> <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Cash Payment</span></p>
                    </div>
                </div>

                <!-- Payment Status -->
                <div class="bg-green-50 border border-green-200 p-4 rounded mb-4">
                    <h3 class="font-semibold mb-2">Payment Status</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center">
                            <span class="inline-block w-6 h-6 mr-2 rounded-full text-white text-center text-xs leading-6" :class="data.buyer_claimed_at ? 'bg-green-500' : 'bg-yellow-500'">
                                <span x-show="!data.buyer_claimed_at">⏳</span>
                                <span x-show="data.buyer_claimed_at">✓</span>
                            </span>
                            <span :class="data.buyer_claimed_at ? 'text-green-700' : 'text-yellow-700'">
                                <span x-show="!data.buyer_claimed_at">Waiting for Buyer to Confirm Payment...</span>
                                <span x-show="data.buyer_claimed_at">Buyer Has Confirmed Payment Sent</span>
                            </span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-block w-6 h-6 mr-2 rounded-full text-white text-center text-xs leading-6" :class="data.seller_response_at ? (data.status === 'seller_confirmed' ? 'bg-green-500' : 'bg-red-500') : 'bg-gray-300'">
                                <span x-show="data.status === 'seller_confirmed'">✓</span>
                                <span x-show="data.status === 'seller_rejected'">✗</span>
                            </span>
                            <span :class="!data.seller_response_at ? 'text-gray-600' : (data.status === 'seller_confirmed' ? 'text-green-700' : 'text-red-700')">
                                <span x-show="!data.seller_response_at">Your Response Pending</span>
                                <span x-show="data.seller_response_at && data.status === 'seller_confirmed'">You Confirmed Receipt</span>
                                <span x-show="data.seller_response_at && data.status === 'seller_rejected'">You Rejected Payment</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Steps -->
                <div class="border-t pt-4" x-show="!data.buyer_claimed_at">
                    <h3 class="font-semibold mb-3 text-lg text-yellow-700">⏳ Waiting for Buyer...</h3>
                    <p class="text-gray-600">The buyer has not yet confirmed they sent the payment. Please wait for their confirmation.</p>
                </div>

                <div class="border-t pt-4" x-show="data.buyer_claimed_at && !data.seller_response_at">
                    <h3 class="font-semibold mb-3 text-lg">Step 2: Verify Payment Receipt</h3>
                    <p class="text-gray-600 mb-4">Did you receive ₱{{ number_format($order->total_amount, 2) }} from the buyer?</p>

                    <div class="grid grid-cols-2 gap-3">
                        <button
                            @click="sellerVerify(true)"
                            :disabled="data.seller_response_at"
                            :class="{ 'opacity-50 cursor-not-allowed': data.seller_response_at }"
                            class="py-3 px-4 rounded-lg font-semibold text-white transition-all"
                            :class="!data.seller_response_at ? 'bg-green-600 hover:bg-green-700 active:scale-95' : 'bg-gray-400'">
                            <span>✓ Yes, Payment Received</span>
                        </button>

                        <button
                            @click="sellerVerify(false)"
                            :disabled="data.seller_response_at"
                            :class="{ 'opacity-50 cursor-not-allowed': data.seller_response_at }"
                            class="py-3 px-4 rounded-lg font-semibold text-white transition-all"
                            :class="!data.seller_response_at ? 'bg-red-600 hover:bg-red-700 active:scale-95' : 'bg-gray-400'">
                            <span>✗ No, Payment Not Received</span>
                        </button>
                    </div>

                    <p class="text-xs text-gray-500 mt-2">Click the appropriate button to confirm or reject the payment.</p>
                </div>

                <!-- Completion Message -->
                <div class="bg-green-50 border border-green-200 p-4 rounded mt-4" x-show="data.seller_response_at && data.status === 'seller_confirmed'">
                    <h4 class="font-semibold text-green-800 mb-2">✓ Payment Confirmed!</h4>
                    <p class="text-sm text-green-700">Payment verified successfully. The order will proceed to the next stage.</p>
                    <p class="text-xs text-gray-500 mt-2">
                        Confirmed at: <span x-text="new Date(data.seller_response_at).toLocaleString()"></span>
                    </p>
                </div>

                <!-- Rejection Message -->
                <div class="bg-red-50 border border-red-200 p-4 rounded mt-4" x-show="data.seller_response_at && data.status === 'seller_rejected'">
                    <h4 class="font-semibold text-red-800 mb-2">✗ Payment Rejected</h4>
                    <p class="text-sm text-red-700">You have rejected this payment. The buyer will be notified to resend the payment or contact you.</p>
                </div>
            </div>

    @endif
</div>

<script>
function cashHandshakeListener(handshakeId, initialData) {
    return {
        data: initialData,
        pollInterval: null,

        init() {
            // Start polling ONLY when on this page
            this.setupPolling();
        },

        setupPolling() {
            this.pollInterval = setInterval(async () => {
                try {
                    const response = await fetch(`/payments/cash/handshake/status?handshakeId=${handshakeId}`);
                    if (response.ok) {
                        const result = await response.json();
                        if (result.handshakeData) {
                            this.data = result.handshakeData;

                            // Redirect if payment confirmed
                            if (result.handshakeData.status === 'seller_confirmed') {
                                this.destroy();
                                setTimeout(() => {
                                    window.location.href = `/orders/{{ $order->id }}`;
                                }, 1500);
                            }
                        }
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }, 3000); // Poll every 3 seconds
        },

        destroy() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
        },

        async buyerConfirmPayment() {
            try {
                const response = await fetch(`/payments/cash/buyer-claimed`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        handshake_id: '{{ $handshakeId }}',
                        order_id: {{ $order->id }}
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    this.data.status = 'buyer_claimed';
                    this.data.buyer_claimed_at = new Date().toISOString();
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Failed to confirm payment'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        },

        async sellerVerify(accepted) {
            const endpoint = accepted ? '/payments/cash/seller-confirmed' : '/payments/cash/seller-rejected';
            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        handshake_id: '{{ $handshakeId }}',
                        order_id: {{ $order->id }}
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    this.data.status = accepted ? 'seller_confirmed' : 'seller_rejected';
                    this.data.seller_response_at = new Date().toISOString();

                    if (accepted) {
                        this.destroy();
                        setTimeout(() => {
                            window.location.href = `/orders/{{ $order->id }}`;
                        }, 1500);
                    }
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Failed to process response'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }
    };
}
</script>
</x-app-layout>
