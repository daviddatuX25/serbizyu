<?php

namespace App\Domains\Listings\Policies;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Users\Models\User;
use Illuminate\Auth\Access\Response;

class OpenOfferPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Any user can view any open offers
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, OpenOffer $openOffer): bool
    {
        return true; // Any user can view a single open offer
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user !== null; // Any authenticated user can create an offer
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OpenOffer $openOffer): bool
    {
        return $user->id === $openOffer->user_id; // Only the offer owner can update it
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OpenOffer $openOffer): bool
    {
        return $user->id === $openOffer->user_id; // Only the offer owner can delete it
    }

    /**
     * Determine whether the user can close the model.
     */
    public function close(User $user, OpenOffer $openOffer): bool
    {
        return $user->id === $openOffer->user_id; // Only the offer owner can close it
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OpenOffer $openOffer): bool
    {
        return $user->id === $openOffer->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OpenOffer $openOffer): bool
    {
        return $user->id === $openOffer->user_id;
    }
}
