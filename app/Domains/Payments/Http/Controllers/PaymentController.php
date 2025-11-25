<?php

namespace App\Domains\Payments\Http\Controllers;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Services\PaymentService;
use App\Domains\Payments\Services\CashPaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;
    protected CashPaymentService $cashPaymentService;

    public function __construct(PaymentService $paymentService, CashPaymentService $cashPaymentService)
    {
        $this->paymentService = $paymentService;
        $this->cashPaymentService = $cashPaymentService;
    }

    /**
     * Display payment checkout page
     * Routes to different pages based on payment method and pay_first requirement
     */
    public function checkout(Order $order): \Illuminate\View\View
    {
        $service = $order->service;
        $payFirstRequired = $service && $service->pay_first;
        $cashEnabled = config('payment.cash_enabled', false);

        return view('payments.checkout', compact('order', 'payFirstRequired', 'cashEnabled'));
    }

    /**
     * Process online payment (Xendit)
     * If pay_first is enabled, payment must succeed before order is created
     */
    public function pay(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        try {
            $paymentMethod = $request->input('payment_method', 'xendit');
            $service = $order->service;
            $payFirstRequired = $service && $service->pay_first;

            if ($paymentMethod === 'cash') {
                return $this->handleCashPayment($order);
            }

            // Xendit online payment
            $invoiceUrl = $this->paymentService->createInvoice($order);
            $order->update(['payment_method' => 'xendit']);

            return redirect()->away($invoiceUrl);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Handle cash payment initiation
     */
    private function handleCashPayment(Order $order): \Illuminate\Http\RedirectResponse
    {
        if (!config('payment.cash_enabled')) {
            return back()->with('error', 'Cash payment is not enabled');
        }

        $handshakeId = $this->cashPaymentService->initiateHandshake($order);
        $order->update(['payment_method' => 'cash']);

        return redirect()->route('payments.cash.handshake', ['handshakeId' => $handshakeId, 'orderId' => $order->id]);
    }

    /**
     * Display cash payment handshake page
     */
    public function cashHandshake(Request $request): \Illuminate\View\View
    {
        $handshakeId = $request->query('handshakeId');
        $orderId = $request->query('orderId');

        $handshakeData = $this->cashPaymentService->getHandshakeStatus($handshakeId);

        if (!$handshakeData) {
            abort(404, 'Handshake not found');
        }

        $order = Order::findOrFail($orderId);

        return view('payments.cash-handshake', compact('handshakeId', 'order', 'handshakeData'));
    }

    /**
     * Buyer claims they have paid (cash method)
     */
    public function buyerClaimedPayment(Request $request): \Illuminate\Http\RedirectResponse
    {
        $handshakeId = $request->input('handshake_id');
        $orderId = $request->input('order_id');

        $this->authorize('update', Order::findOrFail($orderId));

        if ($this->cashPaymentService->buyerClaimedPayment($handshakeId)) {
            return redirect()->route('payments.cash.wait-seller', [
                'handshakeId' => $handshakeId,
                'orderId' => $orderId,
            ])->with('success', 'Payment confirmed. Waiting for seller confirmation...');
        }

        return back()->with('error', 'Unable to confirm payment at this time');
    }

    /**
     * Seller confirms receiving payment (cash method)
     */
    public function sellerConfirmedPayment(Request $request): \Illuminate\Http\RedirectResponse
    {
        $handshakeId = $request->input('handshake_id');
        $orderId = $request->input('order_id');

        $order = Order::findOrFail($orderId);
        $this->authorize('update', $order);

        if ($this->cashPaymentService->sellerConfirmedPayment($handshakeId, $orderId)) {
            return redirect()->route('orders.show', $order)->with('success', 'Payment confirmed! Order is now active.');
        }

        return back()->with('error', 'Unable to confirm payment');
    }

    /**
     * Seller disputes payment (cash method)
     */
    public function sellerRejectedPayment(Request $request): \Illuminate\Http\RedirectResponse
    {
        $handshakeId = $request->input('handshake_id');
        $orderId = $request->input('order_id');
        $reason = $request->input('reason', '');

        $order = Order::findOrFail($orderId);
        $this->authorize('update', $order);

        if ($this->cashPaymentService->sellerRejectedPayment($handshakeId, $orderId, $reason)) {
            return redirect()->route('payments.cash.disputed', [
                'orderId' => $orderId,
                'handshakeId' => $handshakeId,
            ])->with('info', 'Payment dispute recorded. Please try again or contact support.');
        }

        return back()->with('error', 'Unable to process dispute');
    }

    /**
     * Display page showing seller is waiting to confirm
     */
    public function waitingForSeller(Request $request): \Illuminate\View\View
    {
        $handshakeId = $request->query('handshakeId');
        $orderId = $request->query('orderId');

        $handshakeData = $this->cashPaymentService->getHandshakeStatus($handshakeId);

        if (!$handshakeData) {
            abort(404);
        }

        $order = Order::findOrFail($orderId);

        return view('payments.cash-waiting', compact('handshakeId', 'order', 'handshakeData'));
    }

    /**
     * Display page showing payment was disputed
     */
    public function paymentDisputed(Request $request): \Illuminate\View\View
    {
        $handshakeId = $request->query('handshakeId');
        $orderId = $request->query('orderId');

        $handshakeData = $this->cashPaymentService->getHandshakeStatus($handshakeId);

        if (!$handshakeData) {
            abort(404);
        }

        $order = Order::findOrFail($orderId);

        return view('payments.cash-disputed', compact('handshakeId', 'order', 'handshakeData'));
    }

    /**
     * Xendit online payment success
     */
    public function success(Request $request): \Illuminate\View\View
    {
        $paymentId = $request->query('payment_id');
        $payment = Payment::find($paymentId);

        return view('payments.success', compact('payment'));
    }

    /**
     * Xendit online payment failed
     */
    public function failed(Request $request): \Illuminate\View\View
    {
        $paymentId = $request->query('payment_id');
        $payment = Payment::find($paymentId);

        return view('payments.failed', compact('payment'));
    }
}
