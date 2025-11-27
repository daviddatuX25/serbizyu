<?php

namespace App\Domains\Payments\Policies;

use App\Domains\Payments\Models\Refund;
use App\Domains\Users\Models\User;

class RefundPolicy
{
    /**
     * Admins can perform any action on refunds
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any refunds (admin list).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the refund.
     */
    public function view(User $user, Refund $refund): bool
    {
        return $user->hasRole('admin') || $user->id === $refund->order->buyer_id;
    }

    /**
     * Determine whether the user can create refund requests.
     */
    public function create(User $user): bool
    {
        return ! $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the refund (admin only).
     */
    public function update(User $user, Refund $refund): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the refund (admin only).
     */
    public function delete(User $user, Refund $refund): bool
    {
        return false; // Refunds should not be deleted, only handled through approval workflow
    }

    /**
     * Determine whether the user can approve a refund (admin only).
     */
    public function approve(User $user, Refund $refund): bool
    {
        return $user->hasRole('admin') && $refund->status === 'requested';
    }

    /**
     * Determine whether the user can reject a refund (admin only).
     */
    public function reject(User $user, Refund $refund): bool
    {
        return $user->hasRole('admin') && $refund->status === 'requested';
    }

    /**
     * Determine whether the user can mark refund as completed (admin only).
     */
    public function markAsCompleted(User $user, Refund $refund): bool
    {
        return $user->hasRole('admin') && $refund->status === 'approved';
    }

    /**
     * Determine whether the user can view refund bank details (admin only).
     */
    public function viewBankDetails(User $user, Refund $refund): bool
    {
        return $user->hasRole('admin');
    }
}
