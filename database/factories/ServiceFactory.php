<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\WorkflowTemplate;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 50, 1000),
            'pay_first' => $this->faker->boolean(30),
            'category_id' => Category::factory(),
            'creator_id' => User::factory(),
            'workflow_template_id' => WorkflowTemplate::factory(),
        ];
    }
}
