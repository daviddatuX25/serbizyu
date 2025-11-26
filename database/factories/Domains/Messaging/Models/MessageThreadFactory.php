<?php

namespace Database\Factories\Domains\Messaging\Models;

use App\Domains\Messaging\Models\MessageThread;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageThreadFactory extends Factory
{
    protected $model = MessageThread::class;

    public function definition(): array
    {
        return [
            'creator_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'parent_type' => 'App\Models\Example',
            'parent_id' => 1,
        ];
    }
}
