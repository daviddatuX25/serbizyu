<?php

namespace App\Domains\Payments\Policies;

use App\Domains\Payments\Models\Payment;
use App\Domains\Users\Models\User;

class PaymentPolicy
{
    /**
     * Admins can perform any action on payments
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any payments (admin list).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the payment.
     */
    public function view(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin') || $user->id === $payment->user_id || $user->id === $payment->order->seller_id;
    }

    /**
     * Determine whether the user can create payments.
     */
    public function create(User $user): bool
    {
        return false; // Payments are created programmatically
    }

    /**
     * Determine whether the user can update the payment (admin only).
     */
    public function update(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the payment (admin only).
     */
    public function delete(User $user, Payment $payment): bool
    {
        return false; // Payments should not be deleted
    }

    /**
     * Determine whether the user can mark payment as paid (admin only).
     */
    public function markAsPaid(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can mark payment as failed (admin only).
     */
    public function markAsFailed(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view payment provider details (admin only).
     */
    public function viewProviderDetails(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view payment receipts.
     */
    public function viewReceipt(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin') || $user->id === $payment->user_id || $user->id === $payment->order->seller_id;
    }
}
