<x-app-layout>
<div class="max-w-2xl mx-auto py-8 px-4">
    <!-- Seller Role Badge -->
    <div class="mb-6">
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <p class="text-green-800 font-semibold">
                <span class="text-lg">👤</span> Your Role: <strong>SELLER</strong>
            </p>
            <p class="text-sm text-green-700 mt-1">You are verifying and releasing payment for this order</p>
        </div>
    </div>

    <div class="grid gap-6" x-data="sellerPaymentRelease('{{ $handshakeId }}', {{ json_encode($handshakeData) }})" @load="init()" @beforeunload="destroy()">

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-3xl font-bold mb-2 text-gray-800">Verify & Release Payment</h1>
            <p class="text-gray-600 text-sm mb-6">Confirm receipt and release payment to activate the order</p>

            <!-- Order Summary Card -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg border border-green-200 mb-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Order ID</p>
                        <p class="text-2xl font-bold text-gray-800">#{{ $order->id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-600 text-sm font-medium">Expected Amount</p>
                        <p class="text-2xl font-bold text-green-600">₱{{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-green-200">
                    <p class="text-xs text-gray-600"><strong>Payment Method:</strong> Cash Payment (Hand-to-Hand)</p>
                </div>
            </div>

            <!-- Buyer Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Buyer Information</p>
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-blue-200 rounded-full flex items-center justify-center font-bold text-blue-700 mr-3">
                        {{ substr($buyer->first_name, 0, 1) }}{{ substr($buyer->last_name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $buyer->first_name }} {{ $buyer->last_name }}</p>
                        <p class="text-xs text-gray-600">Order Buyer</p>
                    </div>
                </div>
            </div>

            <!-- Status Indicator -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-800">Payment Status</h3>
                    <span class="text-xs font-bold uppercase tracking-wider" :class="data.seller_response_at ? (data.status === 'seller_confirmed' ? 'text-green-600' : 'text-red-600') : 'text-yellow-600'">
                        <span x-show="!data.seller_response_at">Awaiting Action</span>
                        <span x-show="data.seller_response_at && data.status === 'seller_confirmed'">Confirmed</span>
                        <span x-show="data.seller_response_at && data.status === 'seller_rejected'">Rejected</span>
                    </span>
                </div>

                <!-- Status Steps -->
                <div class="space-y-3">
                    <!-- Step 1: Buyer Requests -->
                    <div class="flex items-start">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full text-white font-bold mr-4" :class="data.buyer_claimed_at ? 'bg-green-500' : 'bg-yellow-500'">
                            <span x-show="data.buyer_claimed_at">✓</span>
                            <span x-show="!data.buyer_claimed_at">⏳</span>
                        </div>
                        <div class="flex-1 pt-1">
                            <p class="font-semibold text-gray-700">Buyer Claims Payment</p>
                            <p class="text-xs text-gray-600 mt-1" x-show="!data.buyer_claimed_at">Waiting for buyer to confirm payment sent...</p>
                            <p class="text-xs text-gray-600 mt-1" x-show="data.buyer_claimed_at">
                                Buyer confirmed at: <span x-text="new Date(data.buyer_claimed_at).toLocaleString('en-US', {hour: '2-digit', minute: '2-digit'})"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Step 2: Seller Verifies -->
                    <div class="flex items-start">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full text-white font-bold mr-4" :class="data.seller_response_at ? (data.status === 'seller_confirmed' ? 'bg-green-500' : 'bg-red-500') : 'bg-gray-300'">
                            <span x-show="data.seller_response_at && data.status === 'seller_confirmed'">✓</span>
                            <span x-show="data.seller_response_at && data.status === 'seller_rejected'">✗</span>
                            <span x-show="!data.seller_response_at">2</span>
                        </div>
                        <div class="flex-1 pt-1">
                            <p class="font-semibold text-gray-700">Verify Payment Receipt</p>
                            <p class="text-xs text-gray-600 mt-1">Your action is required - Click below</p>
                        </div>
                    </div>

                    <!-- Step 3: Order Proceeds -->
                    <div class="flex items-start">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full text-white font-bold mr-4" :class="data.status === 'seller_confirmed' ? 'bg-green-500' : 'bg-gray-300'">
                            <span x-show="data.status === 'seller_confirmed'">✓</span>
                            <span x-show="data.status !== 'seller_confirmed'">3</span>
                        </div>
                        <div class="flex-1 pt-1">
                            <p class="font-semibold text-gray-700">Order Proceeds</p>
                            <p class="text-xs text-gray-600 mt-1">Order becomes active after confirmation</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Waiting State -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6" x-show="!data.buyer_claimed_at">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-yellow-800 text-lg">Waiting for Buyer</h4>
                        <p class="text-sm text-yellow-700 mt-1">The buyer hasn't yet confirmed they've sent the payment. This step usually takes just a few moments.</p>
                    </div>
                </div>
            </div>

            <!-- Action Section: Buyer Confirmed Payment -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6" x-show="data.buyer_claimed_at && !data.seller_response_at">
                <h3 class="font-semibold text-gray-800 mb-4 text-lg">✓ Buyer Confirmed Payment Sent</h3>

                <p class="text-sm text-gray-700 mb-6">
                    Have you received <strong>₱{{ number_format($order->total_amount, 2) }}</strong> from the buyer? Click the button below to confirm.
                </p>

                <!-- Single Confirm Button (Primary Action) -->
                <div class="mb-4">
                    <button
                        @click="releasePayment(true)"
                        :disabled="data.seller_response_at"
                        class="w-full py-4 px-6 rounded-lg font-semibold text-white text-base transition-all duration-200 flex items-center justify-center"
                        :class="!data.seller_response_at ? 'bg-green-600 hover:bg-green-700 active:scale-95 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'">
                        <span x-show="!loading || actionType !== 'confirm'" class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            ✓ Confirm Payment Received
                        </span>
                        <span x-show="loading && actionType === 'confirm'" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>

                <!-- Secondary Reject Option -->
                <button
                    @click="releasePayment(false)"
                    :disabled="data.seller_response_at"
                    class="w-full py-3 px-6 rounded-lg font-semibold text-gray-700 text-sm transition-all duration-200 border-2 border-gray-300 hover:border-red-300 hover:bg-red-50"
                    :class="data.seller_response_at ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'">
                    <span x-show="!loading || actionType !== 'reject'">Not Received - Ask Buyer to Retry</span>
                    <span x-show="loading && actionType === 'reject'" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>

                <p class="text-xs text-gray-600 mt-4 text-center">Click "Confirm" if you received the payment</p>
            </div>

            <!-- Action Section: Fallback (If Buyer Forgot) -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mb-6" x-show="!data.buyer_claimed_at && !data.seller_response_at">
                <h3 class="font-semibold text-gray-800 mb-4 text-lg">📝 Record Payment</h3>

                <p class="text-sm text-gray-700 mb-6">
                    If you have already received <strong>₱{{ number_format($order->total_amount, 2) }}</strong> but the buyer hasn't notified you yet, you can record it directly:
                </p>

                <button
                    @click="releasePayment(true)"
                    :disabled="data.seller_response_at"
                    class="w-full py-4 px-6 rounded-lg font-semibold text-white text-base transition-all duration-200 flex items-center justify-center"
                    :class="!data.seller_response_at ? 'bg-amber-600 hover:bg-amber-700 active:scale-95 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'">
                    <span x-show="!loading || actionType !== 'confirm'" class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                        </svg>
                        📝 Record Payment Received
                    </span>
                    <span x-show="loading && actionType === 'confirm'" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>

                <p class="text-xs text-gray-600 mt-3 text-center">Use this if buyer forgot to click "Payment Sent"</p>
            </div>

            <!-- Completion Messages -->
            <!-- Payment Confirmed -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6" x-show="data.seller_response_at && data.status === 'seller_confirmed'">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-green-800 text-lg">✓ Payment Confirmed & Released!</h4>
                        <p class="text-sm text-green-700 mt-2">You have confirmed receipt of ₱{{ number_format($order->total_amount, 2) }}. The order is now active and ready to proceed.</p>
                        <p class="text-xs text-gray-600 mt-3">
                            Released at: <span x-text="new Date(data.seller_response_at).toLocaleString('en-US', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'})"></span>
                        </p>
                        <a href="/orders/{{ $order->id }}" class="inline-block mt-4 px-4 py-2 bg-green-600 text-white rounded font-semibold text-sm hover:bg-green-700">
                            View Order →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Payment Rejected -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6" x-show="data.seller_response_at && data.status === 'seller_rejected'">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-red-800 text-lg">✗ Payment Not Received</h4>
                        <p class="text-sm text-red-700 mt-2">You have indicated that you did not receive payment. The buyer will be asked to resend or contact you.</p>
                        <p x-show="data.rejection_reason" class="text-sm text-red-700 mt-3">
                            <strong>Your Note:</strong> <span x-text="data.rejection_reason"></span>
                        </p>
                        <p class="text-xs text-gray-600 mt-3">
                            Marked at: <span x-text="new Date(data.seller_response_at).toLocaleString('en-US', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'})"></span>
                        </p>
                        <div class="mt-4 flex gap-2">
                            <button @click="window.location.reload()" class="px-4 py-2 bg-red-600 text-white rounded font-semibold text-sm hover:bg-red-700">
                                Start Over
                            </button>
                            <a href="/messages" class="px-4 py-2 bg-gray-600 text-white rounded font-semibold text-sm hover:bg-gray-700">
                                Message Buyer
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="bg-blue-50 rounded-lg p-4 mt-6">
                <h4 class="font-semibold text-blue-900 mb-2">💡 Tips</h4>
                <ul class="text-sm text-blue-900 space-y-1 list-disc list-inside">
                    <li>Verify the exact amount before confirming receipt</li>
                    <li>Check your payment method account for the transfer</li>
                    <li>You have 1 hour to confirm or reject the payment</li>
                    <li>If you reject, the buyer can try again</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function sellerPaymentRelease(handshakeId, initialData) {
    return {
        data: initialData,
        loading: false,
        actionType: null,
        pollInterval: null,

        init() {
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
                        }
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }, 2000); // Poll every 2 seconds
        },

        destroy() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
        },

        async releasePayment(accepted) {
            this.loading = true;
            this.actionType = accepted ? 'confirm' : 'reject';

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
                        }, 2000);
                    }
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Failed to process response'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            } finally {
                this.loading = false;
                this.actionType = null;
            }
        }
    };
}
</script>
</x-app-layout>
