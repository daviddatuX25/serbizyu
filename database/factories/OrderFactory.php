<?php

namespace Database\Factories;

use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Orders\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'service_id' => Service::factory(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'total_amount' => $this->faker->randomFloat(2, 10, 1000),
            'status' => 'pending',
            'payment_status' => 'pending',
        ];
    }
}
