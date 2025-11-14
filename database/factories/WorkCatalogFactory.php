<?php

namespace Database\Factories;

use App\Domains\Listings\Models\WorkCatalog;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkCatalogFactory extends Factory
{
    protected $model = WorkCatalog::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'duration_minutes' => $this->faker->numberBetween(15, 240),
        ];
    }
}
