<?php

namespace App\Domains\Payments\Services;

use App\Domains\Orders\Models\Order;
use App\Events\CashHandshakeBuyerConfirmed;
use App\Events\CashHandshakeSellerResponded;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CashPaymentService
{
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Initiate cash payment handshake
     */
    public function initiateHandshake(Order $order): string
    {
        $handshakeId = 'cash_' . $order->id . '_' . uniqid();

        $handshakeData = [
            'order_id' => $order->id,
            'buyer_id' => $order->buyer_id,
            'seller_id' => $order->seller_id,
            'amount' => $order->total_amount,
            'status' => 'pending', // pending -> buyer_claimed -> seller_confirmed/rejected
            'buyer_claimed_at' => null,
            'seller_response_at' => null,
            'rejection_reason' => null,
            'initiated_at' => now()->toDateTimeString(),
        ];

        Cache::put($handshakeId, $handshakeData, self::CACHE_TTL);

        Log::info('Cash Payment Handshake Initiated', [
            'handshake_id' => $handshakeId,
            'order_id' => $order->id,
            'amount' => $order->total_amount,
        ]);

        return $handshakeId;
    }

    /**
     * Buyer claims payment - "I have paid"
     */
    public function buyerClaimedPayment(string $handshakeId): bool
    {
        $data = Cache::get($handshakeId);

        if (!$data || $data['status'] !== 'pending') {
            Log::warning('Invalid handshake state for buyer claim', [
                'handshake_id' => $handshakeId,
                'current_status' => $data['status'] ?? 'not_found',
            ]);
            return false;
        }

        $data['status'] = 'buyer_claimed';
        $data['buyer_claimed_at'] = now()->toDateTimeString();

        Cache::put($handshakeId, $data, self::CACHE_TTL);

        // Broadcast event for realtime updates
        event(new CashHandshakeBuyerConfirmed(
            $handshakeId,
            $data['order_id'],
            $data['buyer_claimed_at']
        ));

        Log::info('Buyer Claimed Payment', [
            'handshake_id' => $handshakeId,
            'order_id' => $data['order_id'],
        ]);

        return true;
    }

    /**
     * Seller confirms payment - "Yes, I received it"
     * Supports both buyer_claimed (normal flow) and pending (fallback/manual record)
     */
    public function sellerConfirmedPayment(string $handshakeId, int $orderId): bool
    {
        $data = Cache::get($handshakeId);

        // Allow confirmation from both buyer_claimed (normal) and pending (fallback manual record)
        if (!$data || !in_array($data['status'], ['buyer_claimed', 'pending'])) {
            Log::warning('Invalid handshake state for seller confirmation', [
                'handshake_id' => $handshakeId,
                'current_status' => $data['status'] ?? 'not_found',
            ]);
            return false;
        }

        $order = Order::findOrFail($orderId);

        $order->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_method' => 'cash',
        ]);

        $data['status'] = 'seller_confirmed';
        $data['seller_response_at'] = now()->toDateTimeString();

        Cache::put($handshakeId, $data, self::CACHE_TTL);

        // Broadcast event for realtime updates
        event(new CashHandshakeSellerResponded(
            $handshakeId,
            $orderId,
            'seller_confirmed',
            $data['seller_response_at']
        ));

        Log::info('Seller Confirmed Cash Payment', [
            'handshake_id' => $handshakeId,
            'order_id' => $orderId,
        ]);

        return true;
    }

    /**
     * Seller rejects payment - "I didn't receive it"
     * Supports both buyer_claimed (normal) and pending (fallback/manual record)
     */
    public function sellerRejectedPayment(string $handshakeId, int $orderId, string $reason = ''): bool
    {
        $data = Cache::get($handshakeId);

        // Allow rejection from both buyer_claimed (normal) and pending (fallback rejection)
        if (!$data || !in_array($data['status'], ['buyer_claimed', 'pending'])) {
            Log::warning('Invalid handshake state for seller rejection', [
                'handshake_id' => $handshakeId,
                'current_status' => $data['status'] ?? 'not_found',
            ]);
            return false;
        }

        $order = Order::findOrFail($orderId);

        $order->update([
            'payment_status' => 'pending',
        ]);

        $data['status'] = 'seller_rejected';
        $data['seller_response_at'] = now()->toDateTimeString();
        $data['rejection_reason'] = $reason;

        Cache::put($handshakeId, $data, self::CACHE_TTL);

        // Broadcast event for realtime updates
        event(new CashHandshakeSellerResponded(
            $handshakeId,
            $orderId,
            'seller_rejected',
            $data['seller_response_at'],
            $reason
        ));

        Log::warning('Seller Rejected Cash Payment', [
            'handshake_id' => $handshakeId,
            'order_id' => $orderId,
            'reason' => $reason,
        ]);

        return true;
    }

    /**
     * Get handshake status
     */
    public function getHandshakeStatus(string $handshakeId): ?array
    {
        return Cache::get($handshakeId);
    }

    /**
     * Cancel handshake
     */
    public function cancelHandshake(string $handshakeId): bool
    {
        Cache::forget($handshakeId);
        return true;
    }
}
