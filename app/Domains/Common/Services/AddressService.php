<?php

namespace App\Domains\Common\Services;

use App\Domains\Common\Interfaces\AddressProviderInterface;
use App\Domains\Common\Models\Address;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Added

class AddressService
{
    protected AddressProviderInterface $addressProvider; // Added

    public function __construct(AddressProviderInterface $addressProvider) // Modified
    {
        $this->addressProvider = $addressProvider; // Added
    }

    /**
     * Get all addresses for the currently authenticated user.
     */
    public function getAddressesForUser(): Collection
    {
        /** @var \App\Domains\Users\Models\User $user */
        $user = Auth::user();

        return $user->addresses()->withPivot('is_primary')->get();
    }

    public function getAddress(int $addressId): ?Address
    {
        return Address::find($addressId);
    }

    /**
     * Create a new address for the authenticated user.
     */
    public function createAddressForUser(array $data): Address
    {
        /** @var User $user */
        $user = Auth::user();

        $isPrimary = isset($data['is_primary']) ? (bool) $data['is_primary'] : false;

        // Prepare address data for creation/finding
        $addressData = [
            'label' => $data['label'] ?? null,
            'full_address' => $data['full_address'],
            'address_hash' => $this->generateAddressHash($data),
            'api_source' => $data['api_source'] ?? null,
            'api_id' => $data['api_id'] ?? null,
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            // Store individual address components
            'street_address' => $data['street_address'] ?? null,
            'barangay' => $data['barangay'] ?? null,
            'city' => $data['city'] ?? null,
            'province' => $data['province'] ?? null,
            'region' => $data['region'] ?? null,
            'province_name' => $data['province_name'] ?? null,
            'region_name' => $data['region_name'] ?? null,
        ];

        return DB::transaction(function () use ($user, $addressData, $isPrimary) {
            // Find an existing address or create a new one
            $address = Address::firstOrCreate(
                ['address_hash' => $addressData['address_hash']],
                $addressData
            );

            // If the new address is primary, unset other primary addresses
            if ($isPrimary) {
                $this->unsetAllPrimaryAddresses($user);
            }

            // Attach to user with pivot data, unless already attached
            if (! $user->addresses->contains($address->id)) {
                $user->addresses()->attach($address->id, [
                    'is_primary' => $isPrimary,
                ]);
            } else {
                // If already attached, just update the pivot (e.g., is_primary status)
                $user->addresses()->updateExistingPivot($address->id, [
                    'is_primary' => $isPrimary,
                ]);
            }

            return $address->fresh();
        });
    }

    /**
     * Update an existing address for a user.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateUserAddress(int $addressId, array $data): Address
    {
        /** @var User $user */
        $user = Auth::user();

        // Find the current address to be updated and verify it belongs to the user
        $currentAddress = $user->addresses()->findOrFail($addressId);

        $isPrimary = isset($data['is_primary']) ? (bool) $data['is_primary'] : false;

        // Prepare address data for new/updated address
        $newAddressData = [
            'label' => $data['label'] ?? null,
            'full_address' => $data['full_address'],
            'address_hash' => $this->generateAddressHash($data),
            'api_source' => $data['api_source'] ?? null,
            'api_id' => $data['api_id'] ?? null,
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            // Store individual address components
            'street_address' => $data['street_address'] ?? null,
            'barangay' => $data['barangay'] ?? null,
            'city' => $data['city'] ?? null,
            'province' => $data['province'] ?? null,
            'region' => $data['region'] ?? null,
            'province_name' => $data['province_name'] ?? null,
            'region_name' => $data['region_name'] ?? null,
        ];

