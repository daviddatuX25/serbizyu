<?php

namespace Database\Factories\Domains\Messaging\Models;

use App\Domains\Messaging\Models\Message;
use App\Domains\Messaging\Models\MessageThread;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'message_thread_id' => MessageThread::factory(),
            'sender_id' => User::factory(),
            'content' => $this->faker->paragraph(),
            'read_at' => null,
        ];
    }

    public function read()
    {
        return $this->state(function (array $attributes) {
            return [
                'read_at' => now(),
            ];
        });
    }
}
