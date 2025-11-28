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
            'category_id' => null,
        ];
    }
}
