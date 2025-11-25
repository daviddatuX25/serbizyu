<?php

namespace App\Domains\Payments\Http\Controllers;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Services\PaymentHandler;
use Illuminate\Http\Request;

class CashPaymentController
{
    /**
     * Show cash handshake page
     */
    public function showHandshake(Order $order)
    {
        $this->authorize('view', $order);

        $handshake = PaymentHandler::initiateCashHandshake($order);

        return view('payments.cash-handshake', [
            'order' => $order,
            'handshake' => $handshake,
            'isBuyer' => auth()->id() === $order->buyer_id,
            'isSeller' => auth()->id() === $order->seller_id,
        ]);
    }

    /**
     * Buyer confirms they've paid
     */
    public function buyerConfirm(Order $order)
    {
        $this->authorize('view', $order);

        if (auth()->id() !== $order->buyer_id) {
            abort(403, 'Only buyer can confirm payment');
        }

        $result = PaymentHandler::buyerConfirmPayment($order);

        return response()->json($result);
    }

    /**
     * Seller verifies payment
     */
    public function sellerVerify(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        if (auth()->id() !== $order->seller_id) {
            abort(403, 'Only seller can verify payment');
        }

        $validated = $request->validate([
            'accepted' => 'required|boolean',
        ]);

        $result = PaymentHandler::sellerVerifyPayment($order, $validated['accepted']);

        return response()->json($result);
    }
}
