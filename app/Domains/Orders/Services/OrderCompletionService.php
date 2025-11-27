<?php

namespace App\Domains\Orders\Services;

use App\Domains\Orders\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;

class OrderCompletionService
{
    /**
     * Check if an order can be completed
     * An order can be completed if:
     * 1. All work steps are completed (if work instance exists)
     * 2. Payment is settled (payment_status = 'paid')
     */
    public function canComplete(Order $order): bool
    {
        // Payment must be settled
        if ($order->payment_status !== 'paid') {
            return false;
        }

        // If there's a work instance, all steps must be completed
        $workInstance = $order->workInstance;

        if ($workInstance) {
            $incompletedSteps = $workInstance->steps()
                ->where('status', '!=', 'completed')
                ->exists();

            if ($incompletedSteps) {
                return false;
            }
        }

        return true;
    }

    /**
     * Attempt to complete the order if eligible
     * Returns true if order was completed, false otherwise
     */
    public function attemptCompletion(Order $order): bool
    {
        // Already completed
        if ($order->status === OrderStatus::COMPLETED->value || $order->status === 'completed') {
            return false;
        }

        // Check if order can be completed
        if (! $this->canComplete($order)) {
            Log::info('Order cannot be completed yet', [
                'order_id' => $order->id,
                'payment_status' => $order->payment_status,
                'work_instance_exists' => $order->workInstance !== null,
            ]);

            return false;
        }

        // Mark order as completed
        $order->update(['status' => OrderStatus::COMPLETED->value]);

        Log::info('Order completed successfully', [
            'order_id' => $order->id,
            'buyer_id' => $order->buyer_id,
            'seller_id' => $order->seller_id,
        ]);

        return true;
    }

    /**
     * Handle payment completion - checks if order should also be marked as completed
     * This is called from payment webhooks and payment confirmation flows
     */
    public function handlePaymentCompleted(Order $order): void
    {
        $order->refresh();
        $this->attemptCompletion($order);
    }

    /**
     * Handle work completion - checks if order should also be marked as completed
     * This is called from work step completion flows
     */
    public function handleWorkCompleted(Order $order): void
    {
        $order->refresh();
        $this->attemptCompletion($order);
    }
}
