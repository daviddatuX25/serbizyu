<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\Service;
use App\Domains\Users\Models\User;

class OpenOfferBidSeeder extends Seeder
{
    public function run(): void
    {
        // Get unfulfilled open offers
        $openOffers = OpenOffer::where('fulfilled', false)->get();
        
        foreach ($openOffers as $offer) {
            // Each open offer gets 2-4 bids
            $numBids = rand(2, 4);
            
            // Get services in the same category
            $relevantServices = Service::where('category_id', $offer->category_id)
                ->inRandomOrder()
                ->take($numBids)
                ->get();
            
            foreach ($relevantServices as $service) {
                // Calculate proposed price (80%-120% of offer budget)
                $minPrice = $offer->budget * 0.8;
                $maxPrice = $offer->budget * 1.2;
                $proposedPrice = rand($minPrice * 100, $maxPrice * 100) / 100;
                
                // Create bid
                OpenOfferBid::create([
                    'open_offer_id' => $offer->id,
                    'bidder_id' => $service->creator_id,
                    'service_id' => $service->id,
                    'proposed_price' => $proposedPrice,
                ]);
            }
        }
        
        $this->command->info('Created bids for ' . $openOffers->count() . ' open offers');
    }
}