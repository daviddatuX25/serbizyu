<?php

namespace App\Domains\Payments\Http\Controllers;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Models\Refund;
use App\Domains\Payments\Services\RefundService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    protected $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    /**
     * Display refund request form
     */
    public function create(Order $order)
    {
        // Check if user is buyer
        if ($order->buyer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if order is refundable
        if (!$this->refundService->isRefundable($order)) {
            abort(403, 'This order is not eligible for refund.');
        }

        $payment = $order->payment;

        return view('refunds.create', compact('order', 'payment'));
    }

    /**
     * Store refund request
     */
    public function store(Request $request, Order $order)
    {
        // Check if user is buyer
        if ($order->buyer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'reason' => 'required|string|min:10|max:500',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_name' => 'nullable|string',
        ]);

        try {
            $payment = $order->payment;

            $bankDetails = [];
            if ($request->bank_name) {
                $bankDetails = [
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'account_name' => $request->account_name,
                ];
            }

            $refund = $this->refundService->requestRefund($payment, $request->reason, $bankDetails);

            return redirect()->route('refunds.show', $refund)
                ->with('success', 'Refund request submitted successfully. We will review it within 24-48 hours.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display refund request details
     */
    public function show(Refund $refund)
    {
        // Authorization: buyer can view their own refunds
        if ($refund->order->buyer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('refunds.show', compact('refund'));
    }

    /**
     * Admin: Approve refund
     */
    public function approve(Refund $refund)
    {
        try {
            $this->refundService->approveRefund($refund);
            return back()->with('success', 'Refund approved. Seller will be notified.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin: Reject refund
     */
    public function reject(Request $request, Refund $refund)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        try {
            $this->refundService->rejectRefund($refund);
            return back()->with('success', 'Refund rejected. Buyer will be notified.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin: Process refund (mark as processed)
     */
    public function process(Refund $refund)
    {
        try {
            $this->refundService->processRefund($refund);
            return back()->with('success', 'Refund processed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
