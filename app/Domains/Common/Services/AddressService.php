<?php

namespace App\Domains\Common\Services;

use App\Domains\Common\Models\Address;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class AddressService
{
    /**
     * Get all addresses for the currently authenticated user.
     *
     * @return Collection
     */
    public function getAddressesForUser(): Collection
    {
        /** @var \App\Domains\Users\Models\User $user */
        $user = Auth::user();
        return $user->addresses()->withPivot('is_primary')->get();
    }

    /**
     * Create a new address for the authenticated user.
     *
     * @param array $data
     * @return Address
     */
    public function createAddressForUser(array $data): Address
    {
        /** @var User $user */
        $user = Auth::user();

        // Extract is_primary from data
        $isPrimary = isset($data['is_primary']) ? (bool) $data['is_primary'] : false;
        
        // Remove is_primary and id from address data since they're not address fields
        // Don't filter empty strings as they might be intentional
        $addressData = collect($data)
            ->except(['is_primary', 'id'])
            ->toArray();

        return DB::transaction(function () use ($user, $addressData, $isPrimary) {
            // If the new address is primary, unset other primary addresses
            if ($isPrimary) {
                $this->unsetAllPrimaryAddresses($user);
            }

            // Create the address
            $address = Address::create($addressData);

            // Attach to user with pivot data
            $user->addresses()->attach($address->id, [
                'is_primary' => $isPrimary,
            ]);

            return $address->fresh();
        });
    }

    /**
     * Update an existing address for a user.
     *
     * @param int $addressId
     * @param array $data
     * @return Address
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateUserAddress(int $addressId, array $data): Address
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Verify the address belongs to the user
        $address = $user->addresses()->findOrFail($addressId);

        // Extract is_primary from data
        $isPrimary = isset($data['is_primary']) ? (bool) $data['is_primary'] : false;
        
        // Remove is_primary and id from address data since they're not address fields
        $addressData = collect($data)
            ->except(['is_primary', 'id', 'addressId'])
            ->filter(fn($value) => $value !== null && $value !== '')
            ->toArray();

        return DB::transaction(function () use ($user, $address, $addressId, $addressData, $isPrimary) {
            // If the address is being set as primary, unset others
            if ($isPrimary) {
                $this->unsetAllPrimaryAddresses($user);
            }

            // Update the address attributes
            $address->update($addressData);

            // Update the pivot table
            $user->addresses()->updateExistingPivot($addressId, [
                'is_primary' => $isPrimary,
            ]);

            return $address->fresh();
        });
    }

    /**
     * Delete an address for a user.
     *
     * @param int $addressId
     * @return void
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
            $relatedUsersCount = DB::table('user_addresses')
                ->where('address_id', $addressId)
                ->count();
                
            if ($relatedUsersCount === 0) {
                Address::destroy($addressId);
            }
        });
    }

    /**
     * Set a specific address as the primary one for the user.
     *
     * @param int $addressId
     * @return void
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
     *
     * @param User $user
     * @return void
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
}