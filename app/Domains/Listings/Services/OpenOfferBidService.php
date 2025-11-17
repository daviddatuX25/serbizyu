<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\DB;
use App\Enums\OpenOfferStatus;

class OpenOfferBidService
{
    public function createBid(User $bidder, OpenOffer $openOffer, array $data): OpenOfferBid
    {
        return DB::transaction(function () use ($bidder, $openOffer, $data) {
            // Ensure the user hasn't already bid on this offer
            if ($openOffer->bids()->where('bidder_id', $bidder->id)->exists()) {
                throw new \Exception('You have already placed a bid on this offer.');
            }

            // Ensure the offer is still open
            if ($openOffer->status !== OpenOfferStatus::OPEN) {
                throw new \Exception('This offer is no longer open for bids.');
            }

            $bid = $openOffer->bids()->create([
                'bidder_id' => $bidder->id,
                'service_id' => $data['service_id'],
                'amount' => $data['amount'],
                'message' => $data['message'] ?? null,
            ]);

            // Optionally, send a notification to the offer creator
            // Notification::send($openOffer->creator, new BidPlacedNotification($bid));

            return $bid;
        });
    }

    public function acceptBid(OpenOfferBid $bid): OpenOfferBid
    {
        return DB::transaction(function () use ($bid) {
            // Ensure the bid is still pending
            if ($bid->status !== 'pending') {
                throw new \Exception('This bid cannot be accepted.');
            }

            // Accept the bid
            $bid->update(['status' => 'accepted']);

            // Close the open offer
            $bid->openOffer->update(['status' => OpenOfferStatus::CLOSED]);

            // Reject all other bids for this offer
            $bid->openOffer->bids()
                ->where('id', '!=', $bid->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            // Optionally, create an order here
            // OrderService::createOrder($bid);

            // Optionally, send notifications
            // Notification::send($bid->bidder, new BidAcceptedNotification($bid));
            // Notification::send($bid->openOffer->creator, new BidAcceptedNotification($bid));

            return $bid;
        });
    }

    public function rejectBid(OpenOfferBid $bid): OpenOfferBid
    {
        return DB::transaction(function () use ($bid) {
            // Ensure the bid is still pending
            if ($bid->status !== 'pending') {
                throw new \Exception('This bid cannot be rejected.');
            }

            $bid->update(['status' => 'rejected']);

            // Optionally, send notification to the bidder
            // Notification::send($bid->bidder, new BidRejectedNotification($bid));

            return $bid;
        });
    }

    public function updateBid(OpenOfferBid $bid, array $data): OpenOfferBid
    {
        return DB::transaction(function () use ($bid, $data) {
            // Ensure the bid is still pending
            if ($bid->status !== 'pending') {
                throw new \Exception('This bid cannot be updated.');
            }

            $bid->update([
                'amount' => $data['amount'] ?? $bid->amount,
                'message' => $data['message'] ?? $bid->message,
            ]);

            return $bid;
        });
    }

    public function deleteBid(OpenOfferBid $bid): void
    {
        DB::transaction(function () use ($bid) {
            // Ensure the bid is still pending
            if ($bid->status !== 'pending') {
                throw new \Exception('This bid cannot be deleted.');
            }
            $bid->delete();
        });
    }
}
