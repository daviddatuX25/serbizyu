<?php

namespace Database\Factories;

use App\Domains\Orders\Models\Order;
use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\OpenOfferBid;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'service_id' => Service::factory(),
            'open_offer_id' => OpenOffer::factory(),
            'open_offer_bid_id' => OpenOfferBid::factory(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'platform_fee' => $this->faker->randomFloat(2, 1, 100),
            'total_amount' => $this->faker->randomFloat(2, 11, 1100),
            'status' => 'pending',
            'payment_status' => 'pending',
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ];
    }
}
