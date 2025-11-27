<?php

namespace App\Domains\Payments\Http\Controllers;

use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Services\OrderCompletionService;
use App\Domains\Payments\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class PaymentWebhookController extends Controller
{
    protected OrderCompletionService $orderCompletionService;

    public function __construct(OrderCompletionService $orderCompletionService)
    {
        $this->orderCompletionService = $orderCompletionService;
    }

    public function handle(Request $request)
    {
        $webhookToken = Config::get('payment.xendit.webhook_token');
        $requestToken = $request->header('x-callback-token');

        if ($webhookToken !== $requestToken) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();

        $payment = Payment::where('id', $payload['external_id'])->first();

        if ($payment) {
            if ($payload['status'] === 'PAID') {
                $payment->status = 'paid';
                $payment->paid_at = now();
                $payment->metadata = $payload;
                $payment->save();

                // Update order status
                $order = $payment->order;
                $order->payment_status = 'paid';
                $order->save();

                // Check if order can be completed now (if all work is done)
                $this->orderCompletionService->handlePaymentCompleted($order);
            } else {
                $payment->status = 'failed';
                $payment->metadata = $payload;
                $payment->save();

                // Update order status
                $order = $payment->order;
                $order->payment_status = 'failed';
                $order->save();
            }
        }

        return response()->json(['message' => 'Webhook received']);
    }
}
