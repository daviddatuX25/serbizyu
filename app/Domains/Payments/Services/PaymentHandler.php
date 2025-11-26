<?php

namespace App\Domains\Payments\Services;

use App\Domains\Payments\Models\Payment;
use App\Domains\Orders\Models\Order;

class PaymentHandler
{
    /**
     * Check if in test/development mode for Xendit
     */
    public static function isTestMode(): bool
    {
        return env('APP_ENV') === 'local' || env('PAYMENT_MODE') === 'test';
    }

    /**
     * Determine if payment is required before order creation
     */
    public static function isPayFirstRequired(): bool
    {
        return filter_var(env('PAY_FIRST_ENABLED', true), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Check if cash payment is enabled
     */
    public static function isCashEnabled(): bool
    {
        return filter_var(env('CASH_PAYMENT_ENABLED', true), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get platform fee percentage
     */
    public static function getPlatformFeePercentage(): float
    {
        return (float) config('payment.platform_fee.percentage', 5);
    }

    /**
     * Get payment redirect URL or order page based on config
     */
    public static function getOrderFlowUrl(Order $order, ?string $paymentMethod = null): string
    {
        if (self::isPayFirstRequired() && $paymentMethod !== 'cash') {
            return route('payments.checkout', $order);
        }

        if ($paymentMethod === 'cash' && self::isCashEnabled()) {
            return route('payments.cash-handshake', $order);
        }

        return route('orders.show', $order);
    }

    /**
     * Process payment before order creation (pay-first mode)
     */
    public static function processBeforeOrderCreation(array $orderData, string $paymentMethod): array
    {
        if (!self::isPayFirstRequired()) {
            return $orderData;
        }

        if ($paymentMethod === 'cash' && self::isCashEnabled()) {
            $orderData['payment_status'] = 'pending_cash_verification';
            return $orderData;
        }

        $orderData['payment_status'] = 'pending';
        return $orderData;
    }

    /**
     * Get available payment methods based on service/offer configuration
     */
    public static function getAvailableMethods(?string $servicePaymentMethod = null): array
    {
        if ($servicePaymentMethod === 'any') {
            $methods = ['online', 'cash'];
            return $methods;
        }

        if ($servicePaymentMethod === 'cash') {
            return ['cash'];
        }

        // Default to online (card, gcash, etc)
        return ['online'];
    }

    /**
     * Create cash verification handshake (no DB required)
     */
    public static function initiateCashHandshake(Order $order): array
    {
        return [
            'order_id' => $order->id,
            'buyer_id' => $order->buyer_id,
            'seller_id' => $order->seller_id,
            'amount' => $order->total_amount,
            'status' => 'awaiting_buyer_confirmation',
            'buyer_paid' => false,
            'seller_accepted' => false,
            'created_at' => now(),
        ];
    }

    /**
     * Handle buyer claiming payment sent (cash mode)
     */
    public static function buyerConfirmPayment(Order $order): array
    {
        return [
            'order_id' => $order->id,
            'status' => 'awaiting_seller_confirmation',
            'buyer_paid' => true,
            'seller_accepted' => false,
            'message' => 'Seller will verify payment receipt',
        ];
    }

    /**
     * Handle seller confirming or rejecting payment (cash mode)
     */
    public static function sellerVerifyPayment(Order $order, bool $accepted = true): array
    {
        if ($accepted) {
            $order->update(['payment_status' => 'paid']);
            return [
                'status' => 'payment_confirmed',
                'message' => 'Payment verified. Order can proceed.',
            ];
        }

        return [
            'status' => 'payment_rejected',
            'message' => 'Payment not verified. Please contact buyer.',
        ];
    }
}
