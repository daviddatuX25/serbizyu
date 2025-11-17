<?php

namespace App\Domains\Orders\Policies;

use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Orders\Models\Order;
use App\Enums\OrderStatus;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id || $user->id === $order->seller_id;
    }

    public function create(User $user, OpenOfferBid $bid): bool
    {
        return $user->id === $bid->user_id;
    }

    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id && $order->status === OrderStatus::Pending;
    }
}
