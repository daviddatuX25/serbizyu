<x-app-layout>
<div class="max-w-2xl mx-auto py-8 px-4">
    <!-- Buyer Role Badge -->
    <div class="mb-6">
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <p class="text-blue-800 font-semibold">
                <span class="text-lg">👤</span> Your Role: <strong>BUYER</strong>
            </p>
            <p class="text-sm text-blue-700 mt-1">You are requesting to pay for this order</p>
        </div>
    </div>

    <div class="grid gap-6" x-data="buyerPaymentRequest('{{ $handshakeId }}', {{ json_encode($handshakeData) }})" @load="init()" @beforeunload="destroy()">

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-3xl font-bold mb-2 text-gray-800">Payment Notification</h1>
            <p class="text-gray-600 text-sm mb-6">Notify the seller that you've sent the payment</p>

            <!-- Order Summary Card -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border border-blue-200 mb-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Order ID</p>
                        <p class="text-2xl font-bold text-gray-800">#{{ $order->id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-600 text-sm font-medium">Amount Due</p>
                        <p class="text-2xl font-bold text-blue-600">₱{{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-blue-200">
                    <p class="text-xs text-gray-600"><strong>Payment Method:</strong> Cash Payment (Hand-to-Hand)</p>
                </div>
            </div>

            <!-- Status Indicator -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-800">Payment Status</h3>
                    <span class="text-xs font-bold uppercase tracking-wider" :class="data.buyer_claimed_at ? 'text-green-600' : 'text-blue-600'">
                        <span x-show="!data.buyer_claimed_at">Pending</span>
                        <span x-show="data.buyer_claimed_at">Requested</span>
                    </span>
                </div>

                <!-- Status Steps -->
                <div class="space-y-3">
                    <!-- Step 1: Send Payment -->
                    <div class="flex items-start">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full text-white font-bold mr-4" :class="true ? 'bg-green-500' : 'bg-gray-300'">
                            ✓
                        </div>
                        <div class="flex-1 pt-1">
                            <p class="font-semibold text-gray-700">Payment Sent</p>
                            <p class="text-xs text-gray-600 mt-1">Transfer ₱{{ number_format($order->total_amount, 2) }} to the seller</p>
                        </div>
                    </div>

                    <!-- Step 2: Notify Seller -->
                    <div class="flex items-start">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full text-white font-bold mr-4" :class="data.buyer_claimed_at ? 'bg-green-500' : 'bg-yellow-500'">
                            <span x-show="data.buyer_claimed_at">✓</span>
                            <span x-show="!data.buyer_claimed_at">2</span>
                        </div>
                        <div class="flex-1 pt-1">
                            <p class="font-semibold text-gray-700">Notify Seller</p>
                            <p class="text-xs text-gray-600 mt-1">Click button to tell seller payment has been sent</p>
                        </div>
                    </div>

                    <!-- Step 3: Seller Confirms Receipt -->
                    <div class="flex items-start">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full text-white font-bold mr-4" :class="data.seller_response_at && data.status === 'seller_confirmed' ? 'bg-green-500' : 'bg-gray-300'">
                            <span x-show="data.seller_response_at && data.status === 'seller_confirmed'">✓</span>
                            <span x-show="!data.seller_response_at || data.status !== 'seller_confirmed'">3</span>
                        </div>
                        <div class="flex-1 pt-1">
                            <p class="font-semibold text-gray-700">Seller Verification</p>
                            <p class="text-xs text-gray-600 mt-1">Waiting for seller to confirm receipt</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Section -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">Ready to Notify Seller?</h3>

                <p class="text-sm text-gray-700 mb-4" x-show="!data.buyer_claimed_at">
                    You have transferred <strong>₱{{ number_format($order->total_amount, 2) }}</strong> to the seller.
                    Click the button below to notify them.
                </p>

                <button
                    @click="requestPayment()"
                    :disabled="data.buyer_claimed_at"
                    class="w-full py-4 px-6 rounded-lg font-semibold text-white text-lg transition-all duration-200"
                    :class="!data.buyer_claimed_at ? 'bg-blue-600 hover:bg-blue-700 active:scale-95 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'">
                    <span x-show="!data.buyer_claimed_at && !loading">✓ Payment Sent</span>
                    <span x-show="data.buyer_claimed_at">✓ Payment Notified</span>
                    <span x-show="loading" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Requesting...
                    </span>
                </button>

                <p class="text-xs text-gray-600 mt-3 text-center" x-show="data.buyer_claimed_at">
                    Requested at: <span x-text="new Date(data.buyer_claimed_at).toLocaleString('en-US', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'})"></span>
                </p>
            </div>

            <!-- Status Messages -->
            <!-- Waiting for Seller -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4" x-show="data.buyer_claimed_at && !data.seller_response_at">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="font-semibold text-yellow-800">Waiting for Seller Confirmation</h4>
                        <p class="text-sm text-yellow-700 mt-1">The seller will now confirm receipt of your payment...</p>
                    </div>
                </div>
            </div>

            <!-- Payment Confirmed -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4" x-show="data.seller_response_at && data.status === 'seller_confirmed'">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="font-semibold text-green-800">✓ Payment Confirmed!</h4>
                        <p class="text-sm text-green-700 mt-1">The seller has confirmed receipt of your payment. Your order will now proceed.</p>
                        <p class="text-xs text-gray-600 mt-2">
                            Confirmed at: <span x-text="new Date(data.seller_response_at).toLocaleString('en-US', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'})"></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment Rejected -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4" x-show="data.seller_response_at && data.status === 'seller_rejected'">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="font-semibold text-red-800">✗ Payment Not Received</h4>
                        <p class="text-sm text-red-700 mt-1">The seller has not received your payment yet.</p>
                        <p x-show="data.rejection_reason" class="text-sm text-red-700 mt-2">
                            <strong>Seller's Note:</strong> <span x-text="data.rejection_reason"></span>
                        </p>
                        <p class="text-sm text-red-700 mt-2">Please contact the seller to clarify or resend the payment.</p>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="bg-blue-50 rounded-lg p-4 mt-6">
                <h4 class="font-semibold text-blue-900 mb-2">💡 Tips</h4>
                <ul class="text-sm text-blue-900 space-y-1 list-disc list-inside">
                    <li>Confirm payment only after you've transferred the exact amount</li>
                    <li>The seller has up to 1 hour to verify your payment</li>
                    <li>If payment is rejected, contact the seller immediately</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function buyerPaymentRequest(handshakeId, initialData) {
    return {
        data: initialData,
        loading: false,
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

                            // Redirect on confirmation
                            if (result.handshakeData.status === 'seller_confirmed') {
                                this.destroy();
                                setTimeout(() => {
                                    window.location.href = `/orders/{{ $order->id }}`;
                                }, 2000);
                            }
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

        async requestPayment() {
            this.loading = true;
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
                    alert('Error: ' + (error.message || 'Failed to request payment'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
</x-app-layout>
