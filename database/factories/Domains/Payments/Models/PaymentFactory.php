<?php

namespace Database\Factories\Domains\Payments\Models;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Payment;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 50, 1000);
        $platformFee = $amount * 0.05;

        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'amount' => $amount,
            'platform_fee' => $platformFee,
            'total_amount' => $amount + $platformFee,
            'payment_method' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'e_wallet']),
            'provider_reference' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'paid_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
