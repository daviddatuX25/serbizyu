<?php

namespace Database\Factories;

use App\Domains\Common\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'house_no' => $this->faker->buildingNumber,
            'street' => $this->faker->streetName,
            'barangay' => $this->faker->citySuffix, // Using citySuffix as a proxy for barangay
            'town' => $this->faker->city,
            'province' => $this->faker->state,
            'country' => $this->faker->country,
            'lat' => $this->faker->latitude,
            'lng' => $this->faker->longitude,
        ];
    }
}
