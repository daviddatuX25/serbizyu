<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\WorkflowTemplate;

class OpenOfferFactory extends Factory
{

    protected $model = OpenOffer::class;    

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'budget' => $this->faker->randomFloat(2, 10, 100),
            'fulfilled' => $this->faker->boolean(30),
            'pay_first' => $this->faker->boolean(30),
            'category_id' => Category::inRandomOrder()->first()->id,
            'creator_id' => User::inRandomOrder()->first()->id,
            'workflow_template_id' => WorkflowTemplate::inRandomOrder()->first()->id,
        ];
    }
}