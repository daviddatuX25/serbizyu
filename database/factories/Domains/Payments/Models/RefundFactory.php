<?php

namespace Database\Factories\Domains\Payments\Models;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Models\Refund;
use Illuminate\Database\Eloquent\Factories\Factory;

class RefundFactory extends Factory
{
    protected $model = Refund::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'payment_id' => Payment::factory(),
            'reason' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 100, 500),
            'status' => 'requested',
            'bank_details' => null,
            'processed_at' => null,
        ];
    }
}
