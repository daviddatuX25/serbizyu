<?php

namespace App\Policies;

use App\Domains\Orders\Models\Order;
use App\Domains\Users\Models\User;

class OrderPolicy
{
    /**
     * Admins can perform any action on orders
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any orders (admin list).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->hasRole('admin') || $user->id === $order->buyer_id || $user->id === $order->seller_id;
    }

    /**
     * Determine whether the user can create orders.
     */
    public function create(User $user): bool
    {
        return ! $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id || $user->id === $order->seller_id;
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        return false; // Orders should not be deleted, only cancelled
    }

    /**
     * Determine whether the user can cancel the order.
     */
    public function cancel(User $user, Order $order): bool
    {
        // Only buyer can cancel, and only before work starts
        if ($user->id !== $order->buyer_id) {
            return false;
        }

        // Cannot cancel if work has already started
        if ($order->workInstance && $order->workInstance->started_at) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update order status (admin only).
     */
    public function updateStatus(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view order's financial details.
     */
    public function viewFinancials(User $user, Order $order): bool
    {
        return $user->hasRole('admin') || $user->id === $order->buyer_id || $user->id === $order->seller_id;
    }
}
