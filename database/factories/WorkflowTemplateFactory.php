<?php

namespace Database\Factories;

use App\Domains\Listings\Models\WorkflowTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domains\Users\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\WorkflowTemplate>
 */
class WorkflowTemplateFactory extends Factory
{

    protected $model = WorkflowTemplate::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->randomElement(['Basic Approval Flow', 'Advanced Approval Flow', 'Intermediate Approval Flow']),  // e.g. "Basic Approval Flow"
            'description' => $this->faker->paragraph(),
            'creator_id' =>  User::inRandomOrder()->first()->id, // links to users
            'is_public' => $this->faker->boolean(30), // 30% chance public
        ];
    }
}
