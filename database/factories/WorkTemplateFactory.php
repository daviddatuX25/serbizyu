<?php

namespace Database\Factories;

use App\Domains\Listings\Models\WorkTemplate;
use App\Domains\Listings\Models\WorkflowTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkTemplateFactory extends Factory
{
    protected $model = WorkTemplate::class;

    public function definition(): array
    {
        return [
            'workflow_template_id' => WorkflowTemplate::factory(),
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'duration_minutes' => $this->faker->numberBetween(15, 240),
            'order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
