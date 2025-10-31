<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Common\Models\Address;
use App\Domains\Common\Models\UserAddress;
use App\Domains\Users\Models\User;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [
            [
                'house_no' => '123',
                'street' => 'Main Street',
                'barangay' => 'Poblacion',
                'town' => 'Tagudin',
                'province' => 'Ilocos Sur',
                'country' => 'Philippines',
                'lat' => 17.0833,
                'lng' => 120.4500,
            ],
            [
                'house_no' => '456',
                'street' => 'Rizal Avenue',
                'barangay' => 'San Juan',
                'town' => 'Sta. Cruz',
                'province' => 'Ilocos Sur',
                'country' => 'Philippines',
                'lat' => 17.1667,
                'lng' => 120.4833,
            ],
            [
                'house_no' => '789',
                'street' => 'Del Pilar Street',
                'barangay' => 'Centro',
                'town' => 'Candon',
                'province' => 'Ilocos Sur',
                'country' => 'Philippines',
                'lat' => 17.1903,
                'lng' => 120.4472,
            ],
            [
                'house_no' => '321',
                'street' => 'Luna Avenue',
                'barangay' => 'Poblacion East',
                'town' => 'Luna',
                'province' => 'La Union',
                'country' => 'Philippines',
                'lat' => 16.8389,
                'lng' => 120.3833,
            ],
            [
                'house_no' => '654',
                'street' => 'Bonifacio Street',
                'barangay' => 'San Nicolas',
                'town' => 'Vigan',
                'province' => 'Ilocos Sur',
                'country' => 'Philippines',
                'lat' => 17.5747,
                'lng' => 120.3869,
            ],
        ];

        foreach ($addresses as $addressData) {
            Address::create($addressData);
        }

        // Assign addresses to users
        $users = User::all();
        $allAddresses = Address::all();

        foreach ($users as $index => $user) {
            // Each user gets 1-2 addresses
            $numAddresses = rand(1, 2);
            $userAddresses = $allAddresses->random(min($numAddresses, $allAddresses->count()));

            foreach ($userAddresses as $addressIndex => $address) {
                UserAddress::create([
                    'user_id' => $user->id,
                    'address_id' => $address->id,
                    'is_primary' => $addressIndex === 0, // First address is primary
                ]);
            }
        }
    }
}