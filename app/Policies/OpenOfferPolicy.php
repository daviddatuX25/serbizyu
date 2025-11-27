<?php

namespace App\Policies;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Users\Models\User;

class OpenOfferPolicy
{
    /**
     * Admins can perform any action on open offers
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any open offers (admin list).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the open offer.
     */
    public function view(User $user, OpenOffer $openOffer): bool
    {
        return $user->hasRole('admin') || $user->id === $openOffer->creator_id;
    }

    /**
     * Determine whether the user can create open offers.
     */
    public function create(User $user): bool
    {
        return ! $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the open offer.
     */
    public function update(User $user, OpenOffer $openOffer): bool
    {
        return $user->id === $openOffer->creator_id;
    }

    /**
     * Determine whether the user can delete the open offer.
     */
    public function delete(User $user, OpenOffer $openOffer): bool
    {
        return $user->id === $openOffer->creator_id;
    }

    /**
     * Determine whether the user can flag an open offer (report for review).
     */
    public function flag(User $user, OpenOffer $openOffer): bool
    {
        return $user->id !== $openOffer->creator_id;
    }

    /**
     * Determine whether the user can suspend/hide an open offer (admin only).
     */
    public function suspend(User $user, OpenOffer $openOffer): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can approve an open offer (admin only).
     */
    public function approve(User $user, OpenOffer $openOffer): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view bids on an open offer.
     */
    public function viewBids(User $user, OpenOffer $openOffer): bool
    {
        return $user->id === $openOffer->creator_id || $user->hasRole('admin');
    }
}
