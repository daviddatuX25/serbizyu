<?php
namespace App\Domains\Listings\Services;

use App\Domains\Users\Services\UserService;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Exceptions\AuthorizationException;
use App\Exceptions\BusinessRuleException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Collection;

class OpenOfferBidService
{


    public function __construct(
        private UserService $userService,
        private OpenOfferService $openOfferService,
        private ServiceService $serviceService
    ){}



    public function placeBid($data) : OpenOfferBid {

        $this->openOfferService->getOpenOffer($data['open_offer_id']);
        $this->userService->getUser($data['bidder_id']);
        $this->serviceService->getService($data['service_id']);

        if($data['service_id'] != $data['bidder_id']) {
            throw new AuthorizationException('Bidder must be the creator of the service.');
        }

        if ($data['proposed_price'] <= 0) {
            throw new BusinessRuleException('Price must be greater than 0.');
        }

        return OpenOfferBid::create($data);
    }

    public function getOpenOfferBid($id) : OpenOfferBid {
        $bid = OpenOfferBid::where('open_offer_id', $id)->get();

        if ($bid == null) {
            throw new ResourceNotFoundException('Bid does not exist.');
        }

        if ($bid->trashed()) {
            throw new ResourceNotFoundException('Bid has been deleted.');
        }

        return $bid;
    }

    // all open offerse
    public function getAllOpenOfferBids() : Collection {

        $bids = OpenOfferBid::all();

        if ($bids->isEmpty()) {
            throw new ResourceNotFoundException('No bids found.');
        }

        if ($bids->every->trashed()) {
            throw new ResourceNotFoundException('Bids have all been deleted.');
        }
        
        return $bids;
    }

    public function rejectBid($id) : OpenOfferBid 
    {
        $bid = $this->getOpenOfferBid($id);
        return $bid; 
    }

    
}