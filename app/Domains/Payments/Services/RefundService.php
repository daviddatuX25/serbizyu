<?php

namespace App\Domains\Payments\Services;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Models\Refund;

class RefundService
{
    /**
     * Request refund for an order
     */
    public function requestRefund(Payment $payment, string $reason, array $bankDetails = []): Refund
    {
        // Check if order can be refunded (before work starts)
        $order = $payment->order;
        
        if ($order->workInstance && $order->workInstance->hasStarted()) {
            throw new \Exception('Cannot refund orders where work has already started.');
        }

        if ($payment->status !== 'paid') {
            throw new \Exception('Only paid payments can be refunded.');
        }

        return Refund::create([
            'payment_id' => $payment->id,
            'order_id' => $order->id,
            'amount' => $payment->total_amount,
            'reason' => $reason,
            'bank_details' => $bankDetails ?: null,
            'status' => 'requested',
        ]);
    }

    /**
     * Approve refund request (admin action)
     */
    public function approveRefund(Refund $refund): bool
    {
        if (!$refund->isRequested()) {
            throw new \Exception('Only requested refunds can be approved.');
        }

        $refund->approve();

        // Update order status
        $refund->order->update(['payment_status' => 'refunded']);

        return true;
    }

    /**
     * Reject refund request (admin action)
     */
    public function rejectRefund(Refund $refund): bool
    {
        if (!$refund->isRequested()) {
            throw new \Exception('Only requested refunds can be rejected.');
        }

        $refund->reject();

        return true;
    }

    /**
     * Process refund (admin confirms refund sent)
     */
    public function processRefund(Refund $refund): bool
    {
        if (!$refund->isApproved()) {
            throw new \Exception('Only approved refunds can be processed.');
        }

        $refund->complete();

        // Update payment status
        $refund->payment->update(['status' => 'refunded']);

        // Update order status
        $refund->order->update(['status' => 'cancelled']);

        return true;
    }

    /**
     * Get pending refunds (admin dashboard)
     */
    public function getPendingRefunds()
    {
        return Refund::where('status', 'requested')
            ->with(['payment.user', 'order.service'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get refund statistics
     */
    public function getRefundStatistics(?\DateTime $fromDate = null, ?\DateTime $toDate = null): array
    {
        $query = Refund::query();

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $refunds = $query->get();

        return [
            'total_requested' => $refunds->where('status', 'requested')->count(),
            'total_approved' => $refunds->where('status', 'approved')->count(),
            'total_rejected' => $refunds->where('status', 'rejected')->count(),
            'total_completed' => $refunds->where('status', 'completed')->count(),
            'total_amount' => $refunds->where('status', 'completed')->sum('amount'),
            'refunds' => $refunds,
        ];
    }

    /**
     * Check if order is eligible for refund
     */
    public function isRefundable(Order $order): bool
    {
        // Order must not be started or cancelled
        if ($order->status === 'cancelled') {
            return false;
        }

        if ($order->workInstance && $order->workInstance->hasStarted()) {
            return false;
        }

        // Payment must be completed
        if ($order->payment_status !== 'paid') {
            return false;
        }

        return true;
    }
}
