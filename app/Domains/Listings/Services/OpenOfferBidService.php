<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Users\Models\User;
use App\Domains\Messaging\Models\MessageThread;
use Illuminate\Support\Facades\DB;
use App\Enums\OpenOfferStatus;
use App\Enums\BidStatus;

class OpenOfferBidService
{
    public function createBid(User $bidder, OpenOffer $openOffer, array $data): OpenOfferBid
{
    return DB::transaction(function () use ($bidder, $openOffer, $data) {
        // Ensure the user hasn't already bid on this offer that is not rejected
        if ($openOffer->bids()->where('bidder_id', $bidder->id)->where('status', '!=', BidStatus::REJECTED)->exists()) {
            throw new \Exception('You have already placed a bid on this offer.');
        }

        // Ensure the offer is still open
        if ($openOffer->status !== OpenOfferStatus::OPEN) {
            throw new \Exception('This offer is no longer open for bids.');
        }

        try {
            $bid = $openOffer->bids()->create([
                'bidder_id' => $bidder->id,
                'service_id' => $data['service_id'],
                'amount' => $data['amount'],
                'message' => $data['message'] ?? null,
                'status' => BidStatus::PENDING,
            ]);

            // Create message thread for bid discussion
            $this->createBidMessageThread($bid);

            return $bid;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create bid: ' . $e->getMessage());
        }
    });
}

    public function acceptBid(OpenOfferBid $bid): OpenOfferBid
    {
        return DB::transaction(function () use ($bid) {
            // Ensure the bid is still pending
            if ($bid->status !== BidStatus::PENDING) {
                throw new \Exception('This bid cannot be accepted.');
            }

            // Accept the bid
            $bid->update(['status' => BidStatus::ACCEPTED]);

            // Close the open offer
            $bid->openOffer->update(['status' => OpenOfferStatus::FULFILLED]);

            // Reject all other bids for this offer
            $bid->openOffer->bids()
                ->where('id', '!=', $bid->id)
                ->where('status', BidStatus::PENDING)
                ->update(['status' => BidStatus::REJECTED]);

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
            if ($bid->status !== BidStatus::PENDING) {
                throw new \Exception('This bid cannot be rejected.');
            }

            $bid->update(['status' => BidStatus::REJECTED]);

            // Optionally, send notification to the bidder
            // Notification::send($bid->bidder, new BidRejectedNotification($bid));

            return $bid;
        });
    }

    public function updateBid(OpenOfferBid $bid, array $data): OpenOfferBid
    {
        return DB::transaction(function () use ($bid, $data) {
            // Ensure the bid is still pending
            if ($bid->status !== BidStatus::PENDING) {
                throw new \Exception('This bid cannot be updated.');
            }

            $bid->update($data);

            return $bid;
        });
    }

    public function deleteBid(OpenOfferBid $bid): void
    {
        DB::transaction(function () use ($bid) {
            // Ensure the bid is still pending
            if ($bid->status !== BidStatus::PENDING) {
                throw new \Exception('This bid cannot be deleted.');
            }
            $bid->delete();
        });
    }

    protected function createBidMessageThread(OpenOfferBid $bid): void
    {
        MessageThread::create([
            'creator_id' => $bid->bidder_id,
            'title' => "Bid Discussion - {$bid->openOffer->title}",
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $bid->id,
        ]);
    }
}
