<?php

namespace App\Domains\Payments\Http\Controllers;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Services\PaymentService;
use App\Domains\Payments\Services\CashPaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
     * Routes to buyer or seller specific views based on user role
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
        $currentUserId = Auth::id();
        $isBuyer = $currentUserId === $order->buyer_id;
        $isSeller = $currentUserId === $order->seller_id;

        // Debug logging
        Log::debug('Cash Handshake Access', [
            'current_user_id' => $currentUserId,
            'order_id' => $order->id,
            'buyer_id' => $order->buyer_id,
            'seller_id' => $order->seller_id,
            'is_buyer' => $isBuyer,
            'is_seller' => $isSeller,
            'handshake_id' => $handshakeId,
        ]);

        // Verify user is either buyer or seller
        if (!$isBuyer && !$isSeller) {
            Log::warning('Unauthorized cash handshake access attempt', [
                'user_id' => $currentUserId,
                'order_id' => $order->id,
            ]);
            abort(403, 'Unauthorized to access this payment');
        }

        // Route to appropriate view based on user role
        if ($isBuyer) {
            return view('payments.cash-payment-request', compact('handshakeId', 'order', 'handshakeData'));
        } else {
            // Seller view - fetch buyer info
            $buyer = $order->buyer;
            return view('payments.cash-payment-release', compact('handshakeId', 'order', 'handshakeData', 'buyer'));
        }
    }

    /**
     * Get current handshake status (for polling)
     */
    public function getHandshakeStatus(Request $request)
    {
        $handshakeId = $request->query('handshakeId');
        $handshakeData = $this->cashPaymentService->getHandshakeStatus($handshakeId);

        if (!$handshakeData) {
            return response()->json(['error' => 'Handshake not found'], 404);
        }

        return response()->json(['handshakeData' => $handshakeData]);
    }

    /**
     * Buyer claims they have paid (cash method)
     */
    /**
     * Buyer claims they have paid (cash method)
     */
    public function buyerClaimedPayment(Request $request): JsonResponse|RedirectResponse
    {
        $handshakeId = $request->input('handshake_id');
        $orderId = $request->input('order_id');

        $order = Order::findOrFail($orderId);

        // Verify current user is the buyer
        if (Auth::id() !== $order->buyer_id) {
            $message = 'Only the buyer can claim payment';
            Log::warning('Unauthorized buyer payment claim', [
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'actual_buyer_id' => $order->buyer_id,
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }
            return back()->with('error', $message);
        }

        $this->authorize('update', $order);

        if ($this->cashPaymentService->buyerClaimedPayment($handshakeId)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed. Waiting for seller confirmation...'
                ]);
            }
            return redirect()->route('payments.cash.wait-seller', [
                'handshakeId' => $handshakeId,
                'orderId' => $orderId,
            ])->with('success', 'Payment confirmed. Waiting for seller confirmation...');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to confirm payment at this time'
            ], 400);
        }
        return back()->with('error', 'Unable to confirm payment at this time');
    }

    /**
     * Seller confirms receiving payment (cash method)
     */
    public function sellerConfirmedPayment(Request $request): JsonResponse|RedirectResponse
    {
        $handshakeId = $request->input('handshake_id');
        $orderId = $request->input('order_id');

        $order = Order::findOrFail($orderId);

        // Verify current user is the seller
        if (Auth::id() !== $order->seller_id) {
            $message = 'Only the seller can confirm payment';
            Log::warning('Unauthorized seller payment confirmation', [
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'actual_seller_id' => $order->seller_id,
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }
            return back()->with('error', $message);
        }

        $this->authorize('update', $order);

        if ($this->cashPaymentService->sellerConfirmedPayment($handshakeId, $orderId)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed! Order is now active.'
                ]);
            }
            return redirect()->route('orders.show', $order)->with('success', 'Payment confirmed! Order is now active.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to confirm payment'
            ], 400);
        }
        return back()->with('error', 'Unable to confirm payment');
    }

    /**
     * Seller disputes payment (cash method)
     */
    public function sellerRejectedPayment(Request $request): JsonResponse|RedirectResponse
    {
        $handshakeId = $request->input('handshake_id');
        $orderId = $request->input('order_id');
        $reason = $request->input('reason', '');

        $order = Order::findOrFail($orderId);

        // Verify current user is the seller
        if (Auth::id() !== $order->seller_id) {
            $message = 'Only the seller can reject payment';
            Log::warning('Unauthorized seller payment rejection', [
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'actual_seller_id' => $order->seller_id,
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }
            return back()->with('error', $message);
        }

        $this->authorize('update', $order);

        if ($this->cashPaymentService->sellerRejectedPayment($handshakeId, $orderId, $reason)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment dispute recorded. Please try again or contact support.'
                ]);
            }
            return redirect()->route('payments.cash.disputed', [
                'orderId' => $orderId,
                'handshakeId' => $handshakeId,
            ])->with('info', 'Payment dispute recorded. Please try again or contact support.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to process dispute'
            ], 400);
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