        return DB::transaction(function () use ($user, $currentAddress, $newAddressData, $isPrimary) {
            // Find or create the new address based on updated data
            $newAddress = Address::firstOrCreate(
                ['address_hash' => $newAddressData['address_hash']],
                $newAddressData
            );

            // If the address hash has changed, it means the user is effectively updating to a different address record
            if ($currentAddress->id !== $newAddress->id) {
                // Detach the old address from the user
                $user->addresses()->detach($currentAddress->id);

                // Handle orphaned old address
                $this->deleteOrphanedAddress($currentAddress->id);

                // Attach the new address to the user, unless already attached
                if (! $user->addresses->contains($newAddress->id)) {
                    $user->addresses()->attach($newAddress->id, [
                        'is_primary' => $isPrimary,
                    ]);
                } else {
                    // If already attached, just update the pivot (e.g., is_primary status)
                    $user->addresses()->updateExistingPivot($newAddress->id, [
                        'is_primary' => $isPrimary,
                    ]);
                }
            } else {
                // If the address hash has not changed, just update the existing address's attributes and pivot
                $newAddress->update($newAddressData);
                $user->addresses()->updateExistingPivot($newAddress->id, [
                    'is_primary' => $isPrimary,
                ]);
            }

            // If the address is being set as primary, unset others
            if ($isPrimary) {
                $this->unsetAllPrimaryAddresses($user);
            }

            return $newAddress->fresh();
        });
    }

    /**
     * Delete an address for a user.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteUserAddress(int $addressId): void
    {
        /** @var User $user */
        $user = Auth::user();

        // Verify the address belongs to the user
        $user->addresses()->findOrFail($addressId);

        DB::transaction(function () use ($user, $addressId) {
            // Detach the address from the user
            $user->addresses()->detach($addressId);

            // Check if the address is now orphaned and delete it
            $this->deleteOrphanedAddress($addressId);
        });
    }

    /**
     * Set a specific address as the primary one for the user.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function setPrimaryAddress(int $addressId): void
    {
        /** @var User $user */
        $user = Auth::user();

        // Verify the address belongs to the user
        $user->addresses()->findOrFail($addressId);

        DB::transaction(function () use ($user, $addressId) {
            // Unset all primary addresses
            $this->unsetAllPrimaryAddresses($user);

            // Set the specified address as primary
            $user->addresses()->updateExistingPivot($addressId, [
                'is_primary' => true,
            ]);
        });
    }

    /**
     * Unset all primary addresses for a user.
     */
    private function unsetAllPrimaryAddresses(User $user): void
    {
        // Get all address IDs for this user that are currently primary
        $primaryAddressIds = $user->addresses()
            ->wherePivot('is_primary', true)
            ->pluck('addresses.id');

        // Update each one individually
        foreach ($primaryAddressIds as $addressId) {
            $user->addresses()->updateExistingPivot($addressId, [
                'is_primary' => false,
            ]);
        }
    }

    /**
     * Generates a consistent hash for an address.
     */
    private function generateAddressHash(array $addressData): string
    {
        // Ensure consistent order and content for hashing
        // This hash should uniquely identify a physical address
        // The more detailed the address components, the more unique the hash
        $fullAddress = $addressData['full_address'] ?? '';
        $lat = $addressData['lat'] ?? '';
        $lng = $addressData['lng'] ?? '';
        $apiSource = $addressData['api_source'] ?? '';
        $apiId = $addressData['api_id'] ?? '';

        $addressString = implode('|', [
            strtolower(trim($fullAddress)),
            strtolower(trim($apiSource)),
            strtolower(trim($apiId)),
            // Include lat/lng in hash if they are consistently provided and desired for uniqueness
            // strtolower(trim((string)$lat)),
            // strtolower(trim((string)$lng)),
        ]);

        return sha1($addressString);
    }

    /**
     * Helper to delete orphaned addresses.
     */
    private function deleteOrphanedAddress(int $addressId): void
    {
        $relatedUsersCount = DB::table('user_addresses')
            ->where('address_id', $addressId)
            ->count();

        if ($relatedUsersCount === 0) {
            Address::destroy($addressId);
        }
    }
}
