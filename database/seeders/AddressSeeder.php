<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Common\Models\Address;
use App\Domains\Common\Models\UserAddress; // Corrected namespace if UserAddress is not in Common
use App\Domains\Users\Models\User;
// Removed: use App\Domains\Common\Services\AddressService; // No longer injecting/using AddressService for seeding

class AddressSeeder extends Seeder
{
    // Removed: protected AddressService $addressService;
    // Removed: public function __construct(AddressService $addressService) { $this->addressService = $addressService; }

    public function run(): void
    {
        $this->command->info('Seeding Addresses...');

        // Sample data in the new format
        $sampleAddresses = [
            [
                'label' => 'Home Address',
                'street_address' => 'Phase 1 Block 2 Lot 3',
                'region_name' => 'CALABARZON', // For full_address composition
                'province_name' => 'Cavite',    // For full_address composition
                'city_name' => 'Dasmariñas City', // For full_address composition
                'barangay_name' => 'Burol',     // For full_address composition
                'api_source' => 'PSGC_API',
                'api_id' => '040000000-042100000-042108000-042108001', // Example composite API ID
                'lat' => 14.3060, // Example lat/lng for Dasmariñas, Cavite
                'lng' => 120.9400,
            ],
            [
                'label' => 'Work Address',
                'street_address' => 'Unit 101, BGC Tower',
                'region_name' => 'National Capital Region (NCR)',
                'province_name' => 'Metro Manila',
                'city_name' => 'Taguig City',
                'barangay_name' => 'Fort Bonifacio',
                'api_source' => 'PSGC_API',
                'api_id' => '130000000-133900000-137607000-137607005', // Example composite API ID
                'lat' => 14.5458, // Example lat/lng for Taguig
                'lng' => 121.0490,
            ],
            [
                'label' => 'Parents Home',
                'street_address' => '123 Sampaguita St.',
                'region_name' => 'CALABARZON',
                'province_name' => 'Laguna',
                'city_name' => 'Calamba City',
                'barangay_name' => 'Real',
                'api_source' => 'PSGC_API',
                'api_id' => '040000000-043400000-043404000-043404013', // Example composite API ID
                'lat' => 14.2148,
                'lng' => 121.1738,
            ],
        ];

        $allCreatedAddresses = collect();
        foreach($sampleAddresses as $addressData) {
            $fullAddressParts = [];
            if ($addressData['street_address']) {
                $fullAddressParts[] = $addressData['street_address'];
            }
            $fullAddressParts[] = $addressData['barangay_name'];
            $fullAddressParts[] = $addressData['city_name'];
            $fullAddressParts[] = $addressData['province_name'];
            $fullAddressParts[] = $addressData['region_name'];
            $fullAddress = implode(', ', array_filter($fullAddressParts));

            $addressArray = [
                'label' => $addressData['label'],
                'full_address' => $fullAddress,
                'api_source' => $addressData['api_source'],
                'api_id' => $addressData['api_id'],
                'lat' => $addressData['lat'],
                'lng' => $addressData['lng'],
            ];
            
            $addressArray['address_hash'] = sha1(implode('|', [
                strtolower(trim($addressArray['full_address'] ?? '')),
                strtolower(trim($addressArray['api_source'] ?? '')),
                strtolower(trim($addressArray['api_id'] ?? '')),
            ]));

            $address = Address::firstOrCreate(
                ['address_hash' => $addressArray['address_hash']],
                $addressArray
            );
            $allCreatedAddresses->push($address);
        }
        
        // Assign addresses to users
        $users = User::all();

        foreach ($users as $user) {
            // Each user gets 1-2 addresses
            $numAddresses = rand(1, 2);
            $userAddresses = $allCreatedAddresses->random(min($numAddresses, $allCreatedAddresses->count()));

            foreach ($userAddresses as $index => $address) {
                // Check if already attached to avoid duplicate entries
                if (!$user->addresses()->where('address_id', $address->id)->exists()) {
                    $user->addresses()->attach($address->id, [
                        'is_primary' => $index === 0, // First attached address is primary
                    ]);
                } else {
                    // If already attached, ensure is_primary is set correctly if needed
                    if ($index === 0) {
                        $user->addresses()->updateExistingPivot($address->id, ['is_primary' => true]);
                    }
                }
            }
        }
    }
}