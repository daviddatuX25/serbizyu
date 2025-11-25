<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Service;
use App\Enums\BidStatus;

class OpenOfferBidFactory extends Factory
{
    protected $model = OpenOfferBid::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'open_offer_id' => OpenOffer::factory(),
            'bidder_id' => User::factory(),
            'service_id' => Service::factory(),
            'amount' => $this->faker->randomFloat(2, 50, 1000),
            'message' => $this->faker->sentence(),
            'status' => BidStatus::PENDING,
        ];
    }
}