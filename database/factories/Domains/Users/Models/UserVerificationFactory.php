<?php

namespace Database\Factories\Domains\Users\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Users\Models\UserVerification>
 */
class UserVerificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'id_type' => $this->faker->randomElement(['national_id', 'drivers_license', 'passport']),
            'id_front_path' => 'verifications/fake_front.jpg',
            'id_back_path' => 'verifications/fake_back.jpg',
            'status' => 'pending',
            'rejection_reason' => null,
            'reviewed_at' => null,
            'reviewed_by' => null,
        ];
    }
}
