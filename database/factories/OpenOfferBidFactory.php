<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\Service;


class OpenOfferBidFactory extends Factory
{

    protected $model = OpenOffer::class;    

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'open_offer_id' => $this->faker->randomElement(OpenOffer::all())->id,
            'bidder_id' => $this->faker->randomElement(User::all())->id,
            'service_id' => $this->faker->randomElement(Service::all())->id,
            'proposed_price' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}