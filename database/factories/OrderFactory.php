<?php

namespace Database\Factories;

use App\Domains\Listings\Models\Service;
use App\Domains\Orders\Models\Order;
use App\Domains\Users\Models\User;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 50, 1000);
        $platformFee = $price * 0.05;

        return [
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'service_id' => Service::factory(),
            'price' => $price,
            'platform_fee' => $platformFee,
            'total_amount' => $price + $platformFee,
            'status' => OrderStatus::PENDING->value,
            'payment_status' => 'pending',
        ];
    }
}
