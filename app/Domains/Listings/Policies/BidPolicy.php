<?php

namespace App\Domains\Listings\Policies;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Users\Models\User;
use Illuminate\Auth\Access\Response;
use App\Enums\OpenOfferStatus;

class BidPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user, OpenOffer $openOffer): bool
    {
        // Allow viewing bids if the user is the offer creator or has placed a bid.
        return $user?->id === $openOffer->creator_id || $openOffer->bids->where('bidder_id', $user?->id)->isNotEmpty();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OpenOfferBid $openOfferBid): bool
    {
        return $user->id === $openOfferBid->bidder_id || $user->id === $openOfferBid->openOffer->creator_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, OpenOffer $openOffer): bool
    {
        // A user can bid on an offer if they are not the owner and the offer is open.
        return $user->id !== $openOffer->creator_id
            && $openOffer->status === OpenOfferStatus::OPEN;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OpenOfferBid $openOfferBid): bool
    {
        return $user->id === $openOfferBid->bidder_id && $openOfferBid->status === 'pending';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OpenOfferBid $openOfferBid): bool
    {
        return $user->id === $openOfferBid->bidder_id; // Only the bidder can delete their bid
    }

    /**
     * Determine whether the user can accept a bid.
     */
    public function accept(User $user, OpenOfferBid $openOfferBid): bool
    {
        // Only the owner of the offer can accept a bid, and only if the offer is open and the bid is pending
        return $user->id === $openOfferBid->openOffer->creator_id &&
               $openOfferBid->openOffer->status === OpenOfferStatus::OPEN &&
               $openOfferBid->status === 'pending';
    }

    /**
     * Determine whether the user can reject a bid.
     */
    public function reject(User $user, OpenOfferBid $openOfferBid): bool
    {
        // Only the owner of the offer can reject a bid, and only if the bid is pending
        return $user->id === $openOfferBid->openOffer->creator_id &&
               $openOfferBid->status === 'pending';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OpenOfferBid $openOfferBid): bool
    {
        return $user->id === $openOfferBid->bidder_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OpenOfferBid $openOfferBid): bool
    {
        return $user->id === $openOfferBid->bidder_id;
    }
}
