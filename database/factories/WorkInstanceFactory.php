<?php

namespace Database\Factories;

use App\Domains\Orders\Models\Order;
use App\Domains\Work\Models\WorkInstance;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkInstanceFactory extends Factory
{
    protected $model = WorkInstance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'notes' => $this->faker->paragraph(),
        ];
    }
}
