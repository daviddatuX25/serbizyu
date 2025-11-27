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
        $streetAddress = $this->faker->streetAddress();
        $barangay = $this->faker->city();
        $city = '042108'; // Sample city code
        $province = '042100'; // Sample province code
        $region = '040000'; // Sample region code
        $provinceName = 'Cavite';
        $regionName = 'CALABARZON';

        // Compose full address
        $fullAddress = implode(', ', array_filter([
            $streetAddress,
            $barangay,
            $provinceName,
            $regionName,
        ]));

        return [
            'label' => $this->faker->word().' Address',
            'street_address' => $streetAddress,
            'barangay' => $barangay,
            'city' => $city,
            'province' => $province,
            'region' => $region,
            'province_name' => $provinceName,
            'region_name' => $regionName,
            'full_address' => $fullAddress,
            'api_source' => 'PSGC_API',
            'api_id' => "{$region}-{$province}-{$city}",
            'lat' => $this->faker->latitude,
            'lng' => $this->faker->longitude,
        ];
    }
}
