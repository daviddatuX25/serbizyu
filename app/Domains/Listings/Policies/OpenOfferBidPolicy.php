<?php

namespace App\Domains\Listings\Policies;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Users\Models\User;
use Illuminate\Auth\Access\Response;
use App\Enums\BidStatus;

class OpenOfferBidPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, OpenOffer $openOffer): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OpenOfferBid $bid): bool
    {
        return $user->id === $bid->bidder_id || $user->id === $bid->openOffer->creator_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, OpenOffer $openOffer): bool
    {
        if ($user->id === $openOffer->creator_id) {
            return false;
        }

        $hasActiveBid = $openOffer->bids()
            ->where('bidder_id', $user->id)
            ->where('status', '!=', BidStatus::REJECTED)
            ->exists();

        if ($hasActiveBid) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OpenOfferBid $bid): bool
    {
        return $user->id === $bid->bidder_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OpenOfferBid $bid): bool
    {
        return $user->id === $bid->bidder_id;
    }

    /**
     * Determine whether the user can accept the bid.
     */
    public function accept(User $user, OpenOfferBid $bid): bool
    {
        return $user->id === $bid->openOffer->creator_id;
    }

    /**
     * Determine whether the user can reject the bid.
     */
    public function reject(User $user, OpenOfferBid $bid): bool
    {
        return $user->id === $bid->openOffer->creator_id;
    }
}
