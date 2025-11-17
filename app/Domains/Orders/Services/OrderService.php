<?php

namespace App\Domains\Orders\Services;

use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Orders\Events\OrderCreated;
use App\Domains\Orders\Models\Order;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\BusinessRuleException;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(OpenOfferBid $bid, User $buyer): Order
    {
        if (! $bid->is_accepted) {
            throw new BusinessRuleException('Cannot create an order from an unaccepted bid.');
        }

        return DB::transaction(function () use ($bid, $buyer) {
            $platformFeePercentage = config('fees.platform_percentage', 5); // Default to 5%
            $platformFee = ($bid->price * $platformFeePercentage) / 100;
            $totalAmount = $bid->price + $platformFee;

            $order = Order::create([
                'buyer_id' => $buyer->id,
                // Use the service owner as the seller
                'seller_id' => $bid->service->user_id ?? $bid->service->user->id ?? null,
                'service_id' => $bid->service_id,
                'open_offer_id' => $bid->open_offer_id,
                'open_offer_bid_id' => $bid->id,
                'price' => $bid->price,
                'platform_fee' => $platformFee,
                'total_amount' => $totalAmount,
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Unpaid,
            ]);

            event(new OrderCreated($order));

            return $order;
        });
    }
}
