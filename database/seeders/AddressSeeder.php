<?php

namespace Database\Seeders;

use App\Domains\Common\Models\Address;
use App\Domains\Common\Models\UserAddress;
use App\Domains\Users\Models\User; // Corrected namespace if UserAddress is not in Common
use Illuminate\Database\Seeder;

// Removed: use App\Domains\Common\Services\AddressService; // No longer injecting/using AddressService for seeding

class AddressSeeder extends Seeder
{
    // Removed: protected AddressService $addressService;
    // Removed: public function __construct(AddressService $addressService) { $this->addressService = $addressService; }

    public function run(): void
    {
        $this->command->info('Seeding Addresses...');

        // Sample data in the new format (expanded to 15+ addresses)
        $sampleAddresses = [
            [
                'label' => 'Home Address',
                'street_address' => 'Phase 1 Block 2 Lot 3',
                'barangay' => 'Burol',
                'city' => '042108',
                'province' => '042100',
                'region' => '040000',
                'region_name' => 'CALABARZON',
                'province_name' => 'Cavite',
                'api_source' => 'PSGC_API',
                'api_id' => '040000000-042100000-042108000-042108001',
                'lat' => 14.3060,
                'lng' => 120.9400,
            ],
            [
                'label' => 'Work Address',
                'street_address' => 'Unit 101, BGC Tower',
                'barangay' => 'Fort Bonifacio',
                'city' => '137607',
                'province' => '133900',
                'region' => '130000',
                'region_name' => 'National Capital Region (NCR)',
                'province_name' => 'Metro Manila',
                'api_source' => 'PSGC_API',
                'api_id' => '130000000-133900000-137607000-137607005',
                'lat' => 14.5458,
                'lng' => 121.0490,
            ],
            [
                'label' => 'Parents Home',
                'street_address' => '123 Sampaguita St.',
                'barangay' => 'Real',
                'city' => '043404',
                'province' => '043400',
                'region' => '040000',
                'region_name' => 'CALABARZON',
                'province_name' => 'Laguna',
                'api_source' => 'PSGC_API',
                'api_id' => '040000000-043400000-043404000-043404013',
                'lat' => 14.2148,
                'lng' => 121.1738,
            ],
            [
                'label' => 'Business Office',
                'street_address' => '456 Business Park Avenue',
                'barangay' => 'Makati',
                'city' => '137605',
                'province' => '133900',
                'region' => '130000',
                'region_name' => 'National Capital Region (NCR)',
                'province_name' => 'Metro Manila',
                'api_source' => 'PSGC_API',
                'api_id' => '130000000-133900000-137605000-137605001',
                'lat' => 14.5546,
                'lng' => 121.0197,
            ],
            [
                'label' => 'Service Center',
                'street_address' => 'Unit 5 Industrial Complex',
                'barangay' => 'Cabuyao',
                'city' => '042109',
                'province' => '042100',
                'region' => '040000',
                'region_name' => 'CALABARZON',
                'province_name' => 'Cavite',
                'api_source' => 'PSGC_API',
                'api_id' => '040000000-042100000-042109000-042109002',
                'lat' => 14.3456,
                'lng' => 120.9876,
            ],
            [
                'label' => 'Downtown Branch',
                'street_address' => '789 Main Street',
                'barangay' => 'Quiapo',
                'city' => '137602',
                'province' => '133900',
                'region' => '130000',
                'region_name' => 'National Capital Region (NCR)',
                'province_name' => 'Metro Manila',
                'api_source' => 'PSGC_API',
                'api_id' => '130000000-133900000-137602000-137602004',
                'lat' => 14.5988,
                'lng' => 121.0014,
            ],
            [
                'label' => 'Residential Unit',
                'street_address' => 'Condominium Tower B, Unit 1205',
                'barangay' => 'Cebu City Proper',
                'city' => '062001',
                'province' => '062000',
                'region' => '070000',
                'region_name' => 'Central Visayas',
                'province_name' => 'Cebu',
                'api_source' => 'PSGC_API',
                'api_id' => '070000000-062000000-062001000-062001001',
                'lat' => 10.3157,
                'lng' => 123.8854,
            ],
            [
                'label' => 'Studio Space',
                'street_address' => '321 Creative Avenue',
                'barangay' => 'San Juan',
                'city' => '137605',
                'province' => '133900',
                'region' => '130000',
                'region_name' => 'National Capital Region (NCR)',
                'province_name' => 'Metro Manila',
                'api_source' => 'PSGC_API',
                'api_id' => '130000000-133900000-137605000-137605002',
                'lat' => 14.5487,
                'lng' => 121.0303,
            ],
            [
                'label' => 'Workshop',
                'street_address' => '654 Industrial Park Road',
                'barangay' => 'Kawit',
                'city' => '042110',
                'province' => '042100',
                'region' => '040000',
                'region_name' => 'CALABARZON',
                'province_name' => 'Cavite',
                'api_source' => 'PSGC_API',
                'api_id' => '040000000-042100000-042110000-042110001',
                'lat' => 14.4123,
                'lng' => 120.8765,
            ],
            [
                'label' => 'Retail Store',
                'street_address' => 'Ground Floor, Shopping Center',
                'barangay' => 'Taguig',
                'city' => '137608',
                'province' => '133900',
                'region' => '130000',
                'region_name' => 'National Capital Region (NCR)',
                'province_name' => 'Metro Manila',
                'api_source' => 'PSGC_API',
                'api_id' => '130000000-133900000-137608000-137608001',
                'lat' => 14.5212,
                'lng' => 121.0533,
            ],
            [
                'label' => 'Storage Facility',
                'street_address' => 'Building 7, Warehouse District',
                'barangay' => 'Kawayan',
                'city' => '043406',
                'province' => '043400',
                'region' => '040000',
                'region_name' => 'CALABARZON',
                'province_name' => 'Laguna',
                'api_source' => 'PSGC_API',
                'api_id' => '040000000-043400000-043406000-043406001',
                'lat' => 14.1876,
                'lng' => 121.1234,
            ],
            [
                'label' => 'Sales Office',
                'street_address' => 'Suite 200, Corporate Tower',
                'barangay' => 'Pasay',
                'city' => '137609',
                'province' => '133900',
                'region' => '130000',
                'region_name' => 'National Capital Region (NCR)',
                'province_name' => 'Metro Manila',
                'api_source' => 'PSGC_API',
                'api_id' => '130000000-133900000-137609000-137609001',
                'lat' => 14.5445,
                'lng' => 121.0000,
            ],
            [
                'label' => 'Training Center',
                'street_address' => 'Education Plaza, Building A',
                'barangay' => 'Malolos',
                'city' => '030701',
                'province' => '030700',
                'region' => '030000',
                'region_name' => 'Central Luzon',
                'province_name' => 'Bulacan',
                'api_source' => 'PSGC_API',
                'api_id' => '030000000-030700000-030701000-030701001',
                'lat' => 14.8370,
                'lng' => 121.0045,
            ],
            [
                'label' => 'Distribution Hub',
                'street_address' => 'Logistics Center, Bay 12',
                'barangay' => 'Las Pinas',
                'city' => '137610',
                'province' => '133900',
                'region' => '130000',
                'region_name' => 'National Capital Region (NCR)',
                'province_name' => 'Metro Manila',
                'api_source' => 'PSGC_API',
                'api_id' => '130000000-133900000-137610000-137610001',
                'lat' => 14.3635,
                'lng' => 121.0145,
            ],
            [
                'label' => 'Satellite Office',
                'street_address' => 'Premium Business Center',
                'barangay' => 'Davao City Proper',
                'city' => '118103',
                'province' => '118100',
                'region' => '120000',
                'region_name' => 'Davao Region',
                'province_name' => 'Davao City',
                'api_source' => 'PSGC_API',
                'api_id' => '120000000-118100000-118103000-118103001',
                'lat' => 7.0731,
                'lng' => 125.6126,
            ],
        ];

        $allCreatedAddresses = collect();
        foreach ($sampleAddresses as $addressData) {
            // Compose full_address from individual components
            $fullAddressParts = [];
            if ($addressData['street_address']) {
                $fullAddressParts[] = $addressData['street_address'];
            }
            if ($addressData['barangay']) {
                $fullAddressParts[] = $addressData['barangay'];
            }
            if ($addressData['province_name']) {
                $fullAddressParts[] = $addressData['province_name'];
            }
            if ($addressData['region_name']) {
                $fullAddressParts[] = $addressData['region_name'];
            }
            $fullAddress = implode(', ', array_filter($fullAddressParts));

            // Prepare address array with all fields
            $addressArray = [
                'label' => $addressData['label'],
                'street_address' => $addressData['street_address'],
                'barangay' => $addressData['barangay'],
                'city' => $addressData['city'],
                'province' => $addressData['province'],
                'region' => $addressData['region'],
                'province_name' => $addressData['province_name'],
                'region_name' => $addressData['region_name'],
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
                if (! $user->addresses()->where('address_id', $address->id)->exists()) {
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
